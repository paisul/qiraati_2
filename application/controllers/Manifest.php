<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * manifest.json dinamis (bukan file statis) - namanya/logo mengikuti Pengaturan Aplikasi
 * (ambil_pengaturan_aplikasi()), SSOT yang sama dipakai di header/sidebar, supaya identitas
 * PWA yang sudah ter-install ikut berubah kalau admin ganti nama/logo aplikasi nanti.
 */
class Manifest extends CI_Controller
{
  public function index()
  {
    $app = ambil_pengaturan_aplikasi($this->db);

    $manifest = [
      'name' => $app['NamaAplikasi'] . ' - Portal Wali',
      'short_name' => $app['NamaAplikasi'],
      'start_url' => base_url('Wali/index'),
      'scope' => base_url(),
      'display' => 'standalone',
      'background_color' => '#172a3a',
      'theme_color' => '#172a3a',
      'icons' => [
        [
          'src' => base_url('assets/icons/icon-192.png'),
          'sizes' => '192x192',
          'type' => 'image/png',
        ],
        [
          'src' => base_url('assets/icons/icon-512.png'),
          'sizes' => '512x512',
          'type' => 'image/png',
        ],
      ],
    ];

    // Manifest jarang berubah (cuma kalau admin ganti nama/logo aplikasi) - browser boleh cache
    // cukup lama. Ini juga mengurangi request paralel ke server tiap buka halaman (browser bisa
    // fetch manifest.json bersamaan dengan halaman utama, sama-sama menyentuh session PHP - lihat
    // catatan di cek_login()/tampilan_mobile() soal cache halaman personal).
    $this->output
      ->set_content_type('application/manifest+json')
      ->set_header('Cache-Control: public, max-age=86400')
      ->set_output(json_encode($manifest));
  }
}

/* End of file Manifest.php */
