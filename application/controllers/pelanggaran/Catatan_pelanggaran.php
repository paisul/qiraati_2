<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Catatan_pelanggaran extends CI_Controller
{

  public function __construct()
  {
    parent::__construct();
    //Load Dependencies
    cek_login();
    $this->load->model('Catatan_pelanggaran_M');
    $this->load->model('Santri_M');
    $this->load->model('Jenis_pelanggaran_M');
    $this->load->model('Musyrif_M');
  }

  // List all your items
  public function index()
  {
    $pesan = $this->input->get('pesan') ?: $this->session->flashdata('pesan');

    $data = [
      'title' => 'Catatan Pelanggaran',
      'user' => $this->getUserLogin(),
      'pelanggaran' => $this->Catatan_pelanggaran_M->getAllCatatanPelanggaran(),
      'santri' => $this->Santri_M->getAllSantri(),
      'jenisiqob' => $this->Jenis_pelanggaran_M->getAllJenisPelanggaran(),
      'pesan' => $pesan,
      'isi' => tampilan_mobile() ? 'pelanggaran/mobile-catatan_pelanggaran' : 'pelanggaran/v-catatan_pelanggaran',
    ];
    $this->load->view($this->getWrapper(), $data);
  }

  // Add a new item
  public function add()
  {
    $data = [
      'IdSiswa' => $this->input->post('nama'),
      'IdJenisIqob' => $this->input->post('jenisiqob'),
      'Tgl'   => $this->input->post('tgl'),
      'Points' => $this->input->post('poin')
    ];
    // check($data);
    $this->Catatan_pelanggaran_M->addCatatanPelanggaran($data);
    redirect('pelanggaran/catatan_pelanggaran?pesan=' . rawurlencode('Berhasil ditambahkan!'));
  }

  //Update one item
  public function update($id)
  {
    $data = [
      'IdIqob' => $id,
      'IdSiswa' => $this->input->post('nama'),
      'IdJenisIqob' => $this->input->post('jenisiqob'),
      'Tgl'   => $this->input->post('tgl'),
      'Points' => $this->input->post('poin')
    ];
    // check($data);
    $this->Catatan_pelanggaran_M->updateCatatanPelanggaran($data);
    redirect('pelanggaran/catatan_pelanggaran?pesan=' . rawurlencode('Berhasil diubah!'));
  }

  //Delete one item
  public function delete($id)
  {
    $data = [
      'IdIqob' => $id
    ];
    $this->Catatan_pelanggaran_M->deleteCatatanPelanggaran($data);
    redirect('pelanggaran/catatan_pelanggaran?pesan=' . rawurlencode('Berhasil dihapus!'));
  }

  public function getPointById()
  {
    $IdJenisIqob = $this->input->post('IdJenisIqob');
    $getPoin = $this->Catatan_pelanggaran_M->getPoinByIdIqob($IdJenisIqob);
    $hasil = [
      'Poin' => $getPoin['Poin']
    ];
    // check($getPoin);
    echo json_encode($hasil);
  }

  public function reset_pelanggaran()
  {
    $this->Catatan_pelanggaran_M->kosongkanPelanggaran();
    redirect('pelanggaran/catatan_pelanggaran?pesan=' . rawurlencode('Berhasil direset!'));
  }

  public function export_excel()
  {
    $data = [
      'title' => 'Catatan Pelanggaran',
      'user' => $this->getUserLogin(),
      'pelanggaran' => $this->Catatan_pelanggaran_M->getAllCatatanPelanggaran(),
      'santri' => $this->Santri_M->getAllSantri(),
      'jenisiqob' => $this->Jenis_pelanggaran_M->getAllJenisPelanggaran(),
    ];

    $this->load->view('export/excel/pelanggaran/catatan_pelanggaran', $data);
  }

  private function getUserLogin()
  {
    $username = $this->session->userdata('username');

    if ($this->session->userdata('level') == 'Musyrif') {
      $user = $this->Musyrif_M->getDataMusyrif($username);
      return $user ? $user : $this->db->get_where('login', ['username' => $username])->row_array();
    }

    if ($this->session->userdata('level') == 'Guru') {
      $user = $this->db->get_where('login', ['username' => $username])->row_array();
      $user['NamaMusyrif'] = $user['username'];
      $user['Email'] = $user['level'];
      return $user;
    }

    return $this->db->get_where('login', ['username' => $username])->row_array();
  }

  private function getWrapper()
  {
    if (tampilan_mobile()) {
      if ($this->session->userdata('level') == 'Musyrif' || $this->session->userdata('level') == 'Guru') {
        return 'templates/wrapper-musyrif-mobile';
      }
      return 'templates/wrapper-mobile-simple';
    }

    if ($this->session->userdata('level') == 'Musyrif' || $this->session->userdata('level') == 'Guru') {
      return 'templates/wrapper-musyrif';
    }

    return 'templates/wrapper-admin';
  }
}

/* End of file Catatan_pelanggaran.php */