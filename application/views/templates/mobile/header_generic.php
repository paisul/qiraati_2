<?php $tema_aktif = ambil_tema_user($this->db, $this->session->userdata('username')); ?>
<!DOCTYPE html>
<html lang="id" data-theme="<?= $tema_aktif; ?>">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
  <meta http-equiv="x-ua-compatible" content="ie=edge">

  <?php $app = ambil_pengaturan_aplikasi($this->db); ?>
  <title><?= html_escape($app['NamaAplikasi']); ?> | <?= $title; ?></title>

  <link rel="manifest" href="<?= base_url('manifest.json'); ?>">
  <meta name="theme-color" content="<?= $tema_aktif == 'gelap' ? '#0d1117' : '#172a3a'; ?>">
  <meta name="mobile-web-app-capable" content="yes">
  <meta name="apple-mobile-web-app-capable" content="yes">
  <link rel="icon" type="image/png" href="<?= logo_url($app); ?>">
  <link rel="apple-touch-icon" href="<?= base_url('assets/icons/icon-192.png'); ?>">

  <link rel="stylesheet" href="<?= base_url('vendors/'); ?>plugins/fontawesome-free/css/all.min.css">
  <!-- CSS inti SweetAlert2 (dipakai myscript.js untuk konfirmasi hapus & pesan berhasil/gagal) -
       wajib dimuat, kalau tidak popup-nya kehilangan backdrop/tata letak sama sekali. -->
  <link rel="stylesheet" href="<?= base_url('vendors/'); ?>plugins/sweetalert2/sweetalert2.min.css">
  <link rel="stylesheet" href="<?= base_url('assets/css/mobile.css'); ?>?v=<?= filemtime(FCPATH . 'assets/css/mobile.css'); ?>">

  <script src="<?= base_url('vendors/'); ?>plugins/jquery/jquery.min.js"></script>
</head>

<body class="<?= empty($ada_bottom_nav) ? 'm-no-bottom-nav' : ''; ?>">
  <div class="m-app">
    <!-- Logout dipindah ke drawer (m-drawer-logout) untuk semua peran yang lewat header ini
         (Admin/Bagian Administrasi/Musyrif/Guru) - ikon di topbar jadi berlebihan, dihapus. -->
    <div class="m-topbar">
      <button type="button" id="mHamburger" class="m-back" style="background:none; border:none; padding:0;"><i class="fas fa-bars"></i></button>
      <h1><?= html_escape($title); ?></h1>
      <button type="button" id="mThemeToggle" class="m-theme-btn" data-toggle-url="<?= base_url('tema/toggle'); ?>" aria-label="Ganti tema gelap/terang">
        <i class="fas <?= $tema_aktif == 'gelap' ? 'fa-sun' : 'fa-moon'; ?>"></i>
      </button>
    </div>

    <?php require_once __DIR__ . '/drawer_menu.php'; ?>

    <?php
    // Controller boleh kirim $pesan sendiri (mis. Dana - lewat query string ?pesan=, bukan session
    // flashdata, karena flashdata di server ini kadang tidak kadaluarsa dengan benar). Kalau tidak
    // ada, pakai session flashdata seperti biasa (pola lama, masih dipakai controller lain).
    $pesan_tampil = isset($pesan) ? $pesan : $this->session->flashdata('pesan');
    ?>
    <div class="flash-data" data-flashdata="<?= $pesan_tampil ?? ''; ?>" data-title="<?= html_escape($title); ?>"></div>