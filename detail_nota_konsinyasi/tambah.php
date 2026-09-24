<?php 
require_once '../database/koneksi.php';

if (isset($_POST['btn_tambah'])) {
    
    $kode_nota_konsinyasi = trim(mysqli_real_escape_string($con, $_POST['kode_nota_konsinyasi']));
    $kode_barang          = trim(mysqli_real_escape_string($con, $_POST['kode_barang']));
    $jumlah               = trim(mysqli_real_escape_string($con, $_POST['jumlah']));
    $harga_beli           = trim(mysqli_real_escape_string($con, $_POST['harga_beli']));
    $harga_jual           = trim(mysqli_real_escape_string($con, $_POST['harga_jual']));

    $query_simpan = mysqli_query($con, "INSERT INTO tbl_detail_nota_konsinyasi (kode_nota_konsinyasi, kode_barang, jumlah, harga_beli, harga_jual) VALUES ('$kode_nota_konsinyasi', '$kode_barang', '$jumlah', '$harga_beli', '$harga_jual')") or die(mysqli_error($con));

    echo '<script> alert ("data berhasil di simpan"); window.location.href = "index.php?kode_nota_konsinyasi=' . $kode_nota_konsinyasi . '"; </script>';
}
?>