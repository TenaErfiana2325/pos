<?php
require_once "../database/koneksi.php";

if (isset($_POST['tambah'])) {
    $kode_barang   = mysqli_real_escape_string($con, $_POST['kode_barang']);
    $kode_supplier = mysqli_real_escape_string($con, $_POST['kode_supplier']);
    $nama_barang   = mysqli_real_escape_string($con, $_POST['nama_barang']);
    $merk          = mysqli_real_escape_string($con, $_POST['merk']);
    $stok          = mysqli_real_escape_string($con, $_POST['stok']);
    $harga_beli    = mysqli_real_escape_string($con, $_POST['harga_beli']);
    $harga_jual    = mysqli_real_escape_string($con, $_POST['harga_jual']);
    $barcode       = mysqli_real_escape_string($con, $_POST['barcode']);

    // Ambil data file foto
    $nama_foto = $_FILES['foto']['name'];
    $tmp_foto  = $_FILES['foto']['tmp_name'];

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
    $query = "INSERT INTO tbl_konsinyasi (kode_barang, kode_supplier, nama_barang, merk, stok, harga_beli, harga_jual, barcode, foto) 
              VALUES ('$kode_barang', '$kode_supplier', '$nama_barang', '$merk', '$stok', '$harga_beli', '$harga_jual', '$barcode', '$nama_file_db')";

    $simpan = mysqli_query($con, $query);

    if ($simpan) {
        echo "<script>alert('Data berhasil ditambahkan!'); window.location.href='index.php';</script>";
    } else {
        echo "<script>alert('Gagal menambah data: " . mysqli_error($con) . "'); window.location.href='index.php';</script>";
    }
}
?>