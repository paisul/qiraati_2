<div class="m-content">
  <p class="m-page-title">Rekap Setoran per Kelompok</p>

  <div class="m-card">
    <form action="<?= base_url('administrasi'); ?>">
      <div class="m-field">
        <label>Pilih Periode Setoran</label>
        <select name="periode">
          <option value="">-- Pilih Periode Setoran --</option>
          <?php foreach ($periode as $p) : ?>
            <option value="<?= $p['IdPeriode']; ?>" <?= (isset($_GET['periode']) && $_GET['periode'] == $p['IdPeriode']) ? 'selected' : ''; ?>><?= html_escape($p['Periode']); ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="m-field">
        <label>Pilih Kelompok Halaqoh</label>
        <select name="kelompok">
          <option value="">-- Pilih Kelompok Halaqoh --</option>
          <?php foreach ($kelompok_halaqoh as $kh) : ?>
            <option value="<?= $kh['IdKelompok']; ?>" <?= (isset($_GET['kelompok']) && $_GET['kelompok'] == $kh['IdKelompok']) ? 'selected' : ''; ?>><?= html_escape($kh['NamaKelompok']); ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <button type="submit" class="m-btn"><i class="fas fa-book"></i> Tampil Rekap</button>
    </form>
  </div>

  <div class="m-card">
    <?php if ($rekap_setoran_kelompok) : ?>
      <?php foreach ($rekap_setoran_kelompok as $rekap) : ?>
        <div class="m-list-item">
          <div>
            <div class="m-list-title"><?= html_escape($rekap['NamaLengkap']); ?></div>
            <div class="m-list-sub">Pekan <?= html_escape($rekap['PekanRekap']); ?> &middot; <?= html_escape($rekap['Prosentase']); ?>% &middot; <?= html_escape($rekap['Hasil']); ?></div>
            <div class="m-list-sub">Target: <?= (int) $rekap['JmlTugas']; ?> &middot; Selesai: <?= (int) $rekap['JmlSetoran']; ?> &middot; Belum: <?= $rekap['Tidak_Selesai'] == 0 ? '-' : $rekap['Tidak_Selesai']; ?></div>
          </div>
        </div>
      <?php endforeach; ?>
    <?php else : ?>
      <p class="m-empty">Belum ada data rekap setoran untuk pilihan ini.</p>
    <?php endif; ?>
  </div>
</div>
