<div class="m-content">
  <p class="m-page-title">Pengaturan Aplikasi</p>

  <div class="m-card">
    <?= form_open_multipart('pengaturanaplikasi/simpan'); ?>
    <div class="m-field">
      <label>Nama Aplikasi *</label>
      <input type="text" name="nama_aplikasi" value="<?= html_escape($pengaturan['NamaAplikasi']); ?>" required>
      <small style="color:#6c757d;">Tampil di sidebar, judul halaman, dan footer di semua level pengguna.</small>
    </div>
    <div class="m-field">
      <label>Logo Aplikasi <small>(jpg/jpeg/png, maks 2MB)</small></label>
      <img src="<?= base_url('assets/' . $pengaturan['Logo']); ?>" class="m-field-photo-preview">
      <input type="file" name="logo" accept=".jpg,.jpeg,.png">
    </div>
    <button type="submit" class="m-btn"><i class="fas fa-save"></i> Simpan</button>
    <?= form_close(); ?>
  </div>
</div>
