<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Statistik extends CI_Controller
{
  public function __construct()
  {
    parent::__construct();
    $this->load->model('Statistik_public_M');
  }

  public function index()
  {
    $data = [
      'title' => 'Statistik Publik',
      'ringkasan' => $this->Statistik_public_M->getRingkasan(),
      'murid_per_kelas' => $this->Statistik_public_M->getMuridPerKelas(),
      'status_murid' => $this->Statistik_public_M->getStatusMurid(),
    ];

    $this->load->view('public/statistik', $data);
  }
}

/* End of file Statistik.php */
