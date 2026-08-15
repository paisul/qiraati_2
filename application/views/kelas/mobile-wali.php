<div class="m-content">
  <p class="m-page-title">Data Kelas</p>

  <?php if ($kelas) : ?>
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
      </div>
    <?php endforeach; ?>
  <?php else : ?>
    <p class="m-empty">Belum ada data kelas.</p>
  <?php endif; ?>
</div>
