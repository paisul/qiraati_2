<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Kelas_M extends CI_Model
{
  public function __construct()
  {
    parent::__construct();
    $this->ensureKolomTambahan();
  }

  // Data lengkap: nama kelas, pembimbing, jumlah santri (dihitung langsung/live - SSOT, bukan kolom tersimpan).
  public function getAllKelasLengkap()
  {
    // Urut sesuai tingkatan (Jilid 1..5, Al-Qur'an) dari pilihan_nama_kelas(), bukan urutan input -
    // supaya kelas yang ditambah belakangan tetap tampil di posisi tingkatannya, bukan di paling bawah.
    $daftar_diescape = array_map([$this->db, 'escape'], pilihan_nama_kelas());
    $urutan_tingkatan = 'FIELD(kelas.NamaKelas, ' . implode(', ', $daftar_diescape) . ')';

    $this->db->select("kelas.*, musyrif.NamaMusyrif, COUNT(siswa.IdSiswa) AS JumlahSantri,
      SUM(CASE WHEN siswa.JenisKelamin = 'Laki-laki' THEN 1 ELSE 0 END) AS JumlahLaki,
      SUM(CASE WHEN siswa.JenisKelamin = 'Perempuan' THEN 1 ELSE 0 END) AS JumlahPerempuan", FALSE);
    $this->db->from('kelas');
    $this->db->join('musyrif', 'musyrif.IdMusyrif = kelas.IdMusyrif', 'left');
    $this->db->join('siswa', 'siswa.IdKelas = kelas.IdKelas', 'left');
    $this->db->group_by('kelas.IdKelas');
    $this->db->order_by($urutan_tingkatan, 'asc', FALSE);
    $this->db->order_by('kelas.IdKelas', 'asc');
    return $this->db->get()->result_array();
  }

  // Dipakai dropdown Kelas di berbagai form (Santri, Target, dst) - urutannya disamakan dengan
  // halaman Data Kelas (getAllKelasLengkap() di atas): berdasar tingkatan (Jilid 1..5, Al-Qur'an),
  // bukan urutan ID/kapan kelas itu dibuat. Tanpa ini, kelas yang dibuat ULANG (mis. setelah
  // terhapus tidak sengaja) muncul di paling bawah dropdown - padahal di halaman Data Kelas dia
  // tetap tampil di posisi tingkatannya.
  public function getAllKelas()
  {
    $daftar_diescape = array_map([$this->db, 'escape'], pilihan_nama_kelas());
    $urutan_tingkatan = 'FIELD(NamaKelas, ' . implode(', ', $daftar_diescape) . ')';

    $this->db->select('*');
    $this->db->from('kelas');
    $this->db->order_by($urutan_tingkatan, 'asc', FALSE);
    $this->db->order_by('IdKelas', 'asc');
    return $this->db->get()->result_array();
  }

  public function getById($id)
  {
    $this->db->select('kelas.*, musyrif.NamaMusyrif');
    $this->db->from('kelas');
    $this->db->join('musyrif', 'musyrif.IdMusyrif = kelas.IdMusyrif', 'left');
    $this->db->where('kelas.IdKelas', $id);
    $kelas = $this->db->get()->row_array();

    if ($kelas) {
      $kelas['JumlahSantri'] = $this->jumlahSantri($id);
    }

    return $kelas;
  }

  public function jumlahSantri($id_kelas)
  {
    return (int) $this->db->where('IdKelas', $id_kelas)->count_all_results('siswa');
  }

  // Dipakai Musyrif::delete() untuk mencegah penghapusan pembimbing yang masih ditugaskan ke suatu kelas.
  public function isMusyrifJadiPembimbing($id_musyrif)
  {
    return (bool) $this->db->get_where('kelas', ['IdMusyrif' => $id_musyrif])->row_array();
  }

  // Dipakai Kelas::delete() untuk mencegah penghapusan kelas yang masih ada santrinya - tidak ada
  // cascade delete siswa->kelas di database maupun kode, jadi santri di kelas itu TIDAK ikut
  // terhapus, tapi jadi "yatim" (IdKelas-nya menunjuk ke kelas yang sudah tidak ada) dan hilang
  // dari Data Santri (join ke kelas). Kejadian nyata: admin tidak sadar kelas masih ada santrinya.
  public function jumlahSantriDiKelas($id_kelas)
  {
    return (int) $this->db->where('IdKelas', $id_kelas)->where('Status', 'Aktif')->count_all_results('siswa');
  }

  public function addKelas($data)
  {
    $this->db->insert('kelas', $data);
  }

  public function updateKelas($data)
  {
    $this->db->where('IdKelas', $data['IdKelas']);
    unset($data['IdKelas']);
    $this->db->update('kelas', $data);
  }

  public function deleteKelas($data)
  {
    $this->db->where('IdKelas', $data['IdKelas']);
    $this->db->delete('kelas', $data);
  }

  /**
   * Migrasi skema (pola sama seperti Santri_M::ensureKolomTambahan()):
   * - Tambah IdMusyrif (pembimbing utama) & Lokasi/Ruangan.
   * - Perlebar NamaKelas (dulu varchar(5), kini perlu muat "Al-Qur'an"/"Jilid 1" dst).
   * - Longgarkan Tingkat/Kampus/SebutanKelas jadi opsional (skema lama, tidak lagi diminta di form baru,
   *   tapi kolomnya dipertahankan demi kompatibilitas data lama - tidak dihapus).
   */
  private function ensureKolomTambahan()
  {
    if (!$this->db->field_exists('IdMusyrif', 'kelas')) {
      $this->db->query('ALTER TABLE kelas ADD COLUMN IdMusyrif INT(11) NULL AFTER NamaKelas');
    }
    if (!$this->db->field_exists('Lokasi', 'kelas')) {
      $this->db->query('ALTER TABLE kelas ADD COLUMN Lokasi VARCHAR(100) NULL AFTER IdMusyrif');
    }

    $kolom_nama = $this->db->query("SHOW COLUMNS FROM kelas WHERE Field = 'NamaKelas'")->row_array();
    if ($kolom_nama && strpos($kolom_nama['Type'], 'varchar(20)') === FALSE) {
      $this->db->query('ALTER TABLE kelas MODIFY COLUMN NamaKelas VARCHAR(20) NOT NULL');
    }

    if ($this->db->field_exists('Tingkat', 'kelas')) {
      $kolom_tingkat = $this->db->query("SHOW COLUMNS FROM kelas WHERE Field = 'Tingkat'")->row_array();
      if ($kolom_tingkat && stripos($kolom_tingkat['Null'], 'YES') === FALSE) {
        $this->db->query("ALTER TABLE kelas MODIFY COLUMN Tingkat ENUM('MTs','MA') NULL");
      }
    }
    if ($this->db->field_exists('Kampus', 'kelas')) {
      $kolom_kampus = $this->db->query("SHOW COLUMNS FROM kelas WHERE Field = 'Kampus'")->row_array();
      if ($kolom_kampus && stripos($kolom_kampus['Null'], 'YES') === FALSE) {
        $this->db->query("ALTER TABLE kelas MODIFY COLUMN Kampus ENUM('Kampus 1','Kampus 2') NULL");
      }
    }
    if ($this->db->field_exists('SebutanKelas', 'kelas')) {
      $kolom_sebutan = $this->db->query("SHOW COLUMNS FROM kelas WHERE Field = 'SebutanKelas'")->row_array();
      if ($kolom_sebutan && stripos($kolom_sebutan['Null'], 'YES') === FALSE) {
        $this->db->query('ALTER TABLE kelas MODIFY COLUMN SebutanKelas CHAR(1) NULL');
      }
    }
  }
}

/* End of file Kelas_M.php */
