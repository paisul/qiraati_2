<div class="m-content">
  <p class="m-page-title">Hasil Ujian Santri (Rapor)</p>

  <div class="m-card">
    <form action="<?= base_url('Wali/preview'); ?>" method="POST">
      <input type="hidden" name="IdSiswa" value="<?= $user['IdSiswa']; ?>">
      <input type="hidden" name="IdKelas" value="<?= $user['IdKelas']; ?>">

      <div class="m-field">
        <label>Pilih Periode Ujian <small style="color:#dc3545; font-weight:400;">(sesuai kelas santri)</small></label>
        <select name="periode_ujian">
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
