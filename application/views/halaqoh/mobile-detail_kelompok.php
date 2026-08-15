<?php $daftar = $detail_kelompok ? $detail_kelompok : $list_detail_kelompok; ?>
<div class="m-content">
  <p class="m-page-title">Detail Kelompok</p>

  <?php if (validation_errors()) : ?>
    <div class="m-card" style="border: 1px solid #f5c6cb; background: #f8d7da; color: #721c24;">
      <?= validation_errors(); ?>
    </div>
  <?php endif; ?>

  <div class="m-card">
    <button type="button" class="m-btn m-btn-outline" data-toggle-target="#formFilterDetailKelompok">
      <i class="fas fa-filter"></i> Filter Kelas/Kelompok
    </button>

    <div class="m-form-panel" id="formFilterDetailKelompok" hidden>
      <form action="<?= base_url('halaqoh/detail_kelompok'); ?>">
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
          <label>Kelompok Halaqoh</label>
          <select name="kelompok">
            <option value="">-- Pilih Kelompok Halaqoh --</option>
            <?php foreach ($kelompok as $kh) : ?>
              <option value="<?= $kh['IdKelompok']; ?>"><?= html_escape($kh['NamaKelompok']); ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <button type="submit" class="m-btn"><i class="fas fa-book"></i> Tampil Data</button>
      </form>
    </div>

    <button type="button" class="m-btn mt-2" data-toggle-target="#formTambahDetailKelompok">
      <i class="fas fa-plus"></i> Tambah Data
    </button>

    <div class="m-form-panel" id="formTambahDetailKelompok" hidden>
      <?= form_open('halaqoh/detail_kelompok'); ?>
      <div class="m-field">
        <label>Kelompok</label>
        <select name="kelompok">
          <option value="">-- Pilih Kelompok --</option>
          <?php foreach ($kelompok as $klp) : ?>
            <option value="<?= $klp['IdKelompok']; ?>"><?= html_escape($klp['NamaKelompok']); ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="m-field">
        <label>Santri</label>
        <select name="siswa">
          <option value="">-- Pilih Santri --</option>
          <?php foreach ($siswa as $sis) : ?>
            <option value="<?= $sis['IdSiswa']; ?>"><?= html_escape($sis['NamaKelas'] . ' | ' . $sis['NamaLengkap']); ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="m-field">
        <label>Musyrif</label>
        <select name="musyrif">
          <option value="">-- Pilih Musyrif --</option>
          <?php foreach ($musyrif as $mus) : ?>
            <option value="<?= $mus['IdMusyrif']; ?>"><?= html_escape($mus['NamaMusyrif']); ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <button type="submit" class="m-btn"><i class="fas fa-save"></i> Simpan</button>
      <?= form_close(); ?>
    </div>

    <button type="button" class="m-btn mt-2" id="btnModeUbahDetailKelompok" style="background:#fff; color:var(--navy); border:1px solid var(--navy);">
      <i class="fas fa-pen"></i> Ubah
    </button>
    <?php if (!$detail_kelompok) : ?>
      <a href="<?= base_url('halaqoh/Detail_kelompok/export_all_data_excel'); ?>" target="_blank" class="m-btn m-btn-outline mt-2"><i class="fas fa-file-excel"></i> Export Data</a>
    <?php endif; ?>
    <a href="<?= base_url('halaqoh/detail_kelompok/reset_data'); ?>" class="m-btn mt-2 tombol-reset" tipeData="Detail Kelompok" style="background:#ffc107; color:#212529;"><i class="fas fa-ban"></i> Reset Data</a>
  </div>

  <div class="m-card" id="mDetailKelompokList">
    <?php if ($daftar) : ?>
      <?php foreach ($daftar as $dk) : ?>
        <div class="m-dana-item-wrapper">
          <div class="m-list-item">
            <div>
              <div class="m-list-title"><?= html_escape($dk['NamaLengkap']); ?></div>
              <div class="m-list-sub"><?= html_escape($dk['NamaKelas']); ?> &middot; <?= html_escape($dk['NamaKelompok']); ?> &middot; <?= html_escape($dk['NamaMusyrif']); ?></div>
            </div>
          </div>

          <div class="m-dana-actions">
            <button type="button" class="m-dana-btn-ubah" data-toggle-target="#editDetailKelompok<?= $dk['IdDetailKelompok']; ?>">Ubah</button>
            <a href="<?= base_url('halaqoh/detail_kelompok/delete/' . $dk['IdDetailKelompok']); ?>" class="m-dana-btn-hapus tombol-hapus" tipeData="Detail Kelompok" namaData="<?= html_escape($dk['NamaKelompok']); ?>">Hapus</a>
          </div>

          <div class="m-form-panel" id="editDetailKelompok<?= $dk['IdDetailKelompok']; ?>" hidden>
            <?= form_open('halaqoh/detail_kelompok/update/' . $dk['IdDetailKelompok']); ?>
            <div class="m-field">
              <label>Kelompok</label>
              <select name="kelompok">
                <option value="<?= $dk['IdKelompok']; ?>" selected><?= html_escape($dk['NamaKelompok']); ?></option>
                <?php foreach ($kelompok as $klp) : ?>
                  <option value="<?= $klp['IdKelompok']; ?>"><?= html_escape($klp['NamaKelompok']); ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="m-field">
              <label>Santri</label>
              <select name="siswa">
                <option value="<?= $dk['IdSiswa']; ?>" selected><?= html_escape($dk['NamaLengkap']); ?></option>
                <?php foreach ($siswa as $sis) : ?>
                  <option value="<?= $sis['IdSiswa']; ?>"><?= html_escape($sis['NamaKelas'] . ' | ' . $sis['NamaLengkap']); ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="m-field">
              <label>Musyrif</label>
              <select name="musyrif">
                <option value="<?= $dk['IdMusyrif']; ?>" selected><?= html_escape($dk['NamaMusyrif']); ?></option>
                <?php foreach ($musyrif as $mus) : ?>
                  <option value="<?= $mus['IdMusyrif']; ?>"><?= html_escape($mus['NamaMusyrif']); ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <button type="submit" class="m-btn"><i class="fas fa-save"></i> Simpan Perubahan</button>
            <?= form_close(); ?>
          </div>
        </div>
      <?php endforeach; ?>
    <?php else : ?>
      <p class="m-empty">Belum ada data detail kelompok.</p>
    <?php endif; ?>
  </div>
</div>

<script>
  var btnModeUbahDetailKelompok = document.getElementById('btnModeUbahDetailKelompok');
  var daftarDetailKelompok = document.getElementById('mDetailKelompokList');
  if (btnModeUbahDetailKelompok && daftarDetailKelompok) {
    btnModeUbahDetailKelompok.addEventListener('click', function () {
      var aktif = daftarDetailKelompok.classList.toggle('m-dana-edit-mode');
      btnModeUbahDetailKelompok.innerHTML = aktif
        ? '<i class="fas fa-check"></i> Selesai'
        : '<i class="fas fa-pen"></i> Ubah';
    });
  }
</script>
