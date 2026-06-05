<?php
// =====================================================
// SWITCH ACCOUNT — handles account switching from modal
// =====================================================

if (session_status() === PHP_SESSION_NONE) session_start();

$redirect_hal = trim($_POST['redirect_hal'] ?? 'home');
// Sanitize: only allow known page keys
$allowed_pages = ['home','mahasiswa','dosen','dopem','matakuliah','querynilai','anggota'];
if (!in_array($redirect_hal, $allowed_pages)) {
    $redirect_hal = 'home';
}

$username = trim($_POST['username'] ?? '');
$password = $_POST['password'] ?? '';

$valid_users = [
    'admin'      => ['password' => 'admin123',    'role' => 'admin'],
    'memberten'  => ['password' => 'baseten123',  'role' => 'member'],
];

if (isset($valid_users[$username]) && $valid_users[$username]['password'] === $password) {
    // Destroy old session, start fresh
    $_SESSION = [];
    session_destroy();
    session_start();
    $_SESSION['logged_in'] = true;
    $_SESSION['username']  = $username;
    $_SESSION['role']      = $valid_users[$username]['role'];
    header('Location: index.php?hal=' . urlencode($redirect_hal));
    exit;
}

// Failed: redirect back
header('Location: index.php?hal=' . urlencode($redirect_hal) . '&switch_error=1');
exit;
