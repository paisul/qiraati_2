<div class="m-content">
  <p class="m-page-title">Setoran Santri</p>

  <div class="m-card">
    <form action="<?= base_url('Wali/Setoran'); ?>">
      <input type="hidden" name="IdSiswa" value="<?= $user['IdSiswa']; ?>">
      <div class="m-field">
        <select name="pekan" style="width:100%; padding:10px; border-radius:10px; border:1px solid #dde2e6; margin-bottom:8px;">
          <option>-- Pilih Minggu --</option>
          <?php foreach ($pekan as $p) : ?>
            <option value="<?= $p['Pekan']; ?>">Minggu Ke <?= $p['Pekan']; ?></option>
          <?php endforeach; ?>
        </select>
        <select name="periode" style="width:100%; padding:10px; border-radius:10px; border:1px solid #dde2e6;">
          <option>Pilih Periode</option>
          <?php foreach ($periode as $p) : ?>
            <option value="<?= $p['IdPeriode']; ?>"><?= html_escape($p['Periode']); ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <button class="m-btn" type="submit">Tampilkan Setoran</button>
    </form>
  </div>

  <div class="m-card">
    <?php if ($setoran_santri) : ?>
      <?php foreach ($setoran_santri as $s) : ?>
        <div class="m-list-item">
          <div>
            <div class="m-list-title">Pekan <?= html_escape($s['Pekan']); ?> - <?= html_escape($s['IsiTarget']); ?></div>
            <div class="m-list-sub"><?= date_indo($s['Tgl']); ?></div>
          </div>
          <span class="m-badge <?= $s['Keterangan'] === 'Selesai' ? 'm-badge-selesai' : 'm-badge-belum'; ?>">
            <?= html_escape($s['Keterangan']); ?>
          </span>
        </div>
      <?php endforeach; ?>
    <?php else : ?>
      <p class="m-empty">Belum ada data setoran untuk pilihan ini.</p>
    <?php endif; ?>
  </div>
</div>
