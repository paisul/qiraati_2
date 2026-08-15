<div class="m-content">
  <p class="m-page-title">Data Target</p>

  <div class="m-card">
    <button type="button" class="m-btn" data-toggle-target="#formTambahTarget">
      <i class="fas fa-plus"></i> Tambah Data
    </button>

    <div class="m-form-panel" id="formTambahTarget" hidden>
      <?= form_open('tahfidz/target/add'); ?>
      <div class="m-field">
        <label>Kelas</label>
        <select name="kelas">
          <option value="">-- Pilih Kelas --</option>
          <?php foreach ($kelas as $kls) : ?>
            <option value="<?= $kls['IdKelas']; ?>"><?= html_escape($kls['NamaKelas']); ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="m-field">
        <label>Periode</label>
        <select name="periode">
          <option value="">-- Pilih Periode --</option>
          <?php foreach ($periode as $p) : ?>
            <option value="<?= $p['IdPeriode']; ?>"><?= html_escape($p['Periode']); ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="m-field">
        <label>Ajaran</label>
        <select name="ajaran">
          <option value="">-- Pilih Ajaran --</option>
          <?php foreach ($ajaran as $aj) : ?>
            <option value="<?= $aj['IdAjaran']; ?>"><?= html_escape($aj['ThAjaran']); ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="m-field">
        <label>Semester</label>
        <select name="semester">
          <option value="">-- Pilih Semester --</option>
          <?php foreach ($semester as $smt) : ?>
            <option value="<?= $smt['IdSemester']; ?>"><?= html_escape($smt['Semester']); ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="m-field">
        <label>Tanggal Mulai</label>
        <input type="date" name="tglMulai">
      </div>
      <div class="m-field">
        <label>Tanggal Selesai</label>
        <input type="date" name="tglSelesai">
      </div>
      <div class="m-field">
        <label>Pekan Ke- *</label>
        <input type="text" name="pekan" required autocomplete="off">
      </div>
      <button type="submit" class="m-btn"><i class="fas fa-save"></i> Simpan</button>
      <?= form_close(); ?>
    </div>

    <button type="button" class="m-btn mt-2" id="btnModeUbahTarget" style="background:#fff; color:var(--navy); border:1px solid var(--navy);">
      <i class="fas fa-pen"></i> Ubah
    </button>
    <a href="<?= base_url('tahfidz/target/export_excel'); ?>" target="_blank" class="m-btn m-btn-outline mt-2"><i class="fas fa-file-excel"></i> Export Data</a>
  </div>

  <div class="m-card" id="mTargetList">
    <?php if ($target) : ?>
      <?php foreach ($target as $tgt) : ?>
        <div class="m-dana-item-wrapper">
          <div class="m-list-item">
            <div>
              <div class="m-list-title"><?= html_escape($tgt['NamaKelas']); ?> &middot; Pekan <?= html_escape($tgt['Pekan']); ?></div>
              <div class="m-list-sub"><?= html_escape($tgt['Periode'] . ' | ' . $tgt['ThAjaran'] . ' | ' . $tgt['Semester']); ?></div>
              <div class="m-list-sub"><?= date('d F Y', strtotime($tgt['TglMulai'])); ?> &ndash; <?= date('d F Y', strtotime($tgt['TglSelesai'])); ?></div>
            </div>
          </div>

          <div class="m-dana-actions">
            <button type="button" class="m-dana-btn-ubah" data-toggle-target="#editTarget<?= $tgt['IdTarget']; ?>">Ubah</button>
            <a href="<?= base_url('tahfidz/target/delete/' . $tgt['IdTarget']); ?>" class="m-dana-btn-hapus tombol-hapus" tipeData="Target Tahfidz" namaData="<?= html_escape($tgt['Pekan']); ?>">Hapus</a>
          </div>

          <div class="m-form-panel" id="editTarget<?= $tgt['IdTarget']; ?>" hidden>
            <?= form_open('tahfidz/target/update/' . $tgt['IdTarget']); ?>
            <div class="m-field">
              <label>Kelas</label>
              <select name="kelas">
                <option value="<?= $tgt['IdKelas']; ?>" selected><?= html_escape($tgt['NamaKelas']); ?></option>
                <?php foreach ($kelas as $kls) : ?>
                  <option value="<?= $kls['IdKelas']; ?>"><?= html_escape($kls['NamaKelas']); ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="m-field">
              <label>Periode</label>
              <select name="periode">
                <option value="<?= $tgt['IdPeriode']; ?>" selected><?= html_escape($tgt['Periode']); ?></option>
                <?php foreach ($periode as $p) : ?>
                  <option value="<?= $p['IdPeriode']; ?>"><?= html_escape($p['Periode']); ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="m-field">
              <label>Ajaran</label>
              <select name="ajaran">
                <option value="<?= $tgt['IdAjaran']; ?>" selected><?= html_escape($tgt['ThAjaran']); ?></option>
                <?php foreach ($ajaran as $aj) : ?>
                  <option value="<?= $aj['IdAjaran']; ?>"><?= html_escape($aj['ThAjaran']); ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="m-field">
              <label>Semester</label>
              <select name="semester">
                <option value="<?= $tgt['IdSemester']; ?>" selected><?= html_escape($tgt['Semester']); ?></option>
                <?php foreach ($semester as $smt) : ?>
                  <option value="<?= $smt['IdSemester']; ?>"><?= html_escape($smt['Semester']); ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="m-field">
              <label>Tanggal Mulai</label>
              <input type="date" name="tglMulai" value="<?= html_escape($tgt['TglMulai']); ?>">
            </div>
            <div class="m-field">
              <label>Tanggal Selesai</label>
              <input type="date" name="tglSelesai" value="<?= html_escape($tgt['TglSelesai']); ?>">
            </div>
            <div class="m-field">
              <label>Pekan Ke- *</label>
              <input type="text" name="pekan" value="<?= html_escape($tgt['Pekan']); ?>" required autocomplete="off">
            </div>
            <button type="submit" class="m-btn"><i class="fas fa-save"></i> Simpan Perubahan</button>
            <?= form_close(); ?>
          </div>
        </div>
      <?php endforeach; ?>
    <?php else : ?>
      <p class="m-empty">Belum ada data target.</p>
    <?php endif; ?>
  </div>
</div>

<script>
  var btnModeUbahTarget = document.getElementById('btnModeUbahTarget');
  var daftarTarget = document.getElementById('mTargetList');
  if (btnModeUbahTarget && daftarTarget) {
    btnModeUbahTarget.addEventListener('click', function () {
      var aktif = daftarTarget.classList.toggle('m-dana-edit-mode');
      btnModeUbahTarget.innerHTML = aktif
        ? '<i class="fas fa-check"></i> Selesai'
        : '<i class="fas fa-pen"></i> Ubah';
    });
  }
</script>
