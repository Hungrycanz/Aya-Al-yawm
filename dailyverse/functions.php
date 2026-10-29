<?php

$chapter = $_GET['chapter_number'] ?? $_POST['chapter_number'] ?? '';
$verse = $_GET['verse_number'] ?? $_POST['verse_number'] ?? '';
$row = null;

if ($chapter !== '' && $verse !== '') {
    $sql = 'SELECT text AS verse FROM quran.quran_text WHERE sura = ? AND aya = ?';
    $stmt = $conn->prepare($sql);
    $stmt->execute([$chapter, $verse]);
    $row = $stmt->fetch();
}

$randomStatement = $conn->query(
    'SELECT source.sura, source.aya, source.text AS verse
    FROM quran.daily_verses AS daily
    JOIN quran.quran_text AS source ON source."index" = daily.quran_text_index
    WHERE daily.schedule_year = EXTRACT(YEAR FROM CURRENT_DATE)::smallint
        AND daily.day_of_year = EXTRACT(DOY FROM CURRENT_DATE)::smallint'
);
$randomVerse = $randomStatement->fetch();
?>