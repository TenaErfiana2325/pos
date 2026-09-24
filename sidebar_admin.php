<nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
          <li class="nav-item">
            <a href="../home_superadmin/" class="nav-link <?php if ($hal == 'admin') {
                echo 'active';
            } ?>
            ">
              <i class="nav-icon fas fa-chart-pie text-warning"></i>
              <p>Beranda</p>
            </a>
          </li>
           <li class="nav-item">
            <a href="../data_user/" class="nav-link <?= $aktif = ($hal == 'pengguna') ? 'active' : '' ?>"> 
              <i class="nav-icon fas fa-users-cog text-info"></i>
              <p>Data User</p>
            </a>
          </li>
           <li class="nav-item">
            <a href="../supplier_superadmin/" class="nav-link <?= $aktif = ($hal == 'supplier') ? 'active' : '' ?>"> 
              <i class="nav-icon fas fa-truck-loading text-success"></i>
              <p>Data Supplier</p>
            </a>
          </li>
          <li class="nav-item">
            <a href="../barang_superadmin/" class="nav-link <?= $aktif = ($hal == 'barang') ? 'active' : '' ?>"> 
              <i class="nav-icon fas fa-cubes text-primary"></i>
              <p>Data Barang</p>
            </a>
          </li>
          <li class="nav-item">
            <a href="../nota_beli_superadmin/" class="nav-link <?= $aktif = ($hal == 'nota_beli') ? 'active' : '' ?>"> 
              <i class="nav-icon fas fa-file-invoice-dollar text-danger"></i>
              <p>Nota Beli</p>
            </a>
          </li>
           <li class="nav-item">
            <a href="../nota_jual_superadmin/" class="nav-link <?= $aktif = ($hal == 'nota_jual') ? 'active' : '' ?>"> 
              <i class="nav-icon fas fa-receipt text-success"></i>
              <p>Nota Jual</p>
            </a>
          </li>
            <li class="nav-item">
            <a href="../barang_konsinyasi/" class="nav-link <?= $aktif = ($hal == 'barang_konsinyasi') ? 'active' : '' ?>"> 
              <i class="nav-icon fas fa-handshake text-warning"></i>
              <p>Data Barang Konsinyasi</p>
            </a>
          </li>
            <li class="nav-item">
            <a href="../nota_konsinyasi/" class="nav-link <?= $aktif = ($hal == 'nota_konsinyasi') ? 'active' : '' ?>"> 
              <i class="nav-icon fas fa-clipboard-list text-info"></i>
              <p>Nota Konsinyasi</p>
            </a>
          </li>
           <li class="nav-item">
            <a href="../laporan_penjualan/" class="nav-link <?= $aktif = ($hal == 'lap_penjualan') ? 'active' : '' ?>"> 
              <i class="nav-icon fas fa-chart-line text-purple"></i>
              <p>Laporan Penjualan</p>
            </a>
          </li>
           <li class="nav-item">
            <a href="../laporan_pembelian/" class="nav-link <?= $aktif = ($hal == 'lap_pembelian') ? 'active' : '' ?>"> 
              <i class="nav-icon fas fa-chart-bar text-warning"></i>
              <p>Laporan Pembelian</p>
            </a>
          </li>
          <li class="nav-item">
            <a href="../laporan_laba_rugi/" class="nav-link <?= $aktif = ($hal == 'lap_laba_rugi') ? 'active' : '' ?>"> 
              <i class="nav-icon fas fa-chart-bar text-danger"></i>
              <p>Laporan Laba Rugi</p>
            </a>
          </li>
            <li class="nav-item">
            <a href="../ganti_pass_superadmin/" class="nav-link <?= $aktif = ($hal == 'ganti_password')? 'active':'' ?>">
              <i class="nav-icon fas fa-key text-pink"></i>
              <p>Ganti Password</p>
            </a>
          </li>
           <li class="nav-item">
            <a href="../logout.php" class="nav-link">
              <i class="nav-icon fas fa-power-off text-danger"></i>
              <p>Logout</p>
            </a>
          </li>
        </ul>
      </nav>