<?php
$tampil = function ($val) {
  return ($val === null || $val === '') ? '-' : html_escape($val);
};
?>
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
  <div class="content mt-2">
    <div class="container-fluid">
      <div class="row">
        <div class="col-sm">
          <div class="card">
            <div class="card-header bg-success">
              <h4 class="m-0"><?= $title; ?></h4>
            </div>

            <div class="flash-data" data-flashdata="<?= isset($pesan) ? $pesan : $this->session->flashdata('pesan'); ?>" data-title="Profil Saya">
            </div>

            <div class="card-body">
              <?php if (validation_errors()) : ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                  <b><?= validation_errors(); ?></b>
                  <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                  </button>
                </div>
              <?php endif; ?>

              <h5 class="border-bottom pb-2">Data Santri <small class="text-muted">(hanya admin yang bisa mengubah bagian ini)</small></h5>
              <div class="row mb-4">
                <div class="col-md-3 mb-2"><strong>Nama Lengkap</strong><br><?= $tampil($santri['NamaLengkap']); ?></div>
                <div class="col-md-3 mb-2"><strong>NIS</strong><br><?= $tampil($santri['NIS']); ?></div>
                <div class="col-md-3 mb-2"><strong>Jenis Kelamin</strong><br><?= $tampil($santri['JenisKelamin']); ?></div>
                <div class="col-md-3 mb-2"><strong>Kelas</strong><br><?= $tampil(isset($santri['NamaKelas']) ? $santri['NamaKelas'] : null); ?></div>
                <div class="col-md-3 mb-2"><strong>Tempat Lahir</strong><br><?= $tampil($santri['TempatLahir']); ?></div>
                <div class="col-md-3 mb-2"><strong>Tanggal Lahir</strong><br><?= $tampil($santri['TanggalLahir']); ?></div>
              </div>

              <h5 class="border-bottom pb-2">Ubah Profil</h5>
              <?= form_open_multipart('Wali/simpan_profil'); ?>

              <div class="row">
                <div class="form-group col-md-6">
                  <label for="email">Email Login <span class="text-danger">*</span></label>
                  <input type="email" class="form-control <?= form_error('email') ? 'is-invalid' : ''; ?>" id="email" name="email" value="<?= html_escape($user['username']); ?>" required>
                  <div class="invalid-feedback">Email wajib diisi dengan format yang valid dan belum terdaftar.</div>
                </div>
                <div class="form-group col-md-6">
                  <label for="password">Password Baru</label>
                  <input type="password" class="form-control <?= form_error('password') ? 'is-invalid' : ''; ?>" id="password" name="password" minlength="8">
                  <small class="text-muted">Kosongkan jika tidak ingin mengubah password. Isi minimal 8 karakter untuk mengganti.</small>
                </div>
              </div>

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
                  <label for="pasfoto">Pasfoto Santri <small>(jpg/jpeg/png, maks 2MB)</small></label>
                  <div class="mb-2">
                    <?php if (!empty($santri['Pasfoto'])) : ?>
                      <img src="<?= upload_url('santri', $santri['Pasfoto']); ?>" width="70" height="70" class="img-circle" style="object-fit: cover;">
                    <?php else : ?>
                      <div style="width:70px;height:70px;border-radius:50%;background:#172a3a;color:#fff;display:inline-flex;align-items:center;justify-content:center;font-weight:bold;font-size:24px;">
                        <?= strtoupper(substr($santri['NamaLengkap'], 0, 1)); ?>
                      </div>
                    <?php endif; ?>
                  </div>
                  <div class="custom-file">
                    <input type="file" class="custom-file-input" id="pasfoto" name="pasfoto" accept=".jpg,.jpeg,.png">
                    <label class="custom-file-label" for="pasfoto">Ganti Foto</label>
                  </div>
                </div>
                <div class="form-group col-md-4">
                  <label for="alamat">Alamat Lengkap</label>
                  <textarea class="form-control" id="alamat" name="alamat" rows="3"><?= html_escape($santri['Alamat']); ?></textarea>
                </div>
                <div class="form-group col-md-4">
                  <label for="no_id_card">Nomor ID Card</label>
                  <input type="text" class="form-control <?= form_error('no_id_card') ? 'is-invalid' : ''; ?>" id="no_id_card" name="no_id_card" value="<?= html_escape($santri['NoIDCard']); ?>">
                  <div class="invalid-feedback">Nomor ID Card sudah digunakan santri lain.</div>
                </div>
              </div>

              <div class="row">
                <div class="form-group col-md-4">
                  <label for="sekolah_akademik">Sekolah Akademik</label>
                  <input type="text" class="form-control" id="sekolah_akademik" name="sekolah_akademik" value="<?= html_escape($santri['SekolahAkademik']); ?>">
                </div>
                <div class="form-group col-md-4">
                  <label for="sekolah_tadika">Sekolah Tadika</label>
                  <input type="text" class="form-control" id="sekolah_tadika" name="sekolah_tadika" value="<?= html_escape($santri['SekolahTadika']); ?>">
                </div>
                <div class="form-group col-md-4">
                  <label for="tanggal_mulai_belajar">Tanggal Mulai Belajar</label>
                  <input type="date" class="form-control" id="tanggal_mulai_belajar" name="tanggal_mulai_belajar" value="<?= html_escape($santri['TglMulaiBelajar']); ?>">
                </div>
              </div>

              <div class="mt-3">
                <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Simpan Perubahan</button>
              </div>

              <?= form_close(); ?>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
  document.querySelectorAll('.custom-file-input').forEach(function (input) {
    input.addEventListener('change', function () {
      if (!input.files || !input.files[0]) return;
      input.closest('.custom-file').querySelector('.custom-file-label').textContent = input.files[0].name;
    });
  });
</script>
