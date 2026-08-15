<div class="m-content">
  <p class="m-page-title">Jenis Pelanggaran</p>

  <div class="m-card">
    <button type="button" class="m-btn" data-toggle-target="#formTambahJenisPelanggaran">
      <i class="fas fa-plus"></i> Tambah Data
    </button>

    <div class="m-form-panel" id="formTambahJenisPelanggaran" hidden>
      <?= form_open('pelanggaran/jenis_pelanggaran/add'); ?>
      <div class="m-field">
        <label>Jenis Iqob *</label>
        <input type="text" name="jenis_iqob" required>
      </div>
      <div class="m-field">
        <label>Poin *</label>
        <input type="number" name="poin" required>
      </div>
      <div class="m-field">
        <label>Kategori *</label>
        <select name="kategori" required>
          <option value="">-- Pilih Kategori --</option>
          <option value="Ibadah">Ibadah</option>
          <option value="Bahasa">Bahasa</option>
        </select>
      </div>
      <button type="submit" class="m-btn"><i class="fas fa-save"></i> Simpan</button>
      <?= form_close(); ?>
    </div>

    <button type="button" class="m-btn mt-2" id="btnModeUbahJenisPelanggaran" style="background:#fff; color:var(--navy); border:1px solid var(--navy);">
      <i class="fas fa-pen"></i> Ubah
    </button>
    <a href="<?= base_url('pelanggaran/jenis_pelanggaran/export_excel'); ?>" target="_blank" class="m-btn m-btn-outline mt-2"><i class="fas fa-file-excel"></i> Export Data</a>
  </div>

  <div class="m-card" id="mJenisPelanggaranList">
    <?php if ($jenispelanggaran) : ?>
      <?php foreach ($jenispelanggaran as $jp) : ?>
        <div class="m-dana-item-wrapper">
          <div class="m-list-item">
            <div>
              <div class="m-list-title"><?= html_escape($jp['JenisIqob']); ?></div>
              <div class="m-list-sub"><?= html_escape($jp['Kategori']); ?></div>
            </div>
            <span class="m-badge m-badge-belum"><?= (int) $jp['Poin']; ?> poin</span>
          </div>

          <div class="m-dana-actions">
            <button type="button" class="m-dana-btn-ubah" data-toggle-target="#editJenisPelanggaran<?= $jp['IdJenisIqob']; ?>">Ubah</button>
            <a href="<?= base_url('pelanggaran/jenis_pelanggaran/delete/' . $jp['IdJenisIqob']); ?>" class="m-dana-btn-hapus tombol-hapus" tipeData="Jenis Pelanggaran" namaData="<?= html_escape($jp['JenisIqob']); ?>">Hapus</a>
          </div>

          <div class="m-form-panel" id="editJenisPelanggaran<?= $jp['IdJenisIqob']; ?>" hidden>
            <?= form_open('pelanggaran/jenis_pelanggaran/update/' . $jp['IdJenisIqob']); ?>
            <div class="m-field">
              <label>Jenis Iqob *</label>
              <input type="text" name="jenis_iqob" value="<?= html_escape($jp['JenisIqob']); ?>" required>
            </div>
            <div class="m-field">
              <label>Poin *</label>
              <input type="number" name="poin" value="<?= html_escape($jp['Poin']); ?>" required>
            </div>
            <div class="m-field">
              <label>Kategori *</label>
              <select name="kategori" required>
                <option value="Ibadah" <?= $jp['Kategori'] == 'Ibadah' ? 'selected' : ''; ?>>Ibadah</option>
                <option value="Bahasa" <?= $jp['Kategori'] == 'Bahasa' ? 'selected' : ''; ?>>Bahasa</option>
              </select>
            </div>
            <button type="submit" class="m-btn"><i class="fas fa-save"></i> Simpan Perubahan</button>
            <?= form_close(); ?>
          </div>
        </div>
      <?php endforeach; ?>
    <?php else : ?>
      <p class="m-empty">Belum ada data jenis pelanggaran.</p>
    <?php endif; ?>
  </div>
</div>

<script>
  var btnModeUbahJenisPelanggaran = document.getElementById('btnModeUbahJenisPelanggaran');
  var daftarJenisPelanggaran = document.getElementById('mJenisPelanggaranList');
  if (btnModeUbahJenisPelanggaran && daftarJenisPelanggaran) {
    btnModeUbahJenisPelanggaran.addEventListener('click', function () {
      var aktif = daftarJenisPelanggaran.classList.toggle('m-dana-edit-mode');
      btnModeUbahJenisPelanggaran.innerHTML = aktif
        ? '<i class="fas fa-check"></i> Selesai'
        : '<i class="fas fa-pen"></i> Ubah';
    });
  }
</script>
