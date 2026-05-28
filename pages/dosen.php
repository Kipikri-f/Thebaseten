<?php
// =====================================================
// PAGE: Dosen
// =====================================================

require_once __DIR__ . '/../includes/koneksi.php';

// --- CREATE / UPDATE via POST ---
if (canEdit() && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $nid       = mysqli_real_escape_string($link, trim($_POST['nid'] ?? ''));
    $namadosen = mysqli_real_escape_string($link, trim($_POST['namadosen'] ?? ''));
    $act       = $_POST['action'] ?? '';

    if ($act === 'create' && $nid !== '' && $namadosen !== '') {
        mysqli_query($link, "INSERT INTO tbl_dosen (nid, namadosen) VALUES ('$nid', '$namadosen')");
    } elseif ($act === 'update' && isset($_POST['old_nid']) && $_POST['old_nid'] !== '') {
        $old = mysqli_real_escape_string($link, $_POST['old_nid']);
        mysqli_query($link, "UPDATE tbl_dosen SET nid='$nid', namadosen='$namadosen' WHERE nid='$old'");
    }

    header('Location: index.php?hal=dosen');
    exit;
}

// --- DELETE via GET ---
if (canEdit() && isset($_GET['delete'])) {
    $del = mysqli_real_escape_string($link, $_GET['delete']);
    mysqli_query($link, "DELETE FROM tbl_dosen WHERE nid='$del'");
    header('Location: index.php?hal=dosen');
    exit;
}

// --- EDIT mode ---
$editMode = false;
$editNid  = '';
$editNama = '';

if (canEdit() && isset($_GET['edit'])) {
    $editId = mysqli_real_escape_string($link, $_GET['edit']);
    $res    = mysqli_query($link, "SELECT * FROM tbl_dosen WHERE nid='$editId'");
    if ($res && mysqli_num_rows($res) === 1) {
        $row      = mysqli_fetch_assoc($res);
        $editMode = true;
        $editNid  = $row['nid'];
        $editNama = $row['namadosen'];
    }
}

$result = mysqli_query($link, "SELECT * FROM tbl_dosen ORDER BY namadosen ASC");
?>

<div class="box">
    <h2>📋 Data Dosen</h2>
    <p class="subjudul">
        <?= canEdit() ? 'Kelola data dosen pengajar' : 'Hubungi admin untuk perubahan data jika belum terubah' ?>
    </p>

    <?php if (canEdit()): ?>
    <h3><?= $editMode ? 'Edit Data Dosen' : 'Tambah Dosen Baru' ?></h3>

    <form method="POST" action="index.php?hal=dosen">
        <input type="hidden" name="action" value="<?= $editMode ? 'update' : 'create' ?>">
        <?php if ($editMode): ?>
            <input type="hidden" name="old_nid" value="<?= htmlspecialchars($editNid) ?>">
        <?php endif; ?>

        <div class="form-group">
            <label>NID</label>
            <input type="text" name="nid" value="<?= htmlspecialchars($editNid) ?>" required placeholder="Masukkan NID Dosen">
        </div>
        <div class="form-group">
            <label>Nama Dosen</label>
            <input type="text" name="namadosen" value="<?= htmlspecialchars($editNama) ?>" required placeholder="Masukkan Nama Dosen">
        </div>
        <div class="btn-group">
            <button type="submit"><?= $editMode ? 'Perbarui Dosen' : 'Tambah Dosen' ?></button>
            <?php if ($editMode): ?>
                <button type="button" class="btn-cancel-js" id="cancelBtn">Batal</button>
            <?php endif; ?>
        </div>
    </form>
    <?php endif; ?>

    <div class="table-wrapper">
        <table class="data-table">
            <thead>
                <tr>
                    <th>No</th>
                    <th>NID</th>
                    <th>Nama Dosen</th>
                    <?php if (canEdit()): ?><th>Aksi</th><?php endif; ?>
                </tr>
            </thead>
            <tbody>
            <?php
            if ($result && mysqli_num_rows($result) > 0) {
                $no = 1;
                while ($row = mysqli_fetch_assoc($result)):
            ?>
                <tr>
                    <td><?= $no++ ?></td>
                    <td><?= htmlspecialchars($row['nid']) ?></td>
                    <td><?= htmlspecialchars($row['namadosen']) ?></td>
                    <?php if (canEdit()): ?>
                    <td>
                        <a class="btn-edit"  href="index.php?hal=dosen&edit=<?= urlencode($row['nid']) ?>">Edit</a>
                        <a class="btn-hapus" href="index.php?hal=dosen&delete=<?= urlencode($row['nid']) ?>"
                           onclick="return confirm('Hapus dosen ini?')">Hapus</a>
                    </td>
                    <?php endif; ?>
                </tr>
            <?php endwhile;
            } else {
                $colspan = canEdit() ? 4 : 3;
                echo "<tr><td colspan='$colspan' class='no-data'>Belum ada data dosen.</td></tr>";
            } ?>
            </tbody>
        </table>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    var cancel = document.getElementById('cancelBtn');
    if (!cancel) return;
    cancel.addEventListener('click', function () {
        var form = document.querySelector('form');
        form.querySelector('[name="action"]').value = 'create';
        var old = form.querySelector('[name="old_nid"]');
        if (old) old.remove();
        form.querySelector('[name="nid"]').value = '';
        form.querySelector('[name="namadosen"]').value = '';
        form.querySelector('[type="submit"]').textContent = 'Tambah Dosen';
        cancel.remove();
    });
});
</script>
