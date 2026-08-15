<?php
$dikelompokkan = [];
foreach ($target_ujian_kelas as $tuk) {
  $dikelompokkan[$tuk['NamaKelas']][] = $tuk;
}
?>
<div class="m-content">
  <p class="m-page-title">Data Target Ujian Perkelas</p>

  <div class="m-card">
    <button type="button" class="m-btn" data-toggle-target="#formTambahBanyakTargetUjianKelas">
      <i class="fas fa-plus"></i> Tambah Banyak Data
    </button>

    <div class="m-form-panel" id="formTambahBanyakTargetUjianKelas" hidden>
      <?= form_open('ujian/target_ujian_kelas/add'); ?>
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
        <label>Target Ujian <small>(bisa pilih lebih dari satu)</small></label>
        <select name="targetujian[]" multiple size="6">
          <?php foreach ($target_ujian as $tu) : ?>
            <option value="<?= $tu['IdTargetUjian']; ?>"><?= html_escape($tu['NamaUjian'] . ' - ' . $tu['Keterangan']); ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <button type="submit" class="m-btn"><i class="fas fa-save"></i> Simpan</button>
      <?= form_close(); ?>
    </div>

    <button type="button" class="m-btn mt-2" data-toggle-target="#formTambahSatuTargetUjianKelas">
      <i class="fas fa-plus"></i> Tambah 1 Data
    </button>

    <div class="m-form-panel" id="formTambahSatuTargetUjianKelas" hidden>
      <?= form_open('ujian/target_ujian_kelas/add_tunggal'); ?>
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
        <label>Target Ujian</label>
        <select name="targetujian">
          <option value="">-- Pilih Target Ujian --</option>
          <?php foreach ($target_ujian as $tu) : ?>
            <option value="<?= $tu['IdTargetUjian']; ?>"><?= html_escape($tu['NamaUjian'] . ' - ' . $tu['Keterangan']); ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <button type="submit" class="m-btn"><i class="fas fa-save"></i> Simpan</button>
      <?= form_close(); ?>
    </div>
  </div>

  <?php if ($dikelompokkan) : ?>
    <?php foreach ($dikelompokkan as $namaKelas => $daftar) : ?>
      <div class="m-card">
        <p class="m-card-title" style="margin-bottom: 8px;"><?= html_escape($namaKelas); ?></p>
        <?php foreach ($daftar as $tuk) : ?>
          <div class="m-list-item">
            <div class="m-list-title"><?= html_escape($tuk['Keterangan']); ?></div>
            <a href="<?= base_url('ujian/target_ujian_kelas/delete/' . $tuk['IdTargetKelas']); ?>" class="m-dana-btn-hapus tombol-hapus" tipeData="Target Ujian" namaData="<?= html_escape($tuk['Keterangan']); ?>">Hapus</a>
          </div>
        <?php endforeach; ?>
      </div>
    <?php endforeach; ?>
  <?php else : ?>
    <div class="m-card">
      <p class="m-empty">Belum ada data target ujian perkelas.</p>
    </div>
  <?php endif; ?>
</div>
