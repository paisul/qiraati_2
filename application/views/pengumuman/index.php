<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">

  <!-- Main content -->
  <div class="content mt-2">
    <div class="container-fluid">
      <div class="row">

        <!-- /.col-md-6 -->
        <div class="col-sm">
          <div class="card">
            <div class="card-header bg-success">
              <h4 class="m-0"><?= $title; ?></h4>
            </div>

            <!-- Swall -->
            <div class="flash-data" data-flashdata="<?= isset($pesan) ? $pesan : $this->session->flashdata('pesan'); ?>" data-title="Info Penting">
            </div>
            <div class="card-body">
              <div class="col mb-3">
                <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#addPengumuman"><i class="fas fa-plus"></i> Tambah Pengumuman</button>
              </div>

              <table id="example2" class="table table-striped">
                <thead>
                  <tr>
                    <th style="width: 50px;">No</th>
                    <th>Judul</th>
                    <th>Isi</th>
                    <th style="width: 150px;">Diposting</th>
                    <th style="width: 200px;">Aksi</th>
                  </tr>
                </thead>
                <tbody>
                  <?php
                  $no = 1;
                  foreach ($pengumuman as $p) : ?>
                    <tr>
                      <td><?= $no++; ?></td>
                      <td><?= html_escape($p['Judul']); ?></td>
                      <td style="white-space:pre-line;"><?= html_escape($p['Isi']); ?></td>
                      <td><?= html_escape($p['DibuatOleh']); ?><br><small><?= date('d F Y, H:i', strtotime($p['CreatedAt'])); ?></small></td>
                      <td>
                        <button class="btn btn-success" data-toggle="modal" data-target="#editPengumuman<?= $p['IdPengumuman']; ?>">Ubah</button>
                        <a href="<?= base_url('pengumuman/delete/' . $p['IdPengumuman']); ?>" class="btn btn-danger ml-3 tombol-hapus" tipeData="Pengumuman" namaData="<?= html_escape($p['Judul']); ?>">Hapus</a>
                      </td>
                    </tr>
                  <?php endforeach; ?>
                </tbody>
              </table>

            </div>
          </div>

        </div>
        <!-- /.col-md-6 -->
      </div>
      <!-- /.row -->
    </div><!-- /.container-fluid -->
  </div>
  <!-- /.content -->
</div>
<!-- /.content-wrapper -->

<!-- Modal AddPengumuman -->
<div class="modal fade" id="addPengumuman">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header bg-success">
        <h4 class="modal-title">Tambah Pengumuman</h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <?= form_open('pengumuman/add'); ?>
        <div class="form-group">
          <label for="judul">Judul</label>
          <input type="text" class="form-control" id="judul" name="judul" required autocomplete="off">
        </div>
        <div class="form-group">
          <label for="isi">Isi</label>
          <textarea class="form-control" id="isi" name="isi" rows="4" required></textarea>
        </div>
      </div>
      <div class="modal-footer justify-content-between">
        <button type="button" class="btn btn-default" data-dismiss="modal">Batal</button>
        <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Simpan</button>
      </div>
      <?= form_close(); ?>
    </div>
    <!-- /.modal-content -->
  </div>
  <!-- /.modal-dialog -->
</div>
<!-- /.modal -->

<!-- Modal EditPengumuman -->
<?php foreach ($pengumuman as $p) : ?>
  <div class="modal fade" id="editPengumuman<?= $p['IdPengumuman']; ?>">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header bg-success">
          <h4 class="modal-title">Ubah Pengumuman</h4>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <?= form_open('pengumuman/update/' . $p['IdPengumuman']); ?>
          <div class="form-group">
            <label for="judul">Judul</label>
            <input type="text" class="form-control" name="judul" value="<?= html_escape($p['Judul']); ?>" required autocomplete="off">
          </div>
          <div class="form-group">
            <label for="isi">Isi</label>
            <textarea class="form-control" name="isi" rows="4" required><?= html_escape($p['Isi']); ?></textarea>
          </div>
        </div>
        <div class="modal-footer justify-content-between">
          <button type="button" class="btn btn-default" data-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Simpan</button>
        </div>
        <?= form_close(); ?>
      </div>
      <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
  </div>
<?php endforeach; ?>
<!-- /.modal -->
