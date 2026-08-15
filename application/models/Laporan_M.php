<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Laporan_M extends CI_Model
{
  private $status_list = ['Hadir', 'Sakit', 'Izin', 'Alpa'];

  public function getKelas()
  {
    $this->db->select('IdKelas, NamaKelas');
    $this->db->from('kelas');
    $this->db->order_by('NamaKelas', 'asc');
    return $this->db->get()->result_array();
  }

  public function getRekapAbsensi($tanggal_awal, $tanggal_akhir, $id_kelas = null, $id_siswa = null, $nis = null, $gender = null, $urutkan = null)
  {
    $this->db->select('siswa.IdSiswa AS id, siswa.NIS AS nomor, siswa.NamaLengkap AS nama, kelas.NamaKelas AS keterangan, siswa.Pasfoto AS pasfoto, siswa.JenisKelamin AS gender, TIMESTAMPDIFF(YEAR, siswa.TanggalLahir, CURDATE()) AS usia', FALSE);
    $this->db->from('siswa');
    $this->db->join('kelas', 'kelas.IdKelas = siswa.IdKelas', 'left');
    if ($id_kelas) {
      $this->db->where('siswa.IdKelas', $id_kelas);
    }
    if ($id_siswa) {
      $this->db->where('siswa.IdSiswa', $id_siswa);
    }
    if ($nis) {
      $this->db->like('siswa.NIS', $nis);
    }
    if ($gender) {
      $this->db->where('siswa.JenisKelamin', $gender);
    }

    // Urutkan Berdasarkan (opsional) - Nama Lengkap tetap dipakai sebagai urutan kedua supaya rapi.
    if ($urutkan === 'kelas') {
      $this->db->order_by('kelas.NamaKelas', 'asc');
    } elseif ($urutkan === 'gender') {
      $this->db->order_by('siswa.JenisKelamin', 'asc');
    } elseif ($urutkan === 'usia') {
      // Pakai alias 'usia' dari SELECT, bukan expression TIMESTAMPDIFF(...) langsung - order_by()
      // CI3 memecah string berdasar koma, jadi argumen fungsi (ada komanya) bikin SQL rusak.
      $this->db->order_by('usia', 'asc');
    }
    $this->db->order_by('siswa.NamaLengkap', 'asc');
    $siswa = $this->db->get()->result_array();

    return $this->gabungRekap($siswa, 'santri', $tanggal_awal, $tanggal_akhir);
  }

  public function getRekapAbsensiPembimbing($tanggal_awal, $tanggal_akhir)
  {
    $this->db->select('IdMusyrif AS id, Email AS nomor, NamaMusyrif AS nama, NoHp AS keterangan, Pasfoto AS pasfoto', FALSE);
    $this->db->from('musyrif');
    $this->db->order_by('NamaMusyrif', 'asc');
    $pembimbing = $this->db->get()->result_array();

    return $this->gabungRekap($pembimbing, 'pembimbing', $tanggal_awal, $tanggal_akhir);
  }

  private function gabungRekap($peserta, $jenis, $tanggal_awal, $tanggal_akhir)
  {
    $this->db->select('IdPeserta, Status, COUNT(*) AS Jumlah', FALSE);
    $this->db->from('absen');
    $this->db->where('JenisPeserta', $jenis);
    $this->db->where('Tanggal >=', $tanggal_awal);
    $this->db->where('Tanggal <=', $tanggal_akhir);
    $this->db->group_by(['IdPeserta', 'Status']);
    $rows = $this->db->get()->result_array();

    $rekap = [];
    foreach ($rows as $row) {
      $rekap[$row['IdPeserta']][$row['Status']] = (int) $row['Jumlah'];
    }

    $hasil = [];
    foreach ($peserta as $row) {
      foreach ($this->status_list as $status) {
        $row[$status] = isset($rekap[$row['id']][$status]) ? $rekap[$row['id']][$status] : 0;
      }
      $row['Total'] = $row['Hadir'] + $row['Sakit'] + $row['Izin'] + $row['Alpa'];
      $hasil[] = $row;
    }

    return $hasil;
  }
}

/* End of file Laporan_M.php */
