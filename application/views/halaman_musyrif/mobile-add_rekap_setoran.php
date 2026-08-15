<div class="m-content">
  <p class="m-page-title">Proses Rekap Setoran</p>

  <div class="m-card">
    <p class="m-card-title" style="margin-bottom: 8px;">1. Pilih Kelas</p>
    <form action="<?= base_url('Halaman_Musyrif/form_add_rekap_setoran'); ?>">
      <div class="m-field">
        <select name="kelas">
          <option value="">-- Pilih Kelas --</option>
          <?php foreach ($kelas as $kls) : ?>
            <option value="<?= $kls['IdKelas']; ?>" <?= $this->input->get('kelas') == $kls['IdKelas'] ? 'selected' : ''; ?>><?= html_escape($kls['NamaKelas'] . ' | ' . $kls['Tingkat']); ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <button type="submit" class="m-btn"><i class="fas fa-users"></i> Tampil Santri</button>
    </form>
  </div>

  <?php if ($santri) : ?>
    <div class="m-card">
      <p class="m-card-title" style="margin-bottom: 8px;">2. Santri di Kelas Ini (<?= count($santri); ?>)</p>
      <?php foreach ($santri as $san) : ?>
        <div class="m-list-item">
          <div>
            <div class="m-list-title"><?= html_escape($san['NamaLengkap']); ?></div>
            <div class="m-list-sub"><?= html_escape($san['NamaKelas']); ?></div>
          </div>
        </div>
      <?php endforeach; ?>

      <form action="<?= base_url('Halaman_Musyrif/add_hasil_rekap_setoran'); ?>" method="POST" class="mt-2">
        <?php foreach ($santri as $san) : ?>
          <input type="hidden" name="IdSiswa[]" value="<?= $san['IdSiswa']; ?>">
        <?php endforeach; ?>
        <input type="hidden" name="kelas" value="<?= $this->input->get('kelas'); ?>">

        <div class="m-field">
          <label>Batas Lulus / Jumlah Tugas</label>
          <input type="text" name="jml_tugas">
        </div>
        <div class="m-field">
          <label>Pekan</label>
          <input type="text" name="pekan">
        </div>
        <button type="submit" class="m-btn"><i class="fas fa-users"></i> Proses Reward</button>
      </form>
    </div>
  <?php endif; ?>

  <?php if (isset($data_setoran)) : ?>
    <div class="m-card">
      <p class="m-card-title" style="margin-bottom: 8px;">3. Hasil Perhitungan - Pekan <?= html_escape($PekanRekap); ?></p>

      <form action="<?= base_url('Halaman_Musyrif/add_Rekap_Setoran'); ?>" method="POST">
        <?php foreach ($data_setoran as $d_setoran) :
          $pros = prosentase($d_setoran['jumlah_setoran'], $JmlTugas);
          $hasil = $pros == 100 ? 'Selesai' : 'Tidak Selesai';
          $reward = $pros == 100 ? 'Reward' : 'Tidak Dapat Reward';
        ?>
          <div class="m-list-item">
            <div>
              <div class="m-list-title"><?= html_escape($d_setoran['NamaLengkap']); ?></div>
              <div class="m-list-sub">Setor: <?= (int) $d_setoran['jumlah_setoran']; ?>/<?= (int) $JmlTugas; ?> (<?= $pros; ?>%)</div>
            </div>
            <span class="m-badge <?= $hasil === 'Selesai' ? 'm-badge-selesai' : 'm-badge-belum'; ?>"><?= html_escape($hasil); ?></span>
          </div>
          <input type="hidden" name="IdSiswa[]" value="<?= $d_setoran['IdSiswa']; ?>">
          <input type="hidden" name="JmlTugas" value="<?= $JmlTugas; ?>">
          <input type="hidden" name="JmlSetoran[]" value="<?= $d_setoran['jumlah_setoran']; ?>">
          <input type="hidden" name="PekanRekap" value="<?= $PekanRekap; ?>">
          <input type="hidden" name="Hasil[]" value="<?= $hasil; ?>">
          <input type="hidden" name="Prosentase[]" value="<?= $pros; ?>">
          <input type="hidden" name="Reward[]" value="<?= $reward; ?>">
        <?php endforeach; ?>

        <button type="submit" class="m-btn mt-2"><i class="fas fa-users"></i> Proses Hasil</button>
      </form>
    </div>
  <?php endif; ?>
</div>
