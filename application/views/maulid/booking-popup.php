<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="">
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
<div class="maulid-booking-overlay" id="maulidBookingPopup" hidden>
  <div class="maulid-booking-dialog" role="dialog" aria-modal="true" aria-labelledby="maulidBookingTitle">
    <div class="maulid-booking-header">
      <div>
        <small>Booking Maulid <?= (int) $gregorian_year; ?></small>
        <h3 id="maulidBookingTitle" class="js-maulid-booking-title">Booking 1 Rabiul Awal</h3>
      </div>
      <button type="button" class="maulid-popup-close js-close-maulid-booking" aria-label="Tutup popup">&times;</button>
    </div>
    <?= form_open('maulid/create'); ?>
      <div class="maulid-booking-body">
        <?php $form_day = 1; include APPPATH . 'views/maulid/form.php'; ?>
      </div>
      <div class="maulid-booking-footer">
        <button type="button" class="maulid-popup-secondary js-close-maulid-booking">Batal</button>
        <button type="submit" class="maulid-popup-primary js-save-maulid-booking" disabled><i class="fas fa-save"></i> Simpan Booking</button>
      </div>
    <?= form_close(); ?>
  </div>
</div>
