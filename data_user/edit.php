<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once '../database/koneksi.php';

$authority = @$_SESSION['peran'];
if ($authority != 'S') {
    echo '<script>alert("Akun ini melakukan cross authority, akan segera di logout");</script>';
    echo '<script>window.location.href="../logout.php"</script>';
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <?php 
  include '../css.php';
  $hal = 'pengguna';
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
          <i class="far fa-user"></i>
        </a>
        <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
          <div class="dropdown-divider"></div>
          <a href="#" class="dropdown-item">
            <i class="fas fa-user"></i> Profile
          </a>
          <a href="../logout.php" class="dropdown-item">
            <i class="fas fa-sign-out-alt"></i> Logout
          </a>
        </div>
      </li>
    </ul>
  </nav>

  <!-- Main Sidebar Container -->
  <aside class="main-sidebar sidebar-dark-primary elevation-4">
    <div class="sidebar">
      <div class="user-panel mt-3 pb-3 mb-3 d-flex">
        <div class="info">
          <a href="#" class="d-block">POS</a>
        </div>
      </div>
      <?php include '../sidebar_admin.php'; ?>
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
        <div class="card">
          <div class="card-header">
            <h3 class="card-title">Edit Data Pengguna</h3>
          </div>
          <div class="card-body">
            <?php 
            $pengguna = @$_GET['user'];
            
            // Mengambil data pengguna dari database berdasarkan username
            $query = mysqli_query($con, "SELECT * FROM tbl_user WHERE username = '$pengguna'") or die(mysqli_error($con));
            $data = mysqli_fetch_array($query);

            $nama = $data['nama_panggilan'] ?? @$_GET['nama'];
            $peran = $data['peran'] ?? @$_GET['peran'];
            $pin2fa = $data['pin2fa'] ?? '';
            ?>
            <form action="ubah.php" method="post">
              <div class="form-group">
                <label for="username">Username</label>
                <input type="text" class="form-control" id="username_display" value="<?= $pengguna; ?>" disabled>
                <input type="hidden" name="username" value="<?= $pengguna; ?>">
              </div>
              <div class="form-group">
                <label for="nama_panggilan">Nama Panggilan</label>
                <input type="text" name="nama_panggilan" class="form-control" id="nama_panggilan" value="<?= $nama; ?>" placeholder="Masukkan Nama Panggilan" required>
              </div>
              <div class="form-group">
                <label>Peran</label>
                <select class="form-control" name="peran" required>
                  <option value="">-- Pilih Peran --</option>
                  <option value="S" <?= ($peran == 'S') ? 'selected' : ''; ?>>Superadmin</option>
                  <option value="K" <?= ($peran == 'K') ? 'selected' : ''; ?>>Kasir</option>
                </select>
              </div>
              <div class="form-group">
                <label for="pin2fa">PIN 2FA</label>
                <input type="text" name="pin2fa" class="form-control" id="pin2fa" value="<?= $pin2fa; ?>" placeholder="Masukkan 4 Digit PIN 2FA" maxlength="6" required>
              </div>

              <div class="modal-footer justify-content-between px-0">
                <a href="index.php" class="btn btn-default">Batal</a>
                <button type="submit" name="btn_edit" class="btn btn-primary">Simpan Perubahan</button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Main Footer -->
  <?php include '../footer.php'; ?>
</div>

<!-- REQUIRED SCRIPTS -->
<?php include '../script.php'; ?>
</body>
</html>