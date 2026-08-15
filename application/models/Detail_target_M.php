<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Detail_target_M extends CI_Model
{
  public function __construct()
  {
    parent::__construct();
    // Membedakan target Qur'an vs Doa/Qosidah - dipakai rating bintang otomatis Rapor
    // (lihat Raport::aksi_tampil()). Default 'Quran' supaya semua baris lama (sebelum kolom
    // ini ada) tidak ikut terhitung sebagai Doa/Qosidah.
    if (!$this->db->field_exists('JenisTarget', 'detailtarget')) {
      $this->db->query("ALTER TABLE detailtarget ADD COLUMN JenisTarget ENUM('Quran','Doa/Qosidah') NOT NULL DEFAULT 'Quran' AFTER IsiTarget");
    }
  }

  public function getAllDetailTarget()
  {
    $this->db->select('*');
    $this->db->from('detailtarget det');
    $this->db->join('target tgt', 'det.IdTarget = tgt.IdTarget', 'left');
    $this->db->join('kelas kls', 'kls.IdKelas = tgt.IdKelas', 'left');
    $this->db->order_by('det.IdTarget', 'asc');
    return $this->db->get()->result_array();
  }

  public function getDetailTargetByKelas($id_kelas)
  {
    $this->db->select('*');
    $this->db->from('detailtarget det');
    $this->db->join('target tgt', 'det.IdTarget = tgt.IdTarget', 'left');
    $this->db->join('kelas kls', 'kls.IdKelas = tgt.IdKelas', 'left');
    $this->db->where('kls.IdKelas', $id_kelas);
    $this->db->order_by('det.IdTarget', 'asc');
    return $this->db->get()->result_array();
  }

  public function addDetailTarget($data)
  {
    $this->db->insert('detailtarget', $data);
  }

  public function updateDetailTarget($data)
  {
    $this->db->where('IdDetailTarget', $data['IdDetailTarget']);
    $this->db->update('detailtarget', $data);
  }

  public function deleteDetailTarget($data)
  {
    $this->db->where('IdDetailTarget', $data['IdDetailTarget']);
    $this->db->delete('detailtarget', $data);
  }

  // Dipakai rating bintang "Hafalan Doa & Qosidah" di Rapor - jml_tugas = jumlah detailtarget
  // ber-JenisTarget itu dalam rentang tanggal periode ujian untuk kelas santri ybs, jml_selesai =
  // yang setorantarget-nya sudah 'Selesai' untuk detailkelompok santri itu.
  public function getProsentaseJenisTarget($id_siswa, $jenis_target, $tgl_mulai, $tgl_selesai)
  {
    $this->db->select('COUNT(DISTINCT det.IdDetailTarget) AS jml_tugas, COUNT(DISTINCT CASE WHEN st.Keterangan = "Selesai" THEN det.IdDetailTarget END) AS jml_selesai', FALSE);
    $this->db->from('detailtarget det');
    $this->db->join('target tgt', 'tgt.IdTarget = det.IdTarget', 'inner');
    $this->db->join('siswa s', 's.IdKelas = tgt.IdKelas', 'inner');
    $this->db->join('detailkelompok dk', 'dk.IdSiswa = s.IdSiswa', 'inner');
    $this->db->join('setorantarget st', 'st.IdDetailTarget = det.IdDetailTarget AND st.IdDetailKelompok = dk.IdDetailKelompok', 'left');
    $this->db->where('s.IdSiswa', $id_siswa);
    $this->db->where('det.JenisTarget', $jenis_target);
    $this->db->where('det.Tgl >=', $tgl_mulai);
    $this->db->where('det.Tgl <=', $tgl_selesai);
    $row = $this->db->get()->row_array();

    return [
      'jml_tugas' => (int) ($row['jml_tugas'] ?? 0),
      'jml_selesai' => (int) ($row['jml_selesai'] ?? 0),
    ];
  }
}

/* End of file Detail_target_M.php */
