<?php
require __DIR__ . '/includes/init.php';
require_login();

$fpdf = __DIR__ . '/lib/fpdf/fpdf.php';
if (!is_file($fpdf)) {
    flash_set('danger', 'Library FPDF belum dipasang. Letakkan fpdf.php di folder lib/fpdf/.');
    redirect('laporan.php');
}
require $fpdf;

// FPDF memakai encoding Latin-1, jadi teks UTF-8 dikonversi dulu.
function pdf_teks(string $teks, int $maks = 0): string
{
    if ($maks > 0) {
        $teks = mb_strimwidth($teks, 0, $maks, '...', 'UTF-8');
    }
    return mb_convert_encoding($teks, 'ISO-8859-1', 'UTF-8');
}

class LaporanPDF extends FPDF
{
    public string $pembuat = '';
    public array $lebar = [10, 26, 56, 30, 26, 14, 28]; // total 190 mm

    function Header()
    {
        $this->SetFont('Arial', 'B', 14);
        $this->SetTextColor(31, 41, 51);
        $this->Cell(0, 8, 'Laporan Data Barang', 0, 1);

        $this->SetFont('Arial', '', 9);
        $this->SetTextColor(95, 107, 118);
        $this->Cell(0, 5, 'Dicetak ' . date('d-m-Y H:i') . ' oleh ' . pdf_teks($this->pembuat), 0, 1);
        $this->Ln(3);

        $this->SetFont('Arial', 'B', 9);
        $this->SetTextColor(255, 255, 255);
        $this->SetFillColor(31, 79, 130);
        $this->SetDrawColor(31, 79, 130);
        $judul = ['No', 'Kode', 'Nama', 'Kategori', 'Harga (Rp)', 'Stok', 'Nilai (Rp)'];
        foreach ($judul as $i => $teks) {
            $this->Cell($this->lebar[$i], 8, $teks, 1, 0, $i >= 4 ? 'R' : 'L', true);
        }
        $this->Ln();
        $this->SetTextColor(31, 41, 51);
    }

    function Footer()
    {
        $this->SetY(-12);
        $this->SetFont('Arial', '', 8);
        $this->SetTextColor(95, 107, 118);
        $this->Cell(0, 6, 'Halaman ' . $this->PageNo() . ' dari {nb}', 0, 0, 'C');
    }
}

$daftar = barang_semua(db());
$ringkasan = hitung_ringkasan(db());

$pdf = new LaporanPDF('P', 'mm', 'A4');
$pdf->pembuat = (string) ($_SESSION['user_nama'] ?? '');
$pdf->SetMargins(10, 12, 10);
$pdf->SetAutoPageBreak(true, 18);
$pdf->AliasNbPages();
$pdf->AddPage();

$pdf->SetFont('Arial', '', 9);
$pdf->SetDrawColor(221, 226, 231);
$pdf->SetFillColor(244, 246, 248);

if (!$daftar) {
    $pdf->Cell(190, 10, 'Belum ada data barang.', 'B', 1, 'C');
}

foreach ($daftar as $i => $b) {
    $harga = (int) $b['harga'];
    $stok = (int) $b['stok'];
    $isi = $i % 2 === 1;

    $pdf->Cell($pdf->lebar[0], 7, (string) ($i + 1), 'B', 0, 'L', $isi);
    $pdf->Cell($pdf->lebar[1], 7, pdf_teks($b['kode'], 14), 'B', 0, 'L', $isi);
    $pdf->Cell($pdf->lebar[2], 7, pdf_teks($b['nama'], 34), 'B', 0, 'L', $isi);
    $pdf->Cell($pdf->lebar[3], 7, pdf_teks($b['kategori'], 17), 'B', 0, 'L', $isi);
    $pdf->Cell($pdf->lebar[4], 7, number_format($harga, 0, ',', '.'), 'B', 0, 'R', $isi);
    $pdf->Cell($pdf->lebar[5], 7, number_format($stok, 0, ',', '.'), 'B', 0, 'R', $isi);
    $pdf->Cell($pdf->lebar[6], 7, number_format(hitung_nilai($harga, $stok), 0, ',', '.'), 'B', 1, 'R', $isi);
}

if ($daftar) {
    $pdf->SetFont('Arial', 'B', 9);
    $pdf->SetFillColor(238, 241, 244);
    $pdf->Cell(148, 8, 'Total (' . $ringkasan['jumlah'] . ' jenis barang)', 'B', 0, 'R', true);
    $pdf->Cell($pdf->lebar[5], 8, number_format($ringkasan['total_stok'], 0, ',', '.'), 'B', 0, 'R', true);
    $pdf->Cell($pdf->lebar[6], 8, number_format($ringkasan['total_nilai'], 0, ',', '.'), 'B', 1, 'R', true);
}

$pdf->Output('I', 'laporan-barang.pdf');
