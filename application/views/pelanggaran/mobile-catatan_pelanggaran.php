<div class="m-content">
  <p class="m-page-title">Catatan Pelanggaran</p>

  <div class="m-card">
    <button type="button" class="m-btn" data-toggle-target="#formTambahPelanggaran">
      <i class="fas fa-plus"></i> Tambah Data
    </button>

    <div class="m-form-panel" id="formTambahPelanggaran" hidden>
      <?= form_open('pelanggaran/catatan_pelanggaran/add'); ?>
      <div class="m-field">
        <label>Nama Santri</label>
        <select name="nama">
          <option value="">-- Pilih Santri --</option>
          <?php foreach ($santri as $san) : ?>
            <option value="<?= $san['IdSiswa']; ?>"><?= html_escape($san['NamaKelas'] . ' | ' . $san['NamaLengkap']); ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="m-field">
        <label>Jenis Iqob</label>
        <select name="jenisiqob" class="m-cek-poin" data-target-poin="#poinTambah">
          <option value="">-- Pilih Jenis Iqob --</option>
          <?php foreach ($jenisiqob as $jp) : ?>
            <option value="<?= $jp['IdJenisIqob']; ?>"><?= html_escape($jp['JenisIqob']); ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="m-field">
        <label>Poin</label>
        <input type="text" id="poinTambah" name="poin" readonly>
      </div>
      <div class="m-field">
        <label>Tanggal</label>
        <input type="date" name="tgl">
      </div>
      <button type="submit" class="m-btn"><i class="fas fa-save"></i> Simpan</button>
      <?= form_close(); ?>
    </div>

    <button type="button" class="m-btn mt-2" id="btnModeUbahPelanggaran" style="background:#fff; color:var(--navy); border:1px solid var(--navy);">
      <i class="fas fa-pen"></i> Ubah
    </button>
    <a href="<?= base_url('pelanggaran/catatan_pelanggaran/export_excel'); ?>" target="_blank" class="m-btn m-btn-outline mt-2"><i class="fas fa-file-excel"></i> Export Data</a>
    <a href="<?= base_url('pelanggaran/catatan_pelanggaran/reset_pelanggaran'); ?>" class="m-btn mt-2 tombol-reset" tipeData="Catatan Pelanggaran" style="background:#ffc107; color:#212529;"><i class="fas fa-ban"></i> Reset Data</a>
  </div>

  <div class="m-card" id="mPelanggaranList">
    <?php if ($pelanggaran) : ?>
      <?php foreach ($pelanggaran as $pel) : ?>
        <div class="m-dana-item-wrapper">
          <div class="m-list-item">
            <div>
              <div class="m-list-title"><?= html_escape($pel['NamaLengkap']); ?></div>
              <div class="m-list-sub"><?= html_escape($pel['JenisIqob']); ?> &middot; <?= date('d F Y', strtotime($pel['Tgl'])); ?></div>
            </div>
            <span class="m-badge m-badge-belum"><?= (int) $pel['Points']; ?> poin</span>
          </div>

          <div class="m-dana-actions">
            <button type="button" class="m-dana-btn-ubah" data-toggle-target="#editPelanggaran<?= $pel['IdIqob']; ?>">Ubah</button>
            <a href="<?= base_url('pelanggaran/catatan_pelanggaran/delete/' . $pel['IdIqob']); ?>" class="m-dana-btn-hapus tombol-hapus" tipeData="Detail Pelanggaran" namaData="<?= html_escape($pel['NamaLengkap']); ?>">Hapus</a>
          </div>

          <div class="m-form-panel" id="editPelanggaran<?= $pel['IdIqob']; ?>" hidden>
            <?= form_open('pelanggaran/catatan_pelanggaran/update/' . $pel['IdIqob']); ?>
            <div class="m-field">
              <label>Nama Santri</label>
              <select name="nama">
                <option value="<?= $pel['IdSiswa']; ?>" selected><?= html_escape($pel['NamaLengkap']); ?></option>
                <?php foreach ($santri as $san) : ?>
                  <option value="<?= $san['IdSiswa']; ?>"><?= html_escape($san['NamaLengkap']); ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="m-field">
              <label>Jenis Iqob</label>
              <select name="jenisiqob" class="m-cek-poin" data-target-poin="#poinUbah<?= $pel['IdIqob']; ?>">
                <option value="<?= $pel['IdJenisIqob']; ?>" selected><?= html_escape($pel['JenisIqob']); ?></option>
                <?php foreach ($jenisiqob as $iqob) : ?>
                  <option value="<?= $iqob['IdJenisIqob']; ?>"><?= html_escape($iqob['JenisIqob']); ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="m-field">
              <label>Poin</label>
              <input type="text" id="poinUbah<?= $pel['IdIqob']; ?>" name="poin" value="<?= $pel['Points']; ?>" readonly>
            </div>
            <div class="m-field">
              <label>Tanggal</label>
              <input type="date" name="tgl" value="<?= $pel['Tgl']; ?>">
            </div>
            <button type="submit" class="m-btn"><i class="fas fa-save"></i> Simpan Perubahan</button>
            <?= form_close(); ?>
          </div>
        </div>
      <?php endforeach; ?>
    <?php else : ?>
      <p class="m-empty">Belum ada data pelanggaran.</p>
    <?php endif; ?>
  </div>
</div>

<script>
  var btnModeUbahPelanggaran = document.getElementById('btnModeUbahPelanggaran');
  var daftarPelanggaran = document.getElementById('mPelanggaranList');
  if (btnModeUbahPelanggaran && daftarPelanggaran) {
    btnModeUbahPelanggaran.addEventListener('click', function () {
      var aktif = daftarPelanggaran.classList.toggle('m-dana-edit-mode');
      btnModeUbahPelanggaran.innerHTML = aktif
        ? '<i class="fas fa-check"></i> Selesai'
        : '<i class="fas fa-pen"></i> Ubah';
    });
  }

  // Isi otomatis field Poin (readonly) berdasarkan Jenis Iqob yang dipilih.
  document.querySelectorAll('.m-cek-poin').forEach(function (select) {
    select.addEventListener('change', function () {
      var targetPoin = document.querySelector(select.dataset.targetPoin);
      if (!targetPoin || !select.value) return;

      fetch('<?= base_url('pelanggaran/catatan_pelanggaran/getPointById'); ?>', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'IdJenisIqob=' + encodeURIComponent(select.value)
      })
        .then(function (res) { return res.json(); })
        .then(function (json) { targetPoin.value = json.Poin; })
        .catch(function () {});
    });
  });
</script>
