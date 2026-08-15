<div class="m-content">
  <p class="m-page-title">Info Penting</p>

  <div class="m-card">
    <button type="button" class="m-btn" data-toggle-target="#formTambahPengumuman">
      <i class="fas fa-plus"></i> Tambah Pengumuman
    </button>

    <div class="m-form-panel" id="formTambahPengumuman" hidden>
      <?= form_open('pengumuman/add'); ?>
      <div class="m-field">
        <label>Judul *</label>
        <input type="text" name="judul" required autocomplete="off">
      </div>
      <div class="m-field">
        <label>Isi *</label>
        <textarea name="isi" rows="4" required></textarea>
      </div>
      <button type="submit" class="m-btn"><i class="fas fa-save"></i> Simpan</button>
      <?= form_close(); ?>
    </div>

    <button type="button" class="m-btn mt-2" id="btnModeUbahPengumuman" style="background:#fff; color:var(--navy); border:1px solid var(--navy);">
      <i class="fas fa-pen"></i> Ubah
    </button>
  </div>

  <div class="m-card" id="mPengumumanList">
    <?php if ($pengumuman) : ?>
      <?php foreach ($pengumuman as $p) : ?>
        <div class="m-dana-item-wrapper">
          <div class="m-list-item" style="flex-direction: column; align-items: stretch;">
            <div>
              <div class="m-list-title"><?= html_escape($p['Judul']); ?></div>
              <div class="m-list-sub" style="white-space:pre-line;"><?= html_escape($p['Isi']); ?></div>
              <div class="m-list-sub mt-1"><?= html_escape($p['DibuatOleh']); ?> &middot; <?= date('d F Y, H:i', strtotime($p['CreatedAt'])); ?></div>
            </div>
          </div>

          <div class="m-dana-actions">
            <button type="button" class="m-dana-btn-ubah" data-toggle-target="#editPengumuman<?= $p['IdPengumuman']; ?>">Ubah</button>
            <a href="<?= base_url('pengumuman/delete/' . $p['IdPengumuman']); ?>" class="m-dana-btn-hapus tombol-hapus" tipeData="Pengumuman" namaData="<?= html_escape($p['Judul']); ?>">Hapus</a>
          </div>

          <div class="m-form-panel" id="editPengumuman<?= $p['IdPengumuman']; ?>" hidden>
            <?= form_open('pengumuman/update/' . $p['IdPengumuman']); ?>
            <div class="m-field">
              <label>Judul *</label>
              <input type="text" name="judul" value="<?= html_escape($p['Judul']); ?>" required autocomplete="off">
            </div>
            <div class="m-field">
              <label>Isi *</label>
              <textarea name="isi" rows="4" required><?= html_escape($p['Isi']); ?></textarea>
            </div>
            <button type="submit" class="m-btn"><i class="fas fa-save"></i> Simpan Perubahan</button>
            <?= form_close(); ?>
          </div>
        </div>
      <?php endforeach; ?>
    <?php else : ?>
      <p class="m-empty">Belum ada pengumuman.</p>
    <?php endif; ?>
  </div>
</div>

<script>
  var btnModeUbahPengumuman = document.getElementById('btnModeUbahPengumuman');
  var daftarPengumuman = document.getElementById('mPengumumanList');
  if (btnModeUbahPengumuman && daftarPengumuman) {
    btnModeUbahPengumuman.addEventListener('click', function () {
      var aktif = daftarPengumuman.classList.toggle('m-dana-edit-mode');
      btnModeUbahPengumuman.innerHTML = aktif
        ? '<i class="fas fa-check"></i> Selesai'
        : '<i class="fas fa-pen"></i> Ubah';
    });
  }
</script>
