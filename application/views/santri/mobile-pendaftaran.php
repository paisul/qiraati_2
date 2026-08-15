<div class="m-content">
  <p class="m-page-title">Pendaftaran Santri Baru</p>

  <div class="m-card">
    <p style="margin:0; color:#6c757d; font-size:13px;">Pendaftaran yang masuk lewat halaman publik "Daftar Akun Baru" (Google Sign-In). Setujui untuk membuat data santri resmi (NIS otomatis), atau tolak jika tidak valid.</p>
  </div>

  <?php if ($pendaftaran) : ?>
    <?php foreach ($pendaftaran as $p) : ?>
      <div class="m-card">
        <div class="m-absen-card">
          <?php if (!empty($p['Pasfoto'])) : ?>
            <img src="<?= upload_url('santri', $p['Pasfoto']); ?>" class="m-absen-photo">
          <?php else : ?>
            <div class="m-absen-photo-fallback"><?= strtoupper(substr($p['NamaLengkap'], 0, 1)); ?></div>
          <?php endif; ?>
          <div class="m-absen-info">
            <div class="m-absen-nama"><?= html_escape($p['NamaLengkap']); ?></div>
            <div class="m-absen-sub"><?= html_escape($p['Email']); ?></div>
            <div class="m-absen-sub"><?= !empty($p['NamaKelas']) ? html_escape($p['NamaKelas'] . (!empty($p['NamaMusyrif']) ? ' - ' . $p['NamaMusyrif'] : '')) : '-'; ?> &middot; <?= html_escape($p['CreatedAt']); ?></div>
            <?php if ($this->Santri_M->isEmailTerpakai($p['Email'])) : ?>
              <span class="m-badge m-badge-l" style="margin-top:4px; display:inline-block;">
                <i class="fas fa-info-circle"></i> Email sudah terdaftar - akan disambungkan
              </span>
            <?php endif; ?>
          </div>
        </div>

        <div class="m-dana-actions">
          <a href="<?= base_url('santri/setujui_pendaftaran/' . $p['IdPendaftaran']); ?>" class="m-dana-btn-ubah" style="border-color:#28a745; color:#28a745;"><i class="fas fa-check"></i> Setujui</a>
          <a href="<?= base_url('santri/tolak_pendaftaran/' . $p['IdPendaftaran']); ?>" class="m-dana-btn-hapus tombol-hapus" tipeData="Pendaftaran" namaData="<?= html_escape($p['NamaLengkap']); ?>"><i class="fas fa-times"></i> Tolak</a>
        </div>
      </div>
    <?php endforeach; ?>
  <?php else : ?>
    <div class="m-card">
      <p class="m-empty">Tidak ada pendaftaran yang menunggu persetujuan.</p>
    </div>
  <?php endif; ?>
</div>
