<?php
$aksi = $mode === 'tambah' ? base_url('santri/simpan') : base_url('santri/perbarui/' . $santri['IdSiswa']);
?>
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
  <div class="content mt-2">
    <div class="container-fluid">
      <div class="row">
        <div class="col-12">

          <?php if (validation_errors()) : ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
              <b><?= validation_errors(); ?></b>
              <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
          <?php endif; ?>

          <?= form_open_multipart($aksi, ['id' => 'formSantri', 'novalidate' => 'novalidate']); ?>

          <?php $this->load->view('santri/_form_fields', [
            'mode' => $mode,
            'santri' => $santri,
            'nis_baru' => $nis_baru,
            'kelas' => $kelas,
            'jenis_kelamin_list' => $jenis_kelamin_list,
          ]); ?>

          <div class="mb-4">
            <span id="tombolSubmitFormulir">
              <button type="submit" name="aksi" value="simpan" class="btn btn-primary"><i class="fas fa-save"></i> Simpan</button>
              <?php if ($mode === 'tambah') : ?>
                <button type="submit" name="aksi" value="tambah_baru" class="btn btn-success"><i class="fas fa-plus"></i> Simpan & Tambah Baru</button>
              <?php endif; ?>
            </span>
            <a href="<?= base_url('santri'); ?>" class="btn btn-default"><i class="fas fa-times"></i> Batal</a>
          </div>

          <?= form_close(); ?>
        </div>
      </div>
    </div>
  </div>
</div>

<script src="<?= base_url('assets/js/santri-form.js'); ?>"></script>
