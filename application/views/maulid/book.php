<link rel="stylesheet" href="<?= base_url('assets/css/maulid.css'); ?>?v=<?= filemtime(FCPATH . 'assets/css/maulid.css'); ?>">
<?php if ($is_mobile) : ?>
  <div class="m-content">
    <p class="m-page-title">Booking <?= (int) $form_day; ?> Rabiul Awal <?= (int) $year; ?> H</p>
    <div class="m-card">
      <?= form_open('maulid/create'); ?>
        <?php include APPPATH . 'views/maulid/form.php'; ?>
        <button type="submit" class="m-btn"><i class="fas fa-save"></i> Simpan Booking</button>
        <a href="<?= base_url('maulid'); ?>" class="m-btn mt-2" style="display:block;text-align:center;background:#6c757d;text-decoration:none;">Kembali ke Kalender</a>
      <?= form_close(); ?>
    </div>
  </div>
<?php else : ?>
  <div class="content-wrapper"><div class="content pt-3"><div class="container-fluid">
    <div class="row justify-content-center"><div class="col-lg-7"><div class="card">
      <div class="card-header bg-success"><h4 class="m-0">Booking <?= (int) $form_day; ?> Rabiul Awal <?= (int) $year; ?> H</h4></div>
      <?= form_open('maulid/create'); ?><div class="card-body">
        <?php include APPPATH . 'views/maulid/form.php'; ?>
      </div><div class="card-footer d-flex justify-content-between">
        <a href="<?= base_url('maulid'); ?>" class="btn btn-secondary">Kembali</a>
        <button type="submit" class="btn btn-success"><i class="fas fa-save"></i> Simpan Booking</button>
      </div><?= form_close(); ?>
    </div></div></div>
  </div></div></div>
<?php endif; ?>
<script src="<?= base_url('assets/js/maulid.js'); ?>?v=<?= filemtime(FCPATH . 'assets/js/maulid.js'); ?>"></script>
