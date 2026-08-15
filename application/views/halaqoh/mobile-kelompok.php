<div class="m-content">
  <p class="m-page-title">Kelompok</p>

  <div class="m-card">
    <button type="button" class="m-btn" data-toggle-target="#formTambahKelompok">
      <i class="fas fa-plus"></i> Tambah Data
    </button>

    <div class="m-form-panel" id="formTambahKelompok" hidden>
      <?= form_open('halaqoh/kelompok/add'); ?>
      <div class="m-field">
        <label>Nama Kelompok *</label>
        <input type="text" name="kelompok" required autocomplete="off">
      </div>
      <button type="submit" class="m-btn"><i class="fas fa-save"></i> Simpan</button>
      <?= form_close(); ?>
    </div>

    <button type="button" class="m-btn mt-2" id="btnModeUbahKelompok" style="background:#fff; color:var(--navy); border:1px solid var(--navy);">
      <i class="fas fa-pen"></i> Ubah
    </button>
    <a href="<?= base_url('halaqoh/kelompok/export_excel'); ?>" target="_blank" class="m-btn m-btn-outline mt-2"><i class="fas fa-file-excel"></i> Export Data</a>
  </div>

  <div class="m-card" id="mKelompokList">
    <?php if ($kelompokhalaqoh) : ?>
      <?php foreach ($kelompokhalaqoh as $kh) : ?>
        <div class="m-dana-item-wrapper">
          <div class="m-list-item">
            <div class="m-list-title"><?= html_escape($kh['NamaKelompok']); ?></div>
          </div>

          <div class="m-dana-actions">
            <button type="button" class="m-dana-btn-ubah" data-toggle-target="#editKelompok<?= $kh['IdKelompok']; ?>">Ubah</button>
            <a href="<?= base_url('halaqoh/kelompok/delete/' . $kh['IdKelompok']); ?>" class="m-dana-btn-hapus tombol-hapus" tipeData="Kelompok" namaData="<?= html_escape($kh['NamaKelompok']); ?>">Hapus</a>
          </div>

          <div class="m-form-panel" id="editKelompok<?= $kh['IdKelompok']; ?>" hidden>
            <?= form_open('halaqoh/kelompok/update/' . $kh['IdKelompok']); ?>
            <div class="m-field">
              <label>Nama Kelompok *</label>
              <input type="text" name="kelompok" value="<?= html_escape($kh['NamaKelompok']); ?>" required autocomplete="off">
            </div>
            <button type="submit" class="m-btn"><i class="fas fa-save"></i> Simpan Perubahan</button>
            <?= form_close(); ?>
          </div>
        </div>
      <?php endforeach; ?>
    <?php else : ?>
      <p class="m-empty">Belum ada data kelompok.</p>
    <?php endif; ?>
  </div>
</div>

<script>
  var btnModeUbahKelompok = document.getElementById('btnModeUbahKelompok');
  var daftarKelompok = document.getElementById('mKelompokList');
  if (btnModeUbahKelompok && daftarKelompok) {
    btnModeUbahKelompok.addEventListener('click', function () {
      var aktif = daftarKelompok.classList.toggle('m-dana-edit-mode');
      btnModeUbahKelompok.innerHTML = aktif
        ? '<i class="fas fa-check"></i> Selesai'
        : '<i class="fas fa-pen"></i> Ubah';
    });
  }
</script>
