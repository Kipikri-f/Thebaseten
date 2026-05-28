<?php
require_once __DIR__ . '/../includes/koneksi.php';

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

// 4. Total Dosen
$total_dosen = 0;
$r4 = mysqli_query($link, "SELECT COUNT(*) as total FROM tbl_dosen");
if ($r4) {
    $total_dosen = (int) mysqli_fetch_assoc($r4)['total'];
}

// Format angka mahasiswa dengan titik ribuan (misal: 2.451)
$total_mahasiswa_fmt = number_format($total_mahasiswa, 0, ',', '.');

// ─── Jadwal Kuliah Hari Ini (randomized per hari, seed dari nama hari) ────────

$hari_id  = (int) date('N'); // 1=Senin ... 7=Minggu
$hari_map = [1=>'Senin', 2=>'Selasa', 3=>'Rabu', 4=>'Kamis', 5=>'Jumat', 6=>'Sabtu', 7=>'Minggu'];
$nama_hari = $hari_map[$hari_id];

$jadwal_hari_ini = [];
$is_weekend = ($hari_id >= 6);

if (!$is_weekend) {
    // Ambil semua matkul + dosen dari database
    $r_mk = mysqli_query($link,
        "SELECT m.kodemk, m.namamk, m.sks, d.nid, d.namadosen
         FROM tbl_matakuliah m
         LEFT JOIN tbl_dosen d ON d.nid = (
             SELECT nid FROM tbl_dosen ORDER BY RAND() LIMIT 1
         )
         ORDER BY m.kodemk ASC"
    );
    $all_matkul = [];
    if ($r_mk) {
        while ($mk = mysqli_fetch_assoc($r_mk)) {
            $all_matkul[] = $mk;
        }
    }

    // Seed random berdasarkan hari + tanggal agar konsisten dalam satu hari
    $seed = (int) date('Ymd') + $hari_id * 100;
    srand($seed);

    // Ambil semua dosen
    $r_dosen = mysqli_query($link, "SELECT nid, namadosen FROM tbl_dosen ORDER BY nid ASC");
    $all_dosen = [];
    if ($r_dosen) {
        while ($d = mysqli_fetch_assoc($r_dosen)) {
            $all_dosen[] = $d;
        }
    }

    if (count($all_matkul) > 0 && count($all_dosen) > 0) {
        // Shuffle matkul dengan seed
        $keys = array_keys($all_matkul);
        shuffle($keys); // sudah di-seed di atas
        $picked_keys = array_slice($keys, 0, min(2, count($keys)));

        // Slot waktu tetap per hari
        $slots = [
            ['start'=>'08:00', 'end'=>'10:30'],
            ['start'=>'13:00', 'end'=>'15:30'],
        ];

        // Daftar ruangan untuk variasi
        $ruangan_list = ['LAB-RPLA', 'RK-401', 'RK-402', 'LAB-DB', 'RK-301', 'RK-302', 'LAB-NET'];

        foreach ($picked_keys as $i => $key) {
            $mk = $all_matkul[$key];

            // Pilih dosen secara random (konsisten per hari)
            $dosen_idx = ($seed + $i * 37) % count($all_dosen);
            $dosen     = $all_dosen[$dosen_idx];

            // Pilih ruangan
            $ruangan_idx = ($seed + $i * 13 + 7) % count($ruangan_list);

            // Tentukan status berdasarkan waktu sekarang
            $now_minutes  = (int)date('H') * 60 + (int)date('i');
            $start_parts  = explode(':', $slots[$i]['start']);
            $end_parts    = explode(':', $slots[$i]['end']);
            $start_min    = (int)$start_parts[0] * 60 + (int)$start_parts[1];
            $end_min      = (int)$end_parts[0]   * 60 + (int)$end_parts[1];

            if ($now_minutes >= $start_min && $now_minutes < $end_min) {
                $status = 'berlangsung';
            } elseif ($now_minutes < $start_min) {
                $status = 'menunggu';
            } else {
                $status = 'selesai';
            }

            $jadwal_hari_ini[] = [
                'no'       => $i + 1,
                'start'    => $slots[$i]['start'],
                'end'      => $slots[$i]['end'],
                'namamk'   => $mk['namamk'],
                'ruangan'  => $ruangan_list[$ruangan_idx],
                'nid'      => $dosen['nid'],
                'dosen'    => $dosen['namadosen'],
                'status'   => $status,
            ];
        }
    }
}
?>

