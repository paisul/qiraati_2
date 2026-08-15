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
    $html .= '<a href="' . $url . '" class="badge badge-primary mr-1">Tes: ' . html_escape($a['NamaLengkap']) . '</a>';
  }
  return $html;
};
?>
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
  <div class="content mt-2">
    <div class="container-fluid">
      <div class="row">
        <div class="col-12">
          <div class="card">
            <div class="card-header bg-warning">
              <h4 class="m-0"><?= $title; ?></h4>
            </div>

            <div class="flash-data" data-flashdata="<?= isset($pesan) ? $pesan : $this->session->flashdata('pesan'); ?>" data-title="Akun Wali Ganda">
            </div>

            <div class="card-body">
              <p class="text-muted">
                Daftar email yang kebetulan punya lebih dari satu akun login Wali (biasanya karena anak kedua dulu
                ditambahkan dengan password berbeda dari akun pertama, jadi bukan tersambung tapi malah bikin akun
                baru). Wali dengan kondisi ini tidak bisa mengubah profil/passwordnya sendiri karena sistem menganggap
                emailnya sudah dipakai akun lain. Pilih akun mana yang ingin disimpan, sisanya akan digabungkan
                (semua anaknya dipindahkan, lalu akun lamanya dihapus).
              </p>

              <div class="card mb-3">
                <div class="card-header bg-light"><i class="fas fa-search"></i> Cek Data Mentah Satu Email</div>
                <div class="card-body">
                  <form action="<?= base_url('akunganda'); ?>" method="get" class="form-inline mb-3">
                    <input type="text" name="cari" value="<?= html_escape($cari); ?>" class="form-control mr-2" style="min-width:300px;" placeholder="Ketik/tempel email wali di sini">
                    <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i> Cari</button>
                  </form>

                  <?php if ($hasil_cari !== null) : ?>
                    <p><strong>Cocok persis (<?= count($hasil_cari['persis']); ?>):</strong></p>
                    <?php if (!$hasil_cari['persis']) : ?>
                      <p class="text-muted">Tidak ada baris login yang persis sama dengan "<?= html_escape($cari); ?>".</p>
                    <?php else : ?>
                      <table class="table table-sm table-bordered">
                        <thead><tr><th>IdUser</th><th>Level</th><th>username (panjang)</th><th>login.IdSiswa (kolom lama)</th><th>Anak (wali_siswa)</th><th>Simulasi Cepat</th></tr></thead>
                        <tbody>
                          <?php foreach ($hasil_cari['persis'] as $r) : ?>
                            <tr>
                              <td><?= $r['IdUser']; ?></td>
                              <td><?= html_escape($r['level']); ?></td>
                              <td>"<?= html_escape($r['username']); ?>" (<?= $r['panjang']; ?>)</td>
                              <td><?= $r['IdSiswa'] ?? 'NULL'; ?></td>
                              <td><?= $formatAnak($r['anak']); ?></td>
                              <td><?= $tombolTesAnak($r['anak'], $cari); ?></td>
                            </tr>
                          <?php endforeach; ?>
                        </tbody>
                      </table>
                    <?php endif; ?>

                    <p><strong>Mirip/mengandung teks ini (<?= count($hasil_cari['mirip']); ?>):</strong></p>
                    <?php if (!$hasil_cari['mirip']) : ?>
                      <p class="text-muted">Tidak ada baris login lain yang mengandung teks itu.</p>
                    <?php else : ?>
                      <table class="table table-sm table-bordered">
                        <thead><tr><th>IdUser</th><th>Level</th><th>username (panjang)</th><th>login.IdSiswa (kolom lama)</th><th>Anak (wali_siswa)</th></tr></thead>
                        <tbody>
                          <?php foreach ($hasil_cari['mirip'] as $r) : ?>
                            <tr>
                              <td><?= $r['IdUser']; ?></td>
                              <td><?= html_escape($r['level']); ?></td>
                              <td>"<?= html_escape($r['username']); ?>" (<?= $r['panjang']; ?>)</td>
                              <td><?= $r['IdSiswa'] ?? 'NULL'; ?></td>
                              <td><?= $formatAnak($r['anak']); ?></td>
                            </tr>
                          <?php endforeach; ?>
                        </tbody>
                      </table>
                    <?php endif; ?>
                  <?php endif; ?>
                </div>
              </div>

              <div class="card mb-3">
                <div class="card-header bg-light"><i class="fas fa-flask"></i> Simulasi Validasi Email</div>
                <div class="card-body">
                  <p class="text-muted">Jalankan langsung fungsi yang dipakai saat wali menyimpan profil, dengan email &amp; IdSiswa (dari kolom "Anak" di atas) yang Anda masukkan sendiri - supaya kelihatan persis kenapa ditolak/diterima.</p>
                  <form action="<?= base_url('akunganda'); ?>" method="get" class="form-inline mb-3">
                    <?php if ($cari !== '') : ?><input type="hidden" name="cari" value="<?= html_escape($cari); ?>"><?php endif; ?>
                    <input type="text" name="sim_email" value="<?= html_escape($sim_email); ?>" class="form-control mr-2" style="min-width:260px;" placeholder="Email yang mau disimpan">
                    <input type="text" name="sim_id_siswa" value="<?= html_escape($sim_id_siswa); ?>" class="form-control mr-2" style="max-width:160px;" placeholder="IdSiswa aktif">
                    <button type="submit" class="btn btn-primary"><i class="fas fa-play"></i> Jalankan Simulasi</button>
                  </form>

                  <?php if ($hasil_simulasi !== null) : ?>
                    <table class="table table-sm table-bordered">
                      <tr>
                        <th style="width:350px;">getLoginWaliUntukSiswa(<?= html_escape($sim_id_siswa); ?>)</th>
                        <td><?= $hasil_simulasi['akun_pemilik'] ? 'IdUser #' . $hasil_simulasi['akun_pemilik']['IdUser'] . ', username "' . html_escape($hasil_simulasi['akun_pemilik']['username']) . '"' : 'NULL (tidak ditemukan sama sekali)'; ?></td>
                      </tr>
                      <tr>
                        <th>isEmailTerpakai("<?= html_escape($sim_email); ?>", <?= html_escape($sim_id_siswa); ?>)</th>
                        <td>
                          <span class="badge <?= $hasil_simulasi['dipakai'] ? 'badge-danger' : 'badge-success'; ?>"><?= $hasil_simulasi['dipakai'] ? 'TRUE - DITOLAK' : 'FALSE - DITERIMA'; ?></span>
                        </td>
                      </tr>
                    </table>
                  <?php endif; ?>
                </div>
              </div>

              <?php if (!$grup) : ?>
                <div class="alert alert-success">Tidak ada akun wali ganda ditemukan. Semua email login unik satu akun.</div>
              <?php else : ?>
                <?php foreach ($grup as $item) : ?>
                  <div class="card mb-3 border-warning">
                    <div class="card-header bg-light">
                      <strong><?= html_escape($item['username']); ?></strong> - <?= count($item['akun']); ?> akun ditemukan
                    </div>
                    <div class="card-body">
                      <?= form_open('akunganda/gabung'); ?>
                      <?php foreach ($item['akun'] as $i => $a) : ?>
                        <div class="form-check mb-2">
                          <input class="form-check-input" type="radio" name="id_simpan" id="simpan<?= $a['IdUser']; ?>" value="<?= $a['IdUser']; ?>" <?= $i === 0 ? 'checked' : ''; ?> required>
                          <input type="hidden" name="id_lain[]" value="<?= $a['IdUser']; ?>">
                          <label class="form-check-label" for="simpan<?= $a['IdUser']; ?>">
                            IdUser #<?= $a['IdUser']; ?> - Anak: <?= $formatAnak($a['anak']); ?>
                          </label>
                        </div>
                      <?php endforeach; ?>
                      <button type="submit" class="btn btn-warning btn-sm"><i class="fas fa-code-branch"></i> Gabungkan Jadi Satu Akun</button>
                      <?= form_close(); ?>
                    </div>
                  </div>
                <?php endforeach; ?>
              <?php endif; ?>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<!-- /.content-wrapper -->
