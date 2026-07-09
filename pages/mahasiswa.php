<?php
// =====================================================
// PAGE: Mahasiswa
// =====================================================
// Catatan: tabel tbl_mhs pada database `learnclidatabase`
// hanya punya kolom nim, namamhs, handphone (tidak ada
// kode_fakultas/kode_prodi/angkatan, dan tidak ada tabel
// tbl_prodi). Halaman ini disesuaikan dengan skema nyata
// tersebut. Data fakultas/prodi/angkatan/IPK ditampilkan
// terpisah di halaman Rekap (berasal dari tbl_fakultas &
// tbl_mahasiswa_ipk, yang memakai penomoran NIM berbeda).

require_once __DIR__ . '/../includes/koneksi.php';

$action = $_GET['action'] ?? 'tampil';

// Block non-admin from edit/delete/tambah
if (!canEdit() && in_array($action, ['tambah', 'edit', 'delete'])) {
    header('Location: index.php?hal=mahasiswa&msg=no_access');
    exit;
}

// --- TAMBAH ---
if (canEdit() && isset($_POST['submit_tambah'])) {
    $nim       = mysqli_real_escape_string($link, trim($_POST['nim']));
    $namamhs   = mysqli_real_escape_string($link, trim($_POST['namamhs']));
    $handphone = trim($_POST['handphone'] ?? '');
    $hpVal     = ($handphone !== '') ? "'" . mysqli_real_escape_string($link, $handphone) . "'" : 'NULL';

    if ($nim !== '' && $namamhs !== '') {
        $query = "INSERT INTO tbl_mhs (nim, namamhs, handphone) VALUES ('$nim', '$namamhs', $hpVal)";
        $ok    = mysqli_query($link, $query);
        header('Location: index.php?hal=mahasiswa&msg=' . ($ok ? 'added' : 'err'));
        exit;
    }
}

// --- EDIT ---
if (canEdit() && isset($_POST['submit_edit'])) {
    $nim       = mysqli_real_escape_string($link, $_POST['nim']);
    $namamhs   = mysqli_real_escape_string($link, trim($_POST['namamhs']));
    $handphone = trim($_POST['handphone'] ?? '');
    $hpVal     = ($handphone !== '') ? "'" . mysqli_real_escape_string($link, $handphone) . "'" : 'NULL';

    if ($namamhs !== '') {
        $query = "UPDATE tbl_mhs SET namamhs='$namamhs', handphone=$hpVal WHERE nim='$nim'";
        $ok    = mysqli_query($link, $query);
        header('Location: index.php?hal=mahasiswa&msg=' . ($ok ? 'updated' : 'err_upd'));
        exit;
    }
}

// --- HAPUS ---
if (canEdit() && $action === 'delete' && isset($_GET['nim'])) {
    $nim = mysqli_real_escape_string($link, $_GET['nim']);
    if (mysqli_query($link, "DELETE FROM tbl_mhs WHERE nim='$nim'")) {
        header('Location: index.php?hal=mahasiswa&msg=deleted');
    } else {
        header('Location: index.php?hal=mahasiswa&msg=err');
    }
    exit;
}

// Fetch edit data
$data_edit = null;
if (canEdit() && $action === 'edit' && isset($_GET['nim'])) {
    $nim_get = mysqli_real_escape_string($link, $_GET['nim']);
    $res     = mysqli_query($link, "SELECT * FROM tbl_mhs WHERE nim='$nim_get'");
    $data_edit = mysqli_fetch_array($res);
    if (!$data_edit) {
        header('Location: index.php?hal=mahasiswa');
        exit;
    }
}

// Flash messages
$msg = '';
?>

<div class="box">
    <h2>🎓 Data Mahasiswa</h2>
    <p class="subjudul">
        <?= canEdit() ? 'Kelola data mahasiswa dengan mudah' : 'Hubungi Admin untuk perubahan data jika belum terubah' ?>
    </p>

    <?php if (canEdit() && $action === 'tambah'): ?>

        <h3>Tambah Data Mahasiswa</h3>
        <form method="POST" action="index.php?hal=mahasiswa&action=tambah">
            <div class="form-group">
                <label>NIM</label>
                <input type="text" name="nim" required placeholder="Masukkan NIM">
            </div>
            <div class="form-group">
                <label>Nama Mahasiswa</label>
                <input type="text" name="namamhs" required placeholder="Masukkan Nama Lengkap">
            </div>
            <div class="form-group">
                <label>No. Handphone</label>
                <input type="text" name="handphone" placeholder="cth. 08123456789">
            </div>
            <div class="btn-group">
                <button type="submit" name="submit_tambah" class="btn-simpan">Simpan Data</button>
                <a href="index.php?hal=mahasiswa" class="btn-kembali">Batal</a>
            </div>
        </form>

    <?php elseif (canEdit() && $action === 'edit' && $data_edit): ?>

        <h3>Edit Data Mahasiswa</h3>
        <form method="POST" action="index.php?hal=mahasiswa&action=edit">
            <div class="form-group">
                <label>NIM (tidak dapat diubah)</label>
                <input type="text" name="nim" value="<?= htmlspecialchars($data_edit['nim']) ?>" readonly>
            </div>
            <div class="form-group">
                <label>Nama Mahasiswa</label>
                <input type="text" name="namamhs" value="<?= htmlspecialchars($data_edit['namamhs']) ?>" required>
            </div>
            <div class="form-group">
                <label>No. Handphone</label>
                <input type="text" name="handphone" value="<?= htmlspecialchars($data_edit['handphone'] ?? '') ?>" placeholder="cth. 08123456789">
            </div>
            <div class="btn-group">
                <button type="submit" name="submit_edit" class="btn-update">Update Data</button>
                <a href="index.php?hal=mahasiswa" class="btn-kembali">Batal</a>
            </div>
        </form>

    <?php else: ?>

        <?php if (canEdit()): ?>
        <a href="index.php?hal=mahasiswa&action=tambah" class="btn-tambah">+ Tambah Data</a>
        <?php endif; ?>

        <div class="table-wrapper">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>NIM</th>
                        <th>Nama Mahasiswa</th>
                        <th>No. Handphone</th>
                        <?php if (canEdit()): ?><th>Aksi</th><?php endif; ?>
                    </tr>
                </thead>
                <tbody>
                <?php
                $result = mysqli_query($link, "SELECT * FROM tbl_mhs ORDER BY nim ASC");
                if ($result && mysqli_num_rows($result) > 0) {
                    $no = 1;
                    while ($data = mysqli_fetch_array($result)):
                ?>
                    <tr>
                        <td><?= $no++ ?></td>
                        <td><?= htmlspecialchars($data['nim']) ?></td>
                        <td><?= htmlspecialchars($data['namamhs']) ?></td>
                        <td><?= htmlspecialchars($data['handphone'] ?? '-') ?></td>
                        <?php if (canEdit()): ?>
                        <td>
                            <a class="btn-edit" href="index.php?hal=mahasiswa&action=edit&nim=<?= urlencode($data['nim']) ?>">Edit</a>
                            <a class="btn-hapus" href="index.php?hal=mahasiswa&action=delete&nim=<?= urlencode($data['nim']) ?>"
                               onclick="return confirm('Yakin ingin menghapus data mahasiswa ini?')">Hapus</a>
                        </td>
                        <?php endif; ?>
                    </tr>
                <?php endwhile;
                } else {
                    $colspan = canEdit() ? 5 : 4;
                    echo "<tr><td colspan='$colspan' class='no-data'>Belum ada data mahasiswa.</td></tr>";
                } ?>
                </tbody>
            </table>
        </div>

    <?php endif; ?>
</div>
