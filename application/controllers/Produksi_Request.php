<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Produksi_Request Controller
 * Dedicated to production operators for material requests.
 * Separated from Warehouse to avoid access control conflicts.
 */
class Produksi_Request extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Warehouse_model', 'warehouse');
        $this->load->model('M_gudang');
    }

    public function index()
    {
        $user_id = $this->session->userdata('id');

        $data = [
            'controller' => 'produksi_request',
            'function' => 'index',
            'title' => 'Pengajuan Material Produksi',
            'perusahaan' => $this->data['perusahaan'],
            'hakakses' => $this->data['hakakses'],
            'materials' => $this->warehouse->get_materials_dropdown(),
            'warehouses' => $this->warehouse->get_all_warehouses(),
            'temp_items' => $this->warehouse->get_temp_requests($user_id),
            'recent_requests' => $this->warehouse->get_requests_by_user($user_id)
        ];

        $this->load_admin_view('admin/produksi/request_form', $data);
    }

    public function add_item()
    {
        if ($this->input->post()) {
            $data = [
                'material_id' => $this->input->post('material_id', true),
                'qty' => $this->input->post('qty', true),
                'keterangan' => $this->input->post('keterangan', true),
                'user_id' => $this->session->userdata('id')
            ];
            $this->db->insert('tbl_temp_pengajuan', $data);
            $this->session->set_flashdata('success', 'Item ditambahkan.');
        }
        redirect('produksi_request');
    }

    public function delete_item($temp_id)
    {
        $this->db->delete('tbl_temp_pengajuan', [
            'temp_id' => $temp_id,
            'user_id' => $this->session->userdata('id')
        ]);
        $this->session->set_flashdata('success', 'Item dihapus.');
        redirect('produksi_request');
    }

    public function finalize()
    {
        if ($this->input->post()) {
            $user_id = $this->session->userdata('id');
            $gudang_id = $this->input->post('gudang_id', true); // Target Production Warehouse
            $pengajuan_tgl = $this->input->post('pengajuan_tgl', true) ?: date('Y-m-d');

            $temp_items = $this->warehouse->get_temp_requests($user_id);

            if (empty($temp_items)) {
                $this->session->set_flashdata('error', 'Daftar masih kosong!');
                redirect('produksi_request');
            }

            $this->db->trans_start();

            // 1. Header
            $header_data = [
                'pengajuan_tgl' => $pengajuan_tgl,
                'pengajuan_waktu' => date('H:i:s'),
                'karyawan_id' => $this->session->userdata('karyawan_id'),
                'gudang_id' => $gudang_id,
                'status' => 0, // Pending
                'perusahaan_id' => $this->session->userdata('perusahaan_id'),
                'created_at' => date('Y-m-d H:i:s')
            ];
            $this->db->insert('tbl_pengajuan_material', $header_data);
            $pengajuan_id = $this->db->insert_id();

            // 2. Details
            foreach ($temp_items as $item) {
                $detail_data = [
                    'pengajuan_id' => $pengajuan_id,
                    'material_id' => $item->material_id,
                    'qty' => $item->qty,
                    'keterangan' => $item->keterangan
                ];
                $this->db->insert('tbl_pengajuan_material_detail', $detail_data);
            }

            // 3. Clear Temp
            $this->db->delete('tbl_temp_pengajuan', ['user_id' => $user_id]);

            $this->db->trans_complete();

            if ($this->db->trans_status() === TRUE) {
                $this->session->set_flashdata('success', 'Pengajuan berhasil dikirim.');
            } else {
                $this->session->set_flashdata('error', 'Gagal mengirim pengajuan.');
            }
        }
        redirect('produksi_request');
    }
}
