<?php
defined('BASEPATH') or exit('No direct script access allowed');

/*
| -------------------------------------------------------------------------
| URI ROUTING
| -------------------------------------------------------------------------
| This file lets you re-map URI requests to specific controller functions.
|
| Typically there is a one-to-one relationship between a URL string
| and its corresponding controller class/method. The segments in a
| URL normally follow this pattern:
|
|	example.com/class/method/id/
|
| In some instances, however, you may want to remap this relationship
| so that a different class/function is called than the one
| corresponding to the URL.
|
| Please see the user guide for complete details:
|
|	https://codeigniter.com/user_guide/general/routing.html
|
| -------------------------------------------------------------------------
| RESERVED ROUTES
| -------------------------------------------------------------------------
|
| There are three reserved routes:
|
|	$route['default_controller'] = 'welcome';
|
| This route indicates which controller class should be loaded if the
| URI contains no data. In the above example, the "welcome" class
| would be loaded.
|
|	$route['404_override'] = 'errors/page_missing';
|
| This route will tell the Router which controller/method to use if those
| provided in the URL cannot be matched to a valid route.
|
|	$route['translate_uri_dashes'] = FALSE;
|
| This is not exactly a route, but allows you to automatically route
| controller and method names that contain dashes. '-' isn't a valid
| class or method name character, so it requires translation.
| When you set this option to TRUE, it will replace ALL dashes in the
| controller and method URI segments.
|
| Examples:	my-controller/index	-> my_controller/index
|		my-controller/my-method	-> my_controller/my_method
*/
$route['default_controller'] = 'auth';
$route['absen'] = 'absen';
$route['absen/kirim'] = 'absen/kirim';
$route['absen/simpan'] = 'absen/simpan';
$route['absen/simpan_sementara'] = 'absen/simpan_sementara';
$route['absen/kirim_absensi'] = 'absen/kirim_absensi';
$route['absen/reset_status'] = 'absen/reset_status';
$route['absen/(:any)'] = 'absen/index/$1';
$route['dana'] = 'dana';
// URL publiknya pakai tanda hubung ("dana-lain") tapi nama class controller tidak boleh - '-' bukan
// karakter valid untuk nama class PHP, jadi filenya "Dana_lain.php" dan butuh rute eksplisit di sini
// (translate_uri_dashes di atas TIDAK cukup - itu cuma untuk method, bukan mengubah target rute).
$route['dana-lain'] = 'Dana_lain';
$route['dana-lain/(.+)'] = 'Dana_lain/$1';
$route['dana-maulid'] = 'Dana_maulid';
$route['dana-maulid/(.+)'] = 'Dana_maulid/$1';
$route['laporan'] = 'laporan';
$route['laporan/(:any)'] = 'laporan/index/$1';
$route['statistik'] = 'statistik';
$route['maulid'] = 'Maulid';
$route['maulid/(.+)'] = 'Maulid/$1';
$route['public/statistik'] = 'statistik';
// Catatan penting soal (:any): CodeIgniter menerjemahkan (:any) jadi regex [^/]+ - HANYA cocok
// SATU segmen URI (tidak boleh ada '/'). Aksi seperti "delete/5" atau "toggle_aktif/5" adalah DUA
// segmen tambahan, jadi (:any) tidak akan cocok untuk itu - makanya semua rute di bawah pakai
// (.+) (regex mentah, bukan wildcard bawaan CI), yang cocok untuk berapa pun segmen tambahan.
$route['pengaturanaplikasi'] = 'PengaturanAplikasi';
$route['pengaturanaplikasi/(.+)'] = 'PengaturanAplikasi/$1';
$route['pengaturansidebar'] = 'PengaturanSidebar';
$route['pengaturansidebar/(.+)'] = 'PengaturanSidebar/$1';
$route['pengaturanmigrasi'] = 'PengaturanMigrasi';
$route['pengaturanmigrasi/(.+)'] = 'PengaturanMigrasi/$1';
// Folder controller "Tahfidz" (huruf T besar) tidak cocok dengan URL "tahfidz" (huruf kecil) di
// filesystem yang case-sensitive (Linux/Hostinger) - sama seperti kasus PengaturanAplikasi dkk di
// atas, cuma ini untuk nama folder bukan nama file. Tanpa ini 404 di server tapi normal di XAMPP.
$route['tahfidz/ajaran'] = 'Tahfidz/Ajaran';
$route['tahfidz/ajaran/(.+)'] = 'Tahfidz/Ajaran/$1';
$route['tahfidz/semester'] = 'Tahfidz/Semester';
$route['tahfidz/semester/(.+)'] = 'Tahfidz/Semester/$1';
$route['tahfidz/periode'] = 'Tahfidz/Periode';
$route['tahfidz/periode/(.+)'] = 'Tahfidz/Periode/$1';
$route['tahfidz/target'] = 'Tahfidz/Target';
$route['tahfidz/target/(.+)'] = 'Tahfidz/Target/$1';
$route['tahfidz/detail_target'] = 'Tahfidz/Detail_target';
$route['tahfidz/detail_target/(.+)'] = 'Tahfidz/Detail_target/$1';
// Nama file controller "Predikat_Nilai.php" dkk punya huruf besar SETELAH underscore juga, tapi
// URL yang dipakai di seluruh aplikasi ("nilai/predikat_nilai") huruf kecil semua - ucfirst() bawaan
// CI cuma membesarkan huruf PALING AWAL, jadi "Predikat_nilai" (bukan "Predikat_Nilai") yang dicari,
// beda dengan nama file asli di filesystem case-sensitive. Sama persis kelasnya dengan Tahfidz/* di atas.
$route['nilai/predikat_nilai'] = 'nilai/Predikat_Nilai';
$route['nilai/predikat_nilai/(.+)'] = 'nilai/Predikat_Nilai/$1';
$route['nilai/predikat_hasil1'] = 'nilai/Predikat_Hasil1';
$route['nilai/predikat_hasil1/(.+)'] = 'nilai/Predikat_Hasil1/$1';
$route['nilai/predikat_hasil2'] = 'nilai/Predikat_Hasil2';
$route['nilai/predikat_hasil2/(.+)'] = 'nilai/Predikat_Hasil2/$1';
$route['nilai/predikat_hasil3'] = 'nilai/Predikat_Hasil3';
$route['nilai/predikat_hasil3/(.+)'] = 'nilai/Predikat_Hasil3/$1';
$route['ujian/periode_ujian'] = 'ujian/Periode_Ujian';
$route['ujian/periode_ujian/(.+)'] = 'ujian/Periode_Ujian/$1';
$route['ujian/jenis_ujian'] = 'ujian/Jenis_Ujian';
$route['ujian/jenis_ujian/(.+)'] = 'ujian/Jenis_Ujian/$1';
$route['ujian/target_ujian_kelas'] = 'ujian/Target_Ujian_Kelas';
$route['ujian/target_ujian_kelas/(.+)'] = 'ujian/Target_Ujian_Kelas/$1';
$route['ujian/target_ujian'] = 'ujian/Target_Ujian';
$route['ujian/target_ujian/(.+)'] = 'ujian/Target_Ujian/$1';
$route['ujian/rekap_ujian'] = 'ujian/Rekap_Ujian';
$route['ujian/rekap_ujian/(.+)'] = 'ujian/Rekap_Ujian/$1';
$route['ujian/hasil_ujian'] = 'ujian/Hasil_Ujian';
$route['ujian/hasil_ujian/(.+)'] = 'ujian/Hasil_Ujian/$1';
$route['manifest\.json'] = 'Manifest/index';
$route['404_override'] = '';
$route['translate_uri_dashes'] = FALSE;
