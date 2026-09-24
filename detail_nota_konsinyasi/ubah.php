<?php 
require_once '../database/koneksi.php'; 

if (isset($_POST['btn_edit'])) { 

    $id_detail            = trim(mysqli_real_escape_string($con, $_POST['id_detail']));
    $kode_nota_konsinyasi = trim(mysqli_real_escape_string($con, $_POST['kode_nota_konsinyasi'])); 
    $kode_barang          = trim(mysqli_real_escape_string($con, $_POST['kode_barang'])); 
    $jumlah               = trim(mysqli_real_escape_string($con, $_POST['jumlah'])); 
    $harga_beli           = trim(mysqli_real_escape_string($con, $_POST['harga_beli'])); 
    $harga_jual           = trim(mysqli_real_escape_string($con, $_POST['harga_jual'])); 

    $query_edit = mysqli_query($con, "UPDATE tbl_detail_nota_konsinyasi SET 
        kode_barang = '$kode_barang',
        jumlah = '$jumlah',
        harga_beli = '$harga_beli',
        harga_jual = '$harga_jual'
        WHERE id_detail = '$id_detail' 
    ") or die(mysqli_error($con)); 

    echo '<script>  
        alert("Data Berhasil Diedit"); 
        window.location.href = "index.php?kode_nota_konsinyasi=' . $kode_nota_konsinyasi . '"; 
    </script>'; 
} 
?>