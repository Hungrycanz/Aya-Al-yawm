<?php

if (PHP_SAPI !== 'cli') {
    http_response_code(404);
    exit;
}

require __DIR__ . '/server.php';

$sourceFiles = [
    'en.sahih' => __DIR__ . '/en.sahih.txt',
    'sw.barwani' => __DIR__ . '/sw.barwani.txt',
];

$verseIndexes = [];
foreach ($conn->query('SELECT "index", sura, aya FROM quran.quran_text') as $verse) {
    $key = (int) $verse['sura'] . '|' . (int) $verse['aya'];
    $verseIndexes[$key] = (int) $verse['index'];
}

$upsert = $conn->prepare(
    'INSERT INTO quran.verse_translations (quran_text_index, source_code, translation)
    VALUES (?, ?, ?)
    ON CONFLICT (quran_text_index, source_code)
    DO UPDATE SET translation = EXCLUDED.translation'
);

try {
    $conn->beginTransaction();

    foreach ($sourceFiles as $sourceCode => $path) {
        if (!is_file($path)) {
            throw new RuntimeException("Translation file not found: {$path}");
        }

        $seen = [];
        foreach (file($path, FILE_IGNORE_NEW_LINES) as $line) {
            $parts = explode('|', rtrim($line, "\r\n"), 3);
            if (count($parts) !== 3 || !ctype_digit($parts[0]) || !ctype_digit($parts[1])) {
                continue;
            }

            $key = (int) $parts[0] . '|' . (int) $parts[1];
            if (isset($seen[$key])) {
                throw new RuntimeException("Duplicate verse {$key} in {$path}");
            }
            if (!isset($verseIndexes[$key])) {
                throw new RuntimeException("Unknown verse {$key} in {$path}");
            }

            $upsert->execute([$verseIndexes[$key], $sourceCode, $parts[2]]);
            $seen[$key] = true;
        }

        if (count($seen) !== count($verseIndexes)) {
            throw new RuntimeException("Incomplete verse coverage in {$path}");
        }

        printf("%s: imported %d verses\n", $sourceCode, count($seen));
    }

    $conn->commit();
} catch (Throwable $error) {
    if ($conn->inTransaction()) {
        $conn->rollBack();
    }
    fwrite(STDERR, $error->getMessage() . PHP_EOL);
    exit(1);
}