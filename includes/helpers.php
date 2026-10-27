<?php
// Fungsi bantu umum: sesi, escape output, flash message, CSRF, format angka.

date_default_timezone_set('Asia/Jakarta');

if (session_status() === PHP_SESSION_NONE) {
    session_set_cookie_params([
        'lifetime' => 0,
        'httponly' => true,
        'samesite' => 'Lax',
        'secure'   => !empty($_SERVER['HTTPS']),
    ]);
    session_start();
}

function e($nilai): string
{
    return htmlspecialchars((string) $nilai, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function redirect(string $url)
{
    header('Location: ' . $url);
    exit;
}

// Ambil input teks dengan aman (selalu string, sudah di-trim).
function input_teks(array $sumber, string $kunci): string
{
    $v = $sumber[$kunci] ?? '';
    return is_string($v) ? trim($v) : '';
}

function rupiah(int $n): string
{
    return 'Rp ' . number_format($n, 0, ',', '.');
}

function angka(int $n): string
{
    return number_format($n, 0, ',', '.');
}

// Pesan sekali tampil (success, danger, warning, info).
function flash_set(string $tipe, string $pesan): void
{
    $_SESSION['flash'][] = ['tipe' => $tipe, 'pesan' => $pesan];
}

function flash_ambil(): array
{
    $pesan = $_SESSION['flash'] ?? [];
    unset($_SESSION['flash']);
    return $pesan;
}

function csrf_token(): string
{
    if (empty($_SESSION['csrf'])) {
        $_SESSION['csrf'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf'];
}

function csrf_field(): string
{
    return '<input type="hidden" name="csrf" value="' . e(csrf_token()) . '">';
}

// Panggil di awal pemrosesan POST.
function csrf_wajib(string $kembali): void
{
    $kirim = $_POST['csrf'] ?? '';
    $sah = $_SESSION['csrf'] ?? '';

    if (!is_string($kirim) || $sah === '' || !hash_equals($sah, $kirim)) {
        flash_set('danger', 'Sesi formulir tidak valid. Silakan coba lagi.');
        redirect($kembali);
    }
}

function kelas_invalid(array $errors, string $kunci): string
{
    return isset($errors[$kunci]) ? ' is-invalid' : '';
}

function pesan_error(array $errors, string $kunci): string
{
    return isset($errors[$kunci])
        ? '<div class="invalid-feedback">' . e($errors[$kunci]) . '</div>'
        : '';
}
