<div class="m-content">
  <p class="m-page-title"><?= html_escape($title); ?></p>

  <div class="m-card">
    <form action="<?= base_url('ujian/hasil_ujian/perankingan'); ?>">
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
      <button type="submit" class="m-btn"><i class="fas fa-users"></i> Tampil Perankingan</button>
    </form>
  </div>

  <?php if ($ranking_santri) : ?>
    <div class="m-card">
      <p class="m-card-title" style="margin-bottom: 8px;">Hasil Perankingan</p>
      <form action="<?= base_url('ujian/hasil_ujian/proses_perankingan'); ?>" method="POST">
        <?php $i = 1; foreach ($ranking_santri as $rs) : ?>
          <div class="m-list-item">
            <div>
              <div class="m-list-title"><?= html_escape($rs['NamaLengkap']); ?></div>
              <div class="m-list-sub"><?= html_escape($rs['NamaKelas'] . ' | ' . $rs['Periode']); ?></div>
              <div class="m-list-sub">Rata-rata: <?= round($rs['Rata-rata'], 1); ?> &middot; <?= html_escape($rs['Reward']); ?></div>
            </div>
            <span class="m-badge m-badge-selesai">#<?= $i; ?></span>
          </div>
          <input type="hidden" name="IdHasil[]" value="<?= $rs['IdHasil']; ?>">
          <input type="hidden" name="IdSiswa[]" value="<?= $rs['IdSiswa']; ?>">
          <input type="hidden" name="IdPeriodeUjian[]" value="<?= $rs['IdPeriodeUjian']; ?>">
          <input type="hidden" name="TotalNilai[]" value="<?= $rs['Total']; ?>">
          <input type="hidden" name="RataRata[]" value="<?= $rs['Rata-rata']; ?>">
          <input type="hidden" name="Reward[]" value="<?= $rs['Reward']; ?>">
          <input type="hidden" name="Ranking[]" value="<?= $i++; ?>">
        <?php endforeach; ?>
        <button type="submit" class="m-btn mt-2"><i class="fas fa-users"></i> Proses Perankingan</button>
      </form>
    </div>
  <?php endif; ?>
</div>
