<?php
defined('BASEPATH') or exit('No direct script access allowed');
// panggil autoload Spout
require_once APPPATH . 'third_party/Spout/Autoloader/autoload.php';

// Pakai reader/writer Spout
use Box\Spout\Reader\Common\Creator\ReaderEntityFactory;
use Box\Spout\Writer\Common\Creator\WriterEntityFactory;

class Santri extends CI_Controller
{
  private $status_diizinkan = ['Aktif', 'Lulus', 'Pindah', 'Berhenti'];
  private $jenis_kelamin_diizinkan = ['Laki-laki', 'Perempuan'];

  // Diisi sebelum validasi berjalan pada mode ubah, dipakai callback unik-kecuali-diri-sendiri.
  private $editing_id = null;

  public function __construct()
  {
    parent::__construct();
    cek_login();
    $this->load->model('Santri_M');
    $this->load->model('Kelas_M');
    $this->load->model('Pendaftaran_M');
  }

  // List semua santri
  public function index()
  {
    $pesan = $this->input->get('pesan') ?: $this->session->flashdata('pesan');

    $data = [
      'title' => 'Data Santri',
      'user' => $this->getUserLogin(),
      'santri' => $this->Santri_M->getAllSantri(),
      'kelas' => $this->Kelas_M->getAllKelasLengkap(),
      'pesan' => $pesan,
      'isi' => tampilan_mobile() ? 'santri/mobile-index' : 'santri/index',
    ];

    $this->load->view(tampilan_mobile() ? 'templates/wrapper-mobile-simple' : 'templates/wrapper-admin', $data);
  }

  // Halaman pemulihan darurat: sambungkan santri "yatim" (kelasnya sudah terlanjur dihapus) ke
  // satu Kelas pilihan sekaligus, tanpa perlu ubah satu-satu. Dibuat karena kejadian nyata admin
  // tidak sadar sebuah Kelas masih ada santrinya saat menghapusnya - Kelas::delete() sekarang
  // sudah mencegah ini terulang ke depannya, tapi santri yang SUDAH terlanjur yatim dari kejadian
  // sebelumnya masih perlu disambungkan ulang secara manual lewat halaman ini.
  public function sambungkan_kelas()
  {
    cek_level(['Admin', 'Bagian Administrasi']);

    $pesan = $this->input->get('pesan') ?: $this->session->flashdata('pesan');

    $data = [
      'title' => 'Sambungkan Kelas Santri',
      'user' => $this->getUserLogin(),
      'santri_yatim' => $this->Santri_M->getSantriYatim(),
      'kelas' => $this->Kelas_M->getAllKelasLengkap(),
      'pesan' => $pesan,
      'isi' => tampilan_mobile() ? 'santri/mobile-sambungkan_kelas' : 'santri/v-sambungkan_kelas',
    ];

    $this->load->view(tampilan_mobile() ? 'templates/wrapper-mobile-simple' : 'templates/wrapper-admin', $data);
  }

  public function proses_sambungkan_kelas()
  {
    cek_level(['Admin', 'Bagian Administrasi']);

    $daftar_id_siswa = $this->input->post('id_siswa');
    $id_kelas_baru = $this->input->post('kelas');

    if (!is_array($daftar_id_siswa) || empty($daftar_id_siswa) || !$id_kelas_baru) {
      redirect('santri/sambungkan_kelas?pesan=' . rawurlencode('Pilih minimal satu santri dan satu kelas tujuan.'));
      return;
    }

    $jumlah = $this->Santri_M->pindahkanKelasBatch($daftar_id_siswa, $id_kelas_baru);
    redirect('santri/sambungkan_kelas?pesan=' . rawurlencode("Berhasil menyambungkan {$jumlah} santri ke kelas yang dipilih."));
  }

  // Daftar pendaftaran publik (via Google) yang menunggu persetujuan admin
  public function pendaftaran()
  {
    $pesan = $this->input->get('pesan') ?: $this->session->flashdata('pesan');

    $data = [
      'title' => 'Pendaftaran Santri Baru',
      'user' => $this->getUserLogin(),
      'pendaftaran' => $this->Pendaftaran_M->getMenunggu(),
      'pesan' => $pesan,
      'isi' => tampilan_mobile() ? 'santri/mobile-pendaftaran' : 'santri/pendaftaran',
    ];

    $this->load->view(tampilan_mobile() ? 'templates/wrapper-mobile-simple' : 'templates/wrapper-admin', $data);
  }

