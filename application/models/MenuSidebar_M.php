<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Konfigurasi menu sidebar admin (Pengaturan Sidebar): sumber tunggal urutan/status aktif
 * yang dipakai templates/sidebar.php dan hook CekMenuAktif (blokir akses URL langsung).
 * Migrasi tabel & seed ada di helper pastikan_tabel_menu_sidebar() (tahfidz_helper.php),
 * dipakai bersama oleh model ini, hook, dan sidebar.php - lihat komentar di helper itu kenapa.
 *
 * "Dashboard" dan "Pengaturan Sidebar" sengaja TIDAK ada di tabel ini (selalu tampil,
 * tidak bisa dinonaktifkan) - supaya admin tidak pernah bisa mengunci diri sendiri.
 */
class MenuSidebar_M extends CI_Model
{
  public function __construct()
  {
    parent::__construct();
    pastikan_tabel_menu_sidebar($this->db);
  }

  // Dipakai halaman Pengaturan Sidebar - semua baris (termasuk yang nonaktif), dikelompokkan per induk.
  // $grup: 'admin' (templates/sidebar.php), 'musyrif' (templates/sidebar_musyrif.php), atau
  // 'wali' (templates/sidebar_wali.php).
  public function getSemuaMenu($grup)
  {
    return $this->kelompokkan(
      $this->db->where('Grup', $grup)->where('ParentKunci', null)->order_by('Urutan', 'asc')->get('menu_sidebar')->result_array(),
      $this->db->where('Grup', $grup)->where('ParentKunci IS NOT NULL')->order_by('Urutan', 'asc')->get('menu_sidebar')->result_array()
    );
  }

  // Dipakai templates/sidebar.php & sidebar_musyrif.php - hanya yang Aktif=1.
  public function getMenuAktifUntukSidebar($grup)
  {
    return $this->kelompokkan(
      $this->db->where('Grup', $grup)->where('ParentKunci', null)->where('Aktif', 1)->order_by('Urutan', 'asc')->get('menu_sidebar')->result_array(),
      $this->db->where('Grup', $grup)->where('ParentKunci IS NOT NULL')->where('Aktif', 1)->order_by('Urutan', 'asc')->get('menu_sidebar')->result_array()
    );
  }

  private function kelompokkan($atas, $semua_anak)
  {
    $anak_per_induk = [];
    foreach ($semua_anak as $a) {
      $anak_per_induk[$a['ParentKunci']][] = $a;
    }

    foreach ($atas as &$item) {
      $item['anak'] = isset($anak_per_induk[$item['KunciMenu']]) ? $anak_per_induk[$item['KunciMenu']] : [];
    }

    return $atas;
  }

  /**
   * Dipakai hook CekMenuAktif. Kalau $url tidak dikenal (bukan menu yang dikelola fitur ini di
   * grup itu), dianggap aktif (tidak diblok). $grup WAJIB diisi karena url yang sama (mis. "absen")
   * bisa ada di grup admin & musyrif sekaligus dengan status Aktif yang berbeda-beda.
   */
  public function isUrlAktif($url, $grup)
  {
    return $this->isKolomAktif($url, $grup, 'Aktif');
  }

  // Dipakai shortcut dashboard mobile Musyrif/Wali - beda dari Aktif (yang memblokir akses
  // halamannya sekalian), ini murni soal tampil/tidaknya ikon di dashboard.
  public function isTampilDashboard($url, $grup)
  {
    return $this->isKolomAktif($url, $grup, 'TampilDashboard');
  }

  // Dipakai bottom nav mobile Musyrif/Wali - sama seperti isTampilDashboard() tapi untuk menu bawah.
  public function isTampilMenuBawah($url, $grup)
  {
    return $this->isKolomAktif($url, $grup, 'TampilMenuBawah');
  }

  private function isKolomAktif($url, $grup, $kolom)
  {
    $baris = $this->db->where('Url', $url)->where('Grup', $grup)->get('menu_sidebar')->row_array();
    if (!$baris) {
      return true;
    }
    return (bool) $baris[$kolom];
  }

  public function toggleAktif($id)
  {
    return $this->toggleKolom($id, 'Aktif');
  }

  public function toggleTampilDashboard($id)
  {
    return $this->toggleKolom($id, 'TampilDashboard');
  }

  public function toggleTampilMenuBawah($id)
  {
    return $this->toggleKolom($id, 'TampilMenuBawah');
  }

  private function toggleKolom($id, $kolom)
  {
    $baris = $this->db->get_where('menu_sidebar', ['IdMenu' => $id])->row_array();
    if (!$baris) {
      return false;
    }

    $this->db->where('IdMenu', $id);
    $this->db->update('menu_sidebar', [$kolom => $baris[$kolom] ? 0 : 1]);
    return true;
  }

  // $daftar: [['IdMenu' => x, 'Urutan' => y], ...]. Reorder dibatasi dalam levelnya sendiri -
  // method ini sengaja tidak menerima/mengubah ParentKunci (tidak boleh pindah grup).
  public function simpanUrutan($daftar)
  {
    $this->db->trans_start();
    foreach ($daftar as $item) {
      $this->db->where('IdMenu', $item['IdMenu']);
      $this->db->update('menu_sidebar', ['Urutan' => (int) $item['Urutan']]);
    }
    $this->db->trans_complete();
    return $this->db->trans_status();
  }
}

/* End of file MenuSidebar_M.php */
