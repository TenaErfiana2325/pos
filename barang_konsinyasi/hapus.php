<html>
    <head>

    </head>
    <body>
        <?php 
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        require_once '../database/koneksi.php';

        $kode_barang = @$_GET['kode'];

        if (!empty($kode_barang)) {
            // Menghapus data dari tbl_konsinyasi berdasarkan kode_barang
            $hapus_barang = mysqli_query($con, "DELETE FROM tbl_konsinyasi 
            WHERE kode_barang = '$kode_barang'") or die(mysqli_error($con));

            echo '<script>alert("Data Barang '.$kode_barang.' Berhasil Dihapus");
            window.location.href="index.php";
            </script>';
        } else {
            echo '<script>alert("Kode Barang Tidak Ditemukan");
            window.location.href="index.php";
            </script>';
        }
        ?>
    </body>
</html>