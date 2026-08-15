<?php
// Dipakai dua kali (tab Admin & tab Musyrif) - daripada duplikasi markup, render lewat closure ini.
$render_daftar_menu = function ($menu) {
?>
  <ul class="list-group menu-list" data-level="atas">
    <?php foreach ($menu as $m) : ?>
      <li class="list-group-item menu-row" draggable="true" data-id="<?= $m['IdMenu']; ?>">
        <div class="d-flex align-items-center flex-wrap">
          <i class="fas fa-grip-vertical text-muted mr-3" style="cursor:grab;"></i>
          <?php if (!empty($m['anak'])) : ?>
            <button type="button" class="btn btn-sm btn-link p-0 mr-2 btn-expand" data-target="anak-<?= $m['KunciMenu']; ?>"><i class="fas fa-chevron-down"></i></button>
          <?php endif; ?>
          <i class="<?= html_escape($m['Icon']); ?> mr-2"></i>
          <strong class="mr-2"><?= html_escape($m['Label']); ?></strong>
          <?php if (!empty($m['anak'])) : ?><span class="badge badge-secondary mr-2"><?= count($m['anak']); ?> sub-menu</span><?php endif; ?>
          <?php if ($m['HanyaAdmin']) : ?><span class="badge badge-dark mr-2">Khusus Admin</span><?php endif; ?>

          <div class="custom-control custom-switch ml-auto">
            <input type="checkbox" class="custom-control-input toggle-aktif" id="aktif-<?= $m['IdMenu']; ?>" data-id="<?= $m['IdMenu']; ?>" <?= $m['Aktif'] ? 'checked' : ''; ?>>
            <label class="custom-control-label" for="aktif-<?= $m['IdMenu']; ?>">Aktif</label>
          </div>
        </div>

        <?php if (!empty($m['anak'])) : ?>
          <ul class="list-group mt-2 ml-4 menu-list" id="anak-<?= $m['KunciMenu']; ?>" data-level="anak" data-induk="<?= $m['KunciMenu']; ?>">
            <?php foreach ($m['anak'] as $a) : ?>
              <li class="list-group-item menu-row" draggable="true" data-id="<?= $a['IdMenu']; ?>">
                <div class="d-flex align-items-center flex-wrap">
                  <i class="fas fa-grip-vertical text-muted mr-3" style="cursor:grab;"></i>
                  <i class="<?= html_escape($a['Icon']); ?> mr-2"></i>
                  <span class="mr-2"><?= html_escape($a['Label']); ?></span>
                  <?php if ($a['HanyaAdmin']) : ?><span class="badge badge-dark mr-2">Khusus Admin</span><?php endif; ?>

                  <div class="custom-control custom-switch ml-auto">
                    <input type="checkbox" class="custom-control-input toggle-aktif" id="aktif-<?= $a['IdMenu']; ?>" data-id="<?= $a['IdMenu']; ?>" <?= $a['Aktif'] ? 'checked' : ''; ?>>
                    <label class="custom-control-label" for="aktif-<?= $a['IdMenu']; ?>">Aktif</label>
                  </div>
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
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
  <div class="content mt-2">
    <div class="container-fluid">
      <div class="row">
        <div class="col-12">
          <div class="card">
            <div class="card-header bg-success">
              <h4 class="m-0"><?= $title; ?></h4>
            </div>

            <div class="flash-data" data-flashdata="<?= isset($pesan) ? $pesan : $this->session->flashdata('pesan'); ?>" data-title="Pengaturan Sidebar">
            </div>

            <div class="card-body">
              <p class="text-muted">
                Seret (drag) baris untuk mengubah urutan menu - tersimpan otomatis. Menu utama hanya bisa ditukar posisi dengan sesama menu utama;
                sub-menu hanya bisa ditukar posisi dengan sesama sub-menu dalam induk yang sama. Menu "Dashboard" dan "Pengaturan Sidebar" (tab Admin),
                "Dashboard Musyrif" (tab Musyrif), serta "Dashboard Wali" (tab Wali) tidak ditampilkan di sini karena selalu aktif (supaya tidak ada yang terkunci dari halaman utamanya).
              </p>

              <ul class="nav nav-tabs" id="tabSidebar" role="tablist">
                <li class="nav-item">
                  <a class="nav-link active" id="tab-admin-link" data-toggle="tab" href="#tab-admin" role="tab">Sidebar Admin</a>
                </li>
                <li class="nav-item">
                  <a class="nav-link" id="tab-musyrif-link" data-toggle="tab" href="#tab-musyrif" role="tab">Sidebar Musyrif</a>
                </li>
                <li class="nav-item">
                  <a class="nav-link" id="tab-wali-link" data-toggle="tab" href="#tab-wali" role="tab">Sidebar Wali</a>
                </li>
              </ul>
              <div class="tab-content border border-top-0 p-3 mb-3">
                <div class="tab-pane fade show active" id="tab-admin" role="tabpanel">
                  <?= $render_daftar_menu($menu_admin); ?>
                </div>
                <div class="tab-pane fade" id="tab-musyrif" role="tabpanel">
                  <?= $render_daftar_menu($menu_musyrif); ?>
                </div>
                <div class="tab-pane fade" id="tab-wali" role="tabpanel">
                  <?= $render_daftar_menu($menu_wali); ?>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
  (function () {
    var urlSimpanUrutan = '<?= base_url('pengaturansidebar/simpan_urutan'); ?>';
    var urlToggleAktif = '<?= base_url('pengaturansidebar/toggle_aktif'); ?>';

    var dragged = null;

    document.querySelectorAll('.menu-row').forEach(function (row) {
      row.addEventListener('dragstart', function (e) {
        dragged = row;
        row.classList.add('dragging');
        e.stopPropagation();
      });
      row.addEventListener('dragend', function (e) {
        row.classList.remove('dragging');
        dragged = null;
        e.stopPropagation();
        simpanUrutanSemua();
      });
    });

    // Setiap .menu-list (level atas, atau anak per induk, di tab manapun) adalah dropzone TERPISAH -
    // baris hanya bisa dipindah dalam list yang sama (tidak bisa lintas induk / lintas level / lintas tab).
    document.querySelectorAll('.menu-list').forEach(function (list) {
      list.addEventListener('dragover', function (e) {
        if (!dragged) return;
        // Tolak drop kalau baris yang diseret bukan anggota langsung list ini.
        if (dragged.parentElement !== list) return;

        e.preventDefault();
        e.stopPropagation();

        var after = elemenSetelah(list, e.clientY);
        if (after == null) {
          list.appendChild(dragged);
        } else {
          list.insertBefore(dragged, after);
        }
      });
    });

    function elemenSetelah(container, y) {
      var rows = Array.prototype.slice.call(container.children).filter(function (el) {
        return el.classList.contains('menu-row') && el !== dragged;
      });
      var hasil = { offset: -Infinity, element: null };
      rows.forEach(function (row) {
        var box = row.getBoundingClientRect();
        var offset = y - box.top - box.height / 2;
        if (offset < 0 && offset > hasil.offset) {
          hasil = { offset: offset, element: row };
        }
      });
      return hasil.element;
    }

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

    document.querySelectorAll('.toggle-aktif').forEach(function (chk) {
      chk.addEventListener('change', function () {
        var id = chk.dataset.id;
        fetch(urlToggleAktif + '/' + id, { method: 'POST' })
          .then(function (r) { return r.json(); })
          .then(function (json) {
            if (!json.status) {
              chk.checked = !chk.checked;
              Swal.fire({ icon: 'error', title: 'Gagal', text: 'Gagal mengubah status aktif menu ini.' });
            }
          });
      });
    });

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

<style>
  .menu-row.dragging {
    opacity: 0.4;
  }

  .menu-list {
    min-height: 10px;
  }
</style>
