<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Maulid extends CI_Controller
{
  const BOOKING_GREGORIAN_YEAR = 2026;
  const BOOKING_HIJRI_YEAR = 1448;

  public function __construct()
  {
    parent::__construct();
    cek_level(['Admin', 'Wali', 'Musyrif']);
    $this->load->model('Maulid_model');
    $this->load->model('Wali_M');
    $this->load->model('Musyrif_M');
  }

  public function index()
  {
    $year = self::BOOKING_HIJRI_YEAR;
    $level = $this->session->userdata('level');
    $is_admin = $level === 'Admin';
    $is_musyrif = $level === 'Musyrif';
    $user = $this->getLoginUser();
    $wali = (!$is_admin && !$is_musyrif) ? $this->getWali() : null;
    $musyrif = $is_musyrif ? $this->Musyrif_M->getDataMusyrif($this->session->userdata('username')) : null;
    $rows = $this->Maulid_model->getByYear($year, $is_admin);
    $bookings = [];
    foreach ($rows as $row) {
      if ($row['status'] === 'booked') {
        $bookings[(int) $row['rabiul_awal_day']] = $row;
      }
    }
    $gregorian_dates = $this->rabiulAwalGregorianDates($year);
    $active_booking = $is_admin ? null : $this->Maulid_model->getActiveByUser((int) $user['IdUser'], $year);

    $data = [
      'title' => $is_admin ? 'Rekap Booking Maulid' : 'Booking Maulid',
      'user' => $is_admin ? $user : ($is_musyrif ? $musyrif : $wali),
      'year' => $year,
      'gregorian_year' => self::BOOKING_GREGORIAN_YEAR,
      'bookings' => $bookings,
      'gregorian_dates' => $gregorian_dates,
      'calendar_start_offset' => $gregorian_dates[1]['weekday'],
      'rows' => $rows,
      'is_admin' => $is_admin,
      'is_musyrif' => $is_musyrif,
      'active_booking' => $active_booking,
      'has_active_booking' => (bool) $active_booking,
      'current_user_id' => (int) $user['IdUser'],
      'parent_name' => $is_admin ? ($user['username'] ?? 'Admin') : ($is_musyrif ? $this->musyrifName($musyrif) : $this->parentName($wali)),
      'student_names' => ($is_admin || $is_musyrif) ? [] : array_column($wali['daftar_anak'], 'NamaLengkap'),
      'admin_students' => $is_admin ? $this->adminAvailableStudents($year) : [],
      'pesan' => $this->input->get('pesan') ?: $this->session->flashdata('pesan'),
      'isi' => tampilan_mobile() ? 'maulid/mobile-index' : 'maulid/index',
    ];
    $wrapper = $is_admin
      ? (tampilan_mobile() ? 'templates/wrapper-mobile-simple' : 'templates/wrapper-admin')
      : ($is_musyrif
        ? (tampilan_mobile() ? 'templates/wrapper-musyrif-mobile' : 'templates/wrapper-musyrif')
        : (tampilan_mobile() ? 'templates/wrapper-wali-mobile' : 'templates/wrapper-wali'));
    $this->load->view($wrapper, $data);
  }

  public function create()
  {
    $this->requirePost();
    $level = $this->session->userdata('level');
    if (!in_array($level, ['Admin', 'Wali', 'Musyrif'], true)) {
      show_error('Akun ini tidak dapat membuat booking.', 403);
    }

    $wali = $level === 'Wali' ? $this->getWali() : null;
    $musyrif = $level === 'Musyrif' ? $this->Musyrif_M->getDataMusyrif($this->session->userdata('username')) : null;
    $user = $this->getLoginUser();
    $year = (int) $this->input->post('hijri_year');
    $day = filter_var($this->input->post('rabiul_awal_day'), FILTER_VALIDATE_INT, ['options' => ['min_range' => 1, 'max_range' => 30]]);
    $lat = $this->coordinate($this->input->post('latitude'), -90, 90);
    $lng = $this->coordinate($this->input->post('longitude'), -180, 180);
    $maps_url = trim((string) $this->input->post('maps_url', true));
    $admin_student = null;
    if ($level === 'Admin') {
      $admin_student_id = filter_var($this->input->post('admin_student_id'), FILTER_VALIDATE_INT);
      $admin_student = $admin_student_id ? $this->adminStudentForBooking($admin_student_id, $year) : null;
      if (!$admin_student) {
        $this->back($year, 'Nama Santri tidak tersedia atau akun Walinya sudah memiliki booking.');
      }
    }

    if ($year !== self::BOOKING_HIJRI_YEAR || $day === false || $this->input->post('location_confirmed') !== '1' || $lat === null || $lng === null || $maps_url === '') {
      $this->back($year, 'Tanggal dan lokasi Google Maps wajib diisi dengan benar.');
    }
    if ($maps_url !== '' && !filter_var($maps_url, FILTER_VALIDATE_URL)) {
      $this->back($year, 'Link Google Maps tidak valid.');
    }
    $selected_date = $this->rabiulAwalGregorianDates($year)[$day];
    if (in_array((int) $selected_date['weekday'], [4, 6], true)) {
      $this->back($year, 'Hari Kamis dan Sabtu dikunci dan tidak dapat dibooking.');
    }

    $replace_existing = $this->input->post('replace_existing') === '1';
    $result = $this->Maulid_model->createBooking([
      'user_id' => $level === 'Admin' ? (int) $admin_student['IdUser'] : (int) $user['IdUser'],
      'booker_name' => $level === 'Admin' ? $this->parentName($admin_student) : ($level === 'Musyrif' ? $this->musyrifName($musyrif) : $this->parentName($wali)),
      'calendar_name' => $level === 'Admin' ? $admin_student['NamaLengkap'] : ($level === 'Musyrif' ? $this->musyrifName($musyrif) : implode(' / ', array_column($wali['daftar_anak'], 'NamaLengkap'))),
      'hijri_year' => $year,
      'rabiul_awal_day' => $day,
      'location_name' => 'Lokasi Google Maps',
      'latitude' => $lat,
      'longitude' => $lng,
      'maps_url' => $maps_url === '' ? null : $maps_url,
      'notes' => null,
      'status' => 'booked',
      'active_slot' => 1,
      'created_at' => date('Y-m-d H:i:s'),
      'updated_at' => date('Y-m-d H:i:s'),
    ], $replace_existing, false);

    $message = $result['ok']
      ? ($replace_existing ? 'Hari booking Maulid berhasil diganti.' : 'Booking Maulid berhasil disimpan.')
      : ($result['already_booked'] ? 'Satu akun hanya dapat memiliki satu booking aktif.' : ($result['duplicate'] ? "Tanggal {$day} Rabiul Awal sudah dibooking oleh user lain. Silakan pilih tanggal yang masih tersedia." : 'Booking gagal disimpan. Silakan coba lagi.'));
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

  private function musyrifName($musyrif)
  {
    $name = trim((string) ($musyrif['NamaMusyrif'] ?? $musyrif['username'] ?? ''));
    if ($name === '') show_error('Nama Musyrif belum tersedia.', 422);
    return $name;
  }

  private function adminAvailableStudents($year)
  {
    return $this->db->query(
      'SELECT s.IdSiswa, s.NamaLengkap FROM siswa s '
      . 'INNER JOIN wali_siswa ws ON ws.IdSiswa = s.IdSiswa '
      . 'INNER JOIN login l ON l.IdUser = ws.IdUser AND l.level = ? '
      . 'LEFT JOIN maulid_bookings mb ON mb.user_id = l.IdUser AND mb.hijri_year = ? AND mb.status = ? '
      . 'WHERE mb.id IS NULL GROUP BY s.IdSiswa, s.NamaLengkap ORDER BY s.NamaLengkap ASC',
      ['Wali', (int) $year, 'booked']
    )->result_array();
  }

  private function adminStudentForBooking($student_id, $year)
  {
    return $this->db->query(
      'SELECT s.IdSiswa, s.NamaLengkap, s.NamaAyah, s.NamaIbu, l.IdUser FROM siswa s '
      . 'INNER JOIN wali_siswa ws ON ws.IdSiswa = s.IdSiswa '
      . 'INNER JOIN login l ON l.IdUser = ws.IdUser AND l.level = ? '
      . 'LEFT JOIN maulid_bookings mb ON mb.user_id = l.IdUser AND mb.hijri_year = ? AND mb.status = ? '
      . 'WHERE s.IdSiswa = ? AND mb.id IS NULL ORDER BY l.IdUser ASC LIMIT 1',
      ['Wali', (int) $year, 'booked', (int) $student_id]
    )->row_array();
  }

  private function coordinate($value, $min, $max)
  {
    if ($value === null || trim((string) $value) === '') return null;
    if (!is_numeric($value)) return null;
    $number = (float) $value;
    return ($number >= $min && $number <= $max) ? $number : null;
  }

  private function defaultHijriYear()
  {
    return self::BOOKING_HIJRI_YEAR;
  }

  /**
   * Konversi kalender Hijriah sipil (tabular) ke Gregorian tanpa API/ekstensi intl.
   * Hasil kalender rukyat resmi setempat dapat berbeda satu hari.
   */
  private function rabiulAwalGregorianDates($hijri_year)
  {
    $bulan_masehi = [1 => 'Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
    $dates = [];

    for ($day = 1; $day <= 30; $day++) {
      // Rabiul Awal adalah bulan ke-3. Epoch Hijriah sipil: JDN 1948440.
      $jdn = $day
        + (int) ceil(29.5 * (3 - 1))
        + ((int) $hijri_year - 1) * 354
        + (int) floor((3 + 11 * (int) $hijri_year) / 30)
        + 1948439;
      $gregorian = $this->gregorianFromJdn($jdn);
      $timestamp = mktime(12, 0, 0, $gregorian['month'], $gregorian['day'], $gregorian['year']);

      $dates[$day] = [
        'day' => $gregorian['day'],
        'month' => $gregorian['month'],
        'year' => $gregorian['year'],
        'label' => $gregorian['day'] . ' ' . $bulan_masehi[$gregorian['month']] . ' ' . $gregorian['year'],
        // date('w'): 0=Ahad sampai 6=Sabtu, sama dengan urutan header kalender.
        'weekday' => (int) date('w', $timestamp),
      ];
    }

    return $dates;
  }

  private function gregorianFromJdn($jdn)
  {
    $l = (int) $jdn + 68569;
    $n = intdiv(4 * $l, 146097);
    $l = $l - intdiv(146097 * $n + 3, 4);
    $i = intdiv(4000 * ($l + 1), 1461001);
    $l = $l - intdiv(1461 * $i, 4) + 31;
    $j = intdiv(80 * $l, 2447);
    $day = $l - intdiv(2447 * $j, 80);
    $l = intdiv($j, 11);
    $month = $j + 2 - 12 * $l;
    $year = 100 * ($n - 49) + $i + $l;

    return ['year' => $year, 'month' => $month, 'day' => $day];
  }

  private function requirePost()
  {
    if (strtoupper($this->input->method()) !== 'POST') show_error('Metode tidak diizinkan.', 405);
  }

  private function back($year, $message)
  {
    redirect('maulid?pesan=' . rawurlencode($message));
    exit;
  }
}
