<?php
// Dimuat di awal setiap halaman.

set_exception_handler(function (Throwable $t) {
    error_log((string) $t);
    http_response_code(500);
    echo 'Terjadi kesalahan pada server. Silakan coba lagi.';
});

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/helpers.php';
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/crud.php';
require_once __DIR__ . '/validasi.php';
require_once __DIR__ . '/hitung.php';
