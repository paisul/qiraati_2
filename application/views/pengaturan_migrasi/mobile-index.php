<div class="m-content">
  <p class="m-page-title">Backup & Migrasi Database</p>

  <div class="m-card">
    <p class="m-list-sub">
      Gunakan ini saat pindah ke server/hosting lain, atau untuk backup rutin. Export mengunduh seluruh
      database (<?= $jumlah_tabel; ?> tabel) jadi satu file .sql. File foto/tanda tangan/logo di folder
      <code>qiroati_uploads/</code> (di luar folder aplikasi) perlu dipindahkan terpisah (bukan lewat file .sql ini).
    </p>
  </div>

  <div class="m-card">
    <p class="m-card-title"><i class="fas fa-download"></i> Export Database</p>
    <p class="m-list-sub">Unduh salinan database saat ini sebagai file .sql. Aman, tidak mengubah data apa pun.</p>
    <a href="<?= base_url('pengaturanmigrasi/export'); ?>" class="m-btn"><i class="fas fa-download"></i> Unduh Backup .sql</a>
  </div>

  <div class="m-card">
    <p class="m-card-title"><i class="fas fa-upload"></i> Import Database</p>
    <p class="m-list-sub" style="color:#dc3545;"><strong>Peringatan:</strong> Import akan MENIMPA seluruh tabel yang namanya sama dengan yang ada di file .sql. Data yang sudah ada bisa hilang/tertimpa dan tidak bisa dibatalkan. Pastikan sudah export backup dulu sebelum lanjut.</p>
    <form id="formImportMigrasi" action="<?= base_url('pengaturanmigrasi/import'); ?>" method="post" enctype="multipart/form-data">
      <div class="m-field">
        <label>Pilih file .sql</label>
        <input type="file" id="file_sql_mobile" name="file_sql" accept=".sql" required>
      </div>
      <button type="submit" class="m-btn" style="background:#dc3545;"><i class="fas fa-exclamation-triangle"></i> Import & Timpa Database</button>
    </form>
  </div>
</div>

<script>
  document.getElementById('formImportMigrasi').addEventListener('submit', function (e) {
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
