<?php
define('BASEPATH', 'TRUE');
require_once '/Applications/XAMPP/xamppfiles/htdocs/tgm_erp/index.php';
$CI =& get_instance();
$CI->load->database();

function describe($table)
{
    global $CI;
    if (!$CI->db->table_exists($table)) {
        echo "--- $table (DOES NOT EXIST) ---\n";
        return;
    }
    echo "--- $table ---\n";
    $fields = $CI->db->list_fields($table);
    foreach ($fields as $field) {
        echo $field . "\n";
    }
}

describe('tbl_pemesanan_detail');
describe('tbl_temp_transaksi');
describe('tbl_product');
