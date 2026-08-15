<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Laporan extends CI_Controller
{
  public function __construct()
  {
    parent::__construct();
    cek_level(['Admin', 'Musyrif', 'Guru', 'Wali']);
    $this->load->model('Laporan_M');
    $this->load->model('Musyrif_M');
    $this->load->model('Wali_M');
  }

  public function index($jenis = 'santri')
  {
    $is_wali = $this->session->userdata('level') == 'Wali';
    // Wali hanya boleh melihat rekap absen anaknya sendiri, bukan seluruh santri.
    $jenis = ($jenis == 'pembimbing' && !$is_wali) ? 'pembimbing' : 'santri';
    $tanggal_awal = $this->input->get('tanggal_awal') ? $this->input->get('tanggal_awal') : date('Y-m-01');
    $tanggal_akhir = $this->input->get('tanggal_akhir') ? $this->input->get('tanggal_akhir') : date('Y-m-d');
    $id_kelas = $this->input->get('id_kelas') ? $this->input->get('id_kelas') : '';
    $nis = trim((string) $this->input->get('nis'));
    $gender = $this->input->get('gender') ? $this->input->get('gender') : '';
    $urutkan_diizinkan = ['kelas', 'gender', 'usia'];
    $urutkan = in_array($this->input->get('urutkan'), $urutkan_diizinkan, TRUE) ? $this->input->get('urutkan') : '';
    $id_siswa_wali = null;

    if ($is_wali) {
      $id_siswa_wali = $this->getWaliLogin()['IdSiswa'];
      $id_kelas = '';
    }

    $rekap = $jenis == 'pembimbing'
      ? $this->Laporan_M->getRekapAbsensiPembimbing($tanggal_awal, $tanggal_akhir)
      : $this->Laporan_M->getRekapAbsensi($tanggal_awal, $tanggal_akhir, $id_kelas ?: null, $id_siswa_wali, $nis ?: null, $gender ?: null, $urutkan ?: null);

    $data = [
      'title' => 'Laporan Absensi',
      'user' => $this->getUserLogin(),
      'jenis' => $jenis,
      'tanggal_awal' => $tanggal_awal,
      'tanggal_akhir' => $tanggal_akhir,
      'id_kelas' => $id_kelas,
      'nis' => $nis,
      'gender' => $gender,
      'urutkan' => $urutkan,
      // Panel filter lanjutan otomatis terbuka kalau salah satu isinya sedang aktif dipakai.
      'filter_lanjutan_aktif' => (bool) ($id_kelas || $gender || $urutkan),
      'jenis_kelamin_list' => pilihan_jenis_kelamin(),
      'kelas' => $this->Laporan_M->getKelas(),
      'rekap' => $rekap,
      'is_wali' => $is_wali,
      'isi' => tampilan_mobile() ? 'laporan/mobile-index' : 'laporan/index',
    ];

    $this->load->view($this->getWrapper(), $data);
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
      return $this->getWaliLogin();
    }

    return $this->db->get_where('login', ['username' => $username])->row_array();
  }

  // Sama seperti Wali::getWaliLogin() - hentikan & arahkan ke login jika akun wali belum terhubung ke data santri.
  private function getWaliLogin()
  {
    $wali = $this->Wali_M->getDataWali($this->session->userdata('username'));

    if (!$wali) {
      $this->session->unset_userdata('username');
      $this->session->unset_userdata('level');
      $this->session->set_flashdata('message', '<div class="alert alert-danger" role="alert">Akun wali belum terhubung dengan data santri. Silahkan hubungi admin.</div>');
      redirect('auth');
      exit;
    }

    return $wali;
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

/* End of file Laporan.php */
