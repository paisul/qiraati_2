<div class="m-content">
  <p class="m-page-title">Raport</p>

  <div class="m-card">
    <p class="m-card-title" style="margin-bottom: 8px;">Cetak Laporan Ujian Bulanan</p>

    <form action="<?= base_url('Raport/aksi_tampil'); ?>" method="POST">
      <div class="m-field">
        <label>Pilih Santri *</label>
        <select name="siswa" required>
          <option value="">-- Pilih Santri --</option>
          <?php foreach ($santri as $san) : ?>
            <option value="<?= $san['IdSiswa']; ?>"><?= html_escape($san['NamaKelas'] . ' | ' . $san['NamaLengkap']); ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="m-field">
        <label>Pilih Kelas *</label>
        <select name="kelas" required>
          <option value="">-- Pilih Kelas --</option>
          <?php foreach ($kelas as $kls) : ?>
            <option value="<?= $kls['IdKelas']; ?>"><?= html_escape($kls['NamaKelas']); ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="m-field">
        <label>Pilih Periode Ujian *</label>
        <select name="periode_ujian" required>
          <option value="">-- Pilih Periode Ujian --</option>
          <?php foreach ($periode_ujian as $pu) : ?>
            <option value="<?= $pu['IdPeriodeUjian']; ?>"><?= html_escape($pu['Periode'] . ' | ' . $pu['KetPeriode'] . ' | ' . $pu['NamaKelas']); ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <button type="submit" class="m-btn"><i class="far fa-file-pdf"></i> Tampil Raport</button>
    </form>
  </div>
</div>
