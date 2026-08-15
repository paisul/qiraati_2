<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Periode_M extends CI_Model
{

  public function getAllPeriode()
  {
    $this->db->select('*');
    $this->db->from('periode');
    return $this->db->get()->result_array();
  }

  public function addPeriode($data)
  {
    $this->db->insert('periode', $data);
  }

  public function updatePeriode($data)
  {
    $this->db->where('IdPeriode', $data['IdPeriode']);
    $this->db->update('periode', $data);
  }

  // Periode tidak boleh dihapus kalau masih dipakai di periodeujian (FK RESTRICT) - dicek dulu
  // di sini supaya bisa kasih tahu alasannya, bukan diam-diam gagal seperti sebelumnya.
  public function deletePeriode($data)
  {
    if ($this->dipakaiPeriodeUjian($data['IdPeriode'])) {
      return false;
    }

    $this->db->where('IdPeriode', $data['IdPeriode']);
    $this->db->delete('periode', $data);
    return true;
  }

  public function dipakaiPeriodeUjian($id_periode)
  {
    return (int) $this->db->where('IdPeriode', $id_periode)->count_all_results('periodeujian') > 0;
  }
}

/* End of file Periode_M.php */
