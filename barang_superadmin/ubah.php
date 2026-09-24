<?php
require_once "../database/koneksi.php";

if (isset($_POST['btn_edit'])) {
    $kode_brg        = mysqli_real_escape_string($con, $_POST['kode_brg']);
    $kode_supplier   = mysqli_real_escape_string($con, $_POST['kode_supplier']);
    $nama_brg        = mysqli_real_escape_string($con, $_POST['nama_brg']);
    $merk            = mysqli_real_escape_string($con, $_POST['merk']);
    $stok            = mysqli_real_escape_string($con, $_POST['stok']);
    $rata_harga_beli = mysqli_real_escape_string($con, $_POST['rata_harga_beli']);
    $harga_jual      = mysqli_real_escape_string($con, $_POST['harga_jual']);

    $nama_foto = $_FILES['foto_brg']['name'];
    $tmp_foto  = $_FILES['foto_brg']['tmp_name'];

    // Jika pengguna mengunggah foto baru
    if (!empty($nama_foto)) {
        $foto_baru = time() . '_' . $nama_foto;
        $target    = "img/" . $foto_baru;

        // Ambil data foto lama untuk dihapus dari folder img
        $query_lama = mysqli_query($con, "SELECT foto_brg FROM tbl_barang WHERE kode_brg = '$kode_brg'");
        $data_lama  = mysqli_fetch_array($query_lama);

        if (!empty($data_lama['foto_brg']) && file_exists("img/" . $data_lama['foto_brg'])) {
            unlink("img/" . $data_lama['foto_brg']);
        }

        if (move_uploaded_file($tmp_foto, $target)) {
            $query = "UPDATE tbl_barang SET 
                        kode_supplier   = '$kode_supplier',
                        nama_brg        = '$nama_brg',
                        merk            = '$merk',
                        stok            = '$stok',
                        rata_harga_beli = '$rata_harga_beli',
                        harga_jual      = '$harga_jual',
                        foto_brg        = '$foto_baru'
                      WHERE kode_brg    = '$kode_brg'";
        }
    } else {
        // Jika foto tidak diubah
        $query = "UPDATE tbl_barang SET 
                    kode_supplier   = '$kode_supplier',
                    nama_brg        = '$nama_brg',
                    merk            = '$merk',
                    stok            = '$stok',
                    rata_harga_beli = '$rata_harga_beli',
                    harga_jual      = '$harga_jual'
                  WHERE kode_brg    = '$kode_brg'";
    }

    $update = mysqli_query($con, $query);

    if ($update) {
        echo "<script>alert('Data barang berhasil diperbarui!'); window.location.href='index.php';</script>";
    } else {
        echo "<script>alert('Gagal memperbarui data: " . mysqli_error($con) . "'); window.location.href='index.php';</script>";
    }
}
?>