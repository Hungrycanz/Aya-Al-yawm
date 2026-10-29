CREATE TABLE IF NOT EXISTS quran.translation_sources (
    source_code VARCHAR(32) PRIMARY KEY,
    language_code VARCHAR(8) NOT NULL,
    translator TEXT NOT NULL,
    source_name TEXT NOT NULL
);

INSERT INTO quran.translation_sources (source_code, language_code, translator, source_name)
VALUES
    ('en.sahih', 'en', 'Saheeh International', 'Tanzil.net'),
    ('sw.barwani', 'sw', 'Ali Muhsin Al-Barwani', 'Tanzil.net')
ON CONFLICT (source_code) DO UPDATE SET
    language_code = EXCLUDED.language_code,
    translator = EXCLUDED.translator,
    source_name = EXCLUDED.source_name;

CREATE TABLE IF NOT EXISTS quran.verse_translations (
    quran_text_index INTEGER NOT NULL
        REFERENCES quran.quran_text ("index") ON DELETE CASCADE,
    source_code VARCHAR(32) NOT NULL
        REFERENCES quran.translation_sources (source_code),
    translation TEXT NOT NULL,
    PRIMARY KEY (quran_text_index, source_code)
);