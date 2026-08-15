<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Raport extends CI_Controller
{

  public function __construct()
  {
    parent::__construct();
    //Load Dependencies
    cek_login();
    $this->load->model('Santri_M');
    $this->load->model('Kelas_M');
    $this->load->model('Periode_ujian_M');
    $this->load->model('Raport_M');
    $this->load->model('Absen_M');
    $this->load->model('Catatan_pelanggaran_M');
    $this->load->model('Detail_target_M');
  }

  // List all your items
  public function index()
  {
    // Lihat komentar di Dana::index() - query string ?pesan= dipakai supaya pesan gagal (mis. dari
    // aksi_tampil() di bawah) tidak nyangkut lewat session flashdata.
    $pesan = $this->input->get('pesan') ?: $this->session->flashdata('pesan');

    $data = [
      'title' => 'Raport',
      'user' => $this->db->get_where('login', ['username' => $this->session->userdata('username')])->row_array(),
      'santri' => $this->Santri_M->getAllSantri(),
      'kelas' => $this->Kelas_M->getAllKelas(),
      'periode_ujian' => $this->Periode_ujian_M->getAllPeriodeUjian(),
      'pesan' => $pesan,
      'isi' => tampilan_mobile() ? 'raport/mobile-index' : 'raport/index',
    ];

    $this->load->view(tampilan_mobile() ? 'templates/wrapper-mobile-simple' : 'templates/wrapper-admin', $data);
  }

  // Add a new item
  public function aksi_tampil()
  {
    $id_siswa = $this->input->post('siswa');
    $id_kelas = $this->input->post('kelas');
    $id_periode_ujian = $this->input->post('periode_ujian');

    if (!$id_siswa || !$id_kelas || !$id_periode_ujian) {
      redirect('raport?pesan=' . rawurlencode('Santri, kelas, dan periode ujian wajib dipilih semua.'));
      return;
    }

    // Error 500 di halaman ini sempat tidak diketahui penyebab pastinya (laporan user: hanya
    // terjadi untuk periode yang datanya sudah lengkap, periode lain tampil kosong tanpa error).
    // Tangkap di sini supaya pesan errornya tampil jelas lewat ?pesan=, bukan halaman 500 kosong.
    try {
      $this->tampilkanRaport($id_siswa, $id_kelas, $id_periode_ujian);
    } catch (\Throwable $e) {
      redirect('raport?pesan=' . rawurlencode('Gagal menampilkan raport: ' . $e->getMessage() . ' (baris ' . $e->getLine() . ' di ' . basename($e->getFile()) . ')'));
    }
  }

  private function tampilkanRaport($id_siswa, $id_kelas, $id_periode_ujian)
  {
    // Data-data Raport
    $identitas_santri               = $this->Raport_M->getRaportIdentitasSantri($id_siswa, $id_periode_ujian);

    // Query identitas_santri butuh santri itu SUDAH dimasukkan ke Kelompok Halaqoh (menu Halaqoh >
    // Detail Kelompok) DAN sudah ada Periode Ujian untuk kelasnya (menu Ujian > Periode Ujian) -
    // kalau salah satu belum ada, joinnya tidak ketemu sama sekali dan SEMUA field (nama, NIS, dst)
    // ikut kosong tanpa pesan apa pun. Kasih tahu penyebabnya di sini, bukan biarkan tampil kosong.
    if (!$identitas_santri) {
      redirect('raport?pesan=' . rawurlencode('Raport tidak bisa ditampilkan - pastikan santri ini sudah dimasukkan ke Kelompok Halaqoh (menu Halaqoh > Detail Kelompok) dan sudah ada Periode Ujian untuk kelasnya (menu Ujian > Periode Ujian) pada periode yang dipilih.'));
      return;
    }

    // Rentang tanggal periode ujian ini - acuan untuk 3 rating bintang otomatis di bawah.
    $rentang = $this->Raport_M->getRentangTanggalPeriodeUjian($id_periode_ujian);
    if (!$rentang) {
      // Target & Periode Ujian dipilih lewat form terpisah (Tahfidz > Target, Ujian > Periode
      // Ujian) yang masing-masing bebas menentukan Tahun Ajaran/Semester sendiri - jadi "Target
      // belum ada" bisa menyesatkan kalau ternyata Target-nya SUDAH ada tapi Ajaran/Semester-nya
      // beda dengan Periode Ujian yang dipilih. Cek dulu supaya pesannya menunjuk akar masalahnya.
      $info_pu = $this->Raport_M->getInfoPeriodeUjian($id_periode_ujian);
      $ajaran_semester_ada = $info_pu
        ? $this->Raport_M->getAjaranSemesterTargetUntukKelasPeriode($info_pu['IdKelas'], $info_pu['IdPeriode'])
        : [];

      if ($ajaran_semester_ada) {
        $daftar = implode(', ', array_map(function ($t) {
          return $t['ThAjaran'] . ' - ' . $t['Semester'];
        }, $ajaran_semester_ada));
        redirect('raport?pesan=' . rawurlencode("Target untuk kelas & periode ini sudah ada, tapi Tahun Ajaran/Semester-nya beda dengan Periode Ujian yang dipilih. Target yang ada: {$daftar}. Samakan Tahun Ajaran/Semester saat membuat Target (menu Tahfidz > Target) atau Periode Ujian-nya (menu Ujian > Periode Ujian)."));
        return;
      }

      redirect('raport?pesan=' . rawurlencode('Rentang tanggal periode ujian ini belum tersedia - pastikan sudah ada Target (menu Tahfidz > Target) untuk kelas dan periode yang dipilih.'));
      return;
    }

    // Bintang Kehadiran: Izin/Sakit tidak mengurangi (dianggap wajar), hanya Alpa yang mengurangi.
    $ringkasan_absen = $this->Absen_M->getRingkasanRentang($id_siswa, $rentang['TglMulai'], $rentang['TglSelesai']);
    $total_kehadiran = $ringkasan_absen['Hadir'] + $ringkasan_absen['Alpa'];
    $persen_kehadiran = $total_kehadiran > 0 ? round($ringkasan_absen['Hadir'] / $total_kehadiran * 100) : null;
    $bintang_kehadiran = persentase_ke_bintang($persen_kehadiran);

    // Bintang Hafalan Doa & Qosidah: dari Target/Setoran yang ditandai JenisTarget='Doa/Qosidah'.
    $doa_qosidah = $this->Detail_target_M->getProsentaseJenisTarget($id_siswa, 'Doa/Qosidah', $rentang['TglMulai'], $rentang['TglSelesai']);
    $persen_doa_qosidah = $doa_qosidah['jml_tugas'] > 0 ? round($doa_qosidah['jml_selesai'] / $doa_qosidah['jml_tugas'] * 100) : null;
    $bintang_doa_qosidah = persentase_ke_bintang($persen_doa_qosidah);

    // Bintang Adab Akhlak: dari total poin pelanggaran semua kategori (0 poin = 5 bintang, bukan "tidak ada data").
    $total_poin_akhlak = $this->Catatan_pelanggaran_M->getTotalPoinRentang($id_siswa, $rentang['TglMulai'], $rentang['TglSelesai']);
    $bintang_adab_akhlak = poin_pelanggaran_ke_bintang($total_poin_akhlak);

    $prosentase_target              = $this->Raport_M->getRaport_Prosentase($id_siswa, $id_periode_ujian);
    $hasilujian_santri              = $this->Raport_M->getRaportHasilUjian($id_siswa, $id_periode_ujian);
    $nilai_ujian                    = $this->Raport_M->getRaport_NilaiUjian($id_siswa, $id_periode_ujian);
    $sum_avg_rank                   = $this->Raport_M->getRaport_Total_Avg_Rank($id_siswa, $id_periode_ujian);
    $jml_siswa_perKelas             = $this->Raport_M->getRaport_JmlSiswaPerKelas($id_kelas);
    $total_point_pelanggaran_ibadah = $this->Raport_M->getRaport_TotalPointPelanggaranIbadah($id_siswa);
    $keterangan_pelanggaran_ibadah  = $this->Raport_M->getRaport_KeteranganPelanggaranIbadah($id_siswa);
    $total_point_pelanggaran_bahasa = $this->Raport_M->getRaport_TotalPointPelanggaranBahasa($id_siswa);
    $keterangan_pelanggaran_bahasa  = $this->Raport_M->getRaport_KeteranganPelanggaranBahasa($id_siswa);
    $catatan_perkembangan_target    = $this->Raport_M->getRaport_Catatan_PerkembanganTarget($id_siswa, $id_periode_ujian);
    $catatan_sikap_santri           = $this->Raport_M->getRaport_Catatan_SikapSantri($id_siswa, $id_periode_ujian);
    $catatan_akhlaq_perilaku        = $this->Raport_M->getRaport_Catatan_AkhlaqPerilaku($id_siswa, $id_periode_ujian);
    $catatan_kerapian_kebersihan    = $this->Raport_M->getRaport_Catatan_KerapianKebersihan($id_siswa, $id_periode_ujian);
    $catatan_catatan_musyrif        = $this->Raport_M->getRaport_Catatan_CatatanMusyrif($id_siswa, $id_periode_ujian);
    $reward_ujian                   = $this->Raport_M->getRaport_Reward_Ujian($id_siswa, $id_periode_ujian);
    $pengasuh_pondok                = $this->Raport_M->getRaport_Pengesahan_Pengasuh();
    $direktur_tahfidz               = $this->Raport_M->getRaport_Pengesahan_Direktur();
    // check($identitas_santri);

    $data = [
      'identitas_santri'      => $identitas_santri,
      'persen_kehadiran'      => $persen_kehadiran,
      'bintang_kehadiran'     => $bintang_kehadiran,
      'persen_doa_qosidah'    => $persen_doa_qosidah,
      'bintang_doa_qosidah'   => $bintang_doa_qosidah,
      'total_poin_akhlak'     => $total_poin_akhlak,
      'bintang_adab_akhlak'   => $bintang_adab_akhlak,
      'prosentase_target'     => $prosentase_target,
      'hasil_ujian_santri'    => $hasilujian_santri,
      'nilai_ujian'           => $nilai_ujian,
      'hasil_akhir'           => $sum_avg_rank,
      'jml_siswa'             => $jml_siswa_perKelas,
      'points_ibadah'         => $total_point_pelanggaran_ibadah,
      'keterangan_ibadah'     => $keterangan_pelanggaran_ibadah,
      'points_bahasa'         => $total_point_pelanggaran_bahasa,
      'keterangan_bahasa'     => $keterangan_pelanggaran_bahasa,
      'c_perkembangan_target' => $catatan_perkembangan_target,
      'c_sikap_santri'        => $catatan_sikap_santri,
      'c_akhlaq_perilaku'     => $catatan_akhlaq_perilaku,
      'c_kerapian_kebersihan' => $catatan_kerapian_kebersihan,
      'c_catatan_musyrif'     => $catatan_catatan_musyrif,
      'reward_ujian'          => $reward_ujian,
      'pengasuh'              => $pengasuh_pondok,
      'direktur'              => $direktur_tahfidz
    ];
    // check($data['pengasuh']);
    $this->load->view('raport/preview', $data);
  }
}

/* End of file Raport.php */
