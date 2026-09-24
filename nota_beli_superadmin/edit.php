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
  $hal = 'nota_beli';
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
            <h3 class="card-title">Edit Data Nota Beli</h3>
          </div>
          <div class="card-body">
            <?php 
            $kode_nota = @$_GET['kode'];
            
            // Mengambil data nota dari database berdasarkan kode_nota
            $query = mysqli_query($con, "SELECT * FROM tbl_notabeli WHERE kode_nota = '$kode_nota'") or die(mysqli_error($con));
            $data = mysqli_fetch_array($query);
            ?>
            <form action="ubah.php" method="post" enctype="multipart/form-data">
              <div class="form-group">
                <label for="kode_nota">Kode Nota</label>
                <input type="text" class="form-control" id="kode_nota_display" value="<?= $data['kode_nota']; ?>" disabled>
                <input type="hidden" name="kode_nota" value="<?= $data['kode_nota']; ?>">
              </div>
              <div class="form-group">
                <label for="kode_supplier">Supplier</label>
                <select name="kode_supplier" class="form-control" id="kode_supplier" required>
                  <option value="">-- Pilih Supplier --</option>
                  <?php 
                  $sql_supplier = mysqli_query($con, "SELECT * FROM tbl_supplier") or die(mysqli_error($con));
                  while ($sup = mysqli_fetch_array($sql_supplier)) {
                    $selected = ($sup['kode_supplier'] == $data['kode_supplier']) ? 'selected' : '';
                    echo '<option value="'.$sup['kode_supplier'].'" '.$selected.'>'.$sup['kode_supplier'].' - '.$sup['nama_supplier'].'</option>';
                  }
                  ?>
                </select>
              </div>
              <div class="form-group">
                <label for="tgl_pembelian">Tanggal Pembelian</label>
                <input type="date" name="tgl_pembelian" class="form-control" id="tgl_pembelian" value="<?= $data['tgl_pembelian']; ?>" required>
              </div>
              <div class="form-group">
                <label for="total_pembelian">Total Pembelian</label>
                <input type="number" name="total_pembelian" class="form-control" id="total_pembelian" value="<?= $data['total_pembelian']; ?>" placeholder="Masukkan Total Pembelian" required>
              </div>
              <div class="form-group">
                <label for="status">Status</label>
                <select name="status" class="form-control" id="status" required>
                  <option value="">-- Pilih Status --</option>
                  <option value="L" <?= ($data['status'] == 'L') ? 'selected' : ''; ?>>Lunas</option>
                  <option value="2" <?= ($data['status'] == '2') ? 'selected' : ''; ?>>Dibayar 25%</option>
                  <option value="3" <?= ($data['status'] == '3') ? 'selected' : ''; ?>>Dibayar 50%</option>
                  <option value="4" <?= ($data['status'] == '4') ? 'selected' : ''; ?>>Dibayar 75%</option>
                </select>
              </div>
              <div class="form-group">
                <label for="keterangan">Keterangan</label>
                <textarea name="keterangan" class="form-control" id="keterangan" placeholder="Masukkan Keterangan"><?= $data['keterangan']; ?></textarea>
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
<?php
?>