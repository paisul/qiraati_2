<div class="m-content">
  <p class="m-page-title">Setoran</p>

  <div class="m-card">
    <button type="button" class="m-btn" data-toggle-target="#formTambahSetoran">
      <i class="fas fa-plus"></i> Tambah Data
    </button>

    <div class="m-form-panel" id="formTambahSetoran" hidden>
      <?= form_open('halaqoh/setoran/add'); ?>
      <div class="m-field">
        <label>Kelas</label>
        <select name="kelas" id="mSetoranKelas">
          <option value="">-- Pilih Kelas --</option>
          <?php foreach ($kelas as $kls) : ?>
            <option value="<?= $kls['IdKelas']; ?>"><?= html_escape($kls['NamaKelas']); ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="m-field">
        <label>Isi Target</label>
        <select name="isi" id="mSetoranIsiTarget">
          <option value="">-- Pilih Isi Target --</option>
        </select>
      </div>
      <div class="m-field">
        <label>Jadwal Halaqoh</label>
        <select name="jadwalHalaqoh">
          <option value="">-- Pilih Jadwal --</option>
          <?php foreach ($jadwal as $j) : ?>
            <option value="<?= $j['IdJadwal']; ?>"><?= html_escape($j['Waktu'] . ' | ' . $j['Ket']); ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="m-field">
        <label>Kelompok</label>
        <select name="kelompok" id="mSetoranKelompok">
          <option value="">-- Pilih Kelompok --</option>
          <?php foreach ($kelompok as $k) : ?>
            <option value="<?= $k['IdKelompok']; ?>"><?= html_escape($k['NamaKelompok']); ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="m-field">
        <label>Santri</label>
        <select name="detail_kelompok" id="mSetoranSantri">
          <option value="">-- Pilih Santri --</option>
        </select>
      </div>
      <div class="m-field">
        <label>Presensi</label>
        <select name="presensi">
          <option value="">-- Pilih Presensi --</option>
          <option value="Masuk">Masuk</option>
          <option value="Tidak Masuk">Tidak Masuk</option>
        </select>
      </div>
      <div class="m-field">
        <label>Keterangan</label>
        <select name="keterangan">
          <option value="">-- Pilih Keterangan --</option>
          <option value="Selesai">Selesai</option>
          <option value="Tidak Selesai">Tidak Selesai</option>
        </select>
      </div>
      <button type="submit" class="m-btn"><i class="fas fa-save"></i> Simpan</button>
      <?= form_close(); ?>
    </div>

    <button type="button" class="m-btn mt-2" id="btnModeUbahSetoran" style="background:#fff; color:var(--navy); border:1px solid var(--navy);">
      <i class="fas fa-pen"></i> Ubah
    </button>
    <a href="<?= base_url('halaqoh/Setoran/export_excel'); ?>" target="_blank" class="m-btn m-btn-outline mt-2"><i class="fas fa-file-excel"></i> Export Data</a>
    <a href="<?= base_url('halaqoh/setoran/reset_data'); ?>" class="m-btn mt-2 tombol-reset" tipeData="Setoran" style="background:#ffc107; color:#212529;"><i class="fas fa-ban"></i> Reset Data</a>

    <form class="mt-2" action="<?= base_url('halaqoh/Setoran/cari_data'); ?>" method="POST">
      <div class="m-field">
        <label>Cari Nama Kelompok</label>
        <input type="text" name="nama_kelompok" placeholder="Masukkan Nama Kelompok">
      </div>
      <button type="submit" class="m-btn m-btn-outline"><i class="fas fa-search"></i> Cari</button>
    </form>
  </div>

  <div class="m-card" id="mSetoranList">
    <?php if ($setoran) : ?>
      <?php foreach ($setoran as $setor) : ?>
        <div class="m-dana-item-wrapper">
          <div class="m-list-item" style="flex-direction: column; align-items: stretch;">
            <div>
              <div class="m-list-title"><?= html_escape($setor['IsiTarget']); ?></div>
              <div class="m-list-sub"><?= html_escape($setor['NamaKelompok'] . ' (' . $setor['NamaLengkap'] . ')'); ?></div>
              <div class="m-list-sub"><?= html_escape($setor['Waktu']); ?> &middot; <?= html_escape($setor['Presensi']); ?> &middot; <?= html_escape($setor['Keterangan']); ?></div>
            </div>
          </div>

          <div class="m-dana-actions">
            <button type="button" class="m-dana-btn-ubah" data-toggle-target="#editSetoran<?= $setor['IdSetoran']; ?>">Ubah</button>
            <a href="<?= base_url('halaqoh/setoran/delete/' . $setor['IdSetoran']); ?>" class="m-dana-btn-hapus tombol-hapus" tipeData="Setoran" namaData="<?= html_escape($setor['NamaKelompok']); ?>">Hapus</a>
          </div>

          <div class="m-form-panel" id="editSetoran<?= $setor['IdSetoran']; ?>" hidden>
            <?= form_open('halaqoh/setoran/update/' . $setor['IdSetoran']); ?>
            <div class="m-field">
              <label>Isi Target</label>
              <select name="isi" disabled>
                <option selected><?= html_escape($setor['IsiTarget'] . ' | ' . $setor['Keterangan']); ?></option>
              </select>
            </div>
            <div class="m-field">
              <label>Jadwal Halaqoh</label>
              <select name="jadwalHalaqoh" disabled>
                <option selected><?= html_escape($setor['Waktu'] . ' | ' . $setor['Ket']); ?></option>
              </select>
            </div>
            <div class="m-field">
              <label>Kelompok</label>
              <select name="kelompok" disabled>
                <option selected><?= html_escape($setor['NamaKelompok'] . ' | ' . $setor['NamaLengkap']); ?></option>
              </select>
            </div>
            <div class="m-field">
              <label>Presensi</label>
              <select name="presensi">
                <option value="Masuk" <?= $setor['Presensi'] === 'Masuk' ? 'selected' : ''; ?>>Masuk</option>
                <option value="Tidak Masuk" <?= $setor['Presensi'] === 'Tidak Masuk' ? 'selected' : ''; ?>>Tidak Masuk</option>
              </select>
            </div>
            <div class="m-field">
              <label>Keterangan</label>
              <select name="keterangan">
                <option value="Selesai" <?= $setor['Keterangan'] === 'Selesai' ? 'selected' : ''; ?>>Selesai</option>
                <option value="Tidak Selesai" <?= $setor['Keterangan'] === 'Tidak Selesai' ? 'selected' : ''; ?>>Tidak Selesai</option>
              </select>
            </div>
            <button type="submit" class="m-btn"><i class="fas fa-save"></i> Simpan Perubahan</button>
            <?= form_close(); ?>
          </div>
        </div>
      <?php endforeach; ?>
    <?php else : ?>
      <p class="m-empty">Belum ada data setoran.</p>
    <?php endif; ?>
  </div>
