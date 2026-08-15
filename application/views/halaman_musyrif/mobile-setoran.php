<div class="m-content">
  <p class="m-page-title">Data Setoran</p>

  <div class="m-card">
    <button type="button" class="m-btn" data-toggle-target="#formTambahSetoran">
      <i class="fas fa-plus"></i> Tambah Data Setoran
    </button>

    <div class="m-form-panel" id="formTambahSetoran" hidden>
      <?= form_open('Halaman_Musyrif/add_setoran'); ?>
      <div class="m-field">
        <label>Isi Target</label>
        <select name="isi">
          <option>-- Pilih Isi Target --</option>
          <?php foreach ($detail_target as $dt) : ?>
            <option value="<?= $dt['IdDetailTarget']; ?>"><?= html_escape($dt['NamaKelas'] . ' | ' . $dt['IsiTarget'] . ' | ' . $dt['Keterangan']); ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="m-field">
        <label>Jadwal Halaqoh</label>
        <select name="jadwalHalaqoh">
          <option>-- Pilih Jadwal --</option>
          <?php foreach ($jadwal as $j) : ?>
            <option value="<?= $j['IdJadwal']; ?>"><?= html_escape($j['Waktu'] . ' | ' . $j['Ket']); ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="m-field">
        <label>Kelompok</label>
        <select name="kelompok">
          <option>-- Pilih Kelompok --</option>
          <?php foreach ($detail_kelompok as $k) : ?>
            <option value="<?= $k['IdDetailKelompok']; ?>"><?= html_escape($k['NamaKelompok'] . ' | ' . $k['NamaKelas'] . ' - ' . $k['NamaLengkap']); ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="m-field">
        <label>Presensi</label>
        <input type="text" name="presensi" placeholder="Presensi" required autocomplete="off">
      </div>
      <div class="m-field">
        <label>Keterangan</label>
        <input type="text" name="keterangan" placeholder="Keterangan" required autocomplete="off">
      </div>
      <button type="submit" class="m-btn"><i class="fas fa-save"></i> Simpan</button>
      <?= form_close(); ?>
    </div>

    <button type="button" class="m-btn mt-2" id="btnModeUbahSetoran" style="background:#fff; color:var(--navy); border:1px solid var(--navy);">
      <i class="fas fa-pen"></i> Ubah
    </button>
  </div>

  <div class="m-card" id="mSetoranList">
    <?php if ($setoran) : ?>
      <?php foreach ($setoran as $setor) : ?>
        <div class="m-list-item" style="flex-direction: column; align-items: stretch;">
          <div class="d-flex justify-content-between" style="display:flex; justify-content:space-between;">
            <div>
              <div class="m-list-title"><?= html_escape($setor['IsiTarget']); ?></div>
              <div class="m-list-sub"><?= html_escape($setor['NamaKelompok'] . ' (' . $setor['NamaLengkap'] . ')'); ?></div>
              <div class="m-list-sub"><?= html_escape($setor['Waktu']); ?> &middot; <?= html_escape($setor['Presensi']); ?> &middot; <?= html_escape($setor['Keterangan']); ?></div>
            </div>
          </div>

          <div class="m-dana-actions">
            <button type="button" class="m-dana-btn-ubah" data-toggle-target="#editSetoran<?= $setor['IdSetoran']; ?>">Ubah</button>
            <a href="<?= base_url('Halaman_Musyrif/delete_setoran/' . $setor['IdSetoran']); ?>" class="m-dana-btn-hapus tombol-hapus" tipeData="Setoran" namaData="<?= html_escape($setor['NamaKelompok']); ?>">Hapus</a>
          </div>

          <div class="m-form-panel" id="editSetoran<?= $setor['IdSetoran']; ?>" hidden>
            <?= form_open('Halaman_Musyrif/update_setoran/' . $setor['IdSetoran']); ?>
            <div class="m-field">
              <label>Isi Target</label>
              <select name="isi">
                <option value="<?= $setor['IdDetailTarget']; ?>" selected><?= html_escape($setor['IsiTarget'] . ' | ' . $setor['Keterangan']); ?></option>
                <?php foreach ($detail_target as $dt) : ?>
                  <option value="<?= $dt['IdDetailTarget']; ?>"><?= html_escape($dt['NamaKelas'] . ' | ' . $dt['IsiTarget'] . ' | ' . $dt['Keterangan']); ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="m-field">
              <label>Jadwal Halaqoh</label>
              <select name="jadwalHalaqoh">
                <option value="<?= $setor['IdJadwal']; ?>" selected><?= html_escape($setor['Waktu'] . ' | ' . $setor['Ket']); ?></option>
                <?php foreach ($jadwal as $j) : ?>
                  <option value="<?= $j['IdJadwal']; ?>"><?= html_escape($j['Waktu'] . ' | ' . $j['Ket']); ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="m-field">
              <label>Kelompok</label>
              <select name="kelompok">
                <option value="<?= $setor['IdDetailKelompok']; ?>" selected><?= html_escape($setor['NamaKelompok'] . ' | ' . $setor['NamaLengkap']); ?></option>
                <?php foreach ($detail_kelompok as $k) : ?>
                  <option value="<?= $k['IdDetailKelompok']; ?>"><?= html_escape($k['NamaKelompok'] . ' - ' . $k['NamaLengkap']); ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="m-field">
              <label>Presensi</label>
              <input type="text" name="presensi" value="<?= html_escape($setor['Presensi']); ?>" required autocomplete="off">
            </div>
            <div class="m-field">
              <label>Keterangan</label>
              <input type="text" name="keterangan" value="<?= html_escape($setor['Keterangan']); ?>" required autocomplete="off">
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
</script>
