<html>
    <head>

    </head>
    <body>
        <?php 
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        require_once '../database/koneksi.php';

        $kode_supplier = @$_GET['kode'];

        if (!empty($kode_supplier)) {
            // Menghapus data dari tbl_supplier berdasarkan kode_supplier
            $hapus_supplier = mysqli_query($con, "DELETE FROM tbl_supplier 
            WHERE kode_supplier = '$kode_supplier'") or die(mysqli_error($con));

            echo '<script>alert("Data Supplier '.$kode_supplier.' Berhasil Dihapus");
            window.location.href="../supplier_superadmin";
            </script>';
        } else {
            echo '<script>alert("Kode Supplier Tidak Ditemukan");
            window.location.href="../supplier_superadmin";
            </script>';
        }
        ?>
    </body>
</html>