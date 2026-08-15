<?php
defined('BASEPATH') or exit('No direct script access allowed');
// panggil autoload Spout
require_once APPPATH . 'third_party/Spout/Autoloader/autoload.php';

// Pakai reader Spout
use Box\Spout\Reader\Common\Creator\ReaderEntityFactory;

class Musyrif extends CI_Controller
{

  public function __construct()
  {
    parent::__construct();
    //Load Dependencies
    cek_login();
    $this->load->model('Musyrif_M');
    $this->load->model('Kelas_M');
  }

  // List all your items
  public function index()
  {
    $this->form_validation->set_rules('nama_musyrif', 'Nama Musyrif', 'trim|required', [
      'required' => 'Form %s wajib diisi !'
    ]);
    $this->form_validation->set_rules('email', 'Email Musyrif', 'trim|required|valid_email|is_unique[musyrif.Email]', [
      'required' => 'Form %s wajib diisi !',
      'valid_email' => 'Mohon gunakan email yang valid',
      'is_unique' => '%s telah terdaftar dalam sistem'
    ]);
    $this->form_validation->set_rules('password', 'Password', 'trim|required|min_length[4]', [
      'required' => 'Form %s wajib diisi !',
      'min_length' => 'Panjang %s minimal 4 karakter'
    ]);
    $this->form_validation->set_rules('no_hp', 'No Handphone', 'trim|required|is_unique[musyrif.NoHp]', [
      'required' => 'Form %s wajib diisi !',
      'is_unique' => '%s telah terdaftar dalam sistem'
    ]);


    if ($this->form_validation->run() == FALSE) {
      // Jika validasi gagal
      $pesan = $this->input->get('pesan') ?: $this->session->flashdata('pesan');

      $data = [
        'title' => 'Data Musyrif',
        'user' => $this->db->get_where('login', ['username' => $this->session->userdata('username')])->row_array(),
        'musyrif' => $this->Musyrif_M->getAllMusyrif(),
        'pesan' => $pesan,
        'isi' => tampilan_mobile() ? 'musyrif/mobile-index' : 'musyrif/index',
      ];

      $this->load->view(tampilan_mobile() ? 'templates/wrapper-mobile-simple' : 'templates/wrapper-admin', $data);
    } else {
      // Jika validasi sukses
      $this->add();
    }
  }

  // Add a new item
  public function add()
  {
    $nama = $this->input->post('nama_musyrif');
    $email = $this->input->post('email');
    $no_hp = $this->input->post('no_hp');
    $tanda_tangan = $_FILES['ttd']['name'];
    $file_ttd = '';

    if ($tanda_tangan) {
      $namafile                = "TTD_" . $nama;
      $config['file_name']     = $namafile;
      $config['upload_path']   = upload_path('ttd_musyrif');
      if (!is_dir($config['upload_path'])) {
        mkdir($config['upload_path'], 0755, true);
      }
      $config['allowed_types'] = 'jpg|jpeg|png';
      $config['max_size']      = '8192';
      $config['overwrite']     = true;

      $this->load->library('upload', $config);
      if ($this->upload->do_upload('ttd')) {
        $file_ttd = $this->upload->data('file_name');

        if (!file_exists($config['upload_path'] . $file_ttd)) {
          redirect('musyrif?pesan=' . rawurlencode('Tanda tangan gagal tersimpan dengan benar di server, silakan coba upload ulang.'));
        }
      } else {
        redirect('musyrif?pesan=' . rawurlencode(strip_tags($this->upload->display_errors())));
      }
    }

    $file_pasfoto = $this->uploadPasfoto($nama);

    $data_login = [
      'username' => $email,
      'password' => password_hash($this->input->post('password'), PASSWORD_DEFAULT),
      'level' => 'Musyrif',
    ];
    $Id_User = $this->Musyrif_M->addLoginMusyrif($data_login);

    $data = [
      'IdUser' => $Id_User,
      'NamaMusyrif' => $nama,
      'Email' => $email,
      'NoHp' => $no_hp,
      'Ttd' => $file_ttd,
      'Pasfoto' => $file_pasfoto,
    ];

    $pesan = $this->Musyrif_M->addMusyrif($data) ? 'Berhasil ditambah!' : 'Data pembimbing gagal ditambahkan!';
    redirect('musyrif?pesan=' . rawurlencode($pesan));
  }

