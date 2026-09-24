<?php 
require_once '../database/tugas.php';

if (isset($_POST['btn_tambah'])) {
    
    $kode_nota       = trim(mysqli_real_escape_string($con, $_POST['kode_nota']));
    $kode_supplier   = trim(mysqli_real_escape_string($con, $_POST['kode_supplier']));
    $tgl_penjualan   = trim(mysqli_real_escape_string($con, $_POST['tgl_penjualan']));
    $total_penjualan = trim(mysqli_real_escape_string($con, $_POST['total_penjualan']));
    $status          = trim(mysqli_real_escape_string($con, $_POST['status']));
    $keterangan      = trim(mysqli_real_escape_string($con, $_POST['keterangan']));

    $cek_nota = mysqli_query($con, "SELECT kode_nota FROM tbl_nota_jual WHERE kode_nota = '$kode_nota'") or die(mysqli_error($con));

    $rv = mysqli_num_rows($cek_nota);
    if ($rv == 1) {
        echo '<script> alert ("kode_nota sudah terdaftar"); window.location.href = "../nota_jual/"; </script>';
    } else {
        $query_simpan = mysqli_query($con, "INSERT INTO tbl_nota_jual (kode_nota, kode_supplier, tgl_penjualan, total_penjualan, status, keterangan) VALUES ('$kode_nota', '$kode_supplier', '$tgl_penjualan', '$total_penjualan', '$status', '$keterangan')") or die(mysqli_error($con));

        echo '<script> alert ("data berhasil di simpan"); window.location.href = "../nota_jual/"; </script>';
    }
}
?>