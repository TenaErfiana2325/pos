<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once "../database/koneksi.php";

$authority = isset($_SESSION['peran']) ? $_SESSION['peran'] : '';

// Validasi Otoritas Khusus Kasir ('K')
if ($authority != 'K') {
  echo '<script>alert("Akun ini melakukan cross authority, akan segera di logout");</script>';
  echo '<script>window.location.href="../logout.php";</script>';
  exit();
} else {

  // ==========================================
  // QUERY METRIK & DATA DASHBOARD KASIR
  // ==========================================

  $tgl_today = date('Y-m-d');

  // 1. Total Penjualan & Transaksi Kasir Hari Ini
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

  // 2. Total Stok Barang Tersedia
  $q_stok_total = mysqli_query($con, "
    SELECT 
      (SELECT COALESCE(SUM(stok), 0) FROM tbl_barang) + 
      (SELECT COALESCE(SUM(stok), 0) FROM tbl_konsinyasi) AS total_stok
  ");
  $d_stok_total = mysqli_fetch_assoc($q_stok_total);
  $total_stok = $d_stok_total['total_stok'];

  // 3. Peringatan Stok Menipis (Stok <= 20)
  $q_stok_menipis_count = mysqli_query($con, "
    SELECT 
      (SELECT COUNT(*) FROM tbl_barang WHERE stok <= 20) + 
      (SELECT COUNT(*) FROM tbl_konsinyasi WHERE stok <= 20) AS total_menipis
  ");
  $d_stok_menipis_count = mysqli_fetch_assoc($q_stok_menipis_count);
  $total_stok_menipis = $d_stok_menipis_count['total_menipis'];

  // 4. Data Barang Terlaris (Top 5)
  $q_terlaris = mysqli_query($con, "
    SELECT 
      master_brg.kode_brg,
      master_brg.nama_brg,
      master_brg.merk,
      master_brg.stok,
      (SELECT SUM(jumlah) FROM tbl_detail_notajual WHERE kode_brg = master_brg.kode_brg) AS total_terjual
    FROM (
      SELECT kode_brg, nama_brg, merk, stok FROM tbl_barang
      UNION ALL
      SELECT kode_barang AS kode_brg, nama_barang AS nama_brg, merk, stok FROM tbl_konsinyasi
    ) AS master_brg
    WHERE (SELECT SUM(jumlah) FROM tbl_detail_notajual WHERE kode_brg = master_brg.kode_brg) > 0
    ORDER BY total_terjual DESC
    LIMIT 5
  ");

  // 5. Riwayat 5 Transaksi Terakhir Hari Ini
  $q_transaksi_terakhir = mysqli_query($con, "
    SELECT kode_nota, tgl_penjualan, total_penjualan, jenis_pembayaran, status 
    FROM tbl_notajual 
    ORDER BY kode_nota DESC 
    LIMIT 5
  ");

  // 6. Data Grafik Tren Penjualan Kasir (7 Hari Terakhir)
  $chart_labels = [];
  $chart_jual = [];

  for ($i = 6; $i >= 0; $i--) {
      $tgl = date('Y-m-d', strtotime("-$i days"));
      $label_tgl = date('d M', strtotime($tgl));
      
      $q_jual_tgl = mysqli_query($con, "SELECT COALESCE(SUM(total_penjualan), 0) AS total FROM tbl_notajual WHERE tgl_penjualan = '$tgl'");
      $d_jual_tgl = mysqli_fetch_assoc($q_jual_tgl);

      $chart_labels[] = $label_tgl;
      $chart_jual[] = (int)$d_jual_tgl['total'];
  }
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Beranda Kasir - Cinderennamart POS</title>
  <?php
  $hal = 'kasir';
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
          <?= isset($_SESSION['nama_panggilan']) ? $_SESSION['nama_panggilan'] : $_SESSION['username']; ?> - [Kasir] <i class="far fa-user-circle"></i> 
        </a>
        <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
          <div class="dropdown-divider"></div>
          <a href="../logout.php" class="dropdown-item text-danger">
            <i class="fas fa-sign-out-alt mr-2"></i> Logout
          </a>
        </div>
      </li>
    </ul>
  </nav>
  <!-- /.navbar -->

  <!-- Main Sidebar Container (Tema Biru Primary) -->
  <aside class="main-sidebar sidebar-dark-primary elevation-4">
    <a href="#" class="brand-link text-center">
      <span class="brand-text font-weight-bold">CINDERENNA MART</span>
    </a>
    <div class="sidebar">
      <div class="user-panel mt-3 pb-3 mb-3 d-flex">
        <div class="info">
          <a href="#" class="d-block">Kasir System</a>
        </div>
      </div>

      <!-- Sidebar Menu Kasir -->
      <?php
      if (file_exists('../sidebar_kasir.php')) {
          include '../sidebar_kasir.php';
      } else {
          include '../sidebar_admin.php';
      }
      ?>
    </div>
  </aside>

  <!-- Content Wrapper -->
  <div class="content-wrapper">
    <!-- Content Header -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0 text-dark">Dashboard Kasir <small class="text-muted text-sm">| Operasional Mesin Kasir</small></h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#">Beranda</a></li>
              <li class="breadcrumb-item active">Kasir</li>
            </ol>
          </div>
        </div>
      </div>
    </div>
    
    <!-- Main content -->
    <div class="content">
      <div class="container-fluid">

        <!-- BANNER AKSES CEPAT TRANSAKSI (BIRU) -->
        <div class="row mb-3">
          <div class="col-12">
            <div class="callout callout-info bg-white elevation-1">
              <div class="row align-items-center">
                <div class="col-md-8">
                  <h5><i class="fas fa-cash-register text-primary mr-2"></i> <strong>Siap Melayani Transaksi?</strong></h5>
                  <p class="mb-0 text-muted">Klik tombol di samping untuk langsung membuka layar mesin kasir/penjualan barang.</p>
                </div>
                <div class="col-md-4 text-right mt-2 mt-md-0">
                  <a href="../penjualan/transaksi.php" class="btn btn-primary btn-lg shadow-sm">
                    <i class="fas fa-cart-plus mr-2"></i> Buka Mesin Kasir
                  </a>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- RINGKASAN STATISTIK KASIR -->
        <div class="row">
          <div class="col-lg-3 col-6">
            <div class="small-box bg-primary">
              <div class="inner">
                <h3>Rp <?= number_format($penjualan_today, 0, ',', '.'); ?></h3>
                <p>Omzet Saya Hari Ini</p>
              </div>
              <div class="icon">
                <i class="fas fa-coins"></i>
              </div>
            </div>
          </div>

          <div class="col-lg-3 col-6">
            <div class="small-box bg-info">
              <div class="inner">
                <h3><?= number_format($transaksi_today, 0, ',', '.'); ?> <sup style="font-size: 16px">Nota</sup></h3>
                <p>Transaksi Diberkahi</p>
              </div>
              <div class="icon">
                <i class="fas fa-file-invoice-dollar"></i>
              </div>
            </div>
          </div>

          <div class="col-lg-3 col-6">
            <div class="small-box bg-secondary">
              <div class="inner">
                <h3><?= number_format($total_stok, 0, ',', '.'); ?> <sup style="font-size: 16px">pcs</sup></h3>
                <p>Total Item di Toko</p>
              </div>
              <div class="icon">
                <i class="fas fa-boxes"></i>
              </div>
            </div>
          </div>

          <div class="col-lg-3 col-6">
            <div class="small-box bg-warning">
              <div class="inner">
                <h3><?= $total_stok_menipis; ?> <sup style="font-size: 16px">Item</sup></h3>
                <p>Perlu Diingatkan ke Admin</p>
              </div>
              <div class="icon">
                <i class="fas fa-exclamation-circle"></i>
              </div>
            </div>
          </div>
        </div>

        <!-- BARIS GRAFIK & PRODUK LARIS -->
        <div class="row">
          <!-- Grafik Kurva Penjualan Kasir (Biru AdminLTE) -->
          <div class="col-md-7">
            <div class="card card-outline card-primary">
              <div class="card-header">
                <h3 class="card-title"><i class="fas fa-chart-line mr-1"></i> Performa Penjualan (7 Hari Terakhir)</h3>
              </div>
              <div class="card-body">
                <canvas id="kasirSalesChart" style="min-height: 230px; height: 230px; max-height: 230px; max-width: 100%;"></canvas>
              </div>
            </div>
          </div>

          <!-- Top 5 Produk Paling Sering Dicari -->
          <div class="col-md-5">
            <div class="card card-outline card-info">
              <div class="card-header">
                <h3 class="card-title"><i class="fas fa-star text-warning mr-1"></i> Produk Paling Laris</h3>
              </div>
              <div class="card-body p-0">
                <table class="table table-striped table-valign-middle">
                  <thead>
                    <tr>
                      <th>Barang</th>
                      <th class="text-center">Sisa Stok</th>
                      <th class="text-right">Terjual</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php 
                    if (mysqli_num_rows($q_terlaris) > 0) {
                      while ($r_terlaris = mysqli_fetch_assoc($q_terlaris)) {
                    ?>
                      <tr>
                        <td>
                          <strong><?= $r_terlaris['nama_brg']; ?></strong><br>
                          <small class="text-muted"><?= $r_terlaris['merk']; ?></small>
                        </td>
                        <td class="text-center">
                          <span class="badge <?= ($r_terlaris['stok'] <= 20) ? 'badge-danger' : 'badge-info'; ?>">
                            <?= $r_terlaris['stok']; ?> pcs
                          </span>
                        </td>
                        <td class="text-right font-weight-bold text-primary">
                          <?= $r_terlaris['total_terjual']; ?> pcs
                        </td>
                      </tr>
                    <?php 
                      } 
                    } else {
                      echo '<tr><td colspan="3" class="text-center text-muted">Belum ada data.</td></tr>';
                    }
                    ?>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>

        <!-- RIWAYAT TRANSAKSI TERAKHIR -->
<div class="row">
  <div class="col-12">
    <div class="card card-outline card-primary">
      <div class="card-header">
        <h3 class="card-title"><i class="fas fa-history mr-1"></i> 5 Transaksi Penjualan Terakhir</h3>
      </div>
      <div class="card-body p-0">
        <div class="table-responsive">
          <table class="table table-hover align-items-center mb-0">
            <thead>
              <tr>
                <th>Kode Nota</th>
                <th>Tanggal</th>
                <th>Metode Bayar</th>
                <th>Status</th>
                <th class="text-right">Total Transaksi</th>
              </tr>
            </thead>
            <tbody>
              <?php 
              $q_transaksi_terakhir = mysqli_query($con, "
                SELECT kode_nota, tgl_penjualan, total_penjualan, jenis_pembayaran, status 
                FROM tbl_notajual 
                ORDER BY kode_nota DESC 
                LIMIT 5
              ");

              if (mysqli_num_rows($q_transaksi_terakhir) > 0) {
                while ($r_trx = mysqli_fetch_assoc($q_transaksi_terakhir)) {
                  
                  // WARNA METODE BAYAR: CASH (KUNING), TRANSFER (BIRU), QRIS (HIJAU)
                  $bayar_val = strtoupper(trim($r_trx['jenis_pembayaran'] ?? 'CASH'));
                  if ($bayar_val == 'CASH') {
                      $badge_bayar = 'badge-warning'; // KUNING
                  } elseif ($bayar_val == 'TRANSFER') {
                      $badge_bayar = 'badge-primary'; // BIRU
                  } elseif ($bayar_val == 'QRIS') {
                      $badge_bayar = 'badge-success'; // HIJAU
                  } else {
                      $badge_bayar = 'badge-secondary';
                  }

                  // PENYESUAIAN MAPPING STATUS TERMASUK KODE DB '4'
                  $raw_status = strtolower(trim($r_trx['status']));
                  
                  if ($raw_status == 'l' || $raw_status == 'lunas') {
                      $status_text = 'LUNAS';
                      $badge_status = 'badge-success'; // Hijau LUNAS
                  } elseif ($raw_status == '3' || $raw_status == '50%' || $raw_status == 'dibayar 50%') {
                      $status_text = '50%';
                      $badge_status = 'badge-danger';  // Merah Belum Lunas
                  } elseif ($raw_status == '4' || $raw_status == '2' || $raw_status == '75%' || $raw_status == 'dibayar 75%') {
                      // Angka 4 dan 2 dari DB langsung dipetakan ke 75%
                      $status_text = '75%';
                      $badge_status = 'badge-danger';  // Merah Belum Lunas
                  } elseif ($raw_status == '25%' || $raw_status == 'dibayar 25%') {
                      $status_text = '25%';
                      $badge_status = 'badge-danger';  // Merah Belum Lunas
                  } else {
                      $status_text = strtoupper($r_trx['status']);
                      $badge_status = 'badge-danger';  // Merah Belum Lunas
                  }
              ?>
                <tr>
                  <td><code><?= $r_trx['kode_nota']; ?></code></td>
                  <td><?= date('d-m-Y', strtotime($r_trx['tgl_penjualan'])); ?></td>
                  <td>
                    <span class="badge <?= $badge_bayar; ?>">
                      <?= $bayar_val; ?>
                    </span>
                  </td>
                  <td>
                    <span class="badge <?= $badge_status; ?>">
                      <?= $status_text; ?>
                    </span>
                  </td>
                  <td class="text-right font-weight-bold">
                    Rp <?= number_format($r_trx['total_penjualan'], 0, ',', '.'); ?>
                  </td>
                </tr>
              <?php 
                } 
              } else {
                echo '<tr><td colspan="5" class="text-center text-muted">Belum ada transaksi terakhir.</td></tr>';
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

  <!-- Footer -->
  <?php
  include '../footer.php';
  ?>
</div>

<!-- REQUIRED SCRIPTS -->
<?php
include '../script.php';
?>

<!-- SCRIPT CHART KASIR (WARNA BIRU ADMINLTE) -->
<script>
$(document.body).ready(function () {
  var ctxSales = document.getElementById('kasirSalesChart').getContext('2d');
  
  // Gradien Warna Biru AdminLTE (#007bff)
  var gradientJual = ctxSales.createLinearGradient(0, 0, 0, 250);
  gradientJual.addColorStop(0, 'rgba(0, 123, 255, 0.45)');
  gradientJual.addColorStop(1, 'rgba(0, 123, 255, 0.0)');

  new Chart(ctxSales, {
    type: 'line',
    data: {
      labels: <?= json_encode($chart_labels); ?>,
      datasets: [
        {
          label: 'Omzet Penjualan (Rp)',
          data: <?= json_encode($chart_jual); ?>,
          borderColor: '#007bff',
          borderWidth: 3,
          backgroundColor: gradientJual,
          fill: true,
          tension: 0.4,
          pointRadius: 5,
          pointBackgroundColor: '#007bff'
        }
      ]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
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
});
</script>
</body>
</html>
<?php
}
?>