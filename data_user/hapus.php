<html>
    <head>

    </head>
    <body>
        <?php 
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        require_once '../database/koneksi.php';

        $pengguna_login = $_SESSION['username'] ?? '';
        $pengguna       = @$_GET['user'];

        // Pengecekan jumlah Superadmin ('S') di tbl_user
        $cek_admin = mysqli_query($con, "SELECT COUNT(*) AS jumlah
        FROM tbl_user WHERE peran='S'") or die(mysqli_error($con));
        $data   = mysqli_fetch_assoc($cek_admin);
        $jumlah = $data['jumlah'];

        if ($pengguna_login == $pengguna || ($jumlah == 1 && $pengguna_login == $pengguna)) {
            echo '<script>alert("Anda Tidak Dapat Menghapus Akun Diri Anda Sendiri Atau Akun Superadmin Tinggal 1");
            window.location.href="../data_user";
            </script>';

        } elseif ($pengguna_login != $pengguna && $jumlah == 0) {
            $hapus_pengguna = mysqli_query($con, "DELETE FROM tbl_user 
            WHERE username = '$pengguna'") or die(mysqli_error($con));
            echo '<script>alert("Data Pengguna '.$pengguna.' Berhasil Dihapus");
            window.location.href="../data_user";
            </script>';
            
        } else {
            $hapus_pengguna = mysqli_query($con, "DELETE FROM tbl_user 
            WHERE username = '$pengguna'") or die(mysqli_error($con));
            echo '<script>alert("Data Pengguna '.$pengguna.' Berhasil Dihapus");
            window.location.href="../data_user";
            </script>';
        }
        ?>
    </body>
</html>