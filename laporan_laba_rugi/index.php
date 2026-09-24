<?php
require_once '../database/koneksi.php';

// Ambil filter tanggal dari parameter URL/Form jika ada
$tgl_mulai   = $_GET['tgl_mulai'] ?? '';
$tgl_selesai = $_GET['tgl_selesai'] ?? '';

$total_penjualan = 0;
$total_pembelian = 0;
$laba_bersih     = 0;

if (!empty($tgl_mulai) && !empty($tgl_selesai)) {
    // 1. Total Penjualan
    $q_jual = mysqli_query($con, "
        SELECT SUM(total_penjualan) AS total_jual 
        FROM tbl_notajual 
        WHERE tgl_penjualan BETWEEN '$tgl_mulai' AND '$tgl_selesai'
    ");
    $d_jual = mysqli_fetch_assoc($q_jual);
    $total_penjualan = $d_jual['total_jual'] ?? 0;

    // 2. Total Pembelian
    $q_beli = mysqli_query($con, "
        SELECT SUM(total_pembelian) AS total_beli 
        FROM tbl_notabeli 
        WHERE tgl_pembelian BETWEEN '$tgl_mulai' AND '$tgl_selesai'
    ");
    $d_beli = mysqli_fetch_assoc($q_beli);
    $total_pembelian = $d_beli['total_beli'] ?? 0;

    // 3. Hitung Laba Bersih
    $laba_bersih = $total_penjualan - $total_pembelian;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Laporan Laba Rugi</title>
  <?php 
  include '../css.php';
  $hal = 'lap_laba_rugi';
  ?>
</head>
<body class="hold-transition sidebar-mini layout-fixed">
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
          <?= $_SESSION['nama_panggilan'] ?? ''; ?> <?= $_SESSION['peran'] ?? ''; ?>
          <i class="far fa-user"></i>
        </a>
        <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
          <div class="dropdown-divider"></div>
          <a href="#" class="dropdown-item"><i class="fas fa-user"></i> Profile</a>
          <a href="../logout.php" class="dropdown-item"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </div>
      </li>
    </ul>
  </nav>

  <!-- Main Sidebar Container -->
  <aside class="main-sidebar sidebar-dark-primary elevation-4">
    <div class="sidebar">
      <div class="user-panel mt-3 pb-3 mb-3 d-flex">
        <div class="info">
          <a href="#" class="d-block">Sistem Manajemen</a>
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

    <div class="content">
      <div class="container-fluid">

        <div class="card card-outline card-primary">
          <div class="card-header">
            <h3 class="card-title">Laporan Laba Rugi</h3>
          </div>

          <div class="card-body">

            <!-- Form Filter Tanggal -->
            <form method="GET" action="" class="mb-4">
              <div class="form-row align-items-center">
                <div class="col-auto">
                  <input type="date" name="tgl_mulai" class="form-control" value="<?= htmlspecialchars($tgl_mulai) ?>" required>
                </div>
                <div class="col-auto">
                  <span>&mdash;</span>
                </div>
                <div class="col-auto">
                  <input type="date" name="tgl_selesai" class="form-control" value="<?= htmlspecialchars($tgl_selesai) ?>" required>
                </div>
                <div class="col-auto">
                  <button type="submit" class="btn btn-primary">
                    <i class="fas fa-filter"></i> Filter
                  </button>

                  <a href="index.php" class="btn btn-secondary">
                    <i class="fas fa-sync-alt"></i> Reset
                  </a>

                  <a href="pdf.php?tgl_mulai=<?= urlencode($tgl_mulai); ?>&tgl_selesai=<?= urlencode($tgl_selesai); ?>" 
                     target="_blank" 
                     class="btn btn-danger ml-1">
                    <i class="fas fa-file-pdf"></i> Expor pdf
                  </a>
                </div>
              </div>
            </form>

            <?php if (!empty($tgl_mulai) && !empty($tgl_selesai)): ?>
              <!-- Ringkasan Kartu -->
              <div class="row">
                <div class="col-md-4">
                  <div class="info-box bg-success">
                    <span class="info-box-icon"><i class="fas fa-shopping-cart"></i></span>
                    <div class="info-box-content">
                      <span class="info-box-text">Total Penjualan</span>
                      <span class="info-box-number">Rp <?= number_format($total_penjualan, 0, ',', '.'); ?></span>
                    </div>
                  </div>
                </div>

                <div class="col-md-4">
                  <div class="info-box bg-warning">
                    <span class="info-box-icon"><i class="fas fa-truck"></i></span>
                    <div class="info-box-content">
                      <span class="info-box-text">Total Pembelian</span>
                      <span class="info-box-number">Rp <?= number_format($total_pembelian, 0, ',', '.'); ?></span>
                    </div>
                  </div>
                </div>

                <div class="col-md-4">
                  <div class="info-box <?= ($laba_bersih >= 0) ? 'bg-info' : 'bg-danger'; ?>">
                    <span class="info-box-icon"><i class="fas fa-chart-line"></i></span>
                    <div class="info-box-content">
                      <span class="info-box-text"><?= ($laba_bersih >= 0) ? 'Laba Bersih' : 'Rugi Bersih'; ?></span>
                      <span class="info-box-number">Rp <?= number_format(abs($laba_bersih), 0, ',', '.'); ?></span>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Tabel Detail -->
              <div class="table-responsive mt-3">
                <table class="table table-bordered text-center">
                  <thead class="thead-light">
                    <tr>
                      <th>Keterangan</th>
                      <th class="text-right">Jumlah</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr>
                      <td class="text-left">Total Pendapatan Penjualan</td>
                      <td class="text-right text-success">Rp <?= number_format($total_penjualan, 0, ',', '.'); ?></td>
                    </tr>
                    <tr>
                      <td class="text-left">Total Pengeluaran Pembelian (HPP)</td>
                      <td class="text-right text-danger">(Rp <?= number_format($total_pembelian, 0, ',', '.'); ?>)</td>
                    </tr>
                    <tr class="<?= ($laba_bersih >= 0) ? 'table-success' : 'table-danger'; ?>">
                      <th class="text-left"><?= ($laba_bersih >= 0) ? 'TOTAL LABA BERSIH' : 'TOTAL RUGI BERSIH'; ?></th>
                      <th class="text-right">Rp <?= number_format($laba_bersih, 0, ',', '.'); ?></th>
                    </tr>
                  </tbody>
                </table>
              </div>

            <?php else: ?>
              <div class="alert alert-info text-center my-4">
                <i class="fas fa-info-circle mr-1"></i> Silakan pilih rentang tanggal di atas untuk menghitung Laba Rugi.
              </div>
            <?php endif; ?>

          </div>
        </div>

      </div>
    </div>
  </div>

  <?php include '../footer.php' ?>
</div>

<?php include '../script.php' ?>
</body>
</html>