<?php
defined('BASEPATH') or exit('No direct script access allowed');

// Kloning Dana.php - lihat komentar di sana untuk penjelasan pola (pesan lewat ?pesan=, wrapper
// per-role, dst). Tabel & datanya terpisah (lihat Dana_lain_M), tapi alur & hak aksesnya sama persis.
// URL publiknya "dana-lain" (pakai tanda hubung) - butuh rute eksplisit di routes.php karena tanda
// hubung tidak valid di nama class PHP.
class Dana_lain extends CI_Controller
{
  public function __construct()
  {
    parent::__construct();
    cek_level(['Admin', 'Musyrif', 'Guru', 'Wali']);
    $this->load->model('Dana_lain_M');
    $this->load->model('Musyrif_M');
    $this->load->model('Wali_M');
  }

  public function index()
  {
    $pesan = $this->input->get('pesan') ?: $this->session->flashdata('pesan');

    $data = [
      'title' => 'Dana Lain-lain',
      'user' => $this->getUserLogin(),
      'dana' => $this->Dana_lain_M->getAllDanaLain(),
      'ringkasan' => $this->Dana_lain_M->getRingkasan(),
      'pesan' => $pesan,
      'isi' => tampilan_mobile() ? 'dana_lain/mobile-index' : 'dana_lain/index',
    ];

    $this->load->view($this->getWrapper(), $data);
  }

  public function add()
  {
    $this->tolakJikaWali();
    $this->Dana_lain_M->addDanaLain($this->inputDanaLain());
    redirect('dana-lain?pesan=' . rawurlencode('Berhasil ditambahkan!'));
  }

  public function update($id)
  {
    $this->tolakJikaWali();
    $this->Dana_lain_M->updateDanaLain($id, $this->inputDanaLain(TRUE));
    redirect('dana-lain?pesan=' . rawurlencode('Berhasil diubah!'));
  }

  public function delete($id)
  {
    $this->tolakJikaWali();
    $this->Dana_lain_M->deleteDanaLain($id);
    redirect('dana-lain?pesan=' . rawurlencode('Berhasil dihapus!'));
  }

  // Wali hanya boleh melihat (read-only) - tolak percobaan tambah/ubah/hapus lewat URL langsung.
  private function tolakJikaWali()
  {
    if ($this->session->userdata('level') == 'Wali') {
      redirect('dana-lain?pesan=' . rawurlencode('Akun wali hanya dapat melihat data ini.'));
      exit;
    }
  }

  private function inputDanaLain($is_update = FALSE)
  {
    $data = [
      'Tanggal' => $this->input->post('tanggal'),
      'Perihal' => $this->input->post('perihal'),
      'JumlahMasuk' => $this->formatNominal($this->input->post('jumlah_masuk')),
      'JumlahKeluar' => $this->formatNominal($this->input->post('jumlah_keluar')),
    ];

    if ($is_update) {
      $data['UpdatedAt'] = date('Y-m-d H:i:s');
    } else {
      $data['CreatedAt'] = date('Y-m-d H:i:s');
    }

    return $data;
  }

  private function formatNominal($nominal)
  {
    $nominal = preg_replace('/[^0-9.]/', '', (string) $nominal);
    $nominal = $nominal === '' ? 0 : $nominal;
    return max(0, (float) $nominal);
  }

  private function getUserLogin()
  {
    $username = $this->session->userdata('username');

    if ($this->session->userdata('level') == 'Musyrif') {
      $user = $this->Musyrif_M->getDataMusyrif($username);
      return $user ? $user : $this->db->get_where('login', ['username' => $username])->row_array();
    }

    if ($this->session->userdata('level') == 'Guru') {
      $user = $this->db->get_where('login', ['username' => $username])->row_array();
      $user['NamaMusyrif'] = $user['username'];
      $user['Email'] = $user['level'];
      return $user;
    }

    if ($this->session->userdata('level') == 'Wali') {
      $wali = $this->Wali_M->getDataWali($username);

      if (!$wali) {
        $this->session->unset_userdata('username');
        $this->session->unset_userdata('level');
        $this->session->set_flashdata('message', '<div class="alert alert-danger" role="alert">Akun wali belum terhubung dengan data santri. Silahkan hubungi admin.</div>');
        redirect('auth');
        exit;
      }

      return $wali;
    }

    return $this->db->get_where('login', ['username' => $username])->row_array();
  }

  private function getWrapper()
  {
    if ($this->session->userdata('level') == 'Musyrif' || $this->session->userdata('level') == 'Guru') {
      return tampilan_mobile() ? 'templates/wrapper-musyrif-mobile' : 'templates/wrapper-musyrif';
    }

    if ($this->session->userdata('level') == 'Wali') {
      return tampilan_mobile() ? 'templates/wrapper-wali-mobile' : 'templates/wrapper-wali';
    }

    return tampilan_mobile() ? 'templates/wrapper-mobile-simple' : 'templates/wrapper-admin';
  }
}

/* End of file Dana_lain.php */
