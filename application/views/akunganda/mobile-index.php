<?php
$formatAnak = function ($anak) {
  if (!$anak) {
    return '(tidak ada)';
  }
  return implode(', ', array_map(function ($a) {
    return html_escape($a['NamaLengkap']) . ' (IdSiswa=' . $a['IdSiswa'] . ')';
  }, $anak));
};

// Tombol "Tes" per anak - langsung isi form Simulasi (email yang dicari + IdSiswa anak ini) tanpa
// perlu admin salin-tempel angka manual, supaya tidak ketuker sama NIS lagi.
$tombolTesAnak = function ($anak, $email) {
  if (!$anak) {
    return '';
  }
  $html = '';
  foreach ($anak as $a) {
    $url = base_url('akunganda?cari=' . urlencode($email) . '&sim_email=' . urlencode($email) . '&sim_id_siswa=' . (int) $a['IdSiswa']);
    $html .= '<a href="' . $url . '" class="m-badge m-badge-l" style="margin:2px 4px 2px 0; display:inline-block;">Tes: ' . html_escape($a['NamaLengkap']) . '</a>';
  }
  return $html;
};
?>
<div class="m-content">
  <p class="m-page-title">Akun Wali Ganda</p>

  <div class="m-card">
    <p class="m-list-sub" style="margin:0;">
      Daftar email yang kebetulan punya lebih dari satu akun login Wali (biasanya karena anak kedua
      dulu ditambahkan dengan password berbeda dari akun pertama). Wali dengan kondisi ini tidak
      bisa ubah profil/password sendiri karena sistem anggap emailnya sudah dipakai akun lain.
      Pilih akun yang ingin disimpan, sisanya akan digabungkan (anaknya dipindahkan, akun lama dihapus).
    </p>
  </div>

  <div class="m-card">
    <p class="m-profil-section-title"><i class="fas fa-search"></i> Cek Data Mentah Satu Email</p>
    <form action="<?= base_url('akunganda'); ?>" method="get">
      <div class="m-field" style="margin-bottom:8px;">
        <input type="text" name="cari" value="<?= html_escape($cari); ?>" placeholder="Ketik/tempel email wali di sini">
      </div>
      <button type="submit" class="m-btn m-btn-outline"><i class="fas fa-search"></i> Cari</button>
    </form>

    <?php if ($hasil_cari !== null) : ?>
      <div class="mt-2" style="border-top:1px dashed var(--border); padding-top:10px;">
        <p class="m-list-sub" style="font-weight:600;">Cocok persis (<?= count($hasil_cari['persis']); ?>):</p>
        <?php if (!$hasil_cari['persis']) : ?>
          <p class="m-list-sub">Tidak ada baris login yang persis sama dengan "<?= html_escape($cari); ?>".</p>
        <?php else : ?>
          <?php foreach ($hasil_cari['persis'] as $r) : ?>
            <div class="m-list-item" style="flex-direction:column; align-items:stretch;">
              <div class="m-list-title">IdUser #<?= $r['IdUser']; ?> &middot; level: <?= html_escape($r['level']); ?></div>
              <div class="m-list-sub">username: "<?= html_escape($r['username']); ?>" (panjang <?= $r['panjang']; ?> karakter) &middot; login.IdSiswa (kolom lama): <?= $r['IdSiswa'] ?? 'NULL'; ?></div>
              <div class="m-list-sub">Anak (wali_siswa): <?= $formatAnak($r['anak']); ?></div>
              <?php if ($r['anak']) : ?>
                <div class="mt-1"><?= $tombolTesAnak($r['anak'], $cari); ?></div>
              <?php endif; ?>
            </div>
          <?php endforeach; ?>
        <?php endif; ?>

        <p class="m-list-sub mt-2" style="font-weight:600;">Mirip/mengandung teks ini (<?= count($hasil_cari['mirip']); ?>):</p>
        <?php if (!$hasil_cari['mirip']) : ?>
          <p class="m-list-sub">Tidak ada baris login lain yang mengandung teks itu.</p>
        <?php else : ?>
          <?php foreach ($hasil_cari['mirip'] as $r) : ?>
            <div class="m-list-item" style="flex-direction:column; align-items:stretch;">
              <div class="m-list-title">IdUser #<?= $r['IdUser']; ?> &middot; level: <?= html_escape($r['level']); ?></div>
              <div class="m-list-sub">username: "<?= html_escape($r['username']); ?>" (panjang <?= $r['panjang']; ?> karakter) &middot; login.IdSiswa (kolom lama): <?= $r['IdSiswa'] ?? 'NULL'; ?></div>
              <div class="m-list-sub">Anak (wali_siswa): <?= $formatAnak($r['anak']); ?></div>
            </div>
          <?php endforeach; ?>
        <?php endif; ?>
      </div>
    <?php endif; ?>
  </div>

  <div class="m-card">
    <p class="m-profil-section-title"><i class="fas fa-flask"></i> Simulasi Validasi Email</p>
    <p class="m-list-sub">Jalankan langsung fungsi yang dipakai saat wali menyimpan profil, dengan email &amp; IdSiswa (dari daftar anak di atas) yang Anda masukkan sendiri - supaya kelihatan persis kenapa ditolak/diterima.</p>
    <form action="<?= base_url('akunganda'); ?>" method="get">
      <?php if ($cari !== '') : ?><input type="hidden" name="cari" value="<?= html_escape($cari); ?>"><?php endif; ?>
      <div class="m-field">
        <label>Email</label>
        <input type="text" name="sim_email" value="<?= html_escape($sim_email); ?>" placeholder="Email yang mau disimpan di form profil">
      </div>
      <div class="m-field">
        <label>IdSiswa (anak yang sedang aktif saat wali itu login)</label>
        <input type="text" name="sim_id_siswa" value="<?= html_escape($sim_id_siswa); ?>" placeholder="Contoh: 38">
      </div>
      <button type="submit" class="m-btn m-btn-outline"><i class="fas fa-play"></i> Jalankan Simulasi</button>
    </form>

    <?php if ($hasil_simulasi !== null) : ?>
      <div class="mt-2" style="border-top:1px dashed var(--border); padding-top:10px;">
        <div class="m-list-item" style="flex-direction:column; align-items:stretch;">
          <div class="m-list-title">getLoginWaliUntukSiswa(<?= html_escape($sim_id_siswa); ?>) mengembalikan:</div>
          <div class="m-list-sub"><?= $hasil_simulasi['akun_pemilik'] ? 'IdUser #' . $hasil_simulasi['akun_pemilik']['IdUser'] . ', username "' . html_escape($hasil_simulasi['akun_pemilik']['username']) . '"' : 'NULL (tidak ditemukan sama sekali)'; ?></div>
        </div>
        <div class="m-list-item" style="flex-direction:column; align-items:stretch;">
          <div class="m-list-title">isEmailTerpakai("<?= html_escape($sim_email); ?>", <?= html_escape($sim_id_siswa); ?>) mengembalikan:</div>
          <div class="m-list-sub">
            <span class="m-badge <?= $hasil_simulasi['dipakai'] ? 'm-badge-belum' : 'm-badge-selesai'; ?>"><?= $hasil_simulasi['dipakai'] ? 'TRUE (dianggap sudah dipakai - DITOLAK)' : 'FALSE (dianggap kosong - DITERIMA)'; ?></span>
          </div>
        </div>
      </div>
    <?php endif; ?>
  </div>

  <?php if (!$grup) : ?>
    <div class="m-card">
      <p class="m-empty">Tidak ada akun wali ganda ditemukan. Semua email login unik satu akun.</p>
    </div>
  <?php else : ?>
    <?php foreach ($grup as $item) : ?>
      <div class="m-card">
        <p class="m-profil-section-title"><i class="fas fa-user-friends"></i> <?= html_escape($item['username']); ?> <span class="m-profil-readonly-note"><?= count($item['akun']); ?> akun</span></p>

        <?= form_open('akunganda/gabung'); ?>
        <?php foreach ($item['akun'] as $i => $a) : ?>
          <div class="m-field" style="margin-bottom:10px; display:flex; align-items:flex-start; gap:8px;">
            <input type="radio" name="id_simpan" id="simpan<?= $a['IdUser']; ?>" value="<?= $a['IdUser']; ?>" <?= $i === 0 ? 'checked' : ''; ?> required style="margin-top:3px;">
            <input type="hidden" name="id_lain[]" value="<?= $a['IdUser']; ?>">
            <label for="simpan<?= $a['IdUser']; ?>" style="margin:0; font-weight:400;">
              <strong>IdUser #<?= $a['IdUser']; ?></strong><br>
              <span class="m-list-sub">Anak: <?= $formatAnak($a['anak']); ?></span>
            </label>
          </div>
        <?php endforeach; ?>
        <button type="submit" class="m-btn" style="background:#ffc107; color:#212529;"><i class="fas fa-code-branch"></i> Gabungkan Jadi Satu Akun</button>
        <?= form_close(); ?>
      </div>
    <?php endforeach; ?>
  <?php endif; ?>
</div>
