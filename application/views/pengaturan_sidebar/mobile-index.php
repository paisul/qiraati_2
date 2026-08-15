<?php
// Dipakai 3x (tab Admin/Musyrif/Wali) - render lewat closure supaya tidak duplikasi markup.
// $toggle_tambahan: true untuk Musyrif/Wali (punya dashboard + menu bawah terpisah dari drawer),
// false untuk Admin (cuma drawer, jadi cukup toggle Aktif saja).
$render_daftar_menu_mobile = function ($menu, $toggle_tambahan) use (&$render_daftar_menu_mobile) {
  $render_baris_toggle = function ($item) use ($toggle_tambahan) {
?>
    <div class="m-menu-toggles">
      <label class="m-switch-item">
        <span class="m-switch">
          <input type="checkbox" class="toggle-aktif" data-id="<?= $item['IdMenu']; ?>" <?= $item['Aktif'] ? 'checked' : ''; ?>>
          <span class="m-switch-slider"></span>
        </span>
        <span class="m-switch-label">Aktif</span>
      </label>
      <?php if ($toggle_tambahan) : ?>
        <label class="m-switch-item">
          <span class="m-switch">
            <input type="checkbox" class="toggle-dashboard" data-id="<?= $item['IdMenu']; ?>" <?= $item['TampilDashboard'] ? 'checked' : ''; ?>>
            <span class="m-switch-slider"></span>
          </span>
          <span class="m-switch-label">Dashboard</span>
        </label>
        <label class="m-switch-item">
          <span class="m-switch">
            <input type="checkbox" class="toggle-menu-bawah" data-id="<?= $item['IdMenu']; ?>" <?= $item['TampilMenuBawah'] ? 'checked' : ''; ?>>
            <span class="m-switch-slider"></span>
          </span>
          <span class="m-switch-label">Menu Bawah</span>
        </label>
      <?php endif; ?>
    </div>
<?php
  };
?>
  <ul class="menu-list" data-level="atas" style="list-style:none; padding:0; margin:0;">
    <?php foreach ($menu as $m) : ?>
      <li class="menu-row m-card m-menu-card" data-id="<?= $m['IdMenu']; ?>">
        <div class="m-menu-header">
          <div class="m-menu-icon"><i class="<?= html_escape($m['Icon']); ?>"></i></div>
          <div>
            <div class="m-menu-label"><?= html_escape($m['Label']); ?></div>
            <div style="display:flex; gap:6px; margin-top:3px;">
              <?php if (!empty($m['anak'])) : ?><span class="m-badge m-badge-secondary" style="background:#6c757d;"><?= count($m['anak']); ?> sub-menu</span><?php endif; ?>
              <?php if ($m['HanyaAdmin']) : ?><span class="m-badge" style="background:#343a40;">Admin</span><?php endif; ?>
            </div>
          </div>
          <?php if (!empty($m['anak'])) : ?>
            <button type="button" class="m-menu-expand btn-expand" data-target="anak-<?= $m['KunciMenu']; ?>"><i class="fas fa-chevron-down"></i></button>
          <?php endif; ?>
        </div>

        <?php $render_baris_toggle($m); ?>

        <div class="m-menu-urutan">
          <button type="button" class="btn-naik"><i class="fas fa-arrow-up"></i> Naik</button>
          <button type="button" class="btn-turun"><i class="fas fa-arrow-down"></i> Turun</button>
        </div>

        <?php if (!empty($m['anak'])) : ?>
          <ul class="menu-list m-menu-anak" id="anak-<?= $m['KunciMenu']; ?>" data-level="anak" data-induk="<?= $m['KunciMenu']; ?>">
            <?php foreach ($m['anak'] as $a) : ?>
              <li class="menu-row m-card m-menu-card" data-id="<?= $a['IdMenu']; ?>" style="margin-top:10px;">
                <div class="m-menu-header">
                  <div class="m-menu-icon"><i class="<?= html_escape($a['Icon']); ?>"></i></div>
                  <div>
                    <div class="m-menu-label"><?= html_escape($a['Label']); ?></div>
                    <?php if ($a['HanyaAdmin']) : ?><span class="m-badge" style="background:#343a40; margin-top:3px; display:inline-block;">Admin</span><?php endif; ?>
                  </div>
                </div>

                <?php $render_baris_toggle($a); ?>

                <div class="m-menu-urutan">
                  <button type="button" class="btn-naik"><i class="fas fa-arrow-up"></i> Naik</button>
                  <button type="button" class="btn-turun"><i class="fas fa-arrow-down"></i> Turun</button>
                </div>
              </li>
            <?php endforeach; ?>
          </ul>
        <?php endif; ?>
      </li>
    <?php endforeach; ?>
  </ul>
<?php
};
?>
<div class="m-content">
  <p class="m-page-title">Pengaturan Sidebar</p>

  <div class="m-card">
    <p style="margin:0; color:#6c757d; font-size:13px;">
      Pakai tombol Naik/Turun untuk mengubah urutan menu - tersimpan otomatis. Menu utama hanya bisa ditukar posisi dengan sesama menu utama;
      sub-menu hanya bisa ditukar posisi dengan sesama sub-menu dalam induk yang sama. Toggle "Dashboard" &amp; "Menu Bawah" (khusus Musyrif/Wali)
      cuma soal tampil/tidaknya ikon di dua tempat itu - halamannya sendiri tetap bisa diakses selama "Aktif" menyala.
    </p>
  </div>

  <div class="m-tabs">
    <button type="button" class="m-tab-btn active" data-tab-target="#mTabAdmin"><i class="fas fa-user-shield"></i> Admin</button>
    <button type="button" class="m-tab-btn" data-tab-target="#mTabMusyrif"><i class="fas fa-chalkboard-teacher"></i> Musyrif</button>
    <button type="button" class="m-tab-btn" data-tab-target="#mTabWali"><i class="fas fa-user-friends"></i> Wali</button>
  </div>

  <div class="m-tab-panel" id="mTabAdmin">
    <?= $render_daftar_menu_mobile($menu_admin, false); ?>
  </div>
  <div class="m-tab-panel" id="mTabMusyrif" hidden>
    <?= $render_daftar_menu_mobile($menu_musyrif, true); ?>
  </div>
  <div class="m-tab-panel" id="mTabWali" hidden>
    <?= $render_daftar_menu_mobile($menu_wali, true); ?>
  </div>
