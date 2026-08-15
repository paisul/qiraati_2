<div class="m-content m-content-pad-bottombar">
  <p class="m-page-title">Data Kelas</p>

  <?php if ($kelas) : ?>
    <div class="m-stat-row mb-3">
      <div class="m-card m-stat-primary">
        <p class="m-card-title">Total Kelas</p>
        <p class="m-card-value"><?= count($kelas); ?></p>
      </div>
      <div class="m-card m-stat-success">
        <p class="m-card-title">Total Santri</p>
        <p class="m-card-value"><?= array_sum(array_column($kelas, 'JumlahSantri')); ?></p>
      </div>
    </div>

    <?php foreach ($kelas as $kls) : ?>
      <div class="m-card">
        <div class="m-kelas-header">
          <div class="m-kelas-icon"><i class="fas fa-school"></i></div>
          <div>
            <div class="m-kelas-nama"><?= html_escape($kls['NamaKelas']); ?></div>
            <div class="m-kelas-sub">
              <i class="fas fa-chalkboard-teacher"></i>
              <?= !empty($kls['NamaMusyrif']) ? html_escape($kls['NamaMusyrif']) : 'Belum ditentukan'; ?>
            </div>
            <?php if (!empty($kls['Lokasi'])) : ?>
              <div class="m-kelas-sub"><i class="fas fa-map-marker-alt"></i> <?= html_escape($kls['Lokasi']); ?></div>
            <?php endif; ?>
          </div>
        </div>

        <div class="m-kelas-stats">
          <div class="m-kelas-stat"><i class="fas fa-mars" style="color:#007bff;"></i> <?= (int) $kls['JumlahLaki']; ?></div>
          <div class="m-kelas-stat"><i class="fas fa-venus" style="color:var(--pink);"></i> <?= (int) $kls['JumlahPerempuan']; ?></div>
          <div class="m-kelas-stat m-kelas-stat-total"><i class="fas fa-users"></i> <?= (int) $kls['JumlahSantri']; ?> Santri</div>
        </div>

        <div class="m-dana-actions">
          <button type="button" class="m-dana-btn-ubah" onclick="location.href='<?= base_url('kelas/ubah/' . $kls['IdKelas']); ?>'">Ubah</button>
          <?php if ((int) $kls['JumlahSantri'] > 0) : ?>
            <button type="button" class="m-dana-btn-hapus" data-toggle-target="#popupHapusKelas<?= $kls['IdKelas']; ?>">Hapus</button>
          <?php else : ?>
            <a href="<?= base_url('kelas/delete/' . $kls['IdKelas']); ?>" class="m-dana-btn-hapus tombol-hapus" tipeData="Kelas" namaData="<?= html_escape($kls['NamaKelas']); ?>">Hapus</a>
          <?php endif; ?>
        </div>
      </div>
    <?php endforeach; ?>

    <?php foreach ($kelas as $kls) : ?>
      <?php if ((int) $kls['JumlahSantri'] > 0) : ?>
        <div class="m-popup-overlay" id="popupHapusKelas<?= $kls['IdKelas']; ?>" hidden>
          <div class="m-popup-sheet">
            <?= form_open('kelas/hapus_dengan_pindah/' . $kls['IdKelas'], ['class' => 'm-popup-form']); ?>
            <div class="m-popup-header">
              <p class="m-popup-title">Hapus <?= html_escape($kls['NamaKelas']); ?></p>
              <button type="button" class="m-popup-close" data-toggle-target="#popupHapusKelas<?= $kls['IdKelas']; ?>"><i class="fas fa-times"></i></button>
            </div>
            <div class="m-popup-body">
              <p style="margin-top:0;">Kelas ini masih punya <strong><?= (int) $kls['JumlahSantri']; ?> santri</strong>. Pilih kelas tujuan untuk memindahkan mereka dulu - tanpa memilih kelas tujuan, kelas ini tidak bisa dihapus.</p>
              <div class="m-field" style="margin-bottom:0;">
                <label>Pindahkan Santri ke Kelas</label>
                <select name="kelas_tujuan" required>
                  <option value="">-- Pilih Kelas Tujuan --</option>
                  <?php foreach ($kelas as $tujuan) : ?>
                    <?php if ($tujuan['IdKelas'] != $kls['IdKelas']) : ?>
                      <option value="<?= $tujuan['IdKelas']; ?>"><?= html_escape($tujuan['NamaKelas']); ?><?= !empty($tujuan['NamaMusyrif']) ? ' - ' . html_escape($tujuan['NamaMusyrif']) : ''; ?></option>
                    <?php endif; ?>
                  <?php endforeach; ?>
                </select>
              </div>
            </div>
            <div class="m-popup-footer">
              <button type="submit" class="m-btn" style="background:#dc3545;"><i class="fas fa-exchange-alt"></i> Pindahkan &amp; Hapus Kelas</button>
            </div>
            <?= form_close(); ?>
          </div>
        </div>
      <?php endif; ?>
    <?php endforeach; ?>
  <?php else : ?>
    <p class="m-empty">Belum ada data kelas.</p>
  <?php endif; ?>
</div>

<div class="m-bottombar">
  <button type="button" class="m-btn" id="btnModeUbahKelas" style="background:#fff; color:var(--navy); border:1px solid var(--navy);">
    <i class="fas fa-pen"></i> Ubah
  </button>
  <a href="<?= base_url('kelas/tambah'); ?>" class="m-btn"><i class="fas fa-plus"></i> Tambah</a>
</div>

<script>
  var btnModeUbahKelas = document.getElementById('btnModeUbahKelas');
  if (btnModeUbahKelas) {
    btnModeUbahKelas.addEventListener('click', function () {
      var aktif = document.querySelector('.m-content').classList.toggle('m-dana-edit-mode');
      btnModeUbahKelas.innerHTML = aktif
        ? '<i class="fas fa-check"></i> Selesai'
        : '<i class="fas fa-pen"></i> Ubah';
    });
  }
</script>
