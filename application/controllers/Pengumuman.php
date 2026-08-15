<?php
defined('BASEPATH') or exit('No direct script access allowed');

// Info Penting: diposting Admin/Musyrif, tampil ke semua akun Wali di dashboard mobile-nya.
class Pengumuman extends CI_Controller
{
  public function __construct()
  {
    parent::__construct();
    cek_level(['Admin', 'Bagian Administrasi', 'Musyrif', 'Guru']);
    $this->load->model('Pengumuman_M');
  }

  public function index()
  {
    // Lihat komentar di Dana::index() - query string ?pesan= dipakai supaya pesan berhasil/gagal
    // tidak nyangkut lewat session flashdata (kadang tidak "kadaluarsa" dengan benar di server ini).
    $pesan = $this->input->get('pesan') ?: $this->session->flashdata('pesan');

    $data = [
      'title' => 'Info Penting',
      'user' => $this->getUserLogin(),
      'pengumuman' => $this->Pengumuman_M->getAllPengumuman(),
      'pesan' => $pesan,
      'isi' => tampilan_mobile() ? 'pengumuman/mobile-index' : 'pengumuman/index',
    ];

    $this->load->view($this->getWrapper(), $data);
  }

  public function add()
  {
    $judul = trim($this->input->post('judul'));
    $isi = trim($this->input->post('isi'));

    if ($judul === '' || $isi === '') {
      redirect('pengumuman?pesan=' . rawurlencode('Judul dan isi wajib diisi, tidak disimpan.'));
      return;
    }

    $this->Pengumuman_M->addPengumuman([
      'Judul' => $judul,
      'Isi' => $isi,
      'DibuatOleh' => $this->getNamaPembuat(),
    ]);
    redirect('pengumuman?pesan=' . rawurlencode('Berhasil ditambahkan!'));
  }

  public function update($id)
  {
    $judul = trim($this->input->post('judul'));
    $isi = trim($this->input->post('isi'));

    if ($judul === '' || $isi === '') {
      redirect('pengumuman?pesan=' . rawurlencode('Judul dan isi wajib diisi, tidak disimpan.'));
      return;
    }

    $this->Pengumuman_M->updatePengumuman([
      'IdPengumuman' => $id,
      'Judul' => $judul,
      'Isi' => $isi,
    ]);
    redirect('pengumuman?pesan=' . rawurlencode('Berhasil diubah!'));
  }

  public function delete($id)
  {
    $this->Pengumuman_M->deletePengumuman(['IdPengumuman' => $id]);
    redirect('pengumuman?pesan=' . rawurlencode('Berhasil dihapus!'));
  }

  private function getUserLogin()
  {
    $username = $this->session->userdata('username');

    if ($this->session->userdata('level') == 'Musyrif' || $this->session->userdata('level') == 'Guru') {
      $this->load->model('Musyrif_M');
      $user = $this->Musyrif_M->getDataMusyrif($username);
      return $user ? $user : $this->db->get_where('login', ['username' => $username])->row_array();
    }

    return $this->db->get_where('login', ['username' => $username])->row_array();
  }

  // Nama yang tampil di kolom DibuatOleh - jangan pakai username mentah (email login), tampilkan
  // nama pembimbing untuk Musyrif/Guru, atau label perannya untuk Admin (tidak ada nama tersimpan
  // untuk akun Admin di tabel login).
  private function getNamaPembuat()
  {
    $level = $this->session->userdata('level');

    if ($level == 'Musyrif' || $level == 'Guru') {
      $this->load->model('Musyrif_M');
      $user = $this->Musyrif_M->getDataMusyrif($this->session->userdata('username'));
      if ($user && !empty($user['NamaMusyrif'])) {
        return $user['NamaMusyrif'];
      }
    }

    return $level;
  }

  private function getWrapper()
  {
    if ($this->session->userdata('level') == 'Musyrif' || $this->session->userdata('level') == 'Guru') {
      return tampilan_mobile() ? 'templates/wrapper-musyrif-mobile' : 'templates/wrapper-musyrif';
    }

    return tampilan_mobile() ? 'templates/wrapper-mobile-simple' : 'templates/wrapper-admin';
  }
}

/* End of file Pengumuman.php */
