<?php

require_once __DIR__ . '/includes/functions.php';

require_admin();

$articleId = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$article = fetch_article($conn, $articleId);

if ($article) {
    delete_uploaded_image($article['image_path']);

    $statement = mysqli_prepare($conn, 'DELETE FROM articles WHERE id = ?');
    mysqli_stmt_bind_param($statement, 'i', $articleId);
    mysqli_stmt_execute($statement);
    mysqli_stmt_close($statement);
}

header('Location: admin.php');
exit;
