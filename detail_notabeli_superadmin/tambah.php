<?php
require_once '../database/koneksi.php';

if (isset($_POST['btn_tambah'])) {
    $kode_nota = $_POST['kode_nota'];
    $kode_brg = $_POST['kode_brg'];
    $jumlah = (int)$_POST['jumlah'];
    $harga_beli = $_POST['harga_beli'];
    $total_harga_beli = $_POST['total_harga_beli'];

    // 1. Simpan detail nota beli
    $query_simpan = mysqli_query($con, "INSERT INTO tbl_detail_notabeli (kode_nota, kode_brg, jumlah, harga_beli, total_harga_beli) VALUES ('$kode_nota', '$kode_brg', '$jumlah', '$harga_beli', '$total_harga_beli')");

    if ($query_simpan) {
        // 2. Update stok barang (Bertambah)
        mysqli_query($con, "UPDATE tbl_barang SET stok = stok + $jumlah WHERE kode_brg = '$kode_brg'");

        header("Location: index.php?kode_nota=" . $kode_nota);
        exit();
    } else {
        echo "<script>alert('Gagal menambah data'); window.history.back();</script>";
    }
}
?>