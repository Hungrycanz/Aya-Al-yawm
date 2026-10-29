CREATE TABLE IF NOT EXISTS quran.daily_verses (
    schedule_year SMALLINT NOT NULL,
    day_of_year SMALLINT NOT NULL,
    quran_text_index INTEGER NOT NULL,
    PRIMARY KEY (schedule_year, day_of_year),
    UNIQUE (schedule_year, quran_text_index),
    CHECK (
        day_of_year BETWEEN 1 AND CASE
            WHEN schedule_year % 4 = 0
                AND (schedule_year % 100 <> 0 OR schedule_year % 400 = 0)
            THEN 366
            ELSE 365
        END
    ),
    FOREIGN KEY (quran_text_index) REFERENCES quran.quran_text ("index")
);

WITH schedule_years AS (
    SELECT generate_series(
        EXTRACT(YEAR FROM CURRENT_DATE)::integer,
        EXTRACT(YEAR FROM CURRENT_DATE)::integer + 1
    )::smallint AS schedule_year
),
calendar_days AS (
    SELECT
        years.schedule_year,
        generated.scheduled_date::date AS scheduled_date
    FROM schedule_years AS years
    CROSS JOIN LATERAL generate_series(
        make_date(years.schedule_year, 1, 1)::timestamp,
        make_date(years.schedule_year, 12, 31)::timestamp,
        INTERVAL '1 day'
    ) AS generated(scheduled_date)
),
missing_days AS (
    SELECT
        schedule_year,
        EXTRACT(DOY FROM scheduled_date)::smallint AS day_of_year,
        ROW_NUMBER() OVER (PARTITION BY schedule_year ORDER BY scheduled_date)::integer AS slot_number
    FROM calendar_days
    WHERE NOT EXISTS (
        SELECT 1
        FROM quran.daily_verses AS existing
        WHERE existing.schedule_year = calendar_days.schedule_year
            AND existing.day_of_year = EXTRACT(DOY FROM scheduled_date)::smallint
    )
),
available_verses AS (
    SELECT
        years.schedule_year,
        source."index" AS quran_text_index,
        ROW_NUMBER() OVER (PARTITION BY years.schedule_year ORDER BY RANDOM())::integer AS slot_number
    FROM schedule_years AS years
    CROSS JOIN quran.quran_text AS source
    WHERE NOT EXISTS (
        SELECT 1
        FROM quran.daily_verses AS existing
        WHERE existing.schedule_year = years.schedule_year
            AND existing.quran_text_index = source."index"
    )
)
INSERT INTO quran.daily_verses (schedule_year, day_of_year, quran_text_index)
SELECT missing_days.schedule_year, missing_days.day_of_year, available_verses.quran_text_index
FROM missing_days
JOIN available_verses USING (schedule_year, slot_number)
ON CONFLICT DO NOTHING;