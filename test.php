<?php
define('BASEPATH', 'dummy'); // Mock
define('IS_API_CALL', true);

// Setup a minimal CI bootstrap to call the controller directly
$_SERVER['REQUEST_METHOD'] = 'GET';
$_SERVER['REMOTE_ADDR'] = '127.0.0.1';
$_SERVER['HTTP_HOST'] = 'localhost';

chdir('/Applications/XAMPP/xamppfiles/htdocs/tgm_erp');
require_once 'index.php';
