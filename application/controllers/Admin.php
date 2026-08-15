<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Admin extends CI_Controller
{

  public function __construct()
  {
    parent::__construct();
    cek_login();
    $this->load->model('Periode_M');
    $this->load->model('Kelompok_M');
    $this->load->model('Rekap_setoran_M');
    $this->load->model('Pengumuman_M');
  }


  public function index()
  {
    $IdPeriode = $this->input->get('periode');
    $IdKelompok = $this->input->get('kelompok');

    $wrapper = tampilan_mobile() ? 'templates/wrapper-mobile-simple' : 'templates/wrapper-admin';
    $isi = tampilan_mobile() ? 'admin/mobile-dashboard' : 'admin/dashboard';

    if (!$IdPeriode && $IdKelompok) {
      $data = [
        'title' => 'Dashboard',
        'user' => $this->db->get_where('login', ['username' => $this->session->userdata('username')])->row_array(),
        'periode' => $this->Periode_M->getAllPeriode(),
        'kelompok_halaqoh' => $this->Kelompok_M->getAllKelompok(),
        'rekap_setoran_kelompok' => '',
        'pengumuman' => $this->Pengumuman_M->getTerbaruUntukWali(),
        'isi' => $isi,
      ];

      $this->load->view($wrapper, $data);
    } else {
      $data = [
        'title' => 'Dashboard',
        'user' => $this->db->get_where('login', ['username' => $this->session->userdata('username')])->row_array(),
        'periode' => $this->Periode_M->getAllPeriode(),
        'kelompok_halaqoh' => $this->Kelompok_M->getAllKelompok(),
        'rekap_setoran_kelompok' => $this->Rekap_setoran_M->getRekapSetoranBy_Kelompok_Periode($IdPeriode, $IdKelompok),
        'pengumuman' => $this->Pengumuman_M->getTerbaruUntukWali(),
        'isi' => $isi,
      ];

      $this->load->view($wrapper, $data);
    }
  }
}

/* End of file Admin.php */
