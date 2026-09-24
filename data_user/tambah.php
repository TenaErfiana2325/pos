<?php
require_once ('../database/koneksi.php');

if (isset($_POST['tambah'])) {
    $username       = trim(mysqli_real_escape_string($con, $_POST['username']));
    $nama_panggilan = trim(mysqli_real_escape_string($con, $_POST['nama_panggilan']));
    $peran          = trim(mysqli_real_escape_string($con, $_POST['peran']));
    $password       = sha1($username);
    $pin2fa         = '1234';

    // Cek ketersediaan username di tbl_user
    $cek_user = mysqli_query($con, "SELECT username FROM tbl_user WHERE username= '$username' ")
    or die (mysqli_error($con));
    $rv = mysqli_num_rows($cek_user);

    if ($rv == 1) {
        echo '<script> alert("Username Sudah Terdaftar! Input Yang Lain");
        window.location.href="../data_user" </script>';
    } else {
        // Simpan data ke tbl_user
        $query_simpan = mysqli_query($con, "INSERT INTO tbl_user
         (username,
          sandi, 
          peran, 
          nama_panggilan, 
          pin2fa) 
          VALUES
          ('$username',
          '$password',
          '$peran',
          '$nama_panggilan',
          '$pin2fa')
          ") or die (mysqli_error($con));

          echo '<script> alert("Data Berhasil Disimpan");
          window.location.href="../data_user" </script>';
    }
}
?>