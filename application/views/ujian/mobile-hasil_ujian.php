<div class="m-content">
  <p class="m-page-title">Hasil Ujian</p>

  <div class="m-card">
    <a href="<?= base_url('ujian/hasil_ujian/form_add'); ?>" class="m-btn"><i class="fas fa-user"></i> Proses Nilai (Individu)</a>
    <a href="<?= base_url('ujian/hasil_ujian/form_add_banyak'); ?>" class="m-btn mt-2"><i class="fas fa-users"></i> Proses Nilai (Kelas)</a>
    <a href="<?= base_url('ujian/hasil_ujian/perankingan'); ?>" class="m-btn mt-2"><i class="fas fa-sort-numeric-down"></i> Perankingan</a>
    <a href="<?= base_url('ujian/hasil_ujian/export_excel'); ?>" target="_blank" class="m-btn m-btn-outline mt-2"><i class="fas fa-file-excel"></i> Export Data</a>
    <a href="<?= base_url('ujian/hasil_ujian/reset_hasilujian'); ?>" class="m-btn mt-2 tombol-reset" tipeData="Hasil Ujian" style="background:#ffc107; color:#212529;"><i class="fas fa-ban"></i> Reset Data</a>

    <form class="mt-2" action="<?= base_url('ujian/Hasil_Ujian/cari_data'); ?>" method="POST">
      <div class="m-field">
        <label>Cari Nama Santri</label>
        <input type="text" name="nama_santri" placeholder="Masukkan Nama Santri">
      </div>
      <button type="submit" class="m-btn m-btn-outline"><i class="fas fa-search"></i> Cari</button>
    </form>
  </div>

  <div class="m-card">
    <?php if ($hasil_ujian) : ?>
      <?php foreach ($hasil_ujian as $hu) : ?>
        <div class="m-dana-item-wrapper">
          <div class="m-list-item">
            <div>
              <div class="m-list-title"><?= html_escape($hu['NamaLengkap']); ?></div>
              <div class="m-list-sub"><?= html_escape($hu['NamaKelas']); ?> &middot; <?= html_escape($hu['periode']); ?></div>
              <div class="m-list-sub">Total: <?= html_escape($hu['Total']); ?> &middot; Rata-rata: <?= round($hu['Rata-rata'], 1); ?> &middot; <?= html_escape($hu['Reward']); ?></div>
            </div>
            <span class="m-badge m-badge-selesai">Rangking <?= html_escape($hu['Rangking']); ?></span>
          </div>

          <div class="m-dana-actions" style="display:flex;">
            <a href="<?= base_url('ujian/hasil_ujian/form_update/' . $hu['IdHasil']); ?>" class="m-dana-btn-ubah">Ubah</a>
            <a href="<?= base_url('ujian/hasil_ujian/delete/' . $hu['IdHasil']); ?>" class="m-dana-btn-hapus tombol-hapus" tipeData="Hasil Ujian" namaData="<?= html_escape($hu['NamaLengkap']); ?>">Hapus</a>
          </div>
        </div>
      <?php endforeach; ?>
    <?php else : ?>
      <p class="m-empty">Belum ada data hasil ujian.</p>
    <?php endif; ?>
  </div>
</div>
