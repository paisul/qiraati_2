<?php
// Drawer navigasi mobile untuk Admin/Musyrif/Bagian Administrasi/Wali - dipakai lewat header_generic.php
// pada semua halaman yang belum (atau tidak perlu) wrapper mobile khusus per-role. Sumber datanya SAMA
// dengan sidebar desktop (MenuSidebar_M::getMenuAktifUntukSidebar()) - satu sumber kebenaran menu, cuma
// beda cara render (accordion vertikal, bukan treeview sidebar).
if (!class_exists('CI_Model', false)) {
  require_once BASEPATH . 'core/Model.php';
}
require_once APPPATH . 'models/MenuSidebar_M.php';
$menu_model_drawer = new MenuSidebar_M();

$level_drawer = $this->session->userdata('level');
$grup_drawer = 'admin';
$dashboard_url_drawer = 'admin';
$dashboard_label_drawer = 'Dashboard';

if ($level_drawer === 'Bagian Administrasi') {
  $grup_drawer = 'admin';
  $dashboard_url_drawer = 'Administrasi';
  $dashboard_label_drawer = 'Dashboard Administrasi';
} elseif ($level_drawer === 'Musyrif' || $level_drawer === 'Guru') {
  $grup_drawer = 'musyrif';
  $dashboard_url_drawer = 'Halaman_Musyrif/index';
  $dashboard_label_drawer = 'Beranda';
} elseif ($level_drawer === 'Wali') {
  $grup_drawer = 'wali';
  $dashboard_url_drawer = 'Wali/index';
  $dashboard_label_drawer = 'Beranda';
}

$menu_drawer = $menu_model_drawer->getMenuAktifUntukSidebar($grup_drawer);

// Profil kecil di kepala drawer - dicari sendiri di sini (bukan mengandalkan $user dari
// controller pemanggil) karena bentuk $user beda-beda per controller/role, sementara file ini
// dipakai bersama oleh semuanya. Wali tidak lewat sini sama sekali (lihat header_wali.php).
$nama_drawer = $level_drawer === 'Bagian Administrasi' ? 'Bagian Administrasi' : $level_drawer;
$foto_drawer = null;
$inisial_drawer = strtoupper(substr($nama_drawer, 0, 1));

if ($level_drawer === 'Musyrif' || $level_drawer === 'Guru') {
  $this->load->model('Musyrif_M');
  $data_musyrif_drawer = $this->Musyrif_M->getDataMusyrif($this->session->userdata('username'));
  if ($data_musyrif_drawer && !empty($data_musyrif_drawer['NamaMusyrif'])) {
    $nama_drawer = $data_musyrif_drawer['NamaMusyrif'];
    $foto_drawer = $data_musyrif_drawer['Pasfoto'] ?? null;
    $inisial_drawer = strtoupper(substr($nama_drawer, 0, 1));
  }
}
?>
<div class="m-drawer-overlay" id="mDrawerOverlay"></div>
<nav class="m-drawer" id="mDrawer">
  <div class="m-drawer-header">
    <button type="button" id="mDrawerClose"><i class="fas fa-times"></i></button>
    <div class="m-drawer-profil">
      <?php if (!empty($foto_drawer)) : ?>
        <img src="<?= upload_url('pasfoto_musyrif', $foto_drawer); ?>" class="m-drawer-avatar" alt="<?= html_escape($nama_drawer); ?>">
      <?php else : ?>
        <div class="m-drawer-avatar m-drawer-avatar-fallback"><?= html_escape($inisial_drawer); ?></div>
      <?php endif; ?>
      <div class="m-drawer-profil-teks">
        <div class="m-drawer-nama"><?= html_escape($nama_drawer); ?></div>
        <span class="m-drawer-peran"><?= html_escape($level_drawer); ?></span>
      </div>
    </div>
  </div>
  <div class="m-drawer-body">
    <a href="<?= base_url($dashboard_url_drawer); ?>" class="m-drawer-link">
      <span class="m-drawer-icon"><i class="fas fa-home"></i></span> <?= html_escape($dashboard_label_drawer); ?>
    </a>

    <?php foreach ($menu_drawer as $m) :
      if ($m['HanyaAdmin'] && $level_drawer !== 'Admin') {
        continue;
      }

      $ada_anak = !empty($m['anak']);
      if (!$ada_anak && $m['Url'] === null) {
        continue;
      }
    ?>
      <?php if ($ada_anak) : ?>
        <button type="button" class="m-drawer-parent" data-toggle-target="#mDrawerGroup<?= html_escape($m['KunciMenu']); ?>">
          <span><span class="m-drawer-icon"><i class="<?= html_escape($m['Icon']); ?>"></i></span> <?= html_escape($m['Label']); ?></span>
          <i class="fas fa-chevron-down m-drawer-chevron"></i>
        </button>
        <div class="m-drawer-group" id="mDrawerGroup<?= html_escape($m['KunciMenu']); ?>" hidden>
          <?php foreach ($m['anak'] as $a) :
            if ($a['HanyaAdmin'] && $level_drawer !== 'Admin') {
              continue;
            }
          ?>
            <a href="<?= base_url($a['Url']); ?>" class="m-drawer-link m-drawer-link-child">
              <span class="m-drawer-icon m-drawer-icon-sm"><i class="<?= html_escape($a['Icon']); ?>"></i></span> <?= html_escape($a['Label']); ?>
            </a>
            <?php if ($a['KunciMenu'] === 'pengaturan_aplikasi' && $level_drawer === 'Admin') : ?>
              <a href="<?= base_url('pengaturansidebar'); ?>" class="m-drawer-link m-drawer-link-child">
                <span class="m-drawer-icon m-drawer-icon-sm"><i class="fas fa-bars"></i></span> Sidebar
              </a>
            <?php endif; ?>
          <?php endforeach; ?>
        </div>
      <?php else : ?>
        <a href="<?= base_url($m['Url']); ?>" class="m-drawer-link">
          <span class="m-drawer-icon"><i class="<?= html_escape($m['Icon']); ?>"></i></span> <?= html_escape($m['Label']); ?>
        </a>
      <?php endif; ?>
    <?php endforeach; ?>

    <a href="<?= base_url('auth/logout'); ?>" class="m-drawer-link m-drawer-logout">
      <span class="m-drawer-icon"><i class="fas fa-sign-out-alt"></i></span> Logout
    </a>
  </div>
</nav>
