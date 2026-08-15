<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
| -------------------------------------------------------------------------
| Hooks
| -------------------------------------------------------------------------
| This file lets you define "hooks" to extend CI without hacking the core
| files.  Please see the user guide for info:
|
|	https://codeigniter.com/user_guide/general/hooks.html
|
*/

// Blokir akses URL langsung ke menu yang dinonaktifkan lewat Pengaturan Sidebar (khusus level Bagian Administrasi).
$hook['post_controller_constructor'][] = [
  'class' => 'CekMenuAktif',
  'function' => 'cek',
  'filename' => 'CekMenuAktif.php',
  'filepath' => 'hooks',
];
