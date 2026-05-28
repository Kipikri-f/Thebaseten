<?php
// =====================================================
// INDEX — Main Application Shell
// =====================================================

require_once 'includes/auth.php';
require_once 'includes/helpers.php';

$hal = isset($_GET['hal']) ? $_GET['hal'] : 'home';

// Guest can only see home
if (isGuest() && $hal !== 'home') {
    header('Location: index.php?hal=home');
    exit;
}

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

$role = getUserRole();
$roleBadge = [
    'admin'  => ['label' => 'Admin',  'class' => 'role-badge-admin'],
    'member' => ['label' => 'Member', 'class' => 'role-badge-member'],
    'guest'  => ['label' => 'Guest',  'class' => 'role-badge-guest'],
];
$badge = $roleBadge[$role] ?? $roleBadge['guest'];
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
        <div class="topbar-logo-wrap">
            <img src="gambar/unida.png" alt="Logo UNIDA" class="topbar-logo">
        </div>
        <div>
            <h1>Thebaseten</h1>
            <span>Universitas Djuanda</span>
        </div>
    </div>

    <div class="topbar-right">
        <span class="topbar-date" id="topbarClock"></span>

        <!-- Avatar dropdown -->
        <div class="avatar-dropdown-wrap" id="avatarDropdownWrap">
            <button class="avatar-btn" id="avatarBtn" onclick="toggleAvatarDropdown()" title="Akun">
                <?php
                    $uname    = $_SESSION['username'] ?? 'U';
                    $words    = explode(' ', trim($uname));
                    $initials = '';
                    foreach (array_slice($words, 0, 2) as $w) {
                        $initials .= strtoupper(substr($w, 0, 1));
                    }
                    $initials = $initials ?: 'U';
                    $avatarColors = [
                        'admin'  => '#4a7c59',
                        'member' => '#2d6a9f',
                        'guest'  => '#6b3fa0',
                    ];
                    $avatarBg = $avatarColors[$role] ?? '#4a7c59';
                ?>
                <span class="avatar-initial" style="background:<?= $avatarBg ?>;"><?= htmlspecialchars($initials) ?></span>
            </button>
            <div class="avatar-dropdown" id="avatarDropdown">
                <div class="avatar-dropdown-header">
                    <span class="avatar-dropdown-name"><?= htmlspecialchars($_SESSION['username'] ?? 'User') ?></span>
                    <span class="role-badge <?= $badge['class'] ?>"><?= $badge['label'] ?></span>
                </div>
                <div class="avatar-dropdown-divider"></div>
                <a href="logout.php" class="avatar-dropdown-logout" onclick="return confirm('Keluar dari aplikasi?')">
                    <span>🚪</span> Sign out
                </a>
            </div>
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
            <button class="sidebar-toggle-btn" id="sidebarToggleBtn" onclick="toggleSidebarCollapse()" title="Toggle sidebar">
                <span class="toggle-icon-wrap">
                    <!-- Grid icon (expanded state) -->
                    <svg class="icon-grid" width="18" height="18" viewBox="0 0 18 18" fill="currentColor">
                        <rect x="1"  y="1"  width="6" height="6" rx="1.5"/>
                        <rect x="11" y="1"  width="6" height="6" rx="1.5"/>
                        <rect x="1"  y="11" width="6" height="6" rx="1.5"/>
                        <rect x="11" y="11" width="6" height="6" rx="1.5"/>
                    </svg>
                    <!-- Collapse icon (collapsed state) -->
                    <svg class="icon-collapse" width="18" height="18" viewBox="0 0 18 18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
                        <line x1="3"  y1="4"  x2="15" y2="4"/>
                        <line x1="3"  y1="9"  x2="10" y2="9"/>
                        <line x1="3"  y1="14" x2="12" y2="14"/>
                    </svg>
                </span>
            </button>
            <span class="sidebar-dashboard-label" id="sidebarDashboardLabel">Dashboard</span>
        </div>
        <nav>
            <?php foreach ($nav_items as $key => $item):
                // Hide non-home pages for guest
                if (isGuest() && $key !== 'home') continue;
            ?>
                <a href="index.php?hal=<?= $key ?>"
                   class="nav-link <?= ($hal === $key) ? 'active' : '' ?>"
                   title="<?= $item['label'] ?>">
                    <span class="nav-icon"><?= $item['icon'] ?></span>
                    <span class="nav-link-label"><?= $item['label'] ?></span>
                </a>
            <?php endforeach; ?>
        </nav>

        <?php if (!isGuest()): ?>
        <div class="sidebar-profile">
            <div class="sidebar-profile-avatar">
                <?= strtoupper(substr($_SESSION['username'] ?? 'U', 0, 1)) ?>
            </div>
            <div class="sidebar-profile-info">
                <span class="sidebar-profile-name"><?= htmlspecialchars($_SESSION['username'] ?? 'User') ?></span>
                <span class="sidebar-profile-role <?= $badge['class'] ?>"><?= $badge['label'] ?></span>
            </div>
        </div>
        <?php endif; ?>
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
// ── Mobile overlay toggle ──────────────────────────────────────────────────
function toggleSidebar() {
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('sidebarOverlay');
    const isOpen  = sidebar.classList.contains('open');
    sidebar.classList.toggle('open', !isOpen);
    overlay.classList.toggle('show', !isOpen);
}

