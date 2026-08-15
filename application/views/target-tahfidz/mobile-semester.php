<div class="m-content">
  <p class="m-page-title">Data Semester</p>

  <div class="m-card">
    <button type="button" class="m-btn" data-toggle-target="#formTambahSemester">
      <i class="fas fa-plus"></i> Tambah Data
    </button>

    <div class="m-form-panel" id="formTambahSemester" hidden>
      <?= form_open('tahfidz/semester/add'); ?>
      <div class="m-field">
        <label>Semester *</label>
        <input type="text" name="semester" required autocomplete="off">
      </div>
      <button type="submit" class="m-btn"><i class="fas fa-save"></i> Simpan</button>
      <?= form_close(); ?>
    </div>

    <button type="button" class="m-btn mt-2" id="btnModeUbahSemester" style="background:#fff; color:var(--navy); border:1px solid var(--navy);">
      <i class="fas fa-pen"></i> Ubah
    </button>
    <a href="<?= base_url('tahfidz/semester/export_excel'); ?>" target="_blank" class="m-btn m-btn-outline mt-2"><i class="fas fa-file-excel"></i> Export Data</a>
  </div>

  <div class="m-card" id="mSemesterList">
    <?php if ($semester) : ?>
      <?php foreach ($semester as $sm) : ?>
        <div class="m-dana-item-wrapper">
          <div class="m-list-item">
            <div class="m-list-title"><?= html_escape($sm['Semester']); ?></div>
          </div>

          <div class="m-dana-actions">
            <button type="button" class="m-dana-btn-ubah" data-toggle-target="#editSemester<?= $sm['IdSemester']; ?>">Ubah</button>
            <a href="<?= base_url('tahfidz/semester/delete/' . $sm['IdSemester']); ?>" class="m-dana-btn-hapus tombol-hapus" tipeData="Semester" namaData="<?= html_escape($sm['Semester']); ?>">Hapus</a>
          </div>

          <div class="m-form-panel" id="editSemester<?= $sm['IdSemester']; ?>" hidden>
            <?= form_open('tahfidz/semester/update/' . $sm['IdSemester']); ?>
            <div class="m-field">
              <label>Semester *</label>
              <input type="text" name="semester" value="<?= html_escape($sm['Semester']); ?>" required autocomplete="off">
            </div>
            <button type="submit" class="m-btn"><i class="fas fa-save"></i> Simpan Perubahan</button>
            <?= form_close(); ?>
          </div>
        </div>
      <?php endforeach; ?>
    <?php else : ?>
      <p class="m-empty">Belum ada data semester.</p>
    <?php endif; ?>
  </div>
</div>

<script>
  var btnModeUbahSemester = document.getElementById('btnModeUbahSemester');
  var daftarSemester = document.getElementById('mSemesterList');
  if (btnModeUbahSemester && daftarSemester) {
    btnModeUbahSemester.addEventListener('click', function () {
      var aktif = daftarSemester.classList.toggle('m-dana-edit-mode');
      btnModeUbahSemester.innerHTML = aktif
        ? '<i class="fas fa-check"></i> Selesai'
        : '<i class="fas fa-pen"></i> Ubah';
    });
  }
</script>
