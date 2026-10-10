<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Warehouse_model extends CI_Model
{

    public function __construct()
    {
        parent::__construct();
    }

    // =====================================================
    // WAREHOUSE MANAGEMENT METHODS
    // =====================================================

    /**
     * Get all warehouses
     */
    public function get_all_warehouses()
    {
        return $this->db->get('tbl_gudang')->result();
    }

    /**
     * Get warehouse by ID
     */
    public function get_warehouse_by_id($warehouse_id)
    {
        return $this->db->get_where('tbl_gudang', ['gudang_id' => $warehouse_id])->row();
    }

    /**
     * Get warehouse racks (locations)
     */
    public function get_warehouse_locations($warehouse_id = null)
    {
        if ($warehouse_id) {
            $this->db->where('gudang_id', $warehouse_id);
        }
        $this->db->where('is_active', 1);
        $this->db->order_by('rak_kode', 'ASC');
        return $this->db->get('tbl_rak')->result();
    }

    /**
     * Get rack by ID
     */
    public function get_rak_by_id($rak_id)
    {
        return $this->db->get_where('tbl_rak', ['rak_id' => $rak_id])->row();
    }

    /**
     * Get warehouse racks with warehouse details
     */
    public function get_racks_extended()
    {
        $this->db->select('r.*, g.gudang_nama, 
                          COUNT(DISTINCT sg.material_id) as total_items, 
                          SUM(sg.qty) as total_qty');
        $this->db->from('tbl_rak r');
        $this->db->join('tbl_gudang g', 'r.gudang_id = g.gudang_id');
        $this->db->join('tbl_stok_gudang sg', 'r.rak_id = sg.rak_id', 'left');
        $this->db->group_by('r.rak_id');
        $this->db->order_by('g.gudang_nama, r.rak_kode');
        return $this->db->get()->result();
    }

    /**
     * Insert new rack
     */
    public function insert_rak($data)
    {
        return $this->db->insert('tbl_rak', $data);
    }

    /**
     * Update rack
     */
    public function update_rak($id, $data)
    {
        $this->db->where('rak_id', $id);
        return $this->db->update('tbl_rak', $data);
    }

    /**
     * Delete rack (Soft delete or hard delete based on requirement - doing hard delete here for master data)
     */
    public function delete_rak($id)
    {
        $this->db->where('rak_id', $id);
        return $this->db->delete('tbl_rak');
    }

    /**
     * Get warehouse setting
     */
    public function get_warehouse_setting($key)
    {
        $setting = $this->db->get_where('warehouse_settings', ['setting_key' => $key, 'is_active' => 1])->row();
        return $setting ? $setting->setting_value : null;
    }

    // =====================================================
    // STOCK MANAGEMENT METHODS
    // =====================================================

    /**
     * Get all stock across all warehouses
     */
    public function get_all_stock()
    {
        $this->db->select('sg.*, b.bahan_nama, m.varian, b.barcode, s.satuan_nama, g.gudang_nama');
        $this->db->from('tbl_stok_gudang sg');
        $this->db->join('tbl_material m', 'sg.material_id = m.material_id');
        $this->db->join('tbl_bahan b', 'm.bahan_id = b.bahan_id');
        $this->db->join('tbl_satuan s', 'm.satuan_gudang_id = s.satuan_id');
        $this->db->join('tbl_gudang g', 'sg.gudang_id = g.gudang_id');
        $this->db->order_by('g.gudang_id, b.bahan_nama');
        return $this->db->get()->result();
    }

    /**
     * Get stock by warehouse
     */
    public function get_stock_by_warehouse($warehouse_id)
    {
        $start_date = date('Y-m-01'); // First day of current month
        $this->db->select('sg.stok_id, sg.material_id, sg.gudang_id, sg.rak_id, sg.last_update');
        $this->db->select('sg.qty as qty_base, (sg.qty / IFNULL(m.konversi, 1)) as qty_stock');
        $this->db->select('b.bahan_nama, m.varian, m.konversi, m.barcode, s.satuan_nama, sp.satuan_nama as satuan_produksi, g.gudang_nama, r.rak_kode, r.rak_nama');

        // Dynamically change stat column based on warehouse type
        if ($warehouse_id == 1) {
            // For Main Warehouse, show how much has been Dispatched (Mutation Out)
            $this->db->select('(SELECT SUM(qty_out) FROM tbl_stok_log WHERE material_id = sg.material_id AND trx_type = "Mutation" AND gudang_id = 1 AND DATE(created_at) >= "' . $start_date . '") as total_stat_month');
            $this->db->select('"Dispatch" as stat_label');
        } else {
            // For Production Warehouse, show how much has been Consumed (Produksi Out)
            $this->db->select('(SELECT SUM(qty_out) FROM tbl_stok_log WHERE material_id = sg.material_id AND trx_type = "Produksi" AND gudang_id = ' . $warehouse_id . ' AND DATE(created_at) >= "' . $start_date . '") as total_stat_month');
            $this->db->select('"Produksi" as stat_label');
        }

        $this->db->from('tbl_stok_gudang sg');
        $this->db->join('tbl_material m', 'sg.material_id = m.material_id');
        $this->db->join('tbl_bahan b', 'm.bahan_id = b.bahan_id');
        $this->db->join('tbl_satuan s', 'm.satuan_gudang_id = s.satuan_id');
        $this->db->join('tbl_satuan sp', 'b.satuan_id = sp.satuan_id', 'left');
        $this->db->join('tbl_gudang g', 'sg.gudang_id = g.gudang_id');
        $this->db->join('tbl_rak r', 'sg.rak_id = r.rak_id', 'left');
        $this->db->where('sg.gudang_id', $warehouse_id);
        $this->db->order_by('b.bahan_nama');
        return $this->db->get()->result();
    }


    /**
     * Get stock by material
     */
    public function get_stock_by_material($material_id)
    {
        $this->db->select('sg.*, b.bahan_nama, m.varian, b.barcode, s.satuan_nama, g.gudang_nama');
        $this->db->from('tbl_stok_gudang sg');
        $this->db->join('tbl_material m', 'sg.material_id = m.material_id');
        $this->db->join('tbl_bahan b', 'm.bahan_id = b.bahan_id');
        $this->db->join('tbl_satuan s', 'm.satuan_gudang_id = s.satuan_id');
        $this->db->join('tbl_gudang g', 'sg.gudang_id = g.gudang_id');
        $this->db->where('sg.material_id', $material_id);
        return $this->db->get()->result();
    }

    /**
     * Get current stock for material in specific warehouse
     */
    public function get_current_stock($material_id, $warehouse_id)
    {
        $stock = $this->db->get_where('tbl_stok_gudang', [
            'material_id' => $material_id,
            'gudang_id' => $warehouse_id
        ])->row();

        if ($stock) {
            return (float) $stock->qty;
        }
        return 0;
    }

    /**
     * Update stock
     */
    public function update_stock($material_id, $warehouse_id, $qty_change, $reason = '')
    {
        $this->load->model('M_gudang');
        return $this->M_gudang->update_stock_with_log($material_id, $warehouse_id, $qty_change, 'System Update', 0, $reason);
    }

    // =====================================================
    // MUTATION MANAGEMENT METHODS
    // =====================================================

    /**
     * Get all mutations
     */
    public function get_all_mutations($filters = [])
    {
        $this->db->select('sl.*, b.bahan_nama, b.barcode, s.satuan_nama, g.gudang_nama, u.username as created_by_name,
                          sl.trx_type as jenis_mutasi,
                          (sl.qty_in + sl.qty_out) as qty_mutasi,
                          DATE(sl.created_at) as tanggal_mutasi,
                          TIME(sl.created_at) as waktu_mutasi');
        $this->db->from('tbl_stok_log sl');
        $this->db->join('tbl_material m', 'sl.material_id = m.material_id');
        $this->db->join('tbl_bahan b', 'm.bahan_id = b.bahan_id');
        $this->db->join('tbl_satuan s', 'm.satuan_gudang_id = s.satuan_id');
        $this->db->join('tbl_gudang g', 'sl.gudang_id = g.gudang_id', 'left');
        $this->db->join('users u', 'sl.user_id = u.user_id', 'left');

        if (!empty($filters['gudang_id'])) {
            $this->db->where('sl.gudang_id', $filters['gudang_id']);
        }

        if (!empty($filters['limit'])) {
            $this->db->limit($filters['limit']);
        }

        $this->db->order_by('sl.created_at DESC');
        return $this->db->get()->result();
    }

    /**
     * Get mutation by ID
     */
    public function get_mutation_by_id($mutation_id)
    {
        $this->db->select('sl.*, b.bahan_nama, b.barcode, s.satuan_nama, g.gudang_nama, u.username as created_by_name,
                          sl.trx_type as jenis_mutasi,
                          (sl.qty_in + sl.qty_out) as qty_mutasi,
                          DATE(sl.created_at) as tanggal_mutasi,
                          TIME(sl.created_at) as waktu_mutasi');
        $this->db->from('tbl_stok_log sl');
        $this->db->join('tbl_material m', 'sl.material_id = m.material_id');
        $this->db->join('tbl_bahan b', 'm.bahan_id = b.bahan_id');
        $this->db->join('tbl_satuan s', 'm.satuan_gudang_id = s.satuan_id');
        $this->db->join('tbl_gudang g', 'sl.gudang_id = g.gudang_id', 'left');
        $this->db->join('users u', 'sl.user_id = u.user_id', 'left');
        $this->db->where('sl.log_id', $mutation_id);
        return $this->db->get()->row();
    }

    /**
     * Create mutation
     */
    public function create_mutation($data)
    {
        // Legacy method - functionality moved to update_stock_with_log
        return false;
    }

    /**
     * Process stock movement for mutation
     */
    private function process_stock_movement($mutation_id)
    {
        $mutation = $this->get_mutation_by_id($mutation_id);
        if (!$mutation)
            return false;

        $movements = [];

        // Note: Using M_gudang approach is preferred, but keeping this for legacy compatibility
        // if needed. However, tbl_mutasi and tbl_stok_log are the actual tables.

        return true;
    }

    // =====================================================
    // REPORTING METHODS
    // =====================================================

    /**
     * Get stock summary by warehouse
     */
    public function get_stock_summary_by_warehouse()
    {
        $this->db->select('g.gudang_id, g.gudang_nama, 
                          COUNT(sg.material_id) as total_materials,
                          SUM(sg.qty) as total_stock,
                          SUM(CASE WHEN sg.qty <= m.stok_min THEN 1 ELSE 0 END) as low_stock_count');
        $this->db->from('tbl_gudang g');
        $this->db->join('tbl_stok_gudang sg', 'g.gudang_id = sg.gudang_id', 'left');
        $this->db->join('tbl_material m', 'sg.material_id = m.material_id', 'left');
        $this->db->group_by('g.gudang_id, g.gudang_nama');
        $this->db->order_by('g.gudang_id');
        return $this->db->get()->result();
    }

    /**
     * Get low stock materials
     */
    public function get_low_stock_materials($warehouse_id = null)
    {
        $this->db->select('sg.*, b.bahan_nama, m.varian, b.barcode, s.satuan_nama, g.gudang_nama, m.stok_min');
        $this->db->from('tbl_stok_gudang sg');
        $this->db->join('tbl_material m', 'sg.material_id = m.material_id');
        $this->db->join('tbl_bahan b', 'm.bahan_id = b.bahan_id');
        $this->db->join('tbl_satuan s', 'm.satuan_gudang_id = s.satuan_id');
        $this->db->join('tbl_gudang g', 'sg.gudang_id = g.gudang_id');
        $this->db->where('sg.qty <= m.stok_min');

        if ($warehouse_id) {
            $this->db->where('sg.gudang_id', $warehouse_id);
        }

        $this->db->order_by('g.gudang_id, b.bahan_nama');
        return $this->db->get()->result();
    }

    /**
     * Get accumulated stock for all materials across all warehouses
     */
    public function get_accumulated_stock()
    {
        // 1. Get all active warehouses for headers
        $warehouses = $this->db->get('tbl_gudang')->result();

        // 2. Build Dynamic Pivot Query
        $this->db->select('m.material_id, CONCAT(b.bahan_nama, " - ", m.varian) as name, s.satuan_nama as unit');

        // Add dynamic warehouse columns
        foreach ($warehouses as $w) {
            $this->db->select("MAX(CASE WHEN sg.gudang_id = {$w->gudang_id} THEN sg.qty ELSE 0 END) as wh_{$w->gudang_id}");
        }

        $this->db->select('SUM(COALESCE(sg.qty, 0)) as total_qty');
        $this->db->from('tbl_material m');
        $this->db->join('tbl_bahan b', 'm.bahan_id = b.bahan_id');
        $this->db->join('tbl_satuan s', 'm.satuan_gudang_id = s.satuan_id');
        $this->db->join('tbl_stok_gudang sg', 'm.material_id = sg.material_id', 'left');
        $this->db->group_by('m.material_id, b.bahan_nama, m.varian, s.satuan_nama');
        $this->db->order_by('b.bahan_nama', 'ASC');

        $query_results = $this->db->get()->result();

        // 3. Reformat result to match expected frontend structure (optional but keeps compatibility)
        $data = [];
        foreach ($query_results as $row) {
            $item = [
                'material_id' => $row->material_id,
                'name' => $row->name,
                'unit' => $row->unit,
                'total_qty' => (float) $row->total_qty,
                'warehouses' => []
            ];

            foreach ($warehouses as $w) {
                $col = "wh_{$w->gudang_id}";
                $item['warehouses'][$w->gudang_id] = (float) $row->$col;
            }
            $data[] = $item;
        }

        return [
            'warehouses' => $warehouses,
            'data' => $data
        ];
    }

    /**
     * Get mutation summary by date range
     */
    public function get_mutation_summary($date_from, $date_to, $warehouse_id = null)
    {
        $this->db->select('sl.trx_type as jenis_mutasi, 
                          COUNT(*) as total_mutations,
                          SUM(sl.qty_in + sl.qty_out) as total_qty,
                          b.bahan_nama, s.satuan_nama');
        $this->db->from('tbl_stok_log sl');
        $this->db->join('tbl_material m', 'sl.material_id = m.material_id');
        $this->db->join('tbl_bahan b', 'm.bahan_id = b.bahan_id');
        $this->db->join('tbl_satuan s', 'm.satuan_gudang_id = s.satuan_id');
        $this->db->where('sl.created_at >=', $date_from);
        $this->db->where('sl.created_at <=', $date_to);

        if ($warehouse_id) {
            $this->db->where('sl.gudang_id', $warehouse_id);
        }

        $this->db->group_by('sl.trx_type, sl.material_id, b.bahan_nama, s.satuan_nama');
        $this->db->order_by('sl.trx_type, b.bahan_nama');
        return $this->db->get()->result();
    }

    // =====================================================
    // UTILITY METHODS
    // =====================================================

    /**
     * Get materials for dropdown
     */
    public function get_materials_dropdown()
    {
        $this->db->select('m.material_id, b.bahan_nama, b.barcode as bahan_barcode, m.varian, m.barcode as varian_barcode');
        $this->db->from('tbl_material m');
        $this->db->join('tbl_bahan b', 'm.bahan_id = b.bahan_id', 'left');
        $this->db->where_in('m.status', [1, '1']);
        $this->db->order_by('b.bahan_nama', 'ASC');
        return $this->db->get()->result();
    }

    /**
     * Get suppliers for dropdown
     */
    public function get_suppliers_dropdown()
    {
        $this->db->select('supplier_id, supplier_nama');
        $this->db->from('tbl_supplier');
        $this->db->order_by('supplier_nama');
        return $this->db->get()->result();
    }

    /**
     * Get customers for dropdown
     */
    public function get_customers_dropdown()
    {
        $this->db->select('konsumen_id, konsumen_nama');
        $this->db->from('tbl_konsumen');
        $this->db->where('status', 1);
        $this->db->order_by('konsumen_nama');
        return $this->db->get()->result();
    }

    /**
     * Validate stock availability
     */
    public function validate_stock_availability($material_id, $warehouse_id, $qty_needed)
    {
        $current_stock = $this->get_current_stock($material_id, $warehouse_id);
        return $current_stock >= $qty_needed;
    }

    /**
     * Get stock movements for material
     */
    public function get_stock_movements($material_id, $warehouse_id = null, $limit = 50)
    {
        $this->db->select('sl.*, b.bahan_nama, sl.trx_type as jenis_mutasi');
        $this->db->from('tbl_stok_log sl');
        $this->db->join('tbl_material m', 'sl.material_id = m.material_id');
        $this->db->join('tbl_bahan b', 'm.bahan_id = b.bahan_id');
        $this->db->where('sl.material_id', $material_id);

        if ($warehouse_id) {
            $this->db->where('sl.gudang_id', $warehouse_id);
        }

        $this->db->order_by('sl.created_at DESC');
        $this->db->limit($limit);
        return $this->db->get()->result();
    }

    /**
     * Get purchase temp items (cart)
     */
    public function get_purchase_temp_items()
    {
        $this->db->select('tp.*, m.varian, b.bahan_nama, s.satuan_nama');
        $this->db->from('tbl_temp_beli tp');
        $this->db->join('tbl_material m', 'tp.material_id = m.material_id');
        $this->db->join('tbl_bahan b', 'm.bahan_id = b.bahan_id');
        $this->db->join('tbl_satuan s', 'm.satuan_gudang_id = s.satuan_id');
        return $this->db->get()->result();
    }

    /**
     * Get purchase temp total
     */
    public function get_purchase_temp_total()
    {
        $this->db->select('SUM(qty * harga) as total');
        $res = $this->db->get('tbl_temp_beli')->row();
        return $res ? (float) $res->total : 0;
    }

    /**
     * Get dispatch temp items (cart)
     */
    public function get_dispatch_temp_items()
    {
        $this->db->select('tp.*, m.varian, b.bahan_nama, s.satuan_nama');
        $this->db->from('tbl_temp_pengambilan tp');
        $this->db->join('tbl_material m', 'tp.material_id = m.material_id');
        $this->db->join('tbl_bahan b', 'm.bahan_id = b.bahan_id');
        $this->db->join('tbl_satuan s', 'm.satuan_gudang_id = s.satuan_id');
        return $this->db->get()->result();
    }

    /**
     * Get material requests
     */
    public function get_requests($filters = [])
    {
        $this->db->select('pm.*, k.nama_lengkap as karyawan_nama, g.gudang_nama');
        $this->db->from('tbl_pengajuan_material pm');
        $this->db->join('tbl_karyawan k', 'pm.karyawan_id = k.karyawan_id', 'left');
        $this->db->join('tbl_gudang g', 'pm.gudang_id = g.gudang_id', 'left');

        if (isset($filters['status'])) {
            $this->db->where('pm.status', $filters['status']);
        }

        $this->db->order_by('pm.created_at', 'DESC');
        return $this->db->get()->result();
    }

    /**
     * Get material request details
     */
    public function get_request_details($pengajuan_id)
    {
        $this->db->select('pd.*, m.varian, b.bahan_nama, s.satuan_nama');
        $this->db->from('tbl_pengajuan_material_detail pd');
        $this->db->join('tbl_material m', 'pd.material_id = m.material_id');
        $this->db->join('tbl_bahan b', 'm.bahan_id = b.bahan_id');
        $this->db->join('tbl_satuan s', 'm.satuan_gudang_id = s.satuan_id');
        $this->db->where('pd.pengajuan_id', $pengajuan_id);
        return $this->db->get()->result();
    }

    /**
     * Get temp request items
     */
    public function get_temp_requests($user_id)
    {
        $this->db->select('tr.*, m.varian, b.bahan_nama, s.satuan_nama');
        $this->db->from('tbl_temp_pengajuan tr');
        $this->db->join('tbl_material m', 'tr.material_id = m.material_id');
        $this->db->join('tbl_bahan b', 'm.bahan_id = b.bahan_id');
        $this->db->join('tbl_satuan s', 'm.satuan_gudang_id = s.satuan_id');
        $this->db->where('tr.user_id', $user_id);
        return $this->db->get()->result();
    }
    /**
     * Get material requests by specific user
     */
    public function get_requests_by_user($user_id, $limit = 5)
    {
        $this->db->select('pm.*, g.gudang_nama');
        $this->db->from('tbl_pengajuan_material pm');
        $this->db->join('tbl_gudang g', 'pm.gudang_id = g.gudang_id', 'left');
        $this->db->join('users u', 'pm.karyawan_id = u.karyawan_id', 'left');
        $this->db->where('u.user_id', $user_id);
        $this->db->order_by('pm.created_at', 'DESC');
        if ($limit)
            $this->db->limit($limit);
        return $this->db->get()->result();
    }

    /**
     * Get pending material requests from production
     */
    public function get_pending_requests($limit = 10)
    {
        $this->db->select('pm.*, k.nama_lengkap, g.gudang_nama');
        $this->db->from('tbl_pengajuan_material pm');
        $this->db->join('tbl_karyawan k', 'pm.karyawan_id = k.karyawan_id', 'left');
        $this->db->join('tbl_gudang g', 'pm.gudang_id = g.gudang_id', 'left');
        $this->db->where('pm.status', 0); // 0 = Pending
        $this->db->order_by('pm.created_at', 'DESC');
        if ($limit)
            $this->db->limit($limit);
        return $this->db->get()->result();
    }

    /**
     * Get operator activity logs for warehouse module
     */
    public function get_operator_logs($limit = 15)
    {
        $this->db->select('ual.*, u.username, k.nama_lengkap');
        $this->db->from('user_activity_log ual');
        $this->db->join('users u', 'ual.user_id = u.user_id', 'left');
        $this->db->join('tbl_karyawan k', 'u.karyawan_id = k.karyawan_id', 'left');
        $this->db->where('ual.module', 'Warehouse');
        $this->db->order_by('ual.timestamp', 'DESC');
        if ($limit)
            $this->db->limit($limit);
        return $this->db->get()->result();
    }

    /**
     * Factory Reset - DANGEROUS: Clears all process data for Warehouse and Production
     */
    public function factory_reset()
    {
        $this->db->trans_start();

        // Disable foreign key checks
        $this->db->query("SET FOREIGN_KEY_CHECKS = 0");

        $tables_to_truncate = [
            'tbl_bayar_hutang',
            'tbl_hutang',
            'tbl_pembelian',
            'tbl_pembelian_detail',
            'tbl_return_pembelian',
            'tbl_return_pembelian_detail',
            'tbl_stok_gudang',
            'stok_gudang',
            'tbl_stok_log',
            'tbl_stok_opname',
            'tbl_pengambilan',
            'tbl_pengambilan_detail',
            'tbl_temp_beli',
            'tbl_temp_pengambilan',
            'tbl_temp_pengajuan',
            'tbl_pengajuan_material',
            'tbl_pengajuan_material_detail',
            'tbl_stok_bahan'
        ];

        foreach ($tables_to_truncate as $table) {
            if ($this->db->table_exists($table)) {
                $this->db->query("TRUNCATE TABLE $table");
            }
        }

        // Additional: Clean financial logs specifically for Material Purchases (Kategori 1)
        if ($this->db->table_exists('tbl_pengeluaran')) {
            $this->db->where('kategori_pengeluaran_id', 1);
            $this->db->delete('tbl_pengeluaran');
        }

        // Re-enable foreign key checks
        $this->db->query("SET FOREIGN_KEY_CHECKS = 1");

        $this->db->trans_complete();

        return $this->db->trans_status();
    }
}
