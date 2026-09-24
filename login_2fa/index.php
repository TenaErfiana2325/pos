<?php
// Mencegah notice jika session sudah di-start di koneksi.php
if (session_status() === PHP_SESSION_NONE) {
    session_start(); 
}

require_once '../database/koneksi.php';
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>AdminLTE 3 | Log in</title>

  <!-- Google Font: Source Sans Pro -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="../aset_web/plugins/fontawesome-free/css/all.min.css">
  <!-- icheck bootstrap -->
  <link rel="stylesheet" href="../aset_web/plugins/icheck-bootstrap/icheck-bootstrap.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="../aset_web/dist/css/adminlte.min.css">
</head>
<body class="hold-transition login-page">
<div class="login-box">
  <div class="login-logo">
    <a href="../aset_web/index2.html"><b>Point Of Sales</b></a>
  </div>
  <!-- /.login-logo -->
  <div class="card">
    <div class="card-body login-card-body">

      <form action="" method="post">
        <div class="input-group mb-3">
          <input type="number" name="pin" class="form-control" placeholder="Masukan Pin Anda" required>
          <div class="input-group-append">
            <div class="input-group-text">
              <span class="fas fa-user"></span>
            </div>
         </div>
        </div>
        <div class="social-auth-links text-center mb-3">
        <button type="submit" name="submit" class="btn btn-block btn-primary">
          <i class="fas fa-sign-out-alt mr-2"></i> Submit PIN
      </button>
      </div>
      </form>
      <!-- /.social-auth-links -->
      <?php
      if (isset($_POST['submit'])) {
        $pin            = isset($_SESSION['pin']) ? $_SESSION['pin'] : '';
        $peran          = isset($_SESSION['peran']) ? $_SESSION['peran'] : '';
        $username       = isset($_SESSION['username']) ? $_SESSION['username'] : '';
        $nama_panggilan = isset($_SESSION['nama_panggilan']) ? $_SESSION['nama_panggilan'] : '';

        // Pastikan variabel $con sama dengan variabel di database/koneksi.php
        $pin_inputan_user = trim(mysqli_real_escape_string($con, $_POST['pin']));

        if (strval($pin_inputan_user) === strval($pin) && !empty($pin)) {
    // Memastikan Session Kunci Utama Tetap Tersimpan
    $_SESSION['peran']          = $peran;
    $_SESSION['username']       = $username;
    $_SESSION['nama_panggilan'] = $nama_panggilan;

    if ($peran == 'S') {
        echo '
        <script>
        window.location = "../home_superadmin/";
        </script>
        ';
        exit();
    } elseif($peran == 'K') {
        echo '
        <script>
        window.location = "../home_kasir/";
        </script>
        ';
        exit();
    }
        } else {
            echo '
            <script>
            alert("PIN ANDA SALAH! SILAHKAN LOGIN KEMBALI");
            window.location = "../";
            </script>
            ';
            exit();
        }
      }
      ?>
    </div>
    <!-- /.login-card-body -->
  </div>
</div>
<!-- /.login-box -->

<!-- jQuery -->
<script src="../aset_web/plugins/jquery/jquery.min.js"></script>
<!-- Bootstrap 4 -->
<script src="../aset_web/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<!-- AdminLTE App -->
<script src="../aset_web/dist/js/adminlte.min.js"></script>
</body>
</html>