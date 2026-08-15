<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Ajaran extends CI_Controller
{

  public function __construct()
  {
    parent::__construct();
    //Load Dependencies
    cek_login();
    $this->load->model('Ajaran_M');
  }

  // List all your items
  public function index($offset = 0)
  {
    $pesan = $this->input->get('pesan') ?: $this->session->flashdata('pesan');

    $data = [
      'title' => 'Data Ajaran',
      'user' => $this->db->get_where('login', ['username' => $this->session->userdata('username')])->row_array(),
      'ajaran' => $this->Ajaran_M->getAllAjaran(),
      'pesan' => $pesan,
      'isi' => tampilan_mobile() ? 'target-tahfidz/mobile-ajaran' : 'target-tahfidz/v-ajaran',
    ];
    $this->load->view(tampilan_mobile() ? 'templates/wrapper-mobile-simple' : 'templates/wrapper-admin', $data);
  }

  // Add a new item
  public function add()
  {
    $data = [
      'ThAjaran' => $this->input->post('ajaran')
    ];
    $this->Ajaran_M->addAjaran($data);
    redirect('tahfidz/ajaran?pesan=' . rawurlencode('Berhasil ditambahkan!'));
  }

  //Update one item
  public function update($id)
  {
    $data = [
      'IdAjaran' => $id,
      'ThAjaran' => $this->input->post('ajaran')
    ];
    $this->Ajaran_M->updateAjaran($data);
    redirect('tahfidz/ajaran?pesan=' . rawurlencode('Berhasil diubah!'));
  }

  //Delete one item
  public function delete($id)
  {
    $data = [
      'IdAjaran' => $id
    ];
    $berhasil = $this->Ajaran_M->deleteAjaran($data);
    redirect('tahfidz/ajaran?pesan=' . rawurlencode($berhasil
      ? 'Berhasil dihapus!'
      : 'Ajaran ini tidak dapat dihapus karena masih dipakai di data Periode Ujian. Hapus/ubah dulu periode ujian yang memakainya.'));
  }

  public function export_excel()
  {
    $data = [
      'title' => 'Data Ajaran',
      'user' => $this->db->get_where('login', ['username' => $this->session->userdata('username')])->row_array(),
      'ajaran' => $this->Ajaran_M->getAllAjaran(),
    ];

    $this->load->view('export/excel/tahfidz/ajaran', $data);
  }
}

/* End of file Ajaran.php */
