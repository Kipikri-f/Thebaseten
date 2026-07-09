<?php
// =====================================================
// PAGE: Rekap (Ringkasan / Aggregate Report)
// =====================================================
// Halaman ini murni menampilkan hasil query rekap —
// tidak ada CRUD di sini. Semua data diambil dari
// tbl_fakultas (data per-mahasiswa: fakultas, prodi,
// angkatan, status kelulusan) dan tbl_mahasiswa_ipk
// (IPK per mahasiswa), sesuai skema database
// `learnclidatabase` yang sebenarnya dipakai.
//
// Catatan: tbl_fakultas di database ini BUKAN tabel
// referensi kode->nama, melainkan tabel data mahasiswa
// per-baris (nom, nim, nama, kdfakultas, prodi, angkatan,
// sts, tahun). Karena tidak ada tabel referensi terpisah
// untuk nama fakultas, nama fakultas dipetakan di PHP.

require_once __DIR__ . '/../includes/koneksi.php';

// Peta kode fakultas -> nama fakultas (tidak ada tabel referensi di DB)
$namaFakultasMap = [
    'FAK1' => 'Fakultas Ekonomi dan Bisnis',
    'FAK2' => 'Fakultas Ilmu Komputer',
    'FAK3' => 'Fakultas Keguruan dan Ilmu Pendidikan',
    'FAK4' => 'Fakultas Agama Islam',
    'FAK5' => 'Fakultas Teknik dan Ilmu Pertanian',
];
function namaFakultas(array $map, ?string $kode): string {
    if (!$kode) return '-';
    return $map[$kode] ?? $kode;
}

