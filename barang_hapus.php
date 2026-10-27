<?php
require __DIR__ . '/includes/init.php';
require_login();

$id = validasi_id($_GET['id'] ?? null);
$barang = $id !== null ? barang_cari_id(db(), $id) : null;

if ($barang === null) {
    flash_set('danger', 'Data barang tidak ditemukan atau ID tidak valid.');
    redirect('barang.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_wajib('barang.php');

    if (barang_hapus(db(), $id)) {
        flash_set('success', 'Barang "' . $barang['nama'] . '" berhasil dihapus.');
    } else {
        flash_set('danger', 'Barang sudah tidak ada.');
    }
    redirect('barang.php');
}

$judul = 'Hapus Barang';
$subjudul = 'Periksa data di bawah sebelum menghapus.';
$menu = 'barang';
require __DIR__ . '/includes/header.php';
?>
<div class="kartu kartu-isi" style="max-width: 560px;">
    <p class="mb-3">Barang berikut akan dihapus permanen:</p>

    <dl class="row mb-4">
        <dt class="col-4">Kode</dt>
        <dd class="col-8"><?= e($barang['kode']) ?></dd>
        <dt class="col-4">Nama</dt>
        <dd class="col-8"><?= e($barang['nama']) ?></dd>
        <dt class="col-4">Kategori</dt>
        <dd class="col-8"><?= e($barang['kategori']) ?></dd>
        <dt class="col-4">Harga</dt>
        <dd class="col-8"><?= rupiah((int) $barang['harga']) ?></dd>
        <dt class="col-4">Stok</dt>
        <dd class="col-8 mb-0"><?= angka((int) $barang['stok']) ?></dd>
    </dl>

    <form method="post" action="barang_hapus.php?id=<?= (int) $id ?>" class="d-flex flex-column flex-sm-row gap-2">
        <?= csrf_field() ?>
        <button type="submit" class="btn btn-danger">Ya, hapus barang</button>
        <a href="barang.php" class="btn btn-outline-secondary">Batal</a>
    </form>
</div>
<?php require __DIR__ . '/includes/footer.php'; ?>
