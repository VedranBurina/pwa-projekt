<?php

require_once __DIR__ . '/includes/functions.php';

ensure_default_admin($conn);

if (is_logged_in() && is_admin()) {
    header('Location: admin.php');
    exit;
}

$errors = [];
$success = '';
$formData = [
    'full_name' => '',
    'username' => '',
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $formData['full_name'] = trim($_POST['full_name'] ?? '');
    $formData['username'] = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $passwordConfirm = $_POST['password_confirm'] ?? '';

    if ($formData['full_name'] === '') {
        $errors[] = 'Ime i prezime su obavezni.';
    }

    if ($formData['username'] === '') {
        $errors[] = 'Korisničko ime je obavezno.';
    } elseif (!preg_match('/^[a-zA-Z0-9_]{3,20}$/', $formData['username'])) {
        $errors[] = 'Korisničko ime mora imati 3 do 20 znakova i smije sadržavati samo slova, brojeve i donju crtu.';
    }

    if ($password === '') {
        $errors[] = 'Lozinka je obavezna.';
    } elseif (strlen($password) < 6) {
        $errors[] = 'Lozinka mora imati najmanje 6 znakova.';
    }

    if ($passwordConfirm === '') {
        $errors[] = 'Potvrda lozinke je obavezna.';
    } elseif ($password !== $passwordConfirm) {
        $errors[] = 'Lozinka i potvrda lozinke moraju biti iste.';
    }

    if (!$errors) {
        $statement = mysqli_prepare($conn, 'SELECT id FROM users WHERE username = ? LIMIT 1');
        mysqli_stmt_bind_param($statement, 's', $formData['username']);
        mysqli_stmt_execute($statement);
        $result = mysqli_stmt_get_result($statement);
        $existingUser = mysqli_fetch_assoc($result);
        mysqli_stmt_close($statement);

        if ($existingUser) {
            $errors[] = 'Odabrano korisničko ime već postoji. Molimo odaberite drugo.';
        } else {
            $username = $formData['username'];
            $passwordHash = password_hash($password, PASSWORD_DEFAULT);
            $fullName = $formData['full_name'];
            $role = 'user';

            $statement = mysqli_prepare(
                $conn,
                'INSERT INTO users (username, password, full_name, role) VALUES (?, ?, ?, ?)'
            );
            mysqli_stmt_bind_param($statement, 'ssss', $username, $passwordHash, $fullName, $role);
            mysqli_stmt_execute($statement);
            mysqli_stmt_close($statement);

            $success = 'Registracija je uspješna. Sada se možete prijaviti.';
            $formData = [
                'full_name' => '',
                'username' => '',
            ];
        }
    }
}

$pageTitle = 'Registracija - ' . SITE_NAME;

require_once __DIR__ . '/includes/header.php';
?>

<section class="auth-box">
    <h1>Registracija korisnika</h1>
    <p class="auth-hint">Ispunite formu za kreiranje novog korisničkog računa.</p>

    <?php foreach ($errors as $error): ?>
        <div class="message error"><?= e($error) ?></div>
    <?php endforeach; ?>

    <?php if ($success !== ''): ?>
        <div class="message success"><?= e($success) ?></div>
        <p class="auth-links"><a href="login.php">Povratak na prijavu</a></p>
    <?php endif; ?>

    <form action="registracija.php" method="post" class="admin-form">
        <label for="full_name">Ime i prezime</label>
        <input type="text" name="full_name" id="full_name" value="<?= e($formData['full_name']) ?>" required>

        <label for="username">Korisničko ime</label>
        <input type="text" name="username" id="username" value="<?= e($formData['username']) ?>" required>

        <label for="password">Lozinka</label>
        <input type="password" name="password" id="password" required>

        <label for="password_confirm">Potvrda lozinke</label>
        <input type="password" name="password_confirm" id="password_confirm" required>

        <button type="submit">Registriraj korisnika</button>
    </form>

    <p class="auth-links">
        Već imate račun?
        <a href="login.php">Prijavite se ovdje</a>.
    </p>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
