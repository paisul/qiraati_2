<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Dana_maulid_M extends CI_Model
{
  public function __construct()
  {
    parent::__construct();
    $this->ensureTable();
  }

  public function getAll()
  {
    $this->db->order_by('Tanggal', 'desc')->order_by('IdDanaMaulid', 'desc');
    $rows = $this->db->get('dana_maulid')->result_array();
    foreach ($rows as &$row) {
      // View Dana yang dipakai bersama mengharapkan nama kunci ini.
      $row['IdDanaLain'] = $row['IdDanaMaulid'];
    }
    return $rows;
  }

  public function getRingkasan()
  {
    $this->db->select('COALESCE(SUM(JumlahMasuk), 0) AS total_masuk, COALESCE(SUM(JumlahKeluar), 0) AS total_keluar', FALSE);
    $ringkasan = $this->db->get('dana_maulid')->row_array();
    $ringkasan['saldo'] = $ringkasan['total_masuk'] - $ringkasan['total_keluar'];
    return $ringkasan;
  }

  public function add($data) { return $this->db->insert('dana_maulid', $data); }
  public function update($id, $data) { return $this->db->where('IdDanaMaulid', $id)->update('dana_maulid', $data); }
  public function delete($id) { return $this->db->where('IdDanaMaulid', $id)->delete('dana_maulid'); }

  private function ensureTable()
  {
    if ($this->db->table_exists('dana_maulid')) return;

    $this->load->dbforge();
    $this->dbforge->add_field([
      'IdDanaMaulid' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => TRUE, 'auto_increment' => TRUE],
      'Tanggal' => ['type' => 'DATE', 'null' => FALSE],
      'Perihal' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => FALSE],
      'JumlahMasuk' => ['type' => 'DECIMAL', 'constraint' => '15,2', 'default' => 0],
      'JumlahKeluar' => ['type' => 'DECIMAL', 'constraint' => '15,2', 'default' => 0],
      'CreatedAt' => ['type' => 'DATETIME', 'null' => TRUE],
      'UpdatedAt' => ['type' => 'DATETIME', 'null' => TRUE],
    ]);
    $this->dbforge->add_key('IdDanaMaulid', TRUE);
    $this->dbforge->create_table('dana_maulid', TRUE);
  }
}
