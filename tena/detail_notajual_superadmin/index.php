<?php
require_once '../database/tugas.php';

// Mengambil parameter kode_nota dari URL
$kode_nota_get = $_GET['kode_nota'] ?? '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <?php 
  include '../css.php';
  $hal = 'beranda_detail_notajual';
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
          <a href="#" class="d-block">Sistem Manajemen</a>
        </div>
      </div>

      <?php include '../sidebar_superadmin.php'; ?>

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
            <h3 class="card-title">Data Detail Nota Jual (Kode Nota: <?= htmlspecialchars($kode_nota_get) ?>)</h3>
          </div>

          <div class="card-body">

            <div class="mb-2">
              <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#modal-tambah">
                <i class="fas fa-plus"></i><b> Tambah Data</b>
              </button>
              <a href="../nota_jual/index.php" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Kembali
              </a>
            </div>

            <table id="example1" class="table table-bordered table-striped">

              <thead>
                <tr>
                  <th width="5%">No</th>
                  <th>Kode Nota</th>
                  <th>Nama Barang</th>
                  <th>Jumlah</th>
                  <th>Harga Jual</th>
                  <th>Total Harga Beli</th>
                  <th width="15%">Aksi</th>
                </tr>
              </thead>

              <tbody>

              <?php 
              // QUERY TANPA JOIN (MENGGUNAKAN WHERE DAN AND)
              $panggil_data_user = mysqli_query($con, "
                SELECT d.*, b.nama_brg 
                FROM tbl_detail_notajual d, tbl_barang b
                WHERE d.kode_brg = b.kode_brg 
                AND d.kode_nota = '$kode_nota_get'
              ") or die(mysqli_error($con));

              $no = 1;
              $rv = mysqli_num_rows($panggil_data_user);

              if ($rv > 0) {

                while ($data = mysqli_fetch_array($panggil_data_user)) {

                  $urut = $data['urut'];
                  $kode_nota = $data['kode_nota'];
                  $kode_brg = $data['kode_brg'];
                  $nama_brg = $data['nama_brg'];
                  $jumlah = $data['jumlah'];
                  $harga_jual = $data['harga_jual'];
                  $total_harga_beli = $data['total_harga_beli'];
              ?>

              <tr>

                <td><?= $no++ ?></td>

                <td><?= $kode_nota ?></td>

                <!-- MENAMPILKAN NAMA BARANG -->
                <td><?= htmlspecialchars($nama_brg) ?></td>

                <td><?= $jumlah ?></td>

                <td><?= $harga_jual ?></td>

                <td><?= $total_harga_beli ?></td>

                <td>

                  <button type="button" class="btn btn-primary btn-sm"
                    data-toggle="modal"
                    data-target="#modal-edit"

                    data-urut="<?= $urut; ?>"
                    data-kode_nota="<?= $kode_nota; ?>"
                    data-kode_brg="<?= $kode_brg; ?>"
                    data-jumlah="<?= $jumlah; ?>"
                    data-harga_jual="<?= $harga_jual; ?>"
                    data-total_harga_beli="<?= $total_harga_beli; ?>">

                    <i class="fas fa-edit"></i>
                  </button>

                  <a href='hapus.php?urut=<?= $data['urut'];?>&kode_nota=<?= $kode_nota; ?>'
                     class='btn btn-danger btn-sm'
                     onclick="return confirm('Yakin ingin hapus ini?')">

                    <i class="fas fa-trash"></i>

                  </a>

                </td>

              </tr>

              <?php
                }

              } else {

                echo '<tr><td colspan="7" class="text-center">Data Tidak Ditemukan</td></tr>';

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

          <h4 class="modal-title">Tambah Detail Nota Jual</h4>

          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>

        </div>

        <form action="tambah.php" method="post">

          <div class="modal-body">

            <div class="form-group">
              <label for="kode_nota">Kode Nota</label>
              <input type="text" name="kode_nota" maxlength="10" class="form-control"
                     value="<?= htmlspecialchars($kode_nota_get) ?>" readonly required>
            </div>

            <!-- Pilih Barang dari Database -->
            <div class="form-group">
              <label for="kode_brg">Pilih Barang</label>
              <select name="kode_brg" id="tambah_kode_brg" class="form-control" required>
                <option value="">-- Pilih Barang --</option>
                <?php
                $q_brg = mysqli_query($con, "SELECT * FROM tbl_barang") or die(mysqli_error($con));
                while ($d_brg = mysqli_fetch_array($q_brg)) {
                  echo '<option value="' . $d_brg['kode_brg'] . '" data-harga="' . ($d_brg['harga_jual'] ?? $d_brg['rata_harga_beli'] ?? 0) . '">' . $d_brg['kode_brg'] . ' - ' . $d_brg['nama_brg'] . '</option>';
                }
                ?>
              </select>
            </div>

            <!-- Jumlah -->
            <div class="form-group">
              <label for="jumlah">Jumlah</label>
              <input type="number" name="jumlah" id="tambah_jumlah" class="form-control" placeholder="Masukkan Jumlah" required>
            </div>

            <div class="form-group">
              <label for="harga_jual">Harga Jual</label>
              <input type="number" name="harga_jual" id="tambah_harga" class="form-control" placeholder="Harga Jual" required>
            </div>

            <!-- Total Harga Beli -->
            <div class="form-group">
              <label for="total_harga_beli">Total Harga Beli</label>
              <input type="number" name="total_harga_beli" id="tambah_total" class="form-control" placeholder="Total Harga" required>
            </div>

          </div>

          <div class="modal-footer justify-content-between">

            <button type="button" class="btn btn-default" data-dismiss="modal">
              Tutup
            </button>

            <button type="submit" name="btn_tambah" class="btn btn-primary">
              Simpan
            </button>

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

          <h4 class="modal-title">Edit Detail Nota Jual</h4>

          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>

        </div>

        <form action="ubah.php" method="post">

          <div class="modal-body">

            <input type="hidden" name="urut" id="edit_urut">

            <div class="form-group">
              <label for="kode_nota">Kode Nota</label>
              <input type="text" name="kode_nota" maxlength="10" class="form-control"
                     id="edit_kode_nota" readonly required>
            </div>

            <div class="form-group">
              <label for="kode_brg">Pilih Barang</label>
              <select name="kode_brg" id="edit_kode_brg" class="form-control" required>
                <option value="">-- Pilih Barang --</option>
                <?php
                $q_brg_edit = mysqli_query($con, "SELECT * FROM tbl_barang") or die(mysqli_error($con));
                while ($d_brg = mysqli_fetch_array($q_brg_edit)) {
                  echo '<option value="' . $d_brg['kode_brg'] . '" data-harga="' . ($d_brg['harga_jual'] ?? $d_brg['rata_harga_beli'] ?? 0) . '">' . $d_brg['kode_brg'] . ' - ' . $d_brg['nama_brg'] . '</option>';
                }
                ?>
              </select>
            </div>

            <div class="form-group">
              <label for="jumlah">Jumlah</label>
              <input type="number" name="jumlah" id="edit_jumlah" class="form-control"
                     placeholder="Masukkan Jumlah" required>
            </div>

            <div class="form-group">
              <label for="harga_jual">Harga Jual</label>
              <input type="number" name="harga_jual" id="edit_harga" class="form-control"
                     placeholder="Harga Jual" required>
            </div>

            <div class="form-group">
              <label for="total_harga_beli">Total Harga Beli</label>
              <input type="number" name="total_harga_beli" id="edit_total" class="form-control"
                     placeholder="Total Harga Beli" required>
            </div>

          </div>

          <div class="modal-footer justify-content-between">

            <button type="button" class="btn btn-default" data-dismiss="modal">
              Tutup
            </button>

            <button type="submit" name="btn_edit" class="btn btn-primary">
              Edit
            </button>

          </div>

        </form>

      </div>
    </div>
  </div>


  <?php include '../footer.php' ?>

</div>

<?php include '../script.php' ?>

<script>
  // Event saat Modal Edit dibuka
  $('#modal-edit').on('show.bs.modal', function(e) {
    var urut = $(e.relatedTarget).data('urut');
    var kode_nota = $(e.relatedTarget).data('kode_nota');
    var kode_brg = $(e.relatedTarget).data('kode_brg');
    var jumlah = $(e.relatedTarget).data('jumlah');
    var harga_jual = $(e.relatedTarget).data('harga_jual');
    var total_harga_beli = $(e.relatedTarget).data('total_harga_beli');

    $(e.currentTarget).find('#edit_urut').val(urut);
    $(e.currentTarget).find('#edit_kode_nota').val(kode_nota);
    $(e.currentTarget).find('#edit_kode_brg').val(kode_brg);
    $(e.currentTarget).find('#edit_jumlah').val(jumlah);
    $(e.currentTarget).find('#edit_harga').val(harga_jual);
    $(e.currentTarget).find('#edit_total').val(total_harga_beli);
  });
</script>

</body>
</html>