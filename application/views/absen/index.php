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

            <div class="flash-data" data-flashdata="<?= isset($pesan) ? $pesan : $this->session->flashdata('pesan'); ?>" data-title="Data Absen">
            </div>

            <div class="card-body">
              <div class="row mb-3">
                <div class="col-md-6 mb-2">
                  <a href="<?= base_url('absen/santri?tanggal=' . $tanggal); ?>" class="btn <?= $jenis == 'santri' ? 'btn-primary' : 'btn-outline-primary'; ?>">
                    <i class="fas fa-user-graduate"></i> Santri
                  </a>
                  <a href="<?= base_url('absen/pembimbing?tanggal=' . $tanggal); ?>" class="btn <?= $jenis == 'pembimbing' ? 'btn-primary' : 'btn-outline-primary'; ?>">
                    <i class="fas fa-chalkboard-teacher"></i> Pembimbing
                  </a>
                </div>
                <div class="col-md-6">
                  <form action="<?= base_url('absen/' . $jenis); ?>" class="form-inline justify-content-md-end" id="formFilterTanggal">
                    <input type="date" class="form-control mb-2" name="tanggal" value="<?= $tanggal; ?>">
                  </form>
                </div>
              </div>

              <div class="row mb-3">
                <div class="col-md-3 col-6 mb-2">
                  <div class="info-box bg-success">
                    <span class="info-box-icon"><i class="fas fa-check"></i></span>
                    <div class="info-box-content">
                      <span class="info-box-text">Hadir</span>
                      <span class="info-box-number" id="ringkasan-Hadir"><?= $ringkasan['Hadir']; ?></span>
                    </div>
                  </div>
                </div>
                <div class="col-md-3 col-6 mb-2">
                  <div class="info-box bg-warning">
                    <span class="info-box-icon"><i class="fas fa-notes-medical"></i></span>
                    <div class="info-box-content">
                      <span class="info-box-text">Sakit</span>
                      <span class="info-box-number" id="ringkasan-Sakit"><?= $ringkasan['Sakit']; ?></span>
                    </div>
                  </div>
                </div>
                <div class="col-md-3 col-6 mb-2">
                  <div class="info-box bg-info">
                    <span class="info-box-icon"><i class="fas fa-envelope-open-text"></i></span>
                    <div class="info-box-content">
                      <span class="info-box-text">Izin</span>
                      <span class="info-box-number" id="ringkasan-Izin"><?= $ringkasan['Izin']; ?></span>
                    </div>
                  </div>
                </div>
                <div class="col-md-3 col-6 mb-2">
                  <div class="info-box bg-danger">
                    <span class="info-box-icon"><i class="fas fa-times"></i></span>
                    <div class="info-box-content">
                      <span class="info-box-text">Alpa</span>
                      <span class="info-box-number" id="ringkasan-Alpa"><?= $ringkasan['Alpa']; ?></span>
                    </div>
                  </div>
                </div>
              </div>

              <div class="row mb-3 align-items-center" id="kirimAbsensiWrapper">
                <div class="col-12 text-md-right text-center">
                  <span id="indikatorAbsensi" class="badge badge-secondary p-2 mr-2" style="font-size: 14px;"></span>
                  <button type="button" id="btnKirimAbsensi" class="btn btn-primary" style="display:none;">
                    <i class="fas fa-paper-plane"></i> Kirim Absensi
                  </button>
                </div>
              </div>

              <table id="example2" class="table table-bordered table-striped text-center">
                <thead>
                  <tr>
                    <th style="width: 50px;">No</th>
                    <th style="width: 90px;">Pasfoto</th>
                    <th style="width: 140px;"><?= $jenis == 'santri' ? 'NIS' : 'Email'; ?></th>
                    <th>Nama</th>
                    <th>Keterangan</th>
                    <th>Status</th>
                    <th style="width: 400px;">Aksi</th>
                  </tr>
                </thead>
                <tbody>
                  <?php
                  $no = 1;
                  $warna_status = ['Hadir' => 'success', 'Sakit' => 'warning', 'Izin' => 'info', 'Alpa' => 'danger'];
                  foreach ($peserta as $row) :
                    $status = isset($status_awal[$row['id']]) ? $status_awal[$row['id']] : '';
                    $badge = isset($warna_status[$status]) ? $warna_status[$status] : 'secondary';
                    $inisial = strtoupper(substr($row['nama'], 0, 1));
                  ?>
                    <tr>
                      <td><?= $no++; ?></td>
                      <td>
                        <?php if (!empty($row['pasfoto'])) :
                          $folderFoto = $jenis == 'santri' ? 'santri' : 'pasfoto_musyrif';
                        ?>
                          <img src="<?= upload_url($folderFoto, $row['pasfoto']); ?>" width="64" height="64" class="img-circle" style="object-fit: cover; object-position: center top;">
                        <?php else : ?>
                          <div style="width:64px;height:64px;border-radius:50%;background:#172a3a;color:#fff;display:inline-flex;align-items:center;justify-content:center;font-weight:bold;font-size:20px;">
                            <?= $inisial; ?>
                          </div>
                        <?php endif; ?>
                      </td>
                      <td><?= html_escape($row['nomor']); ?></td>
                      <td class="text-left"><?= html_escape($row['nama']); ?></td>
                      <td><?= html_escape($row['keterangan']); ?></td>
                      <td>
                        <span class="badge status-badge badge-<?= $badge; ?>"><?= $status !== '' ? $status : 'Belum Absen'; ?></span>
                      </td>
                      <td>
                        <div class="btn-group-absen" data-id-peserta="<?= $row['id']; ?>">
                          <?php foreach ($warna_status as $label => $warna) :
                            $aktif = $status === $label;
                          ?>
                            <button type="button"
                              class="btn btn-sm btn-absen <?= $aktif ? 'btn-' . $warna : 'btn-outline-' . $warna; ?> mb-1"
                              data-id="<?= $row['id']; ?>"
                              data-status="<?= $label; ?>"
                              data-warna="<?= $warna; ?>"><?= $label; ?></button>
                          <?php endforeach; ?>
                          <button type="button"
                            class="btn btn-sm btn-outline-dark btn-reset-absen mb-1"
                            data-id="<?= $row['id']; ?>"
                            style="<?= $status === '' ? 'display:none;' : ''; ?>">Belum Absen</button>
                        </div>
                      </td>
                    </tr>
                  <?php endforeach; ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<script type="text/javascript">
  (function () {
    var totalPeserta = <?= (int) count($peserta); ?>;
    var jenis = '<?= $jenis; ?>';
    var tanggal = '<?= $tanggal; ?>';
    var urlSimpanSementara = '<?= base_url('absen/simpan_sementara'); ?>';
    var urlKirimAbsensi = '<?= base_url('absen/kirim_absensi'); ?>';
    var urlResetStatus = '<?= base_url('absen/reset_status'); ?>';

    var state = <?= json_encode($status_awal, JSON_FORCE_OBJECT); ?>;
    var sudahDikirim = false;

    var indikator = document.getElementById('indikatorAbsensi');
    var btnKirim = document.getElementById('btnKirimAbsensi');

    // Ganti Tanggal langsung menerapkan filter, tidak perlu tekan tombol Tampil.
    var formTanggal = document.getElementById('formFilterTanggal');
    if (formTanggal) {
      var inputTanggal = formTanggal.querySelector('input[name="tanggal"]');
      if (inputTanggal) {
        inputTanggal.addEventListener('change', function () {
          formTanggal.submit();
        });
      }
    }

    function jumlahTerisi() {
      return Object.keys(state).length;
    }

    function updateIndikator() {
      if (sudahDikirim) {
        btnKirim.style.display = 'none';
        return;
      }

      var terisi = jumlahTerisi();
      indikator.textContent = terisi + '/' + totalPeserta + ' siswa sudah diisi';

      if (totalPeserta > 0 && terisi >= totalPeserta) {
        indikator.classList.remove('badge-secondary');
        indikator.classList.add('badge-success');
        btnKirim.style.display = 'inline-block';
      } else {
        indikator.classList.remove('badge-success');
        indikator.classList.add('badge-secondary');
        btnKirim.style.display = 'none';
      }
    }

    function tandaiTombolAktif(group, statusAktif) {
      group.querySelectorAll('.btn-absen').forEach(function (b) {
        var warna = b.dataset.warna;
        b.classList.remove('btn-' + warna, 'btn-outline-' + warna);
        b.classList.add(b.dataset.status === statusAktif ? 'btn-' + warna : 'btn-outline-' + warna);
      });
    }

    document.querySelectorAll('.btn-absen').forEach(function (btn) {
      btn.addEventListener('click', function () {
        if (sudahDikirim) {
          return;
        }

        var id = btn.dataset.id;
        var status = btn.dataset.status;
        var warna = btn.dataset.warna;
        var group = btn.closest('.btn-group-absen');
        var baris = btn.closest('tr');
        var badge = baris ? baris.querySelector('.status-badge') : null;
        var tombolReset = group.querySelector('.btn-reset-absen');

        state[id] = status;
        tandaiTombolAktif(group, status);

        if (badge) {
          badge.textContent = status;
          badge.className = 'badge status-badge badge-' + warna;
        }

        if (tombolReset) {
          tombolReset.style.display = '';
        }

        updateIndikator();

        fetch(urlSimpanSementara, {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({
            jenis: jenis,
            tanggal: tanggal,
            id_peserta: id,
            status: status
          })
        }).catch(function () {
          // Gagal menyimpan draf sementara tidak menghentikan pengisian; akan divalidasi ulang saat kirim.
        });
      });
    });

    // Tombol "Belum Absen" sengaja TIDAK dicegah oleh sudahDikirim - berbeda dari .btn-absen di atas,
    // fitur ini harus tetap bisa dipakai untuk mengoreksi absen yang sudah dikirim/tersimpan permanen,
    // baik hari ini maupun hari-hari sebelumnya.
    document.querySelectorAll('.btn-reset-absen').forEach(function (btn) {
      btn.addEventListener('click', function () {
        var id = btn.dataset.id;
        var group = btn.closest('.btn-group-absen');
        var baris = btn.closest('tr');
        var badge = baris ? baris.querySelector('.status-badge') : null;

        btn.disabled = true;

        fetch(urlResetStatus, {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({
            jenis: jenis,
            tanggal: tanggal,
            id_peserta: id
          })
        })
          .then(function (res) { return res.json(); })
          .then(function (json) {
            btn.disabled = false;

            if (!json.status) {
              Swal.fire({
                icon: 'error',
                title: 'Gagal',
                text: json.message || 'Gagal mengembalikan status.'
              });
              return;
            }

            delete state[id];
            tandaiTombolAktif(group, null);
            btn.style.display = 'none';

            if (badge) {
              badge.textContent = 'Belum Absen';
              badge.className = 'badge status-badge badge-secondary';
            }

            // Mengoreksi salah satu peserta berarti pengisian jadi belum lengkap lagi -
            // buka kembali kunci "sudah dikirim" supaya bisa dikirim ulang setelah diperbaiki.
            sudahDikirim = false;
            btnKirim.disabled = false;
            btnKirim.innerHTML = '<i class="fas fa-paper-plane"></i> Kirim Absensi';

            if (json.ringkasan) {
              Object.keys(json.ringkasan).forEach(function (key) {
                var el = document.getElementById('ringkasan-' + key);
                if (el) el.textContent = json.ringkasan[key];
              });
            }

            updateIndikator();
          })
          .catch(function () {
            btn.disabled = false;
            Swal.fire({
              icon: 'error',
              title: 'Gagal',
              text: 'Terjadi kesalahan koneksi, silakan coba lagi.'
            });
          });
      });
    });

    btnKirim.addEventListener('click', function () {
      if (jumlahTerisi() < totalPeserta) {
        Swal.fire({
          icon: 'warning',
          title: 'Belum Lengkap',
          text: (totalPeserta - jumlahTerisi()) + ' siswa belum diisi statusnya.'
        });
        return;
      }

      btnKirim.disabled = true;
      btnKirim.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Mengirim...';

      fetch(urlKirimAbsensi, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
          jenis: jenis,
          tanggal: tanggal,
          data: state
        })
      })
        .then(function (res) { return res.json(); })
        .then(function (json) {
          if (json.status) {
            sudahDikirim = true;
            btnKirim.style.display = 'none';
            indikator.classList.remove('badge-success');
            indikator.classList.add('badge-secondary');

            if (json.ringkasan) {
              Object.keys(json.ringkasan).forEach(function (key) {
                var el = document.getElementById('ringkasan-' + key);
                if (el) el.textContent = json.ringkasan[key];
              });
            }

            Swal.fire({
              icon: 'success',
              title: 'Berhasil',
              text: 'Absensi berhasil disimpan'
            });
          } else {
            btnKirim.disabled = false;
            btnKirim.innerHTML = '<i class="fas fa-paper-plane"></i> Kirim Absensi';
            Swal.fire({
              icon: 'error',
              title: 'Gagal',
              text: json.message || 'Absensi gagal disimpan'
            });
          }
        })
        .catch(function () {
          btnKirim.disabled = false;
          btnKirim.innerHTML = '<i class="fas fa-paper-plane"></i> Kirim Absensi';
          Swal.fire({
            icon: 'error',
            title: 'Gagal',
            text: 'Terjadi kesalahan koneksi, silakan coba lagi.'
          });
        });
    });

    updateIndikator();
  })();
</script>
