<html>
    <head>

    </head>
    <body>
        <?php 
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        require_once '../database/koneksi.php';

        $kode_brg = @$_GET['kode'];

        if (!empty($kode_brg)) {
            // Menghapus data dari tbl_barang berdasarkan kode_brg
            $hapus_barang = mysqli_query($con, "DELETE FROM tbl_barang 
            WHERE kode_brg = '$kode_brg'") or die(mysqli_error($con));

            echo '<script>alert("Data Barang '.$kode_brg.' Berhasil Dihapus");
            window.location.href="../barang_superadmin";
            </script>';
        } else {
            echo '<script>alert("Kode Barang Tidak Ditemukan");
            window.location.href="../barang_superadmin";
            </script>';
        }
        ?>
    </body>
</html>