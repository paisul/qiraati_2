<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Periode extends CI_Controller
{

  public function __construct()
  {
    parent::__construct();
    //Load Dependencies
    cek_login();
    $this->load->model('Periode_M');
  }

  // List all your items
  public function index()
  {
    $pesan = $this->input->get('pesan') ?: $this->session->flashdata('pesan');

    $data = [
      'title' => 'Data Periode',
      'user' => $this->db->get_where('login', ['username' => $this->session->userdata('username')])->row_array(),
      'periode' => $this->Periode_M->getAllPeriode(),
      'pesan' => $pesan,
      'isi' => tampilan_mobile() ? 'target-tahfidz/mobile-periode' : 'target-tahfidz/v-periode',
    ];
    $this->load->view(tampilan_mobile() ? 'templates/wrapper-mobile-simple' : 'templates/wrapper-admin', $data);
  }

  // Add a new item
  public function add()
  {
    $data = [
      'Periode' => $this->input->post('periode')
    ];
    $this->Periode_M->addPeriode($data);
    redirect('tahfidz/periode?pesan=' . rawurlencode('Berhasil ditambahkan!'));
  }

  //Update one item
  public function update($id)
  {
    $data = [
      'IdPeriode' => $id,
      'Periode' => $this->input->post('periode')
    ];
    $this->Periode_M->updatePeriode($data);
    redirect('tahfidz/periode?pesan=' . rawurlencode('Berhasil diubah!'));
  }

  //Delete one item
  public function delete($id)
  {
    $data = [
      'IdPeriode' => $id,
    ];
    $berhasil = $this->Periode_M->deletePeriode($data);
    redirect('tahfidz/periode?pesan=' . rawurlencode($berhasil
      ? 'Berhasil dihapus!'
      : 'Periode ini tidak dapat dihapus karena masih dipakai di data Periode Ujian. Hapus/ubah dulu periode ujian yang memakainya.'));
  }

  public function export_excel()
  {
    $data = [
      'title' => 'Data Periode',
      'user' => $this->db->get_where('login', ['username' => $this->session->userdata('username')])->row_array(),
      'periode' => $this->Periode_M->getAllPeriode(),
    ];

    $this->load->view('export/excel/tahfidz/periode', $data);
  }
}

/* End of file Periode.php */
