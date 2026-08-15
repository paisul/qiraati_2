<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
  <div class="content mt-2">
    <div class="container-fluid">
      <div class="row">
        <div class="col-sm">
          <div class="card">
            <div class="card-header bg-success">
              <h4 class="m-0"><?= $title; ?></h4>
            </div>

            <div class="flash-data" data-flashdata="<?= isset($pesan) ? $pesan : $this->session->flashdata('pesan'); ?>" data-title="Pendaftaran Santri">
            </div>

            <div class="card-body">
              <p class="text-muted">Pendaftaran yang masuk lewat halaman publik "Daftar Akun Baru" (Google Sign-In). Setujui untuk membuat data santri resmi (NIS otomatis), atau tolak jika tidak valid.</p>

              <div class="table-responsive">
                <table class="table table-bordered table-striped text-center">
                  <thead>
                    <tr>
                      <th style="width: 50px;">No</th>
                      <th style="width: 90px;">Foto</th>
                      <th>Nama Lengkap</th>
                      <th>Email</th>
                      <th>Kelas</th>
                      <th>Tanggal Daftar</th>
                      <th style="width: 220px;">Aksi</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php $no = 1; foreach ($pendaftaran as $p) : ?>
                      <tr>
                        <td><?= $no++; ?></td>
                        <td>
                          <?php if (!empty($p['Pasfoto'])) : ?>
                            <img src="<?= upload_url('santri', $p['Pasfoto']); ?>" width="50" height="50" class="img-circle" style="object-fit: cover;">
                          <?php else : ?>
                            <div style="width:50px;height:50px;border-radius:50%;background:#172a3a;color:#fff;display:inline-flex;align-items:center;justify-content:center;font-weight:bold;">
                              <?= strtoupper(substr($p['NamaLengkap'], 0, 1)); ?>
                            </div>
                          <?php endif; ?>
                        </td>
                        <td class="text-left"><?= html_escape($p['NamaLengkap']); ?></td>
                        <td>
                          <?= html_escape($p['Email']); ?>
                          <?php if ($this->Santri_M->isEmailTerpakai($p['Email'])) : ?>
                            <br><span class="badge badge-info" title="Email ini sudah dipakai akun Wali lain. Kalau disetujui, santri ini akan otomatis disambungkan ke akun yang sudah ada (bukan bikin akun baru) - cocok kalau ini kakak/adik dari santri yang sudah terdaftar.">
                              <i class="fas fa-info-circle"></i> Email sudah terdaftar - akan disambungkan
                            </span>
                          <?php endif; ?>
                        </td>
                        <td><?= !empty($p['NamaKelas']) ? html_escape($p['NamaKelas'] . (!empty($p['NamaMusyrif']) ? ' - ' . $p['NamaMusyrif'] : '')) : '-'; ?></td>
                        <td><?= html_escape($p['CreatedAt']); ?></td>
                        <td>
                          <a href="<?= base_url('santri/setujui_pendaftaran/' . $p['IdPendaftaran']); ?>" class="btn btn-success btn-sm"><i class="fas fa-check"></i> Setujui</a>
                          <a href="<?= base_url('santri/tolak_pendaftaran/' . $p['IdPendaftaran']); ?>" class="btn btn-danger btn-sm tombol-hapus" tipeData="Pendaftaran" namaData="<?= html_escape($p['NamaLengkap']); ?>"><i class="fas fa-times"></i> Tolak</a>
                        </td>
                      </tr>
                    <?php endforeach; ?>
                    <?php if (empty($pendaftaran)) : ?>
                      <tr>
                        <td colspan="7">Tidak ada pendaftaran yang menunggu persetujuan.</td>
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
