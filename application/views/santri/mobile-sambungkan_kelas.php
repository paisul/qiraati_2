<div class="m-content">
  <div style="display:flex; align-items:center; gap:10px; margin-bottom:4px;">
    <a href="<?= base_url('santri'); ?>" style="color:var(--heading); font-size:16px;"><i class="fas fa-arrow-left"></i></a>
    <p class="m-page-title" style="margin:0;">Sambungkan Kelas Santri</p>
  </div>

  <div class="m-card" style="border: 1px solid #ffe8a1; background: #fff3cd; color: #856404;">
    Halaman ini untuk santri yang kelasnya sudah terlanjur terhapus (jadi tidak muncul kelasnya di Data Santri). Pilih santri yang mau disambungkan, lalu pilih kelas tujuannya.
  </div>

  <?php if ($santri_yatim) : ?>
    <div class="m-card">
      <?= form_open('santri/proses_sambungkan_kelas'); ?>

      <div class="m-field">
        <label><input type="checkbox" id="mPilihSemuaYatim"> Pilih Semua (<?= count($santri_yatim); ?> santri)</label>
      </div>

      <div style="max-height: 320px; overflow-y: auto; margin-bottom: 14px;">
        <?php foreach ($santri_yatim as $s) : ?>
          <div class="m-list-item">
            <label style="display:flex; align-items:center; gap:10px; width:100%;">
              <input type="checkbox" name="id_siswa[]" value="<?= $s['IdSiswa']; ?>" class="mCekYatim">
              <div>
                <div class="m-list-title"><?= html_escape($s['NamaLengkap']); ?></div>
                <div class="m-list-sub">NIS <?= html_escape($s['NIS']); ?></div>
              </div>
            </label>
          </div>
        <?php endforeach; ?>
      </div>

      <div class="m-field">
        <label>Sambungkan ke Kelas</label>
        <select name="kelas" required>
          <option value="">-- Pilih Kelas --</option>
          <?php foreach ($kelas as $kls) : ?>
            <option value="<?= $kls['IdKelas']; ?>"><?= html_escape($kls['NamaKelas']); ?><?= !empty($kls['NamaMusyrif']) ? ' - ' . html_escape($kls['NamaMusyrif']) : ''; ?></option>
          <?php endforeach; ?>
        </select>
      </div>

      <button type="submit" class="m-btn"><i class="fas fa-link"></i> Sambungkan Santri Terpilih</button>
      <?= form_close(); ?>
    </div>
  <?php else : ?>
    <div class="m-card">
      <p class="m-empty">Tidak ada santri yang perlu disambungkan - semua santri sudah punya kelas.</p>
    </div>
  <?php endif; ?>
</div>

<script>
  var pilihSemua = document.getElementById('mPilihSemuaYatim');
  if (pilihSemua) {
    pilihSemua.addEventListener('change', function () {
      document.querySelectorAll('.mCekYatim').forEach(function (cb) {
        cb.checked = pilihSemua.checked;
      });
    });
  }
</script>
