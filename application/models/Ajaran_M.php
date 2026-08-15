<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Ajaran_M extends CI_Model
{
  public function getAllAjaran()
  {
    $this->db->select('*');
    $this->db->from('ajaran');
    return $this->db->get()->result_array();
  }

  public function addAjaran($data)
  {
    $this->db->insert('ajaran', $data);
  }

  public function updateAjaran($data)
  {
    $this->db->where('IdAjaran', $data['IdAjaran']);
    $this->db->update('ajaran', $data);
  }

  // Ajaran tidak boleh dihapus kalau masih dipakai di periodeujian (FK RESTRICT) - dicek dulu
  // di sini supaya bisa kasih tahu alasannya, bukan diam-diam gagal seperti sebelumnya.
  public function deleteAjaran($data)
  {
    if ($this->dipakaiPeriodeUjian($data['IdAjaran'])) {
      return false;
    }

    $this->db->where('IdAjaran', $data['IdAjaran']);
    $this->db->delete('ajaran', $data);
    return true;
  }

  public function dipakaiPeriodeUjian($id_ajaran)
  {
    return (int) $this->db->where('IdAjaran', $id_ajaran)->count_all_results('periodeujian') > 0;
  }
}

/* End of file Ajaran_M.php */