</div>

<script>
  var btnModeUbahSetoran = document.getElementById('btnModeUbahSetoran');
  var daftarSetoran = document.getElementById('mSetoranList');
  if (btnModeUbahSetoran && daftarSetoran) {
    btnModeUbahSetoran.addEventListener('click', function () {
      var aktif = daftarSetoran.classList.toggle('m-dana-edit-mode');
      btnModeUbahSetoran.innerHTML = aktif
        ? '<i class="fas fa-check"></i> Selesai'
        : '<i class="fas fa-pen"></i> Ubah';
    });
  }

  var selKelas = document.getElementById('mSetoranKelas');
  var selIsiTarget = document.getElementById('mSetoranIsiTarget');
  if (selKelas && selIsiTarget) {
    selKelas.addEventListener('change', function () {
      fetch('<?= base_url('halaqoh/Setoran/getDetailTargetByKelas'); ?>', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'id_kelas=' + encodeURIComponent(selKelas.value)
      })
        .then(function (res) { return res.text(); })
        .then(function (html) { selIsiTarget.innerHTML = html; })
        .catch(function () {});
    });
  }

  var selKelompok = document.getElementById('mSetoranKelompok');
  var selSantri = document.getElementById('mSetoranSantri');
  if (selKelompok && selSantri) {
    selKelompok.addEventListener('change', function () {
      fetch('<?= base_url('halaqoh/Setoran/getSantriByKelompok'); ?>', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'id_kelompok=' + encodeURIComponent(selKelompok.value)
      })
        .then(function (res) { return res.text(); })
        .then(function (html) { selSantri.innerHTML = html; })
        .catch(function () {});
    });
  }
</script>
