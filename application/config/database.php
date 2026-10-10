<?php
defined('BASEPATH') OR exit('No direct script access allowed');

$active_group = 'default';
$query_builder = TRUE;

// Auto-detect environment based on hostname
$is_localhost = (isset($_SERVER['HTTP_HOST']) && (strpos($_SERVER['HTTP_HOST'], 'localhost') !== false || strpos($_SERVER['HTTP_HOST'], '127.0.0.1') !== false));

$db['default'] = array(
	'dsn'	=> '',
	'hostname' => $is_localhost ? 'localhost' : 'acireale-db.id.rapidplex.com',
	'username' => $is_localhost ? 'root' : 'tgmprin2_tgmprint',
	'password' => $is_localhost ? '' : 'TGM2026bekasi',
	'database' => $is_localhost ? 'tgm_erp' : 'tgmprin2_erp_2026',
	'dbdriver' => 'mysqli',
	'dbprefix' => '',
	'pconnect' => FALSE,
	'db_debug' => (ENVIRONMENT !== 'production'),
	'cache_on' => FALSE,
	'cachedir' => '',
	'char_set' => 'utf8',
	'dbcollat' => 'utf8_general_ci',
	'swap_pre' => '',
	'encrypt' => FALSE,
	'compress' => FALSE,
	'stricton' => FALSE,
	'failover' => array(),
	'save_queries' => TRUE
);
