<?php
require_once '../database/koneksi.php';

if (isset($_POST['btn_edit'])) {

    $kode_nota        = trim(mysqli_real_escape_string($con, $_POST['kode_nota']));
    $kode_supplier    = trim(mysqli_real_escape_string($con, $_POST['kode_supplier']));
    $tgl_penjualan    = trim(mysqli_real_escape_string($con, $_POST['tgl_penjualan']));
    $total_penjualan  = trim(mysqli_real_escape_string($con, $_POST['total_penjualan']));
    $status           = trim(mysqli_real_escape_string($con, $_POST['status']));
    $jenis_pembayaran = trim(mysqli_real_escape_string($con, $_POST['jenis_pembayaran']));
    
    $keterangan_input = trim($_POST['keterangan']);
    if (empty($keterangan_input)) {
        $keterangan = "NULL";
    } else {
        $keterangan = "'" . mysqli_real_escape_string($con, $keterangan_input) . "'";
    }

    $query_edit = mysqli_query($con, "UPDATE tbl_notajual SET
        kode_supplier    = '$kode_supplier',
        tgl_penjualan    = '$tgl_penjualan',
        total_penjualan  = '$total_penjualan',
        status           = '$status',
        jenis_pembayaran = '$jenis_pembayaran',
        keterangan       = $keterangan
        WHERE kode_nota  = '$kode_nota'
    ") or die(mysqli_error($con));

    echo '<script> alert ("Data Nota Jual Berhasil Diedit");
    window.location.href = "../nota_jual_superadmin";</script>';
    
}
?>