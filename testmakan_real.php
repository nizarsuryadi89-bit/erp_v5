<?php
$_SERVER['DOCUMENT_ROOT'] = '/Applications/XAMPP/xamppfiles/htdocs';
$_SERVER['REMOTE_ADDR'] = '127.0.0.1';
$_SERVER['REQUEST_METHOD'] = 'GET';
define('BASEPATH', '/Applications/XAMPP/xamppfiles/htdocs/tgm_erp/system/');
require '/Applications/XAMPP/xamppfiles/htdocs/tgm_erp/index.php';

$CI =& get_instance();
$CI->load->model('Payroll_model');

$data = $CI->Payroll_model->get_salary_data('2026-06', 18);
print_r($data);
