<?php
// =====================================================
// PAGE: Mahasiswa
// =====================================================

require_once __DIR__ . '/../includes/koneksi.php';

$action = $_GET['action'] ?? 'tampil';

// --- TAMBAH ---
if (isset($_POST['submit_tambah'])) {
    $nim     = mysqli_real_escape_string($link, trim($_POST['nim']));
    $namamhs = mysqli_real_escape_string($link, trim($_POST['namamhs']));

    if ($nim !== '' && $namamhs !== '') {
        $query = "INSERT INTO tbl_mhs (nim, namamhs) VALUES ('$nim', '$namamhs')";
        if (mysqli_query($link, $query)) {
            header('Location: index.php?hal=mahasiswa&msg=added');
            exit;
        } else {
            $error = 'Gagal menambah data. NIM mungkin sudah ada.';
        }
    }
}

// --- EDIT ---
if (isset($_POST['submit_edit'])) {
    $nim     = mysqli_real_escape_string($link, $_POST['nim']);
    $namamhs = mysqli_real_escape_string($link, trim($_POST['namamhs']));

    if ($namamhs !== '') {
        $query = "UPDATE tbl_mhs SET namamhs='$namamhs' WHERE nim='$nim'";
        if (mysqli_query($link, $query)) {
            header('Location: index.php?hal=mahasiswa&msg=updated');
            exit;
        } else {
            $error = 'Gagal mengubah data.';
        }
    }
}

// --- HAPUS ---
if ($action === 'delete' && isset($_GET['nim'])) {
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
if ($action === 'edit' && isset($_GET['nim'])) {
    $nim_get = mysqli_real_escape_string($link, $_GET['nim']);
    $res     = mysqli_query($link, "SELECT * FROM tbl_mhs WHERE nim='$nim_get'");
    $data_edit = mysqli_fetch_array($res);
    if (!$data_edit) {
        header('Location: index.php?hal=mahasiswa');
        exit;
    }
}

// Flash messages
$msg = $_GET['msg'] ?? '';
?>

<div class="box">
    <h2>🎓 Data Mahasiswa</h2>
    <p class="subjudul">Kelola data mahasiswa dengan mudah</p>

    <?php if ($msg === 'added'): ?>
        <div class="alert alert-success" style="margin-bottom:16px;">✅ Data berhasil ditambahkan.</div>
    <?php elseif ($msg === 'updated'): ?>
        <div class="alert alert-success" style="margin-bottom:16px;">✅ Data berhasil diperbarui.</div>
    <?php elseif ($msg === 'deleted'): ?>
        <div class="alert alert-success" style="margin-bottom:16px;">✅ Data berhasil dihapus.</div>
    <?php elseif ($msg === 'err'): ?>
        <div class="alert alert-error" style="margin-bottom:16px;">⚠️ Operasi gagal dilakukan.</div>
    <?php endif; ?>

    <?php if (isset($error)): ?>
        <div class="alert alert-error" style="margin-bottom:16px;">⚠️ <?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <?php if ($action === 'tambah'): ?>

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
            <div class="btn-group">
                <button type="submit" name="submit_tambah" class="btn-simpan">Simpan Data</button>
                <a href="index.php?hal=mahasiswa" class="btn-kembali">Batal</a>
            </div>
        </form>

    <?php elseif ($action === 'edit' && $data_edit): ?>

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
            <div class="btn-group">
                <button type="submit" name="submit_edit" class="btn-update">Update Data</button>
                <a href="index.php?hal=mahasiswa" class="btn-kembali">Batal</a>
            </div>
        </form>

    <?php else: ?>

        <a href="index.php?hal=mahasiswa&action=tambah" class="btn-tambah">+ Tambah Data</a>

        <div class="table-wrapper">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>NIM</th>
                        <th>Nama Mahasiswa</th>
                        <th>Aksi</th>
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
                        <td>
                            <a class="btn-edit" href="index.php?hal=mahasiswa&action=edit&nim=<?= urlencode($data['nim']) ?>">Edit</a>
                            <a class="btn-hapus" href="index.php?hal=mahasiswa&action=delete&nim=<?= urlencode($data['nim']) ?>"
                               onclick="return confirm('Yakin ingin menghapus data mahasiswa ini?')">Hapus</a>
                        </td>
                    </tr>
                <?php endwhile;
                } else {
                    echo "<tr><td colspan='4' class='no-data'>Belum ada data mahasiswa.</td></tr>";
                } ?>
                </tbody>
            </table>
        </div>

    <?php endif; ?>
</div>
