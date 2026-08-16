<link rel="stylesheet" href="<?= base_url('assets/css/maulid.css'); ?>?v=<?= filemtime(FCPATH . 'assets/css/maulid.css'); ?>">
<div class="m-content">
  <div class="flash-data" data-flashdata="<?= html_escape($pesan ?? ''); ?>" data-title="<?= html_escape($title); ?>"></div>

  <?php $mobile_calendar = true; $this->load->view('maulid/calendar'); ?>
  <?php if (!$is_admin && !$has_active_booking) $this->load->view('maulid/booking-popup'); ?>

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
<script src="<?= base_url('assets/js/maulid.js'); ?>?v=<?= filemtime(FCPATH . 'assets/js/maulid.js'); ?>"></script>
