<?php

require_once __DIR__ . '/includes/functions.php';

ensure_default_admin($conn);

if (is_logged_in() && is_admin()) {
    header('Location: admin.php');
    exit;
}

$error = '';
$showRegistrationLink = false;
$formUsername = '';
$accessDeniedMessage = '';

if (isset($_GET['access']) && $_GET['access'] === 'denied' && is_logged_in() && !is_admin()) {
    $name = $_SESSION['full_name'] ?? $_SESSION['username'];
    $accessDeniedMessage = 'Korisnik ' . $name . ' nema pravo pristupa administratorskoj stranici.';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $formUsername = $username;

    if ($username === '' || $password === '') {
        $error = 'Korisničko ime i lozinka su obavezni.';
    } else {
        $statement = mysqli_prepare($conn, 'SELECT * FROM users WHERE username = ? LIMIT 1');
        mysqli_stmt_bind_param($statement, 's', $username);
        mysqli_stmt_execute($statement);
        $result = mysqli_stmt_get_result($statement);
        $user = mysqli_fetch_assoc($result);
        mysqli_stmt_close($statement);

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = (int) $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['full_name'] = $user['full_name'];
            $_SESSION['role'] = $user['role'];

            if ($user['role'] === 'admin') {
                header('Location: admin.php');
                exit;
            }

            $accessDeniedMessage = 'Korisnik ' . $user['full_name'] . ' nema pravo pristupa administratorskoj stranici.';
        } else {
            $error = 'Nije uneseno ispravno korisničko ime i/ili lozinka. Potrebno se prvo registrirati.';
            $showRegistrationLink = true;
        }
    }
}

$pageTitle = 'Prijava - ' . SITE_NAME;

require_once __DIR__ . '/includes/header.php';
?>

<section class="auth-box">
    <h1>Administracija</h1>
    <p class="auth-hint">Zadani korisnik: <strong>admin</strong> / <strong>admin1234</strong></p>

    <?php if ($error !== ''): ?>
        <div class="message error"><?= e($error) ?></div>
    <?php endif; ?>

    <?php if ($accessDeniedMessage !== ''): ?>
        <div class="message warning"><?= e($accessDeniedMessage) ?></div>
    <?php endif; ?>

    <?php if ($showRegistrationLink): ?>
        <p class="auth-links">
            Nemate korisnički račun?
            <a href="registracija.php">Otvorite formu za registraciju</a>.
        </p>
    <?php endif; ?>

    <form action="login.php" method="post" class="admin-form">
        <label for="username">Korisničko ime</label>
        <input type="text" name="username" id="username" value="<?= e($formUsername) ?>" required>

        <label for="password">Lozinka</label>
        <input type="password" name="password" id="password" required>

        <button type="submit">Prijavi se</button>
    </form>

    <p class="auth-links">
        Novi korisnik?
        <a href="registracija.php">Registrirajte se ovdje</a>.
    </p>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
