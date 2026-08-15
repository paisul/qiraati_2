<div class="m-content">
  <p class="m-page-title">Data Pengesahan</p>

  <div class="m-card">
    <button type="button" class="m-btn" data-toggle-target="#formTambahPengesahan">
      <i class="fas fa-plus"></i> Tambah Data
    </button>

    <div class="m-form-panel" id="formTambahPengesahan" hidden>
      <?= form_open_multipart('pengesahan/add'); ?>
      <div class="m-field">
        <label>Nama Lengkap *</label>
        <input type="text" name="nama" required autocomplete="off">
      </div>
      <div class="m-field">
        <label>Jabatan *</label>
        <select name="jabatan" required>
          <option value="">-- Pilih Jabatan --</option>
          <option value="Pengasuh PP Taruna Al Quran">Pengasuh PP Taruna Al Quran</option>
          <option value="Direktur Tahfidzul Quran">Direktur Tahfidzul Quran</option>
        </select>
      </div>
      <div class="m-field">
        <label>NIP *</label>
        <input type="text" name="nip" required autocomplete="off">
      </div>
      <div class="m-field">
        <label>Tanda Tangan * <small>(jpg/png, maks 2MB)</small></label>
        <input type="file" name="ttd" accept=".jpg,.jpeg,.png" required>
      </div>
      <div class="m-field">
        <label>Status *</label>
        <select name="status" required>
          <option value="">-- Pilih Status --</option>
          <option value="Aktif">Aktif</option>
          <option value="Tidak Aktif">Tidak Aktif</option>
        </select>
      </div>
      <button type="submit" class="m-btn"><i class="fas fa-save"></i> Simpan</button>
      <?= form_close(); ?>
    </div>

    <button type="button" class="m-btn mt-2" id="btnModeUbahPengesahan" style="background:#fff; color:var(--navy); border:1px solid var(--navy);">
      <i class="fas fa-pen"></i> Ubah
    </button>
  </div>

  <div class="m-card" id="mPengesahanList">
    <?php if ($pengesahan) : ?>
      <?php foreach ($pengesahan as $p) : ?>
        <div class="m-dana-item-wrapper">
          <div class="m-list-item">
            <div>
              <div class="m-list-title"><?= html_escape($p['Nama']); ?></div>
              <div class="m-list-sub"><?= html_escape($p['Jabatan']); ?> &middot; NIP <?= html_escape($p['Nip']); ?></div>
            </div>
            <span class="m-badge <?= $p['Status'] === 'Aktif' ? 'm-badge-selesai' : 'm-badge-belum'; ?>"><?= html_escape($p['Status']); ?></span>
          </div>

          <div class="m-dana-actions">
            <button type="button" class="m-dana-btn-ubah" data-toggle-target="#editPengesahan<?= $p['IdPengesahan']; ?>">Ubah</button>
            <a href="<?= base_url('pengesahan/delete/' . $p['IdPengesahan']); ?>" class="m-dana-btn-hapus tombol-hapus" tipeData="Pengesahan" namaData="<?= html_escape($p['Nama']); ?>">Hapus</a>
          </div>

          <div class="m-form-panel" id="editPengesahan<?= $p['IdPengesahan']; ?>" hidden>
            <?= form_open_multipart('pengesahan/update/' . $p['IdPengesahan']); ?>
            <div class="m-field">
              <label>Nama Lengkap *</label>
              <input type="text" name="nama" value="<?= html_escape($p['Nama']); ?>" required>
            </div>
            <div class="m-field">
              <label>Jabatan *</label>
              <select name="jabatan" required>
                <option value="Pengasuh PP Taruna Al Quran" <?= $p['Jabatan'] === 'Pengasuh PP Taruna Al Quran' ? 'selected' : ''; ?>>Pengasuh PP Taruna Al Quran</option>
                <option value="Direktur Tahfidzul Quran" <?= $p['Jabatan'] === 'Direktur Tahfidzul Quran' ? 'selected' : ''; ?>>Direktur Tahfidzul Quran</option>
              </select>
            </div>
            <div class="m-field">
              <label>NIP *</label>
              <input type="text" name="nip" value="<?= html_escape($p['Nip']); ?>" required autocomplete="off">
            </div>
            <div class="m-field">
              <label>Tanda Tangan <small>(jpg/png)</small> <?= $p['Ttd'] ? '- sudah tersimpan' : ''; ?></label>
              <input type="file" name="ttd" accept=".jpg,.jpeg,.png">
            </div>
            <div class="m-field">
              <label>Status *</label>
              <select name="status" required>
                <option value="Aktif" <?= $p['Status'] === 'Aktif' ? 'selected' : ''; ?>>Aktif</option>
                <option value="Tidak Aktif" <?= $p['Status'] === 'Tidak Aktif' ? 'selected' : ''; ?>>Tidak Aktif</option>
              </select>
            </div>
            <button type="submit" class="m-btn"><i class="fas fa-save"></i> Simpan Perubahan</button>
            <?= form_close(); ?>
          </div>
        </div>
      <?php endforeach; ?>
    <?php else : ?>
      <p class="m-empty">Belum ada data pengesahan.</p>
    <?php endif; ?>
  </div>
</div>

<script>
  var btnModeUbahPengesahan = document.getElementById('btnModeUbahPengesahan');
  var daftarPengesahan = document.getElementById('mPengesahanList');
  if (btnModeUbahPengesahan && daftarPengesahan) {
    btnModeUbahPengesahan.addEventListener('click', function () {
      var aktif = daftarPengesahan.classList.toggle('m-dana-edit-mode');
      btnModeUbahPengesahan.innerHTML = aktif
        ? '<i class="fas fa-check"></i> Selesai'
        : '<i class="fas fa-pen"></i> Ubah';
    });
  }
</script>
