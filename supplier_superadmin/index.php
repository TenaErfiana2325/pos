<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once "../database/koneksi.php";

$authority = @$_SESSION['peran'];
if ($authority != 'S') {
  echo '<script>alert("Akun ini melakukan cross authority, akan segera di logout");</script>';
  echo '<script>window.location.href="../logout.php"</script>';
} else {
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <?php 
  include '../css.php';
  $hal ='supplier';
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
                <h3 class="card-title">Data Supplier</h3>
              </div>
              <div class="card-body">
                <button type="button" class="btn btn-primary mb-2" data-toggle="modal" data-target="#modal-tambah"> <i class="fas fa-plus"></i><b> Tambah Data</b></button>

                <table id="example1" class="table table-bordered table-striped">
                  <thead>
                  <tr>
                    <th width="5%">No</th>
                    <th>Kode</th>
                    <th>Nama Supplier</th>
                    <th>Nama PIC</th>
                    <th>Kontak PIC</th>
                    <th>Alamat</th>
                    <th>Media Sosial / Web</th>
                    <th width="15%">Aksi</th>
                  </tr>
                  </thead>
                  <tbody>
                  <?php 
                  $panggil_data_supplier = mysqli_query($con, "SELECT * FROM tbl_supplier") or die(mysqli_error($con));
                  
                  $no = 1;
                  $rv = mysqli_num_rows($panggil_data_supplier);
                  if ($rv > 0) {
                    while ($data = mysqli_fetch_array($panggil_data_supplier)) {
                      ?>
                      <tr>
                        <td><?= $no++ ?></td>
                        <td><?= $data['kode_supplier'] ?></td>
                        <td><?= $data['nama_supplier'] ?></td>
                        <td><?= $data['nama_pic'] ?></td>
                        <td><?= $data['kontak_pic'] ?></td>
                        <td><?= $data['alamat_supplier'] ?></td>
                        <td>
                          <small>
                            <b>Web:</b> <?= $data['website'] ?: '-' ?><br>
                            <b>IG:</b> <?= $data['akun_ig'] ?: '-' ?><br>
                            <b>TikTok:</b> <?= $data['akun_tiktok'] ?: '-' ?>
                          </small>
                        </td>
                        <td>
                          <a href="hapus.php?kode=<?= urlencode($data['kode_supplier']); ?>" 
                            class="btn btn-danger btn-sm" 
                            onclick="return confirm('Yakin ingin menghapus data supplier ini?')">
                            <i class="fas fa-trash"></i> Hapus
                          </a>
                          <a href="edit.php?kode=<?= urlencode($data['kode_supplier']); ?>" 
                            class="btn btn-primary btn-sm">
                            <i class="fas fa-edit"></i> Edit
                          </a>
                        </td>
                      </tr>
                      <?php
                    }
                  } else {
                    echo '<tr><td colspan="8" class="text-center">Data Tidak Ditemukan</td></tr>';
                  }
                  ?>
                  </tbody>
                </table>
              </div>
            </div>
      </div>
    </div>
  </div>

  <!-- Control Sidebar -->
  <aside class="control-sidebar control-sidebar-dark"></aside>

  <!-- Modal Tambah Data -->
  <div class="modal fade" id="modal-tambah">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title">Tambah Data Supplier</h4>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <form action="tambah.php" method="post">
          <div class="modal-body">
            <div class="form-group">
              <label for="kode_supplier">Kode Supplier</label>
              <input type="text" name="kode_supplier" class="form-control" id="kode_supplier" placeholder="Masukan Kode Supplier" required>
            </div>
            <div class="form-group">
              <label for="nama_supplier">Nama Supplier</label>
              <input type="text" name="nama_supplier" class="form-control" id="nama_supplier" placeholder="Masukan Nama Supplier" required>
            </div>
            <div class="form-group">
              <label for="nama_pic">Nama PIC</label>
              <input type="text" name="nama_pic" class="form-control" id="nama_pic" placeholder="Masukan Nama PIC" required>
            </div>
            <div class="form-group">
              <label for="kontak_pic">Kontak PIC</label>
              <input type="text" name="kontak_pic" class="form-control" id="kontak_pic" placeholder="Masukan Nomor Kontak/HP PIC" required>
            </div>
            <div class="form-group">
              <label for="alamat_supplier">Alamat Supplier</label>
              <textarea name="alamat_supplier" class="form-control" id="alamat_supplier" rows="2" placeholder="Masukan Alamat Supplier"></textarea>
            </div>
            <div class="form-group">
              <label for="website">Website</label>
              <input type="text" name="website" class="form-control" id="website" placeholder="Masukan Website (Opsional)">
            </div>
            <div class="form-group">
              <label for="akun_ig">Akun Instagram</label>
              <input type="text" name="akun_ig" class="form-control" id="akun_ig" placeholder="Masukan Akun IG (Opsional)">
            </div>
            <div class="form-group">
              <label for="akun_tiktok">Akun TikTok</label>
              <input type="text" name="akun_tiktok" class="form-control" id="akun_tiktok" placeholder="Masukan Akun TikTok (Opsional)">
            </div>
          </div>
          <div class="modal-footer justify-content-between">
            <button type="button" class="btn btn-default" data-dismiss="modal">Tutup</button>
            <button type="submit" name="tambah" class="btn btn-primary">Tambah</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- Main Footer -->
  <?php include '../footer.php' ?>
</div>

<!-- REQUIRED SCRIPTS -->
<?php include '../script.php' ?>
</body>
</html>
<?php
}
?>