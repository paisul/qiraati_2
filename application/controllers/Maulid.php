<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Maulid extends CI_Controller
{
  public function __construct()
  {
    parent::__construct();
    cek_level(['Admin', 'Wali']);
    $this->load->model('Maulid_model');
    $this->load->model('Wali_M');
  }

  public function index()
  {
    $year = $this->validYear($this->input->get('tahun')) ?: $this->defaultHijriYear();
    $is_admin = $this->session->userdata('level') === 'Admin';
    $user = $this->getLoginUser();
    $rows = $this->Maulid_model->getByYear($year, $is_admin);
    $bookings = [];
    foreach ($rows as $row) {
      if ($row['status'] === 'booked') {
        $bookings[(int) $row['rabiul_awal_day']] = $row;
      }
    }

    $data = [
      'title' => $is_admin ? 'Rekap Booking Maulid' : 'Booking Maulid',
      'user' => $is_admin ? $user : $this->getWali(),
      'year' => $year,
      'bookings' => $bookings,
      'rows' => $rows,
      'is_admin' => $is_admin,
      'current_user_id' => (int) $user['IdUser'],
      'parent_name' => $is_admin ? '' : $this->parentName($this->getWali()),
      'pesan' => $this->input->get('pesan') ?: $this->session->flashdata('pesan'),
      'isi' => tampilan_mobile() ? 'maulid/mobile-index' : 'maulid/index',
    ];
    $wrapper = $is_admin
      ? (tampilan_mobile() ? 'templates/wrapper-mobile-simple' : 'templates/wrapper-admin')
      : (tampilan_mobile() ? 'templates/wrapper-wali-mobile' : 'templates/wrapper-wali');
    $this->load->view($wrapper, $data);
  }

  public function create()
  {
    $this->requirePost();
    if ($this->session->userdata('level') !== 'Wali') {
      show_error('Hanya akun Wali yang dapat membuat booking.', 403);
    }

    $wali = $this->getWali();
    $user = $this->getLoginUser();
    $year = $this->validYear($this->input->post('hijri_year'));
    $day = filter_var($this->input->post('rabiul_awal_day'), FILTER_VALIDATE_INT, ['options' => ['min_range' => 1, 'max_range' => 30]]);
    $location = trim((string) $this->input->post('location_name', true));
    $lat = $this->coordinate($this->input->post('latitude'), -90, 90);
    $lng = $this->coordinate($this->input->post('longitude'), -180, 180);
    $maps_url = trim((string) $this->input->post('maps_url', true));
    $notes = trim((string) $this->input->post('notes', true));

    if (!$year || $day === false || $location === '' || (($lat === null || $lng === null) && $maps_url === '')) {
      $this->back($year, 'Tanggal, nama lokasi, dan titik GPS atau link Google Maps wajib diisi dengan benar.');
    }
    if ($maps_url !== '' && !filter_var($maps_url, FILTER_VALIDATE_URL)) {
      $this->back($year, 'Link Google Maps tidak valid.');
    }

    $result = $this->Maulid_model->createBooking([
      'user_id' => (int) $user['IdUser'],
      'booker_name' => $this->parentName($wali),
      'hijri_year' => $year,
      'rabiul_awal_day' => $day,
      'location_name' => $location,
      'latitude' => $lat,
      'longitude' => $lng,
      'maps_url' => $maps_url === '' ? null : $maps_url,
      'notes' => $notes === '' ? null : $notes,
      'status' => 'booked',
      'active_slot' => 1,
      'created_at' => date('Y-m-d H:i:s'),
      'updated_at' => date('Y-m-d H:i:s'),
    ]);

    $message = $result['ok']
      ? 'Booking Maulid berhasil disimpan.'
      : ($result['duplicate'] ? "Tanggal {$day} Rabiul Awal sudah dibooking oleh user lain. Silakan pilih tanggal yang masih tersedia." : 'Booking gagal disimpan. Silakan coba lagi.');
    $this->back($year, $message);
  }

  public function cancel($id)
  {
    $this->requirePost();
    $user = $this->getLoginUser();
    $is_admin = $this->session->userdata('level') === 'Admin';
    $booking = $this->Maulid_model->getById($id);
    $year = $booking ? (int) $booking['hijri_year'] : $this->defaultHijriYear();
    $ok = $this->Maulid_model->cancel($id, $is_admin ? null : (int) $user['IdUser']);
    $this->back($year, $ok ? 'Booking berhasil dibatalkan.' : 'Booking tidak ditemukan atau bukan milik Anda.');
  }

  public function update($id)
  {
    $this->requirePost();
    if ($this->session->userdata('level') !== 'Admin') {
      show_error('Akses ditolak.', 403);
    }
    $booking = $this->Maulid_model->getById($id);
    if (!$booking) {
      $this->back($this->defaultHijriYear(), 'Booking tidak ditemukan.');
    }
    $location = trim((string) $this->input->post('location_name', true));
    $lat = $this->coordinate($this->input->post('latitude'), -90, 90);
    $lng = $this->coordinate($this->input->post('longitude'), -180, 180);
    $maps_url = trim((string) $this->input->post('maps_url', true));
    if ($location === '' || (($lat === null || $lng === null) && $maps_url === '') || ($maps_url !== '' && !filter_var($maps_url, FILTER_VALIDATE_URL))) {
      $this->back($booking['hijri_year'], 'Data lokasi belum lengkap atau tidak valid.');
    }
    $this->Maulid_model->updateByAdmin($id, [
      'location_name' => $location, 'latitude' => $lat, 'longitude' => $lng,
      'maps_url' => $maps_url === '' ? null : $maps_url,
      'notes' => trim((string) $this->input->post('notes', true)) ?: null,
      'updated_at' => date('Y-m-d H:i:s'),
    ]);
    $this->back($booking['hijri_year'], 'Booking berhasil diperbarui.');
  }

  private function getLoginUser()
  {
    $user = $this->db->get_where('login', ['username' => $this->session->userdata('username')])->row_array();
    if (!$user) {
      redirect('auth/logout');
    }
    return $user;
  }

  private function getWali()
  {
    $wali = $this->Wali_M->getDataWali($this->session->userdata('username'));
    if (!$wali) {
      show_error('Akun wali belum terhubung dengan data santri.', 422);
    }
    // getDataWali() sengaja hanya membawa kolom ringkas anak untuk kompatibilitas halaman lama.
    // Ambil kolom profil orang tua langsung dari santri aktif yang sudah terverifikasi milik akun ini.
    $profil = $this->db->get_where('siswa', ['IdSiswa' => (int) $wali['IdSiswa']])->row_array();
    return array_merge($wali, $profil ?: []);
  }

  private function parentName($wali)
  {
    $names = array_values(array_filter([trim((string) ($wali['NamaAyah'] ?? '')), trim((string) ($wali['NamaIbu'] ?? ''))]));
    if (!$names) {
      show_error('Nama Ayah/Ibu belum diisi pada profil santri. Lengkapi profil terlebih dahulu.', 422);
    }
    return implode(' / ', $names);
  }

  private function coordinate($value, $min, $max)
  {
    if ($value === null || trim((string) $value) === '') return null;
    if (!is_numeric($value)) return null;
    $number = (float) $value;
    return ($number >= $min && $number <= $max) ? $number : null;
  }

  private function validYear($value)
  {
    $year = filter_var($value, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1300, 'max_range' => 1700]]);
    return $year === false ? null : (int) $year;
  }

  private function defaultHijriYear()
  {
    return (int) floor((date('Y') - 622) * 33 / 32);
  }

  private function requirePost()
  {
    if (strtoupper($this->input->method()) !== 'POST') show_error('Metode tidak diizinkan.', 405);
  }

  private function back($year, $message)
  {
    redirect('maulid?tahun=' . (int) $year . '&pesan=' . rawurlencode($message));
    exit;
  }
}
