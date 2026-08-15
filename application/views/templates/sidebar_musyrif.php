  <!-- Main Sidebar Container -->
  <aside class="main-sidebar sidebar-light-primary elevation-4" style="background-color: #172a3a;">
    <!-- Brand Logo -->
    <?php $app = ambil_pengaturan_aplikasi($this->db); ?>
    <a href="#" class="brand-link" style="background-color: #172a3a;">
      <img src="<?= logo_url($app); ?>" alt="Logo" class="brand-image img-circle ">
      <span class="brand-text font-weight-bold" style="color: white;"><?= html_escape($app['NamaAplikasi']); ?></span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar" style="color: white;">
      <!-- Sidebar user panel: profil pembimbing (foto, nama, email) -->
      <?php
      // Jaga-jaga: akun yang profil musyrif-nya belum lengkap tetap tampil rapi (tidak error),
      // pakai username/level sebagai cadangan kalau NamaMusyrif/Email belum ada.
      $nama_tampil = !empty($user['NamaMusyrif']) ? $user['NamaMusyrif'] : $user['username'];
      $email_tampil = !empty($user['Email']) ? $user['Email'] : $user['username'];
      ?>
      <div class="user-panel mt-3 pb-2 mb-2 text-center">
        <?php if (!empty($user['Pasfoto'])) : ?>
          <img src="<?= upload_url('pasfoto_musyrif', $user['Pasfoto']); ?>" style="display:block; margin:0 auto; width:100px; height:100px; border-radius:50%; object-fit: cover; border: 3px solid #fff;">
        <?php else : ?>
          <div style="width:100px;height:100px;border-radius:50%;background:#26d0ce;color:#fff;display:flex;align-items:center;justify-content:center;font-weight:bold;font-size:36px;margin:0 auto;border:3px solid #fff;">
            <?= strtoupper(substr($nama_tampil, 0, 1)); ?>
          </div>
        <?php endif; ?>
        <div class="info text-center mt-2">
          <span class="d-block" style="color: white; font-size: 18px;"><?= html_escape($nama_tampil); ?></span>
          <span class="d-block" style="color: white; font-size: 13px;"><?= html_escape($email_tampil); ?></span>
        </div>
      </div>

      <!-- Sidebar Menu -->
      <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar nav-child-indent nav-flat flex-column" data-widget="treeview" role="menu" data-accordion="false">
          <?php
          $url = $this->uri->segment(1);
          $url2 = $this->uri->segment(2);

          // "Dashboard Musyrif" sengaja bukan dari DB (lihat pastikan_seed_menu_sidebar_musyrif()) -
          // supaya tidak ada yang bisa terkunci dari halaman utamanya sendiri.
          // require_once langsung (bukan $this->load->model()) karena $this di view ini adalah
          // instance Loader - lihat komentar sama di templates/sidebar.php.
          if (!class_exists('CI_Model', false)) {
            require_once BASEPATH . 'core/Model.php';
          }
          require_once APPPATH . 'models/MenuSidebar_M.php';
          $menu_model = new MenuSidebar_M();
          $menu_atas = $menu_model->getMenuAktifUntukSidebar('musyrif');

          $cek_aktif = function ($item_url) use ($url, $url2) {
            if ($item_url === null) {
              return false;
            }
            if (strpos($item_url, '/') !== false) {
              list($seg1, $seg2) = explode('/', $item_url, 2);
              return $url === $seg1 && $url2 === $seg2;
            }
            return $url === $item_url;
          };
          ?>

          <!-- Dashboard Musyrif -->
          <li class="nav-item mt-2">
            <a href="<?= base_url('Halaman_Musyrif/index'); ?>" class="nav-link <?= $url2 == "index" ? 'active' : ''; ?>">
              <i class="nav-icon fas fa-fw fa-home" style="color: white;"></i>
              <p style="color: white;">Dashboard Musyrif</p>
            </a>
          </li>

          <?php foreach ($menu_atas as $m) :
            $ada_anak = !empty($m['anak']);
            // Induk tanpa Url sendiri (murni pembungkus submenu) & tidak punya anak -> sembunyikan.
            if (!$ada_anak && $m['Url'] === null) {
              continue;
            }

            $induk_terbuka = $ada_anak && $cek_aktif($m['Url']);
            if ($ada_anak && !$induk_terbuka) {
              foreach ($m['anak'] as $a) {
                if ($cek_aktif($a['Url'])) {
                  $induk_terbuka = true;
                  break;
                }
              }
            }
          ?>
            <?php if ($ada_anak) : ?>
              <li class="nav-item mt-2 has-treeview <?= $induk_terbuka ? 'menu-open' : ''; ?>">
                <a href="#" class="nav-link <?= $induk_terbuka ? 'active' : ''; ?>">
                  <i class="nav-icon <?= html_escape($m['Icon']); ?>" style="color: white;"></i>
                  <p style="color: white;">
                    <?= html_escape($m['Label']); ?>
                    <i class="right fas fa-angle-left" style="color: white;"></i>
                  </p>
                </a>
                <ul class="nav nav-treeview">
                  <?php foreach ($m['anak'] as $a) : ?>
                    <li class="nav-item nav-child-indent">
                      <a href="<?= base_url($a['Url']); ?>" class="nav-link <?= $cek_aktif($a['Url']) ? 'active bg-primary' : ''; ?>">
                        <i class="<?= html_escape($a['Icon']); ?> nav-icon" style="color: white;"></i>
                        <p style="color: white;"><?= html_escape($a['Label']); ?></p>
                      </a>
                    </li>
                  <?php endforeach; ?>
                </ul>
              </li>
            <?php else : ?>
              <li class="nav-item mt-2">
                <a href="<?= base_url($m['Url']); ?>" class="nav-link <?= $cek_aktif($m['Url']) ? 'active' : ''; ?>">
                  <i class="nav-icon <?= html_escape($m['Icon']); ?>" style="color: white;"></i>
                  <p style="color: white;"><?= html_escape($m['Label']); ?></p>
                </a>
              </li>
            <?php endif; ?>
          <?php endforeach; ?>
        </ul>
      </nav>
      <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
  </aside>
