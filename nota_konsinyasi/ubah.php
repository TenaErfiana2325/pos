<?php 
require_once '../database/koneksi.php'; 

if (isset($_POST['btn_edit'])) { 

    $kode_nota_konsinyasi = trim(mysqli_real_escape_string($con, $_POST['kode_nota_konsinyasi'])); 
    $kode_supplier        = trim(mysqli_real_escape_string($con, $_POST['kode_supplier'])); 
    $tgl_titip            = trim(mysqli_real_escape_string($con, $_POST['tgl_titip'])); 
    $total_item           = trim(mysqli_real_escape_string($con, $_POST['total_item'])); 

    $query_edit = mysqli_query($con, "UPDATE tbl_nota_konsinyasi SET 
        kode_supplier = '$kode_supplier',
        tgl_titip = '$tgl_titip',
        total_item = '$total_item'
        WHERE kode_nota_konsinyasi = '$kode_nota_konsinyasi' 
    ") or die(mysqli_error($con)); 

    echo '<script>  
        alert("Data Berhasil Diedit"); 
        window.location.href = "index.php"; 
    </script>'; 
} 
?>