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
            <!-- Swall -->
            <div class="flash-data" data-flashdata="<?= isset($pesan) ? $pesan : $this->session->flashdata('pesan'); ?>" data-title="Data Santri">
            </div>
            <div class="card-body">
              <!-- Add/Import/Export -->
              <div class="col mb-3">
                <a href="<?= base_url('santri/tambah'); ?>" class="btn btn-primary"><i class="fas fa-plus"></i> Tambah Data</a>
                <button type="button" class="btn btn-secondary" data-toggle="modal" data-target="#importSiswa"><i class="fas fa-file-import"></i> Import</button>
                <a href="<?= base_url('santri/export_excel'); ?>" target="_blank" class="btn btn-primary"><i class="fas fa-file-excel"></i> Export Data</a>
              </div>

              <!-- Form Pencarian -->
              <div class="row" style="text-align: right;">
                <div class="col-sm-9">
                </div>
                <div class="col-sm-3">
                  <form class="form-inline" action="<?= base_url('Santri/cari_data'); ?>" method="POST">
                    <label class="sr-only" for="inlineFormInputName2">Cari</label>
                    <input type="text" class="form-control mb-2 mr-sm-2" id="inlineFormInputName2" placeholder="Masukkan Nama Santri" name="nama_santri">
                    <button type="submit" class="btn btn-primary mb-2"><i class="fas fa-fw fa-search"></i> Cari</button>
                  </form>
                </div>
              </div>

              <div class="table-responsive">
                <table id="example2" class="table table-bordered table-striped text-center">
                  <thead>
                    <tr>
                      <th style="width: 50px;">No</th>
                      <th style="width: 90px;">Pasfoto</th>
                      <th style="width: 100px;">NIS</th>
                      <th>Nama Lengkap</th>
                      <th>Jenis Kelamin</th>
                      <th>Status</th>
                      <th>Kelas</th>
                      <th style="width: 260px;">Aksi</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php
                    $no = 1;
                    foreach ($santri as $san) : ?>
                      <tr>
                        <td><?= $no++; ?></td>
                        <td>
                          <?php if (!empty($san['Pasfoto'])) : ?>
                            <img src="<?= upload_url('santri', $san['Pasfoto']); ?>" width="64" height="64" class="img-circle" style="object-fit: cover; object-position: center top;">
                          <?php else : ?>
                            <div style="width:64px;height:64px;border-radius:50%;background:#172a3a;color:#fff;display:inline-flex;align-items:center;justify-content:center;font-weight:bold;">
                              <?= strtoupper(substr($san['NamaLengkap'], 0, 1)); ?>
                            </div>
                          <?php endif; ?>
                        </td>
                        <td><?= $san['NIS']; ?></td>
                        <td class="text-left"><?= $san['NamaLengkap']; ?></td>
                        <td><?= !empty($san['JenisKelamin']) ? $san['JenisKelamin'] : '-'; ?></td>
                        <td>
                          <?php
                          $warna_status = ['Aktif' => '#28a745', 'Lulus' => '#17a2b8', 'Pindah' => '#ffc107', 'Berhenti' => '#dc3545'];
                          $warna = isset($warna_status[$san['Status']]) ? $warna_status[$san['Status']] : '#6c757d';
                          ?>
                          <?= form_open('santri/ubah_status/' . $san['IdSiswa'], ['class' => 'form-inline justify-content-center']); ?>
                          <select name="status" class="form-control form-control-sm" onchange="this.form.submit()" style="min-width: 105px; background-color: <?= $warna; ?>; color: #fff; font-weight: bold;">
                            <?php foreach (['Aktif', 'Lulus', 'Pindah', 'Berhenti'] as $st) : ?>
                              <option value="<?= $st; ?>" <?= $st === $san['Status'] ? 'selected' : ''; ?>><?= $st; ?></option>
                            <?php endforeach; ?>
                          </select>
                          <?= form_close(); ?>
                        </td>
                        <td><?= !empty($san['NamaKelas']) ? html_escape($san['NamaKelas']) : '-'; ?></td>
                        <td>
                          <a href="<?= base_url('santri/detail/' . $san['IdSiswa']); ?>" class="btn btn-info"><i class="fas fa-eye"></i> Detail</a>
                          <a href="<?= base_url('santri/ubah/' . $san['IdSiswa']); ?>" class="btn btn-success"><i class="fas fa-pen"></i> Ubah</a>
                          <a href="<?= base_url('santri/delete/' . $san['IdSiswa']); ?>" class="btn btn-danger tombol-hapus" tipeData="Santri" namaData=<?= $san['NIS']; ?>><i class="fas fa-trash"></i></a>
                        </td>
                      </tr>
                    <?php endforeach; ?>
                    <?php if (empty($santri)) : ?>
                      <tr>
                        <td colspan="8">Belum ada data santri.</td>
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

<!-- Modal ImportSiswa -->
<div class="modal fade" id="importSiswa">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header bg-success">
        <h4 class="modal-title">Import Data Santri</h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <p class="mb-2">Gunakan hasil <strong>Export Data</strong> sebagai template: isi kolom Password yang kosong untuk tiap baris, lalu upload kembali file itu. Urutan kolom mengikuti Formulir Tambah Data Santri:</p>
        <p class="text-muted" style="font-size: 12px;">
          No, NIS <em>(diabaikan, dibuat otomatis)</em>, Nama Lengkap, Jenis Kelamin (Laki-laki/Perempuan), Tempat Lahir, Tanggal Lahir (YYYY-MM-DD), Email Login, Password (min 8 karakter), Kelas (format: NamaKelas - Pembimbing), Nama Ayah, Nama Ibu, Alamat Lengkap, Sekolah Akademik, Sekolah Tadika, Nomor ID Card, Tanggal Mulai Belajar (YYYY-MM-DD). <em>(Status Santri otomatis "Aktif", ubah lewat halaman Data Santri.)</em>
        </p>
        <?= form_open_multipart('santri/import'); ?>
        <div class="form-group">
          <label for="importSiswa">Pilih File</label>
          <small>(.xls/.xlsx)</small>
          <div class="custom-file">
            <input type="file" class="custom-file-input" id="import" name="importSiswa" accept=".xls,.xlsx" onchange="previewImg()">
            <label class="custom-file-label" for="import">Pilih File</label>
          </div>
        </div>
      </div>
      <div class="modal-footer justify-content-between">
        <button type="button" class="btn btn-default" data-dismiss="modal">Batal</button>
        <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Import</button>
      </div>
      <?= form_close(); ?>
    </div>
    <!-- /.modal-content -->
  </div>
  <!-- /.modal-dialog -->
</div>
<!-- /.modal -->
