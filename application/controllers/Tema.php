<?php
defined('BASEPATH') or exit('No direct script access allowed');

// Toggle tema gelap/terang - satu endpoint AJAX dipakai oleh semua peran mobile (Admin/Musyrif/Wali)
// lewat tombol di topbar (lihat header_generic.php/header_wali.php). Preferensi tersimpan per akun
// login, bukan per-HP, supaya ikut terbawa kalau ganti perangkat.
class Tema extends CI_Controller
{
  public function __construct()
  {
    parent::__construct();
    cek_login();
  }

  public function toggle()
  {
    $username = $this->session->userdata('username');
    $sekarang = ambil_tema_user($this->db, $username);
    $baru = $sekarang === 'gelap' ? 'terang' : 'gelap';

    $this->db->where('username', $username);
    $this->db->update('login', ['Tema' => $baru]);

    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['status' => true, 'tema' => $baru]);
  }
}

/* End of file Tema.php */
