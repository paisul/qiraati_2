(function () {
  var bookingMap = null;

  function openBookingPopup(calendarButton, replaceExisting) {
    var bookingPopup = document.getElementById('maulidBookingPopup');
    if (!bookingPopup) return;
    var selectedDay = calendarButton.getAttribute('data-day');
    var bookingForm = bookingPopup.querySelector('form');
    var dayInput = bookingPopup.querySelector('.js-maulid-booking-day');
    var replaceInput = bookingPopup.querySelector('.js-maulid-replace-booking');
    var popupTitle = bookingPopup.querySelector('.js-maulid-booking-title');
    var existingMapPreview = bookingPopup.querySelector('.js-map-preview');
    var locationButton = bookingPopup.querySelector('.js-use-location');
    var locationStatus = bookingPopup.querySelector('.js-location-status');

    if (bookingForm) bookingForm.reset();
    if (locationButton) locationButton.disabled = false;
    if (locationStatus) locationStatus.textContent = 'Tekan untuk mengambil titik lokasi acara';
    if (existingMapPreview) existingMapPreview.hidden = true;
    if (bookingMap) {
      bookingMap.remove();
      bookingMap = null;
    }
    if (dayInput) dayInput.value = selectedDay;
    if (replaceInput) replaceInput.value = replaceExisting ? '1' : '0';
    if (popupTitle) popupTitle.textContent = (replaceExisting ? 'Ganti ke ' : 'Booking ') + selectedDay + ' Rabiul Awal';

    bookingPopup.hidden = false;
    document.body.classList.add('maulid-popup-open');
    var closeButton = bookingPopup.querySelector('.js-close-maulid-booking');
    if (closeButton) closeButton.focus();
  }

  document.addEventListener('click', function (event) {
    var calendarButton = event.target.closest('.js-open-maulid-booking');
    var bookingPopup = document.getElementById('maulidBookingPopup');
    if (calendarButton && calendarButton.getAttribute('data-already-booked-day')) {
      var bookedDay = calendarButton.getAttribute('data-already-booked-day');
      if (window.Swal) {
        window.Swal.fire({
          icon: 'question',
          title: 'Sudah booking tanggal ' + bookedDay,
          text: 'Apakah Anda ingin mengganti hari booking ke tanggal ' + calendarButton.getAttribute('data-day') + ' Rabiul Awal?',
          showCancelButton: true,
          confirmButtonText: 'Ganti Hari',
          cancelButtonText: 'Tidak',
          confirmButtonColor: '#198754',
          reverseButtons: true
        }).then(function (result) {
          if (result.isConfirmed) openBookingPopup(calendarButton, true);
        });
      } else if (window.confirm('Anda sudah booking tanggal ' + bookedDay + ' Rabiul Awal. Ganti hari booking?')) {
        openBookingPopup(calendarButton, true);
      }
      return;
    }
    if (calendarButton && bookingPopup) {
      openBookingPopup(calendarButton, false);
      return;
    }

    var cancelButton = event.target.closest('.js-maulid-cancel');
    if (cancelButton) {
      event.preventDefault();
      var cancelForm = cancelButton.closest('form');
      if (window.Swal) {
        window.Swal.fire({
          icon: 'warning',
          title: 'Batalkan booking?',
          text: 'Tanggal ini akan kembali tersedia untuk pengguna lain.',
          showCancelButton: true,
          confirmButtonText: 'Batalkan Booking',
          cancelButtonText: 'Tetap Booking',
          confirmButtonColor: '#dc3545',
          reverseButtons: true
        }).then(function (result) {
          if (result.isConfirmed) cancelForm.submit();
        });
      } else if (window.confirm('Batalkan booking ini?')) {
        cancelForm.submit();
      }
      return;
    }

    if (event.target.closest('.js-close-maulid-booking') && bookingPopup) {
      bookingPopup.hidden = true;
      document.body.classList.remove('maulid-popup-open');
      return;
    }

    var button = event.target.closest('.js-use-location');
    if (!button) return;
    var form = button.closest('form');
    var status = form.querySelector('.js-location-status');
    if (!navigator.geolocation) {
      status.textContent = 'Browser ini tidak mendukung pengambilan lokasi.';
      return;
    }

    button.disabled = true;
    status.textContent = 'Mengambil lokasi...';
    navigator.geolocation.getCurrentPosition(function (position) {
      var latitude = position.coords.latitude.toFixed(8);
      var longitude = position.coords.longitude.toFixed(8);
      form.querySelector('.js-latitude').value = latitude;
      form.querySelector('.js-longitude').value = longitude;
      form.querySelector('[name="maps_url"]').value = 'https://www.google.com/maps?q=' + latitude + ',' + longitude;
      var mapPreview = form.querySelector('.js-map-preview');
      if (mapPreview) mapPreview.hidden = false;
      var mapCanvas = form.querySelector('.js-map-canvas');
      if (mapCanvas && window.L) {
        bookingMap = window.L.map(mapCanvas, {
          center: [latitude, longitude],
          zoom: 17,
          dragging: true,
          touchZoom: true,
          scrollWheelZoom: false,
          doubleClickZoom: false
        });
        window.L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', {
          // Beberapa wilayah belum memiliki tile Esri level 19. Gunakan citra level 18
          // sebagai sumber, lalu biarkan Leaflet memperbesarnya sampai level 22.
          maxNativeZoom: 18,
          maxZoom: 22,
          attribution: 'Imagery &copy; Esri, Maxar, Earthstar Geographics, and the GIS User Community'
        }).addTo(bookingMap);
        bookingMap.on('moveend', function () {
          var center = bookingMap.getCenter();
          var selectedLatitude = center.lat.toFixed(8);
          var selectedLongitude = center.lng.toFixed(8);
          form.querySelector('.js-latitude').value = selectedLatitude;
          form.querySelector('.js-longitude').value = selectedLongitude;
          form.querySelector('[name="maps_url"]').value = 'https://www.google.com/maps?q=' + selectedLatitude + ',' + selectedLongitude;
          status.textContent = 'Titik tengah peta sudah dipilih. Geser lagi jika belum sesuai.';
        });
        window.setTimeout(function () { bookingMap.invalidateSize(); }, 50);
      }
      status.textContent = 'Lokasi Google Maps berhasil diambil (akurasi sekitar ' + Math.round(position.coords.accuracy) + ' meter).';
      button.disabled = false;
    }, function () {
      status.textContent = 'Lokasi gagal diambil. Aktifkan GPS/izin lokasi, atau tempel link Google Maps.';
      button.disabled = false;
    }, {enableHighAccuracy: true, timeout: 15000, maximumAge: 60000});
  });

  var bookingPopup = document.getElementById('maulidBookingPopup');
  if (bookingPopup) {
    bookingPopup.addEventListener('click', function (event) {
      if (event.target === bookingPopup) {
        bookingPopup.hidden = true;
        document.body.classList.remove('maulid-popup-open');
      }
    });

    document.addEventListener('keydown', function (event) {
      if (event.key === 'Escape' && !bookingPopup.hidden) {
        bookingPopup.hidden = true;
        document.body.classList.remove('maulid-popup-open');
      }
    });
  }
})();
