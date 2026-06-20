<?php

require_once __DIR__ . '/includes/functions.php';

$articleId = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$article = fetch_article($conn, $articleId);

if (!$article) {
    http_response_code(404);
    exit('Članak nije pronađen.');
}

$pageTitle = $article['title'] . ' - ' . SITE_NAME;

require_once __DIR__ . '/includes/header.php';
?>

<article class="article-page">
    <div class="article-header">
        <div class="article-category"><?= e(category_label($article['category'])) ?></div>
        <h1><?= e($article['title']) ?></h1>
        <div class="article-date">AŽURIRANO <?= e(format_date_local($article['published_at'])) ?></div>
    </div>

    <img src="<?= e(article_image_path($article)) ?>" alt="<?= e($article['title']) ?>" class="article-hero">

    <div class="article-body">
        <p class="article-summary article-summary-large"><?= e($article['summary']) ?></p>

        <?php
        $paragraphs = preg_split('/\r\n|\r|\n/', trim((string) $article['content'])) ?: [];
        foreach ($paragraphs as $index => $paragraph):
            if (trim($paragraph) === '') {
                continue;
            }
        ?>
            <p class="<?= $index === 0 ? 'drop-cap' : '' ?>"><?= nl2br(e($paragraph)) ?></p>
        <?php endforeach; ?>
    </div>
</article>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
