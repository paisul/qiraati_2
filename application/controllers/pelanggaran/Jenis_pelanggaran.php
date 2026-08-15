<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Jenis_pelanggaran extends CI_Controller
{

  public function __construct()
  {
    parent::__construct();
    //Load Dependencies
    cek_login();
    $this->load->model('Jenis_pelanggaran_M');
  }

  // List all your items
  public function index()
  {
    $pesan = $this->input->get('pesan') ?: $this->session->flashdata('pesan');

    $data = [
      'title' => 'Data Jenis Pelanggaran',
      'user' => $this->db->get_where('login', ['username' => $this->session->userdata('username')])->row_array(),
      'jenispelanggaran' => $this->Jenis_pelanggaran_M->getAllJenisPelanggaran(),
      'pesan' => $pesan,
      'isi' => tampilan_mobile() ? 'pelanggaran/mobile-jenispelanggaran' : 'pelanggaran/v-jenispelanggaran',
    ];

    $this->load->view(tampilan_mobile() ? 'templates/wrapper-mobile-simple' : 'templates/wrapper-admin', $data);
  }

  // Add a new item
  public function add()
  {
    $data = [
      'JenisIqob' => $this->input->post('jenis_iqob'),
      'Poin' => $this->input->post('poin'),
      'Kategori' => $this->input->post('kategori')
    ];
    $this->Jenis_pelanggaran_M->addJenisPelanggaran($data);
    redirect('pelanggaran/jenis_pelanggaran?pesan=' . rawurlencode('Berhasil ditambahkan!'));
  }

  //Update one item
  public function update($id)
  {
    $data = [
      'IdJenisIqob' => $id,
      'JenisIqob' => $this->input->post('jenis_iqob'),
      'Poin' => $this->input->post('poin'),
      'Kategori' => $this->input->post('kategori')
    ];
    $this->Jenis_pelanggaran_M->updateJenisPelanggaran($data);
    redirect('pelanggaran/jenis_pelanggaran?pesan=' . rawurlencode('Berhasil diubah!'));
  }

  //Delete one item
  public function delete($id)
  {
    $data = ['IdJenisIqob' => $id];
    $this->Jenis_pelanggaran_M->deleteJenisPelanggaran($data);
    redirect('pelanggaran/jenis_pelanggaran?pesan=' . rawurlencode('Berhasil dihapus!'));
  }

  public function export_excel()
  {
    $data = [
      'title' => 'Data Jenis Pelanggaran',
      'user' => $this->db->get_where('login', ['username' => $this->session->userdata('username')])->row_array(),
      'jenispelanggaran' => $this->Jenis_pelanggaran_M->getAllJenisPelanggaran(),
    ];

    $this->load->view('export/excel/pelanggaran/jenis_pelanggaran', $data);
  }
}

/* End of file Jenis_pelanggaran.php */
