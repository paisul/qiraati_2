<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
  <div class="content mt-2">
    <div class="container-fluid">
      <div class="row">
        <div class="col-12">
          <div class="card">
            <div class="card-header bg-success">
              <h4 class="m-0"><?= $title; ?></h4>
            </div>

            <div class="flash-data" data-flashdata="<?= isset($pesan) ? $pesan : $this->session->flashdata('pesan'); ?>" data-title="Backup & Migrasi Database">
            </div>

            <div class="card-body">
              <p class="text-muted">
                Gunakan ini saat pindah ke server/hosting lain, atau untuk backup rutin. Export mengunduh seluruh
                database (<?= $jumlah_tabel; ?> tabel) jadi satu file .sql. File foto/tanda tangan/logo di folder
                <code>qiroati_uploads/</code> (di luar folder aplikasi) perlu dipindahkan terpisah (bukan lewat file .sql ini).
              </p>

              <div class="row">
                <div class="col-md-6 mb-3">
                  <div class="card h-100">
                    <div class="card-header bg-primary">
                      <h5 class="m-0"><i class="fas fa-download"></i> Export Database</h5>
                    </div>
                    <div class="card-body d-flex flex-column">
                      <p>Unduh salinan database saat ini sebagai file .sql. Aman, tidak mengubah data apa pun.</p>
                      <a href="<?= base_url('pengaturanmigrasi/export'); ?>" class="btn btn-primary mt-auto"><i class="fas fa-download"></i> Unduh Backup .sql</a>
                    </div>
                  </div>
                </div>

                <div class="col-md-6 mb-3">
                  <div class="card h-100 border-danger">
                    <div class="card-header bg-danger text-white">
                      <h5 class="m-0"><i class="fas fa-upload"></i> Import Database</h5>
                    </div>
                    <div class="card-body d-flex flex-column">
                      <p class="text-danger"><strong>Peringatan:</strong> Import akan MENIMPA seluruh tabel yang namanya sama dengan yang ada di file .sql. Data yang sudah ada bisa hilang/tertimpa dan tidak bisa dibatalkan. Pastikan sudah export backup dulu sebelum lanjut.</p>
                      <form id="formImport" action="<?= base_url('pengaturanmigrasi/import'); ?>" method="post" enctype="multipart/form-data" class="mt-auto">
                        <div class="custom-file mb-3">
                          <input type="file" class="custom-file-input" id="file_sql" name="file_sql" accept=".sql" required>
                          <label class="custom-file-label" for="file_sql">Pilih file .sql</label>
                        </div>
                        <button type="submit" class="btn btn-danger btn-block"><i class="fas fa-exclamation-triangle"></i> Import & Timpa Database</button>
                      </form>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
  document.getElementById('file_sql').addEventListener('change', function () {
    if (!this.files || !this.files[0]) return;
    this.closest('.custom-file').querySelector('.custom-file-label').textContent = this.files[0].name;
  });

  document.getElementById('formImport').addEventListener('submit', function (e) {
    e.preventDefault();
    var form = this;

    Swal.fire({
      icon: 'warning',
      title: 'Timpa seluruh database?',
      html: 'Semua tabel yang namanya sama dengan isi file .sql akan <strong>ditimpa permanen</strong> dan tidak bisa dibatalkan.<br>Pastikan Anda sudah mengunduh backup terbaru sebelum lanjut.',
      showCancelButton: true,
      confirmButtonColor: '#d33',
      cancelButtonColor: '#3085d6',
      confirmButtonText: 'Ya, Timpa Database',
      cancelButtonText: 'Batal'
    }).then(function (hasil) {
      if (hasil.isConfirmed) {
        form.submit();
      }
    });
  });
</script>
