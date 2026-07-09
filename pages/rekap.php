<?php
// =====================================================
// PAGE: Rekap & Statistik
// Menampilkan ringkasan data akademik menggunakan
// fungsi agregat SQL: COUNT, SUM, DISTINCT, GROUP BY
// Tabel yang dipakai: tbl_mhs, tbl_nilai, tbl_dosen,
// tbl_dopem, tbl_matakuliah, tbl_anggota
// =====================================================

require_once __DIR__ . '/../includes/koneksi.php';

// ── 1. Ringkasan umum nilai (COUNT, SUM) ─────────────────────────────────
$sqlRingkasNilai = "SELECT
        COUNT(*)      AS total_dinilai,
        COUNT(DISTINCT hm) AS variasi_huruf_mutu
    FROM tbl_nilai";
$ringkasNilai = mysqli_fetch_assoc(mysqli_query($link, $sqlRingkasNilai));

$total_mhs = 0;
$r = mysqli_query($link, "SELECT COUNT(*) AS total FROM tbl_mhs");
if ($r) $total_mhs = (int) mysqli_fetch_assoc($r)['total'];

// ── 2. Distribusi Huruf Mutu (GROUP BY + COUNT) ──────────────────────────
$sqlHurufMutu = "SELECT hm, COUNT(*) AS jumlah
    FROM tbl_nilai
    GROUP BY hm
    ORDER BY hm ASC";
$rHurufMutu = mysqli_query($link, $sqlHurufMutu);

// ── 3. Distribusi Status Kelulusan (GROUP BY + COUNT) ────────────────────
$sqlStatus = "SELECT status, COUNT(*) AS jumlah
    FROM tbl_nilai
    GROUP BY status
    ORDER BY jumlah DESC";
$rStatus = mysqli_query($link, $sqlStatus);

// ── 4. Beban Bimbingan tiap Dosen (JOIN + GROUP BY + COUNT) ──────────────
$sqlBimbingan = "SELECT d.nid, d.namadosen, COUNT(dp.nim) AS jumlah_bimbingan
    FROM tbl_dopem dp
    INNER JOIN tbl_dosen d ON dp.nid = d.nid
    GROUP BY d.nid, d.namadosen
    ORDER BY jumlah_bimbingan DESC";
$rBimbingan = mysqli_query($link, $sqlBimbingan);

// ── 5. Rekap Mata Kuliah per SKS (GROUP BY + COUNT + SUM) ────────────────
$sqlSks = "SELECT sks, COUNT(*) AS jumlah_mk, SUM(sks) AS total_sks
    FROM tbl_matakuliah
    GROUP BY sks
    ORDER BY sks ASC";
$rSks = mysqli_query($link, $sqlSks);

$totalSksKurikulum = 0;
$rTotalSks = mysqli_query($link, "SELECT SUM(sks) AS total FROM tbl_matakuliah");
if ($rTotalSks) $totalSksKurikulum = (int) (mysqli_fetch_assoc($rTotalSks)['total'] ?? 0);

// ── 6. Data Mahasiswa & Dosen Pembimbing (JOIN) ──────────────────────────
$sqlMhsDopem = "SELECT m.nim, m.namamhs, d.namadosen
    FROM tbl_mhs m
    LEFT JOIN tbl_dopem dp ON dp.nim = m.nim
    LEFT JOIN tbl_dosen d  ON d.nid  = dp.nid
    ORDER BY m.nim ASC";
$rMhsDopem = mysqli_query($link, $sqlMhsDopem);

// ── 7. Daftar IPK Mahasiswa (dikonversi dari huruf mutu tbl_nilai) ───────
$sqlIpk = "SELECT m.nim, m.namamhs,
        CASE n.hm
            WHEN 'A' THEN 4.00
            WHEN 'B' THEN 3.00
            WHEN 'C' THEN 2.00
            WHEN 'D' THEN 1.00
            WHEN 'E' THEN 0.00
            ELSE NULL
        END AS ipk
    FROM tbl_mhs m
    LEFT JOIN tbl_nilai n ON n.nim = m.nim
    ORDER BY m.nim ASC";
$rIpk = mysqli_query($link, $sqlIpk);

$badgeHm = [
    'A' => 'badge-lulus-sm',
    'B' => 'badge-lulus-m',
    'C' => 'badge-lulus',
];
?>

<div class="box">
    <h2>📈 Rekap &amp; Statistik</h2>
    <p class="subjudul">Ringkasan data akademik secara keseluruhan</p>

    <!-- Stat Cards -->
    <div class="stats-row" style="grid-template-columns: repeat(3, 1fr);">
        <div class="stat-card">
            <div class="stat-icon">🎓</div>
            <div class="stat-value"><?= number_format($total_mhs, 0, ',', '.') ?></div>
            <div class="stat-label">Total Mahasiswa</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon">📝</div>
            <div class="stat-value"><?= (int) ($ringkasNilai['total_dinilai'] ?? 0) ?></div>
            <div class="stat-label">Mahasiswa Dinilai</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon">🔠</div>
            <div class="stat-value"><?= (int) ($ringkasNilai['variasi_huruf_mutu'] ?? 0) ?></div>
            <div class="stat-label">Variasi Huruf Mutu</div>
        </div>
    </div>
