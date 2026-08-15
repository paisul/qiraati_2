<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Jenis_Ujian extends CI_Controller
{

  public function __construct()
  {
    parent::__construct();
    //Load Dependencies
    cek_login();
    $this->load->model('Jenis_ujian_M');
  }

  // List all your items
  public function index($offset = 0)
  {
    $pesan = $this->input->get('pesan') ?: $this->session->flashdata('pesan');

    $data = [
      'title' => 'Jenis Ujian',
      'user' => $this->db->get_where('login', ['username' => $this->session->userdata('username')])->row_array(),
      'jenis_ujian' => $this->Jenis_ujian_M->getAllJenisUjian(),
      'pesan' => $pesan,
      'isi' => tampilan_mobile() ? 'ujian/mobile-jenis_ujian' : 'ujian/v-jenis_ujian',
    ];
    $this->load->view(tampilan_mobile() ? 'templates/wrapper-mobile-simple' : 'templates/wrapper-admin', $data);
  }

  // Add a new item
  public function add()
  {
    $data = [
      'NamaUjian' => $this->input->post('ujian')
    ];
    $this->Jenis_ujian_M->addJenisUjian($data);
    redirect('ujian/jenis_ujian?pesan=' . rawurlencode('Berhasil ditambahkan!'));
  }

  //Update one item
  public function update($id)
  {
    $data = [
      'IdJenisUjian' => $id,
      'NamaUjian' => $this->input->post('ujian')
    ];
    $this->Jenis_ujian_M->updateJenisUjian($data);
    redirect('ujian/jenis_ujian?pesan=' . rawurlencode('Berhasil diubah!'));
  }

  //Delete one item
  public function delete($id)
  {
    $data = [
      'IdJenisUjian' => $id
    ];
    $this->Jenis_ujian_M->deleteJenisUjian($data);
    redirect('ujian/jenis_ujian?pesan=' . rawurlencode('Berhasil dihapus!'));
  }

  public function export_excel()
  {
    $data = [
      'title' => 'Jenis Ujian',
      'user' => $this->db->get_where('login', ['username' => $this->session->userdata('username')])->row_array(),
      'jenis_ujian' => $this->Jenis_ujian_M->getAllJenisUjian(),
    ];

    $this->load->view('export/excel/ujian/jenis_ujian', $data);
  }
}

/* End of file Jenis_Ujian.php */
