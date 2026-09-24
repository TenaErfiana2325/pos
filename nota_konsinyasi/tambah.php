<?php 
require_once '../database/koneksi.php';

if (isset($_POST['btn_tambah'])) {
    
    $kode_nota_konsinyasi = trim(mysqli_real_escape_string($con, $_POST['kode_nota_konsinyasi']));
    $kode_supplier        = trim(mysqli_real_escape_string($con, $_POST['kode_supplier']));
    $tgl_titip            = trim(mysqli_real_escape_string($con, $_POST['tgl_titip']));
    $total_item           = trim(mysqli_real_escape_string($con, $_POST['total_item']));

    $cek_brg = mysqli_query($con, "SELECT kode_nota_konsinyasi FROM tbl_nota_konsinyasi WHERE kode_nota_konsinyasi = '$kode_nota_konsinyasi'") or die(mysqli_error($con));

    $rv = mysqli_num_rows($cek_brg);
    if ($rv == 1) {
        echo '<script> alert ("kode_nota_konsinyasi sudah terdaftar"); window.location.href = "index.php"; </script>';
    } else {
        $query_simpan = mysqli_query($con, "INSERT INTO tbl_nota_konsinyasi (kode_nota_konsinyasi, kode_supplier, tgl_titip, total_item) VALUES ('$kode_nota_konsinyasi', '$kode_supplier', '$tgl_titip', '$total_item')") or die(mysqli_error($con));

        echo '<script> alert ("data berhasil di simpan"); window.location.href = "index.php"; </script>';
    }
}
?>