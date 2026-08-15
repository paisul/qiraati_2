<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Pengumuman_M extends CI_Model
{
  public function __construct()
  {
    parent::__construct();
    pastikan_tabel_pengumuman($this->db);
  }

  public function getAllPengumuman()
  {
    return $this->db->order_by('CreatedAt', 'desc')->get('pengumuman')->result_array();
  }

  public function getTerbaruUntukWali($limit = 5)
  {
    return $this->db->order_by('CreatedAt', 'desc')->limit($limit)->get('pengumuman')->result_array();
  }

  public function getById($id)
  {
    return $this->db->get_where('pengumuman', ['IdPengumuman' => $id])->row_array();
  }

  public function addPengumuman($data)
  {
    return $this->db->insert('pengumuman', $data);
  }

  public function updatePengumuman($data)
  {
    $this->db->where('IdPengumuman', $data['IdPengumuman']);
    return $this->db->update('pengumuman', $data);
  }

  public function deletePengumuman($data)
  {
    return $this->db->delete('pengumuman', $data);
  }
}

/* End of file Pengumuman_M.php */
