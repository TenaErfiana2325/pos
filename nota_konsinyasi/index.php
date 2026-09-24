<?php
require_once '../database/koneksi.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <?php 
  include '../css.php';
  $hal ='nota_konsinyasi';
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
            <h3 class="card-title">Data Nota Konsinyasi</h3>
          </div>

          <div class="card-body">

            <button type="button" class="btn btn-primary mb-2" data-toggle="modal" data-target="#modal-tambah">
              <i class="fas fa-plus"></i><b> Tambah Data</b>
            </button>
              

            <?php
            $pengguna = $_SESSION['username'] ?? '';
            ?>

            <table id="example1" class="table table-bordered table-striped">

              <thead>
                <tr>
                  <th width="5%">No</th>
                  <th>kode_nota_konsinyasi</th>
                  <th>nama_supplier</th>
                  <th>tgl_titip</th>
                  <th>total_item</th>
                  <th width="15%">Aksi</th>
                </tr>
              </thead>

              <tbody>

              <?php 
              // Tanpa JOIN, relasi diambil lewat klausa WHERE
              $panggil_data_user = mysqli_query($con, "SELECT tbl_nota_konsinyasi.*, tbl_supplier.nama_supplier FROM tbl_nota_konsinyasi, tbl_supplier WHERE tbl_nota_konsinyasi.kode_supplier = tbl_supplier.kode_supplier") or die(mysqli_error($con));

              $no = 1;
              $rv = mysqli_num_rows($panggil_data_user);

              if ($rv > 0) {

                while ($data = mysqli_fetch_array($panggil_data_user)) {

                  $kode_nota_konsinyasi = $data['kode_nota_konsinyasi'];
                  $kode_supplier = $data['kode_supplier'];
                  $nama_supplier = $data['nama_supplier'];
                  $tgl_titip = $data['tgl_titip'];
                  $total_item = $data['total_item'];
              ?>

              <tr>

                <td><?= $no++ ?></td>

                <td><?= $kode_nota_konsinyasi ?></td>

                <td><?= $nama_supplier ?></td>

                <td><?= $tgl_titip ?></td>

                <td><?= $total_item ?></td>

                <td>

                  <button type="button" class="btn btn-primary btn-sm"
                    data-toggle="modal"
                    data-target="#modal-edit"

                    data-kode_nota_konsinyasi="<?= $kode_nota_konsinyasi; ?>"
                    data-kode_supplier="<?= $kode_supplier; ?>"
                    data-tgl_titip="<?= $tgl_titip; ?>"
                    data-total_item="<?= $total_item; ?>">

                    <i class="fas fa-edit"></i>
                  </button>

                  <a href='hapus.php?kode_nota_konsinyasi=<?= $data['kode_nota_konsinyasi'];?>'
                     class='btn btn-danger btn-sm'
                     onclick="return confirm('Yakin ingin hapus ini?')">

                    <i class="fas fa-trash"></i>

                  </a>

                  <a href="../detail_nota_konsinyasi/index.php?kode_nota_konsinyasi=<?= $data['kode_nota_konsinyasi'];?>" 
                     class="btn btn-info btn-sm">
                    <i class="fas fa-file-alt"></i>
                  </a>

                </td>

              </tr>

              <?php
                }

              } else {

                echo '<tr><td colspan="6" class="text-center">Data Tidak Ditemukan</td></tr>';

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

          <h4 class="modal-title">Tambah Data Nota Konsinyasi</h4>

          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>

        </div>

        <form action="tambah.php" method="post">

          <div class="modal-body">

            <div class="form-group">
              <label for="kode_nota_konsinyasi">kode_nota_konsinyasi</label>
              <input type="text" name="kode_nota_konsinyasi" class="form-control"
                     id="kode_nota_konsinyasi" placeholder="Masukan kode_nota_konsinyasi" required>
            </div>

            <div class="form-group">
              <label for="kode_supplier">Supplier</label>
              <select name="kode_supplier" id="kode_supplier" class="form-control" required>
                <option value="">-- Pilih Supplier --</option>
                <?php
                $q_sup = mysqli_query($con, "SELECT * FROM tbl_supplier") or die(mysqli_error($con));
                while ($d_sup = mysqli_fetch_array($q_sup)) {
                  echo '<option value="' . $d_sup['kode_supplier'] . '">' . $d_sup['kode_supplier'] . ' - ' . $d_sup['nama_supplier'] . '</option>';
                }
                ?>
              </select>
            </div>

            <div class="form-group">
              <label for="tgl_titip">tgl_titip</label>
              <input type="date" name="tgl_titip" class="form-control"
                     id="tgl_titip" required>
            </div>

            <div class="form-group">
              <label for="total_item">total_item</label>
              <input type="number" name="total_item" class="form-control"
                     id="total_item" placeholder="Masukan total item" required>
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

          <h4 class="modal-title">Edit Data Nota Konsinyasi</h4>

          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>

        </div>

        <form action="ubah.php" method="post">

          <div class="modal-body">

            <div class="form-group">
              <label for="kode_nota_konsinyasi">kode_nota_konsinyasi</label>
              <input type="text" name="kode_nota_konsinyasi" class="form-control"
                     id="kode_nota_konsinyasi" readonly required>
            </div>

            <div class="form-group">
              <label for="kode_supplier">Supplier</label>
              <select name="kode_supplier" id="kode_supplier" class="form-control" required>
                <option value="">-- Pilih Supplier --</option>
                <?php
                $q_sup_edit = mysqli_query($con, "SELECT * FROM tbl_supplier") or die(mysqli_error($con));
                while ($d_sup = mysqli_fetch_array($q_sup_edit)) {
                  echo '<option value="' . $d_sup['kode_supplier'] . '">' . $d_sup['kode_supplier'] . ' - ' . $d_sup['nama_supplier'] . '</option>';
                }
                ?>
              </select>
            </div>

            <div class="form-group">
              <label for="tgl_titip">tgl_titip</label>
              <input type="date" name="tgl_titip" class="form-control"
                     id="tgl_titip" required>
            </div>

            <div class="form-group">
              <label for="total_item">total_item</label>
              <input type="number" name="total_item" class="form-control"
                     id="total_item" placeholder="Masukan total item" required>
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

  $('#modal-edit').on('show.bs.modal', function(e) {

    var kode_nota_konsinyasi = $(e.relatedTarget).data('kode_nota_konsinyasi');
    var kode_supplier = $(e.relatedTarget).data('kode_supplier');
    var tgl_titip = $(e.relatedTarget).data('tgl_titip');
    var total_item = $(e.relatedTarget).data('total_item');

    $(e.currentTarget).find('input[name="kode_nota_konsinyasi"]').val(kode_nota_konsinyasi);
    $(e.currentTarget).find('select[name="kode_supplier"]').val(kode_supplier);
    $(e.currentTarget).find('input[name="tgl_titip"]').val(tgl_titip);
    $(e.currentTarget).find('input[name="total_item"]').val(total_item);

  })

</script>

</body>
</html>