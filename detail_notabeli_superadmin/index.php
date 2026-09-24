<?php
require_once '../database/koneksi.php';

// Mengambil parameter dari URL
$kode_nota_get = $_GET['kode_nota'] ?? '';
$tab_active = $_GET['tab'] ?? 'semua'; // Default tab adalah 'semua'

// Query Header Nota Beli
$tgl_pembelian = '-';
$total_pembelian = 0;
$keterangan = '-';
$nama_supplier = '-';

if (!empty($kode_nota_get)) {
    $q_nota = mysqli_query($con, "
        SELECT n.*, s.nama_supplier 
        FROM tbl_notabeli n, tbl_supplier s
        WHERE n.kode_supplier = s.kode_supplier
        AND n.kode_nota = '$kode_nota_get'
    ") or die(mysqli_error($con));
    
    if ($d_nota = mysqli_fetch_array($q_nota)) {
        $tgl_pembelian = $d_nota['tgl_pembelian'] ?? ($d_nota['tgl_nota'] ?? '-');
        $total_pembelian = $d_nota['total_pembelian'] ?? ($d_nota['total_harga'] ?? 0);
        $keterangan = $d_nota['keterangan'] ?? '-';
        $nama_supplier = $d_nota['nama_supplier'] ?? '-';
    } else {
        // Fallback jika query WHERE supplier tidak menemukan data
        $q_nota_single = mysqli_query($con, "
            SELECT * FROM tbl_notabeli WHERE kode_nota = '$kode_nota_get'
        ") or die(mysqli_error($con));
        if ($d_nota = mysqli_fetch_array($q_nota_single)) {
            $tgl_pembelian = $d_nota['tgl_pembelian'] ?? ($d_nota['tgl_nota'] ?? '-');
            $total_pembelian = $d_nota['total_pembelian'] ?? ($d_nota['total_harga'] ?? 0);
            $keterangan = $d_nota['keterangan'] ?? '-';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Detail Nota Beli</title>
  <?php 
  include '../css.php';
  $hal = 'beranda_detail_notabeli';
  ?>
  <!-- CSS Kustom untuk Styling Tab Biru -->
  <style>
    .custom-blue-tabs .nav-link {
      color: #007bff;
      font-weight: 500;
    }
    .custom-blue-tabs .nav-link.active {
      background-color: #007bff !important;
      color: #ffffff !important;
      border-color: #007bff !important;
    }
    .custom-blue-tabs .nav-link:hover {
      background-color: #e8f0fe;
    }
    .custom-blue-tabs .nav-link.active:hover {
      background-color: #0056b3 !important;
    }
  </style>
</head>
<body class="hold-transition sidebar-mini">
<div class="wrapper">

  <!-- Navbar -->
  <nav class="main-header navbar navbar-expand navbar-white navbar-light">
    <ul class="navbar-nav">
      <li class="nav-item">
        <a class="nav-link" data-widget="pushmenu" href="#" role="button">
          <i class="fas fa-bars"></i>
        </a>
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

  <!-- Sidebar -->
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

        <!-- CARD DETAIL INFORMASI NOTA BELI -->
        <div class="card card-primary">
          <div class="card-header">
            <h3 class="card-title">Detail Informasi Nota Beli</h3>
          </div>
          <div class="card-body">
            <div class="row">
              <div class="col-md-6">
                <table class="table table-borderless table-sm">
                  <tr>
                    <td width="35%"><strong>Kode Nota</strong></td>
                    <td width="5%">:</td>
                    <td><?= htmlspecialchars($kode_nota_get) ?></td>
                  </tr>
                  <tr>
                    <td><strong>Tgl Pembelian</strong></td>
                    <td>:</td>
                    <td><?= htmlspecialchars($tgl_pembelian) ?></td>
                  </tr>
                  <tr>
                    <td><strong>Supplier</strong></td>
                    <td>:</td>
                    <td><?= htmlspecialchars($nama_supplier) ?></td>
                  </tr>
                </table>
              </div>
              <div class="col-md-6">
                <table class="table table-borderless table-sm">
                  <tr>
                    <td width="35%"><strong>Total Pembelian</strong></td>
                    <td width="5%">:</td>
                    <td>Rp <?= number_format($total_pembelian, 0, ',', '.') ?></td>
                  </tr>
                  <tr>
                    <td><strong>Keterangan</strong></td>
                    <td>:</td>
                    <td><?= htmlspecialchars($keterangan) ?></td>
                  </tr>
                </table>
              </div>
            </div>
          </div>
        </div>

        <!-- CARD TABULASI SISTEM URL (BERWARNA BIRU) -->
        <div class="card card-primary card-tabs">
          <div class="card-header p-0 pt-1 bg-primary">
            <ul class="nav nav-tabs" id="custom-tabs-one-tab" role="tablist">
              <li class="nav-item">
                <a class="nav-link <?= ($tab_active == 'semua') ? 'active' : 'text-white'; ?>" 
                  href="?kode_nota=<?= urlencode($kode_nota_get); ?>&tab=semua">
                  Semua
                </a>
              </li>
              <li class="nav-item">
                <a class="nav-link <?= ($tab_active == 'barang') ? 'active' : 'text-white'; ?>" 
                  href="?kode_nota=<?= urlencode($kode_nota_get); ?>&tab=barang">
                  Barang Reguler
                </a>
              </li>
              <li class="nav-item">
                <a class="nav-link <?= ($tab_active == 'konsinyasi') ? 'active' : 'text-white'; ?>" 
                  href="?kode_nota=<?= urlencode($kode_nota_get); ?>&tab=konsinyasi">
                  Konsinyasi
                </a>
              </li>
            </ul>
          </div>

          <div class="card-body">
            <!-- Aksi Tombol -->
            <div class="mb-3">
              <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#modal-tambah">
                <i class="fas fa-plus"></i><b> Tambah Data</b>
              </button>
              <a href="pdf.php?kode_nota=<?= urlencode($kode_nota_get); ?>" target="_blank" class="btn btn-danger">
                <i class="fa fa-file-pdf"></i> Ekspor PDF
              </a>
              <a href="../nota_beli_superadmin/index.php" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Kembali
              </a>
            </div>

            <!-- TABEL DATA -->
            <table class="table table-bordered table-striped example-table" style="width:100%">
              <thead>
                <tr>
                  <th width="5%">No</th>
                  <th>Kode Nota</th>
                  <th>Nama Barang</th>
                  <th>Jenis Barang</th>
                  <th>Jumlah</th>
                  <th>Harga Beli</th>
                  <th>Total Harga Beli</th>
                  <th width="15%">Aksi</th>
                </tr>
              </thead>
              <tbody>
              <?php 
              if ($tab_active == 'barang') {
                  $q_detail = mysqli_query($con, "
                    SELECT d.*, b.nama_brg AS nama_barang, 'REGULER' AS jenis_barang
                    FROM tbl_detail_notabeli d, tbl_barang b
                    WHERE d.kode_brg = b.kode_brg
                    AND d.kode_nota = '$kode_nota_get'
                  ") or die(mysqli_error($con));

              } elseif ($tab_active == 'konsinyasi') {
                  $q_detail = mysqli_query($con, "
                    SELECT d.*, k.nama_barang, 'KONSINYASI' AS jenis_barang
                    FROM tbl_detail_notabeli d, tbl_konsinyasi k
                    WHERE d.kode_brg = k.kode_barang
                    AND d.kode_nota = '$kode_nota_get'
                  ") or die(mysqli_error($con));

              } else {
                  $q_detail = mysqli_query($con, "
                    SELECT d.*, b.nama_brg AS nama_barang, 'REGULER' AS jenis_barang
                    FROM tbl_detail_notabeli d, tbl_barang b
                    WHERE d.kode_brg = b.kode_brg
                    AND d.kode_nota = '$kode_nota_get'

                    UNION ALL

                    SELECT d.*, k.nama_barang, 'KONSINYASI' AS jenis_barang
                    FROM tbl_detail_notabeli d, tbl_konsinyasi k
                    WHERE d.kode_brg = k.kode_barang
                    AND d.kode_nota = '$kode_nota_get'
                  ") or die(mysqli_error($con));
              }

              $no = 1;
              if (mysqli_num_rows($q_detail) > 0) {
                while ($data = mysqli_fetch_array($q_detail)) {
                  $jenis = $data['jenis_barang'] ?? 'REGULER';
              ?>
                <tr>
                  <td><?= $no++ ?></td>
                  <td><?= htmlspecialchars($data['kode_nota']) ?></td>
                  <td><?= htmlspecialchars($data['nama_barang']) ?></td>
                  <td>
                    <span class="badge <?= ($jenis == 'REGULER') ? 'badge-info' : 'badge-warning'; ?>">
                      <?= htmlspecialchars($jenis) ?>
                    </span>
                  </td>
                  <td><?= htmlspecialchars($data['jumlah']) ?></td>
                  <td>Rp <?= number_format($data['harga_beli'], 0, ',', '.') ?></td>
                  <td>Rp <?= number_format($data['total_harga_beli'], 0, ',', '.') ?></td>
                  <td>
                    <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#modal-edit"
                      data-urut="<?= $data['urut']; ?>"
                      data-kode_nota="<?= $data['kode_nota']; ?>"
                      data-kode_brg="<?= $data['kode_brg']; ?>"
                      data-jenis_barang="<?= $jenis; ?>"
                      data-jumlah="<?= $data['jumlah']; ?>"
                      data-harga_beli="<?= $data['harga_beli']; ?>"
                      data-total_harga_beli="<?= $data['total_harga_beli']; ?>">
                      <i class="fas fa-edit"></i>
                    </button>
                    <a href="hapus.php?urut=<?= $data['urut']; ?>&kode_nota=<?= urlencode($data['kode_nota']); ?>" class="btn btn-danger btn-sm" onclick="return confirm('Yakin ingin menghapus item ini?')">
                      <i class="fas fa-trash"></i>
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

  <aside class="control-sidebar control-sidebar-dark"></aside>

  <!-- Modal Tambah Data -->
  <div class="modal fade" id="modal-tambah">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title">Tambah Detail Nota Beli</h4>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <form action="tambah.php" method="post">
          <div class="modal-body">
            <div class="form-group">
              <label for="kode_nota">Kode Nota</label>
              <input type="text" name="kode_nota" maxlength="10" class="form-control" value="<?= htmlspecialchars($kode_nota_get) ?>" readonly required>
            </div>
            <div class="form-group">
              <label for="kode_brg">Pilih Barang</label>
              <select name="kode_brg" id="tambah_kode_brg" class="form-control" required>
                <option value="">-- Pilih Barang --</option>
                <optgroup label="Barang Reguler">
                  <?php
                  if (!empty($kode_nota_get)) {
                    $q_brg = mysqli_query($con, "
                      SELECT DISTINCT b.* 
                      FROM tbl_barang b, tbl_notabeli n 
                      WHERE b.kode_supplier = n.kode_supplier 
                      AND n.kode_nota = '$kode_nota_get'
                    ") or die(mysqli_error($con));

                    while ($d_brg = mysqli_fetch_array($q_brg)) {
                      echo '<option value="' . $d_brg['kode_brg'] . '" data-harga="' . $d_brg['rata_harga_beli'] . '" data-jenis="REGULER">' . $d_brg['kode_brg'] . ' - ' . $d_brg['nama_brg'] . '</option>';
                    }
                  }
                  ?>
                </optgroup>
                <optgroup label="Barang Konsinyasi">
                  <?php
                  if (!empty($kode_nota_get)) {
                    $q_brg_k = mysqli_query($con, "
                      SELECT DISTINCT k.* 
                      FROM tbl_konsinyasi k, tbl_notabeli n 
                      WHERE k.kode_supplier = n.kode_supplier 
                      AND n.kode_nota = '$kode_nota_get'
                    ") or die(mysqli_error($con));

                    while ($d_brg_k = mysqli_fetch_array($q_brg_k)) {
                      echo '<option value="' . $d_brg_k['kode_barang'] . '" data-harga="' . $d_brg_k['harga_beli'] . '" data-jenis="KONSINYASI">' . $d_brg_k['kode_barang'] . ' - ' . $d_brg_k['nama_barang'] . '</option>';
                    }
                  }
                  ?>
                </optgroup>
              </select>
            </div>
            <input type="hidden" name="jenis_barang" id="tambah_jenis_barang" value="REGULER">
            <div class="form-group">
              <label for="jumlah">Jumlah</label>
              <input type="number" name="jumlah" class="form-control" id="tambah_jumlah" placeholder="Masukan Jumlah" required>
            </div>
            <div class="form-group">
              <label for="harga_beli">Harga Beli</label>
              <input type="number" name="harga_beli" class="form-control" id="tambah_harga" placeholder="Masukan Harga Beli" required>
            </div>
            <div class="form-group">
              <label for="total_harga_beli">Total Harga Beli</label>
              <input type="number" name="total_harga_beli" class="form-control" id="tambah_total" placeholder="Masukan Total Harga Beli" required>
            </div>
          </div>
          <div class="modal-footer justify-content-between">
            <button type="button" class="btn btn-default" data-dismiss="modal">Tutup</button>
            <button type="submit" name="btn_tambah" class="btn btn-primary">Simpan</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- Modal Edit -->
  <div class="modal fade" id="modal-edit">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title">Edit Detail Nota Beli</h4>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <form action="ubah.php" method="post">
          <div class="modal-body">
            <input type="hidden" name="urut" id="edit_urut">
            <div class="form-group">
              <label for="kode_nota">Kode Nota</label>
              <input type="text" name="kode_nota" maxlength="10" class="form-control" id="edit_kode_nota" readonly required>
            </div>
            <div class="form-group">
              <label for="kode_brg">Pilih Barang</label>
              <select name="kode_brg" id="edit_kode_brg" class="form-control" required>
                <option value="">-- Pilih Barang --</option>
                <optgroup label="Barang Reguler">
                  <?php
                  $q_brg_edit = mysqli_query($con, "
                    SELECT DISTINCT b.* 
                    FROM tbl_barang b, tbl_notabeli n 
                    WHERE b.kode_supplier = n.kode_supplier 
                    AND n.kode_nota = '$kode_nota_get'
                  ") or die(mysqli_error($con));
                  
                  while ($d_brg = mysqli_fetch_array($q_brg_edit)) {
                    echo '<option value="' . $d_brg['kode_brg'] . '" data-harga="' . $d_brg['rata_harga_beli'] . '" data-jenis="REGULER">' . $d_brg['kode_brg'] . ' - ' . $d_brg['nama_brg'] . '</option>';
                  }
                  ?>
                </optgroup>
                <optgroup label="Barang Konsinyasi">
                  <?php
                  $q_brg_edit_k = mysqli_query($con, "
                    SELECT DISTINCT k.* 
                    FROM tbl_konsinyasi k, tbl_notabeli n 
                    WHERE k.kode_supplier = n.kode_supplier 
                    AND n.kode_nota = '$kode_nota_get'
                  ") or die(mysqli_error($con));
                  
                  while ($d_brg_k = mysqli_fetch_array($q_brg_edit_k)) {
                    echo '<option value="' . $d_brg_k['kode_barang'] . '" data-harga="' . $d_brg_k['harga_beli'] . '" data-jenis="KONSINYASI">' . $d_brg_k['kode_barang'] . ' - ' . $d_brg_k['nama_barang'] . '</option>';
                  }
                  ?>
                </optgroup>
              </select>
            </div>
            <input type="hidden" name="jenis_barang" id="edit_jenis_barang">
            <div class="form-group">
              <label for="jumlah">Jumlah</label>
              <input type="number" name="jumlah" id="edit_jumlah" class="form-control" placeholder="Masukan Jumlah" required>
            </div>
            <div class="form-group">
              <label for="harga_beli">Harga Beli</label>
              <input type="number" name="harga_beli" id="edit_harga" class="form-control" placeholder="Harga Beli" required>
            </div>
            <div class="form-group">
              <label for="total_harga_beli">Total Harga Beli</label>
              <input type="number" name="total_harga_beli" id="edit_total" class="form-control" placeholder="Total Harga Beli" required>
            </div>
          </div>
          <div class="modal-footer justify-content-between">
            <button type="button" class="btn btn-default" data-dismiss="modal">Tutup</button>
            <button type="submit" name="btn_edit" class="btn btn-primary">Edit</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <?php include '../footer.php' ?>

</div>

<?php include '../script.php' ?>

<script>
  $(document).ready(function() {
    if ($.fn.DataTable) {
      $('.example-table').DataTable({
        "responsive": true, 
        "autoWidth": false
      });
    }

    // Hitung Otomatis Modal Tambah
    $('#tambah_kode_brg').on('change', function() {
      var harga = $(this).find(':selected').data('harga') || 0;
      var jenis = $(this).find(':selected').data('jenis') || 'REGULER';
      $('#tambah_harga').val(harga);
      $('#tambah_jenis_barang').val(jenis);
      hitungTotalTambah();
    });

    $('#tambah_jumlah, #tambah_harga').on('input', function() {
      hitungTotalTambah();
    });

    function hitungTotalTambah() {
      var jumlah = parseFloat($('#tambah_jumlah').val()) || 0;
      var harga = parseFloat($('#tambah_harga').val()) || 0;
      $('#tambah_total').val(jumlah * harga);
    }

    // Hitung Otomatis Modal Edit
    $('#edit_kode_brg').on('change', function() {
      var harga = $(this).find(':selected').data('harga') || 0;
      var jenis = $(this).find(':selected').data('jenis') || 'REGULER';
      $('#edit_harga').val(harga);
      $('#edit_jenis_barang').val(jenis);
      hitungTotalEdit();
    });

    $('#edit_jumlah, #edit_harga').on('input', function() {
      hitungTotalEdit();
    });

    function hitungTotalEdit() {
      var jumlah = parseFloat($('#edit_jumlah').val()) || 0;
      var harga = parseFloat($('#edit_harga').val()) || 0;
      $('#edit_total').val(jumlah * harga);
    }

    // Mengisi Modal Edit
    $('#modal-edit').on('show.bs.modal', function(e) {
      var button = $(e.relatedTarget);
      $('#edit_urut').val(button.data('urut'));
      $('#edit_kode_nota').val(button.data('kode_nota'));
      $('#edit_kode_brg').val(button.data('kode_brg'));
      $('#edit_jenis_barang').val(button.data('jenis_barang'));
      $('#edit_jumlah').val(button.data('jumlah'));
      $('#edit_harga').val(button.data('harga_beli'));
      $('#edit_total').val(button.data('total_harga_beli'));
    });
  });
</script>

</body>
</html>