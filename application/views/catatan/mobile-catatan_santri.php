<div class="m-content">
  <p class="m-page-title">Catatan Santri</p>

  <div class="m-card">
    <button type="button" class="m-btn" data-toggle-target="#formTambahCatatan">
      <i class="fas fa-plus"></i> Tambah Data
    </button>

    <div class="m-form-panel" id="formTambahCatatan" hidden>
      <?= form_open('catatan/catatan_santri/add'); ?>
      <div class="m-field">
        <label>Nama Santri</label>
        <select name="nama">
          <option value="">-- Pilih Nama Santri --</option>
          <?php foreach ($santri as $san) : ?>
            <option value="<?= $san['IdSiswa']; ?>"><?= html_escape($san['NamaLengkap']); ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="m-field">
        <label>Periode</label>
        <select name="periode">
          <option value="">-- Pilih Periode --</option>
          <?php foreach ($periode as $p) : ?>
            <option value="<?= $p['IdPeriode']; ?>"><?= html_escape($p['Periode']); ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="m-field">
        <label>Jenis Catatan</label>
        <select name="jeniscatatan" id="mJenisCatatan">
          <option value="">-- Pilih Jenis Catatan --</option>
          <?php foreach ($jenis_catatan as $jc) : ?>
            <option value="<?= $jc['IdJenisCatatan']; ?>"><?= html_escape($jc['JenisCatatan']); ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="m-field">
        <label>Detail Jenis Catatan</label>
        <select multiple name="detailjeniscatatan[]" id="mDetailJenisCatatan" style="min-height: 100px;"></select>
      </div>
      <div class="m-field">
        <label>Catatan Musyrif</label>
        <textarea name="catatan_musyrif" rows="4" placeholder="Form ini digunakan untuk Catatan Jenis Catatan Musyrif."></textarea>
      </div>
      <button type="submit" class="m-btn"><i class="fas fa-save"></i> Simpan</button>
      <?= form_close(); ?>
    </div>

    <a href="<?= base_url('catatan/catatan_santri/export_excel'); ?>" target="_blank" class="m-btn m-btn-outline mt-2"><i class="fas fa-file-excel"></i> Export Data</a>
    <a href="<?= base_url('catatan/catatan_santri/reset_data'); ?>" class="m-btn mt-2 tombol-reset" tipeData="Catatan Santri" style="background:#ffc107; color:#212529;"><i class="fas fa-ban"></i> Reset Data</a>
  </div>

  <div class="m-card">
    <?php if ($catatan_santri) : ?>
      <?php foreach ($catatan_santri as $cs) : ?>
        <div class="m-list-item" style="flex-direction:column; align-items:stretch;">
          <div class="d-flex justify-content-between" style="display:flex; justify-content:space-between;">
            <div>
              <div class="m-list-title"><?= html_escape($cs['NamaLengkap']); ?></div>
              <div class="m-list-sub"><?= html_escape($cs['Periode'] . ' - ' . $cs['JenisCatatan']); ?></div>
            </div>
          </div>
          <?php if (!empty($cs['IsiCatatan'])) : ?>
            <div class="m-list-sub mt-1">Detail: <?= html_escape($cs['IsiCatatan']); ?></div>
          <?php endif; ?>
          <?php if (!empty($cs['CatatanMusyrif'])) : ?>
            <div class="m-list-sub">Catatan: <?= html_escape($cs['CatatanMusyrif']); ?></div>
          <?php endif; ?>
          <a href="<?= base_url('catatan/catatan_santri/delete/' . $cs['IdCatatan']); ?>" class="m-dana-btn-hapus tombol-hapus mt-2" tipeData="Catatan" namaData="<?= html_escape($cs['NamaLengkap']); ?>" style="display:block; text-align:center;">Hapus</a>
        </div>
      <?php endforeach; ?>
    <?php else : ?>
      <p class="m-empty">Belum ada data catatan santri.</p>
    <?php endif; ?>
  </div>
</div>

<script>
  var mJenisCatatan = document.getElementById('mJenisCatatan');
  var mDetailJenisCatatan = document.getElementById('mDetailJenisCatatan');
  if (mJenisCatatan && mDetailJenisCatatan) {
    mJenisCatatan.addEventListener('change', function () {
      if (!mJenisCatatan.value) {
        mDetailJenisCatatan.innerHTML = '';
        return;
      }

      fetch('<?= base_url('catatan/Catatan_santri/getDetailCatatanByJenis'); ?>', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'id_jenis_catatan=' + encodeURIComponent(mJenisCatatan.value)
      })
        .then(function (res) { return res.text(); })
        .then(function (html) { mDetailJenisCatatan.innerHTML = html; })
        .catch(function () {});
    });
  }
</script>