<div class="welcome-box">
    <div class="welcome-badge">🏫 Sistem Informasi Akademik</div>
    <h2>Selamat Datang! 👋</h2>
    <p>Selamat datang di <strong>TheBaseTen</strong> &mdash; platform pengelolaan data akademik.</p>
</div>

<!-- Stats Cards -->
<div class="stats-row">
    <div class="stat-card">
        <div class="stat-icon">🎓</div>
        <div class="stat-value"><?= $total_mahasiswa_fmt ?></div>
        <div class="stat-label">Total Mahasiswa</div>
    </div>
    <div class="stat-card">
        <div class="stat-icon">👨‍🏫</div>
        <div class="stat-value"><?= $total_dosen ?></div>
        <div class="stat-label">Total Dosen</div>
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

<?php
// ─── Fetch list mahasiswa & dosen for panel ───────────────────────────────────
$list_mhs   = [];
$r_list_mhs = mysqli_query($link, "SELECT nim, namamhs FROM tbl_mhs ORDER BY nim ASC");
if ($r_list_mhs) {
    while ($row = mysqli_fetch_assoc($r_list_mhs)) {
        $list_mhs[] = $row;
    }
}

$list_dosen   = [];
$r_list_dosen = mysqli_query($link, "SELECT nid, namadosen FROM tbl_dosen ORDER BY nid ASC");
if ($r_list_dosen) {
    while ($row = mysqli_fetch_assoc($r_list_dosen)) {
        $list_dosen[] = $row;
    }
}
?>

<!-- Data Panel: Mahasiswa + Dosen -->
<?php if (!isGuest()): ?>
<div class="data-panel-row">
    <!-- Mahasiswa -->
    <div class="box data-panel-box">
        <h3 class="data-panel-title">🎓 Data Mahasiswa</h3>
        <p class="subjudul" style="margin-bottom:10px;">Daftar mahasiswa terdaftar</p>
        <div class="data-panel-scroll">
            <table class="data-table data-panel-table">
                <thead>
                    <tr>
                        <th style="width:36%">NIM</th>
                        <th style="text-align:left;padding-left:14px;">Nama Mahasiswa</th>
                    </tr>
                </thead>
                <tbody>
                <?php if (!empty($list_mhs)): ?>
                    <?php foreach ($list_mhs as $m):
                        $mhs_words    = explode(' ', $m['namamhs']);
                        $mhs_initials = '';
                        foreach (array_slice($mhs_words, 0, 2) as $w) {
                            $mhs_initials .= strtoupper(substr($w, 0, 1));
                        }
                        $mhs_colors = ['#3d7ebf','#5a9e6f','#c0633a','#7b52ab','#2e8b7a'];
                        $mhs_cidx   = abs(crc32($m['nim'])) % count($mhs_colors);
                    ?>
                    <tr>
                        <td><span class="badge badge-nim"><?= htmlspecialchars($m['nim']) ?></span></td>
                        <td style="text-align:left;padding-left:14px;">
                            <div class="dosen-cell">
                                <span class="dosen-avatar" style="background:<?= $mhs_colors[$mhs_cidx] ?>;"><?= $mhs_initials ?></span>
                                <?= htmlspecialchars($m['namamhs']) ?>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="2" class="no-data">Belum ada data mahasiswa.</td></tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Dosen -->
    <div class="box data-panel-box">
        <h3 class="data-panel-title">👨‍🏫 Data Dosen</h3>
        <p class="subjudul" style="margin-bottom:10px;">Daftar dosen pengajar</p>
        <div class="data-panel-scroll">
            <table class="data-table data-panel-table">
                <thead>
                    <tr>
                        <th style="width:36%">NID</th>
                        <th style="text-align:left;padding-left:14px;">Nama Dosen</th>
                    </tr>
                </thead>
                <tbody>
                <?php if (!empty($list_dosen)): ?>
                    <?php foreach ($list_dosen as $d):
                        $words    = explode(' ', $d['namadosen']);
                        $initials = '';
                        foreach (array_slice($words, 0, 2) as $w) {
                            $initials .= strtoupper(substr($w, 0, 1));
                        }
                        $avatar_colors = ['#4a7c59','#2d6a9f','#8b5e3c','#6b3fa0','#b05b3b'];
                        $cidx = abs(crc32($d['nid'])) % count($avatar_colors);
                    ?>
                    <tr>
                        <td><span class="badge badge-nim"><?= htmlspecialchars($d['nid']) ?></span></td>
                        <td style="text-align:left;padding-left:14px;">
                            <div class="dosen-cell">
                                <span class="dosen-avatar" style="background:<?= $avatar_colors[$cidx] ?>;"><?= $initials ?></span>
                                <?= htmlspecialchars($d['namadosen']) ?>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="2" class="no-data">Belum ada data dosen.</td></tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php endif; ?>

