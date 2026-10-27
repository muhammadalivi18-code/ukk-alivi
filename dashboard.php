<?php
require __DIR__ . '/includes/init.php';
require_login();

$ringkasan = hitung_ringkasan(db());
$terbaru = barang_terbaru(db(), 5);

$judul = 'Dashboard';
$subjudul = 'Ringkasan data barang saat ini.';
$menu = 'dashboard';
require __DIR__ . '/includes/header.php';
?>
<div class="row g-3 mb-4">
    <div class="col-12 col-md-4">
        <div class="kartu stat">
            <div class="label">Jumlah barang</div>
            <div class="nilai"><?= angka($ringkasan['jumlah']) ?></div>
        </div>
    </div>
    <div class="col-12 col-md-4">
        <div class="kartu stat">
            <div class="label">Total stok</div>
            <div class="nilai"><?= angka($ringkasan['total_stok']) ?></div>
        </div>
    </div>
    <div class="col-12 col-md-4">
        <div class="kartu stat">
            <div class="label">Total nilai</div>
            <div class="nilai"><?= rupiah($ringkasan['total_nilai']) ?></div>
        </div>
    </div>
</div>

<div class="kartu">
    <div class="kartu-judul d-flex justify-content-between align-items-center gap-2">
        <h2>Barang terbaru</h2>
        <div class="d-flex gap-2">
            <a href="barang_form.php" class="btn btn-primary btn-sm">Tambah barang</a>
            <a href="barang.php" class="btn btn-outline-secondary btn-sm">Lihat semua</a>
        </div>
    </div>
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th>Kode</th>
                    <th>Nama</th>
                    <th>Kategori</th>
                    <th class="angka">Harga</th>
                    <th class="angka">Stok</th>
                </tr>
            </thead>
            <tbody>
            <?php if (!$terbaru): ?>
                <tr><td colspan="5" class="text-center text-secondary py-4">Belum ada data barang.</td></tr>
            <?php endif; ?>
            <?php foreach ($terbaru as $b): ?>
                <tr>
                    <td><?= e($b['kode']) ?></td>
                    <td><?= e($b['nama']) ?></td>
                    <td><?= e($b['kategori']) ?></td>
                    <td class="angka"><?= rupiah((int) $b['harga']) ?></td>
                    <td class="angka"><?= angka((int) $b['stok']) ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php require __DIR__ . '/includes/footer.php'; ?>
