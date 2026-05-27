<?php
// =====================================================
// PAGE: Mata Kuliah
// =====================================================

require_once __DIR__ . '/../includes/koneksi.php';

// --- CREATE / UPDATE via POST ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $kodemk = mysqli_real_escape_string($link, trim($_POST['kodemk'] ?? ''));
    $namamk = mysqli_real_escape_string($link, trim($_POST['namamk'] ?? ''));
    $sks    = mysqli_real_escape_string($link, trim($_POST['sks']    ?? ''));
    $act    = $_POST['action'] ?? '';

    if ($act === 'create' && $kodemk && $namamk && $sks) {
        mysqli_query($link, "INSERT INTO tbl_matakuliah (kodemk, namamk, sks) VALUES ('$kodemk', '$namamk', '$sks')");
    } elseif ($act === 'update' && isset($_POST['old_kodemk']) && $_POST['old_kodemk'] !== '') {
        $old = mysqli_real_escape_string($link, $_POST['old_kodemk']);
        mysqli_query($link, "UPDATE tbl_matakuliah SET kodemk='$kodemk', namamk='$namamk', sks='$sks' WHERE kodemk='$old'");
    }

    header('Location: index.php?hal=matakuliah');
    exit;
}

// --- DELETE via GET ---
if (isset($_GET['delete'])) {
    $del = mysqli_real_escape_string($link, $_GET['delete']);
    mysqli_query($link, "DELETE FROM tbl_matakuliah WHERE kodemk='$del'");
    header('Location: index.php?hal=matakuliah');
    exit;
}

// --- EDIT mode ---
$editMode = false;
$editKode = $editNama = $editSks = '';

if (isset($_GET['edit'])) {
    $editId = mysqli_real_escape_string($link, $_GET['edit']);
    $res    = mysqli_query($link, "SELECT * FROM tbl_matakuliah WHERE kodemk='$editId'");
    if ($res && mysqli_num_rows($res) === 1) {
        $row      = mysqli_fetch_assoc($res);
        $editMode = true;
        $editKode = $row['kodemk'];
        $editNama = $row['namamk'];
        $editSks  = $row['sks'];
    }
}

$result = mysqli_query($link, "SELECT * FROM tbl_matakuliah ORDER BY kodemk ASC");
?>

<div class="box">
    <h2>📚 Data Mata Kuliah</h2>
    <p class="subjudul">Input dan modifikasi kurikulum serta bobot SKS mata kuliah</p>

    <h3><?= $editMode ? 'Edit Mata Kuliah' : 'Tambah Mata Kuliah Baru' ?></h3>

    <form method="POST" action="index.php?hal=matakuliah">
        <input type="hidden" name="action" value="<?= $editMode ? 'update' : 'create' ?>">
        <?php if ($editMode): ?>
            <input type="hidden" name="old_kodemk" value="<?= htmlspecialchars($editKode) ?>">
        <?php endif; ?>

        <div class="form-row">
            <div class="form-group" style="flex:1;min-width:120px;">
                <label>Kode MK</label>
                <input type="text" name="kodemk" value="<?= htmlspecialchars($editKode) ?>" required placeholder="Contoh: MK001">
            </div>
            <div class="form-group" style="flex:3;min-width:200px;">
                <label>Nama Mata Kuliah</label>
                <input type="text" name="namamk" value="<?= htmlspecialchars($editNama) ?>" required placeholder="Contoh: Pemrograman Web">
            </div>
            <div class="form-group sks-group">
                <label>SKS</label>
                <select name="sks" required>
                    <option value="">Pilih</option>
                    <?php foreach ([1,2,3,4] as $s): ?>
                        <option value="<?= $s ?>" <?= $editSks == $s ? 'selected' : '' ?>><?= $s ?> SKS</option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <div class="btn-group">
            <button type="submit" class="btn-simpan"><?= $editMode ? 'Perbarui' : 'Simpan' ?></button>
            <?php if ($editMode): ?>
                <button type="button" class="btn-cancel-js" id="cancelBtn">Batal</button>
            <?php endif; ?>
        </div>
    </form>

    <div class="table-wrapper">
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width:18%">Kode MK</th>
                    <th style="text-align:left;padding-left:24px;">Nama Mata Kuliah</th>
                    <th style="width:14%">SKS</th>
                    <th style="width:15%">Aksi</th>
                </tr>
            </thead>
            <tbody>
            <?php
            if ($result && mysqli_num_rows($result) > 0) {
                while ($row = mysqli_fetch_assoc($result)):
            ?>
                <tr>
                    <td><strong><?= htmlspecialchars($row['kodemk']) ?></strong></td>
                    <td style="text-align:left;padding-left:24px;"><?= htmlspecialchars($row['namamk']) ?></td>
                    <td><span class="badge badge-sks"><?= htmlspecialchars($row['sks']) ?> SKS</span></td>
                    <td>
                        <a class="btn-edit"  href="index.php?hal=matakuliah&edit=<?= urlencode($row['kodemk']) ?>">Edit</a>
                        <a class="btn-hapus" href="index.php?hal=matakuliah&delete=<?= urlencode($row['kodemk']) ?>"
                           onclick="return confirm('Hapus mata kuliah ini?')">Hapus</a>
                    </td>
                </tr>
            <?php endwhile;
            } else {
                echo "<tr><td colspan='4' class='no-data'>Belum ada data mata kuliah.</td></tr>";
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
        var old = form.querySelector('[name="old_kodemk"]');
        if (old) old.remove();
        form.querySelector('[name="kodemk"]').value = '';
        form.querySelector('[name="namamk"]').value = '';
        form.querySelector('[name="sks"]').value = '';
        form.querySelector('[type="submit"]').textContent = 'Simpan';
        cancel.remove();
    });
});
</script>
