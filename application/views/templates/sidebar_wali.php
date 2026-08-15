  <!-- Main Sidebar Container -->
  <aside class="main-sidebar sidebar-light-primary elevation-4" style="background-color: #172a3a;">
    <!-- Brand Logo -->
    <?php $app = ambil_pengaturan_aplikasi($this->db); ?>
    <a href="#" class="brand-link" style="background-color: #172a3a;">
      <img src="<?= logo_url($app); ?>" alt="Logo" class="brand-image img-circle ">
      <span class="brand-text font-weight-bold" style="color: white;"><?= html_escape($app['NamaAplikasi']); ?></span>
    </a>

    <!-- Sidebar -->
    <!-- atur warna backgound sidebar -->
    <!-- <div class="sidebar bg-navy"> -->
    <div class="sidebar" style="color: white;">
      <!-- Sidebar user panel: profil santri (foto, nama, NIS, jilid) -->
      <div class="user-panel mt-3 pb-2 mb-2 text-center">
        <?php if (!empty($user['Pasfoto'])) : ?>
          <img src="<?= upload_url('santri', $user['Pasfoto']); ?>" style="display:block; margin:0 auto; width:100px; height:100px; border-radius:50%; object-fit: cover; border: 3px solid #fff;">
        <?php else : ?>
          <div style="width:100px;height:100px;border-radius:50%;background:#26d0ce;color:#fff;display:flex;align-items:center;justify-content:center;font-weight:bold;font-size:36px;margin:0 auto;border:3px solid #fff;">
            <?= strtoupper(substr($user['NamaLengkap'], 0, 1)); ?>
          </div>
        <?php endif; ?>
        <div class="info text-center mt-2">
          <span class="d-block" style="color: white; font-size: 18px;"><?= html_escape($user['NamaLengkap']); ?></span>
          <span class="d-block" style="color: white; font-size: 13px;">NIS: <?= html_escape($user['NIS']); ?></span>
          <span class="d-block" style="color: white; font-size: 13px;"><?= html_escape($user['NamaKelas']); ?></span>
        </div>

        <?php if (!empty($user['daftar_anak']) && count($user['daftar_anak']) > 1) : ?>
          <div class="mt-2 px-3">
            <button class="btn btn-sm btn-block" type="button" data-toggle="collapse" data-target="#daftarAnakWali" style="background-color:#26d0ce; color:#fff;">
              <i class="fas fa-fw fa-exchange-alt"></i> Ganti Anak
            </button>
            <div class="collapse mt-1" id="daftarAnakWali">
              <?php foreach ($user['daftar_anak'] as $anak) : ?>
                <a href="<?= base_url('Wali/pilih_anak/' . $anak['IdSiswa']); ?>" class="d-block text-left py-1 px-2 mb-1 rounded <?= $anak['IdSiswa'] == $user['IdSiswa'] ? 'font-weight-bold' : ''; ?>" style="background-color:<?= $anak['IdSiswa'] == $user['IdSiswa'] ? '#26d0ce' : 'rgba(255,255,255,0.1)'; ?>; color:#fff;">
                  <?= html_escape($anak['NamaLengkap']); ?> <small>(<?= html_escape($anak['NamaKelas']); ?>)</small>
                </a>
              <?php endforeach; ?>
            </div>
          </div>
        <?php endif; ?>
      </div>

      <!-- Sidebar Menu -->
      <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar nav-child-indent nav-flat flex-column" data-widget="treeview" role="menu" data-accordion="false">
          <?php
          $url = $this->uri->segment(1);
          $url2 = $this->uri->segment(2);

          // "Dashboard Wali" sengaja bukan dari DB (lihat pastikan_seed_menu_sidebar_wali()) -
          // supaya tidak ada yang bisa terkunci dari halaman utamanya sendiri.
          // require_once langsung (bukan $this->load->model()) karena $this di view ini adalah
          // instance Loader - lihat komentar sama di templates/sidebar.php.
          if (!class_exists('CI_Model', false)) {
            require_once BASEPATH . 'core/Model.php';
          }
          require_once APPPATH . 'models/MenuSidebar_M.php';
          $menu_model = new MenuSidebar_M();
          $menu_atas = $menu_model->getMenuAktifUntukSidebar('wali');

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

          <!-- Dashboard Wali -->
          <li class="nav-item mt-2">
            <a href="<?= base_url('Wali/index'); ?>" class="nav-link <?= $url2 == "index" ? 'active' : ''; ?>">
              <i class="nav-icon fas fa-fw fa-home" style="color: white;"></i>
              <p style="color: white;">Dashboard Wali</p>
            </a>
          </li>

          <?php foreach ($menu_atas as $m) : ?>
            <li class="nav-item mt-2">
              <a href="<?= base_url($m['Url']); ?>" class="nav-link <?= $cek_aktif($m['Url']) ? 'active' : ''; ?>">
                <i class="nav-icon <?= html_escape($m['Icon']); ?>" style="color: white;"></i>
                <p style="color: white;"><?= html_escape($m['Label']); ?></p>
              </a>
            </li>
          <?php endforeach; ?>
        </ul>
      </nav>
      <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
  </aside>
