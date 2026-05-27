<?php
// =====================================================
// LOGIN PAGE
// =====================================================

if (session_status() === PHP_SESSION_NONE) session_start();

// Already logged in → redirect to dashboard
if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) {
    header('Location: index.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    // Simple credential check — change these as needed
    $valid_users = [
        'admin'      => 'admin123',
        'kelompok10' => 'baseten2026',
    ];

    if (isset($valid_users[$username]) && $valid_users[$username] === $password) {
        $_SESSION['logged_in'] = true;
        $_SESSION['username']  = $username;
        header('Location: index.php');
        exit;
    } else {
        $error = 'Username atau password salah.';
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login — BasisData2026</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body class="login-body">

<div class="login-wrapper">

    <div class="login-card">

        <div class="login-header">
            <img src="gambar/unida.png" alt="Logo UNIDA" class="login-logo">
            <h1>BasisData<span>2026</span></h1>
            <p>Universitas Djuanda &mdash; Kelompok 10</p>
        </div>

        <?php if ($error): ?>
            <div class="alert alert-error">
                <span>&#9888;</span> <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="login.php" class="login-form">
            <div class="form-group">
                <label for="username">Username</label>
                <div class="input-icon-wrap">
                    <span class="input-icon">&#128100;</span>
                    <input type="text" id="username" name="username"
                           placeholder="Masukkan username"
                           value="<?= htmlspecialchars($_POST['username'] ?? '') ?>"
                           required autofocus>
                </div>
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <div class="input-icon-wrap">
                    <span class="input-icon">&#128274;</span>
                    <input type="password" id="password" name="password"
                           placeholder="Masukkan password"
                           required>
                    <button type="button" class="toggle-pw" onclick="togglePw()" title="Tampilkan password">
                        &#128065;
                    </button>
                </div>
            </div>

            <button type="submit" class="btn-login">Masuk</button>
        </form>

        <div class="login-hint">
            <!-- <small>Default: <code>admin</code> / <code>admin123</code></small> -->
        </div>

    </div>

    <p class="login-footer">TheBaseTen &copy; 2026 &mdash; Sistem Basis Data MySQL</p>

</div>

<script>
function togglePw() {
    var pw = document.getElementById('password');
    pw.type = pw.type === 'password' ? 'text' : 'password';
}
</script>

</body>
</html>
