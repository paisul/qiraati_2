<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Pendaftaran_M extends CI_Model
{
  public function __construct()
  {
    parent::__construct();
    $this->ensureTable();
  }

  public function getMenunggu()
  {
    $this->db->select('pendaftaran_santri.*, kelas.NamaKelas, musyrif.NamaMusyrif');
    $this->db->from('pendaftaran_santri');
    $this->db->join('kelas', 'kelas.IdKelas = pendaftaran_santri.IdKelas', 'left');
    $this->db->join('musyrif', 'musyrif.IdMusyrif = kelas.IdMusyrif', 'left');
    $this->db->where('StatusPendaftaran', 'Menunggu');
    $this->db->order_by('CreatedAt', 'asc');
    return $this->db->get()->result_array();
  }

  public function countMenunggu()
  {
    return (int) $this->db->where('StatusPendaftaran', 'Menunggu')->count_all_results('pendaftaran_santri');
  }

  public function getById($id)
  {
    $this->db->where('IdPendaftaran', $id);
    return $this->db->get('pendaftaran_santri')->row_array();
  }

  public function simpan($data)
  {
    $data['StatusPendaftaran'] = 'Menunggu';
    $data['CreatedAt'] = date('Y-m-d H:i:s');
    $this->db->insert('pendaftaran_santri', $data);
    return $this->db->insert_id();
  }

  public function tandaiDiproses($id, $status, $admin_username)
  {
    $this->db->where('IdPendaftaran', $id);
    $this->db->update('pendaftaran_santri', [
      'StatusPendaftaran' => $status,
      'DiprosesOleh' => $admin_username,
      'DiprosesPada' => date('Y-m-d H:i:s'),
    ]);
  }

  private function ensureTable()
  {
    if ($this->db->table_exists('pendaftaran_santri')) {
      return;
    }

    $this->load->dbforge();
    $this->dbforge->add_field([
      'IdPendaftaran' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => TRUE, 'auto_increment' => TRUE],
      'NamaLengkap' => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => FALSE],
      'JenisKelamin' => ['type' => 'ENUM("Laki-laki","Perempuan")', 'null' => FALSE],
      'TempatLahir' => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => FALSE],
      'TanggalLahir' => ['type' => 'DATE', 'null' => FALSE],
      'Email' => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => FALSE],
      'Password' => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => FALSE],
      'Status' => ['type' => 'ENUM("Aktif","Lulus","Pindah","Berhenti")', 'null' => FALSE],
      'IdKelas' => ['type' => 'INT', 'constraint' => 11, 'null' => FALSE],
      'Pasfoto' => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => TRUE],
      'NamaAyah' => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => TRUE],
      'NamaIbu' => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => TRUE],
      'Alamat' => ['type' => 'TEXT', 'null' => TRUE],
      'SekolahAkademik' => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => TRUE],
      'SekolahTadika' => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => TRUE],
      'NoIDCard' => ['type' => 'VARCHAR', 'constraint' => 30, 'null' => TRUE],
      'TglMulaiBelajar' => ['type' => 'DATE', 'null' => TRUE],
      'TingkatPembelajaran' => ['type' => 'ENUM("Pemula","Jilid 1","Jilid 2","Jilid 3","Jilid 4","Jilid 5","Al-Qur\'an")', 'null' => TRUE],
      'StatusPendaftaran' => ['type' => 'ENUM("Menunggu","Disetujui","Ditolak")', 'null' => FALSE, 'default' => 'Menunggu'],
      'DiprosesOleh' => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => TRUE],
      'DiprosesPada' => ['type' => 'DATETIME', 'null' => TRUE],
      'CreatedAt' => ['type' => 'DATETIME', 'null' => TRUE],
    ]);
    $this->dbforge->add_key('IdPendaftaran', TRUE);
    $this->dbforge->create_table('pendaftaran_santri', TRUE);
  }
}

/* End of file Pendaftaran_M.php */
