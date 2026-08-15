<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Catatan_santri extends CI_Controller
{

  public function __construct()
  {
    parent::__construct();
    //Load Dependencies
    cek_login();
    $this->load->model('Santri_M');
    $this->load->model('Jenis_catatan_M');
    $this->load->model('Catatan_santri_M');
    $this->load->model('Detail_Jenis_catatan_M');
    $this->load->model('Periode_M');
    $this->load->model('Musyrif_M');
  }

  // List all your items
  public function index()
  {
    $pesan = $this->input->get('pesan') ?: $this->session->flashdata('pesan');

    $data = [
      'title' => 'Catatan Santri',
      'user' => $this->getUserLogin(),
      'catatan_santri' => $this->Catatan_santri_M->getAllCatatanSantri(),
      'santri' => $this->Santri_M->getAllSantri(),
      'periode' => $this->Periode_M->getAllPeriode(),
      'jenis_catatan' => $this->Jenis_catatan_M->getAllJenisCatatan(),
      'pesan' => $pesan,
      'isi' => tampilan_mobile() ? 'catatan/mobile-catatan_santri' : 'catatan/v-catatan_santri',
    ];
    $this->load->view($this->getWrapper(), $data);
  }

  public function getDetailCatatanByJenis()
  {
    $IdJenisCatatan = $this->input->post('id_jenis_catatan');
    $DataDetailJenisCatatan = $this->Detail_Jenis_catatan_M->getDetailByJenisCatatan($IdJenisCatatan);

    foreach ($DataDetailJenisCatatan as $detailJenis) {
      echo "<option value='" . $detailJenis['DetailCatatan'] . "'id_detail_jenis_catatan='" . $detailJenis['IdDetailJenisCatatan'] . "' >" . $detailJenis['DetailCatatan'] . "</option>";
    }
  }

  // Add a new item
  public function add()
  {
    $IdSiswa = $this->input->post('nama');
    $IdPeriode = $this->input->post('periode');
    $IdJenisCatatan = $this->input->post('jeniscatatan');
    $IdDetailJenisCatatan = $this->input->post('detailjeniscatatan');
    $catatan_musyrif = $this->input->post('catatan_musyrif');


    // Ubah isi array detail jenis catatab menjadi string
    $pecahdetail = implode(",", $IdDetailJenisCatatan);
    // $jmlDetCat = count($IdDetailJenisCatatan);
    // $isi = $this->input->post('isi');
    $data = [
      'IdSiswa' => $IdSiswa,
      'IdPeriode' => $IdPeriode,
      'IdJenisCatatan' => $IdJenisCatatan,
      'IsiCatatan' => $pecahdetail,
      'CatatanMusyrif' => $catatan_musyrif
    ];
    // check($data);

    $this->Catatan_santri_M->addCatatanSantri($data);
    redirect('catatan/catatan_santri?pesan=' . rawurlencode('Berhasil ditambahkan!'));

    // for ($i = 0; $i < $jmlDetCat; $i++) {
    //   # code...
    //   $data[$i]['IdSiswa'] = $IdSiswa;
    //   $data[$i]['IsiCatatan'] = $isi;
    //   $data[$i]['IdJenisCatatan'] = $IdJenisCatatan;
    //   foreach ($IdDetailJenisCatatan as $key => $val) {
    //     $data[$key]['IdDetailJenisCatatan'] = $val;
    //   }
    // }

    // check($data);
    // $data = [
    //   'IdSiswa' => $this->input->post('nama'),
    //   'IdJenisCatatan' => $this->input->post('jeniscatatan'),
    //   'IdDetailJenisCatatan' => $this->input->post('detailjeniscatatan'),
    //   'IsiCatatan' => $this->input->post('isi')
    // ];

  }

  //Update one item
  public function update($id)
  {
    $data = [
      'IdCatatan' => $id,
      'IdSiswa' => $this->input->post('nama'),
      'IdJenisCatatan' => $this->input->post('jeniscatatan'),
      'IsiCatatan' => $this->input->post('isi')
    ];
    $this->Catatan_santri_M->updateCatatanSantri($data);
    redirect('catatan/catatan_santri?pesan=' . rawurlencode('Berhasil diubah!'));
  }

  //Delete one item
  public function delete($id)
  {
    $data = [
      'IdCatatan' => $id
    ];
    $this->Catatan_santri_M->deleteCatatanSantri($data);
    redirect('catatan/catatan_santri?pesan=' . rawurlencode('Berhasil dihapus!'));
  }

  public function reset_data()
  {
    $this->Catatan_santri_M->kosongkanCatatanSantri();
    redirect('catatan/catatan_santri?pesan=' . rawurlencode('Berhasil direset!'));
  }

  public function export_excel()
  {
    $data = [
      'title' => 'Catatan Santri',
      'user' => $this->getUserLogin(),
      'catatan_santri' => $this->Catatan_santri_M->getAllCatatanSantri(),
      'santri' => $this->Santri_M->getAllSantri(),
      'periode' => $this->Periode_M->getAllPeriode(),
      'jenis_catatan' => $this->Jenis_catatan_M->getAllJenisCatatan(),
    ];

    $this->load->view('export/excel/catatan/catatan_santri', $data);
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

/* End of file Catatan_santri.php */
