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
  $hal = 'supplier';
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
            <h3 class="card-title">Edit Data Supplier</h3>
          </div>
          <div class="card-body">
            <?php 
            $kode = @$_GET['kode'];
            
            // Mengambil data supplier berdasarkan kode_supplier
            $query = mysqli_query($con, "SELECT * FROM tbl_supplier WHERE kode_supplier = '$kode'") or die(mysqli_error($con));
            $data = mysqli_fetch_array($query);
            ?>
            <form action="ubah.php" method="post">
              <div class="form-group">
                <label for="kode_supplier">Kode Supplier</label>
                <input type="text" class="form-control" id="kode_display" value="<?= $kode; ?>" disabled>
                <input type="hidden" name="kode_supplier" value="<?= $kode; ?>">
              </div>
              <div class="form-group">
                <label for="nama_supplier">Nama Supplier</label>
                <input type="text" name="nama_supplier" class="form-control" id="nama_supplier" value="<?= $data['nama_supplier'] ?? ''; ?>" placeholder="Masukkan Nama Supplier" required>
              </div>
              <div class="form-group">
                <label for="nama_pic">Nama PIC</label>
                <input type="text" name="nama_pic" class="form-control" id="nama_pic" value="<?= $data['nama_pic'] ?? ''; ?>" placeholder="Masukkan Nama PIC" required>
              </div>
              <div class="form-group">
                <label for="kontak_pic">Kontak PIC</label>
                <input type="text" name="kontak_pic" class="form-control" id="kontak_pic" value="<?= $data['kontak_pic'] ?? ''; ?>" placeholder="Masukkan Nomor Kontak PIC" required>
              </div>
              <div class="form-group">
                <label for="alamat_supplier">Alamat Supplier</label>
                <textarea name="alamat_supplier" class="form-control" id="alamat_supplier" rows="2" placeholder="Masukkan Alamat Supplier"><?= $data['alamat_supplier'] ?? ''; ?></textarea>
              </div>
              <div class="form-group">
                <label for="website">Website</label>
                <input type="text" name="website" class="form-control" id="website" value="<?= $data['website'] ?? ''; ?>" placeholder="Masukkan Website (Opsional)">
              </div>
              <div class="form-group">
                <label for="akun_ig">Akun Instagram</label>
                <input type="text" name="akun_ig" class="form-control" id="akun_ig" value="<?= $data['akun_ig'] ?? ''; ?>" placeholder="Masukkan Akun Instagram (Opsional)">
              </div>
              <div class="form-group">
                <label for="akun_tiktok">Akun TikTok</label>
                <input type="text" name="akun_tiktok" class="form-control" id="akun_tiktok" value="<?= $data['akun_tiktok'] ?? ''; ?>" placeholder="Masukkan Akun TikTok (Opsional)">
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