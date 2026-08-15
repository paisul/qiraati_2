<?php
defined('BASEPATH') or exit('No direct script access allowed');

// Pengaturan Sidebar: admin mengatur aktif/nonaktif & urutan menu sidebar secara mandiri.
class PengaturanSidebar extends CI_Controller
{
  public function __construct()
  {
    parent::__construct();
    cek_level('Admin');
    $this->load->model('MenuSidebar_M');
  }

  public function index()
  {
    $data = [
      'title' => 'Pengaturan Sidebar',
      'user' => $this->getUserLogin(),
      'menu_admin' => $this->MenuSidebar_M->getSemuaMenu('admin'),
      'menu_musyrif' => $this->MenuSidebar_M->getSemuaMenu('musyrif'),
      'menu_wali' => $this->MenuSidebar_M->getSemuaMenu('wali'),
      'isi' => tampilan_mobile() ? 'pengaturan_sidebar/mobile-index' : 'pengaturan_sidebar/index',
    ];

    $this->load->view(tampilan_mobile() ? 'templates/wrapper-mobile-simple' : 'templates/wrapper-admin', $data);
  }

  // AJAX: urutan diubah lewat drag-and-drop, tersimpan seketika.
  public function simpan_urutan()
  {
    $payload = json_decode(file_get_contents('php://input'), true);
    $daftar = isset($payload['daftar']) && is_array($payload['daftar']) ? $payload['daftar'] : [];

    if (!$daftar) {
      echo json_encode(['status' => false, 'message' => 'Tidak ada perubahan urutan.']);
      return;
    }

    $ok = $this->MenuSidebar_M->simpanUrutan($daftar);
    echo json_encode(['status' => (bool) $ok]);
  }

  public function toggle_aktif($id)
  {
    $ok = $this->MenuSidebar_M->toggleAktif($id);
    echo json_encode(['status' => $ok]);
  }

  // AJAX: toggle tampil/tidaknya ikon menu ini di dashboard mobile (Musyrif/Wali) - terpisah dari
  // Aktif (yang memblokir akses halamannya sekalian).
  public function toggle_dashboard($id)
  {
    $ok = $this->MenuSidebar_M->toggleTampilDashboard($id);
    echo json_encode(['status' => $ok]);
  }

  // AJAX: sama seperti toggle_dashboard() tapi untuk ikon di menu bawah (bottom nav) mobile.
  public function toggle_menu_bawah($id)
  {
    $ok = $this->MenuSidebar_M->toggleTampilMenuBawah($id);
    echo json_encode(['status' => $ok]);
  }

  private function getUserLogin()
  {
    return $this->db->get_where('login', ['username' => $this->session->userdata('username')])->row_array();
  }
}

/* End of file PengaturanSidebar.php */
