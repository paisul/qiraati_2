<?php
defined('BASEPATH') or exit('No direct script access allowed');

// Pengaturan Aplikasi: admin mengubah nama & logo yang tampil di seluruh sidebar/header/footer/login.
class PengaturanAplikasi extends CI_Controller
{
  public function __construct()
  {
    parent::__construct();
    cek_level('Admin');
    $this->load->model('PengaturanAplikasi_M');
  }

  public function index()
  {
    $pesan = $this->input->get('pesan') ?: $this->session->flashdata('pesan');
    $data = [
      'title' => 'Pengaturan Aplikasi',
      'user' => $this->getUserLogin(),
      'pengaturan' => $this->PengaturanAplikasi_M->getPengaturan(),
      'pesan' => $pesan,
      'isi' => tampilan_mobile() ? 'pengaturan_aplikasi/mobile-index' : 'pengaturan_aplikasi/index',
    ];

    $this->load->view(tampilan_mobile() ? 'templates/wrapper-mobile-simple' : 'templates/wrapper-admin', $data);
  }

  public function simpan()
  {
    $pengaturan = $this->PengaturanAplikasi_M->getPengaturan();

    $nama = trim($this->input->post('nama_aplikasi'));
    if ($nama === '') {
      redirect('pengaturanaplikasi?pesan=' . rawurlencode('Nama aplikasi tidak boleh kosong, tidak disimpan.'));
      return;
    }

    $data = ['NamaAplikasi' => $nama];

    $logo = $this->uploadLogo('logo');
    if ($logo) {
      $data['Logo'] = $logo;
    }

    $this->PengaturanAplikasi_M->updatePengaturan($pengaturan['Id'], $data);
    redirect('pengaturanaplikasi?pesan=' . rawurlencode('Berhasil disimpan!'));
  }

  private function uploadLogo($field)
  {
    if (empty($_FILES[$field]['name'])) {
      return null;
    }

    $config['upload_path']   = upload_path('pengaturan');
    $config['allowed_types'] = 'jpg|jpeg|png';
    $config['max_size']      = '2048';
    $config['file_name']     = 'logo_' . time() . '_' . preg_replace('/[^a-zA-Z0-9]/', '', $_FILES[$field]['name']);

    if (!is_dir($config['upload_path'])) {
      mkdir($config['upload_path'], 0755, true);
    }

    $this->load->library('upload');
    $this->upload->initialize($config);

    if ($this->upload->do_upload($field)) {
      // Nama file polos (bukan path sebagian "upload/pengaturan/...") - konsisten dengan kolom
      // upload lain (Pasfoto, Ttd, dst), supaya bisa dirender lewat upload_url('pengaturan', ...)
      // yang sama seperti file upload lainnya.
      return $this->upload->data('file_name');
    }

    return null;
  }

  private function getUserLogin()
  {
    return $this->db->get_where('login', ['username' => $this->session->userdata('username')])->row_array();
  }
}

/* End of file PengaturanAplikasi.php */
