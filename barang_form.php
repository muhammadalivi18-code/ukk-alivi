<?php
require __DIR__ . '/includes/init.php';
require_login();

// Mode ubah bila ada ?id=, selain itu mode tambah.
$id = null;
$barang = null;

if (isset($_GET['id'])) {
    $id = validasi_id($_GET['id']);
    $barang = $id !== null ? barang_cari_id(db(), $id) : null;

    if ($barang === null) {
        flash_set('danger', 'Data barang tidak ditemukan atau ID tidak valid.');
        redirect('barang.php');
    }
}

$modeUbah = $barang !== null;
$halaman = $modeUbah ? 'barang_form.php?id=' . $id : 'barang_form.php';

$form = [
    'kode'     => $barang['kode'] ?? '',
    'nama'     => $barang['nama'] ?? '',
    'kategori' => $barang['kategori'] ?? '',
    'harga'    => isset($barang['harga']) ? (string) $barang['harga'] : '',
    'stok'     => isset($barang['stok']) ? (string) $barang['stok'] : '',
];
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_wajib($halaman);

    [$errors, $bersih] = validasi_barang($_POST, db(), $id);
    foreach (array_keys($form) as $kunci) {
        $form[$kunci] = input_teks($_POST, $kunci);
    }

    if (!$errors) {
        try {
            if ($modeUbah) {
                barang_ubah(db(), $id, $bersih);
                flash_set('success', 'Barang "' . $bersih['nama'] . '" berhasil diperbarui.');
            } else {
                barang_tambah(db(), $bersih);
                flash_set('success', 'Barang "' . $bersih['nama'] . '" berhasil ditambahkan.');
            }
            redirect('barang.php');
        } catch (mysqli_sql_exception $ex) {
            if ($ex->getCode() === 1062) {
                $errors['kode'] = 'Kode barang sudah digunakan.';
            } else {
                $errors['umum'] = 'Data gagal disimpan. Silakan coba lagi.';
            }
        }
    }
}

$kategoriDaftar = barang_kategori_daftar(db());

$judul = $modeUbah ? 'Ubah Barang' : 'Tambah Barang';
$subjudul = $modeUbah ? 'Perbarui data barang yang dipilih.' : 'Isi data barang baru.';
$menu = 'barang';
require __DIR__ . '/includes/header.php';
?>
<div class="kartu kartu-isi">
    <?php if (isset($errors['umum'])): ?>
        <div class="alert alert-danger"><?= e($errors['umum']) ?></div>
    <?php endif; ?>

    <form method="post" action="<?= e($halaman) ?>" novalidate>
        <?= csrf_field() ?>

        <div class="row g-3">
            <div class="col-12 col-md-4">
                <label for="kode" class="form-label">Kode barang</label>
                <input type="text" id="kode" name="kode" maxlength="20" required
                       class="form-control<?= kelas_invalid($errors, 'kode') ?>"
                       value="<?= e($form['kode']) ?>">
                <?= pesan_error($errors, 'kode') ?>
            </div>

            <div class="col-12 col-md-8">
                <label for="nama" class="form-label">Nama barang</label>
                <input type="text" id="nama" name="nama" maxlength="100" required
                       class="form-control<?= kelas_invalid($errors, 'nama') ?>"
                       value="<?= e($form['nama']) ?>">
                <?= pesan_error($errors, 'nama') ?>
            </div>

            <div class="col-12 col-md-4">
                <label for="kategori" class="form-label">Kategori</label>
                <input type="text" id="kategori" name="kategori" maxlength="50" required
                       list="daftar-kategori" autocomplete="off"
                       class="form-control<?= kelas_invalid($errors, 'kategori') ?>"
                       value="<?= e($form['kategori']) ?>">
                <datalist id="daftar-kategori">
                    <?php foreach ($kategoriDaftar as $k): ?>
                        <option value="<?= e($k) ?>"></option>
                    <?php endforeach; ?>
                </datalist>
                <?= pesan_error($errors, 'kategori') ?>
            </div>

            <div class="col-12 col-md-4">
                <label for="harga" class="form-label">Harga (Rp)</label>
                <input type="number" id="harga" name="harga" min="0" max="999999999" step="1" required
                       inputmode="numeric"
                       class="form-control<?= kelas_invalid($errors, 'harga') ?>"
                       value="<?= e($form['harga']) ?>">
                <?= pesan_error($errors, 'harga') ?>
            </div>

            <div class="col-12 col-md-4">
                <label for="stok" class="form-label">Stok</label>
                <input type="number" id="stok" name="stok" min="0" max="9999999" step="1" required
                       inputmode="numeric"
                       class="form-control<?= kelas_invalid($errors, 'stok') ?>"
                       value="<?= e($form['stok']) ?>">
                <?= pesan_error($errors, 'stok') ?>
            </div>
        </div>

        <div class="d-flex flex-column flex-sm-row gap-2 mt-4">
            <button type="submit" class="btn btn-primary"><?= $modeUbah ? 'Simpan perubahan' : 'Simpan barang' ?></button>
            <a href="barang.php" class="btn btn-outline-secondary">Batal</a>
        </div>
    </form>
</div>
<?php require __DIR__ . '/includes/footer.php'; ?>
