<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Rekap_setoran extends CI_Controller
{

  public function __construct()
  {
    parent::__construct();
    //Load Dependencies
    cek_login();
    $this->load->model('Rekap_setoran_M');
    $this->load->model('Santri_M');
    $this->load->model('Kelas_M');
  }

  // List all your items
  public function index()
  {
    $pesan = $this->input->get('pesan') ?: $this->session->flashdata('pesan');

    $data = [
      'title'   => 'Rekap Setoran',
      'user'    => $this->db->get_where('login', ['username' => $this->session->userdata('username')])->row_array(),
      'rekap_setoran' => $this->Rekap_setoran_M->getAllRekapSetoran(),
      'santri' => $this->Santri_M->getAllSantri(),
      'pesan' => $pesan,
      'isi'     => tampilan_mobile() ? 'halaqoh/mobile-rekap_setoran' : 'halaqoh/v-rekap_setoran',
    ];
    // var_dump($data['rekap_setoran']);
    // die;

    $this->load->view(tampilan_mobile() ? 'templates/wrapper-mobile-simple' : 'templates/wrapper-admin', $data);
  }

  public function form_add()
  {
    $kelas = $this->input->get('kelas');
    $isi = tampilan_mobile() ? 'halaqoh/mobile-add_rekap_setoran' : 'halaqoh/v-add_rekap_setoran';
    $wrapper = tampilan_mobile() ? 'templates/wrapper-mobile-simple' : 'templates/wrapper-admin';
    // check($kelas);
    if (!$kelas) {
      $data = [
        'title'   => 'Tambah Data Rekap Setoran',
        'user'    => $this->db->get_where('login', ['username' => $this->session->userdata('username')])->row_array(),
        'santri' => '',
        'kelas' => $this->Kelas_M->getAllKelas(),
        'isi'     => $isi,
      ];
      // var_dump($data['detail_kelompok']);
      // die;

      $this->load->view($wrapper, $data);
    } else {
      $data = [
        'title'   => 'Tambah Data Rekap Setoran',
        'user'    => $this->db->get_where('login', ['username' => $this->session->userdata('username')])->row_array(),
        'santri' => $this->Santri_M->getSantriKelas($kelas),
        'kelas' => $this->Kelas_M->getAllKelas(),
        'isi'     => $isi,
      ];
      // var_dump($data['detail_kelompok']);
      // die;

      $this->load->view($wrapper, $data);
    }
  }

  public function add_hasil()
  {
    $kelas = $this->input->post('kelas');

    $idSiswa = $this->input->post('IdSiswa');
    // check($idSiswa);
    $pekan = $this->input->post('pekan');


    $jml_setoran = $this->Rekap_setoran_M->countKeterangan($idSiswa, $pekan);
    // check($this->db->last_query());
    $data = [
      'title'   => 'Tambah Data Rekap Setoran',
      'user'    => $this->db->get_where('login', ['username' => $this->session->userdata('username')])->row_array(),
      'santri' => $this->Santri_M->getSantriKelas($kelas),
      'kelas' => $this->Kelas_M->getAllKelas(),
      'isi'     => tampilan_mobile() ? 'halaqoh/mobile-add_rekap_setoran' : 'halaqoh/v-add_rekap_setoran',
      'IdSiswa' => $idSiswa,
      'JmlTugas' => $this->input->post('jml_tugas'),
      'id_kelas' => $kelas,
      'data_setoran' => $jml_setoran,
      'PekanRekap' => $pekan,
    ];
    // check($data);
    $this->load->view(tampilan_mobile() ? 'templates/wrapper-mobile-simple' : 'templates/wrapper-admin', $data);
  }

  // Add a new item
  public function add()
  {
    $idSiswa = $this->input->post('IdSiswa');
    $JmlTugas = $this->input->post('JmlTugas');
    $JmlSetoran = $this->input->post('JmlSetoran');
    $PekanRekap = $this->input->post('PekanRekap');
    $Hasil = $this->input->post('Hasil');
    $Prosentase = $this->input->post('Prosentase');
    $Reward = $this->input->post('Reward');
    $data = [];

    // Method ini cuma dimaksudkan sebagai target submit form Proses Setoran (data array dari
    // beberapa santri sekaligus) - kalau diakses langsung (mis. lewat URL tanpa mengisi form
    // dulu), field-field di atas semuanya NULL dan foreach(null) di bawah akan memicu warning
    // PHP yang bikin redirect() gagal ("headers already sent"), sehingga halamannya rusak/kosong
    // alih-alih menampilkan pesan yang jelas.
    if (!is_array($idSiswa) || !is_array($Reward) || !is_array($Prosentase) || !is_array($Hasil) || !is_array($JmlSetoran)) {
      redirect('halaqoh/rekap_setoran?pesan=' . rawurlencode('Data tidak lengkap, silakan proses lewat form Proses Setoran.'));
      return;
    }

    // Kelima array ini SEJAJAR (satu entri per santri, urutan sama - lihat
    // mobile-add_rekap_setoran.php: setiap santri di foreach ($data_setoran as $d_setoran) ikut
    // menulis SATU hidden input ke masing-masing array secara bersamaan). Dulu ditulis sebagai
    // 5 foreach bersarang (satu per array) - bukan cuma O(jumlah_santri^5) yang bikin timeout
    // 500 untuk kelas dengan santri lebih dari segelintir, tapi juga keliru memasangkan index
    // milik array yang beda-beda ($siswa, $setoran, $hasil, $pros, $reward) padahal seharusnya
    // satu index yang sama menunjuk ke satu santri yang sama.
    // rekapsetoran TIDAK punya kolom IdKelas sendiri (lihat catatan di Rekap_setoran_M::
    // getAllRekapSetoran()) - kelasnya didapat lewat join ke siswa.IdKelas, bukan disimpan
    // langsung di sini. Menyertakan 'IdKelas' di data insert (seperti sebelumnya) bikin
    // insert_batch() menimpa kolom yang tidak ada, dan MySQL menolaknya - itulah sebab
    // sebenarnya di balik error 500 pada "Proses Hasil", bukan cuma perulangan yang lambat.
    foreach ($idSiswa as $i => $s) {
      $data[] = [
        'IdSiswa' => $s,
        'JmlTugas' => $JmlTugas,
        'JmlSetoran' => $JmlSetoran[$i] ?? null,
        'PekanRekap' => $PekanRekap,
        'Hasil' => $Hasil[$i] ?? null,
        'Prosentase' => $Prosentase[$i] ?? null,
        'Reward' => $Reward[$i] ?? null,
      ];
    }
    $this->Rekap_setoran_M->addRekapSetoran($data);
    redirect('halaqoh/rekap_setoran?pesan=' . rawurlencode('Berhasil ditambahkan!'));
  }

  //Update one item
  public function update($id = NULL)
  {
  }

  //Delete one item
  public function delete($IdKelas)
  {
    $data = [
      'IdKelas' => $IdKelas
    ];
    $this->Rekap_setoran_M->deleteByKelas($data);
    redirect('halaqoh/Rekap_setoran?pesan=' . rawurlencode('Berhasil dihapus!'));
  }

  public function cari_data()
  {
    $nama_santri = $this->input->post('nama_santri');
    $pesan = $this->input->get('pesan') ?: $this->session->flashdata('pesan');
    $data = [
      'title'   => 'Rekap Setoran',
      'user'    => $this->db->get_where('login', ['username' => $this->session->userdata('username')])->row_array(),
      'rekap_setoran' => $this->Rekap_setoran_M->getRekapSetoranByNamaSantri($nama_santri),
      'santri' => $this->Santri_M->getAllSantri(),
      'pesan' => $pesan,
      'isi'     => tampilan_mobile() ? 'halaqoh/mobile-rekap_setoran' : 'halaqoh/v-rekap_setoran',
    ];
    // var_dump($data['detail_kelompok']);
    // die;

    $this->load->view(tampilan_mobile() ? 'templates/wrapper-mobile-simple' : 'templates/wrapper-admin', $data);
  }

  public function reset_data()
  {
    $this->Rekap_setoran_M->kosongkanRekapSetoran();
    redirect('halaqoh/rekap_setoran?pesan=' . rawurlencode('Berhasil direset!'));
  }

  public function export_excel()
  {
    $data = [
      'title'   => 'Rekap Setoran',
      'user'    => $this->db->get_where('login', ['username' => $this->session->userdata('username')])->row_array(),
      'rekap_setoran' => $this->Rekap_setoran_M->getAllRekapSetoran(),
      'santri' => $this->Santri_M->getAllSantri(),
    ];

    $this->load->view('export/excel/halaqoh/rekap_setoran', $data);
  }
}

/* End of file Rekap_setoran.php */
