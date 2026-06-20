<?php

require_once __DIR__ . '/includes/functions.php';

ensure_default_admin($conn);

$politikaArticles = fetch_homepage_articles($conn, 'politika');
$sportArticles = fetch_homepage_articles($conn, 'sport');
$pageTitle = 'Naslovnica - ' . SITE_NAME;

require_once __DIR__ . '/includes/header.php';
?>

<section class="news-section" id="politik">
    <div class="section-label">
        <span class="section-line"></span>
        <h2>POLITIK</h2>
    </div>

    <div class="cards-grid">
        <?php foreach ($politikaArticles as $article): ?>
            <article class="news-card">
                <a href="clanak.php?id=<?= (int) $article['id'] ?>" class="card-image-link">
                    <img src="<?= e(article_image_path($article)) ?>" alt="<?= e($article['title']) ?>" class="card-image">
                </a>
                <div class="card-meta"><?= e(card_meta_text($article)) ?></div>
                <h3 class="card-title">
                    <a href="clanak.php?id=<?= (int) $article['id'] ?>"><?= e($article['title']) ?></a>
                </h3>
                <p class="card-summary"><?= e($article['summary']) ?></p>
                <div class="card-footer">
                    <span><?= e(time_ago($article['published_at'])) ?></span>
                    <span>&#9733; <?= (int) $article['rating'] ?></span>
                </div>
            </article>
        <?php endforeach; ?>
    </div>
</section>

<section class="news-section" id="sport">
    <div class="section-label">
        <span class="section-line"></span>
        <h2>SPORT</h2>
    </div>

    <div class="cards-grid">
        <?php foreach ($sportArticles as $article): ?>
            <article class="news-card">
                <a href="clanak.php?id=<?= (int) $article['id'] ?>" class="card-image-link">
                    <img src="<?= e(article_image_path($article)) ?>" alt="<?= e($article['title']) ?>" class="card-image">
                </a>
                <div class="card-meta"><?= e(card_meta_text($article)) ?></div>
                <h3 class="card-title">
                    <a href="clanak.php?id=<?= (int) $article['id'] ?>"><?= e($article['title']) ?></a>
                </h3>
                <p class="card-summary"><?= e($article['summary']) ?></p>
                <div class="card-footer">
                    <span><?= e(time_ago($article['published_at'])) ?></span>
                    <span>&#9733; <?= (int) $article['rating'] ?></span>
                </div>
            </article>
        <?php endforeach; ?>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
