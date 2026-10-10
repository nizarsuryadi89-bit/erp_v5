<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Complaint_tools extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        if (!$this->input->is_cli_request()) { show_404(); exit; }
        date_default_timezone_set('Asia/Jakarta');
        $this->config->load('complaint');
        $this->load->model('Complaint_model');
    }

    public function help()
    {
        echo "php tools/complaint.php install  : pasang 4 tabel baru dan menu\n";
        echo "php tools/complaint.php menu     : daftar menu setelah impor SQL manual\n";
        echo "php tools/complaint.php run 50 1 : proses hingga 50 pesan cabang 1\n";
    }

    public function install()
    {
        try {
            $file = FCPATH.'database/complaint_center.sql';
            if (!is_file($file)) { throw new RuntimeException('File database/complaint_center.sql tidak ditemukan.'); }
            $sql = preg_replace('/^\s*--.*$/m', '', file_get_contents($file));
            foreach (explode(';', $sql) as $statement) {
                if (trim($statement) !== '' && !$this->db->query($statement)) { throw new RuntimeException('Pemasangan tabel gagal.'); }
            }
            if (!$this->Complaint_model->ready()) { throw new RuntimeException('Tabel modul belum lengkap.'); }
            $this->menu();
            echo "Tabel Complaint Center tersedia. Logout/login ERP untuk memperbarui menu.\n";
        } catch (Throwable $e) { fwrite(STDERR, $e->getMessage()."\n"); exit(1); }
    }

    public function menu()
    {
        $locked = FALSE;
        try {
            if (!$this->db->table_exists('menu') || !$this->db->table_exists('tbl_menu_access')) { throw new RuntimeException('Tabel hak akses ERP tidak ditemukan.'); }
            $lock = $this->db->query("SELECT GET_LOCK('tgm_complaint_menu_install', 10) AS acquired")->row_array();
            if (empty($lock['acquired'])) { throw new RuntimeException('Installer lain sedang berjalan.'); }
            $locked = TRUE;
            $pk = $this->db->field_exists('id', 'menu') ? 'id' : 'id_menu';
            $role = $this->db->field_exists('role_id', 'tbl_menu_access') ? 'role_id' : 'level_id';
            $access_menu = $this->db->field_exists('menu_id', 'tbl_menu_access') ? 'menu_id' : 'id_menu';
            $this->db->trans_begin();
            $menu = $this->db->get_where('menu', array('link' => 'complaint'))->row_array();
            if (!$menu) {
                $data = array('name' => 'Complaint Center', 'nama_menu' => 'Complaint Center', 'alias' => 'Komplain & Trouble', 'link' => 'complaint',
                    'icon' => 'bi-exclamation-triangle', 'icon_fa' => 'bi-exclamation-triangle', 'iconfa' => 'bi-exclamation-triangle', 'status' => 1, 'urutan' => 95);
                $data = array_intersect_key($data, array_flip($this->db->list_fields('menu')));
                if (!$this->db->insert('menu', $data)) { throw new RuntimeException('Pendaftaran menu gagal.'); }
                $id = $this->db->insert_id();
            } else { $id = $menu[$pk]; }
            foreach ($this->config->item('complaint_manager_roles') as $manager_role) {
                $where = array($role => $manager_role, $access_menu => $id);
                if (!$this->db->get_where('tbl_menu_access', $where)->row_array()) {
                    $grant = $where;
                    if ($this->db->field_exists('is_readonly', 'tbl_menu_access')) { $grant['is_readonly'] = 0; }
                    if (!$this->db->insert('tbl_menu_access', $grant)) { throw new RuntimeException('Pendaftaran akses gagal.'); }
                }
            }
            if (!$this->db->trans_status()) { throw new RuntimeException('Pendaftaran menu dibatalkan.'); }
            $this->db->trans_commit();
            echo "Menu Complaint Center terdaftar untuk pengelola. Atur akses PIC melalui pengaturan hak akses ERP.\n";
        } catch (Throwable $e) { $this->db->trans_rollback(); fwrite(STDERR, $e->getMessage()."\n"); exit(1); }
        finally { if ($locked) { $this->db->query("SELECT RELEASE_LOCK('tgm_complaint_menu_install')"); } }
    }

    public function run($limit = '50', $company = NULL)
    {
        if (!ctype_digit((string) $limit) || (int) $limit < 1 || (int) $limit > 500 ||
            ($company !== NULL && (!ctype_digit((string) $company) || (int) $company < 1))) {
            fwrite(STDERR, "Gunakan run [1-500] [ID cabang opsional].\n"); exit(1);
        }
        try {
            $this->load->library('Complaint_processor');
            $result = $this->complaint_processor->run((int) $limit, $company === NULL ? NULL : (int) $company, TRUE);
            echo json_encode($result)."\n";
            if ($result['failed']) { exit(1); }
        } catch (Throwable $e) { fwrite(STDERR, "Pemrosesan gagal; periksa konfigurasi dan log ERP.\n"); exit(1); }
    }
}
