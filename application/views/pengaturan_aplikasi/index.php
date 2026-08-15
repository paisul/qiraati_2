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

            <div class="flash-data" data-flashdata="<?= isset($pesan) ? $pesan : $this->session->flashdata('pesan'); ?>" data-title="Pengaturan Aplikasi">
            </div>

            <div class="card-body">
              <?= form_open_multipart('pengaturanaplikasi/simpan'); ?>

              <div class="row">
                <div class="form-group col-md-6">
                  <label for="nama_aplikasi">Nama Aplikasi</label>
                  <input type="text" class="form-control" id="nama_aplikasi" name="nama_aplikasi" value="<?= html_escape($pengaturan['NamaAplikasi']); ?>" required>
                  <small class="text-muted">Tampil di sidebar, judul halaman, dan footer di semua level pengguna.</small>
                </div>

                <div class="form-group col-md-6">
                  <label for="logo">Logo Aplikasi <small>(jpg/jpeg/png, maks 2MB)</small></label>
                  <div class="mb-2">
                    <img id="previewLogo" src="<?= base_url('assets/' . $pengaturan['Logo']); ?>" width="80" height="80" style="object-fit: contain;">
                  </div>
                  <div class="custom-file">
                    <input type="file" class="custom-file-input" id="logo" name="logo" accept=".jpg,.jpeg,.png" onchange="previewLogoBaru(this)">
                    <label class="custom-file-label" for="logo">Ganti Logo</label>
                  </div>
                </div>
              </div>

              <div class="mt-3">
                <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Simpan</button>
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
  function previewLogoBaru(input) {
    if (!input.files || !input.files[0]) return;

    input.closest('.custom-file').querySelector('.custom-file-label').textContent = input.files[0].name;

    var reader = new FileReader();
    reader.onload = function (e) {
      document.getElementById('previewLogo').src = e.target.result;
    };
    reader.readAsDataURL(input.files[0]);
  }
</script>
