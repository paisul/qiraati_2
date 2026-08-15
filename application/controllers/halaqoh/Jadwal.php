<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Jadwal extends CI_Controller
{

  public function __construct()
  {
    parent::__construct();
    //Load Dependencies
    cek_login();
    $this->load->model('Jadwal_M');
  }

  // List all your items
  public function index()
  {
    $pesan = $this->input->get('pesan') ?: $this->session->flashdata('pesan');

    $data = [
      'title'   => 'Waktu Halaqoh',
      'user'    => $this->db->get_where('login', ['username' => $this->session->userdata('username')])->row_array(),
      'jadwal'  => $this->Jadwal_M->getAllJadwal(),
      'pesan'   => $pesan,
      'isi'     => tampilan_mobile() ? 'halaqoh/mobile-jadwal' : 'halaqoh/v-jadwal',
    ];

    $this->load->view(tampilan_mobile() ? 'templates/wrapper-mobile-simple' : 'templates/wrapper-admin', $data);
  }

  // Add a new item
  public function add()
  {
    $data = [
      'Waktu' => $this->input->post('waktu'),
      'Ket' => $this->input->post('keterangan')
    ];
    $this->Jadwal_M->addJadwal($data);
    redirect('halaqoh/jadwal?pesan=' . rawurlencode('Berhasil ditambahkan!'));
  }

  //Update one item
  public function update($id)
  {
    $data = [
      'IdJadwal' => $id,
      'Waktu' => $this->input->post('waktu'),
      'Ket' => $this->input->post('keterangan')
    ];
    $this->Jadwal_M->updateJadwal($data);
    redirect('halaqoh/jadwal?pesan=' . rawurlencode('Berhasil diubah!'));
  }

  //Delete one item
  public function delete($id)
  {
    $data = [
      'IdJadwal' => $id
    ];
    $this->Jadwal_M->deleteJadwal($data);
    redirect('halaqoh/jadwal?pesan=' . rawurlencode('Berhasil dihapus!'));
  }

  public function export_excel()
  {
    $data = [
      'title'   => 'Waktu Halaqoh',
      'user'    => $this->db->get_where('login', ['username' => $this->session->userdata('username')])->row_array(),
      'jadwal'  => $this->Jadwal_M->getAllJadwal(),
    ];

    $this->load->view('export/excel/halaqoh/jadwal', $data);
  }
}

/* End of file Jadwal.php */
