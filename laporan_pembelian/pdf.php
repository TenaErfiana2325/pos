<?php
// Bersihkan output buffer agar tidak ada spasi/HTML liar yang merusak PDF
if (ob_get_length()) ob_clean();

require_once '../database/koneksi.php';
require_once '../aset_web/fpdf/fpdf.php';

// Ambil parameter tanggal dari URL jika ada
$tgl_mulai   = $_GET['tgl_mulai'] ?? '';
$tgl_selesai = $_GET['tgl_selesai'] ?? '';

// Filter Query Data Pembelian
if (!empty($tgl_mulai) && !empty($tgl_selesai)) {
    $query = mysqli_query($con, "
        SELECT * FROM tbl_notabeli 
        WHERE tgl_pembelian BETWEEN '$tgl_mulai' AND '$tgl_selesai' 
        ORDER BY tgl_pembelian DESC
    ") or die(mysqli_error($con));
    $sub_judul = 'Periode: ' . date('d/m/Y', strtotime($tgl_mulai)) . ' s/d ' . date('d/m/Y', strtotime($tgl_selesai));
} else {
    $query = mysqli_query($con, "
        SELECT * FROM tbl_notabeli 
        ORDER BY tgl_pembelian DESC
    ") or die(mysqli_error($con));
    $sub_judul = 'Semua Periode Pembelian';
}

// Inisialisasi FPDF (Ukuran A4 Portrait)
$pdf = new FPDF('P', 'mm', 'A4');
$pdf->SetMargins(12, 12, 12);
$pdf->AddPage();

// ================= KOP TOKO (CINDERENNA MART) =================
$pdf->SetFont('Arial', 'B', 20);
// Warna Teks Pink Tua / Soft Magenta (RGB: 216, 112, 147)
$pdf->SetTextColor(216, 112, 147); 
$pdf->Cell(186, 8, 'CINDERENNA MART', 0, 1, 'C');

$pdf->SetFont('Arial', '', 9);
$pdf->SetTextColor(100, 100, 100); // Warna Grey
$pdf->Cell(186, 5, 'Jl. Bandung, Indonesia | Telp: (021) 555-0199', 0, 1, 'C');

// Garis Pembatas Kop Surat Warna Lilac (RGB: 200, 162, 200)
$pdf->SetDrawColor(200, 162, 200);
$pdf->SetLineWidth(0.8);
$pdf->Line(12, 27, 198, 27);
$pdf->Ln(5);

// ================= JUDUL LAPORAN =================
$pdf->SetFont('Arial', 'B', 14);
$pdf->SetTextColor(60, 60, 60);
$pdf->Cell(186, 7, 'LAPORAN PEMBELIAN', 0, 1, 'C');

$pdf->SetFont('Arial', 'I', 10);
$pdf->SetTextColor(120, 120, 120);
$pdf->Cell(186, 5, $sub_judul, 0, 1, 'C');
$pdf->Ln(6);

// ================= HEADER TABEL =================
$pdf->SetFont('Arial', 'B', 10);

// Warna Background Header Tabel: Soft Lilac (RGB: 216, 191, 216)
$pdf->SetFillColor(216, 191, 216); 
// Warna Teks Header Tabel: Ungu Tua (RGB: 75, 0, 130)
$pdf->SetTextColor(75, 0, 130); 
$pdf->SetDrawColor(200, 162, 200);
$pdf->SetLineWidth(0.3);

$pdf->Cell(15, 8, 'No', 1, 0, 'C', true);
$pdf->Cell(55, 8, 'Kode Nota', 1, 0, 'C', true);
$pdf->Cell(56, 8, 'Tgl. Pembelian', 1, 0, 'C', true);
$pdf->Cell(60, 8, 'Total Pembelian', 1, 1, 'C', true);

// ================= ISI TABEL =================
$pdf->SetFont('Arial', '', 10);
$pdf->SetTextColor(40, 40, 40); // Warna teks isi hitam kelabu

$no = 1;
$grand_total = 0;

if (mysqli_num_rows($query) > 0) {
    while ($row = mysqli_fetch_array($query)) {
        $kode_nota = $row['kode_nota'];
        $tgl_beli  = date('d/m/Y', strtotime($row['tgl_pembelian']));
        $total     = $row['total_pembelian'];
        $grand_total += $total;

        // Striping / Selang-seling warna latar baris (Soft Pink vs Putih)
        if ($no % 2 == 0) {
            $pdf->SetFillColor(253, 242, 248); // Very soft pink
            $fill = true;
        } else {
            $fill = false;
        }

        $pdf->Cell(15, 7, $no++, 1, 0, 'C', $fill);
        $pdf->Cell(55, 7, $kode_nota, 1, 0, 'C', $fill);
        $pdf->Cell(56, 7, $tgl_beli, 1, 0, 'C', $fill);
        $pdf->Cell(60, 7, 'Rp ' . number_format($total, 0, ',', '.'), 1, 1, 'R', $fill);
    }

    // ================= BARIS TOTAL KESELURUHAN =================
    $pdf->SetFont('Arial', 'B', 10);
    $pdf->SetFillColor(245, 225, 238); // Combination Lilac-Pink Soft
    $pdf->SetTextColor(120, 20, 80);

    $pdf->Cell(126, 8, 'TOTAL KESELURUHAN', 1, 0, 'C', true);
    $pdf->Cell(60, 8, 'Rp ' . number_format($grand_total, 0, ',', '.'), 1, 1, 'R', true);
} else {
    $pdf->Cell(186, 8, 'Tidak ada data pembelian pada rentang tanggal ini.', 1, 1, 'C');
}

$pdf->Ln(10);

// ================= TANDA TANGAN / FOOTER =================
$pdf->SetFont('Arial', '', 10);
$pdf->SetTextColor(80, 80, 80);

$pdf->Cell(120, 5, '', 0, 0);
$pdf->Cell(66, 5, 'Bandung, ' . date('d F Y'), 0, 1, 'C');

$pdf->Cell(120, 5, '', 0, 0);
$pdf->Cell(66, 5, 'Manager Cinderenna Mart', 0, 1, 'C');

$pdf->Ln(15); // Ruang Tanda Tangan

$pdf->SetFont('Arial', 'U', 10);
$pdf->Cell(120, 5, '', 0, 0);
$pdf5 = $pdf->Cell(66, 5, '( Admin Operasional )', 0, 1, 'C');

// Output PDF langsung ke tab baru browser
$pdf->Output('I', 'Laporan_Pembelian_CinderennaMart.pdf');
exit;
?>