</div>

<div class="rekap-grid">

<!-- ===== Rekap 1: Distribusi Huruf Mutu ===== -->
<div class="box">
    <h3>🔠 Distribusi Huruf Mutu</h3>
    <p class="subjudul" style="margin-top:-10px;">Jumlah mahasiswa berdasarkan huruf mutu</p>

    <div class="table-wrapper">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Huruf Mutu</th>
                    <th>Jumlah Mahasiswa</th>
                </tr>
            </thead>
            <tbody>
            <?php $ada = false; while ($row = mysqli_fetch_assoc($rHurufMutu)): $ada = true;
                $bc = $badgeHm[$row['hm']] ?? 'badge-tidak';
            ?>
                <tr>
                    <td><span class="badge <?= $bc ?>"><?= htmlspecialchars($row['hm']) ?></span></td>
                    <td><strong><?= (int) $row['jumlah'] ?></strong></td>
                </tr>
            <?php endwhile; ?>
            <?php if (!$ada): ?>
                <tr><td colspan="2" class="no-data">Belum ada data nilai.</td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- ===== Rekap 2: Distribusi Status Kelulusan ===== -->
<div class="box">
    <h3>🏁 Distribusi Status Kelulusan</h3>
    <p class="subjudul" style="margin-top:-10px;">Jumlah mahasiswa berdasarkan status akhir</p>

    <div class="table-wrapper">
        <table class="data-table">
            <thead>
                <tr>
                    <th style="text-align:left;padding-left:24px;">Status</th>
                    <th>Jumlah</th>
                </tr>
            </thead>
            <tbody>
            <?php $ada = false; while ($row = mysqli_fetch_assoc($rStatus)): $ada = true; ?>
                <tr>
                    <td style="text-align:left;padding-left:24px;"><?= htmlspecialchars($row['status']) ?></td>
                    <td><strong><?= (int) $row['jumlah'] ?></strong></td>
                </tr>
            <?php endwhile; ?>
            <?php if (!$ada): ?>
                <tr><td colspan="2" class="no-data">Belum ada data nilai.</td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- ===== Rekap 3: Beban Bimbingan Dosen ===== -->
<div class="box">
    <h3>👨‍🏫 Beban Bimbingan Dosen</h3>
    <p class="subjudul" style="margin-top:-10px;">Jumlah mahasiswa bimbingan tiap dosen</p>

    <div class="table-wrapper">
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width:18%">NID</th>
                    <th style="text-align:left;padding-left:24px;">Nama Dosen</th>
                    <th>Jumlah Bimbingan</th>
                </tr>
            </thead>
            <tbody>
            <?php $ada = false; while ($row = mysqli_fetch_assoc($rBimbingan)): $ada = true; ?>
                <tr>
                    <td><span class="badge badge-nim"><?= htmlspecialchars($row['nid']) ?></span></td>
                    <td style="text-align:left;padding-left:24px;"><?= htmlspecialchars($row['namadosen']) ?></td>
                    <td><strong><?= (int) $row['jumlah_bimbingan'] ?></strong> mahasiswa</td>
                </tr>
            <?php endwhile; ?>
            <?php if (!$ada): ?>
                <tr><td colspan="3" class="no-data">Belum ada data bimbingan.</td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- ===== Rekap 4: Mata Kuliah per SKS ===== -->
<div class="box">
    <h3>📚 Rekap Mata Kuliah per SKS</h3>
    <p class="subjudul" style="margin-top:-10px;">Jumlah &amp; total SKS per kelompok bobot</p>

    <div class="table-wrapper">
        <table class="data-table">
            <thead>
                <tr>
                    <th>SKS</th>
                    <th>Jumlah Mata Kuliah</th>
                    <th>Total SKS</th>
                </tr>
            </thead>
            <tbody>
            <?php $ada = false; while ($row = mysqli_fetch_assoc($rSks)): $ada = true; ?>
                <tr>
                    <td><span class="badge badge-sks"><?= (int) $row['sks'] ?> SKS</span></td>
                    <td><?= (int) $row['jumlah_mk'] ?></td>
                    <td><strong><?= (int) $row['total_sks'] ?></strong></td>
                </tr>
            <?php endwhile; ?>
            <?php if (!$ada): ?>
                <tr><td colspan="3" class="no-data">Belum ada data mata kuliah.</td></tr>
            <?php endif; ?>
            </tbody>
            <?php if ($ada): ?>
            <tfoot>
                <tr>
                    <td style="text-align:right;font-weight:700;" colspan="2">Total SKS Kurikulum</td>
                    <td><strong><?= $totalSksKurikulum ?></strong></td>
                </tr>
            </tfoot>
            <?php endif; ?>
        </table>
    </div>
