<?php

require_once __DIR__ . '/includes/functions.php';

require_admin();

$errors = [];
$success = '';
$formData = [
    'title' => '',
    'subtitle' => '',
    'summary' => '',
    'content' => '',
    'category' => 'politika',
    'published_at' => date('Y-m-d\TH:i'),
    'rating' => 5,
    'show_on_homepage' => 1,
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $formData['title'] = trim($_POST['title'] ?? '');
    $formData['subtitle'] = trim($_POST['subtitle'] ?? '');
    $formData['summary'] = trim($_POST['summary'] ?? '');
    $formData['content'] = trim($_POST['content'] ?? '');
    $formData['category'] = normalize_category($_POST['category'] ?? 'politika');
    $formData['published_at'] = trim($_POST['published_at'] ?? '');
    $formData['rating'] = max(1, min(5, (int) ($_POST['rating'] ?? 1)));
    $formData['show_on_homepage'] = isset($_POST['show_on_homepage']) ? 1 : 0;

    $errors = validate_article_input($formData);

    if (!$errors) {
        try {
            $imagePath = handle_image_upload('image');
            $title = $formData['title'];
            $subtitle = $formData['subtitle'];
            $summary = $formData['summary'];
            $content = $formData['content'];
            $category = $formData['category'];
            $publishedAt = date('Y-m-d H:i:s', strtotime($formData['published_at']));
            $rating = $formData['rating'];
            $showOnHomepage = $formData['show_on_homepage'];

            $statement = mysqli_prepare(
                $conn,
                'INSERT INTO articles
                (title, subtitle, summary, content, category, image_path, published_at, rating, show_on_homepage)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)'
            );
            mysqli_stmt_bind_param(
                $statement,
                'sssssssii',
                $title,
                $subtitle,
                $summary,
                $content,
                $category,
                $imagePath,
                $publishedAt,
                $rating,
                $showOnHomepage
            );
            mysqli_stmt_execute($statement);
            mysqli_stmt_close($statement);

            $success = 'Članak je uspješno spremljen.';
            $formData = [
                'title' => '',
                'subtitle' => '',
                'summary' => '',
                'content' => '',
                'category' => 'politika',
                'published_at' => date('Y-m-d\TH:i'),
                'rating' => 5,
                'show_on_homepage' => 1,
            ];
        } catch (RuntimeException $exception) {
            $errors[] = $exception->getMessage();
        }
    }
}

$articles = fetch_all_articles($conn);
$pageTitle = 'Administracija - ' . SITE_NAME;

require_once __DIR__ . '/includes/header.php';
?>

<section class="admin-layout">
    <div class="admin-topbar admin-topbar-alt">
        <div>
            <h1>Administracija portala</h1>
            <p>Prijavljeni ste kao <?= e($_SESSION['username']) ?>.</p>
        </div>
        <a href="logout.php" class="button-link button-link-light">Logout</a>
    </div>

    <?php foreach ($errors as $error): ?>
        <div class="message error"><?= e($error) ?></div>
    <?php endforeach; ?>

    <?php if ($success !== ''): ?>
        <div class="message success"><?= e($success) ?></div>
    <?php endif; ?>

    <div class="admin-grid">
        <section class="panel admin-panel">
            <h2>Unos novog članka</h2>
            <form action="admin.php" method="post" enctype="multipart/form-data" class="admin-form">
                <label for="title">Naslov članka</label>
                <input type="text" name="title" id="title" value="<?= e($formData['title']) ?>" required>

                <label for="subtitle">Kratka nadnaslovna oznaka</label>
                <input type="text" name="subtitle" id="subtitle" value="<?= e($formData['subtitle']) ?>" placeholder="npr. Večernje izdanje">

                <label for="summary">Kratki sadržaj / sažetak</label>
                <textarea name="summary" id="summary" rows="4" required><?= e($formData['summary']) ?></textarea>

                <label for="content">Puni tekst članka</label>
                <textarea name="content" id="content" rows="10" required><?= e($formData['content']) ?></textarea>

                <label for="category">Kategorija</label>
                <select name="category" id="category" required>
                    <option value="politika" <?= $formData['category'] === 'politika' ? 'selected' : '' ?>>Politika</option>
                    <option value="sport" <?= $formData['category'] === 'sport' ? 'selected' : '' ?>>Sport</option>
                </select>

                <label for="image">Slika članka (JPG ili PNG)</label>
                <input type="file" name="image" id="image" accept=".jpg,.jpeg,.png" required>

                <label for="published_at">Datum objave</label>
                <input type="datetime-local" name="published_at" id="published_at" value="<?= e($formData['published_at']) ?>" required>

                <label for="rating">Ocjena / broj zvjezdica</label>
                <input type="number" name="rating" id="rating" min="1" max="5" value="<?= (int) $formData['rating'] ?>" required>

                <label class="checkbox-row">
                    <input type="checkbox" name="show_on_homepage" value="1" <?= (int) $formData['show_on_homepage'] === 1 ? 'checked' : '' ?>>
                    Prikaži članak na naslovnici
                </label>

                <button type="submit">Spremi članak</button>
            </form>
        </section>

        <section class="panel admin-panel admin-panel-list">
            <h2>Popis članaka</h2>

            <?php if (!$articles): ?>
                <p>Trenutno nema spremljenih članaka.</p>
            <?php endif; ?>

            <div class="article-table-wrap">
                <table class="article-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Naslov</th>
                            <th>Kategorija</th>
                            <th>Datum</th>
                            <th>Naslovnica</th>
                            <th>Akcije</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($articles as $article): ?>
                            <tr>
                                <td><?= (int) $article['id'] ?></td>
                                <td><?= e($article['title']) ?></td>
                                <td><?= e(category_label($article['category'])) ?></td>
                                <td><?= e(format_date_local($article['published_at'])) ?></td>
                                <td><?= (int) $article['show_on_homepage'] === 1 ? 'Da' : 'Ne' ?></td>
                                <td class="actions">
                                    <a href="clanak.php?id=<?= (int) $article['id'] ?>" class="action-view">Prikaži</a>
                                    <a href="edit.php?id=<?= (int) $article['id'] ?>" class="action-edit">Uredi</a>
                                    <a href="delete.php?id=<?= (int) $article['id'] ?>" class="action-delete" onclick="return confirm('Jeste li sigurni da želite obrisati članak?');">Obriši</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </section>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