  // Setujui pendaftaran: pindahkan data dari staging (pendaftaran_santri) ke siswa+login, SSOT tetap satu sumber.
  public function setujui_pendaftaran($id)
  {
    $daftar = $this->Pendaftaran_M->getById($id);

    if (!$daftar || $daftar['StatusPendaftaran'] !== 'Menunggu') {
      redirect('santri/pendaftaran?pesan=' . rawurlencode('Pendaftaran tidak ditemukan atau sudah diproses.'));
      return;
    }

    $data = [
      'NIS' => $this->Santri_M->generateNis(),
      'NamaLengkap' => $daftar['NamaLengkap'],
      'JenisKelamin' => $daftar['JenisKelamin'],
      'TempatLahir' => $daftar['TempatLahir'],
      'TanggalLahir' => $daftar['TanggalLahir'],
      'Status' => $daftar['Status'],
      'IdKelas' => $daftar['IdKelas'],
      'Pasfoto' => $daftar['Pasfoto'],
      'NamaAyah' => $daftar['NamaAyah'],
      'NamaIbu' => $daftar['NamaIbu'],
      'Alamat' => $daftar['Alamat'],
      'SekolahAkademik' => $daftar['SekolahAkademik'],
      'SekolahTadika' => $daftar['SekolahTadika'],
      'NoIDCard' => $daftar['NoIDCard'],
      // Kalau tidak dipilih saat daftar, pakai tanggal santri mendaftar (bukan tanggal disetujui) sebagai Tanggal Mulai Belajar.
      'TglMulaiBelajar' => !empty($daftar['TglMulaiBelajar']) ? $daftar['TglMulaiBelajar'] : substr($daftar['CreatedAt'], 0, 10),
    ];

    $IdSiswa = $this->Santri_M->addSantri($data);
    // Kalau email ini sudah dipakai akun wali yang ada (mis. kakak/adiknya), anak baru ini
    // disambungkan ke akun itu - BUKAN bikin akun terpisah lagi (itu yang sebelumnya menyebabkan
    // "Akun Wali Ganda"). Password dari pendaftaran ini sengaja diabaikan kalau akunnya sudah ada -
    // pendaftaran publik tidak lagi diblokir gara-gara email sudah terdaftar/password beda.
    $this->Santri_M->tautkanAtauBuatAkunWaliDariPendaftaran([
      'IdSiswa' => $IdSiswa,
      'username' => $daftar['Email'],
      'password' => $daftar['Password'],
      'level' => 'Wali',
    ]);

    $this->Pendaftaran_M->tandaiDiproses($id, 'Disetujui', $this->session->userdata('username'));

    redirect('santri/pendaftaran?pesan=' . rawurlencode('Pendaftaran "' . $daftar['NamaLengkap'] . '" disetujui, NIS: ' . $data['NIS'] . '.'));
  }

  public function tolak_pendaftaran($id)
  {
    $daftar = $this->Pendaftaran_M->getById($id);

    if (!$daftar || $daftar['StatusPendaftaran'] !== 'Menunggu') {
      redirect('santri/pendaftaran?pesan=' . rawurlencode('Pendaftaran tidak ditemukan atau sudah diproses.'));
      return;
    }

    $this->Pendaftaran_M->tandaiDiproses($id, 'Ditolak', $this->session->userdata('username'));
    redirect('santri/pendaftaran?pesan=' . rawurlencode('Pendaftaran "' . $daftar['NamaLengkap'] . '" ditolak.'));
  }

  // Form tambah data santri (Data Wajib + Data Opsional)
  public function tambah()
  {
    $data = [
      'title' => 'Tambah Data Santri',
      'user' => $this->getUserLogin(),
      'mode' => 'tambah',
      'santri' => $this->formKosong(),
      'nis_baru' => $this->Santri_M->generateNis(),
      'kelas' => $this->Kelas_M->getAllKelasLengkap(),
      'jenis_kelamin_list' => $this->jenis_kelamin_diizinkan,
      'isi' => tampilan_mobile() ? 'santri/mobile-form' : 'santri/form',
    ];

    $this->load->view(tampilan_mobile() ? 'templates/wrapper-mobile-simple' : 'templates/wrapper-admin', $data);
  }

