<html>
    <head>

    </head>
    <body>
        <?php 
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        require_once '../database/koneksi.php';

        $kode_nota = @$_GET['kode'];

        if (!empty($kode_nota)) {

            $hapus_nota = mysqli_query($con, "DELETE FROM tbl_notajual
            WHERE kode_nota = '$kode_nota'") or die(mysqli_error($con));

            echo '<script>alert("Data Barang '.$kode_nota.' Berhasil Dihapus");
            window.location.href="../nota_jual_superadmin";
            </script>';
        } else {
            echo '<script>alert("Kode Barang Tidak Ditemukan");
            window.location.href="../nota_jual_superadmin";
            </script>';
        }
        ?>
    </body>
</html>