(function () {
  document.addEventListener('click', function (event) {
    var calendarButton = event.target.closest('.maulid-day-book[data-toggle-target]');
    if (calendarButton) {
      var target = document.querySelector(calendarButton.getAttribute('data-toggle-target'));
      if (target) {
        document.querySelectorAll('.m-form-panel[id^="bookDay"]').forEach(function (panel) {
          if (panel !== target) panel.hidden = true;
        });
        // Jamin panel tetap terbuka walau handler generik mobile ikut memproses tombol yang sama.
        target.hidden = false;
        calendarButton.setAttribute('aria-expanded', 'true');
      }
      window.setTimeout(function () {
        if (target && !target.hidden) target.scrollIntoView({behavior: 'smooth', block: 'start'});
      }, 80);
    }

    var button = event.target.closest('.js-use-location');
    if (!button) return;
    var form = button.closest('form');
    var status = form.querySelector('.js-location-status');
    if (!navigator.geolocation) { status.textContent = 'Browser ini tidak mendukung pengambilan lokasi.'; return; }
    button.disabled = true;
    status.textContent = 'Mengambil lokasi…';
    navigator.geolocation.getCurrentPosition(function (position) {
      form.querySelector('.js-latitude').value = position.coords.latitude.toFixed(8);
      form.querySelector('.js-longitude').value = position.coords.longitude.toFixed(8);
      status.textContent = 'Lokasi berhasil diambil (akurasi ±' + Math.round(position.coords.accuracy) + ' meter).';
      button.disabled = false;
    }, function () {
      status.textContent = 'Lokasi gagal diambil. Aktifkan GPS/izin lokasi, atau tempel link Google Maps.';
      button.disabled = false;
    }, {enableHighAccuracy: true, timeout: 15000, maximumAge: 60000});
  });
})();
