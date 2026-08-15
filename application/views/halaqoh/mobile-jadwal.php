<div class="m-content">
  <p class="m-page-title">Waktu Halaqoh</p>

  <div class="m-card">
    <button type="button" class="m-btn" data-toggle-target="#formTambahJadwal">
      <i class="fas fa-plus"></i> Tambah Data
    </button>

    <div class="m-form-panel" id="formTambahJadwal" hidden>
      <?= form_open('halaqoh/jadwal/add'); ?>
      <div class="m-field">
        <label>Waktu *</label>
        <input type="text" name="waktu" required autocomplete="off">
      </div>
      <div class="m-field">
        <label>Keterangan *</label>
        <input type="text" name="keterangan" required autocomplete="off">
      </div>
      <button type="submit" class="m-btn"><i class="fas fa-save"></i> Simpan</button>
      <?= form_close(); ?>
    </div>

    <button type="button" class="m-btn mt-2" id="btnModeUbahJadwal" style="background:#fff; color:var(--navy); border:1px solid var(--navy);">
      <i class="fas fa-pen"></i> Ubah
    </button>
    <a href="<?= base_url('halaqoh/jadwal/export_excel'); ?>" target="_blank" class="m-btn m-btn-outline mt-2"><i class="fas fa-file-excel"></i> Export Data</a>
  </div>

  <div class="m-card" id="mJadwalList">
    <?php if ($jadwal) : ?>
      <?php foreach ($jadwal as $j) : ?>
        <div class="m-dana-item-wrapper">
          <div class="m-list-item">
            <div>
              <div class="m-list-title"><?= html_escape($j['Waktu']); ?></div>
              <div class="m-list-sub"><?= html_escape($j['Ket']); ?></div>
            </div>
          </div>

          <div class="m-dana-actions">
            <button type="button" class="m-dana-btn-ubah" data-toggle-target="#editJadwal<?= $j['IdJadwal']; ?>">Ubah</button>
            <a href="<?= base_url('halaqoh/jadwal/delete/' . $j['IdJadwal']); ?>" class="m-dana-btn-hapus tombol-hapus" tipeData="Tanggal" namaData="<?= date('d F Y', strtotime($j['Waktu'])); ?>">Hapus</a>
          </div>

          <div class="m-form-panel" id="editJadwal<?= $j['IdJadwal']; ?>" hidden>
            <?= form_open('halaqoh/jadwal/update/' . $j['IdJadwal']); ?>
            <div class="m-field">
              <label>Waktu *</label>
              <input type="text" name="waktu" value="<?= html_escape($j['Waktu']); ?>" required autocomplete="off">
            </div>
            <div class="m-field">
              <label>Keterangan *</label>
              <input type="text" name="keterangan" value="<?= html_escape($j['Ket']); ?>" required autocomplete="off">
            </div>
            <button type="submit" class="m-btn"><i class="fas fa-save"></i> Simpan Perubahan</button>
            <?= form_close(); ?>
          </div>
        </div>
      <?php endforeach; ?>
    <?php else : ?>
      <p class="m-empty">Belum ada data waktu halaqoh.</p>
    <?php endif; ?>
  </div>
</div>

<script>
  var btnModeUbahJadwal = document.getElementById('btnModeUbahJadwal');
  var daftarJadwal = document.getElementById('mJadwalList');
  if (btnModeUbahJadwal && daftarJadwal) {
    btnModeUbahJadwal.addEventListener('click', function () {
      var aktif = daftarJadwal.classList.toggle('m-dana-edit-mode');
      btnModeUbahJadwal.innerHTML = aktif
        ? '<i class="fas fa-check"></i> Selesai'
        : '<i class="fas fa-pen"></i> Ubah';
    });
  }
</script>