// ---------------------------------------------------------
// 1) Jumlah mahasiswa per fakultas
// ---------------------------------------------------------
$rekapFakultas = [];
$q1 = mysqli_query($link, "
    SELECT kdfakultas, COUNT(*) AS jumlah
    FROM tbl_fakultas
    WHERE kdfakultas IS NOT NULL AND kdfakultas <> ''
    GROUP BY kdfakultas
    ORDER BY kdfakultas
");
if ($q1) { while ($r = mysqli_fetch_assoc($q1)) { $rekapFakultas[] = $r; } }

$avgFakultas = 0;
if (count($rekapFakultas) > 0) {
    $avgFakultas = array_sum(array_column($rekapFakultas, 'jumlah')) / count($rekapFakultas);
}
$rekapFakultasTop = array_values(array_filter($rekapFakultas, function ($r) use ($avgFakultas) {
    return $r['jumlah'] > $avgFakultas;
}));

// ---------------------------------------------------------
// 2) Jumlah mahasiswa per prodi
// ---------------------------------------------------------
$rekapProdi = [];
$q2 = mysqli_query($link, "
    SELECT prodi, COUNT(*) AS jumlah
    FROM tbl_fakultas
    WHERE prodi IS NOT NULL AND prodi <> ''
    GROUP BY prodi
    ORDER BY prodi
");
if ($q2) { while ($r = mysqli_fetch_assoc($q2)) { $rekapProdi[] = $r; } }

$avgProdi = 0;
if (count($rekapProdi) > 0) {
    $avgProdi = array_sum(array_column($rekapProdi, 'jumlah')) / count($rekapProdi);
}
$rekapProdiTop = array_values(array_filter($rekapProdi, function ($r) use ($avgProdi) {
    return $r['jumlah'] > $avgProdi;
}));

// ---------------------------------------------------------
// 3) Jumlah mahasiswa LULUS per fakultas
//    (lulus = tbl_fakultas.sts = 'LULUS')
// ---------------------------------------------------------
$rekapLulus = [];
$q3 = mysqli_query($link, "
    SELECT kdfakultas, COUNT(*) AS jumlah_lulus
    FROM tbl_fakultas
    WHERE sts = 'LULUS'
    GROUP BY kdfakultas
    ORDER BY kdfakultas
");
if ($q3) { while ($r = mysqli_fetch_assoc($q3)) { $rekapLulus[] = $r; } }

$avgLulus = 0;
if (count($rekapLulus) > 0) {
    $avgLulus = array_sum(array_column($rekapLulus, 'jumlah_lulus')) / count($rekapLulus);
}
$rekapLulusTop = array_values(array_filter($rekapLulus, function ($r) use ($avgLulus) {
    return $r['jumlah_lulus'] > $avgLulus;
}));

// ---------------------------------------------------------
// 4) Detail data mahasiswa (bisa difilter per angkatan)
// ---------------------------------------------------------
$listAngkatan = [];
$qa = mysqli_query($link, "SELECT DISTINCT angkatan FROM tbl_fakultas WHERE angkatan IS NOT NULL AND angkatan <> '' ORDER BY angkatan DESC");
if ($qa) { while ($r = mysqli_fetch_assoc($qa)) { $listAngkatan[] = $r['angkatan']; } }

$filterAngkatan = isset($_GET['angkatan']) && $_GET['angkatan'] !== '' ? trim($_GET['angkatan']) : null;

$whereAngkatan = '';
if ($filterAngkatan !== null) {
    $whereAngkatan = "WHERE angkatan = '" . mysqli_real_escape_string($link, $filterAngkatan) . "'";
}

$detailMhs = [];
$q4 = mysqli_query($link, "
    SELECT nim, nama, kdfakultas, prodi, angkatan,
           COALESCE(NULLIF(sts, ''), '-') AS status,
           CASE WHEN sts = 'LULUS' THEN 'Lulus' ELSE '-' END AS lulus
    FROM tbl_fakultas
    $whereAngkatan
    ORDER BY nim ASC
");
if ($q4) { while ($r = mysqli_fetch_assoc($q4)) { $detailMhs[] = $r; } }

// ---------------------------------------------------------
// 5) Top mahasiswa berdasarkan IPK tertinggi
// ---------------------------------------------------------
$topIpk = [];
$q5 = mysqli_query($link, "
    SELECT f.nim, f.nama, i.ipk
    FROM tbl_mahasiswa_ipk i
    INNER JOIN tbl_fakultas f ON f.nim = i.nim
    ORDER BY i.ipk DESC, f.nama ASC
    LIMIT 10
");
if ($q5) { while ($r = mysqli_fetch_assoc($q5)) { $topIpk[] = $r; } }
?>

<div class="box">
    <h2>🧾 Rekap Data Akademik</h2>
    <p class="subjudul">Ringkasan agregat mahasiswa berdasarkan fakultas, prodi, status kelulusan, dan IPK</p>

    <!-- ============ 1. REKAP PER FAKULTAS ============ -->
    <h3>1. Jumlah Mahasiswa per Fakultas</h3>
    <div class="table-wrapper">
        <table class="data-table">
            <thead><tr><th>Fakultas</th><th>Nama Fakultas</th><th>Jumlah</th></tr></thead>
            <tbody>
            <?php if ($rekapFakultas): foreach ($rekapFakultas as $r): ?>
                <tr>
                    <td><span class="badge badge-nim"><?= htmlspecialchars($r['kdfakultas']) ?></span></td>
                    <td><?= htmlspecialchars(namaFakultas($namaFakultasMap, $r['kdfakultas'])) ?></td>
                    <td><strong><?= (int) $r['jumlah'] ?></strong></td>
                </tr>
            <?php endforeach; else: ?>
                <tr><td colspan="3" class="no-data">Belum ada data fakultas.</td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>

    <p class="subjudul" style="margin-top:18px;">Fakultas dengan jumlah mahasiswa di atas rata-rata (&gt; <?= number_format($avgFakultas, 1) ?>)</p>
    <div class="table-wrapper">
        <table class="data-table">
            <thead><tr><th>Fakultas</th><th>Jumlah</th></tr></thead>
            <tbody>
            <?php if ($rekapFakultasTop): foreach ($rekapFakultasTop as $r): ?>
                <tr>
                    <td><span class="badge badge-nim"><?= htmlspecialchars($r['kdfakultas']) ?></span></td>
                    <td><strong><?= (int) $r['jumlah'] ?></strong></td>
                </tr>
            <?php endforeach; else: ?>
                <tr><td colspan="2" class="no-data">Tidak ada fakultas di atas rata-rata.</td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- ============ 2. REKAP PER PRODI ============ -->
    <h3 style="margin-top:32px;">2. Jumlah Mahasiswa per Program Studi</h3>
    <div class="table-wrapper">
        <table class="data-table">
            <thead><tr><th>Prodi</th><th>Jumlah</th></tr></thead>
            <tbody>
            <?php if ($rekapProdi): foreach ($rekapProdi as $r): ?>
                <tr>
                    <td><span class="badge badge-sks"><?= htmlspecialchars($r['prodi']) ?></span></td>
                    <td><strong><?= (int) $r['jumlah'] ?></strong></td>
                </tr>
            <?php endforeach; else: ?>
                <tr><td colspan="2" class="no-data">Belum ada data prodi.</td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>

    <p class="subjudul" style="margin-top:18px;">Prodi dengan jumlah mahasiswa di atas rata-rata (&gt; <?= number_format($avgProdi, 1) ?>)</p>
    <div class="table-wrapper">
        <table class="data-table">
            <thead><tr><th>Prodi</th><th>Jumlah</th></tr></thead>
            <tbody>
            <?php if ($rekapProdiTop): foreach ($rekapProdiTop as $r): ?>
                <tr>
                    <td><span class="badge badge-sks"><?= htmlspecialchars($r['prodi']) ?></span></td>
                    <td><strong><?= (int) $r['jumlah'] ?></strong></td>
                </tr>
            <?php endforeach; else: ?>
                <tr><td colspan="2" class="no-data">Tidak ada prodi di atas rata-rata.</td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- ============ 3. REKAP LULUS PER FAKULTAS ============ -->
    <h3 style="margin-top:32px;">3. Jumlah Mahasiswa Lulus per Fakultas</h3>
    <div class="table-wrapper">
        <table class="data-table">
            <thead><tr><th>Fakultas</th><th>Nama Fakultas</th><th>Jumlah Lulus</th></tr></thead>
            <tbody>
            <?php if ($rekapLulus): foreach ($rekapLulus as $r): ?>
                <tr>
                    <td><span class="badge badge-nim"><?= htmlspecialchars($r['kdfakultas']) ?></span></td>
                    <td><?= htmlspecialchars(namaFakultas($namaFakultasMap, $r['kdfakultas'])) ?></td>
                    <td><strong><?= (int) $r['jumlah_lulus'] ?></strong></td>
                </tr>
            <?php endforeach; else: ?>
                <tr><td colspan="3" class="no-data">Belum ada data kelulusan.</td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>

    <p class="subjudul" style="margin-top:18px;">Fakultas dengan kelulusan di atas rata-rata (&gt; <?= number_format($avgLulus, 1) ?>)</p>
    <div class="table-wrapper">
        <table class="data-table">
            <thead><tr><th>Fakultas</th><th>Jumlah Lulus</th></tr></thead>
            <tbody>
            <?php if ($rekapLulusTop): foreach ($rekapLulusTop as $r): ?>
                <tr>
                    <td><span class="badge badge-nim"><?= htmlspecialchars($r['kdfakultas']) ?></span></td>
                    <td><strong><?= (int) $r['jumlah_lulus'] ?></strong></td>
                </tr>
            <?php endforeach; else: ?>
                <tr><td colspan="2" class="no-data">Tidak ada fakultas di atas rata-rata.</td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- ============ 4. DETAIL DATA MAHASISWA ============ -->
    <h3 style="margin-top:32px;">4. Detail Data Mahasiswa</h3>
    <form method="GET" action="index.php" class="form-row" style="align-items:flex-end;margin-bottom:14px;">
        <input type="hidden" name="hal" value="rekap">
        <div class="form-group" style="max-width:220px;">
            <label>Filter Angkatan</label>
            <select name="angkatan" onchange="this.form.submit()">
                <option value="">-- Semua Angkatan --</option>
                <?php foreach ($listAngkatan as $ang): ?>
                    <option value="<?= htmlspecialchars($ang) ?>" <?= ($filterAngkatan === (string) $ang) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($ang) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
    </form>
    <div class="table-wrapper">
        <table class="data-table">
            <thead>
                <tr>
                    <th>No</th><th>NIM</th><th>Nama</th><th>Fakultas</th>
                    <th>Prodi</th><th>Angkatan</th><th>Status</th><th>Lulus</th>
                </tr>
            </thead>
            <tbody>
            <?php if ($detailMhs): $no = 1; foreach ($detailMhs as $r): ?>
                <tr>
                    <td><?= $no++ ?></td>
                    <td><?= htmlspecialchars($r['nim']) ?></td>
                    <td><?= htmlspecialchars($r['nama']) ?></td>
                    <td><?= htmlspecialchars($r['kdfakultas'] ?? '-') ?></td>
                    <td><?= htmlspecialchars($r['prodi'] ?? '-') ?></td>
                    <td><?= htmlspecialchars($r['angkatan'] ?? '-') ?></td>
                    <td><?= htmlspecialchars($r['status']) ?></td>
                    <td><?= htmlspecialchars($r['lulus']) ?></td>
                </tr>
            <?php endforeach; else: ?>
                <tr><td colspan="8" class="no-data">Belum ada data mahasiswa.</td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- ============ 5. TOP MAHASISWA BERDASARKAN IPK ============ -->
    <h3 style="margin-top:32px;">5. Top 10 Mahasiswa Berdasarkan IPK</h3>
    <div class="table-wrapper">
        <table class="data-table">
            <thead><tr><th>NIM</th><th>Nama</th><th>IPK</th></tr></thead>
            <tbody>
            <?php if ($topIpk): foreach ($topIpk as $r): ?>
                <tr>
                    <td><span class="badge badge-nim"><?= htmlspecialchars($r['nim']) ?></span></td>
                    <td><?= htmlspecialchars($r['nama']) ?></td>
                    <td><strong><?= number_format((float) $r['ipk'], 2) ?></strong></td>
                </tr>
            <?php endforeach; else: ?>
                <tr><td colspan="3" class="no-data">Belum ada data IPK untuk ditampilkan.</td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
