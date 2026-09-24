<?php
require_once "../database/koneksi.php";

if (isset($_POST['tambah'])) {
    $kode_brg        = mysqli_real_escape_string($con, $_POST['kode_brg']);
    $kode_supplier   = mysqli_real_escape_string($con, $_POST['kode_supplier']);
    $nama_brg        = mysqli_real_escape_string($con, $_POST['nama_brg']);
    $merk            = mysqli_real_escape_string($con, $_POST['merk']);
    $stok            = mysqli_real_escape_string($con, $_POST['stok']);
    $rata_harga_beli = mysqli_real_escape_string($con, $_POST['rata_harga_beli']);
    $harga_jual      = mysqli_real_escape_string($con, $_POST['harga_jual']);

    // Ambil data file foto
    $nama_foto = $_FILES['foto_brg']['name'];
    $tmp_foto  = $_FILES['foto_brg']['tmp_name'];

    if (!empty($nama_foto)) {
        // Buat nama file unik agar tidak timpa-menimpa
        $foto_baru = time() . '_' . $nama_foto;
        $target    = "img/" . $foto_baru;

        // Pindahkan file ke folder img
        if (move_uploaded_file($tmp_foto, $target)) {
            $nama_file_db = $foto_baru;
        } else {
            $nama_file_db = NULL;
        }
    } else {
        $nama_file_db = NULL;
    }

    // Query simpan ke database
    $query = "INSERT INTO tbl_barang (kode_brg, kode_supplier, nama_brg, merk, stok, rata_harga_beli, harga_jual, foto_brg) 
              VALUES ('$kode_brg', '$kode_supplier', '$nama_brg', '$merk', '$stok', '$rata_harga_beli', '$harga_jual', '$nama_file_db')";

    $simpan = mysqli_query($con, $query);

    if ($simpan) {
        echo "<script>alert('Data berhasil ditambahkan!'); window.location.href='index.php';</script>";
    } else {
        echo "<script>alert('Gagal menambah data: " . mysqli_error($con) . "'); window.location.href='index.php';</script>";
    }
}
?>