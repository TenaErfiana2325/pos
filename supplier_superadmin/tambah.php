<?php
require_once ('../database/koneksi.php');

if (isset($_POST['tambah'])) {
    $kode_supplier   = trim(mysqli_real_escape_string($con, $_POST['kode_supplier']));
    $nama_supplier   = trim(mysqli_real_escape_string($con, $_POST['nama_supplier']));
    $nama_pic        = trim(mysqli_real_escape_string($con, $_POST['nama_pic']));
    $kontak_pic      = trim(mysqli_real_escape_string($con, $_POST['kontak_pic']));
    $alamat_supplier = trim(mysqli_real_escape_string($con, $_POST['alamat_supplier']));
    $website         = trim(mysqli_real_escape_string($con, $_POST['website']));
    $akun_ig         = trim(mysqli_real_escape_string($con, $_POST['akun_ig']));
    $akun_tiktok     = trim(mysqli_real_escape_string($con, $_POST['akun_tiktok']));

    // Cek ketersediaan kode_supplier di tbl_supplier
    $cek_supplier = mysqli_query($con, "SELECT kode_supplier FROM tbl_supplier WHERE kode_supplier= '$kode_supplier' ")
    or die (mysqli_error($con));
    $rv = mysqli_num_rows($cek_supplier);

    if ($rv == 1) {
        echo '<script> alert("Kode Supplier Sudah Terdaftar! Input Yang Lain");
        window.location.href="../supplier_superadmin" </script>';
    } else {
        // Simpan data ke tbl_supplier
        $query_simpan = mysqli_query($con, "INSERT INTO tbl_supplier
         (kode_supplier,
          nama_supplier, 
          nama_pic, 
          kontak_pic, 
          alamat_supplier,
          website,
          akun_ig,
          akun_tiktok) 
          VALUES
          ('$kode_supplier',
          '$nama_supplier',
          '$nama_pic',
          '$kontak_pic',
          '$alamat_supplier',
          '$website',
          '$akun_ig',
          '$akun_tiktok')
          ") or die (mysqli_error($con));

          echo '<script> alert("Data Supplier Berhasil Disimpan");
          window.location.href="../supplier_superadmin" </script>';
    }
}
?>