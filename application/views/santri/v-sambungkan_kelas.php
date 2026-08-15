<div class="content-wrapper">
  <div class="content mt-2">
    <div class="container-fluid">
      <div class="row">
        <div class="col-sm">
          <div class="card">
            <div class="card-header bg-warning">
              <h4 class="m-0"><?= $title; ?></h4>
            </div>

            <div class="flash-data" data-flashdata="<?= isset($pesan) ? $pesan : $this->session->flashdata('pesan'); ?>" data-title="Sambungkan Kelas Santri"></div>

            <div class="card-body">
              <div class="alert alert-warning">
                Halaman ini untuk santri yang kelasnya sudah terlanjur terhapus (jadi tidak muncul kelasnya di Data Santri). Pilih santri yang mau disambungkan, lalu pilih kelas tujuannya.
              </div>

              <?php if ($santri_yatim) : ?>
                <?= form_open('santri/proses_sambungkan_kelas'); ?>

                <div class="form-group">
                  <div class="custom-control custom-checkbox">
                    <input type="checkbox" class="custom-control-input" id="cekSemuaYatim">
                    <label class="custom-control-label" for="cekSemuaYatim">Pilih Semua (<?= count($santri_yatim); ?> santri)</label>
                  </div>
                </div>

                <table class="table table-striped table-bordered">
                  <thead>
                    <tr>
                      <th style="width: 40px;"></th>
                      <th>NIS</th>
                      <th>Nama Lengkap</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php foreach ($santri_yatim as $s) : ?>
                      <tr>
                        <td><input type="checkbox" class="cekYatimBaris" name="id_siswa[]" value="<?= $s['IdSiswa']; ?>"></td>
                        <td><?= html_escape($s['NIS']); ?></td>
                        <td><?= html_escape($s['NamaLengkap']); ?></td>
                      </tr>
                    <?php endforeach; ?>
                  </tbody>
                </table>

                <div class="form-group">
                  <label>Sambungkan ke Kelas</label>
                  <select name="kelas" class="form-control" required>
                    <option value="">-- Pilih Kelas --</option>
                    <?php foreach ($kelas as $kls) : ?>
                      <option value="<?= $kls['IdKelas']; ?>"><?= html_escape($kls['NamaKelas']); ?><?= !empty($kls['NamaMusyrif']) ? ' - ' . html_escape($kls['NamaMusyrif']) : ''; ?></option>
                    <?php endforeach; ?>
                  </select>
                </div>

                <button type="submit" class="btn btn-primary"><i class="fas fa-link"></i> Sambungkan Santri Terpilih</button>
                <?= form_close(); ?>
              <?php else : ?>
                <p class="text-muted">Tidak ada santri yang perlu disambungkan - semua santri sudah punya kelas.</p>
              <?php endif; ?>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
  var cekSemuaYatim = document.getElementById('cekSemuaYatim');
  if (cekSemuaYatim) {
    cekSemuaYatim.addEventListener('change', function () {
      document.querySelectorAll('.cekYatimBaris').forEach(function (cb) {
        cb.checked = cekSemuaYatim.checked;
      });
    });
  }
</script>