</div>

<script>
  (function () {
    var urlSimpanUrutan = '<?= base_url('pengaturansidebar/simpan_urutan'); ?>';
    var urlToggleAktif = '<?= base_url('pengaturansidebar/toggle_aktif'); ?>';
    var urlToggleDashboard = '<?= base_url('pengaturansidebar/toggle_dashboard'); ?>';
    var urlToggleMenuBawah = '<?= base_url('pengaturansidebar/toggle_menu_bawah'); ?>';

    document.querySelectorAll('[data-tab-target]').forEach(function (btn) {
      btn.addEventListener('click', function () {
        document.querySelectorAll('[data-tab-target]').forEach(function (b) { b.classList.remove('active'); });
        btn.classList.add('active');
        document.querySelectorAll('.m-tab-panel').forEach(function (panel) { panel.hidden = true; });
        document.querySelector(btn.dataset.tabTarget).hidden = false;
      });
    });

    function simpanUrutanSemua() {
      var daftar = [];
      document.querySelectorAll('.menu-list').forEach(function (list) {
        Array.prototype.slice.call(list.children).filter(function (el) {
          return el.classList.contains('menu-row') && el.parentElement === list;
        }).forEach(function (row, i) {
          daftar.push({ IdMenu: row.dataset.id, Urutan: i + 1 });
        });
      });

      fetch(urlSimpanUrutan, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ daftar: daftar })
      }).then(function (r) { return r.json(); }).then(function (json) {
        if (!json.status) {
          Swal.fire({ icon: 'error', title: 'Gagal', text: 'Urutan gagal disimpan, muat ulang halaman.' });
        }
      }).catch(function () {
        Swal.fire({ icon: 'error', title: 'Gagal', text: 'Terjadi kesalahan koneksi.' });
      });
    }

    document.querySelectorAll('.btn-naik').forEach(function (btn) {
      btn.addEventListener('click', function () {
        var row = btn.closest('.menu-row');
        var prev = row.previousElementSibling;
        if (prev && prev.classList.contains('menu-row')) {
          row.parentElement.insertBefore(row, prev);
          simpanUrutanSemua();
        }
      });
    });

    document.querySelectorAll('.btn-turun').forEach(function (btn) {
      btn.addEventListener('click', function () {
        var row = btn.closest('.menu-row');
        var next = row.nextElementSibling;
        if (next && next.classList.contains('menu-row')) {
          row.parentElement.insertBefore(next, row);
          simpanUrutanSemua();
        }
      });
    });

    function pasangToggle(selector, url, labelGagal) {
      document.querySelectorAll(selector).forEach(function (chk) {
        chk.addEventListener('change', function () {
          var id = chk.dataset.id;
          fetch(url + '/' + id, { method: 'POST' })
            .then(function (r) { return r.json(); })
            .then(function (json) {
              if (!json.status) {
                chk.checked = !chk.checked;
                Swal.fire({ icon: 'error', title: 'Gagal', text: labelGagal });
              }
            });
        });
      });
    }

    pasangToggle('.toggle-aktif', urlToggleAktif, 'Gagal mengubah status aktif menu ini.');
    pasangToggle('.toggle-dashboard', urlToggleDashboard, 'Gagal mengubah status tampil di dashboard.');
    pasangToggle('.toggle-menu-bawah', urlToggleMenuBawah, 'Gagal mengubah status tampil di menu bawah.');

    document.querySelectorAll('.btn-expand').forEach(function (btn) {
      btn.addEventListener('click', function () {
        var target = document.getElementById(btn.dataset.target);
        if (!target) return;
        var tampil = target.style.display !== 'none';
        target.style.display = tampil ? 'none' : '';
        btn.querySelector('i').classList.toggle('fa-chevron-down', tampil);
        btn.querySelector('i').classList.toggle('fa-chevron-right', !tampil);
      });
    });
  })();
</script>
