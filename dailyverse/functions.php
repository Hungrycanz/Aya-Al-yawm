<?php

$chapter = $_GET['chapter_number'] ?? $_POST['chapter_number'] ?? '';
$verse = $_GET['verse_number'] ?? $_POST['verse_number'] ?? '';
$row = null;

if ($chapter !== '' && $verse !== '') {
    $sql = 'SELECT
        source.text AS verse,
        english.translation AS english_translation,
        swahili.translation AS swahili_translation
    FROM quran.quran_text AS source
    LEFT JOIN quran.verse_translations AS english
        ON english.quran_text_index = source."index" AND english.source_code = ?
    LEFT JOIN quran.verse_translations AS swahili
        ON swahili.quran_text_index = source."index" AND swahili.source_code = ?
    WHERE source.sura = ? AND source.aya = ?';
    $stmt = $conn->prepare($sql);
    $stmt->execute(['en.sahih', 'sw.barwani', $chapter, $verse]);
    $row = $stmt->fetch();
}

$randomStatement = $conn->query(
    'SELECT
        source.sura,
        source.aya,
        source.text AS verse,
        english.translation AS english_translation,
        swahili.translation AS swahili_translation
    FROM quran.daily_verses AS daily
    JOIN quran.quran_text AS source ON source."index" = daily.quran_text_index
    LEFT JOIN quran.verse_translations AS english
        ON english.quran_text_index = source."index" AND english.source_code = \'en.sahih\'
    LEFT JOIN quran.verse_translations AS swahili
        ON swahili.quran_text_index = source."index" AND swahili.source_code = \'sw.barwani\'
    WHERE daily.schedule_year = EXTRACT(YEAR FROM CURRENT_DATE)::smallint
        AND daily.day_of_year = EXTRACT(DOY FROM CURRENT_DATE)::smallint'
);
$randomVerse = $randomStatement->fetch();
?>