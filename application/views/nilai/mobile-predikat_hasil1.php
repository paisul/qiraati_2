<div class="m-content">
  <p class="m-page-title">Predikat Hasil Ujian Kelas 1 dan 2</p>

  <div class="m-card">
    <button type="button" class="m-btn" data-toggle-target="#formTambahPredikatHasil1">
      <i class="fas fa-plus"></i> Tambah Data
    </button>

    <div class="m-form-panel" id="formTambahPredikatHasil1" hidden>
      <?= form_open('nilai/predikat_hasil1/add'); ?>
      <div class="m-field">
        <label>Batas Bawah Hasil *</label>
        <input type="text" name="batas_bawah1" required autocomplete="off">
      </div>
      <div class="m-field">
        <label>Batas Atas Hasil *</label>
        <input type="text" name="batas_atas1" required autocomplete="off">
      </div>
      <div class="m-field">
        <label>Predikat Hasil *</label>
        <input type="text" name="predikat_hasil1" required autocomplete="off">
      </div>
      <div class="m-field">
        <label>Keterangan Hasil *</label>
        <input type="text" name="ket_hasil1" required autocomplete="off">
      </div>
      <button type="submit" class="m-btn"><i class="fas fa-save"></i> Simpan</button>
      <?= form_close(); ?>
    </div>

    <button type="button" class="m-btn mt-2" id="btnModeUbahPredikatHasil1" style="background:#fff; color:var(--navy); border:1px solid var(--navy);">
      <i class="fas fa-pen"></i> Ubah
    </button>
  </div>

  <div class="m-card" id="mPredikatHasil1List">
    <?php if ($predikathasil1) : ?>
      <?php foreach ($predikathasil1 as $ph1) : ?>
        <div class="m-dana-item-wrapper">
          <div class="m-list-item">
            <div>
              <div class="m-list-title"><?= html_escape($ph1['PredikatHasil1']); ?></div>
              <div class="m-list-sub">Batas <?= html_escape($ph1['BatasNilaiBawah1']); ?> - <?= html_escape($ph1['BatasNilaiAtas1']); ?> &middot; <?= html_escape($ph1['KetHasil1']); ?></div>
            </div>
          </div>

          <div class="m-dana-actions">
            <button type="button" class="m-dana-btn-ubah" data-toggle-target="#editPredikatHasil1<?= $ph1['IdPredikatHasil1']; ?>">Ubah</button>
            <a href="<?= base_url('nilai/predikat_hasil1/delete/' . $ph1['IdPredikatHasil1']); ?>" class="m-dana-btn-hapus tombol-hapus" tipeData="PredikatHasil1" namaData="<?= html_escape($ph1['BatasNilaiBawah1']); ?>">Hapus</a>
          </div>

          <div class="m-form-panel" id="editPredikatHasil1<?= $ph1['IdPredikatHasil1']; ?>" hidden>
            <?= form_open('nilai/predikat_hasil1/update/' . $ph1['IdPredikatHasil1']); ?>
            <div class="m-field">
              <label>Batas Bawah Hasil</label>
              <input type="text" name="batas_bawah1" value="<?= html_escape($ph1['BatasNilaiBawah1']); ?>">
            </div>
            <div class="m-field">
              <label>Batas Atas Hasil</label>
              <input type="text" name="batas_atas1" value="<?= html_escape($ph1['BatasNilaiAtas1']); ?>">
            </div>
            <div class="m-field">
              <label>Predikat Hasil</label>
              <input type="text" name="predikat_hasil1" value="<?= html_escape($ph1['PredikatHasil1']); ?>">
            </div>
            <div class="m-field">
              <label>Keterangan Hasil</label>
              <input type="text" name="ket_hasil1" value="<?= html_escape($ph1['KetHasil1']); ?>">
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
  var btnModeUbahPredikatHasil1 = document.getElementById('btnModeUbahPredikatHasil1');
  var daftarPredikatHasil1 = document.getElementById('mPredikatHasil1List');
  if (btnModeUbahPredikatHasil1 && daftarPredikatHasil1) {
    btnModeUbahPredikatHasil1.addEventListener('click', function () {
      var aktif = daftarPredikatHasil1.classList.toggle('m-dana-edit-mode');
      btnModeUbahPredikatHasil1.innerHTML = aktif
        ? '<i class="fas fa-check"></i> Selesai'
        : '<i class="fas fa-pen"></i> Ubah';
    });
  }
</script>