</div>

<!-- ===== Rekap 5: Mahasiswa & Dosen Pembimbing ===== -->
<div class="box">
    <h3>👥 Mahasiswa &amp; Dosen Pembimbing <span class="tbl-inline-note"></span></h3>
    <p class="subjudul" style="margin-top:-10px;">Pasangan mahasiswa dengan dosen pembimbingnya</p>

    <div class="table-wrapper-scroll">
        <table class="data-table" id="tabelMhsDopem">
            <thead>
                <tr>
                    <th data-sort="number">NIM <span class="sort-arrow"></span></th>
                    <th data-sort="text" style="text-align:left;padding-left:24px;">Nama Mahasiswa <span class="sort-arrow"></span></th>
                    <th data-sort="text">Dosen Pembimbing <span class="sort-arrow"></span></th>
                </tr>
            </thead>
            <tbody>
            <?php $ada = false; while ($row = mysqli_fetch_assoc($rMhsDopem)): $ada = true; ?>
                <tr>
                    <td><span class="badge badge-nim"><?= htmlspecialchars($row['nim']) ?></span></td>
                    <td style="text-align:left;padding-left:24px;"><?= htmlspecialchars($row['namamhs']) ?></td>
                    <td><?= $row['namadosen'] ? htmlspecialchars($row['namadosen']) : '<span class="no-data" style="padding:0;">Belum ada</span>' ?></td>
                </tr>
            <?php endwhile; ?>
            <?php if (!$ada): ?>
                <tr><td colspan="3" class="no-data">Belum ada data mahasiswa.</td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- ===== Rekap 6: Daftar IPK Mahasiswa ===== -->
<div class="box">
    <h3>🏆 Daftar IPK Mahasiswa <span class="tbl-inline-note"></span></h3>
    <p class="subjudul" style="margin-top:-10px;">IPK dikonversi dari huruf mutu nilai mahasiswa</p>

    <div class="table-wrapper-scroll">
        <table class="data-table" id="tabelIpk">
            <thead>
                <tr>
                    <th data-sort="number">NIM <span class="sort-arrow"></span></th>
                    <th data-sort="text" style="text-align:left;padding-left:24px;">Nama <span class="sort-arrow"></span></th>
                    <th data-sort="number">IPK <span class="sort-arrow"></span></th>
                </tr>
            </thead>
            <tbody>
            <?php $ada = false; while ($row = mysqli_fetch_assoc($rIpk)): $ada = true; ?>
                <tr>
                    <td><span class="badge badge-nim"><?= htmlspecialchars($row['nim']) ?></span></td>
                    <td style="text-align:left;padding-left:24px;"><?= htmlspecialchars($row['namamhs']) ?></td>
                    <td><strong><?= $row['ipk'] !== null ? number_format((float) $row['ipk'], 2) : '-' ?></strong></td>
                </tr>
            <?php endwhile; ?>
            <?php if (!$ada): ?>
                <tr><td colspan="3" class="no-data">Belum ada data nilai.</td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

</div>
<!-- /rekap-grid -->

<script>
document.addEventListener('DOMContentLoaded', function () {
    function makeSortable(tableId) {
        var table = document.getElementById(tableId);
        if (!table) return;
        var thead = table.querySelector('thead');
        var tbody = table.querySelector('tbody');
        var headers = thead.querySelectorAll('th[data-sort]');

        headers.forEach(function (th, idx) {
            th.classList.add('sortable-th');
            th.addEventListener('click', function () {
                var type = th.dataset.sort;
                var dir  = th.dataset.dir === 'asc' ? 'desc' : 'asc';

                headers.forEach(function (t) {
                    t.dataset.dir = '';
                    var arrow = t.querySelector('.sort-arrow');
                    if (arrow) arrow.textContent = '';
                });
                th.dataset.dir = dir;
                var arrow = th.querySelector('.sort-arrow');
                if (arrow) arrow.textContent = dir === 'asc' ? '▲' : '▼';

                var rows = Array.prototype.slice.call(tbody.querySelectorAll('tr'));
                if (rows.length === 1 && rows[0].querySelector('.no-data')) return;

                rows.sort(function (a, b) {
                    var cellA = a.children[idx] ? a.children[idx].textContent.trim() : '';
                    var cellB = b.children[idx] ? b.children[idx].textContent.trim() : '';
                    var cmp;
                    if (type === 'number') {
                        cmp = (parseFloat(cellA) || 0) - (parseFloat(cellB) || 0);
                    } else {
                        cmp = cellA.localeCompare(cellB, 'id', { sensitivity: 'base' });
                    }
                    return dir === 'asc' ? cmp : -cmp;
                });

                rows.forEach(function (r) { tbody.appendChild(r); });
            });
        });
    }

    makeSortable('tabelMhsDopem');
    makeSortable('tabelIpk');
});
</script>
