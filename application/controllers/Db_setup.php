<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Db_setup extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
        $this->load->dbforge();
    }

    public function index()
    {
        echo "<h1>Database Setup: Geolocation</h1>";

        // 1. Update absensi table
        if (!$this->db->field_exists('latitude', 'absensi')) {
            $fields = [
                'latitude' => ['type' => 'DECIMAL', 'constraint' => '11,8', 'null' => TRUE, 'after' => 'status'],
                'longitude' => ['type' => 'DECIMAL', 'constraint' => '11,8', 'null' => TRUE, 'after' => 'latitude'],
                'jarak_meter' => ['type' => 'FLOAT', 'null' => TRUE, 'after' => 'longitude'],
            ];
            $this->dbforge->add_column('absensi', $fields);
            echo "[SUCCESS] Added columns (latitude, longitude, jarak_meter) to table 'absensi'.<br>";
        } else {
            echo "[INFO] Table 'absensi' already has geolocation columns.<br>";
        }

        // 2. Update tbl_perusahaan table
        if (!$this->db->field_exists('latitude', 'tbl_perusahaan')) {
            $fields = [
                'latitude' => ['type' => 'DECIMAL', 'constraint' => '11,8', 'null' => TRUE],
                'longitude' => ['type' => 'DECIMAL', 'constraint' => '11,8', 'null' => TRUE],
                'radius_meter' => ['type' => 'INT', 'default' => 100],
            ];
            $this->dbforge->add_column('tbl_perusahaan', $fields);

            // Set default TGM Office location (Example: Jakarta Monas as placeholder if unknown, but better leave null)
            // Or just print instruction
            echo "[SUCCESS] Added columns (latitude, longitude, radius_meter) to table 'tbl_perusahaan'.<br>";
        } else {
            echo "[INFO] Table 'tbl_perusahaan' already has geolocation columns.<br>";
        }

        echo "<hr>Setup Complete. You can delete this file now.";
    }
}
