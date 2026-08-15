<?php
$tampil = function ($val) {
  return ($val === null || $val === '') ? '-' : html_escape($val);
};
?>
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
  <div class="content mt-2">
    <div class="container-fluid">
      <div class="row">
        <div class="col-12">
          <div class="card">
            <div class="card-header bg-success d-flex justify-content-between align-items-center">
              <h4 class="m-0"><?= $title; ?></h4>
              <div>
                <a href="<?= base_url('santri/ubah/' . $santri['IdSiswa']); ?>" class="btn btn-sm btn-light"><i class="fas fa-pen"></i> Ubah</a>
                <a href="<?= base_url('santri'); ?>" class="btn btn-sm btn-light"><i class="fas fa-arrow-left"></i> Kembali</a>
              </div>
            </div>
            <div class="card-body">
              <div class="row mb-4">
                <div class="col-md-2 text-center">
                  <?php if (!empty($santri['Pasfoto'])) : ?>
                    <img src="<?= upload_url('santri', $santri['Pasfoto']); ?>" width="120" height="120" class="img-circle" style="object-fit: cover;">
                  <?php else : ?>
                    <div style="width:120px;height:120px;border-radius:50%;background:#172a3a;color:#fff;display:inline-flex;align-items:center;justify-content:center;font-weight:bold;font-size:36px;">
                      <?= strtoupper(substr($santri['NamaLengkap'], 0, 1)); ?>
                    </div>
                  <?php endif; ?>
                </div>
                <div class="col-md-10">
                  <h3 class="mb-0"><?= html_escape($santri['NamaLengkap']); ?></h3>
                  <p class="text-muted mb-0">NIS: <?= html_escape($santri['NIS']); ?></p>
                  <?php
                  $warna_status = ['Aktif' => 'success', 'Lulus' => 'info', 'Pindah' => 'warning', 'Berhenti' => 'danger'];
                  $warna = isset($warna_status[$santri['Status']]) ? $warna_status[$santri['Status']] : 'secondary';
                  ?>
                  <span class="badge badge-<?= $warna; ?> mt-2"><?= $santri['Status']; ?></span>
                </div>
              </div>

              <h5 class="border-bottom pb-2">Data Wajib</h5>
              <div class="row mb-4">
                <div class="col-md-4 mb-2"><strong>Jenis Kelamin</strong><br><?= $tampil($santri['JenisKelamin']); ?></div>
                <div class="col-md-4 mb-2"><strong>Tempat Lahir</strong><br><?= $tampil($santri['TempatLahir']); ?></div>
                <div class="col-md-4 mb-2"><strong>Tanggal Lahir</strong><br><?= $tampil($santri['TanggalLahir']); ?></div>
                <div class="col-md-4 mb-2"><strong>Kelas</strong><br><?= $tampil(isset($santri['NamaKelas']) ? $santri['NamaKelas'] : null); ?></div>
                <div class="col-md-4 mb-2"><strong>Email Login</strong><br><?= $tampil($santri['login']['username'] ?? null); ?></div>
              </div>

              <h5 class="border-bottom pb-2">Data Opsional</h5>
              <div class="row">
                <div class="col-md-4 mb-2"><strong>Nama Ayah</strong><br><?= $tampil($santri['NamaAyah']); ?></div>
                <div class="col-md-4 mb-2"><strong>Nama Ibu</strong><br><?= $tampil($santri['NamaIbu']); ?></div>
                <div class="col-md-4 mb-2"><strong>Nomor ID Card</strong><br><?= $tampil($santri['NoIDCard']); ?></div>
                <div class="col-md-8 mb-2"><strong>Alamat Lengkap</strong><br><?= nl2br($tampil($santri['Alamat'])); ?></div>
                <div class="col-md-4 mb-2"><strong>Sekolah Akademik</strong><br><?= $tampil($santri['SekolahAkademik']); ?></div>
                <div class="col-md-4 mb-2"><strong>Sekolah Tadika</strong><br><?= $tampil($santri['SekolahTadika']); ?></div>
                <div class="col-md-4 mb-2"><strong>Tanggal Mulai Belajar</strong><br><?= $tampil($santri['TglMulaiBelajar']); ?></div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
