<?php
require_once '../database/koneksi.php';

if (isset($_POST['btn_edit'])) {

    $kode_nota        = $_POST['kode_nota'];
    $kode_supplier    = $_POST['kode_supplier'];
    $tgl_pembelian    = $_POST['tgl_pembelian'];
    $total_pembelian  = $_POST['total_pembelian'];
    $status           = $_POST['status'];
    $jenis_pembayaran = $_POST['jenis_pembayaran']; // <--- Menerima data jenis_pembayaran
    $keterangan       = $_POST['keterangan'];

    // Update query dengan menambahkan jenis_pembayaran
    $query = mysqli_query($con, "UPDATE tbl_notabeli SET 
                kode_supplier    = '$kode_supplier',
                tgl_pembelian    = '$tgl_pembelian',
                total_pembelian  = '$total_pembelian',
                status           = '$status',
                jenis_pembayaran = '$jenis_pembayaran', 
                keterangan       = '$keterangan'
                WHERE kode_nota  = '$kode_nota'") or die(mysqli_error($con));

    if ($query) {
        echo "<script>alert('Data berhasil diubah!'); window.location='index.php';</script>";
    } else {
        echo "<script>alert('Gagal mengubah data!'); window.location='index.php';</script>";
    }
}
?>