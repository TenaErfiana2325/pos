<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once "../database/koneksi.php";

$authority = isset($_SESSION['peran']) ? $_SESSION['peran'] : '';

if ($authority != 'S') {
  echo '<script>alert("Akun ini melakukan cross authority, akan segera di logout");</script>';
  echo '<script>window.location.href="../logout.php";</script>';
  exit();
} else {

  // ==========================================
  // QUERY METRIK & DATA DASHBOARD CINDERENNAMART
  // ==========================================

  // 1. Total Jenis Barang (Reguler + Konsinyasi)
  $q_total_jenis = mysqli_query($con, "
    SELECT 
      (SELECT COUNT(*) FROM tbl_barang) + 
      (SELECT COUNT(*) FROM tbl_konsinyasi) AS total_jenis
  ");
  $d_total_jenis = mysqli_fetch_assoc($q_total_jenis);
  $total_jenis_barang = $d_total_jenis['total_jenis'] ?? 0;

  // 2. Stok Barang Menipis (Stok <= 20)
  $q_stok_menipis_count = mysqli_query($con, "
    SELECT 
      (SELECT COUNT(*) FROM tbl_barang WHERE stok <= 20) + 
      (SELECT COUNT(*) FROM tbl_konsinyasi WHERE stok <= 20) AS total_menipis
  ");
  $d_stok_menipis_count = mysqli_fetch_assoc($q_stok_menipis_count);
  $total_stok_menipis = $d_stok_menipis_count['total_menipis'] ?? 0;

  // 3. Penjualan Hari Ini & Total Transaksi Penjualan Hari Ini
  $tgl_today = date('Y-m-d');
  $q_jual_today = mysqli_query($con, "
    SELECT 
      COALESCE(SUM(total_penjualan), 0) AS omzet_today,
      COUNT(kode_nota) AS jml_transaksi
    FROM tbl_notajual 
    WHERE tgl_penjualan = '$tgl_today'
  ");
  $d_jual_today = mysqli_fetch_assoc($q_jual_today);
  $penjualan_today = $d_jual_today['omzet_today'];
  $transaksi_today = $d_jual_today['jml_transaksi'];

  // 4. Pembelian Hari Ini
  $q_beli_today = mysqli_query($con, "
    SELECT COALESCE(SUM(total_pembelian), 0) AS beli_today 
    FROM tbl_notabeli 
    WHERE tgl_pembelian = '$tgl_today'
  ");
  $d_beli_today = mysqli_fetch_assoc($q_beli_today);
  $pembelian_today = $d_beli_today['beli_today'];

  // 5. Total Akumulasi Keseluruhan (Penjualan & Pembelian)
  $q_tot_jual = mysqli_query($con, "SELECT COALESCE(SUM(total_penjualan), 0) AS total_jual FROM tbl_notajual");
  $d_tot_jual = mysqli_fetch_assoc($q_tot_jual);
  $total_penjualan_all = $d_tot_jual['total_jual'];

  $q_tot_beli = mysqli_query($con, "SELECT COALESCE(SUM(total_pembelian), 0) AS total_beli FROM tbl_notabeli");
  $d_tot_beli = mysqli_fetch_assoc($q_tot_beli);
  $total_pembelian_all = $d_tot_beli['total_beli'];

  // Estimasi Laba Bersih (Tanpa JOIN)
  $q_laba = mysqli_query($con, "
    SELECT 
      COALESCE(SUM((harga_jual - (SELECT rata_harga_beli FROM tbl_barang WHERE kode_brg = tbl_detail_notajual.kode_brg)) * jumlah), 0) AS estimasi_laba
    FROM tbl_detail_notajual
    WHERE kode_brg IN (SELECT kode_brg FROM tbl_barang)
  ");
  $d_laba = mysqli_fetch_assoc($q_laba);
  $total_laba_rugi = $d_laba['estimasi_laba'];

  // 6. Data Barang Terlaris (Top 5) - TANPA JOIN (Otomatis mengeliminasi kode barang yang tidak ada di master)
  $q_terlaris = mysqli_query($con, "
    SELECT 
      master_brg.kode_brg,
      master_brg.nama_brg,
      master_brg.merk,
      (SELECT SUM(jumlah) FROM tbl_detail_notajual WHERE kode_brg = master_brg.kode_brg) AS total_terjual,
      (SELECT SUM(jumlah * harga_jual) FROM tbl_detail_notajual WHERE kode_brg = master_brg.kode_brg) AS total_omzet
    FROM (
      SELECT kode_brg, nama_brg, merk FROM tbl_barang
      UNION ALL
      SELECT kode_barang AS kode_brg, nama_barang AS nama_brg, merk FROM tbl_konsinyasi
    ) AS master_brg
    WHERE (SELECT SUM(jumlah) FROM tbl_detail_notajual WHERE kode_brg = master_brg.kode_brg) > 0
    ORDER BY total_terjual DESC
    LIMIT 5
  ");

  // 7. Data Barang Stok Menipis (Stok <= 20) - TANPA JOIN
  $q_stok_menipis = mysqli_query($con, "
    SELECT kode_brg, nama_brg, merk, stok, 'Reguler' AS tipe FROM tbl_barang WHERE stok <= 20
    UNION ALL
    SELECT kode_barang AS kode_brg, nama_barang AS nama_brg, merk, stok, 'Konsinyasi' AS tipe FROM tbl_konsinyasi WHERE stok <= 20
    ORDER BY stok ASC
    LIMIT 5
  ");

  // 8. Data Grafik Kurva 7 Hari Terakhir
  $chart_labels = [];
  $chart_jual = [];
  $chart_beli = [];

  for ($i = 6; $i >= 0; $i--) {
      $tgl = date('Y-m-d', strtotime("-$i days"));
      $label_tgl = date('d M', strtotime($tgl));
      
      $q_jual_tgl = mysqli_query($con, "SELECT COALESCE(SUM(total_penjualan), 0) AS total FROM tbl_notajual WHERE tgl_penjualan = '$tgl'");
      $d_jual_tgl = mysqli_fetch_assoc($q_jual_tgl);
      
      $q_beli_tgl = mysqli_query($con, "SELECT COALESCE(SUM(total_pembelian), 0) AS total FROM tbl_notabeli WHERE tgl_pembelian = '$tgl'");
      $d_beli_tgl = mysqli_fetch_assoc($q_beli_tgl);

      $chart_labels[] = $label_tgl;
      $chart_jual[] = (int)$d_jual_tgl['total'];
      $chart_beli[] = (int)$d_beli_tgl['total'];
  }

  // 9. Data Metode Pembayaran
  $q_pay_method = mysqli_query($con, "
    SELECT jenis_pembayaran, COUNT(*) as total 
    FROM tbl_notajual 
    WHERE jenis_pembayaran IS NOT NULL AND jenis_pembayaran != ''
    GROUP BY jenis_pembayaran
  ");
  $pay_labels = [];
  $pay_counts = [];
  while($r_pay = mysqli_fetch_assoc($q_pay_method)) {
      $pay_labels[] = strtoupper($r_pay['jenis_pembayaran']);
      $pay_counts[] = $r_pay['total'];
  }
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Beranda Admin - Cinderenna Mart POS</title>
  <?php
  $hal = 'admin';
  include '../css.php';
  ?>
  <!-- Chart.js CDN -->
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body class="hold-transition sidebar-mini layout-fixed">
<div class="wrapper">
  <!-- Navbar -->
  <nav class="main-header navbar navbar-expand navbar-white navbar-light">
    <!-- Left navbar links -->
    <ul class="navbar-nav">
      <li class="nav-item">
        <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
      </li>
    </ul>

    <!-- Right navbar links -->
    <ul class="navbar-nav ml-auto">
      <li class="nav-item dropdown">
        <a class="nav-link" data-toggle="dropdown" href="#">
          <?= isset($_SESSION['nama_panggilan']) ? $_SESSION['nama_panggilan'] : $_SESSION['username']; ?> - [<?=$_SESSION['peran']; ?>] <i class="far fa-user"></i> 
        </a>
        <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
          <div class="dropdown-divider"></div>
          <a href="#" class="dropdown-item">
            <i class="fas fa-user mr-2"></i> Profil Admin
          </a>
          <div class="dropdown-divider"></div>
          <a href="../logout.php" class="dropdown-item">
            <i class="fas fa-sign-out-alt mr-2"></i> Logout
          </a>
        </div>
      </li>
    </ul>
  </nav>
  <!-- /.navbar -->

  <!-- Main Sidebar Container -->
  <aside class="main-sidebar sidebar-dark-primary elevation-4">
    <a href="#" class="brand-link text-center">
      <span class="brand-text font-weight-bold">CINDERENNA MART</span>
    </a>
    <div class="sidebar">
      <div class="user-panel mt-3 pb-3 mb-3 d-flex">
        <div class="info">
          <a href="#" class="d-block">Admin System</a>
        </div>
      </div>

      <!-- Sidebar Menu -->
      <?php
      include '../sidebar_admin.php';
      ?>
    </div>
  </aside>

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0 text-dark">Dashboard POS <small class="text-muted text-sm">| CINDERENNA MART</small></h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#">Beranda</a></li>
              <li class="breadcrumb-item active">Dashboard Admin</li>
            </ol>
          </div>
        </div>
      </div>
    </div>
    
    <!-- Main content -->
    <div class="content">
      <div class="container-fluid">
        <?php $pengguna = $_SESSION['username']; ?>

        <!-- RINGKASAN STATISTIK (SMALL BOXES) -->
        <div class="row">
          <div class="col-lg-3 col-6">
            <div class="small-box bg-success">
              <div class="inner">
                <h3>Rp <?= number_format($penjualan_today, 0, ',', '.'); ?></h3>
                <p>Penjualan Hari Ini</p>
              </div>
              <div class="icon">
                <i class="fas fa-shopping-cart"></i>
              </div>
              <a href="#" class="small-box-footer">Lihat Penjualan <i class="fas fa-arrow-circle-right"></i></a>
            </div>
          </div>

          <div class="col-lg-3 col-6">
            <div class="small-box bg-info">
              <div class="inner">
                <h3><?= number_format($transaksi_today, 0, ',', '.'); ?> <sup style="font-size: 16px">Nota</sup></h3>
                <p>Transaksi Hari Ini</p>
              </div>
              <div class="icon">
                <i class="fas fa-receipt"></i>
              </div>
              <a href="#" class="small-box-footer">Detail Transaksi <i class="fas fa-arrow-circle-right"></i></a>
            </div>
          </div>

          <div class="col-lg-3 col-6">
            <div class="small-box bg-warning">
              <div class="inner">
                <h3><?= $total_jenis_barang; ?> <sup style="font-size: 16px">Jenis</sup></h3>
                <p>Total Jenis Barang</p>
              </div>
              <div class="icon">
                <i class="fas fa-boxes"></i>
              </div>
              <a href="#" class="small-box-footer">Kelola Stok <i class="fas fa-arrow-circle-right"></i></a>
            </div>
          </div>

          <div class="col-lg-3 col-6">
            <div class="small-box bg-danger">
              <div class="inner">
                <h3><?= $total_stok_menipis; ?> <sup style="font-size: 16px">Item</sup></h3>
                <p>Stok Menipis (<=20)</p>
              </div>
              <div class="icon">
                <i class="fas fa-exclamation-triangle"></i>
              </div>
              <a href="#" class="small-box-footer">Periksa Stok <i class="fas fa-arrow-circle-right"></i></a>
            </div>
          </div>
        </div>

        <!-- RINGKASAN FINANSIAL & LABA RUGI -->
        <div class="row">
          <div class="col-md-4">
            <div class="info-box bg-gradient-info">
              <span class="info-box-icon"><i class="fas fa-arrow-down"></i></span>
              <div class="info-box-content">
                <span class="info-box-text">Total Pembelian (Kulakan)</span>
                <span class="info-box-number">Rp <?= number_format($total_pembelian_all, 0, ',', '.'); ?></span>
                <div class="progress"><div class="progress-bar" style="width: 100%"></div></div>
                <span class="progress-description">Pembelian Hari Ini: Rp <?= number_format($pembelian_today, 0, ',', '.'); ?></span>
              </div>
            </div>
          </div>

          <div class="col-md-4">
            <div class="info-box bg-gradient-success">
              <span class="info-box-icon"><i class="fas fa-arrow-up"></i></span>
              <div class="info-box-content">
                <span class="info-box-text">Total Omzet Penjualan</span>
                <span class="info-box-number">Rp <?= number_format($total_penjualan_all, 0, ',', '.'); ?></span>
                <div class="progress"><div class="progress-bar" style="width: 100%"></div></div>
                <span class="progress-description">Penjualan Hari Ini: Rp <?= number_format($penjualan_today, 0, ',', '.'); ?></span>
              </div>
            </div>
          </div>

          <div class="col-md-4">
            <div class="info-box <?= ($total_laba_rugi >= 0) ? 'bg-gradient-primary' : 'bg-gradient-danger'; ?>">
              <span class="info-box-icon"><i class="fas fa-chart-line"></i></span>
              <div class="info-box-content">
                <span class="info-box-text">Estimasi Laba Bersih</span>
                <span class="info-box-number">Rp <?= number_format($total_laba_rugi, 0, ',', '.'); ?></span>
                <div class="progress"><div class="progress-bar" style="width: 100%"></div></div>
                <span class="progress-description">Margin Penjualan Item Reguler</span>
              </div>
            </div>
          </div>
        </div>

        <!-- BARIS GRAFIK DIAGRAM KURVA -->
        <div class="row">
          <!-- Grafik Kurva Penjualan vs Pembelian (7 Hari Terakhir) -->
          <div class="col-md-8">
            <div class="card card-outline card-primary">
              <div class="card-header">
                <h3 class="card-title"><i class="fas fa-chart-line mr-1"></i> Tren Penjualan vs Pembelian (7 Hari Terakhir)</h3>
                <div class="card-tools">
                  <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i></button>
                </div>
              </div>
              <div class="card-body">
                <canvas id="salesChart" style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;"></canvas>
              </div>
            </div>
          </div>

          <!-- Grafik Diagram Metode Pembayaran -->
          <div class="col-md-4">
            <div class="card card-outline card-info">
              <div class="card-header">
                <h3 class="card-title"><i class="fas fa-wallet mr-1"></i> Metode Pembayaran</h3>
                <div class="card-tools">
                  <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i></button>
                </div>
              </div>
              <div class="card-body">
                <canvas id="paymentChart" style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;"></canvas>
              </div>
            </div>
          </div>
        </div>

        <!-- TABEL KELOLA BARANG TERLARIS & STOK MENIPIS -->
        <div class="row">
          <!-- Tabel Barang Terlaris -->
          <div class="col-md-7">
            <div class="card card-outline card-success">
              <div class="card-header">
                <h3 class="card-title"><i class="fas fa-fire text-danger mr-1"></i> Top 5 Barang Terlaris</h3>
              </div>
              <div class="card-body p-0">
                <div class="table-responsive">
                  <table class="table table-striped table-valign-middle">
                    <thead>
                      <tr>
                        <th>Kode</th>
                        <th>Nama Barang</th>
                        <th>Merk</th>
                        <th class="text-center">Terjual</th>
                        <th class="text-right">Total Nilai</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php 
                      if (mysqli_num_rows($q_terlaris) > 0) {
                        while ($r_terlaris = mysqli_fetch_assoc($q_terlaris)) {
                      ?>
                        <tr>
                          <td><code><?= $r_terlaris['kode_brg']; ?></code></td>
                          <td><?= $r_terlaris['nama_brg']; ?></td>
                          <td><?= $r_terlaris['merk']; ?></td>
                          <td class="text-center"><span class="badge badge-success"><?= $r_terlaris['total_terjual']; ?> pcs</span></td>
                          <td class="text-right">Rp <?= number_format($r_terlaris['total_omzet'], 0, ',', '.'); ?></td>
                        </tr>
                      <?php 
                        } 
                      } else {
                        echo '<tr><td colspan="5" class="text-center text-muted">Belum ada data penjualan.</td></tr>';
                      }
                      ?>
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
          </div>

          <!-- Tabel Peringatan Stok Menipis -->
          <div class="col-md-5">
            <div class="card card-outline card-danger">
              <div class="card-header">
                <h3 class="card-title"><i class="fas fa-bell text-warning mr-1"></i> Stok Barang Menipis</h3>
              </div>
              <div class="card-body p-0">
                <div class="table-responsive">
                  <table class="table table-striped table-valign-middle">
                    <thead>
                      <tr>
                        <th>Nama Barang</th>
                        <th>Tipe</th>
                        <th class="text-center">Sisa Stok</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php 
                      if (mysqli_num_rows($q_stok_menipis) > 0) {
                        while ($r_stok = mysqli_fetch_assoc($q_stok_menipis)) {
                      ?>
                        <tr>
                          <td>
                            <strong><?= $r_stok['nama_brg']; ?></strong><br>
                            <small class="text-muted"><?= $r_stok['merk']; ?></small>
                          </td>
                          <td>
                            <span class="badge <?= ($r_stok['tipe'] == 'Reguler') ? 'badge-info' : 'badge-secondary'; ?>">
                              <?= $r_stok['tipe']; ?>
                            </span>
                          </td>
                          <td class="text-center">
                            <span class="badge badge-danger"><?= $r_stok['stok']; ?> pcs</span>
                          </td>
                        </tr>
                      <?php 
                        } 
                      } else {
                        echo '<tr><td colspan="3" class="text-center text-muted">Semua stok barang aman.</td></tr>';
                      }
                      ?>
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
          </div>
        </div>

      </div>
    </div>
  </div>

  <!-- Control Sidebar -->
  <aside class="control-sidebar control-sidebar-dark">
  </aside>

  <!-- Main Footer -->
  <?php
  include '../footer.php';
  ?>
</div>

<!-- REQUIRED SCRIPTS -->
<?php
include '../script.php';
?>

<!-- SCRIPT INISIALISASI CHART.JS -->
<script>
$(document.body).ready(function () {

  // 1. Chart Kurva Area: Perbandingan Penjualan vs Pembelian (7 Hari Terakhir)
  var ctxSales = document.getElementById('salesChart').getContext('2d');
  
  var gradientJual = ctxSales.createLinearGradient(0, 0, 0, 300);
  gradientJual.addColorStop(0, 'rgba(40, 167, 69, 0.4)');
  gradientJual.addColorStop(1, 'rgba(40, 167, 69, 0.0)');

  var gradientBeli = ctxSales.createLinearGradient(0, 0, 0, 300);
  gradientBeli.addColorStop(0, 'rgba(23, 162, 184, 0.4)');
  gradientBeli.addColorStop(1, 'rgba(23, 162, 184, 0.0)');

  new Chart(ctxSales, {
    type: 'line',
    data: {
      labels: <?= json_encode($chart_labels); ?>,
      datasets: [
        {
          label: 'Penjualan (Omzet)',
          data: <?= json_encode($chart_jual); ?>,
          borderColor: '#28a745',
          borderWidth: 3,
          backgroundColor: gradientJual,
          fill: true,
          tension: 0.4,
          pointRadius: 4,
          pointBackgroundColor: '#28a745'
        },
        {
          label: 'Pembelian (Kulakan)',
          data: <?= json_encode($chart_beli); ?>,
          borderColor: '#17a2b8',
          borderWidth: 3,
          backgroundColor: gradientBeli,
          fill: true,
          tension: 0.4,
          pointRadius: 4,
          pointBackgroundColor: '#17a2b8'
        }
      ]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      interaction: {
        intersect: false,
        mode: 'index'
      },
      scales: {
        y: {
          beginAtZero: true,
          ticks: {
            callback: function(value) {
              return 'Rp ' + value.toLocaleString('id-ID');
            }
          }
        }
      }
    }
  });

  // 2. Chart Doughnut: Metode Pembayaran
  var ctxPay = document.getElementById('paymentChart').getContext('2d');
  new Chart(ctxPay, {
    type: 'doughnut',
    data: {
      labels: <?= json_encode(!empty($pay_labels) ? $pay_labels : ['CASH', 'TRANSFER', 'QRIS']); ?>,
      datasets: [{
        data: <?= json_encode(!empty($pay_counts) ? $pay_counts : [0, 0, 0]); ?>,
        backgroundColor: ['#28a745', '#007bff', '#ffc107', '#dc3545'],
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: {
        legend: {
          position: 'bottom'
        }
      }
    }
  });

});
</script>
</body>
</html>
<?php
}
?>