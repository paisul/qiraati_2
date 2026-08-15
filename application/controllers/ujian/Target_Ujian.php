<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Target_Ujian extends CI_Controller
{

  public function __construct()
  {
    parent::__construct();
    //Load Dependencies
    cek_login();
    $this->load->model('Target_ujian_M');
    $this->load->model('Jenis_ujian_M');
  }

  // List all your items
  public function index()
  {
    $pesan = $this->input->get('pesan') ?: $this->session->flashdata('pesan');

    $data = [
      'title'   => 'Data Target Ujian',
      'user'    => $this->db->get_where('login', ['username' => $this->session->userdata('username')])->row_array(),
      'jenis_ujian' => $this->Jenis_ujian_M->getAllJenisUjian(),
      'target_ujian' => $this->Target_ujian_M->getAllTargetUjian(),
      'pesan' => $pesan,
      'isi' => tampilan_mobile() ? 'ujian/mobile-target_ujian' : 'ujian/v-target_ujian'
    ];
    $this->load->view(tampilan_mobile() ? 'templates/wrapper-mobile-simple' : 'templates/wrapper-admin', $data);
  }

  // Add a new item
  public function add()
  {
    $data = [
      'IdJenisUjian' => $this->input->post('jenis_ujian'),
      'Keterangan' => $this->input->post('keterangan')
    ];
    $this->Target_ujian_M->addTargetUjian($data);
    redirect('ujian/target_ujian?pesan=' . rawurlencode('Berhasil ditambahkan!'));
  }

  //Update one item
  public function update($id)
  {
    $data = [
      'IdTargetUjian' => $id,
      'IdJenisUjian' => $this->input->post('jenis_ujian'),
      'Keterangan' => $this->input->post('keterangan')
    ];
    $this->Target_ujian_M->updateTargetUjian($data);
    redirect('ujian/target_ujian?pesan=' . rawurlencode('Berhasil diubah!'));
  }

  //Delete one item
  public function delete($id)
  {
    $data = ['IdTargetUjian' => $id];
    $this->Target_ujian_M->deleteTargetUjian($data);
    redirect('ujian/target_ujian?pesan=' . rawurlencode('Berhasil dihapus!'));
  }

  public function export_excel()
  {
    $data = [
      'title'   => 'Data Target Ujian',
      'user'    => $this->db->get_where('login', ['username' => $this->session->userdata('username')])->row_array(),
      'jenis_ujian' => $this->Jenis_ujian_M->getAllJenisUjian(),
      'target_ujian' => $this->Target_ujian_M->getAllTargetUjian(),
    ];

    $this->load->view('export/excel/ujian/target_ujian', $data);
  }
}

/* End of file Target_Ujian.php */
