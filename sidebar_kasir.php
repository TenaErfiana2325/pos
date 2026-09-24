<nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
          <li class="nav-item">
            <a href="../home_kasir/" class="nav-link <?php if ($hal == 'kasir') {
                echo 'active';
            } ?>
            ">
              <i class="nav-icon fas fa-tachometer-alt"></i>
              <p>Beranda</p>
            </a>
          </li>
            <li class="nav-item">
            <a href="../nota_beli_kasir/" class="nav-link <?= $aktif = ($hal == 'nota_beli_kasir') ? 'active' : '' ?>"> 
              <i class="nav-icon fas fa-receipt text-danger"></i>
              <p>Nota Beli</p>
            </a>
          </li>
            <li class="nav-item">
            <a href="../nota_jual_kasir/" class="nav-link <?= $aktif = ($hal == 'nota_jual_kasir') ? 'active' : '' ?>"> 
              <i class="nav-icon fas fa-receipt text-success"></i>
              <p>Nota Jual</p>
            </a>
          </li>
            <li class="nav-item">
            <a href="../ganti_pass_kasir/" class="nav-link <?= $aktif = ($hal == 'ganti_password')? 'active':'' ?>">
              <i class="nav-icon fas fa-lock"></i>
              <p>Ganti Password</p>
            </a>
          </li>
           <li class="nav-item">
            <a href="../logout.php" class="nav-link">
              <i class="nav-icon fas fa-sign-out-alt"></i>
              <p>Logout</p>
            </a>
          </li>
        </ul>
      </nav>