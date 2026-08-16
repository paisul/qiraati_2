<input type="hidden" name="hijri_year" value="<?= (int)$year; ?>"><input type="hidden" class="js-maulid-booking-day" name="rabiul_awal_day" value="<?= (int)$form_day; ?>">
<?php if (!empty($is_musyrif)) : ?>
<div class="form-group"><label>Nama Musyrif</label><input class="form-control" value="<?= html_escape($parent_name); ?>" readonly></div>
<?php else : ?>
<div class="form-group"><label>Nama Santri</label><textarea class="form-control" rows="<?= max(1, count($student_names)); ?>" readonly><?= html_escape(implode("\n", $student_names)); ?></textarea></div>
<div class="form-group"><label>Nama Wali</label><input class="form-control" value="<?= html_escape($parent_name); ?>" readonly></div>
<?php endif; ?>
<input type="hidden" class="js-latitude" name="latitude"><input type="hidden" class="js-longitude" name="longitude">
<input type="hidden" name="maps_url">
<button type="button" class="maulid-location-button js-use-location">
  <span class="maulid-location-icon"><i class="fas fa-location-arrow"></i></span>
  <span class="maulid-location-copy"><strong>Gunakan Lokasi Saya</strong><small class="js-location-status">Tekan untuk mengambil titik lokasi acara</small></span>
  <span class="maulid-location-action"><i class="fas fa-chevron-right"></i></span>
</button>
<div class="maulid-map-preview js-map-preview" hidden>
  <div class="maulid-map-canvas js-map-canvas" aria-label="Geser peta untuk menentukan titik lokasi"></div>
  <div class="maulid-map-center-pin" aria-hidden="true"><i class="fas fa-map-marker-alt"></i></div>
  <div class="maulid-map-confirm"><i class="fas fa-hand-pointer"></i> Geser peta dengan satu jari dan gunakan tombol +/− untuk memperbesar. Titik yang dipilih selalu berada tepat di tengah.</div>
</div>
