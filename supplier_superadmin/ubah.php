<?php
require_once '../database/koneksi.php';

if (isset($_POST['btn_edit'])) {

    $kode_supplier   = trim(mysqli_real_escape_string($con, $_POST['kode_supplier']));
    $nama_supplier   = trim(mysqli_real_escape_string($con, $_POST['nama_supplier']));
    $nama_pic        = trim(mysqli_real_escape_string($con, $_POST['nama_pic']));
    $kontak_pic      = trim(mysqli_real_escape_string($con, $_POST['kontak_pic']));
    $alamat_supplier = trim(mysqli_real_escape_string($con, $_POST['alamat_supplier']));
    $website         = trim(mysqli_real_escape_string($con, $_POST['website']));
    $akun_ig         = trim(mysqli_real_escape_string($con, $_POST['akun_ig']));
    $akun_tiktok     = trim(mysqli_real_escape_string($con, $_POST['akun_tiktok']));

    // Memperbarui data ke tabel tbl_supplier
    $query_edit = mysqli_query($con, "UPDATE tbl_supplier SET
        nama_supplier   = '$nama_supplier',
        nama_pic        = '$nama_pic',
        kontak_pic      = '$kontak_pic',
        alamat_supplier = '$alamat_supplier',
        website         = '$website',
        akun_ig         = '$akun_ig',
        akun_tiktok     = '$akun_tiktok'
        WHERE kode_supplier = '$kode_supplier'
    ") or die(mysqli_error($con));

    // Mengarahkan kembali ke folder supplier_superadmin
    echo '<script> alert ("Data Supplier Berhasil Diedit");
    window.location.href = "../supplier_superadmin";</script>';
    
}
?>