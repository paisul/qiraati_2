<link rel="stylesheet" href="<?= base_url('assets/css/maulid.css'); ?>?v=<?= filemtime(FCPATH . 'assets/css/maulid.css'); ?>">
<div class="m-content">
  <p class="m-page-title"><?= html_escape($title); ?></p>
  <div class="flash-data" data-flashdata="<?= html_escape($pesan ?? ''); ?>" data-title="<?= html_escape($title); ?>"></div>

  <div class="m-card">
    <form method="get">
      <div class="m-field"><label>Tahun Hijriah</label><input type="number" name="tahun" min="1300" max="1700" value="<?= (int) $year; ?>" required></div>
      <button class="m-btn">Tampilkan Kalender</button>
    </form>
    <?php if (!$is_admin) : ?>
      <p class="m-list-sub mt-2">Nama booking: <strong><?= html_escape($parent_name); ?></strong></p>
      <p class="m-list-sub">Geser kalender ke samping bila perlu, lalu tekan tanggal berwarna hijau untuk booking.</p>
    <?php endif; ?>
  </div>

  <?php $mobile_calendar = true; $this->load->view('maulid/calendar'); ?>

  <?php if (!$is_admin) foreach (range(1, 30) as $day) if (empty($bookings[$day])) : ?>
    <div class="m-card m-form-panel" id="bookDay<?= $day; ?>" hidden>
      <div class="m-list-title">Booking <?= $day; ?> Rabiul Awal <?= (int) $year; ?> H</div>
      <?= form_open('maulid/create'); ?>
        <?php $form_day = $day; include APPPATH . 'views/maulid/form.php'; ?>
        <button class="m-btn">Simpan Booking</button>
      <?= form_close(); ?>
    </div>
  <?php endif; ?>

  <?php if ($is_admin) : ?>
    <div class="m-card">
      <div class="m-list-title">Riwayat termasuk pembatalan</div>
      <?php foreach ($rows as $r) : ?>
        <div class="m-list-item" style="display:block">
          <strong><?= (int) $r['rabiul_awal_day']; ?> Rabiul Awal - <?= html_escape($r['booker_name']); ?></strong>
          <div class="m-list-sub"><?= html_escape($r['location_name']); ?> - <?= $r['status'] === 'booked' ? 'Aktif' : 'Dibatalkan'; ?></div>
          <?php if ($r['status'] === 'booked') : ?>
            <button type="button" class="m-btn mt-2" data-toggle-target="#editBooking<?= (int) $r['id']; ?>">Ubah</button>
            <div class="m-form-panel" id="editBooking<?= (int) $r['id']; ?>" hidden>
              <?= form_open('maulid/update/' . $r['id']); ?>
                <div class="m-field"><label>Nama/Lokasi</label><input name="location_name" value="<?= html_escape($r['location_name']); ?>" required></div>
                <div class="m-field"><label>Latitude</label><input name="latitude" value="<?= html_escape($r['latitude']); ?>"></div>
                <div class="m-field"><label>Longitude</label><input name="longitude" value="<?= html_escape($r['longitude']); ?>"></div>
                <div class="m-field"><label>Link Maps</label><input type="url" name="maps_url" value="<?= html_escape($r['maps_url']); ?>"></div>
                <div class="m-field"><label>Catatan</label><textarea name="notes"><?= html_escape($r['notes']); ?></textarea></div>
                <button class="m-btn">Simpan</button>
              <?= form_close(); ?>
            </div>
          <?php endif; ?>
        </div>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>
</div>
<script src="<?= base_url('assets/js/maulid.js'); ?>"></script>
