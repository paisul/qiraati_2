<?php $baca_saja = $user['level'] == 'Wali'; ?>
<div class="m-content">
  <p class="m-page-title">Data Dana</p>

  <div class="m-stat-row m-dana-stat-sticky">
    <div class="m-card m-stat-success" style="padding: 10px;">
      <p class="m-card-title" style="font-size:11px;">Masuk</p>
      <p class="m-card-value" style="font-size:16px;"><?= number_format($ringkasan['total_masuk'], 0, ',', '.'); ?></p>
    </div>
    <div class="m-card m-stat-danger" style="padding: 10px;">
      <p class="m-card-title" style="font-size:11px;">Keluar</p>
      <p class="m-card-value" style="font-size:16px;"><?= number_format($ringkasan['total_keluar'], 0, ',', '.'); ?></p>
    </div>
    <div class="m-card m-stat-primary" style="padding: 10px;">
      <p class="m-card-title" style="font-size:11px;">Saldo</p>
      <p class="m-card-value" style="font-size:16px;"><?= number_format($ringkasan['saldo'], 0, ',', '.'); ?></p>
    </div>
  </div>

  <?php if (!$baca_saja) : ?>
    <div class="m-popup-overlay" id="formTambahDana" hidden>
      <div class="m-popup-sheet">
        <?= form_open('dana/add', ['class' => 'm-popup-form']); ?>
        <div class="m-popup-header">
          <p class="m-popup-title">Tambah Dana</p>
          <button type="button" class="m-popup-close" data-toggle-target="#formTambahDana"><i class="fas fa-times"></i></button>
        </div>
        <div class="m-popup-body">
          <div class="m-field">
            <label>Tanggal</label>
            <input type="date" name="tanggal" value="<?= date('Y-m-d'); ?>" required>
          </div>
          <div class="m-field">
            <label>Perihal</label>
            <input type="text" name="perihal" placeholder="Masukkan perihal dana" required autocomplete="off">
          </div>
          <div class="m-field">
            <label>Jumlah Masuk</label>
            <input type="text" inputmode="numeric" class="m-input-nominal m-input-nominal-masuk" name="jumlah_masuk" value="0" placeholder="0" required>
          </div>
          <div class="m-field">
            <label>Jumlah Keluar</label>
            <input type="text" inputmode="numeric" class="m-input-nominal m-input-nominal-keluar" name="jumlah_keluar" value="0" placeholder="0" required>
          </div>
        </div>
        <div class="m-popup-footer">
          <button type="submit" class="m-btn m-btn-sticky" data-dana-submit disabled><i class="fas fa-save"></i> Simpan</button>
        </div>
        <?= form_close(); ?>
      </div>
    </div>
  <?php endif; ?>

  <div id="mDanaList">
    <?php if ($dana) : ?>
      <?php foreach ($dana as $dn) : ?>
        <?php if (!$baca_saja) : ?>
          <div class="m-dana-swipe-wrap">
            <div class="m-dana-swipe-actions-kiri">
              <button type="button" data-toggle-target="#editDana<?= $dn['IdDana']; ?>">
                <i class="fas fa-pen"></i> Ubah
              </button>
            </div>
            <div class="m-dana-swipe-actions-kanan">
              <a href="<?= base_url('dana/delete/' . $dn['IdDana']); ?>" class="tombol-hapus" tipeData="Dana" namaData="<?= html_escape($dn['Perihal']); ?>">
                <i class="fas fa-trash"></i> Hapus
              </a>
            </div>
            <div class="m-card m-dana-swipe-item">
              <div class="m-dana-item">
                <div>
                  <div class="m-dana-perihal"><?= html_escape($dn['Perihal']); ?></div>
                  <div class="m-dana-tanggal"><?= date_indo($dn['Tanggal']); ?></div>
                </div>
                <div style="text-align:right;">
                  <?php if ($dn['JumlahMasuk'] > 0) : ?>
                    <div class="m-dana-nominal-masuk">+<?= number_format($dn['JumlahMasuk'], 0, ',', '.'); ?></div>
                  <?php endif; ?>
                  <?php if ($dn['JumlahKeluar'] > 0) : ?>
                    <div class="m-dana-nominal-keluar">-<?= number_format($dn['JumlahKeluar'], 0, ',', '.'); ?></div>
                  <?php endif; ?>
                </div>
              </div>
            </div>
          </div>

          <div class="m-popup-overlay" id="editDana<?= $dn['IdDana']; ?>" hidden>
            <div class="m-popup-sheet">
              <?= form_open('dana/update/' . $dn['IdDana'], ['class' => 'm-popup-form']); ?>
              <div class="m-popup-header">
                <p class="m-popup-title">Ubah Dana</p>
                <button type="button" class="m-popup-close" data-toggle-target="#editDana<?= $dn['IdDana']; ?>"><i class="fas fa-times"></i></button>
              </div>
              <div class="m-popup-body">
                <div class="m-field">
                  <label>Tanggal</label>
                  <input type="date" name="tanggal" value="<?= $dn['Tanggal']; ?>" required>
                </div>
                <div class="m-field">
                  <label>Perihal</label>
                  <input type="text" name="perihal" value="<?= html_escape($dn['Perihal']); ?>" required autocomplete="off">
                </div>
                <div class="m-field">
                  <label>Jumlah Masuk</label>
                  <input type="text" inputmode="numeric" class="m-input-nominal m-input-nominal-masuk" name="jumlah_masuk" value="<?= number_format($dn['JumlahMasuk'], 0, ',', '.'); ?>" placeholder="0" required>
                </div>
                <div class="m-field">
                  <label>Jumlah Keluar</label>
                  <input type="text" inputmode="numeric" class="m-input-nominal m-input-nominal-keluar" name="jumlah_keluar" value="<?= number_format($dn['JumlahKeluar'], 0, ',', '.'); ?>" placeholder="0" required>
                </div>
              </div>
              <div class="m-popup-footer">
                <button type="submit" class="m-btn m-btn-sticky" data-dana-submit disabled><i class="fas fa-save"></i> Simpan Perubahan</button>
              </div>
              <?= form_close(); ?>
            </div>
          </div>
        <?php else : ?>
          <div class="m-card">
            <div class="m-dana-item">
              <div>
                <div class="m-dana-perihal"><?= html_escape($dn['Perihal']); ?></div>
                <div class="m-dana-tanggal"><?= date_indo($dn['Tanggal']); ?></div>
              </div>
              <div style="text-align:right;">
                <?php if ($dn['JumlahMasuk'] > 0) : ?>
                  <div class="m-dana-nominal-masuk">+<?= number_format($dn['JumlahMasuk'], 0, ',', '.'); ?></div>
                <?php endif; ?>
                <?php if ($dn['JumlahKeluar'] > 0) : ?>
                  <div class="m-dana-nominal-keluar">-<?= number_format($dn['JumlahKeluar'], 0, ',', '.'); ?></div>
                <?php endif; ?>
              </div>
            </div>
          </div>
        <?php endif; ?>
      <?php endforeach; ?>
    <?php else : ?>
      <div class="m-card">
        <p class="m-empty">Belum ada data dana.</p>
      </div>
    <?php endif; ?>
  </div>
