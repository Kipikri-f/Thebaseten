<?php
// =====================================================
// PAGE: Anggota Kelompok
// =====================================================

require_once __DIR__ . '/../includes/koneksi.php';

// --- SIMPAN ---
if (isset($_POST['simpan'])) {
    $nim     = mysqli_real_escape_string($link, trim($_POST['nim']));
    $nama    = mysqli_real_escape_string($link, trim($_POST['namamhs']));
    $jurusan = mysqli_real_escape_string($link, trim($_POST['jurusan']));
    $ok = mysqli_query($link, "INSERT INTO tbl_anggota VALUES('$nim','$nama','$jurusan')");
    header('Location: index.php?hal=anggota&msg=' . ($ok ? 'added' : 'err'));
    exit;
}

// --- UPDATE ---
if (isset($_POST['update'])) {
    $nim_lama = mysqli_real_escape_string($link, $_POST['nim_lama']);
    $nim_baru = mysqli_real_escape_string($link, trim($_POST['nim_baru']));
    $nama     = mysqli_real_escape_string($link, trim($_POST['namamhs']));
    $jurusan  = mysqli_real_escape_string($link, trim($_POST['jurusan']));
    $ok = mysqli_query($link, "UPDATE tbl_anggota SET nim='$nim_baru', namamhs='$nama', jurusan='$jurusan' WHERE nim='$nim_lama'");
    header('Location: index.php?hal=anggota&msg=' . ($ok ? 'updated' : 'err_upd'));
    exit;
}

// --- HAPUS ---
if (isset($_GET['hapus'])) {
    $nim = mysqli_real_escape_string($link, $_GET['hapus']);
    $ok  = mysqli_query($link, "DELETE FROM tbl_anggota WHERE nim='$nim'");
    header('Location: index.php?hal=anggota&msg=' . ($ok ? 'deleted' : 'err_del'));
    exit;
}

$action = $_GET['action'] ?? 'tampil';
$msg    = '';

// Fetch edit data
$data_edit = null;
if ($action === 'edit' && isset($_GET['nim'])) {
    $nim = mysqli_real_escape_string($link, $_GET['nim']);
    $res = mysqli_query($link, "SELECT * FROM tbl_anggota WHERE nim='$nim'");
    $data_edit = mysqli_fetch_assoc($res);
}
?>

<div class="box">
    <h2>👥 Anggota Kelompok</h2>
    <p class="subjudul">Kelompok 10 &mdash; Universitas Djuanda 2026</p>

    <?php if ($action === 'tambah'): ?>

        <h3>Tambah Anggota Kelompok</h3>
        <form method="POST" action="index.php?hal=anggota&action=tambah">
            <div class="form-group">
                <label>NIM</label>
                <input type="text" name="nim" required placeholder="Masukkan NIM">
            </div>
            <div class="form-group">
                <label>Nama</label>
                <input type="text" name="namamhs" required placeholder="Masukkan Nama Lengkap">
            </div>
            <div class="form-group">
                <label>Jurusan</label>
                <input type="text" name="jurusan" required placeholder="Masukkan Jurusan">
            </div>
            <div class="btn-group">
                <button type="submit" name="simpan" class="btn-simpan">Tambah Data</button>
                <a href="index.php?hal=anggota" class="btn-kembali">Batal</a>
            </div>
        </form>

    <?php elseif ($action === 'edit' && $data_edit): ?>

        <h3>Edit Data Anggota</h3>
        <form method="POST" action="index.php?hal=anggota&action=edit">
            <input type="hidden" name="nim_lama" value="<?= htmlspecialchars($data_edit['nim']) ?>">
            <div class="form-group">
                <label>NIM</label>
                <input type="text" name="nim_baru" value="<?= htmlspecialchars($data_edit['nim']) ?>" required>
            </div>
            <div class="form-group">
                <label>Nama</label>
                <input type="text" name="namamhs" value="<?= htmlspecialchars($data_edit['namamhs']) ?>" required>
            </div>
            <div class="form-group">
                <label>Jurusan</label>
                <input type="text" name="jurusan" value="<?= htmlspecialchars($data_edit['jurusan']) ?>" required>
            </div>
            <div class="btn-group">
                <button type="submit" name="update" class="btn-update">Update Data</button>
                <a href="index.php?hal=anggota" class="btn-kembali">Batal</a>
            </div>
        </form>

    <?php else: ?>

        <a href="index.php?hal=anggota&action=tambah" class="btn-tambah">+ Tambah Anggota</a>

        <div class="table-wrapper">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>NIM</th>
                        <th>Nama</th>
                        <th>Jurusan</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                <?php
                $query = mysqli_query($link, "SELECT * FROM tbl_anggota ORDER BY namamhs ASC");
                $i = 1; $ada = false;
                while ($data = mysqli_fetch_assoc($query)):
                    $ada = true;
                ?>
                    <tr>
                        <td><?= $i++ ?></td>
                        <td><?= htmlspecialchars($data['nim']) ?></td>
                        <td><?= htmlspecialchars($data['namamhs']) ?></td>
                        <td><?= htmlspecialchars($data['jurusan']) ?></td>
                        <td>
                            <a href="index.php?hal=anggota&action=edit&nim=<?= urlencode($data['nim']) ?>" class="btn-edit">Edit</a>
                            <a href="index.php?hal=anggota&hapus=<?= urlencode($data['nim']) ?>"
                               onclick="return confirm('Yakin ingin menghapus data ini?')"
                               class="btn-hapus">Hapus</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
                <?php if (!$ada): ?>
                    <tr><td colspan="5" class="no-data">Belum ada data anggota kelompok.</td></tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>

    <?php endif; ?>
</div>
