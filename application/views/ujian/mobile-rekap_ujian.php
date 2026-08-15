<div class="m-content">
  <p class="m-page-title">Rekap Ujian</p>

  <div class="m-card">
    <a href="<?= base_url('ujian/rekap_ujian/form_add'); ?>" class="m-btn"><i class="fas fa-plus"></i> Tambah Data</a>
    <a href="<?= base_url('ujian/rekap_ujian/export_excel'); ?>" target="_blank" class="m-btn m-btn-outline mt-2"><i class="fas fa-file-excel"></i> Export Data</a>
    <a href="<?= base_url('ujian/rekap_ujian/reset_data'); ?>" class="m-btn mt-2 tombol-reset" tipeData="Rekap Ujian" style="background:#ffc107; color:#212529;"><i class="fas fa-ban"></i> Reset Data</a>

    <form class="mt-2" action="<?= base_url('ujian/Rekap_Ujian/cari_data'); ?>" method="POST">
      <div class="m-field">
        <label>Cari Nama Santri</label>
        <input type="text" name="nama_santri" placeholder="Masukkan Nama Santri">
      </div>
      <button type="submit" class="m-btn m-btn-outline"><i class="fas fa-search"></i> Cari</button>
    </form>
  </div>

  <div class="m-card">
    <?php if ($rekap_ujian) : ?>
      <?php foreach ($rekap_ujian as $ru) : ?>
        <div class="m-dana-item-wrapper">
          <div class="m-list-item">
            <div>
              <div class="m-list-title"><?= html_escape($ru['NamaLengkap']); ?></div>
              <div class="m-list-sub"><?= html_escape($ru['NamaUjian']); ?> &middot; <?= html_escape($ru['Keterangan']); ?></div>
              <div class="m-list-sub"><?= html_escape($ru['Periode'] . ' | ' . $ru['NamaKelas']); ?></div>
              <div class="m-list-sub">Nilai: <?= html_escape($ru['Nilai']); ?> &middot; <?= html_escape($ru['Predikat']); ?> &middot; <b><?= html_escape($ru['ket_rekap']); ?></b></div>
            </div>
          </div>

          <div class="m-dana-actions" style="display:flex;">
            <a href="<?= base_url('ujian/rekap_ujian/form_update/' . $ru['IdUjian']); ?>" class="m-dana-btn-ubah">Ubah</a>
            <a href="<?= base_url('ujian/rekap_ujian/delete/' . $ru['IdUjian']); ?>" class="m-dana-btn-hapus tombol-hapus" tipeData="Rekap Ujian" namaData="<?= html_escape($ru['NamaLengkap']); ?>">Hapus</a>
          </div>
        </div>
      <?php endforeach; ?>
    <?php else : ?>
      <p class="m-empty">Belum ada data rekap ujian.</p>
    <?php endif; ?>
  </div>
</div>
