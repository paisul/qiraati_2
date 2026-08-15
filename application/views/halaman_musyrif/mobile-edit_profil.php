<?php
$inisial = strtoupper(substr($user['NamaMusyrif'] ?? $user['Email'] ?? '?', 0, 1));
?>
<div class="m-content m-content-pad-bottombar">
  <div style="display:flex; align-items:center; gap:10px; margin-bottom:4px;">
    <a href="<?= base_url('Halaman_Musyrif/profil'); ?>" style="color:var(--heading); font-size:16px;"><i class="fas fa-arrow-left"></i></a>
    <p class="m-page-title" style="margin:0;">Ubah Profil</p>
  </div>

  <?php if (validation_errors()) : ?>
    <div class="m-card" style="border: 1px solid #f5c6cb; background: #f8d7da; color: #721c24;">
      <?= validation_errors(); ?>
    </div>
  <?php endif; ?>

  <?= form_open_multipart('Halaman_Musyrif/simpan_profil'); ?>

  <div class="m-card m-profil-hero">
    <label class="m-photo-upload" for="inputPasfotoProfil">
      <?php if (!empty($user['Pasfoto'])) : ?>
        <img src="<?= upload_url('pasfoto_musyrif', $user['Pasfoto']); ?>" alt="Pasfoto" id="previewPasfotoProfil" data-fallback-class="m-profil-hero-fallback" data-fallback-text="<?= html_escape($inisial); ?>" onerror="mFotoGagalMuat(this)">
      <?php else : ?>
        <div class="m-profil-hero-fallback" id="previewPasfotoProfil"><?= $inisial; ?></div>
      <?php endif; ?>
      <span class="m-photo-upload-badge"><i class="fas fa-camera"></i></span>
      <input type="file" name="pasfoto" id="inputPasfotoProfil" accept=".jpg,.jpeg,.png" data-photo-input="#previewPasfotoProfil" hidden>
    </label>
    <div class="m-profil-hero-nama"><?= html_escape($user['NamaMusyrif']); ?></div>
    <span class="m-profil-hero-peran">Pembimbing</span>
  </div>

  <div class="m-card">
    <p class="m-profil-section-title"><i class="fas fa-id-card"></i> Data Diri</p>

    <div class="m-field">
      <label>Nama Lengkap *</label>
      <input type="text" name="nama_musyrif" value="<?= html_escape($user['NamaMusyrif']); ?>" required>
    </div>

    <div class="m-field">
      <label>Email <small style="color:var(--text-muted); font-weight:400;">(hubungi admin untuk mengubah)</small></label>
      <input type="text" value="<?= html_escape($user['Email']); ?>" readonly>
    </div>

    <div class="m-field">
      <label>No Handphone *</label>
      <input type="text" name="no_hp" value="<?= html_escape($user['NoHp']); ?>" required>
    </div>

    <div class="m-field" style="margin-bottom:0;">
      <label>Password Baru</label>
      <input type="password" name="password" minlength="4">
      <small style="color:var(--text-muted);">Kosongkan jika tidak ingin mengubah password.</small>
    </div>
  </div>

  <div class="m-card">
    <p class="m-profil-section-title"><i class="fas fa-signature"></i> Tanda Tangan</p>

    <label class="m-photo-upload m-photo-upload-onlight" for="inputTtdProfil">
      <div class="m-photo-upload-ttd">
        <?php if (!empty($user['Ttd'])) : ?>
          <img src="<?= upload_url('ttd_musyrif', $user['Ttd']); ?>" id="previewTtdProfil">
        <?php else : ?>
          <i class="fas fa-signature" id="previewTtdProfil"></i>
        <?php endif; ?>
      </div>
      <span class="m-photo-upload-badge"><i class="fas fa-camera"></i></span>
      <input type="file" name="ttd" id="inputTtdProfil" accept=".jpg,.jpeg,.png" data-photo-input="#previewTtdProfil" hidden>
    </label>
  </div>

  <div class="m-bottombar">
    <button type="submit" class="m-btn m-btn-sticky" data-dirty-submit disabled><i class="fas fa-save"></i> Simpan Perubahan</button>
  </div>

  <?= form_close(); ?>
</div>
