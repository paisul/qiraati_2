<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Backup & Migrasi Database: export seluruh database jadi file .sql yang bisa diimport lagi
 * di server lain (mis. lewat phpMyAdmin), atau langsung diimport ulang di sini.
 * Pure PHP (tanpa exec/shell_exec/mysqldump) supaya tetap jalan di shared hosting yang biasanya
 * menonaktifkan fungsi shell.
 */
class PengaturanMigrasi extends CI_Controller
{
  public function __construct()
  {
    parent::__construct();
    cek_level('Admin');
    // Tidak dipakai langsung di controller ini (semua pakai $this->db mentah) - tapi memuat
    // CI_Model di sini supaya templates/sidebar.php (yang extends CI_Model) tidak fatal karena
    // induknya belum ke-load sama sekali di request ini.
    $this->load->model('MenuSidebar_M');
  }

  public function index()
  {
    $pesan = $this->input->get('pesan') ?: $this->session->flashdata('pesan');

    $data = [
      'title' => 'Backup & Migrasi Database',
      'user' => $this->getUserLogin(),
      'jumlah_tabel' => count($this->db->list_tables()),
      'pesan' => $pesan,
      'isi' => tampilan_mobile() ? 'pengaturan_migrasi/mobile-index' : 'pengaturan_migrasi/index',
    ];

    $this->load->view(tampilan_mobile() ? 'templates/wrapper-mobile-simple' : 'templates/wrapper-admin', $data);
  }

  // Unduh seluruh database (struktur + isi) sebagai satu file .sql.
  public function export()
  {
    $tables = $this->db->list_tables();
    $sql = "-- Backup database qiroati - " . date('Y-m-d H:i:s') . "\n";
    $sql .= "SET FOREIGN_KEY_CHECKS=0;\n\n";

    foreach ($tables as $table) {
      $buat = $this->db->query('SHOW CREATE TABLE `' . $table . '`')->row_array();
      $sql .= "DROP TABLE IF EXISTS `{$table}`;\n";
      $sql .= $buat['Create Table'] . ";\n\n";

      $rows = $this->db->get($table)->result_array();
      if ($rows) {
        $kolom = '`' . implode('`, `', array_keys($rows[0])) . '`';
        $batch = [];

        foreach ($rows as $i => $row) {
          $nilai = array_map(function ($v) {
            return $v === null ? 'NULL' : "'" . $this->db->escape_str($v) . "'";
          }, $row);
          $batch[] = '(' . implode(', ', $nilai) . ')';

          // Kirim per 200 baris supaya satu statement INSERT tidak raksasa.
          if (count($batch) >= 200 || $i === count($rows) - 1) {
            $sql .= "INSERT INTO `{$table}` ({$kolom}) VALUES\n" . implode(",\n", $batch) . ";\n";
            $batch = [];
          }
        }
        $sql .= "\n";
      }
    }

    $sql .= "SET FOREIGN_KEY_CHECKS=1;\n";

    $this->load->helper('download');
    force_download('backup_qiroati_' . date('Y-m-d_His') . '.sql', $sql);
  }

  // Jalankan file .sql yang diupload terhadap database saat ini - MENIMPA tabel yang namanya sama.
  public function import()
  {
    if (empty($_FILES['file_sql']['tmp_name']) || $_FILES['file_sql']['error'] !== UPLOAD_ERR_OK) {
      redirect('pengaturanmigrasi?pesan=' . rawurlencode('Pilih file .sql yang valid terlebih dahulu, tidak diproses.'));
      return;
    }

    if (strtolower(pathinfo($_FILES['file_sql']['name'], PATHINFO_EXTENSION)) !== 'sql') {
      redirect('pengaturanmigrasi?pesan=' . rawurlencode('File harus berformat .sql, tidak diproses.'));
      return;
    }

    $maks_ukuran = 50 * 1024 * 1024;
    if ($_FILES['file_sql']['size'] > $maks_ukuran) {
      redirect('pengaturanmigrasi?pesan=' . rawurlencode('Ukuran file .sql maksimal 50MB, tidak diproses.'));
      return;
    }

    if (function_exists('set_time_limit')) {
      @set_time_limit(120);
    }

    $sql = file_get_contents($_FILES['file_sql']['tmp_name']);
    $conn = $this->db->conn_id;
    $berhasil = $conn->multi_query($sql);

    if ($berhasil) {
      // Kuras semua result set dari statement bertumpuk supaya koneksi tidak nyangkut.
      do {
        if ($hasil = $conn->store_result()) {
          $hasil->free();
        }
      } while ($conn->more_results() && $conn->next_result());
    }

    $pesan = ($berhasil && !$conn->error) ? 'Database berhasil diimport!' : 'Import gagal: ' . $conn->error;
    redirect('pengaturanmigrasi?pesan=' . rawurlencode($pesan));
  }

  private function getUserLogin()
  {
    return $this->db->get_where('login', ['username' => $this->session->userdata('username')])->row_array();
  }
}

/* End of file PengaturanMigrasi.php */
