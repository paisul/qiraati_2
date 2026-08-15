<?php
if (!class_exists('CI_Model', false)) {
  require_once BASEPATH . 'core/Model.php';
}
require_once APPPATH . 'models/MenuSidebar_M.php';
$menu_model_shortcut = new MenuSidebar_M();
?>
<div class="m-content">

  <!-- Profil anak aktif -->
  <div class="m-card m-profil-anak">
    <?php if (!empty($user['Pasfoto'])) : ?>
      <img src="<?= upload_url('santri', $user['Pasfoto']); ?>" alt="<?= html_escape($user['NamaLengkap']); ?>" data-fallback-class="m-profil-anak-fallback" data-fallback-text="<?= html_escape(strtoupper(substr($user['NamaLengkap'], 0, 1))); ?>" onerror="mFotoGagalMuat(this)">
    <?php else : ?>
      <div class="m-profil-anak-fallback"><?= strtoupper(substr($user['NamaLengkap'], 0, 1)); ?></div>
    <?php endif; ?>
    <div>
      <div class="m-profil-anak-nama"><?= html_escape($user['NamaLengkap']); ?></div>
      <div class="m-profil-anak-sub">NIS <?= html_escape($user['NIS']); ?> &middot; <?= html_escape($user['NamaKelas']); ?></div>
    </div>
  </div>

  <!-- Pintasan navigasi cepat -->
  <div class="m-shortcut-row">
    <?php if ($menu_model_shortcut->isUrlAktif('Wali/Setoran', 'wali') && $menu_model_shortcut->isTampilDashboard('Wali/Setoran', 'wali')) : ?>
      <a href="<?= base_url('Wali/Setoran'); ?>" class="m-shortcut">
        <i class="fas fa-address-card"></i>
        <span>Setoran</span>
      </a>
    <?php endif; ?>
    <?php if ($menu_model_shortcut->isUrlAktif('kelas', 'wali') && $menu_model_shortcut->isTampilDashboard('kelas', 'wali')) : ?>
      <a href="<?= base_url('kelas'); ?>" class="m-shortcut">
        <i class="fas fa-school"></i>
        <span>Kelas</span>
      </a>
    <?php endif; ?>
    <?php if ($menu_model_shortcut->isUrlAktif('Wali/Raport', 'wali') && $menu_model_shortcut->isTampilDashboard('Wali/Raport', 'wali')) : ?>
      <a href="<?= base_url('Wali/Raport'); ?>" class="m-shortcut">
        <i class="fas fa-file-archive"></i>
        <span>Rapor</span>
      </a>
    <?php endif; ?>
    <?php if ($menu_model_shortcut->isUrlAktif('dana', 'wali') && $menu_model_shortcut->isTampilDashboard('dana', 'wali')) : ?>
      <a href="<?= base_url('dana'); ?>" class="m-shortcut">
        <i class="fas fa-wallet"></i>
        <span>Dana</span>
      </a>
    <?php endif; ?>
    <?php if ($menu_model_shortcut->isUrlAktif('laporan', 'wali') && $menu_model_shortcut->isTampilDashboard('laporan', 'wali')) : ?>
      <a href="<?= base_url('laporan'); ?>" class="m-shortcut">
        <i class="fas fa-chart-bar"></i>
        <span>Laporan Absen</span>
      </a>
    <?php endif; ?>
    <?php if ($menu_model_shortcut->isUrlAktif('statistik', 'wali') && $menu_model_shortcut->isTampilDashboard('statistik', 'wali')) : ?>
      <a href="<?= base_url('statistik'); ?>" class="m-shortcut">
        <i class="fas fa-chart-pie"></i>
        <span>Statistik Publik</span>
      </a>
    <?php endif; ?>
  </div>

  <!-- Ringkasan absen bulan ini -->
  <p class="m-page-title">Absen Bulan Ini</p>
  <div class="m-stat-row">
    <div class="m-card m-stat-success" style="padding:10px;">
      <p class="m-card-title" style="font-size:11px;">Hadir</p>
      <p class="m-card-value" style="font-size:18px;"><?= (int) $ringkasan_absen['Hadir']; ?></p>
    </div>
    <div class="m-card" style="padding:10px;">
      <p class="m-card-title" style="font-size:11px;">Sakit</p>
      <p class="m-card-value" style="font-size:18px; color:#b98900;"><?= (int) $ringkasan_absen['Sakit']; ?></p>
    </div>
    <div class="m-card" style="padding:10px;">
      <p class="m-card-title" style="font-size:11px;">Izin</p>
      <p class="m-card-value" style="font-size:18px; color:#17a2b8;"><?= (int) $ringkasan_absen['Izin']; ?></p>
    </div>
    <div class="m-card m-stat-danger" style="padding:10px;">
      <p class="m-card-title" style="font-size:11px;">Alpa</p>
      <p class="m-card-value" style="font-size:18px;"><?= (int) $ringkasan_absen['Alpa']; ?></p>
    </div>
  </div>

  <!-- Ajukan Izin/Sakit hari ini -->
  <div class="m-card">
    <?php if ($status_hari_ini && in_array($status_hari_ini['Status'], ['Sakit', 'Izin'], TRUE)) :
      $warna_status = $status_hari_ini['Status'] === 'Sakit' ? '#b98900' : '#17a2b8';
    ?>
      <div class="m-list-item" style="border-bottom:none; padding-bottom:0;">
        <div>
          <div class="m-list-title">Status Hari Ini</div>
          <?php if (!empty($status_hari_ini['Keterangan'])) : ?>
            <div class="m-list-sub"><?= html_escape($status_hari_ini['Keterangan']); ?></div>
          <?php endif; ?>
        </div>
        <span class="m-badge" style="background:<?= $warna_status; ?>;"><?= html_escape($status_hari_ini['Status']); ?></span>
      </div>
      <p class="mt-1" style="margin:8px 0 0; font-size:12px; color:#6c757d;">Sudah diajukan hari ini. Kirim lagi kalau ingin mengubah status/keterangannya.</p>
    <?php endif; ?>

    <button type="button" class="m-btn mt-2" data-toggle-target="#formIzinSakit" style="background:#ffc107; color:#212529;">
      <i class="fas fa-clipboard-list"></i> Ajukan Izin/Sakit Hari Ini
    </button>

    <div class="m-form-panel" id="formIzinSakit" hidden>
      <?= form_open('Wali/ajukan_izin_sakit'); ?>
      <div class="m-field">
        <label>Status *</label>
        <select name="status" required>
          <option value="">-- Pilih Status --</option>
          <option value="Sakit">Sakit</option>
          <option value="Izin">Izin</option>
        </select>
      </div>
      <div class="m-field">
        <label>Keterangan *</label>
        <textarea name="keterangan" rows="3" required placeholder="Contoh: Demam sejak semalam"></textarea>
      </div>
      <button type="submit" class="m-btn"><i class="fas fa-paper-plane"></i> Kirim</button>
      <?= form_close(); ?>
    </div>
  </div>

  <!-- Info penting dari Admin/Musyrif -->
  <p class="m-page-title">Info Penting</p>

  <div class="m-card">
    <?php if ($pengumuman) : ?>
      <?php foreach ($pengumuman as $p) : ?>
        <div class="m-list-item" style="flex-direction: column; align-items: stretch;">
          <div>
            <div class="m-list-title"><i class="fas fa-bullhorn" style="color:var(--teal); margin-right:4px;"></i> <?= html_escape($p['Judul']); ?></div>
            <div class="m-list-sub" style="white-space:pre-line; margin-top:4px;"><?= html_escape($p['Isi']); ?></div>
            <div class="m-list-sub mt-1"><?= html_escape($p['DibuatOleh']); ?> &middot; <?= date('d F Y', strtotime($p['CreatedAt'])); ?></div>
          </div>
        </div>
      <?php endforeach; ?>
    <?php else : ?>
      <p class="m-empty">Belum ada info penting.</p>
    <?php endif; ?>
  </div>
</div>

<script>
  document.addEventListener('DOMContentLoaded', function () {
    cekPengumumanBaru('pengumuman_terakhir_<?= html_escape($this->session->userdata('username')); ?>', <?= !empty($pengumuman) ? (int) $pengumuman[0]['IdPengumuman'] : 0; ?>);
  });
</script>
