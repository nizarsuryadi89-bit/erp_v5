<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Konsumen extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        $userid = $this->session->userdata('id');
        $table = 'tbl_konsumen';

        // Optimized: Single query for all status counts
        $stats = $this->db->query("
            SELECT 
                COUNT(*) as total,
                SUM(CASE WHEN status = '1' THEN 1 ELSE 0 END) as active,
                SUM(CASE WHEN status = '0' THEN 1 ELSE 0 END) as inactive
            FROM $table
        ")->row();

        // Optimized: Get real total of customers with transactions (not limited by top 10)
        $trx_count = $this->db->query("SELECT COUNT(DISTINCT konsumen_id) as cnt FROM tbl_pemesanan")->row()->cnt;

        // Get Top 10 Customers
        $top_customers = $this->M_transaksi->get_konsumen_dengan_transaksi()->result();

        $data = array(
            'controller' => $this->uri->segment(1),
            'function' => $this->uri->segment(2),
            'title' => 'CRM - Dashboard Konsumen',
            'table' => $table,
            'jml_konsumen' => $stats->total,

            // Dashboard statistics
            'total_customers' => $stats->total,
            'active_customers' => $stats->active ?? 0,
            'inactive_customers' => $stats->inactive ?? 0,
            'customers_with_transactions' => $trx_count,

            // Top customers by transaction
            'top_customers' => $top_customers,

            'logo' => $this->conf['perusahaan']['logo'] ?? '',
            // 'level' & 'menu' handled by MY_Controller to reduce load
            'submenu' => $this->M_master->getmenu($this->uri->segment(1))->result(),
        );

        $this->load_admin_view('admin/konsumen/index', $data);
    }

    public function konsumen()
    {
        $controller = $this->uri->segment(1);
        $userid = $this->session->userdata('id');
        $table = 'tbl_konsumen';

        $conf = array(
            'perusahaan' => $this->db->get_where('tbl_perusahaan', array('id_perusahaan' => $this->session->userdata('perusahaan_id')))->row_array(),
        );

        // Performance Check: Use count_all_results instead of get()->num_rows()
        // for large datasets (16,000+ rows)
        $total_customers = $this->db->count_all($table);
        $active_customers = $this->db->where('status', '1')->count_all_results($table);
        $inactive_customers = $this->db->where('status', '0')->count_all_results($table);
        $customers_with_email = $this->db->where('konsumen_email IS NOT NULL')->where('konsumen_email !=', '')->count_all_results($table);

        $data = array(
            'controller' => $this->uri->segment(1),
            'function' => $this->uri->segment(2),
            'title' => 'CRM - Daftar Konsumen',
            'table' => $table,
            // Optimized: We don't fetch all data here because the view uses AJAX Server-side DataTables
            'tampilData' => [],
            'total_rows' => $total_customers,
            'jml_konsumen' => $total_customers,
            'active_customers' => $active_customers,
            'inactive_customers' => $inactive_customers,
            'customers_with_email' => $customers_with_email,
            'perusahaan' => $conf['perusahaan']['nama_perusahaan'] ?? '',
            'logo' => $conf['perusahaan']['logo'] ?? '',
            'level' => $this->db->get_where('users', array('user_id' => $userid))->row_array(),
            // 'menu' diambil otomatis dari MY_Controller
            'submenu' => $this->M_master->getmenu($controller)->result(),
        );

        $this->load_admin_view('admin/konsumen/konsumen', $data);
    }

    public function aktivitas_konsumen()
    {
        $controller = $this->uri->segment(1);
        $function = $this->uri->segment(2);

        $table = 'tbl_konsumen';
        $data['order'] = 'konsumen_nama';
        $data['id'] = 'kategori_id';
        $userid = $this->session->userdata('id');

        $conf = array(
            'perusahaan' => $this->db->get_where('tbl_perusahaan', array('id_perusahaan' => $this->session->userdata('perusahaan_id')))->row_array(),
            'hakakses' => $this->db->get_where('tbl_divisi', array('divisi_id' => $this->session->userdata('level')))->row_array(),
        );

        $data = array(
            'controller' => $this->uri->segment(1),
            'function' => $this->uri->segment(2),
            'title' => 'CRM - Aktivitas Konsumen',
            'table' => 'tbl_kategori',
            'tampil_data' => $this->M_transaksi->get_konsumen_dengan_transaksi()->result(),
            'total_rows' => $this->M_transaksi->get_konsumen_dengan_transaksi()->num_rows(),
            'logo' => $conf['perusahaan']['logo'],
            'level' => $this->db->get_where('users', array('user_id' => $userid))->row_array(),
            // 'menu' diambil otomatis dari MY_Controller
            'submenu' => $this->M_master->getmenu($controller)->result(),


        );

        $this->load_admin_view('admin/konsumen/aktivitas_konsumen', $data);
    }


    function actionAdd()
    {
        $table = 'tbl_konsumen';
        $controller = $this->uri->segment(1);

        // Query tidak dipakai, dikomentari untuk optimasi
        /*
        $conf = array(
            'perusahaan' => $this->db->get_where('tbl_perusahaan', array('id_perusahaan' => $this->session->userdata('perusahaan_id')))->row_array(),
            'hakakses' => $this->db->get_where('tbl_divisi', array('divisi_id' => $this->session->userdata('level')))->row_array(),
        );
        */

        $data = array(
            'konsumen_nama' => $this->input->post('konsumen_nama', true),
            'konsumen_alamat' => $this->input->post('konsumen_alamat', true),
            'konsumen_nohp' => $this->input->post('konsumen_nohp', true),
            'konsumen_email' => $this->input->post('konsumen_email', true),
        );
        
        $add = $this->M_master->addData($table, $data);

        if ($add == true) {
            $this->session->set_flashdata('success', 'Data Berhasil Disimpan');
        } else {
            $this->session->set_flashdata('error', 'Gagal menyimpan data.');
        }
        
        redirect('konsumen/konsumen');
    }

    public function actionAddAjax()
    {
        // Validasi input
        $this->form_validation->set_rules('konsumen_nama', 'Nama Konsumen', 'required');
        $this->form_validation->set_rules('konsumen_nohp', 'No. HP', 'required');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode([
                'status' => 'error',
                'message' => validation_errors()
            ]);
            return;
        }

        $table = 'tbl_konsumen';
        $data = array(
            'konsumen_nama' => $this->input->post('konsumen_nama', true),
            'konsumen_alamat' => $this->input->post('konsumen_alamat', true),
            'konsumen_nohp' => $this->input->post('konsumen_nohp', true),
            'konsumen_email' => $this->input->post('konsumen_email', true),
            // Default fields jika tidak disertakan dalam AJAX
            // 'password'              => md5($this->input->post('konsumen_nohp', true)),
            // 'status'                => '1'
        );

        $add = $this->M_master->addData($table, $data);

        if ($add) {
            // Ambil ID yang baru saja diinsert
            $insert_id = $this->db->insert_id();
            $data['konsumen_id'] = $insert_id;
            
            echo json_encode([
                'status' => 'success',
                'message' => 'Data Konsumen Berhasil Disimpan',
                'data' => $data
            ]);
        } else {
            echo json_encode([
                'status' => 'error',
                'message' => 'Gagal menyimpan data ke database'
            ]);
        }
    }

    function aksi_delete()
    {

        $id = (int)$this->uri->segment(3);
        $table = 'tbl_konsumen';
        $data['id'] = 'konsumen_id';
        $controller = $this->uri->segment(1);

        $this->db->where($data['id'], $id);
        $delete = $this->db->delete($table);

        if ($delete == true) {
            $this->session->set_flashdata('success', 'Data Berhasil Dihapus');
            redirect($controller);
        }
    }




    public function aksi_edit()
    {
        $id = (int)$this->input->post('konsumen_id', true);
        $data = [
            'konsumen_nama' => $this->input->post('konsumen_nama', true),
            'konsumen_alamat' => $this->input->post('konsumen_alamat', true),
            'konsumen_nohp' => $this->input->post('konsumen_nohp', true),
            'konsumen_email' => $this->input->post('konsumen_email', true),
            'status' => $this->input->post('status', true)
        ];

        // Validasi input
        if ($id && !empty($data['konsumen_nama'])) {
            $this->db->where('konsumen_id', $id);
            $update = $this->db->update('tbl_konsumen', $data);

            if ($update) {
                $this->session->set_flashdata('success', 'Data konsumen berhasil diperbarui.');
            } else {
                $this->session->set_flashdata('error', 'Gagal memperbarui data konsumen.');
            }
        } else {
            $this->session->set_flashdata('error', 'Data tidak valid.');
        }
        redirect('konsumen/konsumen');
    }




    public function get_ajax()
    {
        $request = $_POST;
        $this->load->model('Konsumen_model');

        $data = $this->Konsumen_model->get_datatables($request);
        echo json_encode($data);
    }

    public function search_ajax()
    {
        $term = $this->input->get('q', TRUE);
        
        if (empty($term)) {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([]));
            return;
        }

        $this->db->group_start();
        $this->db->like('LOWER(konsumen_nama)', strtolower($term));
        $this->db->or_like('konsumen_nohp', $term);
        $this->db->group_end();
        $this->db->limit(20);
        $data = $this->db->get('tbl_konsumen')->result();

        $result = [];
        foreach ($data as $row) {
            $result[] = [
                'konsumen_id' => $row->konsumen_id,
                'konsumen_nama' => $row->konsumen_nama,
                'konsumen_nohp' => $row->konsumen_nohp
            ];
        }

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($result));
    }

    public function get_dashboard_stats()
    {
        $table = 'tbl_konsumen';

        // Get customer statistics
        $total_customers = $this->db->get($table)->num_rows();

        // Get customers with transactions
        $customers_with_transactions = 0;
        $top_customers = [];
        if (method_exists($this->M_transaksi, 'get_konsumen_dengan_transaksi')) {
            $top_customers = $this->M_transaksi->get_konsumen_dengan_transaksi()->result();
            $customers_with_transactions = count($top_customers);
        }

        $stats = [
            'total_customers' => $total_customers,
            'customers_with_transactions' => $customers_with_transactions,
            'top_customers_count' => count($top_customers),
            'conversion_rate' => $total_customers > 0 ? round(($customers_with_transactions / $total_customers) * 100, 1) : 0
        ];

        echo json_encode($stats);
    }


}


?>