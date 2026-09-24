<?php
require_once '../database/koneksi.php';
require_once '../aset_web/fpdf/fpdf.php'; 

// 1. Ambil kode_nota WAJIB dari URL
if (!isset($_GET['kode_nota_konsinyasi']) || empty($_GET['kode_nota_konsinyasi'])) {
    die("Error: Kode Nota Konsinyasi tidak ditemukan.");
}

$kode_nota = mysqli_real_escape_string($con, $_GET['kode_nota_konsinyasi']);

// 2. Query Detail Nota Konsinyasi & JOIN ke Barang Konsinyasi
$query_detail = mysqli_query($con, "
    SELECT d.*, k.nama_barang 
    FROM tbl_detail_nota_konsinyasi d
    LEFT JOIN tbl_konsinyasi k ON d.kode_barang = k.kode_barang
    WHERE d.kode_nota_konsinyasi = '$kode_nota'
") or die(mysqli_error($con));

$rv = mysqli_num_rows($query_detail);

if ($rv == 0) {
    die("Error: Data transaksi untuk nota konsinyasi ini tidak ditemukan.");
}

$data_array = array();
$total_qty_titip = 0;

while ($row = mysqli_fetch_array($query_detail)) {
    $data_array[] = $row;
    $total_qty_titip += $row['jumlah'];
}

// 3. Ambil info supplier/pemasok dan tanggal dari header nota
$query_header = mysqli_query($con, "
    SELECT n.*, s.nama_supplier 
    FROM tbl_nota_konsinyasi n
    LEFT JOIN tbl_supplier s ON n.kode_supplier = s.kode_supplier
    WHERE n.kode_nota_konsinyasi = '$kode_nota'
");
$data_header = mysqli_fetch_assoc($query_header);

$nama_pemasok = $data_header['nama_supplier'] ?? '-';
$tgl_transaksi = isset($data_header['tanggal']) ? date('d/m/Y', strtotime($data_header['tanggal'])) : date('d/m/Y');

// 4. Hitung tinggi nota dinamis (Struk thermal 58mm)
$tinggi_header 	= 22; 
$tinggi_info   	= 16;
$tinggi_th     	= 8;  
$tinggi_td     	= $rv * 5; 
$tinggi_footer 	= 38; 

$total_tinggi = $tinggi_header + $tinggi_info + $tinggi_th + $tinggi_td + $tinggi_footer;
if ($total_tinggi < 110) {
    $total_tinggi = 110;
}

// Inisialisasi PDF Ukuran 58mm
$pdf = new FPDF('P', 'mm', array(58, $total_tinggi));
$pdf->SetMargins(2, 3, 2);
$pdf->SetAutoPageBreak(false);
$pdf->AddPage();

// Gunakan font Courier agar tampilan menyerupai monospace/plaintext
$pdf->SetFont('Courier', '', 7);

// --- HEADER ---
$pdf->Cell(54, 3, '========================================', 0, 1, 'C');
$pdf->SetFont('Courier', 'B', 7.5);
$pdf->Cell(54, 4, 'NOTA TANDA TERIMA TITIP BARANG', 0, 1, 'C');
$pdf->SetFont('Courier', '', 7);
$pdf->Cell(54, 3, '========================================', 0, 1, 'C');

$pdf->Ln(1);

// --- INFO TRANSAKSI ---
$pdf->Cell(15, 3.5, 'No. Nota', 0, 0, 'L');
$pdf->Cell(39, 3.5, ': ' . $kode_nota, 0, 1, 'L');

$pdf->Cell(15, 3.5, 'Tanggal', 0, 0, 'L');
$pdf->Cell(39, 3.5, ': ' . $tgl_transaksi, 0, 1, 'L');

$pdf->Cell(15, 3.5, 'Pemasok', 0, 0, 'L');
$pdf->Cell(39, 3.5, ': ' . $nama_pemasok, 0, 1, 'L');

$pdf->Cell(54, 3, '----------------------------------------', 0, 1, 'C');

// --- HEADER TABEL ---
$pdf->SetFont('Courier', 'B', 7);
$pdf->Cell(26, 4, 'Nama Barang', 0, 0, 'L');
$pdf->Cell(10, 4, 'Qty', 0, 0, 'C');
$pdf->Cell(18, 4, 'Harga Jual', 0, 1, 'R');

$pdf->SetFont('Courier', '', 7);
$pdf->Cell(54, 3, '----------------------------------------', 0, 1, 'C');

// --- ISI TABEL ---
foreach ($data_array as $data) {
    $nama_item = !empty($data['nama_barang']) ? $data['nama_barang'] : $data['kode_barang'];
    
    // Potong nama barang jika terlalu panjang agar tidak merusak layout 58mm
    if (strlen($nama_item) > 16) {
        $nama_item = substr($nama_item, 0, 14) . '..';
    }

    $pdf->Cell(26, 4, $nama_item, 0, 0, 'L');
    $pdf->Cell(10, 4, $data['jumlah'] . ' pcs', 0, 0, 'C');
    $pdf->Cell(18, 4, 'Rp ' . number_format($data['harga_jual'], 0, ',', '.'), 0, 1, 'R');
}

$pdf->Cell(54, 3, '----------------------------------------', 0, 1, 'C');

// --- TOTAL BARANG TITIP ---
$pdf->SetFont('Courier', 'B', 7.5);
$pdf->Cell(54, 4, 'Total Barang Titip : ' . $total_qty_titip . ' pcs', 0, 1, 'L');

$pdf->Ln(2);

// --- AREA TANDA TANGAN ---
$pdf->SetFont('Courier', '', 7);
$pdf->Cell(27, 3.5, 'Penerima,', 0, 0, 'C');
$pdf->Cell(27, 3.5, 'Penitip,', 0, 1, 'C');

$pdf->Ln(9); // Spacing tempat tanda tangan

$pdf->Cell(27, 3.5, '( Kasir/Toko )', 0, 0, 'C');
$pdf->Cell(27, 3.5, '( ' . $nama_pemasok . ' )', 0, 1, 'C');

$pdf->Ln(1);
$pdf->Cell(54, 3, '========================================', 0, 1, 'C');

// Output PDF langsung di browser
$pdf->Output('I', 'Tanda_Terima_Titip_' . $kode_nota . '.pdf');
?>