  private function uploadPasfoto($nama)
  {
    if (empty($_FILES['pasfoto']['name'])) {
      return '';
    }

    $config['file_name']     = 'Pasfoto_' . $nama . '_' . time();
    $config['upload_path']   = upload_path('pasfoto_musyrif');
    if (!is_dir($config['upload_path'])) {
      mkdir($config['upload_path'], 0755, true);
    }
    $config['allowed_types'] = 'jpg|jpeg|png';
    $config['max_size']      = '8192';
    $config['overwrite']     = true;

    $this->load->library('upload');
    $this->upload->initialize($config);

    if ($this->upload->do_upload('pasfoto')) {
      $nama_file = $this->upload->data('file_name');

      // Jarang terjadi, tapi pernah: do_upload() lapor berhasil padahal filenya sendiri ternyata
      // tidak benar-benar ada di server - lebih baik dianggap gagal di sini daripada terlanjur
      // menyimpan nama file yang ujung-ujungnya jadi foto rusak begitu ditampilkan nanti.
      if (!file_exists($config['upload_path'] . $nama_file)) {
        redirect('musyrif?pesan=' . rawurlencode('Foto gagal tersimpan dengan benar di server, silakan coba upload ulang.'));
      }

      return $nama_file;
    }

    // Kalau upload gagal (mis. foto dari HP lebih dari batas ukuran), JANGAN lanjut simpan data
    // lain lalu bilang "berhasil" - redirect() di sini akan langsung exit (perilaku CI3), jadi
    // add()/update() aman tidak lanjut ke redirect sukses miliknya sendiri.
    redirect('musyrif?pesan=' . rawurlencode(strip_tags($this->upload->display_errors())));
  }

  //Update one item
  public function update($id)
  {
    $id_Musyrif = $id;
    $nama = $this->input->post('nama_musyrif');
    $email = $this->input->post('email');
    $no_hp = $this->input->post('no_hp');
    $password = $this->input->post('password');
    $tanda_tangan = $_FILES['ttd']['name'];

    $musyrif_sekarang = $this->Musyrif_M->getMusyrifById($id_Musyrif);
    if (!$musyrif_sekarang) {
      redirect('musyrif?pesan=' . rawurlencode('Data pembimbing tidak ditemukan.'));
      return;
    }

    // Musyrif lama (dibuat sebelum kolom musyrif.IdUser ada) belum tertaut ke akun login-nya -
    // coba sambungkan dulu lewat email yang SEKARANG dipakai (seharusnya sama dengan
    // login.username kalau selama ini masih bisa login), supaya sinkronisasi di bawah tidak
    // diam-diam terlewati gara-gara IdUser kosong.
    if (empty($musyrif_sekarang['IdUser'])) {
      $login_lama = $this->Musyrif_M->cariLoginByUsername($musyrif_sekarang['Email']);
      if ($login_lama) {
        $this->Musyrif_M->tautkanIdUserMusyrif($id_Musyrif, $login_lama['IdUser']);
        $musyrif_sekarang['IdUser'] = $login_lama['IdUser'];
      }
    }

    // Tabel login dipakai bersama semua peran - kalau email diubah, pastikan belum dipakai akun
    // lain sebelum ditimpa ke tabel musyrif maupun login.
    if ($email !== $musyrif_sekarang['Email']) {
      $id_user_kecuali = !empty($musyrif_sekarang['IdUser']) ? $musyrif_sekarang['IdUser'] : null;
      if ($this->Musyrif_M->isEmailDipakaiLoginLain($email, $id_user_kecuali)) {
        redirect('musyrif?pesan=' . rawurlencode('Email tersebut sudah dipakai akun lain.'));
        return;
      }
    }

    $data = [
      'IdMusyrif' => $id_Musyrif,
      'NamaMusyrif' => $nama,
      'Email' => $email,
      'NoHp' => $no_hp,
    ];

    $file_pasfoto = $this->uploadPasfoto($nama);
    if ($file_pasfoto) {
      $data['Pasfoto'] = $file_pasfoto;
    }

    if ($tanda_tangan) {
      $namafile                = "TTD_" . $nama;
      $config['file_name']     = $namafile;
      $config['upload_path']   = upload_path('ttd_musyrif');
      if (!is_dir($config['upload_path'])) {
        mkdir($config['upload_path'], 0755, true);
      }
      $config['allowed_types'] = 'jpg|jpeg|png';
      $config['max_size']      = '8192';
      $config['overwrite']     = true;

      $this->load->library('upload', $config);
      if ($this->upload->do_upload('ttd')) {
        $data['Ttd'] = $this->upload->data('file_name');

        if (!file_exists($config['upload_path'] . $data['Ttd'])) {
          redirect('musyrif?pesan=' . rawurlencode('Tanda tangan gagal tersimpan dengan benar di server, silakan coba upload ulang.'));
          return;
        }
      } else {
        redirect('musyrif?pesan=' . rawurlencode(strip_tags($this->upload->display_errors())));
        return;
      }
    }

    $this->Musyrif_M->updateMusyrif($data);

    // Kalau ternyata belum ada akun login sama sekali (bukan cuma belum tertaut) dan admin
    // mengisi Password Baru sekarang, buatkan akun login-nya sekalian - daripada terus gagal
    // diam-diam setiap kali diubah karena tidak ada IdUser untuk ditautkan.
    if (empty($musyrif_sekarang['IdUser']) && $password !== '' && $password !== null) {
      $id_user_baru = $this->Musyrif_M->addLoginMusyrif([
        'username' => $email,
        'password' => password_hash($password, PASSWORD_DEFAULT),
        'level' => 'Musyrif',
      ]);
      $this->Musyrif_M->tautkanIdUserMusyrif($id_Musyrif, $id_user_baru);
      $musyrif_sekarang['IdUser'] = $id_user_baru;
    }

    // Samakan juga akun login-nya (dipakai buat masuk & yang tampil di Pengaturan User) - kalau
    // tidak disamakan di sini, mengubah Email di form ini saja bikin data musyrif dan akun
    // login-nya jadi tidak sinkron (musyrif tampil email baru, tapi login masih pakai yang lama).
    if (!empty($musyrif_sekarang['IdUser'])) {
      $data_login = [];
      if ($email !== $musyrif_sekarang['Email']) {
        $data_login['username'] = $email;
      }
      if ($password !== '' && $password !== null) {
        $data_login['password'] = password_hash($password, PASSWORD_DEFAULT);
      }
      if ($data_login) {
        $this->Musyrif_M->updateLoginMusyrif($musyrif_sekarang['IdUser'], $data_login);
      }
    }

    redirect('musyrif?pesan=' . rawurlencode('Berhasil diubah!'));
  }

