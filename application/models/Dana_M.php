<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Dana_M extends CI_Model
{
  public function __construct()
  {
    parent::__construct();
    $this->ensureTable();
  }

  public function getAllDana()
  {
    $this->db->order_by('Tanggal', 'desc');
    $this->db->order_by('IdDana', 'desc');
    return $this->db->get('dana')->result_array();
  }

  public function getRingkasan()
  {
    $this->db->select('COALESCE(SUM(JumlahMasuk), 0) AS total_masuk, COALESCE(SUM(JumlahKeluar), 0) AS total_keluar', FALSE);
    $ringkasan = $this->db->get('dana')->row_array();
    $ringkasan['saldo'] = $ringkasan['total_masuk'] - $ringkasan['total_keluar'];
    return $ringkasan;
  }

  public function addDana($data)
  {
    $this->db->insert('dana', $data);
  }

  public function updateDana($id, $data)
  {
    $this->db->where('IdDana', $id);
    $this->db->update('dana', $data);
  }

  public function deleteDana($id)
  {
    $this->db->where('IdDana', $id);
    $this->db->delete('dana');
  }

  private function ensureTable()
  {
    if ($this->db->table_exists('dana')) {
      return;
    }

    $this->load->dbforge();
    $this->dbforge->add_field([
      'IdDana' => [
        'type' => 'INT',
        'constraint' => 11,
        'unsigned' => TRUE,
        'auto_increment' => TRUE,
      ],
      'Tanggal' => [
        'type' => 'DATE',
        'null' => FALSE,
      ],
      'Perihal' => [
        'type' => 'VARCHAR',
        'constraint' => 255,
        'null' => FALSE,
      ],
      'JumlahMasuk' => [
        'type' => 'DECIMAL',
        'constraint' => '15,2',
        'default' => 0,
      ],
      'JumlahKeluar' => [
        'type' => 'DECIMAL',
        'constraint' => '15,2',
        'default' => 0,
      ],
      'CreatedAt' => [
        'type' => 'DATETIME',
        'null' => TRUE,
      ],
      'UpdatedAt' => [
        'type' => 'DATETIME',
        'null' => TRUE,
      ],
    ]);
    $this->dbforge->add_key('IdDana', TRUE);
    $this->dbforge->create_table('dana', TRUE);
  }
}

/* End of file Dana_M.php */
