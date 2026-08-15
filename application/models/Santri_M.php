<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Santri_M extends CI_Model
{
  public function __construct()
  {
    parent::__construct();
    $this->ensurePasfotoField();
    $this->ensureKolomTambahan();
    pastikan_tabel_wali_siswa($this->db);
  }

  // 'left' (bukan inner join bawaan) - kalau Kelas seorang santri terhapus (mis. admin menghapus
  // Kelas tanpa sadar masih ada santrinya), santri itu TETAP tampil di Data Santri (dengan Kelas
  // kosong) daripada hilang sama sekali dari daftar padahal datanya sendiri masih ada di database.
  public function getAllSantri()
  {
    $this->db->select('*');
    $this->db->from('siswa');
    $this->db->join('kelas', 'kelas.IdKelas = siswa.IdKelas', 'left');
    $this->db->order_by('siswa.IdKelas', 'asc');
    return $this->db->get()->result_array();
  }

  // Santri "yatim" - IdKelas-nya tidak lagi cocok dengan kelas manapun yang masih ada (kelasnya
  // sudah dihapus). Dipakai halaman pemulihan darurat (Santri::sambungkan_kelas()) untuk
  // menyambungkan mereka semua ke satu kelas pilihan sekaligus, tanpa perlu ubah satu-satu.
  public function getSantriYatim()
  {
    $this->db->select('siswa.*');
    $this->db->from('siswa');
    $this->db->join('kelas', 'kelas.IdKelas = siswa.IdKelas', 'left');
    $this->db->where('kelas.IdKelas', null);
    $this->db->where('siswa.Status', 'Aktif');
    $this->db->order_by('siswa.NamaLengkap', 'asc');
    return $this->db->get()->result_array();
  }

  public function pindahkanKelasBatch($daftar_id_siswa, $id_kelas_baru)
  {
    $this->db->where_in('IdSiswa', $daftar_id_siswa);
    $this->db->update('siswa', ['IdKelas' => $id_kelas_baru]);
    return $this->db->affected_rows();
  }

  // Dipakai Kelas::hapus_dengan_pindah() - pindahkan SEMUA santri di satu kelas ke kelas lain
  // sekaligus, sebelum kelas lamanya dihapus (supaya tidak ada yang jadi "yatim" lagi).
  public function pindahkanSemuaSantriKelas($id_kelas_lama, $id_kelas_baru)
  {
    $this->db->where('IdKelas', $id_kelas_lama);
    $this->db->update('siswa', ['IdKelas' => $id_kelas_baru]);
    return $this->db->affected_rows();
  }

  // Data lengkap (semua kolom formulir + email login) untuk kebutuhan export.
  public function getAllSantriLengkap()
  {
    $this->db->select('siswa.*, kelas.NamaKelas, musyrif.NamaMusyrif AS NamaPembimbingKelas, login.username AS Email');
    $this->db->from('siswa');
    $this->db->join('kelas', 'kelas.IdKelas = siswa.IdKelas', 'left');
    $this->db->join('musyrif', 'musyrif.IdMusyrif = kelas.IdMusyrif', 'left');
    $this->db->join('login', 'login.IdSiswa = siswa.IdSiswa AND login.level = "Wali"', 'left');
    $this->db->order_by('siswa.IdSiswa', 'asc');
    return $this->db->get()->result_array();
  }

  // Mencocokkan label "NamaKelas - Pembimbing" (format yang sama seperti di dropdown Kelas) ke IdKelas, untuk kebutuhan import.
  public function cariIdKelas($label_kelas)
  {
    $this->db->select('kelas.IdKelas, kelas.NamaKelas, musyrif.NamaMusyrif');
    $this->db->from('kelas');
    $this->db->join('musyrif', 'musyrif.IdMusyrif = kelas.IdMusyrif', 'left');
    $daftar_kelas = $this->db->get()->result_array();

    foreach ($daftar_kelas as $row) {
      $label = $row['NamaKelas'] . (!empty($row['NamaMusyrif']) ? ' - ' . $row['NamaMusyrif'] : '');
      if (strcasecmp(trim($label_kelas), $label) === 0) {
        return $row['IdKelas'];
      }
    }

    return null;
  }

  public function getSantriByNama($nama_santri)
  {
    $this->db->select('*');
    $this->db->from('siswa');
    $this->db->join('kelas', 'kelas.IdKelas = siswa.IdKelas');
    $this->db->order_by('siswa.IdKelas', 'asc');
    $this->db->like('NamaLengkap', $nama_santri);
    return $this->db->get()->result_array();
  }

  public function getSantriKelas($kelas)
  {
    $this->db->select('*');
    $this->db->from('siswa');
    $this->db->join('kelas', 'kelas.IdKelas = siswa.IdKelas');
    $this->db->order_by('siswa.IdSiswa', 'asc');
    $this->db->where('siswa.IdKelas', $kelas);
    return $this->db->get()->result_array();
  }

  public function getSantriDetail($id_siswa)
  {
    $this->db->select('siswa.*, kelas.NamaKelas, kelas.Tingkat, kelas.Kampus');
    $this->db->from('siswa');
    $this->db->join('kelas', 'kelas.IdKelas = siswa.IdKelas', 'left');
    $this->db->where('siswa.IdSiswa', $id_siswa);
    $santri = $this->db->get()->row_array();

    if ($santri) {
      $login = $this->getLoginWaliUntukSiswa($id_siswa);
      $santri['punya_login'] = (bool) $login;
      $santri['login'] = $login ? $login : ['username' => ''];
    }

    return $santri;
  }

  public function getSantri_Periode_Nilai($IdSiswa, $periode_ujian)
  {
    $query = $this->db->query('
    SELECT `rekapujian`.`IdSiswa`,`rekapujian`.`IdPeriodeUjian`,`rekapujian`.`Nilai`,SUM(`rekapujian`.`Nilai`) AS TotalNilai,AVG(`rekapujian`.`Nilai`) AS rata_rata,`siswa`.`NamaLengkap`,`kelas`.`NamaKelas`,`periode`.`Periode`,`rekapsetoran`.`Prosentase`
    FROM `rekapujian`
    JOIN `siswa` ON `rekapujian`.`IdSiswa` = `siswa`.`IdSiswa`
    JOIN `kelas` ON `siswa`.`IdKelas` = `kelas`.`IdKelas`
    JOIN `periodeujian` ON `rekapujian`.`IdPeriodeUjian` = `periodeujian`.`IdPeriodeUjian`
    JOIN `periode` ON `periodeujian`.`IdPeriode` = `periode`.`IdPeriode`
    JOIN `rekapsetoran` ON `rekapsetoran`.`IdSiswa` = `siswa`.`IdSiswa`
    WHERE `rekapujian`.`IdSiswa` ="' . $IdSiswa . '" AND `rekapujian`.`IdPeriodeUjian` = "' . $periode_ujian . '"');
    return $query->result_array();
  }

  public function getSantri_Periode($idKelas, $idPeriodeUjian)
  {
    $this->db->select('s.*,kelas.*,pu.*,p.Periode,aj.ThAjaran,smt.Semester');
    $this->db->from('siswa s');
    $this->db->join('kelas', 'kelas.IdKelas = s.IdKelas');
    $this->db->join('periodeujian pu', 'pu.IdKelas = kelas.IdKelas', 'left');
    $this->db->join('periode p', 'p.IdPeriode = pu.IdPeriode', 'left');
    $this->db->join('ajaran aj', 'aj.IdAjaran = pu.IdAjaran', 'left');
    $this->db->join('semester smt', 'smt.IdSemester = pu.IdSemester', 'left');
    $this->db->order_by('s.NIS', 'asc');
    $this->db->where('s.IdKelas', $idKelas);
    $this->db->where('pu.IdPeriodeUjian', $idPeriodeUjian);
    return $this->db->get()->result_array();
  }

  public function nilai_santri($idKelas, $idPeriodeUjian)
  {
    $query = $this->db->query('SELECT `rekapujian`.`IdSiswa`,`rekapujian`.`Nilai`,SUM(`rekapujian`.`Nilai`) AS total_nilai,AVG(`rekapujian`.`Nilai`) AS rata_rata,`siswa`.`NamaLengkap`,`kelas`.`NamaKelas`,`periodeujian`.*,`periode`.`Periode`,`ajaran`.`ThAjaran`,`semester`.`Semester`,`rekapsetoran`.`Prosentase`
    FROM `rekapujian`
    JOIN `siswa` ON `siswa`.`IdSiswa` = `rekapujian`.`IdSiswa`
    JOIN `kelas` ON `kelas`.`IdKelas`=`siswa`.`IdKelas`
    JOIN `periodeujian` ON `periodeujian`.`IdKelas`= `kelas`.`IdKelas`
    JOIN `periode` ON `periode`.`IdPeriode` = `periodeujian`.`IdPeriode`
    JOIN `ajaran` ON `ajaran`.`IdAjaran` = `periodeujian`.`IdAjaran`
    JOIN `semester` ON `semester`.`IdSemester` = `periodeujian`.`IdSemester`
    JOIN `rekapsetoran` ON `rekapsetoran`.`IdSiswa` = `siswa`.`IdSiswa`
    WHERE `siswa`.`IdKelas` = "' . $idKelas . '"
    AND `rekapujian`.`IdPeriodeUjian` = "' . $idPeriodeUjian . '"
    GROUP BY (`rekapujian`.`IdSiswa`)');
    return $query->result_array();
  }

  /**
   * Nomor Induk Santri dibuat otomatis: 4 digit tahun + 3 digit urutan (mis. 2026001).
   * Diverifikasi ulang saat benar-benar disimpan agar tidak bentrok dengan pendaftaran lain.
   */
  public function generateNis()
  {
    $tahun = date('Y');

    $this->db->select('NIS');
    $this->db->from('siswa');
    $this->db->like('NIS', $tahun, 'after');
    $this->db->order_by('NIS', 'desc');
    $this->db->limit(1);
    $terakhir = $this->db->get()->row_array();

    $urutan = 1;
    if ($terakhir && strlen($terakhir['NIS']) > strlen($tahun)) {
      $urutan = (int) substr($terakhir['NIS'], strlen($tahun)) + 1;
    }

    do {
      $nis = $tahun . str_pad($urutan, 3, '0', STR_PAD_LEFT);
      $urutan++;
    } while ($this->db->get_where('siswa', ['NIS' => $nis])->row_array());

    return $nis;
  }

  public function isNisTerpakai($nis)
  {
    return (bool) $this->db->get_where('siswa', ['NIS' => $nis])->row_array();
  }

  public function isEmailTerpakai($email, $kecuali_id_siswa = null)
  {
    // PENTING: selesaikan dulu query "siapa akun sendiri" SEBELUM mulai membangun query utama di
    // bawah - $this->db adalah query builder yang sama (stateful) dipakai getLoginWaliUntukSiswa().
    // Kalau where('username', $email) dipanggil DULU baru getLoginWaliUntukSiswa() dipanggil di
    // tengah (sebelum get() dieksekusi), pemanggilan get()/get_where() DI DALAM
    // getLoginWaliUntukSiswa() ikut me-reset builder ini - kondisi "username" yang sudah
    // ditambahkan tadi hilang, dan query akhir jadi cuma "WHERE IdUser != X" (cocok ke HAMPIR SEMUA
    // akun lain di sistem) - selalu mengembalikan TRUE walau email yang diperiksa sebenarnya
    // cuma dipakai akun sendiri. Ini penyebab wali multi-anak selalu ditolak "email sudah
    // terdaftar" walau tidak ganti email sama sekali.
    $id_user_sendiri = null;
    if ($kecuali_id_siswa) {
      $login_sendiri = $this->getLoginWaliUntukSiswa($kecuali_id_siswa);
      if ($login_sendiri) {
        $id_user_sendiri = $login_sendiri['IdUser'];
      }
    }

    $this->db->where('username', $email);
    if ($id_user_sendiri !== null) {
      $this->db->where('IdUser !=', $id_user_sendiri);
    }

    return (bool) $this->db->get('login')->row_array();
  }

  // Akun login Wali yang terhubung ke satu santri, baik lewat login.IdSiswa (anak pertama/lama)
  // maupun lewat wali_siswa (anak kedua dst). Dipakai isEmailTerpakai/updateLoginSantri/deleteSantri
  // (dan Santri.php::cek_email_unik/perbarui() dari luar) supaya semuanya konsisten "menemukan"
  // akun yang benar terlepas dari anak yang mana yang dipakai.
  public function getLoginWaliUntukSiswa($id_siswa)
  {
    $login = $this->db->get_where('login', ['IdSiswa' => $id_siswa, 'level' => 'Wali'])->row_array();
    if ($login) {
      return $login;
    }

    $this->db->select('login.*');
    $this->db->from('wali_siswa');
    $this->db->join('login', 'login.IdUser = wali_siswa.IdUser');
    $this->db->where('wali_siswa.IdSiswa', $id_siswa);
    $this->db->where('login.level', 'Wali');
    return $this->db->get()->row_array();
  }

  /**
   * Cari email Wali yang KEBETULAN punya lebih dari satu baris `login` (akun wali yang "terpecah").
   * Ini bisa terjadi dari data lama sebelum validasi cek_email_unik()/validasiBarisImport() ada -
   * mis. anak kedua ditambahkan dengan password yang beda dari akun pertama, jadi bukan disambungkan
   * tapi malah bikin akun baru dengan email yang sama. Akibatnya wali itu tidak bisa ubah profil/
   * password sendiri (sistem anggap emailnya "sudah dipakai akun lain", padahal itu akun dia sendiri
   * yang terpecah). Dipakai halaman Admin > Akun Wali Ganda untuk mendeteksi & menggabungkannya.
   */
  public function getAkunWaliGanda()
  {
    $this->db->select('username');
    $this->db->from('login');
    $this->db->where('level', 'Wali');
    $this->db->group_by('username');
    $this->db->having('COUNT(*) > 1', null, false);
    $daftar_username = $this->db->get()->result_array();

    $grup = [];
    foreach ($daftar_username as $u) {
      $akun = $this->db->where(['username' => $u['username'], 'level' => 'Wali'])->order_by('IdUser', 'asc')->get('login')->result_array();

      foreach ($akun as &$a) {
        $this->db->select('siswa.IdSiswa, siswa.NamaLengkap, siswa.NIS');
        $this->db->from('wali_siswa');
        $this->db->join('siswa', 'siswa.IdSiswa = wali_siswa.IdSiswa');
        $this->db->where('wali_siswa.IdUser', $a['IdUser']);
        $a['anak'] = $this->db->get()->result_array();
      }
      unset($a);

      $grup[] = ['username' => $u['username'], 'akun' => $akun];
    }

    return $grup;
  }

  /**
   * Jalankan LANGSUNG fungsi validasi yang sesungguhnya dipakai Wali::cek_email_wali() dengan
   * input tertentu, supaya kelihatan persis apa yang dikembalikan di server yang sesungguhnya -
   * dipakai halaman Admin > Akun Wali Ganda saat dugaan "akun ganda" sudah tidak cocok lagi.
   */
  public function simulasiCekEmailWali($email, $id_siswa)
  {
    return [
      'akun_pemilik' => $this->getLoginWaliUntukSiswa($id_siswa),
      'dipakai' => $this->isEmailTerpakai($email, $id_siswa),
    ];
  }

  /**
   * Cari SEMUA baris login (persis maupun mirip - LIKE - untuk menangkap beda spasi/kapital yang
   * tidak kelihatan) untuk satu email tertentu, plus anak yang tertaut ke tiap akun. Dipakai
   * halaman Admin > Akun Wali Ganda saat getAkunWaliGanda() sudah bersih tapi wali tetap gagal
   * ubah profil - supaya bisa lihat persis data mentahnya (kolom login.IdSiswa, wali_siswa, dst).
   */
  public function cariRawAkunWali($email)
  {
    $ambilAkun = function ($rows) {
      foreach ($rows as &$r) {
        $r['panjang'] = strlen($r['username']);
        $this->db->select('siswa.IdSiswa, siswa.NamaLengkap');
        $this->db->from('wali_siswa');
        $this->db->join('siswa', 'siswa.IdSiswa = wali_siswa.IdSiswa');
        $this->db->where('wali_siswa.IdUser', $r['IdUser']);
        $r['anak'] = $this->db->get()->result_array();
      }
      unset($r);
      return $rows;
    };

    $persis = $this->db->where('username', $email)->get('login')->result_array();
    $mirip = $this->db->like('username', $email, 'both')->get('login')->result_array();

    return [
      'persis' => $ambilAkun($persis),
      'mirip' => $ambilAkun($mirip),
    ];
  }

  /**
   * Gabungkan akun wali "terpecah" - pindahkan semua anak dari $id_user_lain ke $id_user_simpan
   * (lewat wali_siswa), lalu hapus baris login $id_user_lain. $id_user_simpan yang dipilih ADMIN
   * (lihat catatan getAkunWaliGanda) tetap jadi satu-satunya akun untuk email itu setelah ini.
   */
  public function gabungkanAkunWali($id_user_simpan, $id_user_lain)
  {
    $anak_lain = $this->db->where('IdUser', $id_user_lain)->get('wali_siswa')->result_array();

    foreach ($anak_lain as $anak) {
      if ($this->db->where(['IdUser' => $id_user_simpan, 'IdSiswa' => $anak['IdSiswa']])->count_all_results('wali_siswa') === 0) {
        $this->db->insert('wali_siswa', ['IdUser' => $id_user_simpan, 'IdSiswa' => $anak['IdSiswa']]);
      }
    }

    $this->db->where('IdUser', $id_user_lain)->delete('wali_siswa');
    $this->db->where('IdUser', $id_user_lain)->delete('login');
  }

  // Hubungkan (anak, akun) di wali_siswa kalau belum ada - dipakai addWaliSantri/addOrHubungkanWaliSantri
  // supaya wali_siswa selalu jadi sumber data yang lengkap untuk akun manapun, baru atau lama.
  private function hubungkanWaliSiswa($id_user, $id_siswa)
  {
    if (!$id_user) {
      return;
    }

    if ($this->db->where(['IdUser' => $id_user, 'IdSiswa' => $id_siswa])->count_all_results('wali_siswa') > 0) {
      return;
    }

    $this->db->insert('wali_siswa', ['IdUser' => $id_user, 'IdSiswa' => $id_siswa]);
  }

  public function isIdCardTerpakai($no_id_card, $kecuali_id_siswa = null)
  {
    $this->db->where('NoIDCard', $no_id_card);
    if ($kecuali_id_siswa) {
      $this->db->where('IdSiswa !=', $kecuali_id_siswa);
    }
    return (bool) $this->db->get('siswa')->row_array();
  }

  public function addSantri($data)
  {
    $this->db->insert('siswa', $data);
    return $this->db->insert_id();
  }

  public function addWaliSantri($dataWali)
  {
    $this->db->insert('login', $dataWali);
    $id_user = $this->db->insert_id();
    $this->hubungkanWaliSiswa($id_user, $dataWali['IdSiswa']);
    return $id_user;
  }

  /**
   * Dipakai di tempat yang masih pegang password mentah sebelum di-hash (form Tambah Santri manual
   * & import Excel), DAN mensyaratkan admin/pengimpor benar-benar tahu password akun yang sudah
   * ada sebelum menyambungkan anak baru ke situ - kalau tidak cocok, dianggap GAGAL (form
   * ditolak lewat cek_email_unik()/validasiBarisImport(), tidak sampai pernah memanggil fungsi
   * ini dengan password yang salah). Untuk pendaftaran PUBLIK (Daftar.php), lihat
   * tautkanAtauBuatAkunWaliDariPendaftaran() di bawah - beda karena TIDAK mensyaratkan password
   * cocok sama sekali.
   */
  public function addOrHubungkanWaliSantri($dataWali, $password_mentah)
  {
    $existing = $this->getLoginWaliByUsername($dataWali['username']);

    if ($existing && password_verify($password_mentah, $existing['password'])) {
      $this->hubungkanWaliSiswa($existing['IdUser'], $dataWali['IdSiswa']);
      return $existing['IdUser'];
    }

    return $this->addWaliSantri($dataWali);
  }

  /**
   * Dipakai HANYA oleh Santri::setujui_pendaftaran() (pendaftaran publik lewat Daftar.php).
   * Sengaja TIDAK ADA pengecekan password sama sekali - orang tua boleh mendaftarkan anak kedua
   * dst dengan email yang sama walau passwordnya beda dari akun yang sudah ada, karena pendaftaran
   * publik sudah tidak lagi diblokir gara-gara itu. Kalau email ini sudah dipakai akun wali yang
   * ada, anak baru cukup DISAMBUNGKAN ke situ (password dari pendaftaran ini diabaikan, akun lama
   * tetap pakai passwordnya sendiri) - BUKAN bikin akun terpisah yang baru (itu yang sebelumnya
   * menyebabkan "Akun Wali Ganda", lihat getAkunWaliGanda()/gabungkanAkunWali()).
   */
  public function tautkanAtauBuatAkunWaliDariPendaftaran($dataWali)
  {
    $existing = $this->getLoginWaliByUsername($dataWali['username']);

    if ($existing) {
      $this->hubungkanWaliSiswa($existing['IdUser'], $dataWali['IdSiswa']);
      return $existing['IdUser'];
    }

    return $this->addWaliSantri($dataWali);
  }

  // Dipakai addOrHubungkanWaliSantri() & validasi cek_email_unik/validasiBarisImport di Santri.php
  // untuk cek "email ini sudah dipakai akun mana" sebelum tahu password-nya cocok atau tidak.
  public function getLoginWaliByUsername($username)
  {
    return $this->db->where('username', $username)->where('level', 'Wali')->get('login')->row_array();
  }

  public function updateSantri($data)
  {
    $this->db->where('IdSiswa', $data['IdSiswa']);
    unset($data['IdSiswa']);
    $this->db->update('siswa', $data);
  }

  public function updateLoginSantri($id_siswa, $data)
  {
    $existing = $this->getLoginWaliUntukSiswa($id_siswa);

    if ($existing) {
      $this->db->where('IdUser', $existing['IdUser']);
      $this->db->update('login', $data);
      return;
    }

    $data['IdSiswa'] = $id_siswa;
    $data['level'] = 'Wali';
    $this->db->insert('login', $data);
    $this->hubungkanWaliSiswa($this->db->insert_id(), $id_siswa);
  }

  /**
   * Pindahkan satu santri (mis. saat admin ubah emailnya jadi sama dengan email akun kakak/adiknya
   * lewat form Ubah Data Santri) supaya ikut ke akun WALI YANG SUDAH ADA ($id_user_tujuan), bukan
   * bikin/ubah akunnya sendiri jadi duplikat username. Akun lamanya (kalau ada & jadi tidak punya
   * anak sama sekali setelah ini) ikut dibersihkan - sama seperti pembersihan di deleteSantri().
   */
  public function hubungkanKeAkunWaliLain($id_siswa, $id_user_tujuan)
  {
    $akun_lama = $this->getLoginWaliUntukSiswa($id_siswa);

    $this->db->where('IdSiswa', $id_siswa);
    $this->db->delete('wali_siswa');

    if ($akun_lama && (int) $akun_lama['IdUser'] !== (int) $id_user_tujuan) {
      $sisa_anak = $this->db->where('IdUser', $akun_lama['IdUser'])->count_all_results('wali_siswa');
      if ($sisa_anak === 0) {
        $this->db->where('IdUser', $akun_lama['IdUser']);
        $this->db->where('level', 'Wali');
        $this->db->delete('login');
      }
    }

    $this->hubungkanWaliSiswa($id_user_tujuan, $id_siswa);
  }

  public function deleteSantri($data)
  {
    $id_siswa = $data['IdSiswa'];

    // Hapus data terkait lebih dulu (SSOT: santri dihapus = seluruh jejaknya ikut hilang).
    if ($this->db->table_exists('absen')) {
      $this->db->where('JenisPeserta', 'santri');
      $this->db->where('IdPeserta', $id_siswa);
      $this->db->delete('absen');
    }

    // Cari akun wali-nya SEBELUM link-nya dihapus, supaya bisa dicek nanti apakah akun itu masih
    // punya anak lain (kalau masih, akun & anak lain itu jangan sampai ikut kehapus).
    $login = $this->getLoginWaliUntukSiswa($id_siswa);

    $this->db->where('IdSiswa', $id_siswa);
    $this->db->delete('wali_siswa');

    if ($login) {
      $sisa_anak = $this->db->where('IdUser', $login['IdUser'])->count_all_results('wali_siswa');
      if ($sisa_anak === 0) {
        $this->db->where('IdUser', $login['IdUser']);
        $this->db->where('level', 'Wali');
        $this->db->delete('login');
      }
    }

    $this->db->where('IdSiswa', $data['IdSiswa']);
    $this->db->delete('siswa', $data);
  }


  private function ensurePasfotoField()
  {
    if ($this->db->field_exists('Pasfoto', 'siswa')) {
      return;
    }

    $this->load->dbforge();
    $fields = [
      'Pasfoto' => [
        'type' => 'VARCHAR',
        'constraint' => 100,
        'null' => TRUE,
      ],
    ];
    $this->dbforge->add_column('siswa', $fields);
  }

  /**
   * Migrasi kolom Data Wajib/Opsional Formulir Santri. Mengikuti pola ensurePasfotoField():
   * dijalankan tiap request, hanya benar-benar ALTER TABLE kalau kolomnya belum ada.
   */
  private function ensureKolomTambahan()
  {
    $kolom_baru = [
      'JenisKelamin' => "ALTER TABLE siswa ADD COLUMN JenisKelamin ENUM('Laki-laki','Perempuan') NULL AFTER NamaLengkap",
      'TempatLahir' => "ALTER TABLE siswa ADD COLUMN TempatLahir VARCHAR(50) NULL AFTER JenisKelamin",
      'TanggalLahir' => "ALTER TABLE siswa ADD COLUMN TanggalLahir DATE NULL AFTER TempatLahir",
      'NamaAyah' => "ALTER TABLE siswa ADD COLUMN NamaAyah VARCHAR(50) NULL AFTER TanggalLahir",
      'NamaIbu' => "ALTER TABLE siswa ADD COLUMN NamaIbu VARCHAR(50) NULL AFTER NamaAyah",
      'Alamat' => "ALTER TABLE siswa ADD COLUMN Alamat TEXT NULL AFTER NamaIbu",
      'SekolahAkademik' => "ALTER TABLE siswa ADD COLUMN SekolahAkademik VARCHAR(100) NULL AFTER Alamat",
      'SekolahTadika' => "ALTER TABLE siswa ADD COLUMN SekolahTadika VARCHAR(100) NULL AFTER SekolahAkademik",
      'NoIDCard' => "ALTER TABLE siswa ADD COLUMN NoIDCard VARCHAR(30) NULL AFTER SekolahTadika",
      'TglMulaiBelajar' => "ALTER TABLE siswa ADD COLUMN TglMulaiBelajar DATE NULL AFTER NoIDCard",
      'TingkatPembelajaran' => "ALTER TABLE siswa ADD COLUMN TingkatPembelajaran ENUM('Pemula','Jilid 1','Jilid 2','Jilid 3','Jilid 4','Jilid 5','Al-Qur''an') NULL AFTER TglMulaiBelajar",
    ];

    foreach ($kolom_baru as $kolom => $sql) {
      if (!$this->db->field_exists($kolom, 'siswa')) {
        $this->db->query($sql);
      }
    }

    // Perluas pilihan Status sesuai ketentuan formulir baru (Aktif, Lulus, Pindah, Berhenti).
    $kolom_status = $this->db->query("SHOW COLUMNS FROM siswa WHERE Field = 'Status'")->row_array();
    if ($kolom_status && strpos($kolom_status['Type'], 'Pindah') === FALSE) {
      $this->db->query("ALTER TABLE siswa MODIFY COLUMN Status ENUM('Aktif','Lulus','Pindah','Berhenti') NOT NULL");
    }
  }
}

/* End of file Santri_M.php */
