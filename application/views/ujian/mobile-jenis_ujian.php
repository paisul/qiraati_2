<div class="m-content">
  <p class="m-page-title">Jenis Ujian</p>

  <div class="m-card">
    <button type="button" class="m-btn" data-toggle-target="#formTambahJenisUjian">
      <i class="fas fa-plus"></i> Tambah Data
    </button>

    <div class="m-form-panel" id="formTambahJenisUjian" hidden>
      <?= form_open('ujian/jenis_ujian/add'); ?>
      <div class="m-field">
        <label>Nama Ujian *</label>
        <input type="text" name="ujian" required autocomplete="off">
      </div>
      <button type="submit" class="m-btn"><i class="fas fa-save"></i> Simpan</button>
      <?= form_close(); ?>
    </div>

    <button type="button" class="m-btn mt-2" id="btnModeUbahJenisUjian" style="background:#fff; color:var(--navy); border:1px solid var(--navy);">
      <i class="fas fa-pen"></i> Ubah
    </button>
    <a href="<?= base_url('ujian/jenis_ujian/export_excel'); ?>" target="_blank" class="m-btn m-btn-outline mt-2"><i class="fas fa-file-excel"></i> Export Data</a>
  </div>

  <div class="m-card" id="mJenisUjianList">
    <?php if ($jenis_ujian) : ?>
      <?php foreach ($jenis_ujian as $ju) : ?>
        <div class="m-dana-item-wrapper">
          <div class="m-list-item">
            <div class="m-list-title"><?= html_escape($ju['NamaUjian']); ?></div>
          </div>

          <div class="m-dana-actions">
            <button type="button" class="m-dana-btn-ubah" data-toggle-target="#editJenisUjian<?= $ju['IdJenisUjian']; ?>">Ubah</button>
            <a href="<?= base_url('ujian/jenis_ujian/delete/' . $ju['IdJenisUjian']); ?>" class="m-dana-btn-hapus tombol-hapus" tipeData="Jenis Ujian" namaData="<?= html_escape($ju['NamaUjian']); ?>">Hapus</a>
          </div>

          <div class="m-form-panel" id="editJenisUjian<?= $ju['IdJenisUjian']; ?>" hidden>
            <?= form_open('ujian/jenis_ujian/update/' . $ju['IdJenisUjian']); ?>
            <div class="m-field">
              <label>Nama Ujian *</label>
              <input type="text" name="ujian" value="<?= html_escape($ju['NamaUjian']); ?>" required autocomplete="off">
            </div>
            <button type="submit" class="m-btn"><i class="fas fa-save"></i> Simpan Perubahan</button>
            <?= form_close(); ?>
          </div>
        </div>
      <?php endforeach; ?>
    <?php else : ?>
      <p class="m-empty">Belum ada data jenis ujian.</p>
    <?php endif; ?>
  </div>
</div>

<script>
  var btnModeUbahJenisUjian = document.getElementById('btnModeUbahJenisUjian');
  var daftarJenisUjian = document.getElementById('mJenisUjianList');
  if (btnModeUbahJenisUjian && daftarJenisUjian) {
    btnModeUbahJenisUjian.addEventListener('click', function () {
      var aktif = daftarJenisUjian.classList.toggle('m-dana-edit-mode');
      btnModeUbahJenisUjian.innerHTML = aktif
        ? '<i class="fas fa-check"></i> Selesai'
        : '<i class="fas fa-pen"></i> Ubah';
    });
  }
</script>
