<div class="m-content">
  <p class="m-page-title"><?= html_escape($title); ?></p>

  <div class="m-card">
    <?php foreach ($hasil_ujian as $hu) : ?>
      <form action="<?= base_url('ujian/hasil_ujian/aksi_update'); ?>" method="POST">
        <input type="hidden" name="IdHasil" value="<?= $hu['IdHasil']; ?>">
        <div class="m-field">
          <label>Nama Santri</label>
          <select name="IdSiswa" disabled>
            <option value="<?= $hu['IdSiswa']; ?>"><?= html_escape($hu['NamaLengkap']); ?></option>
          </select>
        </div>
        <div class="m-field">
          <label>Periode Ujian</label>
          <select name="periodeujian" disabled>
            <option value="<?= $hu['IdPeriodeUjian']; ?>"><?= html_escape($hu['periode']); ?></option>
          </select>
        </div>
        <div class="m-field">
          <label>Total Nilai</label>
          <input type="text" name="totalnilai" value="<?= html_escape($hu['Total']); ?>" readonly>
        </div>
        <div class="m-field">
          <label>Rata-Rata</label>
          <input type="text" name="ratarata" value="<?= html_escape($hu['Rata-rata']); ?>" readonly>
        </div>
        <div class="m-field">
          <label>Reward</label>
          <input type="text" name="reward" value="<?= html_escape($hu['Reward']); ?>">
        </div>
        <div class="m-field">
          <label>Ranking</label>
          <input type="text" name="ranking" value="<?= html_escape($hu['Rangking']); ?>" readonly>
        </div>
        <button type="submit" class="m-btn"><i class="fas fa-save"></i> Simpan</button>
        <a href="<?= base_url('ujian/hasil_ujian'); ?>" class="m-btn m-btn-outline mt-2"><i class="fas fa-arrow-left"></i> Kembali</a>
      </form>
    <?php endforeach; ?>
  </div>
</div>
