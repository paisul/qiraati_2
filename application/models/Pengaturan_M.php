<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Pengaturan_M extends CI_Model
{
  // LEFT JOIN musyrif supaya baris akun level Musyrif ikut bawa IdMusyrif-nya - dipakai di view
  // untuk menautkan username langsung ke Ubah Data Pembimbing musyrif itu (mis. login.IdSiswa,
  // kolom skalar lama di tabel login sendiri, yang sudah dipakai buat menautkan ke Detail Santri).
  public function getAllPengguna()
  {
    $this->db->select('login.*, musyrif.IdMusyrif');
    $this->db->from('login');
    $this->db->join('musyrif', 'musyrif.IdUser = login.IdUser', 'left');
    return $this->db->get()->result_array();
  }

  public function addUser($data)
  {
    $this->db->insert('login', $data);
  }

  public function updatePengguna($data)
  {
    $this->db->where('IdUser', $data['IdUser']);
    $this->db->update('login', $data);
  }

  // Akun admin utama - tidak boleh dihapus lewat fitur ini.
  const AKUN_TERPROTEKSI = 'turanisia.tv@gmail.com';

  public function deletePengguna($data)
  {
    $pengguna = $this->db->get_where('login', ['IdUser' => $data['IdUser']])->row_array();

    if ($pengguna && strcasecmp($pengguna['username'], self::AKUN_TERPROTEKSI) === 0) {
      return false;
    }

    $this->db->where('IdUser', $data['IdUser']);
    $this->db->delete('login', $data);
    return true;
  }
}

/* End of file Pengaturan_M.php */
