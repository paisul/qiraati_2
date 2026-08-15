<?php $tema_aktif = ambil_tema_user($this->db, $this->session->userdata('username')); ?>
<!DOCTYPE html>
<html lang="id" data-theme="<?= $tema_aktif; ?>">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
  <meta http-equiv="x-ua-compatible" content="ie=edge">

  <?php $app = ambil_pengaturan_aplikasi($this->db); ?>
  <title><?= html_escape($app['NamaAplikasi']); ?> | <?= $title; ?></title>

  <!-- PWA -->
  <link rel="manifest" href="<?= base_url('manifest.json'); ?>">
  <meta name="theme-color" content="<?= $tema_aktif == 'gelap' ? '#0d1117' : '#172a3a'; ?>">
  <meta name="mobile-web-app-capable" content="yes">
  <meta name="apple-mobile-web-app-capable" content="yes">
  <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
  <meta name="apple-mobile-web-app-title" content="<?= html_escape($app['NamaAplikasi']); ?>">
  <link rel="icon" type="image/png" href="<?= logo_url($app); ?>">
  <link rel="apple-touch-icon" href="<?= base_url('assets/icons/icon-192.png'); ?>">

  <!-- Font Awesome (dipakai bottom nav & ikon kartu) -->
  <link rel="stylesheet" href="<?= base_url('vendors/'); ?>plugins/fontawesome-free/css/all.min.css">
  <!-- CSS inti SweetAlert2 (dipakai myscript.js untuk konfirmasi hapus & pesan berhasil/gagal) -
       wajib dimuat, kalau tidak popup-nya kehilangan backdrop/tata letak sama sekali. -->
  <link rel="stylesheet" href="<?= base_url('vendors/'); ?>plugins/sweetalert2/sweetalert2.min.css">

  <!-- CSS mobile khusus - bukan turunan AdminLTE desktop -->
  <link rel="stylesheet" href="<?= base_url('assets/css/mobile.css'); ?>?v=<?= filemtime(FCPATH . 'assets/css/mobile.css'); ?>">

  <script src="<?= base_url('vendors/'); ?>plugins/jquery/jquery.min.js"></script>
</head>

<body>
  <div class="m-app">
    <div class="m-topbar">
      <img src="<?= logo_url($app); ?>" alt="Logo">
      <h1><?= html_escape($title); ?></h1>
      <button type="button" id="mThemeToggle" class="m-theme-btn" data-toggle-url="<?= base_url('tema/toggle'); ?>" aria-label="Ganti tema gelap/terang">
        <i class="fas <?= $tema_aktif == 'gelap' ? 'fa-sun' : 'fa-moon'; ?>"></i>
      </button>
      <a href="<?= base_url('auth/logout'); ?>" class="m-logout"><i class="fas fa-sign-out-alt"></i></a>
    </div>

    <?php
    // Controller boleh kirim $pesan sendiri lewat query string ?pesan= (mis. redirect dari hook
    // CekMenuAktif) - session flashdata di server ini kadang tidak kadaluarsa dengan benar, lihat
    // komentar di Dana::index() & header_generic.php.
    $pesan_tampil = isset($pesan) ? $pesan : $this->session->flashdata('pesan');
    ?>
    <div class="flash-data" data-flashdata="<?= $pesan_tampil ?? ''; ?>" data-title="<?= html_escape($title); ?>"></div>

    <div id="mInstallBar" class="m-install-bar">
      <span>Pasang aplikasi ini di layar utama HP Anda</span>
      <button id="mInstallBtn" type="button">Pasang</button>
    </div>
