<?php
// =====================================================
// INDEX — Main Application Shell
// =====================================================

ob_start(); // FIX: buffer semua output supaya header() di sub-pages bisa bekerja

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
    'rekap'      => 'pages/rekap.php',
    'anggota'    => 'pages/anggota.php',
];

$page_file = $pages[$hal] ?? $pages['home'];

$nav_items = [
    'home'       => ['icon' => 'home',       'label' => 'Home'],
    'mahasiswa'  => ['icon' => 'mahasiswa',  'label' => 'Mahasiswa'],
    'dosen'      => ['icon' => 'dosen',      'label' => 'Dosen'],
    'dopem'      => ['icon' => 'dopem',      'label' => 'Dosen Pembimbing'],
    'matakuliah' => ['icon' => 'matakuliah', 'label' => 'Mata Kuliah'],
    'querynilai' => ['icon' => 'querynilai', 'label' => 'Nilai Mahasiswa'],
    'rekap'      => ['icon' => 'rekap',      'label' => 'Rekap'],
    'anggota'    => ['icon' => 'anggota',    'label' => 'Anggota Kelompok'],
];

// SVG icon map
$nav_svgs = [
    'home' => '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 9.5L12 3l9 6.5V20a1 1 0 01-1 1H4a1 1 0 01-1-1V9.5z"/><polyline points="9 21 9 12 15 12 15 21"/></svg>',
    'mahasiswa' => '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 10v6M2 10l10-5 10 5-10 5z"/><path d="M6 12v5c3.33 2 8.67 2 12 0v-5"/></svg>',
    'dosen' => '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="6" r="3"/><path d="M6 21v-1a6 6 0 0 1 12 0v1"/><line x1="4" y1="11" x2="20" y2="11"/><line x1="4" y1="11" x2="4" y2="17"/><line x1="20" y1="11" x2="20" y2="17"/><line x1="4" y1="17" x2="20" y2="17"/></svg>',
    'dopem' => '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 00-3-3.87"/><path d="M16 3.13a4 4 0 010 7.75"/></svg>',
    'matakuliah' => '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 19.5A2.5 2.5 0 016.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 014 19.5v-15A2.5 2.5 0 016.5 2z"/></svg>',
    'querynilai' => '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/></svg>',
    'rekap' => '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 17V7a2 2 0 012-2h7a2 2 0 012 2v10a2 2 0 01-2 2h-9a2 2 0 01-2-2z"/><path d="M9 17H4a2 2 0 01-2-2V9a2 2 0 012-2h1"/><line x1="12" y1="8" x2="17" y2="8"/><line x1="12" y1="12" x2="17" y2="12"/></svg>',
    'anggota' => '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="9" cy="7" r="4"/><path d="M3 21v-2a4 4 0 014-4h4a4 4 0 014 4v2"/><path d="M16 3.13a4 4 0 010 7.75"/><path d="M21 21v-2a4 4 0 00-3-3.87"/></svg>',
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
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@600;700;800&display=swap" rel="stylesheet">
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
            <img src="gambar/unida.png" alt="Logo UNIDA" class="topbar-logo" style="border-radius:0;">
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
                    <div class="avatar-dropdown-user-row">
                        <span class="avatar-initial avatar-initial-sm" style="background:<?= $avatarBg ?>;"><?= htmlspecialchars($initials) ?></span>
                        <div class="avatar-dropdown-user-info">
                            <span class="avatar-dropdown-name"><?= htmlspecialchars($_SESSION['username'] ?? 'User') ?></span>
                            <span class="role-badge <?= $badge['class'] ?>"><?= $badge['label'] ?></span>
                        </div>
                    </div>
                </div>
                <div class="avatar-dropdown-divider"></div>
                <!-- Switch Account button -->
                <button class="avatar-dropdown-switch" onclick="openSwitchAccount()">
                    <span class="avatar-dd-icon">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M17 1l4 4-4 4"/><path d="M3 11V9a4 4 0 014-4h14"/>
                            <path d="M7 23l-4-4 4-4"/><path d="M21 13v2a4 4 0 01-4 4H3"/>
                        </svg>
                    </span>
                    Switch Account
                </button>
                <div class="avatar-dropdown-divider"></div>
                <a href="logout.php" class="avatar-dropdown-logout" onclick="return confirm('Keluar dari aplikasi?')">
                    <span class="avatar-dd-icon">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M9 21H5a2 2 0 01-2-2V5a2 2 0 012-2h4"/>
                            <polyline points="16 17 21 12 16 7"/>
                            <line x1="21" y1="12" x2="9" y2="12"/>
                        </svg>
                    </span>
                    Sign out
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
        <div class="sidebar-brand-section" id="sidebarBrandSection">
            <div class="sidebar-brand-logo">
                <img src="gambar/unida.png" alt="UNIDA" class="sidebar-brand-img">
            </div>
            <div class="sidebar-brand-text">
                <span class="sidebar-brand-name">TheBaseTen</span>
                <span class="sidebar-brand-sub">Univ. Djuanda</span>
            </div>
        </div>
        <nav>
            <?php foreach ($nav_items as $key => $item):
                // Hide non-home pages for guest
                if (isGuest() && $key !== 'home') continue;
            ?>
                <a href="index.php?hal=<?= $key ?>"
                   class="nav-link <?= ($hal === $key) ? 'active' : '' ?>"
                   title="<?= $item['label'] ?>">
                    <span class="nav-icon nav-icon-svg"><?= $nav_svgs[$item['icon']] ?? '' ?></span>
                    <span class="nav-link-label"><?= $item['label'] ?></span>
                </a>
            <?php endforeach; ?>
        </nav>

        <?php if (!isGuest()): ?>
        <div class="sidebar-profile" id="sidebarProfile">
            <!-- Popup menu (opens upward) -->
            <div class="sidebar-profile-menu" id="sidebarProfileMenu">
                <div class="sidebar-menu-header">
                    <span class="sidebar-menu-email"><?= htmlspecialchars($_SESSION['username'] ?? 'User') ?></span>
                </div>
                <button class="sidebar-menu-item switch" onclick="openSwitchAccount(); closeSidebarProfileMenu()">
                    <span class="sidebar-menu-icon">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M17 1l4 4-4 4"/><path d="M3 11V9a4 4 0 014-4h14"/>
                            <path d="M7 23l-4-4 4-4"/><path d="M21 13v2a4 4 0 01-4 4H3"/>
                        </svg>
                    </span>
                    Switch Account
                </button>
                <div class="sidebar-menu-divider"></div>
                <a href="logout.php" class="sidebar-menu-item logout" onclick="return confirm('Keluar dari aplikasi?')">
                    <span class="sidebar-menu-icon">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M9 21H5a2 2 0 01-2-2V5a2 2 0 012-2h4"/>
                            <polyline points="16 17 21 12 16 7"/>
                            <line x1="21" y1="12" x2="9" y2="12"/>
                        </svg>
                    </span>
                    Sign out
                </a>
            </div>
            <!-- Clickable profile row -->
            <button class="sidebar-profile-btn" id="sidebarProfileBtn" onclick="toggleSidebarProfileMenu()" type="button">
                <div class="sidebar-profile-avatar">
                    <?= strtoupper(substr($_SESSION['username'] ?? 'U', 0, 1)) ?>
                </div>
                <div class="sidebar-profile-info">
                    <span class="sidebar-profile-name"><?= htmlspecialchars($_SESSION['username'] ?? 'User') ?></span>
                    <span class="sidebar-profile-role <?= $badge['class'] ?>"><?= $badge['label'] ?></span>
                </div>
                <span class="sidebar-profile-chevron">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round">
                        <polyline points="18 15 12 9 6 15"/>
                    </svg>
                </span>
            </button>
        </div>
        <?php endif; ?>
    </aside>

    <!-- MAIN CONTENT -->
    <main class="main-content">
        <?php require_once $page_file; ?>
    </main>

</div>

<!-- ===== SCROLL TO TOP ===== -->
<button class="scroll-top-btn" id="scrollTopBtn" onclick="scrollToTop()" title="Back to top">
    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
        <polyline points="18 15 12 9 6 15"/>
    </svg>
</button>

<!-- ===== TOAST NOTIFICATION ===== -->
<div class="toast-overlay" id="toastOverlay" onclick="closeToast()">
    <div class="toast-card" onclick="event.stopPropagation()">
        <div class="toast-icon-wrap" id="toastIconWrap">
            <svg id="toastSvg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"></svg>
        </div>
        <div class="toast-title" id="toastTitle"></div>
        <div class="toast-sub"   id="toastSub"></div>
        <button class="toast-close-btn" onclick="closeToast()">OK</button>
    </div>
</div>


<!-- ===== SWITCH ACCOUNT MODAL OVERLAY ===== -->
<div class="switch-account-overlay" id="switchAccountOverlay" onclick="closeSwitchAccountOnBg(event)">
    <div class="switch-account-modal" id="switchAccountModal">

        <div class="switch-login-header">
            <div class="switch-login-logo-wrap">
                <img src="gambar/unida.png" alt="Logo UNIDA" class="login-logo">
            </div>
            <h1>Thebase<span>TEN</span></h1>
            <p>Universitas Djuanda &mdash; Kelompok 10</p>
        </div>

        <div class="role-tabs">
            <button class="role-tab active" onclick="switchTab('admin')" id="sw-tab-admin">
                <span>🛡️</span> Admin
            </button>
            <button class="role-tab" onclick="switchTab('member')" id="sw-tab-member">
                <span>👤</span> Member
            </button>
        </div>

        <div id="switch-error-box" style="display:none;" class="alert alert-error switch-alert">
            <span>&#9888;</span> <span id="switch-error-msg">Username atau password salah.</span>
        </div>

        <form class="login-form switch-login-form" id="switchAccountForm" onsubmit="doSwitchAccount(event)">
            <div class="form-group">
                <label for="sw-username">Username</label>
                <div class="input-icon-wrap">
                    <span class="input-icon">&#128100;</span>
                    <input type="text" id="sw-username" name="username"
                           placeholder="Masukkan username" required autocomplete="username">
                </div>
            </div>
            <div class="form-group">
                <label for="sw-password">Password</label>
                <div class="input-icon-wrap">
                    <span class="input-icon">&#128274;</span>
                    <input type="password" id="sw-password" name="password"
                           placeholder="Masukkan password" required autocomplete="current-password">
                    <button type="button" class="toggle-pw" onclick="toggleSwPw()" title="Tampilkan password">
                        &#128065;
                    </button>
                </div>
            </div>
            <button type="submit" class="btn-login">
                <span>🔓</span> Switch Account
            </button>
        </form>

        <div class="login-divider"><span>atau</span></div>

        <div class="guest-section" style="padding-bottom:20px;">
            <button type="button" class="btn-guest" onclick="closeSwitchAccount()">
                <span>✖</span> Batal / Tetap di akun ini
            </button>
        </div>

    </div>
</div>


<script>
// ── Sidebar Profile Menu ───────────────────────────────────────────────────
function toggleSidebarProfileMenu() {
    const menu    = document.getElementById('sidebarProfileMenu');
    const profile = document.getElementById('sidebarProfile');
    if (!menu) return;
    const isOpen = menu.classList.contains('show');
    menu.classList.toggle('show', !isOpen);
    profile.classList.toggle('open', !isOpen);
}

function closeSidebarProfileMenu() {
    const menu    = document.getElementById('sidebarProfileMenu');
    const profile = document.getElementById('sidebarProfile');
    if (menu) menu.classList.remove('show');
    if (profile) profile.classList.remove('open');
}

// Close sidebar profile menu when clicking outside
document.addEventListener('click', function(e) {
    const profile = document.getElementById('sidebarProfile');
    if (profile && !profile.contains(e.target)) {
        closeSidebarProfileMenu();
    }
});

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
    const colW     = getComputedStyle(document.documentElement).getPropertyValue('--sidebar-collapsed-w').trim();
    const fullW    = getComputedStyle(document.documentElement).getPropertyValue('--sidebar-w').trim();

    if (collapsed) {
        sidebar.classList.add('collapsed');
        if (btn)     btn.classList.add('is-collapsed');
        if (label)   label.style.opacity = '0';
        if (dbLabel) dbLabel.style.opacity = '0';
        if (main)    main.style.marginLeft = colW;
    } else {
        sidebar.classList.remove('collapsed');
        if (btn)     btn.classList.remove('is-collapsed');
        if (label)   label.style.opacity = '1';
        if (dbLabel) dbLabel.style.opacity = '1';
        if (main)    main.style.marginLeft = fullW;
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

// ── Switch Account Modal ───────────────────────────────────────────────────
function openSwitchAccount() {
    // Close avatar dropdown first
    document.getElementById('avatarDropdown').classList.remove('show');
    document.getElementById('switchAccountOverlay').classList.add('show');
    document.getElementById('switch-error-box').style.display = 'none';
    document.getElementById('switchAccountForm').reset();
    setTimeout(function() {
        document.getElementById('sw-username').focus();
    }, 120);
}

function closeSwitchAccount() {
    document.getElementById('switchAccountOverlay').classList.remove('show');
}

function closeSwitchAccountOnBg(e) {
    if (e.target === document.getElementById('switchAccountOverlay')) {
        closeSwitchAccount();
    }
}

function toggleSwPw() {
    var pw = document.getElementById('sw-password');
    pw.type = pw.type === 'password' ? 'text' : 'password';
}

// Tab switch inside modal
function switchTab(role) {
    ['admin','member'].forEach(function(r) {
        document.getElementById('sw-tab-' + r).classList.toggle('active', r === role);
    });
}

// Submit switch account via fetch (POST to switch_account.php)
function doSwitchAccount(e) {
    e.preventDefault();
    var username = document.getElementById('sw-username').value.trim();
    var password = document.getElementById('sw-password').value;

    // Valid users (same as login.php)
    var validUsers = {
        'admin':     { password: 'admin123',   role: 'admin'  },
        'memberten': { password: 'baseten123', role: 'member' }
    };

    var errBox = document.getElementById('switch-error-box');
    var errMsg = document.getElementById('switch-error-msg');

    if (validUsers[username] && validUsers[username].password === password) {
        // Redirect to switch_account.php with credentials via hidden form POST
        var form = document.createElement('form');
        form.method = 'POST';
        form.action = 'switch_account.php';
        ['username','password'].forEach(function(field) {
            var inp = document.createElement('input');
            inp.type  = 'hidden';
            inp.name  = field;
            inp.value = field === 'username' ? username : password;
            form.appendChild(inp);
        });
        // Pass current page to redirect back after switch
        var pageInp = document.createElement('input');
        pageInp.type  = 'hidden';
        pageInp.name  = 'redirect_hal';
        pageInp.value = '<?= htmlspecialchars($hal) ?>';
        form.appendChild(pageInp);
        document.body.appendChild(form);
        form.submit();
    } else {
        errBox.style.display = 'flex';
        errMsg.textContent = 'Username atau password salah.';
        document.getElementById('sw-password').value = '';
        document.getElementById('sw-password').focus();
    }
}

// Close modal on Escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') closeSwitchAccount();
});

