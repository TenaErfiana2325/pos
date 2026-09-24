<?php
if (ob_get_length()) ob_clean();

require_once '../database/koneksi.php';
require_once '../aset_web/fpdf/fpdf.php';

$tgl_mulai   = $_GET['tgl_mulai'] ?? '';
$tgl_selesai = $_GET['tgl_selesai'] ?? '';

if (!empty($tgl_mulai) && !empty($tgl_selesai)) {
    // 1. Total Penjualan
    $q_jual = mysqli_query($con, "
        SELECT SUM(total_penjualan) AS total_jual 
        FROM tbl_notajual 
        WHERE tgl_penjualan BETWEEN '$tgl_mulai' AND '$tgl_selesai'
    ");
    $d_jual = mysqli_fetch_assoc($q_jual);
    $total_penjualan = $d_jual['total_jual'] ?? 0;

    // 2. Total Pembelian
    $q_beli = mysqli_query($con, "
        SELECT SUM(total_pembelian) AS total_beli 
        FROM tbl_notabeli 
        WHERE tgl_pembelian BETWEEN '$tgl_mulai' AND '$tgl_selesai'
    ");
    $d_beli = mysqli_fetch_assoc($q_beli);
    $total_pembelian = $d_beli['total_beli'] ?? 0;

    $sub_judul = 'Periode: ' . date('d/m/Y', strtotime($tgl_mulai)) . ' s/d ' . date('d/m/Y', strtotime($tgl_selesai));
} else {
    $q_jual = mysqli_query($con, "SELECT SUM(total_penjualan) AS total_jual FROM tbl_notajual");
    $d_jual = mysqli_fetch_assoc($q_jual);
    $total_penjualan = $d_jual['total_jual'] ?? 0;

    $q_beli = mysqli_query($con, "SELECT SUM(total_pembelian) AS total_beli FROM tbl_notabeli");
    $d_beli = mysqli_fetch_assoc($q_beli);
    $total_pembelian = $d_beli['total_beli'] ?? 0;

    $sub_judul = 'Semua Periode';
}

$laba_bersih = $total_penjualan - $total_pembelian;

$pdf = new FPDF('P', 'mm', 'A4');
$pdf->SetMargins(12, 12, 12);
$pdf->AddPage();

// ---------------------------------------------------------
// LOGO DAN HEADER (KOP TOKO)
// ---------------------------------------------------------
// Jalur/path logo kamu (ganti sesuai lokasi dan nama file logomu)
$logo_path = '../aset_web/img/logotoko.png'; 

if (file_exists($logo_path)) {
    // $pdf->Image(path, x, y, width, height)
    $pdf->Image($logo_path, 14, 9, 22); 
    $pdf->SetX(40); // Geser teks ke kanan setelah logo
    $pdf->SetFont('Times', 'B', 18);
    $pdf->SetTextColor(216, 112, 147);
    $pdf->Cell(158, 7, 'CINDERENNA MART', 0, 1, 'L');

    $pdf->SetX(40);
    $pdf->SetFont('Times', '', 9);
    $pdf->SetTextColor(100, 100, 100);
    $pdf->Cell(158, 5, 'Jl. Bandung, Indonesia | Telp: (021) 555-0199', 0, 1, 'L');

    $pdf->SetX(40);
    $pdf->Cell(158, 5, 'Email: info@cinderennamart.com', 0, 1, 'L');
} else {
    // Jika file logo tidak ditemukan, tampilkan nama toko di tengah
    $pdf->SetFont('Times', 'B', 20);
    $pdf->SetTextColor(216, 112, 147);
    $pdf->Cell(186, 8, 'CINDERENNA MART', 0, 1, 'C');

    $pdf->SetFont('Times', '', 9);
    $pdf->SetTextColor(100, 100, 100);
    $pdf->Cell(186, 5, 'Jl. Bandung, Indonesia | Telp: (021) 555-0199', 0, 1, 'C');
}

// Garis Pembatas KOP
$pdf->SetDrawColor(200, 162, 200);
$pdf->SetLineWidth(0.8);
$pdf->Line(12, 33, 198, 33);
$pdf->Ln(8);

// ---------------------------------------------------------
// JUDUL LAPORAN
// ---------------------------------------------------------
$pdf->SetFont('Times', 'B', 14);
$pdf->SetTextColor(60, 60, 60);
$pdf->Cell(186, 6, 'LAPORAN LABA RUGI', 0, 1, 'C');

$pdf->SetFont('Times', 'I', 10);
$pdf->SetTextColor(120, 120, 120);
$pdf->Cell(186, 5, $sub_judul, 0, 1, 'C');
$pdf->Ln(6);

// ---------------------------------------------------------
// TABEL LAPORAN
// ---------------------------------------------------------
$pdf->SetFont('Times', 'B', 10);
$pdf->SetFillColor(216, 191, 216); // Warna Header Ungu Soft
$pdf->SetTextColor(75, 0, 130); 
$pdf->SetDrawColor(200, 162, 200);
$pdf->SetLineWidth(0.3);

$pdf->Cell(126, 8, 'Keterangan Transaksi', 1, 0, 'C', true);
$pdf->Cell(60, 8, 'Jumlah (Rp)', 1, 1, 'C', true);

$pdf->SetFont('Times', '', 10);
$pdf->SetTextColor(40, 40, 40);

// Row 1: Penjualan
$pdf->Cell(126, 8, ' Total Pendapatan Penjualan', 1, 0, 'L');
$pdf->Cell(60, 8, 'Rp ' . number_format($total_penjualan, 0, ',', '.'), 1, 1, 'R');

// Row 2: Pembelian
$pdf->SetFillColor(253, 242, 248);
$pdf->Cell(126, 8, ' Total Pengeluaran Pembelian (HPP)', 1, 0, 'L', true);
$pdf->Cell(60, 8, '(Rp ' . number_format($total_pembelian, 0, ',', '.') . ')', 1, 1, 'R', true);

// Row 3: Laba / Rugi Bersih
$pdf->SetFont('Times', 'B', 10);
if ($laba_bersih >= 0) {
    $pdf->SetFillColor(245, 225, 238);
    $pdf->SetTextColor(120, 20, 80);
    $status_teks = 'TOTAL LABA BERSIH';
} else {
    $pdf->SetFillColor(255, 218, 224);
    $pdf->SetTextColor(180, 0, 0);
    $status_teks = 'TOTAL RUGI BERSIH';
}

$pdf->Cell(126, 9, ' ' . $status_teks, 1, 0, 'L', true);
$pdf->Cell(60, 9, 'Rp ' . number_format($laba_bersih, 0, ',', '.'), 1, 1, 'R', true);

$pdf->Ln(12);

// ---------------------------------------------------------
// TANDA TANGAN
// ---------------------------------------------------------
$pdf->SetFont('Times', '', 10);
$pdf->SetTextColor(80, 80, 80);

$pdf->Cell(120, 5, '', 0, 0);
$pdf->Cell(66, 5, 'Bandung, ' . date('d F Y'), 0, 1, 'C');

$pdf->Cell(120, 5, '', 0, 0);
$pdf->Cell(66, 5, 'Manager Cinderenna Mart', 0, 1, 'C');

$pdf->Ln(18);

$pdf->SetFont('Times', 'U', 10);
$pdf->Cell(120, 5, '', 0, 0);
$pdf->Cell(66, 5, '( TENA ERFIANA )', 0, 1, 'C');

$pdf->Output('I', 'Laporan_Laba_Rugi_CinderennaMart.pdf');
exit;
?>