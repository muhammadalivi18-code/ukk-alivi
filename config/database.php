<?php
// Koneksi database (default XAMPP). Bisa diganti lewat environment variable.

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

function db(): mysqli
{
    static $koneksi = null;

    if ($koneksi === null) {
        $host = getenv('DB_HOST') ?: 'localhost';
        $user = getenv('DB_USER') ?: 'root';
        $pass = getenv('DB_PASS') ?: '';
        $nama = getenv('DB_NAME') ?: 'db_inventaris';

        try {
            $koneksi = new mysqli($host, $user, $pass, $nama);
            $koneksi->set_charset('utf8mb4');
        } catch (mysqli_sql_exception $e) {
            error_log($e->getMessage());
            http_response_code(500);
            exit('Koneksi database gagal. Pastikan MySQL di XAMPP aktif dan database.sql sudah diimpor.');
        }
    }

    return $koneksi;
}
