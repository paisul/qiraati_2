<!DOCTYPE html>
<html lang="id">

<?php $app = ambil_pengaturan_aplikasi($this->db); ?>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= html_escape($app['NamaAplikasi']); ?> | <?= $title; ?></title>
  <link rel="icon" type="image/png" href="<?= logo_url($app); ?>">
  <link rel="stylesheet" href="<?= base_url('vendors'); ?>/plugins/fontawesome-free/css/all.min.css">
  <link rel="stylesheet" href="<?= base_url('vendors'); ?>/plugins/bootstrap/css/bootstrap.min.css">
  <style>
    body {
      background: #f4f7fb;
      color: #14213d;
      font-family: "Source Sans Pro", Arial, sans-serif;
    }

    .topbar {
      background: #ffffff;
      border-bottom: 1px solid #dce3ec;
    }

    .brand-logo {
      width: 48px;
      height: 48px;
      object-fit: contain;
    }

    .hero {
      background: #0b3954;
      color: #ffffff;
      padding: 48px 0;
    }

    .stat-card {
      background: #ffffff;
      border: 1px solid #dce3ec;
      border-radius: 8px;
      min-height: 138px;
      padding: 22px;
      box-shadow: 0 12px 26px rgba(16, 24, 40, .08);
    }

    .stat-icon {
      width: 44px;
      height: 44px;
      border-radius: 8px;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      color: #ffffff;
    }

    .stat-number {
      font-size: 34px;
      line-height: 1.1;
      font-weight: 700;
      margin: 14px 0 4px;
    }

    .section-panel {
      background: #ffffff;
      border: 1px solid #dce3ec;
      border-radius: 8px;
      padding: 22px;
    }

    .bar-track {
      background: #e9eef5;
      border-radius: 6px;
      height: 12px;
      overflow: hidden;
    }

    .bar-fill {
      background: #2a9d8f;
      height: 12px;
    }

    .muted {
      color: #62748c;
    }
  </style>
</head>

<body>
  <nav class="topbar py-3">
    <div class="container d-flex align-items-center justify-content-between">
      <div class="d-flex align-items-center">
        <img class="brand-logo mr-3" src="<?= logo_url($app); ?>" alt="Logo">
        <div>
          <strong><?= html_escape($app['NamaAplikasi']); ?></strong>
          <div class="small muted">Statistik Publik</div>
        </div>
      </div>
      <a href="<?= base_url('auth'); ?>" class="btn btn-outline-primary btn-sm">
        <i class="fas fa-sign-in-alt mr-1"></i> Login
      </a>
    </div>
  </nav>

  <header class="hero">
    <div class="container">
      <div class="row align-items-center">
        <div class="col-lg-8">
          <h1 class="mb-3">Statistik Qiroati</h1>
          <p class="lead mb-0">Ringkasan data murid, guru, dan dana yang dapat dilihat tanpa login.</p>
        </div>
        <div class="col-lg-4 mt-4 mt-lg-0 text-lg-right">
          <span class="badge badge-light p-2">Periode terbaru: <?= $ringkasan['periode']; ?></span>
        </div>
      </div>
    </div>
  </header>

  <main class="container py-5">
    <div class="row">
      <div class="col-md-4 mb-4">
        <div class="stat-card">
          <span class="stat-icon" style="background:#2a9d8f;"><i class="fas fa-user-graduate"></i></span>
          <div class="stat-number"><?= number_format($ringkasan['murid'], 0, ',', '.'); ?></div>
          <div class="muted">Total Murid</div>
          <small><?= number_format($ringkasan['murid_aktif'], 0, ',', '.'); ?> murid aktif</small>
        </div>
      </div>
      <div class="col-md-4 mb-4">
        <div class="stat-card">
          <span class="stat-icon" style="background:#e76f51;"><i class="fas fa-chalkboard-teacher"></i></span>
          <div class="stat-number"><?= number_format($ringkasan['guru'], 0, ',', '.'); ?></div>
          <div class="muted">Total Guru</div>
          <small><?= number_format($ringkasan['kelas'], 0, ',', '.'); ?> kelas terdaftar</small>
        </div>
      </div>
      <div class="col-md-4 mb-4">
        <div class="stat-card">
          <span class="stat-icon" style="background:#457b9d;"><i class="fas fa-wallet"></i></span>
          <div class="stat-number" style="font-size:28px;">Rp <?= number_format($ringkasan['dana'], 0, ',', '.'); ?></div>
          <div class="muted">Total Dana</div>
          <small>Otomatis terbaca jika tabel dana tersedia</small>
        </div>
      </div>
    </div>

    <div class="row">
      <div class="col-lg-7 mb-4">
        <div class="section-panel">
          <h5 class="mb-3">Murid per Kelas</h5>
          <?php
          $maksimum = 1;
          foreach ($murid_per_kelas as $kelas) {
            if ((int) $kelas['Jumlah'] > $maksimum) {
              $maksimum = (int) $kelas['Jumlah'];
            }
          }
          ?>
          <?php foreach ($murid_per_kelas as $kelas) : ?>
            <?php $persen = ((int) $kelas['Jumlah'] / $maksimum) * 100; ?>
            <div class="mb-3">
              <div class="d-flex justify-content-between mb-1">
                <span><?= $kelas['NamaKelas']; ?></span>
                <strong><?= number_format($kelas['Jumlah'], 0, ',', '.'); ?></strong>
              </div>
              <div class="bar-track">
                <div class="bar-fill" style="width: <?= $persen; ?>%;"></div>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
      </div>
      <div class="col-lg-5 mb-4">
        <div class="section-panel">
          <h5 class="mb-3">Status Murid</h5>
          <table class="table table-sm">
            <tbody>
              <?php foreach ($status_murid as $status) : ?>
                <tr>
                  <td><?= $status['Status']; ?></td>
                  <td class="text-right"><strong><?= number_format($status['Jumlah'], 0, ',', '.'); ?></strong></td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
          <hr>
          <div class="d-flex justify-content-between">
            <span class="muted">Akun wali</span>
            <strong><?= number_format($ringkasan['wali'], 0, ',', '.'); ?></strong>
          </div>
        </div>
      </div>
    </div>
  </main>

  <script src="<?= base_url('vendors'); ?>/plugins/jquery/jquery.min.js"></script>
  <script src="<?= base_url('vendors'); ?>/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>

</html>
