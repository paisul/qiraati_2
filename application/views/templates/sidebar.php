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
      <!-- Sidebar Menu -->
      <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar nav-child-indent nav-flat flex-column" data-widget="treeview" role="menu" data-accordion="false">
          <?php
          $url = $this->uri->segment(1);
          $subUrl = $this->uri->segment(2);

          // "Dashboard" dan "Pengaturan Sidebar" sengaja bukan dari DB (lihat MenuSidebar_M) -
          // supaya admin tidak pernah bisa mengunci diri sendiri dari kedua halaman itu.
          // require_once langsung (bukan $this->load->model()) karena $this di view ini adalah
          // instance Loader - model yang di-load lewat $this->load->model() di sini nempel ke
          // objek CI utama, bukan ke $this di scope ini (lihat pola sama utk $jumlah_menunggu di bawah).
          // CI_Model sendiri baru ter-load kalau ADA controller yang sempat panggil $this->load->model()
          // di request ini - kalau controllernya (mis. PengaturanMigrasi) tidak load model apa pun,
          // "extends CI_Model" di bawah akan fatal karena class induknya belum ada. Muat manual dulu.
          if (!class_exists('CI_Model', false)) {
            require_once BASEPATH . 'core/Model.php';
          }
          require_once APPPATH . 'models/MenuSidebar_M.php';
          $menu_model = new MenuSidebar_M();
          $menu_atas = $menu_model->getMenuAktifUntukSidebar('admin');

          $jumlah_menunggu = $this->db->table_exists('pendaftaran_santri')
            ? $this->db->where('StatusPendaftaran', 'Menunggu')->count_all_results('pendaftaran_santri')
            : 0;

          // Kumpulkan segmen-1 yang punya sibling url 2-segmen (mis. "santri" krn ada "santri/pendaftaran"),
          // supaya item 1-segmen itu (Data Santri) tidak ikut aktif saat di /santri/pendaftaran.
          $punya_sibling_spesifik = [];
          foreach ($menu_atas as $m) {
            $daftar_url = array_merge([$m['Url']], array_column($m['anak'], 'Url'));
            foreach ($daftar_url as $u) {
              if ($u && strpos($u, '/') !== false) {
                $punya_sibling_spesifik[explode('/', $u)[0]] = true;
              }
            }
          }

          $cek_aktif = function ($item_url) use ($url, $subUrl, $punya_sibling_spesifik) {
            if ($item_url === null) {
              return false;
            }
            if (strpos($item_url, '/') !== false) {
              list($seg1, $seg2) = explode('/', $item_url, 2);
              return $url === $seg1 && $subUrl === $seg2;
            }
            if ($url !== $item_url) {
              return false;
            }
            return !(isset($punya_sibling_spesifik[$item_url]) && $subUrl !== '');
          };
          ?>

          <?php if ($user['level'] == 'Bagian Administrasi') : ?>
            <li class="nav-item mt-2">
              <a href="<?= base_url('Administrasi'); ?>" class="nav-link <?= $url == "Administrasi" ? 'active' : ''; ?>">
                <i class="nav-icon fas fa-fw fa-home" style="color: white;"></i>
                <p style="color: white;">Dashboard Administrasi</p>
              </a>
            </li>
          <?php else : ?>
            <li class="nav-item mt-2">
              <a href="<?= base_url('admin'); ?>" class="nav-link <?= $url == "admin" ? 'active' : ''; ?>">
                <i class="nav-icon fas fa-fw fa-home" style="color: white;"></i>
                <p style="color: white;">Dashboard</p>
              </a>
            </li>
          <?php endif; ?>

          <?php foreach ($menu_atas as $m) :
            if ($m['HanyaAdmin'] && $user['level'] != 'Admin') {
              continue;
            }

            $ada_anak = !empty($m['anak']);
            // Induk tanpa Url sendiri (murni pembungkus submenu) & semua anaknya nonaktif/tidak terlihat -> sembunyikan.
            if (!$ada_anak && $m['Url'] === null) {
              continue;
            }

            $anak_segmen1 = array_unique(array_filter(array_map(function ($a) {
              return $a['Url'] ? explode('/', $a['Url'])[0] : null;
            }, $m['anak'])));
            // "Sidebar" bukan dari DB (lihat komentar di atas) - tambahkan manual supaya induk
            // "Pengaturan" ikut terbuka saat sedang di halaman /pengaturansidebar.
            if ($m['KunciMenu'] === 'pengaturan') {
              $anak_segmen1[] = 'pengaturansidebar';
            }
            $induk_terbuka = $ada_anak && (in_array($url, $anak_segmen1, true) || $cek_aktif($m['Url']));
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
                  <?php foreach ($m['anak'] as $a) :
                    if ($a['HanyaAdmin'] && $user['level'] != 'Admin') {
                      continue;
                    }
                    $aktif_anak = $cek_aktif($a['Url']);
                  ?>
                    <li class="nav-item nav-child-indent">
                      <a href="<?= base_url($a['Url']); ?>" class="nav-link <?= $aktif_anak ? 'active bg-primary' : ''; ?>">
                        <i class="<?= html_escape($a['Icon']); ?> nav-icon" style="color: white;"></i>
                        <p style="color: white;">
                          <?= html_escape($a['Label']); ?>
                          <?php if ($a['KunciMenu'] === 'santri_pendaftaran' && $jumlah_menunggu > 0) : ?>
                            <span class="badge badge-danger right"><?= $jumlah_menunggu; ?></span>
                          <?php endif; ?>
                        </p>
                      </a>
                    </li>
                    <?php if ($a['KunciMenu'] === 'pengaturan_aplikasi' && $user['level'] == 'Admin') : ?>
                      <li class="nav-item nav-child-indent">
                        <a href="<?= base_url('pengaturansidebar'); ?>" class="nav-link <?= $url == 'pengaturansidebar' ? 'active bg-primary' : ''; ?>">
                          <i class="fas fa-fw fa-bars nav-icon" style="color: white;"></i>
                          <p style="color: white;">Sidebar</p>
                        </a>
                      </li>
                    <?php endif; ?>
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
