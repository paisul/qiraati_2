<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Wali extends CI_Controller
{
  // Dipakai callback validasi profil() supaya cek unik email/no ID card mengecualikan data santri sendiri.
  private $editing_id_santri = null;

  public function __construct()
  {
    parent::__construct();
    //Do your magic here
    cek_level('Wali');
    $this->load->model('Wali_M');
    $this->load->model('Periode_M');
    $this->load->model('Periode_ujian_M');
    $this->load->model('Raport_M');
    $this->load->model('Santri_M');
    $this->load->model('Absen_M');
    $this->load->model('Pengumuman_M');
  }

  private function getWaliLogin()
  {
    $username =  $this->session->userdata('username');
    $wali = $this->Wali_M->getDataWali($username);

    if (!$wali) {
      $this->session->unset_userdata('username');
      $this->session->unset_userdata('level');
      $this->session->set_flashdata('message', '<div class="alert alert-danger" role="alert">Akun wali belum terhubung dengan data santri. Silahkan hubungi admin.</div>');
      redirect('auth');
    }

    return $wali;
  }

  // Pilih wrapper desktop (AdminLTE/sidebar) atau mobile (PWA/bottom nav) - layout & navigasi
  // BEDA total, bukan cuma resize (lihat templates/wrapper-wali-mobile.php).
  private function getWrapper()
  {
    return tampilan_mobile() ? 'templates/wrapper-wali-mobile' : 'templates/wrapper-wali';
  }

  // Path view halaman isi, ikut cabang mobile/desktop yang sama seperti getWrapper().
  private function isi($nama)
  {
    return tampilan_mobile() ? 'wali/mobile/' . $nama : 'wali/' . $nama;
  }

  // Ganti anak yang sedang ditampilkan (dropdown sidebar) - hanya kalau anaknya memang milik akun ini.
  public function pilih_anak($id_siswa)
  {
    $wali = $this->getWaliLogin();
    $id_valid = array_map('intval', array_column($wali['daftar_anak'], 'IdSiswa'));

    if (in_array((int) $id_siswa, $id_valid, TRUE)) {
      $this->session->set_userdata('IdSiswaAktif', (int) $id_siswa);
    }

    $referer = $this->input->server('HTTP_REFERER');
    redirect($referer ? $referer : 'Wali/index');
  }

  // Wali ajukan Izin/Sakit untuk anak yang sedang aktif, SELALU untuk hari ini (tanggal dihitung
  // di server, bukan input Wali) - supaya Musyrif langsung tahu saat buka halaman Absen hari itu.
  public function ajukan_izin_sakit()
  {
    $wali = $this->getWaliLogin();
    $status = $this->input->post('status');
    $keterangan = trim($this->input->post('keterangan'));

    if (!in_array($status, ['Sakit', 'Izin'], TRUE) || $keterangan === '') {
      redirect('Wali/index?pesan=' . rawurlencode('Status dan keterangan wajib diisi, tidak dikirim.'));
      return;
    }

    $this->Absen_M->ajukanIzinSakitWali($wali['IdSiswa'], date('Y-m-d'), $status, $keterangan);
    redirect('Wali/index?pesan=' . rawurlencode('Berhasil dikirim, Musyrif akan langsung melihatnya saat absen.'));
  }


  public function index()
  {
    $wali = $this->getWaliLogin();
    $IdSiswa = $wali['IdSiswa'];
    // $Pekan = $this->input->get('pekan');
    $IdPeriode = $this->input->get('periode');
    $ringkasan_absen = $this->Absen_M->getRingkasanBulanIni($IdSiswa);
    $status_hari_ini = $this->Absen_M->getStatusSantriTanggal($IdSiswa, date('Y-m-d'));
    // Lihat komentar di Dana::index() - query string ?pesan= dipakai supaya redirect dari hook
    // CekMenuAktif (menu dinonaktifkan admin) tidak nyangkut lewat session flashdata.
    $pesan = $this->input->get('pesan') ?: $this->session->flashdata('pesan');
    // $cek = [
    //   'IdSiswa' => $IdSiswa,
    //   'Pekan' => $Pekan,
    //   'IdPeriode' => $IdPeriode
    // ];
    // check($cek);

    if (!$IdSiswa || !$IdPeriode) {
      $data = [
        'title' => 'Beranda',
        'user' => $wali,
        'pekan' => $this->Wali_M->getPekanSetoran(),
        'periode' => $this->Periode_M->getAllPeriode(),
        'setoran_santri' => '',
        'jumlah_setoran' => ['Jumlah_Setoran' => 0],
        'setoran_selesai' => ['Jumlah_Setoran_Selesai' => 0],
        'setoran_tidak_selesai' => ['Jumlah_Setoran_Tidak_Selesai' => 0],
        'rekap_setoran_selesai' => '',
        'rekap_setoran_tidak_selesai' => '',
        'ringkasan_absen' => $ringkasan_absen,
        'status_hari_ini' => $status_hari_ini,
        'pengumuman' => $this->Pengumuman_M->getTerbaruUntukWali(),
        'pesan' => $pesan,
        'isi' => $this->isi('dashboard'),
      ];
      // check($data['user']);
      $this->load->view($this->getWrapper(), $data);
    } else {
      $data = [
        'title' => 'Beranda',
        'user' => $wali,
        'pekan' => $this->Wali_M->getPekanSetoran(),
        'periode' => $this->Periode_M->getAllPeriode(),
        'setoran_santri' => $this->Wali_M->getDataRekapSetoranSantri($IdSiswa, $IdPeriode),
        'jumlah_setoran' => $this->Wali_M->getJumlahSetoranSantri($IdSiswa, $IdPeriode),
        'setoran_selesai' => $this->Wali_M->getJumlahSetoranSelesai($IdSiswa, $IdPeriode),
        'setoran_tidak_selesai' => $this->Wali_M->getJumlahSetoranTidakSelesai($IdSiswa, $IdPeriode),
        'rekap_setoran_selesai' => $this->Wali_M->getSetoranSantri_Selesai($IdSiswa, $IdPeriode),
        'rekap_setoran_tidak_selesai' => $this->Wali_M->getSetoranSantri_TidakSelesai($IdSiswa, $IdPeriode),
        'ringkasan_absen' => $ringkasan_absen,
        'status_hari_ini' => $status_hari_ini,
        'pengumuman' => $this->Pengumuman_M->getTerbaruUntukWali(),
        'pesan' => $pesan,
        'isi' => $this->isi('dashboard'),
      ];
      // check($data['user']);
      $this->load->view($this->getWrapper(), $data);
    }
  }

  public function Setoran()
  {
    $wali = $this->getWaliLogin();
    $IdSiswa = $wali['IdSiswa'];
    $Pekan = $this->input->get('pekan');
    $IdPeriode = $this->input->get('periode');

    if (!$IdSiswa || !$Pekan || !$IdPeriode) {
      $data = [
        'title' => 'Setoran Santri',
        'user' => $wali,
        'pekan' => $this->Wali_M->getPekanSetoran(),
        'periode' => $this->Periode_M->getAllPeriode(),
        'setoran_santri' => '',
        'isi' => $this->isi('setoran'),
      ];
      // check($data['user']);
      $this->load->view($this->getWrapper(), $data);
    } else {
      $data = [
        'title' => 'Setoran Santri',
        'user' => $wali,
        'pekan' => $this->Wali_M->getPekanSetoran(),
        'periode' => $this->Periode_M->getAllPeriode(),
        'setoran_santri' => $this->Wali_M->getSetoranSantri($IdSiswa, $IdPeriode, $Pekan),
        'isi' => $this->isi('setoran'),
      ];
      // check($data['user']);
      $this->load->view($this->getWrapper(), $data);
    }
  }

  public function Raport()
  {
    $wali = $this->getWaliLogin();
    $data = [
      'title' => 'Hasil Ujian Santri (Rapor)',
      'user' => $wali,
      'pekan' => $this->Wali_M->getPekanSetoran(),
      'periode_ujian' => $this->Periode_ujian_M->getAllPeriodeUjian(),
      'isi' => $this->isi('rapor'),
    ];
    // check($data['user']);
    $this->load->view($this->getWrapper(), $data);
  }

  public function preview()
  {
    $wali = $this->getWaliLogin();
    $id_siswa = $wali['IdSiswa'];
    $id_kelas = $wali['IdKelas'];
    // Data inputan form
    $id_periode_ujian = $this->input->post('periode_ujian');

    // Ambil idKelas berdasarkan periodeujian yang dipilih
    $idKelasdiPeriode = $this->Periode_ujian_M->getPeriodeUjianById($id_periode_ujian);
    // check($idKelasdiPeriode['IdKelas']);

    // Cek apakah Periode Ujian Sudah dipilih atau belum
    if ($id_periode_ujian) {
      // Cek apakah kelas yang dipilih sesuai dengan kelas santri
      if ($id_kelas == $idKelasdiPeriode['IdKelas']) {
        // Data-data Raport 
        $identitas_santri               = $this->Raport_M->getRaportIdentitasSantri($id_siswa, $id_periode_ujian);
        $prosentase_target              = $this->Raport_M->getRaport_Prosentase($id_siswa, $id_periode_ujian);
        $hasilujian_santri              = $this->Raport_M->getRaportHasilUjian($id_siswa, $id_periode_ujian);
        $nilai_ujian                    = $this->Raport_M->getRaport_NilaiUjian($id_siswa, $id_periode_ujian);
        $sum_avg_rank                   = $this->Raport_M->getRaport_Total_Avg_Rank($id_siswa, $id_periode_ujian);
        $jml_siswa_perKelas             = $this->Raport_M->getRaport_JmlSiswaPerKelas($id_kelas);
        $total_point_pelanggaran_ibadah = $this->Raport_M->getRaport_TotalPointPelanggaranIbadah($id_siswa);
        $keterangan_pelanggaran_ibadah  = $this->Raport_M->getRaport_KeteranganPelanggaranIbadah($id_siswa);
        $total_point_pelanggaran_bahasa = $this->Raport_M->getRaport_TotalPointPelanggaranBahasa($id_siswa);
        $keterangan_pelanggaran_bahasa  = $this->Raport_M->getRaport_KeteranganPelanggaranBahasa($id_siswa);
        $catatan_perkembangan_target    = $this->Raport_M->getRaport_Catatan_PerkembanganTarget($id_siswa, $id_periode_ujian);
        $catatan_sikap_santri           = $this->Raport_M->getRaport_Catatan_SikapSantri($id_siswa, $id_periode_ujian);
        $catatan_akhlaq_perilaku        = $this->Raport_M->getRaport_Catatan_AkhlaqPerilaku($id_siswa, $id_periode_ujian);
        $catatan_kerapian_kebersihan    = $this->Raport_M->getRaport_Catatan_KerapianKebersihan($id_siswa, $id_periode_ujian);
        $catatan_catatan_musyrif        = $this->Raport_M->getRaport_Catatan_CatatanMusyrif($id_siswa, $id_periode_ujian);
        $reward_ujian                   = $this->Raport_M->getRaport_Reward_Ujian($id_siswa, $id_periode_ujian);
        $pengasuh_pondok                = $this->Raport_M->getRaport_Pengesahan_Pengasuh();
        $direktur_tahfidz               = $this->Raport_M->getRaport_Pengesahan_Direktur();

        $data = [
          'identitas_santri'      => $identitas_santri,
          'prosentase_target'     => $prosentase_target,
          'hasil_ujian_santri'    => $hasilujian_santri,
          'nilai_ujian'           => $nilai_ujian,
          'hasil_akhir'           => $sum_avg_rank,
          'jml_siswa'             => $jml_siswa_perKelas,
          'points_ibadah'         => $total_point_pelanggaran_ibadah,
          'keterangan_ibadah'     => $keterangan_pelanggaran_ibadah,
          'points_bahasa'         => $total_point_pelanggaran_bahasa,
          'keterangan_bahasa'     => $keterangan_pelanggaran_bahasa,
          'c_perkembangan_target' => $catatan_perkembangan_target,
          'c_sikap_santri'        => $catatan_sikap_santri,
          'c_akhlaq_perilaku'     => $catatan_akhlaq_perilaku,
          'c_kerapian_kebersihan' => $catatan_kerapian_kebersihan,
          'c_catatan_musyrif'     => $catatan_catatan_musyrif,
          'reward_ujian'          => $reward_ujian,
          'pengasuh'              => $pengasuh_pondok,
          'direktur'              => $direktur_tahfidz
        ];
        // check($data['pengasuh']);
        $this->load->view('raport/preview', $data);
      } else {
        $this->session->set_flashdata('pesan', '<div class="alert alert-danger" role="alert">Maaf, kelas yang dipilih tidak sesuai dengan kelas santri</div>');
        redirect('Wali/Raport');
      }
    } else {
      $this->session->set_flashdata('pesan', '<div class="alert alert-danger" role="alert">Silahkan pilih periode ujian terlebih dahulu</div>');
      redirect('Wali/Raport');
    }
  }

  /**
   * Wali mengubah data profil sendiri: Email/Password login, dan Data Opsional anaknya
   * (Nama Ayah, Nama Ibu, Alamat, dst). Data Wajib (Nama, JenisKelamin, TanggalLahir, Kelas)
   * tetap hanya admin yang boleh ubah - SSOT tidak berubah.
   */
  public function profil()
  {
    $wali = $this->getWaliLogin();
    $pesan = $this->input->get('pesan') ?: $this->session->flashdata('pesan');
    $data = [
      'title' => 'Profil Saya',
      'user' => $wali,
      'santri' => $this->Santri_M->getSantriDetail($wali['IdSiswa']),
      'pesan' => $pesan,
      'isi' => $this->isi('profil'),
    ];

    $this->load->view($this->getWrapper(), $data);
  }

  // Form ubah profil - halaman terpisah dari detail (Wali/profil), supaya menekan menu "Profil
  // Saya" langsung menampilkan detail dulu, bukan form. Khusus mobile - di desktop (belum
  // dipisah) tetap satu halaman gabungan seperti sebelumnya.
  public function edit_profil()
  {
    if (!tampilan_mobile()) {
      redirect('Wali/profil');
      return;
    }

    $wali = $this->getWaliLogin();
    $data = [
      'title' => 'Ubah Profil',
      'user' => $wali,
      'santri' => $this->Santri_M->getSantriDetail($wali['IdSiswa']),
      'isi' => 'wali/mobile/edit_profil',
    ];

    $this->load->view($this->getWrapper(), $data);
  }

  public function simpan_profil()
  {
    $wali = $this->getWaliLogin();
    $this->editing_id_santri = $wali['IdSiswa'];

    $pesan_wajib = ['required' => 'Form %s wajib diisi !'];
    $this->form_validation->set_rules('email', 'Email Login', 'trim|required|valid_email|callback_cek_email_wali', $pesan_wajib + [
      'valid_email' => 'Mohon gunakan format email yang valid',
    ]);
    $this->form_validation->set_rules('password', 'Password', 'trim|min_length[8]', [
      'min_length' => '%s minimal 8 karakter',
    ]);
    $this->form_validation->set_rules('no_id_card', 'Nomor ID Card', 'trim|callback_cek_idcard_wali');
    $this->form_validation->set_rules('tanggal_mulai_belajar', 'Tanggal Mulai Belajar', 'trim|callback_cek_tanggal_wali');

    if ($this->form_validation->run() == FALSE) {
      $data = [
        'title' => 'Ubah Profil',
        'user' => $wali,
        'santri' => $this->Santri_M->getSantriDetail($wali['IdSiswa']),
        'isi' => tampilan_mobile() ? 'wali/mobile/edit_profil' : 'wali/profil',
      ];
      $this->load->view($this->getWrapper(), $data);
      return;
    }

    $data_santri = [
      'IdSiswa' => $wali['IdSiswa'],
      'NamaAyah' => $this->kosongkanJadiNullWali($this->input->post('nama_ayah')),
      'NamaIbu' => $this->kosongkanJadiNullWali($this->input->post('nama_ibu')),
      'Alamat' => $this->kosongkanJadiNullWali($this->input->post('alamat')),
      'SekolahAkademik' => $this->kosongkanJadiNullWali($this->input->post('sekolah_akademik')),
      'SekolahTadika' => $this->kosongkanJadiNullWali($this->input->post('sekolah_tadika')),
      'NoIDCard' => $this->kosongkanJadiNullWali($this->input->post('no_id_card')),
      'TglMulaiBelajar' => $this->kosongkanJadiNullWali($this->input->post('tanggal_mulai_belajar')),
    ];

    // Kalau ada foto dipilih tapi upload gagal (mis. lebih dari 8MB, format tidak didukung),
    // JANGAN lanjut simpan lalu bilang "berhasil" - langsung redirect dengan pesan error yang
    // sesungguhnya supaya wali tahu foto TIDAK tersimpan (sebelumnya ini "berhasil" palsu karena
    // pesan error upload gampang tertimpa pesan sukses simpan profil di akhir method ini).
    if (!empty($_FILES['pasfoto']['name'])) {
      $config['upload_path']   = upload_path('santri');
      if (!is_dir($config['upload_path'])) {
        mkdir($config['upload_path'], 0755, true);
      }
      $config['allowed_types'] = 'jpg|jpeg|png';
      $config['max_size']      = '8192';
      $config['file_name']     = 'Pasfoto_' . time() . '_' . preg_replace('/[^a-zA-Z0-9]/', '', $_FILES['pasfoto']['name']);
      $this->load->library('upload');
      $this->upload->initialize($config);

      if ($this->upload->do_upload('pasfoto')) {
        $data_santri['Pasfoto'] = $this->upload->data('file_name');

        if (!file_exists($config['upload_path'] . $data_santri['Pasfoto'])) {
          redirect((tampilan_mobile() ? 'Wali/edit_profil' : 'Wali/profil') . '?pesan=' . rawurlencode('Foto gagal tersimpan dengan benar di server, silakan coba upload ulang.'));
          return;
        }
      } else {
        redirect((tampilan_mobile() ? 'Wali/edit_profil' : 'Wali/profil') . '?pesan=' . rawurlencode(strip_tags($this->upload->display_errors())));
        return;
      }
    }

    $this->Santri_M->updateSantri($data_santri);

    $data_login = ['username' => $this->input->post('email')];
    if ($this->input->post('password')) {
      $data_login['password'] = password_hash($this->input->post('password'), PASSWORD_DEFAULT);
    }
    $this->Santri_M->updateLoginSantri($wali['IdSiswa'], $data_login);

    redirect('Wali/profil?pesan=' . rawurlencode('Profil berhasil diperbarui!'));
  }

  public function cek_email_wali($email)
  {
    if ($this->Santri_M->isEmailTerpakai($email, $this->editing_id_santri)) {
      $this->form_validation->set_message('cek_email_wali', '%s sudah terdaftar, gunakan email lain.');
      return FALSE;
    }

    return TRUE;
  }

  public function cek_idcard_wali($value)
  {
    if ($value === '' || $value === null) {
      return TRUE;
    }

    if ($this->Santri_M->isIdCardTerpakai($value, $this->editing_id_santri)) {
      $this->form_validation->set_message('cek_idcard_wali', '%s sudah digunakan santri lain.');
      return FALSE;
    }

    return TRUE;
  }

  public function cek_tanggal_wali($value)
  {
    if ($value === '' || $value === null) {
      return TRUE;
    }

    if (!tanggal_valid($value)) {
      $this->form_validation->set_message('cek_tanggal_wali', '%s bukan tanggal yang valid.');
      return FALSE;
    }

    return TRUE;
  }

  private function kosongkanJadiNullWali($value)
  {
    return ($value === null || $value === '') ? null : $value;
  }
}

/* End of file Wali.php */
