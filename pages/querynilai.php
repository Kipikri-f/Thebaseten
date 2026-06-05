<?php
// =====================================================
// PAGE: Nilai Mahasiswa
// =====================================================

require_once __DIR__ . '/../includes/koneksi.php';

function hitungNilai(float $tugas, float $uts, float $uas): array
{
    $akhir = round(($tugas * 0.20) + ($uts * 0.35) + ($uas * 0.45), 2);

    if ($akhir >= 85)     $hm = 'A';
    elseif ($akhir >= 70) $hm = 'B';
    elseif ($akhir >= 55) $hm = 'C';
    elseif ($akhir >= 40) $hm = 'D';
    else                  $hm = 'E';

    if ($akhir >= 85)     $status = 'Lulus Sangat Memuaskan';
    elseif ($akhir >= 70) $status = 'Lulus Memuaskan';
    elseif ($akhir >= 55) $status = 'Lulus';
    elseif ($akhir >= 40) $status = 'Tidak Lulus';
    else                  $status = 'Tidak Lulus';
    return [$akhir, $hm, $status];
}

// --- SIMPAN ---
if (isset($_POST['simpan'])) {
    $nim   = mysqli_real_escape_string($link, $_POST['nim']);
    $cek   = mysqli_query($link, "SELECT * FROM tbl_nilai WHERE nim='$nim'");
    if (mysqli_num_rows($cek) > 0) {
        header('Location: index.php?hal=querynilai&msg=duplicate');
        exit;
    } else {
        $tugas = (float) $_POST['tugas'];
        $uts   = (float) $_POST['uts'];
        $uas   = (float) $_POST['uas'];
        [$akhir, $hm, $status] = hitungNilai($tugas, $uts, $uas);
        $ok = mysqli_query($link, "INSERT INTO tbl_nilai VALUES('$nim','$tugas','$uts','$uas','$akhir','$hm','$status')");
        header('Location: index.php?hal=querynilai&msg=' . ($ok ? 'added' : 'err'));
        exit;
    }
}

// --- HAPUS ---
if (isset($_GET['hapus'])) {
    $nim = mysqli_real_escape_string($link, $_GET['hapus']);
    $ok  = mysqli_query($link, "DELETE FROM tbl_nilai WHERE nim='$nim'");
    header('Location: index.php?hal=querynilai&msg=' . ($ok ? 'deleted' : 'err_del'));
    exit;
}

// --- EDIT ---
$editData = null;
$edit     = false;
if (isset($_GET['edit'])) {
    $nim_edit = mysqli_real_escape_string($link, $_GET['edit']);
    $q        = mysqli_query($link, "SELECT * FROM tbl_nilai WHERE nim='$nim_edit'");
    $editData = mysqli_fetch_assoc($q);
    if ($editData) $edit = true;
}

