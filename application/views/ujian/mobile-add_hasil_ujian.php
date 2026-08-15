<div class="m-content">
  <p class="m-page-title"><?= html_escape($title); ?></p>

  <div class="m-card">
    <p class="m-card-title" style="margin-bottom: 8px;">1. Pilih Santri &amp; Periode</p>
    <form action="<?= base_url('ujian/Hasil_Ujian/form_add'); ?>">
      <div class="m-field">
        <label>Pilih Santri</label>
        <select name="santri">
          <option value="">-- Pilih Santri --</option>
          <?php foreach ($santri as $san) : ?>
            <option value="<?= $san['IdSiswa']; ?>" <?= $this->input->get('santri') == $san['IdSiswa'] ? 'selected' : ''; ?>><?= html_escape($san['NamaLengkap'] . ' | ' . $san['NamaKelas']); ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="m-field">
        <label>Pilih Periode Ujian</label>
        <select name="periodeujian">
          <option value="">-- Pilih Periode Ujian --</option>
          <?php foreach ($periode_ujian as $p_ujian) : ?>
            <option value="<?= $p_ujian['IdPeriodeUjian']; ?>" <?= $this->input->get('periodeujian') == $p_ujian['IdPeriodeUjian'] ? 'selected' : ''; ?>><?= html_escape($p_ujian['Periode']); ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <button type="submit" class="m-btn"><i class="fas fa-users"></i> Proses Nilai</button>
    </form>
  </div>

  <?php if ($data_santri) : ?>
    <div class="m-card">
      <p class="m-card-title" style="margin-bottom: 8px;">2. Hasil Perhitungan</p>
      <form action="<?= base_url('ujian/Hasil_Ujian/aksi_form_add'); ?>" method="POST">
        <?php foreach ($data_santri as $ds) :
          $reward = ($ds['Nilai'] >= 80 && $ds['Prosentase'] == 100) ? 'PULANG' : 'RIHLAH';
        ?>
          <div class="m-list-item">
            <div>
              <div class="m-list-title"><?= html_escape($ds['NamaLengkap']); ?></div>
              <div class="m-list-sub"><?= html_escape($ds['NamaKelas'] . ' | ' . $ds['Periode']); ?></div>
              <div class="m-list-sub">Total: <?= html_escape($ds['TotalNilai']); ?> &middot; Rata-rata: <?= html_escape($ds['rata_rata']); ?></div>
            </div>
            <span class="m-badge <?= $reward === 'PULANG' ? 'm-badge-selesai' : 'm-badge-belum'; ?>"><?= $reward; ?></span>
          </div>
          <input type="hidden" name="IdSiswa" value="<?= $ds['IdSiswa']; ?>">
          <input type="hidden" name="IdPeriodeUjian" value="<?= $ds['IdPeriodeUjian']; ?>">
          <input type="hidden" name="TotalNilai" value="<?= $ds['TotalNilai']; ?>">
          <input type="hidden" name="RataRata" value="<?= $ds['rata_rata']; ?>">
          <input type="hidden" name="Reward" value="<?= $reward; ?>">
        <?php endforeach; ?>
        <button type="submit" class="m-btn mt-2"><i class="fas fa-users"></i> Proses Reward</button>
      </form>
    </div>
  <?php endif; ?>
</div>
