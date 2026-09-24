<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once 'database/koneksi.php';
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
  <link rel="stylesheet" href="aset_web/plugins/fontawesome-free/css/all.min.css">
  <!-- icheck bootstrap -->
  <link rel="stylesheet" href="aset_web/plugins/icheck-bootstrap/icheck-bootstrap.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="aset_web/dist/css/adminlte.min.css">
</head>
<body class="hold-transition login-page">
<div class="login-box">
  <div class="login-logo">
    <a href="aset_web/index2.html"><b>Point Of Sales</b></a>
  </div>
  <!-- /.login-logo -->
  <div class="card">
    <div class="card-body login-card-body">

      <form action="" method="post">
        <div class="input-group mb-3">
          <input type="text" name="user" class="form-control" placeholder="Username / No HP" required>
          <div class="input-group-append">
            <div class="input-group-text">
              <span class="fas fa-user"></span>
            </div>
          </div>
        </div>
        <div class="input-group mb-3">
          <input type="password" name="sandi" class="form-control" placeholder="Password" required>
          <div class="input-group-append">
            <div class="input-group-text">
              <span class="fas fa-lock"></span>
            </div>
          </div>
        </div>
        <div class="social-auth-links text-center mb-3">
          <button type="submit" name="login" class="btn btn-block btn-primary">
            <i class="fas fa-sign-out-alt mr-2"></i> Login Akun
          </button>
        </div>
      </form>

      <!-- /.social-auth-links -->
      <?php
      if (isset($_POST['login'])) {
        $pengguna = trim(mysqli_real_escape_string($con, $_POST['user']));
        $sandi    = trim(mysqli_real_escape_string($con, $_POST['sandi']));

        // Query menyesuaikan tabel 'user' (atau 'tbl_user' sesuai database kamu)
        $query_cek = mysqli_query($con, "SELECT * FROM tbl_user WHERE username='$pengguna' AND sandi='$sandi'") or die(mysqli_error($con));
        $rv        = mysqli_num_rows($query_cek);

        if ($rv == 1) {
          $data = mysqli_fetch_assoc($query_cek);

          $peran          = $data['peran'];
          $pin            = $data['pin2fa']; // Kolom pin2fa tanpa garis bawah
          $nama_panggilan = $data['nama_panggilan'];

          $_SESSION['peran']          = $peran;
          $_SESSION['pin']            = $pin; 
          $_SESSION['username']       = $pengguna;
          $_SESSION['nama_panggilan'] = $nama_panggilan;

          // Diarahkan ke folder 'login_2fa/' yang memakai garis bawah
          echo '
          <script>
          window.location = "login_2fa/";
          </script>';
          exit();
        } else {
          echo '
          <script>
          alert("Username atau Sandi Salah!");
          </script>';
        }
      }
      ?>
    </div>
    <!-- /.login-card-body -->
  </div>
</div>
<!-- /.login-box -->

<!-- jQuery -->
<script src="aset_web/plugins/jquery/jquery.min.js"></script>
<!-- Bootstrap 4 -->
<script src="aset_web/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<!-- AdminLTE App -->
<script src="aset_web/dist/js/adminlte.min.js"></script>
</body>
</html>