  // Simpan data santri baru
  public function simpan()
  {
    $this->aturValidasi('tambah');

    if ($this->form_validation->run() == FALSE) {
      $data = [
        'title' => 'Tambah Data Santri',
        'user' => $this->getUserLogin(),
        'mode' => 'tambah',
        'santri' => $this->formDariPost(),
        'nis_baru' => $this->input->post('nis'),
        'kelas' => $this->Kelas_M->getAllKelasLengkap(),
        'jenis_kelamin_list' => $this->jenis_kelamin_diizinkan,
        'isi' => tampilan_mobile() ? 'santri/mobile-form' : 'santri/form',
      ];

      $this->load->view(tampilan_mobile() ? 'templates/wrapper-mobile-simple' : 'templates/wrapper-admin', $data);
      return;
    }

    // NIS otomatis dan tidak boleh diedit manual: buat ulang di server, abaikan kiriman klien.
    $nis = $this->Santri_M->generateNis();
    $pasfoto = $this->uploadPasfoto('pasfoto', 'santri/tambah');

    $data = [
      'NIS' => $nis,
      'NamaLengkap' => $this->input->post('nama'),
      'JenisKelamin' => $this->input->post('jenis_kelamin'),
      'TempatLahir' => $this->input->post('tempat_lahir'),
      'TanggalLahir' => $this->input->post('tanggal_lahir'),
      // Status tidak lagi diisi lewat formulir - santri baru selalu Aktif, diubah nanti dari halaman Data Santri.
      'Status' => 'Aktif',
      'IdKelas' => $this->input->post('kelas'),
      'Pasfoto' => $pasfoto,
      'NamaAyah' => $this->kosongkanJadiNull($this->input->post('nama_ayah')),
      'NamaIbu' => $this->kosongkanJadiNull($this->input->post('nama_ibu')),
      'Alamat' => $this->kosongkanJadiNull($this->input->post('alamat')),
      'SekolahAkademik' => $this->kosongkanJadiNull($this->input->post('sekolah_akademik')),
      'SekolahTadika' => $this->kosongkanJadiNull($this->input->post('sekolah_tadika')),
      'NoIDCard' => $this->kosongkanJadiNull($this->input->post('no_id_card')),
      // Kalau tidak dipilih, pakai tanggal daftar (hari ini) sebagai Tanggal Mulai Belajar.
      'TglMulaiBelajar' => $this->kosongkanJadiTanggalHariIni($this->input->post('tanggal_mulai_belajar')),
    ];

    $IdSiswa = $this->Santri_M->addSantri($data);

    $password_mentah = $this->input->post('password');
    $dataWali = [
      'IdSiswa' => $IdSiswa,
      'username' => $this->input->post('email'),
      'password' => password_hash($password_mentah, PASSWORD_DEFAULT),
      'level' => 'Wali',
    ];
    // Kalau email & password-nya cocok dengan akun wali yang sudah ada (mis. adik dari santri
    // lain), santri ini disambungkan ke akun itu juga - bukan bikin akun terpisah lagi.
    $this->Santri_M->addOrHubungkanWaliSantri($dataWali, $password_mentah);

    if ($this->input->post('aksi') === 'tambah_baru') {
      redirect('santri/tambah?pesan=' . rawurlencode('Data santri "' . $data['NamaLengkap'] . '" berhasil disimpan! Silakan isi data berikutnya.'));
      return;
    }

    redirect('santri?pesan=' . rawurlencode('Data santri "' . $data['NamaLengkap'] . '" berhasil disimpan!'));
  }

  // Form ubah data santri
  public function ubah($id)
  {
    $santri = $this->Santri_M->getSantriDetail($id);

    if (!$santri) {
      redirect('santri?pesan=' . rawurlencode('Data santri tidak ditemukan!'));
    }

    $data = [
      'title' => 'Ubah Data Santri',
      'user' => $this->getUserLogin(),
      'mode' => 'ubah',
      'santri' => $santri,
      'nis_baru' => $santri['NIS'],
      'kelas' => $this->Kelas_M->getAllKelasLengkap(),
      'jenis_kelamin_list' => $this->jenis_kelamin_diizinkan,
      'isi' => tampilan_mobile() ? 'santri/mobile-form' : 'santri/form',
    ];

    $this->load->view(tampilan_mobile() ? 'templates/wrapper-mobile-simple' : 'templates/wrapper-admin', $data);
  }

