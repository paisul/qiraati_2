<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">

  <!-- Main content -->
  <div class="content mt-2">
    <div class="container-fluid">
      <?php
      $total_laki = array_sum(array_column($kelas, 'JumlahLaki'));
      $total_perempuan = array_sum(array_column($kelas, 'JumlahPerempuan'));
      $label_kelas = array_column($kelas, 'NamaKelas');
      $jumlah_santri_per_kelas = array_map('intval', array_column($kelas, 'JumlahSantri'));
      ?>
      <div class="row">
        <div class="col-md-6">
          <div class="card">
            <div class="card-header bg-info">
              <h5 class="m-0">Perbandingan Santri Laki-laki & Perempuan</h5>
            </div>
            <div class="card-body">
              <canvas id="chartGender" style="min-height: 250px; max-height: 300px;"></canvas>
            </div>
          </div>
        </div>
        <div class="col-md-6">
          <div class="card">
            <div class="card-header bg-info">
              <h5 class="m-0">Perbandingan Jumlah Santri per Kelas</h5>
            </div>
            <div class="card-body">
              <canvas id="chartKelas" style="min-height: 250px; max-height: 300px;"></canvas>
            </div>
          </div>
        </div>
      </div>

      <div class="row">

        <div class="col-sm">
          <div class="card">
            <div class="card-header bg-success">
              <h4 class="m-0"><?= $title; ?></h4>
            </div>

            <div class="flash-data" data-flashdata="<?= isset($pesan) ? $pesan : $this->session->flashdata('pesan'); ?>" data-title="Data Kelas">
            </div>
            <div class="card-body">
              <?php $baca_saja = $user['level'] == 'Wali'; ?>

              <div class="col mb-3">
                <?php if (!$baca_saja) : ?>
                  <a href="<?= base_url('kelas/tambah'); ?>" class="btn btn-primary"><i class="fas fa-plus"></i> Tambah Data</a>
                <?php endif; ?>
                <a href="<?= base_url('kelas/export_excel'); ?>" target="_blank" class="btn btn-primary"><i class="fas fa-file-excel"></i> Export Data</a>
              </div>

              <div class="table-responsive">
                <table id="example2" class="table table-bordered table-striped text-center">
                  <thead>
                    <tr>
                      <th style="width: 50px;">No</th>
                      <th>Nama Kelas</th>
                      <th>Pembimbing</th>
                      <th>Santri (L)</th>
                      <th>Santri (P)</th>
                      <th>Total Santri</th>
                      <th>Lokasi/Ruangan</th>
                      <?php if (!$baca_saja) : ?>
                        <th style="width: 200px;">Aksi</th>
                      <?php endif; ?>
                    </tr>
                  </thead>
                  <tbody>
                    <?php
                    $no = 1;
                    foreach ($kelas as $kls) : ?>
                      <tr>
                        <td><?= $no++; ?></td>
                        <td class="text-left"><?= html_escape($kls['NamaKelas']); ?></td>
                        <td><?= !empty($kls['NamaMusyrif']) ? html_escape($kls['NamaMusyrif']) : '<span class="text-danger">Belum ditentukan</span>'; ?></td>
                        <td><span class="badge badge-primary"><?= (int) $kls['JumlahLaki']; ?></span></td>
                        <td><span class="badge badge-pink" style="background-color:#e83e8c;color:#fff;"><?= (int) $kls['JumlahPerempuan']; ?></span></td>
                        <td><span class="badge badge-info"><?= $kls['JumlahSantri']; ?></span></td>
                        <td><?= !empty($kls['Lokasi']) ? html_escape($kls['Lokasi']) : '-'; ?></td>
                        <?php if (!$baca_saja) : ?>
                          <td>
                            <a href="<?= base_url('kelas/ubah/' . $kls['IdKelas']); ?>" class="btn btn-success"><i class="fas fa-pen"></i> Ubah</a>
                            <?php if ((int) $kls['JumlahSantri'] > 0) : ?>
                              <button type="button" class="btn btn-danger ml-2" data-toggle="modal" data-target="#modalHapusKelas<?= $kls['IdKelas']; ?>"><i class="fas fa-trash"></i></button>
                            <?php else : ?>
                              <a href="<?= base_url('kelas/delete/' . $kls['IdKelas']); ?>" class="btn btn-danger ml-2 tombol-hapus" tipeData="Kelas" namaData="<?= html_escape($kls['NamaKelas']); ?>"><i class="fas fa-trash"></i></a>
                            <?php endif; ?>
                          </td>
                        <?php endif; ?>
                      </tr>
                    <?php endforeach; ?>
                    <?php if (empty($kelas)) : ?>
                      <tr>
                        <td colspan="8">Belum ada data kelas.</td>
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
</div>

