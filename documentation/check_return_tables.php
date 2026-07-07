<?php
// Script untuk mengecek dan membuat tabel return pembelian
// Jalankan script ini di browser untuk memastikan tabel sudah ada

// Load CodeIgniter
require_once 'index.php';

$CI =& get_instance();
$CI->load->database();

echo "<h2>Checking Return Pembelian Tables</h2>";

// Check if tables exist
$tables_to_check = [
    'tbl_return_pembelian',
    'tbl_return_pembelian_detail', 
    'tbl_temp_return'
];

foreach($tables_to_check as $table) {
    $exists = $CI->db->table_exists($table);
    echo "<p><strong>$table:</strong> " . ($exists ? "✅ EXISTS" : "❌ NOT EXISTS") . "</p>";
    
    if($exists) {
        // Show table structure
        $fields = $CI->db->list_fields($table);
        echo "<ul>";
        foreach($fields as $field) {
            echo "<li>$field</li>";
        }
        echo "</ul>";
        
        // Show record count
        $count = $CI->db->count_all($table);
        echo "<p>Records: $count</p>";
    }
}

// Check if required tables exist
$required_tables = [
    'tbl_supplier',
    'tbl_bahan', 
    'tbl_karyawan',
    'tbl_stok_gudang'
];

echo "<h3>Required Tables Check:</h3>";
foreach($required_tables as $table) {
    $exists = $CI->db->table_exists($table);
    echo "<p><strong>$table:</strong> " . ($exists ? "✅ EXISTS" : "❌ NOT EXISTS") . "</p>";
}

// Test insert to temp table
echo "<h3>Testing Temp Table Insert:</h3>";
try {
    $test_data = [
        'bahan_id' => 1,
        'qty_return' => 10,
        'harga_return' => 1000,
        'alasan_return' => 'Test data'
    ];
    
    $result = $CI->db->insert('tbl_temp_return', $test_data);
    echo "<p>Temp insert test: " . ($result ? "✅ SUCCESS" : "❌ FAILED") . "</p>";
    
    if($result) {
        // Clean up test data
        $CI->db->where('bahan_id', 1)->where('alasan_return', 'Test data')->delete('tbl_temp_return');
        echo "<p>Test data cleaned up</p>";
    }
    
} catch(Exception $e) {
    echo "<p>❌ Error: " . $e->getMessage() . "</p>";
}

// Show database error log
echo "<h3>Database Errors:</h3>";
$errors = $CI->db->error();
if($errors['code'] != 0) {
    echo "<p>❌ Error: " . $errors['message'] . "</p>";
} else {
    echo "<p>✅ No database errors</p>";
}

echo "<hr>";
echo "<p><a href='gudang/addReturnPembelian'>Go to Add Return Pembelian</a></p>";
?> 