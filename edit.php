<?php

require_once __DIR__ . '/includes/functions.php';

require_admin();

$articleId = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$article = fetch_article($conn, $articleId);

if (!$article) {
    exit('Članak nije pronađen.');
}

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $subtitle = trim($_POST['subtitle'] ?? '');
    $summary = trim($_POST['summary'] ?? '');
    $content = trim($_POST['content'] ?? '');
    $category = normalize_category($_POST['category'] ?? 'politika');
    $publishedAtInput = trim($_POST['published_at'] ?? '');
    $rating = max(1, min(5, (int) ($_POST['rating'] ?? 1)));
    $showOnHomepage = isset($_POST['show_on_homepage']) ? 1 : 0;

    $input = [
        'title' => $title,
        'summary' => $summary,
        'content' => $content,
        'category' => $category,
        'published_at' => $publishedAtInput,
    ];

    $errors = validate_article_input($input, true);

    if (!$errors) {
        try {
            $imagePath = handle_image_upload('image', $article['image_path']);
            $publishedAt = date('Y-m-d H:i:s', strtotime($publishedAtInput));

            $statement = mysqli_prepare(
                $conn,
                'UPDATE articles
                 SET title = ?, subtitle = ?, summary = ?, content = ?, category = ?, image_path = ?, published_at = ?, rating = ?, show_on_homepage = ?
                 WHERE id = ?'
            );
            mysqli_stmt_bind_param(
                $statement,
                'sssssssiii',
                $title,
                $subtitle,
                $summary,
                $content,
                $category,
                $imagePath,
                $publishedAt,
                $rating,
                $showOnHomepage,
                $articleId
            );
            mysqli_stmt_execute($statement);
            mysqli_stmt_close($statement);

            header('Location: admin.php');
            exit;
        } catch (RuntimeException $exception) {
            $errors[] = $exception->getMessage();
        }
    }

    $article['title'] = $title;
    $article['subtitle'] = $subtitle;
    $article['summary'] = $summary;
    $article['content'] = $content;
    $article['category'] = $category;
    $article['published_at'] = date('Y-m-d H:i:s', strtotime($publishedAtInput));
    $article['rating'] = $rating;
    $article['show_on_homepage'] = $showOnHomepage;
}

$pageTitle = 'Uredi članak - ' . SITE_NAME;

require_once __DIR__ . '/includes/header.php';
?>

<section class="panel panel-narrow admin-panel">
    <div class="admin-topbar admin-topbar-alt">
        <div>
            <h1>Uređivanje članka</h1>
            <p>Promijenite podatke i spremite novu verziju zapisa.</p>
        </div>
        <a href="admin.php" class="button-link button-link-light">Natrag</a>
    </div>

    <?php foreach ($errors as $error): ?>
        <div class="message error"><?= e($error) ?></div>
    <?php endforeach; ?>

    <form action="edit.php?id=<?= (int) $articleId ?>" method="post" enctype="multipart/form-data" class="admin-form">
        <label for="title">Naslov članka</label>
        <input type="text" name="title" id="title" value="<?= e($article['title']) ?>" required>

        <label for="subtitle">Kratka nadnaslovna oznaka</label>
        <input type="text" name="subtitle" id="subtitle" value="<?= e($article['subtitle']) ?>">

        <label for="summary">Kratki sadržaj / sažetak</label>
        <textarea name="summary" id="summary" rows="4" required><?= e($article['summary']) ?></textarea>

        <label for="content">Puni tekst članka</label>
        <textarea name="content" id="content" rows="10" required><?= e($article['content']) ?></textarea>

        <label for="category">Kategorija</label>
        <select name="category" id="category" required>
            <option value="politika" <?= $article['category'] === 'politika' ? 'selected' : '' ?>>Politika</option>
            <option value="sport" <?= $article['category'] === 'sport' ? 'selected' : '' ?>>Sport</option>
        </select>

        <label>Trenutna slika</label>
        <img src="<?= e(article_image_path($article)) ?>" alt="<?= e($article['title']) ?>" class="edit-preview">

        <label for="image">Nova slika (opcionalno)</label>
        <input type="file" name="image" id="image" accept=".jpg,.jpeg,.png">

        <label for="published_at">Datum objave</label>
        <input type="datetime-local" name="published_at" id="published_at" value="<?= e(date('Y-m-d\TH:i', strtotime($article['published_at']))) ?>" required>

        <label for="rating">Ocjena / broj zvjezdica</label>
        <input type="number" name="rating" id="rating" min="1" max="5" value="<?= (int) $article['rating'] ?>" required>

        <label class="checkbox-row">
            <input type="checkbox" name="show_on_homepage" value="1" <?= (int) $article['show_on_homepage'] === 1 ? 'checked' : '' ?>>
            Prikaži članak na naslovnici
        </label>

        <button type="submit">Spremi izmjene</button>
    </form>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
