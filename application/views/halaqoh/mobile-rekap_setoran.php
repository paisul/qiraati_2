<?php
$dikelompokkan = [];
foreach ($rekap_setoran as $rs) {
  $dikelompokkan[$rs['IdKelas']]['NamaKelas'] = $rs['NamaKelas'];
  $dikelompokkan[$rs['IdKelas']]['santri'][] = $rs;
}
?>
<div class="m-content">
  <p class="m-page-title">Rekap Setoran</p>

  <div class="m-card">
    <a href="<?= base_url('halaqoh/rekap_setoran/form_add'); ?>" class="m-btn"><i class="fas fa-plus"></i> Proses Setoran</a>
    <a href="<?= base_url('halaqoh/rekap_setoran/export_excel'); ?>" target="_blank" class="m-btn m-btn-outline mt-2"><i class="fas fa-file-excel"></i> Export Data</a>
    <a href="<?= base_url('halaqoh/rekap_setoran/reset_data'); ?>" class="m-btn mt-2 tombol-reset" tipeData="Rekap Setoran" style="background:#ffc107; color:#212529;"><i class="fas fa-ban"></i> Reset Data</a>

    <form class="mt-2" action="<?= base_url('halaqoh/Rekap_Setoran/cari_data'); ?>" method="POST">
      <div class="m-field">
        <label>Cari Nama Santri</label>
        <input type="text" name="nama_santri" placeholder="Masukkan Nama Santri">
      </div>
      <button type="submit" class="m-btn m-btn-outline"><i class="fas fa-search"></i> Cari</button>
    </form>
  </div>

  <?php if ($dikelompokkan) : ?>
    <?php foreach ($dikelompokkan as $idKelas => $grup) : ?>
      <div class="m-card">
        <div class="m-list-item">
          <div class="m-list-title"><?= html_escape($grup['NamaKelas']); ?></div>
          <a href="<?= base_url('halaqoh/Rekap_setoran/delete/' . $idKelas); ?>" class="m-dana-btn-hapus tombol-hapus" tipeData="Kelas" namaData="<?= html_escape($grup['NamaKelas']); ?>">Hapus</a>
        </div>
        <?php foreach ($grup['santri'] as $rs) : ?>
          <div class="m-list-item">
            <div>
              <div class="m-list-title"><?= html_escape($rs['NamaLengkap']); ?></div>
              <div class="m-list-sub">Pekan <?= html_escape($rs['PekanRekap']); ?> &middot; <?= html_escape($rs['Prosentase']); ?>% &middot; <?= html_escape($rs['Hasil']); ?></div>
              <div class="m-list-sub">Tugas: <?= (int) $rs['JmlTugas']; ?> &middot; Setoran: <?= (int) $rs['JmlSetoran']; ?> &middot; Reward: <?= html_escape($rs['Reward']); ?></div>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    <?php endforeach; ?>
  <?php else : ?>
    <div class="m-card">
      <p class="m-empty">Belum ada data rekap setoran.</p>
    </div>
  <?php endif; ?>
</div>
