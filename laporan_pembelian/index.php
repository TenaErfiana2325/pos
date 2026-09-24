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
  <title>Laporan Pembelian</title>
  <?php 
  include '../css.php';
  $hal = 'lap_pembelian';
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
            <h3 class="card-title">Laporan Pembelian</h3>
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
                   <a href="pdf.php" class="btn btn-danger">
                    <i class="fas fa-sync-alt"></i> Ekspor pdf
                  </a>
                </div>
              </div>
            </form>

            <?php if (!empty($tgl_mulai) && !empty($tgl_selesai)): ?>
              <!-- Tabel Data (Hanya Muncul Jika Tanggal Sudah Dihitung / Difilter) -->
              <div class="table-responsive">
                <table id="example1" class="table table-bordered table-striped text-center">
                  <thead>
                    <tr>
                      <th style="width: 50px;">No</th>
                      <th>Kode Nota</th>
                      <th>Supplier</th>
                      <th>Tgl. Pembelian</th>
                      <th>Total Pembelian</th>
                      <th style="width: 80px;">Aksi</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php
                    $no = 1;

                    // Query mengambil data dari tbl_notabeli JOIN ke tbl_supplier jika ada
                    $query = mysqli_query($con, "
                        SELECT b.*, s.nama_supplier 
                        FROM tbl_notabeli b
                        LEFT JOIN tbl_supplier s ON b.kode_supplier = s.kode_supplier
                        WHERE b.tgl_pembelian BETWEEN '$tgl_mulai' AND '$tgl_selesai' 
                        ORDER BY b.tgl_pembelian DESC
                    ") or mysqli_query($con, "
                        SELECT * FROM tbl_notabeli 
                        WHERE tgl_pembelian BETWEEN '$tgl_mulai' AND '$tgl_selesai' 
                        ORDER BY tgl_pembelian DESC
                    ");

                    while ($row = mysqli_fetch_array($query)) {
                        $kode_nota = $row['kode_nota'] ?? $row['kode_nota_beli'] ?? '';
                        $supplier  = $row['nama_supplier'] ?? $row['kode_supplier'] ?? '-';
                        $tgl_beli  = date('d/m/Y', strtotime($row['tgl_pembelian'] ?? $row['tanggal'] ?? 'now'));
                        $total     = $row['total_pembelian'] ?? $row['total_bayar'] ?? 0;
                    ?>
                    <tr>
                      <td><?= $no++; ?></td>
                      <td><?= htmlspecialchars($kode_nota); ?></td>
                      <td><?= htmlspecialchars($supplier); ?></td>
                      <td><?= $tgl_beli; ?></td>
                      <td>Rp <?= number_format($total, 0, ',', '.'); ?></td>
                      <td>
                        <!-- Tombol Detail (Mata Hijau) -->
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
              <!-- Tampilan awal sebelum pengguna menentukan filter tanggal -->
              <div class="alert alert-info text-center my-4">
                <i class="fas fa-info-circle mr-1"></i> Silakan pilih tanggal awal dan tanggal akhir di atas, lalu klik tombol <strong>Filter</strong> untuk menampilkan Laporan Pembelian.
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
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.print.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.colVis.min.js"></script>

<script>
  $(function () {
    if ($("#example1").length > 0) {
      // Hancurkan inisialisasi lama jika sudah ada agar tidak bentrok
      if ($.fn.DataTable.isDataTable('#example1')) {
        $('#example1').DataTable().destroy();
      }

      var table = $("#example1").DataTable({
        "responsive": true, 
        "lengthChange": false, 
        "autoWidth": false,
        "language": {
          "search": "Search:",
          "paginate": {
            "previous": "Previous",
            "next": "Next"
          },
          "info": "Showing _START_ to _END_ of _TOTAL_ entries",
          "infoEmpty": "Showing 0 to 0 of 0 entries",
          "zeroRecords": "Tidak ada data pembelian pada rentang tanggal ini"
        },
        "buttons": [
          "copy", "csv", "excel", "pdf", "print", "colvis"
        ]
      });

      table.buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');
    }
  });
</script>

</body>
</html>