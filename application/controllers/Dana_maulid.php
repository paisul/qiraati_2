<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Dana_maulid extends CI_Controller
{
  public function __construct()
  {
    parent::__construct();
    cek_level(['Admin', 'Musyrif', 'Guru', 'Wali']);
    $this->load->model('Dana_maulid_M');
    $this->load->model('Musyrif_M');
    $this->load->model('Wali_M');
  }

  public function index()
  {
    $data = [
      'title' => 'Dana Maulid',
      'user' => $this->getUserLogin(),
      'dana' => $this->Dana_maulid_M->getAll(),
      'ringkasan' => $this->Dana_maulid_M->getRingkasan(),
      'pesan' => $this->input->get('pesan') ?: $this->session->flashdata('pesan'),
      'dana_route' => 'dana-maulid',
      'dana_label' => 'Maulid',
      'isi' => tampilan_mobile() ? 'dana_maulid/mobile-index' : 'dana_maulid/index',
    ];
    $this->load->view($this->getWrapper(), $data);
  }

  public function add()
  {
    $this->tolakJikaWali();
    $this->Dana_maulid_M->add($this->inputDana());
    $this->kembali('Berhasil ditambahkan!');
  }

  public function update($id)
  {
    $this->tolakJikaWali();
    $this->Dana_maulid_M->update($id, $this->inputDana(TRUE));
    $this->kembali('Berhasil diubah!');
  }

  public function delete($id)
  {
    $this->tolakJikaWali();
    $this->Dana_maulid_M->delete($id);
    $this->kembali('Berhasil dihapus!');
  }

  private function kembali($pesan)
  {
    redirect('dana-maulid?pesan=' . rawurlencode($pesan));
  }

  private function tolakJikaWali()
  {
    if ($this->session->userdata('level') === 'Wali') {
      $this->kembali('Akun wali hanya dapat melihat data ini.');
      exit;
    }
  }

  private function inputDana($is_update = FALSE)
  {
    $data = [
      'Tanggal' => $this->input->post('tanggal'),
      'Perihal' => $this->input->post('perihal'),
      'JumlahMasuk' => $this->formatNominal($this->input->post('jumlah_masuk')),
      'JumlahKeluar' => $this->formatNominal($this->input->post('jumlah_keluar')),
    ];
    $data[$is_update ? 'UpdatedAt' : 'CreatedAt'] = date('Y-m-d H:i:s');
    return $data;
  }

  private function formatNominal($nominal)
  {
    $nominal = preg_replace('/[^0-9.]/', '', (string) $nominal);
    return max(0, (float) ($nominal === '' ? 0 : $nominal));
  }

  private function getUserLogin()
  {
    $username = $this->session->userdata('username');
    $level = $this->session->userdata('level');
    if ($level === 'Musyrif') {
      return $this->Musyrif_M->getDataMusyrif($username) ?: $this->db->get_where('login', ['username' => $username])->row_array();
    }
    if ($level === 'Guru') {
      $user = $this->db->get_where('login', ['username' => $username])->row_array();
      $user['NamaMusyrif'] = $user['username'];
      $user['Email'] = $user['level'];
      return $user;
    }
    if ($level === 'Wali') {
      $wali = $this->Wali_M->getDataWali($username);
      if (!$wali) redirect('auth');
      return $wali;
    }
    return $this->db->get_where('login', ['username' => $username])->row_array();
  }

  private function getWrapper()
  {
    $level = $this->session->userdata('level');
    if ($level === 'Musyrif' || $level === 'Guru') return tampilan_mobile() ? 'templates/wrapper-musyrif-mobile' : 'templates/wrapper-musyrif';
    if ($level === 'Wali') return tampilan_mobile() ? 'templates/wrapper-wali-mobile' : 'templates/wrapper-wali';
    return tampilan_mobile() ? 'templates/wrapper-mobile-simple' : 'templates/wrapper-admin';
  }
}
