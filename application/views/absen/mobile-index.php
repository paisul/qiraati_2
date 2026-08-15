<div class="m-content m-content-pad-bottombar">
  <div class="m-absen-filter">
    <a href="<?= base_url('absen/santri?tanggal=' . $tanggal); ?>" class="m-chip-toggle <?= $jenis == 'santri' ? 'active' : ''; ?>">
      <i class="fas fa-user-graduate"></i> Santri
    </a>
    <a href="<?= base_url('absen/pembimbing?tanggal=' . $tanggal); ?>" class="m-chip-toggle <?= $jenis == 'pembimbing' ? 'active' : ''; ?>">
      <i class="fas fa-chalkboard-teacher"></i> Pembimbing
    </a>
  </div>

  <div class="m-card">
    <form action="<?= base_url('absen/' . $jenis); ?>" id="formFilterTanggal">
      <div class="m-field" style="margin-bottom: 0;">
        <label>Tanggal</label>
        <input type="date" name="tanggal" value="<?= $tanggal; ?>">
      </div>
    </form>
  </div>

  <div class="m-stat-row">
    <div class="m-card m-stat-success" style="padding: 10px;">
      <p class="m-card-title" style="font-size:11px;">Hadir</p>
      <p class="m-card-value" style="font-size:20px;" id="ringkasan-Hadir"><?= $ringkasan['Hadir']; ?></p>
    </div>
    <div class="m-card" style="padding: 10px;">
      <p class="m-card-title" style="font-size:11px;">Sakit</p>
      <p class="m-card-value" style="font-size:20px; color:#b98900;" id="ringkasan-Sakit"><?= $ringkasan['Sakit']; ?></p>
    </div>
    <div class="m-card" style="padding: 10px;">
      <p class="m-card-title" style="font-size:11px;">Izin</p>
      <p class="m-card-value" style="font-size:20px; color:#17a2b8;" id="ringkasan-Izin"><?= $ringkasan['Izin']; ?></p>
    </div>
    <div class="m-card m-stat-danger" style="padding: 10px;">
      <p class="m-card-title" style="font-size:11px;">Alpa</p>
      <p class="m-card-value" style="font-size:20px;" id="ringkasan-Alpa"><?= $ringkasan['Alpa']; ?></p>
    </div>
  </div>

  <?php
  $no = 1;
  $warna_status = ['Hadir' => 'success', 'Sakit' => 'warning', 'Izin' => 'info', 'Alpa' => 'danger'];
  foreach ($peserta as $row) :
    $status = isset($status_awal[$row['id']]) ? $status_awal[$row['id']] : '';
    $badge = isset($warna_status[$status]) ? $warna_status[$status] : 'secondary';
    $inisial = strtoupper(substr($row['nama'], 0, 1));
    $diajukan_wali = !empty($absen[$row['id']]['DiajukanOlehWali']);
  ?>
    <div class="m-card">
      <div class="m-absen-card">
        <?php if (!empty($row['pasfoto'])) :
          $folderFoto = $jenis == 'santri' ? 'santri' : 'pasfoto_musyrif';
        ?>
          <img src="<?= upload_url($folderFoto, $row['pasfoto']); ?>" class="m-absen-photo">
        <?php else : ?>
          <div class="m-absen-photo-fallback"><?= $inisial; ?></div>
        <?php endif; ?>
        <div class="m-absen-info">
          <div class="m-absen-nama"><?= html_escape($row['nama']); ?></div>
          <div class="m-absen-sub"><?= $jenis == 'santri' ? 'NIS: ' : ''; ?><?= html_escape($row['nomor']); ?></div>
          <span class="badge status-badge badge-<?= $badge; ?>"><?= $status !== '' ? $status : 'Belum Absen'; ?></span>
          <?php if ($diajukan_wali) : ?>
            <span class="badge badge-secondary m-badge-diajukan-wali" style="background:#6f42c1;"><i class="fas fa-user-friends"></i> Diajukan Wali</span>
          <?php endif; ?>
        </div>
      </div>

      <?php if ($diajukan_wali && !empty($absen[$row['id']]['Keterangan'])) : ?>
        <div class="m-keterangan-wali" style="margin-top:8px; padding:8px 10px; background:#f4f0fb; border-radius:8px; font-size:12px; color:#4a3a6b;">
          <i class="fas fa-quote-left" style="margin-right:4px;"></i><?= html_escape($absen[$row['id']]['Keterangan']); ?>
        </div>
      <?php endif; ?>

      <div class="btn-group-absen" data-id-peserta="<?= $row['id']; ?>">
        <?php foreach ($warna_status as $label => $warna) :
          $aktif = $status === $label;
        ?>
          <button type="button"
            class="btn btn-absen <?= $aktif ? 'btn-' . $warna : 'btn-outline-' . $warna; ?>"
            data-id="<?= $row['id']; ?>"
            data-status="<?= $label; ?>"
            data-warna="<?= $warna; ?>"><?= $label; ?></button>
        <?php endforeach; ?>
        <button type="button"
          class="btn btn-outline-dark btn-reset-absen"
          data-id="<?= $row['id']; ?>"
          style="<?= $status === '' ? 'display:none;' : ''; ?>">Belum Absen</button>
      </div>
    </div>
  <?php endforeach; ?>

  <?php if (empty($peserta)) : ?>
    <p class="m-empty">Belum ada data.</p>
  <?php endif; ?>
</div>

<div class="m-bottombar" id="kirimAbsensiWrapper">
  <span id="indikatorAbsensi" class="badge badge-secondary"></span>
  <button type="button" id="btnKirimAbsensi" class="m-btn" style="display:none;">
    <i class="fas fa-paper-plane"></i> Kirim Absensi
  </button>
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
        var kartu = btn.closest('.m-card');
        var badge = kartu ? kartu.querySelector('.status-badge') : null;
        var tombolReset = group.querySelector('.btn-reset-absen');

        state[id] = status;
        tandaiTombolAktif(group, status);

        if (badge) {
          badge.textContent = status;
          badge.className = 'badge status-badge badge-' + warna;
        }

        // Klik status manual berarti Musyrif mengambil alih - label "Diajukan Wali" & keterangannya
        // akan dibersihkan di server saat "Kirim Absensi" (lihat Absen_M::simpanAbsen()), sembunyikan
        // juga di tampilan supaya tidak membingungkan sebelum benar-benar dikirim.
        if (kartu) {
          var badgeWali = kartu.querySelector('.m-badge-diajukan-wali');
          if (badgeWali) badgeWali.style.display = 'none';
          var keteranganWali = kartu.querySelector('.m-keterangan-wali');
          if (keteranganWali) keteranganWali.style.display = 'none';
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
        var kartu = btn.closest('.m-card');
        var badge = kartu ? kartu.querySelector('.status-badge') : null;

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
