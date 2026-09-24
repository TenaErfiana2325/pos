<?php
require_once '../database/koneksi.php';
require('../aset_web/fpdf/fpdf.php');
require('../aset_web/fpdf/code128.php'); 

// 1. Ambil kode_nota WAJIB dari URL
if (!isset($_GET['kode_nota']) || empty($_GET['kode_nota'])) {
    die("Error: Kode Nota tidak ditemukan.");
}

$kode_nota = mysqli_real_escape_string($con, $_GET['kode_nota']);

// 2. Query Status dari Master Tabel Nota Beli (tbl_notabeli)
$q_nota = mysqli_query($con, "SELECT status FROM tbl_notabeli WHERE kode_nota = '$kode_nota'") or die(mysqli_error($con));
$d_nota = mysqli_fetch_array($q_nota);
$status_nota = $d_nota['status'] ?? 'L';

// 3. Query HANYA data nota yang dipilih dengan klausa AND (tanpa JOIN)
$query_detail = mysqli_query($con, "SELECT d.*, b.nama_brg 
                                    FROM tbl_detail_notabeli d, tbl_barang b 
                                    WHERE d.kode_brg = b.kode_brg 
                                    AND d.kode_nota = '$kode_nota'") or die(mysqli_error($con));

$rv = mysqli_num_rows($query_detail);

if ($rv == 0) {
    die("Error: Data transaksi untuk nota ini tidak ditemukan.");
}

$data_array = array();
$grand_total = 0;

while ($row = mysqli_fetch_array($query_detail)) {
    $data_array[] = $row;
    $grand_total += $row['total_harga_beli'];
}

// 4. Logika Perhitungan Status Pembayaran berdasarkan ENUM ('L', '2', '3', '4')
$persen_bayar = 1; // Default 100% (Lunas)

if ($status_nota == '2') {
    $persen_bayar = 0.25; // 25%
    $label_status = 'Belum Lunas (DP 25%)';
} elseif ($status_nota == '3') {
    $persen_bayar = 0.50; // 50%
    $label_status = 'Belum Lunas (DP 50%)';
} elseif ($status_nota == '4') {
    $persen_bayar = 0.75; // 75%
    $label_status = 'Belum Lunas (DP 75%)';
} else {
    $persen_bayar = 1.00; // Lunas
    $label_status = 'Lunas';
}

$dibayar = $grand_total * $persen_bayar;
$sisa_bayar = $grand_total - $dibayar;

// 5. Hitung tinggi nota dinamis
$tinggi_header  = 28; 
$tinggi_info    = 18;
$tinggi_th      = 6;  
$tinggi_td      = $rv * 9; 
$tinggi_barcode = 15; 
$tinggi_footer  = 35; 

$total_tinggi = $tinggi_header + $tinggi_info + $tinggi_th + $tinggi_td + $tinggi_barcode + $tinggi_footer;
if ($total_tinggi < 110) {
    $total_tinggi = 110;
}

// Inisialisasi PDF Ukuran 58mm
$pdf = new PDF_Barcode('P', 'mm', array(58, $total_tinggi));
$pdf->SetMargins(3, 4, 3);
$pdf->SetAutoPageBreak(false);
$pdf->AddPage();

// --- HEADER TOKO ---
$pdf->Image('../aset_web/img/Logotoko.png', 4, 3, 10);

$pdf->SetXY(15, 4);
$pdf->SetFont('Arial', 'B', 8.5);
$pdf->Cell(40, 3.5, 'CINDERENNA MART', 0, 1, 'L');

$pdf->SetX(15);
$pdf->SetFont('Arial', '', 5);
$pdf->Cell(40, 2.3, 'Jl. Raya Thumbelinna KM 1', 0, 1, 'L');
$pdf->SetX(15);
$pdf->Cell(40, 2.3, 'Bandung, Jawa Barat 52275', 0, 1, 'L');

$pdf->SetLineWidth(0.2);
$pdf->Line(3, 17, 55, 17);

// --- INFO TRANSAKSI ---
$pdf->SetY(19);
$pdf->SetFont('Arial', 'B', 7.5);
$pdf->Cell(52, 4, 'NOTA PEMBELIAN', 0, 1, 'C');

$pdf->SetFont('Arial', '', 6.5);
$pdf->Cell(15, 3.5, 'No. Nota', 0, 0, 'L');
$pdf->Cell(37, 3.5, ': ' . $kode_nota, 0, 1, 'L');

$pdf->Cell(15, 3.5, 'Tgl / Waktu', 0, 0, 'L');
$pdf->Cell(37, 3.5, ': ' . date('d/m/Y H:i'), 0, 1, 'L');

$pdf->Cell(15, 3.5, 'Status', 0, 0, 'L');
$pdf->Cell(37, 3.5, ': ' . $label_status, 0, 1, 'L');

$pdf->Ln(1);
$pdf->Line(3, $pdf->GetY(), 55, $pdf->GetY());
$pdf->Ln(1.5);

// --- HEADER TABEL ---
$pdf->SetFont('Arial', 'B', 6.5);
$pdf->Cell(24, 4, 'Nama Barang', 0, 0, 'L');
$pdf->Cell(6,  4, 'Qty', 0, 0, 'C');
$pdf->Cell(10, 4, 'Harga', 0, 0, 'R');
$pdf->Cell(12, 4, 'Total', 0, 1, 'R');

$pdf->Line(3, $pdf->GetY(), 55, $pdf->GetY());
$pdf->Ln(1.5);

// --- ISI TABEL ---
$pdf->SetFont('Arial', '', 6.5);
foreach ($data_array as $data) {
    $nama_item = !empty($data['nama_brg']) ? $data['nama_brg'] : $data['kode_brg'];
    
    $pdf->SetFont('Arial', 'B', 6.5);
    $pdf->Cell(52, 4, $nama_item, 0, 1, 'L');
    
    $pdf->SetFont('Arial', '', 6.5);
    $pdf->Cell(24, 4, '', 0, 0, 'L');
    $pdf->Cell(6,  4, $data['jumlah'] . 'x', 0, 0, 'C');
    $pdf->Cell(10, 4, number_format($data['harga_beli']), 0, 0, 'R');
    $pdf->Cell(12, 4, number_format($data['total_harga_beli']), 0, 1, 'R');
}

$pdf->Ln(1);
$pdf->Line(3, $pdf->GetY(), 55, $pdf->GetY());
$pdf->Ln(2);

// --- TOTAL & RINCIAN PEMBAYARAN ---
$pdf->SetFont('Arial', 'B', 6.5);

if ($status_nota != 'L') {
    $pdf->Cell(24, 3.5, 'TOTAL HAKIKI', 0, 0, 'L');
    $pdf->Cell(28, 3.5, 'Rp ' . number_format($grand_total), 0, 1, 'R');

    $pdf->Cell(24, 3.5, 'BAYAR (DP ' . ($persen_bayar * 100) . '%)', 0, 0, 'L');
    $pdf->Cell(28, 3.5, 'Rp ' . number_format($dibayar), 0, 1, 'R');

    $pdf->Cell(24, 3.5, 'SISA KEKURANGAN', 0, 0, 'L');
    $pdf->Cell(28, 3.5, 'Rp ' . number_format($sisa_bayar), 0, 1, 'R');
} else {
    $pdf->Cell(24, 4, 'TOTAL BAYAR', 0, 0, 'L');
    $pdf->Cell(28, 4, 'Rp ' . number_format($grand_total), 0, 1, 'R');
}

$pdf->Ln(1);
$pdf->Line(3, $pdf->GetY(), 55, $pdf->GetY());
$pdf->Ln(3);

// --- FOOTER ---
$pdf->SetFont('Arial', 'B', 7);
$pdf->Cell(52, 4, 'Terima Kasih Atas Kunjungannya!', 0, 1, 'C');
$pdf->SetFont('Arial', 'I', 6);
$pdf->Cell(52, 3.5, 'Jangan lupa datang kembali yaa *(^o^)*', 0, 1, 'C');
$pdf->Cell(52, 3, '~ Cinderenna Mart ~', 0, 1, 'C');

$pdf->Output('I', 'Nota_Beli_' . $kode_nota . '.pdf');
?>