<!-- Jadwal Kuliah Hari Ini -->
<div class="box jadwal-box">
    <h2>📅 Jadwal Kuliah Hari Ini</h2>
    <p class="subjudul">Daftar kelas yang berlangsung hari ini — <?= $nama_hari ?>, <?= date('d F Y') ?></p>

    <?php if ($is_weekend): ?>
    <div class="jadwal-weekend">
        <div class="jadwal-weekend-icon">🎉</div>
        <p class="jadwal-weekend-text">Tidak ada mata kuliah aktif hari ini.</p>
        <p class="jadwal-weekend-sub">Selamat menikmati akhir pekan!</p>
    </div>

    <?php elseif (empty($jadwal_hari_ini)): ?>
    <div class="jadwal-weekend">
        <div class="jadwal-weekend-icon">📭</div>
        <p class="jadwal-weekend-text">Belum ada data mata kuliah atau dosen.</p>
    </div>

    <?php else: ?>
    <div class="table-wrapper">
        <table class="data-table jadwal-table">
            <thead>
                <tr>
                    <th style="width:5%">No</th>
                    <th style="width:18%">Batas Waktu</th>
                    <th style="text-align:left;padding-left:20px;">Mata Kuliah</th>
                    <th style="width:14%">Ruangan</th>
                    <th style="width:24%">Dosen Pengampu</th>
                    <th style="width:15%">Status</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($jadwal_hari_ini as $j): ?>
                <?php
                    // Ambil inisial dosen untuk avatar
                    $words    = explode(' ', $j['dosen']);
                    $initials = '';
                    foreach (array_slice($words, 0, 2) as $w) {
                        $initials .= strtoupper(substr($w, 0, 1));
                    }
                    // Warna avatar dari NID seed
                    $avatar_colors = ['#4a7c59','#2d6a9f','#8b5e3c','#6b3fa0','#b05b3b'];
                    $color_idx     = crc32($j['nid']) % count($avatar_colors);
                    $avatar_color  = $avatar_colors[abs($color_idx)];
                ?>
                <tr>
                    <td><?= $j['no'] ?></td>
                    <td><strong><?= $j['start'] ?> - <?= $j['end'] ?></strong></td>
                    <td style="text-align:left;padding-left:20px;"><?= htmlspecialchars($j['namamk']) ?></td>
                    <td><span class="badge badge-ruangan"><?= htmlspecialchars($j['ruangan']) ?></span></td>
                    <td>
                        <div class="dosen-cell">
                            <span class="dosen-avatar" style="background:<?= $avatar_color ?>;"><?= $initials ?></span>
                            <?= htmlspecialchars($j['dosen']) ?>
                        </div>
                    </td>
                    <td>
                        <?php if ($j['status'] === 'berlangsung'): ?>
                            <span class="status-badge status-berlangsung">● Sedang Berlangsung</span>
                        <?php elseif ($j['status'] === 'menunggu'): ?>
                            <span class="status-badge status-menunggu">◌ Menunggu</span>
                        <?php else: ?>
                            <span class="status-badge status-selesai">✓ Selesai</span>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php endif; ?>
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
<script>
// Realtime WIB datetime for welcome box
(function updateWelcomeTime() {
    const el = document.getElementById('welcomeDateTime');
    if (!el) return;
    const days   = ['Minggu','Senin','Selasa','Rabu','Kamis','Jumat','Sabtu'];
    const months = ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
    function tick() {
        const now = new Date();
        const wib = new Date(now.getTime() + 7 * 3600000);
        const day = days[wib.getUTCDay()];
        const d   = wib.getUTCDate();
        const mon = months[wib.getUTCMonth()];
        const yr  = wib.getUTCFullYear();
        const h   = String(wib.getUTCHours()).padStart(2,'0');
        const m   = String(wib.getUTCMinutes()).padStart(2,'0');
        const s   = String(wib.getUTCSeconds()).padStart(2,'0');
        el.textContent = '⏰ ' + h + ':' + m + ':' + s + ' WIB';
    }
    tick();
    setInterval(tick, 1000);
})();
</script>
