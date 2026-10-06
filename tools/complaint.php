<?php
// CLI bootstrap for ERP's HTTP_HOST-based configuration on XAMPP.
if (PHP_SAPI !== 'cli') { http_response_code(404); exit; }
$command = isset($argv[1]) ? $argv[1] : 'help';
if (!in_array($command, array('install', 'menu', 'run', 'help'), TRUE)) { fwrite(STDERR, "Perintah tidak dikenal.\n"); exit(1); }
$arguments = array_merge(array('index.php', 'complaint_tools', $command), array_slice($argv, 2));
$_SERVER['argv'] = $arguments; $_SERVER['argc'] = count($arguments);
$argv = $arguments; $argc = count($arguments);
$_SERVER['HTTP_HOST'] = 'localhost'; $_SERVER['SERVER_NAME'] = 'localhost';
$_SERVER['SERVER_PORT'] = '80'; $_SERVER['SCRIPT_NAME'] = '/tgm_erp/index.php';
chdir(dirname(__DIR__));
require 'index.php';
