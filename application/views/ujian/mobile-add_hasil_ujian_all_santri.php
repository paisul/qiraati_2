<div class="m-content">
  <p class="m-page-title"><?= html_escape($title); ?></p>

  <div class="m-card">
    <p class="m-card-title" style="margin-bottom: 8px;">1. Pilih Kelas &amp; Periode</p>
    <form action="<?= base_url('ujian/Hasil_Ujian/form_add_banyak'); ?>">
      <div class="m-field">
        <label>Pilih Kelas</label>
        <select name="kelas">
          <option value="">-- Pilih Kelas --</option>
          <?php foreach ($kelas as $kls) : ?>
            <option value="<?= $kls['IdKelas']; ?>" <?= $this->input->get('kelas') == $kls['IdKelas'] ? 'selected' : ''; ?>><?= html_escape($kls['NamaKelas']); ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="m-field">
        <label>Pilih Periode Ujian</label>
        <select name="periodeujian">
          <option value="">-- Pilih Periode Ujian --</option>
          <?php foreach ($periode_ujian as $p_ujian) : ?>
            <option value="<?= $p_ujian['IdPeriodeUjian']; ?>" <?= $this->input->get('periodeujian') == $p_ujian['IdPeriodeUjian'] ? 'selected' : ''; ?>><?= html_escape($p_ujian['Periode'] . '|' . $p_ujian['Semester'] . '|' . $p_ujian['NamaKelas']); ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <button type="submit" class="m-btn"><i class="fas fa-users"></i> Proses Nilai dan Reward</button>
    </form>
  </div>

  <?php if ($data_santri) : ?>
    <div class="m-card">
      <p class="m-card-title" style="margin-bottom: 8px;">2. Hasil Perhitungan (<?= count($data_santri); ?> Santri)</p>
      <form action="<?= base_url('ujian/Hasil_Ujian/aksi_Hasil_Ujian_Kelas'); ?>" method="POST">
        <?php foreach ($data_santri as $ds) :
          if ($ds['Nilai'] >= 80 && $ds['Prosentase'] == 100) {
            $reward = 'PULANG';
          } elseif ($ds['Nilai'] < 80 && $ds['Prosentase'] == 100) {
            $reward = 'RIHLAH';
          } else {
            $reward = 'Tidak Dapat Reward';
          }
        ?>
          <div class="m-list-item">
            <div>
              <div class="m-list-title"><?= html_escape($ds['NamaLengkap']); ?></div>
              <div class="m-list-sub"><?= html_escape($ds['NamaKelas'] . ' | ' . $ds['Periode']); ?></div>
              <div class="m-list-sub">Total: <?= html_escape($ds['total_nilai']); ?> &middot; Rata-rata: <?= round($ds['rata_rata'], 1); ?></div>
            </div>
            <span class="m-badge <?= $reward === 'PULANG' ? 'm-badge-selesai' : 'm-badge-belum'; ?>"><?= html_escape($reward); ?></span>
          </div>
          <input type="hidden" name="IdSiswa[]" value="<?= $ds['IdSiswa']; ?>">
          <input type="hidden" name="IdKelas" value="<?= $ds['IdKelas']; ?>">
          <input type="hidden" name="IdPeriodeUjian" value="<?= $ds['IdPeriodeUjian']; ?>">
          <input type="hidden" name="TotalNilai[]" value="<?= $ds['total_nilai']; ?>">
          <input type="hidden" name="RataRata[]" value="<?= $ds['rata_rata']; ?>">
          <input type="hidden" name="Reward[]" value="<?= $reward; ?>">
        <?php endforeach; ?>
        <button type="submit" class="m-btn mt-2"><i class="fas fa-users"></i> Proses Hasil</button>
      </form>
    </div>
  <?php endif; ?>
</div>
