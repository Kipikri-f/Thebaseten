<?php
// =====================================================
// PAGE: Dosen Pembimbing
// =====================================================

require_once __DIR__ . '/../includes/koneksi.php';

$edit = false;
$nim = $nid = $nim_lama = '';

// --- SIMPAN ---
if (isset($_POST['simpan'])) {
    $nim = mysqli_real_escape_string($link, $_POST['nim']);
    $nid = mysqli_real_escape_string($link, $_POST['nid']);
    $cek = mysqli_query($link, "SELECT * FROM tbl_dopem WHERE nim='$nim' AND nid='$nid'");
    if (mysqli_num_rows($cek) > 0) {
        $error = 'Data dosen pembimbing untuk mahasiswa ini sudah ada.';
    } else {
        mysqli_query($link, "INSERT INTO tbl_dopem (nim, nid) VALUES ('$nim', '$nid')");
        header('Location: index.php?hal=dopem&msg=added');
        exit;
    }
}

// --- HAPUS ---
if (isset($_GET['hapus'])) {
    $nim = mysqli_real_escape_string($link, $_GET['hapus']);
    mysqli_query($link, "DELETE FROM tbl_dopem WHERE nim='$nim'");
    header('Location: index.php?hal=dopem&msg=deleted');
    exit;
}

// --- EDIT ---
if (isset($_GET['edit'])) {
    $edit    = true;
    $nim_e   = mysqli_real_escape_string($link, $_GET['edit']);
    $q       = mysqli_query($link, "SELECT * FROM tbl_dopem WHERE nim='$nim_e'");
    $d       = mysqli_fetch_assoc($q);
    if ($d) {
        $nim      = $d['nim'];
        $nid      = $d['nid'];
        $nim_lama = $d['nim'];
    }
}

// --- UPDATE ---
if (isset($_POST['update'])) {
    $nim      = mysqli_real_escape_string($link, $_POST['nim']);
    $nid      = mysqli_real_escape_string($link, $_POST['nid']);
    $nim_lama = mysqli_real_escape_string($link, $_POST['nim_lama']);
    $cek      = mysqli_query($link, "SELECT * FROM tbl_dopem WHERE nim='$nim' AND nid='$nid' AND nim!='$nim_lama'");
    if (mysqli_num_rows($cek) > 0) {
        $error = 'Data sudah ada untuk kombinasi mahasiswa dan dosen tersebut.';
    } else {
        mysqli_query($link, "UPDATE tbl_dopem SET nim='$nim', nid='$nid' WHERE nim='$nim_lama'");
        header('Location: index.php?hal=dopem&msg=updated');
        exit;
    }
}

$msg = $_GET['msg'] ?? '';
?>

<div class="box">
    <h2>👨‍🏫 Data Dosen Pembimbing</h2>
    <p class="subjudul">Kelola relasi mahasiswa dengan dosen pembimbingnya</p>

    <?php if ($msg === 'added'): ?>
        <div class="alert alert-success" style="margin-bottom:16px;">✅ Data berhasil ditambahkan.</div>
    <?php elseif ($msg === 'updated'): ?>
        <div class="alert alert-success" style="margin-bottom:16px;">✅ Data berhasil diperbarui.</div>
    <?php elseif ($msg === 'deleted'): ?>
        <div class="alert alert-success" style="margin-bottom:16px;">✅ Data berhasil dihapus.</div>
    <?php endif; ?>

    <?php if (isset($error)): ?>
        <div class="alert alert-error" style="margin-bottom:16px;">⚠️ <?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <h3><?= $edit ? 'Edit Dosen Pembimbing' : 'Tambah Dosen Pembimbing' ?></h3>

    <form method="POST" action="index.php?hal=dopem">
        <input type="hidden" name="nim_lama" value="<?= htmlspecialchars($nim_lama) ?>">

        <div class="form-group">
            <label>Pilih Mahasiswa</label>
            <select name="nim" required>
                <option value="">-- Pilih Mahasiswa --</option>
                <?php
                $mhs = mysqli_query($link, "SELECT * FROM tbl_mhs ORDER BY namamhs");
                while ($m = mysqli_fetch_assoc($mhs)):
                ?>
                    <option value="<?= htmlspecialchars($m['nim']) ?>"
                        <?= ($nim === $m['nim']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($m['namamhs']) ?> (<?= htmlspecialchars($m['nim']) ?>)
                    </option>
                <?php endwhile; ?>
            </select>
        </div>

        <div class="form-group">
            <label>Pilih Dosen</label>
            <select name="nid" required>
                <option value="">-- Pilih Dosen --</option>
                <?php
                $dsn = mysqli_query($link, "SELECT * FROM tbl_dosen ORDER BY namadosen");
                while ($d = mysqli_fetch_assoc($dsn)):
                ?>
                    <option value="<?= htmlspecialchars($d['nid']) ?>"
                        <?= ($nid === $d['nid']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($d['namadosen']) ?> (<?= htmlspecialchars($d['nid']) ?>)
                    </option>
                <?php endwhile; ?>
            </select>
        </div>

        <div class="btn-group">
            <?php if ($edit): ?>
                <button name="update" class="btn-update">Update Data</button>
                <a href="index.php?hal=dopem" class="btn-kembali">Batal</a>
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
                    <th>Nama Mahasiswa</th>
                    <th>NID</th>
                    <th>Nama Dosen</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
            <?php
            $data = mysqli_query($link,
                "SELECT tbl_dopem.nim, tbl_mhs.namamhs, tbl_dopem.nid, tbl_dosen.namadosen
                 FROM tbl_dopem
                 INNER JOIN tbl_mhs   ON tbl_dopem.nim = tbl_mhs.nim
                 INNER JOIN tbl_dosen ON tbl_dopem.nid = tbl_dosen.nid
                 ORDER BY tbl_dopem.nim"
            );
            $no = 1; $ada = false;
            while ($row = mysqli_fetch_assoc($data)):
                $ada = true;
            ?>
                <tr>
                    <td><?= $no++ ?></td>
                    <td><?= htmlspecialchars($row['nim']) ?></td>
                    <td><?= htmlspecialchars($row['namamhs']) ?></td>
                    <td><?= htmlspecialchars($row['nid']) ?></td>
                    <td><?= htmlspecialchars($row['namadosen']) ?></td>
                    <td>
                        <a class="btn-edit"  href="index.php?hal=dopem&edit=<?= urlencode($row['nim']) ?>">Edit</a>
                        <a class="btn-hapus" href="index.php?hal=dopem&hapus=<?= urlencode($row['nim']) ?>"
                           onclick="return confirm('Yakin hapus data ini?')">Hapus</a>
                    </td>
                </tr>
            <?php endwhile; ?>
            <?php if (!$ada): ?>
                <tr><td colspan="6" class="no-data">Belum ada data dosen pembimbing.</td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
