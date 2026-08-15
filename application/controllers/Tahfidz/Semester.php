<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Semester extends CI_Controller
{

  public function __construct()
  {
    parent::__construct();
    //Load Dependencies
    cek_login();
    $this->load->model('Semester_M');
  }

  // List all your items
  public function index()
  {
    $pesan = $this->input->get('pesan') ?: $this->session->flashdata('pesan');

    $data = [
      'title' => 'Data Semester',
      'user' => $this->db->get_where('login', ['username' => $this->session->userdata('username')])->row_array(),
      'semester' => $this->Semester_M->getAllSemester(),
      'pesan' => $pesan,
      'isi' => tampilan_mobile() ? 'target-tahfidz/mobile-semester' : 'target-tahfidz/v-semester',
    ];
    $this->load->view(tampilan_mobile() ? 'templates/wrapper-mobile-simple' : 'templates/wrapper-admin', $data);
  }

  // Add a new item
  public function add()
  {
    $data = [
      'Semester' => $this->input->post('semester')
    ];
    $this->Semester_M->addSemester($data);
    redirect('tahfidz/semester?pesan=' . rawurlencode('Berhasil ditambahkan!'));
  }

  //Update one item
  public function update($id)
  {
    $data = [
      'IdSemester' => $id,
      'Semester' => $this->input->post('semester')
    ];
    $this->Semester_M->updateSemester($data);
    redirect('tahfidz/semester?pesan=' . rawurlencode('Berhasil diubah!'));
  }

  //Delete one item
  public function delete($id)
  {
    $data = [
      'IdSemester' => $id
    ];
    $berhasil = $this->Semester_M->deleteSemester($data);
    redirect('tahfidz/semester?pesan=' . rawurlencode($berhasil
      ? 'Berhasil dihapus!'
      : 'Semester ini tidak dapat dihapus karena masih dipakai di data Periode Ujian. Hapus/ubah dulu periode ujian yang memakainya.'));
  }

  public function export_excel()
  {
    $data = [
      'title' => 'Data Semester',
      'user' => $this->db->get_where('login', ['username' => $this->session->userdata('username')])->row_array(),
      'semester' => $this->Semester_M->getAllSemester(),
    ];

    $this->load->view('export/excel/tahfidz/semester', $data);
  }
}

/* End of file Semester.php */
