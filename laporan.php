<?php
require __DIR__ . '/includes/init.php';
require_login();

$daftar = barang_semua(db());
$ringkasan = hitung_ringkasan(db());

$judul = 'Laporan';
$subjudul = 'Rekap seluruh barang beserta nilai persediaan.';
$menu = 'laporan';
require __DIR__ . '/includes/header.php';
?>
<div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
    <span class="text-secondary">Per <?= date('d-m-Y H:i') ?></span>
    <a href="laporan_pdf.php" target="_blank" rel="noopener" class="btn btn-primary">Cetak PDF</a>
</div>

<div class="kartu">
    <div class="table-responsive">
        <table class="table mb-0">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Kode</th>
                    <th>Nama</th>
                    <th>Kategori</th>
                    <th class="angka">Harga</th>
                    <th class="angka">Stok</th>
                    <th class="angka">Nilai</th>
                </tr>
            </thead>
            <tbody>
            <?php if (!$daftar): ?>
                <tr><td colspan="7" class="text-center text-secondary py-4">Belum ada data barang.</td></tr>
            <?php endif; ?>
            <?php foreach ($daftar as $i => $b): ?>
                <tr>
                    <td><?= $i + 1 ?></td>
                    <td><?= e($b['kode']) ?></td>
                    <td><?= e($b['nama']) ?></td>
                    <td><?= e($b['kategori']) ?></td>
                    <td class="angka"><?= rupiah((int) $b['harga']) ?></td>
                    <td class="angka"><?= angka((int) $b['stok']) ?></td>
                    <td class="angka"><?= rupiah(hitung_nilai((int) $b['harga'], (int) $b['stok'])) ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
            <?php if ($daftar): ?>
            <tfoot>
                <tr>
                    <td colspan="5" class="text-end">Total (<?= angka($ringkasan['jumlah']) ?> jenis barang)</td>
                    <td class="angka"><?= angka($ringkasan['total_stok']) ?></td>
                    <td class="angka"><?= rupiah($ringkasan['total_nilai']) ?></td>
                </tr>
            </tfoot>
            <?php endif; ?>
        </table>
    </div>
</div>
<?php require __DIR__ . '/includes/footer.php'; ?>
