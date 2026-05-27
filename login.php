<?php
// =====================================================
// LOGIN PAGE — with role-based access (Admin / Member / Guest)
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

    // Users with roles: admin = full CRUD, member = view only
    $valid_users = [
        'admin'      => ['password' => 'admin123',    'role' => 'admin'],
        'memberten' => ['password' => 'baseten123', 'role' => 'member'],
    ];

    if (isset($valid_users[$username]) && $valid_users[$username]['password'] === $password) {
        $_SESSION['logged_in'] = true;
        $_SESSION['username']  = $username;
        $_SESSION['role']      = $valid_users[$username]['role'];
        header('Location: index.php');
        exit;
    } else {
        $error = 'Username atau password salah.';
    }
}

// Guest login — no POST needed, just redirect with guest session
if (isset($_GET['guest'])) {
    $_SESSION['logged_in'] = true;
    $_SESSION['username']  = 'Guest';
    $_SESSION['role']      = 'guest';
    header('Location: index.php');
    exit;
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
            <div class="login-logo-wrap">
                <img src="gambar/unida.png" alt="Logo UNIDA" class="login-logo">
            </div>
            <h1>BasisData<span>2026</span></h1>
            <p>Universitas Djuanda &mdash; Kelompok 10</p>
        </div>

        <!-- Role tabs -->
        <div class="role-tabs">
            <button class="role-tab active" onclick="switchTab('admin')" id="tab-admin">
                <span>🛡️</span> Admin
            </button>
            <button class="role-tab" onclick="switchTab('member')" id="tab-member">
                <span>👤</span> Member
            </button>
        </div>

        <?php if ($error): ?>
            <div class="alert alert-error">
                <span>&#9888;</span> <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <!-- Admin hint -->
        <!-- <div class="role-hint" id="hint-admin">
            <span>🛡️</span> Login sebagai <strong>Admin</strong> untuk akses penuh (CRUD data)
        </div>
        <div class="role-hint" id="hint-member" style="display:none">
            <span>👁️</span> Login sebagai <strong>Member</strong> untuk akses view / review saja
        </div> -->

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

            <button type="submit" class="btn-login">
                <span>🔓</span> Masuk
            </button>
        </form>

        <div class="login-divider"><span>atau</span></div>

        <div class="guest-section">
            <a href="login.php?guest=1" class="btn-guest">
                <span>👁️</span> Lanjut sebagai Guest
            </a>
            <p class="guest-note">Guest hanya bisa melihat halaman utama</p>
        </div>

    </div>

    <p class="login-footer">TheBaseTen &copy; 2026 &mdash; Sistem Basis Data MySQL</p>

</div>

<script>
function togglePw() {
    var pw = document.getElementById('password');
    pw.type = pw.type === 'password' ? 'text' : 'password';
}

function switchTab(role) {
    document.querySelectorAll('.role-tab').forEach(function(t) { t.classList.remove('active'); });
    document.getElementById('tab-' + role).classList.add('active');

    document.querySelectorAll('.role-hint').forEach(function(h) { h.style.display = 'none'; });
    document.getElementById('hint-' + role).style.display = 'flex';
}
</script>

</body>
</html>