// Scroll to top button
function scrollToTop() {
    window.scrollTo({ top: 0, behavior: 'smooth' });
}

const scrollTopBtn = document.getElementById('scrollTopBtn');
const mainContent  = document.querySelector('.main-content');
if (mainContent) {
    mainContent.addEventListener('scroll', function() {
        scrollTopBtn.classList.toggle('visible', mainContent.scrollTop > 200);
    });
}
window.addEventListener('scroll', function() {
    scrollTopBtn.classList.toggle('visible', window.scrollY > 200);
});

// ===== TOAST NOTIFICATION =====
var TOAST_ICONS = {
    success: '<polyline points="20 6 9 17 4 12"/>',
    error:   '<line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/>'
};

function showToast(type, title, sub) {
    var overlay = document.getElementById('toastOverlay');
    var wrap    = document.getElementById('toastIconWrap');
    var svg     = document.getElementById('toastSvg');
    var titleEl = document.getElementById('toastTitle');
    var subEl   = document.getElementById('toastSub');

    wrap.className    = 'toast-icon-wrap ' + type;
    svg.innerHTML     = TOAST_ICONS[type] || TOAST_ICONS.success;
    titleEl.textContent = title;
    subEl.textContent   = sub || '';

    // Re-trigger animation by replacing the node
    var newSvg = svg.cloneNode(true);
    newSvg.innerHTML = TOAST_ICONS[type] || TOAST_ICONS.success;
    svg.parentNode.replaceChild(newSvg, svg);

    overlay.classList.add('show');
    document.body.style.overflow = 'hidden';
}