  // Simpan perubahan data santri
  public function perbarui($id)
  {
    $santri_lama = $this->Santri_M->getSantriDetail($id);

    if (!$santri_lama) {
      redirect('santri?pesan=' . rawurlencode('Data santri tidak ditemukan!'));
    }

    $this->editing_id = $id;
    // Data lama (mis. hasil import Excel) mungkin belum punya akun login sama sekali;
    // dalam kasus itu password wajib diisi sekarang, seperti pada mode tambah.
    $this->aturValidasi($santri_lama['punya_login'] ? 'ubah' : 'tambah');

    if ($this->form_validation->run() == FALSE) {
      $santri_gagal = $this->formDariPost();
      $santri_gagal['IdSiswa'] = $id;
      $santri_gagal['Pasfoto'] = $santri_lama['Pasfoto'];

      $data = [
        'title' => 'Ubah Data Santri',
        'user' => $this->getUserLogin(),
        'mode' => 'ubah',
        'santri' => $santri_gagal,
        'nis_baru' => $santri_lama['NIS'],
        'kelas' => $this->Kelas_M->getAllKelasLengkap(),
        'jenis_kelamin_list' => $this->jenis_kelamin_diizinkan,
        'isi' => tampilan_mobile() ? 'santri/mobile-form' : 'santri/form',
      ];

      $this->load->view(tampilan_mobile() ? 'templates/wrapper-mobile-simple' : 'templates/wrapper-admin', $data);
      return;
    }

    $pasfoto = $this->uploadPasfoto('pasfoto', 'santri/ubah/' . $id);

    $data = [
      'IdSiswa' => $id,
      'NamaLengkap' => $this->input->post('nama'),
      'JenisKelamin' => $this->input->post('jenis_kelamin'),
      'TempatLahir' => $this->input->post('tempat_lahir'),
      'TanggalLahir' => $this->input->post('tanggal_lahir'),
      // Status sengaja tidak disentuh di sini - hanya bisa diubah lewat aksi khusus di halaman Data Santri.
      'IdKelas' => $this->input->post('kelas'),
      'NamaAyah' => $this->kosongkanJadiNull($this->input->post('nama_ayah')),
      'NamaIbu' => $this->kosongkanJadiNull($this->input->post('nama_ibu')),
      'Alamat' => $this->kosongkanJadiNull($this->input->post('alamat')),
      'SekolahAkademik' => $this->kosongkanJadiNull($this->input->post('sekolah_akademik')),
      'SekolahTadika' => $this->kosongkanJadiNull($this->input->post('sekolah_tadika')),
      'NoIDCard' => $this->kosongkanJadiNull($this->input->post('no_id_card')),
      'TglMulaiBelajar' => $this->kosongkanJadiNull($this->input->post('tanggal_mulai_belajar')),
    ];

    if ($pasfoto) {
      $data['Pasfoto'] = $pasfoto;
    }

    $this->Santri_M->updateSantri($data);

    // Email/password login santri: hanya admin yang boleh mengubah, password opsional (kosongkan = tidak berubah).
    $email_baru = $this->input->post('email');
    $akun_tujuan = $this->Santri_M->getLoginWaliByUsername($email_baru);
    $akun_sendiri = $this->Santri_M->getLoginWaliUntukSiswa($id);

    if ($akun_tujuan && (!$akun_sendiri || (int) $akun_tujuan['IdUser'] !== (int) $akun_sendiri['IdUser'])) {
      // Email diarahkan ke akun WALI LAIN yang sudah ada (password-nya sudah divalidasi cocok
      // lewat cek_email_unik()) - sambungkan ke akun itu, jangan bikin/ubah baris login sendiri.
      $this->Santri_M->hubungkanKeAkunWaliLain($id, $akun_tujuan['IdUser']);
    } else {
      $dataLogin = ['username' => $email_baru];
      if ($this->input->post('password')) {
        $dataLogin['password'] = password_hash($this->input->post('password'), PASSWORD_DEFAULT);
      }
      $this->Santri_M->updateLoginSantri($id, $dataLogin);
    }

    redirect('santri?pesan=' . rawurlencode('Data santri "' . $data['NamaLengkap'] . '" berhasil diubah!'));
  }

  // Lihat detail data santri (read-only)
  public function detail($id)
  {
    $santri = $this->Santri_M->getSantriDetail($id);

    if (!$santri) {
      redirect('santri?pesan=' . rawurlencode('Data santri tidak ditemukan!'));
    }

    $data = [
      'title' => 'Detail Data Santri',
      'user' => $this->getUserLogin(),
      'santri' => $santri,
      'isi' => tampilan_mobile() ? 'santri/mobile-detail' : 'santri/detail',
    ];

    $this->load->view(tampilan_mobile() ? 'templates/wrapper-mobile-simple' : 'templates/wrapper-admin', $data);
  }

  // Ubah status santri langsung dari halaman Data Santri (bukan lewat formulir Tambah/Ubah).
  public function ubah_status($id)
  {
    $status = $this->input->post('status');

    if (!in_array($status, $this->status_diizinkan, TRUE)) {
      redirect('santri?pesan=' . rawurlencode('Status tidak valid.'));
      return;
    }

    $this->Santri_M->updateSantri(['IdSiswa' => $id, 'Status' => $status]);
    redirect('santri?pesan=' . rawurlencode('Status santri berhasil diubah menjadi "' . $status . '".'));
  }

  //Delete one item
  public function delete($id)
  {
    $data = [
      'IdSiswa' => $id
    ];
    $this->Santri_M->deleteSantri($data);
    redirect('santri?pesan=' . rawurlencode('Berhasil dihapus!'));
  }

