<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Detail_target extends CI_Controller
{

  public function __construct()
  {
    parent::__construct();
    //Load Dependencies
    cek_login();
    $this->load->model('Detail_target_M');
    $this->load->model('Target_M');
  }

  // List all your items
  public function index()
  {
    $pesan = $this->input->get('pesan') ?: $this->session->flashdata('pesan');

    $data = [
      'title'   => 'Data Detail Target',
      'user'    => $this->db->get_where('login', ['username' => $this->session->userdata('username')])->row_array(),
      'detail'  => $this->Detail_target_M->getAllDetailTarget(),
      'target'  => $this->Target_M->getAllTarget(),
      'pesan'   => $pesan,
      'isi'     => tampilan_mobile() ? 'target-tahfidz/mobile-detail_target' : 'target-tahfidz/v-detail_target',
    ];
    $this->load->view(tampilan_mobile() ? 'templates/wrapper-mobile-simple' : 'templates/wrapper-admin', $data);
  }

  // Add a new item
  public function add()
  {
    $data = [
      'IdTarget' => $this->input->post('pekan'),
      'IsiTarget' => $this->input->post('isi'),
      'JenisTarget' => $this->input->post('jenis_target'),
      'Keterangan' => $this->input->post('keterangan'),
      'Tgl' => $this->input->post('tgl'),
    ];
    $this->Detail_target_M->addDetailTarget($data);
    redirect('tahfidz/detail_target?pesan=' . rawurlencode('Berhasil ditambahkan!'));
  }

  //Update one item
  public function update($id)
  {
    $data = [
      'IdDetailTarget' => $id,
      'IdTarget' => $this->input->post('pekan'),
      'IsiTarget' => $this->input->post('isi'),
      'JenisTarget' => $this->input->post('jenis_target'),
      'Keterangan' => $this->input->post('keterangan'),
      'Tgl' => $this->input->post('tgl')
    ];
    $this->Detail_target_M->updateDetailTarget($data);
    redirect('tahfidz/detail_target?pesan=' . rawurlencode('Berhasil diubah!'));
  }

  //Delete one item
  public function delete($id)
  {
    $data = ['IdDetailTarget' => $id];
    $this->Detail_target_M->deleteDetailTarget($data);
    redirect('tahfidz/detail_target?pesan=' . rawurlencode('Berhasil dihapus!'));
  }

  public function export_excel()
  {
    $data = [
      'title'   => 'Data Detail Target',
      'user'    => $this->db->get_where('login', ['username' => $this->session->userdata('username')])->row_array(),
      'detail'  => $this->Detail_target_M->getAllDetailTarget(),
      'target'  => $this->Target_M->getAllTarget(),
    ];

    $this->load->view('export/excel/tahfidz/detail_target', $data);
  }
}

/* End of file Detail_target.php */
