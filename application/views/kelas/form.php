<?php
$aksi = $mode === 'tambah' ? base_url('kelas/simpan') : base_url('kelas/perbarui/' . $kelas['IdKelas']);
?>
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
  <div class="content mt-2">
    <div class="container-fluid">
      <div class="row">
        <div class="col-12">

          <?php if (validation_errors()) : ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
              <b><?= validation_errors(); ?></b>
              <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
          <?php endif; ?>

          <div class="card">
            <div class="card-header bg-success">
              <h4 class="m-0"><?= $title; ?></h4>
            </div>
            <div class="card-body">
              <?= form_open($aksi, ['id' => 'formKelas', 'novalidate' => 'novalidate']); ?>

              <div class="row">
                <div class="form-group col-md-4">
                  <label for="nama_kelas">Nama Kelas <span class="text-danger">*</span></label>
                  <select class="form-control <?= form_error('nama_kelas') ? 'is-invalid' : ''; ?>" id="nama_kelas" name="nama_kelas" required>
                    <option value="">-- Pilih Nama Kelas --</option>
                    <?php foreach ($nama_kelas_list as $nk) : ?>
                      <option value="<?= $nk; ?>" <?= (string) $nk === (string) $kelas['NamaKelas'] ? 'selected' : ''; ?>><?= $nk; ?></option>
                    <?php endforeach; ?>
                  </select>
                  <div class="invalid-feedback">Nama kelas wajib dipilih.</div>
                </div>
                <div class="form-group col-md-4">
                  <label for="pembimbing">Pembimbing <span class="text-danger">*</span></label>
                  <select class="form-control <?= form_error('pembimbing') ? 'is-invalid' : ''; ?>" id="pembimbing" name="pembimbing" required>
                    <option value="">-- Pilih Pembimbing --</option>
                    <?php foreach ($musyrif as $m) : ?>
                      <option value="<?= $m['IdMusyrif']; ?>" <?= (string) $m['IdMusyrif'] === (string) $kelas['IdMusyrif'] ? 'selected' : ''; ?>><?= html_escape($m['NamaMusyrif']); ?></option>
                    <?php endforeach; ?>
                  </select>
                  <div class="invalid-feedback">Pembimbing wajib dipilih.</div>
                  <?php if (empty($musyrif)) : ?>
                    <small class="text-danger">Belum ada data pembimbing. Tambahkan data Pembimbing terlebih dahulu.</small>
                  <?php endif; ?>
                </div>
                <div class="form-group col-md-4">
                  <label for="jumlah_santri">Jumlah Santri</label>
                  <input type="text" class="form-control" id="jumlah_santri" value="<?= $kelas['JumlahSantri']; ?>" readonly>
                  <small class="text-muted">Dihitung otomatis dari data santri, tidak dapat diubah manual.</small>
                </div>
              </div>

              <div class="row">
                <div class="form-group col-md-6">
                  <label for="lokasi">Lokasi/Ruangan</label>
                  <input type="text" class="form-control" id="lokasi" name="lokasi" placeholder="Mis. Ruang Tahsin, Aula Utama" value="<?= html_escape($kelas['Lokasi']); ?>">
                </div>
              </div>

              <div class="mt-3">
                <button type="submit" name="aksi" value="simpan" class="btn btn-primary"><i class="fas fa-save"></i> Simpan</button>
                <?php if ($mode === 'tambah') : ?>
                  <button type="submit" name="aksi" value="tambah_baru" class="btn btn-success"><i class="fas fa-plus"></i> Simpan & Tambah Baru</button>
                <?php endif; ?>
                <a href="<?= base_url('kelas'); ?>" class="btn btn-default"><i class="fas fa-times"></i> Batal</a>
              </div>

              <?= form_close(); ?>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<script type="text/javascript">
  (function () {
    var form = document.getElementById('formKelas');
    form.addEventListener('submit', function (e) {
      if (!form.checkValidity()) {
        e.preventDefault();
        e.stopPropagation();
      }
      form.classList.add('was-validated');
    });
  })();
</script>
