<div class="m-content m-content-pad-bottombar">
  <p class="m-page-title">Data Santri</p>

  <div class="m-card">
    <form action="<?= base_url('Santri/cari_data'); ?>" method="POST">
      <div class="m-field" style="margin-bottom: 0; display:flex; gap:8px;">
        <input type="text" name="nama_santri" placeholder="Cari nama santri" style="flex:1;">
        <button type="submit" class="m-btn" style="width:auto; padding:0 16px;"><i class="fas fa-search"></i></button>
      </div>
    </form>
  </div>

  <div class="m-card">
    <button type="button" class="m-btn m-btn-outline" data-toggle-target="#formImportSantri">
      <i class="fas fa-file-import"></i> Import Data
    </button>
    <div class="m-form-panel" id="formImportSantri" hidden>
      <p style="font-size:12px; color:var(--text-muted);">Gunakan hasil <strong>Export Data</strong> sebagai template: isi kolom Password yang kosong tiap baris, lalu upload kembali file itu.</p>
      <?= form_open_multipart('santri/import'); ?>
      <div class="m-field">
        <label>Pilih File (.xls/.xlsx)</label>
        <input type="file" name="importSiswa" accept=".xls,.xlsx">
      </div>
      <button type="submit" class="m-btn"><i class="fas fa-save"></i> Import</button>
      <?= form_close(); ?>
    </div>
  </div>

  <div id="mSantriList">
    <?php if ($santri) : ?>
      <?php
      $warna_status = ['Aktif' => '#28a745', 'Lulus' => '#17a2b8', 'Pindah' => '#ffc107', 'Berhenti' => '#dc3545'];
      foreach ($santri as $san) :
        $warna = isset($warna_status[$san['Status']]) ? $warna_status[$san['Status']] : '#6c757d';
      ?>
        <div class="m-swipe-wrap">
          <div class="m-swipe-actions">
            <a href="<?= base_url('santri/delete/' . $san['IdSiswa']); ?>" class="tombol-hapus" tipeData="Santri" namaData="<?= html_escape($san['NIS']); ?>">
              <i class="fas fa-trash"></i> Hapus
            </a>
          </div>
          <div class="m-card m-santri-card m-swipe-item" style="border-left: 4px solid <?= $warna; ?>;" data-detail-url="<?= base_url('santri/detail/' . $san['IdSiswa']); ?>">
            <div class="m-absen-card">
              <?php if (!empty($san['Pasfoto'])) : ?>
                <img src="<?= upload_url('santri', $san['Pasfoto']); ?>" class="m-absen-photo m-santri-photo" data-fallback-class="m-absen-photo-fallback m-santri-photo" data-fallback-text="<?= html_escape(strtoupper(substr($san['NamaLengkap'], 0, 1))); ?>" onerror="mFotoGagalMuat(this)">
              <?php else : ?>
                <div class="m-absen-photo-fallback m-santri-photo"><?= strtoupper(substr($san['NamaLengkap'], 0, 1)); ?></div>
              <?php endif; ?>
              <div class="m-absen-info">
                <div class="m-absen-nama"><?= html_escape($san['NamaLengkap']); ?></div>
                <div class="m-absen-sub"><i class="fas fa-id-card"></i> <?= html_escape($san['NIS']); ?></div>
                <div class="m-absen-sub"><i class="fas fa-school"></i> <?= !empty($san['NamaKelas']) ? html_escape($san['NamaKelas']) : 'Belum ada kelas'; ?> &middot; <?= !empty($san['JenisKelamin']) ? html_escape($san['JenisKelamin']) : '-'; ?></div>
                <div class="m-santri-status-form">
                  <?= form_open('santri/ubah_status/' . $san['IdSiswa']); ?>
                  <select name="status" onchange="this.form.submit()" class="m-santri-status-select" style="background-color: <?= $warna; ?>;">
                    <?php foreach (['Aktif', 'Lulus', 'Pindah', 'Berhenti'] as $st) : ?>
                      <option value="<?= $st; ?>" <?= $st === $san['Status'] ? 'selected' : ''; ?>><?= $st; ?></option>
                    <?php endforeach; ?>
                  </select>
                  <?= form_close(); ?>
                </div>
              </div>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    <?php else : ?>
      <div class="m-card">
        <p class="m-empty">Belum ada data santri.</p>
      </div>
    <?php endif; ?>
  </div>
</div>

<div class="m-bottombar">
  <a href="<?= base_url('santri/tambah'); ?>" class="m-btn"><i class="fas fa-plus"></i> Tambah Santri</a>
</div>
