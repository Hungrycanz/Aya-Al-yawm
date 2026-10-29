<?php
include('server.php');
include('functions.php');
$hasSearch = $chapter !== '' && $verse !== '';
$chapterValue = is_scalar($chapter) ? htmlspecialchars((string) $chapter, ENT_QUOTES, 'UTF-8') : '';
$verseValue = is_scalar($verse) ? htmlspecialchars((string) $verse, ENT_QUOTES, 'UTF-8') : '';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#f1f4ec">
    <title>Aya | Quran Lookup</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="site-shell">
        <header class="masthead">
            <a class="wordmark" href="index.php" aria-label="Aya home">
                <span class="wordmark-mark" aria-hidden="true">۞</span>
                <span class="wordmark-name">AYA<small>QURAN INDEX</small></span>
            </a>
            <span class="masthead-note">A DAILY VERSE</span>
        </header>

        <main>
            <section class="lookup" aria-label="Quran verse lookup">
                <div class="lookup-controls">
                    <p class="eyebrow">THE QURAN, BY REFERENCE</p>
                    <h1>Find a verse<span>.</span></h1>

                    <form class="lookup-form" action="index.php" method="GET">
                        <label class="field" for="chapter-number">
                            <span>Surah</span>
                            <input id="chapter-number" name="chapter_number" type="number" min="1" max="114" inputmode="numeric" placeholder="2" value="<?= $chapterValue ?>" required>
                        </label>
                        <label class="field" for="verse-number">
                            <span>Ayah</span>
                            <input id="verse-number" name="verse_number" type="number" min="1" max="286" inputmode="numeric" placeholder="255" value="<?= $verseValue ?>" required>
                        </label>
                        <button class="submit-button" type="submit">Find ayah <span aria-hidden="true">&#8594;</span></button>
                    </form>

                    <nav class="quick-references" aria-label="Quick references">
                        <p class="quick-heading">Quick references</p>
                        <a href="?chapter_number=2&amp;verse_number=255"><span>Al-Baqarah</span><span>2:255</span></a>
                        <a href="?chapter_number=96&amp;verse_number=1"><span>Al-'Alaq</span><span>96:1</span></a>
                        <a href="?chapter_number=114&amp;verse_number=6"><span>An-Nas</span><span>114:6</span></a>
                    </nav>
                </div>

                <section class="verse-panel" aria-live="polite" aria-atomic="true" aria-label="Verse result">
                    <div class="panel-heading"><span>VERSE</span><span aria-hidden="true">01</span></div>
                    <?php if ($hasSearch && $row): ?>
                        <p class="verse-reference">SURAH <?= $chapterValue ?> <span>/</span> AYAH <?= $verseValue ?></p>
                        <p class="verse-text" lang="ar" dir="rtl"><?= htmlspecialchars((string) $row['verse'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?></p>
                        <div class="translation-list" aria-label="Translations">
                            <section class="translation" lang="en">
                                <p class="translation-label">ENGLISH · SAHEEH INTERNATIONAL · TANZIL.NET</p>
                                <p class="translation-text"><?= htmlspecialchars((string) $row['english_translation'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?></p>
                            </section>
                            <section class="translation" lang="sw">
                                <p class="translation-label">KISWAHILI · ALI MUHSIN AL-BARWANI · TANZIL.NET</p>
                                <p class="translation-text"><?= htmlspecialchars((string) $row['swahili_translation'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?></p>
                            </section>
                        </div>
                    <?php elseif ($hasSearch): ?>
                        <div class="empty-state">
                            <span class="empty-mark" aria-hidden="true">&#8709;</span>
                            <h2>No verse found</h2>
                            <p>Check the reference and try again.</p>
                        </div>
                    <?php else: ?>
                        <div class="empty-state">
                            <span class="empty-mark" aria-hidden="true">۞</span>
                            <p class="empty-kicker">YOUR NEXT AYAH</p>
                            <p class="empty-copy">Choose a reference to begin.</p>
                        </div>
                    <?php endif; ?>
                </section>
            </section>

            <section class="daily-verse" aria-labelledby="daily-verse-heading">
                <div class="daily-verse-meta">
                    <p class="eyebrow">FIXED FOR TODAY</p>
                    <h2 id="daily-verse-heading">Aya of the day</h2>
                    <?php if ($randomVerse): ?>
                        <p class="daily-reference">SURAH <?= (int) $randomVerse['sura'] ?> <span>/</span> AYAH <?= (int) $randomVerse['aya'] ?></p>
                    <?php endif; ?>
                </div>
                <?php if ($randomVerse): ?>
                    <p class="daily-verse-text" lang="ar" dir="rtl"><?= htmlspecialchars((string) $randomVerse['verse'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?></p>
                    <div class="translation-list daily-translations" aria-label="Translations">
                        <section class="translation" lang="en">
                            <p class="translation-label">ENGLISH · SAHEEH INTERNATIONAL · TANZIL.NET</p>
                            <p class="translation-text"><?= htmlspecialchars((string) $randomVerse['english_translation'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?></p>
                        </section>
                        <section class="translation" lang="sw">
                            <p class="translation-label">KISWAHILI · ALI MUHSIN AL-BARWANI · TANZIL.NET</p>
                            <p class="translation-text"><?= htmlspecialchars((string) $randomVerse['swahili_translation'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?></p>
                        </section>
                    </div>
                <?php else: ?>
                    <p class="daily-verse-text daily-verse-unavailable">This year's verse schedule has not been generated yet.</p>
                <?php endif; ?>
            </section>
        </main>

        <footer class="page-footer">
            <span>AYA AL-YAWM</span>
            <span>QURAN TEXT</span>
        </footer>
    </div>
</body>
</html>