<div class="m-content">
  <p class="m-page-title">Data Predikat Nilai</p>

  <div class="m-card">
    <button type="button" class="m-btn" data-toggle-target="#formTambahPredikatNilai">
      <i class="fas fa-plus"></i> Tambah Data
    </button>

    <div class="m-form-panel" id="formTambahPredikatNilai" hidden>
      <?= form_open('nilai/predikat_nilai/add'); ?>
      <div class="m-field">
        <label>Batas Nilai Bawah *</label>
        <input type="text" name="batas_bawah" required autocomplete="off">
      </div>
      <div class="m-field">
        <label>Batas Nilai Atas *</label>
        <input type="text" name="batas_atas" required autocomplete="off">
      </div>
      <div class="m-field">
        <label>Predikat Nilai *</label>
        <input type="text" name="predikat_nilai" required autocomplete="off">
      </div>
      <div class="m-field">
        <label>Keterangan *</label>
        <input type="text" name="ket_nilai" required autocomplete="off">
      </div>
      <button type="submit" class="m-btn"><i class="fas fa-save"></i> Simpan</button>
      <?= form_close(); ?>
    </div>

    <button type="button" class="m-btn mt-2" id="btnModeUbahPredikatNilai" style="background:#fff; color:var(--navy); border:1px solid var(--navy);">
      <i class="fas fa-pen"></i> Ubah
    </button>
  </div>

  <div class="m-card" id="mPredikatNilaiList">
    <?php if ($predikatnilai) : ?>
      <?php foreach ($predikatnilai as $pn) : ?>
        <div class="m-dana-item-wrapper">
          <div class="m-list-item">
            <div>
              <div class="m-list-title"><?= html_escape($pn['PredikatNilai']); ?></div>
              <div class="m-list-sub">Batas <?= html_escape($pn['BatasNilaiBawah']); ?> - <?= html_escape($pn['BatasNilaiAtas']); ?> &middot; <?= html_escape($pn['KetNilai']); ?></div>
            </div>
          </div>

          <div class="m-dana-actions">
            <button type="button" class="m-dana-btn-ubah" data-toggle-target="#editPredikatNilai<?= $pn['IdPredikatNilai']; ?>">Ubah</button>
            <a href="<?= base_url('nilai/predikat_nilai/delete/' . $pn['IdPredikatNilai']); ?>" class="m-dana-btn-hapus tombol-hapus" tipeData="PredikatNilai" namaData="<?= html_escape($pn['BatasNilaiBawah']); ?>">Hapus</a>
          </div>

          <div class="m-form-panel" id="editPredikatNilai<?= $pn['IdPredikatNilai']; ?>" hidden>
            <?= form_open('nilai/predikat_nilai/update/' . $pn['IdPredikatNilai']); ?>
            <div class="m-field">
              <label>Batas Nilai Bawah</label>
              <input type="text" name="batas_bawah" value="<?= html_escape($pn['BatasNilaiBawah']); ?>">
            </div>
            <div class="m-field">
              <label>Batas Nilai Atas</label>
              <input type="text" name="batas_atas" value="<?= html_escape($pn['BatasNilaiAtas']); ?>">
            </div>
            <div class="m-field">
              <label>Predikat Nilai</label>
              <input type="text" name="predikat_nilai" value="<?= html_escape($pn['PredikatNilai']); ?>">
            </div>
            <div class="m-field">
              <label>Keterangan</label>
              <input type="text" name="ket_nilai" value="<?= html_escape($pn['KetNilai']); ?>">
            </div>
            <button type="submit" class="m-btn"><i class="fas fa-save"></i> Simpan Perubahan</button>
            <?= form_close(); ?>
          </div>
        </div>
      <?php endforeach; ?>
    <?php else : ?>
      <p class="m-empty">Belum ada data predikat nilai.</p>
    <?php endif; ?>
  </div>
</div>

<script>
  var btnModeUbahPredikatNilai = document.getElementById('btnModeUbahPredikatNilai');
  var daftarPredikatNilai = document.getElementById('mPredikatNilaiList');
  if (btnModeUbahPredikatNilai && daftarPredikatNilai) {
    btnModeUbahPredikatNilai.addEventListener('click', function () {
      var aktif = daftarPredikatNilai.classList.toggle('m-dana-edit-mode');
      btnModeUbahPredikatNilai.innerHTML = aktif
        ? '<i class="fas fa-check"></i> Selesai'
        : '<i class="fas fa-pen"></i> Ubah';
    });
  }
</script>
