<?php
$nama_hari = ['Ahad', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
$mobile_calendar = !empty($mobile_calendar);
?>
<div class="maulid-calendar-scroll">
  <div class="maulid-calendar" role="grid" aria-label="Kalender Rabiul Awal <?= (int) $year; ?> Hijriah tahun <?= (int) $gregorian_year; ?> Masehi">
    <div class="maulid-week-header" role="row">
      <?php foreach ($nama_hari as $hari) : ?>
        <div class="maulid-week-day" role="columnheader"><?= html_escape($hari); ?></div>
      <?php endforeach; ?>
    </div>
    <div class="maulid-calendar-days" role="rowgroup">
      <?php for ($blank = 0; $blank < $calendar_start_offset; $blank++) : ?><div class="maulid-day is-empty" aria-hidden="true"></div><?php endfor; ?>
      <?php for ($day = 1; $day <= 30; $day++) :
        $b = $bookings[$day] ?? null;
        $masehi = $gregorian_dates[$day];
        $maps_url_tampil = $b ? ($b['latitude'] !== null && $b['longitude'] !== null
          ? 'https://www.google.com/maps/search/?api=1&query=' . rawurlencode($b['latitude'] . ',' . $b['longitude'])
          : $b['maps_url']) : null;
      ?>
        <div class="maulid-day <?= $b ? 'is-booked' : 'is-available'; ?>" role="gridcell">
          <?php if (!$b && !$is_admin) : ?>
            <button type="button" class="maulid-day-book"
              <?php if ($mobile_calendar) : ?>data-toggle-target="#bookDay<?= $day; ?>"<?php else : ?>data-toggle="modal" data-target="#bookDay<?= $day; ?>"<?php endif; ?>
              aria-label="Booking tanggal <?= $day; ?> Rabiul Awal">
              <span class="maulid-day-number"><span><?= $day; ?></span><small>H</small></span>
              <span class="maulid-gregorian"><?= html_escape($masehi['label']); ?></span>
              <span class="maulid-status available">Tersedia</span>
              <span class="maulid-tap-hint">Tekan untuk booking</span>
            </button>
          <?php else : ?>
            <div class="maulid-day-content">
              <span class="maulid-day-number"><span><?= $day; ?></span><small>H</small></span>
              <span class="maulid-gregorian"><?= html_escape($masehi['label']); ?></span>
              <?php if ($b) : ?>
                <span class="maulid-status booked">Dibooking</span>
                <strong class="maulid-booker"><?= html_escape($b['booker_name']); ?></strong>
                <span class="maulid-location"><?= html_escape($b['location_name']); ?></span>
                <?php if ($maps_url_tampil) : ?><a class="maulid-map-link" target="_blank" rel="noopener noreferrer" href="<?= html_escape($maps_url_tampil); ?>"><i class="fas fa-map-marker-alt"></i> Lokasi</a><?php endif; ?>
                <?php if ($is_admin || (int) $b['user_id'] === $current_user_id) : ?>
                  <?= form_open('maulid/cancel/' . $b['id'], ['class' => 'maulid-cancel-form']); ?><button type="submit" class="maulid-cancel" onclick="return confirm('Batalkan booking ini?')">Batalkan</button><?= form_close(); ?>
                <?php endif; ?>
              <?php else : ?><span class="maulid-status available">Tersedia</span><?php endif; ?>
            </div>
          <?php endif; ?>
        </div>
      <?php endfor; ?>
      <?php $trailing = (7 - (($calendar_start_offset + 30) % 7)) % 7; ?>
      <?php for ($empty = 0; $empty < $trailing; $empty++) : ?><div class="maulid-day is-empty" aria-hidden="true"></div><?php endfor; ?>
    </div>
  </div>
</div>
