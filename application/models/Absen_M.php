<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Absen_M extends CI_Model
{
  public function __construct()
  {
    parent::__construct();
    $this->ensureTable();
  }

  // Urutan roster: dikelompokkan per pembimbing (lalu kelas), laki-laki dulu sampai habis baru
  // perempuan. Dulu mengandalkan ORDER BY JenisKelamin ASC begitu saja (asumsi "Laki-laki" <
  // "Perempuan" secara alfabet, atau urutan ENUM) - ternyata urutannya bisa meleset kalau
  // kolomnya/datanya tidak persis sesuai asumsi itu. FIELD(...) di sini eksplisit menyebutkan
  // urutan yang diinginkan, jadi tidak bergantung pada tipe kolom/koleksi apa pun.
  // $id_musyrif: kalau diisi, hanya santri di kelas pembimbing itu yang muncul (dipakai saat Musyrif absen sendiri).
  public function getSantri($id_musyrif = null)
  {
    $this->db->select('siswa.IdSiswa AS id, siswa.NIS AS nomor, siswa.NamaLengkap AS nama, kelas.NamaKelas AS keterangan, siswa.Pasfoto AS pasfoto');
    $this->db->from('siswa');
    $this->db->join('kelas', 'kelas.IdKelas = siswa.IdKelas', 'left');
    $this->db->join('musyrif', 'musyrif.IdMusyrif = kelas.IdMusyrif', 'left');
    // Santri yang sudah Pindah/Berhenti/Lulus tidak lagi diabsen.
    $this->db->where('siswa.Status', 'Aktif');
    if ($id_musyrif !== null) {
      $this->db->where('kelas.IdMusyrif', $id_musyrif);
    }
    $this->db->order_by('musyrif.NamaMusyrif', 'asc');
    $this->db->order_by('kelas.NamaKelas', 'asc');
    $this->db->order_by("FIELD(siswa.JenisKelamin, 'Laki-laki', 'Perempuan')", '', FALSE);
    $this->db->order_by('siswa.NamaLengkap', 'asc');
    return $this->db->get()->result_array();
  }

  public function getPembimbing()
  {
    $this->db->select('IdMusyrif AS id, Email AS nomor, NamaMusyrif AS nama, NoHp AS keterangan, Pasfoto AS pasfoto');
    $this->db->from('musyrif');
    $this->db->order_by('NamaMusyrif', 'asc');
    return $this->db->get()->result_array();
  }

  public function getAbsenByFilter($jenis, $tanggal)
  {
    $this->db->where('JenisPeserta', $jenis);
    $this->db->where('Tanggal', $tanggal);
    $rows = $this->db->get('absen')->result_array();
    $absen = [];

    foreach ($rows as $row) {
      $absen[$row['IdPeserta']] = $row;
    }

    return $absen;
  }

  public function getRingkasan($jenis, $tanggal)
  {
    $status = ['Hadir', 'Sakit', 'Izin', 'Alpa'];
    $ringkasan = array_fill_keys($status, 0);

    $this->db->select('Status, COUNT(IdAbsen) AS Jumlah', FALSE);
    $this->db->from('absen');
    $this->db->where('JenisPeserta', $jenis);
    $this->db->where('Tanggal', $tanggal);
    $this->db->group_by('Status');
    $rows = $this->db->get()->result_array();

    foreach ($rows as $row) {
      $ringkasan[$row['Status']] = (int) $row['Jumlah'];
    }

    return $ringkasan;
  }

  // Ringkasan absen SATU santri sepanjang bulan berjalan - dipakai Dashboard Wali (beda dari
  // getRingkasan() yang untuk SEMUA peserta pada satu tanggal, dipakai halaman absen admin/musyrif).
  public function getRingkasanBulanIni($id_siswa)
  {
    $status = ['Hadir', 'Sakit', 'Izin', 'Alpa'];
    $ringkasan = array_fill_keys($status, 0);

    $this->db->select('Status, COUNT(IdAbsen) AS Jumlah', FALSE);
    $this->db->from('absen');
    $this->db->where('JenisPeserta', 'santri');
    $this->db->where('IdPeserta', $id_siswa);
    $this->db->where('Tanggal >=', date('Y-m-01'));
    $this->db->group_by('Status');
    $rows = $this->db->get()->result_array();

    foreach ($rows as $row) {
      $ringkasan[$row['Status']] = (int) $row['Jumlah'];
    }

    return $ringkasan;
  }

  // Ringkasan absen SATU santri dalam rentang tanggal bebas - dipakai rating bintang "Kehadiran"
  // di Rapor (beda dari getRingkasanBulanIni() yang dikunci ke bulan berjalan).
  public function getRingkasanRentang($id_siswa, $tgl_mulai, $tgl_selesai)
  {
    $status = ['Hadir', 'Sakit', 'Izin', 'Alpa'];
    $ringkasan = array_fill_keys($status, 0);

    $this->db->select('Status, COUNT(IdAbsen) AS Jumlah', FALSE);
    $this->db->from('absen');
    $this->db->where('JenisPeserta', 'santri');
    $this->db->where('IdPeserta', $id_siswa);
    $this->db->where('Tanggal >=', $tgl_mulai);
    $this->db->where('Tanggal <=', $tgl_selesai);
    $this->db->group_by('Status');
    $rows = $this->db->get()->result_array();

    foreach ($rows as $row) {
      $ringkasan[$row['Status']] = (int) $row['Jumlah'];
    }

    return $ringkasan;
  }

  public function simpanAbsen($jenis, $id_peserta, $tanggal, $status)
  {
    $data = [
      'JenisPeserta' => $jenis,
      'IdPeserta' => $id_peserta,
      'Tanggal' => $tanggal,
      'Status' => $status,
      // Absen manual oleh Musyrif menggantikan ajuan Izin/Sakit dari Wali sepenuhnya (kalau ada) -
      // supaya tidak ada label "diajukan Wali" yang nyangkut di baris yang baru saja ditimpa manual.
      'Keterangan' => null,
      'DiajukanOlehWali' => 0,
      'UpdatedAt' => date('Y-m-d H:i:s'),
    ];

    $this->db->where('JenisPeserta', $jenis);
    $this->db->where('IdPeserta', $id_peserta);
    $this->db->where('Tanggal', $tanggal);
    $existing = $this->db->get('absen')->row_array();

    if ($existing) {
      $this->db->where('IdAbsen', $existing['IdAbsen']);
      $this->db->update('absen', $data);
      return;
    }

    $data['CreatedAt'] = date('Y-m-d H:i:s');
    $this->db->insert('absen', $data);
  }

  // Wali mengajukan Izin/Sakit untuk anaknya HARI INI SAJA (tanggal dihitung di controller, bukan
  // dari input Wali, supaya tidak bisa diisi tanggal sembarangan). Menimpa status yang sudah ada
  // di hari itu (mis. kalau Musyrif kebetulan sudah absen manual duluan) - keputusan Wali dianggap
  // paling baru & benar. DiajukanOlehWali=1 supaya halaman Absen Musyrif tahu ini bukan hasil
  // absen manual, dan Keterangan berisi alasannya.
  public function ajukanIzinSakitWali($id_siswa, $tanggal, $status, $keterangan)
  {
    $data = [
      'JenisPeserta' => 'santri',
      'IdPeserta' => $id_siswa,
      'Tanggal' => $tanggal,
      'Status' => $status,
      'Keterangan' => $keterangan,
      'DiajukanOlehWali' => 1,
      'UpdatedAt' => date('Y-m-d H:i:s'),
    ];

    $this->db->where('JenisPeserta', 'santri');
    $this->db->where('IdPeserta', $id_siswa);
    $this->db->where('Tanggal', $tanggal);
    $existing = $this->db->get('absen')->row_array();

    if ($existing) {
      $this->db->where('IdAbsen', $existing['IdAbsen']);
      $this->db->update('absen', $data);
      return;
    }

    $data['CreatedAt'] = date('Y-m-d H:i:s');
    $this->db->insert('absen', $data);
  }

  // Dipakai dashboard mobile Wali - tampilkan status hari ini (kalau ada) sebelum/sesudah mengajukan.
  public function getStatusSantriTanggal($id_siswa, $tanggal)
  {
    $this->db->where('JenisPeserta', 'santri');
    $this->db->where('IdPeserta', $id_siswa);
    $this->db->where('Tanggal', $tanggal);
    return $this->db->get('absen')->row_array();
  }

  // Kembalikan satu peserta ke "Belum Absen" - hapus baris tersimpan permanennya kalau ada
  // (berlaku juga untuk absen hari lalu yang sudah dikirim, bukan cuma draf hari ini).
  public function hapusAbsen($jenis, $id_peserta, $tanggal)
  {
    $this->db->where('JenisPeserta', $jenis);
    $this->db->where('IdPeserta', $id_peserta);
    $this->db->where('Tanggal', $tanggal);
    $this->db->delete('absen');
  }

  public function getDataKirim($jenis, $tanggal, $id_musyrif = null)
  {
    $peserta = $jenis == 'pembimbing' ? $this->getPembimbing() : $this->getSantri($id_musyrif);
    $absen = $this->getAbsenByFilter($jenis, $tanggal);
    $rows = [];

    foreach ($peserta as $row) {
      if (!isset($absen[$row['id']])) {
        continue;
      }

      $rows[] = [
        'jenis' => $jenis,
        'tanggal' => $tanggal,
        'nomor' => $row['nomor'],
        'nama' => $row['nama'],
        'keterangan' => $row['keterangan'],
        'status' => $absen[$row['id']]['Status'],
      ];
    }

    return $rows;
  }

  private function ensureTable()
  {
    if (!$this->db->table_exists('absen')) {
      $this->load->dbforge();
      $this->dbforge->add_field([
        'IdAbsen' => [
          'type' => 'INT',
          'constraint' => 11,
          'unsigned' => TRUE,
          'auto_increment' => TRUE,
        ],
        'JenisPeserta' => [
          'type' => 'ENUM("santri","pembimbing")',
          'null' => FALSE,
        ],
        'IdPeserta' => [
          'type' => 'INT',
          'constraint' => 11,
          'null' => FALSE,
        ],
        'Tanggal' => [
          'type' => 'DATE',
          'null' => FALSE,
        ],
        'Status' => [
          'type' => 'ENUM("Hadir","Sakit","Izin","Alpa")',
          'null' => FALSE,
        ],
        'Keterangan' => [
          'type' => 'VARCHAR',
          'constraint' => 255,
          'null' => TRUE,
        ],
        'DiajukanOlehWali' => [
          'type' => 'TINYINT',
          'constraint' => 1,
          'null' => FALSE,
          'default' => 0,
        ],
        'CreatedAt' => [
          'type' => 'DATETIME',
          'null' => TRUE,
        ],
        'UpdatedAt' => [
          'type' => 'DATETIME',
          'null' => TRUE,
        ],
      ]);
      $this->dbforge->add_key('IdAbsen', TRUE);
      $this->dbforge->create_table('absen', TRUE);
      return;
    }

    // Migrasi: Wali bisa ajukan Izin/Sakit sendiri untuk hari ini (lihat Wali::ajukan_izin_sakit()) -
    // Keterangan = alasan yang diisi Wali, DiajukanOlehWali menandai baris itu bukan hasil absen
    // manual Musyrif, supaya tampil beda di halaman Absen.
    if (!$this->db->field_exists('Keterangan', 'absen')) {
      $this->db->query("ALTER TABLE absen ADD COLUMN Keterangan VARCHAR(255) NULL");
    }
    if (!$this->db->field_exists('DiajukanOlehWali', 'absen')) {
      $this->db->query("ALTER TABLE absen ADD COLUMN DiajukanOlehWali TINYINT(1) NOT NULL DEFAULT 0");
    }
  }
}

/* End of file Absen_M.php */