  /**
   * Import Excel dengan kolom sama persis dengan Formulir Tambah Data Santri
   * (Data Wajib + Data Opsional). NIS di file diabaikan - selalu dibuat otomatis,
   * konsisten dengan aturan "NIS tidak dapat diubah manual" pada form tambah.
   *
   * Urutan kolom (index 0-based, baris 1 = header). Status Santri tidak diimport - santri baru
   * selalu Aktif, diubah nanti dari halaman Data Santri.
   * 0:No 1:NIS(diabaikan) 2:Nama Lengkap 3:Jenis Kelamin 4:Tempat Lahir 5:Tanggal Lahir
   * 6:Email Login 7:Password 8:Kelas (format NamaKelas - Pembimbing)
   * 9:Nama Ayah 10:Nama Ibu 11:Alamat Lengkap 12:Sekolah Akademik 13:Sekolah Tadika
   * 14:Nomor ID Card 15:Tanggal Mulai Belajar
   */
  public function import()
  {
    $config['upload_path']    = upload_path('santri');
    if (!is_dir($config['upload_path'])) {
      mkdir($config['upload_path'], 0755, true);
    }
    $config['allowed_types']  = 'xls|xlsx';
    $config['file_name']       = 'data santri ' . time();
    $this->load->library('upload', $config);

    if (!$this->upload->do_upload('importSiswa')) {
      redirect('santri?pesan=' . rawurlencode('Import gagal: ' . strip_tags($this->upload->display_errors())));
      return;
    }

    $file = $this->upload->data();
    $reader = ReaderEntityFactory::createXLSXReader();
    $reader->open(upload_path('santri') . $file['file_name']);

    $berhasil = 0;
    $gagal = [];

    foreach ($reader->getSheetIterator() as $sheets) {
      $numRow = 1;
      foreach ($sheets->getRowIterator() as $row) {
        if ($numRow === 1) {
          $numRow++;
          continue;
        }

        $ambil = function ($index) use ($row) {
          $nilai = $row->getCellAtIndex($index);
          if ($nilai instanceof \DateTimeInterface) {
            return $nilai->format('Y-m-d');
          }
          return $nilai === null ? '' : trim((string) $nilai);
        };

        $nama = $ambil(2);
        if ($nama === '') {
          // Baris kosong (mis. baris terakhir file) dilewati diam-diam.
          $numRow++;
          continue;
        }

        $baris = [
          'nama' => $nama,
          'jenis_kelamin' => $ambil(3),
          'tempat_lahir' => $ambil(4),
          'tanggal_lahir' => $ambil(5),
          'email' => $ambil(6),
          'password' => $ambil(7),
          'kelas_label' => $ambil(8),
        ];

        $id_kelas = null;
        $error = $this->validasiBarisImport($numRow, $baris, $id_kelas);

        if ($error) {
          $gagal[] = $error;
          $numRow++;
          continue;
        }

        $data = [
          'NIS' => $this->Santri_M->generateNis(),
          'NamaLengkap' => $baris['nama'],
          'JenisKelamin' => $baris['jenis_kelamin'],
          'TempatLahir' => $baris['tempat_lahir'],
          'TanggalLahir' => $baris['tanggal_lahir'],
          'Status' => 'Aktif',
          'IdKelas' => $id_kelas,
          'NamaAyah' => $this->kosongkanJadiNull($ambil(9)),
          'NamaIbu' => $this->kosongkanJadiNull($ambil(10)),
          'Alamat' => $this->kosongkanJadiNull($ambil(11)),
          'SekolahAkademik' => $this->kosongkanJadiNull($ambil(12)),
          'SekolahTadika' => $this->kosongkanJadiNull($ambil(13)),
          'NoIDCard' => $this->kosongkanJadiNull($ambil(14)),
          'TglMulaiBelajar' => $this->kosongkanJadiTanggalHariIni($ambil(15)),
        ];

        $IdSiswa = $this->Santri_M->addSantri($data);
        $this->Santri_M->addOrHubungkanWaliSantri([
          'IdSiswa' => $IdSiswa,
          'username' => $baris['email'],
          'password' => password_hash($baris['password'], PASSWORD_DEFAULT),
          'level' => 'Wali',
        ], $baris['password']);

        $berhasil++;
        $numRow++;
      }
      $reader->close();
    }

    $pesan = $berhasil . ' data santri berhasil diimport.';
    if ($gagal) {
      $pesan .= ' ' . count($gagal) . ' baris dilewati -> ' . implode(' | ', $gagal);
    }
    redirect('santri?pesan=' . rawurlencode($pesan));
  }

