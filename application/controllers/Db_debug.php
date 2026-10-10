<?php
// application/controllers/Db_debug.php
defined('BASEPATH') OR exit('No direct script access allowed');

class Db_debug extends CI_Controller {
    public function index() {
        header('Content-Type: text/plain');
        echo "DATABASE DEBUGGING TOOL\n";
        echo "-----------------------\n\n";

        echo "Database Host: " . $this->db->hostname . "\n";
        echo "Database Name: " . $this->db->database . "\n";
        echo "Table Prefix:  " . $this->db->dbprefix . "\n\n";

        $table = 'menu';
        $full_table = $this->db->dbprefix($table);

        echo "Testing Table: " . $table . " (Prefix Applied: " . $full_table . ")\n";
        
        // Count all
        echo "count_all('" . $table . "') results: " . $this->db->count_all($table) . "\n";
        
        // Count with where status 1
        $this->db->where('status', 1);
        echo "count_all_results('" . $table . "') where status=1: " . $this->db->count_all_results($table) . "\n";
        
        // Count with where status 0
        $this->db->where('status', 0);
        echo "count_all_results('" . $table . "') where status=0: " . $this->db->count_all_results($table) . "\n";

        // Raw Select *
        $res = $this->db->query("SELECT * FROM " . $full_table)->result_array();
        echo "Query 'SELECT * FROM " . $full_table . "' count: " . count($res) . "\n";
        
        if (count($res) > 0) {
            echo "\nData Preview (IDs): ";
            $ids = array_column($res, 'id');
            if (empty($ids)) $ids = array_column($res, 'id_menu'); // Check alternative PK
            echo implode(', ', $ids) . "\n";
            
            echo "Statuses: ";
            $statuses = array_column($res, 'status');
            echo implode(', ', $statuses) . "\n";
        }

        echo "\n\nTesting with Native PDO (Bypass CI):\n";
        try {
            $dsn = "mysql:host={$this->db->hostname};dbname={$this->db->database}";
            $pdo = new PDO($dsn, $this->db->username, $this->db->password);
            $stmt = $pdo->query("SELECT * FROM " . $full_table);
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
            echo "PDO count from " . $full_table . ": " . count($rows) . "\n";
        } catch (Exception $e) {
            echo "PDO Error: " . $e->getMessage() . "\n";
        }
    }
}
