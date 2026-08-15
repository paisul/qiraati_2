<?php
defined('BASEPATH') or exit('No direct script access allowed');

// Kloning Dana_M - struktur tabel & method sama persis, cuma tabelnya sendiri ('dana_lain')
// supaya datanya terpisah dari Data Dana utama. Lihat Dana_M untuk penjelasan pola ensureTable().
class Dana_lain_M extends CI_Model
{
  public function __construct()
  {
    parent::__construct();
    $this->ensureTable();
  }

  public function getAllDanaLain()
  {
    $this->db->order_by('Tanggal', 'desc');
    $this->db->order_by('IdDanaLain', 'desc');
    return $this->db->get('dana_lain')->result_array();
  }

  public function getRingkasan()
  {
    $this->db->select('COALESCE(SUM(JumlahMasuk), 0) AS total_masuk, COALESCE(SUM(JumlahKeluar), 0) AS total_keluar', FALSE);
    $ringkasan = $this->db->get('dana_lain')->row_array();
    $ringkasan['saldo'] = $ringkasan['total_masuk'] - $ringkasan['total_keluar'];
    return $ringkasan;
  }

  public function addDanaLain($data)
  {
    $this->db->insert('dana_lain', $data);
  }

  public function updateDanaLain($id, $data)
  {
    $this->db->where('IdDanaLain', $id);
    $this->db->update('dana_lain', $data);
  }

  public function deleteDanaLain($id)
  {
    $this->db->where('IdDanaLain', $id);
    $this->db->delete('dana_lain');
  }

  private function ensureTable()
  {
    if ($this->db->table_exists('dana_lain')) {
      return;
    }

    $this->load->dbforge();
    $this->dbforge->add_field([
      'IdDanaLain' => [
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
    $this->dbforge->add_key('IdDanaLain', TRUE);
    $this->dbforge->create_table('dana_lain', TRUE);
  }
}

/* End of file Dana_lain_M.php */
