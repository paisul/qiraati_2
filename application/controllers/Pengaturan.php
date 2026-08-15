<?php


defined('BASEPATH') or exit('No direct script access allowed');

class Pengaturan extends CI_Controller
{

  public function __construct()
  {
    parent::__construct();
    //Load Dependencies
    cek_login();
    $this->load->model('Pengaturan_M');
  }

  // List all your items
  public function index($offset = 0)
  {
    $pesan = $this->input->get('pesan') ?: $this->session->flashdata('pesan');

    $data = [
      'title' => 'Pengaturan Akun',
      'user' => $this->db->get_where('login', ['username' => $this->session->userdata('username')])->row_array(),
      'pengguna' => $this->Pengaturan_M->getAllPengguna(),
      'pesan' => $pesan,
      'isi' => tampilan_mobile() ? 'pengaturan/mobile-index' : 'pengaturan/index',
    ];

    $this->load->view(tampilan_mobile() ? 'templates/wrapper-mobile-simple' : 'templates/wrapper-admin', $data);
  }

  // Add a new item - SENGAJA dibatasi hanya Admin/Bagian Administrasi: akun Wali/Musyrif/Santri
  // harus dibuat lewat alur masing-masing (Musyrif::add(), pendaftaran Wali, dst) yang sekalian
  // membuat baris profil tertautnya (musyrif/santri/wali_siswa) - kalau dibuat langsung di sini,
  // akan jadi akun login "yatim" tanpa profil sama sekali. Dicek di server juga, bukan cuma
  // menyembunyikan pilihan di form, supaya tidak bisa dilewati lewat POST langsung.
  public function add()
  {
    $level = $this->input->post('level');

    if (!in_array($level, ['Admin', 'Bagian Administrasi'], true)) {
      redirect('pengaturan?pesan=' . rawurlencode('Tambah Data di sini hanya untuk level Admin/Bagian Administrasi. Akun Wali/Musyrif/Santri dibuat lewat halaman masing-masing.'));
      return;
    }

    $data = [
      'username' => $this->input->post('username'),
      'password' => password_hash($this->input->post('password'), PASSWORD_DEFAULT),
      'level' => $level,
    ];
    $this->Pengaturan_M->addUser($data);
    redirect('pengaturan?pesan=' . rawurlencode('Berhasil ditambahkan!'));
  }

  //Update one item
  public function update($id)
  {
    $data = [
      'IdUser' => $id,
      'username' => $this->input->post('username'),
      'password' => password_hash($this->input->post('password'), PASSWORD_DEFAULT),
      'level' => $this->input->post('level'),
    ];
    $this->Pengaturan_M->updatePengguna($data);
    redirect('pengaturan?pesan=' . rawurlencode('Berhasil diubah!'));
  }

  //Delete one item
  public function delete($id)
  {
    $data = ['IdUser' => $id];
    $berhasil = $this->Pengaturan_M->deletePengguna($data);
    redirect('pengaturan?pesan=' . rawurlencode($berhasil ? 'Berhasil dihapus!' : 'Akun admin utama tidak dapat dihapus!'));
  }
}

/* End of file Pengaturan.php */
