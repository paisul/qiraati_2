<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Daftar extends CI_Controller
{
  // Diisi uploadPasfoto() kalau upload gagal, dibaca simpan() untuk ditampilkan ulang di form -
  // supaya kegagalan upload (mis. foto dari HP lebih dari batas ukuran) tidak "berhasil" palsu.
  private $pesan_upload_gagal = null;

  public function __construct()
  {
    parent::__construct();
    $this->load->model('Pendaftaran_M');
    $this->load->model('Santri_M');
    $this->load->model('Kelas_M');
  }

  // Formulir pendaftaran santri baru (sama dengan Formulir Tambah Data Santri admin), diakses publik.
  public function index()
  {
    $data = [
      'title' => 'Pendaftaran Santri Baru',
      'mode' => 'tambah',
      'santri' => $this->formKosong(),
      'nis_baru' => $this->Santri_M->generateNis(),
      'kelas' => $this->Kelas_M->getAllKelasLengkap(),
      'jenis_kelamin_list' => pilihan_jenis_kelamin(),
    ];

    $this->load->view('daftar/index', $data);
  }

  // Simpan pendaftaran ke tabel staging (menunggu persetujuan admin) - BUKAN langsung ke tabel siswa.
  public function simpan()
  {
    $this->aturValidasiPendaftaran();

    if ($this->form_validation->run() == FALSE) {
      $data = [
        'title' => 'Pendaftaran Santri Baru',
        'mode' => 'tambah',
        'santri' => $this->formDariPost(),
        'nis_baru' => $this->input->post('nis'),
        'kelas' => $this->Kelas_M->getAllKelasLengkap(),
        'jenis_kelamin_list' => pilihan_jenis_kelamin(),
      ];

      $this->load->view('daftar/index', $data);
      return;
    }

    $pasfoto = $this->uploadPasfoto('pasfoto');

    if ($pasfoto === false) {
      $data = [
        'title' => 'Pendaftaran Santri Baru',
        'mode' => 'tambah',
        'santri' => $this->formDariPost(),
        'nis_baru' => $this->input->post('nis'),
        'kelas' => $this->Kelas_M->getAllKelasLengkap(),
        'jenis_kelamin_list' => pilihan_jenis_kelamin(),
        'pesan_upload_gagal' => $this->pesan_upload_gagal,
      ];

      $this->load->view('daftar/index', $data);
      return;
    }

    $data = [
      'NamaLengkap' => $this->input->post('nama'),
      'JenisKelamin' => $this->input->post('jenis_kelamin'),
      'TempatLahir' => $this->input->post('tempat_lahir'),
      'TanggalLahir' => $this->input->post('tanggal_lahir'),
      'Email' => $this->input->post('email'),
      'Password' => password_hash($this->input->post('password'), PASSWORD_DEFAULT),
      // Status tidak diisi lewat form pendaftaran - selalu Aktif saat disetujui admin.
      'Status' => 'Aktif',
      'IdKelas' => $this->input->post('kelas'),
      'Pasfoto' => $pasfoto ?: null,
      'NamaAyah' => $this->kosongkanJadiNull($this->input->post('nama_ayah')),
      'NamaIbu' => $this->kosongkanJadiNull($this->input->post('nama_ibu')),
      'Alamat' => $this->kosongkanJadiNull($this->input->post('alamat')),
      'SekolahAkademik' => $this->kosongkanJadiNull($this->input->post('sekolah_akademik')),
      'SekolahTadika' => $this->kosongkanJadiNull($this->input->post('sekolah_tadika')),
      'NoIDCard' => $this->kosongkanJadiNull($this->input->post('no_id_card')),
      // Kalau tidak dipilih, pakai tanggal daftar (hari ini) sebagai Tanggal Mulai Belajar.
      'TglMulaiBelajar' => $this->kosongkanJadiTanggalHariIni($this->input->post('tanggal_mulai_belajar')),
    ];

    $this->Pendaftaran_M->simpan($data);

    redirect('daftar/selesai');
  }

  public function selesai()
  {
    $this->load->view('daftar/selesai', ['title' => 'Pendaftaran Terkirim']);
  }

  private function aturValidasiPendaftaran()
  {
    $pesan_wajib = ['required' => 'Form %s wajib diisi !'];

    $this->form_validation->set_rules('nama', 'Nama Lengkap', 'trim|required|min_length[3]', $pesan_wajib + [
      'min_length' => '%s minimal 3 karakter',
    ]);
    $this->form_validation->set_rules('jenis_kelamin', 'Jenis Kelamin', 'trim|required|in_list[' . implode(',', pilihan_jenis_kelamin()) . ']', $pesan_wajib);
    $this->form_validation->set_rules('tempat_lahir', 'Tempat Lahir', 'trim|required', $pesan_wajib);
    $this->form_validation->set_rules('tanggal_lahir', 'Tanggal Lahir', 'trim|required|callback_cek_tanggal_lahir_daftar', $pesan_wajib);
    $this->form_validation->set_rules('kelas', 'Kelas', 'trim|required', $pesan_wajib);
    // Sengaja TIDAK ada pengecekan "email sudah terdaftar" di sini - orang tua boleh mendaftarkan
    // anak kedua dst dengan email yang sama walau passwordnya beda dari akun yang sudah ada;
    // Santri::setujui_pendaftaran() yang akan menyambungkan ke akun lama itu (bukan bikin akun
    // ganda) saat admin menyetujui, terlepas dari password yang diisi di sini.
    $this->form_validation->set_rules('email', 'Email Login', 'trim|required|valid_email', $pesan_wajib + [
      'valid_email' => 'Mohon gunakan format email yang valid',
    ]);
    $this->form_validation->set_rules('password', 'Password', 'trim|required|min_length[8]', $pesan_wajib + [
      'min_length' => '%s minimal 8 karakter',
    ]);

    $this->form_validation->set_rules('nama_ayah', 'Nama Ayah', 'trim');
    $this->form_validation->set_rules('nama_ibu', 'Nama Ibu', 'trim');
    $this->form_validation->set_rules('alamat', 'Alamat Lengkap', 'trim');
    $this->form_validation->set_rules('sekolah_akademik', 'Sekolah Akademik', 'trim');
    $this->form_validation->set_rules('sekolah_tadika', 'Sekolah Tadika', 'trim');
    $this->form_validation->set_rules('no_id_card', 'Nomor ID Card', 'trim|callback_cek_idcard_daftar');
    $this->form_validation->set_rules('tanggal_mulai_belajar', 'Tanggal Mulai Belajar', 'trim|callback_cek_tanggal_daftar');
  }

  public function cek_idcard_daftar($value)
  {
    if ($value === '' || $value === null) {
      return TRUE;
    }

    if ($this->Santri_M->isIdCardTerpakai($value)) {
      $this->form_validation->set_message('cek_idcard_daftar', '%s sudah digunakan santri lain.');
      return FALSE;
    }

    return TRUE;
  }

  public function cek_tanggal_daftar($value)
  {
    if ($value === '' || $value === null) {
      return TRUE;
    }

    if (!tanggal_valid($value)) {
      $this->form_validation->set_message('cek_tanggal_daftar', '%s bukan tanggal yang valid.');
      return FALSE;
    }

    return TRUE;
  }

  // Sama seperti cek_tanggal_daftar(), tapi khusus Tanggal Lahir: tidak boleh tanggal di masa depan.
  public function cek_tanggal_lahir_daftar($value)
  {
    if ($value === '' || $value === null) {
      return TRUE;
    }

    if (!tanggal_valid($value)) {
      $this->form_validation->set_message('cek_tanggal_lahir_daftar', '%s bukan tanggal yang valid.');
      return FALSE;
    }

    if ($value > date('Y-m-d')) {
      $this->form_validation->set_message('cek_tanggal_lahir_daftar', '%s tidak boleh setelah hari ini.');
      return FALSE;
    }

    return TRUE;
  }

  private function kosongkanJadiNull($value)
  {
    return ($value === null || $value === '') ? null : $value;
  }

  // Kalau Tanggal Mulai Belajar tidak diisi, dianggap sama dengan tanggal daftar (hari ini).
  private function kosongkanJadiTanggalHariIni($value)
  {
    return ($value === null || $value === '') ? date('Y-m-d') : $value;
  }

  private function formKosong()
  {
    return [
      'IdSiswa' => null,
      'NIS' => '',
      'NamaLengkap' => '',
      'JenisKelamin' => '',
      'TempatLahir' => '',
      'TanggalLahir' => '',
      'IdKelas' => '',
      'Pasfoto' => '',
      'NamaAyah' => '',
      'NamaIbu' => '',
      'Alamat' => '',
      'SekolahAkademik' => '',
      'SekolahTadika' => '',
      'NoIDCard' => '',
      'TglMulaiBelajar' => '',
      'login' => ['username' => ''],
    ];
  }

  private function formDariPost()
  {
    return [
      'IdSiswa' => null,
      'NIS' => $this->input->post('nis'),
      'NamaLengkap' => $this->input->post('nama'),
      'JenisKelamin' => $this->input->post('jenis_kelamin'),
      'TempatLahir' => $this->input->post('tempat_lahir'),
      'TanggalLahir' => $this->input->post('tanggal_lahir'),
      'IdKelas' => $this->input->post('kelas'),
      'Pasfoto' => '',
      'NamaAyah' => $this->input->post('nama_ayah'),
      'NamaIbu' => $this->input->post('nama_ibu'),
      'Alamat' => $this->input->post('alamat'),
      'SekolahAkademik' => $this->input->post('sekolah_akademik'),
      'SekolahTadika' => $this->input->post('sekolah_tadika'),
      'NoIDCard' => $this->input->post('no_id_card'),
      'TglMulaiBelajar' => $this->input->post('tanggal_mulai_belajar'),
      'login' => ['username' => $this->input->post('email')],
    ];
  }

  // Return '' = tidak ada file dipilih (boleh lanjut, wajar). Return false = file dipilih TAPI
  // upload gagal (mis. lebih dari batas ukuran) - pesan_upload_gagal diisi supaya simpan() bisa
  // menampilkan ulang formnya dengan pesan yang jelas, bukan lanjut simpan pendaftaran tanpa foto
  // sambil bilang "berhasil".
  private function uploadPasfoto($field)
  {
    if (empty($_FILES[$field]['name'])) {
      return '';
    }

    $config['upload_path']   = upload_path('santri');
    if (!is_dir($config['upload_path'])) {
      mkdir($config['upload_path'], 0755, true);
    }
    $config['allowed_types'] = 'jpg|jpeg|png';
    $config['max_size']      = '8192';
    $config['file_name']     = 'Pasfoto_' . time() . '_' . preg_replace('/[^a-zA-Z0-9]/', '', $_FILES[$field]['name']);

    $this->load->library('upload');
    $this->upload->initialize($config);

    if ($this->upload->do_upload($field)) {
      $nama_file = $this->upload->data('file_name');

      // Jarang terjadi, tapi pernah: do_upload() lapor berhasil padahal filenya sendiri ternyata
      // tidak benar-benar ada di server - lebih baik dianggap gagal di sini daripada terlanjur
      // menyimpan nama file yang ujung-ujungnya jadi foto rusak begitu ditampilkan nanti.
      if (!file_exists($config['upload_path'] . $nama_file)) {
        $this->pesan_upload_gagal = 'Foto gagal tersimpan dengan benar di server, silakan coba upload ulang.';
        return false;
      }

      return $nama_file;
    }

    $this->pesan_upload_gagal = strip_tags($this->upload->display_errors());
    return false;
  }
}

/* End of file Daftar.php */
