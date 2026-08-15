<?php
$tampil = function ($val) {
  return ($val === null || $val === '') ? '-' : html_escape($val);
};
$warna_status = ['Aktif' => 'm-badge-selesai', 'Lulus' => '', 'Pindah' => '', 'Berhenti' => 'm-badge-belum'];
$kelas_badge = isset($warna_status[$santri['Status']]) && $warna_status[$santri['Status']] ? $warna_status[$santri['Status']] : 'm-badge-secondary';
?>
<div class="m-content">
  <div class="m-card m-profil-anak">
    <?php if (!empty($santri['Pasfoto'])) : ?>
      <img src="<?= upload_url('santri', $santri['Pasfoto']); ?>" alt="<?= html_escape($santri['NamaLengkap']); ?>" data-fallback-class="m-profil-anak-fallback" data-fallback-text="<?= html_escape(strtoupper(substr($santri['NamaLengkap'], 0, 1))); ?>" onerror="mFotoGagalMuat(this)">
    <?php else : ?>
      <div class="m-profil-anak-fallback"><?= strtoupper(substr($santri['NamaLengkap'], 0, 1)); ?></div>
    <?php endif; ?>
    <div>
      <div class="m-profil-anak-nama"><?= html_escape($santri['NamaLengkap']); ?></div>
      <div class="m-profil-anak-sub">NIS <?= html_escape($santri['NIS']); ?></div>
      <span class="m-badge <?= $kelas_badge; ?>" style="<?= $kelas_badge === 'm-badge-secondary' ? 'background:#6c757d;' : ''; ?>"><?= html_escape($santri['Status']); ?></span>
    </div>
  </div>

  <div class="m-card">
    <p class="m-card-title" style="margin-bottom: 8px;">Data Wajib</p>
    <div class="m-list-item"><span>Jenis Kelamin</span><strong><?= $tampil($santri['JenisKelamin']); ?></strong></div>
    <div class="m-list-item"><span>Tempat Lahir</span><strong><?= $tampil($santri['TempatLahir']); ?></strong></div>
    <div class="m-list-item"><span>Tanggal Lahir</span><strong><?= $tampil($santri['TanggalLahir']); ?></strong></div>
    <div class="m-list-item"><span>Kelas</span><strong><?= $tampil(isset($santri['NamaKelas']) ? $santri['NamaKelas'] : null); ?></strong></div>
    <div class="m-list-item"><span>Email Login</span><strong><?= $tampil($santri['login']['username'] ?? null); ?></strong></div>
  </div>

  <div class="m-card">
    <p class="m-card-title" style="margin-bottom: 8px;">Data Opsional</p>
    <div class="m-list-item"><span>Nama Ayah</span><strong><?= $tampil($santri['NamaAyah']); ?></strong></div>
    <div class="m-list-item"><span>Nama Ibu</span><strong><?= $tampil($santri['NamaIbu']); ?></strong></div>
    <div class="m-list-item"><span>Nomor ID Card</span><strong><?= $tampil($santri['NoIDCard']); ?></strong></div>
    <div class="m-list-item"><span>Alamat</span><strong style="text-align:right; max-width:60%;"><?= nl2br($tampil($santri['Alamat'])); ?></strong></div>
    <div class="m-list-item"><span>Sekolah Akademik</span><strong><?= $tampil($santri['SekolahAkademik']); ?></strong></div>
    <div class="m-list-item"><span>Sekolah Tadika</span><strong><?= $tampil($santri['SekolahTadika']); ?></strong></div>
    <div class="m-list-item"><span>Tanggal Mulai Belajar</span><strong><?= $tampil($santri['TglMulaiBelajar']); ?></strong></div>
  </div>

  <a href="<?= base_url('santri/ubah/' . $santri['IdSiswa']); ?>" class="m-btn"><i class="fas fa-pen"></i> Ubah</a>
  <a href="<?= base_url('santri'); ?>" class="m-btn m-btn-outline mt-2"><i class="fas fa-arrow-left"></i> Kembali</a>
</div>
