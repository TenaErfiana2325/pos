<?php
require_once "../database/koneksi.php";

if (isset($_POST['btn_edit'])) {
    $kode_barang   = mysqli_real_escape_string($con, $_POST['kode_barang']);
    $kode_supplier = mysqli_real_escape_string($con, $_POST['kode_supplier']);
    $nama_barang   = mysqli_real_escape_string($con, $_POST['nama_barang']);
    $merk          = mysqli_real_escape_string($con, $_POST['merk']);
    $stok          = mysqli_real_escape_string($con, $_POST['stok']);
    $harga_beli    = mysqli_real_escape_string($con, $_POST['harga_beli']);
    $harga_jual    = mysqli_real_escape_string($con, $_POST['harga_jual']);
    $barcode       = mysqli_real_escape_string($con, $_POST['barcode']);

    $nama_foto = $_FILES['foto']['name'];
    $tmp_foto  = $_FILES['foto']['tmp_name'];

    // Jika pengguna mengunggah foto baru
    if (!empty($nama_foto)) {
        $foto_baru = time() . '_' . $nama_foto;
        $target    = "img/" . $foto_baru;

        // Ambil data foto lama untuk dihapus dari folder img
        $query_lama = mysqli_query($con, "SELECT foto FROM tbl_konsinyasi WHERE kode_barang = '$kode_barang'");
        $data_lama  = mysqli_fetch_array($query_lama);

        if (!empty($data_lama['foto']) && file_exists("img/" . $data_lama['foto'])) {
            unlink("img/" . $data_lama['foto']);
        }

        if (move_uploaded_file($tmp_foto, $target)) {
            $query = "UPDATE tbl_konsinyasi SET 
                        kode_supplier = '$kode_supplier',
                        nama_barang   = '$nama_barang',
                        merk          = '$merk',
                        stok          = '$stok',
                        harga_beli    = '$harga_beli',
                        harga_jual    = '$harga_jual',
                        barcode       = '$barcode',
                        foto          = '$foto_baru'
                      WHERE kode_barang = '$kode_barang'";
        }
    } else {
        // Jika foto tidak diubah
        $query = "UPDATE tbl_konsinyasi SET 
                    kode_supplier = '$kode_supplier',
                    nama_barang   = '$nama_barang',
                    merk          = '$merk',
                    stok          = '$stok',
                    harga_beli    = '$harga_beli',
                    harga_jual    = '$harga_jual',
                    barcode       = '$barcode'
                  WHERE kode_barang = '$kode_barang'";
    }

    $update = mysqli_query($con, $query);

    if ($update) {
        echo "<script>alert('Data barang berhasil diperbarui!'); window.location.href='index.php';</script>";
    } else {
        echo "<script>alert('Gagal memperbarui data: " . mysqli_error($con) . "'); window.location.href='index.php';</script>";
    }
}
?>