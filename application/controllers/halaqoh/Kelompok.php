<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Kelompok extends CI_Controller
{

  public function __construct()
  {
    parent::__construct();
    //Load Dependencies
    cek_login();
    $this->load->model('Kelompok_M');
  }

  // List all your items
  public function index()
  {
    $pesan = $this->input->get('pesan') ?: $this->session->flashdata('pesan');

    $data = [
      'title'           => 'Kelompok',
      'user'            => $this->db->get_where('login', ['username' => $this->session->userdata('username')])->row_array(),
      'kelompokhalaqoh' => $this->Kelompok_M->getAllKelompok(),
      'pesan'           => $pesan,
      'isi'             => tampilan_mobile() ? 'halaqoh/mobile-kelompok' : 'halaqoh/v-kelompok',
    ];

    $this->load->view(tampilan_mobile() ? 'templates/wrapper-mobile-simple' : 'templates/wrapper-admin', $data);
  }

  // Add a new item
  public function add()
  {
    $data = ['NamaKelompok' => $this->input->post('kelompok')];
    $this->Kelompok_M->addKelompok($data);
    redirect('halaqoh/kelompok?pesan=' . rawurlencode('Berhasil ditambahkan!'));
  }

  //Update one item
  public function update($id)
  {
    $data = [
      'IdKelompok' => $id,
      'NamaKelompok' => $this->input->post('kelompok')
    ];
    $this->Kelompok_M->updateKelompok($data);
    redirect('halaqoh/kelompok?pesan=' . rawurlencode('Berhasil diubah!'));
  }

  //Delete one item
  public function delete($id)
  {
    $data = [
      'IdKelompok' => $id
    ];
    $this->Kelompok_M->deleteKelompok($data);
    redirect('halaqoh/kelompok?pesan=' . rawurlencode('Berhasil dihapus!'));
  }

  public function export_excel()
  {
    $data = [
      'title'           => 'Kelompok',
      'user'            => $this->db->get_where('login', ['username' => $this->session->userdata('username')])->row_array(),
      'kelompokhalaqoh' => $this->Kelompok_M->getAllKelompok(),
    ];

    $this->load->view('export/excel/halaqoh/kelompok', $data);
  }
}

/* End of file Kelompok.php */
