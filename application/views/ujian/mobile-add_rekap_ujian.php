<div class="m-content">
  <p class="m-page-title"><?= html_escape($title); ?></p>

  <div class="m-card">
    <form action="<?= base_url('ujian/rekap_ujian/add'); ?>" method="POST">
      <div class="m-field">
        <label>Periode Ujian</label>
        <select name="periode_ujian">
          <option value="">-- Pilih Periode Ujian --</option>
          <?php foreach ($periode_ujian as $pu) : ?>
            <option value="<?= $pu['IdPeriodeUjian']; ?>"><?= html_escape($pu['Periode'] . ' | (' . $pu['Semester'] . ' - ' . $pu['ThAjaran'] . ') / ' . $pu['NamaKelas']); ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="m-field">
        <label>Kelas</label>
        <select name="kelas" id="mRekapUjianKelas">
          <option value="">-- Pilih Kelas --</option>
          <?php foreach ($kelas as $kls) : ?>
            <option value="<?= $kls['IdKelas']; ?>"><?= html_escape($kls['NamaKelas']); ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="m-field">
        <label>Target Ujian</label>
        <select name="target_ujian" id="mRekapUjianTarget">
          <option value="">-- Pilih Target Ujian --</option>
        </select>
      </div>
      <div class="m-field">
        <label>Nama Santri</label>
        <select name="nama_santri" id="mRekapUjianSantri">
          <option value="">-- Pilih Santri --</option>
        </select>
      </div>
      <div class="m-field">
        <label>Nilai *</label>
        <input type="text" id="mRekapUjianNilai" name="nilai" required autocomplete="off">
      </div>
      <div class="m-field">
        <label>Predikat</label>
        <input type="text" id="mRekapUjianPredikat" name="predikat" readonly>
      </div>
      <div class="m-field">
        <label>Keterangan</label>
        <input type="text" id="mRekapUjianKeterangan" name="keterangan" readonly>
      </div>
      <button type="submit" class="m-btn"><i class="fas fa-save"></i> Simpan</button>
      <a href="<?= base_url('ujian/rekap_ujian'); ?>" class="m-btn m-btn-outline mt-2"><i class="fas fa-times"></i> Batal</a>
    </form>
  </div>
</div>

<script>
  var selKelas = document.getElementById('mRekapUjianKelas');
  var selTarget = document.getElementById('mRekapUjianTarget');
  var selSantri = document.getElementById('mRekapUjianSantri');
  if (selKelas) {
    selKelas.addEventListener('change', function () {
      fetch('<?= base_url('ujian/Rekap_Ujian/getTargetByKelas'); ?>', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'id_kelas=' + encodeURIComponent(selKelas.value)
      })
        .then(function (res) { return res.text(); })
        .then(function (html) { selTarget.innerHTML = html; })
        .catch(function () {});

      fetch('<?= base_url('ujian/Rekap_Ujian/getSantriByKelas'); ?>', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'id_kelas=' + encodeURIComponent(selKelas.value)
      })
        .then(function (res) { return res.text(); })
        .then(function (html) { selSantri.innerHTML = html; })
        .catch(function () {});
    });
  }

  var inputNilai = document.getElementById('mRekapUjianNilai');
  var inputPredikat = document.getElementById('mRekapUjianPredikat');
  var inputKeterangan = document.getElementById('mRekapUjianKeterangan');
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
