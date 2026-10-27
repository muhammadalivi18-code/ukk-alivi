<?php
require __DIR__ . '/includes/init.php';
require_login();

$cari = mb_substr(input_teks($_GET, 'q'), 0, 100);
$daftar = barang_semua(db(), $cari);

$judul = 'Data Barang';
$subjudul = 'Kelola daftar barang: cari, tambah, ubah, dan hapus.';
$menu = 'barang';
require __DIR__ . '/includes/header.php';
?>
<div class="row g-2 mb-3 align-items-center">
    <div class="col-12 col-md">
        <form method="get" action="barang.php" class="row g-2">
            <div class="col">
                <input type="search" name="q" maxlength="100" class="form-control"
                       placeholder="Cari kode, nama, atau kategori"
                       aria-label="Cari barang" value="<?= e($cari) ?>">
            </div>
            <div class="col-auto">
                <button type="submit" class="btn btn-primary">Cari</button>
            </div>
            <?php if ($cari !== ''): ?>
                <div class="col-auto">
                    <a href="barang.php" class="btn btn-outline-secondary">Reset</a>
                </div>
            <?php endif; ?>
        </form>
    </div>
    <div class="col-12 col-md-auto">
        <a href="barang_form.php" class="btn btn-primary w-100">Tambah barang</a>
    </div>
</div>

<div class="kartu">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th>Kode</th>
                    <th>Nama</th>
                    <th>Kategori</th>
                    <th class="angka">Harga</th>
                    <th class="angka">Stok</th>
                    <th class="text-end">Aksi</th>
                </tr>
            </thead>
            <tbody>
            <?php if (!$daftar): ?>
                <tr>
                    <td colspan="6" class="text-center text-secondary py-4">
                        <?= $cari !== '' ? 'Tidak ada barang yang cocok dengan pencarian.' : 'Belum ada data barang.' ?>
                    </td>
                </tr>
            <?php endif; ?>
            <?php foreach ($daftar as $b): ?>
                <tr>
                    <td><?= e($b['kode']) ?></td>
                    <td><?= e($b['nama']) ?></td>
                    <td><?= e($b['kategori']) ?></td>
                    <td class="angka"><?= rupiah((int) $b['harga']) ?></td>
                    <td class="angka"><?= angka((int) $b['stok']) ?></td>
                    <td class="text-end text-nowrap">
                        <a href="barang_form.php?id=<?= (int) $b['id'] ?>" class="btn btn-outline-secondary btn-sm">Ubah</a>
                        <a href="barang_hapus.php?id=<?= (int) $b['id'] ?>" class="btn btn-outline-secondary btn-sm">Hapus</a>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<p class="text-secondary small mt-2 mb-0"><?= angka(count($daftar)) ?> barang ditampilkan.</p>
<?php require __DIR__ . '/includes/footer.php'; ?>
