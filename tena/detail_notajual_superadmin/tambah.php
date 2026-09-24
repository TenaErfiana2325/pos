<?php
require_once '../database/tugas.php';

if (isset($_POST['btn_tambah'])) {
    $kode_nota        = $_POST['kode_nota'];
    $kode_brg         = $_POST['kode_brg'];
    $jumlah           = $_POST['jumlah'];
    $harga_jual       = $_POST['harga_jual'];
    $total_harga_beli = $_POST['total_harga_beli'];

    // 1. Simpan data detail nota jual
    $query_insert = "INSERT INTO tbl_detail_notajual (kode_nota, kode_brg, jumlah, harga_jual, total_harga_beli) 
                     VALUES ('$kode_nota', '$kode_brg', '$jumlah', '$harga_jual', '$total_harga_beli')";

    if (mysqli_query($con, $query_insert)) {
        // 2. Kurangi stok barang di tbl_barang
        $query_update_stok = "UPDATE tbl_barang 
                              SET stok = stok - $jumlah 
                              WHERE kode_brg = '$kode_brg'";
                              
        mysqli_query($con, $query_update_stok);

        header("Location: index.php?kode_nota=" . $kode_nota);
    } else {
        echo "Gagal menambahkan data: " . mysqli_error($con);
    }
}
?>