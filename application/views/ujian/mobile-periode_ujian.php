<div class="m-content">
  <p class="m-page-title">Periode Ujian</p>

  <div class="m-card">
    <button type="button" class="m-btn" data-toggle-target="#formTambahPeriodeUjian">
      <i class="fas fa-plus"></i> Tambah Data
    </button>

    <div class="m-form-panel" id="formTambahPeriodeUjian" hidden>
      <?= form_open('ujian/periode_ujian/add'); ?>
      <div class="m-field">
        <label>Periode *</label>
        <select name="periode" required>
          <option value="">-- Pilih Periode --</option>
          <?php foreach ($periode as $p) : ?>
            <option value="<?= $p['IdPeriode']; ?>"><?= html_escape($p['Periode']); ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="m-field">
        <label>Tahun Ajaran *</label>
        <select name="ajaran" required>
          <option value="">-- Pilih Tahun Ajaran --</option>
          <?php foreach ($tahun_ajaran as $ta) : ?>
            <option value="<?= $ta['IdAjaran']; ?>"><?= html_escape($ta['ThAjaran']); ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="m-field">
        <label>Semester *</label>
        <select name="semester" required>
          <option value="">-- Pilih Semester --</option>
          <?php foreach ($semester as $smt) : ?>
            <option value="<?= $smt['IdSemester']; ?>"><?= html_escape($smt['Semester']); ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="m-field">
        <label>Kelas *</label>
        <select name="kelas" required>
          <option value="">-- Pilih Kelas --</option>
          <?php foreach ($kelas as $kls) : ?>
            <option value="<?= $kls['IdKelas']; ?>"><?= html_escape($kls['NamaKelas']); ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="m-field">
        <label>Keterangan Periode *</label>
        <input type="text" name="KetPeriode" required autocomplete="off">
      </div>
      <button type="submit" class="m-btn"><i class="fas fa-save"></i> Simpan</button>
      <?= form_close(); ?>
    </div>

    <button type="button" class="m-btn mt-2" id="btnModeUbahPeriodeUjian" style="background:#fff; color:var(--navy); border:1px solid var(--navy);">
      <i class="fas fa-pen"></i> Ubah
    </button>
    <a href="<?= base_url('ujian/periode_ujian/export_excel'); ?>" target="_blank" class="m-btn m-btn-outline mt-2"><i class="fas fa-file-excel"></i> Export Data</a>
  </div>

  <div class="m-card" id="mPeriodeUjianList">
    <?php if ($periode_ujian) : ?>
      <?php foreach ($periode_ujian as $pu) : ?>
        <div class="m-dana-item-wrapper">
          <div class="m-list-item">
            <div>
              <div class="m-list-title"><?= html_escape($pu['NamaKelas']); ?> &middot; <?= html_escape($pu['Periode']); ?></div>
              <div class="m-list-sub"><?= html_escape($pu['ThAjaran'] . ' | ' . $pu['Semester']); ?></div>
              <div class="m-list-sub"><?= html_escape($pu['KetPeriode']); ?></div>
            </div>
          </div>

          <div class="m-dana-actions">
            <button type="button" class="m-dana-btn-ubah" data-toggle-target="#editPeriodeUjian<?= $pu['IdPeriodeUjian']; ?>">Ubah</button>
            <a href="<?= base_url('ujian/periode_ujian/delete/' . $pu['IdPeriodeUjian']); ?>" class="m-dana-btn-hapus tombol-hapus" tipeData="Periode Ujian" namaData="<?= html_escape($pu['NamaKelas']); ?>">Hapus</a>
          </div>

          <div class="m-form-panel" id="editPeriodeUjian<?= $pu['IdPeriodeUjian']; ?>" hidden>
            <?= form_open('ujian/periode_ujian/update/' . $pu['IdPeriodeUjian']); ?>
            <div class="m-field">
              <label>Periode *</label>
              <select name="periode" required>
                <option value="<?= $pu['IdPeriode']; ?>" selected><?= html_escape($pu['Periode']); ?></option>
                <?php foreach ($periode as $p) : ?>
                  <option value="<?= $p['IdPeriode']; ?>"><?= html_escape($p['Periode']); ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="m-field">
              <label>Tahun Ajaran *</label>
              <select name="ajaran" required>
                <option value="<?= $pu['IdAjaran']; ?>" selected><?= html_escape($pu['ThAjaran']); ?></option>
                <?php foreach ($tahun_ajaran as $ta) : ?>
                  <option value="<?= $ta['IdAjaran']; ?>"><?= html_escape($ta['ThAjaran']); ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="m-field">
              <label>Semester *</label>
              <select name="semester" required>
                <option value="<?= $pu['IdSemester']; ?>" selected><?= html_escape($pu['Semester']); ?></option>
                <?php foreach ($semester as $smt) : ?>
                  <option value="<?= $smt['IdSemester']; ?>"><?= html_escape($smt['Semester']); ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="m-field">
              <label>Kelas *</label>
              <select name="kelas" required>
                <option value="<?= $pu['IdKelas']; ?>" selected><?= html_escape($pu['NamaKelas']); ?></option>
                <?php foreach ($kelas as $kls) : ?>
                  <option value="<?= $kls['IdKelas']; ?>"><?= html_escape($kls['NamaKelas']); ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="m-field">
              <label>Keterangan Periode *</label>
              <input type="text" name="KetPeriode" value="<?= html_escape($pu['KetPeriode']); ?>" required autocomplete="off">
            </div>
            <button type="submit" class="m-btn"><i class="fas fa-save"></i> Simpan Perubahan</button>
            <?= form_close(); ?>
          </div>
        </div>
      <?php endforeach; ?>
    <?php else : ?>
      <p class="m-empty">Belum ada data periode ujian.</p>
    <?php endif; ?>
  </div>
</div>

<script>
  var btnModeUbahPeriodeUjian = document.getElementById('btnModeUbahPeriodeUjian');
  var daftarPeriodeUjian = document.getElementById('mPeriodeUjianList');
  if (btnModeUbahPeriodeUjian && daftarPeriodeUjian) {
    btnModeUbahPeriodeUjian.addEventListener('click', function () {
      var aktif = daftarPeriodeUjian.classList.toggle('m-dana-edit-mode');
      btnModeUbahPeriodeUjian.innerHTML = aktif
        ? '<i class="fas fa-check"></i> Selesai'
        : '<i class="fas fa-pen"></i> Ubah';
    });
  }
</script>
