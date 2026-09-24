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
  $hal ='barang_konsinyasi';
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
                <h3 class="card-title">Data Barang Konsinyasi</h3>
              </div>
              <div class="card-body">
                <button type="button" class="btn btn-primary mb-2" data-toggle="modal" data-target="#modal-tambah"> <i class="fas fa-plus"></i><b> Tambah Data</b></button>

                <table id="example1" class="table table-bordered table-striped">
                  <thead>
                  <tr>
                    <th width="5%">No</th>
                    <th>Kode Barang</th>
                    <th>Kode Supplier</th>
                    <th>Nama Barang</th>
                    <th>Merk</th>
                    <th>Stok</th>
                    <th>Harga Beli</th>
                    <th>Harga Jual</th>
                    <th>Barcode</th>
                    <th>Foto Barang</th>
                    <th width="15%">Aksi</th>
                  </tr>
                  </thead>
                  <tbody>
                  <?php 
                  $panggil_data_barang = mysqli_query($con, "SELECT * FROM tbl_konsinyasi") or die(mysqli_error($con));
                  
                  $no = 1;
                  $rv = mysqli_num_rows($panggil_data_barang);
                  if ($rv > 0) {
                    while ($data = mysqli_fetch_array($panggil_data_barang)) {
                      ?>
                      <tr>
                        <td><?= $no++ ?></td>
                        <td><?= $data['kode_barang'] ?></td>
                        <td><?= $data['kode_supplier'] ?></td>
                        <td><?= $data['nama_barang'] ?></td>
                        <td><?= $data['merk'] ?></td>
                        <td><?= $data['stok'] ?></td>
                        <td>Rp <?= number_format($data['harga_beli'], 0, ',', '.') ?></td>
                        <td>Rp <?= number_format($data['harga_jual'], 0, ',', '.') ?></td>
                        <td>
                          <svg class="barcode" 
                            jsbarcode-value="<?= !empty($data['barcode']) ? $data['barcode'] : $data['kode_barang']; ?>"
                            jsbarcode-height="30"
                            jsbarcode-fontsize="12">
                          </svg>
                        </td>
                        <td>
                        <?php if (!empty($data['foto']) && file_exists('img/' . $data['foto'])) : ?>
                          <img src="img/<?= $data['foto']; ?>" width="70" class="img-thumbnail" alt="<?= $data['nama_barang']; ?>">
                        <?php else : ?>
                          <span class="badge bg-secondary">Tidak ada foto</span>
                        <?php endif; ?>
                      </td>
                        <td>
                          <a href="hapus.php?kode=<?= urlencode($data['kode_barang']); ?>" 
                            class="btn btn-danger btn-sm" 
                            onclick="return confirm('Yakin ingin menghapus data barang ini?')">
                            <i class="fas fa-trash"></i>
                          </a>
                          <a href="edit.php?kode=<?= urlencode($data['kode_barang']); ?>" 
                            class="btn btn-primary btn-sm">
                            <i class="fas fa-edit"></i>
                          </a>
                        </td>
                      </tr>
                      <?php
                    }
                  } else {
                    echo '<tr><td colspan="11" class="text-center">Data Tidak Ditemukan</td></tr>';
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
          <h4 class="modal-title">Tambah Data Barang Konsinyasi</h4>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <form action="tambah.php" method="post" enctype="multipart/form-data">
          <div class="modal-body">
            <div class="form-group">
              <label for="kode_barang">Kode Barang</label>
              <input type="text" name="kode_barang" class="form-control" id="kode_barang" placeholder="Masukkan Kode Barang" required>
            </div>
            <div class="form-group">
              <label for="kode_supplier">Supplier</label>
              <select name="kode_supplier" class="form-control" id="kode_supplier" required>
                <option value="">-- Pilih Supplier --</option>
                <?php 
                $sql_supplier = mysqli_query($con, "SELECT * FROM tbl_supplier") or die(mysqli_error($con));
                while ($sup = mysqli_fetch_array($sql_supplier)) {
                  echo '<option value="'.$sup['kode_supplier'].'">'.$sup['kode_supplier'].' - '.$sup['nama_supplier'].'</option>';
                }
                ?>
              </select>
            </div>
            <div class="form-group">
              <label for="nama_barang">Nama Barang</label>
              <input type="text" name="nama_barang" class="form-control" id="nama_barang" placeholder="Masukkan Nama Barang" required>
            </div>
            <div class="form-group">
              <label for="merk">Merk</label>
              <input type="text" name="merk" class="form-control" id="merk" placeholder="Masukkan Merk Barang">
            </div>
            <div class="form-group">
              <label for="stok">Stok</label>
              <input type="number" name="stok" class="form-control" id="stok" placeholder="Masukkan Jumlah Stok" required>
            </div>
            <div class="form-group">
              <label for="harga_beli">Harga Beli (Setor Supplier)</label>
              <input type="number" name="harga_beli" class="form-control" id="harga_beli" placeholder="Masukkan Harga Beli" required>
            </div>
            <div class="form-group">
              <label for="harga_jual">Harga Jual</label>
              <input type="number" name="harga_jual" class="form-control" id="harga_jual" placeholder="Masukkan Harga Jual" required>
            </div>
            <div class="form-group">
              <label for="barcode">Barcode</label>
              <input type="text" name="barcode" class="form-control" id="barcode" placeholder="Masukkan Kode Barcode (Opsional)">
            </div>
            <div class="form-group">
              <label for="foto">Foto Barang</label>
              <input type="file" name="foto" class="form-control-file" id="foto">
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
<!-- Library JsBarcode -->
<script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.5/dist/JsBarcode.all.min.js"></script>

<!-- Script Inisialisasi Otomatis -->
<script>
  $(document).ready(function() {
    JsBarcode(".barcode").init();
    if ($.fn.DataTable.isDataTable('#example1')) {
      $('#example1').on('draw.dt', function() {
        JsBarcode(".barcode").init();
      });
    }
  });
</script>
</body>
</html>
<?php
}
?>