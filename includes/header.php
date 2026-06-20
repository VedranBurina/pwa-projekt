<?php

declare(strict_types=1);

$pageTitle = $pageTitle ?? SITE_NAME;
?>
<!DOCTYPE html>
<html lang="hr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($pageTitle) ?></title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="page-shell">
        <header class="site-header">
            <nav class="top-nav">
                <a href="index.php">HOME</a>
                <a href="index.php#politik">POLITIK</a>
                <a href="index.php#sport">SPORT</a>
                <?php if (is_logged_in()): ?>
                    <a href="admin.php">ADMINISTRACIJA</a>
                <?php else: ?>
                    <a href="login.php">ADMINISTRACIJA</a>
                <?php endif; ?>
            </nav>

            <div class="logo-wrap">
                <a class="site-logo" href="index.php"><?= e(SITE_NAME) ?></a>
            </div>
        </header>
        <main class="content-area">
