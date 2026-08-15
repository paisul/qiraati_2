<!DOCTYPE html>
<html>

<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <?php $app = ambil_pengaturan_aplikasi($this->db); ?>
  <title><?= html_escape($app['NamaAplikasi']); ?> | <?= $title; ?></title>
  <link rel="icon" type="image/png" href="<?= logo_url($app); ?>">
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <link rel="stylesheet" href="<?= base_url('vendors'); ?>/plugins/fontawesome-free/css/all.min.css">
  <link rel="stylesheet" href="<?= base_url('vendors'); ?>/dist/css/adminlte.min.css">
</head>

<body class="hold-transition login-page">
  <div class="login-box">
    <div class="card" style="width: 25rem;">
      <div class="card-body login-card-body text-center">
        <i class="fas fa-check-circle text-success" style="font-size: 60px;"></i>
        <h4 class="mt-3">Pendaftaran Terkirim!</h4>
        <p class="mt-2">Terima kasih, data pendaftaran Anda sudah kami terima dan sedang <strong>menunggu persetujuan admin</strong>. Anda akan bisa login setelah pendaftaran disetujui.</p>
        <a href="<?= base_url('auth'); ?>" class="btn btn-primary mt-2">Kembali ke Halaman Login</a>
      </div>
    </div>
  </div>
</body>

</html>
