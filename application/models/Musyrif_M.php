<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Musyrif_M extends CI_Model
{
  public function __construct()
  {
    parent::__construct();
    $this->ensureKolomTambahan();
  }

  public function getAllMusyrif()
  {
    $this->db->select('*');
    $this->db->from('musyrif');
    return $this->db->get()->result_array();
  }
  public function getMusyrifByNama($nama_musyrif)
  {
    $this->db->select('*');
    $this->db->from('musyrif');
    $this->db->like('NamaMusyrif', $nama_musyrif);
    return $this->db->get()->result_array();
  }

  // Selalu mengembalikan minimal baris login (tak pernah null utk username valid),
  // dan menambahkan profil musyrif (NamaMusyrif, Email, dst) kalau sudah tertaut lewat IdUser.
  public function getDataMusyrif($username)
  {
    $login = $this->db->get_where('login', ['username' => $username])->row_array();

    if (!$login) {
      return null;
    }

    if (!empty($login['IdUser'])) {
      $musyrif = $this->db->get_where('musyrif', ['IdUser' => $login['IdUser']])->row_array();
      if ($musyrif) {
        return array_merge($login, $musyrif);
      }
    }

    return $login;
  }

  public function addLoginMusyrif($data_login)
  {
    $this->db->insert('login', $data_login);
    return $this->db->insert_id();
  }

  public function getMusyrifById($id)
  {
    return $this->db->get_where('musyrif', ['IdMusyrif' => $id])->row_array();
  }

  public function cariLoginByUsername($username)
  {
    return $this->db->get_where('login', ['username' => $username])->row_array();
  }

  // Musyrif lama (dibuat sebelum kolom musyrif.IdUser ada) tidak pernah tertaut ke akun
  // login-nya - dipanggil begitu ketauan IdUser-nya kosong, supaya update berikutnya tidak
  // diam-diam melewati sinkronisasi ke tabel login lagi.
  public function tautkanIdUserMusyrif($id_musyrif, $id_user)
  {
    $this->db->where('IdMusyrif', $id_musyrif);
    $this->db->update('musyrif', ['IdUser' => $id_user]);
  }

  // Dicek sebelum email diubah lewat form Ubah Data Musyrif - tabel login dipakai bersama semua
  // peran (Admin/Wali/Musyrif/Santri), jadi email baru itu harus dipastikan belum dipakai akun
  // manapun (selain akun musyrif ini sendiri), bukan cuma dicek ke sesama musyrif.
  public function isEmailDipakaiLoginLain($email, $id_user_sendiri = null)
  {
    $this->db->where('username', $email);
    if ($id_user_sendiri !== null) {
      $this->db->where('IdUser !=', $id_user_sendiri);
    }
    return (bool) $this->db->get('login')->row_array();
  }

  // Menyamakan akun login (dipakai buat masuk & muncul di Pengaturan User) begitu Email/Password
  // musyrif diubah lewat form admin - sebelumnya update() cuma menimpa tabel musyrif sendiri,
  // jadi email/password di tabel login jadi basi/tidak sinkron dengan yang ditampilkan di sini.
  public function updateLoginMusyrif($id_user, $data_login)
  {
    $this->db->where('IdUser', $id_user);
    $this->db->update('login', $data_login);
  }

  public function addMusyrif($data)
  {
    $this->db->insert('musyrif', $this->filterData('musyrif', $data));
    return $this->db->affected_rows() > 0;
  }

  public function updateMusyrif($data)
  {
    $this->db->where('IdMusyrif', $data['IdMusyrif']);
    unset($data['IdMusyrif']);
    $this->db->update('musyrif', $this->filterData('musyrif', $data));
  }

  public function deleteMusyrif($data)
  {
    $this->db->where('IdMusyrif', $data['IdMusyrif']);
    $this->db->delete('musyrif', $data);
  }

  public function importMusyrif($data)
  {
    $jumlah = count($data);
    if ($jumlah > 0) {
      for ($i = 0; $i < $jumlah; $i++) {
        $this->db->insert('musyrif', $this->filterData('musyrif', $data[$i]));
      }
    }
  }

  private function filterData($table, $data)
  {
    $fields = $this->db->list_fields($table);
    return array_intersect_key($data, array_flip($fields));
  }

  /**
   * Migrasi skema: dulu kolom `login`.`level` ENUM tidak punya pilihan "Musyrif"/"Guru"
   * (jadi kalau ada pembimbing baru dibuat, levelnya diam-diam berubah jadi kosong),
   * dan tabel `musyrif` tidak punya `IdUser` untuk menautkan ke akun login-nya.
   * Method ini memastikan keduanya benar setiap kali model ini dipakai.
   */
  private function ensureKolomTambahan()
  {
    if (!$this->db->field_exists('IdUser', 'musyrif')) {
      $this->db->query('ALTER TABLE musyrif ADD COLUMN IdUser INT(11) NULL AFTER IdMusyrif');
    }

    if (!$this->db->field_exists('Pasfoto', 'musyrif')) {
      $this->db->query('ALTER TABLE musyrif ADD COLUMN Pasfoto VARCHAR(100) NULL AFTER NoHp');
    }

    $kolom_level = $this->db->query("SHOW COLUMNS FROM login WHERE Field = 'level'")->row_array();
    if ($kolom_level && strpos($kolom_level['Type'], 'Musyrif') === FALSE) {
      $this->db->query("ALTER TABLE login MODIFY COLUMN level ENUM('Admin','Wali','Bagian Administrasi','Musyrif','Guru') NOT NULL");
    }
  }
}

/* End of file Musyrif_M.php */
