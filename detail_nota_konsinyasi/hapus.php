<?php 
require_once '../database/koneksi.php';

$id_detail = $_GET['id_detail'] ?? '';

if (!empty($id_detail)) {
    // Ambil kode_nota_konsinyasi untuk redirect kembali ke halaman detail
    $query_get = mysqli_query($con, "SELECT kode_nota_konsinyasi FROM tbl_detail_nota_konsinyasi WHERE id_detail = '$id_detail'") or die(mysqli_error($con));
    $data_get  = mysqli_fetch_array($query_get);
    $kode_nota_konsinyasi = $data_get['kode_nota_konsinyasi'] ?? '';

    // Menghapus data detail nota konsinyasi berdasarkan id_detail
    $hapus_nota = mysqli_query($con, "DELETE FROM tbl_detail_nota_konsinyasi WHERE id_detail = '$id_detail'") or die(mysqli_error($con));

    echo '<script> 
        alert("Data item berhasil dihapus");
        window.location.href = "index.php?kode_nota_konsinyasi=' . $kode_nota_konsinyasi . '";
    </script>';
} else {
    echo '<script> 
        window.location.href = "../nota_konsinyasi/index.php";
    </script>';
}
?>