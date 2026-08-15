<?php
$inisial = strtoupper(substr($santri['NamaLengkap'] ?? '?', 0, 1));
?>
<div class="m-content m-content-pad-bottombar">
  <div style="display:flex; align-items:center; gap:10px; margin-bottom:4px;">
    <a href="<?= base_url('Wali/profil'); ?>" style="color:var(--heading); font-size:16px;"><i class="fas fa-arrow-left"></i></a>
    <p class="m-page-title" style="margin:0;">Ubah Profil</p>
  </div>

  <?php if (validation_errors()) : ?>
    <div class="m-card" style="border: 1px solid #f5c6cb; background: #f8d7da; color: #721c24;">
      <?= validation_errors(); ?>
    </div>
  <?php endif; ?>

  <?= form_open_multipart('Wali/simpan_profil'); ?>

  <div class="m-card m-profil-hero">
    <label class="m-photo-upload" for="inputPasfotoWali">
      <?php if (!empty($santri['Pasfoto'])) : ?>
        <img src="<?= upload_url('santri', $santri['Pasfoto']); ?>" alt="Pasfoto" id="previewPasfotoWali" data-fallback-class="m-profil-hero-fallback" data-fallback-text="<?= html_escape($inisial); ?>" onerror="mFotoGagalMuat(this)">
      <?php else : ?>
        <div class="m-profil-hero-fallback" id="previewPasfotoWali"><?= $inisial; ?></div>
      <?php endif; ?>
      <span class="m-photo-upload-badge"><i class="fas fa-camera"></i></span>
      <input type="file" name="pasfoto" id="inputPasfotoWali" accept=".jpg,.jpeg,.png" data-photo-input="#previewPasfotoWali" hidden>
    </label>
    <div class="m-profil-hero-nama"><?= html_escape($santri['NamaLengkap'] ?? ''); ?></div>
    <span class="m-profil-hero-peran">Wali Santri</span>
  </div>

  <div class="m-card">
    <p class="m-profil-section-title"><i class="fas fa-key"></i> Akun Login</p>

    <div class="m-field">
      <label for="email">Email Login *</label>
      <input type="email" id="email" name="email" value="<?= html_escape($user['username']); ?>" required>
      <?php if (form_error('email')) : ?><div class="m-error">Email wajib valid dan belum terdaftar.</div><?php endif; ?>
    </div>

    <div class="m-field" style="margin-bottom:0;">
      <label for="password">Password Baru</label>
      <input type="password" id="password" name="password" minlength="8">
      <small style="color:var(--text-muted);">Kosongkan jika tidak ingin mengubah password.</small>
    </div>
  </div>

  <div class="m-card">
    <p class="m-profil-section-title"><i class="fas fa-home"></i> Data Tambahan</p>

    <div class="m-field">
      <label for="nama_ayah">Nama Ayah</label>
      <input type="text" id="nama_ayah" name="nama_ayah" value="<?= html_escape($santri['NamaAyah']); ?>">
    </div>

    <div class="m-field">
      <label for="nama_ibu">Nama Ibu</label>
      <input type="text" id="nama_ibu" name="nama_ibu" value="<?= html_escape($santri['NamaIbu']); ?>">
    </div>

    <div class="m-field">
      <label for="alamat">Alamat Lengkap</label>
      <textarea id="alamat" name="alamat" rows="3"><?= html_escape($santri['Alamat']); ?></textarea>
    </div>

    <div class="m-field">
      <label for="no_id_card">Nomor ID Card</label>
      <input type="text" id="no_id_card" name="no_id_card" value="<?= html_escape($santri['NoIDCard']); ?>">
      <?php if (form_error('no_id_card')) : ?><div class="m-error">Nomor ID Card sudah dipakai santri lain.</div><?php endif; ?>
    </div>

    <div class="m-field">
      <label for="sekolah_akademik">Sekolah Akademik</label>
      <input type="text" id="sekolah_akademik" name="sekolah_akademik" value="<?= html_escape($santri['SekolahAkademik']); ?>">
    </div>

    <div class="m-field">
      <label for="sekolah_tadika">Sekolah Tadika</label>
      <input type="text" id="sekolah_tadika" name="sekolah_tadika" value="<?= html_escape($santri['SekolahTadika']); ?>">
    </div>

    <div class="m-field" style="margin-bottom:0;">
      <label for="tanggal_mulai_belajar">Tanggal Mulai Belajar</label>
      <input type="date" id="tanggal_mulai_belajar" name="tanggal_mulai_belajar" value="<?= html_escape($santri['TglMulaiBelajar']); ?>">
    </div>
  </div>

  <div class="m-bottombar">
    <button type="submit" class="m-btn m-btn-sticky" data-dirty-submit disabled><i class="fas fa-save"></i> Simpan Perubahan</button>
  </div>

  <?= form_close(); ?>
</div>
