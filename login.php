<?php
require __DIR__ . '/includes/init.php';
require_guest();

$errors = [];
$username = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_wajib('login.php');

    $username = strtolower(input_teks($_POST, 'username'));
    $password = is_string($_POST['password'] ?? null) ? $_POST['password'] : '';

    $pengguna = $username !== '' ? pengguna_cari_username(db(), $username) : null;

    if ($pengguna !== null && password_verify($password, $pengguna['password'])) {
        login_pengguna($pengguna);
        flash_set('success', 'Selamat datang, ' . $pengguna['nama'] . '.');
        redirect('dashboard.php');
    }

    $errors['umum'] = 'Username atau kata sandi salah.';
}

$judul = 'Masuk';
$subjudul = 'Masuk untuk mengelola data barang.';
$tamu = true;
require __DIR__ . '/includes/header.php';
?>
<div class="auth-box">
    <div class="kartu kartu-isi">
        <?php if (isset($errors['umum'])): ?>
            <div class="alert alert-danger"><?= e($errors['umum']) ?></div>
        <?php endif; ?>

        <form method="post" action="login.php" novalidate>
            <?= csrf_field() ?>

            <div class="mb-3">
                <label for="username" class="form-label">Username</label>
                <input type="text" id="username" name="username" maxlength="30" required
                       autocomplete="username" autocapitalize="none" class="form-control"
                       value="<?= e($username) ?>">
            </div>

            <div class="mb-4">
                <label for="password" class="form-label">Kata sandi</label>
                <input type="password" id="password" name="password" maxlength="72" required
                       autocomplete="current-password" class="form-control">
            </div>

            <button type="submit" class="btn btn-primary w-100">Masuk</button>
        </form>
    </div>
    <p class="text-center mt-3 mb-0">Belum punya akun? <a href="register.php">Daftar</a></p>
</div>
<?php require __DIR__ . '/includes/footer.php'; ?>
