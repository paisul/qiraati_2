<div class="m-content">
  <p class="m-page-title">Data Target Ujian</p>

  <div class="m-card">
    <button type="button" class="m-btn" data-toggle-target="#formTambahTargetUjian">
      <i class="fas fa-plus"></i> Tambah Data
    </button>

    <div class="m-form-panel" id="formTambahTargetUjian" hidden>
      <?= form_open('ujian/target_ujian/add'); ?>
      <div class="m-field">
        <label>Nama Ujian</label>
        <select name="jenis_ujian">
          <option value="">-- Pilih Ujian --</option>
          <?php foreach ($jenis_ujian as $ju) : ?>
            <option value="<?= $ju['IdJenisUjian']; ?>"><?= html_escape($ju['NamaUjian']); ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="m-field">
        <label>Keterangan</label>
        <input type="text" name="keterangan" autocomplete="off">
      </div>
      <button type="submit" class="m-btn"><i class="fas fa-save"></i> Simpan</button>
      <?= form_close(); ?>
    </div>

    <button type="button" class="m-btn mt-2" id="btnModeUbahTargetUjian" style="background:#fff; color:var(--navy); border:1px solid var(--navy);">
      <i class="fas fa-pen"></i> Ubah
    </button>
    <a href="<?= base_url('ujian/target_ujian/export_excel'); ?>" target="_blank" class="m-btn m-btn-outline mt-2"><i class="fas fa-file-excel"></i> Export Data</a>
  </div>

  <div class="m-card" id="mTargetUjianList">
    <?php if ($target_ujian) : ?>
      <?php foreach ($target_ujian as $tu) : ?>
        <div class="m-dana-item-wrapper">
          <div class="m-list-item">
            <div>
              <div class="m-list-title"><?= html_escape($tu['NamaUjian']); ?></div>
              <div class="m-list-sub"><?= html_escape($tu['Keterangan']); ?></div>
            </div>
          </div>

          <div class="m-dana-actions">
            <button type="button" class="m-dana-btn-ubah" data-toggle-target="#editTargetUjian<?= $tu['IdTargetUjian']; ?>">Ubah</button>
            <a href="<?= base_url('ujian/target_ujian/delete/' . $tu['IdTargetUjian']); ?>" class="m-dana-btn-hapus tombol-hapus" tipeData="Target Ujian" namaData="<?= html_escape($tu['NamaUjian']); ?>">Hapus</a>
          </div>

          <div class="m-form-panel" id="editTargetUjian<?= $tu['IdTargetUjian']; ?>" hidden>
            <?= form_open('ujian/target_ujian/update/' . $tu['IdTargetUjian']); ?>
            <div class="m-field">
              <label>Nama Ujian</label>
              <select name="jenis_ujian">
                <option value="<?= $tu['IdJenisUjian']; ?>" selected><?= html_escape($tu['NamaUjian']); ?></option>
                <?php foreach ($jenis_ujian as $ju) : ?>
                  <option value="<?= $ju['IdJenisUjian']; ?>"><?= html_escape($ju['NamaUjian']); ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="m-field">
              <label>Keterangan</label>
              <input type="text" name="keterangan" value="<?= html_escape($tu['Keterangan']); ?>" autocomplete="off">
            </div>
            <button type="submit" class="m-btn"><i class="fas fa-save"></i> Simpan Perubahan</button>
            <?= form_close(); ?>
          </div>
        </div>
      <?php endforeach; ?>
    <?php else : ?>
      <p class="m-empty">Belum ada data target ujian.</p>
    <?php endif; ?>
  </div>
</div>

<script>
  var btnModeUbahTargetUjian = document.getElementById('btnModeUbahTargetUjian');
  var daftarTargetUjian = document.getElementById('mTargetUjianList');
  if (btnModeUbahTargetUjian && daftarTargetUjian) {
    btnModeUbahTargetUjian.addEventListener('click', function () {
      var aktif = daftarTargetUjian.classList.toggle('m-dana-edit-mode');
      btnModeUbahTargetUjian.innerHTML = aktif
        ? '<i class="fas fa-check"></i> Selesai'
        : '<i class="fas fa-pen"></i> Ubah';
    });
  }
</script>
