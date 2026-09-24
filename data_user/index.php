<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once "../database/koneksi.php";

$authority = @$_SESSION['peran'];
if ($authority != 'S') {
  // PERBAIKAN: Mengembalikan blok penanganan cross authority yang hilang
  echo '<script>alert("Akun ini melakukan cross authority, akan segera di logout");</script>';
  echo '<script>window.location.href="../logout.php"</script>';
} else { // PERBAIKAN: Menambahkan 'else {' agar HTML ditampilkan KHUSUS Superadmin ('S')
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <?php 
  include '../css.php';
  $hal ='pengguna';
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
          <a href="#" class="dropdown-item">
            <i class="fas fa-sign-out-alt"></i> logout
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
                <h3 class="card-title">Data User</h3>
              </div>
              <div class="card-body">
                <button type="button" class="btn btn-primary mb-2" data-toggle="modal" data-target="#modal-tambah"> <i class="fas fa-plus"></i><b> Tambah Data</b></button>

                <?php $pengguna = $_SESSION['username'] ?? ''; ?>
                <table id="example1" class="table table-bordered table-striped">
                  <thead>
                  <tr>
                    <th width="5%">No</th>
                    <th>Username</th>
                    <th>Nama Pengguna</th>
                    <th>Peran</th>
                    <th width="15%">Aksi</th>
                  </tr>
                  </thead>
                  <tbody>
                  <?php 
                  $panggil_data_user = mysqli_query($con, "SELECT * FROM tbl_user") or die(mysqli_error($con));
                  
                  $no = 1;
                  $rv = mysqli_num_rows($panggil_data_user);
                  if ($rv > 0) {
                    while ($data = mysqli_fetch_array($panggil_data_user)) {
                      $user = $data['username'];
                      $nama = $data['nama_panggilan'];
                      $peran = $data['peran'];
                      ?>
                      <tr>
                        <td><?= $no++ ?></td>
                        <td><?= $user ?></td>
                        <td><?= $nama ?></td>
                        <td>
                          <?php 
                          $role = $data['peran'];
                          if ($role == 'S') {
                            echo 'Superadmin';
                          } elseif ($role == 'K') {
                            echo 'Kasir';
                          }
                          ?>
                        </td>
                        <td>
                          <a href="hapus.php?user=<?= urlencode($data['username']); ?>" 
                            class="btn btn-danger btn-sm" 
                            onclick="return confirm('Yakin ingin menghapus data ini?')">
                            <i class="fas fa-trash"></i>
                          </a>
                          <a href="edit.php?user=<?= urlencode($data['username']); ?>&nama=<?= urlencode($data['nama_panggilan'] ?? $data['username']); ?>" 
                            class="btn btn-primary btn-sm">
                            <i class="fas fa-edit"></i>
                          </a>
                        </td>
                      </tr>
                      <?php
                    }
                  } else {
                    echo '<tr><td colspan="5" class="text-center">Data Tidak Ditemukan</td></tr>';
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
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title">Tambah Data Pengguna</h4>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <form action="tambah.php" method="post">
          <div class="modal-body">
            <div class="form-group">
              <label for="username">Username</label>
              <input type="text" name="username" class="form-control" id="username" placeholder="Masukan Username" required>
            </div>
            <div class="form-group">
              <label for="nama_panggilan">Nama</label>
              <input type="text" name="nama_panggilan" class="form-control" id="nama_panggilan" placeholder="Masukan Nama panggilan" required>
            </div>
            <div class="form-group">
              <label>Peran</label>
              <select class="form-control" name="peran" required>
                <option value="">-- Pilih Peran --</option>
                <option value="S">Superadmin</option>
                <option value="K">Kasir</option>
              </select>
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