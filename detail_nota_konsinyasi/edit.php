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
  $hal = 'beranda_detail_nota_konsinyasi';
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
            <h3 class="card-title">Edit Detail Nota Konsinyasi</h3>
          </div>
          <div class="card-body">
            <?php 
            $id_detail = @$_GET['id_detail'];
            
            // Mengambil data detail nota konsinyasi berdasarkan id_detail
            $query = mysqli_query($con, "SELECT * FROM tbl_detail_nota_konsinyasi WHERE id_detail = '$id_detail'") or die(mysqli_error($con));
            $data = mysqli_fetch_array($query);
            ?>
            <form action="ubah.php" method="post" enctype="multipart/form-data">
              <input type="hidden" name="id_detail" value="<?= $data['id_detail']; ?>">

              <div class="form-group">
                <label for="kode_nota_konsinyasi">Kode Nota Konsinyasi</label>
                <input type="text" class="form-control" id="kode_nota_display" value="<?= $data['kode_nota_konsinyasi']; ?>" disabled>
                <input type="hidden" name="kode_nota_konsinyasi" value="<?= $data['kode_nota_konsinyasi']; ?>">
              </div>

              <div class="form-group">
                <label for="kode_barang">Pilih Barang</label>
                <select name="kode_barang" class="form-control" id="kode_barang" required>
                  <option value="">-- Pilih Barang --</option>
                  <?php 
                  $sql_barang = mysqli_query($con, "SELECT * FROM tbl_barang") or die(mysqli_error($con));
                  while ($brg = mysqli_fetch_array($sql_barang)) {
                    $selected = ($brg['kode_brg'] == $data['kode_barang']) ? 'selected' : '';
                    echo '<option value="'.$brg['kode_brg'].'" '.$selected.'>'.$brg['kode_brg'].' - '.$brg['nama_brg'].'</option>';
                  }
                  ?>
                </select>
              </div>

              <div class="form-group">
                <label for="jumlah">Jumlah</label>
                <input type="number" name="jumlah" class="form-control" id="jumlah" value="<?= $data['jumlah']; ?>" placeholder="Masukkan Jumlah" required>
              </div>

              <div class="form-group">
                <label for="harga_beli">Harga Beli</label>
                <input type="number" name="harga_beli" class="form-control" id="harga_beli" value="<?= $data['harga_beli']; ?>" placeholder="Masukkan Harga Beli" required>
              </div>

              <div class="form-group">
                <label for="harga_jual">Harga Jual</label>
                <input type="number" name="harga_jual" class="form-control" id="harga_jual" value="<?= $data['harga_jual']; ?>" placeholder="Masukkan Harga Jual" required>
              </div>

              <div class="modal-footer justify-content-between px-0">
                <a href="index.php?kode_nota_konsinyasi=<?= $data['kode_nota_konsinyasi']; ?>" class="btn btn-default">Batal</a>
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