<?php
// Versi mobile dari santri/_form_fields.php (desktop) - field & nama input identik supaya
// Santri::simpan()/perbarui() tidak perlu diubah. Satu halaman penuh (bukan wizard bertahap) -
// semua field langsung tampil supaya lebih cepat diisi/dikoreksi di HP.
$dipilih = function ($nilai, $target) {
  return (string) $nilai === (string) $target ? 'selected' : '';
};
$inisial = strtoupper(substr($santri['NamaLengkap'] !== '' ? $santri['NamaLengkap'] : '?', 0, 1));
?>
<div style="display:flex; justify-content:center; margin-bottom:18px;">
  <label class="m-photo-upload m-photo-upload-onlight" for="inputPasfotoSantri">
    <?php if (!empty($santri['Pasfoto'])) : ?>
      <img src="<?= upload_url('santri', $santri['Pasfoto']); ?>" class="m-field-photo-preview" id="previewPasfotoSantri" style="margin-bottom:0;" data-fallback-class="m-field-photo-fallback" data-fallback-text="<?= html_escape($inisial); ?>" data-fallback-style="margin-bottom:0;" onerror="mFotoGagalMuat(this)">
    <?php else : ?>
      <div class="m-field-photo-fallback" id="previewPasfotoSantri" style="margin-bottom:0;"><?= $inisial; ?></div>
    <?php endif; ?>
    <span class="m-photo-upload-badge"><i class="fas fa-camera"></i></span>
    <input type="file" name="pasfoto" id="inputPasfotoSantri" accept=".jpg,.jpeg,.png" data-photo-input="#previewPasfotoSantri" hidden>
  </label>
</div>

<p class="m-profil-section-title"><i class="fas fa-id-card"></i> Data Wajib</p>

<div class="m-field">
  <label>NIS</label>
  <input type="text" value="<?= html_escape($nis_baru); ?>" readonly>
  <small style="color:#6c757d;">Dibuat otomatis oleh sistem.</small>
</div>
<div class="m-field">
  <label>Nama Lengkap *</label>
  <input type="text" name="nama" minlength="3" required value="<?= html_escape($santri['NamaLengkap']); ?>">
</div>
<div class="m-field">
  <label>Jenis Kelamin *</label>
  <select name="jenis_kelamin" required>
    <option value="">-- Pilih Jenis Kelamin --</option>
    <?php foreach ($jenis_kelamin_list as $jk) : ?>
      <option value="<?= $jk; ?>" <?= $dipilih($jk, $santri['JenisKelamin']); ?>><?= $jk; ?></option>
    <?php endforeach; ?>
  </select>
</div>
<div class="m-field">
  <label>Tempat Lahir *</label>
  <input type="text" name="tempat_lahir" required value="<?= html_escape($santri['TempatLahir']); ?>">
</div>
<div class="m-field">
  <label>Tanggal Lahir *</label>
  <input type="date" name="tanggal_lahir" required value="<?= html_escape($santri['TanggalLahir']); ?>">
</div>
<div class="m-field">
  <label>Kelas *</label>
  <select name="kelas" required>
    <option value="">-- Pilih Kelas --</option>
    <?php foreach ($kelas as $kls) : ?>
      <option value="<?= $kls['IdKelas']; ?>" <?= $dipilih($kls['IdKelas'], $santri['IdKelas']); ?>><?= $kls['NamaKelas']; ?><?= !empty($kls['NamaMusyrif']) ? ' - ' . $kls['NamaMusyrif'] : ''; ?></option>
    <?php endforeach; ?>
  </select>
</div>
<div class="m-field">
  <label>Email untuk Login *</label>
  <input type="email" name="email" required value="<?= html_escape($santri['login']['username']); ?>">
</div>
<div class="m-field">
  <label>Password <?= $mode === 'tambah' ? '*' : ''; ?></label>
  <input type="password" name="password" minlength="8" <?= $mode === 'tambah' ? 'required' : ''; ?>>
  <small style="color:#6c757d;"><?= $mode === 'tambah' ? 'Minimal 8 karakter.' : 'Kosongkan jika tidak ingin mengubah password.'; ?></small>
</div>

<p class="m-profil-section-title" style="margin-top:18px;"><i class="fas fa-plus-circle"></i> Data Tambahan</p>

<div class="m-field">
  <label>Nama Ayah</label>
  <input type="text" name="nama_ayah" value="<?= html_escape($santri['NamaAyah']); ?>">
</div>
<div class="m-field">
  <label>Nama Ibu</label>
  <input type="text" name="nama_ibu" value="<?= html_escape($santri['NamaIbu']); ?>">
</div>
<div class="m-field">
  <label>Alamat Lengkap</label>
  <textarea name="alamat" rows="3"><?= html_escape($santri['Alamat']); ?></textarea>
</div>
<div class="m-field">
  <label>Sekolah Akademik</label>
  <input type="text" name="sekolah_akademik" value="<?= html_escape($santri['SekolahAkademik']); ?>">
</div>
<div class="m-field">
  <label>Sekolah Tadika</label>
  <input type="text" name="sekolah_tadika" value="<?= html_escape($santri['SekolahTadika']); ?>">
</div>
<div class="m-field">
  <label>Nomor ID Card</label>
  <input type="text" name="no_id_card" value="<?= html_escape($santri['NoIDCard']); ?>">
</div>
<div class="m-field" style="margin-bottom:0;">
  <label>Tanggal Mulai Belajar</label>
  <input type="date" name="tanggal_mulai_belajar" value="<?= html_escape($santri['TglMulaiBelajar']); ?>">
</div>