  private function validasiBarisImport($nomor_baris, $data, &$id_kelas)
  {
    if (strlen($data['nama']) < 3) {
      return "Baris {$nomor_baris}: Nama Lengkap minimal 3 karakter.";
    }
    if (!in_array($data['jenis_kelamin'], $this->jenis_kelamin_diizinkan, TRUE)) {
      return "Baris {$nomor_baris}: Jenis Kelamin harus Laki-laki/Perempuan.";
    }
    if ($data['tempat_lahir'] === '') {
      return "Baris {$nomor_baris}: Tempat Lahir wajib diisi.";
    }
    if (!$this->tanggalValid($data['tanggal_lahir'])) {
      return "Baris {$nomor_baris}: Tanggal Lahir tidak valid (format YYYY-MM-DD).";
    }
    if ($data['tanggal_lahir'] > date('Y-m-d')) {
      return "Baris {$nomor_baris}: Tanggal Lahir tidak boleh setelah hari ini.";
    }
    if ($data['email'] === '' || !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
      return "Baris {$nomor_baris}: Email Login tidak valid.";
    }
    // Email yang sama boleh dipakai lagi (disambungkan sebagai anak dari akun yang sama - lihat
    // Santri_M::addOrHubungkanWaliSantri()) SELAMA passwordnya cocok dengan akun yang sudah ada.
    $login_terpakai = $this->Santri_M->getLoginWaliByUsername($data['email']);
    if ($login_terpakai && !password_verify($data['password'], $login_terpakai['password'])) {
      return "Baris {$nomor_baris}: Email '{$data['email']}' sudah terdaftar dengan password berbeda.";
    }
    if (strlen($data['password']) < 8) {
      return "Baris {$nomor_baris}: Password minimal 8 karakter.";
    }

    $id_kelas = $this->Santri_M->cariIdKelas($data['kelas_label']);
    if (!$id_kelas) {
      return "Baris {$nomor_baris}: Kelas '{$data['kelas_label']}' tidak ditemukan (format: NamaKelas - Pembimbing).";
    }

    return null;
  }

  private function tanggalValid($value)
  {
    if ($value === '' || $value === null) {
      return FALSE;
    }

    $tanggal = DateTime::createFromFormat('Y-m-d', $value);
    return $tanggal && $tanggal->format('Y-m-d') === $value;
  }

  /**
   * Export .xlsx asli (bukan HTML berkedok .xls) supaya bisa langsung dipakai ulang
   * sebagai file import (kolom persis sama dengan Formulir Tambah Data Santri).
   */
  public function export_excel()
  {
    $santri = $this->Santri_M->getAllSantriLengkap();

    $header = [
      'No', 'NIS', 'Nama Lengkap', 'Jenis Kelamin', 'Tempat Lahir', 'Tanggal Lahir',
      'Email Login', 'Password', 'Kelas',
      'Nama Ayah', 'Nama Ibu', 'Alamat Lengkap', 'Sekolah Akademik', 'Sekolah Tadika',
      'Nomor ID Card', 'Tanggal Mulai Belajar',
    ];

    $writer = WriterEntityFactory::createXLSXWriter();
    $writer->openToBrowser('Data Santri.xlsx');
    $writer->addRow(WriterEntityFactory::createRowFromArray($header));

    $no = 1;
    foreach ($santri as $s) {
      $label_kelas = !empty($s['NamaKelas']) ? $s['NamaKelas'] . (!empty($s['NamaPembimbingKelas']) ? ' - ' . $s['NamaPembimbingKelas'] : '') : '';

      $writer->addRow(WriterEntityFactory::createRowFromArray([
        $no++,
        (string) $s['NIS'],
        (string) $s['NamaLengkap'],
        (string) $s['JenisKelamin'],
        (string) $s['TempatLahir'],
        (string) $s['TanggalLahir'],
        (string) $s['Email'],
        '', // Password tidak pernah diekspor (hash tak bisa dipakai ulang); isi manual jika untuk import ulang.
        $label_kelas,
        (string) $s['NamaAyah'],
        (string) $s['NamaIbu'],
        (string) $s['Alamat'],
        (string) $s['SekolahAkademik'],
        (string) $s['SekolahTadika'],
        (string) $s['NoIDCard'],
        (string) $s['TglMulaiBelajar'],
      ]));
    }

    $writer->close();
  }

  // Cari data santri
  public function cari_data()
  {
    $nama_santri = $this->input->post('nama_santri');
    $data = [
      'title' => 'Data Santri',
      'user' => $this->getUserLogin(),
      'santri' => $this->Santri_M->getSantriByNama($nama_santri),
      'kelas' => $this->Kelas_M->getAllKelasLengkap(),
      'isi' => tampilan_mobile() ? 'santri/mobile-index' : 'santri/index',
    ];

    $this->load->view(tampilan_mobile() ? 'templates/wrapper-mobile-simple' : 'templates/wrapper-admin', $data);
  }

