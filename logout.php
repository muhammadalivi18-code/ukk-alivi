<?php
require __DIR__ . '/includes/init.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect(sudah_login() ? 'dashboard.php' : 'login.php');
}

csrf_wajib(sudah_login() ? 'dashboard.php' : 'login.php');

logout_pengguna();
flash_set('success', 'Anda sudah keluar.');
redirect('login.php');
