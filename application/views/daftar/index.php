<!DOCTYPE html>
<html>

<?php $app = ambil_pengaturan_aplikasi($this->db); ?>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title><?= html_escape($app['NamaAplikasi']); ?> | <?= $title; ?></title>
  <link rel="icon" type="image/png" href="<?= logo_url($app); ?>">
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <link rel="stylesheet" href="<?= base_url('vendors'); ?>/plugins/fontawesome-free/css/all.min.css">
  <link rel="stylesheet" href="<?= base_url('vendors'); ?>/dist/css/adminlte.min.css">
  <link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700" rel="stylesheet">
</head>

<body class="hold-transition" style="background: #f4f6f9;">
  <div class="container mt-4 mb-4">
    <div class="row justify-content-center">
      <div class="col-12 col-lg-10">
        <div class="text-center mb-3">
          <img src="<?= logo_url($app); ?>" width="70px">
          <h4 class="mt-2">Formulir Pendaftaran Santri Baru</h4>
          <p class="text-muted">Setelah formulir dikirim, pendaftaran akan menunggu persetujuan admin.</p>
        </div>

        <?php if (validation_errors()) : ?>
          <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <b><?= validation_errors(); ?></b>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
        <?php endif; ?>

        <?php if (!empty($pesan_upload_gagal)) : ?>
          <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <b>Foto gagal diunggah:</b> <?= html_escape($pesan_upload_gagal); ?>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
        <?php endif; ?>

        <?= form_open_multipart(base_url('daftar/simpan'), ['id' => 'formSantri', 'novalidate' => 'novalidate']); ?>

        <?php $this->load->view('santri/_form_fields', [
          'mode' => $mode,
          'santri' => $santri,
          'nis_baru' => $nis_baru,
          'kelas' => $kelas,
          'jenis_kelamin_list' => $jenis_kelamin_list,
        ]); ?>

        <div class="mb-4 text-center">
          <span id="tombolSubmitFormulir">
            <button type="submit" class="btn btn-primary"><i class="fas fa-paper-plane"></i> Daftar</button>
          </span>
          <a href="<?= base_url('auth'); ?>" class="btn btn-default"><i class="fas fa-times"></i> Batal</a>
        </div>

        <?= form_close(); ?>
      </div>
    </div>
  </div>

  <script src="<?= base_url('vendors'); ?>/plugins/jquery/jquery.min.js"></script>
  <script src="<?= base_url('vendors'); ?>/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="<?= base_url('vendors'); ?>/dist/js/adminlte.min.js"></script>
  <script src="<?= base_url('assets/js/santri-form.js'); ?>"></script>
</body>

</html>
