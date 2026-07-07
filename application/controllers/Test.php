<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Test extends MY_Controller {
    public function index() {
        if (!defined('IS_API_CALL')) define('IS_API_CALL', true);
        require_once APPPATH . 'controllers/Mobile.php';
        $mobile = new Mobile();
        // Just directly test the query for $karyawan_id = 1 ?
        $u = $this->db->get('users')->row_array();
        echo "Karyawan ID: " . $u['karyawan_id'] . "\n";
        
        $this->load->model('Payroll_model');
        $data = $this->Payroll_model->get_salary_data('2026-02', $u['karyawan_id']);
        if (empty($data)) echo "EMPTY ARRAY\n";
        else print_r($data);
    }
}
