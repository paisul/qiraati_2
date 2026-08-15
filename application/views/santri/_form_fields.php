<?php
// Partial field Formulir Data Santri (Data Wajib + Data Opsional), dirender sebagai wizard 2 langkah.
// Dipakai bersama oleh santri/form.php (admin) dan daftar/formulir.php (pendaftaran publik).
// Variabel wajib ada: $mode, $santri, $nis_baru, $kelas, $jenis_kelamin_list.
// Variabel opsional: $readonly_email (bool, default false).
$dipilih = function ($nilai, $target) {
  return (string) $nilai === (string) $target ? 'selected' : '';
};
$readonly_email = isset($readonly_email) ? $readonly_email : false;
?>
<!-- Indikator Langkah -->
<div class="mb-3">
  <div class="d-flex justify-content-between flex-wrap mb-1">
    <span class="wizard-step-label font-weight-bold" data-step-label="1">Langkah 1: Data Wajib</span>
    <span class="wizard-step-label text-muted" data-step-label="2">Langkah 2: Data Tambahan</span>
  </div>
  <div class="progress" style="height: 8px;">
    <div class="progress-bar bg-success" id="wizardProgressBar" role="progressbar" style="width: 50%;" aria-valuemin="0" aria-valuemax="100"></div>
  </div>
</div>

<!-- Langkah 1: Data Wajib -->
<div class="wizard-step" data-step="1">
  <div class="card">
    <div class="card-header bg-success">
      <h4 class="m-0">Data Wajib</h4>
    </div>
    <div class="card-body">
      <div class="row">
        <div class="form-group col-md-3">
          <label for="nis">Nomor Induk Santri (NIS)</label>
          <input type="text" class="form-control" id="nis" name="nis" value="<?= html_escape($nis_baru); ?>" readonly>
          <small class="text-muted">Dibuat otomatis oleh sistem, tidak dapat diubah.</small>
        </div>
        <div class="form-group col-md-5">
          <label for="nama">Nama Lengkap <span class="text-danger">*</span></label>
          <input type="text" class="form-control <?= form_error('nama') ? 'is-invalid' : ''; ?>" id="nama" name="nama" minlength="3" required value="<?= html_escape($santri['NamaLengkap']); ?>">
          <div class="invalid-feedback">Nama lengkap wajib diisi, minimal 3 karakter.</div>
        </div>
        <div class="form-group col-md-4">
          <label for="jenis_kelamin">Jenis Kelamin <span class="text-danger">*</span></label>
          <select class="form-control <?= form_error('jenis_kelamin') ? 'is-invalid' : ''; ?>" id="jenis_kelamin" name="jenis_kelamin" required>
            <option value="">-- Pilih Jenis Kelamin --</option>
            <?php foreach ($jenis_kelamin_list as $jk) : ?>
              <option value="<?= $jk; ?>" <?= $dipilih($jk, $santri['JenisKelamin']); ?>><?= $jk; ?></option>
            <?php endforeach; ?>
          </select>
          <div class="invalid-feedback">Jenis kelamin wajib dipilih.</div>
        </div>
      </div>

      <div class="row">
        <div class="form-group col-md-4">
          <label for="tempat_lahir">Tempat Lahir <span class="text-danger">*</span></label>
          <input type="text" class="form-control <?= form_error('tempat_lahir') ? 'is-invalid' : ''; ?>" id="tempat_lahir" name="tempat_lahir" required value="<?= html_escape($santri['TempatLahir']); ?>">
          <div class="invalid-feedback">Tempat lahir wajib diisi.</div>
        </div>
        <div class="form-group col-md-4">
          <label for="tanggal_lahir">Tanggal Lahir <span class="text-danger">*</span></label>
          <input type="date" class="form-control <?= form_error('tanggal_lahir') ? 'is-invalid' : ''; ?>" id="tanggal_lahir" name="tanggal_lahir" required value="<?= html_escape($santri['TanggalLahir']); ?>">
          <div class="invalid-feedback">Tanggal lahir wajib diisi dan tidak boleh setelah hari ini.</div>
        </div>
      </div>

      <div class="row">
        <div class="form-group col-md-4">
          <label for="kelas">Kelas <span class="text-danger">*</span></label>
          <select class="form-control <?= form_error('kelas') ? 'is-invalid' : ''; ?>" id="kelas" name="kelas" required>
            <option value="">-- Pilih Kelas --</option>
            <?php foreach ($kelas as $kls) : ?>
              <option value="<?= $kls['IdKelas']; ?>" <?= $dipilih($kls['IdKelas'], $santri['IdKelas']); ?>><?= $kls['NamaKelas']; ?><?= !empty($kls['NamaMusyrif']) ? ' - ' . $kls['NamaMusyrif'] : ''; ?></option>
            <?php endforeach; ?>
          </select>
          <div class="invalid-feedback">Kelas wajib dipilih.</div>
        </div>
        <div class="form-group col-md-4">
          <label for="email">Email untuk Login <span class="text-danger">*</span></label>
          <input type="email" class="form-control <?= form_error('email') ? 'is-invalid' : ''; ?>" id="email" name="email" required value="<?= html_escape($santri['login']['username']); ?>" <?= $readonly_email ? 'readonly' : ''; ?>>
          <?php if ($readonly_email) : ?>
            <small class="text-muted">Diambil dari akun Google yang Anda pakai untuk daftar.</small>
          <?php endif; ?>
          <div class="invalid-feedback">Email wajib diisi dengan format yang valid dan belum terdaftar.</div>
        </div>
        <div class="form-group col-md-4">
          <label for="password">Password <span class="text-danger">*</span></label>
          <input type="password" class="form-control <?= form_error('password') ? 'is-invalid' : ''; ?>" id="password" name="password" minlength="8" <?= $mode === 'tambah' ? 'required' : ''; ?>>
          <?php if ($mode === 'tambah') : ?>
            <small class="text-muted">Minimal 8 karakter.</small>
          <?php else : ?>
            <small class="text-muted">Kosongkan jika tidak ingin mengubah password. Isi minimal 8 karakter untuk mengganti.</small>
          <?php endif; ?>
          <div class="invalid-feedback">Password minimal 8 karakter.</div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Langkah 2: Data Tambahan -->
