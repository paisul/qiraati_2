<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Hasil_Ujian extends CI_Controller
{

  public function __construct()
  {
    parent::__construct();
    cek_login();
    $this->load->model('Target_ujian_M');
    $this->load->model('Santri_M');
    $this->load->model('Kelas_M');
    $this->load->model('Periode_ujian_M');
    $this->load->model('Rekap_ujian_M');
    $this->load->model('Hasil_ujian_M');
  }


  public function index()
  {
    $pesan = $this->input->get('pesan') ?: $this->session->flashdata('pesan');

    $data = [
      'title' => 'Hasil Ujian',
      'user' => $this->db->get_where('login', ['username' => $this->session->userdata('username')])->row_array(),
      // 'target_ujian' => $this->Target_ujian_M->getAllTargetUjian(),
      'santri' => $this->Santri_M->getAllSantri(),
      'periode_ujian' => $this->Periode_ujian_M->getAllPeriodeUjian(),
      'rekap_ujian' => $this->Rekap_ujian_M->getAllRekapUjian(),
      'hasil_ujian' => $this->Hasil_ujian_M->getAllHasilUjian(),
      'pesan' => $pesan,
      'isi' => tampilan_mobile() ? 'ujian/mobile-hasil_ujian' : 'ujian/v-hasil_ujian',
    ];
    // check($data['hasil_ujian']);
    $this->load->view(tampilan_mobile() ? 'templates/wrapper-mobile-simple' : 'templates/wrapper-admin', $data);
  }

  public function form_add()
  {
    $IdSiswa = $this->input->get('santri');
    $periode_ujian = $this->input->get('periodeujian');
    $isi = tampilan_mobile() ? 'ujian/mobile-add_hasil_ujian' : 'ujian/v-add_hasil_ujian';
    $wrapper = tampilan_mobile() ? 'templates/wrapper-mobile-simple' : 'templates/wrapper-admin';
    $pesan = $this->input->get('pesan') ?: $this->session->flashdata('pesan');

    if (!$IdSiswa && $periode_ujian) {
      $data = [
        'title' => 'Hasil Ujian',
        'user' => $this->db->get_where('login', ['username' => $this->session->userdata('username')])->row_array(),
        'santri' => $this->Santri_M->getAllSantri(),
        'data_santri' => '',
        'kelas' => $this->Kelas_M->getAllKelas(),
        'periode_ujian' => $this->Periode_ujian_M->getAllPeriodeUjian(),
        'pesan' => $pesan,
        'isi' => $isi,
      ];
      // check($data['santri']);
      $this->load->view($wrapper, $data);
    } else {
      $data = [
        'title' => 'Hasil Ujian',
        'user' => $this->db->get_where('login', ['username' => $this->session->userdata('username')])->row_array(),
        'santri' => $this->Santri_M->getAllSantri(),
        'data_santri' => $this->Santri_M->getSantri_Periode_Nilai($IdSiswa, $periode_ujian),
        'kelas' => $this->Kelas_M->getAllKelas(),
        'periode_ujian' => $this->Periode_ujian_M->getAllPeriodeUjian(),
        'pesan' => $pesan,
        'isi' => $isi,
      ];
      // check($data['data_santri']);
      $this->load->view($wrapper, $data);
    }
  }

  public function aksi_form_add()
  {
    $IdSiswa = $this->input->post('IdSiswa');
    $IdPeriodeUjian = $this->input->post('IdPeriodeUjian');
    $TotalNilai = $this->input->post('TotalNilai');
    $RataRata = $this->input->post('RataRata');
    $Reward = $this->input->post('Reward');

    $data = [
      'IdSiswa' => $IdSiswa,
      'IdPeriodeUjian' => $IdPeriodeUjian,
      'Total' => $TotalNilai,
      'Rata-rata' => $RataRata,
      'Reward' => $Reward
    ];
    // check($data);
    $this->Hasil_ujian_M->addHasilUjianIndividu($data);
    redirect('ujian/hasil_ujian?pesan=' . rawurlencode('Berhasil ditambahkan!'));
  }

  public function form_add_banyak()
  {
    $idKelas = $this->input->get('kelas');
    $idPeriodeUjian = $this->input->get('periodeujian');
    $isi = tampilan_mobile() ? 'ujian/mobile-add_hasil_ujian_all_santri' : 'ujian/v-add_hasil_ujian_all_santri';
    $wrapper = tampilan_mobile() ? 'templates/wrapper-mobile-simple' : 'templates/wrapper-admin';
    $pesan = $this->input->get('pesan') ?: $this->session->flashdata('pesan');

    if (!$idKelas && $idPeriodeUjian) {
      $data = [
        'title' => 'Hasil Ujian',
        'user' => $this->db->get_where('login', ['username' => $this->session->userdata('username')])->row_array(),
        'data_santri' => '',
        'nilai_santri' => '',
        'kelas' => $this->Kelas_M->getAllKelas(),
        'periode_ujian' => $this->Periode_ujian_M->getAllPeriodeUjian(),
        // 'rekap_ujian' => $this->Rekap_ujian_M->getAllRekapUjian(),
        // 'hasil_ujian' => $this->Hasil_ujian_M->getAllHasilUjian(),
        'pesan' => $pesan,
        'isi' => $isi,
      ];
      // check($data['santri']);
      $this->load->view($wrapper, $data);
    } else {
      $data = [
        'title' => 'Hasil Ujian',
        'user' => $this->db->get_where('login', ['username' => $this->session->userdata('username')])->row_array(),
        'data_santri' => $this->Santri_M->nilai_santri($idKelas, $idPeriodeUjian),
        // 'data_santri' => '',
        // 'nilai_santri' => '',
        'kelas' => $this->Kelas_M->getAllKelas(),
        'periode_ujian' => $this->Periode_ujian_M->getAllPeriodeUjian(),
        // 'rekap_ujian' => $this->Rekap_ujian_M->getAllRekapUjian(),
        // 'hasil_ujian' => $this->Hasil_ujian_M->getAllHasilUjian(),
        'pesan' => $pesan,
        'isi' => $isi,
      ];
      // check($data['data_santri']);
      $this->load->view($wrapper, $data);
    }
  }

  public function aksi_Hasil_Ujian_Kelas()
  {
    $IdSiswa = $this->input->post('IdSiswa');
    // $IdKelas = $this->input->post('IdKelas');
    $IdPeriodeUjian = $this->input->post('IdPeriodeUjian');
    $TotalNilai = $this->input->post('TotalNilai');
    $RataRata = $this->input->post('RataRata');
    $Reward = $this->input->post('Reward');
    $data = [];

    foreach ($Reward as $keys => $values) {
      foreach ($RataRata as $key => $val) {
        foreach ($TotalNilai as $Keys => $Values) {
          foreach ($IdSiswa as $Key => $Value) {
            $data[$Key]['IdSiswa']        = $Value;
            $data[$Key]['IdPeriodeUjian'] = $IdPeriodeUjian;
            $data[$Keys]['Total']         = $Values;
            $data[$key]['Rata-rata']      = $val;
            $data[$keys]['Reward']        = $values;
          }
        }
      }
    }
    // check($data);
    $this->Hasil_ujian_M->addHasilUjian($data);
    redirect('ujian/hasil_ujian?pesan=' . rawurlencode('Berhasil ditambahkan!'));
  }

  public function perankingan()
  {
    $IdKelas = $this->input->get('kelas');
    $IdPeriodeUjian = $this->input->get('periodeujian');

    if (!$IdKelas & $IdPeriodeUjian) {
      $data = [
        'title' => 'Perankingan Hasil Ujian',
        'user' => $this->db->get_where('login', ['username' => $this->session->userdata('username')])->row_array(),
        // 'target_ujian' => $this->Target_ujian_M->getAllTargetUjian(),
        'ranking_santri' => '',
        'kelas' => $this->Kelas_M->getAllKelas(),
        'periode_ujian' => $this->Periode_ujian_M->getAllPeriodeUjian(),
        'hasil_ujian' => $this->Hasil_ujian_M->getAllHasilUjian(),
        'isi' => tampilan_mobile() ? 'ujian/mobile-perankingan' : 'ujian/v-perankingan',
      ];
      // check($data['hasil_ujian']);
      $this->load->view(tampilan_mobile() ? 'templates/wrapper-mobile-simple' : 'templates/wrapper-admin', $data);
    } else {
      $data = [
        'title' => 'Perankingan Hasil Ujian',
        'user' => $this->db->get_where('login', ['username' => $this->session->userdata('username')])->row_array(),
        // 'target_ujian' => $this->Target_ujian_M->getAllTargetUjian(),
        'ranking_santri' => $this->Hasil_ujian_M->perankingan_kelas($IdKelas, $IdPeriodeUjian),
        'kelas' => $this->Kelas_M->getAllKelas(),
        'periode_ujian' => $this->Periode_ujian_M->getAllPeriodeUjian(),
        'hasil_ujian' => $this->Hasil_ujian_M->getAllHasilUjian(),
        'isi' => tampilan_mobile() ? 'ujian/mobile-perankingan' : 'ujian/v-perankingan',
      ];
      // check($data['ranking_santri']);
      $this->load->view(tampilan_mobile() ? 'templates/wrapper-mobile-simple' : 'templates/wrapper-admin', $data);
    }
  }

  public function proses_perankingan()
  {
    $IdHasil = $this->input->post('IdHasil');
    $IdSiswa = $this->input->post('IdSiswa');
    $IdPeriodeUjian = $this->input->post('IdPeriodeUjian');
    $TotalNilai = $this->input->post('TotalNilai');
    $RataRata = $this->input->post('RataRata');
    $Reward = $this->input->post('Reward');
    $Ranking = $this->input->post('Ranking');
    $data = [];

    foreach ($Ranking as $Rank => $rank) {
      foreach ($Reward as $Hadiah => $reward) {
        foreach ($RataRata as $Avg => $average) {
          foreach ($TotalNilai as $Total => $totalnilai) {
            foreach ($IdPeriodeUjian as $PeriodeUjian => $periodeujian) {
              foreach ($IdSiswa as $Idsiswa => $idsiswa) {
                foreach ($IdHasil as $Idhasil => $idhasil) {
                  $data[$Idhasil]['IdHasil'] = $idhasil;
                  $data[$Idsiswa]['IdSiswa'] = $idsiswa;
                  $data[$PeriodeUjian]['IdPeriodeUjian'] = $periodeujian;
                  $data[$Total]['Total'] = $totalnilai;
                  $data[$Avg]['Rata-rata'] = $average;
                  $data[$Hadiah]['Reward'] = $reward;
                  $data[$Rank]['Rangking'] = $rank;
                }
              }
            }
          }
        }
      }
    }

    // check($data);
    $this->Hasil_ujian_M->Update_Perankingan($data);
    redirect('ujian/hasil_ujian?pesan=' . rawurlencode('Berhasil dirangking!'));
  }

  public function form_update($IdHasil)
  {
    $data = [
      'title' => 'Ubah Reward Hasil Ujian',
      'user' => $this->db->get_where('login', ['username' => $this->session->userdata('username')])->row_array(),
      // 'target_ujian' => $this->Target_ujian_M->getAllTargetUjian(),
      // 'ranking_santri' => $this->Hasil_ujian_M->perankingan_kelas($IdKelas, $IdPeriodeUjian),
      'kelas' => $this->Kelas_M->getAllKelas(),
      'periode_ujian' => $this->Periode_ujian_M->getAllPeriodeUjian(),
      'hasil_ujian' => $this->Hasil_ujian_M->getHasilUjianById($IdHasil),
      'isi' => tampilan_mobile() ? 'ujian/mobile-form_update' : 'ujian/v-form_update',
    ];
    // var_dump($data['hasil_ujian']);
    $this->load->view(tampilan_mobile() ? 'templates/wrapper-mobile-simple' : 'templates/wrapper-admin', $data);
  }

  public function aksi_update()
  {
    $IdHasil = $this->input->post('IdHasil');
    $reward = $this->input->post('reward');
    // check($reward);
    $data = [
      'IdHasil' => $IdHasil,
      'Reward' => $reward
    ];
    $this->Hasil_ujian_M->updateReward($data);
    redirect('ujian/hasil_ujian?pesan=' . rawurlencode('Berhasil Diubah!'));
  }

  public function delete($id_Hasil)
  {
    $data = ['IdHasil' => $id_Hasil];
    $this->Hasil_ujian_M->deleteHasilUjian($data);
    redirect('ujian/hasil_ujian?pesan=' . rawurlencode('Berhasil Dihapus!'));
  }

  public function cari_data()
  {
    $nama_santri = $this->input->post('nama_santri');
    $pesan = $this->input->get('pesan') ?: $this->session->flashdata('pesan');
    $data = [
      'title' => 'Hasil Ujian',
      'user' => $this->db->get_where('login', ['username' => $this->session->userdata('username')])->row_array(),
      // 'target_ujian' => $this->Target_ujian_M->getAllTargetUjian(),
      'santri' => $this->Santri_M->getAllSantri(),
      'periode_ujian' => $this->Periode_ujian_M->getAllPeriodeUjian(),
      'rekap_ujian' => $this->Rekap_ujian_M->getAllRekapUjian(),
      'hasil_ujian' => $this->Hasil_ujian_M->getHasilUjianByNamaSantri($nama_santri),
      'pesan' => $pesan,
      'isi' => tampilan_mobile() ? 'ujian/mobile-hasil_ujian' : 'ujian/v-hasil_ujian',
    ];
    // check($data['hasil_ujian']);
    $this->load->view(tampilan_mobile() ? 'templates/wrapper-mobile-simple' : 'templates/wrapper-admin', $data);
  }

  public function export_excel()
  {
    $data = [
      'title' => 'Hasil Ujian',
      'user' => $this->db->get_where('login', ['username' => $this->session->userdata('username')])->row_array(),
      // 'target_ujian' => $this->Target_ujian_M->getAllTargetUjian(),
      'santri' => $this->Santri_M->getAllSantri(),
      'periode_ujian' => $this->Periode_ujian_M->getAllPeriodeUjian(),
      'rekap_ujian' => $this->Rekap_ujian_M->getAllRekapUjian(),
      'hasil_ujian' => $this->Hasil_ujian_M->getAllHasilUjian(),
    ];

    $this->load->view('export/excel/ujian/rekap_hasil_ujian', $data);
  }

  public function reset_hasilujian()
  {
    $this->Hasil_ujian_M->kosongkanHasilUjian();
    redirect('ujian/hasil_ujian?pesan=' . rawurlencode('Berhasil direset!'));
  }
}

/* End of file Hasil_Ujian.php */
