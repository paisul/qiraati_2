<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Wali_M extends CI_Model
{
  public function __construct()
  {
    parent::__construct();
    pastikan_tabel_wali_siswa($this->db);
  }

  // Semua anak yang terhubung ke satu akun login Wali (satu akun bisa lebih dari satu anak).
  public function getDaftarAnak($id_user)
  {
    $this->db->select('siswa.IdSiswa, siswa.NIS, siswa.NamaLengkap, siswa.Pasfoto, siswa.IdKelas, kelas.NamaKelas');
    $this->db->from('wali_siswa');
    $this->db->join('siswa', 'siswa.IdSiswa = wali_siswa.IdSiswa');
    $this->db->join('kelas', 'kelas.IdKelas = siswa.IdKelas', 'left');
    $this->db->where('wali_siswa.IdUser', $id_user);
    $this->db->order_by('siswa.NamaLengkap', 'asc');
    return $this->db->get()->result_array();
  }

  /**
   * Data akun Wali + "anak aktif" (anak yang sedang dipilih untuk dilihat, lewat sidebar
   * "Ganti Anak" - lihat Wali::pilih_anak()). Bentuk hasil (kolom login digabung rata dengan
   * kolom siswa anak aktif) sengaja dipertahankan sama seperti sebelum fitur multi-anak ada,
   * supaya semua controller yang sudah baca $wali['IdSiswa']/$wali['IdKelas'] (Wali, Laporan,
   * Dana, Kelas) tetap jalan tanpa perlu diubah - cukup tambah key 'daftar_anak' baru di sini.
   */
  public function getDataWali($username)
  {
    $login = $this->db->where('username', $username)->where('level', 'Wali')->get('login')->row_array();

    if (!$login) {
      return NULL;
    }

    $daftar_anak = $this->getDaftarAnak($login['IdUser']);

    if (empty($daftar_anak)) {
      return NULL;
    }

    $id_siswa_aktif = $this->session->userdata('IdSiswaAktif');
    $anak_aktif = null;

    foreach ($daftar_anak as $anak) {
      if ((int) $anak['IdSiswa'] === (int) $id_siswa_aktif) {
        $anak_aktif = $anak;
        break;
      }
    }

    if (!$anak_aktif) {
      $anak_aktif = $daftar_anak[0];
    }

    return array_merge($login, $anak_aktif, ['daftar_anak' => $daftar_anak]);
  }

  public function getPekanSetoran()
  {
    $query = 'SELECT `target`.`Pekan`
    FROM `target`
    GROUP BY `target`.`Pekan`';
    return $this->db->query($query)->result_array();
  }

  public function getDataRekapSetoranSantri($IdSiswa, $IdPeriode)
  {
    $this->db->select('siswa.NamaLengkap, detailtarget.IsiTarget, detailtarget.Keterangan, target.Pekan');
    $this->db->from('setorantarget');
    $this->db->join('detailtarget', 'detailtarget.IdDetailTarget = setorantarget.IdDetailTarget');
    $this->db->join('target', 'target.IdTarget = detailtarget.IdTarget');
    $this->db->join('detailkelompok', 'detailkelompok.IdDetailKelompok = setorantarget.IdDetailKelompok');
    $this->db->join('siswa', 'siswa.IdSiswa = detailkelompok.IdSiswa');
    $this->db->where('siswa.IdSiswa', $IdSiswa);
    $this->db->where('target.IdPeriode', $IdPeriode);
    return $this->db->get()->result_array();
  }

  public function getJumlahSetoranSantri($IdSiswa, $IdPeriode)
  {
    return $this->countSetoran($IdSiswa, $IdPeriode, NULL, 'Jumlah_Setoran');
  }

  public function getJumlahSetoranSelesai($IdSiswa, $IdPeriode)
  {
    return $this->countSetoran($IdSiswa, $IdPeriode, 'Selesai', 'Jumlah_Setoran_Selesai');
  }

  public function getJumlahSetoranTidakSelesai($IdSiswa, $IdPeriode)
  {
    return $this->countSetoran($IdSiswa, $IdPeriode, 'Tidak Selesai', 'Jumlah_Setoran_Tidak_Selesai');
  }

  public function getSetoranSantri($IdSiswa, $IdPeriode, $Pekan = NULL)
  {
    return $this->getSetoranByStatus($IdSiswa, $IdPeriode, NULL, $Pekan);
  }

  public function getSetoranSantri_TidakSelesai($IdSiswa, $IdPeriode)
  {
    return $this->getSetoranByStatus($IdSiswa, $IdPeriode, 'Tidak Selesai');
  }

  public function getSetoranSantri_Selesai($IdSiswa, $IdPeriode)
  {
    return $this->getSetoranByStatus($IdSiswa, $IdPeriode, 'Selesai');
  }

  private function countSetoran($IdSiswa, $IdPeriode, $keterangan, $alias)
  {
    $this->db->select('COUNT(setorantarget.IdDetailTarget) AS ' . $alias, FALSE);
    $this->db->from('setorantarget');
    $this->db->join('detailtarget', 'detailtarget.IdDetailTarget = setorantarget.IdDetailTarget');
    $this->db->join('target', 'target.IdTarget = detailtarget.IdTarget');
    $this->db->join('detailkelompok', 'detailkelompok.IdDetailKelompok = setorantarget.IdDetailKelompok');
    $this->db->join('siswa', 'siswa.IdSiswa = detailkelompok.IdSiswa');
    $this->db->where('siswa.IdSiswa', $IdSiswa);
    $this->db->where('target.IdPeriode', $IdPeriode);

    if ($keterangan) {
      $this->db->where('setorantarget.Keterangan', $keterangan);
    }

    return $this->db->get()->row_array();
  }

  private function getSetoranByStatus($IdSiswa, $IdPeriode, $keterangan = NULL, $Pekan = NULL)
  {
    $this->db->select('detailtarget.IsiTarget, setorantarget.Keterangan, target.Pekan, detailtarget.Tgl');
    $this->db->from('setorantarget');
    $this->db->join('detailtarget', 'detailtarget.IdDetailTarget = setorantarget.IdDetailTarget');
    $this->db->join('target', 'target.IdTarget = detailtarget.IdTarget');
    $this->db->join('detailkelompok', 'detailkelompok.IdDetailKelompok = setorantarget.IdDetailKelompok');
    $this->db->join('siswa', 'siswa.IdSiswa = detailkelompok.IdSiswa');
    $this->db->where('siswa.IdSiswa', $IdSiswa);
    $this->db->where('target.IdPeriode', $IdPeriode);

    if ($keterangan) {
      $this->db->where('setorantarget.Keterangan', $keterangan);
    }

    if ($Pekan) {
      $this->db->where('target.Pekan', $Pekan);
    }

    return $this->db->get()->result_array();
  }
}

/* End of file Wali_M.php */