<div class="wizard-step" data-step="2" style="display:none;">
  <div class="card">
    <div class="card-header bg-secondary">
      <h4 class="m-0">Data Tambahan</h4>
    </div>
    <div class="card-body">
      <div class="row">
        <div class="form-group col-md-6">
          <label for="nama_ayah">Nama Ayah</label>
          <input type="text" class="form-control" id="nama_ayah" name="nama_ayah" value="<?= html_escape($santri['NamaAyah']); ?>">
        </div>
        <div class="form-group col-md-6">
          <label for="nama_ibu">Nama Ibu</label>
          <input type="text" class="form-control" id="nama_ibu" name="nama_ibu" value="<?= html_escape($santri['NamaIbu']); ?>">
        </div>
      </div>

      <div class="row">
        <div class="form-group col-md-4">
          <label for="pasfoto">Foto Santri <small>(jpg/jpeg/png, maks 2MB)</small></label>
          <div class="custom-file">
            <input type="file" class="custom-file-input" id="pasfoto" name="pasfoto" accept=".jpg,.jpeg,.png">
            <label class="custom-file-label" for="pasfoto">Pilih Foto</label>
          </div>
          <div id="pasfotoError" class="text-danger mt-1" style="display:none;"></div>
        </div>
        <div class="form-group col-md-2 text-center">
          <label class="d-block">Preview</label>
          <?php if (!empty($santri['Pasfoto'])) : ?>
            <img id="previewPasfoto" src="<?= upload_url('santri', $santri['Pasfoto']); ?>" width="70" height="70" class="img-circle" style="object-fit: cover;">
          <?php else : ?>
            <img id="previewPasfoto" src="" width="70" height="70" class="img-circle" style="object-fit: cover; display:none;">
          <?php endif; ?>
        </div>
        <div class="form-group col-md-6">
          <label for="alamat">Alamat Lengkap</label>
          <textarea class="form-control" id="alamat" name="alamat" rows="3"><?= html_escape($santri['Alamat']); ?></textarea>
        </div>
      </div>

      <div class="row">
        <div class="form-group col-md-6">
          <label for="sekolah_akademik">Sekolah Akademik</label>
          <input type="text" class="form-control" id="sekolah_akademik" name="sekolah_akademik" value="<?= html_escape($santri['SekolahAkademik']); ?>">
        </div>
        <div class="form-group col-md-6">
          <label for="sekolah_tadika">Sekolah Tadika</label>
          <input type="text" class="form-control" id="sekolah_tadika" name="sekolah_tadika" value="<?= html_escape($santri['SekolahTadika']); ?>">
        </div>
      </div>

      <div class="row">
        <div class="form-group col-md-6">
          <label for="no_id_card">Nomor ID Card</label>
          <input type="text" class="form-control <?= form_error('no_id_card') ? 'is-invalid' : ''; ?>" id="no_id_card" name="no_id_card" value="<?= html_escape($santri['NoIDCard']); ?>">
          <div class="invalid-feedback">Nomor ID Card sudah digunakan santri lain.</div>
        </div>
        <div class="form-group col-md-6">
          <label for="tanggal_mulai_belajar">Tanggal Mulai Belajar</label>
          <input type="date" class="form-control" id="tanggal_mulai_belajar" name="tanggal_mulai_belajar" value="<?= html_escape($santri['TglMulaiBelajar']); ?>">
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Navigasi Wizard (tombol Simpan/Daftar/Batal ada di luar partial ini, disembunyikan sampai langkah terakhir lewat #tombolSubmitFormulir) -->
<div class="d-flex flex-wrap mb-3" style="gap: 8px;">
  <button type="button" id="btnKembaliLangkah" class="btn btn-default" style="display:none;"><i class="fas fa-arrow-left"></i> Kembali</button>
  <button type="button" id="btnLanjutLangkah" class="btn btn-primary ml-auto"><i class="fas fa-arrow-right"></i> Lanjut</button>
</div>

<script>
  // DOMContentLoaded dipakai karena tombol Simpan/Daftar (#tombolSubmitFormulir) dirender
  // di file pemanggil (santri/form.php / daftar/index.php) SETELAH partial ini, jadi belum
  // ada di DOM saat script partial ini sendiri dieksekusi.
  document.addEventListener('DOMContentLoaded', function () {
    var langkahEl = document.querySelectorAll('.wizard-step');
    var labelEl = document.querySelectorAll('.wizard-step-label');
    var progressBar = document.getElementById('wizardProgressBar');
    var btnKembali = document.getElementById('btnKembaliLangkah');
    var btnLanjut = document.getElementById('btnLanjutLangkah');
    var tombolSubmit = document.getElementById('tombolSubmitFormulir');
    var totalLangkah = langkahEl.length;

    // Kalau formulir dimuat ulang karena validasi server gagal dan errornya ada di langkah 2,
    // langsung mulai dari langkah 2 supaya field yang salah langsung terlihat.
    var langkahAwal = 1;
    var langkah2 = document.querySelector('.wizard-step[data-step="2"]');
    if (langkah2 && langkah2.querySelector('.is-invalid')) {
      langkahAwal = 2;
    }

    var langkahSekarang = langkahAwal;

    function tampilkanLangkah(nomor) {
      langkahEl.forEach(function (el) {
        el.style.display = (parseInt(el.dataset.step, 10) === nomor) ? '' : 'none';
      });

      labelEl.forEach(function (el) {
        var aktif = parseInt(el.dataset.stepLabel, 10) === nomor;
        el.classList.toggle('font-weight-bold', aktif);
        el.classList.toggle('text-muted', !aktif);
      });

      if (progressBar) {
        progressBar.style.width = (nomor / totalLangkah * 100) + '%';
      }

      btnKembali.style.display = nomor > 1 ? '' : 'none';

      var langkahTerakhir = nomor >= totalLangkah;
      btnLanjut.style.display = langkahTerakhir ? 'none' : '';
      if (tombolSubmit) {
        tombolSubmit.style.display = langkahTerakhir ? '' : 'none';
      }
    }

    function validasiLangkah(nomor) {
      var el = document.querySelector('.wizard-step[data-step="' + nomor + '"]');
      if (!el) return true;

      var valid = true;
      var pertamaTidakValid = null;

      el.querySelectorAll('input, select, textarea').forEach(function (field) {
        if (!field.checkValidity()) {
          valid = false;
          if (!pertamaTidakValid) pertamaTidakValid = field;
        }
      });

      if (!valid && pertamaTidakValid) {
        pertamaTidakValid.reportValidity();
      }

      return valid;
    }

    btnLanjut.addEventListener('click', function () {
      if (!validasiLangkah(langkahSekarang)) return;
      if (langkahSekarang < totalLangkah) {
        langkahSekarang++;
        tampilkanLangkah(langkahSekarang);
      }
    });

    btnKembali.addEventListener('click', function () {
      if (langkahSekarang > 1) {
        langkahSekarang--;
        tampilkanLangkah(langkahSekarang);
      }
    });

    tampilkanLangkah(langkahSekarang);
  });
</script>
