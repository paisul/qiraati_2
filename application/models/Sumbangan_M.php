<?php
defined('BASEPATH') or exit('No direct script access allowed');

// Kloning Dana_M - struktur tabel & method sama persis, cuma tabelnya sendiri ('sumbangan')
// supaya datanya terpisah dari Data Dana utama. Lihat Dana_M untuk penjelasan pola ensureTable().
class Sumbangan_M extends CI_Model
{
  public function __construct()
  {
    parent::__construct();
    $this->ensureTable();
  }

  public function getAllSumbangan()
  {
    $this->db->order_by('Tanggal', 'desc');
    $this->db->order_by('IdSumbangan', 'desc');
    return $this->db->get('sumbangan')->result_array();
  }

  public function getRingkasan()
  {
    $this->db->select('COALESCE(SUM(JumlahMasuk), 0) AS total_masuk, COALESCE(SUM(JumlahKeluar), 0) AS total_keluar', FALSE);
    $ringkasan = $this->db->get('sumbangan')->row_array();
    $ringkasan['saldo'] = $ringkasan['total_masuk'] - $ringkasan['total_keluar'];
    return $ringkasan;
  }

  public function addSumbangan($data)
  {
    $this->db->insert('sumbangan', $data);
  }

  public function updateSumbangan($id, $data)
  {
    $this->db->where('IdSumbangan', $id);
    $this->db->update('sumbangan', $data);
  }

  public function deleteSumbangan($id)
  {
    $this->db->where('IdSumbangan', $id);
    $this->db->delete('sumbangan');
  }

  private function ensureTable()
  {
    if ($this->db->table_exists('sumbangan')) {
      return;
    }

    $this->load->dbforge();
    $this->dbforge->add_field([
      'IdSumbangan' => [
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
    $this->dbforge->add_key('IdSumbangan', TRUE);
    $this->dbforge->create_table('sumbangan', TRUE);
  }
}

/* End of file Sumbangan_M.php */
