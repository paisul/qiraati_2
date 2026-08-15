<?php
function cek_login()
{
  // Halaman yang butuh login itu personal (data & pesan flashdata beda per pengguna/per aksi) -
  // JANGAN pernah di-cache LiteSpeed, kalau tidak pesan "berhasil" dari satu aksi bisa nyangkut
  // dan tampil lagi ke kunjungan/pengguna berikutnya yang membuka URL yang sama.
  if (!headers_sent()) {
    header('Cache-Control: no-cache, no-store, must-revalidate, private');
    header('X-LiteSpeed-Cache-Control: no-cache');
  }

  $ci = get_instance();
  if (!$ci->session->userdata('username')) {
    $ci->session->set_flashdata('message', '<div class="alert alert-danger" role="alert">Anda Belum Login!, silahkan login terlebih dahulu</div>');
    redirect('auth');
  }
}

function cek_level($level)
{
  cek_login();

  $ci = get_instance();
  $level_login = $ci->session->userdata('level');
  $level_diizinkan = is_array($level) ? $level : [$level];

  if (!in_array($level_login, $level_diizinkan)) {
    $ci->session->set_flashdata('message', '<div class="alert alert-danger" role="alert">Anda tidak memiliki akses ke halaman tersebut.</div>');
    redirect('auth');
  }
}

// Dipakai controller yang punya versi tampilan mobile terpisah (bukan responsive) - lihat
// Wali.php/Kelas.php getWrapper(). Library 'user_agent' bawaan CodeIgniter, diaktifkan di autoload.php.
// Kirim header X-LiteSpeed-Vary supaya cache halaman (LSCache, aktif di hosting ini) disimpan
// TERPISAH untuk mobile & desktop - tanpa ini, pengunjung HP bisa kebagian versi desktop yang
// kebetulan ke-cache duluan (atau sebaliknya), sampai cache di-purge manual.
function tampilan_mobile()
{
  if (!headers_sent()) {
    header('X-LiteSpeed-Vary: ismobile');
  }

  return get_instance()->agent->is_mobile();
}

/**
 * Tabel relasi wali <-> santri (satu akun login Wali bisa punya lebih dari satu anak).
 * login.IdSiswa (kolom lama, skalar) tetap dipertahankan apa adanya untuk kompatibilitas
 * mundur (anak pertama/satu-satunya yang tercatat di sana) tapi TIDAK lagi jadi satu-satunya
 * sumber kebenaran - wali_siswa yang otentik untuk daftar semua anak sejak fitur ini ada.
 * Dipanggil dari Wali_M & Santri_M (pola sama seperti pastikan_tabel_menu_sidebar): idempotent,
 * aman dipanggil berkali-kali dari model manapun tanpa peduli urutan load.
 */
