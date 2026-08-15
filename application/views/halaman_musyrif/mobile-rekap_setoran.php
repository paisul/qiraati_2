<div class="m-content">
  <p class="m-page-title">Rekap Setoran</p>

  <div class="m-card">
    <a href="<?= base_url('Halaman_Musyrif/form_add_rekap_setoran'); ?>" class="m-btn"><i class="fas fa-plus"></i> Proses Setoran</a>
  </div>

  <div class="m-card">
    <?php if ($rekap_setoran) : ?>
      <?php foreach ($rekap_setoran as $rs) : ?>
        <div class="m-list-item">
          <div>
            <div class="m-list-title"><?= html_escape($rs['NamaLengkap']); ?></div>
            <div class="m-list-sub">Pekan <?= html_escape($rs['PekanRekap']); ?> &middot; <?= html_escape($rs['Prosentase']); ?>% &middot; <?= html_escape($rs['Hasil']); ?></div>
            <div class="m-list-sub">Tugas: <?= (int) $rs['JmlTugas']; ?> &middot; Setoran: <?= (int) $rs['JmlSetoran']; ?> &middot; Reward: <?= html_escape($rs['Reward']); ?></div>
          </div>
        </div>
      <?php endforeach; ?>
    <?php else : ?>
      <p class="m-empty">Belum ada data rekap setoran.</p>
    <?php endif; ?>
  </div>
</div>
