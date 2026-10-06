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
    public function get_stock_by_warehouse($warehouse_id, $category = null)
    {
        $start_date = date('Y-m-01'); // First day of current month
        
        // Start from tbl_material to include items with no physical stock record yet
        // Aggregate stock per material to avoid duplicate material rows when multiple racks exist
        $this->db->select((int)$warehouse_id . ' as gudang_id, m.material_id');
        $this->db->select('IFNULL(SUM(sg.qty), 0) as qty_base');
        $this->db->select('(IFNULL(SUM(sg.qty), 0) / IFNULL(m.konversi, 1)) as qty_stock');
        $this->db->select('b.bahan_nama, b.jenis_barang, m.varian, m.konversi, m.barcode, m.stok_min, s.satuan_nama, sp.satuan_nama as satuan_produksi, g.gudang_nama');
        $this->db->select('GROUP_CONCAT(DISTINCT r.rak_kode ORDER BY r.rak_kode SEPARATOR ", ") as rack_codes');
        $this->db->select('GROUP_CONCAT(DISTINCT r.rak_nama ORDER BY r.rak_nama SEPARATOR ", ") as rack_names');

        // Dynamically change stat columns based on warehouse type
        if ($warehouse_id == 1) {
            // For Main Warehouse: 
            // In = Purchasing, Out = Dispatching to Production
            $this->db->select('(SELECT IFNULL(SUM(qty_in), 0) FROM tbl_stok_log WHERE material_id = m.material_id AND trx_type = "Pembelian" AND gudang_id = 1 AND DATE(created_at) >= "' . $start_date . '") as total_in_month');
            $this->db->select('(SELECT IFNULL(SUM(qty_out), 0) FROM tbl_stok_log WHERE material_id = m.material_id AND trx_type = "Mutation" AND gudang_id = 1 AND DATE(created_at) >= "' . $start_date . '") as total_out_month');
            $this->db->select('(SELECT IFNULL(SUM(qty_out), 0) FROM tbl_stok_log WHERE material_id = m.material_id AND trx_type = "Mutation" AND gudang_id = 1) as total_out_alltime');

            $this->db->select('"Pembelian" as in_label');
            $this->db->select('"Dispatch" as out_label');
        } else {
            // For Production Warehouse: 
            // In = Received from Dispatch, Out = Consumed in Production
            $this->db->select('(SELECT IFNULL(SUM(qty_in), 0) FROM tbl_stok_log WHERE material_id = m.material_id AND trx_type = "Mutation" AND gudang_id = ' . (int)$warehouse_id . ' AND DATE(created_at) >= "' . $start_date . '") as total_in_month');
            $this->db->select('(SELECT (IFNULL(SUM(CASE WHEN trx_type="Produksi" THEN qty_out ELSE 0 END), 0) - IFNULL(SUM(CASE WHEN trx_type="Return" THEN qty_in ELSE 0 END), 0)) FROM tbl_stok_log WHERE material_id = m.material_id AND trx_type IN ("Produksi", "Return") AND gudang_id = ' . (int)$warehouse_id . ' AND DATE(created_at) >= "' . $start_date . '") as total_out_month');
            $this->db->select('(SELECT (IFNULL(SUM(CASE WHEN trx_type="Produksi" THEN qty_out ELSE 0 END), 0) - IFNULL(SUM(CASE WHEN trx_type="Return" THEN qty_in ELSE 0 END), 0)) FROM tbl_stok_log WHERE material_id = m.material_id AND trx_type IN ("Produksi", "Return") AND gudang_id = ' . (int)$warehouse_id . ') as total_out_alltime');

            $this->db->select('"Dispatch" as in_label');
            $this->db->select('"Produksi" as out_label');
        }

        // Add total aggregation regardless of transaction type to calculate precise beginning balance
        $this->db->select('(SELECT IFNULL(SUM(qty_in), 0) FROM tbl_stok_log WHERE material_id = m.material_id AND gudang_id = ' . (int)$warehouse_id . ' AND DATE(created_at) >= "' . $start_date . '") as total_in_all_month');
        $this->db->select('(SELECT IFNULL(SUM(qty_out), 0) FROM tbl_stok_log WHERE material_id = m.material_id AND gudang_id = ' . (int)$warehouse_id . ' AND DATE(created_at) >= "' . $start_date . '") as total_out_all_month');

        $this->db->from('tbl_material m');
        $this->db->join('tbl_bahan b', 'm.bahan_id = b.bahan_id');
        $this->db->join('tbl_satuan s', 'm.satuan_gudang_id = s.satuan_id', 'left');
        $this->db->join('tbl_satuan sp', 'b.satuan_id = sp.satuan_id', 'left');
        
        // Left join to stok gudang specific to this warehouse
        $this->db->join('tbl_stok_gudang sg', 'm.material_id = sg.material_id AND sg.gudang_id = ' . (int)$warehouse_id, 'left');
        
        // Join warehouse table just to get the name
        $this->db->join('tbl_gudang g', 'g.gudang_id = ' . (int)$warehouse_id, 'left');
        
        $this->db->join('tbl_rak r', 'sg.rak_id = r.rak_id', 'left');
        
        // Ensure we only show active materials
        $this->db->where_in('m.status', [1, '1']);
        
        if ($category) {
            $this->db->where('b.jenis_barang', $category);
        }
        // Group by material to ensure single row per material
        $this->db->group_by('m.material_id');

        $this->db->order_by('total_out_alltime', 'DESC');
        $this->db->order_by('total_out_month', 'DESC');
        $this->db->order_by('b.bahan_nama', 'ASC');
        $this->db->order_by('m.varian', 'ASC');
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

        if (!empty($filters['date'])) {
            $this->db->where('DATE(sl.created_at)', $filters['date']);
        }

        if (!empty($filters['limit'])) {
            $this->db->limit($filters['limit']);
        }

        $this->db->order_by('sl.created_at DESC');
        return $this->db->get()->result();
    }

    /**
     * Get Detailed Log History for specific material in specific warehouse/rack
     */
    public function get_material_log_history($material_id, $warehouse_id, $rak_id = null)
    {
        $this->db->select('sl.*, u.username as user_nama');
        $this->db->from('tbl_stok_log sl');
        $this->db->join('users u', 'u.user_id = sl.user_id', 'left');
        $this->db->where('sl.material_id', $material_id);
        $this->db->where('sl.gudang_id', $warehouse_id);
        if ($rak_id !== null && $rak_id !== '') {
            $this->db->where('sl.rak_id', $rak_id);
        } else {
            $this->db->where('sl.rak_id IS NULL');
        }
        $this->db->order_by('sl.created_at', 'DESC');
        $this->db->limit(10); // Last 10 activities
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
    public function get_low_stock_materials($warehouse_id = 1)
    {
        $this->db->select('sg.*, b.bahan_nama, m.varian, b.barcode, s.satuan_nama, g.gudang_nama, m.stok_min, m.konversi, (m.stok_min * IFNULL(m.konversi, 1)) as stok_min_base');
        $this->db->from('tbl_stok_gudang sg');
        $this->db->join('tbl_material m', 'sg.material_id = m.material_id');
        $this->db->join('tbl_bahan b', 'm.bahan_id = b.bahan_id');
        $this->db->join('tbl_satuan s', 'm.satuan_gudang_id = s.satuan_id', 'left');
        $this->db->join('tbl_gudang g', 'sg.gudang_id = g.gudang_id');
        $this->db->where('sg.qty <= (m.stok_min * IFNULL(m.konversi, 1))');
        $this->db->where('m.stok_min >', 0); // Sync logic with get_critical_stock()

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
        // Single source of truth: tbl_stok_gudang stores quantities in BASE units.
        // Convert back to warehouse/display units so printed reports match the stock screen.
        $warehouses = $this->db->get('tbl_gudang')->result();
        $conversion = 'IFNULL(NULLIF(m.konversi, 0), 1)';

        $this->db->select('m.material_id, CONCAT(b.bahan_nama, " - ", m.varian) as name, s.satuan_nama as unit', false);

        // A material may be split across several racks. SUM is required; MAX loses stock on other racks.
        foreach ($warehouses as $w) {
            $warehouse_id = (int) $w->gudang_id;
            $this->db->select(
                "SUM(CASE WHEN sg.gudang_id = {$warehouse_id} THEN COALESCE(sg.qty, 0) ELSE 0 END) / {$conversion} as wh_{$warehouse_id}",
                false
            );
        }

        $this->db->select("SUM(COALESCE(sg.qty, 0)) / {$conversion} as total_qty", false);
        $this->db->select('(SELECT COUNT(*) FROM tbl_stok_log sl WHERE sl.material_id = m.material_id) as popularity', false);
        $this->db->from('tbl_material m');
        $this->db->join('tbl_bahan b', 'm.bahan_id = b.bahan_id', 'left');
        $this->db->join('tbl_satuan s', 'm.satuan_gudang_id = s.satuan_id', 'left');
        $this->db->join('tbl_stok_gudang sg', 'm.material_id = sg.material_id', 'left');
        $this->db->group_by('m.material_id, b.bahan_nama, m.varian, s.satuan_nama, m.konversi');
        $this->db->order_by('popularity', 'DESC');
        $this->db->order_by('b.bahan_nama', 'ASC');

        $query_results = $this->db->get()->result();

        $data = [];
        foreach ($query_results as $row) {
            $item = [
                'material_id' => $row->material_id,
                'name' => $row->name ?: ('Material #' . $row->material_id),
                'unit' => $row->unit ?: 'Unit',
                'total_qty' => (float) $row->total_qty,
                'warehouses' => []
            ];

            foreach ($warehouses as $w) {
                $col = "wh_{$w->gudang_id}";
                $item['warehouses'][$w->gudang_id] = (float) ($row->$col ?? 0);
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

    /**
     * Get daily stock movements
     */
    public function get_daily_stock_report($date_from, $date_to, $warehouse_id = null)
    {
        // Read directly from the stock ledger. LEFT JOIN keeps historical ledger rows
        // visible even if a material master record was later changed/deactivated.
        $this->db->select('
            DATE(sl.created_at) as tanggal,
            sl.material_id,
            COALESCE(b.bahan_nama, CONCAT("Material #", sl.material_id)) as bahan_nama,
            COALESCE(m.varian, "-") as varian,
            COALESCE(s.satuan_nama, s_prod.satuan_nama, "Unit") as satuan_nama,
            s_prod.satuan_nama as satuan_produksi,
            COALESCE(NULLIF(m.konversi, 0), 1) as konversi,
            sl.trx_type,
            SUM(sl.qty_in) as total_masuk,
            SUM(sl.qty_out) as total_keluar,
            SUM(
                CASE
                    WHEN sl.qty_base > 0 THEN sl.qty_base
                    ELSE ABS(sl.qty_out) * COALESCE(NULLIF(m.konversi, 0), 1)
                END
            ) as total_produksi_keluar
        ', false);
        $this->db->from('tbl_stok_log sl');
        $this->db->join('tbl_material m', 'sl.material_id = m.material_id', 'left');
        $this->db->join('tbl_bahan b', 'm.bahan_id = b.bahan_id', 'left');
        $this->db->join('tbl_satuan s', 'm.satuan_gudang_id = s.satuan_id', 'left');
        $this->db->join('tbl_satuan s_prod', 'b.satuan_id = s_prod.satuan_id', 'left');
        $this->db->where('DATE(sl.created_at) >=', $date_from);
        $this->db->where('DATE(sl.created_at) <=', $date_to);

        if ($warehouse_id) {
            $this->db->where('sl.gudang_id', (int) $warehouse_id);
        }

        $this->db->group_by('DATE(sl.created_at), sl.material_id, sl.trx_type');
        $this->db->order_by('DATE(sl.created_at) DESC, bahan_nama ASC');

        return $this->db->get()->result();
    }

    /**
     * Get most frequently produced/dispatched materials
     */
    public function get_fast_moving_materials($limit = 10, $date_from = null, $date_to = null)
    {
        $this->db->select('b.bahan_nama, m.varian, s.satuan_nama,
                          COUNT(sl.log_id) as frekuensi,
                          SUM(sl.qty_out) as total_qty_keluar');
        $this->db->from('tbl_stok_log sl');
        $this->db->join('tbl_material m', 'sl.material_id = m.material_id');
        $this->db->join('tbl_bahan b', 'm.bahan_id = b.bahan_id');
        $this->db->join('tbl_satuan s', 'm.satuan_gudang_id = s.satuan_id', 'left');
        
        // Consider 'Mutation' (dispatch to prod) or 'Produksi' (consumed)
        $this->db->where_in('sl.trx_type', ['Mutation', 'Produksi', 'Pengambilan']);
        $this->db->where('sl.qty_out >', 0);

        if ($date_from && $date_to) {
            $this->db->where('DATE(sl.created_at) >=', $date_from);
            $this->db->where('DATE(sl.created_at) <=', $date_to);
        }

        $this->db->group_by('sl.material_id');
        $this->db->order_by('total_qty_keluar', 'DESC');
        $this->db->limit($limit);
        
        return $this->db->get()->result();
    }

    /**
     * Get popular & frequently used materials for the selected warehouse
     * Includes fallback mechanism to show all-time items if recent 30-day logs are empty/low.
     */
    public function get_popular_materials($gudang_id, $limit = 4)
    {
        $out_type = ($gudang_id == 1) ? 'Mutation' : 'Produksi';
        
        // 1. Try querying usage logs from the last 30 days
        $start_date = date('Y-m-d', strtotime('-30 days'));
        
        $this->db->select('
            m.material_id, 
            b.bahan_nama, 
            m.varian, 
            m.konversi,
            s.satuan_nama as satuan_gudang, 
            sp.satuan_nama as satuan_produksi,
            COUNT(sl.log_id) as frekuensi,
            SUM(sl.qty_out) as total_qty_keluar,
            COALESCE(sg.qty, 0) as qty_base,
            (COALESCE(sg.qty, 0) / IFNULL(m.konversi, 1)) as qty_stock,
            m.stok_min
        ');
        $this->db->from('tbl_stok_log sl');
        $this->db->join('tbl_material m', 'sl.material_id = m.material_id');
        $this->db->join('tbl_bahan b', 'm.bahan_id = b.bahan_id');
        $this->db->join('tbl_satuan s', 'm.satuan_gudang_id = s.satuan_id', 'left');
        $this->db->join('tbl_satuan sp', 'b.satuan_id = sp.satuan_id', 'left');
        $this->db->join('tbl_stok_gudang sg', 'sg.material_id = m.material_id AND sg.gudang_id = ' . (int)$gudang_id, 'left');
        
        $this->db->where('sl.gudang_id', $gudang_id);
        $this->db->where('sl.trx_type', $out_type);
        $this->db->where('sl.qty_out >', 0);
        $this->db->where('DATE(sl.created_at) >=', $start_date);
        
        $this->db->group_by('sl.material_id, b.bahan_nama, m.varian, m.konversi, s.satuan_nama, sp.satuan_nama, sg.qty, m.stok_min');
        $this->db->order_by('total_qty_keluar', 'DESC');
        $this->db->limit($limit);
        
        $results = $this->db->get()->result();
        
        // 2. If fewer than 2 results are found, query all-time usage logs to guarantee data is present
        if (count($results) < 2) {
            $this->db->select('
                m.material_id, 
                b.bahan_nama, 
                m.varian, 
                m.konversi,
                s.satuan_nama as satuan_gudang, 
                sp.satuan_nama as satuan_produksi,
                COUNT(sl.log_id) as frekuensi,
                SUM(sl.qty_out) as total_qty_keluar,
                COALESCE(sg.qty, 0) as qty_base,
                (COALESCE(sg.qty, 0) / IFNULL(m.konversi, 1)) as qty_stock,
                m.stok_min
            ');
            $this->db->from('tbl_stok_log sl');
            $this->db->join('tbl_material m', 'sl.material_id = m.material_id');
            $this->db->join('tbl_bahan b', 'm.bahan_id = b.bahan_id');
            $this->db->join('tbl_satuan s', 'm.satuan_gudang_id = s.satuan_id', 'left');
            $this->db->join('tbl_satuan sp', 'b.satuan_id = sp.satuan_id', 'left');
            $this->db->join('tbl_stok_gudang sg', 'sg.material_id = m.material_id AND sg.gudang_id = ' . (int)$gudang_id, 'left');
            
            $this->db->where('sl.gudang_id', $gudang_id);
            $this->db->where('sl.trx_type', $out_type);
            $this->db->where('sl.qty_out >', 0);
            
            $this->db->group_by('sl.material_id, b.bahan_nama, m.varian, m.konversi, s.satuan_nama, sp.satuan_nama, sg.qty, m.stok_min');
            $this->db->order_by('total_qty_keluar', 'DESC');
            $this->db->limit($limit);
            
            $results = $this->db->get()->result();
        }
        
        return $results;
    }

    /**
     * Get summary of purchases by supplier
     */
    public function get_supplier_purchase_summary($date_from = null, $date_to = null)
    {
        $this->db->select('s.supplier_nama,
                          COUNT(p.pembelian_id) as total_transaksi,
                          SUM(p.pembelian_total) as total_nilai');
        $this->db->from('tbl_pembelian p');
        $this->db->join('tbl_supplier s', 'p.supplier_id = s.supplier_id');
        
        if ($date_from && $date_to) {
            $this->db->where('p.pembelian_tgl >=', $date_from);
            $this->db->where('p.pembelian_tgl <=', $date_to);
        }

        $this->db->group_by('p.supplier_id');
        $this->db->order_by('total_nilai', 'DESC');
        
        return $this->db->get()->result();
    }

    /**
     * OWNER REPORT 1: Current Inventory Valuation
     * Nilai total inventaris = stok aktif × harga beli terakhir
     */
    public function get_inventory_valuation()
    {
        $this->db->select('
            b.bahan_nama, m.varian, s.satuan_nama,
            sg.qty as stok_aktif,
            (SELECT pd.harga_beli 
             FROM tbl_pembelian_detail pd 
             WHERE pd.material_id = m.material_id 
             ORDER BY pd.pembelian_id DESC LIMIT 1) as harga_beli_terakhir,
            (sg.qty * (SELECT pd.harga_beli 
             FROM tbl_pembelian_detail pd 
             WHERE pd.material_id = m.material_id 
             ORDER BY pd.pembelian_id DESC LIMIT 1)) as nilai_stok,
            g.gudang_nama
        ');
        $this->db->from('tbl_stok_gudang sg');
        $this->db->join('tbl_material m', 'sg.material_id = m.material_id');
        $this->db->join('tbl_bahan b', 'm.bahan_id = b.bahan_id');
        $this->db->join('tbl_satuan s', 'm.satuan_gudang_id = s.satuan_id', 'left');
        $this->db->join('tbl_gudang g', 'sg.gudang_id = g.gudang_id', 'left');
        $this->db->where('sg.qty >', 0);
        $this->db->order_by('nilai_stok', 'DESC');
        return $this->db->get()->result();
    }

    /**
     * Get total sum of inventory value
     */
    public function get_total_inventory_value()
    {
        $this->db->select('SUM(sg.qty * (SELECT pd.harga_beli 
             FROM tbl_pembelian_detail pd 
             WHERE pd.material_id = m.material_id 
             ORDER BY pd.pembelian_id DESC LIMIT 1)) as total_value', FALSE);
        $this->db->from('tbl_stok_gudang sg');
        $this->db->join('tbl_material m', 'sg.material_id = m.material_id');
        $this->db->where('sg.qty >', 0);
        $result = $this->db->get()->row();
        return $result ? (float)$result->total_value : 0;
    }

    /**
     * OWNER REPORT 2: Critical Stock (below minimum or near zero)
     */    
    public function get_critical_stock()
    {
        $this->db->select('
            b.bahan_nama, m.varian, s.satuan_nama as satuan_gudang,
            "Gudang Utama" as gudang_nama,
            IFNULL(SUM(sg.qty), 0) as stok_aktif_base,
            m.stok_min,
            (m.stok_min * IFNULL(m.konversi, 1)) as stok_min_base,
            ((m.stok_min * IFNULL(m.konversi, 1)) - IFNULL(SUM(sg.qty), 0)) as kekurangan_base,
            m.konversi,
            sp.satuan_nama as satuan_produksi
        ');
        $this->db->from('tbl_material m');
        $this->db->join('tbl_stok_gudang sg', 'sg.material_id = m.material_id AND sg.gudang_id = 1', 'left'); // Only Gudang Utama
        $this->db->join('tbl_bahan b', 'm.bahan_id = b.bahan_id');
        $this->db->join('tbl_satuan s', 'm.satuan_gudang_id = s.satuan_id', 'left');
        $this->db->join('tbl_satuan sp', 'b.satuan_id = sp.satuan_id', 'left');
        $this->db->where('m.stok_min >', 0);
        $this->db->group_by('m.material_id');
        $this->db->having('stok_aktif_base <= stok_min_base');
        $this->db->order_by('kekurangan_base', 'DESC');

        return $this->db->get()->result();
    }

    /**
     * OWNER REPORT 3: Dead Stock — items with no outgoing movement for N days
     */
    public function get_dead_stock($days = 30)
    {
        $cutoff = date('Y-m-d', strtotime("-{$days} days"));
        $this->db->select('
            b.bahan_nama, m.varian, s.satuan_nama,
            g.gudang_nama,
            sg.qty as stok_aktif,
            MAX(sl.created_at) as last_movement
        ');
        $this->db->from('tbl_stok_gudang sg');
        $this->db->join('tbl_material m', 'sg.material_id = m.material_id');
        $this->db->join('tbl_bahan b', 'm.bahan_id = b.bahan_id');
        $this->db->join('tbl_satuan s', 'm.satuan_gudang_id = s.satuan_id', 'left');
        $this->db->join('tbl_gudang g', 'sg.gudang_id = g.gudang_id', 'left');
        $this->db->join('tbl_stok_log sl', 'sl.material_id = m.material_id AND sl.qty_out > 0', 'left');
        $this->db->where('sg.qty >', 0);
        $this->db->group_by('sg.material_id, sg.gudang_id');
        $this->db->having('last_movement IS NULL OR last_movement <', $cutoff);
        $this->db->order_by('last_movement', 'ASC');
        return $this->db->get()->result();
    }

    /**
     * OWNER REPORT 4: Monthly purchase trend (last 12 months)
     */
    public function get_monthly_purchase_trend()
    {
        $this->db->select("
            DATE_FORMAT(p.pembelian_tgl, '%Y-%m') as bulan,
            DATE_FORMAT(p.pembelian_tgl, '%b %Y') as bulan_label,
            COUNT(p.pembelian_id) as total_transaksi,
            SUM(p.pembelian_total) as total_nilai
        ");
        $this->db->from('tbl_pembelian p');
        $this->db->where('p.pembelian_tgl >=', date('Y-m-d', strtotime('-12 months')));
        $this->db->group_by("DATE_FORMAT(p.pembelian_tgl, '%Y-%m')");
        $this->db->order_by('bulan', 'ASC');
        return $this->db->get()->result();
    }

    /**
     * OWNER REPORT 5: Purchase vs Usage comparison per material
     */
    public function get_purchase_vs_usage($date_from = null, $date_to = null)
    {
        $date_from = $date_from ?: date('Y-m-01');
        $date_to   = $date_to   ?: date('Y-m-t');

        // IMPORTANT:
        // Mutation/Pengambilan is an internal transfer between warehouses, not consumption.
        // Real material usage is recorded as Produksi. Counting both transfer and Produksi
        // makes monthly usage appear much larger than the real warehouse consumption.
        $sql = "
            SELECT
                sl.material_id,
                COALESCE(b.bahan_nama, CONCAT('Material #', sl.material_id)) as bahan_nama,
                COALESCE(m.varian, '-') as varian,
                COALESCE(NULLIF(m.konversi, 0), 1) as konversi,
                COALESCE(s.satuan_nama, sp.satuan_nama, 'Unit') as satuan_nama,
                sp.satuan_nama as satuan_produksi,
                IFNULL(SUM(CASE
                    WHEN sl.trx_type IN ('Pembelian', 'Pembelian/Koreksi')
                    THEN sl.qty_in ELSE 0 END), 0) as total_dibeli,
                IFNULL(SUM(CASE
                    WHEN sl.trx_type = 'Produksi'
                    THEN sl.qty_out ELSE 0 END), 0) as total_terpakai,
                IFNULL(SUM(CASE
                    WHEN sl.trx_type IN ('Pembelian', 'Pembelian/Koreksi')
                    THEN sl.qty_in ELSE 0 END), 0)
                -
                IFNULL(SUM(CASE
                    WHEN sl.trx_type = 'Produksi'
                    THEN sl.qty_out ELSE 0 END), 0) as sisa_bersih
            FROM tbl_stok_log sl
            LEFT JOIN tbl_material m ON sl.material_id = m.material_id
            LEFT JOIN tbl_bahan b ON m.bahan_id = b.bahan_id
            LEFT JOIN tbl_satuan s ON m.satuan_gudang_id = s.satuan_id
            LEFT JOIN tbl_satuan sp ON b.satuan_id = sp.satuan_id
            WHERE DATE(sl.created_at) BETWEEN ? AND ?
            GROUP BY sl.material_id, b.bahan_nama, m.varian, m.konversi, s.satuan_nama, sp.satuan_nama
            HAVING (total_dibeli > 0 OR total_terpakai > 0)
            ORDER BY total_terpakai DESC, bahan_nama ASC
        ";

        return $this->db->query($sql, [$date_from, $date_to])->result();
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
        $this->db->join('tbl_bahan b', 'm.bahan_id = b.bahan_id');
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
        $this->db->select('pd.*, m.varian, m.konversi, b.bahan_nama, s.satuan_nama, s2.satuan_nama as satuan_dasar_nama');
        $this->db->from('tbl_pengajuan_material_detail pd');
        $this->db->join('tbl_material m', 'pd.material_id = m.material_id');
        $this->db->join('tbl_bahan b', 'm.bahan_id = b.bahan_id');
        $this->db->join('tbl_satuan s', 'm.satuan_gudang_id = s.satuan_id');
        $this->db->join('tbl_satuan s2', 'b.satuan_id = s2.satuan_id', 'left');
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

    /**
     * Get recent stock mutations for all materials
     */
    public function get_recent_stock_mutations($limit = 50)
    {
        return $this->get_stock_mutations_filtered([], $limit);
    }

    public function get_stock_mutations_filtered($filters = [], $limit = 500)
    {
        $this->db->select('sl.*, b.bahan_nama, m.barcode, s.satuan_nama, s2.satuan_nama as satuan_dasar_nama, g.gudang_nama, u.username as created_by_name,
                          sl.trx_type as jenis_mutasi,
                          m.konversi,
                          (sl.qty_in + sl.qty_out) as qty_mutasi,
                          DATE(sl.created_at) as tanggal_mutasi,
                          TIME(sl.created_at) as waktu_mutasi');
        $this->db->from('tbl_stok_log sl');
        $this->db->join('tbl_material m', 'sl.material_id = m.material_id');
        $this->db->join('tbl_bahan b', 'm.bahan_id = b.bahan_id');
        $this->db->join('tbl_satuan s', 'm.satuan_gudang_id = s.satuan_id', 'left');
        $this->db->join('tbl_satuan s2', 'b.satuan_id = s2.satuan_id', 'left');
        $this->db->join('tbl_gudang g', 'sl.gudang_id = g.gudang_id', 'left');
        $this->db->join('users u', 'sl.user_id = u.user_id', 'left');

        if (!empty($filters['material_id'])) {
            $this->db->where('sl.material_id', $filters['material_id']);
        }
        if (!empty($filters['gudang_id'])) {
            $this->db->where('sl.gudang_id', $filters['gudang_id']);
        }
        if (!empty($filters['start_date'])) {
            $this->db->where('DATE(sl.created_at) >=', $filters['start_date']);
        }
        if (!empty($filters['end_date'])) {
            $this->db->where('DATE(sl.created_at) <=', $filters['end_date']);
        }

        if (!empty($filters['material_id'])) {
            // If filtering by material, show chronologically for ledger
            $this->db->order_by('sl.created_at', 'ASC');
        } else {
            $this->db->order_by('sl.created_at', 'DESC');
        }

        if ($limit && empty($filters['material_id'])) {
            $this->db->limit($limit);
        }

        return $this->db->get()->result();
    }

    public function get_saldo_awal($filters)
    {
        if (empty($filters['material_id'])) return 0;
        if (empty($filters['start_date'])) return 0;
        
        $this->db->select('SUM(sl.qty_in) as total_in, SUM(sl.qty_out) as total_out, MAX(m.konversi) as konversi');
        $this->db->from('tbl_stok_log sl');
        $this->db->join('tbl_material m', 'sl.material_id = m.material_id', 'left');
        $this->db->where('sl.material_id', $filters['material_id']);
        
        if (!empty($filters['gudang_id'])) {
            $this->db->where('sl.gudang_id', $filters['gudang_id']);
        }
        
        $this->db->where('DATE(sl.created_at) <', $filters['start_date']);
        
        $row = $this->db->get()->row();
        if ($row) {
            $konversi = (float)$row->konversi > 0 ? (float)$row->konversi : 1;
            $saldo_pack = (float)$row->total_in - (float)$row->total_out;
            return $saldo_pack * $konversi;
        }
        return 0;
    }

    /**
     * Get Total Purchases / Spending for the current month
     */
    public function get_monthly_spending($year = null, $month = null)
    {
        if (!$year) $year = date('Y');
        if (!$month) $month = date('m');

        $this->db->select('SUM(pd.qty * pd.harga_beli) as total_spending');
        $this->db->from('tbl_pembelian p');
        $this->db->join('tbl_pembelian_detail pd', 'p.pembelian_id = pd.pembelian_id', 'left');
        $this->db->where('YEAR(p.pembelian_tgl)', $year);
        $this->db->where('MONTH(p.pembelian_tgl)', $month);
        
        $result = $this->db->get()->row();
        return $result ? (float)$result->total_spending : 0;
    }

    /**
     * Get count of today's inbound (pembelian)
     */
    public function get_today_inbound_count() {
        $this->db->where('DATE(pembelian_tgl)', date('Y-m-d'));
        return $this->db->count_all_results('tbl_pembelian');
    }

    /**
     * Get count of today's outbound (pengambilan)
     */
    public function get_today_outbound_count() {
        $this->db->where('DATE(pengambilan_tgl)', date('Y-m-d'));
        return $this->db->count_all_results('tbl_pengambilan');
    }

    /**
     * Get Rack Utilization Statistics
     */
    public function get_rack_utilization()
    {
        $total_racks = $this->db->count_all('tbl_rak');
        if ($total_racks == 0) {
            return ['used' => 0, 'total' => 0, 'percentage' => 0];
        }
        
        $this->db->select('rak_id');
        $this->db->from('tbl_stok_gudang');
        $this->db->where('qty >', 0);
        $this->db->where('rak_id IS NOT NULL');
        $this->db->where('rak_id !=', 0);
        $this->db->group_by('rak_id');
        $used_racks = $this->db->get()->num_rows();
        
        $percentage = ($used_racks / $total_racks) * 100;
        
        return [
            'used' => $used_racks,
            'total' => $total_racks,
            'percentage' => round($percentage, 1)
        ];
    }

    /**
     * Get Dead Stock Count (No movement in last 90 days)
     */
    public function get_dead_stock_count($days = 90)
    {
        $date_threshold = date('Y-m-d', strtotime("-$days days"));
        
        $this->db->select('m.material_id');
        $this->db->from('tbl_material m');
        $this->db->join('tbl_stok_gudang sg', 'm.material_id = sg.material_id', 'inner');
        $this->db->where('sg.qty >', 0);
        $this->db->where("m.material_id NOT IN (SELECT material_id FROM tbl_stok_log WHERE created_at >= '$date_threshold')", NULL, FALSE);
        $this->db->group_by('m.material_id');
        
        $query = $this->db->get();
        return $query->num_rows();
    }

    /**
     * Get material movement history for a specific material in a specific warehouse
     */
    public function get_material_history($material_id, $gudang_id, $limit = 50)
    {
        $this->db->select('l.*, u.username, m.konversi, sg.satuan_nama as satuan_gudang, sp.satuan_nama as satuan_produksi');
        $this->db->from('tbl_stok_log l');
        $this->db->join('users u', 'u.user_id = l.user_id', 'left');
        $this->db->join('tbl_material m', 'm.material_id = l.material_id', 'left');
        $this->db->join('tbl_bahan b', 'b.bahan_id = m.bahan_id', 'left');
        $this->db->join('tbl_satuan sg', 'sg.satuan_id = m.satuan_gudang_id', 'left');
        $this->db->join('tbl_satuan sp', 'sp.satuan_id = b.satuan_id', 'left');
        $this->db->where('l.material_id', $material_id);
        $this->db->where('l.gudang_id', $gudang_id);
        $this->db->order_by('l.log_id', 'desc');
        $this->db->limit($limit);
        
        return $this->db->get()->result();
    }
}
