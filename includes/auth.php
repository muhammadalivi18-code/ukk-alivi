<?php
// Autentikasi dan proteksi halaman berbasis session.

function sudah_login(): bool
{
    return !empty($_SESSION['user_id']);
}

function require_login(): void
{
    if (!sudah_login()) {
        flash_set('warning', 'Silakan masuk terlebih dahulu.');
        redirect('login.php');
    }
}

function require_guest(): void
{
    if (sudah_login()) {
        redirect('dashboard.php');
    }
}

function login_pengguna(array $pengguna): void
{
    session_regenerate_id(true);
    $_SESSION['user_id'] = (int) $pengguna['id'];
    $_SESSION['user_nama'] = $pengguna['nama'];
}

function logout_pengguna(): void
{
    $_SESSION = [];
    session_regenerate_id(true);
}
