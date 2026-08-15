<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Folder upload disimpan sebagai sibling folder aplikasi agar tidak terhapus
 * ketika aplikasi dideploy ulang.
 */
function upload_path($subfolder)
{
  return rtrim(FCPATH, '/') . '/../qiroati_uploads/' . $subfolder . '/';
}

function upload_url($subfolder, $filename)
{
  if (empty($filename)) {
    return '';
  }

  $protokol = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') ? 'https' : 'http';
  $folder_app = str_replace(basename($_SERVER['SCRIPT_NAME']), '', $_SERVER['SCRIPT_NAME']);
  $folder_induk = rtrim(dirname(rtrim($folder_app, '/')), '/') . '/';
  $file_path = upload_path($subfolder) . $filename;
  $version = is_file($file_path) ? '?v=' . filemtime($file_path) : '';

  return $protokol . '://' . $_SERVER['HTTP_HOST'] . $folder_induk . 'qiroati_uploads/' . $subfolder . '/' . rawurlencode($filename) . $version;
}

/* End of file upload_helper.php */
