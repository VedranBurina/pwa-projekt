<?php

require_once __DIR__ . '/includes/config.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$conn = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if (!$conn) {
    exit('Greška pri spajanju na bazu: ' . htmlspecialchars(mysqli_connect_error()));
}

mysqli_set_charset($conn, 'utf8mb4');
