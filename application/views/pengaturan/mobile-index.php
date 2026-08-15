<div class="m-content">
  <p class="m-page-title">Pengaturan Akun</p>

  <div class="m-card">
    <button type="button" class="m-btn" data-toggle-target="#formTambahPengguna">
      <i class="fas fa-plus"></i> Tambah Data
    </button>

    <div class="m-form-panel" id="formTambahPengguna" hidden>
      <?= form_open('pengaturan/add'); ?>
      <div class="m-field">
        <label>Username *</label>
        <input type="text" name="username" required autocomplete="off">
      </div>
      <div class="m-field">
        <label>Level</label>
        <select name="level">
          <option value="">-- Pilih Level --</option>
          <option value="Admin">Admin</option>
          <option value="Bagian Administrasi">Bagian Administrasi</option>
        </select>
        <small style="color:var(--text-muted);">Akun Wali/Musyrif/Santri dibuat lewat halaman masing-masing, bukan di sini.</small>
      </div>
      <div class="m-field">
        <label>Password *</label>
        <input type="password" name="password" required autocomplete="off">
      </div>
      <button type="submit" class="m-btn"><i class="fas fa-save"></i> Simpan</button>
      <?= form_close(); ?>
    </div>

    <button type="button" class="m-btn mt-2" id="btnModeUbahPengguna" style="background:#fff; color:var(--navy); border:1px solid var(--navy);">
      <i class="fas fa-pen"></i> Ubah
    </button>
  </div>

  <div class="m-card" id="mPenggunaList">
    <?php if ($pengguna) : ?>
      <?php foreach ($pengguna as $akun) : ?>
        <div class="m-dana-item-wrapper">
          <div class="m-list-item">
            <div>
              <div class="m-list-title">
                <?php if (!empty($akun['IdSiswa'])) : ?>
                  <a href="<?= base_url('santri/detail/' . $akun['IdSiswa']); ?>"><?= html_escape($akun['username']); ?></a>
                <?php elseif (!empty($akun['IdMusyrif'])) : ?>
                  <a href="<?= base_url('musyrif#editMusyrif' . $akun['IdMusyrif']); ?>"><?= html_escape($akun['username']); ?></a>
                <?php else : ?>
                  <?= html_escape($akun['username']); ?>
                <?php endif; ?>
              </div>
              <div class="m-list-sub"><?= html_escape($akun['level']); ?></div>
            </div>
            <?php if (strcasecmp($akun['username'], 'turanisia.tv@gmail.com') === 0) : ?>
              <span class="m-badge m-badge-secondary" style="background:#6c757d;"><i class="fas fa-lock"></i> Admin Utama</span>
            <?php endif; ?>
          </div>

          <div class="m-dana-actions">
            <button type="button" class="m-dana-btn-ubah" data-toggle-target="#editPengguna<?= $akun['IdUser']; ?>">Ubah</button>
            <?php if (strcasecmp($akun['username'], 'turanisia.tv@gmail.com') !== 0) : ?>
              <a href="<?= base_url('pengaturan/delete/' . $akun['IdUser']); ?>" class="m-dana-btn-hapus tombol-hapus" tipeData="Akun" namaData="<?= html_escape($akun['username']); ?>">Hapus</a>
            <?php endif; ?>
          </div>

          <div class="m-form-panel" id="editPengguna<?= $akun['IdUser']; ?>" hidden>
            <?= form_open('pengaturan/update/' . $akun['IdUser']); ?>
            <div class="m-field">
              <label>Username *</label>
              <input type="text" name="username" value="<?= html_escape($akun['username']); ?>" required autocomplete="off">
            </div>
            <div class="m-field">
              <label>Level</label>
              <select name="level">
                <option value="Admin" <?= $akun['level'] === 'Admin' ? 'selected' : ''; ?>>Admin</option>
                <option value="Bagian Administrasi" <?= $akun['level'] === 'Bagian Administrasi' ? 'selected' : ''; ?>>Bagian Administrasi</option>
                <option value="Wali" <?= $akun['level'] === 'Wali' ? 'selected' : ''; ?>>Wali</option>
                <option value="Musyrif" <?= $akun['level'] === 'Musyrif' ? 'selected' : ''; ?>>Musyrif</option>
              </select>
            </div>
            <div class="m-field">
              <label>Password Baru *</label>
              <input type="password" name="password" required autocomplete="off">
            </div>
            <button type="submit" class="m-btn"><i class="fas fa-save"></i> Simpan Perubahan</button>
            <?= form_close(); ?>
          </div>
        </div>
      <?php endforeach; ?>
    <?php else : ?>
      <p class="m-empty">Belum ada data pengguna.</p>
    <?php endif; ?>
  </div>
</div>

<script>
  var btnModeUbahPengguna = document.getElementById('btnModeUbahPengguna');
  var daftarPengguna = document.getElementById('mPenggunaList');
  if (btnModeUbahPengguna && daftarPengguna) {
    btnModeUbahPengguna.addEventListener('click', function () {
      var aktif = daftarPengguna.classList.toggle('m-dana-edit-mode');
      btnModeUbahPengguna.innerHTML = aktif
        ? '<i class="fas fa-check"></i> Selesai'
        : '<i class="fas fa-pen"></i> Ubah';
    });
  }
</script>
