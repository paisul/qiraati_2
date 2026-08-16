<input type="hidden" name="hijri_year" value="<?= (int)$year; ?>"><input type="hidden" class="js-maulid-booking-day" name="rabiul_awal_day" value="<?= (int)$form_day; ?>">
<div class="form-group"><label>Nama Santri</label><textarea class="form-control" rows="<?= max(1, count($student_names)); ?>" readonly><?= html_escape(implode("\n", $student_names)); ?></textarea></div>
<div class="form-group"><label>Nama Wali</label><input class="form-control" value="<?= html_escape($parent_name); ?>" readonly></div>
<input type="hidden" class="js-latitude" name="latitude"><input type="hidden" class="js-longitude" name="longitude">
<div class="form-group"><label>Lokasi Google Maps *</label><input type="url" class="form-control" name="maps_url" placeholder="Tempel link Google Maps di sini"><small class="form-text text-muted js-location-status">Tempel link Google Maps atau gunakan lokasi perangkat.</small></div>
<button type="button" class="btn btn-outline-primary btn-block js-use-location"><i class="fas fa-crosshairs"></i> Gunakan Lokasi Saya</button>
