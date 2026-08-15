<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Target extends CI_Controller
{

  public function __construct()
  {
    parent::__construct();
    //Load Dependencies
    cek_login();
    $this->load->model('Target_M');
    $this->load->model('Ajaran_M');
    $this->load->model('Kelas_M');
    $this->load->model('Periode_M');
    $this->load->model('Semester_M');
  }

  // List all your items
  public function index($offset = 0)
  {
    $pesan = $this->input->get('pesan') ?: $this->session->flashdata('pesan');

    $data = [
      'title'    => 'Data Target',
      'user'     => $this->db->get_where('login', ['username' => $this->session->userdata('username')])->row_array(),
      'target'   => $this->Target_M->getAllTarget(),
      'kelas'    => $this->Kelas_M->getAllKelas(),
      'periode'  => $this->Periode_M->getAllPeriode(),
      'ajaran'   => $this->Ajaran_M->getAllAjaran(),
      'semester' => $this->Semester_M->getAllSemester(),
      'pesan'    => $pesan,
      'isi'      => tampilan_mobile() ? 'target-tahfidz/mobile-target' : 'target-tahfidz/v-target',
    ];
    $this->load->view(tampilan_mobile() ? 'templates/wrapper-mobile-simple' : 'templates/wrapper-admin', $data);
  }

  // Add a new item
  public function add()
  {
    $data = [
      'IdKelas'    => $this->input->post('kelas'),
      'IdPeriode'  => $this->input->post('periode'),
      'IdAjaran'   => $this->input->post('ajaran'),
      'IdSemester' => $this->input->post('semester'),
      'TglMulai'   => $this->input->post('tglMulai'),
      'TglSelesai' => $this->input->post('tglSelesai'),
      'Pekan'      => $this->input->post('pekan'),
    ];
    $this->Target_M->addTarget($data);
    redirect('tahfidz/target?pesan=' . rawurlencode('Berhasil ditambahkan!'));
  }

  //Update one item
  public function update($id)
  {
    // ? Gagal update
    $data = [
      'IdTarget'   => $id,
      'IdKelas'    => $this->input->post('kelas'),
      'IdPeriode'  => $this->input->post('periode'),
      'IdAjaran'   => $this->input->post('ajaran'),
      'IdSemester' => $this->input->post('semester'),
      'TglMulai'   => $this->input->post('tglMulai'),
      'TglSelesai' => $this->input->post('tglSelesai'),
      'Pekan'      => $this->input->post('pekan'),
    ];
    $this->Target_M->updateTarget($data);
    redirect('tahfidz/target?pesan=' . rawurlencode('Berhasil diubah!'));
  }

  //Delete one item
  public function delete($id)
  {
    $data = ['IdTarget' => $id];
    $this->Target_M->deleteTarget($data);
    redirect('tahfidz/target?pesan=' . rawurlencode('Berhasil dihapus!'));
  }

  public function export_excel()
  {
    $data = [
      'title'    => 'Data Target',
      'user'     => $this->db->get_where('login', ['username' => $this->session->userdata('username')])->row_array(),
      'target'   => $this->Target_M->getAllTarget(),
      'kelas'    => $this->Kelas_M->getAllKelas(),
      'periode'  => $this->Periode_M->getAllPeriode(),
      'ajaran'   => $this->Ajaran_M->getAllAjaran(),
      'semester' => $this->Semester_M->getAllSemester(),
    ];

    $this->load->view('export/excel/tahfidz/target', $data);
  }
}

/* End of file Target.php */
