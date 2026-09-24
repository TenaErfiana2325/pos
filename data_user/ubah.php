<?php
require_once '../database/koneksi.php';

if (isset($_POST['btn_edit'])) {

    $username       = trim(mysqli_real_escape_string($con, $_POST['username']));
    $nama_panggilan = trim(mysqli_real_escape_string($con, $_POST['nama_panggilan']));
    $peran          = trim(mysqli_real_escape_string($con, $_POST['peran']));
    $pin2fa         = trim(mysqli_real_escape_string($con, $_POST['pin2fa']));

    // Memperbarui data ke tabel tbl_user
    $query_edit = mysqli_query($con, "UPDATE tbl_user SET
        nama_panggilan = '$nama_panggilan',
        peran = '$peran',
        pin2fa = '$pin2fa'
        WHERE username = '$username'
    ") or die(mysqli_error($con));

    // Mengarahkan kembali ke folder data_user
    echo '<script> alert ("Data Berhasil Diedit");
    window.location.href = "../data_user"</script>';
    
}
?>