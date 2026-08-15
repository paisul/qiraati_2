<div class="m-content">
  <p class="m-page-title">Jenis Catatan</p>

  <div class="m-card">
    <button type="button" class="m-btn" data-toggle-target="#formTambahJenisCatatan">
      <i class="fas fa-plus"></i> Tambah Data
    </button>

    <div class="m-form-panel" id="formTambahJenisCatatan" hidden>
      <?= form_open('catatan/jenis_catatan/add'); ?>
      <div class="m-field">
        <label>Jenis Catatan *</label>
        <input type="text" name="jenis_catatan" required>
      </div>
      <button type="submit" class="m-btn"><i class="fas fa-save"></i> Simpan</button>
      <?= form_close(); ?>
    </div>

    <button type="button" class="m-btn mt-2" id="btnModeUbahJenisCatatan" style="background:#fff; color:var(--navy); border:1px solid var(--navy);">
      <i class="fas fa-pen"></i> Ubah
    </button>
  </div>

  <div class="m-card" id="mJenisCatatanList">
    <?php if ($jenis_catatan) : ?>
      <?php foreach ($jenis_catatan as $jc) : ?>
        <div class="m-dana-item-wrapper">
          <div class="m-list-item">
            <div>
              <div class="m-list-title"><?= html_escape($jc['JenisCatatan']); ?></div>
            </div>
          </div>

          <div class="m-dana-actions">
            <button type="button" class="m-dana-btn-ubah" data-toggle-target="#editJenisCatatan<?= $jc['IdJenisCatatan']; ?>">Ubah</button>
            <a href="<?= base_url('catatan/jenis_catatan/delete/' . $jc['IdJenisCatatan']); ?>" class="m-dana-btn-hapus tombol-hapus" tipeData="Jenis Catatan" namaData="<?= html_escape($jc['JenisCatatan']); ?>">Hapus</a>
          </div>

          <div class="m-form-panel" id="editJenisCatatan<?= $jc['IdJenisCatatan']; ?>" hidden>
            <?= form_open('catatan/jenis_catatan/update/' . $jc['IdJenisCatatan']); ?>
            <div class="m-field">
              <label>Jenis Catatan *</label>
              <input type="text" name="jenis_catatan" value="<?= html_escape($jc['JenisCatatan']); ?>" required>
            </div>
            <button type="submit" class="m-btn"><i class="fas fa-save"></i> Simpan Perubahan</button>
            <?= form_close(); ?>
          </div>
        </div>
      <?php endforeach; ?>
    <?php else : ?>
      <p class="m-empty">Belum ada data jenis catatan.</p>
    <?php endif; ?>
  </div>
</div>

<script>
  var btnModeUbahJenisCatatan = document.getElementById('btnModeUbahJenisCatatan');
  var daftarJenisCatatan = document.getElementById('mJenisCatatanList');
  if (btnModeUbahJenisCatatan && daftarJenisCatatan) {
    btnModeUbahJenisCatatan.addEventListener('click', function () {
      var aktif = daftarJenisCatatan.classList.toggle('m-dana-edit-mode');
      btnModeUbahJenisCatatan.innerHTML = aktif
        ? '<i class="fas fa-check"></i> Selesai'
        : '<i class="fas fa-pen"></i> Ubah';
    });
  }
</script>
