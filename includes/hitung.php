<?php
// Perhitungan total.

function hitung_nilai(int $harga, int $stok): int
{
    return $harga * $stok;
}

// Jumlah jenis barang, total stok, dan total nilai persediaan.
function hitung_ringkasan(mysqli $db): array
{
    $baris = $db->query(
        'SELECT COUNT(*) AS jumlah,
                COALESCE(SUM(stok), 0) AS total_stok,
                COALESCE(SUM(harga * stok), 0) AS total_nilai
         FROM barang'
    )->fetch_assoc();

    return [
        'jumlah'      => (int) $baris['jumlah'],
        'total_stok'  => (int) $baris['total_stok'],
        'total_nilai' => (int) $baris['total_nilai'],
    ];
}