// ── Desktop collapse toggle ────────────────────────────────────────────────
const COLLAPSE_KEY = 'sidebarCollapsed';

function applySidebarState(collapsed) {
    const sidebar  = document.getElementById('sidebar');
    const btn      = document.getElementById('sidebarToggleBtn');
    const label    = document.getElementById('navLabel');
    const dbLabel  = document.getElementById('sidebarDashboardLabel');
    const main     = document.querySelector('.main-content');
    const footer   = document.querySelector('.app-footer');
    const colW     = getComputedStyle(document.documentElement).getPropertyValue('--sidebar-collapsed-w').trim();
    const fullW    = getComputedStyle(document.documentElement).getPropertyValue('--sidebar-w').trim();

    if (collapsed) {
        sidebar.classList.add('collapsed');
        if (btn)     btn.classList.add('is-collapsed');
        if (label)   label.style.opacity = '0';
        if (dbLabel) dbLabel.style.opacity = '0';
        if (main)    main.style.marginLeft = colW;
        if (footer)  footer.style.left     = colW;
    } else {
        sidebar.classList.remove('collapsed');
        if (btn)     btn.classList.remove('is-collapsed');
        if (label)   label.style.opacity = '1';
        if (dbLabel) dbLabel.style.opacity = '1';
        if (main)    main.style.marginLeft = fullW;
        if (footer)  footer.style.left     = fullW;
    }
}

function toggleSidebarCollapse() {
    const isCollapsed = document.getElementById('sidebar').classList.contains('collapsed');
    const newState    = !isCollapsed;
    localStorage.setItem(COLLAPSE_KEY, newState ? '1' : '0');
    applySidebarState(newState);
}

// Init on load
document.addEventListener('DOMContentLoaded', function () {
    // Only apply collapse on desktop (≥ 900px)
    if (window.innerWidth >= 900) {
        const saved = localStorage.getItem(COLLAPSE_KEY);
        if (saved === '1') applySidebarState(true);
    }

    // ── Realtime WIB clock ─────────────────────────────────────────────────
    function updateClock() {
        const now = new Date();
        // WIB = UTC+7
        const wib = new Date(now.getTime() + (7 * 60 * 60 * 1000));
        const h   = String(wib.getUTCHours()).padStart(2, '0');
        const m   = String(wib.getUTCMinutes()).padStart(2, '0');
        const s   = String(wib.getUTCSeconds()).padStart(2, '0');
        const days   = ['Minggu','Senin','Selasa','Rabu','Kamis','Jumat','Sabtu'];
        const months = ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];
        const day  = days[wib.getUTCDay()];
        const date = wib.getUTCDate();
        const mon  = months[wib.getUTCMonth()];
        const yr   = wib.getUTCFullYear();
        const el   = document.getElementById('topbarClock');
        if (el) el.textContent = day + ', ' + date + ' ' + mon + ' ' + yr + '  ·  ' + h + ':' + m + ' WIB';
    }
    updateClock();
    setInterval(updateClock, 1000);
});

// ── Avatar dropdown toggle ─────────────────────────────────────────────────
function toggleAvatarDropdown() {
    const dd = document.getElementById('avatarDropdown');
    dd.classList.toggle('show');
}

// Close dropdown when clicking outside
document.addEventListener('click', function(e) {
    const wrap = document.getElementById('avatarDropdownWrap');
    if (wrap && !wrap.contains(e.target)) {
        document.getElementById('avatarDropdown').classList.remove('show');
    }
});

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
