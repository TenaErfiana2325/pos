<?php 
require_once '../database/koneksi.php'; 

if (isset($_POST['btn_edit'])) { 

    $urut             = trim(mysqli_real_escape_string($con, $_POST['urut'])); 
    $kode_nota        = trim(mysqli_real_escape_string($con, $_POST['kode_nota'])); 
    $kode_brg         = trim(mysqli_real_escape_string($con, $_POST['kode_brg'])); 
    $jumlah           = trim(mysqli_real_escape_string($con, $_POST['jumlah'])); 
    $harga_beli       = trim(mysqli_real_escape_string($con, $_POST['harga_beli'])); 
    $total_harga_beli = trim(mysqli_real_escape_string($con, $_POST['total_harga_beli'])); 

    $query_edit = mysqli_query($con, "UPDATE tbl_detail_notabeli SET 
        kode_nota        = '$kode_nota',
        kode_brg         = '$kode_brg',
        jumlah           = '$jumlah',
        harga_beli       = '$harga_beli',
        total_harga_beli = '$total_harga_beli'
        WHERE urut       = '$urut' 
    ") or die(mysqli_error($con)); 

    echo '<script>  
        alert("Data Detail Nota Beli Berhasil Diedit"); 
        window.location.href = "index.php"; 
    </script>'; 
} 
?>