function closeToast() {
    var overlay = document.getElementById('toastOverlay');
    overlay.classList.remove('show');
    document.body.style.overflow = '';

    // Remove msg param from URL without reload
    var url = new URL(window.location.href);
    url.searchParams.delete('msg');
    history.replaceState(null, '', url.toString());
}

// Auto-trigger from URL ?msg=
(function() {
    var params = new URLSearchParams(window.location.search);
    var msg    = params.get('msg');
    if (!msg) return;

    var map = {
        'added':     ['success', 'Data berhasil ditambahkan',   ''],
        'updated':   ['success', 'Data berhasil diperbarui',    ''],
        'deleted':   ['success', 'Data berhasil dihapus',       ''],
        'err':       ['error',   'Data tidak dapat ditambahkan','Terjadi kesalahan, coba lagi.'],
        'err_upd':   ['error',   'Data tidak dapat diperbarui', 'Terjadi kesalahan, coba lagi.'],
        'err_del':   ['error',   'Data tidak dapat dihapus',    'Terjadi kesalahan, coba lagi.'],
        'no_access': ['error',   'Akses ditolak',               'Hanya Admin yang dapat melakukan perubahan.'],
        'duplicate': ['error',   'Data tidak dapat ditambahkan','Data sudah ada atau NIM/ID duplikat.'],
    };

    var entry = map[msg];
    if (entry) {
        showToast(entry[0], entry[1], entry[2]);
    }
})();
</script>

</body>
</html>
