<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Testmakan extends CI_Controller {
    public function index() {
        $this->load->model('Payroll_model');
        $data = $this->Payroll_model->get_salary_data('2026-06', 18);
        echo json_encode($data);
    }
}
