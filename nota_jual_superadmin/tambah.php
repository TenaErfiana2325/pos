<?php 
require_once '../database/koneksi.php';

if (isset($_POST['btn_tambah'])) {
    
    $kode_nota       = trim(mysqli_real_escape_string($con, $_POST['kode_nota']));
    $kode_supplier   = trim(mysqli_real_escape_string($con, $_POST['kode_supplier']));
    $tgl_penjualan   = trim(mysqli_real_escape_string($con, $_POST['tgl_penjualan']));
    $total_penjualan = trim(mysqli_real_escape_string($con, $_POST['total_penjualan']));
    $status          = trim(mysqli_real_escape_string($con, $_POST['status']));
    $jenis_pembayaran = trim(mysqli_real_escape_string($con, $_POST['jenis_pembayaran']));
    $keterangan      = trim(mysqli_real_escape_string($con, $_POST['keterangan']));

    $cek_nota = mysqli_query($con, "SELECT kode_nota FROM tbl_notajual WHERE kode_nota = '$kode_nota'") or die(mysqli_error($con));

    $rv = mysqli_num_rows($cek_nota);
    if ($rv == 1) {
        echo '<script> alert ("kode_nota sudah terdaftar"); window.location.href = "../nota_jual_superadmin/"; </script>';
    } else {
        $query_simpan = mysqli_query($con, "INSERT INTO tbl_notajual (kode_nota, kode_supplier, tgl_penjualan, total_penjualan, status, jenis_pembayaran, keterangan) VALUES ('$kode_nota', '$kode_supplier', '$tgl_penjualan', '$total_penjualan', '$status', '$jenis_pembayaran', '$keterangan')") or die(mysqli_error($con));

        echo '<script> alert ("data berhasil di simpan"); window.location.href = "../nota_jual_superadmin/"; </script>';
    }
}
?>