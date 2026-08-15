<div class="m-content">
  <p class="m-page-title">Predikat Hasil Ujian Kelas 5 dan 6</p>

  <div class="m-card">
    <button type="button" class="m-btn" data-toggle-target="#formTambahPredikatHasil3">
      <i class="fas fa-plus"></i> Tambah Data
    </button>

    <div class="m-form-panel" id="formTambahPredikatHasil3" hidden>
      <?= form_open('nilai/predikat_hasil3/add'); ?>
      <div class="m-field">
        <label>Batas Bawah Hasil *</label>
        <input type="text" name="batas_bawah3" required autocomplete="off">
      </div>
      <div class="m-field">
        <label>Batas Atas Hasil *</label>
        <input type="text" name="batas_atas3" required autocomplete="off">
      </div>
      <div class="m-field">
        <label>Predikat Hasil *</label>
        <input type="text" name="predikat_hasil3" required autocomplete="off">
      </div>
      <div class="m-field">
        <label>Keterangan Hasil *</label>
        <input type="text" name="ket_hasil3" required autocomplete="off">
      </div>
      <button type="submit" class="m-btn"><i class="fas fa-save"></i> Simpan</button>
      <?= form_close(); ?>
    </div>

    <button type="button" class="m-btn mt-2" id="btnModeUbahPredikatHasil3" style="background:#fff; color:var(--navy); border:1px solid var(--navy);">
      <i class="fas fa-pen"></i> Ubah
    </button>
  </div>

  <div class="m-card" id="mPredikatHasil3List">
    <?php if ($predikathasil3) : ?>
      <?php foreach ($predikathasil3 as $ph3) : ?>
        <div class="m-dana-item-wrapper">
          <div class="m-list-item">
            <div>
              <div class="m-list-title"><?= html_escape($ph3['PredikatHasil3']); ?></div>
              <div class="m-list-sub">Batas <?= html_escape($ph3['BatasBawahHasil3']); ?> - <?= html_escape($ph3['BatasAtasHasil3']); ?> &middot; <?= html_escape($ph3['KetHasil3']); ?></div>
            </div>
          </div>

          <div class="m-dana-actions">
            <button type="button" class="m-dana-btn-ubah" data-toggle-target="#editPredikatHasil3<?= $ph3['IdPredikatHasil3']; ?>">Ubah</button>
            <a href="<?= base_url('nilai/predikat_hasil3/delete/' . $ph3['IdPredikatHasil3']); ?>" class="m-dana-btn-hapus tombol-hapus" tipeData="PredikatHasil3" namaData="<?= html_escape($ph3['BatasBawahHasil3']); ?>">Hapus</a>
          </div>

          <div class="m-form-panel" id="editPredikatHasil3<?= $ph3['IdPredikatHasil3']; ?>" hidden>
            <?= form_open('nilai/predikat_hasil3/update/' . $ph3['IdPredikatHasil3']); ?>
            <div class="m-field">
              <label>Batas Bawah Hasil</label>
              <input type="text" name="batas_bawah3" value="<?= html_escape($ph3['BatasBawahHasil3']); ?>">
            </div>
            <div class="m-field">
              <label>Batas Atas Hasil</label>
              <input type="text" name="batas_atas3" value="<?= html_escape($ph3['BatasAtasHasil3']); ?>">
            </div>
            <div class="m-field">
              <label>Predikat Hasil</label>
              <input type="text" name="predikat_hasil3" value="<?= html_escape($ph3['PredikatHasil3']); ?>">
            </div>
            <div class="m-field">
              <label>Keterangan Hasil</label>
              <input type="text" name="ket_hasil3" value="<?= html_escape($ph3['KetHasil3']); ?>">
            </div>
            <button type="submit" class="m-btn"><i class="fas fa-save"></i> Simpan Perubahan</button>
            <?= form_close(); ?>
          </div>
        </div>
      <?php endforeach; ?>
    <?php else : ?>
      <p class="m-empty">Belum ada data predikat hasil.</p>
    <?php endif; ?>
  </div>
</div>

<script>
  var btnModeUbahPredikatHasil3 = document.getElementById('btnModeUbahPredikatHasil3');
  var daftarPredikatHasil3 = document.getElementById('mPredikatHasil3List');
  if (btnModeUbahPredikatHasil3 && daftarPredikatHasil3) {
    btnModeUbahPredikatHasil3.addEventListener('click', function () {
      var aktif = daftarPredikatHasil3.classList.toggle('m-dana-edit-mode');
      btnModeUbahPredikatHasil3.innerHTML = aktif
        ? '<i class="fas fa-check"></i> Selesai'
        : '<i class="fas fa-pen"></i> Ubah';
    });
  }
</script>
