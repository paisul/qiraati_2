<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">

  <!-- Main content -->
  <div class="content mt-2">
    <div class="container-fluid">
      <div class="row">
        <div class="col-sm">
          <div class="card">
            <div class="card-header bg-success">
              <h4 class="m-0"><?= $title; ?></h4>
            </div>

            <div class="flash-data" data-flashdata="<?= $pesan ?? ''; ?>" data-title="Sumbangan">
            </div>

            <div class="card-body">
              <div class="row mb-3">
                <div class="col-md-4 mb-2">
                  <div class="info-box bg-success">
                    <span class="info-box-icon"><i class="fas fa-arrow-down"></i></span>
                    <div class="info-box-content">
                      <span class="info-box-text">Total Masuk</span>
                      <span class="info-box-number"><?= number_format($ringkasan['total_masuk'], 0, ',', '.'); ?></span>
                    </div>
                  </div>
                </div>
                <div class="col-md-4 mb-2">
                  <div class="info-box bg-danger">
                    <span class="info-box-icon"><i class="fas fa-arrow-up"></i></span>
                    <div class="info-box-content">
                      <span class="info-box-text">Total Keluar</span>
                      <span class="info-box-number"><?= number_format($ringkasan['total_keluar'], 0, ',', '.'); ?></span>
                    </div>
                  </div>
                </div>
                <div class="col-md-4 mb-2">
                  <div class="info-box bg-primary">
                    <span class="info-box-icon"><i class="fas fa-wallet"></i></span>
                    <div class="info-box-content">
                      <span class="info-box-text">Saldo</span>
                      <span class="info-box-number"><?= number_format($ringkasan['saldo'], 0, ',', '.'); ?></span>
                    </div>
                  </div>
                </div>
              </div>

              <?php $baca_saja = $user['level'] == 'Wali'; ?>

              <?php if (!$baca_saja) : ?>
                <div class="col mb-3">
                  <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#addDana"><i class="fas fa-plus"></i> Tambah Data</button>
                </div>
              <?php endif; ?>

              <table id="example2" class="table table-bordered table-striped text-center">
                <thead>
                  <tr>
                    <th style="width: 50px;">No</th>
                    <th>Tanggal</th>
                    <th>Perihal</th>
                    <th>Jumlah Masuk</th>
                    <th>Jumlah Keluar</th>
                    <?php if (!$baca_saja) : ?>
                      <th style="width: 200px;">Aksi</th>
                    <?php endif; ?>
                  </tr>
                </thead>
                <tbody>
                  <?php
                  $no = 1;
                  foreach ($dana as $dn) : ?>
                    <tr>
                      <td><?= $no++; ?></td>
                      <td><?= date_indo($dn['Tanggal']); ?></td>
                      <td class="text-left"><?= html_escape($dn['Perihal']); ?></td>
                      <td class="text-right"><?= number_format($dn['JumlahMasuk'], 0, ',', '.'); ?></td>
                      <td class="text-right"><?= number_format($dn['JumlahKeluar'], 0, ',', '.'); ?></td>
                      <?php if (!$baca_saja) : ?>
                        <td>
                          <button class="btn btn-success" data-toggle="modal" data-target="#editDana<?= $dn['IdSumbangan']; ?>">Ubah</button>
                          <a href="<?= base_url('sumbangan/delete/' . $dn['IdSumbangan']); ?>" class="btn btn-danger ml-3 tombol-hapus" tipeData="Sumbangan" namaData="<?= html_escape($dn['Perihal']); ?>">Hapus</a>
                        </td>
                      <?php endif; ?>
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

<?php if (!$baca_saja) : ?>
<!-- Modal AddDana -->
<div class="modal fade" id="addDana">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header bg-success">
        <h4 class="modal-title">Tambah Sumbangan</h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <?= form_open('sumbangan/add'); ?>
        <div class="form-group">
          <label for="tanggal">Tanggal</label>
          <input type="date" class="form-control" id="tanggal" name="tanggal" value="<?= date('Y-m-d'); ?>" required>
        </div>
        <div class="form-group">
          <label for="perihal">Perihal</label>
          <input type="text" class="form-control" id="perihal" name="perihal" placeholder="Masukkan perihal sumbangan" required autocomplete="off">
        </div>
        <div class="form-group">
          <label for="jumlah_masuk">Jumlah Masuk</label>
          <input type="number" class="form-control" id="jumlah_masuk" name="jumlah_masuk" value="0" min="0" step="1" oninput="this.value = Math.max(0, this.value || 0)" required>
          <small class="text-muted">Minimal 0</small>
        </div>
        <div class="form-group">
          <label for="jumlah_keluar">Jumlah Keluar</label>
          <input type="number" class="form-control" id="jumlah_keluar" name="jumlah_keluar" value="0" min="0" step="1" oninput="this.value = Math.max(0, this.value || 0)" required>
          <small class="text-muted">Minimal 0</small>
        </div>
      </div>
      <div class="modal-footer justify-content-between">
        <button type="button" class="btn btn-default" data-dismiss="modal">Batal</button>
        <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Simpan</button>
      </div>
      <?= form_close(); ?>
    </div>
  </div>
</div>

<!-- Modal EditDana -->
<?php foreach ($dana as $dn) : ?>
  <div class="modal fade" id="editDana<?= $dn['IdSumbangan']; ?>">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header bg-success">
          <h4 class="modal-title">Ubah Sumbangan</h4>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <?= form_open('sumbangan/update/' . $dn['IdSumbangan']); ?>
          <div class="form-group">
            <label for="tanggal<?= $dn['IdSumbangan']; ?>">Tanggal</label>
            <input type="date" class="form-control" id="tanggal<?= $dn['IdSumbangan']; ?>" name="tanggal" value="<?= $dn['Tanggal']; ?>" required>
          </div>
          <div class="form-group">
            <label for="perihal<?= $dn['IdSumbangan']; ?>">Perihal</label>
            <input type="text" class="form-control" id="perihal<?= $dn['IdSumbangan']; ?>" name="perihal" value="<?= html_escape($dn['Perihal']); ?>" required autocomplete="off">
          </div>
          <div class="form-group">
            <label for="jumlah_masuk<?= $dn['IdSumbangan']; ?>">Jumlah Masuk</label>
            <input type="number" class="form-control" id="jumlah_masuk<?= $dn['IdSumbangan']; ?>" name="jumlah_masuk" value="<?= $dn['JumlahMasuk']; ?>" min="0" step="1" oninput="this.value = Math.max(0, this.value || 0)" required>
            <small class="text-muted">Minimal 0</small>
          </div>
          <div class="form-group">
            <label for="jumlah_keluar<?= $dn['IdSumbangan']; ?>">Jumlah Keluar</label>
            <input type="number" class="form-control" id="jumlah_keluar<?= $dn['IdSumbangan']; ?>" name="jumlah_keluar" value="<?= $dn['JumlahKeluar']; ?>" min="0" step="1" oninput="this.value = Math.max(0, this.value || 0)" required>
            <small class="text-muted">Minimal 0</small>
          </div>
        </div>
        <div class="modal-footer justify-content-between">
          <button type="button" class="btn btn-default" data-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-success">Ubah</button>
        </div>
        <?= form_close(); ?>
      </div>
    </div>
  </div>
<?php endforeach; ?>
<?php endif; ?>