// --- UPDATE ---
if (isset($_POST['update'])) {
    $nim      = mysqli_real_escape_string($link, $_POST['nim']);
    $nim_lama = mysqli_real_escape_string($link, $_POST['nim_lama']);
    $tugas    = (float) $_POST['tugas'];
    $uts      = (float) $_POST['uts'];
    $uas      = (float) $_POST['uas'];

    if ($nim !== $nim_lama) {
        $cek = mysqli_query($link, "SELECT * FROM tbl_nilai WHERE nim='$nim'");
        if (mysqli_num_rows($cek) > 0) {
            header('Location: index.php?hal=querynilai&msg=duplicate');
            exit;
        }
    }

    [$akhir, $hm, $status] = hitungNilai($tugas, $uts, $uas);
    $ok = mysqli_query($link, "UPDATE tbl_nilai SET nim='$nim', tugas='$tugas', uts='$uts', uas='$uas',
                         akhir='$akhir', hm='$hm', status='$status' WHERE nim='$nim_lama'");
    header('Location: index.php?hal=querynilai&msg=' . ($ok ? 'updated' : 'err_upd'));
    exit;
}

$msg = '';
?>

<div class="box">
    <h2>📊 Data Nilai Mahasiswa</h2>
    <p class="subjudul">Data nilai mahasiswa selama pembelajaran</p>

    <h3><?= $edit ? 'Edit Data Nilai' : 'Input Nilai Mahasiswa' ?></h3>

    <form method="POST" action="index.php?hal=querynilai">
        <input type="hidden" name="nim_lama" value="<?= htmlspecialchars($editData['nim'] ?? '') ?>">

        <div class="form-group">
            <label>Mahasiswa</label>
            <select name="nim" required>
                <option value="">-- Pilih Mahasiswa --</option>
                <?php
                $mhs = mysqli_query($link, "SELECT * FROM tbl_mhs ORDER BY namamhs");
                while ($m = mysqli_fetch_assoc($mhs)):
                ?>
                    <option value="<?= htmlspecialchars($m['nim']) ?>"
                        <?= ($editData && $editData['nim'] === $m['nim']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($m['nim']) ?> — <?= htmlspecialchars($m['namamhs']) ?>
                    </option>
                <?php endwhile; ?>
            </select>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label>Nilai Tugas (20%)</label>
                <input type="number" name="tugas" placeholder="0 – 100" min="0" max="100" required
                       value="<?= htmlspecialchars($editData['tugas'] ?? '') ?>">
            </div>
            <div class="form-group">
                <label>Nilai UTS (35%)</label>
                <input type="number" name="uts" placeholder="0 – 100" min="0" max="100" required
                       value="<?= htmlspecialchars($editData['uts'] ?? '') ?>">
            </div>
            <div class="form-group">
                <label>Nilai UAS (45%)</label>
                <input type="number" name="uas" placeholder="0 – 100" min="0" max="100" required
                       value="<?= htmlspecialchars($editData['uas'] ?? '') ?>">
            </div>
        </div>

        <div class="btn-group">
            <?php if ($edit): ?>
                <button name="update" class="btn-update">Update Data</button>
                <a href="index.php?hal=querynilai" class="btn-kembali">Batal</a>
            <?php else: ?>
                <button name="simpan" class="btn-simpan">Simpan Data</button>
            <?php endif; ?>
        </div>
    </form>

    <div class="table-wrapper">
        <table class="data-table">
            <thead>
                <tr>
                    <th>No</th>
                    <th>NIM</th>
                    <th>Nama</th>
                    <th>Tugas</th>
                    <th>UTS</th>
                    <th>UAS</th>
                    <th>Akhir</th>
                    <th>HM</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
            <?php
            $q = mysqli_query($link,
                "SELECT tbl_nilai.*, tbl_mhs.namamhs
                 FROM tbl_nilai
                 INNER JOIN tbl_mhs ON tbl_nilai.nim = tbl_mhs.nim
                 ORDER BY tbl_nilai.nim"
            );
            $no = 1; $ada = false;
            while ($d = mysqli_fetch_assoc($q)):
                $ada = true;
                $badgeClass = match($d['status']) {
                    'Lulus Sangat Memuaskan' => 'badge-lulus-sm',
                    'Lulus Memuaskan'        => 'badge-lulus-m',
                    'Lulus'                  => 'badge-lulus',
                    default                  => 'badge-tidak',
                };
            ?>
                <tr>
                    <td><?= $no++ ?></td>
                    <td><?= htmlspecialchars($d['nim']) ?></td>
                    <td><?= htmlspecialchars($d['namamhs']) ?></td>
                    <td><?= htmlspecialchars($d['tugas']) ?></td>
                    <td><?= htmlspecialchars($d['uts']) ?></td>
                    <td><?= htmlspecialchars($d['uas']) ?></td>
                    <td><strong><?= htmlspecialchars($d['akhir']) ?></strong></td>
                    <td><span class="badge badge-nilai"><?= htmlspecialchars($d['hm']) ?></span></td>
                    <td><span class="badge <?= $badgeClass ?>"><?= htmlspecialchars($d['status']) ?></span></td>
                    <td>
                        <a class="btn-edit"  href="index.php?hal=querynilai&edit=<?= urlencode($d['nim']) ?>">Edit</a>
                        <a class="btn-hapus" href="index.php?hal=querynilai&hapus=<?= urlencode($d['nim']) ?>"
                           onclick="return confirm('Yakin hapus data nilai ini?')">Hapus</a>
                    </td>
                </tr>
            <?php endwhile; ?>
            <?php if (!$ada): ?>
                <tr><td colspan="10" class="no-data">Belum ada data nilai mahasiswa.</td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
