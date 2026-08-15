<?php
if (!class_exists('CI_Model', false)) {
  require_once BASEPATH . 'core/Model.php';
}
require_once APPPATH . 'models/MenuSidebar_M.php';
$menu_model_shortcut = new MenuSidebar_M();
?>
<div class="m-content">

  <!-- Profil admin -->
  <div class="m-card m-profil-anak">
    <div class="m-profil-anak-fallback"><?= strtoupper(substr($user['username'], 0, 1)); ?></div>
    <div>
      <div class="m-profil-anak-nama"><?= html_escape($user['level']); ?></div>
      <div class="m-profil-anak-sub"><?= html_escape($user['username']); ?></div>
    </div>
  </div>

  <!-- Pintasan navigasi cepat -->
  <div class="m-shortcut-row">
    <?php if ($menu_model_shortcut->isUrlAktif('santri', 'admin')) : ?>
      <a href="<?= base_url('santri'); ?>" class="m-shortcut">
        <i class="fas fa-user-graduate"></i>
        <span>Santri</span>
      </a>
    <?php endif; ?>
    <?php if ($menu_model_shortcut->isUrlAktif('musyrif', 'admin')) : ?>
      <a href="<?= base_url('musyrif'); ?>" class="m-shortcut">
        <i class="fas fa-chalkboard-teacher"></i>
        <span>Pembimbing</span>
      </a>
    <?php endif; ?>
    <?php if ($menu_model_shortcut->isUrlAktif('kelas', 'admin')) : ?>
      <a href="<?= base_url('kelas'); ?>" class="m-shortcut">
        <i class="fas fa-school"></i>
        <span>Kelas</span>
      </a>
    <?php endif; ?>
    <?php if ($menu_model_shortcut->isUrlAktif('dana', 'admin')) : ?>
      <a href="<?= base_url('dana'); ?>" class="m-shortcut">
        <i class="fas fa-wallet"></i>
        <span>Dana</span>
      </a>
    <?php endif; ?>
  </div>

  <!-- Info penting dari Admin/Musyrif -->
  <p class="m-page-title">Info Penting</p>

  <div class="m-card">
    <?php if ($pengumuman) : ?>
      <?php foreach ($pengumuman as $p) : ?>
        <div class="m-list-item" style="flex-direction: column; align-items: stretch;">
          <div>
            <div class="m-list-title"><i class="fas fa-bullhorn" style="color:var(--teal); margin-right:4px;"></i> <?= html_escape($p['Judul']); ?></div>
            <div class="m-list-sub" style="white-space:pre-line; margin-top:4px;"><?= html_escape($p['Isi']); ?></div>
            <div class="m-list-sub mt-1"><?= html_escape($p['DibuatOleh']); ?> &middot; <?= date('d F Y', strtotime($p['CreatedAt'])); ?></div>
          </div>
        </div>
      <?php endforeach; ?>
    <?php else : ?>
      <p class="m-empty">Belum ada info penting.</p>
    <?php endif; ?>
  </div>
</div>
