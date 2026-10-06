<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Warehouse extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('Warehouse_model', 'warehouse');
        $this->load->model('M_gudang', 'm_gudang');
        $this->load->model('M_master');

        $this->_init_warehouse_tables();
    }

    /**
     * Ensure all required tables for Warehouse module exist
     */
    private function _init_warehouse_tables()
    {
        // 1. Temporary Table for Requests
        $this->db->query("CREATE TABLE IF NOT EXISTS tbl_temp_pengajuan (
            temp_id INT AUTO_INCREMENT PRIMARY KEY,
            material_id INT,
            qty DECIMAL(10,2),
            user_id INT,
            keterangan TEXT
        )");

        // 2. Main Request Table
        $this->db->query("CREATE TABLE IF NOT EXISTS tbl_pengajuan_material (
            pengajuan_id INT AUTO_INCREMENT PRIMARY KEY,
            pengajuan_tgl DATE,
            pengajuan_waktu TIME,
            karyawan_id INT,
            gudang_id INT,
            status INT DEFAULT 0,
            perusahaan_id INT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )");

        // 3. Request Detail Table
        $this->db->query("CREATE TABLE IF NOT EXISTS tbl_pengajuan_material_detail (
            detail_id INT AUTO_INCREMENT PRIMARY KEY,
            pengajuan_id INT,
            material_id INT,
            qty DECIMAL(10,2),
            keterangan TEXT
        )");
    }

    private function _get_conf()
    {
        return [
            'perusahaan' => $this->db->get_where('tbl_perusahaan', ['id_perusahaan' => $this->session->userdata('perusahaan_id')])->row_array(),
            'hakakses' => $this->db->get_where('tbl_divisi', ['divisi_id' => $this->session->userdata('level')])->row_array(),
        ];
    }

    /**
     * Dashboard - Overview of all warehouses
     */
    public function index()
    {
        $conf = $this->_get_conf();
        $data = [
            'controller' => 'warehouse',
            'function' => 'index',
            'title' => 'Warehouse Management System',
            'perusahaan' => $conf['perusahaan']['nama_perusahaan'],
            'hakakses' => $conf['hakakses']['divisi_nama'],
            // Lightweight initial data
            'warehouses' => $this->warehouse->get_all_warehouses(),
        ];

        $this->load_admin_view('admin/warehouse/dashboard', $data);
    }

    /**
     * SOP Guide - Visual instructions for warehouse operations
     */
    public function guide()
    {
        $conf = $this->_get_conf();
        $data = [
            'controller' => 'warehouse',
            'function' => 'guide',
            'title' => 'Panduan Operasional (SOP) Gudang',
            'perusahaan' => $conf['perusahaan']['nama_perusahaan'],
            'hakakses' => $conf['hakakses']['divisi_nama'],
        ];

        $this->load_admin_view('admin/warehouse/guide', $data);
    }

    /**
     * AJAX: Get Dashboard stats (Counters)
     */
    public function ajax_dashboard_stats()
    {
        $stock_summary = $this->warehouse->get_stock_summary_by_warehouse();
        $low_stock_materials = $this->warehouse->get_low_stock_materials();
        $recent_mutations = $this->warehouse->get_all_mutations(['limit' => 1]);
        $pending_requests = $this->warehouse->get_pending_requests();
        $rack_utilization = $this->warehouse->get_rack_utilization();
        $dead_stock_count = $this->warehouse->get_dead_stock_count();

        $data = [
            'inbound_today' => $this->warehouse->get_today_inbound_count(),
            'outbound_today' => $this->warehouse->get_today_outbound_count(),
            'low_stock_count' => count($low_stock_materials),
            'mutation_count' => count($this->warehouse->get_all_mutations(['date' => date('Y-m-d')])),
            'pending_requests_count' => count($pending_requests),
            'monthly_spending' => 'Rp ' . number_format($this->warehouse->get_monthly_spending(), 0, ',', '.'),
            'rack_utilization_pct' => $rack_utilization['percentage'],
            'rack_utilization_used' => $rack_utilization['used'],
            'rack_utilization_total' => $rack_utilization['total'],
            'dead_stock_count' => $dead_stock_count,
            'total_inventory_value' => 'Rp ' . number_format($this->warehouse->get_total_inventory_value(), 0, ',', '.')
        ];

        header('Content-Type: application/json');
        echo json_encode($data);
    }

    /**
     * AJAX: Get Today's Transactions for Modal
     */
    public function ajax_today_transactions($type)
    {
        $today = date('Y-m-d');
        if ($type === 'inbound') {
            $this->db->join('tbl_supplier', 'tbl_pembelian.supplier_id = tbl_supplier.supplier_id', 'left');
            $this->db->where('DATE(pembelian_tgl)', $today);
            $this->db->order_by('pembelian_id', 'DESC');
            $transactions = $this->db->get('tbl_pembelian')->result();
            
            foreach ($transactions as $t) {
                $this->db->select("GROUP_CONCAT(CONCAT(b.bahan_nama, ' (', pd.qty, ')') SEPARATOR '|||') as materials");
                $this->db->from('tbl_pembelian_detail pd');
                $this->db->join('tbl_material m', 'pd.material_id = m.material_id');
                $this->db->join('tbl_bahan b', 'm.bahan_id = b.bahan_id');
                $this->db->where('pd.pembelian_id', $t->pembelian_id);
                $detail = $this->db->get()->row();
                $t->materials_summary = $detail && $detail->materials ? $detail->materials : '-';
            }

            $data['transactions'] = $transactions;
            $data['type'] = 'inbound';
        } else {
            $this->db->join('tbl_karyawan', 'tbl_pengambilan.karyawan_id = tbl_karyawan.karyawan_id', 'left');
            $this->db->join('tbl_gudang', 'tbl_pengambilan.gudang_id = tbl_gudang.gudang_id', 'left');
            $this->db->where('DATE(pengambilan_tgl)', $today);
            $this->db->order_by('pengambilan_id', 'DESC');
            $transactions = $this->db->get('tbl_pengambilan')->result();

            foreach ($transactions as $t) {
                $this->db->select("GROUP_CONCAT(CONCAT(b.bahan_nama, ' (', pd.qty, ')') SEPARATOR '|||') as materials");
                $this->db->from('tbl_pengambilan_detail pd');
                $this->db->join('tbl_material m', 'pd.material_id = m.material_id');
                $this->db->join('tbl_bahan b', 'm.bahan_id = b.bahan_id');
                $this->db->where('pd.pengambilan_id', $t->pengambilan_id);
                $detail = $this->db->get()->row();
                $t->materials_summary = $detail && $detail->materials ? $detail->materials : '-';
            }

            $data['transactions'] = $transactions;
            $data['type'] = 'outbound';
        }
        $this->load->view('admin/warehouse/components/modal_today_transactions', $data);
    }

    /**
     * AJAX: Get Warehouse Summary Cards
     */
    public function ajax_warehouse_summary()
    {
        $data['stock_summary'] = $this->warehouse->get_stock_summary_by_warehouse();
        $this->load->view('admin/warehouse/components/warehouse_cards', $data);
    }

    /**
     * AJAX: Get Recent Activity & Alerts
     */
    public function ajax_recent_activity()
    {
        $data['recent_mutations'] = $this->warehouse->get_all_mutations(['limit' => 10]);
        $data['low_stock_materials'] = $this->warehouse->get_low_stock_materials();
        $data['pending_requests'] = $this->warehouse->get_pending_requests(5);
        $data['operator_logs'] = $this->warehouse->get_operator_logs(10);

        $this->load->view('admin/warehouse/components/live_monitor', $data);
    }

    /**
     * AJAX Search for Suppliers
     */
    public function get_supplier_options()
    {
        header('Content-Type: application/json');
        $search = $this->input->get('q', true);
        $this->db->select('supplier_id as id, supplier_nama as text, supplier_kode');
        $this->db->from('tbl_supplier');
        if ($search) {
            $this->db->group_start();
            $this->db->like('supplier_nama', $search);
            $this->db->or_like('supplier_kode', $search);
            $this->db->group_end();
        }
        $this->db->limit(50);
        $result = $this->db->get()->result();

        // Format for Select2
        foreach ($result as &$r) {
            $r->text = $r->text . " [" . $r->supplier_kode . "]";
        }

        echo json_encode(['results' => $result]);
        exit;
    }

    /**
     * AJAX: Get Transaction Monitor (Purchases & Dispatches to WIP)
     */
    public function ajax_transaction_monitor()
    {
        // 1. Get Latest Purchases
        $this->db->select('p.*, s.supplier_nama');
        $this->db->from('tbl_pembelian p');
        $this->db->join('tbl_supplier s', 'p.supplier_id = s.supplier_id', 'left');
        $this->db->order_by('p.pembelian_id', 'DESC');
        $this->db->limit(6);
        $data['latest_purchases'] = $this->db->get()->result();

        // 2. Get Latest Dispatches (to WIP/Production)
        $this->db->select('pg.*, g.gudang_nama');
        $this->db->from('tbl_pengambilan pg');
        $this->db->join('tbl_gudang g', 'pg.gudang_id = g.gudang_id', 'left');
        $this->db->order_by('pg.pengambilan_id', 'DESC');
        $this->db->limit(6);
        $data['latest_dispatches'] = $this->db->get()->result();

        $this->load->view('admin/warehouse/components/transaction_monitor', $data);
    }

    public function ajax_production_monitor($gudang_id = 0)
    {
        // For Main Warehouse (ID 1), show Dispatches (Mutation)
        // For Production Warehouses, show Consumption (Produksi)
        $type = ($gudang_id == 1) ? 'Mutation' : 'Produksi';
        $label_type = ($gudang_id == 1) ? 'Dispatch' : 'Produksi';

        $this->db->select('l.*, m.varian, b.bahan_nama, g.gudang_nama, u.username, k.nama_lengkap, m.konversi, sg.satuan_nama as satuan_gudang, sp.satuan_nama as satuan_produksi');
        $this->db->from('tbl_stok_log l');
        $this->db->join('tbl_material m', 'm.material_id = l.material_id');
        $this->db->join('tbl_bahan b', 'b.bahan_id = m.bahan_id');
        $this->db->join('tbl_gudang g', 'g.gudang_id = l.gudang_id');
        $this->db->join('users u', 'u.user_id = l.user_id', 'left');
        $this->db->join('tbl_karyawan k', 'u.karyawan_id = k.karyawan_id', 'left');
        $this->db->join('tbl_satuan sg', 'sg.satuan_id = m.satuan_gudang_id', 'left');
        $this->db->join('tbl_satuan sp', 'sp.satuan_id = b.satuan_id', 'left');

        $this->db->where('l.trx_type', $type);
        if ($gudang_id > 0) {
            $this->db->where('l.gudang_id', $gudang_id);
        }

        $this->db->order_by('l.created_at', 'DESC');
        $this->db->limit(15);
        $data['consumption_logs'] = $this->db->get()->result();
        $data['selected_gudang'] = $gudang_id;
        $data['monitor_type'] = $label_type;

        $this->load->view('admin/warehouse/components/live_consumption', $data);
    }

    /**
     * 1. PROCESS PEMESANAN MATERIAL (Material Request/Order)
     */
    public function order()
    {
        $conf = $this->_get_conf();
        $data = [
            'controller' => 'warehouse',
            'function' => 'order',
            'title' => 'Pemesanan Material',
            'perusahaan' => $conf['perusahaan']['nama_perusahaan'],
            'hakakses' => $conf['hakakses']['divisi_nama'],
            'materials' => $this->warehouse->get_materials_dropdown(),
            'suppliers' => $this->warehouse->get_suppliers_dropdown(),
            // Fetch low stock items as recommendations for ordering
            'recommendations' => $this->warehouse->get_low_stock_materials()
        ];

        $this->load_admin_view('admin/warehouse/order', $data);
    }

    /**
     * 2. PROCESS PEMBELIAN MATERIAL (Material Purchase/Recording)
     */
    public function purchase()
    {
        $conf = $this->_get_conf();
        $data = [
            'controller' => 'warehouse',
            'function' => 'purchase',
            'title' => 'Penerimaan & Pembelian Material',
            'perusahaan' => $conf['perusahaan']['nama_perusahaan'],
            'hakakses' => $conf['hakakses']['divisi_nama'],
            'materials' => $this->warehouse->get_materials_dropdown(),
            'suppliers' => $this->warehouse->get_suppliers_dropdown(),
            'temp_items' => $this->warehouse->get_purchase_temp_items(),
            'cart_total' => $this->warehouse->get_purchase_temp_total(),
            'recent_purchases' => $this->m_gudang->getDataPembelian()->result(),
            'rekening' => $this->db->get_where('tbl_rekening', ['active' => '1'])->result() ?: $this->db->get('tbl_rekening')->result()
        ];

        $this->load_admin_view('admin/warehouse/purchase', $data);
    }

    /**
     * Add item to purchase cart
     */
    public function purchase_add_item()
    {
        if ($this->input->post()) {
            $data = [
                'material_id' => $this->input->post('material_id', true),
                'rak_id' => $this->input->post('rak_id', true),
                'qty' => $this->input->post('qty', true),
                'harga' => $this->input->post('harga', true),
            ];
            $this->db->insert('tbl_temp_beli', $data);
            // [USER ACTIVITY] Log draft input
            log_activity($this->session->userdata('id'), 'input_item_keranjang', 'Warehouse', $this->input->post('material_id', true));

            $this->session->set_flashdata('success', 'Item berhasil ditambahkan ke keranjang.');
        }
        redirect('warehouse/purchase');
    }

    /**
     * Delete item from purchase cart
     */
    public function purchase_delete_item($temp_id)
    {
        $this->db->delete('tbl_temp_beli', ['temp_id' => $temp_id]);
        $this->session->set_flashdata('success', 'Item berhasil dihapus.');
        redirect('warehouse/purchase');
    }

    /**
     * Finalize purchase transaction
     */
    public function purchase_finalize()
    {
        if ($this->input->post()) {
            $supplier_id = $this->input->post('supplier_id', true);
            $pembelian_tgl = $this->input->post('pembelian_tgl', true);
            $no_faktur = $this->input->post('no_faktur', true);
            $total = $this->input->post('pembelian_total', true);

            $metode_bayar = $this->input->post('metode_bayar', true);

            $temp_items = $this->warehouse->get_purchase_temp_items();

            if (empty($temp_items)) {
                $this->session->set_flashdata('error', 'Keranjang masih kosong!');
                redirect('warehouse/purchase');
            }

            $this->db->trans_start();

            // 1. Record Header
            $purchase_data = [
                'supplier_id' => $supplier_id,
                'pembelian_tgl' => $pembelian_tgl,
                'pembelian_waktu' => date('Y-m-d H:i:s'),
                'no_faktur' => $no_faktur,
                'metode_bayar' => $metode_bayar,
                'karyawan_id' => $this->session->userdata('id'),
                'pembelian_total' => $total
            ];
            $this->db->insert('tbl_pembelian', $purchase_data);
            $pembelian_id = $this->db->insert_id();

            // 2. Record Detail & Update Stock
            foreach ($temp_items as $item) {
                $detail_data = [
                    'pembelian_id' => $pembelian_id,
                    'material_id' => $item->material_id,
                    'rak_id' => $item->rak_id,
                    'qty' => $item->qty,
                    'harga_beli' => $item->harga,
                    'subtotal' => $item->qty * $item->harga
                ];
                $this->db->insert('tbl_pembelian_detail', $detail_data);

                // Update Stock (Main Warehouse ID 1) with specific Rack
                $this->m_gudang->update_stock_with_log(
                    $item->material_id,
                    1,
                    $item->qty,
                    'Pembelian',
                    $pembelian_id,
                    'Pembelian Ref: ' . $no_faktur,
                    $item->rak_id
                );
            }

            // 3. Payment Logic
            if ($metode_bayar == 'Cash' || $metode_bayar == 'Transfer') {
                $supplier = $this->db->get_where('tbl_supplier', ['supplier_id' => $supplier_id])->row();

                $pengeluaran_data = [
                    'pengeluaran_tgl' => $pembelian_tgl,
                    'kategori_pengeluaran_id' => 1, // Pembelanjaan Bahan Baku
                    'pengeluaran_detail' => 'Pembelian Material (No. Faktur: ' . $no_faktur . ')',
                    'pengeluaran_jumlah' => $total,
                    'pengeluaran_penerima' => $supplier ? $supplier->supplier_nama : 'Supplier ID: ' . $supplier_id,
                    'metode_bayar' => ($metode_bayar == 'Cash' ? 'tunai' : 'transfer'),
                    'user_id' => $this->session->userdata('id'),
                    'perusahaan_id' => 1, // Fix: Selalu dibebankan ke perusahaan utama (Pusat) / ID 1
                    'created_at' => date('Y-m-d H:i:s')
                ];

                if ($metode_bayar == 'Transfer') {
                    $pengeluaran_data['rekening_id'] = $this->input->post('rekening_id', true);
                    $pengeluaran_data['rekening_tujuan'] = $this->input->post('rekening_tujuan', true);
                }

                $this->db->insert('tbl_pengeluaran', $pengeluaran_data);
            } else if ($metode_bayar == 'Hutang') {
                $hutang_data = [
                    'pembelian_id' => $pembelian_id,
                    'supplier_id' => $supplier_id,
                    'total_tagihan' => $total,
                    'total_bayar' => 0,
                    'sisa_hutang' => $total,
                    'jatuh_tempo' => $this->input->post('jatuh_tempo', true),
                    'status' => '0', // Unpaid
                    'created_at' => date('Y-m-d H:i:s')
                ];
                $this->db->insert('tbl_hutang', $hutang_data);
            }

            // 3. Clear Temp
            $this->db->empty_table('tbl_temp_beli');

            $this->db->trans_complete();

            if ($this->db->trans_status() === TRUE) {
                // [AUDIT LOG] Manual entry for explicit tracking
                $this->_log_manual("Finalisasi Pembelian Material: $no_faktur", json_encode([
                    'supplier_id' => $supplier_id,
                    'total' => $total,
                    'items_count' => count($temp_items)
                ]), 'WRITE');

                // [USER ACTIVITY] For performance dashboard
                log_activity($this->session->userdata('id'), 'input_pembelian', 'Warehouse', $pembelian_id);

                $this->session->set_flashdata('success', 'Transaksi pembelian berhasil disimpan.');
            } else {
                $this->session->set_flashdata('error', 'Gagal menyimpan transaksi.');
            }
        }
        redirect('warehouse/purchase');
    }

    /**
     * Edit Purchase View
     */
    public function purchase_edit($pembelian_id)
    {
        $conf = $this->_get_conf();
        $pembelian = $this->db->get_where('tbl_pembelian', ['pembelian_id' => $pembelian_id])->row();

        if (!$pembelian) {
            $this->session->set_flashdata('error', 'Data pembelian tidak ditemukan.');
            redirect('warehouse/laporan_pembelian');
        }

        // Get Details
        $this->db->select('d.*, b.bahan_nama, m.varian, s.satuan_nama, r.rak_kode');
        $this->db->from('tbl_pembelian_detail d');
        $this->db->join('tbl_material m', 'd.material_id = m.material_id', 'left');
        $this->db->join('tbl_bahan b', 'm.bahan_id = b.bahan_id', 'left');
        $this->db->join('tbl_satuan s', 'm.satuan_gudang_id = s.satuan_id', 'left');
        $this->db->join('tbl_rak r', 'd.rak_id = r.rak_id', 'left');
        $this->db->where('d.pembelian_id', $pembelian_id);
        $details = $this->db->get()->result();

        // Get Rekening (if transfer)
        $pengeluaran = $this->db->get_where('tbl_pengeluaran', ['pengeluaran_detail' => 'Pembelian Material (No. Faktur: ' . $pembelian->no_faktur . ')'])->row();

        $data = [
            'controller' => 'warehouse',
            'function' => 'purchase_edit',
            'title' => 'Edit Pembelian Material',
            'perusahaan' => $conf['perusahaan']['nama_perusahaan'],
            'hakakses' => $conf['hakakses']['divisi_nama'],
            'pembelian' => $pembelian,
            'details' => $details,
            'pengeluaran' => $pengeluaran,
            'materials' => $this->warehouse->get_materials_dropdown(),
            'suppliers' => $this->warehouse->get_suppliers_dropdown(),
            'rekening' => $this->db->get_where('tbl_rekening', ['active' => '1'])->result() ?: $this->db->get('tbl_rekening')->result()
        ];

        $this->load_admin_view('admin/warehouse/purchase_edit', $data);
    }

    /**
     * Process Purchase Update
     */
    public function purchase_update($pembelian_id)
    {
        if ($this->input->post()) {
            $pembelian = $this->db->get_where('tbl_pembelian', ['pembelian_id' => $pembelian_id])->row();
            if (!$pembelian)
                redirect('warehouse/laporan_pembelian');

            $supplier_id = $this->input->post('supplier_id', true);
            $pembelian_tgl = $this->input->post('pembelian_tgl', true);
            $no_faktur = $this->input->post('no_faktur', true);
            $metode_bayar = $this->input->post('metode_bayar', true);

            $material_ids = $this->input->post('material_id', true);
            $rak_ids = $this->input->post('rak_id', true);
            $qtys = $this->input->post('qty', true);
            $hargas = $this->input->post('harga', true);

            if (empty($material_ids)) {
                $this->session->set_flashdata('error', 'Keranjang material tidak boleh kosong!');
                redirect('warehouse/purchase_edit/' . $pembelian_id);
            }

            $this->db->trans_start();

            // 1. REVERT OLD STOCK LOGIC
            $old_details = $this->db->get_where('tbl_pembelian_detail', ['pembelian_id' => $pembelian_id])->result();
            foreach ($old_details as $old) {
                // Reverse it by passing qty as negative
                $this->m_gudang->update_stock_with_log(
                    $old->material_id,
                    1,
                    -$old->qty,
                    'Koreksi/Edit Pembelian',
                    $pembelian_id,
                    'Revert Invoice: ' . $pembelian->no_faktur,
                    $old->rak_id
                );
            }

            // 2. Clear old detail, hutang, pengeluaran
            $this->db->delete('tbl_pembelian_detail', ['pembelian_id' => $pembelian_id]);
            $this->db->delete('tbl_hutang', ['pembelian_id' => $pembelian_id]);
            $this->db->delete('tbl_pengeluaran', ['pengeluaran_detail' => 'Pembelian Material (No. Faktur: ' . $pembelian->no_faktur . ')']);

            // 3. Process new data
            $total = 0;
            for ($i = 0; $i < count($material_ids); $i++) {
                $mId = $material_ids[$i];
                $rId = empty($rak_ids[$i]) ? null : $rak_ids[$i];
                $q = $qtys[$i];
                $h = str_replace(['Rp ', '.'], '', $hargas[$i]);
                $sub = $q * $h;
                $total += $sub;

                $detail_data = [
                    'pembelian_id' => $pembelian_id,
                    'material_id' => $mId,
                    'rak_id' => $rId,
                    'qty' => $q,
                    'harga_beli' => $h,
                    'subtotal' => $sub
                ];
                $this->db->insert('tbl_pembelian_detail', $detail_data);

                // Add new stock
                $this->m_gudang->update_stock_with_log(
                    $mId,
                    1,
                    $q,
                    'Pembelian/Koreksi',
                    $pembelian_id,
                    'Pembelian Ref (Edit): ' . $no_faktur,
                    $rId
                );
            }

            // 4. Update Header
            $purchase_data = [
                'supplier_id' => $supplier_id,
                'pembelian_tgl' => $pembelian_tgl,
                'no_faktur' => $no_faktur,
                'metode_bayar' => $metode_bayar,
                'pembelian_total' => $total
            ];
            $this->db->where('pembelian_id', $pembelian_id);
            $this->db->update('tbl_pembelian', $purchase_data);

            // 5. Re-create Payment Logic
            if ($metode_bayar == 'Cash' || $metode_bayar == 'Transfer') {
                $supplier = $this->db->get_where('tbl_supplier', ['supplier_id' => $supplier_id])->row();
                $pengeluaran_data = [
                    'pengeluaran_tgl' => $pembelian_tgl,
                    'kategori_pengeluaran_id' => 1,
                    'pengeluaran_detail' => 'Pembelian Material (No. Faktur: ' . $no_faktur . ')',
                    'pengeluaran_jumlah' => $total,
                    'pengeluaran_penerima' => $supplier ? $supplier->supplier_nama : 'Supplier ID: ' . $supplier_id,
                    'metode_bayar' => ($metode_bayar == 'Cash' ? 'tunai' : 'transfer'),
                    'user_id' => $this->session->userdata('id'),
                    'perusahaan_id' => 1, // Fix: Selalu dibebankan ke perusahaan utama
                    'created_at' => date('Y-m-d H:i:s')
                ];

                if ($metode_bayar == 'Transfer') {
                    $pengeluaran_data['rekening_id'] = $this->input->post('rekening_id', true);
                    $pengeluaran_data['rekening_tujuan'] = $this->input->post('rekening_tujuan', true);
                }
                $this->db->insert('tbl_pengeluaran', $pengeluaran_data);
            } else if ($metode_bayar == 'Hutang') {
                $hutang_data = [
                    'pembelian_id' => $pembelian_id,
                    'supplier_id' => $supplier_id,
                    'total_tagihan' => $total,
                    'total_bayar' => 0,
                    'sisa_hutang' => $total,
                    'jatuh_tempo' => $this->input->post('jatuh_tempo', true),
                    'status' => '0',
                    'created_at' => date('Y-m-d H:i:s')
                ];
                $this->db->insert('tbl_hutang', $hutang_data);
            }

            $this->db->trans_complete();

            if ($this->db->trans_status() === TRUE) {
                // [AUDIT LOG]
                $this->_log_manual("Edit Pembelian Material: $no_faktur", json_encode([
                    'supplier_id' => $supplier_id,
                    'total' => $total,
                    'items_count' => count($material_ids)
                ]), 'UPDATE');

                log_activity($this->session->userdata('id'), 'edit_pembelian', 'Warehouse', $pembelian_id);
                $this->session->set_flashdata('success', 'Transaksi pembelian berhasil diupdate.');
            } else {
                $this->session->set_flashdata('error', 'Gagal update transaksi.');
            }
        }
        redirect('warehouse/laporan_pembelian');
    }

    /**
     * 3. UPDATE STOK (Direct Adjustment)
     */
    public function stock()
    {
        $conf = $this->_get_conf();
        $gudang_id = $this->input->get('gudang_id', true) ?: 1;
        $category = $this->input->get('category', true);

        $data = [
            'controller' => 'warehouse',
            'function' => 'stock',
            'title' => 'Manajemen Stok Gudang',
            'perusahaan' => $conf['perusahaan']['nama_perusahaan'],
            'hakakses' => $conf['hakakses']['divisi_nama'],
            'warehouses' => $this->warehouse->get_all_warehouses(),
            'selected_gudang' => $gudang_id,
            'selected_category' => $category,
            'stock_data' => $this->warehouse->get_stock_by_warehouse($gudang_id, $category),
            'popular_materials' => $this->warehouse->get_popular_materials($gudang_id, 4)
        ];

        $this->load_admin_view('admin/warehouse/stock', $data);
    }

    /**
     * RESET STOK GUDANG & LOG PENGGUNAAN
     */
    public function reset_stock_gudang($gudang_id)
    {
        // Require specific permission or just allow if it's accessible.
        // Assuming user has access to warehouse module.
        if ($gudang_id) {
            $this->db->trans_start();

            // Reset stok_gudang to 0
            $this->db->where('gudang_id', $gudang_id);
            $this->db->update('tbl_stok_gudang', ['qty' => 0]);

            // Clear log usage (tbl_stok_log) for this warehouse
            $this->db->where('gudang_id', $gudang_id);
            $this->db->delete('tbl_stok_log');

            $this->db->trans_complete();

            if ($this->db->trans_status() === TRUE) {
                // Log action
                $this->_log_manual("Reset Stok & Penggunaan", "Gudang ID: $gudang_id", 'DELETE');
                $this->session->set_flashdata('success', 'Stok dan log penggunaan bahan gudang berhasil direset.');
            } else {
                $this->session->set_flashdata('error', 'Gagal mereset stok gudang.');
            }
        }
        redirect('warehouse/stock?gudang_id=' . $gudang_id);
    }

    /**
     * AJAX Detail Dispatch
     */
    public function ajax_detail_dispatch($material_id)
    {
        $gudang_id = $this->input->get('gudang_id');
        $start_date = date('Y-m-01');
        $end_date = date('Y-m-t');

        $this->db->select('sl.*, m.varian as material_nama, m.konversi, b.bahan_nama, s.satuan_nama as satuan_gudang, s_prod.satuan_nama as satuan_produksi, p.pengambilan_id as nospk, k.nama_lengkap as operator_nama, g.gudang_nama');
        $this->db->from('tbl_stok_log sl');
        $this->db->join('tbl_material m', 'm.material_id = sl.material_id', 'left');
        $this->db->join('tbl_bahan b', 'b.bahan_id = m.bahan_id', 'left');
        $this->db->join('tbl_satuan s', 's.satuan_id = m.satuan_gudang_id', 'left');
        $this->db->join('tbl_satuan s_prod', 's_prod.satuan_id = b.satuan_id', 'left');
        $this->db->join('tbl_pengambilan p', 'p.pengambilan_id = sl.trx_id', 'left');
        $this->db->join('users u', 'u.user_id = sl.user_id', 'left');
        $this->db->join('tbl_karyawan k', 'k.karyawan_id = COALESCE(sl.karyawan_id, u.karyawan_id, p.karyawan_id)', 'left');
        $this->db->join('tbl_gudang g', 'g.gudang_id = sl.gudang_id', 'left');
        
        $this->db->where('sl.trx_type', 'Mutation');
        $this->db->where('sl.material_id', $material_id);
        if ($gudang_id) {
            $this->db->where('sl.gudang_id', $gudang_id);
        }
        $this->db->where('DATE(sl.created_at) >=', $start_date);
        $this->db->where('DATE(sl.created_at) <=', $end_date);
        $this->db->order_by('sl.created_at', 'DESC');
        
        $data['logs'] = $this->db->get()->result();
        
        $html = $this->load->view('admin/warehouse/partial_dispatch_detail', $data, true);
        echo json_encode(['status' => 'success', 'html' => $html]);
    }

    /**
     * AJAX Detail Pembelian
     */
    public function ajax_detail_pembelian($material_id)
    {
        $gudang_id = $this->input->get('gudang_id');
        $start_date = date('Y-m-01');
        $end_date = date('Y-m-t');

        $this->db->select('sl.*, m.varian as material_nama, m.konversi, b.bahan_nama, s.satuan_nama as satuan_gudang, s_prod.satuan_nama as satuan_produksi, p.no_faktur as nospk, sup.supplier_nama, k.nama_lengkap as operator_nama, g.gudang_nama');
        $this->db->from('tbl_stok_log sl');
        $this->db->join('tbl_material m', 'm.material_id = sl.material_id', 'left');
        $this->db->join('tbl_bahan b', 'b.bahan_id = m.bahan_id', 'left');
        $this->db->join('tbl_satuan s', 's.satuan_id = m.satuan_gudang_id', 'left');
        $this->db->join('tbl_satuan s_prod', 's_prod.satuan_id = b.satuan_id', 'left');
        $this->db->join('tbl_pembelian p', 'p.pembelian_id = sl.trx_id', 'left');
        $this->db->join('tbl_supplier sup', 'sup.supplier_id = p.supplier_id', 'left');
        $this->db->join('users u', 'u.user_id = sl.user_id', 'left');
        $this->db->join('tbl_karyawan k', 'k.karyawan_id = COALESCE(sl.karyawan_id, u.karyawan_id, p.karyawan_id)', 'left');
        $this->db->join('tbl_gudang g', 'g.gudang_id = sl.gudang_id', 'left');
        
        $this->db->where('sl.trx_type', 'Pembelian');
        $this->db->where('sl.material_id', $material_id);
        if ($gudang_id) {
            $this->db->where('sl.gudang_id', $gudang_id);
        }
        $this->db->where('DATE(sl.created_at) >=', $start_date);
        $this->db->where('DATE(sl.created_at) <=', $end_date);
        $this->db->order_by('sl.created_at', 'DESC');
        
        $data['logs'] = $this->db->get()->result();
        
        $html = $this->load->view('admin/warehouse/partial_pembelian_detail', $data, true);
        echo json_encode(['status' => 'success', 'html' => $html]);
    }

    /**
     * 4. PENGIRIMAN STOK KE GUDANG PRODUKSI (Distribution)
     */
    public function dispatch()
    {
        $conf = $this->_get_conf();
        $data = [
            'controller' => 'warehouse',
            'function' => 'dispatch',
            'title' => 'Pengiriman Ke Produksi',
            'perusahaan' => $conf['perusahaan']['nama_perusahaan'],
            'hakakses' => $conf['hakakses']['divisi_nama'],
            'materials' => $this->warehouse->get_materials_dropdown(),
            'warehouses' => $this->warehouse->get_all_warehouses(),
            'temp_items' => $this->warehouse->get_dispatch_temp_items(),
            'recent_dispatches' => $this->m_gudang->getPengambilanWithDetails()->result(),
            'materials_ready' => $this->db
                ->select('m.*, b.bahan_nama, s.satuan_nama, COALESCE(sg.qty, 0) as jumlah_stok')
                ->from('tbl_material m')
                ->join('tbl_bahan b', 'm.bahan_id = b.bahan_id')
                ->join('tbl_stok_gudang sg', 'm.material_id = sg.material_id AND sg.gudang_id = 1', 'left')
                ->join('tbl_satuan s', 'm.satuan_gudang_id = s.satuan_id', 'left')
                ->where('sg.qty >', 0)
                ->order_by('b.bahan_nama', 'ASC')
                ->get()->result()
        ];

        $this->load_admin_view('admin/warehouse/dispatch', $data);
    }

    /**
     * Add item to dispatch cart
     */
    public function dispatch_add_item()
    {
        if ($this->input->post()) {
            $material_id = $this->input->post('material_id', true);
            $qty = $this->input->post('qty', true);
            $bahan_id = $this->input->post('bahan_id', true);

            // Fetch material konversi
            $material = $this->db->get_where('tbl_material', ['material_id' => $material_id])->row();
            $konversi = $material ? (float) ($material->konversi ?: 1) : 1;

            // Check availability in Main Warehouse (ID 1)
            $available = $this->warehouse->get_current_stock($material_id, 1);

            // Check if already in temp to sum up
            $existing = $this->db->get_where('tbl_temp_pengambilan', ['material_id' => $material_id])->row();
            $new_qty = ($existing ? $existing->qty : 0) + $qty;
            $new_qty_base = $new_qty * $konversi;

            if ($available < $new_qty_base) {
                $avail_in_unit = $available / $konversi;
                $this->session->set_flashdata('error', 'Stok tidak mencukupi! Tersedia: ' . number_format($avail_in_unit, 2) . ' (Di keranjang: ' . ($existing ? $existing->qty : 0) . ')');
                redirect('warehouse/dispatch');
                return;
            }

            if ($existing) {
                $this->db->where('temp_id', $existing->temp_id);
                $this->db->update('tbl_temp_pengambilan', [
                    'qty' => $new_qty,
                    'keterangan' => $this->input->post('keterangan', true) ?: $existing->keterangan
                ]);
            } else {
                $data = [
                    'material_id' => $material_id,
                    'bahan_id' => $bahan_id,
                    'qty' => $qty,
                    'keterangan' => $this->input->post('keterangan', true)
                ];
                $this->db->insert('tbl_temp_pengambilan', $data);
            }

            log_activity($this->session->userdata('id'), 'input_data_pengambilan', 'Warehouse', $material_id);
            $this->session->set_flashdata('success', 'Item berhasil ditambahkan ke rencana pengiriman.');
        }
        redirect('warehouse/dispatch');
    }

    /**
     * AJAX: Quick Add item to dispatch cart (Drag & Drop)
     */
    public function ajax_dispatch_add_item()
    {
        $material_id = $this->input->post('material_id', true);
        $bahan_id = $this->input->post('bahan_id', true);
        $qty = (float) ($this->input->post('qty', true) ?: 1);

        // Fetch material konversi
        $material = $this->db->get_where('tbl_material', ['material_id' => $material_id])->row();
        $konversi = $material ? (float) ($material->konversi ?: 1) : 1;

        // 1. Calculate total current quantity in temp for this material
        $q_temp = $this->db->query("SELECT SUM(qty) as total FROM tbl_temp_pengambilan WHERE material_id = ?", [$material_id])->row();
        $current_temp_total = (float) ($q_temp->total ?? 0);

        // 2. Identify Gudang Utama dynamically
        $g_utama = $this->db->get_where('tbl_gudang', ['gudang_nama' => 'Gudang Utama'])->row();
        $u_id = $g_utama ? $g_utama->gudang_id : 1;

        // 3. Check physical availability
        $q_stock = $this->db->query("SELECT qty FROM tbl_stok_gudang WHERE material_id = ? AND gudang_id = ?", [$material_id, $u_id])->row();
        $avail = $q_stock ? (float) $q_stock->qty : 0;

        $proposed = $current_temp_total + $qty;
        $proposed_base = $proposed * $konversi;

        if ($avail < $proposed_base) {
            $avail_unit = $avail / $konversi;
            echo json_encode([
                'status' => 'error',
                'message' => "STOK TIDAK CUKUP! Tersedia: $avail_unit. Di Picklist: $current_temp_total. Gagal menambah: $qty."
            ]);
            return;
        }

        // 3. Update existing row or insert new
        $existing = $this->db->get_where('tbl_temp_pengambilan', [
            'material_id' => $material_id
        ])->row();

        // 3. Update existing row (Increment Qty) or insert new
        if ($existing) {
            $this->db->where('temp_id', $existing->temp_id);
            $this->db->update('tbl_temp_pengambilan', ['qty' => $existing->qty + $qty]);
            echo json_encode(['status' => 'success', 'message' => 'Quantity bertambah (+1)']);
        } else {
            $this->db->insert('tbl_temp_pengambilan', [
                'material_id' => $material_id,
                'bahan_id' => $bahan_id,
                'qty' => $qty,
                'keterangan' => ''
            ]);
            echo json_encode(['status' => 'success', 'message' => 'Ditambahkan ke Picklist']);
        }
    }

    /**
     * AJAX: Update quantity of item already in dispatch cart
     */
    public function ajax_dispatch_update_qty()
    {
        $temp_id = $this->input->post('temp_id', true);
        $qty = (float) $this->input->post('qty', true);

        // Get current entry info
        $temp = $this->db->get_where('tbl_temp_pengambilan', ['temp_id' => $temp_id])->row();
        if (!$temp) {
            echo json_encode(['status' => 'error', 'message' => 'Data tidak ditemukan']);
            return;
        }

        // Fetch material konversi
        $material = $this->db->get_where('tbl_material', ['material_id' => $temp->material_id])->row();
        $konversi = $material ? (float) ($material->konversi ?: 1) : 1;

        // Check stock availability
        $available = $this->warehouse->get_current_stock($temp->material_id, 1);
        $qty_base = $qty * $konversi;

        if ($available < $qty_base) {
            $avail_unit = $available / $konversi;
            echo json_encode(['status' => 'error', 'message' => 'Stok tidak cukup! (Max: ' . $avail_unit . ')']);
            return;
        }

        // Update
        $this->db->where('temp_id', $temp_id);
        $this->db->update('tbl_temp_pengambilan', ['qty' => $qty]);

        echo json_encode(['status' => 'success', 'message' => 'Qty diperbarui']);
    }

    /**
     * Delete item from dispatch cart
     */
    public function dispatch_delete_item($temp_id)
    {
        $this->db->delete('tbl_temp_pengambilan', ['temp_id' => $temp_id]);
        $this->session->set_flashdata('success', 'Item berhasil dihapus.');
        redirect('warehouse/dispatch');
    }

    /**
     * Finalize dispatch transaction
     */
    public function dispatch_finalize()
    {
        if ($this->input->post()) {
            $gudang_id = $this->input->post('gudang_id', true);
            $pengambilan_tgl = $this->input->post('pengambilan_tgl', true);

            $temp_items = $this->warehouse->get_dispatch_temp_items();

            if (empty($temp_items)) {
                $this->session->set_flashdata('error', 'Rencana pengiriman masih kosong!');
                redirect('warehouse/dispatch');
            }

            $this->db->trans_start();

            // 1. Record Header
            $mut_data = [
                'pengambilan_tgl' => $pengambilan_tgl,
                'pengambilan_waktu' => date('Y-m-d H:i:s'),
                'karyawan_id' => $this->session->userdata('id'),
                'gudang_id' => $gudang_id,
            ];
            $this->db->insert('tbl_pengambilan', $mut_data);
            $pengambilan_id = $this->db->insert_id();

            // 2. Record Detail & Update Stock
            foreach ($temp_items as $item) {
                $det_data = [
                    'pengambilan_id' => $pengambilan_id,
                    'material_id' => $item->material_id,
                    'qty' => $item->qty,
                    'gudang_id' => $gudang_id,
                    'keterangan' => $item->keterangan ?? ''
                ];
                $this->db->insert('tbl_pengambilan_detail', $det_data);

                // Update Stocks
                $log_note = !empty($item->keterangan) ? " ($item->keterangan)" : "";

                // Out from Main (1)
                $this->m_gudang->update_stock_with_log($item->material_id, 1, -$item->qty, 'Mutation', $pengambilan_id, 'Dikirim ke Produksi' . $log_note);
                // In to Production (gudang_id)
                $this->m_gudang->update_stock_with_log($item->material_id, $gudang_id, $item->qty, 'Mutation', $pengambilan_id, 'Diterima dari Gudang Utama' . $log_note);
            }

            // 3. Clear Temp
            $this->db->empty_table('tbl_temp_pengambilan');

            $this->db->trans_complete();

            if ($this->db->trans_status() === TRUE) {
                // [AUDIT LOG]
                $this->_log_manual("Pengiriman Material ke Produksi", json_encode([
                    'gudang_tujuan' => $gudang_id,
                    'item_count' => count($temp_items)
                ]), 'WRITE');

                // [USER ACTIVITY]
                log_activity($this->session->userdata('id'), 'dispatch_material', 'Warehouse', $pengambilan_id);

                // We only set last_dispatch_id, the view will handle the success alert + print option
                $this->session->set_flashdata('last_dispatch_id', $pengambilan_id);
            } else {
                $this->session->set_flashdata('error', 'Gagal memproses pengiriman.');
            }
        }
        redirect('warehouse/dispatch');
    }

    /**
     * Print Surat Jalan (Delivery Note)
     */
    public function print_dispatch($id)
    {
        $this->db->select('p.*, g.gudang_nama, k.nama_lengkap as pengambil_nama, u.username as creator');
        $this->db->from('tbl_pengambilan p');
        $this->db->join('tbl_gudang g', 'p.gudang_id = g.gudang_id', 'left');
        $this->db->join('users u', 'p.karyawan_id = u.user_id', 'left');
        $this->db->join('tbl_karyawan k', 'u.karyawan_id = k.karyawan_id', 'left');
        $this->db->where('p.pengambilan_id', $id);
        $header = $this->db->get()->row_array();

        if (!$header)
            show_404();

        $this->db->select('pd.*, m.varian, b.bahan_nama, s.satuan_nama');
        $this->db->from('tbl_pengambilan_detail pd');
        $this->db->join('tbl_material m', 'pd.material_id = m.material_id');
        $this->db->join('tbl_bahan b', 'm.bahan_id = b.bahan_id');
        $this->db->join('tbl_satuan s', 'm.satuan_gudang_id = s.satuan_id');
        $this->db->where('pd.pengambilan_id', $id);
        $details = $this->db->get()->result();

        $data = [
            'header' => $header,
            'details' => $details,
            'title' => 'SURAT JALAN - DOC#' . str_pad($id, 5, '0', STR_PAD_LEFT)
        ];

        $this->load->view('admin/warehouse/print_dispatch', $data);
    }

    /**
     * 5. STOK OPNAME (Reconciliation)
     */
    public function opname()
    {
        $conf = $this->_get_conf();

        if ($this->input->post()) {
            $material_id = $this->input->post('material_id', true);
            $gudang_id = $this->input->post('gudang_id', true);
            $son_real = (float) $this->input->post('son_real', true);
            $son_aplikasi = (float) $this->input->post('son_aplikasi', true);
            $sync_now = $this->input->post('sync_now', true) == '1' ? true : false;
            $catatan = $this->input->post('catatan', true) ?: 'Koreksi Stok Opname';

            $this->db->trans_start();

            // 1. Record Opname
            $opname_data = [
                'material_id' => $material_id,
                'son_tgl' => date('Y-m-d H:i:s'),
                'son_aplikasi' => $son_aplikasi,
                'son_real' => $son_real,
                'gudang_id' => $gudang_id,
                'verifikasi' => ($sync_now ? '1' : '0'),
                'son_keterangan' => $catatan
            ];
            $this->db->insert('tbl_stok_opname', $opname_data);
            $opname_id = $this->db->insert_id();

            // 2. Sync if requested
            if ($sync_now) {
                $diff = $son_real - $son_aplikasi;
                if ($diff != 0) {
                    $this->m_gudang->update_stock_with_log(
                        $material_id,
                        $gudang_id,
                        $diff,
                        'Opname',
                        $opname_id,
                        "Auto-Sync Opname: " . ($diff > 0 ? "+" : "") . $diff . " unit. " . $catatan
                    );
                }
            }

            $this->db->trans_complete();

            if ($this->db->trans_status() === TRUE) {
                $msg = 'Data Opname berhasil disimpan.';
                if ($sync_now) {
                    $msg .= ' Stok telah diperbarui secara otomatis.';
                    // [AUDIT LOG]
                    $this->_log_manual("Stok Opname & Sinkronisasi: $material_id", "Real: $son_real, App: $son_aplikasi", 'WRITE');
                }

                // [USER ACTIVITY]
                log_activity($this->session->userdata('id'), 'stok_opname', 'Warehouse', $opname_id);

                $this->session->set_flashdata('success', $msg);
            } else {
                $this->session->set_flashdata('error', 'Gagal menyimpan stok opname.');
            }
            redirect('warehouse/opname');
        }

        $data = [
            'controller' => 'warehouse',
            'function' => 'opname',
            'title' => 'Stok Opname (Penyesuaian)',
            'perusahaan' => $conf['perusahaan']['nama_perusahaan'],
            'hakakses' => $conf['hakakses']['divisi_nama'],
            'materials' => $this->warehouse->get_materials_dropdown(),
            'warehouses' => $this->warehouse->get_all_warehouses(),
            'recent_opname' => $this->m_gudang->getDataStokOpname()->result()
        ];

        $this->load_admin_view('admin/warehouse/opname', $data);
    }

    /**
     * AJAX: Get current stock for material in specific warehouse
     */
    public function ajax_get_current_stock()
    {
        $material_id = $this->input->post('material_id', true);
        $warehouse_id = $this->input->post('gudang_id', true) ?: $this->input->post('warehouse_id', true);

        // Get Metadata FIRST to get conversion
        $this->db->select('m.bahan_id, m.konversi, sg.satuan_nama as satuan_gudang, sp.satuan_nama as satuan_produksi');
        $this->db->from('tbl_material m');
        $this->db->join('tbl_bahan b', 'b.bahan_id = m.bahan_id', 'left');
        $this->db->join('tbl_satuan sg', 'sg.satuan_id = m.satuan_gudang_id', 'left');
        $this->db->join('tbl_satuan sp', 'sp.satuan_id = b.satuan_id', 'left');
        $this->db->where('m.material_id', $material_id);
        $meta = $this->db->get()->row();

        $konversi = $meta ? (float) $meta->konversi : 1;
        if ($konversi <= 0) $konversi = 1;

        // Get Stock (SUM all racks)
        $this->db->select_sum('qty');
        $this->db->where(['material_id' => $material_id, 'gudang_id' => $warehouse_id]);
        $stock = $this->db->get('tbl_stok_gudang')->row();

        $base_qty = $stock && $stock->qty !== null ? (float) $stock->qty : 0;

        // CONVERT for Display (Display unit = Satuan Gudang)
        $display_qty = $base_qty / $konversi;

        // Get Products Using this Material (Limit 5 to avoid bloating)
        $usage_info = [];
        if ($meta && !empty($meta->bahan_id)) {
            $this->db->select('p.product_nama, bl.quantity, bl.calc_type');
            $this->db->from('bom_lines bl');
            $this->db->join('tbl_product p', 'p.product_id = bl.product_id', 'left');
            $this->db->where('bl.bahan_id', $meta->bahan_id);
            $this->db->group_by('p.product_id');
            $this->db->limit(10);
            $products = $this->db->get()->result();

            foreach ($products as $p) {
                $qty = (float)$p->quantity;
                if ($qty > 0 && $qty < 1) {
                    $cap = floor(1 / $qty) . ' Pcs/Bhn';
                } else {
                    $cap = '1 Pcs = ' . $qty . ' Bhn';
                }
                $usage_info[] = [
                    'product_nama' => $p->product_nama,
                    'kapasitas' => $cap
                ];
            }
        }

        header('Content-Type: application/json');
        echo json_encode([
            'current_stock' => $display_qty, // In Satuan Gudang
            'base_stock' => $base_qty,       // In Satuan Produksi
            'satuan_gudang' => !empty($meta->satuan_gudang) ? $meta->satuan_gudang : 'Unit',
            'satuan_produksi' => !empty($meta->satuan_produksi) ? $meta->satuan_produksi : (!empty($meta->satuan_gudang) ? $meta->satuan_gudang : 'Pcs'),
            'konversi' => $konversi,
            'usage_info' => $usage_info
        ]);
    }

    /**
     * AJAX: Get all stock for opname checklist modal
     */
    public function ajax_get_all_stock_opname()
    {
        $gudang_id = $this->input->post('gudang_id', true) ?: 1;
        $stocks = $this->warehouse->get_stock_by_warehouse($gudang_id);
        echo json_encode(['success' => true, 'data' => $stocks]);
    }
    public function ajax_get_inventory_detail()
    {
        $barcode = trim($this->input->post('barcode', true));
        $warehouse_id = $this->input->post('warehouse_id', true);

        if (empty($barcode)) {
            echo json_encode(['success' => false, 'message' => 'Barcode tidak boleh kosong.']);
            return;
        }

        // 1. Get Material Info by Barcode (Search master material first)
        $this->db->select('m.material_id, m.barcode, m.varian, m.konversi, b.bahan_nama, s.satuan_nama, sp.satuan_nama as satuan_produksi, r.rak_kode, sg.qty, sg.gudang_id as mat_gudang_id, sg.rak_id');
        $this->db->from('tbl_material m');
        $this->db->join('tbl_bahan b', 'b.bahan_id = m.bahan_id');
        $this->db->join('tbl_satuan s', 's.satuan_id = m.satuan_gudang_id');
        $this->db->join('tbl_satuan sp', 'sp.satuan_id = b.satuan_id', 'left');
        $this->db->join('tbl_stok_gudang sg', 'sg.material_id = m.material_id AND sg.gudang_id = ' . $this->db->escape($warehouse_id), 'left');
        $this->db->join('tbl_rak r', 'r.rak_id = sg.rak_id', 'left');
        $this->db->where('m.barcode', $barcode);
        $material = $this->db->get()->row();

        if ($material) {
            // Fill default values if no stock entry exists
            if (empty($material->mat_gudang_id)) {
                $material->qty = 0;
            }

            $history = $this->warehouse->get_material_log_history($material->material_id, $warehouse_id, $material->rak_id);
            echo json_encode(['success' => true, 'material' => $material, 'history' => $history]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Barcode ' . $barcode . ' tidak ditemukan di Master Data.']);
        }
    }


    /**
     * 6. KARTU STOK (Stock Ledger)
     */
    public function ledger($gudang_id = 0)
    {
        $conf = $this->_get_conf();
        $warehouses = $this->db->get('tbl_gudang')->result();

        $this->db->select('l.*, m.varian, b.bahan_nama, g.gudang_nama, r.rak_nama, u.username, m.konversi, sg.satuan_nama as satuan_gudang, sp.satuan_nama as satuan_produksi');
        $this->db->from('tbl_stok_log l');
        $this->db->join('tbl_material m', 'm.material_id = l.material_id');
        $this->db->join('tbl_bahan b', 'b.bahan_id = m.bahan_id');
        $this->db->join('tbl_gudang g', 'g.gudang_id = l.gudang_id');
        $this->db->join('tbl_rak r', 'r.rak_id = l.rak_id', 'left');
        $this->db->join('users u', 'u.user_id = l.user_id', 'left');
        $this->db->join('tbl_satuan sg', 'sg.satuan_id = m.satuan_gudang_id', 'left');
        $this->db->join('tbl_satuan sp', 'sp.satuan_id = b.satuan_id', 'left');

        if ($gudang_id > 0) {
            $this->db->where('l.gudang_id', $gudang_id);
        }

        $this->db->order_by('l.log_id', 'desc');
        $this->db->limit(100);
        $logs = $this->db->get()->result();

        // Fetch Stock Summary
        $stock_summary = [];
        if ($gudang_id > 0) {
            $stock_summary = $this->m_gudang->get_warehouse_stock($gudang_id);
        } else {
            $all_stock = $this->m_gudang->get_all_warehouse_stock();
            $grouped = [];
            foreach ($all_stock as $row) {
                if (!isset($grouped[$row->material_id])) {
                    $grouped[$row->material_id] = clone $row;
                    $grouped[$row->material_id]->gudang_qtys = [];
                    $grouped[$row->material_id]->qty_base = 0;
                }
                
                if (!isset($grouped[$row->material_id]->gudang_qtys[$row->gudang_id])) {
                    $grouped[$row->material_id]->gudang_qtys[$row->gudang_id] = 0;
                }
                $grouped[$row->material_id]->gudang_qtys[$row->gudang_id] += (float) $row->qty_base;
                $grouped[$row->material_id]->qty_base += (float) $row->qty_base;
            }
            $stock_summary = array_values($grouped);
        }

        $data = [
            'controller' => 'warehouse',
            'function' => 'ledger',
            'title' => 'Kartu Stok (Ledger)',
            'perusahaan' => $conf['perusahaan']['nama_perusahaan'],
            'hakakses' => $conf['hakakses']['divisi_nama'],
            'current_gudang' => $gudang_id,
            'warehouses' => $warehouses,
            'logs' => $logs,
            'stock_summary' => $stock_summary
        ];

        if ($this->input->is_ajax_request()) {
            $this->load->view('admin/warehouse/ledger_content', $data);
        } else {
            $this->load_admin_view('admin/warehouse/ledger', $data);
        }
    }

    /**
     * AJAX: Manual Stock Adjustment
     */
    public function actionAdjustmentStok()
    {
        $material_id = $this->input->post('material_id', true);
        $gudang_id = $this->input->post('gudang_id', true);
        $qty_aktual_input = (float) $this->input->post('qty_aktual', true);
        $catatan = $this->input->post('catatan', true) ?: 'Saldo Awal / Adjustment Manual';

        if (empty($material_id) || empty($gudang_id)) {
            echo json_encode(['success' => false, 'message' => 'Material dan Gudang harus dipilih.']);
            return;
        }
        
        // Convert display unit (from UI) to base unit
        $material = $this->db->where('material_id', $material_id)->get('tbl_material')->row();
        $konversi = ($material && !empty($material->konversi)) ? (float) $material->konversi : 1;
        $qty_aktual = $qty_aktual_input * $konversi;

        // 1. Get current stock in system
        $current = $this->db->where([
            'material_id' => $material_id,
            'gudang_id' => $gudang_id
        ])->get('tbl_stok_gudang')->row();

        $qty_sistem = $current ? (float) $current->qty : 0;

        $diff = $qty_aktual - $qty_sistem;

        if ($diff == 0) {
            echo json_encode(['success' => false, 'message' => 'Stok aktual sama dengan stok sistem. Tidak ada perubahan.']);
            return;
        }

        // Convert diff back to display units because update_stock_with_log expects display units
        $diff_display = $diff / $konversi;

        // 3. Verify Owner/Developer PIN from tbl_karyawan (pin_produksi)
        $submitted_pin = $this->input->post('owner_pin', true);

        $this->db->select('k.nama_lengkap, u.username');
        $this->db->from('tbl_karyawan k');
        $this->db->join('users u', 'u.karyawan_id = k.karyawan_id');
        $this->db->where('k.pin_produksi', $submitted_pin);
        $this->db->group_start();
        $this->db->where_in('u.role_id', [1, 5, 17]); // Admin, Operator Gudang, Owner Role
        $this->db->or_where('u.username', 'nizarsuryadi'); // Developer Bypass
        $this->db->group_end();
        
        $authorizer = $this->db->get()->row();

        if (!$authorizer) {
            echo json_encode(['success' => false, 'message' => 'VERIFIKASI GAGAL! PIN salah atau Anda tidak memiliki otoritas (Admin/Operator/Owner).']);
            return;
        }

        $catatan .= " - Diotorisasi oleh: " . $authorizer->nama_lengkap;

        // 4. Update stock with log (pass display units!)
        $result = $this->m_gudang->update_stock_with_log($material_id, $gudang_id, $diff_display, 'Adjustment', 0, $catatan);

        if ($result) {
            // [AUDIT LOG]
            $this->_log_manual("Adjustment Stok Manual: $material_id", "Qty Aktual (Base Unit): $qty_aktual, Diff: $diff_display (Display Unit), Catatan: $catatan", 'WRITE');
            // [USER ACTIVITY]
            log_activity($this->session->userdata('id'), 'stock_adjustment', 'Warehouse', $material_id);

            echo json_encode(['success' => true, 'message' => 'Stok berhasil diperbarui secara manual.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Gagal memperbarui stok.']);
        }
    }

    /**
     * AJAX: Get material list for selector
     */
    public function get_material_list_json()
    {
        $q = $this->input->get('q', true);
        $this->db->select('m.material_id as id, CONCAT(b.bahan_nama, " - ", m.varian) as text, s.satuan_nama as satuan');
        $this->db->from('tbl_material m');
        $this->db->join('tbl_bahan b', 'b.bahan_id = m.bahan_id');
        $this->db->join('tbl_satuan s', 's.satuan_id = m.satuan_gudang_id', 'left');
        if ($q) {
            $this->db->group_start();
            $this->db->like('b.bahan_nama', $q);
            $this->db->or_like('m.varian', $q);
            $this->db->group_end();
        }
        $this->db->limit(30);
        $data = $this->db->get()->result();

        header('Content-Type: application/json');
        echo json_encode($data);
    }

    /**
     * MASTER DATA: Audit Trail (Rekam Jejak)
     */
    public function audit_log()
    {
        $conf = $this->_get_conf();
        $data = [
            'controller' => 'warehouse',
            'function' => 'audit_log',
            'conf' => $conf
        ];

        // Filter parameters
        $filters = [
            'start_date' => $this->input->get('start_date'),
            'end_date'   => $this->input->get('end_date'),
            'material_id'=> $this->input->get('material_id'),
            'gudang_id'  => $this->input->get('gudang_id'),
        ];
        $data['filters'] = $filters;

        // Fetch logs (user activity and stock mutations)
        $this->load->model('Warehouse_model', 'warehouse_mdl');
        $data['activity_logs'] = $this->warehouse_mdl->get_operator_logs(500); // larger limit for the main page
        $data['stock_logs'] = $this->warehouse_mdl->get_stock_mutations_filtered($filters, 500);
        
        // If specific material is selected, get starting balance
        $data['saldo_awal'] = 0;
        if (!empty($filters['material_id'])) {
            $data['saldo_awal'] = $this->warehouse_mdl->get_saldo_awal($filters);
        }

        // Fetch master data for dropdowns
        $this->db->select('m.material_id, b.bahan_nama, m.varian');
        $this->db->from('tbl_material m');
        $this->db->join('tbl_bahan b', 'm.bahan_id = b.bahan_id');
        $this->db->where('m.status', '1');
        $this->db->order_by('b.bahan_nama', 'ASC');
        $data['materials'] = $this->db->get()->result();
        
        $data['warehouses'] = $this->db->get('tbl_gudang')->result();

        $this->load_admin_view('admin/warehouse/audit_log', $data);
    }

    /**
     * MASTER DATA: Rak Penyimpanan (Bin Locations)
     */
    public function racks()
    {
        $conf = $this->_get_conf();
        $data = [
            'controller' => 'warehouse',
            'function' => 'racks',
            'title' => 'Manajemen Rak Penyimpanan',
            'perusahaan' => $conf['perusahaan']['nama_perusahaan'],
            'hakakses' => $conf['hakakses']['divisi_nama'],
            'warehouses' => $this->warehouse->get_all_warehouses(),
            'racks' => $this->warehouse->get_racks_extended()
        ];

        $this->load_admin_view('admin/warehouse/racks', $data);
    }

    /**
     * AJAX: Update material rack location (Put-away / Move)
     */
    public function ajax_update_material_rack()
    {
        $material_id = $this->input->post('material_id', true);
        $gudang_id = $this->input->post('gudang_id', true);
        $rak_asal = $this->input->post('rak_asal', true);
        $rak_tujuan = $this->input->post('rak_tujuan', true);
        $catatan = $this->input->post('catatan', true) ?: 'Penataan Stok (Bin Location)';

        if (!$material_id || !$gudang_id || !$rak_tujuan) {
            echo json_encode(['status' => 'error', 'message' => 'Data tidak lengkap.']);
            return;
        }

        if ($rak_asal == $rak_tujuan) {
            echo json_encode(['status' => 'error', 'message' => 'Material sudah berada di rak tujuan yang sama.']);
            return;
        }

        $this->db->trans_start();

        // 1. Ambil stok di lokasi asal
        $source_where = [
            'material_id' => $material_id,
            'gudang_id' => $gudang_id,
            'rak_id' => (empty($rak_asal) ? NULL : $rak_asal)
        ];
        $source = $this->db->get_where('tbl_stok_gudang', $source_where)->row();

        if (!$source || $source->qty <= 0) {
            echo json_encode(['status' => 'error', 'message' => 'Stok di lokasi asal tidak ditemukan atau sudah kosong.']);
            return;
        }

        $qty_to_move = (float) $source->qty;

        // 2. Kurangi dari asal
        $this->m_gudang->update_stock_with_log(
            $material_id,
            $gudang_id,
            -$qty_to_move,
            'Mutasi Rak (Out)',
            0,
            $catatan . " (Pindah ke Rak ID: $rak_tujuan)",
            (empty($rak_asal) ? NULL : $rak_asal)
        );

        // 3. Tambah ke tujuan
        $this->m_gudang->update_stock_with_log(
            $material_id,
            $gudang_id,
            $qty_to_move,
            'Mutasi Rak (In)',
            0,
            $catatan . " (Masuk dari " . ($rak_asal ? "Rak ID: $rak_asal" : "Staging Area") . ")",
            $rak_tujuan
        );

        $this->db->trans_complete();

        if ($this->db->trans_status() === TRUE) {
            echo json_encode(['status' => 'success', 'message' => 'Koordinat rak berhasil diperbarui.']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Gagal memperbarui data rak.']);
        }
    }

    public function ajax_add_rak()
    {
        $data = [
            'rak_nama' => $this->input->post('rak_nama', true),
            'rak_kode' => $this->input->post('rak_kode', true),
            'gudang_id' => $this->input->post('gudang_id', true),
            'keterangan' => $this->input->post('keterangan', true),
            'is_active' => 1
        ];

        if ($this->warehouse->insert_rak($data)) {
            echo json_encode(['status' => 'success', 'message' => 'Rak berhasil ditambahkan.']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Gagal menambahkan rak.']);
        }
    }

    public function ajax_update_rak()
    {
        $id = $this->input->post('rak_id', true);
        $data = [
            'rak_nama' => $this->input->post('rak_nama', true),
            'rak_kode' => $this->input->post('rak_kode', true),
            'gudang_id' => $this->input->post('gudang_id', true),
            'keterangan' => $this->input->post('keterangan', true),
            'is_active' => $this->input->post('is_active', true)
        ];

        if ($this->warehouse->update_rak($id, $data)) {
            echo json_encode(['status' => 'success', 'message' => 'Data rak berhasil diperbarui.']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Gagal memperbarui data.']);
        }
    }

    public function ajax_delete_rak($id)
    {
        if ($this->warehouse->delete_rak($id)) {
            echo json_encode(['status' => 'success', 'message' => 'Rak berhasil dihapus.']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Gagal menghapus rak.']);
        }
    }

    /**
     * AJAX: Get products inside a specific rack
     */
    public function ajax_get_rack_contents($rak_id)
    {
        $this->db->select('sg.*, b.bahan_nama, m.varian, s.satuan_nama');
        $this->db->from('tbl_stok_gudang sg');
        $this->db->join('tbl_material m', 'sg.material_id = m.material_id');
        $this->db->join('tbl_bahan b', 'm.bahan_id = b.bahan_id');
        $this->db->join('tbl_satuan s', 'm.satuan_gudang_id = s.satuan_id');
        $this->db->where('sg.rak_id', $rak_id);
        $this->db->where('sg.qty >', 0);
        $this->db->order_by('b.bahan_nama', 'ASC');
        $contents = $this->db->get()->result();

        echo json_encode(['status' => 'success', 'data' => $contents]);
    }

    /**
     * 7. MASTER BAHAN & MATERIAL (Primary Data)
     */
    public function materials()
    {
        $conf = $this->_get_conf();
        $data = [
            'controller' => 'warehouse',
            'function' => 'materials',
            'title' => 'Master Bahan & Material',
            'perusahaan' => $conf['perusahaan']['nama_perusahaan'],
            'hakakses' => $conf['hakakses']['divisi_nama'],
            'satuan' => $this->db->get('tbl_satuan')->result(),
        ];

        $this->load_admin_view('admin/warehouse/materials', $data);
    }

    public function get_bahan_baku_json()
    {
        header('Content-Type: application/json');
        $data = $this->m_gudang->getAllBahanbaku()->result();
        echo json_encode(['status' => 'success', 'data' => $data]);
    }

    public function get_purchase_history_json()
    {
        header('Content-Type: application/json');
        $bahan_id = $this->input->post('bahan_id', true);
        if (!$bahan_id) {
            echo json_encode(['status' => 'error', 'message' => 'ID Bahan wajib diisi!']);
            return;
        }

        $this->db->select('pd.harga_beli, pd.qty, p.pembelian_tgl, p.no_faktur, s.supplier_nama, m.varian, m.konversi');
        $this->db->from('tbl_pembelian_detail pd');
        $this->db->join('tbl_pembelian p', 'p.pembelian_id = pd.pembelian_id', 'left');
        $this->db->join('tbl_supplier s', 's.supplier_id = p.supplier_id', 'left');
        $this->db->join('tbl_material m', 'm.material_id = pd.material_id', 'left');
        $this->db->where('m.bahan_id', $bahan_id);
        $this->db->order_by('p.pembelian_tgl', 'DESC');
        $this->db->order_by('pd.detail_id', 'DESC');
        $this->db->limit(30);
        $data = $this->db->get()->result();

        echo json_encode(['status' => 'success', 'data' => $data]);
    }

    public function actionAddBahanBaku()
    {
        $nama = trim($this->input->post('bahanbaku_nama', true));
        $barcode = trim($this->input->post('barcode', true));
        $satuan_id = $this->input->post('satuan_id', true);
        $jenis = $this->input->post('jenis_barang', true);

        if (empty($nama) || empty($satuan_id) || empty($jenis)) {
            echo json_encode(['status' => 'error', 'pesan' => 'Field wajib diisi!']);
            return;
        }

        // Jika barcode kosong, generate otomatis
        if (empty($barcode)) {
            $last = $this->db->select('barcode')->from('tbl_bahan')->order_by('CAST(barcode AS UNSIGNED)', 'DESC')->limit(1)->get()->row();
            $barcode = ($last && is_numeric($last->barcode)) ? str_pad($last->barcode + 1, 8, '0', STR_PAD_LEFT) : '00000001';
        }

        $data = [
            'bahan_nama' => $nama,
            'barcode' => $barcode,
            'jenis_barang' => $jenis,
            'satuan_id' => $satuan_id
        ];

        if ($this->db->insert('tbl_bahan', $data)) {
            $new_id = $this->db->insert_id();
            // [LOGGING]
            $this->_log_manual("Tambah Master Bahan Baku: $nama", json_encode($data), 'WRITE');
            // [USER ACTIVITY]
            log_activity($this->session->userdata('id'), 'add_master_bahan', 'Warehouse', $new_id);

            echo json_encode(['status' => 'success', 'pesan' => 'Bahan berhasil ditambahkan.']);
        } else {
            echo json_encode(['status' => 'error', 'pesan' => 'Gagal menyimpan data.']);
        }
    }

    public function actionEditBahanBaku()
    {
        $id = $this->input->post('bahanbaku_id', true);
        $nama = trim($this->input->post('bahanbaku_nama', true));
        $barcode = trim($this->input->post('barcode', true));
        $jenis = $this->input->post('jenis_barang', true);
        $satuan_id = $this->input->post('satuan_id', true);

        if (empty($nama) || empty($satuan_id) || empty($jenis) || empty($id)) {
            echo json_encode(['status' => 'error', 'pesan' => 'Data wajib harus diisi.']);
            return;
        }

        $data = [
            'bahan_nama' => $nama,
            'barcode' => $barcode,
            'jenis_barang' => $jenis,
            'satuan_id' => $satuan_id
        ];

        $this->db->where('bahan_id', $id);
        if ($this->db->update('tbl_bahan', $data)) {
            // [LOGGING]
            $this->_log_manual("Edit Master Bahan Baku: $nama (ID: $id)", json_encode($data), 'WRITE');
            // [USER ACTIVITY]
            log_activity($this->session->userdata('id'), 'edit_master_bahan', 'Warehouse', $id);

            echo json_encode(['status' => 'success', 'pesan' => 'Bahan berhasil diupdate.']);
        } else {
            echo json_encode(['status' => 'error', 'pesan' => 'Gagal update data.']);
        }
    }

    public function actionDeleteBahanBaku($id = null)
    {
        if (!$id)
            return;

        $this->db->trans_start();
        // Delete related materials first
        $this->db->delete('tbl_material', ['bahan_id' => $id]);
        // Delete the bahan
        $this->db->delete('tbl_bahan', ['bahan_id' => $id]);
        $this->db->trans_complete();

        if ($this->db->trans_status() === TRUE) {
            echo json_encode(['status' => 'success', 'pesan' => 'Bahan dan varian terkait berhasil dihapus.']);
        } else {
            echo json_encode(['status' => 'error', 'pesan' => 'Gagal menghapus data.']);
        }
    }

    public function get_material_count_by_bahan()
    {
        $id = $this->input->get('bahan_id', true);
        $count = $this->db->where('bahan_id', $id)->count_all_results('tbl_material');
        echo json_encode(['material_count' => $count]);
    }

    public function get_material_by_bahan_json()
    {
        $id = $this->input->get('bahan_id', true);

        // Fetch Parent Info (Satuan Produksi)
        $parent = $this->db->select('b.bahan_nama, s.satuan_nama as satuan_produksi')
            ->from('tbl_bahan b')
            ->join('tbl_satuan s', 's.satuan_id = b.satuan_id', 'left')
            ->where('b.bahan_id', $id)
            ->get()->row();

        $this->db->select('m.*, s.satuan_nama');
        $this->db->from('tbl_material m');
        $this->db->join('tbl_satuan s', 'm.satuan_gudang_id = s.satuan_id', 'left');
        $this->db->where('m.bahan_id', $id);
        $data = $this->db->get()->result();

        echo json_encode([
            'status' => 'success',
            'data' => $data,
            'parent' => $parent
        ]);
    }


    public function actionAddMaterial()
    {
        $bahan_id = $this->input->post('bahan_id', true);
        $varian = trim($this->input->post('varian', true));
        $barcode = trim($this->input->post('barcode', true));
        $satuan = $this->input->post('satuan_gudang_id', true);
        $konversi = $this->input->post('konversi', true);

        // Check duplicate barcode
        if (!empty($barcode)) {
            $exist = $this->db->get_where('tbl_material', ['barcode' => $barcode])->row();
            if ($exist) {
                echo json_encode(['status' => 'error', 'pesan' => 'Barcode sudah digunakan variant lain!']);
                return;
            }
        }

        $data = [
            'bahan_id' => $bahan_id,
            'varian' => $varian,
            'barcode' => $barcode,
            'satuan_gudang_id' => empty($satuan) ? 0 : $satuan,
            'konversi' => $konversi,
            'stok_min' => (float)$this->input->post('stok_min', true),
            'status' => $this->input->post('status', true),
            'created_at' => date('Y-m-d H:i:s')
        ];

        if ($this->db->insert('tbl_material', $data)) {
            $new_id = $this->db->insert_id();
            // [AUDIT LOG]
            $this->_log_manual("Tambah Master Material Baru: $varian", json_encode($data), 'WRITE');
            // [USER ACTIVITY]
            log_activity($this->session->userdata('id'), 'add_master_material', 'Warehouse', $new_id);

            echo json_encode(['status' => 'success', 'pesan' => 'Varian berhasil ditambahkan.']);
        } else {
            echo json_encode(['status' => 'error', 'pesan' => 'Gagal menambahkan varian.']);
        }
    }

    public function actionUpdateMaterial()
    {
        $id = $this->input->post('material_id', true);
        $varian = trim($this->input->post('varian', true));
        $barcode = trim($this->input->post('barcode', true));
        $satuan = $this->input->post('satuan_gudang_id', true);
        $konversi = $this->input->post('konversi', true);

        // Check duplicate barcode
        if (!empty($barcode)) {
            $exist = $this->db->get_where('tbl_material', ['barcode' => $barcode, 'material_id !=' => $id])->row();
            if ($exist) {
                echo json_encode(['status' => 'error', 'pesan' => 'Barcode sudah digunakan variant lain!']);
                return;
            }
        }

        $data = [
            'varian' => $varian,
            'barcode' => $barcode,
            'satuan_gudang_id' => empty($satuan) ? 0 : $satuan,
            'konversi' => $konversi,
            'stok_min' => (float)$this->input->post('stok_min', true),
            'status' => $this->input->post('status', true),
            'updated_at' => date('Y-m-d H:i:s')
        ];

        $this->db->where('material_id', $id);
        if ($this->db->update('tbl_material', $data)) {
            echo json_encode(['status' => 'success', 'pesan' => 'Varian berhasil diupdate.']);
        } else {
            echo json_encode(['status' => 'error', 'pesan' => 'Gagal update varian.']);
        }
    }

    public function actionUpdateMaterialBarcodeOnly()
    {
        if (!$this->input->is_ajax_request())
            exit('No direct access allowed');

        $id = $this->input->post('material_id', true);
        $barcode = trim($this->input->post('barcode', true));

        if (empty($id)) {
            echo json_encode(['status' => 'error', 'pesan' => 'ID tidak valid']);
            return;
        }

        // Check duplicate barcode if not empty
        if (!empty($barcode)) {
            $exist = $this->db->get_where('tbl_material', ['barcode' => $barcode, 'material_id !=' => $id])->row();
            if ($exist) {
                echo json_encode(['status' => 'error', 'pesan' => 'Barcode sudah digunakan variant lain!']);
                return;
            }
        }

        $this->db->where('material_id', $id);
        if ($this->db->update('tbl_material', ['barcode' => $barcode])) {
            // [AUDIT LOG]
            $this->_log_manual("Update Barcode Material (Quick): $id", "New Barcode: $barcode", 'WRITE');
            echo json_encode(['status' => 'success']);
        } else {
            echo json_encode(['status' => 'error', 'pesan' => 'Gagal update database']);
        }
    }

    public function actionUpdateMaterialVarianOnly()
    {
        if (!$this->input->is_ajax_request())
            exit('No direct access allowed');

        $id = $this->input->post('material_id', true);
        $varian = trim($this->input->post('varian', true));

        if (empty($id)) {
            echo json_encode(['status' => 'error', 'pesan' => 'ID tidak valid']);
            return;
        }
        
        if (empty($varian)) {
            echo json_encode(['status' => 'error', 'pesan' => 'Nama Varian tidak boleh kosong']);
            return;
        }

        $this->db->where('material_id', $id);
        if ($this->db->update('tbl_material', ['varian' => $varian])) {
            // [AUDIT LOG]
            $this->_log_manual("Update Nama Varian Material (Quick): $id", "New Varian: $varian", 'WRITE');
            echo json_encode(['status' => 'success']);
        } else {
            echo json_encode(['status' => 'error', 'pesan' => 'Gagal update database']);
        }
    }

    public function actionDeleteMaterial()
    {
        $id = $this->input->post('material_id', true);
        // Begin transaction to ensure atomic deletion of material and related data
        $this->db->trans_start();
        // Delete related stock entries in gudang
        $this->db->where('material_id', $id);
        $this->db->delete('tbl_stok_gudang');
        // Delete related stock log entries
        $this->db->where('material_id', $id);
        $this->db->delete('tbl_stok_log');
        // Delete dependent purchase detail rows (foreign key)
        $this->db->where('material_id', $id);
        $this->db->delete('tbl_pembelian_detail');
        // Delete dependent pengambilan detail rows (foreign key)
        $this->db->where('material_id', $id);
        $this->db->delete('tbl_pengambilan_detail');
        // Delete dependent stok opname rows
        $this->db->where('material_id', $id);
        $this->db->delete('tbl_stok_opname');
        // Delete dependent return pembelian detail rows
        $this->db->where('material_id', $id);
        $this->db->delete('tbl_return_pembelian_detail');
        // Delete material record
        $this->db->where('material_id', $id);
        $this->db->delete('tbl_material');
        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE) {
            echo json_encode(['status' => 'error', 'pesan' => 'Gagal menghapus varian dan data terkait.']);
        } else {
            // Audit log for deletion
            $this->_log_manual('Delete Material Variant', "Material ID: $id", 'DELETE');
            echo json_encode(['status' => 'success', 'pesan' => 'Varian berhasil dihapus beserta data terkait.']);
        }
    }

    public function actionDeleteMaterialBulk()
    {
        $ids = $this->input->post('ids', true);
        if (empty($ids) || !is_array($ids)) {
            echo json_encode(['status' => 'error', 'pesan' => 'Tidak ada varian yang dipilih.']);
            return;
        }

        $this->db->trans_start();
        $this->db->where_in('material_id', $ids);
        $this->db->delete('tbl_stok_gudang');
        $this->db->where_in('material_id', $ids);
        $this->db->delete('tbl_stok_log');
        $this->db->where_in('material_id', $ids);
        $this->db->delete('tbl_pembelian_detail');
        $this->db->where_in('material_id', $ids);
        $this->db->delete('tbl_pengambilan_detail');
        $this->db->where_in('material_id', $ids);
        $this->db->delete('tbl_stok_opname');
        $this->db->where_in('material_id', $ids);
        $this->db->delete('tbl_return_pembelian_detail');
        $this->db->where_in('material_id', $ids);
        $this->db->delete('tbl_material');
        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE) {
            echo json_encode(['status' => 'error', 'pesan' => 'Gagal menghapus varian dan data terkait.']);
        } else {
            $this->_log_manual('Delete Bulk Material Variant', "Material IDs: " . implode(',', $ids), 'DELETE');
            echo json_encode(['status' => 'success', 'pesan' => count($ids) . ' varian berhasil dihapus.']);
        }
    }

    public function actionUpdateBarcode()
    {
        $input = json_decode(file_get_contents('php://input'), true);
        $id = $input['bahan_id'];
        $barcode = $input['barcode'];

        $this->db->where('bahan_id', $id);
        if ($this->db->update('tbl_bahan', ['barcode' => $barcode])) {
            // [AUDIT LOG]
            $this->_log_manual("Update Barcode Master Bahan: $id", "New Barcode: $barcode", 'WRITE');
            echo json_encode(['status' => 'success']);
        } else {
            echo json_encode(['status' => 'error', 'pesan' => 'Gagal update barcode.']);
        }
    }

    public function cetak_barcode_bulk()
    {
        $ids = $this->input->post('ids', true) ?: $this->input->get('ids', true);
        $type = $this->input->post('type', true) ?: $this->input->get('type', true) ?: 'bahan';

        if (empty($ids))
            return;
        if (is_string($ids) && $ids !== 'all')
            $ids = explode(',', $ids);

        if ($type == 'variant' || $type == 'material') {
            $this->db->select('CONCAT(b.bahan_nama, " - ", m.varian) as name, m.barcode, s.satuan_nama as unit');
            $this->db->from('tbl_material m');
            $this->db->join('tbl_bahan b', 'b.bahan_id = m.bahan_id');
            $this->db->join('tbl_satuan s', 'm.satuan_gudang_id = s.satuan_id', 'left');
            if ($ids !== 'all') {
                $this->db->where_in('m.material_id', $ids);
            } else {
                $this->db->group_start()
                    ->where('m.barcode !=', '')
                    ->where('m.barcode IS NOT NULL')
                    ->where('m.barcode !=', '0')
                    ->group_end();
            }
        } else {
            $this->db->select('b.bahan_nama as name, b.barcode, s.satuan_nama as unit');
            $this->db->from('tbl_bahan b');
            $this->db->join('tbl_satuan s', 'b.satuan_id = s.satuan_id', 'left');
            if ($ids !== 'all') {
                $this->db->where_in('bahan_id', $ids);
            } else {
                $this->db->group_start()
                    ->where('barcode !=', '')
                    ->where('barcode IS NOT NULL')
                    ->where('barcode !=', '0')
                    ->group_end();
            }
        }

        $items = $this->db->get()->result_array();

        if (empty($items)) {
            echo "No items found for printing.";
            return;
        }

        $data = ['items' => $items, 'title' => 'Cetak Barcode Bulk'];
        $this->load->view('admin/gudang/cetak_barcode_bulk', $data);
    }

    /**
     * 8. LAPORAN AKUMULASI STOK (Executive Report)
     */
    public function stock_report()
    {
        $conf = $this->_get_conf();
        $report_data = $this->warehouse->get_accumulated_stock();

        $data = [
            'controller' => 'warehouse',
            'function' => 'stock_report',
            'title' => 'Laporan Rekapitulasi Stok',
            'perusahaan' => $conf['perusahaan']['nama_perusahaan'],
            'hakakses' => $conf['hakakses']['divisi_nama'],
            'warehouses' => $report_data['warehouses'],
            'report_data' => $report_data['data']
        ];

        $this->load_admin_view('admin/warehouse/report_stock', $data);
    }

    /**
     * Laporan Analytics - Landing Page
     */
    public function analytics()
    {
        $conf = $this->_get_conf();
        $data = [
            'controller' => 'warehouse',
            'function'   => 'analytics',
            'title'      => 'Laporan Gudang',
            'perusahaan' => $conf['perusahaan']['nama_perusahaan'],
            'hakakses'   => $conf['hakakses']['divisi_nama'],
        ];
        $this->load_admin_view('admin/warehouse/analytics', $data);
    }

    /**
     * Laporan Stok Harian
     */
    public function laporan_stok_harian()
    {
        $conf = $this->_get_conf();
        $date_from     = $this->input->get('date_from', true) ?: date('Y-m-01');
        $date_to       = $this->input->get('date_to', true)   ?: date('Y-m-t');
        $warehouse_id  = $this->input->get('gudang_id', true) ?: null;

        $data = [
            'controller'        => 'warehouse',
            'function'          => 'laporan_stok_harian',
            'title'             => 'Laporan Stok Harian',
            'perusahaan'        => $conf['perusahaan']['nama_perusahaan'],
            'hakakses'          => $conf['hakakses']['divisi_nama'],
            'date_from'         => $date_from,
            'date_to'           => $date_to,
            'selected_warehouse'=> $warehouse_id,
            'warehouses'        => $this->warehouse->get_all_warehouses(),
            'daily_stock'       => $this->warehouse->get_daily_stock_report($date_from, $date_to, $warehouse_id),
        ];
        $this->load_admin_view('admin/warehouse/laporan_stok_harian', $data);
    }

    /**
     * Laporan Bahan Paling Sering Diproduksi
     */
    public function laporan_fast_moving()
    {
        $conf = $this->_get_conf();
        $date_from = $this->input->get('date_from', true) ?: date('Y-m-01');
        $date_to   = $this->input->get('date_to', true)   ?: date('Y-m-t');

        $data = [
            'controller'  => 'warehouse',
            'function'    => 'laporan_fast_moving',
            'title'       => 'Laporan Bahan Sering Diproduksi',
            'perusahaan'  => $conf['perusahaan']['nama_perusahaan'],
            'hakakses'    => $conf['hakakses']['divisi_nama'],
            'date_from'   => $date_from,
            'date_to'     => $date_to,
            'fast_moving' => $this->warehouse->get_fast_moving_materials(50, $date_from, $date_to),
        ];
        $this->load_admin_view('admin/warehouse/laporan_fast_moving', $data);
    }

    /**
     * Laporan Pembelian per Supplier
     */
    public function laporan_supplier()
    {
        $conf = $this->_get_conf();
        $date_from = $this->input->get('date_from', true) ?: date('Y-m-01');
        $date_to   = $this->input->get('date_to', true)   ?: date('Y-m-t');

        $data = [
            'controller'      => 'warehouse',
            'function'        => 'laporan_supplier',
            'title'           => 'Laporan Pembelian per Supplier',
            'perusahaan'      => $conf['perusahaan']['nama_perusahaan'],
            'hakakses'        => $conf['hakakses']['divisi_nama'],
            'date_from'       => $date_from,
            'date_to'         => $date_to,
            'supplier_summary'=> $this->warehouse->get_supplier_purchase_summary($date_from, $date_to),
        ];
        $this->load_admin_view('admin/warehouse/laporan_supplier', $data);
    }

    /** Laporan Nilai Inventaris */
    public function laporan_nilai_inventaris()
    {
        $conf = $this->_get_conf();
        $data_inv = $this->warehouse->get_inventory_valuation();
        $total_nilai = array_sum(array_column($data_inv, 'nilai_stok'));
        $data = [
            'controller'  => 'warehouse', 'function' => 'laporan_nilai_inventaris',
            'title'       => 'Nilai Inventaris Gudang',
            'perusahaan'  => $conf['perusahaan']['nama_perusahaan'],
            'hakakses'    => $conf['hakakses']['divisi_nama'],
            'inventaris'  => $data_inv,
            'total_nilai' => $total_nilai,
        ];
        $this->load_admin_view('admin/warehouse/laporan_nilai_inventaris', $data);
    }

    /** Laporan Stok Kritis */
    public function laporan_stok_kritis()
    {
        $conf = $this->_get_conf();
        $data = [
            'controller' => 'warehouse', 'function' => 'laporan_stok_kritis',
            'title'      => 'Laporan Stok Kritis',
            'perusahaan' => $conf['perusahaan']['nama_perusahaan'],
            'hakakses'   => $conf['hakakses']['divisi_nama'],
            'stok_kritis'=> $this->warehouse->get_critical_stock(),
        ];
        $this->load_admin_view('admin/warehouse/laporan_stok_kritis', $data);
    }

    /** Laporan Dead Stock */
    public function laporan_dead_stock()
    {
        $conf = $this->_get_conf();
        $days = $this->input->get('days', true) ?: 30;
        $data = [
            'controller' => 'warehouse', 'function' => 'laporan_dead_stock',
            'title'      => 'Laporan Stok Tidak Bergerak',
            'perusahaan' => $conf['perusahaan']['nama_perusahaan'],
            'hakakses'   => $conf['hakakses']['divisi_nama'],
            'dead_stock' => $this->warehouse->get_dead_stock($days),
            'days'       => $days,
        ];
        $this->load_admin_view('admin/warehouse/laporan_dead_stock', $data);
    }

    /** Laporan Tren Pembelian Bulanan */
    public function laporan_tren_pembelian()
    {
        $conf = $this->_get_conf();
        $trend = $this->warehouse->get_monthly_purchase_trend();
        $data = [
            'controller'  => 'warehouse', 'function' => 'laporan_tren_pembelian',
            'title'       => 'Tren Pembelian Bulanan',
            'perusahaan'  => $conf['perusahaan']['nama_perusahaan'],
            'hakakses'    => $conf['hakakses']['divisi_nama'],
            'trend'       => $trend,
            'trend_json'  => json_encode(array_values($trend)),
        ];
        $this->load_admin_view('admin/warehouse/laporan_tren_pembelian', $data);
    }

    /** Laporan Pembelian vs Pemakaian */
    public function laporan_pembelian_vs_pemakaian()
    {
        $conf = $this->_get_conf();
        $date_from = $this->input->get('date_from', true) ?: date('Y-m-01');
        $date_to   = $this->input->get('date_to', true)   ?: date('Y-m-t');
        $data = [
            'controller'  => 'warehouse', 'function' => 'laporan_pembelian_vs_pemakaian',
            'title'       => 'Pembelian vs Pemakaian',
            'perusahaan'  => $conf['perusahaan']['nama_perusahaan'],
            'hakakses'    => $conf['hakakses']['divisi_nama'],
            'date_from'   => $date_from,
            'date_to'     => $date_to,
            'comparison'  => $this->warehouse->get_purchase_vs_usage($date_from, $date_to),
        ];
        $this->load_admin_view('admin/warehouse/laporan_pembelian_vs_pemakaian', $data);
    }

    /**
     * 9. PURCHASE RETURNS (Retur Pembelian)
     */
    public function returns()
    {
        $conf = $this->_get_conf();

        // Fetch recent returns
        $this->db->select('r.*, s.supplier_nama, u.username');
        $this->db->from('tbl_return_pembelian r');
        $this->db->join('tbl_supplier s', 's.supplier_id = r.supplier_id');
        $this->db->join('users u', 'u.karyawan_id = r.karyawan_id', 'left');
        $this->db->order_by('r.return_id', 'desc');
        $this->db->limit(50);
        $returns = $this->db->get()->result();

        $data = [
            'controller' => 'warehouse',
            'function' => 'returns',
            'title' => 'Retur Pembelian Supplier',
            'perusahaan' => $conf['perusahaan']['nama_perusahaan'],
            'hakakses' => $conf['hakakses']['divisi_nama'],
            'returns' => $returns
        ];

        $this->load_admin_view('admin/warehouse/returns', $data);
    }

    /**
     * 10. STOCK SYNCHRONIZATION (Maintenance)
     */
    public function ajax_sync_stock()
    {
        if (!$this->input->is_ajax_request())
            exit('No direct access allowed');

        $materials = $this->db->get('tbl_material')->result();
        $warehouses = $this->db->get('tbl_gudang')->result();
        $fixed_count = 0;

        foreach ($materials as $m) {
            foreach ($warehouses as $w) {
                $this->db->select('SUM(qty_in) - SUM(qty_out) as actual_qty');
                $this->db->where('material_id', $m->material_id);
                $this->db->where('gudang_id', $w->gudang_id);
                $log_res = $this->db->get('tbl_stok_log')->row();
                $actual_qty = $log_res ? (float) $log_res->actual_qty : 0;

                $current_stock = $this->db->get_where('tbl_stok_gudang', [
                    'material_id' => $m->material_id,
                    'gudang_id' => $w->gudang_id
                ])->row();

                if (!$current_stock) {
                    if ($actual_qty != 0) {
                        $this->db->insert('tbl_stok_gudang', [
                            'material_id' => $m->material_id,
                            'gudang_id' => $w->gudang_id,
                            'qty' => $actual_qty
                        ]);
                        $fixed_count++;
                    }
                } else {
                    if (abs((float) $current_stock->qty - $actual_qty) > 0.0001) {
                        $this->db->where('stok_id', $current_stock->stok_id);
                        $this->db->update('tbl_stok_gudang', ['qty' => $actual_qty]);
                        $fixed_count++;
                    }
                }
            }
        }

        echo json_encode(['success' => true, 'message' => "Stock synchronization complete. $fixed_count records adjusted."]);
    }

    /**
     * 11. SMART SCANNER: AJAX Barcode Lookup
     */
    public function ajax_lookup_barcode()
    {
        if (!$this->input->is_ajax_request())
            exit('No direct access allowed');

        $barcode = trim($this->input->get('barcode', true));

        // 1. Try to find in tbl_material (Specific Variant)
        $this->db->select('m.material_id, m.bahan_id, m.varian, m.barcode as mat_barcode, 
                          b.bahan_nama, b.barcode as master_barcode, 
                          s.satuan_nama');
        $this->db->from('tbl_material m');
        $this->db->join('tbl_bahan b', 'm.bahan_id = b.bahan_id');
        $this->db->join('tbl_satuan s', 'm.satuan_gudang_id = s.satuan_id', 'left');
        $this->db->where('m.barcode', $barcode);
        $res = $this->db->get()->row();

        // 2. If not found, try to find in tbl_bahan (Parent)
        if (!$res) {
            $this->db->select('m.material_id, m.bahan_id, m.varian, m.barcode as mat_barcode, 
                              b.bahan_nama, b.barcode as master_barcode, 
                              s.satuan_nama');
            $this->db->from('tbl_bahan b');
            $this->db->join('tbl_material m', 'm.bahan_id = b.bahan_id', 'left');
            $this->db->join('tbl_satuan s', 'm.satuan_gudang_id = s.satuan_id', 'left');
            $this->db->where('b.barcode', $barcode);
            $this->db->limit(1); // Fallback: ambil varian pertama jika scan barcode induk
            $res = $this->db->get()->row();
        }

        if ($res && !empty($res->material_id)) {
            // Get Stock
            $this->db->select('g.gudang_nama, COALESCE(sg.qty, 0) as qty');
            $this->db->from('tbl_gudang g');
            $this->db->join('tbl_stok_gudang sg', 'g.gudang_id = sg.gudang_id AND sg.material_id = ' . $res->material_id, 'left');
            $this->db->order_by('g.gudang_id');
            $stock = $this->db->get()->result();

            // Format used barcode
            $used_barcode = ($barcode == $res->mat_barcode) ? $res->mat_barcode : $res->master_barcode;

            echo json_encode([
                'success' => true,
                'data' => [
                    'item_name' => $res->bahan_nama . ($res->varian ? ' - ' . $res->varian : ''),
                    'barcode' => $used_barcode,
                    'unit' => $res->satuan_nama,
                    'material_id' => $res->material_id,
                    'bahan_id' => $res->bahan_id,
                    'stock' => $stock
                ]
            ]);
        } else {
            // Debug info if needed
            echo json_encode([
                'success' => false,
                'message' => 'Material tidak ditemukan untuk barcode: ' . $barcode
            ]);
        }
    }

    // =====================================================
    // 12. PRINTING & EXPORTS (Migrated from Gudang)
    // =====================================================

    public function cetakPembelian($id)
    {
        $data = array(
            'controller' => $this->uri->segment(1),
            'function' => $this->uri->segment(2),
            'title' => 'Cetak Pembelian Bahan',
            'table' => 'tbl_kategori',
            'tampilData' => $this->m_gudang->getdetailPembelian($id)->result(),
            'getData' => $this->m_gudang->getdetailPembelian($id)->row_array(),
        );
        $this->load->view('admin/gudang/cetakPembelian', $data);
    }

    public function cetakPengambilan($id)
    {
        $data = array(
            'controller' => $this->uri->segment(1),
            'function' => $this->uri->segment(2),
            'title' => 'Cetak Pembelian Bahan',
            'table' => 'tbl_kategori',
            'tampilData' => $this->m_gudang->getdetailPengambilan($id)->result(),
            'getData' => $this->m_gudang->getdetailPengambilan($id)->row_array(),
        );
        $this->load->view('admin/gudang/cetakPengambilan', $data);
    }

    public function cetakReturnPembelian($id)
    {
        $data = array(
            'controller' => $this->uri->segment(1),
            'function' => $this->uri->segment(2),
            'title' => 'Cetak Return Pembelian',
            'table' => 'tbl_kategori',
            'tampilData' => $this->m_gudang->getDetailReturnPembelian($id)->result(),
            'getData' => $this->m_gudang->getReturnPembelianById($id)->row_array(),
        );
        $this->load->view('admin/gudang/cetakReturnPembelian', $data);
    }

    public function cetakSonDetail($id)
    {
        $data['detail'] = $this->m_gudang->getSonDetail($id)->row();
        $this->load->view('admin/gudang/son/cetak_detail', $data);
    }

    public function export_akumulasi_stok_excel()
    {
        // Use the same stock source as the warehouse dashboard/report.
        // This avoids discrepancies with the legacy `stok_gudang` table
        // and keeps material variants (material_id) such as Stopper 2 cm visible.
        $report_data = $this->warehouse->get_accumulated_stock();
        $warehouses = $report_data['warehouses'];
        $rows = $report_data['data'];

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=laporan_akumulasi_stok.csv');

        $output = fopen('php://output', 'w');

        $headers = ['No', 'Nama Material', 'Satuan'];
        foreach ($warehouses as $warehouse) {
            $headers[] = $warehouse->gudang_nama;
        }
        $headers[] = 'Total Stok';
        fputcsv($output, $headers);

        $no = 1;
        foreach ($rows as $row) {
            $line = [
                $no++,
                $row['name'],
                $row['unit'],
            ];

            foreach ($warehouses as $warehouse) {
                $line[] = $row['warehouses'][$warehouse->gudang_id] ?? 0;
            }

            $line[] = $row['total_qty'];
            fputcsv($output, $line);
        }

        fclose($output);
        exit;
    }
    // =====================================================
    // 13. ADDITIONAL REPORTS (Migrated from Gudang)
    // =====================================================

    public function monitoring_stok()
    {
        $userid = $this->session->userdata('id');
        $data = array(
            'controller' => $this->uri->segment(1),
            'function' => $this->uri->segment(2),
            'title' => 'Monitoring Stok Bahan',
            'level' => $this->db->get_where('users', array('user_id' => $userid))->row_array(),
            'bulanini' => date('m'),
            'monitoring' => $this->m_gudang->getMonitoringTransaksi(),
        );
        $this->load_admin_view('admin/gudang/monitoring', $data);
    }

    public function laporan_pembelian()
    {
        $year = $this->input->get('year', true) ?: date('Y');
        $month = $this->input->get('month', true) ?: date('m');
        $selected_date = $this->input->get('date', true) ?: date('Y-m-d');

        $calendar_data = $this->m_gudang->getPembelianCalendarData($year, $month)->result();
        $calendar_events = [];
        $total_transaksi_bulan = 0;
        $total_nilai_bulan = 0;
        
        foreach ($calendar_data as $event) {
            $calendar_events[$event->tanggal] = [
                'jumlah_transaksi' => $event->jumlah_transaksi,
                'total_nilai' => $event->total_nilai
            ];
            $total_transaksi_bulan += $event->jumlah_transaksi;
            $total_nilai_bulan += $event->total_nilai;
        }

        $pembelian_data = $this->m_gudang->getPembelianByDate($selected_date);
        $pembelian_detail = $this->m_gudang->getPembelianDetailByDate($selected_date)->result();

        $total_transaksi = count($pembelian_data);
        $total_nilai = 0;
        foreach ($pembelian_data as $pembelian) {
            $total_nilai += $pembelian->pembelian_total;
        }

        $data = [
            'title' => 'Laporan Pembelian',
            'table' => 'tbl_kategori',
            'tampilData' => $this->m_gudang->getLaporanPembelian()->result(),
            'calendar_events' => $calendar_events,
            'selected_date' => $selected_date,
            'selected_year' => $year,
            'selected_month' => $month,
            'pembelian_data' => $pembelian_data,
            'pembelian_detail' => $pembelian_detail,
            'total_transaksi' => $total_transaksi,
            'total_nilai' => $total_nilai,
            'total_transaksi_bulan' => $total_transaksi_bulan,
            'total_nilai_bulan' => $total_nilai_bulan,
            'supplier_list' => $this->db->get('tbl_supplier')->result(),
            'bahan_list' => $this->db->get('tbl_bahan')->result(),
        ];
        $this->load_admin_view('admin/gudang/laporan_pembelian', $data);
    }

    public function get_pembelian_by_date()
    {
        $date = $this->input->post('date', true);
        if (!$date) {
            echo json_encode(['success' => false, 'message' => 'Tanggal tidak boleh kosong']);
            return;
        }

        $pembelian_data = $this->m_gudang->getPembelianByDate($date);
        $pembelian_detail = $this->m_gudang->getPembelianDetailByDate($date)->result();

        $total_transaksi = count($pembelian_data);
        $total_nilai = 0;
        foreach ($pembelian_data as $pembelian) {
            $total_nilai += (float) $pembelian->pembelian_total;
        }

        $response = [
            'success' => true,
            'data' => [
                'pembelian_data' => $pembelian_data,
                'pembelian_detail' => $pembelian_detail,
                'total_transaksi' => $total_transaksi,
                'total_nilai' => $total_nilai,
                'formatted_date' => date('d F Y', strtotime($date))
            ]
        ];

        header('Content-Type: application/json');
        echo json_encode($response);
    }

    public function cetak_laporan_pembelian_harian()
    {
        $date = $this->input->get('date', true) ?: date('Y-m-d');
        
        $pembelian_data = $this->m_gudang->getPembelianByDate($date);
        $pembelian_detail = $this->m_gudang->getPembelianDetailByDate($date)->result();
        
        $total_transaksi = count($pembelian_data);
        $total_nilai = 0;
        foreach ($pembelian_data as $pembelian) {
            $total_nilai += (float) $pembelian->pembelian_total;
        }

        $data = [
            'title' => 'Laporan Pembelian Harian',
            'date' => $date,
            'pembelian_data' => $pembelian_data,
            'pembelian_detail' => $pembelian_detail,
            'total_transaksi' => $total_transaksi,
            'total_nilai' => $total_nilai,
            'formatted_date' => date('d F Y', strtotime($date))
        ];

        $this->load->view('admin/gudang/cetak_laporan_pembelian_harian', $data);
    }

    // =====================================================
    // 14. AJAX DASHBOARD ENDPOINTS (Migrated from Gudang)
    // =====================================================

    public function get_realtime_stats()
    {
        header('Content-Type: application/json');
        $data = array(
            'total_bahan' => $this->db->get('tbl_bahan')->num_rows(),
            'total_masuk' => $this->db->get('tbl_pembelian_detail')->num_rows(),
            'total_keluar' => $this->db->get('tbl_pengambilan_detail')->num_rows(),
            'total_stok' => $this->db->get('tbl_stok_gudang')->num_rows(),
        );
        echo json_encode($data);
    }

    public function get_dash_stats_ajax()
    {
        $stats = [
            'pembelian' => $this->db->where('pembelian_tgl >=', date('Y-m-d', strtotime('-30 days')))->count_all_results('tbl_pembelian'),
            'pengiriman' => $this->db->count_all('tbl_pengambilan'),
            'stok' => $this->db->count_all('tbl_stok_gudang'),
            'bahan' => $this->db->count_all('tbl_material')
        ];

        $pemb = $this->db->select('pembelian_tgl as tgl, "Pembelian" as tipe, no_faktur as ref, supplier_nama as desc, 0 as nilai')
            ->join('tbl_supplier s', 's.supplier_id = p.supplier_id')
            ->order_by('pembelian_id', 'desc')->limit(5)->get('tbl_pembelian p')->result();

        $peng = $this->db->select('pengambilan_tgl as tgl, "Mutation" as tipe, "MUT-" as ref, "Transfer to Production" as desc, 0 as nilai')
            ->order_by('pengambilan_id', 'desc')->limit(5)->get('tbl_pengambilan')->result();

        $recent = array_merge($pemb, $peng);
        usort($recent, function ($a, $b) {
            return strtotime($b->tgl) - strtotime($a->tgl);
        });
        $recent = array_slice($recent, 0, 8);

        foreach ($recent as &$r) {
            $r->tanggal = date('d/m H:i', strtotime($r->tgl));
            $r->nilai = 'Rp ' . number_format($r->nilai, 0, ',', '.');
        }

        $critical = $this->db->select('b.bahan_nama as nama, m.varian, s.qty as stok, 10 as min')
            ->from('tbl_stok_gudang s')
            ->join('tbl_material m', 'm.material_id = s.material_id')
            ->join('tbl_bahan b', 'b.bahan_id = m.bahan_id')
            ->where('s.qty <=', 10)
            ->limit(5)->get()->result();

        $dist = [
            'labels' => ['Gudang Utama', 'Gudang Produksi 1', 'Gudang Produksi 2'],
            'values' => [
                (float) $this->db->select_sum('qty')->where('gudang_id', 1)->get('tbl_stok_gudang')->row()->qty ?: 0,
                (float) $this->db->select_sum('qty')->where('gudang_id', 2)->get('tbl_stok_gudang')->row()->qty ?: 0,
                (float) $this->db->select_sum('qty')->where('gudang_id', 3)->get('tbl_stok_gudang')->row()->qty ?: 0
            ]
        ];

        header('Content-Type: application/json');
        echo json_encode(['success' => true, 'stats' => $stats, 'recent' => $recent, 'critical' => $critical, 'dist' => $dist]);
    }

    public function get_dashboard_overview_json()
    {
        try {
            $response = array(
                'status' => 'success',
                'data' => array(
                    'total_pembelian' => (int) $this->db->count_all('tbl_pembelian'),
                    'total_pengiriman' => (int) $this->db->count_all('tbl_pengambilan'),
                    'total_stok_gudang' => (int) $this->db->count_all('tbl_stok_gudang'),
                    'total_bahan_baku' => (int) $this->db->count_all('tbl_bahan')
                )
            );
        } catch (Exception $e) {
            $response = array('status' => 'error', 'pesan' => $e->getMessage());
        }
        header('Content-Type: application/json');
        echo json_encode($response);
    }

    public function get_recent_activities_json()
    {
        try {
            $activities = $this->m_gudang->getMonitoringTransaksi();
            $recent_activities = array();
            $count = 0;
            foreach ($activities as $activity) {
                if ($count >= 10)
                    break;
                $recent_activities[] = array(
                    'tanggal' => date('d/m/Y H:i', strtotime($activity->tanggal)),
                    'material_nama' => $activity->varian,
                    'tipe' => $activity->tipe,
                    'qty' => ($activity->qty_masuk > 0 ? '+' : '-') . ($activity->qty_masuk + $activity->qty_keluar),
                    'petugas' => $activity->nama_karyawan,
                    'status' => 'Selesai'
                );
                $count++;
            }
            $response = array('status' => 'success', 'data' => $recent_activities);
        } catch (Exception $e) {
            $response = array('status' => 'error', 'pesan' => $e->getMessage());
        }
        header('Content-Type: application/json');
        echo json_encode($response);
    }
    // =====================================================
    // 15. TRANSACTION DASHBOARDS (Migrated from Gudang)
    // =====================================================

    public function gudang_bahan()
    {
        $data = array(
            'controller' => $this->uri->segment(1),
            'function' => $this->uri->segment(2),
            'title' => 'Stok Gudang Bahan',
            'table' => 'tbl_kategori',
            'bulanini' => date('m'),
            'tampilData' => $this->m_gudang->getGudangBahan()->result(),
            'total_rows' => $this->m_gudang->getGudangBahan()->num_rows(),
        );
        $this->load_admin_view('admin/warehouse/stok_gudang_bahan', $data);
    }

    public function laporan_barang_masuk()
    {
        $userid = $this->session->userdata('id');
        $data = array(
            'controller' => $this->uri->segment(1),
            'function' => $this->uri->segment(2),
            'title' => 'Laporan Barang Masuk',
            'table' => 'tbl_kategori',
            'tampilData' => $this->m_gudang->getbelibahan()->result(),
            'total_rows' => $this->m_gudang->getbelibahan()->num_rows(),
            'bahan' => $this->db->get('tbl_bahan')->result(),
            'supplier' => $this->db->get('tbl_supplier')->result(),
            'temp' => $this->m_gudang->getTemp('tbl_temp_beli')->result(),
            'jml_stok_minim' => $this->m_gudang->stok_minim()->num_rows(),
            'level' => $this->db->get_where('users', array('user_id' => $userid))->row_array(),
        );
        $this->load_admin_view('admin/gudang/daftarPembelian', $data);
    }

    public function laporan_barang_keluar()
    {
        $current_month = date('m');
        $current_year = date('Y');
        $pengambilan_data = $this->m_gudang->getPengambilanWithDetails($current_month, $current_year);
        $data = array(
            'controller' => $this->uri->segment(1),
            'function' => $this->uri->segment(2),
            'title' => 'Laporan Barang Keluar',
            'table' => 'tbl_kategori',
            'tampilData' => $pengambilan_data->result(),
            'total_rows' => $pengambilan_data->num_rows(),
            'total_karyawan' => $this->M_master->get_karyawan()->num_rows(),
            'total_gudang' => $this->db->get('tbl_gudang')->num_rows(),
        );
        $this->load_admin_view('admin/gudang/daftarPengambilan', $data);
    }

    public function barang_masuk()
    {
        $data = array(
            'controller' => $this->uri->segment(1),
            'function' => $this->uri->segment(2),
            'title' => 'Barang Masuk',
            'table' => 'tbl_kategori',
            'bulanini' => date('m'),
            'tampilData' => $this->m_gudang->getbelibahan()->result(),
            'total_rows' => $this->m_gudang->getbelibahan()->num_rows(),
            'bahan' => $this->db->get('tbl_bahan')->result(),
            'supplier' => $this->db->get('tbl_supplier')->result(),
            'temp' => $this->m_gudang->getTemp('tbl_temp_beli')->result(),
        );
        $this->load_admin_view('admin/gudang/daftarPembelian', $data);
    }

    public function barang_keluar()
    {
        $current_month = date('m');
        $current_year = date('Y');
        $pengambilan_data = $this->m_gudang->getPengambilanWithDetails($current_month, $current_year);
        $data = array(
            'controller' => $this->uri->segment(1),
            'function' => $this->uri->segment(2),
            'title' => 'Laporan Barang Keluar',
            'table' => 'tbl_kategori',
            'tampilData' => $pengambilan_data->result(),
            'total_rows' => $pengambilan_data->num_rows(),
            'total_karyawan' => $this->M_master->get_karyawan()->num_rows(),
            'total_gudang' => $this->db->get('tbl_gudang')->num_rows(),
            'gudang' => $this->db->get('tbl_gudang')->result(),
        );
        $this->load_admin_view('admin/gudang/daftarPengambilan', $data);
    }

    public function DaftarPembelian()
    {
        $pembelian_data = $this->m_gudang->getbelibahan()->result();
        $total_nilai_pembelian = 0;
        $pembelian_bulan_ini = 0;
        foreach ($pembelian_data as $pembelian) {
            $detail_total = $this->db->select('SUM(qty * harga_beli) as total')->where('pembelian_id', $pembelian->pembelian_id)->get('tbl_pembelian_detail')->row();
            $pembelian->total_nilai = $detail_total ? $detail_total->total : 0;
            $total_nilai_pembelian += $pembelian->total_nilai;
            if (date('Y-m') == date('Y-m', strtotime($pembelian->pembelian_tgl)))
                $pembelian_bulan_ini++;
        }
        $data = [
            'controller' => $this->uri->segment(1),
            'function' => $this->uri->segment(2),
            'title' => 'Daftar Pembelian Bahan',
            'table' => 'tbl_kategori',
            'tampilData' => $pembelian_data,
            'total_rows' => count($pembelian_data),
            'total_nilai_pembelian' => $total_nilai_pembelian,
            'pembelian_bulan_ini' => $pembelian_bulan_ini,
            'total_supplier' => $this->db->get('tbl_supplier')->num_rows(),
            'total_return' => $this->db->get('tbl_return_pembelian')->num_rows(),
            'bahan' => $this->db->get('tbl_bahan')->result(),
            'supplier' => $this->db->get('tbl_supplier')->result(),
            'temp' => $this->m_gudang->getTemp('tbl_temp_beli')->result(),
            'jml_stok_minim' => $this->m_gudang->stok_minim()->num_rows(),
        ];
        $this->load_admin_view('admin/gudang/daftarPembelian', $data);
    }

    public function DaftarPengambilan()
    {
        $current_month = date('m');
        $current_year = date('Y');
        $pengambilan_data = $this->m_gudang->getPengambilanWithDetails($current_month, $current_year)->result();
        $pengambilan_bulan_ini = 0;
        foreach ($pengambilan_data as $pengambilan) {
            if (date('Y-m') == date('Y-m', strtotime($pengambilan->pengambilan_tgl)))
                $pengambilan_bulan_ini++;
        }
        $data = array(
            'controller' => $this->uri->segment(1),
            'function' => $this->uri->segment(2),
            'title' => 'Daftar Pengambilan Barang',
            'table' => 'tbl_kategori',
            'tampilData' => $pengambilan_data,
            'total_rows' => count($pengambilan_data),
            'pengambilan_bulan_ini' => $pengambilan_bulan_ini,
            'total_karyawan' => $this->M_master->get_karyawan()->num_rows(),
            'total_gudang' => $this->db->get('tbl_gudang')->num_rows(),
            'karyawan_list' => $this->db->select('DISTINCT(tbl_karyawan.karyawan_id), nama_lengkap')->join('tbl_pengambilan', 'tbl_pengambilan.karyawan_id = tbl_karyawan.karyawan_id')->where('nama_lengkap NOT LIKE', '%Terminal%')->where('nama_lengkap NOT LIKE', '%OPERATOR%')->where('nama_lengkap NOT LIKE', '%DEPT%')->where('nama_lengkap NOT LIKE', '%DTG%')->where('nama_lengkap NOT LIKE', '%DTF%')->where('nama_lengkap NOT LIKE', '%WORK CENTER%')->where('nama_lengkap NOT LIKE', '%LARGE FORMAT%')->where('nama_lengkap NOT LIKE', '%A3+%')->where("nik NOT LIKE", 'STATION%')->where("nik NOT LIKE", 'TERM%')->get('tbl_karyawan')->result(),
            'gudang_list' => $this->db->select('DISTINCT(tbl_gudang.gudang_id), gudang_nama')->join('tbl_pengambilan', 'tbl_pengambilan.gudang_id = tbl_gudang.gudang_id')->get('tbl_gudang')->result(),
        );
        $this->load_admin_view('admin/gudang/daftarPengambilan', $data);
    }

    public function PengambilanBahan()
    {
        $data = array(
            'controller' => $this->uri->segment(1),
            'function' => $this->uri->segment(2),
            'title' => 'Pengambilan Material ke Produksi',
            'karyawan' => $this->M_master->get_karyawan()->result(),
            'temp' => $this->m_gudang->getTemp('tbl_temp_pengambilan')->result(),
            'cabang' => $this->db->get('tbl_perusahaan')->result(),
            'gudang' => $this->db->get('tbl_gudang')->result(),
            'material_ready' => $this->db->select('tbl_material.*, tbl_bahan.bahan_nama, tbl_satuan.satuan_nama, COALESCE(tbl_stok_gudang.qty, 0) as jumlah_stok')->from('tbl_material')->join('tbl_bahan', 'tbl_material.bahan_id = tbl_bahan.bahan_id')->join('tbl_stok_gudang', 'tbl_material.material_id = tbl_stok_gudang.material_id AND tbl_stok_gudang.gudang_id = 1', 'inner')->join('tbl_satuan', 'tbl_material.satuan_gudang_id = tbl_satuan.satuan_id', 'left')->where('tbl_stok_gudang.qty >', 0)->order_by('tbl_bahan.bahan_nama', 'ASC')->get()->result(),
            'bahanbaku' => $this->m_gudang->getAllBahanbaku()->result(),
        );
        $this->load_admin_view('admin/gudang/pengambilanBahan', $data);
    }

    public function action_add_barang_keluar()
    {
        $material_id = $this->input->post('material_id', true);
        $qty = $this->input->post('qty', true);
        $stok = $this->m_gudang->getStok($material_id)->row_array();
        if (!$stok || $qty > $stok['qty']) {
            $this->session->set_flashdata('error', 'Stok tidak mencukupi');
            redirect('warehouse/PengambilanBahan');
            return;
        }
        $data = array('material_id' => $material_id, 'bahan_id' => $this->input->post('bahan_id', true), 'qty' => $qty);
        if ($this->db->insert('tbl_temp_pengambilan', $data)) {
            $this->session->set_flashdata('success', 'Berhasil menambahkan material');
        } else {
            $this->session->set_flashdata('error', 'Gagal menambahkan material');
        }
        redirect('warehouse/PengambilanBahan');
    }

    public function cetak_barcode_semua()
    {
        $data['bahan'] = $this->m_gudang->getAllBahan()->result();
        $this->load->view('admin/gudang/cetak_barcode', $data);
    }

    public function cetak_barcode($bahanId)
    {
        $data['bahan'] = $this->m_gudang->getBahan($bahanId)->row_array();
        $this->load->view('admin/gudang/get_cetak_barcode', $data);
    }
    public function deletePembelian($id)
    {
        $where = array('pembelian_id' => $id);
        $this->m_gudang->deleteData('tbl_pembelian_detail', $where);
        $this->m_gudang->deleteData('tbl_hutang', $where);
        $this->m_gudang->deleteData('tbl_pembelian', $where);
        redirect('warehouse/DaftarPembelian');
    }

    public function updateHarga()
    {
        $pemb_detail_id = $this->input->post('pemb_detail_id', true);
        $harga = str_replace(',', '', $this->input->post('pemb_harga', true));
        $harga = is_numeric($harga) ? $harga : 0;
        $this->db->update('tbl_pembelian_detail', array('pemb_harga' => $harga), array('pemb_detail_id' => $pemb_detail_id));
        redirect('warehouse/DaftarPembelian');
    }
    // =====================================================
    // 16. MASTER CATEGORIES (Migrated from Kategori)
    // =====================================================


    // =====================================================
    // 17. MASTER SUB-CATEGORIES (Migrated from Subkategori)
    // =====================================================

    public function subcategories()
    {
        $this->db->select('tbl_subkategori.*, tbl_kategori.kategori_nama');
        $this->db->from('tbl_subkategori');
        $this->db->join('tbl_kategori', 'tbl_kategori.kategori_id = tbl_subkategori.subkategori_ket', 'left');
        $this->db->order_by('tbl_subkategori.subkategori_urutan', 'ASC');

        $data = array(
            'function' => 'Data Sub Kategori',
            'title' => 'Daftar Sub Kategori',
            'tampilData' => $this->db->get()->result(),
            'kategori' => $this->db->get('tbl_kategori')->result(),
            'total_rows' => $this->db->count_all('tbl_subkategori'),
        );
        $this->load_admin_view('admin/subkategori/index', $data);
    }

    public function actionAddSubcategory()
    {
        $use_pxl = $this->input->post('use_pxl', true) ? 1 : 0;
        $config = json_encode(['use_pxl' => $use_pxl]);

        $data = array(
            'subkategori_nama' => $this->input->post('subkategori_nama', true),
            'subkategori_deskripsi' => $this->input->post('subkategori_deskripsi', true),
            'subkategori_urutan' => $this->input->post('subkategori_urutan', true),
            'status' => $this->input->post('status', true),
            'subkategori_ket' => $this->input->post('kategori_id', true),
            'subkategori_config' => $config
        );
        if ($this->M_master->addData('tbl_subkategori', $data)) {
            $this->session->set_flashdata('success', 'Data Berhasil Disimpan');
        }

        $redirect_id = $this->input->post('redirect_kategori_id', true);
        if ($redirect_id) {
            redirect('pengaturan/subkategori?kategori_id=' . $redirect_id);
        } else {
            redirect('pengaturan/subkategori');
        }
    }

    public function actionUpdateSubcategory()
    {
        $id = $this->input->post('subkategori_id', true);
        $use_pxl = $this->input->post('use_pxl', true) ? 1 : 0;
        $config = json_encode(['use_pxl' => $use_pxl]);

        $where = array('subkategori_id' => $id);
        $data = array(
            'subkategori_nama' => $this->input->post('subkategori_nama', true),
            'subkategori_deskripsi' => $this->input->post('subkategori_deskripsi', true),
            'subkategori_urutan' => $this->input->post('subkategori_urutan', true),
            'status' => $this->input->post('status', true),
            'subkategori_ket' => $this->input->post('kategori_id', true),
            'subkategori_config' => $config
        );
        if ($this->M_master->updateData('tbl_subkategori', $where, $data)) {
            $this->session->set_flashdata('success', 'Data Berhasil Diupdate');
        }

        $redirect_id = $this->input->post('redirect_kategori_id', true);
        if ($redirect_id) {
            redirect('pengaturan/subkategori?kategori_id=' . $redirect_id);
        } else {
            redirect('pengaturan/subkategori');
        }
    }

    public function actionDeleteSubcategory($id)
    {
        $this->db->where('subkategori_id', $id);
        if ($this->db->delete('tbl_subkategori')) {
            $this->session->set_flashdata('success', 'Data Berhasil Dihapus');
        }

        $redirect_id = $this->input->get('kategori_id', true);
        if ($redirect_id) {
            redirect('pengaturan/subkategori?kategori_id=' . $redirect_id);
        } else {
            redirect('pengaturan/subkategori');
        }
    }

    // =====================================================
    // 18. MASTER UNITS (Migrated from Satuan)
    // =====================================================


    // =====================================================
    // 19. MASTER SUPPLIER (Migrated from Supplier)
    // =====================================================


    // =====================================================
    // 20. MASTER MATERIALS & JASA (Migrated from Bahan)
    // =====================================================

    public function materials_jasa()
    {
        $data = array(
            'title' => 'Master Induk & Jasa',
            'table' => 'tbl_kategori',
            'tampilData' => $this->M_master->getDataBahan()->result(),
            'total_rows' => $this->M_master->getDataBahan()->num_rows(),
            'satuan' => $this->db->get('tbl_satuan')->result(),
            'last_barcode' => $this->db->select('barcode')->from('tbl_bahan')->order_by('CAST(barcode AS UNSIGNED)', 'DESC')->limit(1)->get()->row(),
        );

        // Count statistics by jenis_barang
        $data['jumlah_bahan_baku'] = $this->db->get_where('tbl_bahan', ['jenis_barang' => 'bahan_baku'])->num_rows();
        $data['jumlah_bahan_operasional'] = $this->db->get_where('tbl_bahan', ['jenis_barang' => 'bahan_operasional'])->num_rows();
        $data['jumlah_barang_jadi'] = $this->db->get_where('tbl_bahan', ['jenis_barang' => 'barang_jadi'])->num_rows();
        $data['jumlah_spare_part'] = $this->db->get_where('tbl_bahan', ['jenis_barang' => 'spare_part'])->num_rows();
        $data['jumlah_operasional'] = $this->db->get_where('tbl_bahan', ['jenis_barang' => 'operasional'])->num_rows();

        $this->load_admin_view('admin/warehouse/materials_jasa', $data);
    }

    public function product_bahan_detail($kode)
    {
        $namabahan = $this->db->get_where('tbl_bahan', array('bahan_id' => $kode))->row_array();
        $data = array(
            'title' => 'Daftar product Bahan ' . ($namabahan['bahan_nama'] ?? ''),
            'tampil' => $this->M_master->get_produk_by_bahan($kode)->result(),
        );
        $this->load_admin_view('admin/warehouse/product_bahan', $data);
    }

    public function actionAddBahanLegacy()
    {
        $barcode = $this->input->post('barcode', true);
        if (empty($barcode)) {
            $last = $this->db->select('barcode')->from('tbl_bahan')->order_by('bahan_id', 'DESC')->limit(1)->get()->row();
            $barcode = ($last && is_numeric($last->barcode)) ? str_pad($last->barcode + 1, 8, '0', STR_PAD_LEFT) : '00000001';
        }

        $data = array(
            'barcode' => $barcode,
            'bahan_nama' => $this->input->post('bahan_nama', true),
            'satuan_id' => $this->input->post('satuan_id', true),
            'jenis_barang' => $this->input->post('jenis_barang', true),
        );

        $bahanid = $this->input->post('bahan_id', true);
        if (empty($bahanid)) {
            $last_id = $this->db->select('bahan_id')->from('tbl_bahan')->order_by('bahan_id', 'DESC')->limit(1)->get()->row();
            $bahanid = $last_id ? ($last_id->bahan_id + 1) : 1;
        }

        if ($this->M_master->addData('tbl_bahan', array_merge($data, ['bahan_id' => $bahanid]))) {
            $this->session->set_flashdata('success', 'Data Berhasil Disimpan');
        }
        redirect('warehouse/materials_jasa');
    }

    public function actionUpdateBahanLegacy()
    {
        $id = $this->input->post('bahan_id', true);
        $data = [
            'bahan_nama' => $this->input->post('bahan_nama', true),
            'jenis_barang' => $this->input->post('jenis_barang', true),
            'satuan_id' => $this->input->post('satuan_id', true),
        ];
        $this->db->where('bahan_id', $id);
        if ($this->db->update('tbl_bahan', $data)) {
            $this->session->set_flashdata('success', 'Data Berhasil Diupdate');
        }
        redirect('warehouse/materials_jasa');
    }

    public function actionDeleteBahanLegacy($id)
    {
        $this->db->where('bahan_id', $id);
        if ($this->db->delete('tbl_bahan')) {
            $this->session->set_flashdata('success', 'Data Berhasil Dihapus');
        }
        redirect('warehouse/materials_jasa');
    }

    public function bulkUpdateSatuan()
    {
        header('Content-Type: application/json');

        $ids = $this->input->post('ids', true);       // array of bahan_id
        $satuan_id = (int) $this->input->post('satuan_id', true);

        // Validate
        if (empty($ids) || !is_array($ids) || $satuan_id <= 0) {
            echo json_encode(['success' => false, 'message' => 'Data tidak valid.']);
            return;
        }

        // Ensure satuan exists
        $satuan = $this->db->get_where('tbl_satuan', ['satuan_id' => $satuan_id])->row();
        if (!$satuan) {
            echo json_encode(['success' => false, 'message' => 'Satuan tidak ditemukan.']);
            return;
        }

        // Sanitize IDs to integers only
        $safe_ids = array_filter(array_map('intval', $ids), fn($v) => $v > 0);
        if (empty($safe_ids)) {
            echo json_encode(['success' => false, 'message' => 'Tidak ada ID yang valid.']);
            return;
        }

        $this->db->where_in('bahan_id', $safe_ids);
        $this->db->update('tbl_bahan', ['satuan_id' => $satuan_id]);

        $affected = $this->db->affected_rows();
        echo json_encode([
            'success' => true,
            'message' => $affected . ' data berhasil diperbarui ke satuan "' . $satuan->satuan_nama . '".'
        ]);
    }

    public function bulkUpdateJenis()
    {
        header('Content-Type: application/json');

        $ids = $this->input->post('ids', true);       // array of bahan_id
        $jenis_barang = $this->input->post('jenis_barang', true);

        // Validate
        if (empty($ids) || !is_array($ids) || empty($jenis_barang)) {
            echo json_encode(['success' => false, 'message' => 'Data tidak valid.']);
            return;
        }

        // Sanitize IDs to integers only
        $safe_ids = array_filter(array_map('intval', $ids), fn($v) => $v > 0);
        if (empty($safe_ids)) {
            echo json_encode(['success' => false, 'message' => 'Tidak ada ID yang valid.']);
            return;
        }

        $this->db->where_in('bahan_id', $safe_ids);
        $this->db->update('tbl_bahan', ['jenis_barang' => $jenis_barang]);

        $affected = $this->db->affected_rows();
        echo json_encode([
            'success' => true,
            'message' => "Jenis berhasil diperbarui untuk $affected item."
        ]);
    }

    public function bulkGenerateBarcodes()
    {
        if (!$this->input->is_ajax_request())
            exit('No direct access allowed');

        // 1. Get max barcode from both tables to ensure uniqueness
        $max_bahan = $this->db->select_max('barcode')->get('tbl_bahan')->row()->barcode;
        $max_material = $this->db->select_max('barcode')->get('tbl_material')->row()->barcode;

        $max = max((int) $max_bahan, (int) $max_material);

        // If no barcodes yet or under 10.000.000, start from a safe range
        if ($max < 10000000)
            $max = 10000000;

        // 2. Find empty barcodes in tbl_material (Variants)
        $empty_materials = $this->db->group_start()
            ->where('barcode', '')
            ->or_where('barcode', NULL)
            ->or_where('barcode', '0')
            ->group_end()
            ->get('tbl_material')
            ->result();

        $count = 0;
        foreach ($empty_materials as $item) {
            $max++;
            $new_barcode = str_pad($max, 8, '0', STR_PAD_LEFT);
            $this->db->where('material_id', $item->material_id)
                ->update('tbl_material', ['barcode' => $new_barcode]);
            $count++;
        }

        // 3. Optional: Find empty barcodes in tbl_bahan (Induk) - usually already filled
        $empty_bahan = $this->db->group_start()
            ->where('barcode', '')
            ->or_where('barcode', NULL)
            ->or_where('barcode', '0')
            ->group_end()
            ->get('tbl_bahan')
            ->result();

        foreach ($empty_bahan as $item) {
            $max++;
            $new_barcode = str_pad($max, 8, '0', STR_PAD_LEFT);
            $this->db->where('bahan_id', $item->bahan_id)
                ->update('tbl_bahan', ['barcode' => $new_barcode]);
            $count++;
        }

        echo json_encode([
            'success' => true,
            'message' => "Berhasil menghasilkan $count barcode baru secara otomatis."
        ]);
    }

    public function updateBarcodeBahanLegacy()
    {
        $id = $this->input->post('bahan_id', true);
        $barcode = $this->input->post('barcode', true);
        if (empty($barcode)) {
            $last = $this->db->select('barcode')->from('tbl_bahan')->order_by('CAST(barcode AS UNSIGNED)', 'DESC')->limit(1)->get()->row();
            $barcode = ($last && is_numeric($last->barcode)) ? str_pad($last->barcode + 1, 8, '0', STR_PAD_LEFT) : '00000001';
        }
        $this->db->where('bahan_id', $id);
        $this->db->update('tbl_bahan', ['barcode' => $barcode]);
        redirect('warehouse/materials_jasa');
    }

    public function cekBarcodeBahan()
    {
        header('Content-Type: application/json');
        $barcode = $this->input->post('barcode', true);
        $result = $this->db->get_where('tbl_bahan', ['barcode' => $barcode])->row_array();
        echo json_encode($result ? ['status' => 'ada', 'data' => $result] : ['status' => 'tidak_ada']);
    }
    // ===========================================
    // Sync Logic Migrated from Pembelian.php
    // ===========================================

    public function deleteTempAmbil($id)
    {
        $where = array('temp_id' => $id);
        $this->m_gudang->deleteData('tbl_temp_pengambilan', $where);
        redirect('warehouse/PengambilanBahan');
    }

    public function syncronisasi_stok()
    {
        $userid = $this->session->userdata('id');
        $conf = $this->_get_conf();

        $data = array(
            'controller' => $this->uri->segment(1),
            'function' => $this->uri->segment(2),
            'title' => 'Syncronnisasi Stok Masuk',
            'table' => 'tbl_kategori',
            'bulanini' => date('m'),
            'tampilData' => $this->m_gudang->stokjasa_masuk($this->session->userdata('perusahaan_id'))->result(),
            'total_rows' => $this->m_gudang->getDataBahan()->num_rows(),
            'logo' => $conf['perusahaan']['logo'],
            'gudangmasuk' => $this->m_gudang->stokjasa($this->session->userdata('perusahaan_id'))->num_rows(),
            'jml_stok_minim' => $this->m_gudang->stok_minim()->num_rows(),
            'level' => $this->db->get_where('users', array('user_id' => $userid))->row_array(),
        );
        $this->load_admin_view('admin/warehouse/syncronisasi_stok', $data);
    }

    function syncronisasi_stok_action()
    {
        $this->form_validation->set_rules('qty_real', 'Quanntity real', 'required');

        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata('error', 'Gagal menambahkan data');
            redirect('warehouse/syncronisasi_stok');
        } else {
            $bahanId = $this->input->post('bahan_id', true);
            $cekStok = $this->m_gudang->getStokBahan($bahanId)->row_array();

            $where = ['bahan_id' => $bahanId];
            $updateStok = ['stok_tambah' => $cekStok['stok_tambah'] + $this->input->post('qty_real', true)];

            $this->m_gudang->updateData('tbl_stok_bahan', $where, $updateStok);

            $where1 = ['pengambilan_id' => $this->input->post('pengambilan_id', true)];
            $update1 = ['approve' => '1'];

            $this->m_gudang->updateData('tbl_pengambilan_detail', $where1, $update1);
        }

        $this->session->set_flashdata('success', 'Berhasil menambahkan data');
        redirect('warehouse/syncronisasi_stok');
    }

    public function reloadstok()
    {
        $stok_id = $this->input->post('stok_id', true);
        $stokaktif = $this->input->post('stokaktif', true);

        $where = array('stok_id' => $stok_id);
        $update = array(
            'stok_awal' => $stokaktif,
            'stok_tambah' => 0,
            'stok_kurang' => 0,
        );

        $this->m_gudang->updateData('tbl_stok_bahan', $where, $update);
        redirect('warehouse');
    }

    /**
     * 8. PENGAJUAN MATERIAL (Internal Request from Production)
     */
    public function requests()
    {
        $conf = $this->_get_conf();
        $user_id = $this->session->userdata('id');

        $data = [
            'controller' => 'warehouse',
            'function' => 'requests',
            'title' => 'Pengajuan Material Produksi',
            'perusahaan' => $conf['perusahaan']['nama_perusahaan'],
            'hakakses' => $conf['hakakses']['divisi_nama'],
            'materials' => $this->warehouse->get_materials_dropdown(),
            'warehouses' => $this->warehouse->get_all_warehouses(),
            'temp_items' => $this->warehouse->get_temp_requests($user_id),
            'recent_requests' => $this->warehouse->get_requests(['current_month' => true])
        ];

        $this->load_admin_view('admin/warehouse/requests', $data);
    }

    public function requests_add_item()
    {
        if ($this->input->post()) {
            $data = [
                'material_id' => $this->input->post('material_id', true),
                'qty' => $this->input->post('qty', true),
                'keterangan' => $this->input->post('keterangan', true),
                'user_id' => $this->session->userdata('id')
            ];
            $this->db->insert('tbl_temp_pengajuan', $data);
            $this->session->set_flashdata('success', 'Item ditambahkan ke daftar pengajuan.');
        }
        redirect('warehouse/requests');
    }

    public function requests_delete_item($temp_id)
    {
        $this->db->delete('tbl_temp_pengajuan', ['temp_id' => $temp_id, 'user_id' => $this->session->userdata('id')]);
        $this->session->set_flashdata('success', 'Item dihapus dari daftar.');
        redirect('warehouse/requests');
    }

    public function requests_finalize()
    {
        if ($this->input->post()) {
            $user_id = $this->session->userdata('id');
            $gudang_id = $this->input->post('gudang_id', true);
            $pengajuan_tgl = $this->input->post('pengajuan_tgl', true);

            $temp_items = $this->warehouse->get_temp_requests($user_id);

            if (empty($temp_items)) {
                $this->session->set_flashdata('error', 'Daftar pengajuan masih kosong!');
                redirect('warehouse/requests');
            }

            $this->db->trans_start();

            // 1. Header
            $header_data = [
                'pengajuan_tgl' => $pengajuan_tgl,
                'pengajuan_waktu' => date('H:i:s'),
                'karyawan_id' => $this->session->userdata('karyawan_id') ?: $user_id,
                'gudang_id' => $gudang_id,
                'status' => 0, // Pending
                'perusahaan_id' => $this->session->userdata('perusahaan_id')
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
                $this->session->set_flashdata('success', 'Pengajuan material berhasil dikirim dan menunggu verifikasi.');
            } else {
                $this->session->set_flashdata('error', 'Gagal mengirim pengajuan.');
            }
        }
        redirect('warehouse/requests');
    }

    public function requests_get_detail_json($id)
    {
        $details = $this->warehouse->get_request_details($id);

        // Enrich with current stock from Main Warehouse (ID 1)
        foreach ($details as &$item) {
            $item->stock_utama = $this->warehouse->get_current_stock($item->material_id, 1);
        }

        header('Content-Type: application/json');
        echo json_encode(['status' => 'success', 'data' => $details]);
    }

    /**
     * AJAX: Process Request into Dispatch Staging
     */
    public function ajax_process_request_to_dispatch($id)
    {
        $details = $this->warehouse->get_request_details($id);
        if (empty($details)) {
            echo json_encode(['status' => 'error', 'message' => 'Detail pengajuan tidak ditemukan.']);
            return;
        }

        $this->db->trans_start();

        // 1. Clear current dispatch temp for this user
        $this->db->empty_table('tbl_temp_pengambilan'); // Or by user_id if table supports it

        // 2. Map items to dispatch temp
        foreach ($details as $item) {
            $this->db->insert('tbl_temp_pengambilan', [
                'material_id' => $item->material_id,
                'bahan_id' => ($this->db->get_where('tbl_material', ['material_id' => $item->material_id])->row()->bahan_id),
                'qty' => $item->qty,
                'keterangan' => 'Referensi DOC#' . str_pad($id, 5, '0', STR_PAD_LEFT) . ': ' . $item->keterangan
            ]);
        }

        // 3. Mark request as 'Processing' or Verified
        $this->db->where('pengajuan_id', $id)->update('tbl_pengajuan_material', ['status' => 1]); // 1 = Verified/Processed

        $this->db->trans_complete();

        if ($this->db->trans_status() === TRUE) {
            echo json_encode(['status' => 'success', 'message' => 'Data berhasil dipindahkan ke Dispatch Staging.']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Gagal memproses pengajuan.']);
        }
    }

    /**
     * Reject request
     */
    public function requests_reject($id)
    {
        $this->db->where('pengajuan_id', $id)->update('tbl_pengajuan_material', ['status' => 2]); // 2 = Rejected
        $this->session->set_flashdata('success', 'Pengajuan berhasil ditolak.');
        redirect('warehouse/requests');
    }

    /**
     * SYSTEM RESET - Prepare for Warehouse Reset
     */
    public function system_reset()
    {
        $conf = $this->_get_conf();

        // Security: Only allow high-level roles (e.g. Manager or Admin)
        // Adjust this if necessary
        if ($this->session->userdata('level') > 2) {
            $this->session->set_flashdata('error', 'Anda tidak memiliki hak akses untuk fitur ini.');
            redirect('warehouse');
        }

        $data = [
            'controller' => 'warehouse',
            'function' => 'system_reset',
            'title' => 'Factory Reset Data Gudang',
            'perusahaan' => $conf['perusahaan']['nama_perusahaan'],
            'hakakses' => $conf['hakakses']['divisi_nama'],
        ];

        $this->load_admin_view('admin/warehouse/system_reset', $data);
    }

    /**
     * SYSTEM RESET - Execute Clearing Data
     */
    public function action_system_reset()
    {
        $confirm = $this->input->post('confirm_code', true);

        if ($confirm !== 'RESET-GUDANG') {
            $this->session->set_flashdata('error', 'Kode konfirmasi salah!');
            redirect('warehouse/system_reset');
        }

        // Security
        if ($this->session->userdata('level') > 2) {
            $this->session->set_flashdata('error', 'Akses ditolak.');
            redirect('warehouse');
        }

        $result = $this->warehouse->factory_reset();

        if ($result) {
            // [AUDIT LOG]
            $this->_log_manual("System Factory Reset", "User performing complete data wipe of warehouse/production processes.", 'DELETE');

            $this->session->set_flashdata('success', 'Sistem berhasil dibersihkan! Seluruh riwayat transaksi telah dikosongkan.');
        } else {
            $this->session->set_flashdata('error', 'Terjadi kesalahan saat mengosongkan data.');
        }

        redirect('warehouse');
    }
    /**
     * AJAX: Add new supplier (Used by Quick Add in Purchase view)
     */
    public function supplier_add()
    {
        $data = [
            'supplier_kode' => $this->input->post('supplier_kode', true),
            'supplier_nama' => $this->input->post('supplier_nama', true),
            'supplier_alamat' => $this->input->post('supplier_alamat', true) ?: '',
            'kota_provinsi' => $this->input->post('kota_provinsi', true) ?: '',
            'supplier_nohp' => $this->input->post('supplier_nohp', true) ?: '',
            'supplier_telp' => $this->input->post('supplier_telp', true) ?: '',
            'supplier_email' => $this->input->post('supplier_email', true) ?: '',
            'alamat_pajak' => $this->input->post('alamat_pajak', true) ?: '',
            'NPWP' => $this->input->post('npwp', true) ?: '',
            'termin' => $this->input->post('termin', true) ?: 30
        ];
        $this->db->insert('tbl_supplier', $data);
        
        // [AUDIT LOG]
        $this->_log_manual("Tambah Supplier Baru (Quick Add via Warehouse): " . $data['supplier_nama'], json_encode($data), 'WRITE');
        
        echo json_encode(['status' => 'success', 'pesan' => 'Supplier berhasil ditambahkan.']);
    }

    /**
     * AJAX: Generate next supplier code
     */
    public function supplier_generate_code()
    {
        header('Content-Type: application/json');
        $this->db->select('supplier_kode');
        $this->db->like('supplier_kode', 'SUP', 'after');
        $this->db->order_by('supplier_id', 'DESC');
        $this->db->limit(1);
        $last_row = $this->db->get('tbl_supplier')->row();

        $last_code = $last_row ? $last_row->supplier_kode : 'SUP000';
        $num = (int) substr($last_code, 3);
        $code = 'SUP' . str_pad($num + 1, 3, '0', STR_PAD_LEFT);
        echo json_encode(['status' => 'success', 'code' => $code]);
    }

    /**
     * AJAX: Get all suppliers as JSON
     */
    public function supplier_get_json()
    {
        header('Content-Type: application/json');
        $data = $this->db->get('tbl_supplier')->result();
        echo json_encode(['status' => 'success', 'data' => $data]);
    }

    public function export_laporan_barang_keluar()
    {
        // Add export logic for items out report
    }

    /**
     * LAPORAN PENGIRIMAN (DISPATCH) DENGAN KALENDER
     */
    public function laporan_pengiriman()
    {
        $year = $this->input->get('year', true) ?: date('Y');
        $month = $this->input->get('month', true) ?: date('m');
        $selected_date = $this->input->get('date', true) ?: date('Y-m-d');

        $calendar_data = $this->m_gudang->getPengambilanCalendarData($year, $month)->result();
        $calendar_events = [];
        $total_transaksi_bulan = 0;
        $total_qty_bulan = 0;
        
        foreach ($calendar_data as $event) {
            $calendar_events[$event->tanggal] = [
                'jumlah_transaksi' => $event->jumlah_transaksi,
                'total_qty' => (float)$event->total_qty
            ];
            $total_transaksi_bulan += $event->jumlah_transaksi;
            $total_qty_bulan += $event->total_qty;
        }

        $pengiriman_data = $this->m_gudang->getPengambilanByDate($selected_date);
        $pengiriman_detail = $this->m_gudang->getPengambilanDetailByDate($selected_date)->result();

        $total_transaksi = count($pengiriman_data);
        $total_qty = 0;
        foreach ($pengiriman_detail as $detail) {
            $total_qty += $detail->qty;
        }

        $data = [
            'title' => 'Laporan Pengiriman (Dispatch)',
            'calendar_events' => $calendar_events,
            'selected_date' => $selected_date,
            'selected_year' => $year,
            'selected_month' => $month,
            'pengiriman_data' => $pengiriman_data,
            'pengiriman_detail' => $pengiriman_detail,
            'total_transaksi' => $total_transaksi,
            'total_qty' => $total_qty,
            'total_transaksi_bulan' => $total_transaksi_bulan,
            'total_qty_bulan' => $total_qty_bulan,
            'karyawan_list' => $this->db->get('tbl_karyawan')->result(),
            'gudang_list' => $this->db->get('tbl_gudang')->result(),
        ];
        $this->load_admin_view('admin/gudang/laporan_pengiriman', $data);
    }

    public function get_pengiriman_by_date()
    {
        $date = $this->input->post('date', true);
        if (!$date) {
            echo json_encode(['success' => false, 'message' => 'Tanggal tidak boleh kosong']);
            return;
        }

        $pengiriman_data = $this->m_gudang->getPengambilanByDate($date);
        $pengiriman_detail = $this->m_gudang->getPengambilanDetailByDate($date)->result();

        $total_transaksi = count($pengiriman_data);
        $total_qty = 0;
        foreach ($pengiriman_detail as $detail) {
            $total_qty += (float) $detail->qty;
        }

        $response = [
            'success' => true,
            'data' => [
                'pengiriman_data' => $pengiriman_data,
                'pengiriman_detail' => $pengiriman_detail,
                'total_transaksi' => $total_transaksi,
                'total_qty' => $total_qty,
                'formatted_date' => date_indo($date)
            ]
        ];

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($response));
    }
    public function cetak_laporan_pengiriman_harian()
    {
        $date = $this->input->get('date', true) ?: date('Y-m-d');
        
        $pengiriman_data = $this->m_gudang->getPengambilanByDate($date);
        $pengiriman_detail = $this->m_gudang->getPengambilanDetailByDate($date)->result();
        
        $total_transaksi = count($pengiriman_data);
        $total_qty = 0;
        foreach ($pengiriman_detail as $detail) {
            $total_qty += (float) $detail->qty;
        }

        $data = [
            'title' => 'Laporan Pengiriman Harian',
            'date' => $date,
            'pengiriman_data' => $pengiriman_data,
            'pengiriman_detail' => $pengiriman_detail,
            'total_transaksi' => $total_transaksi,
            'total_qty' => $total_qty,
            'formatted_date' => date_indo($date)
        ];

        $this->load->view('admin/gudang/cetak_laporan_pengiriman_harian', $data);
    }

    public function ajax_get_material_history()
    {
        if (!$this->input->is_ajax_request()) {
            exit('No direct script access allowed');
        }

        $material_id = (int)$this->input->get('material_id');
        $gudang_id = (int)$this->input->get('gudang_id');

        if ($material_id <= 0 || $gudang_id <= 0) {
            echo json_encode(['status' => 'error', 'message' => 'Invalid parameters']);
            return;
        }

        $history = $this->warehouse->get_material_history($material_id, $gudang_id);
        
        $formatted_data = [];
        foreach ($history as $row) {
            $konversi = (float)$row->konversi > 0 ? (float)$row->konversi : 1;
            $satuan_produksi = $row->satuan_produksi ?: 'pcs';
            $satuan_gudang = $row->satuan_gudang ?: 'pcs';
            
            // Calculate in big units (satuan gudang)
            $qty_in_big = (float)$row->qty_in;
            $qty_out_big = (float)$row->qty_out;
            $qty_balance_big = (float)$row->qty_balance / $konversi;

            // Calculate in base units (satuan produksi)
            $qty_in_base = round((float)$row->qty_in * $konversi);
            $qty_out_base = round((float)$row->qty_out * $konversi);
            $qty_balance_base = round((float)$row->qty_balance);

            // Clean up redundant (Total Base: ...) text from catatan to make it simpler
            $clean_catatan = preg_replace('/\s*\(Total Base: -?\d+(\.\d+)?\)/i', '', $row->catatan);

            $format_compound = function($qty_base) use ($konversi, $satuan_gudang, $satuan_produksi) {
                if ($konversi <= 1 || $qty_base == 0) {
                    return rtrim(rtrim(number_format($qty_base / max(1, $konversi), 3, ',', '.'), '0'), ',') . ' ' . $satuan_gudang;
                }
                $is_negative = $qty_base < 0;
                $abs_base = abs($qty_base);
                $whole = floor($abs_base / $konversi);
                $pieces = $abs_base % $konversi;
                
                $res = "";
                if ($whole > 0 || ($whole == 0 && $pieces == 0)) {
                    $res .= $whole . " " . $satuan_gudang;
                }
                if ($pieces > 0) {
                    $res .= ($res ? " " : "") . $pieces . " " . $satuan_produksi;
                }
                return ($is_negative && $res !== '0 ' . $satuan_gudang ? '-' : '') . $res;
            };

            $formatted_data[] = [
                'tanggal' => date('d/m/Y H:i', strtotime($row->created_at)),
                'trx_type' => $row->trx_type,
                'qty_in' => $qty_in_big,
                'qty_out' => $qty_out_big,
                'qty_balance' => $qty_balance_big,
                'qty_in_fmt' => $format_compound($qty_in_base),
                'qty_out_fmt' => $format_compound($qty_out_base),
                'qty_balance_fmt' => $format_compound($qty_balance_base),
                'qty_in_base_fmt' => number_format($qty_in_base, 0, ',', '.') . ' ' . $satuan_produksi,
                'qty_out_base_fmt' => number_format($qty_out_base, 0, ',', '.') . ' ' . $satuan_produksi,
                'qty_balance_base_fmt' => number_format($qty_balance_base, 0, ',', '.') . ' ' . $satuan_produksi,
                'catatan' => $clean_catatan,
                'username' => $row->username
            ];
        }

        echo json_encode(['status' => 'success', 'data' => $formatted_data]);
    }
}
