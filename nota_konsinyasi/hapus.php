<?php 
require_once '../database/koneksi.php';

$kode_nota_konsinyasi = $_GET['kode_nota_konsinyasi'] ?? '';

if (!empty($kode_nota_konsinyasi)) {
    // Menghapus data nota konsinyasi berdasarkan kode_nota_konsinyasi
    $hapus_nota = mysqli_query($con, "DELETE FROM tbl_nota_konsinyasi WHERE kode_nota_konsinyasi = '$kode_nota_konsinyasi'") or die(mysqli_error($con));

    echo '<script> 
        alert("Data nota ' . $kode_nota_konsinyasi . ' berhasil dihapus");
        window.location.href = "index.php";
    </script>';
} else {
    echo '<script> 
        window.location.href = "index.php";
    </script>';
}
?>