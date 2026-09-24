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
  $hal = 'barang';
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
            <h3 class="card-title">Edit Data Barang</h3>
          </div>
          <div class="card-body">
            <?php 
            $kode_brg = @$_GET['kode'];
            
            // Mengambil data barang dari database berdasarkan kode_brg
            $query = mysqli_query($con, "SELECT * FROM tbl_barang WHERE kode_brg = '$kode_brg'") or die(mysqli_error($con));
            $data = mysqli_fetch_array($query);
            ?>
            <form action="ubah.php" method="post" enctype="multipart/form-data">
              <div class="form-group">
                <label for="kode_brg">Kode Barang</label>
                <input type="text" class="form-control" id="kode_brg_display" value="<?= $data['kode_brg']; ?>" disabled>
                <input type="hidden" name="kode_brg" value="<?= $data['kode_brg']; ?>">
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
                <label for="nama_brg">Nama Barang</label>
                <input type="text" name="nama_brg" class="form-control" id="nama_brg" value="<?= $data['nama_brg']; ?>" placeholder="Masukkan Nama Barang" required>
              </div>
              <div class="form-group">
                <label for="merk">Merk</label>
                <input type="text" name="merk" class="form-control" id="merk" value="<?= $data['merk']; ?>" placeholder="Masukkan Merk Barang" required>
              </div>
              <div class="form-group">
                <label for="stok">Stok</label>
                <input type="number" name="stok" class="form-control" id="stok" value="<?= $data['stok']; ?>" placeholder="Masukkan Jumlah Stok" required>
              </div>
              <div class="form-group">
                <label for="rata_harga_beli">Harga Beli Rata-Rata</label>
                <input type="number" name="rata_harga_beli" class="form-control" id="rata_harga_beli" value="<?= $data['rata_harga_beli']; ?>" placeholder="Masukkan Harga Beli" required>
              </div>
              <div class="form-group">
                <label for="harga_jual">Harga Jual</label>
                <input type="number" name="harga_jual" class="form-control" id="harga_jual" value="<?= $data['harga_jual']; ?>" placeholder="Masukkan Harga Jual" required>
              </div>
              <div class="form-group">
                <label for="foto_brg">Foto Barang</label>
                <div class="mb-2">
                  <?php if (!empty($data['foto_brg']) && file_exists('img/' . $data['foto_brg'])) : ?>
                    <img src="img/<?= $data['foto_brg']; ?>" width="100" class="img-thumbnail" alt="Foto Lama">
                  <?php else : ?>
                    <span class="badge bg-secondary">Belum ada foto</span>
                  <?php endif; ?>
                </div>
                <input type="file" name="foto_brg" class="form-control-file" id="foto_brg">
                <small class="form-text text-muted">Biarkan kosong jika tidak ingin mengubah foto.</small>
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