  //Delete one item
  public function delete($id)
  {
    // Kelas SSOT: pembimbing yang masih ditugaskan ke suatu kelas tidak boleh dihapus
    // sebelum kelas itu diberi pembimbing pengganti terlebih dahulu.
    if ($this->Kelas_M->isMusyrifJadiPembimbing($id)) {
      redirect('musyrif?pesan=' . rawurlencode('Pembimbing ini tidak dapat dihapus karena masih menjadi pembimbing utama sebuah kelas. Ganti pembimbing kelas tersebut terlebih dahulu sebelum menghapus.'));
      return;
    }

    $data = ['IdMusyrif' => $id];
    $this->Musyrif_M->deleteMusyrif($data);
    redirect('musyrif?pesan=' . rawurlencode('Berhasil dihapus!'));
  }

  public function import()
  {
    $config['upload_path']    = upload_path('musyrif');
    if (!is_dir($config['upload_path'])) {
      mkdir($config['upload_path'], 0755, true);
    }
    $config['allowed_types']  = 'xls|xlsx';
    $config['file_name']       = 'data musyrif ' . time();
    $this->load->library('upload', $config);
    if ($this->upload->do_upload('importMusyrif')) {
      $file = $this->upload->data();
      $reader = ReaderEntityFactory::createXLSXReader();

      // Baca file excel yang diupload
      $reader->open(upload_path('musyrif') . $file['file_name']);
      $save = array();
      foreach ($reader->getSheetIterator() as $sheets) {
        $numRow = 1;
        // Looping row dalam sheet
        foreach ($sheets->getRowIterator() as $row) {
          if ($numRow > 1) {
            $dataSiswa = array(
              'NamaMusyrif' => $row->getCellAtIndex(1),
              'Email'       => $row->getCellAtIndex(2),
              'NoHp'        => $row->getCellAtIndex(3),
            );
            array_push($save, $dataSiswa);
          }
          $numRow++;
        }
        $reader->close();
        $this->Musyrif_M->importMusyrif($save);
        redirect('musyrif?pesan=' . rawurlencode('Berhasil diimport!'));
      }
    } else {
      echo "Errors : " . $this->upload->display_errors();
    }
  }

  public function export_excel()
  {
    $data = [
      'title' => 'Data Musyrif',
      'user' => $this->db->get_where('login', ['username' => $this->session->userdata('username')])->row_array(),
      'musyrif' => $this->Musyrif_M->getAllMusyrif(),
      'isi' => 'musyrif/index',
    ];

    $this->load->view('export/excel/musyrif', $data);
  }

  public function export_pdf()
  {
    $mpdf = new \Mpdf\Mpdf();
    $namafile = 'Data Musyrif.pdf';
    $dataMusyrif = $this->Musyrif_M->getAllMusyrif();
    $tampilan = $this->load->view('export/pdf/musyrif', ['musyrif' => $dataMusyrif], TRUE);
    $mpdf->WriteHTML($tampilan);
    $mpdf->Output($namafile, "D");
  }

  public function cari_data()
  {
    $nama_musyrif = $this->input->post('nama_musyrif');
    $data = [
      'title' => 'Data Musyrif',
      'user' => $this->db->get_where('login', ['username' => $this->session->userdata('username')])->row_array(),
      'musyrif' => $this->Musyrif_M->getMusyrifByNama($nama_musyrif),
      'isi' => tampilan_mobile() ? 'musyrif/mobile-index' : 'musyrif/index',
    ];

    $this->load->view(tampilan_mobile() ? 'templates/wrapper-mobile-simple' : 'templates/wrapper-admin', $data);
  }
}

/* End of file Musyrif.php */
