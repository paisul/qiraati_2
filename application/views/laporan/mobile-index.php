<div class="m-content">
  <p class="m-page-title">Laporan Absensi <?= $jenis == 'santri' ? 'Santri' : 'Pembimbing'; ?></p>

  <?php if ($is_wali) : ?>
    <div class="m-card">
      <p style="margin:0; color:#6c757d; font-size:13px;">Rekap kehadiran anak Anda. Hubungi admin jika ingin melihat/mengubah data lain.</p>
    </div>
  <?php else : ?>
    <div class="m-absen-filter">
      <a href="<?= base_url('laporan/santri?tanggal_awal=' . $tanggal_awal . '&tanggal_akhir=' . $tanggal_akhir); ?>" class="m-chip-toggle <?= $jenis == 'santri' ? 'active' : ''; ?>">
        <i class="fas fa-user-graduate"></i> Santri
      </a>
      <a href="<?= base_url('laporan/pembimbing?tanggal_awal=' . $tanggal_awal . '&tanggal_akhir=' . $tanggal_akhir); ?>" class="m-chip-toggle <?= $jenis == 'pembimbing' ? 'active' : ''; ?>">
        <i class="fas fa-chalkboard-teacher"></i> Pembimbing
      </a>
    </div>
  <?php endif; ?>

  <div class="m-card">
    <form action="<?= base_url('laporan/' . $jenis); ?>" id="formFilterLaporan">
      <div class="m-field">
        <label>Dari Tanggal</label>
        <input type="date" name="tanggal_awal" value="<?= $tanggal_awal; ?>">
      </div>
      <div class="m-field">
        <label>Sampai Tanggal</label>
        <input type="date" name="tanggal_akhir" value="<?= $tanggal_akhir; ?>">
      </div>

      <?php if ($jenis == 'santri' && !$is_wali) : ?>
        <div class="m-field">
          <label>Cari NIS</label>
          <input type="text" name="nis" value="<?= html_escape($nis); ?>" placeholder="Cari NIS">
        </div>

        <button type="button" class="m-btn m-btn-outline" data-toggle-target="#panelFilterLanjutan">
          <i class="fas fa-sliders-h"></i> Filter Lanjutan<?= ($id_kelas || $gender || $urutkan) ? ' (aktif)' : ''; ?>
        </button>

        <div class="m-form-panel" id="panelFilterLanjutan" <?= $filter_lanjutan_aktif ? '' : 'hidden'; ?>>
          <div class="m-field">
            <label>Kelas</label>
            <select name="id_kelas">
              <option value="">-- Semua Kelas --</option>
              <?php foreach ($kelas as $row) : ?>
                <option value="<?= $row['IdKelas']; ?>" <?= (string) $id_kelas === (string) $row['IdKelas'] ? 'selected' : ''; ?>><?= html_escape($row['NamaKelas']); ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="m-field">
            <label>Gender</label>
            <select name="gender">
              <option value="">-- Semua Gender --</option>
              <?php foreach ($jenis_kelamin_list as $jk) : ?>
                <option value="<?= $jk; ?>" <?= (string) $gender === (string) $jk ? 'selected' : ''; ?>><?= $jk; ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="m-field">
            <label>Urutkan Berdasarkan</label>
            <select name="urutkan">
              <option value="">-- Nama (default) --</option>
              <option value="kelas" <?= $urutkan === 'kelas' ? 'selected' : ''; ?>>Kelas</option>
              <option value="gender" <?= $urutkan === 'gender' ? 'selected' : ''; ?>>Gender</option>
              <option value="usia" <?= $urutkan === 'usia' ? 'selected' : ''; ?>>Usia</option>
            </select>
          </div>
        </div>
      <?php endif; ?>

      <button type="submit" class="m-btn mt-2">Tampilkan</button>
      <?php if ($id_kelas || $gender || $urutkan || $nis) : ?>
        <a href="<?= base_url('laporan/' . $jenis . '?tanggal_awal=' . $tanggal_awal . '&tanggal_akhir=' . $tanggal_akhir); ?>" class="m-btn m-btn-outline mt-2"><i class="fas fa-times"></i> Reset Filter</a>
      <?php endif; ?>
    </form>
  </div>

  <?php if ($rekap) : ?>
    <?php $folder_foto = $jenis == 'santri' ? 'santri' : 'pasfoto_musyrif'; ?>
    <?php foreach ($rekap as $row) : ?>
      <div class="m-card">
        <div class="m-absen-card">
          <?php if (!empty($row['pasfoto'])) : ?>
            <img src="<?= upload_url($folder_foto, $row['pasfoto']); ?>" class="m-absen-photo">
          <?php else : ?>
            <div class="m-absen-photo-fallback"><?= strtoupper(substr($row['nama'], 0, 1)); ?></div>
          <?php endif; ?>
          <div class="m-absen-info">
            <div class="m-absen-nama"><?= html_escape($row['nama']); ?></div>
            <div class="m-absen-sub"><?= html_escape($row['nomor']); ?> &middot; <?= html_escape($row['keterangan']); ?><?= isset($row['usia']) ? ' &middot; ' . (int) $row['usia'] . ' th' : ''; ?></div>
            <div class="mt-1" style="display:flex; gap:6px; flex-wrap:wrap;">
              <span class="m-badge m-badge-selesai">Hadir <?= (int) $row['Hadir']; ?></span>
              <span class="m-badge" style="background:#ffc107; color:#212529;">Sakit <?= (int) $row['Sakit']; ?></span>
              <span class="m-badge" style="background:#17a2b8;">Izin <?= (int) $row['Izin']; ?></span>
              <span class="m-badge m-badge-belum">Alpa <?= (int) $row['Alpa']; ?></span>
            </div>
          </div>
        </div>
      </div>
    <?php endforeach; ?>
  <?php else : ?>
    <div class="m-card">
      <p class="m-empty">Tidak ada data untuk ditampilkan.</p>
    </div>
  <?php endif; ?>
</div>
