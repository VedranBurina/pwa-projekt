<?php

require_once __DIR__ . '/../db.php';

function e($value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function is_logged_in(): bool
{
    return isset($_SESSION['user_id'], $_SESSION['username']);
}

function is_admin(): bool
{
    return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}

function require_login(): void
{
    if (!is_logged_in()) {
        header('Location: login.php');
        exit;
    }
}

function require_admin(): void
{
    require_login();

    if (!is_admin()) {
        header('Location: login.php?access=denied');
        exit;
    }
}

function ensure_default_admin(mysqli $conn): void
{
    $result = mysqli_query($conn, 'SELECT COUNT(*) AS total FROM users');
    $row = mysqli_fetch_assoc($result);

    if ((int) $row['total'] === 0) {
        $username = 'admin';
        $password = password_hash('admin1234', PASSWORD_DEFAULT);
        $fullName = 'Administrator';
        $role = 'admin';

        $statement = mysqli_prepare(
            $conn,
            'INSERT INTO users (username, password, full_name, role) VALUES (?, ?, ?, ?)'
        );
        mysqli_stmt_bind_param($statement, 'ssss', $username, $password, $fullName, $role);
        mysqli_stmt_execute($statement);
        mysqli_stmt_close($statement);
    }
}

function normalize_category(string $category): string
{
    return strtolower(trim($category)) === 'sport' ? 'sport' : 'politika';
}

function category_label(string $category): string
{
    return $category === 'sport' ? 'SPORT' : 'POLITIK';
}

function card_meta_text(array $article): string
{
    return strtoupper(trim((string) ($article['subtitle'] ?? '')));
}

function format_date_local(string $date): string
{
    return date('d.m.Y. H:i', strtotime($date));
}

function time_ago(string $date): string
{
    $seconds = time() - strtotime($date);

    if ($seconds < 3600) {
        return 'prije ' . max(1, floor($seconds / 60)) . ' min';
    }

    if ($seconds < 86400) {
        return 'prije ' . floor($seconds / 3600) . ' h';
    }

    return 'prije ' . floor($seconds / 86400) . ' dana';
}

function fetch_homepage_articles(mysqli $conn, string $category): array
{
    $category = normalize_category($category);
    $articles = [];

    $statement = mysqli_prepare(
        $conn,
        'SELECT * FROM articles
         WHERE category = ? AND show_on_homepage = 1
         ORDER BY published_at DESC, id DESC
         LIMIT 3'
    );
    mysqli_stmt_bind_param($statement, 's', $category);
    mysqli_stmt_execute($statement);
    $result = mysqli_stmt_get_result($statement);

    while ($row = mysqli_fetch_assoc($result)) {
        $articles[] = $row;
    }

    mysqli_stmt_close($statement);
    return $articles;
}

function fetch_all_articles(mysqli $conn): array
{
    $articles = [];
    $result = mysqli_query($conn, 'SELECT * FROM articles ORDER BY published_at DESC, id DESC');

    while ($row = mysqli_fetch_assoc($result)) {
        $articles[] = $row;
    }

    return $articles;
}

function fetch_article(mysqli $conn, int $id): ?array
{
    $statement = mysqli_prepare($conn, 'SELECT * FROM articles WHERE id = ?');
    mysqli_stmt_bind_param($statement, 'i', $id);
    mysqli_stmt_execute($statement);
    $result = mysqli_stmt_get_result($statement);
    $article = mysqli_fetch_assoc($result) ?: null;
    mysqli_stmt_close($statement);

    return $article;
}

function article_image_path(array $article): string
{
    return !empty($article['image_path']) ? $article['image_path'] : 'images/default-news.svg';
}

function validate_article_input(array $data, bool $isEdit = false): array
{
    $errors = [];

    if (trim($data['title'] ?? '') === '') {
        $errors[] = 'Naslov članka je obavezan.';
    }

    if (trim($data['summary'] ?? '') === '') {
        $errors[] = 'Kratki sadržaj je obavezan.';
    }

    if (trim($data['content'] ?? '') === '') {
        $errors[] = 'Puni tekst članka je obavezan.';
    }

    if (!in_array($data['category'] ?? '', ['politika', 'sport'], true)) {
        $errors[] = 'Kategorija mora biti politika ili sport.';
    }

    if (trim($data['published_at'] ?? '') === '' || strtotime((string) $data['published_at']) === false) {
        $errors[] = 'Datum objave nije ispravan.';
    }

    if (!$isEdit && empty($_FILES['image']['name'])) {
        $errors[] = 'Slika članka je obavezna.';
    }

    return $errors;
}

function handle_image_upload(string $fieldName, ?string $currentImage = null): ?string
{
    if (empty($_FILES[$fieldName]['name'])) {
        return $currentImage;
    }

    if (!isset($_FILES[$fieldName]['error']) || $_FILES[$fieldName]['error'] !== UPLOAD_ERR_OK) {
        throw new RuntimeException('Greška prilikom učitavanja slike.');
    }

    $allowedTypes = [
        'image/jpeg' => 'jpg',
        'image/png' => 'png',
    ];

    $info = finfo_open(FILEINFO_MIME_TYPE);
    $mimeType = finfo_file($info, $_FILES[$fieldName]['tmp_name']);
    finfo_close($info);

    if (!isset($allowedTypes[$mimeType])) {
        throw new RuntimeException('Dozvoljene su samo JPG, JPEG i PNG slike.');
    }

    $fileName = uniqid('article_', true) . '.' . $allowedTypes[$mimeType];
    $target = __DIR__ . '/../uploads/' . $fileName;

    if (!move_uploaded_file($_FILES[$fieldName]['tmp_name'], $target)) {
        throw new RuntimeException('Slika nije uspješno spremljena.');
    }

    if ($currentImage && str_starts_with($currentImage, 'uploads/')) {
        $oldFile = __DIR__ . '/../' . $currentImage;
        if (file_exists($oldFile)) {
            unlink($oldFile);
        }
    }

    return 'uploads/' . $fileName;
}

function delete_uploaded_image(?string $imagePath): void
{
    if ($imagePath && str_starts_with($imagePath, 'uploads/')) {
        $file = __DIR__ . '/../' . $imagePath;
        if (file_exists($file)) {
            unlink($file);
        }
    }
}
