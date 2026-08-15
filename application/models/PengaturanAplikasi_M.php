<?php
defined('BASEPATH') or exit('No direct script access allowed');

class PengaturanAplikasi_M extends CI_Model
{
  public function __construct()
  {
    parent::__construct();
    pastikan_tabel_pengaturan_aplikasi($this->db);
  }

  public function getPengaturan()
  {
    return $this->db->order_by('Id', 'asc')->limit(1)->get('pengaturan_aplikasi')->row_array();
  }

  public function updatePengaturan($id, $data)
  {
    $this->db->where('Id', $id);
    $this->db->update('pengaturan_aplikasi', $data);
  }
}

/* End of file PengaturanAplikasi_M.php */
