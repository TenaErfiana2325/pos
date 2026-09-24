<?php
require_once '../database/koneksi.php';

// Ambil filter tanggal dari parameter URL/Form jika ada
$tgl_mulai   = $_GET['tgl_mulai'] ?? '';
$tgl_selesai = $_GET['tgl_selesai'] ?? '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Laporan Penjualan</title>
  <?php 
  include '../css.php';
  $hal = 'lap_penjualan';
  ?>
  <!-- DataTables CSS Bootstrap 4 & Buttons -->
  <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap4.min.css">
  <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.2.2/css/buttons.bootstrap4.min.css">
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
            <h3 class="card-title">Laporan Penjualan</h3>
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
                  <!-- Tombol Filter -->
                  <button type="submit" class="btn btn-primary">
                    <i class="fas fa-filter"></i> Filter
                  </button>

                  <!-- Tombol Reset -->
                  <a href="index.php" class="btn btn-secondary">
                    <i class="fas fa-sync-alt"></i> Reset
                  </a>

                  <!-- Tombol Export PDF (Membuka Tab Baru & Membawa Tanggal Filter) -->
                  <a href="pdf.php?tgl_mulai=<?= urlencode($tgl_mulai); ?>&tgl_selesai=<?= urlencode($tgl_selesai); ?>" 
                     target="_blank" 
                     class="btn btn-danger ml-1">
                    <i class="fas fa-file-pdf"></i> Expor pdf
                  </a>
                </div>
              </div>
            </form>

            <?php if (!empty($tgl_mulai) && !empty($tgl_selesai)): ?>
              <!-- Tabel Data -->
              <div class="table-responsive">
                <table id="example1" class="table table-bordered table-striped text-center">
                  <thead>
                    <tr>
                      <th style="width: 50px;">No</th>
                      <th>Kode Nota</th>
                      <th>Tgl. Penjualan</th>
                      <th>Total Penjualan</th>
                      <th style="width: 80px;">Aksi</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php
                    $no = 1;

                    $query = mysqli_query($con, "
                        SELECT * FROM tbl_notajual 
                        WHERE tgl_penjualan BETWEEN '$tgl_mulai' AND '$tgl_selesai' 
                        ORDER BY tgl_penjualan DESC
                    ");

                    while ($row = mysqli_fetch_array($query)) {
                        $kode_nota = $row['kode_nota'];
                        $tgl_jual  = date('d/m/Y', strtotime($row['tgl_penjualan']));
                        $total     = $row['total_penjualan'];
                    ?>
                    <tr>
                      <td><?= $no++; ?></td>
                      <td><?= htmlspecialchars($kode_nota); ?></td>
                      <td><?= $tgl_jual; ?></td>
                      <td>Rp <?= number_format($total, 0, ',', '.'); ?></td>
                      <td>
                        <a href="detail.php?kode_nota=<?= urlencode($kode_nota); ?>" class="btn btn-success btn-sm" title="Lihat Detail">
                          <i class="fas fa-eye"></i>
                        </a>
                      </td>
                    </tr>
                    <?php } ?>
                  </tbody>
                </table>
              </div>
            <?php else: ?>
              <div class="alert alert-info text-center my-4">
                <i class="fas fa-info-circle mr-1"></i> Silakan pilih tanggal awal dan tanggal akhir di atas, lalu klik tombol <strong>Filter</strong> untuk menampilkan Laporan Penjualan.
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

<!-- Script DataTables & Export Buttons -->
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap4.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.2.2/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.bootstrap4.min.js"></script>

<script>
  $(function () {
    if ($("#example1").length > 0) {
      if ($.fn.DataTable.isDataTable('#example1')) {
        $('#example1').DataTable().destroy();
      }

      var table = $("#example1").DataTable({
        "responsive": true, 
        "lengthChange": false, 
        "autoWidth": false,
        "language": {
          "search": "Cari:",
          "paginate": {
            "previous": "Sebelumnya",
            "next": "Selanjutnya"
          },
          "info": "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
          "infoEmpty": "Menampilkan 0 sampai 0 dari 0 data",
          "zeroRecords": "Tidak ada data penjualan pada rentang tanggal ini"
        },
        "buttons": ["copy", "csv", "excel", "print", "colvis"]
      });

      table.buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');
    }
  });
</script>
</body>
</html>