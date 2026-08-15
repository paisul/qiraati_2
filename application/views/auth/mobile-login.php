<!DOCTYPE html>
<html lang="id">

<?php $app = ambil_pengaturan_aplikasi($this->db); ?>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
  <title><?= html_escape($app['NamaAplikasi']); ?> | <?= $title; ?></title>

  <link rel="manifest" href="<?= base_url('manifest.json'); ?>">
  <meta name="theme-color" content="#172a3a">
  <meta name="mobile-web-app-capable" content="yes">
  <meta name="apple-mobile-web-app-capable" content="yes">
  <link rel="icon" type="image/png" href="<?= logo_url($app); ?>">
  <link rel="apple-touch-icon" href="<?= base_url('assets/icons/icon-192.png'); ?>">

  <link rel="stylesheet" href="<?= base_url('vendors/'); ?>plugins/fontawesome-free/css/all.min.css">
  <link rel="stylesheet" href="<?= base_url('vendors/'); ?>plugins/sweetalert2/sweetalert2.min.css">
  <link rel="stylesheet" href="<?= base_url('assets/css/mobile.css'); ?>?v=<?= filemtime(FCPATH . 'assets/css/mobile.css'); ?>">

  <script src="<?= base_url('vendors/'); ?>plugins/jquery/jquery.min.js"></script>
</head>

<body>
  <div class="m-login-page">
    <div class="flash-data" data-flashdata="<?= strip_tags($this->session->flashdata('message')); ?>" data-title="<?= html_escape($app['NamaAplikasi']); ?>"></div>

    <div class="m-login-brand">
      <img src="<?= logo_url($app); ?>" alt="Logo">
      <h1><?= html_escape($app['NamaAplikasi']); ?></h1>
      <p>Aplikasi Monitoring Metode Qiroati</p>
    </div>

    <div class="m-login-card">
      <form action="<?= base_url('auth'); ?>" method="post">
        <div class="m-field">
          <label>Username/Email</label>
          <input type="text" name="username" placeholder="Username/Email" autofocus required autocomplete="off">
          <?= form_error('username', '<div class="m-error">', '</div>'); ?>
        </div>
        <div class="m-field">
          <label>Password</label>
          <input type="password" name="password" placeholder="Password" required>
          <?= form_error('password', '<div class="m-error">', '</div>'); ?>
        </div>
        <button type="submit" class="m-btn"><i class="fas fa-sign-in-alt"></i> Log In</button>
      </form>

      <a href="<?= base_url('daftar'); ?>" class="m-btn m-btn-outline mt-2"><i class="fas fa-user-plus"></i> Daftar Akun Baru</a>

      <p class="m-login-lupa">
        <a href="<?= base_url('auth/forgotpassword'); ?>">Lupa Password?</a>
      </p>
    </div>
  </div>

  <script src="<?= base_url('vendors/'); ?>plugins/sweetalert2/sweetalert2.min.js"></script>
  <script src="<?= base_url('assets'); ?>/js/myscript.js?v=<?= filemtime(FCPATH . 'assets/js/myscript.js'); ?>"></script>
</body>

</html>
