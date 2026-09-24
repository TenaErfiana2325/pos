<?php 
require_once '../database/koneksi.php';

$kode_nota = $_GET['kode_nota'] ?? '';

if (!empty($kode_nota)) {
    // Menghapus data nota beli berdasarkan kode_nota
    $hapus_nota = mysqli_query($con, "DELETE FROM tbl_notabeli WHERE kode_nota = '$kode_nota'") or die(mysqli_error($con));

    echo '<script> 
        alert("Data nota ' . $kode_nota . ' berhasil dihapus");
        window.location.href = "../nota_beli_superadmin/";
    </script>';
} else {
    echo '<script> 
        window.location.href = "../nota_beli_superadmin/";
    </script>';
}
?>