<div class="m-content">
  <p class="m-page-title">Predikat Hasil Ujian Kelas 3 dan 4</p>

  <div class="m-card">
    <button type="button" class="m-btn" data-toggle-target="#formTambahPredikatHasil2">
      <i class="fas fa-plus"></i> Tambah Data
    </button>

    <div class="m-form-panel" id="formTambahPredikatHasil2" hidden>
      <?= form_open('nilai/predikat_hasil2/add'); ?>
      <div class="m-field">
        <label>Batas Bawah Hasil *</label>
        <input type="text" name="batas_bawah2" required autocomplete="off">
      </div>
      <div class="m-field">
        <label>Batas Atas Hasil *</label>
        <input type="text" name="batas_atas2" required autocomplete="off">
      </div>
      <div class="m-field">
        <label>Predikat Hasil *</label>
        <input type="text" name="predikat_hasil2" required autocomplete="off">
      </div>
      <div class="m-field">
        <label>Keterangan Hasil *</label>
        <input type="text" name="ket_hasil2" required autocomplete="off">
      </div>
      <button type="submit" class="m-btn"><i class="fas fa-save"></i> Simpan</button>
      <?= form_close(); ?>
    </div>

    <button type="button" class="m-btn mt-2" id="btnModeUbahPredikatHasil2" style="background:#fff; color:var(--navy); border:1px solid var(--navy);">
      <i class="fas fa-pen"></i> Ubah
    </button>
  </div>

  <div class="m-card" id="mPredikatHasil2List">
    <?php if ($predikathasil2) : ?>
      <?php foreach ($predikathasil2 as $ph2) : ?>
        <div class="m-dana-item-wrapper">
          <div class="m-list-item">
            <div>
              <div class="m-list-title"><?= html_escape($ph2['PredikatHasil2']); ?></div>
              <div class="m-list-sub">Batas <?= html_escape($ph2['BatasNilaiBawah2']); ?> - <?= html_escape($ph2['BatasNilaiAtas2']); ?> &middot; <?= html_escape($ph2['KetHasil2']); ?></div>
            </div>
          </div>

          <div class="m-dana-actions">
            <button type="button" class="m-dana-btn-ubah" data-toggle-target="#editPredikatHasil2<?= $ph2['IdPredikatHasil2']; ?>">Ubah</button>
            <a href="<?= base_url('nilai/predikat_hasil2/delete/' . $ph2['IdPredikatHasil2']); ?>" class="m-dana-btn-hapus tombol-hapus" tipeData="PredikatHasil2" namaData="<?= html_escape($ph2['BatasNilaiBawah2']); ?>">Hapus</a>
          </div>

          <div class="m-form-panel" id="editPredikatHasil2<?= $ph2['IdPredikatHasil2']; ?>" hidden>
            <?= form_open('nilai/predikat_hasil2/update/' . $ph2['IdPredikatHasil2']); ?>
            <div class="m-field">
              <label>Batas Bawah Hasil</label>
              <input type="text" name="batas_bawah2" value="<?= html_escape($ph2['BatasNilaiBawah2']); ?>">
            </div>
            <div class="m-field">
              <label>Batas Atas Hasil</label>
              <input type="text" name="batas_atas2" value="<?= html_escape($ph2['BatasNilaiAtas2']); ?>">
            </div>
            <div class="m-field">
              <label>Predikat Hasil</label>
              <input type="text" name="predikat_hasil2" value="<?= html_escape($ph2['PredikatHasil2']); ?>">
            </div>
            <div class="m-field">
              <label>Keterangan Hasil</label>
              <input type="text" name="ket_hasil2" value="<?= html_escape($ph2['KetHasil2']); ?>">
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
  var btnModeUbahPredikatHasil2 = document.getElementById('btnModeUbahPredikatHasil2');
  var daftarPredikatHasil2 = document.getElementById('mPredikatHasil2List');
  if (btnModeUbahPredikatHasil2 && daftarPredikatHasil2) {
    btnModeUbahPredikatHasil2.addEventListener('click', function () {
      var aktif = daftarPredikatHasil2.classList.toggle('m-dana-edit-mode');
      btnModeUbahPredikatHasil2.innerHTML = aktif
        ? '<i class="fas fa-check"></i> Selesai'
        : '<i class="fas fa-pen"></i> Ubah';
    });
  }
</script>
