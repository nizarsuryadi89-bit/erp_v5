<?php
define('BASEPATH', 'TRUE');
require_once '/Applications/XAMPP/xamppfiles/htdocs/tgm_erp/index.php';
$CI =& get_instance();
$CI->load->database();

function describe($table)
{
    global $CI;
    echo "--- $table ---\n";
    $fields = $CI->db->list_fields($table);
    foreach ($fields as $field) {
        echo $field . "\n";
    }
}

describe('tbl_subkategori');
describe('tbl_type');
describe('tbl_product');
