<?php if (!empty($user['daftar_anak']) && count($user['daftar_anak']) > 1) : ?>
  <div class="m-anak-switcher">
    <?php foreach ($user['daftar_anak'] as $anak) : ?>
      <a href="<?= base_url('Wali/pilih_anak/' . $anak['IdSiswa']); ?>" class="m-anak-chip <?= $anak['IdSiswa'] == $user['IdSiswa'] ? 'active' : ''; ?>">
        <?php if (!empty($anak['Pasfoto'])) : ?>
          <img src="<?= upload_url('santri', $anak['Pasfoto']); ?>" alt="<?= html_escape($anak['NamaLengkap']); ?>">
        <?php else : ?>
          <div class="m-anak-avatar-fallback"><?= strtoupper(substr($anak['NamaLengkap'], 0, 1)); ?></div>
        <?php endif; ?>
        <span><?= html_escape($anak['NamaLengkap']); ?></span>
      </a>
    <?php endforeach; ?>
  </div>
<?php endif; ?>
