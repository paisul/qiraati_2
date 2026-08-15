<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Kelas extends CI_Controller
{
  // Diisi sebelum validasi berjalan pada mode ubah, dipakai untuk cek pembimbing tanpa masalah lain.
  private $editing_id = null;

  public function __construct()
  {
    parent::__construct();
    cek_login();
    $this->load->model('Kelas_M');
    $this->load->model('Musyrif_M');
    $this->load->model('Wali_M');
    $this->load->model('Santri_M');
  }

  // List semua kelas
  public function index()
  {
    $isi = 'kelas/index';
    if (tampilan_mobile()) {
      $isi = $this->session->userdata('level') == 'Wali' ? 'kelas/mobile-wali' : 'kelas/mobile-index';
    }

    $pesan = $this->input->get('pesan') ?: $this->session->flashdata('pesan');

    $data = [
      'title' => 'Data Kelas',
      'user' => $this->getUserLogin(),
      'kelas' => $this->Kelas_M->getAllKelasLengkap(),
      'pesan' => $pesan,
      'isi' => $isi,
    ];

    $this->load->view($this->getWrapper(), $data);
  }

  // Form tambah kelas
  public function tambah()
  {
    $this->tolakJikaWali();

    $data = [
      'title' => 'Tambah Data Kelas',
      'user' => $this->getUserLogin(),
      'mode' => 'tambah',
      'kelas' => $this->formKosong(),
      'musyrif' => $this->Musyrif_M->getAllMusyrif(),
      'nama_kelas_list' => pilihan_nama_kelas(),
      'isi' => tampilan_mobile() ? 'kelas/mobile-form' : 'kelas/form',
    ];

    $this->load->view($this->getWrapper(), $data);
  }

  public function simpan()
  {
    $this->tolakJikaWali();
    $this->aturValidasi();

    if ($this->form_validation->run() == FALSE) {
      $data = [
        'title' => 'Tambah Data Kelas',
        'user' => $this->getUserLogin(),
        'mode' => 'tambah',
        'kelas' => $this->formDariPost(),
        'musyrif' => $this->Musyrif_M->getAllMusyrif(),
        'nama_kelas_list' => pilihan_nama_kelas(),
        'isi' => tampilan_mobile() ? 'kelas/mobile-form' : 'kelas/form',
      ];

      $this->load->view($this->getWrapper(), $data);
      return;
    }

    $data = [
      'NamaKelas' => $this->input->post('nama_kelas'),
      'IdMusyrif' => $this->input->post('pembimbing'),
      'Lokasi' => $this->kosongkanJadiNull($this->input->post('lokasi')),
    ];

    $this->Kelas_M->addKelas($data);

    if ($this->input->post('aksi') === 'tambah_baru') {
      redirect('kelas/tambah?pesan=' . rawurlencode('Kelas "' . $data['NamaKelas'] . '" berhasil disimpan! Silakan tambah kelas berikutnya.'));
      return;
    }

    redirect('kelas?pesan=' . rawurlencode('Kelas "' . $data['NamaKelas'] . '" berhasil disimpan!'));
  }

  // Form ubah kelas
  public function ubah($id)
  {
    $this->tolakJikaWali();

    $kelas = $this->Kelas_M->getById($id);

    if (!$kelas) {
      redirect('kelas?pesan=' . rawurlencode('Data kelas tidak ditemukan!'));
      return;
    }

    $data = [
      'title' => 'Ubah Data Kelas',
      'user' => $this->getUserLogin(),
      'mode' => 'ubah',
      'kelas' => $kelas,
      'musyrif' => $this->Musyrif_M->getAllMusyrif(),
      'nama_kelas_list' => pilihan_nama_kelas(),
      'isi' => tampilan_mobile() ? 'kelas/mobile-form' : 'kelas/form',
    ];

    $this->load->view($this->getWrapper(), $data);
  }

  public function perbarui($id)
  {
    $this->tolakJikaWali();

    $kelas_lama = $this->Kelas_M->getById($id);

    if (!$kelas_lama) {
      redirect('kelas?pesan=' . rawurlencode('Data kelas tidak ditemukan!'));
      return;
    }

    $this->editing_id = $id;
    $this->aturValidasi();

    if ($this->form_validation->run() == FALSE) {
      $kelas_gagal = $this->formDariPost();
      $kelas_gagal['IdKelas'] = $id;
      $kelas_gagal['JumlahSantri'] = $kelas_lama['JumlahSantri'];

      $data = [
        'title' => 'Ubah Data Kelas',
        'user' => $this->getUserLogin(),
        'mode' => 'ubah',
        'kelas' => $kelas_gagal,
        'musyrif' => $this->Musyrif_M->getAllMusyrif(),
        'nama_kelas_list' => pilihan_nama_kelas(),
        'isi' => tampilan_mobile() ? 'kelas/mobile-form' : 'kelas/form',
      ];

      $this->load->view($this->getWrapper(), $data);
      return;
    }

    $data = [
      'IdKelas' => $id,
      'NamaKelas' => $this->input->post('nama_kelas'),
      'IdMusyrif' => $this->input->post('pembimbing'),
      'Lokasi' => $this->kosongkanJadiNull($this->input->post('lokasi')),
    ];

    $this->Kelas_M->updateKelas($data);
    redirect('kelas?pesan=' . rawurlencode('Kelas "' . $data['NamaKelas'] . '" berhasil diubah!'));
  }

  public function delete($id)
  {
    $this->tolakJikaWali();

    // Cuma untuk kelas yang SUDAH kosong (link Hapus biasa di view hanya muncul kalau
    // JumlahSantri == 0) - tetap dijaga di sini juga sebagai jaring pengaman kalau URL-nya
    // diakses langsung. Kelas yang masih ada santrinya HARUS lewat hapus_dengan_pindah()
    // di bawah (popup pilih kelas tujuan), bukan lewat sini.
    $jumlah_santri = $this->Kelas_M->jumlahSantriDiKelas($id);
    if ($jumlah_santri > 0) {
      redirect('kelas?pesan=' . rawurlencode("Kelas ini tidak bisa dihapus karena masih ada {$jumlah_santri} santri aktif di dalamnya. Pilih kelas tujuan dulu lewat tombol Hapus di halaman Data Kelas."));
      return;
    }

    $this->Kelas_M->deleteKelas(['IdKelas' => $id]);
    redirect('kelas?pesan=' . rawurlencode('Berhasil dihapus!'));
  }

  // Kelas yang masih ada santrinya harus lewat sini (dipicu popup di view, bukan link Hapus
  // biasa) - pindahkan dulu semua santrinya ke kelas tujuan pilihan admin, baru kelasnya dihapus.
  // Tanpa kelas_tujuan dipilih, penghapusan DITOLAK sama sekali.
  public function hapus_dengan_pindah($id)
  {
    $this->tolakJikaWali();

    $id_kelas_tujuan = $this->input->post('kelas_tujuan');
    if (!$id_kelas_tujuan) {
      redirect('kelas?pesan=' . rawurlencode('Pilih kelas tujuan untuk memindahkan santrinya - kelas ini tidak bisa dihapus tanpa memilih tujuan.'));
      return;
    }

    if ((string) $id_kelas_tujuan === (string) $id) {
      redirect('kelas?pesan=' . rawurlencode('Kelas tujuan tidak boleh sama dengan kelas yang mau dihapus.'));
      return;
    }

    $jumlah_dipindah = $this->Santri_M->pindahkanSemuaSantriKelas($id, $id_kelas_tujuan);
    $this->Kelas_M->deleteKelas(['IdKelas' => $id]);
    redirect('kelas?pesan=' . rawurlencode("Berhasil dihapus - {$jumlah_dipindah} santri sudah dipindahkan ke kelas tujuan."));
  }

  public function export_excel()
  {
    $data = [
      'title' => 'Data Kelas',
      'user' => $this->getUserLogin(),
      'kelas' => $this->Kelas_M->getAllKelasLengkap(),
    ];

    $this->load->view('export/excel/kelas', $data);
  }

  // Wali hanya boleh melihat (read-only) - tolak percobaan tambah/ubah/hapus lewat URL langsung.
  private function tolakJikaWali()
  {
    if ($this->session->userdata('level') == 'Wali') {
      redirect('kelas?pesan=' . rawurlencode('Akun wali hanya dapat melihat data ini.'));
      exit;
    }
  }

  private function aturValidasi()
  {
    $pesan_wajib = ['required' => 'Form %s wajib diisi !'];

    $this->form_validation->set_rules('nama_kelas', 'Nama Kelas', 'trim|required|in_list[' . implode(',', pilihan_nama_kelas()) . ']', $pesan_wajib);
    $this->form_validation->set_rules('pembimbing', 'Pembimbing', 'trim|required|callback_cek_musyrif_ada', $pesan_wajib);
    $this->form_validation->set_rules('lokasi', 'Lokasi/Ruangan', 'trim');
  }

  public function cek_musyrif_ada($id_musyrif)
  {
    $musyrif = $this->Musyrif_M->getAllMusyrif();
    $id_valid = array_column($musyrif, 'IdMusyrif');

    if (!in_array((int) $id_musyrif, array_map('intval', $id_valid), TRUE)) {
      $this->form_validation->set_message('cek_musyrif_ada', 'Pembimbing yang dipilih tidak valid.');
      return FALSE;
    }

    return TRUE;
  }

  private function kosongkanJadiNull($value)
  {
    return ($value === null || $value === '') ? null : $value;
  }

  private function formKosong()
  {
    return [
      'IdKelas' => null,
      'NamaKelas' => '',
      'IdMusyrif' => '',
      'Lokasi' => '',
      'JumlahSantri' => 0,
    ];
  }

  private function formDariPost()
  {
    return [
      'IdKelas' => null,
      'NamaKelas' => $this->input->post('nama_kelas'),
      'IdMusyrif' => $this->input->post('pembimbing'),
      'Lokasi' => $this->input->post('lokasi'),
      'JumlahSantri' => 0,
    ];
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

    if ($this->session->userdata('level') == 'Wali') {
      $wali = $this->Wali_M->getDataWali($username);

      if (!$wali) {
        $this->session->unset_userdata('username');
        $this->session->unset_userdata('level');
        $this->session->set_flashdata('message', '<div class="alert alert-danger" role="alert">Akun wali belum terhubung dengan data santri. Silahkan hubungi admin.</div>');
        redirect('auth');
        exit;
      }

      return $wali;
    }

    return $this->db->get_where('login', ['username' => $username])->row_array();
  }

  private function getWrapper()
  {
    if ($this->session->userdata('level') == 'Wali') {
      return tampilan_mobile() ? 'templates/wrapper-wali-mobile' : 'templates/wrapper-wali';
    }

    if ($this->session->userdata('level') == 'Musyrif' || $this->session->userdata('level') == 'Guru') {
      return tampilan_mobile() ? 'templates/wrapper-musyrif-mobile' : 'templates/wrapper-musyrif';
    }

    return tampilan_mobile() ? 'templates/wrapper-mobile-simple' : 'templates/wrapper-admin';
  }
}

/* End of file Kelas.php */
