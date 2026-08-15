<?php
$seg1 = strtolower($this->uri->segment(1));
$seg2 = strtolower($this->uri->segment(2));

if (!class_exists('CI_Model', false)) {
  require_once BASEPATH . 'core/Model.php';
}
require_once APPPATH . 'models/MenuSidebar_M.php';
$menu_model_bottom_nav = new MenuSidebar_M();

// Beranda (dashboard) sengaja tidak dicek Aktif-nya - selalu tampil, sama seperti drawer_menu.php.
$menu = [
  ['Wali/index', 'fa-home', 'Beranda', 'wali', 'index', null],
  ['Wali/Setoran', 'fa-address-card', 'Setoran', 'wali', 'setoran', 'Wali/Setoran'],
  ['kelas', 'fa-school', 'Kelas', 'kelas', '', 'kelas'],
  ['Wali/Raport', 'fa-file-archive', 'Rapor', 'wali', 'raport', 'Wali/Raport'],
  ['dana', 'fa-wallet', 'Dana', 'dana', '', 'dana'],
  ['Wali/profil', 'fa-user-circle', 'Profil', 'wali', 'profil', 'Wali/profil'],
];
?>
<nav class="m-bottom-nav">
  <?php foreach ($menu as $m) :
    list($url, $icon, $label, $cek_seg1, $cek_seg2, $url_menu) = $m;
    if ($url_menu !== null && (!$menu_model_bottom_nav->isUrlAktif($url_menu, 'wali') || !$menu_model_bottom_nav->isTampilMenuBawah($url_menu, 'wali'))) {
      continue;
    }
    $aktif = $seg1 === $cek_seg1 && ($cek_seg2 === '' || $seg2 === $cek_seg2);
  ?>
    <a href="<?= base_url($url); ?>" class="<?= $aktif ? 'active' : ''; ?>">
      <i class="fas <?= $icon; ?>"></i>
      <span><?= $label; ?></span>
    </a>
  <?php endforeach; ?>
</nav>
