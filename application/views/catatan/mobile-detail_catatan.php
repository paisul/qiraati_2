<div class="m-content">
  <p class="m-page-title">Detail Jenis Catatan</p>

  <div class="m-card">
    <button type="button" class="m-btn" data-toggle-target="#formTambahDetailCatatan">
      <i class="fas fa-plus"></i> Tambah Data
    </button>

    <div class="m-form-panel" id="formTambahDetailCatatan" hidden>
      <?= form_open('catatan/detail_catatan/add'); ?>
      <div class="m-field">
        <label>Jenis Catatan *</label>
        <select name="jeniscatatan" required>
          <option value="">-- Pilih Jenis Catatan --</option>
          <?php foreach ($jenis_catatan as $jc) : ?>
            <option value="<?= $jc['IdJenisCatatan']; ?>"><?= html_escape($jc['JenisCatatan']); ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="m-field">
        <label>Detail Catatan *</label>
        <textarea name="isidetailcatatan" rows="4" required></textarea>
      </div>
      <button type="submit" class="m-btn"><i class="fas fa-save"></i> Simpan</button>
      <?= form_close(); ?>
    </div>

    <button type="button" class="m-btn mt-2" id="btnModeUbahDetailCatatan" style="background:#fff; color:var(--navy); border:1px solid var(--navy);">
      <i class="fas fa-pen"></i> Ubah
    </button>
  </div>

  <div class="m-card" id="mDetailCatatanList">
    <?php if ($detail_jenis_catatan) : ?>
      <?php foreach ($detail_jenis_catatan as $djc) : ?>
        <div class="m-dana-item-wrapper">
          <div class="m-list-item">
            <div>
              <div class="m-list-title"><?= html_escape($djc['JenisCatatan']); ?></div>
              <div class="m-list-sub"><?= html_escape($djc['DetailCatatan']); ?></div>
            </div>
          </div>

          <div class="m-dana-actions">
            <button type="button" class="m-dana-btn-ubah" data-toggle-target="#editDetailCatatan<?= $djc['IdDetailJenisCatatan']; ?>">Ubah</button>
            <a href="<?= base_url('catatan/detail_catatan/delete/' . $djc['IdDetailJenisCatatan']); ?>" class="m-dana-btn-hapus tombol-hapus" tipeData="Detail Jenis Catatan" namaData="<?= html_escape($djc['JenisCatatan']); ?>">Hapus</a>
          </div>

          <div class="m-form-panel" id="editDetailCatatan<?= $djc['IdDetailJenisCatatan']; ?>" hidden>
            <?= form_open('catatan/detail_catatan/update/' . $djc['IdDetailJenisCatatan']); ?>
            <div class="m-field">
              <label>Jenis Catatan *</label>
              <select name="jeniscatatan" required>
                <option value="<?= $djc['IdJenisCatatan']; ?>" selected><?= html_escape($djc['JenisCatatan']); ?></option>
                <?php foreach ($jenis_catatan as $jc) : ?>
                  <option value="<?= $jc['IdJenisCatatan']; ?>"><?= html_escape($jc['JenisCatatan']); ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="m-field">
              <label>Detail Catatan *</label>
              <textarea name="isidetailcatatan" rows="4" required><?= html_escape($djc['DetailCatatan']); ?></textarea>
            </div>
            <button type="submit" class="m-btn"><i class="fas fa-save"></i> Simpan Perubahan</button>
            <?= form_close(); ?>
          </div>
        </div>
      <?php endforeach; ?>
    <?php else : ?>
      <p class="m-empty">Belum ada data detail jenis catatan.</p>
    <?php endif; ?>
  </div>
</div>

<script>
  var btnModeUbahDetailCatatan = document.getElementById('btnModeUbahDetailCatatan');
  var daftarDetailCatatan = document.getElementById('mDetailCatatanList');
  if (btnModeUbahDetailCatatan && daftarDetailCatatan) {
    btnModeUbahDetailCatatan.addEventListener('click', function () {
      var aktif = daftarDetailCatatan.classList.toggle('m-dana-edit-mode');
      btnModeUbahDetailCatatan.innerHTML = aktif
        ? '<i class="fas fa-check"></i> Selesai'
        : '<i class="fas fa-pen"></i> Ubah';
    });
  }
</script>
