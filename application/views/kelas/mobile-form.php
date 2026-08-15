<?php
$aksi = $mode === 'tambah' ? base_url('kelas/simpan') : base_url('kelas/perbarui/' . $kelas['IdKelas']);
?>
<div class="m-content">
  <p class="m-page-title"><?= html_escape($title); ?></p>

  <?php if (validation_errors()) : ?>
    <div class="m-card" style="border: 1px solid #f5c6cb; background: #f8d7da; color: #721c24;">
      <?= validation_errors(); ?>
    </div>
  <?php endif; ?>

  <div class="m-card">
    <?= form_open($aksi); ?>

    <div class="m-field">
      <label>Nama Kelas *</label>
      <select name="nama_kelas" required>
        <option value="">-- Pilih Nama Kelas --</option>
        <?php foreach ($nama_kelas_list as $nk) : ?>
          <option value="<?= $nk; ?>" <?= (string) $nk === (string) $kelas['NamaKelas'] ? 'selected' : ''; ?>><?= $nk; ?></option>
        <?php endforeach; ?>
      </select>
    </div>

    <div class="m-field">
      <label>Pembimbing *</label>
      <select name="pembimbing" required>
        <option value="">-- Pilih Pembimbing --</option>
        <?php foreach ($musyrif as $m) : ?>
          <option value="<?= $m['IdMusyrif']; ?>" <?= (string) $m['IdMusyrif'] === (string) $kelas['IdMusyrif'] ? 'selected' : ''; ?>><?= html_escape($m['NamaMusyrif']); ?></option>
        <?php endforeach; ?>
      </select>
      <?php if (empty($musyrif)) : ?>
        <div class="m-error">Belum ada data pembimbing. Tambahkan data Pembimbing terlebih dahulu.</div>
      <?php endif; ?>
    </div>

    <div class="m-field">
      <label>Jumlah Santri</label>
      <input type="text" value="<?= $kelas['JumlahSantri']; ?>" readonly>
      <small style="color:#6c757d;">Dihitung otomatis dari data santri, tidak dapat diubah manual.</small>
    </div>

    <div class="m-field">
      <label>Lokasi/Ruangan</label>
      <input type="text" name="lokasi" placeholder="Mis. Ruang Tahsin, Aula Utama" value="<?= html_escape($kelas['Lokasi']); ?>">
    </div>

    <button type="submit" name="aksi" value="simpan" class="m-btn"><i class="fas fa-save"></i> Simpan</button>
    <?php if ($mode === 'tambah') : ?>
      <button type="submit" name="aksi" value="tambah_baru" class="m-btn mt-2" style="background:#28a745;"><i class="fas fa-plus"></i> Simpan &amp; Tambah Baru</button>
    <?php endif; ?>
    <a href="<?= base_url('kelas'); ?>" class="m-btn m-btn-outline mt-2"><i class="fas fa-times"></i> Batal</a>

    <?= form_close(); ?>
  </div>
</div>
