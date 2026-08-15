<div class="m-content">
  <p class="m-page-title">Data Periode</p>

  <div class="m-card">
    <button type="button" class="m-btn" data-toggle-target="#formTambahPeriode">
      <i class="fas fa-plus"></i> Tambah Data
    </button>

    <div class="m-form-panel" id="formTambahPeriode" hidden>
      <?= form_open('tahfidz/periode/add'); ?>
      <div class="m-field">
        <label>Periode *</label>
        <input type="text" name="periode" required autocomplete="off">
      </div>
      <button type="submit" class="m-btn"><i class="fas fa-save"></i> Simpan</button>
      <?= form_close(); ?>
    </div>

    <button type="button" class="m-btn mt-2" id="btnModeUbahPeriode" style="background:#fff; color:var(--navy); border:1px solid var(--navy);">
      <i class="fas fa-pen"></i> Ubah
    </button>
    <a href="<?= base_url('tahfidz/periode/export_excel'); ?>" target="_blank" class="m-btn m-btn-outline mt-2"><i class="fas fa-file-excel"></i> Export Data</a>
  </div>

  <div class="m-card" id="mPeriodeList">
    <?php if ($periode) : ?>
      <?php foreach ($periode as $pr) : ?>
        <div class="m-dana-item-wrapper">
          <div class="m-list-item">
            <div class="m-list-title"><?= html_escape($pr['Periode']); ?></div>
          </div>

          <div class="m-dana-actions">
            <button type="button" class="m-dana-btn-ubah" data-toggle-target="#editPeriode<?= $pr['IdPeriode']; ?>">Ubah</button>
            <a href="<?= base_url('tahfidz/periode/delete/' . $pr['IdPeriode']); ?>" class="m-dana-btn-hapus tombol-hapus" tipeData="Periode" namaData="<?= html_escape($pr['Periode']); ?>">Hapus</a>
          </div>

          <div class="m-form-panel" id="editPeriode<?= $pr['IdPeriode']; ?>" hidden>
            <?= form_open('tahfidz/periode/update/' . $pr['IdPeriode']); ?>
            <div class="m-field">
              <label>Periode *</label>
              <input type="text" name="periode" value="<?= html_escape($pr['Periode']); ?>" required autocomplete="off">
            </div>
            <button type="submit" class="m-btn"><i class="fas fa-save"></i> Simpan Perubahan</button>
            <?= form_close(); ?>
          </div>
        </div>
      <?php endforeach; ?>
    <?php else : ?>
      <p class="m-empty">Belum ada data periode.</p>
    <?php endif; ?>
  </div>
</div>

<script>
  var btnModeUbahPeriode = document.getElementById('btnModeUbahPeriode');
  var daftarPeriode = document.getElementById('mPeriodeList');
  if (btnModeUbahPeriode && daftarPeriode) {
    btnModeUbahPeriode.addEventListener('click', function () {
      var aktif = daftarPeriode.classList.toggle('m-dana-edit-mode');
      btnModeUbahPeriode.innerHTML = aktif
        ? '<i class="fas fa-check"></i> Selesai'
        : '<i class="fas fa-pen"></i> Ubah';
    });
  }
</script>
