<?php
$inisial = strtoupper(substr($user['NamaMusyrif'] ?? $user['Email'] ?? '?', 0, 1));
?>
<div class="m-content">
  <p class="m-page-title">Profil Saya</p>

  <?php if (empty($user['IdMusyrif'])) : ?>
    <div class="m-card" style="border: 1px solid #ffe8a1; background: #fff3cd; color: #856404;">
      Profil pembimbing Anda belum tertaut. Silakan hubungi admin.
    </div>
  <?php else : ?>
    <div class="m-card m-profil-hero">
      <a href="<?= base_url('Halaman_Musyrif/edit_profil'); ?>" class="m-profil-hero-edit" aria-label="Ubah Profil"><i class="fas fa-pen"></i></a>
      <?php if (!empty($user['Pasfoto'])) : ?>
        <img src="<?= upload_url('pasfoto_musyrif', $user['Pasfoto']); ?>" alt="Pasfoto" data-fallback-class="m-profil-hero-fallback" data-fallback-text="<?= html_escape($inisial); ?>" onerror="mFotoGagalMuat(this)">
      <?php else : ?>
        <div class="m-profil-hero-fallback"><?= $inisial; ?></div>
      <?php endif; ?>
      <div class="m-profil-hero-nama"><?= html_escape($user['NamaMusyrif']); ?></div>
      <div class="m-profil-hero-sub"><?= html_escape($user['Email']); ?></div>
      <span class="m-profil-hero-peran">Pembimbing</span>
    </div>

    <div class="m-card">
      <p class="m-profil-section-title"><i class="fas fa-id-card"></i> Data Diri</p>
      <div class="m-list-item"><span>Nama Lengkap</span><strong><?= html_escape($user['NamaMusyrif']); ?></strong></div>
      <div class="m-list-item"><span>Email</span><strong><?= html_escape($user['Email']); ?></strong></div>
      <div class="m-list-item"><span>No Handphone</span><strong><?= html_escape($user['NoHp'] ?: '-'); ?></strong></div>
    </div>

    <div class="m-card">
      <p class="m-profil-section-title"><i class="fas fa-signature"></i> Tanda Tangan</p>
      <?php if (!empty($user['Ttd'])) : ?>
        <img src="<?= upload_url('ttd_musyrif', $user['Ttd']); ?>" class="m-field-photo-preview" style="border-radius: 8px;">
      <?php else : ?>
        <p class="m-list-sub" style="margin:0;">Belum ada</p>
      <?php endif; ?>
    </div>

    <a href="<?= base_url('Halaman_Musyrif/edit_profil'); ?>" class="m-btn"><i class="fas fa-pen"></i> Ubah Profil</a>
  <?php endif; ?>
</div>
