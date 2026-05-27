<?php
// =====================================================
// DATABASE CONNECTION
// =====================================================

define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'learnclidatabase');

$link = mysqli_connect(DB_HOST, DB_USER, DB_PASS);

if (!$link) {
    die("<div style='font-family:sans-serif;padding:40px;text-align:center;color:#dc2626;'>
        <h2>❌ Koneksi Database Gagal</h2>
        <p>" . mysqli_connect_error() . "</p>
    </div>");
}

if (!mysqli_select_db($link, DB_NAME)) {
    die("<div style='font-family:sans-serif;padding:40px;text-align:center;color:#dc2626;'>
        <h2>❌ Database Tidak Ditemukan</h2>
        <p>Database <strong>" . DB_NAME . "</strong> tidak ada. Pastikan sudah diimport.</p>
    </div>");
}

mysqli_set_charset($link, 'utf8');
