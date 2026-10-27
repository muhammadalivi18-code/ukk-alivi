<?php
// Akses data: pengguna dan barang (semua query memakai prepared statement).

// ---------- Pengguna ----------

function pengguna_cari_username(mysqli $db, string $username): ?array
{
    $stmt = $db->prepare('SELECT id, nama, username, password FROM users WHERE username = ? LIMIT 1');
    $stmt->bind_param('s', $username);
    $stmt->execute();
    $baris = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    return $baris ?: null;
}

function pengguna_buat(mysqli $db, string $nama, string $username, string $passwordHash): int
{
    $stmt = $db->prepare('INSERT INTO users (nama, username, password) VALUES (?, ?, ?)');
    $stmt->bind_param('sss', $nama, $username, $passwordHash);
    $stmt->execute();
    $id = $stmt->insert_id;
    $stmt->close();

    return $id;
}

// ---------- Barang ----------

function barang_semua(mysqli $db, string $cari = ''): array
{
    if ($cari !== '') {
        $like = '%' . addcslashes($cari, '%_\\') . '%';
        $stmt = $db->prepare(
            'SELECT id, kode, nama, kategori, harga, stok FROM barang
             WHERE kode LIKE ? OR nama LIKE ? OR kategori LIKE ?
             ORDER BY nama ASC'
        );
        $stmt->bind_param('sss', $like, $like, $like);
    } else {
        $stmt = $db->prepare('SELECT id, kode, nama, kategori, harga, stok FROM barang ORDER BY nama ASC');
    }

    $stmt->execute();
    $hasil = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt->close();

    return $hasil;
}

function barang_terbaru(mysqli $db, int $batas): array
{
    $stmt = $db->prepare('SELECT id, kode, nama, kategori, harga, stok FROM barang ORDER BY id DESC LIMIT ?');
    $stmt->bind_param('i', $batas);
    $stmt->execute();
    $hasil = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt->close();

    return $hasil;
}

function barang_cari_id(mysqli $db, int $id): ?array
{
    $stmt = $db->prepare('SELECT id, kode, nama, kategori, harga, stok FROM barang WHERE id = ? LIMIT 1');
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $baris = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    return $baris ?: null;
}

function barang_kode_ada(mysqli $db, string $kode, ?int $abaikanId = null): bool
{
    $abaikan = $abaikanId ?? 0;
    $stmt = $db->prepare('SELECT id FROM barang WHERE kode = ? AND id <> ? LIMIT 1');
    $stmt->bind_param('si', $kode, $abaikan);
    $stmt->execute();
    $ada = $stmt->get_result()->num_rows > 0;
    $stmt->close();

    return $ada;
}

function barang_kategori_daftar(mysqli $db): array
{
    $hasil = $db->query('SELECT DISTINCT kategori FROM barang ORDER BY kategori ASC');
    return array_column($hasil->fetch_all(MYSQLI_ASSOC), 'kategori');
}

function barang_tambah(mysqli $db, array $d): int
{
    $stmt = $db->prepare('INSERT INTO barang (kode, nama, kategori, harga, stok) VALUES (?, ?, ?, ?, ?)');
    $stmt->bind_param('sssii', $d['kode'], $d['nama'], $d['kategori'], $d['harga'], $d['stok']);
    $stmt->execute();
    $id = $stmt->insert_id;
    $stmt->close();

    return $id;
}

function barang_ubah(mysqli $db, int $id, array $d): void
{
    $stmt = $db->prepare('UPDATE barang SET kode = ?, nama = ?, kategori = ?, harga = ?, stok = ? WHERE id = ?');
    $stmt->bind_param('sssiii', $d['kode'], $d['nama'], $d['kategori'], $d['harga'], $d['stok'], $id);
    $stmt->execute();
    $stmt->close();
}

function barang_hapus(mysqli $db, int $id): bool
{
    $stmt = $db->prepare('DELETE FROM barang WHERE id = ?');
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $terhapus = $stmt->affected_rows > 0;
    $stmt->close();

    return $terhapus;
}
