<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Employee_monitor extends MY_Controller {

    public function __construct() {
        parent::__construct();
        // Cek login
        if (!$this->session->userdata('logged_in')) {
            redirect('auth');
        }
        
        // Proteksi: Hanya role Owner (misal role_id == 1) atau Superadmin yang boleh akses
        $role_id = $this->session->userdata('role_id') ?: $this->session->userdata('level');
        if ($role_id != 1 && $role_id != 2 && $role_id != 17) { // Sesuaikan jika role owner/admin berbeda ID nya
            $this->session->set_flashdata('error', 'Anda tidak memiliki akses ke halaman ini.');
            redirect('dashboard');
        }

        $this->load->model('Employee_monitor_model');
        
        // AUTO MIGRATION: Register Employee Monitor Menu
        $cek_menu = $this->db->get_where('menu', ['link' => 'employee_monitor'])->row_array();
        if (!$cek_menu) {
            $max_urutan = $this->db->select_max('urutan')->get('menu')->row()->urutan;
            $this->db->insert('menu', [
                'nama_menu' => 'Monitoring Kinerja',
                'alias' => 'Monitor',
                'icon' => 'fas fa-chart-line',
                'icon_fa' => 'fas fa-chart-line',
                'link' => 'employee_monitor',
                'icon_menu' => 'fas fa-chart-line',
                'urutan' => $max_urutan + 1,
                'status' => '1'
            ]);
            $menu_id = $this->db->insert_id();
            
            // Give access to Superadmin(1) and Owner(17)
            $roles = [1, 17];
            $access_data = [];
            foreach ($roles as $role) {
                $access_data[] = [
                    'role_id' => $role,
                    'menu_id' => $menu_id
                ];
            }
            $this->db->insert_batch('tbl_menu_access', $access_data);
        }
    }

    public function index() {
        $data['title'] = 'Monitoring Karyawan';
        
        $date_start = $this->input->get('date_start');
        $date_end = $this->input->get('date_end');

        if (!$date_start) $date_start = date('Y-m-01');
        if (!$date_end) $date_end = date('Y-m-t');

        $data['date_start'] = $date_start;
        $data['date_end'] = $date_end;
        
        $data['top_kasir'] = $this->Employee_monitor_model->get_top_kasir($date_start, $date_end);
        $data['top_produksi'] = $this->Employee_monitor_model->get_top_produksi($date_start, $date_end);
        $data['online_status'] = $this->Employee_monitor_model->get_online_status();
        $data['timeline'] = $this->Employee_monitor_model->get_live_timeline(50);
        $data['audit_logs'] = $this->Employee_monitor_model->get_sensitive_logs(50);

        $this->load_admin_view('admin/owner/employee_monitor', $data);
    }
}
