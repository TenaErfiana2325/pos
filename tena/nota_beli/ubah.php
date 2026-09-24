<?php 
require_once '../database/tugas.php'; 
 
if (isset($_POST['btn_edit'])) { 
 
    $kode_nota       = trim(mysqli_real_escape_string($con, $_POST['kode_nota'])); 
    $kode_supplier   = trim(mysqli_real_escape_string($con, $_POST['kode_supplier'])); 
    $tgl_pembelian   = trim(mysqli_real_escape_string($con, $_POST['tgl_pembelian'])); 
    $total_pembelian = trim(mysqli_real_escape_string($con, $_POST['total_pembelian'])); 
    $status          = trim(mysqli_real_escape_string($con, $_POST['status'])); 
    $keterangan      = trim(mysqli_real_escape_string($con, $_POST['keterangan'])); 
 
    $query_edit = mysqli_query($con, "UPDATE tbl_nota_beli SET 
        kode_supplier = '$kode_supplier',
        tgl_pembelian = '$tgl_pembelian',
        total_pembelian = '$total_pembelian',
        status = '$status',
        keterangan = '$keterangan'
        WHERE kode_nota = '$kode_nota' 
    ") or die(mysqli_error($con)); 
 
    echo '<script>  
        alert("Data Berhasil Diedit"); 
        window.location.href = "../nota_beli"; 
    </script>'; 
} 
?>