  /**
   * Validasi form Data Wajib + Data Opsional. Sama untuk tambah & ubah,
   * kecuali keharusan Password (wajib saat tambah, opsional saat ubah).
   */
  private function aturValidasi($mode)
  {
    $pesan_wajib = ['required' => 'Form %s wajib diisi !'];

    $this->form_validation->set_rules('nama', 'Nama Lengkap', 'trim|required|min_length[3]', $pesan_wajib + [
      'min_length' => '%s minimal 3 karakter',
    ]);
    $this->form_validation->set_rules('jenis_kelamin', 'Jenis Kelamin', 'trim|required|in_list[' . implode(',', $this->jenis_kelamin_diizinkan) . ']', $pesan_wajib);
    $this->form_validation->set_rules('tempat_lahir', 'Tempat Lahir', 'trim|required', $pesan_wajib);
    $this->form_validation->set_rules('tanggal_lahir', 'Tanggal Lahir', 'trim|required|callback_cek_tanggal_lahir', $pesan_wajib);
    $this->form_validation->set_rules('kelas', 'Kelas', 'trim|required', $pesan_wajib);

    $aturan_email = 'trim|required|valid_email|callback_cek_email_unik';
    $this->form_validation->set_rules('email', 'Email Login', $aturan_email, $pesan_wajib + [
      'valid_email' => 'Mohon gunakan format email yang valid',
    ]);

    if ($mode === 'tambah') {
      $this->form_validation->set_rules('password', 'Password', 'trim|required|min_length[8]', $pesan_wajib + [
        'min_length' => '%s minimal 8 karakter',
      ]);
    } else {
      // Saat ubah, password boleh dikosongkan (artinya tidak diganti).
      $this->form_validation->set_rules('password', 'Password', 'trim|min_length[8]', [
        'min_length' => '%s minimal 8 karakter',
      ]);
    }

    // Data Opsional
    $this->form_validation->set_rules('nama_ayah', 'Nama Ayah', 'trim');
    $this->form_validation->set_rules('nama_ibu', 'Nama Ibu', 'trim');
    $this->form_validation->set_rules('alamat', 'Alamat Lengkap', 'trim');
    $this->form_validation->set_rules('sekolah_akademik', 'Sekolah Akademik', 'trim');
    $this->form_validation->set_rules('sekolah_tadika', 'Sekolah Tadika', 'trim');
    $this->form_validation->set_rules('no_id_card', 'Nomor ID Card', 'trim|callback_cek_idcard_unik');
    $this->form_validation->set_rules('tanggal_mulai_belajar', 'Tanggal Mulai Belajar', 'trim|callback_cek_tanggal');
  }

  public function cek_email_unik($email)
  {
    $existing = $this->Santri_M->getLoginWaliByUsername($email);

    if (!$existing) {
      return TRUE;
    }

    // Mode ubah & email yang diisi memang masih akun sendiri (belum ganti) - selalu boleh.
    if ($this->editing_id !== null) {
      $akun_sendiri = $this->Santri_M->getLoginWaliUntukSiswa($this->editing_id);
      if ($akun_sendiri && (int) $akun_sendiri['IdUser'] === (int) $existing['IdUser']) {
        return TRUE;
      }
    }

    // Email ini milik akun WALI LAIN (mis. kakak/adiknya). Boleh disambungkan ke akun itu -
    // baik lewat Tambah Santri baru maupun Ubah Data Santri - TAPI hanya kalau password yang
    // diisi memang benar password akun itu, bukan sekadar mengklaim. Lihat Santri_M::
    // addOrHubungkanWaliSantri()/hubungkanKeAkunWaliLain() untuk logika penyambungannya.
    if (!password_verify((string) $this->input->post('password'), $existing['password'])) {
      $this->form_validation->set_message('cek_email_unik', '%s sudah terdaftar dengan password berbeda. Untuk menyambungkan sebagai anak dari akun yang sama, gunakan password akun tersebut.');
      return FALSE;
    }

    return TRUE;
  }

  public function cek_idcard_unik($value)
  {
    if ($value === '' || $value === null) {
      return TRUE;
    }

    if ($this->Santri_M->isIdCardTerpakai($value, $this->editing_id)) {
      $this->form_validation->set_message('cek_idcard_unik', '%s sudah digunakan santri lain.');
      return FALSE;
    }

    return TRUE;
  }

  public function cek_tanggal($value)
  {
    if ($value === '' || $value === null) {
      return TRUE;
    }

    $tanggal = DateTime::createFromFormat('Y-m-d', $value);
    if (!$tanggal || $tanggal->format('Y-m-d') !== $value) {
      $this->form_validation->set_message('cek_tanggal', '%s bukan tanggal yang valid.');
      return FALSE;
    }

    return TRUE;
  }