</div>

<?php if (!$baca_saja) : ?>
  <button type="button" class="m-fab" data-toggle-target="#formTambahDana" aria-label="Tambah Dana">
    <i class="fas fa-plus"></i>
  </button>
<?php endif; ?>

<script>
  // Toggle generik [data-toggle-target] (dipakai FAB Tambah, tombol Ubah, & tombol X tutup popup
  // di atas) sudah ditangani global oleh assets/js/mobile.js - tidak perlu disalin lagi di sini.

  // Input nominal: tampilkan pemisah ribuan otomatis waktu mengetik (mis. 10.000), tapi kirim
  // ke server dalam bentuk angka polos - form_dana/Dana::formatNominal() tidak perlu diubah.
  document.querySelectorAll('.m-input-nominal').forEach(function (input) {
    function taruhKursorDiUjung() {
      var panjang = input.value.length;
      input.setSelectionRange(panjang, panjang);
    }

    input.addEventListener('input', function () {
      var angka = input.value.replace(/\D/g, '');
      input.value = angka ? new Intl.NumberFormat('id-ID').format(angka) : '';
      // Nilainya ditulis ulang di atas - tanpa ini kursor ikut lompat balik ke awal teks
      // (rata kanan "0" jadi kelihatan seperti diketik dari kiri, bukan disambung di kanan).
      taruhKursorDiUjung();
    });

    // Ditunda lewat setTimeout supaya jalan SETELAH browser selesai naruh kursornya sendiri
    // (mis. di posisi disentuh) - kalau tidak, hasil kita bisa langsung ketiban lagi.
    input.addEventListener('focus', function () {
      setTimeout(taruhKursorDiUjung, 0);
    });

    var form = input.closest('form');
    if (form) {
      form.addEventListener('submit', function () {
        document.querySelectorAll('.m-input-nominal').forEach(function (el) {
          el.value = el.value.replace(/\D/g, '') || '0';
        });
      });
    }
  });
</script>
