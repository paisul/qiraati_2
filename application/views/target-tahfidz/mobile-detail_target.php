<div class="m-content">
  <p class="m-page-title">Data Detail Target</p>

  <div class="m-card">
    <button type="button" class="m-btn" data-toggle-target="#formTambahDetailTarget">
      <i class="fas fa-plus"></i> Tambah Data
    </button>

    <div class="m-form-panel" id="formTambahDetailTarget" hidden>
      <?= form_open('tahfidz/detail_target/add'); ?>
      <div class="m-field">
        <label>Pekan</label>
        <select name="pekan">
          <option value="">-- Pilih Pekan --</option>
          <?php foreach ($target as $tgt) : ?>
            <option value="<?= $tgt['IdTarget']; ?>">Pekan Ke - <?= html_escape($tgt['Pekan']); ?> | Kelas - <?= html_escape($tgt['NamaKelas']); ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="m-field">
        <label>Isi Target *</label>
        <input type="text" name="isi" required>
      </div>
      <div class="m-field">
        <label>Jenis Target *</label>
        <select name="jenis_target" required>
          <option value="Quran">Qur'an</option>
          <option value="Doa/Qosidah">Doa/Qosidah</option>
        </select>
      </div>
      <div class="m-field">
        <label>Keterangan</label>
        <textarea name="keterangan" rows="4"></textarea>
      </div>
      <div class="m-field">
        <label>Tanggal</label>
        <input type="date" name="tgl">
      </div>
      <button type="submit" class="m-btn"><i class="fas fa-save"></i> Simpan</button>
      <?= form_close(); ?>
    </div>

    <button type="button" class="m-btn mt-2" id="btnModeUbahDetailTarget" style="background:#fff; color:var(--navy); border:1px solid var(--navy);">
      <i class="fas fa-pen"></i> Ubah
    </button>
    <a href="<?= base_url('tahfidz/detail_target/export_excel'); ?>" target="_blank" class="m-btn m-btn-outline mt-2"><i class="fas fa-file-excel"></i> Export Data</a>
  </div>

  <div class="m-card" id="mDetailTargetList">
    <?php if ($detail) : ?>
      <?php foreach ($detail as $det) : ?>
        <div class="m-dana-item-wrapper">
          <div class="m-list-item">
            <div>
              <div class="m-list-title"><?= html_escape($det['Pekan'] . ' / ' . $det['NamaKelas']); ?></div>
              <div class="m-list-sub"><?= html_escape($det['IsiTarget']); ?></div>
              <div class="m-list-sub"><span class="m-badge"><?= $det['JenisTarget'] == 'Doa/Qosidah' ? 'Doa/Qosidah' : "Qur'an"; ?></span></div>
              <div class="m-list-sub"><?= html_escape($det['Keterangan']); ?> &middot; <?= date('d F Y', strtotime($det['Tgl'])); ?></div>
            </div>
          </div>

          <div class="m-dana-actions">
            <button type="button" class="m-dana-btn-ubah" data-toggle-target="#editDetailTarget<?= $det['IdDetailTarget']; ?>">Ubah</button>
            <a href="<?= base_url('tahfidz/detail_target/delete/' . $det['IdDetailTarget']); ?>" class="m-dana-btn-hapus tombol-hapus" tipeData="Detail Target" namaData="<?= html_escape($det['Pekan']); ?>">Hapus</a>
          </div>

          <div class="m-form-panel" id="editDetailTarget<?= $det['IdDetailTarget']; ?>" hidden>
            <?= form_open('tahfidz/detail_target/update/' . $det['IdDetailTarget']); ?>
            <div class="m-field">
              <label>Pekan</label>
              <select name="pekan">
                <option value="<?= $det['IdTarget']; ?>" selected>Pekan Ke - <?= html_escape($det['Pekan']); ?> | Kelas - <?= html_escape($det['NamaKelas']); ?></option>
                <?php foreach ($target as $tgt) : ?>
                  <option value="<?= $tgt['IdTarget']; ?>">Pekan Ke - <?= html_escape($tgt['Pekan']); ?> | Kelas - <?= html_escape($tgt['NamaKelas']); ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="m-field">
              <label>Isi Target *</label>
              <input type="text" name="isi" value="<?= html_escape($det['IsiTarget']); ?>" required>
            </div>
            <div class="m-field">
              <label>Jenis Target *</label>
              <select name="jenis_target" required>
                <option value="Quran" <?= $det['JenisTarget'] != 'Doa/Qosidah' ? 'selected' : ''; ?>>Qur'an</option>
                <option value="Doa/Qosidah" <?= $det['JenisTarget'] == 'Doa/Qosidah' ? 'selected' : ''; ?>>Doa/Qosidah</option>
              </select>
            </div>
            <div class="m-field">
              <label>Keterangan</label>
              <textarea name="keterangan" rows="4"><?= html_escape($det['Keterangan']); ?></textarea>
            </div>
            <div class="m-field">
              <label>Tanggal</label>
              <input type="date" name="tgl" value="<?= html_escape($det['Tgl']); ?>">
            </div>
            <button type="submit" class="m-btn"><i class="fas fa-save"></i> Simpan Perubahan</button>
            <?= form_close(); ?>
          </div>
        </div>
      <?php endforeach; ?>
    <?php else : ?>
      <p class="m-empty">Belum ada data detail target.</p>
    <?php endif; ?>
  </div>
</div>

<script>
  var btnModeUbahDetailTarget = document.getElementById('btnModeUbahDetailTarget');
  var daftarDetailTarget = document.getElementById('mDetailTargetList');
  if (btnModeUbahDetailTarget && daftarDetailTarget) {
    btnModeUbahDetailTarget.addEventListener('click', function () {
      var aktif = daftarDetailTarget.classList.toggle('m-dana-edit-mode');
      btnModeUbahDetailTarget.innerHTML = aktif
        ? '<i class="fas fa-check"></i> Selesai'
        : '<i class="fas fa-pen"></i> Ubah';
    });
  }
</script>
