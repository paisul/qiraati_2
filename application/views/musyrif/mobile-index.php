<div class="m-content">
  <p class="m-page-title">Data Musyrif</p>

  <?php if (validation_errors()) : ?>
    <div class="m-card" style="border: 1px solid #f5c6cb; background: #f8d7da; color: #721c24;">
      <?= validation_errors(); ?>
    </div>
  <?php endif; ?>

  <div class="m-card">
    <a href="<?= base_url('musyrif/export_excel'); ?>" target="_blank" class="m-btn m-btn-outline"><i class="fas fa-file-excel"></i> Export Data</a>

    <form class="mt-2" action="<?= base_url('Musyrif/cari_data'); ?>" method="POST">
      <div class="m-field">
        <label>Cari Nama Musyrif</label>
        <input type="text" name="nama_musyrif" placeholder="Masukkan Nama Musyrif">
      </div>
      <button type="submit" class="m-btn m-btn-outline"><i class="fas fa-search"></i> Cari</button>
    </form>
  </div>

  <div class="m-popup-overlay" id="formTambahMusyrif" hidden>
    <div class="m-popup-sheet">
      <?= form_open_multipart('musyrif', ['class' => 'm-popup-form']); ?>
      <div class="m-popup-header">
        <p class="m-popup-title">Tambah Data Musyrif</p>
        <button type="button" class="m-popup-close" data-toggle-target="#formTambahMusyrif"><i class="fas fa-times"></i></button>
      </div>
      <div class="m-popup-body">
        <div style="display:flex; gap:16px; margin-bottom:16px;">
          <label class="m-photo-upload m-photo-upload-onlight" for="inputPasfotoTambahMusyrif">
            <div class="m-field-photo-fallback" id="previewPasfotoTambahMusyrif" style="margin-bottom:0;"><i class="fas fa-user"></i></div>
            <span class="m-photo-upload-badge"><i class="fas fa-camera"></i></span>
            <input type="file" name="pasfoto" id="inputPasfotoTambahMusyrif" accept=".jpg,.jpeg,.png" data-photo-input="#previewPasfotoTambahMusyrif" hidden>
          </label>
          <label class="m-photo-upload m-photo-upload-onlight" for="inputTtdTambahMusyrif">
            <div class="m-photo-upload-ttd">
              <i class="fas fa-signature" id="previewTtdTambahMusyrif"></i>
            </div>
            <span class="m-photo-upload-badge"><i class="fas fa-camera"></i></span>
            <input type="file" name="ttd" id="inputTtdTambahMusyrif" accept=".jpg,.jpeg,.png" data-photo-input="#previewTtdTambahMusyrif" hidden>
          </label>
        </div>

        <div class="m-field">
          <label>Nama Lengkap *</label>
          <input type="text" name="nama_musyrif" required autocomplete="off">
        </div>
        <div class="m-field">
          <label>Email *</label>
          <input type="text" name="email" required autocomplete="off">
        </div>
        <div class="m-field">
          <label>Password *</label>
          <input type="password" name="password" required>
        </div>
        <div class="m-field" style="margin-bottom:0;">
          <label>No Handphone *</label>
          <input type="text" name="no_hp" required autocomplete="off">
        </div>
      </div>
      <div class="m-popup-footer">
        <button type="submit" class="m-btn m-btn-sticky" data-dirty-submit disabled><i class="fas fa-save"></i> Simpan</button>
      </div>
      <?= form_close(); ?>
    </div>
  </div>

  <div id="mMusyrifList">
    <?php if ($musyrif) : ?>
      <?php foreach ($musyrif as $m) : ?>
        <div class="m-dana-swipe-wrap">
          <div class="m-dana-swipe-actions-kiri">
            <button type="button" data-toggle-target="#editMusyrif<?= $m['IdMusyrif']; ?>">
              <i class="fas fa-pen"></i> Ubah
            </button>
          </div>
          <div class="m-dana-swipe-actions-kanan">
            <a href="<?= base_url('musyrif/delete/' . $m['IdMusyrif']); ?>" class="tombol-hapus" tipeData="Musyrif" namaData="<?= html_escape($m['NamaMusyrif']); ?>">
              <i class="fas fa-trash"></i> Hapus
            </a>
          </div>
          <div class="m-card m-dana-swipe-item">
            <div class="m-absen-card">
              <?php if (!empty($m['Pasfoto'])) : ?>
                <img src="<?= upload_url('pasfoto_musyrif', $m['Pasfoto']); ?>" class="m-absen-photo m-santri-photo" data-fallback-class="m-absen-photo-fallback m-santri-photo" data-fallback-text="<?= html_escape(strtoupper(substr($m['NamaMusyrif'], 0, 1))); ?>" onerror="mFotoGagalMuat(this)">
              <?php else : ?>
                <div class="m-absen-photo-fallback m-santri-photo"><?= strtoupper(substr($m['NamaMusyrif'], 0, 1)); ?></div>
              <?php endif; ?>
              <div class="m-absen-info">
                <div class="m-absen-nama"><?= html_escape($m['NamaMusyrif']); ?></div>
                <div class="m-absen-sub">
                  <button type="button" class="m-link-btn" data-toggle-target="#editMusyrif<?= $m['IdMusyrif']; ?>"><?= html_escape($m['Email']); ?></button>
                </div>
                <div class="m-absen-sub"><i class="fas fa-phone"></i> <?= html_escape($m['NoHp']); ?></div>
              </div>
            </div>
          </div>
        </div>

        <div class="m-popup-overlay" id="editMusyrif<?= $m['IdMusyrif']; ?>" hidden>
          <div class="m-popup-sheet">
            <?= form_open_multipart('musyrif/update/' . $m['IdMusyrif'], ['class' => 'm-popup-form']); ?>
            <div class="m-popup-header">
              <p class="m-popup-title">Ubah Data Musyrif</p>
              <button type="button" class="m-popup-close" data-toggle-target="#editMusyrif<?= $m['IdMusyrif']; ?>"><i class="fas fa-times"></i></button>
            </div>
            <div class="m-popup-body">
              <div style="display:flex; gap:16px; margin-bottom:16px;">
                <label class="m-photo-upload m-photo-upload-onlight" for="inputPasfotoMusyrif<?= $m['IdMusyrif']; ?>">
                  <?php if (!empty($m['Pasfoto'])) : ?>
                    <img src="<?= upload_url('pasfoto_musyrif', $m['Pasfoto']); ?>" class="m-field-photo-preview" id="previewPasfotoMusyrif<?= $m['IdMusyrif']; ?>" style="margin-bottom:0;" data-fallback-class="m-field-photo-fallback" data-fallback-icon="fas fa-user" data-fallback-style="margin-bottom:0;" onerror="mFotoGagalMuat(this)">
                  <?php else : ?>
                    <div class="m-field-photo-fallback" id="previewPasfotoMusyrif<?= $m['IdMusyrif']; ?>" style="margin-bottom:0;"><i class="fas fa-user"></i></div>
                  <?php endif; ?>
                  <span class="m-photo-upload-badge"><i class="fas fa-camera"></i></span>
                  <input type="file" name="pasfoto" id="inputPasfotoMusyrif<?= $m['IdMusyrif']; ?>" accept=".jpg,.jpeg,.png" data-photo-input="#previewPasfotoMusyrif<?= $m['IdMusyrif']; ?>" hidden>
                </label>
                <label class="m-photo-upload m-photo-upload-onlight" for="inputTtdMusyrif<?= $m['IdMusyrif']; ?>">
                  <div class="m-photo-upload-ttd">
                    <?php if (!empty($m['Ttd'])) : ?>
                      <img src="<?= upload_url('ttd_musyrif', $m['Ttd']); ?>" id="previewTtdMusyrif<?= $m['IdMusyrif']; ?>">
                    <?php else : ?>
                      <i class="fas fa-signature" id="previewTtdMusyrif<?= $m['IdMusyrif']; ?>"></i>
                    <?php endif; ?>
                  </div>
                  <span class="m-photo-upload-badge"><i class="fas fa-camera"></i></span>
                  <input type="file" name="ttd" id="inputTtdMusyrif<?= $m['IdMusyrif']; ?>" accept=".jpg,.jpeg,.png" data-photo-input="#previewTtdMusyrif<?= $m['IdMusyrif']; ?>" hidden>
                </label>
              </div>

              <div class="m-field">
                <label>Nama Lengkap *</label>
                <input type="text" name="nama_musyrif" value="<?= html_escape($m['NamaMusyrif']); ?>" required>
              </div>
              <div class="m-field">
                <label>Email *</label>
                <input type="text" name="email" value="<?= html_escape($m['Email']); ?>" required autocomplete="off">
              </div>
              <div class="m-field">
                <label>Password Baru</label>
                <input type="password" name="password" minlength="4" autocomplete="new-password">
                <small style="color:var(--text-muted);">Kosongkan jika tidak ingin mengubah password.</small>
              </div>
              <div class="m-field" style="margin-bottom:0;">
                <label>No Handphone *</label>
                <input type="text" name="no_hp" value="<?= html_escape($m['NoHp']); ?>" required autocomplete="off">
              </div>
            </div>
            <div class="m-popup-footer">
              <button type="submit" class="m-btn m-btn-sticky" data-dirty-submit disabled><i class="fas fa-save"></i> Simpan Perubahan</button>
            </div>
            <?= form_close(); ?>
          </div>
        </div>
      <?php endforeach; ?>
    <?php else : ?>
      <div class="m-card">
        <p class="m-empty">Belum ada data pembimbing.</p>
      </div>
    <?php endif; ?>
  </div>
</div>

<button type="button" class="m-fab" data-toggle-target="#formTambahMusyrif" aria-label="Tambah Data Musyrif">
  <i class="fas fa-plus"></i>
</button>

<script>
  // Dipanggil dari link "username" di halaman Pengaturan Akun (mis. #editMusyrif12) - buka
  // langsung popup Ubah Data Musyrif yang sesuai begitu halaman ini dimuat.
  (function () {
    var hash = window.location.hash;
    if (!hash || hash.indexOf('#editMusyrif') !== 0) return;

    var popup = document.querySelector(hash);
    if (popup) popup.hidden = false;
  })();
</script>
