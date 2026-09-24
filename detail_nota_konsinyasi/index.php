<?php
require_once '../database/koneksi.php';

// Mengambil parameter kode_nota_konsinyasi dari URL
$kode_nota_get = $_GET['kode_nota_konsinyasi'] ?? '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Detail Nota Konsinyasi</title>
  <?php 
  include '../css.php';
  $hal = 'beranda_detail_nota_konsinyasi';
  ?>
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

        <div class="card">
          <div class="card-header">
            <h3 class="card-title">Data Detail Nota Konsinyasi (Kode Nota: <?= htmlspecialchars($kode_nota_get) ?>)</h3>
          </div>

          <div class="card-body">

            <div class="mb-2">
              <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#modal-tambah">
                <i class="fas fa-plus"></i><b> Tambah Data</b>
              </button>

              <a href="pdf.php?kode_nota_konsinyasi=<?= urlencode($kode_nota_get); ?>" target="_blank" class="btn btn-danger">
                <i class="fa fa-file-pdf"></i> ekspor pdf
              </a>

              <a href="../nota_konsinyasi/index.php" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Kembali
              </a>
            </div>

            <table class="table table-bordered table-striped">
              <thead>
                <tr>
                  <th>No</th>
                  <th>Kode Nota</th>
                  <th>Nama Barang</th>
                  <th>Titip</th>
                  <th>Terjual</th>
                  <th>Sisa</th>
                  <th>Harga Beli</th>
                  <th>Harga Jual</th>
                  <th>Setor Supplier</th>
                  <th>Laba Toko</th>
                  <th>Aksi</th>
                </tr>
              </thead>
              <tbody>
                <?php
                $no = 1;
                $total_setor_all = 0;
                $total_laba_all  = 0;

                // Query mengambil detail nota + nama barang dari tbl_konsinyasi
                $query = mysqli_query($con, "
                    SELECT d.*, k.nama_barang 
                    FROM tbl_detail_nota_konsinyasi d, tbl_konsinyasi k 
                    WHERE d.kode_barang = k.kode_barang 
                    AND d.kode_nota_konsinyasi = '$kode_nota_get'
                ");

                while ($row = mysqli_fetch_array($query)) {
                    $nama_barang = !empty($row['nama_barang']) ? $row['nama_barang'] : $row['kode_barang'];
                    
                    // Perhitungan otomatis
                    $titip       = $row['jumlah'];
                    $terjual     = $row['terjual'] ?? 0;
                    $sisa        = $titip - $terjual;
                    
                    $harga_beli  = $row['harga_beli'];
                    $harga_jual  = $row['harga_jual'];
                    
                    $setor_supplier = $terjual * $harga_beli;
                    $laba_toko      = $terjual * ($harga_jual - $harga_beli);
                    
                    $total_setor_all += $setor_supplier;
                    $total_laba_all  += $laba_toko;

                    // Ambil ID Detail dari tabel
                    $id_detail_val = $row['id_detail'] ?? $row['id'] ?? '';
                ?>
                <tr>
                  <td><?= $no++; ?></td>
                  <td><?= htmlspecialchars($row['kode_nota_konsinyasi']); ?></td>
                  <td><?= htmlspecialchars($nama_barang); ?></td>
                  <td><span class="badge bg-info"><?= $titip; ?></span></td>
                  <td><span class="badge bg-success"><?= $terjual; ?></span></td>
                  <td><span class="badge bg-warning"><?= $sisa; ?></span></td>
                  <td>Rp <?= number_format($harga_beli); ?></td>
                  <td>Rp <?= number_format($harga_jual); ?></td>
                  <td><strong>Rp <?= number_format($setor_supplier); ?></strong></td>
                  <td><strong class="text-success">Rp <?= number_format($laba_toko); ?></strong></td>
                  <td>
                    <!-- Tombol Edit -->
                    <button type="button" 
                            class="btn btn-sm btn-primary" 
                            data-toggle="modal" 
                            data-target="#modal-edit"
                            data-id_detail="<?= $id_detail_val ?>"
                            data-kode_nota_konsinyasi="<?= htmlspecialchars($row['kode_nota_konsinyasi']) ?>"
                            data-kode_barang="<?= htmlspecialchars($row['kode_barang']) ?>"
                            data-jumlah="<?= $row['jumlah'] ?>"
                            data-terjual="<?= $terjual ?>"
                            data-harga_beli="<?= $row['harga_beli'] ?>"
                            data-harga_jual="<?= $row['harga_jual'] ?>">
                        <i class="fas fa-edit"></i>
                    </button>

                    <!-- Tombol Hapus -->
                    <a href="hapus.php?id=<?= $id_detail_val ?>&kode_nota_konsinyasi=<?= urlencode($kode_nota_get) ?>" 
                       class="btn btn-sm btn-danger" 
                       onclick="return confirm('Apakah Anda yakin ingin menghapus data ini?')">
                        <i class="fas fa-trash"></i>
                    </a>
                  </td>
                </tr>
                <?php } ?>
              </tbody>
              <tfoot>
                <tr style="background-color: #f8f9fa;">
                  <th colspan="8" class="text-end">TOTAL KESELURUHAN:</th>
                  <th>Rp <?= number_format($total_setor_all); ?></th>
                  <th class="text-success">Rp <?= number_format($total_laba_all); ?></th>
                  <th></th>
                </tr>
              </tfoot>
            </table>

          </div>
        </div>

      </div>
    </div>
  </div>

  <!-- Modal Tambah Data -->
  <div class="modal fade" id="modal-tambah">
    <div class="modal-dialog">
      <div class="modal-content">

        <div class="modal-header">
          <h4 class="modal-title">Tambah Detail Nota Konsinyasi</h4>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>

        <form action="tambah.php" method="post">
          <div class="modal-body">

            <div class="form-group">
              <label for="kode_nota_konsinyasi">Kode Nota Konsinyasi</label>
              <input type="text" name="kode_nota_konsinyasi" class="form-control" value="<?= htmlspecialchars($kode_nota_get) ?>" readonly required>
            </div>

            <div class="form-group">
              <label for="kode_barang">Pilih Barang Konsinyasi</label>
              <select name="kode_barang" id="tambah_kode_barang" class="form-control" required>
                <option value="">-- Pilih Barang Konsinyasi --</option>
                <?php
                $q_brg = mysqli_query($con, "SELECT * FROM tbl_konsinyasi") or die(mysqli_error($con));
                while ($d_brg = mysqli_fetch_array($q_brg)) {
                  $kd_brg   = $d_brg['kode_barang'] ?? $d_brg['kode_brg'] ?? '';
                  $nm_brg   = $d_brg['nama_barang'] ?? $d_brg['nama_brg'] ?? '';
                  $hrg_beli = $d_brg['harga_beli'] ?? 0;
                  $hrg_jual = $d_brg['harga_jual'] ?? 0;

                  echo '<option value="' . $kd_brg . '" data-harga-beli="' . $hrg_beli . '" data-harga-jual="' . $hrg_jual . '">' . $kd_brg . ' - ' . $nm_brg . '</option>';
                }
                ?>
              </select>
            </div>

            <div class="form-group">
              <label for="jumlah">Jumlah Titip</label>
              <input type="number" name="jumlah" class="form-control" id="jumlah" placeholder="Masukan Jumlah Titip" required min="1">
            </div>

            <div class="form-group">
              <label for="harga_beli">Harga Beli</label>
              <input type="number" name="harga_beli" class="form-control" id="harga_beli" placeholder="Masukan Harga Beli" required>
            </div>

            <div class="form-group">
              <label for="harga_jual">Harga Jual</label>
              <input type="number" name="harga_jual" class="form-control" id="harga_jual" placeholder="Masukan Harga Jual" required>
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
          <h4 class="modal-title">Edit Detail Nota Konsinyasi</h4>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>

        <form action="ubah.php" method="post">
          <div class="modal-body">

            <input type="hidden" name="id_detail" id="edit_id_detail">

            <div class="form-group">
              <label for="kode_nota_konsinyasi">Kode Nota Konsinyasi</label>
              <input type="text" name="kode_nota_konsinyasi" class="form-control" id="edit_kode_nota_konsinyasi" readonly required>
            </div>

            <div class="form-group">
              <label for="kode_barang">Pilih Barang Konsinyasi</label>
              <select name="kode_barang" id="edit_kode_barang" class="form-control" required>
                <option value="">-- Pilih Barang Konsinyasi --</option>
                <?php
                $q_brg_edit = mysqli_query($con, "SELECT * FROM tbl_konsinyasi") or die(mysqli_error($con));
                while ($d_brg = mysqli_fetch_array($q_brg_edit)) {
                  $kd_brg   = $d_brg['kode_barang'] ?? $d_brg['kode_brg'] ?? '';
                  $nm_brg   = $d_brg['nama_barang'] ?? $d_brg['nama_brg'] ?? '';
                  $hrg_beli = $d_brg['harga_beli'] ?? 0;
                  $hrg_jual = $d_brg['harga_jual'] ?? 0;

                  echo '<option value="' . $kd_brg . '" data-harga-beli="' . $hrg_beli . '" data-harga-jual="' . $hrg_jual . '">' . $kd_brg . ' - ' . $nm_brg . '</option>';
                }
                ?>
              </select>
            </div>

            <div class="form-group">
              <label for="jumlah">Jumlah Titip</label>
              <input type="number" name="jumlah" id="edit_jumlah" class="form-control" placeholder="Masukan Jumlah Titip" required min="1">
            </div>

            <div class="form-group">
              <label for="terjual">Jumlah Terjual</label>
              <input type="number" name="terjual" id="edit_terjual" class="form-control" placeholder="Masukan Jumlah Terjual" required min="0">
            </div>

            <div class="form-group">
              <label for="harga_beli">Harga Beli</label>
              <input type="number" name="harga_beli" id="edit_harga_beli" class="form-control" placeholder="Harga Beli" required>
            </div>

            <div class="form-group">
              <label for="harga_jual">Harga Jual</label>
              <input type="number" name="harga_jual" id="edit_harga_jual" class="form-control" placeholder="Harga Jual" required>
            </div>

          </div>

          <div class="modal-footer justify-content-between">
            <button type="button" class="btn btn-default" data-dismiss="modal">Tutup</button>
            <button type="submit" name="btn_edit" class="btn btn-primary">Simpan Perubahan</button>
          </div>
        </form>

      </div>
    </div>
  </div>

  <?php include '../footer.php' ?>
</div>

<?php include '../script.php' ?>

<script>
  // Otomatis isi Harga Beli & Harga Jual saat barang dipilih (Modal Tambah)
  $('#tambah_kode_barang').on('change', function() {
    var hargaBeli = $(this).find(':selected').data('harga-beli') || 0;
    var hargaJual = $(this).find(':selected').data('harga-jual') || 0;
    $('#harga_beli').val(hargaBeli);
    $('#harga_jual').val(hargaJual);
  });

  // Otomatis isi Harga Beli & Harga Jual saat barang diganti (Modal Edit)
  $('#edit_kode_barang').on('change', function() {
    var hargaBeli = $(this).find(':selected').data('harga-beli') || 0;
    var hargaJual = $(this).find(':selected').data('harga-jual') || 0;
    $('#edit_harga_beli').val(hargaBeli);
    $('#edit_harga_jual').val(hargaJual);
  });

  // Memasukkan data dari tabel ke Modal Edit saat tombol Edit diklik
  $('#modal-edit').on('show.bs.modal', function(e) {
    var button = $(e.relatedTarget);
    $(this).find('#edit_id_detail').val(button.data('id_detail'));
    $(this).find('#edit_kode_nota_konsinyasi').val(button.data('kode_nota_konsinyasi'));
    $(this).find('#edit_kode_barang').val(button.data('kode_barang'));
    $(this).find('#edit_jumlah').val(button.data('jumlah'));
    $(this).find('#edit_terjual').val(button.data('terjual'));
    $(this).find('#edit_harga_beli').val(button.data('harga_beli'));
    $(this).find('#edit_harga_jual').val(button.data('harga_jual'));
  });
</script>

</body>
</html>