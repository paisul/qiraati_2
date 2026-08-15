<?php
$tampil = function ($val) {
  return ($val === null || $val === '') ? '-' : html_escape($val);
};
$inisial = strtoupper(substr($santri['NamaLengkap'] ?? '?', 0, 1));
$ttl = trim(($santri['TempatLahir'] ?? '') . ($santri['TempatLahir'] && $santri['TanggalLahir'] ? ', ' : '') . ($santri['TanggalLahir'] ? date('d F Y', strtotime($santri['TanggalLahir'])) : ''));
?>
<div class="m-content">
  <p class="m-page-title">Profil Saya</p>

  <div class="m-card m-profil-hero">
    <a href="<?= base_url('Wali/edit_profil'); ?>" class="m-profil-hero-edit" aria-label="Ubah Profil"><i class="fas fa-pen"></i></a>
    <?php if (!empty($santri['Pasfoto'])) : ?>
      <img src="<?= upload_url('santri', $santri['Pasfoto']); ?>" alt="Pasfoto" data-fallback-class="m-profil-hero-fallback" data-fallback-text="<?= html_escape($inisial); ?>" onerror="mFotoGagalMuat(this)">
    <?php else : ?>
      <div class="m-profil-hero-fallback"><?= $inisial; ?></div>
    <?php endif; ?>
    <div class="m-profil-hero-nama"><?= $tampil($santri['NamaLengkap'] ?? null); ?></div>
    <div class="m-profil-hero-sub"><?= html_escape($user['username']); ?></div>
    <span class="m-profil-hero-peran">Wali Santri</span>
  </div>

  <div class="m-card">
    <p class="m-profil-section-title"><i class="fas fa-user-graduate"></i> Data Santri <span class="m-profil-readonly-note">hanya admin bisa ubah</span></p>
    <div class="m-list-item"><span>NIS</span><strong><?= $tampil($santri['NIS'] ?? null); ?></strong></div>
    <div class="m-list-item"><span>Jenis Kelamin</span><strong><?= $tampil($santri['JenisKelamin'] ?? null); ?></strong></div>
    <div class="m-list-item"><span>Kelas</span><strong><?= $tampil($santri['NamaKelas'] ?? null); ?></strong></div>
    <div class="m-list-item"><span>Tempat, Tanggal Lahir</span><strong><?= $ttl !== '' ? html_escape($ttl) : '-'; ?></strong></div>
  </div>

  <div class="m-card">
    <p class="m-profil-section-title"><i class="fas fa-home"></i> Data Tambahan</p>
    <div class="m-list-item"><span>Nama Ayah</span><strong><?= $tampil($santri['NamaAyah'] ?? null); ?></strong></div>
    <div class="m-list-item"><span>Nama Ibu</span><strong><?= $tampil($santri['NamaIbu'] ?? null); ?></strong></div>
    <div class="m-list-item"><span>Alamat</span><strong><?= $tampil($santri['Alamat'] ?? null); ?></strong></div>
    <div class="m-list-item"><span>Sekolah Akademik</span><strong><?= $tampil($santri['SekolahAkademik'] ?? null); ?></strong></div>
    <div class="m-list-item"><span>Sekolah Tadika</span><strong><?= $tampil($santri['SekolahTadika'] ?? null); ?></strong></div>
    <div class="m-list-item"><span>Nomor ID Card</span><strong><?= $tampil($santri['NoIDCard'] ?? null); ?></strong></div>
    <div class="m-list-item"><span>Tanggal Mulai Belajar</span><strong><?= !empty($santri['TglMulaiBelajar']) ? date('d F Y', strtotime($santri['TglMulaiBelajar'])) : '-'; ?></strong></div>
  </div>

  <div class="m-card">
    <p class="m-profil-section-title"><i class="fas fa-key"></i> Akun Login</p>
    <div class="m-list-item"><span>Email</span><strong><?= html_escape($user['username']); ?></strong></div>
  </div>

  <a href="<?= base_url('Wali/edit_profil'); ?>" class="m-btn"><i class="fas fa-pen"></i> Ubah Profil</a>
</div>

