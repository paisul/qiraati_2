<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Hook post_controller_constructor: blokir akses URL langsung ke menu yang dinonaktifkan lewat
 * Pengaturan Sidebar. Admin selalu lolos (tidak pernah terkunci dari fiturnya sendiri, karena
 * Admin sendiri yang mengatur toggle-nya).
 *
 * - Bagian Administrasi -> dicek terhadap grup 'admin' (audiens kedua templates/sidebar.php).
 * - Musyrif & Guru -> dicek terhadap grup 'musyrif' (templates/sidebar_musyrif.php, dipakai bersama).
 * - Wali -> dicek terhadap grup 'wali' (templates/sidebar_wali.php).
 *
 * Beberapa controller (kelas, absen, laporan, dana) dipakai lintas-role lewat jalur lain (mis.
 * Wali baca-saja ke Kelas, atau Admin/Musyrif ke controller yang sama) - itu AMAN karena setiap
 * baris menu_sidebar terikat ke Grup masing-masing (WHERE Url=... AND Grup=...), jadi toggle
 * Wali atas "Data Kelas" tidak pernah menyentuh baris toggle Admin/Musyrif atas URL yang sama.
 *
 * Dipasang di post_controller_constructor (bukan pre_controller) supaya get_instance() dan
 * autoload session/db/helper sudah siap - CI_Controller::$instance baru ada setelah controller
 * selesai di-construct, get_instance() akan gagal kalau dipanggil lebih awal dari itu.
 *
 * Pesan blokir dikirim lewat query string ?pesan= (bukan session flashdata) - pola sama seperti
 * Dana (lihat komentar di Dana::index()), karena flashdata di server ini kadang tidak kadaluarsa
 * dengan benar dan bikin popup ini nyangkut muncul lagi di halaman-halaman lain setelahnya.
 */
class CekMenuAktif
{
  public function cek()
  {
    $CI = &get_instance();
    $level = $CI->session->userdata('level');

    if ($level === 'Bagian Administrasi') {
      $grup = 'admin';
      $redirect_ke = 'Administrasi';
    } elseif ($level === 'Musyrif' || $level === 'Guru') {
      $grup = 'musyrif';
      $redirect_ke = 'Halaman_Musyrif';
    } elseif ($level === 'Wali') {
      $grup = 'wali';
      $redirect_ke = 'Wali/index';
    } else {
      // Admin atau belum login sama sekali - tidak disentuh.
      return;
    }

    pastikan_tabel_menu_sidebar($CI->db);

    $segmen1 = $CI->uri->segment(1);
    $segmen2 = $CI->uri->segment(2);

    if (!$segmen1) {
      return;
    }

    $CI->load->model('MenuSidebar_M');

    // Coba cocokkan url 2-segmen dulu (mis. tahfidz/semester), baru fallback 1-segmen (mis. musyrif).
    if ($segmen2 && !$CI->MenuSidebar_M->isUrlAktif($segmen1 . '/' . $segmen2, $grup)) {
      redirect($redirect_ke . '?pesan=' . rawurlencode('Menu ini sedang dinonaktifkan oleh admin.'));
      return;
    }

    if (!$CI->MenuSidebar_M->isUrlAktif($segmen1, $grup)) {
      redirect($redirect_ke . '?pesan=' . rawurlencode('Menu ini sedang dinonaktifkan oleh admin.'));
    }
  }
}

/* End of file CekMenuAktif.php */
