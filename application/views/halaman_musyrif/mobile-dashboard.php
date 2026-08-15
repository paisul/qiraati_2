<?php
if (!class_exists('CI_Model', false)) {
  require_once BASEPATH . 'core/Model.php';
}
require_once APPPATH . 'models/MenuSidebar_M.php';
$menu_model_shortcut = new MenuSidebar_M();
?>
<div class="m-content">

  <!-- Profil pembimbing -->
  <div class="m-card m-profil-anak">
    <?php if (!empty($user['Pasfoto'])) : ?>
      <img src="<?= upload_url('pasfoto_musyrif', $user['Pasfoto']); ?>" alt="<?= html_escape($user['NamaMusyrif']); ?>" data-fallback-class="m-profil-anak-fallback" data-fallback-text="<?= html_escape(strtoupper(substr($user['NamaMusyrif'], 0, 1))); ?>" onerror="mFotoGagalMuat(this)">
    <?php else : ?>
      <div class="m-profil-anak-fallback"><?= strtoupper(substr($user['NamaMusyrif'], 0, 1)); ?></div>
    <?php endif; ?>
    <div>
      <div class="m-profil-anak-nama"><?= html_escape($user['NamaMusyrif']); ?></div>
      <div class="m-profil-anak-sub"><?= html_escape($user['Email']); ?></div>
    </div>
  </div>

  <!-- Pintasan navigasi cepat -->
  <div class="m-shortcut-row">
    <?php if ($menu_model_shortcut->isUrlAktif('Halaman_Musyrif/setoran_oleh_Musyrif', 'musyrif') && $menu_model_shortcut->isTampilDashboard('Halaman_Musyrif/setoran_oleh_Musyrif', 'musyrif')) : ?>
      <a href="<?= base_url('Halaman_Musyrif/setoran_oleh_Musyrif'); ?>" class="m-shortcut">
        <i class="fas fa-address-card"></i>
        <span>Setoran</span>
      </a>
    <?php endif; ?>
    <?php if ($menu_model_shortcut->isUrlAktif('Halaman_Musyrif/rekap_setoran_oleh_Musyrif', 'musyrif') && $menu_model_shortcut->isTampilDashboard('Halaman_Musyrif/rekap_setoran_oleh_Musyrif', 'musyrif')) : ?>
      <a href="<?= base_url('Halaman_Musyrif/rekap_setoran_oleh_Musyrif'); ?>" class="m-shortcut">
        <i class="fas fa-book"></i>
        <span>Rekap</span>
      </a>
    <?php endif; ?>
    <?php if ($menu_model_shortcut->isUrlAktif('absen', 'musyrif') && $menu_model_shortcut->isTampilDashboard('absen', 'musyrif')) : ?>
      <a href="<?= base_url('absen'); ?>" class="m-shortcut">
        <i class="fas fa-clipboard-check"></i>
        <span>Absen</span>
      </a>
    <?php endif; ?>
    <?php if ($menu_model_shortcut->isUrlAktif('Halaman_Musyrif/profil', 'musyrif') && $menu_model_shortcut->isTampilDashboard('Halaman_Musyrif/profil', 'musyrif')) : ?>
      <a href="<?= base_url('Halaman_Musyrif/profil'); ?>" class="m-shortcut">
        <i class="fas fa-user-circle"></i>
        <span>Profil</span>
      </a>
    <?php endif; ?>
  </div>

  <!-- Info penting dari Admin/Musyrif lain -->
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

<script>
  document.addEventListener('DOMContentLoaded', function () {
    cekPengumumanBaru('pengumuman_terakhir_<?= html_escape($this->session->userdata('username')); ?>', <?= !empty($pengumuman) ? (int) $pengumuman[0]['IdPengumuman'] : 0; ?>);
  });
</script>
