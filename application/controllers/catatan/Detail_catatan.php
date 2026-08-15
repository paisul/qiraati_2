<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Detail_catatan extends CI_Controller
{

  public function __construct()
  {
    parent::__construct();
    //Load Dependencies
    cek_login();
    $this->load->model('Jenis_catatan_M');
    $this->load->model('Detail_Jenis_catatan_M');
  }

  // List all your items
  public function index($offset = 0)
  {
    $pesan = $this->input->get('pesan') ?: $this->session->flashdata('pesan');

    $data = [
      'title' => 'Detail Jenis Catatan',
      'user' => $this->db->get_where('login', ['username' => $this->session->userdata('username')])->row_array(),
      'jenis_catatan' => $this->Jenis_catatan_M->getAllJenisCatatan(),
      'detail_jenis_catatan' => $this->Detail_Jenis_catatan_M->getAllDetailJenisCatatan(),
      'pesan' => $pesan,
      'isi' => tampilan_mobile() ? 'catatan/mobile-detail_catatan' : 'catatan/v-detail_catatan',
    ];
    // check($data['detail_jenis_catatan']);

    $this->load->view(tampilan_mobile() ? 'templates/wrapper-mobile-simple' : 'templates/wrapper-admin', $data);
  }

  // Add a new item
  public function add()
  {
    $data = [
      'IdJenisCatatan' => $this->input->post('jeniscatatan'),
      'DetailCatatan' => $this->input->post('isidetailcatatan')
    ];
    $this->Detail_Jenis_catatan_M->addDetailJenisCatatan($data);
    redirect('catatan/detail_catatan?pesan=' . rawurlencode('Berhasil ditambahkan!'));
  }

  //Update one item
  public function update($id)
  {
    $data = [
      'IdDetailJenisCatatan' => $id,
      'IdJenisCatatan' => $this->input->post('jeniscatatan'),
      'DetailCatatan' => $this->input->post('isidetailcatatan')
    ];
    $this->Detail_Jenis_catatan_M->updateDetailJenisCatatan($data);
    redirect('catatan/detail_catatan?pesan=' . rawurlencode('Berhasil diubah!'));
  }

  //Delete one item
  public function delete($id)
  {
    $data = [
      'IdDetailJenisCatatan' => $id
    ];
    $this->Detail_Jenis_catatan_M->deleteDetailJenisCatatan($data);
    redirect('catatan/detail_catatan?pesan=' . rawurlencode('Berhasil dihapus!'));
  }
}

/* End of file Detail_catatab.php */
