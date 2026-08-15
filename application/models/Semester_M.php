<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Semester_M extends CI_Model
{

  public function getAllSemester()
  {
    $this->db->select('*');
    $this->db->from('semester');
    return $this->db->get()->result_array();
  }

  public function addSemester($data)
  {
    $this->db->insert('semester', $data);
  }

  public function updateSemester($data)
  {
    $this->db->where('IdSemester', $data['IdSemester']);
    $this->db->update('semester', $data);
  }

  // Semester tidak boleh dihapus kalau masih dipakai di periodeujian (FK RESTRICT) - dicek dulu
  // di sini supaya bisa kasih tahu alasannya, bukan diam-diam gagal seperti sebelumnya.
  public function deleteSemester($data)
  {
    if ($this->dipakaiPeriodeUjian($data['IdSemester'])) {
      return false;
    }

    $this->db->where('IdSemester', $data['IdSemester']);
    $this->db->delete('semester', $data);
    return true;
  }

  public function dipakaiPeriodeUjian($id_semester)
  {
    return (int) $this->db->where('IdSemester', $id_semester)->count_all_results('periodeujian') > 0;
  }
}

/* End of file Semester_M.php */
