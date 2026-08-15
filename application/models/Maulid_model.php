<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Maulid_model extends CI_Model
{
  public function __construct()
  {
    parent::__construct();
    $this->pastikanTabel();
  }

  private function pastikanTabel()
  {
    if ($this->db->table_exists('maulid_bookings')) {
      return;
    }

    $this->db->query("CREATE TABLE maulid_bookings (
      id INT UNSIGNED NOT NULL AUTO_INCREMENT,
      user_id INT(11) NOT NULL,
      booker_name VARCHAR(101) NOT NULL,
      hijri_year INT UNSIGNED NOT NULL,
      rabiul_awal_day TINYINT UNSIGNED NOT NULL,
      location_name VARCHAR(255) NOT NULL,
      latitude DECIMAL(10,8) NULL,
      longitude DECIMAL(11,8) NULL,
      maps_url TEXT NULL,
      notes TEXT NULL,
      status ENUM('booked','cancelled') NOT NULL DEFAULT 'booked',
      active_slot TINYINT UNSIGNED NULL DEFAULT 1,
      created_at DATETIME NULL,
      updated_at DATETIME NULL,
      PRIMARY KEY (id),
      UNIQUE KEY uq_maulid_active_date (hijri_year, rabiul_awal_day, active_slot),
      KEY idx_maulid_user (user_id),
      KEY idx_maulid_year_status (hijri_year, status),
      CONSTRAINT chk_maulid_day CHECK (rabiul_awal_day BETWEEN 1 AND 30),
      CONSTRAINT chk_maulid_lat CHECK (latitude IS NULL OR latitude BETWEEN -90 AND 90),
      CONSTRAINT chk_maulid_lng CHECK (longitude IS NULL OR longitude BETWEEN -180 AND 180)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
  }

  public function getByYear($year, $include_cancelled = false)
  {
    $this->db->where('hijri_year', (int) $year);
    if (!$include_cancelled) {
      $this->db->where('status', 'booked');
    }
    return $this->db->order_by('rabiul_awal_day', 'asc')->order_by('id', 'desc')->get('maulid_bookings')->result_array();
  }

  public function getById($id)
  {
    return $this->db->get_where('maulid_bookings', ['id' => (int) $id])->row_array();
  }

  public function createBooking($data)
  {
    $this->db->trans_begin();
    // Duplicate key adalah hasil bisnis yang wajar saat dua wali booking bersamaan; jangan biarkan
    // db_debug CI menampilkan halaman error sebelum controller bisa memberi pesan yang ramah.
    $db_debug = $this->db->db_debug;
    $this->db->db_debug = false;
    $ok = $this->db->insert('maulid_bookings', $data);
    $db_error = $this->db->error();
    $this->db->db_debug = $db_debug;

    if (!$ok || $this->db->trans_status() === false) {
      $this->db->trans_rollback();
      return ['ok' => false, 'duplicate' => isset($db_error['code']) && (int) $db_error['code'] === 1062];
    }

    $this->db->trans_commit();
    return ['ok' => true, 'duplicate' => false];
  }

  public function cancel($id, $user_id = null)
  {
    $this->db->where('id', (int) $id)->where('status', 'booked');
    if ($user_id !== null) {
      $this->db->where('user_id', (int) $user_id);
    }
    $this->db->update('maulid_bookings', [
      'status' => 'cancelled',
      'active_slot' => null,
      'updated_at' => date('Y-m-d H:i:s'),
    ]);
    return $this->db->affected_rows() === 1;
  }

  public function updateByAdmin($id, $data)
  {
    $this->db->where('id', (int) $id)->where('status', 'booked');
    return $this->db->update('maulid_bookings', $data);
  }
}
