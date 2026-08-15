// Dipakai bersama oleh halaman Tambah/Ubah Santri (admin) dan Pendaftaran Santri (publik).
// Preview foto sebelum upload + validasi tipe/ukuran file, dan highlight Bootstrap saat submit gagal.
(function () {
  var MAKS_UKURAN = 2 * 1024 * 1024; // 2MB
  var TIPE_DIIZINKAN = ['image/jpeg', 'image/png', 'image/jpg'];

  var inputFoto = document.getElementById('pasfoto');
  var preview = document.getElementById('previewPasfoto');
  var errorFoto = document.getElementById('pasfotoError');
  var label = document.querySelector('label[for="pasfoto"]');

  if (inputFoto) {
    inputFoto.addEventListener('change', function () {
      errorFoto.style.display = 'none';
      errorFoto.textContent = '';

      var file = inputFoto.files[0];
      if (!file) {
        return;
      }

      label.textContent = file.name;

      if (TIPE_DIIZINKAN.indexOf(file.type) === -1) {
        errorFoto.textContent = 'Format foto harus JPG, JPEG, atau PNG.';
        errorFoto.style.display = 'block';
        inputFoto.value = '';
        label.textContent = 'Pilih Foto';
        return;
      }

      if (file.size > MAKS_UKURAN) {
        errorFoto.textContent = 'Ukuran foto maksimal 2MB.';
        errorFoto.style.display = 'block';
        inputFoto.value = '';
        label.textContent = 'Pilih Foto';
        return;
      }

      var reader = new FileReader();
      reader.onload = function (e) {
        preview.src = e.target.result;
        preview.style.display = 'inline-block';
      };
      reader.readAsDataURL(file);
    });
  }

  // Validasi Bootstrap bawaan browser: tampilkan highlight is-invalid saat submit gagal.
  var form = document.getElementById('formSantri');
  if (form) {
    form.addEventListener('submit', function (e) {
      if (!form.checkValidity()) {
        e.preventDefault();
        e.stopPropagation();
      }
      form.classList.add('was-validated');
    });
  }
})();
