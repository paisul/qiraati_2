<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Absen extends CI_Controller
{
  private $google_sheet_url = 'https://docs.google.com/spreadsheets/d/e/2PACX-1vQBV6UytjcqsF1xqxh43PdbtP0ZlEYvXj800GHrUsAGdd5J8WhMAa61FQgBDpc4pCoawd8_Toc-l3_6/pubhtml?gid=0&single=true';

  public function __construct()
  {
    parent::__construct();
    cek_level(['Admin', 'Musyrif', 'Guru']);
    $this->load->model('Absen_M');
    $this->load->model('Musyrif_M');
  }

  public function index($jenis = 'santri')
  {
    $jenis = $jenis == 'pembimbing' ? 'pembimbing' : 'santri';
    $tanggal = $this->input->get('tanggal') ? $this->input->get('tanggal') : date('Y-m-d');
    $peserta = $jenis == 'pembimbing' ? $this->Absen_M->getPembimbing() : $this->Absen_M->getSantri($this->idMusyrifSaya());
    $absen = $this->Absen_M->getAbsenByFilter($jenis, $tanggal);
    $status_awal = $this->hitungStatusAwal($peserta, $absen, $jenis, $tanggal);
    $ringkasan = $this->hitungRingkasan($status_awal);
    // Query string ?pesan= dipakai supaya pesan berhasil/gagal tidak nyangkut lewat session flashdata.
    $pesan = $this->input->get('pesan') ?: $this->session->flashdata('pesan');

    $data = [
      'title' => 'Absen',
      'user' => $this->getUserLogin(),
      'jenis' => $jenis,
      'tanggal' => $tanggal,
      'peserta' => $peserta,
      'absen' => $absen,
      'status_awal' => $status_awal,
      'ringkasan' => $ringkasan,
      'siap_kirim' => count($peserta) > 0 && count($status_awal) >= count($peserta),
      'pesan' => $pesan,
      'isi' => tampilan_mobile() ? 'absen/mobile-index' : 'absen/index',
    ];

    $this->load->view($this->getWrapper(), $data);
  }

  public function simpan()
  {
    $jenis = $this->input->post('jenis') == 'pembimbing' ? 'pembimbing' : 'santri';
    $tanggal = $this->input->post('tanggal') ? $this->input->post('tanggal') : date('Y-m-d');
    $id_peserta = $this->input->post('id_peserta');
    $status = $this->input->post('status');
    $status_diizinkan = ['Hadir', 'Sakit', 'Izin', 'Alpa'];

    if ($id_peserta && in_array($status, $status_diizinkan)) {
      $this->Absen_M->simpanAbsen($jenis, $id_peserta, $tanggal, $status);
      $pesan = 'Absen berhasil disimpan!';
    } else {
      $pesan = 'Absen gagal disimpan!';
    }

    redirect('absen/' . $jenis . '?tanggal=' . $tanggal . '&pesan=' . rawurlencode($pesan));
  }

  /**
   * Endpoint AJAX: menyimpan pilihan status satu peserta secara sementara ke session.
   * Dipanggil setiap kali admin memilih status di tabel, tanpa reload halaman.
   * Belum menyentuh database - baru benar-benar disimpan saat kirim_absensi().
   */
  public function simpan_sementara()
  {
    header('Content-Type: application/json; charset=utf-8');

    $raw = file_get_contents('php://input');
    $payload = json_decode($raw, TRUE);

    if (!is_array($payload)) {
      $this->_json(['status' => false, 'message' => 'Format data tidak valid.']);
      return;
    }

    $jenis = ($payload['jenis'] ?? '') === 'pembimbing' ? 'pembimbing' : 'santri';
    $tanggal = isset($payload['tanggal']) ? $payload['tanggal'] : date('Y-m-d');
    $id_peserta = isset($payload['id_peserta']) ? (int) $payload['id_peserta'] : 0;
    $status = isset($payload['status']) ? $payload['status'] : '';
    $status_diizinkan = ['Hadir', 'Sakit', 'Izin', 'Alpa'];

    $peserta = $jenis == 'pembimbing' ? $this->Absen_M->getPembimbing() : $this->Absen_M->getSantri($this->idMusyrifSaya());
    $id_valid = array_map('intval', array_column($peserta, 'id'));

    if (!$id_peserta || !in_array($id_peserta, $id_valid, TRUE) || !in_array($status, $status_diizinkan, TRUE)) {
      $this->_json(['status' => false, 'message' => 'Data tidak valid.']);
      return;
    }

    $kunci = $this->kunciSementara($jenis, $tanggal);
    $sementara = $this->session->userdata('absen_sementara');
    $sementara = is_array($sementara) ? $sementara : [];
    $sementara[$kunci][$id_peserta] = $status;
    $this->session->set_userdata('absen_sementara', $sementara);

    $this->_json([
      'status'        => true,
      'message'       => 'Status tersimpan sementara.',
      'jumlah_terisi' => count($sementara[$kunci]),
      'total'         => count($peserta),
    ]);
  }

  /**
   * Endpoint AJAX: menyimpan absen ke database tanpa reload.
   * Menerima JSON { jenis, tanggal, data: { id_peserta: status, ... } }
   * Melakukan validasi ulang bahwa SEMUA peserta sudah diisi sebelum disimpan.
   */
  public function kirim_absensi()
  {
    header('Content-Type: application/json; charset=utf-8');

    $raw = file_get_contents('php://input');
    $payload = json_decode($raw, TRUE);

    if (!is_array($payload)) {
      $this->_json(['status' => false, 'message' => 'Format data tidak valid.']);
      return;
    }

    $jenis = ($payload['jenis'] ?? '') === 'pembimbing' ? 'pembimbing' : 'santri';
    $tanggal = isset($payload['tanggal']) ? $payload['tanggal'] : date('Y-m-d');
    $data = isset($payload['data']) && is_array($payload['data']) ? $payload['data'] : [];
    $status_diizinkan = ['Hadir', 'Sakit', 'Izin', 'Alpa'];

    $peserta = $jenis == 'pembimbing' ? $this->Absen_M->getPembimbing() : $this->Absen_M->getSantri($this->idMusyrifSaya());
    $total = count($peserta);

    if ($total === 0) {
      $this->_json(['status' => false, 'message' => 'Tidak ada peserta yang dapat diabsen.']);
      return;
    }

    // Validasi ulang: setiap peserta WAJIB punya status yang valid.
    $id_valid = array_column($peserta, 'id');
    $id_valid = array_map('strval', $id_valid);

    $bersih = [];
    foreach ($data as $id => $status) {
      if (!in_array((string) $id, $id_valid, TRUE)) {
        continue;
      }
      if (!in_array($status, $status_diizinkan, TRUE)) {
        continue;
      }
      $bersih[(int) $id] = $status;
    }

    if (count($bersih) < $total) {
      $this->_json([
        'status'  => false,
        'message' => 'Masih ada ' . ($total - count($bersih)) . '/' . $total . ' peserta yang belum diabsen.',
      ]);
      return;
    }

    // Simpan batch ke database.
    foreach ($bersih as $id_peserta => $status) {
      $this->Absen_M->simpanAbsen($jenis, $id_peserta, $tanggal, $status);
    }

    // Draf sementara di session sudah final tersimpan di DB, hapus agar tidak dobel.
    $kunci = $this->kunciSementara($jenis, $tanggal);
    $sementara = $this->session->userdata('absen_sementara');
    if (is_array($sementara) && isset($sementara[$kunci])) {
      unset($sementara[$kunci]);
      $this->session->set_userdata('absen_sementara', $sementara);
    }

    // Dihitung dari $bersih (sudah dibatasi ke roster saat ini), bukan query terpisah yang tidak
    // ikut memfilter per-pembimbing - supaya kotak ringkasan tidak ikut menghitung kelas lain.
    $status_list = ['Hadir', 'Sakit', 'Izin', 'Alpa'];
    $ringkasan = array_fill_keys($status_list, 0);
    foreach ($bersih as $status) {
      if (isset($ringkasan[$status])) {
        $ringkasan[$status]++;
      }
    }

    $this->_json([
      'status'   => true,
      'message'  => 'Absensi berhasil disimpan!',
      'ringkasan' => $ringkasan,
    ]);
  }

  /**
   * Endpoint AJAX: kembalikan satu peserta ke status "Belum Absen".
   * Berlaku juga untuk absen yang sudah dikirim/tersimpan permanen di hari sebelumnya, bukan
   * cuma draf hari ini - "Belum Absen" berarti tidak ada baris sama sekali di tabel absen,
   * jadi di sini baris DB-nya (kalau ada) ikut dihapus, bukan sekadar diubah nilainya.
   */
  public function reset_status()
  {
    header('Content-Type: application/json; charset=utf-8');

    $raw = file_get_contents('php://input');
    $payload = json_decode($raw, TRUE);

    if (!is_array($payload)) {
      $this->_json(['status' => false, 'message' => 'Format data tidak valid.']);
      return;
    }

    $jenis = ($payload['jenis'] ?? '') === 'pembimbing' ? 'pembimbing' : 'santri';
    $tanggal = isset($payload['tanggal']) ? $payload['tanggal'] : date('Y-m-d');
    $id_peserta = isset($payload['id_peserta']) ? (int) $payload['id_peserta'] : 0;

    $peserta = $jenis == 'pembimbing' ? $this->Absen_M->getPembimbing() : $this->Absen_M->getSantri($this->idMusyrifSaya());
    $id_valid = array_map('intval', array_column($peserta, 'id'));

    if (!$id_peserta || !in_array($id_peserta, $id_valid, TRUE)) {
      $this->_json(['status' => false, 'message' => 'Peserta tidak valid.']);
      return;
    }

    $this->Absen_M->hapusAbsen($jenis, $id_peserta, $tanggal);

    $kunci = $this->kunciSementara($jenis, $tanggal);
    $sementara = $this->session->userdata('absen_sementara');
    if (is_array($sementara) && isset($sementara[$kunci][$id_peserta])) {
      unset($sementara[$kunci][$id_peserta]);
      $this->session->set_userdata('absen_sementara', $sementara);
    }

    $absen = $this->Absen_M->getAbsenByFilter($jenis, $tanggal);
    $status_awal = $this->hitungStatusAwal($peserta, $absen, $jenis, $tanggal);
    $ringkasan = $this->hitungRingkasan($status_awal);

    $this->_json([
      'status'    => true,
      'message'   => 'Status dikembalikan ke Belum Absen.',
      'ringkasan' => $ringkasan,
    ]);
  }

  // Gabungkan status yang sudah tersimpan permanen di DB dengan draf sementara di session,
  // dibatasi ke peserta yang benar-benar tampil di roster saat ini (mis. Musyrif yang login
  // hanya melihat santri kelasnya sendiri - jangan ikut terhitung punya kelas lain).
  private function hitungStatusAwal($peserta, $absen, $jenis, $tanggal)
  {
    $id_peserta_valid = array_map('intval', array_column($peserta, 'id'));

    $status_awal = [];
    foreach ($absen as $id_peserta => $row) {
      if (in_array((int) $id_peserta, $id_peserta_valid, TRUE)) {
        $status_awal[$id_peserta] = $row['Status'];
      }
    }
    foreach ($this->getSementara($jenis, $tanggal) as $id_peserta => $status) {
      if (!in_array((int) $id_peserta, $id_peserta_valid, TRUE)) {
        continue;
      }
      // Draf sementara di session Musyrif ini bisa jadi lebih lama dari ajuan Izin/Sakit Wali yang
      // baru masuk ke database (Wali & Musyrif punya session masing-masing, tidak saling tahu) -
      // begitu ajuan Wali masuk (DiajukanOlehWali=1), itu WAJIB menang, jangan sampai tertimpa
      // draf lama yang belum sempat di-"Kirim Absensi"-kan.
      if (!empty($absen[$id_peserta]['DiajukanOlehWali'])) {
        continue;
      }
      $status_awal[$id_peserta] = $status;
    }

    return $status_awal;
  }

  private function hitungRingkasan($status_awal)
  {
    $status_list = ['Hadir', 'Sakit', 'Izin', 'Alpa'];
    $ringkasan = array_fill_keys($status_list, 0);
    foreach ($status_awal as $status) {
      if (isset($ringkasan[$status])) {
        $ringkasan[$status]++;
      }
    }

    return $ringkasan;
  }

  private function _json($array)
  {
    echo json_encode($array);
    return;
  }

  private function kunciSementara($jenis, $tanggal)
  {
    return $jenis . '_' . $tanggal;
  }

  private function getSementara($jenis, $tanggal)
  {
    $sementara = $this->session->userdata('absen_sementara');
    $kunci = $this->kunciSementara($jenis, $tanggal);

    if (is_array($sementara) && isset($sementara[$kunci]) && is_array($sementara[$kunci])) {
      return $sementara[$kunci];
    }

    return [];
  }

  public function kirim()
  {
    $jenis = $this->input->post('jenis') == 'pembimbing' ? 'pembimbing' : 'santri';
    $tanggal = $this->input->post('tanggal') ? $this->input->post('tanggal') : date('Y-m-d');
    $rows = $this->Absen_M->getDataKirim($jenis, $tanggal, $this->idMusyrifSaya());

    if (!$rows) {
      redirect('absen/' . $jenis . '?tanggal=' . $tanggal . '&pesan=' . rawurlencode('Data absen belum lengkap untuk dikirim!'));
    }

    if (strpos($this->google_sheet_url, '/pubhtml') !== FALSE) {
      redirect('absen/' . $jenis . '?tanggal=' . $tanggal . '&pesan=' . rawurlencode('Link Google Sheet masih berupa halaman publik/read-only. Gunakan URL Google Apps Script Web App agar data absen bisa terkirim ke Sheet.'));
    }

    $payload = json_encode([
      'jenis' => $jenis,
      'tanggal' => $tanggal,
      'rows' => $rows,
    ]);

    $berhasil = FALSE;
    if (function_exists('curl_init')) {
      $ch = curl_init($this->google_sheet_url);
      curl_setopt($ch, CURLOPT_POST, TRUE);
      curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
      curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
      curl_setopt($ch, CURLOPT_TIMEOUT, 15);
      curl_exec($ch);
      $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
      curl_close($ch);
      $berhasil = $http_code >= 200 && $http_code < 300;
    }

    $pesan = $berhasil ? 'Data absen berhasil dikirim!' : 'Data absen gagal dikirim. Periksa URL tujuan Google Sheet.';
    redirect('absen/' . $jenis . '?tanggal=' . $tanggal . '&pesan=' . rawurlencode($pesan));
  }

  // IdMusyrif milik pembimbing yang sedang login, atau null kalau Admin/Guru/tidak tertaut ke profil manapun
  // (dalam kasus itu roster santri tidak difilter - tetap tampil semua, seperti sebelumnya).
  private function idMusyrifSaya()
  {
    if ($this->session->userdata('level') !== 'Musyrif') {
      return null;
    }

    $user = $this->getUserLogin();
    return !empty($user['IdMusyrif']) ? (int) $user['IdMusyrif'] : null;
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

    return $this->db->get_where('login', ['username' => $username])->row_array();
  }

  private function getWrapper()
  {
    // Absen dipakai buat mengisi kehadiran cepat sambil pegang HP - di mobile, pakai wrapper
    // ringan tanpa sidebar admin, bukan AdminLTE yang dikecilkan (lihat isi() juga).
    if (tampilan_mobile()) {
      return 'templates/wrapper-mobile-simple';
    }

    if ($this->session->userdata('level') == 'Musyrif' || $this->session->userdata('level') == 'Guru') {
      return 'templates/wrapper-musyrif';
    }

    return 'templates/wrapper-admin';
  }
}

/* End of file Absen.php */