  // Sama seperti cek_tanggal(), tapi khusus Tanggal Lahir: tidak boleh tanggal di masa depan.
  public function cek_tanggal_lahir($value)
  {
    if ($value === '' || $value === null) {
      return TRUE;
    }

    $tanggal = DateTime::createFromFormat('Y-m-d', $value);
    if (!$tanggal || $tanggal->format('Y-m-d') !== $value) {
      $this->form_validation->set_message('cek_tanggal_lahir', '%s bukan tanggal yang valid.');
      return FALSE;
    }

    if ($value > date('Y-m-d')) {
      $this->form_validation->set_message('cek_tanggal_lahir', '%s tidak boleh setelah hari ini.');
      return FALSE;
    }

    return TRUE;
  }

  private function kosongkanJadiNull($value)
  {
    return ($value === null || $value === '') ? null : $value;
  }

  // Kalau Tanggal Mulai Belajar tidak diisi, dianggap sama dengan tanggal daftar (hari ini).
  private function kosongkanJadiTanggalHariIni($value)
  {
    return ($value === null || $value === '') ? date('Y-m-d') : $value;
  }

  private function formKosong()
  {
    return [
      'IdSiswa' => null,
      'NIS' => '',
      'NamaLengkap' => '',
      'JenisKelamin' => '',
      'TempatLahir' => '',
      'TanggalLahir' => '',
      'IdKelas' => '',
      'Pasfoto' => '',
      'NamaAyah' => '',
      'NamaIbu' => '',
      'Alamat' => '',
      'SekolahAkademik' => '',
      'SekolahTadika' => '',
      'NoIDCard' => '',
      'TglMulaiBelajar' => '',
      'login' => ['username' => ''],
    ];
  }

  // Dipakai untuk mengembalikan input yang sudah diisi user ketika validasi gagal, supaya tidak perlu isi ulang.
  private function formDariPost()
  {
    return [
      'IdSiswa' => null,
      'NIS' => $this->input->post('nis'),
      'NamaLengkap' => $this->input->post('nama'),
      'JenisKelamin' => $this->input->post('jenis_kelamin'),
      'TempatLahir' => $this->input->post('tempat_lahir'),
      'TanggalLahir' => $this->input->post('tanggal_lahir'),
      'IdKelas' => $this->input->post('kelas'),
      'Pasfoto' => '',
      'NamaAyah' => $this->input->post('nama_ayah'),
      'NamaIbu' => $this->input->post('nama_ibu'),
      'Alamat' => $this->input->post('alamat'),
      'SekolahAkademik' => $this->input->post('sekolah_akademik'),
      'SekolahTadika' => $this->input->post('sekolah_tadika'),
      'NoIDCard' => $this->input->post('no_id_card'),
      'TglMulaiBelajar' => $this->input->post('tanggal_mulai_belajar'),
      'login' => ['username' => $this->input->post('email')],
    ];
  }

  // $redirect_gagal: halaman untuk kembali kalau upload gagal (mis. foto dari HP lebih dari batas
  // ukuran) - beda antara Tambah ('santri/tambah') dan Ubah ('santri/ubah/<id>'). redirect() di
  // sini langsung exit (perilaku CI3), jadi pemanggilnya aman tidak lanjut simpan data lalu
  // bilang "berhasil" padahal fotonya sendiri tidak tersimpan.
  private function uploadPasfoto($field, $redirect_gagal = 'santri')
  {
    if (empty($_FILES[$field]['name'])) {
      return '';
    }

    $config['upload_path']   = upload_path('santri');
    if (!is_dir($config['upload_path'])) {
      mkdir($config['upload_path'], 0755, true);
    }
    $config['allowed_types'] = 'jpg|jpeg|png';
    $config['max_size']      = '8192';
    $config['file_name']     = 'Pasfoto_' . time() . '_' . preg_replace('/[^a-zA-Z0-9]/', '', $_FILES[$field]['name']);

    $this->load->library('upload');
    $this->upload->initialize($config);

    if ($this->upload->do_upload($field)) {
      $nama_file = $this->upload->data('file_name');

      // Jarang terjadi, tapi pernah: do_upload() lapor berhasil padahal filenya sendiri ternyata
      // tidak benar-benar ada di server (mis. gangguan disk/permission) - lebih baik dianggap
      // gagal di sini daripada terlanjur menyimpan nama file yang ujung-ujungnya jadi foto rusak
      // begitu ditampilkan nanti.
      if (!file_exists($config['upload_path'] . $nama_file)) {
        redirect($redirect_gagal . '?pesan=' . rawurlencode('Foto gagal tersimpan dengan benar di server, silakan coba upload ulang.'));
        return;
      }

      return $nama_file;
    }

    redirect($redirect_gagal . '?pesan=' . rawurlencode(strip_tags($this->upload->display_errors())));
  }

  private function getUserLogin()
  {
    return $this->db->get_where('login', ['username' => $this->session->userdata('username')])->row_array();
  }
}

/* End of file Santri.php */
