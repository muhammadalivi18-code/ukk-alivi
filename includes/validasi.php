<?php
// Validasi input. Fungsi form mengembalikan [daftar_error, data_bersih].

// ID harus bilangan bulat positif; selain itu dianggap tidak valid (null).
function validasi_id($nilai): ?int
{
    if (is_string($nilai) && ctype_digit($nilai) && strlen($nilai) <= 10 && (int) $nilai > 0) {
        return (int) $nilai;
    }
    return null;
}

function validasi_registrasi(array $in, mysqli $db): array
{
    $err = [];
    $nama = input_teks($in, 'nama');
    $username = strtolower(input_teks($in, 'username'));
    $password = is_string($in['password'] ?? null) ? $in['password'] : '';
    $konfirmasi = is_string($in['konfirmasi'] ?? null) ? $in['konfirmasi'] : '';

    if (mb_strlen($nama) < 3 || mb_strlen($nama) > 100) {
        $err['nama'] = 'Nama lengkap harus 3 sampai 100 karakter.';
    }

    if (!preg_match('/^[a-z0-9_]{4,30}$/', $username)) {
        $err['username'] = 'Username 4 sampai 30 karakter: huruf kecil, angka, atau garis bawah.';
    } elseif (pengguna_cari_username($db, $username) !== null) {
        $err['username'] = 'Username sudah dipakai.';
    }

    if (strlen($password) < 8) {
        $err['password'] = 'Kata sandi minimal 8 karakter.';
    } elseif (strlen($password) > 72) {
        $err['password'] = 'Kata sandi maksimal 72 karakter.';
    }

    if ($password !== $konfirmasi) {
        $err['konfirmasi'] = 'Konfirmasi kata sandi tidak sama.';
    }

    return [$err, ['nama' => $nama, 'username' => $username, 'password' => $password]];
}

function validasi_barang(array $in, mysqli $db, ?int $idSaatIni = null): array
{
    $err = [];
    $kode = strtoupper(input_teks($in, 'kode'));
    $nama = input_teks($in, 'nama');
    $kategori = input_teks($in, 'kategori');
    $harga = input_teks($in, 'harga');
    $stok = input_teks($in, 'stok');

    if (!preg_match('/^[A-Z0-9_-]{2,20}$/', $kode)) {
        $err['kode'] = 'Kode 2 sampai 20 karakter: huruf, angka, strip, atau garis bawah.';
    } elseif (barang_kode_ada($db, $kode, $idSaatIni)) {
        $err['kode'] = 'Kode barang sudah digunakan.';
    }

    if (mb_strlen($nama) < 2 || mb_strlen($nama) > 100) {
        $err['nama'] = 'Nama barang harus 2 sampai 100 karakter.';
    }

    if (mb_strlen($kategori) < 2 || mb_strlen($kategori) > 50) {
        $err['kategori'] = 'Kategori harus 2 sampai 50 karakter.';
    }

    if (!ctype_digit($harga) || strlen($harga) > 9) {
        $err['harga'] = 'Harga harus berupa angka bulat 0 sampai 999.999.999, tanpa titik atau koma.';
    }

    if (!ctype_digit($stok) || strlen($stok) > 7) {
        $err['stok'] = 'Stok harus berupa angka bulat 0 sampai 9.999.999.';
    }

    return [$err, [
        'kode'     => $kode,
        'nama'     => $nama,
        'kategori' => $kategori,
        'harga'    => (int) $harga,
        'stok'     => (int) $stok,
    ]];
}
