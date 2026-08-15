<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Halaman_Musyrif extends CI_Controller
{

  public function __construct()
  {
    parent::__construct();
    cek_login();
    $this->load->model('Musyrif_M');
    $this->load->model('Periode_M');
    $this->load->model('Kelompok_M');
    $this->load->model('Rekap_setoran_M');
    $this->load->model('Setoran_M');
    $this->load->model('Detail_kelompok_M');
    $this->load->model('Jadwal_M');
    $this->load->model('Detail_target_M');
    $this->load->model('Rekap_setoran_M');
    $this->load->model('Santri_M');
    $this->load->model('Kelas_M');
    $this->load->model('Pengumuman_M');
  }


  public function index()
  {
    $IdPeriode = $this->input->get('periode');
    $IdKelompok = $this->input->get('kelompok');

    $wrapper = tampilan_mobile() ? 'templates/wrapper-musyrif-mobile' : 'templates/wrapper-musyrif';
    $isi = tampilan_mobile() ? 'halaman_musyrif/mobile-dashboard' : 'halaman_musyrif/dashboard';
    // Lihat komentar di Dana::index() - query string ?pesan= dipakai supaya redirect dari hook
    // CekMenuAktif (menu dinonaktifkan admin) tidak nyangkut lewat session flashdata.
    $pesan = $this->input->get('pesan') ?: $this->session->flashdata('pesan');

    if (!$IdPeriode && $IdKelompok) {
      $username =  $this->session->userdata('username');
      $data = [
        'title' => 'Dashboard',
        'user' => $this->Musyrif_M->getDataMusyrif($username),
        'periode' => $this->Periode_M->getAllPeriode(),
        'kelompok_halaqoh' => $this->Kelompok_M->getAllKelompok(),
        'rekap_setoran_kelompok' => '',
        'pengumuman' => $this->Pengumuman_M->getTerbaruUntukWali(),
        'pesan' => $pesan,
        'isi' => $isi,
      ];

      $this->load->view($wrapper, $data);
    } else {
      $username =  $this->session->userdata('username');
      $data = [
        'title' => 'Dashboard',
        'user' => $this->Musyrif_M->getDataMusyrif($username),
        'periode' => $this->Periode_M->getAllPeriode(),
        'kelompok_halaqoh' => $this->Kelompok_M->getAllKelompok(),
        'rekap_setoran_kelompok' => $this->Rekap_setoran_M->getRekapSetoranBy_Kelompok_Periode($IdPeriode, $IdKelompok),
        'pengumuman' => $this->Pengumuman_M->getTerbaruUntukWali(),
        'pesan' => $pesan,
        'isi' => $isi,
      ];

      $this->load->view($wrapper, $data);
    }
  }

  // Setoran
  public function setoran_oleh_Musyrif()
  {
    $username =  $this->session->userdata('username');
    $pesan = $this->input->get('pesan') ?: $this->session->flashdata('pesan');
    $data = [
      'title'   => 'Setoran',
      'user' => $this->Musyrif_M->getDataMusyrif($username),
      'setoran' => $this->Setoran_M->getAllSetoran(),
      'detail_target' => $this->Detail_target_M->getAllDetailTarget(),
      'detail_kelompok' => $this->Detail_kelompok_M->tampilAllDetailKelompok(),
      'jadwal' => $this->Jadwal_M->getAllJadwal(),
      'kelompok' => $this->Kelompok_M->getAllKelompok(),
      'pesan' => $pesan,
      'isi'     => tampilan_mobile() ? 'halaman_musyrif/mobile-setoran' : 'halaman_musyrif/v-setoran',
    ];
    // var_dump($data['detail_kelompok']);
    // die;

    $this->load->view(tampilan_mobile() ? 'templates/wrapper-musyrif-mobile' : 'templates/wrapper-musyrif', $data);
  }

  public function add_setoran()
  {
    $data = [
      'IdDetailTarget' => $this->input->post('isi'),
      'IdJadwal' => $this->input->post('jadwalHalaqoh'),
      'IdDetailKelompok' => $this->input->post('kelompok'),
      'Presensi' => $this->input->post('presensi'),
      'Keterangan' => $this->input->post('keterangan')
    ];
    // check($data);
    $this->Setoran_M->addSetoran($data);
    redirect('Halaman_Musyrif/setoran_oleh_Musyrif?pesan=' . rawurlencode('Berhasil ditambahkan!'));
  }

  public function update_setoran($id)
  {
    $data = [
      'IdSetoran' => $id,
      'IdDetailTarget' => $this->input->post('isi'),
      'IdJadwal' => $this->input->post('jadwalHalaqoh'),
      'IdDetailKelompok' => $this->input->post('kelompok'),
      'Presensi' => $this->input->post('presensi'),
      'Keterangan' => $this->input->post('keterangan')
    ];
    $this->Setoran_M->updateSetoran($data);
    redirect('Halaman_Musyrif/setoran_oleh_Musyrif?pesan=' . rawurlencode('Berhasil diubah!'));
  }

  public function delete_setoran($id)
  {
    $data = [
      'IdSetoran' => $id
    ];
    $this->Setoran_M->deleteSetoran($data);
    redirect('Halaman_Musyrif/setoran_oleh_Musyrif?pesan=' . rawurlencode('Berhasil dihapus!'));
  }
  // /. Setoran

  // Rekap Setoran
  public function rekap_setoran_oleh_Musyrif()
  {
    $username =  $this->session->userdata('username');
    $pesan = $this->input->get('pesan') ?: $this->session->flashdata('pesan');
    $data = [
      'title'   => 'Rekap Setoran',
      'user' => $this->Musyrif_M->getDataMusyrif($username),
      'rekap_setoran' => $this->Rekap_setoran_M->getAllRekapSetoran(),
      'santri' => $this->Santri_M->getAllSantri(),
      'pesan' => $pesan,
      'isi'     => tampilan_mobile() ? 'halaman_musyrif/mobile-rekap_setoran' : 'halaman_musyrif/v-rekap_setoran',
    ];
    // var_dump($data['detail_kelompok']);
    // die;

    $this->load->view(tampilan_mobile() ? 'templates/wrapper-musyrif-mobile' : 'templates/wrapper-musyrif', $data);
  }

  public function form_add_rekap_setoran()
  {
    $kelas = $this->input->get('kelas');
    $wrapper = tampilan_mobile() ? 'templates/wrapper-musyrif-mobile' : 'templates/wrapper-musyrif';
    $isi = tampilan_mobile() ? 'halaman_musyrif/mobile-add_rekap_setoran' : 'halaman_musyrif/v-add_rekap_setoran';
    // check($kelas);
    if (!$kelas) {
      $username =  $this->session->userdata('username');
      $data = [
        'title'   => 'Tambah Data Rekap Setoran',
        'user' => $this->Musyrif_M->getDataMusyrif($username),
        'santri' => '',
        'kelas' => $this->Kelas_M->getAllKelas(),
        'isi'     => $isi,
      ];
      // var_dump($data['detail_kelompok']);
      // die;

      $this->load->view($wrapper, $data);
    } else {
      $username =  $this->session->userdata('username');
      $data = [
        'title'   => 'Tambah Data Rekap Setoran',
        'user' => $this->Musyrif_M->getDataMusyrif($username),
        'santri' => $this->Santri_M->getSantriKelas($kelas),
        'kelas' => $this->Kelas_M->getAllKelas(),
        'isi'     => $isi,
      ];
      // var_dump($data['detail_kelompok']);
      // die;

      $this->load->view($wrapper, $data);
    }
  }

  public function add_hasil_rekap_setoran()
  {
    $kelas = $this->input->post('kelas');

    $idSiswa = $this->input->post('IdSiswa');
    // check($idSiswa);
    $pekan = $this->input->post('pekan');


    $jml_setoran = $this->Rekap_setoran_M->countKeterangan($idSiswa, $pekan);
    $username =  $this->session->userdata('username');
    // check($this->db->last_query());
    $data = [
      'title'   => 'Tambah Data Rekap Setoran',
      'user' => $this->Musyrif_M->getDataMusyrif($username),
      'santri' => $this->Santri_M->getSantriKelas($kelas),
      'kelas' => $this->Kelas_M->getAllKelas(),
      'isi'     => tampilan_mobile() ? 'halaman_musyrif/mobile-add_rekap_setoran' : 'halaman_musyrif/v-add_rekap_setoran',
      'IdSiswa' => $idSiswa,
      'JmlTugas' => $this->input->post('jml_tugas'),
      'data_setoran' => $jml_setoran,
      'PekanRekap' => $pekan,
    ];
    // check($data);
    $this->load->view(tampilan_mobile() ? 'templates/wrapper-musyrif-mobile' : 'templates/wrapper-musyrif', $data);
  }

  public function add_Rekap_Setoran()
  {
    $idSiswa = $this->input->post('IdSiswa');
    $JmlTugas = $this->input->post('JmlTugas');
    $JmlSetoran = $this->input->post('JmlSetoran');
    $PekanRekap = $this->input->post('PekanRekap');
    $Hasil = $this->input->post('Hasil');
    $Prosentase = $this->input->post('Prosentase');
    $Reward = $this->input->post('Reward');
    $data = [];
    // $cek = [
    //   'IdSiswa' => $idSiswa,
    //   'JmlTugas' => $JmlTugas,
    //   'JmlSetoran' => $JmlSetoran,
    //   'PekanRekap' => $PekanRekap,
    //   'Hasil' => $Hasil,
    //   'Prosentase' => $Prosentase,
    //   'Reward' => $Reward
    // ];


    foreach ($Reward as $reward => $r) {
      foreach ($Prosentase as $pros => $p) {
        foreach ($Hasil as $hasil => $h) {
          foreach ($JmlSetoran as $setoran => $js) {
            foreach ($idSiswa as $siswa => $s) {
              $data[$siswa]['IdSiswa'] = $s;
              $data[$siswa]['JmlTugas'] = $JmlTugas;
              $data[$setoran]['JmlSetoran'] = $js;
              $data[$siswa]['PekanRekap'] = $PekanRekap;
              $data[$hasil]['Hasil'] = $h;
              $data[$pros]['Prosentase'] = $p;
              $data[$reward]['Reward'] = $r;
            }
          }
        }
      }
    }
    // check($data);
    $this->Rekap_setoran_M->addRekapSetoran($data);
    redirect('Halaman_Musyrif/rekap_setoran_oleh_Musyrif?pesan=' . rawurlencode('Berhasil ditambahkan!'));
  }

  // Pembimbing mengubah profilnya sendiri (nama, no HP, foto, tanda tangan, password) - bukan lewat menu admin.
  public function profil()
  {
    $username = $this->session->userdata('username');
    $pesan = $this->input->get('pesan') ?: $this->session->flashdata('pesan');
    $data = [
      'title' => 'Profil Saya',
      'user' => $this->Musyrif_M->getDataMusyrif($username),
      'pesan' => $pesan,
      'isi' => tampilan_mobile() ? 'halaman_musyrif/mobile-profil' : 'halaman_musyrif/profil',
    ];

    $this->load->view(tampilan_mobile() ? 'templates/wrapper-musyrif-mobile' : 'templates/wrapper-musyrif', $data);
  }

  // Form ubah profil - halaman terpisah dari detail (Halaman_Musyrif/profil), supaya menekan
  // menu "Profil Saya" langsung menampilkan detail dulu, bukan form. Khusus mobile - di desktop
  // (belum dipisah) tetap satu halaman gabungan seperti sebelumnya.
  public function edit_profil()
  {
    if (!tampilan_mobile()) {
      redirect('Halaman_Musyrif/profil');
      return;
    }

    $username = $this->session->userdata('username');
    $user = $this->Musyrif_M->getDataMusyrif($username);

    if (!$user || empty($user['IdMusyrif'])) {
      redirect('Halaman_Musyrif/profil');
      return;
    }

    $data = [
      'title' => 'Ubah Profil',
      'user' => $user,
      'isi' => 'halaman_musyrif/mobile-edit_profil',
    ];
    $this->load->view('templates/wrapper-musyrif-mobile', $data);
  }

  public function simpan_profil()
  {
    $username = $this->session->userdata('username');
    $user = $this->Musyrif_M->getDataMusyrif($username);

    if (!$user || empty($user['IdMusyrif'])) {
      redirect('Halaman_Musyrif/profil?pesan=' . rawurlencode('Profil pembimbing Anda belum lengkap/tertaut. Silakan hubungi admin.'));
      return;
    }

    $this->form_validation->set_rules('nama_musyrif', 'Nama Lengkap', 'trim|required', [
      'required' => 'Form %s wajib diisi !',
    ]);
    $this->form_validation->set_rules('no_hp', 'No Handphone', 'trim|required', [
      'required' => 'Form %s wajib diisi !',
    ]);
    $this->form_validation->set_rules('password', 'Password', 'trim|min_length[4]', [
      'min_length' => 'Panjang %s minimal 4 karakter',
    ]);

    if ($this->form_validation->run() == FALSE) {
      $data = [
        'title' => 'Ubah Profil',
        'user' => $user,
        'isi' => tampilan_mobile() ? 'halaman_musyrif/mobile-edit_profil' : 'halaman_musyrif/profil',
      ];
      $this->load->view(tampilan_mobile() ? 'templates/wrapper-musyrif-mobile' : 'templates/wrapper-musyrif', $data);
      return;
    }

    $data_musyrif = [
      'IdMusyrif' => $user['IdMusyrif'],
      'NamaMusyrif' => $this->input->post('nama_musyrif'),
      'NoHp' => $this->input->post('no_hp'),
    ];

    if (!empty($_FILES['pasfoto']['name'])) {
      $config['file_name']     = 'Pasfoto_' . $this->input->post('nama_musyrif') . '_' . time();
      $config['upload_path']   = upload_path('pasfoto_musyrif');
      if (!is_dir($config['upload_path'])) {
        mkdir($config['upload_path'], 0755, true);
      }
      $config['allowed_types'] = 'jpg|jpeg|png';
      $config['max_size']      = '8192';
      $config['overwrite']     = true;
      $this->load->library('upload');
      $this->upload->initialize($config);

      if ($this->upload->do_upload('pasfoto')) {
        $data_musyrif['Pasfoto'] = $this->upload->data('file_name');

        if (!file_exists($config['upload_path'] . $data_musyrif['Pasfoto'])) {
          redirect((tampilan_mobile() ? 'Halaman_Musyrif/edit_profil' : 'Halaman_Musyrif/profil') . '?pesan=' . rawurlencode('Foto gagal tersimpan dengan benar di server, silakan coba upload ulang.'));
          return;
        }
      } else {
        redirect((tampilan_mobile() ? 'Halaman_Musyrif/edit_profil' : 'Halaman_Musyrif/profil') . '?pesan=' . rawurlencode(strip_tags($this->upload->display_errors())));
        return;
      }
    }

    if (!empty($_FILES['ttd']['name'])) {
      $config['file_name']     = 'TTD_' . $this->input->post('nama_musyrif');
      $config['upload_path']   = upload_path('ttd_musyrif');
      if (!is_dir($config['upload_path'])) {
        mkdir($config['upload_path'], 0755, true);
      }
      $config['allowed_types'] = 'jpg|jpeg|png';
      $config['max_size']      = '8192';
      $config['overwrite']     = true;
      $this->load->library('upload');
      $this->upload->initialize($config);

      if ($this->upload->do_upload('ttd')) {
        $data_musyrif['Ttd'] = $this->upload->data('file_name');

        if (!file_exists($config['upload_path'] . $data_musyrif['Ttd'])) {
          redirect((tampilan_mobile() ? 'Halaman_Musyrif/edit_profil' : 'Halaman_Musyrif/profil') . '?pesan=' . rawurlencode('Tanda tangan gagal tersimpan dengan benar di server, silakan coba upload ulang.'));
          return;
        }
      } else {
        redirect((tampilan_mobile() ? 'Halaman_Musyrif/edit_profil' : 'Halaman_Musyrif/profil') . '?pesan=' . rawurlencode(strip_tags($this->upload->display_errors())));
        return;
      }
    }

    $this->Musyrif_M->updateMusyrif($data_musyrif);

    if ($this->input->post('password')) {
      $this->db->where('IdUser', $user['IdUser']);
      $this->db->update('login', ['password' => password_hash($this->input->post('password'), PASSWORD_DEFAULT)]);
    }

    redirect('Halaman_Musyrif/profil?pesan=' . rawurlencode('Profil berhasil diperbarui!'));
  }
}

/* End of file Halaman_Musyrif.php */
