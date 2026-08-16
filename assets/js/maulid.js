(function () {
  var bookingMap = null;
  var VILLAGE_LATITUDE = 6.1748008;
  var VILLAGE_LONGITUDE = 101.8331653;
  var VILLAGE_RADIUS_METERS = 2000;

  function distanceMeters(lat1, lng1, lat2, lng2) {
    var radius = 6371000;
    var toRadians = function (value) { return value * Math.PI / 180; };
    var deltaLat = toRadians(lat2 - lat1);
    var deltaLng = toRadians(lng2 - lng1);
    var a = Math.sin(deltaLat / 2) * Math.sin(deltaLat / 2)
      + Math.cos(toRadians(lat1)) * Math.cos(toRadians(lat2))
      * Math.sin(deltaLng / 2) * Math.sin(deltaLng / 2);
    return radius * 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
  }

  function setLocationPending(form, status, message) {
    form.querySelector('.js-location-confirmed').value = '0';
    form.querySelector('.js-save-maulid-booking').disabled = true;
    form.querySelector('.js-confirm-map-location').hidden = false;
    status.textContent = message || 'Geser peta, lalu tekan Simpan Titik Lokasi.';
  }

  function updateCenterFields(form, status) {
    var center = bookingMap.getCenter();
    var latitude = center.lat.toFixed(8);
    var longitude = center.lng.toFixed(8);
    form.querySelector('.js-latitude').value = latitude;
    form.querySelector('.js-longitude').value = longitude;
    form.querySelector('[name="maps_url"]').value = 'https://www.google.com/maps?q=' + latitude + ',' + longitude;
    setLocationPending(form, status);
  }

  function showMap(form, latitude, longitude, status, message) {
    var mapPreview = form.querySelector('.js-map-preview');
    var mapCanvas = form.querySelector('.js-map-canvas');
    if (!mapPreview || !mapCanvas || !window.L) return;
    mapPreview.hidden = false;
    if (bookingMap) bookingMap.remove();
    bookingMap = window.L.map(mapCanvas, {
      center: [latitude, longitude], zoom: 17, dragging: true, touchZoom: true,
      scrollWheelZoom: false, doubleClickZoom: false
    });
    window.L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', {
      maxNativeZoom: 18, maxZoom: 22,
      attribution: 'Imagery &copy; Esri, Maxar, Earthstar Geographics, and the GIS User Community'
    }).addTo(bookingMap);
    bookingMap.on('moveend', function () { updateCenterFields(form, status); });
    updateCenterFields(form, status);
    status.textContent = message;
    window.setTimeout(function () { bookingMap.invalidateSize(); }, 50);
  }

  function openBookingPopup(calendarButton, replaceExisting) {
    var popup = document.getElementById('maulidBookingPopup');
    if (!popup) return;
    var form = popup.querySelector('form');
    var selectedDay = calendarButton.getAttribute('data-day');
    if (form) form.reset();
    if (bookingMap) { bookingMap.remove(); bookingMap = null; }
    popup.querySelector('.js-maulid-booking-day').value = selectedDay;
    popup.querySelector('.js-maulid-replace-booking').value = replaceExisting ? '1' : '0';
    popup.querySelector('.js-maulid-booking-title').textContent = (replaceExisting ? 'Ganti ke ' : 'Booking ') + selectedDay + ' Rabiul Awal';
    popup.querySelector('.js-save-maulid-booking').disabled = true;
    popup.querySelector('.js-use-location').disabled = false;
    popup.hidden = false;
    document.body.classList.add('maulid-popup-open');
    var status = popup.querySelector('.js-location-status');
    window.setTimeout(function () {
      showMap(form, VILLAGE_LATITUDE, VILLAGE_LONGITUDE, status, 'Peta dimulai dari pusat kampung. Geser lalu simpan titik lokasi.');
    }, 20);
  }

  document.addEventListener('click', function (event) {
    var calendarButton = event.target.closest('.js-open-maulid-booking');
    var popup = document.getElementById('maulidBookingPopup');
    if (calendarButton && calendarButton.getAttribute('data-already-booked-day')) {
      var bookedDay = calendarButton.getAttribute('data-already-booked-day');
      if (window.Swal) {
        window.Swal.fire({icon:'question', title:'Sudah booking tanggal ' + bookedDay,
          text:'Apakah Anda ingin mengganti hari booking ke tanggal ' + calendarButton.getAttribute('data-day') + ' Rabiul Awal?',
          showCancelButton:true, confirmButtonText:'Ganti Hari', cancelButtonText:'Tidak', confirmButtonColor:'#198754', reverseButtons:true
        }).then(function (result) { if (result.isConfirmed || result.value) openBookingPopup(calendarButton, true); });
      } else if (window.confirm('Anda sudah booking tanggal ' + bookedDay + ' Rabiul Awal. Ganti hari booking?')) openBookingPopup(calendarButton, true);
      return;
    }
    if (calendarButton && popup) { openBookingPopup(calendarButton, false); return; }

    var cancelButton = event.target.closest('.js-maulid-cancel');
    if (cancelButton) {
      event.preventDefault();
      var cancelForm = cancelButton.closest('form');
      if (window.Swal) {
        window.Swal.fire({icon:'warning', title:'Batalkan booking?', text:'Tanggal ini akan kembali tersedia untuk pengguna lain.',
          showCancelButton:true, confirmButtonText:'Batalkan Booking', cancelButtonText:'Tetap Booking', confirmButtonColor:'#dc3545', reverseButtons:true
        }).then(function (result) { if (result.isConfirmed || result.value) HTMLFormElement.prototype.submit.call(cancelForm); });
      } else if (window.confirm('Batalkan booking ini?')) HTMLFormElement.prototype.submit.call(cancelForm);
      return;
    }

    if (event.target.closest('.js-close-maulid-booking') && popup) {
      popup.hidden = true; document.body.classList.remove('maulid-popup-open'); return;
    }

    var confirmLocation = event.target.closest('.js-confirm-map-location');
    if (confirmLocation) {
      var confirmForm = confirmLocation.closest('form');
      confirmForm.querySelector('.js-location-confirmed').value = '1';
      confirmForm.querySelector('.js-save-maulid-booking').disabled = false;
      confirmLocation.hidden = true;
      confirmForm.querySelector('.js-location-status').textContent = 'Titik lokasi sudah disimpan. Booking siap disimpan.';
      return;
    }

    var locationButton = event.target.closest('.js-use-location');
    if (!locationButton) return;
    var form = locationButton.closest('form');
    var status = form.querySelector('.js-location-status');
    if (!navigator.geolocation) { status.textContent = 'Browser tidak mendukung GPS. Pilih titik dari peta kampung.'; return; }
    locationButton.disabled = true;
    status.textContent = 'Memeriksa posisi Anda...';
    navigator.geolocation.getCurrentPosition(function (position) {
      var userLat = position.coords.latitude;
      var userLng = position.coords.longitude;
      var insideVillage = distanceMeters(userLat, userLng, VILLAGE_LATITUDE, VILLAGE_LONGITUDE) <= VILLAGE_RADIUS_METERS;
      showMap(form, insideVillage ? userLat : VILLAGE_LATITUDE, insideVillage ? userLng : VILLAGE_LONGITUDE, status,
        insideVillage ? 'Posisi Anda berada di dalam kampung. Geser lalu simpan titik.' : 'Posisi Anda di luar kampung. Peta dikembalikan ke pusat kampung.');
      locationButton.disabled = false;
    }, function () {
      showMap(form, VILLAGE_LATITUDE, VILLAGE_LONGITUDE, status, 'GPS tidak tersedia. Pilih titik dari peta kampung.');
      locationButton.disabled = false;
    }, {enableHighAccuracy:true, timeout:15000, maximumAge:60000});
  });

  var popup = document.getElementById('maulidBookingPopup');
  if (popup) {
    popup.addEventListener('click', function (event) {
      if (event.target === popup) { popup.hidden = true; document.body.classList.remove('maulid-popup-open'); }
    });
    document.addEventListener('keydown', function (event) {
      if (event.key === 'Escape' && !popup.hidden) { popup.hidden = true; document.body.classList.remove('maulid-popup-open'); }
    });
  }
})();
