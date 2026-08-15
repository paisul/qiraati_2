<div class="m-content">
  <p class="m-page-title">Data Ajaran</p>

  <div class="m-card">
    <button type="button" class="m-btn" data-toggle-target="#formTambahAjaran">
      <i class="fas fa-plus"></i> Tambah Data
    </button>

    <div class="m-form-panel" id="formTambahAjaran" hidden>
      <?= form_open('tahfidz/ajaran/add'); ?>
      <div class="m-field">
        <label>Tahun Ajaran *</label>
        <input type="text" name="ajaran" required autocomplete="off">
      </div>
      <button type="submit" class="m-btn"><i class="fas fa-save"></i> Simpan</button>
      <?= form_close(); ?>
    </div>

    <button type="button" class="m-btn mt-2" id="btnModeUbahAjaran" style="background:#fff; color:var(--navy); border:1px solid var(--navy);">
      <i class="fas fa-pen"></i> Ubah
    </button>
    <a href="<?= base_url('tahfidz/ajaran/export_excel'); ?>" target="_blank" class="m-btn m-btn-outline mt-2"><i class="fas fa-file-excel"></i> Export Data</a>
  </div>

  <div class="m-card" id="mAjaranList">
    <?php if ($ajaran) : ?>
      <?php foreach ($ajaran as $aj) : ?>
        <div class="m-dana-item-wrapper">
          <div class="m-list-item">
            <div class="m-list-title"><?= html_escape($aj['ThAjaran']); ?></div>
          </div>

          <div class="m-dana-actions">
            <button type="button" class="m-dana-btn-ubah" data-toggle-target="#editAjaran<?= $aj['IdAjaran']; ?>">Ubah</button>
            <a href="<?= base_url('tahfidz/ajaran/delete/' . $aj['IdAjaran']); ?>" class="m-dana-btn-hapus tombol-hapus" tipeData="Tahun Ajaran" namaData="<?= html_escape($aj['ThAjaran']); ?>">Hapus</a>
          </div>

          <div class="m-form-panel" id="editAjaran<?= $aj['IdAjaran']; ?>" hidden>
            <?= form_open('tahfidz/ajaran/update/' . $aj['IdAjaran']); ?>
            <div class="m-field">
              <label>Tahun Ajaran *</label>
              <input type="text" name="ajaran" value="<?= html_escape($aj['ThAjaran']); ?>" required autocomplete="off">
            </div>
            <button type="submit" class="m-btn"><i class="fas fa-save"></i> Simpan Perubahan</button>
            <?= form_close(); ?>
          </div>
        </div>
      <?php endforeach; ?>
    <?php else : ?>
      <p class="m-empty">Belum ada data ajaran.</p>
    <?php endif; ?>
  </div>
</div>

<script>
  var btnModeUbahAjaran = document.getElementById('btnModeUbahAjaran');
  var daftarAjaran = document.getElementById('mAjaranList');
  if (btnModeUbahAjaran && daftarAjaran) {
    btnModeUbahAjaran.addEventListener('click', function () {
      var aktif = daftarAjaran.classList.toggle('m-dana-edit-mode');
      btnModeUbahAjaran.innerHTML = aktif
        ? '<i class="fas fa-check"></i> Selesai'
        : '<i class="fas fa-pen"></i> Ubah';
    });
  }
</script>