<?php foreach ($kelas as $kls) : ?>
  <?php if ((int) $kls['JumlahSantri'] > 0) : ?>
    <div class="modal fade" id="modalHapusKelas<?= $kls['IdKelas']; ?>">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header bg-danger">
            <h4 class="modal-title">Hapus <?= html_escape($kls['NamaKelas']); ?></h4>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <?= form_open('kelas/hapus_dengan_pindah/' . $kls['IdKelas']); ?>
          <div class="modal-body">
            <p>Kelas ini masih punya <strong><?= (int) $kls['JumlahSantri']; ?> santri</strong>. Pilih kelas tujuan untuk memindahkan mereka dulu - tanpa memilih kelas tujuan, kelas ini tidak bisa dihapus.</p>
            <div class="form-group">
              <label>Pindahkan Santri ke Kelas</label>
              <select name="kelas_tujuan" class="form-control" required>
                <option value="">-- Pilih Kelas Tujuan --</option>
                <?php foreach ($kelas as $tujuan) : ?>
                  <?php if ($tujuan['IdKelas'] != $kls['IdKelas']) : ?>
                    <option value="<?= $tujuan['IdKelas']; ?>"><?= html_escape($tujuan['NamaKelas']); ?><?= !empty($tujuan['NamaMusyrif']) ? ' - ' . html_escape($tujuan['NamaMusyrif']) : ''; ?></option>
                  <?php endif; ?>
                <?php endforeach; ?>
              </select>
            </div>
          </div>
          <div class="modal-footer justify-content-between">
            <button type="button" class="btn btn-default" data-dismiss="modal">Batal</button>
            <button type="submit" class="btn btn-danger"><i class="fas fa-exchange-alt"></i> Pindahkan &amp; Hapus Kelas</button>
          </div>
          <?= form_close(); ?>
        </div>
      </div>
    </div>
  <?php endif; ?>
<?php endforeach; ?>

<script>
  $(function() {
    // Tampilkan jumlah di legend ("Label: angka") & tooltip, bukan cuma proporsi visual.
    function opsiPieDenganAngka() {
      return {
        responsive: true,
        maintainAspectRatio: false,
        legend: {
          labels: {
            generateLabels: function(chart) {
              var data = chart.data;
              var dataset = data.datasets[0];
              return data.labels.map(function(label, i) {
                return {
                  text: label + ': ' + dataset.data[i],
                  fillStyle: dataset.backgroundColor[i],
                  index: i,
                };
              });
            },
          },
        },
        tooltips: {
          callbacks: {
            label: function(tooltipItem, data) {
              var label = data.labels[tooltipItem.index];
              var value = data.datasets[0].data[tooltipItem.index];
              return label + ': ' + value;
            },
          },
        },
      };
    }

    new Chart(document.getElementById('chartGender'), {
      type: 'pie',
      data: {
        labels: ['Laki-laki', 'Perempuan'],
        datasets: [{
          data: [<?= (int) $total_laki; ?>, <?= (int) $total_perempuan; ?>],
          backgroundColor: ['#007bff', '#e83e8c'],
        }],
      },
      options: opsiPieDenganAngka(),
    });

    new Chart(document.getElementById('chartKelas'), {
      type: 'pie',
      data: {
        labels: <?= json_encode($label_kelas); ?>,
        datasets: [{
          data: <?= json_encode($jumlah_santri_per_kelas); ?>,
          backgroundColor: ['#007bff', '#28a745', '#ffc107', '#e83e8c', '#17a2b8', '#6f42c1', '#fd7e14', '#20c997'],
        }],
      },
      options: opsiPieDenganAngka(),
    });
  });
</script>
