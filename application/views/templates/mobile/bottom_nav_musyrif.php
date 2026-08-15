<?php
$seg1 = strtolower($this->uri->segment(1));
$seg2 = strtolower($this->uri->segment(2));

if (!class_exists('CI_Model', false)) {
  require_once BASEPATH . 'core/Model.php';
}
require_once APPPATH . 'models/MenuSidebar_M.php';
$menu_model_bottom_nav = new MenuSidebar_M();
?>
<nav class="m-bottom-nav">
  <?php if ($menu_model_bottom_nav->isUrlAktif('Halaman_Musyrif/setoran_oleh_Musyrif', 'musyrif') && $menu_model_bottom_nav->isTampilMenuBawah('Halaman_Musyrif/setoran_oleh_Musyrif', 'musyrif')) : ?>
    <a href="<?= base_url('Halaman_Musyrif/setoran_oleh_Musyrif'); ?>" class="<?= $seg2 === 'setoran_oleh_musyrif' ? 'active' : ''; ?>">
      <i class="fas fa-address-card"></i>
      <span>Setoran</span>
    </a>
  <?php endif; ?>
  <?php if ($menu_model_bottom_nav->isUrlAktif('absen', 'musyrif') && $menu_model_bottom_nav->isTampilMenuBawah('absen', 'musyrif')) : ?>
    <a href="<?= base_url('absen'); ?>" class="<?= $seg1 === 'absen' ? 'active' : ''; ?>">
      <i class="fas fa-clipboard-check"></i>
      <span>Absen</span>
    </a>
  <?php endif; ?>
</nav>
