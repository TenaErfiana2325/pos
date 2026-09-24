<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once "../database/koneksi.php";
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <?php
  $hal = 'ganti_password';
  include '../css.php';
  ?>
</head>

<body class="hold-transition sidebar-mini">
<div class="wrapper">
  <!-- Navbar -->
  <nav class="main-header navbar navbar-expand navbar-white navbar-light">
    <ul class="navbar-nav">
      <li class="nav-item">
        <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
      </li>
    </ul>

    <ul class="navbar-nav ml-auto">
      <li class="nav-item dropdown">
        <a class="nav-link" data-toggle="dropdown" href="#">
          <?= isset($_SESSION['nama_panggilan']) ? $_SESSION['nama_panggilan'] : $_SESSION['username']; ?> - [<?=$_SESSION['peran']; ?>] <i class="far fa-user"></i> 
        </a>
        <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
          <div class="dropdown-divider"></div>
          <a href="../logout.php" class="dropdown-item">
            <i class="fas fa-sign-out-alt mr-2"></i> Logout
          </a>
        </div>
      </li>
    </ul>
  </nav>

  <!-- Sidebar Container -->
  <aside class="main-sidebar sidebar-dark-primary elevation-4">
    <div class="sidebar">
      <div class="user-panel mt-3 pb-3 mb-3 d-flex">
        <div class="info">
          <a href="#" class="d-block">Point Of Sales</a>
        </div>
      </div>
      <?php include '../sidebar_kasir.php'; ?>
    </div>
  </aside>

  <!-- Content Wrapper -->
  <div class="content-wrapper">
    <div class="content-header">
      <div class="container-fluid"></div>
    </div>

    <!-- Main content -->
    <div class="content">
      <div class="container-fluid">
        <div class="row">
          <div class="col-lg-5">
            <div class="card card-primary">
              <div class="card-header">
                <h3 class="card-title"><i class="fas fa-lock mr-2"></i>Ganti Password</h3>
              </div>
              <div class="card-body">
                <form action="" method="post"> 
                  <div class="form-group">
                    <label for="password_lama">Password Lama</label>
                    <?php $pengguna = @$_SESSION['username']; ?>
                    <input type="hidden" value="<?= $pengguna; ?>" name="pengguna">
                    <input type="password" name="password_lama" class="form-control" placeholder="Input password lama" required>
                  </div>
                  <div class="form-group">
                    <label for="password_baru">Password Baru</label>
                    <input type="password" name="password_baru" class="form-control" maxlength="10" placeholder="Input password baru (Max 10 Char)" required>
                  </div>
                  <div class="form-group">
                    <label for="pin">PIN</label>
                    <!-- Perbaikan: name="pin" -->
                    <input type="number" name="pin" class="form-control" placeholder="Input PIN Anda" required>
                  </div>
                  <div class="form-group">
                    <button type="submit" name="edit_pw" class="btn btn-primary btn-block"><i class="fas fa-edit mr-1"></i> Edit Password</button>
                  </div>
                </form>

                <?php
                if (isset($_POST['edit_pw'])) {
                    $pengguna           = trim(mysqli_real_escape_string($con, $_POST['pengguna'])); 
                    $password_lama_user = trim(mysqli_real_escape_string($con, $_POST['password_lama']));
                    $password_baru_user = trim(mysqli_real_escape_string($con, $_POST['password_baru']));
                    $pin_user           = trim(mysqli_real_escape_string($con, $_POST['pin']));

                    // Ambil data sandi dan pin dari database
                    $query_pengguna = mysqli_query($con, "SELECT sandi, pin2fa FROM tbl_user WHERE username = '$pengguna'") or die(mysqli_error($con)); 
                    $arr            = mysqli_fetch_assoc($query_pengguna); 
                    
                    $sandi_db = $arr['sandi']; 
                    $pin_db   = $arr['pin2fa']; // Menggunakan kolom pin2fa sesuai tabel kamu

                    // Pengecekan Kategori (Gunakan sha1 jika di database ter-enkripsi, atau samakan teks biasa)
                    if ($password_lama_user == $sandi_db && $pin_user == $pin_db) {
                        $query_update = mysqli_query($con, "UPDATE tbl_user SET sandi = '$password_baru_user' WHERE username = '$pengguna'") or die(mysqli_error($con)); 
                        echo '<script>alert("Password Berhasil Diupdate!"); window.location.href="index.php";</script>';
                    } else { 
                        echo '<script>alert("Password Lama atau PIN Salah!");</script>';
                    }    
                }
                ?>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <?php include '../footer.php'; ?>
</div>

<?php include '../script.php'; ?>
</body>
</html>