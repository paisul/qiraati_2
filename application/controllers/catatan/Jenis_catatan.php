<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Jenis_catatan extends CI_Controller
{

  public function __construct()
  {
    parent::__construct();
    //Load Dependencies
    cek_login();
    $this->load->model('Jenis_catatan_M');
  }

  // List all your items
  public function index()
  {
    $pesan = $this->input->get('pesan') ?: $this->session->flashdata('pesan');

    $data = [
      'title' => 'Jenis Catatan',
      'user' => $this->db->get_where('login', ['username' => $this->session->userdata('username')])->row_array(),
      'jenis_catatan' => $this->Jenis_catatan_M->getAllJenisCatatan(),
      'pesan' => $pesan,
      'isi' => tampilan_mobile() ? 'catatan/mobile-jenis_catatan' : 'catatan/v-jenis_catatan',
    ];

    $this->load->view(tampilan_mobile() ? 'templates/wrapper-mobile-simple' : 'templates/wrapper-admin', $data);
  }

  // Add a new item
  public function add()
  {
    $data = [
      'JenisCatatan' => $this->input->post('jenis_catatan')
    ];
    $this->Jenis_catatan_M->addJenisCatatan($data);
    redirect('catatan/jenis_catatan?pesan=' . rawurlencode('Berhasil ditambahkan!'));
  }

  //Update one item
  public function update($id)
  {
    $data = [
      'IdJenisCatatan' => $id,
      'JenisCatatan' => $this->input->post('jenis_catatan')
    ];
    $this->Jenis_catatan_M->updateJenisCatatan($data);
    redirect('catatan/jenis_catatan?pesan=' . rawurlencode('Berhasil diubah!'));
  }

  //Delete one item
  public function delete($id)
  {
    $data = [
      'IdJenisCatatan' => $id,
    ];
    $this->Jenis_catatan_M->deleteJenisCatatan($data);
    redirect('catatan/jenis_catatan?pesan=' . rawurlencode('Berhasil dihapus!'));
  }
}

/* End of file Jenis_catatan.php */
