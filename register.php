<?php
require __DIR__ . '/includes/init.php';
require_guest();

$errors = [];
$form = ['nama' => '', 'username' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_wajib('register.php');

    [$errors, $bersih] = validasi_registrasi($_POST, db());
    $form = ['nama' => $bersih['nama'], 'username' => $bersih['username']];

    if (!$errors) {
        try {
            $hash = password_hash($bersih['password'], PASSWORD_DEFAULT);
            pengguna_buat(db(), $bersih['nama'], $bersih['username'], $hash);
            flash_set('success', 'Registrasi berhasil. Silakan masuk.');
            redirect('login.php');
        } catch (mysqli_sql_exception $ex) {
            if ($ex->getCode() === 1062) {
                $errors['username'] = 'Username sudah dipakai.';
            } else {
                $errors['umum'] = 'Registrasi gagal. Silakan coba lagi.';
            }
        }
    }
}

$judul = 'Registrasi';
$subjudul = 'Buat akun untuk mengelola data barang.';
$tamu = true;
require __DIR__ . '/includes/header.php';
?>
<div class="auth-box">
    <div class="kartu kartu-isi">
        <?php if (isset($errors['umum'])): ?>
            <div class="alert alert-danger"><?= e($errors['umum']) ?></div>
        <?php endif; ?>

        <form method="post" action="register.php" novalidate>
            <?= csrf_field() ?>

            <div class="mb-3">
                <label for="nama" class="form-label">Nama lengkap</label>
                <input type="text" id="nama" name="nama" maxlength="100" required
                       class="form-control<?= kelas_invalid($errors, 'nama') ?>"
                       value="<?= e($form['nama']) ?>">
                <?= pesan_error($errors, 'nama') ?>
            </div>

            <div class="mb-3">
                <label for="username" class="form-label">Username</label>
                <input type="text" id="username" name="username" maxlength="30" required
                       autocomplete="username" autocapitalize="none"
                       class="form-control<?= kelas_invalid($errors, 'username') ?>"
                       value="<?= e($form['username']) ?>">
                <?= pesan_error($errors, 'username') ?>
            </div>

            <div class="mb-3">
                <label for="password" class="form-label">Kata sandi</label>
                <input type="password" id="password" name="password" maxlength="72" required
                       autocomplete="new-password"
                       class="form-control<?= kelas_invalid($errors, 'password') ?>">
                <?= pesan_error($errors, 'password') ?>
            </div>

            <div class="mb-4">
                <label for="konfirmasi" class="form-label">Ulangi kata sandi</label>
                <input type="password" id="konfirmasi" name="konfirmasi" maxlength="72" required
                       autocomplete="new-password"
                       class="form-control<?= kelas_invalid($errors, 'konfirmasi') ?>">
                <?= pesan_error($errors, 'konfirmasi') ?>
            </div>

            <button type="submit" class="btn btn-primary w-100">Daftar</button>
        </form>
    </div>
    <p class="text-center mt-3 mb-0">Sudah punya akun? <a href="login.php">Masuk</a></p>
</div>
<?php require __DIR__ . '/includes/footer.php'; ?>
