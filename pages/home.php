<?php
require_once __DIR__ . '/../includes/koneksi.php';

<<<<<<< HEAD
// ─── Fetch stats dari database ───────────────────────────────────────────────

// 1. Total Mahasiswa
$total_mahasiswa = 0;
$r1 = mysqli_query($link, "SELECT COUNT(*) as total FROM tbl_mhs");
if ($r1) {
    $total_mahasiswa = (int) mysqli_fetch_assoc($r1)['total'];
}

// 2. Total Mata Kuliah
$total_matakuliah = 0;
$r2 = mysqli_query($link, "SELECT COUNT(*) as total FROM tbl_matakuliah");
if ($r2) {
    $total_matakuliah = (int) mysqli_fetch_assoc($r2)['total'];
}

// 3. Rata-rata IPK dihitung dari huruf mutu di tbl_nilai
//    Konversi: A=4.0, B=3.0, C=2.0, D=1.0, E=0.0
$rata_ipk = '—';
$r3 = mysqli_query($link,
    "SELECT AVG(
        CASE hm
            WHEN 'A' THEN 4.0
            WHEN 'B' THEN 3.0
            WHEN 'C' THEN 2.0
            WHEN 'D' THEN 1.0
            ELSE 0.0
        END
    ) AS avg_ipk FROM tbl_nilai"
);
if ($r3) {
    $row = mysqli_fetch_assoc($r3);
    if ($row['avg_ipk'] !== null) {
        $rata_ipk = number_format((float) $row['avg_ipk'], 2);
    }
}

// Format angka mahasiswa dengan titik ribuan (misal: 2.451)
$total_mahasiswa_fmt = number_format($total_mahasiswa, 0, ',', '.');
=======
// Fetch stats from DB (fallback to 0 if error)
$total_mahasiswa = 2.451;
$total_matakuliah = 21;
$rata_ipk = 3.91;

// if (isset($link) && $link) {
//     $r = mysqli_query($link, "SELECT COUNT(*) as total FROM tbl_mhs");
//     if ($r) $total_mahasiswa = (int) mysqli_fetch_assoc($r)['total'];

//     $r2 = mysqli_query($link, "SELECT COUNT(*) as total FROM tbl_mk");
//     if ($r2) $total_matakuliah = (int) mysqli_fetch_assoc($r2)['total'];

//     // Try to compute average IPK from querynilai or fallback table
//     $r3 = mysqli_query($link, "SELECT AVG(ipk) as avg_ipk FROM tbl_ipk");
//     if ($r3) {
//         $row = mysqli_fetch_assoc($r3);
//         $rata_ipk = $row['avg_ipk'] ? number_format((float)$row['avg_ipk'], 2) : '—';
//     } else {
//         $rata_ipk = '—';
//     }
// }
>>>>>>> 0653d9652c091e43d86ffe3d108f2fcb36449525
?>

<div class="welcome-box">
    <div class="welcome-badge">🏫 Sistem Informasi Akademik</div>
    <h2>Selamat Datang<?= isGuest() ? ', Guest!' : ', ' . htmlspecialchars($_SESSION['username'] ?? 'User') . '!' ?></h2>
    <p>Sistem Informasi Basis Data MySQL &mdash; <strong>TheBaseTen</strong></p>

</div>

<!-- Stats Cards -->
<div class="stats-row">
    <div class="stat-card">
        <div class="stat-icon">🎓</div>
<<<<<<< HEAD
        <div class="stat-value"><?= $total_mahasiswa_fmt ?></div>
=======
        <div class="stat-value"><?= $total_mahasiswa ?></div>
>>>>>>> 0653d9652c091e43d86ffe3d108f2fcb36449525
        <div class="stat-label">Total Mahasiswa</div>
    </div>
    <div class="stat-card">
        <div class="stat-icon">📚</div>
        <div class="stat-value"><?= $total_matakuliah ?></div>
        <div class="stat-label">Mata Kuliah</div>
    </div>
    <div class="stat-card">
        <div class="stat-icon">⭐</div>
        <div class="stat-value"><?= $rata_ipk ?></div>
        <div class="stat-label">Rata-rata IPK</div>
    </div>
</div>

<?php if (!isGuest()): ?>
<div class="box">
    <h2>Tentang Aplikasi</h2>
    <p class="subjudul">Aplikasi manajemen data akademik berbasis PHP &amp; MySQL</p>
    <div class="info-grid">
        <div class="info-card">
            <span class="icon">🎓</span>
            <p>Data Mahasiswa</p>
        </div>
        <div class="info-card">
            <span class="icon">📋</span>
            <p>Data Dosen</p>
        </div>
        <div class="info-card">
            <span class="icon">📚</span>
            <p>Mata Kuliah</p>
        </div>
        <div class="info-card">
            <span class="icon">📊</span>
            <p>Nilai Mahasiswa</p>
        </div>
        <div class="info-card">
            <span class="icon">👥</span>
            <p>Anggota Kelompok</p>
        </div>
    </div>
</div>
<?php else: ?>
<div class="box guest-locked-box">
    <div class="guest-lock-icon">🔒</div>
    <h3>Akses Terbatas</h3>
    <p>Sebagai Guest, kamu hanya bisa melihat halaman ini. Login sebagai <strong>Member</strong> untuk melihat data, atau <strong>Admin</strong> untuk akses penuh.</p>
    <a href="logout.php" class="btn-login-link">⬅ Kembali ke Login</a>
</div>
<?php endif; ?>
