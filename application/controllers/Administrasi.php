<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Administrasi extends CI_Controller
{

  public function __construct()
  {
    parent::__construct();
    cek_login();
    $this->load->model('Periode_M');
    $this->load->model('Kelompok_M');
    $this->load->model('Rekap_setoran_M');
  }


  public function index()
  {
    $IdPeriode = $this->input->get('periode');
    $IdKelompok = $this->input->get('kelompok');
    // Lihat komentar di Dana::index() - query string ?pesan= dipakai supaya redirect dari hook
    // CekMenuAktif (menu dinonaktifkan admin) tidak nyangkut lewat session flashdata.
    $pesan = $this->input->get('pesan') ?: $this->session->flashdata('pesan');

    $isi = tampilan_mobile() ? 'administrasi/mobile-dashboard' : 'administrasi/dashboard';
    $wrapper = tampilan_mobile() ? 'templates/wrapper-mobile-simple' : 'templates/wrapper-admin';

    if (!$IdPeriode && $IdKelompok) {
      $data = [
        'title' => 'Dashboard Administrasi',
        'user' => $this->db->get_where('login', ['username' => $this->session->userdata('username')])->row_array(),
        'periode' => $this->Periode_M->getAllPeriode(),
        'kelompok_halaqoh' => $this->Kelompok_M->getAllKelompok(),
        'rekap_setoran_kelompok' => '',
        'pesan' => $pesan,
        'isi' => $isi,
      ];
      $this->load->view($wrapper, $data);
    } else {
      $data = [
        'title' => 'Dashboard Administrasi',
        'user' => $this->db->get_where('login', ['username' => $this->session->userdata('username')])->row_array(),
        'periode' => $this->Periode_M->getAllPeriode(),
        'kelompok_halaqoh' => $this->Kelompok_M->getAllKelompok(),
        'rekap_setoran_kelompok' => $this->Rekap_setoran_M->getRekapSetoranBy_Kelompok_Periode($IdPeriode, $IdKelompok),
        'pesan' => $pesan,
        'isi' => $isi,
      ];
      $this->load->view($wrapper, $data);
    }
  }
}

/* End of file Admin.php */