function pastikan_tabel_wali_siswa($db)
{
  if ($db->table_exists('wali_siswa')) {
    return;
  }

  $db->query("CREATE TABLE wali_siswa (
    IdUser INT(11) NOT NULL,
    IdSiswa INT(11) NOT NULL,
    PRIMARY KEY (IdUser, IdSiswa)
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

  // Backfill sekali saja tepat saat tabel dibuat (bukan digantungkan ke count_all - supaya tidak
  // pernah re-insert kalau baris jadi 0 lagi lewat alur hapus santri normal): akun wali lama (satu
  // anak, lewat login.IdSiswa) langsung tercatat di sini juga tanpa perlu migrasi manual.
  $db->query("INSERT INTO wali_siswa (IdUser, IdSiswa)
    SELECT IdUser, IdSiswa FROM login WHERE level = 'Wali' AND IdSiswa IS NOT NULL");
}

/**
 * Pastikan tabel menu_sidebar (Pengaturan Sidebar) ada & sudah ter-seed, dipanggil dari beberapa
 * tempat berbeda (hook CekMenuAktif, MenuSidebar_M, templates/sidebar.php) yang masing-masing
 * punya $db sendiri (bukan lewat model, supaya tidak tergantung urutan load/autoload CI3) -
 * makanya logikanya di sini sebagai fungsi biasa, bukan method model, dan menerima $db langsung.
 */
function pastikan_tabel_menu_sidebar($db)
{
  if (!$db->table_exists('menu_sidebar')) {
    $db->query("CREATE TABLE menu_sidebar (
      IdMenu INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
      KunciMenu VARCHAR(50) NOT NULL,
      ParentKunci VARCHAR(50) NULL,
      Label VARCHAR(100) NOT NULL,
      Url VARCHAR(100) NULL,
      Icon VARCHAR(100) NOT NULL DEFAULT '',
      Urutan INT(11) NOT NULL DEFAULT 1,
      Aktif TINYINT(1) NOT NULL DEFAULT 1,
      HanyaAdmin TINYINT(1) NOT NULL DEFAULT 0,
      Grup VARCHAR(20) NOT NULL DEFAULT 'admin',
      TampilDashboard TINYINT(1) NOT NULL DEFAULT 1,
      TampilMenuBawah TINYINT(1) NOT NULL DEFAULT 1,
      PRIMARY KEY (IdMenu),
      UNIQUE KEY KunciMenu (KunciMenu)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
  } elseif (!$db->field_exists('Grup', 'menu_sidebar')) {
    // Migrasi dari versi sebelum sidebar Musyrif dikelola - semua baris lama adalah sidebar admin.
    $db->query("ALTER TABLE menu_sidebar ADD COLUMN Grup VARCHAR(20) NOT NULL DEFAULT 'admin'");
  }

  // Migrasi: toggle terpisah ikon dashboard vs menu bawah (mobile Wali/Musyrif) - "Aktif" tetap
  // satu-satunya yang memblokir akses halaman (hook CekMenuAktif) & tampil di drawer, dua kolom
  // ini murni soal tampil/tidaknya ikon di dua tempat itu.
  if (!$db->field_exists('TampilDashboard', 'menu_sidebar')) {
    $db->query("ALTER TABLE menu_sidebar ADD COLUMN TampilDashboard TINYINT(1) NOT NULL DEFAULT 1");
  }
  if (!$db->field_exists('TampilMenuBawah', 'menu_sidebar')) {
    $db->query("ALTER TABLE menu_sidebar ADD COLUMN TampilMenuBawah TINYINT(1) NOT NULL DEFAULT 1");
  }

  pastikan_seed_menu_sidebar_admin($db);
  pastikan_seed_menu_sidebar_musyrif($db);
  pastikan_seed_menu_sidebar_wali($db);
  pastikan_menu_pengaturan_aplikasi($db);
  pastikan_menu_pengaturan_migrasi($db);
  pastikan_menu_pengaturan_user($db);
  pastikan_menu_pengumuman($db);
  pastikan_menu_akun_ganda($db);
  pastikan_menu_dana_submenu($db);
  pastikan_label_menu_dana($db);
  pastikan_menu_dana_submenu_musyrif($db);
}

// Sama seperti pastikan_menu_dana_submenu() tapi untuk grup 'musyrif' (dipakai Musyrif & Guru) -
// sidebar_musyrif.php sebelumnya flat (tidak ada sub-menu), sudah ditambah dukungan treeview
// yang sama seperti templates/sidebar.php supaya menu ini bisa tampil di sana juga.
function pastikan_menu_dana_submenu_musyrif($db)
{
  if ($db->where('KunciMenu', 'msy_dana_sumbangan')->count_all_results('menu_sidebar') > 0) {
    return;
  }

  $db->trans_start();

  $db->insert('menu_sidebar', [
    'KunciMenu' => 'msy_dana_tabung', 'ParentKunci' => 'msy_dana', 'Label' => 'Tabung',
    'Url' => 'dana', 'Icon' => 'fas fa-fw fa-wallet',
    'Urutan' => 1, 'Aktif' => 1, 'HanyaAdmin' => 0, 'Grup' => 'musyrif',
  ]);
  $db->insert('menu_sidebar', [
    'KunciMenu' => 'msy_dana_sumbangan', 'ParentKunci' => 'msy_dana', 'Label' => 'Sumbangan',
    'Url' => 'sumbangan', 'Icon' => 'fas fa-fw fa-hand-holding-heart',
    'Urutan' => 2, 'Aktif' => 1, 'HanyaAdmin' => 0, 'Grup' => 'musyrif',
  ]);
  $db->insert('menu_sidebar', [
    'KunciMenu' => 'msy_dana_lain', 'ParentKunci' => 'msy_dana', 'Label' => 'Lain-lain',
    'Url' => 'dana-lain', 'Icon' => 'fas fa-fw fa-ellipsis-h',
    'Urutan' => 3, 'Aktif' => 1, 'HanyaAdmin' => 0, 'Grup' => 'musyrif',
  ]);

  $db->where('KunciMenu', 'msy_dana')->where('Grup', 'musyrif')->update('menu_sidebar', [
    'Label' => 'Dana', 'Url' => null,
  ]);

  $db->trans_complete();
}

// Ganti label menu Data Dana sesuai permintaan user: induk "Data Dana" -> "Dana", anak pertama
// (submenu yang menuju /dana) "Data Dana" -> "Tabung". UPDATE langsung (bukan lewat seed di atas)
// karena instalasi yang sudah pernah menjalankan pastikan_menu_dana_submenu() di atas tidak akan
// pernah insert ulang baris itu (dicegah guard-nya) - jadi labelnya perlu dikoreksi terpisah di sini.
function pastikan_label_menu_dana($db)
{
  $db->where('KunciMenu', 'dana')->where('Grup', 'admin')->where('Label !=', 'Dana')->update('menu_sidebar', ['Label' => 'Dana']);
  $db->where('KunciMenu', 'dana_data')->where('Label !=', 'Tabung')->update('menu_sidebar', ['Label' => 'Tabung']);
}

// Restrukturisasi "Data Dana" jadi induk dgn sub-menu: Data Dana (halaman lama, tetap di /dana),
// Sumbangan, dan Lain-lain (dua tabel baru, terpisah dari data dana utama) - pola sama seperti
// pastikan_menu_pengaturan_user() di atas (leaf lama jadi anak pertama dari dirinya sendiri yg
// jadi pembungkus). Cuma grup 'admin' yang diubah - sidebar Musyrif/Wali tetap flat menuju /dana
// seperti semula (menu ini murni untuk sidebar Admin/Bagian Administrasi).
function pastikan_menu_dana_submenu($db)
{
  if ($db->where('KunciMenu', 'dana_sumbangan')->count_all_results('menu_sidebar') > 0) {
    return;
  }

  $db->trans_start();

  $db->insert('menu_sidebar', [
    'KunciMenu' => 'dana_data', 'ParentKunci' => 'dana', 'Label' => 'Tabung',
    'Url' => 'dana', 'Icon' => 'fas fa-fw fa-wallet',
    'Urutan' => 1, 'Aktif' => 1, 'HanyaAdmin' => 1, 'Grup' => 'admin',
  ]);
  $db->insert('menu_sidebar', [
    'KunciMenu' => 'dana_sumbangan', 'ParentKunci' => 'dana', 'Label' => 'Sumbangan',
    'Url' => 'sumbangan', 'Icon' => 'fas fa-fw fa-hand-holding-heart',
    'Urutan' => 2, 'Aktif' => 1, 'HanyaAdmin' => 1, 'Grup' => 'admin',
  ]);
  $db->insert('menu_sidebar', [
    'KunciMenu' => 'dana_lain', 'ParentKunci' => 'dana', 'Label' => 'Lain-lain',
    'Url' => 'dana-lain', 'Icon' => 'fas fa-fw fa-ellipsis-h',
    'Urutan' => 3, 'Aktif' => 1, 'HanyaAdmin' => 1, 'Grup' => 'admin',
  ]);

  // "Data Dana" jadi pembungkus submenu saja (Url dikosongkan) - begitu dia punya anak,
  // templates/sidebar.php merender link-nya sebagai toggle (href="#"), bukan tautan langsung.
  $db->where('KunciMenu', 'dana')->where('Grup', 'admin')->update('menu_sidebar', ['Url' => null]);

  $db->trans_complete();
}

// Alat bantu Admin: cari & gabungkan akun Wali yang "terpecah" jadi lebih dari satu baris login
// dengan email yang sama (lihat Santri_M::getAkunWaliGanda()). Ditaruh sebagai sub-menu Pengaturan,
// pola sama seperti pastikan_menu_pengaturan_migrasi().
function pastikan_menu_akun_ganda($db)
{
  if ($db->where('KunciMenu', 'pengaturan_akun_ganda')->count_all_results('menu_sidebar') > 0) {
    return;
  }

  $db->insert('menu_sidebar', [
    'KunciMenu' => 'pengaturan_akun_ganda', 'ParentKunci' => 'pengaturan', 'Label' => 'Akun Wali Ganda',
    'Url' => 'akunganda', 'Icon' => 'fas fa-fw fa-user-friends',
    'Urutan' => 5, 'Aktif' => 1, 'HanyaAdmin' => 1, 'Grup' => 'admin',
  ]);
}

// Restrukturisasi "Pengaturan" jadi induk dgn sub-menu: User, Aplikasi, Sidebar, Migrasi (urutan
// diminta user). Dipanggil SETELAH pastikan_menu_pengaturan_aplikasi()/pastikan_menu_pengaturan_migrasi()
// supaya kedua baris itu (sebelumnya menu utama terpisah) sudah pasti ada untuk di-reparent ke sini.
// "Sidebar" sendiri sengaja TIDAK dipindah ke tabel ini - tetap hardcode di sidebar.php (lihat alasan
// di komentar atasnya), cuma posisi render-nya yang dipindah supaya tampil di antara Aplikasi & Migrasi.
function pastikan_menu_pengaturan_user($db)
{
  if ($db->where('KunciMenu', 'pengaturan_user')->count_all_results('menu_sidebar') > 0) {
    return;
  }

  $db->trans_start();

  $db->insert('menu_sidebar', [
    'KunciMenu' => 'pengaturan_user', 'ParentKunci' => 'pengaturan', 'Label' => 'User',
    'Url' => 'pengaturan', 'Icon' => 'fas fa-fw fa-user-cog',
    'Urutan' => 1, 'Aktif' => 1, 'HanyaAdmin' => 1, 'Grup' => 'admin',
  ]);

  // "Pengaturan" jadi pembungkus submenu saja (Url dikosongkan) - pola sama seperti induk lain
  // yang punya anak, mis. "Halaqoh"/"Target QIROATI".
  $db->where('KunciMenu', 'pengaturan')->update('menu_sidebar', ['Url' => null]);

  $db->where('KunciMenu', 'pengaturan_aplikasi')->update('menu_sidebar', [
    'ParentKunci' => 'pengaturan', 'Label' => 'Aplikasi', 'Urutan' => 2,
  ]);

  $db->where('KunciMenu', 'pengaturan_migrasi')->update('menu_sidebar', [
    'ParentKunci' => 'pengaturan', 'Label' => 'Migrasi', 'Urutan' => 4,
  ]);

  $db->trans_complete();
}

// Ditambahkan belakangan (fitur backup & migrasi database) - pola sama seperti pastikan_menu_pengaturan_aplikasi().
function pastikan_menu_pengaturan_migrasi($db)
{
  if ($db->where('KunciMenu', 'pengaturan_migrasi')->count_all_results('menu_sidebar') > 0) {
    return;
  }

  $urutan_maks = $db->select_max('Urutan')->where('Grup', 'admin')->where('ParentKunci', null)->get('menu_sidebar')->row_array();

  $db->insert('menu_sidebar', [
    'KunciMenu' => 'pengaturan_migrasi', 'ParentKunci' => null, 'Label' => 'Backup & Migrasi',
    'Url' => 'pengaturanmigrasi', 'Icon' => 'fas fa-fw fa-database',
    'Urutan' => (int) ($urutan_maks['Urutan'] ?? 0) + 1, 'Aktif' => 1, 'HanyaAdmin' => 1, 'Grup' => 'admin',
  ]);
}

// Ditambahkan belakangan (fitur ganti nama & logo) - per-item, bukan per-grup, dengan alasan
// yang sama seperti item Catatan Pelanggaran/Catatan Santri di pastikan_seed_menu_sidebar_musyrif().
function pastikan_menu_pengaturan_aplikasi($db)
{
  if ($db->where('KunciMenu', 'pengaturan_aplikasi')->count_all_results('menu_sidebar') > 0) {
    return;
  }

  $urutan_maks = $db->select_max('Urutan')->where('Grup', 'admin')->where('ParentKunci', null)->get('menu_sidebar')->row_array();

  $db->insert('menu_sidebar', [
    'KunciMenu' => 'pengaturan_aplikasi', 'ParentKunci' => null, 'Label' => 'Pengaturan Aplikasi',
    'Url' => 'pengaturanaplikasi', 'Icon' => 'fas fa-fw fa-palette',
    'Urutan' => (int) ($urutan_maks['Urutan'] ?? 0) + 1, 'Aktif' => 1, 'HanyaAdmin' => 1, 'Grup' => 'admin',
  ]);
}

function pastikan_seed_menu_sidebar_admin($db)
{
  if ((int) $db->where('Grup', 'admin')->count_all_results('menu_sidebar') > 0) {
    return;
  }

  $db->trans_start();

  // Level atas - persis struktur & urutan sidebar.php sebelum fitur ini ada.
  $atas = [
    ['dana', 'Dana', 'dana', 'fas fa-fw fa-wallet', 1, 1],
    ['santri', 'Data Santri', null, 'fas fa-fw fa-users', 2, 0],
    ['musyrif', 'Pembimbing', 'musyrif', 'fas fa-fw fa-chalkboard-teacher', 3, 0],
    ['kelas', 'Data Kelas', 'kelas', 'fas fa-fw fa-school', 4, 0],
    ['tahfidz', 'Target QIROATI', null, 'fas fa-fw fa-address-book', 5, 0],
    ['halaqoh', 'Halaqoh', null, 'fas fa-fw fa-file-archive', 6, 0],
    ['nilai', 'Ketentuan Nilai', null, 'fas fa-fw fa-tasks', 7, 0],
    ['ujian', 'Ujian Bulanan', null, 'fas fa-fw fa-tasks', 8, 0],
    ['pelanggaran', 'Pelanggaran', null, 'fas fa-fw fa-clipboard', 9, 0],
    ['catatan', 'Catatan Santri', null, 'fas fa-fw fa-file', 10, 0],
    ['pengesahan', 'Pengesahan', 'pengesahan', 'fas fa-fw fa-file-contract', 11, 0],
    ['raport', 'Raport', 'raport', 'fas fa-fw fa-file-archive', 12, 0],
    ['pengaturan', 'Pengaturan', 'pengaturan', 'fas fa-fw fa-cog', 13, 1],
  ];

  foreach ($atas as $a) {
    $db->insert('menu_sidebar', [
      'KunciMenu' => $a[0], 'ParentKunci' => null, 'Label' => $a[1], 'Url' => $a[2],
      'Icon' => $a[3], 'Urutan' => $a[4], 'Aktif' => 1, 'HanyaAdmin' => $a[5], 'Grup' => 'admin',
    ]);
  }

  // Anak - dikelompokkan per induk, urutan dalam grupnya masing-masing mulai dari 1.
  $anak = [
    'santri' => [
      ['santri_data', 'Data Santri', 'santri', 'fas fa-fw fa-user-graduate', 1, 0],
      ['santri_pendaftaran', 'Pendaftaran Baru', 'santri/pendaftaran', 'fas fa-fw fa-user-clock', 2, 0],
      ['santri_absen', 'Absen', 'absen', 'fas fa-fw fa-clipboard-check', 3, 1],
      ['santri_laporan', 'Laporan', 'laporan', 'fas fa-fw fa-chart-bar', 4, 1],
    ],
    'tahfidz' => [
      ['tahfidz_semester', 'Semester', 'tahfidz/semester', 'fas fa-fw fa-calendar', 1, 0],
      ['tahfidz_periode', 'Periode', 'tahfidz/periode', 'fas fa-fw fa-calendar-plus', 2, 0],
      ['tahfidz_ajaran', 'Ajaran', 'tahfidz/ajaran', 'fas fa-fw fa-calendar-check', 3, 0],
      ['tahfidz_target', 'Target', 'tahfidz/target', 'fas fa-check-circle', 4, 0],
      ['tahfidz_detail_target', 'Detail Target', 'tahfidz/detail_target', 'fas fa-info-circle', 5, 0],
    ],
    'halaqoh' => [
      ['halaqoh_kelompok', 'Kelompok', 'halaqoh/kelompok', 'fas fa-fw fa-user-circle', 1, 0],
      ['halaqoh_jadwal', 'Waktu Halaqoh', 'halaqoh/jadwal', 'fas fa-fw fa-calendar-minus', 2, 0],
      ['halaqoh_detail_kelompok', 'Detail Kelompok', 'halaqoh/detail_kelompok', 'fas fa-fw fa-user', 3, 0],
      ['halaqoh_setoran', 'Setoran', 'halaqoh/setoran', 'fas fa-fw fa-address-card', 4, 0],
      ['halaqoh_rekap_setoran', 'Rekap Setoran', 'halaqoh/rekap_setoran', 'fas fa-fw fa-book', 5, 0],
    ],
    'nilai' => [
      ['nilai_predikat_nilai', 'Predikat Nilai', 'nilai/predikat_nilai', 'fas fa-fw fa-calendar-times', 1, 0],
      ['nilai_predikat_hasil1', 'Predikat Hasil I', 'nilai/predikat_hasil1', 'fas fa-fw fa-calendar-times', 2, 0],
      ['nilai_predikat_hasil2', 'Predikat Hasil II', 'nilai/predikat_hasil2', 'fas fa-fw fa-calendar-times', 3, 0],
      ['nilai_predikat_hasil3', 'Predikat Hasil III', 'nilai/predikat_hasil3', 'fas fa-fw fa-calendar-times', 4, 0],
    ],
    'ujian' => [
      ['ujian_periode_ujian', 'Periode Ujian', 'ujian/periode_ujian', 'fas fa-fw fa-calendar-times', 1, 0],
      ['ujian_jenis_ujian', 'Jenis Ujian', 'ujian/jenis_ujian', 'fas fa-fw fa-list', 2, 0],
      ['ujian_target_ujian', 'Target Ujian', 'ujian/target_ujian', 'fas fa-fw fa-list-alt', 3, 0],
      ['ujian_target_ujian_kelas', 'Target Ujian Perkelas', 'ujian/Target_Ujian_Kelas', 'fas fa-fw fa-sort-alpha-down', 4, 0],
      ['ujian_rekap_ujian', 'Rekap Ujian', 'ujian/rekap_ujian', 'fas fa-fw fa-table', 5, 0],
      ['ujian_hasil_ujian', 'Hasil Ujian', 'ujian/hasil_ujian', 'fas fa-fw fa-columns', 6, 0],
    ],
    'pelanggaran' => [
      ['pelanggaran_jenis', 'Jenis Pelanggaran', 'pelanggaran/jenis_pelanggaran', 'fas fa-fw fa-paragraph', 1, 0],
      ['pelanggaran_catatan', 'Catatan Pelanggaran', 'pelanggaran/catatan_pelanggaran', 'fas fa-fw fa-file-alt', 2, 0],
    ],
    'catatan' => [
      ['catatan_jenis', 'Jenis Catatan', 'catatan/jenis_catatan', 'far fa-fw fa-file-alt', 1, 0],
      ['catatan_detail', 'Detail Catatan Santri', 'catatan/detail_catatan', 'fas fa-fw fa-file-code', 2, 0],
      ['catatan_santri', 'Catatan Santri', 'catatan/catatan_santri', 'fas fa-fw fa-file', 3, 0],
    ],
  ];

  foreach ($anak as $parent_kunci => $daftar_anak) {
    foreach ($daftar_anak as $a) {
      $db->insert('menu_sidebar', [
        'KunciMenu' => $a[0], 'ParentKunci' => $parent_kunci, 'Label' => $a[1], 'Url' => $a[2],
        'Icon' => $a[3], 'Urutan' => $a[4], 'Aktif' => 1, 'HanyaAdmin' => $a[5], 'Grup' => 'admin',
      ]);
    }
  }

  $db->trans_complete();
}

/**
 * Seed sidebar Musyrif (templates/sidebar_musyrif.php) - dipakai level Musyrif & Guru.
 * "Dashboard Musyrif" sengaja tidak di sini (selalu tampil), sama seperti "Dashboard" di sidebar admin.
 * Flat (tidak ada sub-menu), jadi semua ParentKunci NULL.
 */
function pastikan_seed_menu_sidebar_musyrif($db)
{
  // Per-item (bukan per-grup) supaya nambah item baru ke daftar ini nanti (seperti Catatan
  // Pelanggaran & Catatan Santri) otomatis ke-insert juga di instalasi yang grup musyrif-nya
  // sudah pernah ke-seed sebelumnya, bukan cuma di instalasi baru.
  $daftar = [
    ['msy_dana', 'Dana', 'dana', 'fas fa-fw fa-wallet', 1],
    ['msy_absen', 'Absen', 'absen', 'fas fa-fw fa-clipboard-check', 2],
    ['msy_laporan', 'Laporan', 'laporan', 'fas fa-fw fa-chart-bar', 3],
    ['msy_setoran', 'Setoran', 'Halaman_Musyrif/setoran_oleh_Musyrif', 'fas fa-fw fa-address-card', 4],
    ['msy_rekap_setoran', 'Rekap Setoran', 'Halaman_Musyrif/rekap_setoran_oleh_Musyrif', 'fas fa-fw fa-book', 5],
    ['msy_profil', 'Profil Saya', 'Halaman_Musyrif/profil', 'fas fa-fw fa-user-circle', 6],
    ['msy_catatan_pelanggaran', 'Catatan Pelanggaran', 'pelanggaran/catatan_pelanggaran', 'fas fa-fw fa-file-alt', 7],
    ['msy_catatan_santri', 'Catatan Santri', 'catatan/catatan_santri', 'fas fa-fw fa-file', 8],
    ['msy_kelas', 'Data Kelas', 'kelas', 'fas fa-fw fa-school', 9],
  ];

  foreach ($daftar as $d) {
    if ($db->where('KunciMenu', $d[0])->count_all_results('menu_sidebar') > 0) {
      continue;
    }

    $db->insert('menu_sidebar', [
      'KunciMenu' => $d[0], 'ParentKunci' => null, 'Label' => $d[1], 'Url' => $d[2],
      'Icon' => $d[3], 'Urutan' => $d[4], 'Aktif' => 1, 'HanyaAdmin' => 0, 'Grup' => 'musyrif',
    ]);
  }
}

/**
 * Seed sidebar Wali (templates/sidebar_wali.php). "Dashboard Wali" & panel profil santri sengaja
 * tidak di sini (selalu tampil), sama seperti Dashboard/Dashboard Musyrif di sidebar lain.
 * Flat (tidak ada sub-menu), jadi semua ParentKunci NULL. Url pakai casing PERSIS seperti href
 * lama di sidebar_wali.php ("Wali/Setoran", bukan "wali/setoran") - dicocokkan case-sensitive
 * oleh $cek_aktif() & hook CekMenuAktif lewat $this->uri->segment().
 */
function pastikan_seed_menu_sidebar_wali($db)
{
  $daftar = [
    ['wali_setoran', 'Setoran Santri', 'Wali/Setoran', 'fas fa-fw fa-address-card', 1],
    ['wali_raport', 'Hasil Ujian (Rapor Santri)', 'Wali/Raport', 'fas fa-fw fa-file-archive', 2],
    ['wali_laporan', 'Laporan Absen', 'laporan', 'fas fa-fw fa-chart-bar', 3],
    ['wali_kelas', 'Data Kelas', 'kelas', 'fas fa-fw fa-school', 4],
    ['wali_dana', 'Data Dana', 'dana', 'fas fa-fw fa-wallet', 5],
    ['wali_statistik', 'Statistik Publik', 'statistik', 'fas fa-fw fa-chart-bar', 6],
    ['wali_profil', 'Profil Saya', 'Wali/profil', 'fas fa-fw fa-user-circle', 7],
  ];

  foreach ($daftar as $d) {
    if ($db->where('KunciMenu', $d[0])->count_all_results('menu_sidebar') > 0) {
      continue;
    }

    $db->insert('menu_sidebar', [
      'KunciMenu' => $d[0], 'ParentKunci' => null, 'Label' => $d[1], 'Url' => $d[2],
      'Icon' => $d[3], 'Urutan' => $d[4], 'Aktif' => 1, 'HanyaAdmin' => 0, 'Grup' => 'wali',
    ]);
  }
}

/**
 * Pengaturan nama & logo aplikasi (dulunya hardcode "QIROATI" + assets/img/qiroati.png di
 * puluhan file). Logo disimpan sebagai path relatif ke folder assets/ - default 'img/qiroati.png'
 * (file bawaan yang sudah ada) supaya sebelum admin pernah upload apa pun, tampilannya identik
 * dengan sebelum fitur ini ada. Setelah upload baru, jadi 'upload/pengaturan/<namafile>'.
 * Dipanggil langsung dari banyak view (sidebar/header/footer/login/dst) - pola $db langsung
 * yang sama seperti pastikan_tabel_menu_sidebar(), bukan lewat model, karena view-view itu
 * dipakai oleh puluhan controller berbeda yang tidak semuanya bisa diubah untuk kirim data ini.
 */
function pastikan_tabel_pengaturan_aplikasi($db)
{
  if (!$db->table_exists('pengaturan_aplikasi')) {
    $db->query("CREATE TABLE pengaturan_aplikasi (
      Id INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
      NamaAplikasi VARCHAR(100) NOT NULL DEFAULT 'QIROATI',
      Logo VARCHAR(150) NOT NULL DEFAULT 'img/qiroati.png',
      PRIMARY KEY (Id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
  }

  if ((int) $db->count_all('pengaturan_aplikasi') === 0) {
    $db->insert('pengaturan_aplikasi', ['NamaAplikasi' => 'QIROATI', 'Logo' => 'img/qiroati.png']);
  }
}

function ambil_pengaturan_aplikasi($db)
{
  pastikan_tabel_pengaturan_aplikasi($db);
  return $db->order_by('Id', 'asc')->limit(1)->get('pengaturan_aplikasi')->row_array();
}

// Kolom Logo bisa berisi 3 bentuk: default bawaan aplikasi ("img/qiroati.png", aset statis di
// assets/img/ - ikut ter-track git, BUKAN upload), data lama sebelum folder upload dipindah keluar
// git (path sebagian "upload/pengaturan/logo_x.png" - lihat PengaturanAplikasi::uploadLogo()), atau
// nama file polos (upload baru, lewat upload_url()). Fungsi ini yang membedakan ketiganya supaya
// view tinggal panggil logo_url($app) tanpa perlu tahu bentuk mana yang tersimpan.
function logo_url($app)
{
  $logo = $app['Logo'];

  if (strpos($logo, 'upload/pengaturan/') === 0) {
    return upload_url('pengaturan', substr($logo, strlen('upload/pengaturan/')));
  }

  if (strpos($logo, '/') === false) {
    return upload_url('pengaturan', $logo);
  }

  return base_url('assets/' . $logo);
}

// Pengumuman (Info Penting): diposting Admin/Musyrif, tampil ke semua akun Wali di dashboard
// mobile-nya (menggantikan bagian Progres Setoran). Dipanggil dari Pengumuman_M (pola sama
// seperti pastikan_tabel_menu_sidebar).
function pastikan_tabel_pengumuman($db)
{
  if (!$db->table_exists('pengumuman')) {
    $db->query("CREATE TABLE pengumuman (
      IdPengumuman INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
      Judul VARCHAR(150) NOT NULL,
      Isi TEXT NOT NULL,
      DibuatOleh VARCHAR(100) NOT NULL,
      CreatedAt DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
      PRIMARY KEY (IdPengumuman)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
  }
}

// Seed menu drawer/sidebar "Info Penting" untuk Admin & Musyrif (idempotent per baris, pola sama
// seperti pastikan_menu_pengaturan_aplikasi() - aman dipanggil di instalasi yang sudah pernah
// ke-seed sebelumnya).
function pastikan_menu_pengumuman($db)
{
  if ($db->where('KunciMenu', 'pengumuman_admin')->count_all_results('menu_sidebar') === 0) {
    $urutan_maks = $db->select_max('Urutan')->where('Grup', 'admin')->where('ParentKunci', null)->get('menu_sidebar')->row_array();
    $db->insert('menu_sidebar', [
      'KunciMenu' => 'pengumuman_admin', 'ParentKunci' => null, 'Label' => 'Info Penting',
      'Url' => 'pengumuman', 'Icon' => 'fas fa-fw fa-bullhorn',
      'Urutan' => (int) ($urutan_maks['Urutan'] ?? 0) + 1, 'Aktif' => 1, 'HanyaAdmin' => 0, 'Grup' => 'admin',
    ]);
  }

  if ($db->where('KunciMenu', 'msy_pengumuman')->count_all_results('menu_sidebar') === 0) {
    $urutan_maks = $db->select_max('Urutan')->where('Grup', 'musyrif')->where('ParentKunci', null)->get('menu_sidebar')->row_array();
    $db->insert('menu_sidebar', [
      'KunciMenu' => 'msy_pengumuman', 'ParentKunci' => null, 'Label' => 'Info Penting',
      'Url' => 'pengumuman', 'Icon' => 'fas fa-fw fa-bullhorn',
      'Urutan' => (int) ($urutan_maks['Urutan'] ?? 0) + 1, 'Aktif' => 1, 'HanyaAdmin' => 0, 'Grup' => 'musyrif',
    ]);
  }
}

// Seed 3 Jenis Iqob baru untuk mendukung rating bintang "Adab Akhlak" otomatis di Rapor - mengikuti
// skala poin yang sudah ada (30-150). Idempotent per baris (pola sama seperti pastikan_menu_pengumuman()),
// dipanggil dari Jenis_pelanggaran_M::__construct().
function pastikan_seed_jenis_iqob_tambahan($db)
{
  $daftar = [
    ['Membawa makanan', 30, 'Ibadah'],
    ['Membawa mainan', 30, 'Ibadah'],
    ['Kertas doa/Qosidah hilang', 40, 'Ibadah'],
  ];

  foreach ($daftar as $d) {
    if ($db->where('JenisIqob', $d[0])->count_all_results('jenispelanggaran') > 0) {
      continue;
    }

    $db->insert('jenispelanggaran', [
      'JenisIqob' => $d[0], 'Poin' => $d[1], 'Kategori' => $d[2],
    ]);
  }
}

// Tema gelap/terang - preferensi PER AKUN (bukan per-HP), supaya ikut terbawa kalau ganti perangkat.
// Dipanggil langsung dari header_generic.php/header_wali.php (pola sama seperti
// ambil_pengaturan_aplikasi()) dan dari Tema::toggle().
function pastikan_kolom_tema_login($db)
{
  if (!$db->field_exists('Tema', 'login')) {
    $db->query("ALTER TABLE login ADD COLUMN Tema ENUM('terang','gelap') NOT NULL DEFAULT 'terang'");
  }
}

function ambil_tema_user($db, $username)
{
  pastikan_kolom_tema_login($db);

  if (!$username) {
    return 'terang';
  }

  $baris = $db->select('Tema')->where('username', $username)->get('login')->row_array();
  return ($baris && $baris['Tema'] === 'gelap') ? 'gelap' : 'terang';
}

// var_dump dari ary
function check($data)
{
  // exit(print("<pre>" . print_r($array_data, true) . "</pre>"));
  var_dump($data);
  die;
}

// Hitung Prosentase pada menu rekap setoran
function prosentase($jml_setoran, $jml_tugas)
{
  return round(($jml_setoran / $jml_tugas) * 100);
}

// Konversi persentase (Kehadiran, Hafalan Doa & Qosidah) jadi rating bintang 1-5 untuk Rapor
// otomatis. NULL berarti belum ada data untuk dihitung - jangan diperlakukan sebagai 0%.
function persentase_ke_bintang($persentase)
{
  if ($persentase === null) {
    return null;
  }
  if ($persentase >= 95) {
    return 5;
  }
  if ($persentase >= 85) {
    return 4;
  }
  if ($persentase >= 70) {
    return 3;
  }
  if ($persentase >= 50) {
    return 2;
  }
  return 1;
}

// Konversi total poin pelanggaran (semua kategori digabung) jadi rating bintang "Adab Akhlak" -
// selalu punya nilai (0 poin = 5 bintang), tidak pernah "belum ada data" seperti persentase_ke_bintang().
function poin_pelanggaran_ke_bintang($total_poin)
{
  if ($total_poin <= 0) {
    return 5;
  }
  if ($total_poin <= 40) {
    return 4;
  }
  if ($total_poin <= 100) {
    return 3;
  }
  if ($total_poin <= 200) {
    return 2;
  }
  return 1;
}

function pilihan_jenis_kelamin()
{
  return ['Laki-laki', 'Perempuan'];
}

// Pilihan Nama Kelas pada Formulir Tambah Kelas.
function pilihan_nama_kelas()
{
  return ['Jilid 1', 'Jilid 2', 'Jilid 3', 'Jilid 4', 'Jilid 5', "Al-Qur'an"];
}

// Validasi tanggal format Y-m-d (dipakai callback_cek_tanggal di form_validation).
function tanggal_valid($value)
{
  if ($value === '') {
    return FALSE;
  }

  $tanggal = DateTime::createFromFormat('Y-m-d', $value);
  return $tanggal && $tanggal->format('Y-m-d') === $value;
}
