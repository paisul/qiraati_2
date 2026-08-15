<?php
$aksi = $mode === 'tambah' ? base_url('santri/simpan') : base_url('santri/perbarui/' . $santri['IdSiswa']);
?>
<div class="m-content m-content-pad-bottombar">
  <p class="m-page-title"><?= html_escape($title); ?></p>

  <?php if (validation_errors()) : ?>
    <div class="m-card" style="border: 1px solid #f5c6cb; background: #f8d7da; color: #721c24;">
      <?= validation_errors(); ?>
    </div>
  <?php endif; ?>

  <?= form_open_multipart($aksi); ?>

  <div class="m-card">
    <?php $this->load->view('santri/_mobile_form_fields', [
      'mode' => $mode,
      'santri' => $santri,
      'nis_baru' => $nis_baru,
      'kelas' => $kelas,
      'jenis_kelamin_list' => $jenis_kelamin_list,
    ]); ?>
  </div>

  <div class="m-card">
    <?php if ($mode === 'tambah') : ?>
      <button type="submit" name="aksi" value="tambah_baru" class="m-btn m-btn-outline"><i class="fas fa-plus"></i> Simpan &amp; Tambah Baru</button>
      <a href="<?= base_url('santri'); ?>" class="m-btn m-btn-outline mt-2"><i class="fas fa-times"></i> Batal</a>
    <?php else : ?>
      <a href="<?= base_url('santri/detail/' . $santri['IdSiswa']); ?>" class="m-btn m-btn-outline"><i class="fas fa-times"></i> Batal</a>
    <?php endif; ?>
  </div>

  <div class="m-bottombar">
    <button type="submit" name="aksi" value="simpan" class="m-btn m-btn-sticky" data-dirty-submit disabled><i class="fas fa-save"></i> Simpan</button>
  </div>

  <?= form_close(); ?>
</div>
