<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Statistik_public_M extends CI_Model
{
  public function getRingkasan()
  {
    return [
      'murid' => $this->countRows('siswa'),
      'murid_aktif' => $this->countRows('siswa', ['Status' => 'Aktif']),
      'guru' => $this->countRows('musyrif'),
      'kelas' => $this->countRows('kelas'),
      'wali' => $this->countRows('login', ['level' => 'Wali']),
      'dana' => $this->getTotalDana(),
      'periode' => $this->getPeriodeTerbaru(),
    ];
  }

  public function getMuridPerKelas()
  {
    $this->db->select('kelas.NamaKelas, COUNT(siswa.IdSiswa) AS Jumlah', FALSE);
    $this->db->from('kelas');
    $this->db->join('siswa', 'siswa.IdKelas = kelas.IdKelas', 'left');
    $this->db->group_by('kelas.IdKelas');
    $this->db->order_by('kelas.IdKelas', 'asc');
    return $this->db->get()->result_array();
  }

  public function getStatusMurid()
  {
    $this->db->select('Status, COUNT(IdSiswa) AS Jumlah', FALSE);
    $this->db->from('siswa');
    $this->db->group_by('Status');
    return $this->db->get()->result_array();
  }

  private function countRows($table, $where = [])
  {
    if (!$this->db->table_exists($table)) {
      return 0;
    }

    if ($where) {
      $this->db->where($where);
    }

    return $this->db->count_all_results($table);
  }

  private function getPeriodeTerbaru()
  {
    if (!$this->db->table_exists('periode')) {
      return '-';
    }

    $this->db->order_by('IdPeriode', 'desc');
    $periode = $this->db->get('periode', 1)->row_array();
    return $periode ? $periode['Periode'] : '-';
  }

  private function getTotalDana()
  {
    if ($this->db->table_exists('dana') && $this->db->field_exists('JumlahMasuk', 'dana') && $this->db->field_exists('JumlahKeluar', 'dana')) {
      $this->db->select('COALESCE(SUM(JumlahMasuk), 0) - COALESCE(SUM(JumlahKeluar), 0) AS total_dana', FALSE);
      $result = $this->db->get('dana')->row_array();
      return $result && $result['total_dana'] ? (int) $result['total_dana'] : 0;
    }

    $pilihan_tabel = ['dana', 'keuangan', 'kas', 'pembayaran', 'transaksi'];
    $pilihan_kolom = ['nominal', 'Nominal', 'jumlah', 'Jumlah', 'total', 'Total', 'debit', 'Debit', 'pemasukan', 'Pemasukan'];

    foreach ($pilihan_tabel as $table) {
      if (!$this->db->table_exists($table)) {
        continue;
      }

      foreach ($pilihan_kolom as $field) {
        if ($this->db->field_exists($field, $table)) {
          $this->db->select_sum($field, 'total_dana');
          $result = $this->db->get($table)->row_array();
          return $result && $result['total_dana'] ? (int) $result['total_dana'] : 0;
        }
      }
    }

    return 0;
  }
}

/* End of file Statistik_public_M.php */
