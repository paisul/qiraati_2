<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Target_ujian_kelas_M extends CI_Model
{
  public function __construct()
  {
    parent::__construct();
    $this->ensureTable();
  }

  public function getAllTargetUjianKelas()
  {
    $query = 'SELECT `tuk`.*,`kls`.`NamaKelas`,`tu`.`Keterangan`,(SELECT COUNT(`IdKelas`) FROM `targetujiankelas` WHERE `IdKelas`=`tuk`.`IdKelas`) AS JumlahKelas
    FROM `targetujiankelas` tuk
    JOIN `kelas` kls ON `kls`.`IdKelas` = `tuk`.`IdKelas`
    JOIN `targetujian` tu ON `tu`.`IdTargetUjian` = `tuk`.`IdTargetUjian`
    ORDER BY tuk.`IdKelas` ASC';
    return $this->db->query($query)->result_array();
  }

  public function getTargetUjianByKelas($IdKelas)
  {
    $query = 'SELECT `targetujiankelas`.`IdTargetKelas`,`targetujiankelas`.`IdTargetUjian`,`kelas`.`NamaKelas`,`targetujian`.`Keterangan`
    FROM `targetujiankelas`
    JOIN `kelas` ON `kelas`.`IdKelas`=`targetujiankelas`.`IdKelas`
    JOIN `targetujian` ON `targetujiankelas`.`IdTargetUjian`=`targetujian`.`IdTargetUjian`
    WHERE `kelas`.`IdKelas`="' . $IdKelas . '"';
    return $this->db->query($query)->result_array();
  }


  public function addTargetKelas($data)
  {
    $this->db->insert_batch('targetujiankelas', $data);
  }

  public function addTarget($data)
  {
    $this->db->insert('targetujiankelas', $data);
  }

  public function hapusTargetKelas($data)
  {
    $this->db->where('IdTargetKelas', $data['IdTargetKelas']);
    $this->db->delete('targetujiankelas', $data);
  }

  // Tabel ini belum pernah dibuat di database - dibuat otomatis saat pertama kali dipakai.
  private function ensureTable()
  {
    if ($this->db->table_exists('targetujiankelas')) {
      return;
    }

    $this->load->dbforge();
    $this->dbforge->add_field([
      'IdTargetKelas' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => TRUE, 'auto_increment' => TRUE],
      'IdKelas' => ['type' => 'INT', 'constraint' => 11, 'null' => FALSE],
      'IdTargetUjian' => ['type' => 'INT', 'constraint' => 11, 'null' => FALSE],
    ]);
    $this->dbforge->add_key('IdTargetKelas', TRUE);
    $this->dbforge->create_table('targetujiankelas', TRUE);
  }
}

/* End of file Target_ujian_kelas_M.php */
