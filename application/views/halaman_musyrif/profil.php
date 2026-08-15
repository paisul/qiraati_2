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

              <?php if (empty($user['IdMusyrif'])) : ?>
                <div class="alert alert-warning">Profil pembimbing Anda belum tertaut. Silakan hubungi admin.</div>
              <?php else : ?>
                <?= form_open_multipart('Halaman_Musyrif/simpan_profil'); ?>

                <div class="row">
                  <div class="form-group col-md-6">
                    <label for="nama_musyrif">Nama Lengkap <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="nama_musyrif" name="nama_musyrif" value="<?= html_escape($user['NamaMusyrif']); ?>" required>
                  </div>
                  <div class="form-group col-md-6">
                    <label>Email <small class="text-muted">(hubungi admin untuk mengubah)</small></label>
                    <input type="text" class="form-control" value="<?= html_escape($user['Email']); ?>" readonly>
                  </div>
                </div>

                <div class="row">
                  <div class="form-group col-md-6">
                    <label for="no_hp">No Handphone <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="no_hp" name="no_hp" value="<?= html_escape($user['NoHp']); ?>" required>
                  </div>
                  <div class="form-group col-md-6">
                    <label for="password">Password Baru</label>
                    <input type="password" class="form-control" id="password" name="password" minlength="4">
                    <small class="text-muted">Kosongkan jika tidak ingin mengubah password.</small>
                  </div>
                </div>

                <div class="row">
                  <div class="form-group col-md-3 text-center">
                    <label class="d-block">Pasfoto</label>
                    <?php if (!empty($user['Pasfoto'])) : ?>
                      <img src="<?= upload_url('pasfoto_musyrif', $user['Pasfoto']); ?>" width="80" height="80" class="img-circle" style="object-fit: cover;">
                    <?php else : ?>
                      <div style="width:80px;height:80px;border-radius:50%;background:#172a3a;color:#fff;display:inline-flex;align-items:center;justify-content:center;font-weight:bold;font-size:24px;">
                        <?= strtoupper(substr($user['NamaMusyrif'], 0, 1)); ?>
                      </div>
                    <?php endif; ?>
                    <div class="custom-file mt-2">
                      <input type="file" class="custom-file-input" id="pasfoto" name="pasfoto" accept=".jpg,.jpeg,.png">
                      <label class="custom-file-label" for="pasfoto">Ganti Foto</label>
                    </div>
                  </div>
                  <div class="form-group col-md-3 text-center">
                    <label class="d-block">Tanda Tangan</label>
                    <?php if (!empty($user['Ttd'])) : ?>
                      <img src="<?= upload_url('ttd_musyrif', $user['Ttd']); ?>" width="80" height="80" style="object-fit: contain;">
                    <?php else : ?>
                      <span class="text-muted d-block">Belum ada</span>
                    <?php endif; ?>
                    <div class="custom-file mt-2">
                      <input type="file" class="custom-file-input" id="ttd" name="ttd" accept=".jpg,.jpeg,.png">
                      <label class="custom-file-label" for="ttd">Ganti Tanda Tangan</label>
                    </div>
                  </div>
                </div>

                <div class="mt-3">
                  <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Simpan Perubahan</button>
                </div>

                <?= form_close(); ?>
              <?php endif; ?>
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
