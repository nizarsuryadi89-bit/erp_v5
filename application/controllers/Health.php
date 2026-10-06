<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Health extends CI_Controller {

    public function __construct()
    {
        // Don't call parent::__construct() if it forces authentication 
        // but we need it for $this->db. Let's just load what we need.
        parent::__construct();
    }

    public function index()
    {
        $response = [
            'status' => 'ok',
            'timestamp' => date('Y-m-d H:i:s'),
            'database' => 'error'
        ];

        // Check Database
        if ($this->db->conn_id) {
            $response['database'] = 'ok';
        }

        header('Content-Type: application/json');
        
        if ($response['status'] === 'ok' && $response['database'] === 'ok') {
            http_response_code(200);
        } else {
            http_response_code(500);
        }

        echo json_encode($response);
        exit;
    }
}
