<div class="m-content">
  <p class="m-page-title"><?= html_escape($title); ?></p>

  <div class="m-card">
    <form action="<?= base_url('ujian/rekap_ujian/update'); ?>" method="POST">
      <input type="hidden" name="IdUjian" value="<?= $rekap_ujian['IdUjian']; ?>">
      <div class="m-field">
        <label>Target Ujian</label>
        <select name="target_ujian">
          <option value="<?= $rekap_ujian['IdTargetUjian']; ?>" selected><?= html_escape($rekap_ujian['NamaUjian'] . ' | ' . $rekap_ujian['Keterangan']); ?></option>
          <?php foreach ($target_ujian as $tu) : ?>
            <option value="<?= $tu['IdTargetUjian']; ?>"><?= html_escape($tu['NamaUjian'] . ' | (' . $tu['Keterangan'] . ')'); ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="m-field">
        <label>Nama Santri</label>
        <select name="nama_santri">
          <option value="<?= $rekap_ujian['IdSiswa']; ?>" selected><?= html_escape($rekap_ujian['NamaLengkap']); ?></option>
          <?php foreach ($santri as $s) : ?>
            <option value="<?= $s['IdSiswa']; ?>"><?= html_escape($s['NamaLengkap'] . ' | ' . $s['NamaKelas']); ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="m-field">
        <label>Periode Ujian</label>
        <select name="periode_ujian">
          <option value="<?= $rekap_ujian['IdPeriodeUjian']; ?>" selected><?= html_escape($rekap_ujian['Periode'] . ' | (' . $rekap_ujian['Semester'] . ' - ' . $rekap_ujian['ThAjaran'] . ') / ' . $rekap_ujian['NamaKelas']); ?></option>
          <?php foreach ($periode_ujian as $pu) : ?>
            <option value="<?= $pu['IdPeriodeUjian']; ?>"><?= html_escape($pu['Periode'] . ' | (' . $pu['Semester'] . ' - ' . $pu['ThAjaran'] . ') / ' . $pu['NamaKelas']); ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="m-field">
        <label>Nilai *</label>
        <input type="text" id="mUpdateRekapUjianNilai" name="nilai" value="<?= html_escape($rekap_ujian['Nilai']); ?>" required autocomplete="off">
      </div>
      <div class="m-field">
        <label>Predikat</label>
        <input type="text" id="mUpdateRekapUjianPredikat" name="predikat" value="<?= html_escape($rekap_ujian['Predikat']); ?>" readonly>
      </div>
      <div class="m-field">
        <label>Keterangan</label>
        <input type="text" id="mUpdateRekapUjianKeterangan" name="keterangan" value="<?= html_escape($rekap_ujian['ket_rekap']); ?>" readonly>
      </div>
      <button type="submit" class="m-btn"><i class="fas fa-save"></i> Simpan</button>
      <a href="<?= base_url('ujian/rekap_ujian'); ?>" class="m-btn m-btn-outline mt-2"><i class="fas fa-times"></i> Batal</a>
    </form>
  </div>
</div>

<script>
  var inputNilai = document.getElementById('mUpdateRekapUjianNilai');
  var inputPredikat = document.getElementById('mUpdateRekapUjianPredikat');
  var inputKeterangan = document.getElementById('mUpdateRekapUjianKeterangan');
  if (inputNilai) {
    inputNilai.addEventListener('keyup', function () {
      fetch('<?= base_url('ujian/rekap_ujian/predikat_ket'); ?>', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'Nilai=' + encodeURIComponent(inputNilai.value)
      })
        .then(function (res) { return res.json(); })
        .then(function (json) {
          inputPredikat.value = json.PredikatNilai;
          inputKeterangan.value = json.KetNilai;
        })
        .catch(function () {});
    });
  }
</script>
