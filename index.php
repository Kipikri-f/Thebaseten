<?php
// =====================================================
// INDEX — Main Application Shell
// =====================================================

require_once 'includes/auth.php';
require_once 'includes/helpers.php';

$hal = isset($_GET['hal']) ? $_GET['hal'] : 'home';

$pages = [
    'home'       => 'pages/home.php',
    'mahasiswa'  => 'pages/mahasiswa.php',
    'dosen'      => 'pages/dosen.php',
    'dopem'      => 'pages/dopem.php',
    'matakuliah' => 'pages/matakuliah.php',
    'querynilai' => 'pages/querynilai.php',
    'anggota'    => 'pages/anggota.php',
];

$page_file = $pages[$hal] ?? $pages['home'];

$nav_items = [
    'home'       => ['icon' => '🏠', 'label' => 'Home'],
    'mahasiswa'  => ['icon' => '🎓', 'label' => 'Mahasiswa'],
    'dosen'      => ['icon' => '📋', 'label' => 'Dosen'],
    'dopem'      => ['icon' => '👨‍🏫', 'label' => 'Dosen Pembimbing'],
    'matakuliah' => ['icon' => '📚', 'label' => 'Mata Kuliah'],
    'querynilai' => ['icon' => '📊', 'label' => 'Nilai Mahasiswa'],
    'anggota'    => ['icon' => '👥', 'label' => 'Anggota Kelompok'],
];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TheBaseTen &mdash; Sistem Basis Data</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>

<!-- ===== TOPBAR ===== -->
<header class="topbar">
    <button class="hamburger" id="hamburgerBtn" onclick="toggleSidebar()" aria-label="Toggle Menu">
        <span></span><span></span><span></span>
    </button>

    <div class="topbar-brand">
        <img src="gambar/unida.png" alt="Logo UNIDA" class="topbar-logo">
        <div>
            <h1>BasisData2026</h1>
            <span>Universitas Djuanda</span>
        </div>
    </div>

    <div class="topbar-right">
        <span class="topbar-date"><?= getIndonesianDate() ?></span>
        <div class="user-menu">
            <span class="user-pill">
                <span>&#128100;</span>
                <?= htmlspecialchars($_SESSION['username'] ?? 'User') ?>
            </span>
            <a href="logout.php" class="btn-logout" onclick="return confirm('Keluar dari aplikasi?')">
                &#128275; Logout
            </a>
        </div>
    </div>
</header>

<!-- ===== OVERLAY ===== -->
<div class="sidebar-overlay" id="sidebarOverlay" onclick="toggleSidebar()"></div>

<!-- ===== LAYOUT ===== -->
<div class="app-layout">

    <!-- SIDEBAR -->
    <aside class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <span class="nav-label">Navigation</span>
        </div>
        <nav>
            <?php foreach ($nav_items as $key => $item): ?>
                <a href="index.php?hal=<?= $key ?>"
                   class="nav-link <?= ($hal === $key) ? 'active' : '' ?>">
                    <span class="nav-icon"><?= $item['icon'] ?></span>
                    <span><?= $item['label'] ?></span>
                </a>
            <?php endforeach; ?>
        </nav>
        <div class="sidebar-footer">
            <a href="logout.php" class="nav-link nav-logout"
               onclick="return confirm('Keluar dari aplikasi?')">
                <span class="nav-icon">🚪</span>
                <span>Logout</span>
            </a>
        </div>
    </aside>

    <!-- MAIN CONTENT -->
    <main class="main-content">
        <?php require_once $page_file; ?>
    </main>

</div>

<!-- ===== FOOTER ===== -->
<footer class="app-footer">
    TheBaseTen &copy; 2026 &mdash; Kelompok 10 &middot; Universitas Djuanda
</footer>

<script>
function toggleSidebar() {
    const sidebar  = document.getElementById('sidebar');
    const overlay  = document.getElementById('sidebarOverlay');
    const isOpen   = sidebar.classList.contains('open');
    sidebar.classList.toggle('open', !isOpen);
    overlay.classList.toggle('show', !isOpen);
}

// Close sidebar on nav link click (mobile)
document.querySelectorAll('.nav-link').forEach(function(link) {
    link.addEventListener('click', function() {
        if (window.innerWidth < 900) {
            document.getElementById('sidebar').classList.remove('open');
            document.getElementById('sidebarOverlay').classList.remove('show');
        }
    });
});
</script>

</body>
</html>
