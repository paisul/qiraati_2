<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">

  <!-- Main content -->
  <div class="content mt-2">
    <div class="container-fluid">
      <div class="row">
        <div class="col-sm">
          <div class="card">
            <div class="card-header bg-success">
              <h4 class="m-0"><?= $title; ?> <?= $jenis == 'santri' ? 'Santri' : 'Pembimbing'; ?></h4>
            </div>

            <div class="card-body">
              <?php if ($is_wali) : ?>
                <p class="text-muted">Rekap kehadiran anak Anda. Hubungi admin jika ingin melihat/mengubah data lain.</p>
              <?php else : ?>
                <div class="mb-3">
                  <a href="<?= base_url('laporan/santri?tanggal_awal=' . $tanggal_awal . '&tanggal_akhir=' . $tanggal_akhir); ?>" class="btn <?= $jenis == 'santri' ? 'btn-primary' : 'btn-outline-primary'; ?>">
                    <i class="fas fa-user-graduate"></i> Santri
                  </a>
                  <a href="<?= base_url('laporan/pembimbing?tanggal_awal=' . $tanggal_awal . '&tanggal_akhir=' . $tanggal_akhir); ?>" class="btn <?= $jenis == 'pembimbing' ? 'btn-primary' : 'btn-outline-primary'; ?>">
                    <i class="fas fa-chalkboard-teacher"></i> Pembimbing
                  </a>
                </div>
              <?php endif; ?>

              <?php
              $jumlah_filter_lanjutan = ($jenis == 'santri' && !$is_wali)
                ? count(array_filter([$id_kelas, $gender, $urutkan], function ($v) {
                  return $v !== '' && $v !== null;
                }))
                : 0;
              ?>
              <form action="<?= base_url('laporan/' . $jenis); ?>" class="mb-3" id="formFilterLaporan">
                <div class="form-row align-items-end">
                  <div class="col-md-3 col-6 mb-2">
                    <label class="mb-1">Dari Tanggal</label>
                    <input type="date" class="form-control" name="tanggal_awal" value="<?= $tanggal_awal; ?>">
                  </div>
                  <div class="col-md-3 col-6 mb-2">
                    <label class="mb-1">Sampai Tanggal</label>
                    <input type="date" class="form-control" name="tanggal_akhir" value="<?= $tanggal_akhir; ?>">
                  </div>
                  <?php if ($jenis == 'santri' && !$is_wali) : ?>
                    <div class="col-md-3 col-6 mb-2">
                      <label class="mb-1">Cari NIS</label>
                      <input type="text" class="form-control" name="nis" value="<?= html_escape($nis); ?>" placeholder="Cari NIS">
                    </div>
                  <?php endif; ?>
                  <div class="col-md-3 col-6 mb-2 d-flex align-items-end flex-wrap" style="gap: 8px;">
                    <?php if ($jenis == 'santri' && !$is_wali) : ?>
                      <button class="btn btn-outline-secondary" type="button" data-toggle="collapse" data-target="#panelFilterLanjutan">
                        <i class="fas fa-sliders-h"></i> Filter Lanjutan
                        <?php if ($jumlah_filter_lanjutan > 0) : ?>
                          <span class="badge badge-primary ml-1"><?= $jumlah_filter_lanjutan; ?></span>
                        <?php endif; ?>
                      </button>
                    <?php endif; ?>
                    <?php if ($jumlah_filter_lanjutan > 0 || $nis || $id_kelas) : ?>
                      <a href="<?= base_url('laporan/' . $jenis . '?tanggal_awal=' . $tanggal_awal . '&tanggal_akhir=' . $tanggal_akhir); ?>" class="btn btn-outline-danger"><i class="fas fa-times"></i> Reset Filter</a>
                    <?php endif; ?>
                  </div>
                </div>

                <?php if ($jenis == 'santri' && !$is_wali) : ?>
                  <div class="collapse <?= $filter_lanjutan_aktif ? 'show' : ''; ?>" id="panelFilterLanjutan">
                    <div class="card card-body bg-light">
                      <div class="form-row">
                        <div class="col-md-4 col-6 mb-2">
                          <label class="mb-1">Kelas</label>
                          <select class="form-control select-modern" name="id_kelas">
                            <option value="">-- Semua Kelas --</option>
                            <?php foreach ($kelas as $row) : ?>
                              <option value="<?= $row['IdKelas']; ?>" <?= (string) $id_kelas === (string) $row['IdKelas'] ? 'selected' : ''; ?>><?= html_escape($row['NamaKelas']); ?></option>
                            <?php endforeach; ?>
                          </select>
                        </div>
                        <div class="col-md-4 col-6 mb-2">
                          <label class="mb-1">Gender</label>
                          <select class="form-control select-modern" name="gender">
                            <option value="">-- Semua Gender --</option>
                            <?php foreach ($jenis_kelamin_list as $jk) : ?>
                              <option value="<?= $jk; ?>" <?= (string) $gender === (string) $jk ? 'selected' : ''; ?>><?= $jk; ?></option>
                            <?php endforeach; ?>
                          </select>
                        </div>
                        <div class="col-md-4 col-6 mb-2">
                          <label class="mb-1">Urutkan Berdasarkan</label>
                          <select class="form-control select-modern" name="urutkan">
                            <option value="">-- Nama (default) --</option>
                            <option value="kelas" <?= $urutkan === 'kelas' ? 'selected' : ''; ?>>Kelas</option>
                            <option value="gender" <?= $urutkan === 'gender' ? 'selected' : ''; ?>>Gender</option>
                            <option value="usia" <?= $urutkan === 'usia' ? 'selected' : ''; ?>>Usia</option>
                          </select>
                        </div>
                      </div>
                    </div>
                  </div>
                <?php endif; ?>
              </form>

              <table id="example2" class="table table-bordered table-striped text-center">
                <thead>
                  <tr>
                    <th style="width: 50px;">No</th>
                    <?php if ($jenis == 'santri') : ?>
                      <th style="width: 70px;">Pasfoto</th>
                    <?php endif; ?>
                    <th style="width: 140px;"><?= $jenis == 'santri' ? 'NIS' : 'Email'; ?></th>
                    <th>Nama</th>
                    <th><?= $jenis == 'santri' ? 'Kelas' : 'No HP'; ?></th>
                    <?php if ($jenis == 'santri') : ?>
                      <th style="width: 70px;">Usia</th>
                    <?php endif; ?>
                    <th>Hadir</th>
                    <th>Sakit</th>
                    <th>Izin</th>
                    <th>Alpa</th>
                    <th>Total Absen</th>
                  </tr>
                </thead>
                <tbody>
                  <?php $no = 1; foreach ($rekap as $row) : ?>
                    <tr>
                      <td><?= $no++; ?></td>
                      <?php if ($jenis == 'santri') : ?>
                        <td>
                          <?php if (!empty($row['pasfoto'])) : ?>
                            <img src="<?= upload_url('santri', $row['pasfoto']); ?>" width="48" height="48" class="img-circle" style="object-fit: cover; object-position: center top;">
                          <?php else : ?>
                            <div style="width:48px;height:48px;border-radius:50%;background:#172a3a;color:#fff;display:inline-flex;align-items:center;justify-content:center;font-weight:bold;">
                              <?= strtoupper(substr($row['nama'], 0, 1)); ?>
                            </div>
                          <?php endif; ?>
                        </td>
                      <?php endif; ?>
                      <td><?= html_escape($row['nomor']); ?></td>
                      <td class="text-left"><?= html_escape($row['nama']); ?></td>
                      <td><?= html_escape($row['keterangan']); ?></td>
                      <?php if ($jenis == 'santri') : ?>
                        <td><?= isset($row['usia']) ? (int) $row['usia'] : '-'; ?></td>
                      <?php endif; ?>
                      <td><span class="badge badge-success"><?= $row['Hadir']; ?></span></td>
                      <td><span class="badge badge-warning"><?= $row['Sakit']; ?></span></td>
                      <td><span class="badge badge-info"><?= $row['Izin']; ?></span></td>
                      <td><span class="badge badge-danger"><?= $row['Alpa']; ?></span></td>
                      <td><?= $row['Total']; ?></td>
                    </tr>
                  <?php endforeach; ?>
                  <?php if (empty($rekap)) : ?>
                    <tr>
                      <td colspan="<?= $jenis == 'santri' ? 11 : 9; ?>">Tidak ada data untuk ditampilkan.</td>
                    </tr>
                  <?php endif; ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
  // DOMContentLoaded dipakai karena plugin Select2 dimuat di footer.php, setelah konten ini -
  // baru tersedia di jQuery setelah seluruh halaman selesai di-parse.
  document.addEventListener('DOMContentLoaded', function () {
    var form = document.getElementById('formFilterLaporan');

    // Auto-tampilkan: ganti Tanggal langsung menerapkan filter, tidak perlu tekan tombol Tampilkan.
    ['tanggal_awal', 'tanggal_akhir'].forEach(function (nama) {
      var el = form.querySelector('input[name="' + nama + '"]');
      if (el) {
        el.addEventListener('change', function () {
          form.submit();
        });
      }
    });

    // NIS: submit otomatis saat mengetik, tapi ditunda sebentar (debounce) supaya tidak reload
    // di setiap huruf yang diketik - baru dikirim kalau sudah berhenti mengetik selama 500ms.
    var nisInput = form.querySelector('input[name="nis"]');
    if (nisInput) {
      var timerNis = null;
      nisInput.addEventListener('input', function () {
        clearTimeout(timerNis);
        timerNis = setTimeout(function () {
          form.submit();
        }, 500);
      });

      // Setiap reload dari auto-search NIS menghapus fokus (browser reload halaman baru) -
      // kembalikan fokus + posisi kursor ke akhir teks supaya bisa lanjut mengetik tanpa klik ulang.
      if (nisInput.value) {
        nisInput.focus();
        var panjang = nisInput.value.length;
        nisInput.setSelectionRange(panjang, panjang);
      }
    }

    // Kelas/Gender/Urutkan dibungkus Select2 - event 'change'-nya dipicu lewat sistem event jQuery
    // sendiri (bukan native dispatchEvent), jadi listener-nya WAJIB lewat jQuery .on(), bukan
    // addEventListener biasa, supaya benar-benar tertangkap saat admin memilih opsi baru.
    if (window.jQuery && jQuery.fn.select2) {
      jQuery('.select-modern').select2({ width: '100%', theme: 'bootstrap4' });
      jQuery('select[name="id_kelas"], select[name="gender"], select[name="urutkan"]', form).on('change', function () {
        form.submit();
      });
    }
  });
</script>
