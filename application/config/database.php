<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
| Keep credentials outside the deployed repository.
| - Production: domains/<domain>/qiraati-private/database.php
| - Local XAMPP: application/config/database.local.php
| QIRAATI_DATABASE_CONFIG can override both locations.
*/
$database_config_candidates = array();
$database_config_override = getenv('QIRAATI_DATABASE_CONFIG');

if (is_string($database_config_override) && $database_config_override !== '') {
	$database_config_candidates[] = $database_config_override;
}

$database_config_candidates[] = dirname(FCPATH, 2)
	. DIRECTORY_SEPARATOR . 'qiraati-private'
	. DIRECTORY_SEPARATOR . 'database.php';
$database_config_candidates[] = APPPATH . 'config' . DIRECTORY_SEPARATOR . 'database.local.php';

$database_config_loaded = FALSE;

foreach ($database_config_candidates as $database_config_path) {
	if (is_file($database_config_path) && is_readable($database_config_path)) {
		require $database_config_path;
		$database_config_loaded = isset($db) && is_array($db);
		break;
	}
}

if ( ! $database_config_loaded) {
	throw new RuntimeException('Database configuration is unavailable.');
}

unset(
	$database_config_candidates,
	$database_config_override,
	$database_config_path,
	$database_config_loaded
);
