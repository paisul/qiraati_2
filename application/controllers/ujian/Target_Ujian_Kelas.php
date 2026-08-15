<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Target_Ujian_Kelas extends CI_Controller
{

  public function __construct()
  {
    parent::__construct();
    //Load Dependencies
    $this->load->model('Target_ujian_kelas_M');
    $this->load->model('Kelas_M');
    $this->load->model('Target_ujian_M');
  }

  // List all your items
  public function index()
  {
    $pesan = $this->input->get('pesan') ?: $this->session->flashdata('pesan');

    $data = [
      'title'   => 'Data Target Ujian Perkelas',
      'user'    => $this->db->get_where('login', ['username' => $this->session->userdata('username')])->row_array(),
      'target_ujian' => $this->Target_ujian_M->getAllTargetUjian(),
      'kelas' => $this->Kelas_M->getAllKelas(),
      'target_ujian_kelas' => $this->Target_ujian_kelas_M->getAllTargetUjianKelas(),
      'pesan' => $pesan,
      'isi' => tampilan_mobile() ? 'ujian/mobile-target_ujian_kelas' : 'ujian/v-target_ujian_kelas'
    ];
    $this->load->view(tampilan_mobile() ? 'templates/wrapper-mobile-simple' : 'templates/wrapper-admin', $data);
  }

  // Add a new item
  public function add()
  {
    $idKelas = $this->input->post('kelas');
    $targetujian = $this->input->post('targetujian');
    $data = [];
    foreach ($targetujian as $val => $tu) {
      $data[$val]['IdKelas'] = $idKelas;
      $data[$val]['IdTargetUjian'] = $tu;
    }
    // check($data);
    $this->Target_ujian_kelas_M->addTargetKelas($data);
    redirect('ujian/Target_Ujian_Kelas?pesan=' . rawurlencode('Berhasil ditambahkan!'));
  }

  public function add_tunggal()
  {
    $idKelas = $this->input->post('kelas');
    $targetujian = $this->input->post('targetujian');

    $data = [
      'IdKelas' => $idKelas,
      'IdTargetUjian' => $targetujian
    ];
    // check($data);
    $this->Target_ujian_kelas_M->addTarget($data);
    redirect('ujian/Target_Ujian_Kelas?pesan=' . rawurlencode('Berhasil ditambahkan!'));
  }

  //Update one item
  public function update($id = NULL)
  {
  }

  //Delete one item
  public function delete($id)
  {
    $idTargetKelas = $id;
    $data = [
      'IdTargetKelas' => $idTargetKelas
    ];
    $this->Target_ujian_kelas_M->hapusTargetKelas($data);
    redirect('ujian/Target_Ujian_Kelas?pesan=' . rawurlencode('Berhasil dihapus!'));
  }
}

/* End of file Target_Ujian_Kelas.php */
