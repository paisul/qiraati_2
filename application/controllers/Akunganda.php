<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Alat bantu Admin: temukan & gabungkan akun Wali yang tidak sengaja "terpecah" jadi lebih dari
 * satu baris `login` dengan email yang sama (data lama sebelum ada validasi cek_email_unik()/
 * validasiBarisImport() - mis. anak kedua dulu ditambahkan dengan password beda dari akun
 * pertama, jadi bukan disambungkan tapi malah bikin akun baru). Efeknya: wali itu tidak bisa
 * ubah profil/password sendiri karena sistem anggap emailnya "sudah dipakai akun lain" - padahal
 * itu akun DIA SENDIRI yang terpecah. Lihat Santri_M::getAkunWaliGanda()/gabungkanAkunWali().
 */
class Akunganda extends CI_Controller
{
  public function __construct()
  {
    parent::__construct();
    cek_level('Admin');
    $this->load->model('Santri_M');
  }

  public function index()
  {
    $pesan = $this->input->get('pesan') ?: $this->session->flashdata('pesan');
    $cari = trim((string) $this->input->get('cari'));
    $sim_email = trim((string) $this->input->get('sim_email'));
    $sim_id_siswa = trim((string) $this->input->get('sim_id_siswa'));
    $data = [
      'title' => 'Akun Wali Ganda',
      'user' => $this->db->get_where('login', ['username' => $this->session->userdata('username')])->row_array(),
      'grup' => $this->Santri_M->getAkunWaliGanda(),
      'cari' => $cari,
      'hasil_cari' => $cari !== '' ? $this->Santri_M->cariRawAkunWali($cari) : null,
      'sim_email' => $sim_email,
      'sim_id_siswa' => $sim_id_siswa,
      'hasil_simulasi' => ($sim_email !== '' && $sim_id_siswa !== '') ? $this->Santri_M->simulasiCekEmailWali($sim_email, $sim_id_siswa) : null,
      'pesan' => $pesan,
      'isi' => tampilan_mobile() ? 'akunganda/mobile-index' : 'akunganda/index',
    ];
    $this->load->view(tampilan_mobile() ? 'templates/wrapper-mobile-simple' : 'templates/wrapper-admin', $data);
  }

  public function gabung()
  {
    $id_simpan = $this->input->post('id_simpan');
    $id_lain = $this->input->post('id_lain');

    if (!$id_simpan || !$id_lain) {
      redirect('akunganda?pesan=' . rawurlencode('Pilih akun yang ingin disimpan, tidak diproses.'));
      return;
    }

    foreach ((array) $id_lain as $lain) {
      if ((int) $lain === (int) $id_simpan) {
        continue;
      }
      $this->Santri_M->gabungkanAkunWali($id_simpan, $lain);
    }

    redirect('akunganda?pesan=' . rawurlencode('Akun berhasil digabungkan menjadi satu.'));
  }
}

/* End of file Akunganda.php */
