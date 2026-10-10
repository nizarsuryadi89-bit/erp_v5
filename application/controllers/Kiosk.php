<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Kiosk extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('M_kiosk');
        $this->load->model('M_transaksi');
        $this->load->helper('security');

        // Ensure timezone is correct
        date_default_timezone_set('Asia/Jakarta');

        $this->_init_kiosk_table();
    }

    private function _init_kiosk_table()
    {
        // Create table if not exists
        $this->db->query("CREATE TABLE IF NOT EXISTS tbl_pemesanan_mandiri (
            id INT AUTO_INCREMENT PRIMARY KEY,
            no_ref VARCHAR(20) NOT NULL,
            konsumen_id INT,
            konsumen_nama VARCHAR(100),
            no_wa VARCHAR(20),
            perusahaan_id INT,
            product_id INT,
            harga_id INT,
            qty DECIMAL(10,2),
            panjang DECIMAL(10,2),
            lebar DECIMAL(10,2),
            total_harga DECIMAL(15,2),
            catatan TEXT,
            status VARCHAR(20) DEFAULT 'draft',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            INDEX(no_ref),
            INDEX(konsumen_id),
            INDEX(status)
        )");

        // Add konsumen_id column if it doesn't exist (migration for existing tables)
        $fields = $this->db->field_data('tbl_pemesanan_mandiri');
        $has_konsumen_id = false;
        foreach ($fields as $field) {
            if ($field->name == 'konsumen_id') {
                $has_konsumen_id = true;
                break;
            }
        }

        if (!$has_konsumen_id) {
            $this->db->query("ALTER TABLE tbl_pemesanan_mandiri ADD COLUMN konsumen_id INT AFTER no_ref");
            $this->db->query("ALTER TABLE tbl_pemesanan_mandiri ADD INDEX(konsumen_id)");
        }
    }

    /**
     * AJAX: Search existing member
     */
    public function search_member()
    {
        $term = $this->input->get('term', true);
        $this->db->like('konsumen_nama', $term);
        $this->db->or_like('konsumen_nohp', $term);
        $this->db->limit(10);
        $query = $this->db->get('tbl_konsumen')->result();

        $results = [];
        foreach ($query as $row) {
            $results[] = [
                'id' => $row->konsumen_id,
                'label' => $row->konsumen_nama . ' (' . $row->konsumen_nohp . ')',
                'nama' => $row->konsumen_nama,
                'nohp' => $row->konsumen_nohp
            ];
        }
        echo json_encode($results);
    }

    /**
     * Queue Landing Page - Quick registration for queue number
     */
    public function queue_landing()
    {
        // Branch ID handling (Default to 1 if not specified)
        $branch_id = (int) $this->input->get('perusahaan_id', true) ?: 1;

        // Fetch Branch Name
        $branch = $this->db->get_where('tbl_perusahaan', ['id_perusahaan' => $branch_id])->row();
        $branch_name = $branch ? $branch->nama_perusahaan : 'TGM Print';

        $data['branch_id'] = $branch_id;
        $data['branch_name'] = $branch_name;

        $this->load->view('kiosk/queue_landing', $data);
    }

    /**
     * Main Kiosk Dashboard - Central Control Panel (DEFAULT HOMEPAGE)
     * Access all branches' queue landing and display boards
     */
    public function index()
    {
        // Fetch all active branches/companies
        $this->db->where('status', '1'); // Active only
        $this->db->order_by('nama_perusahaan', 'ASC');
        $branches = $this->db->get('tbl_perusahaan')->result();

        $data['branches'] = $branches;
        $data['title'] = 'InstaPrint - Kiosk Dashboard';

        $this->load->view('kiosk/dashboard', $data);
    }

    /**
     * Order Form - Full product selection and cart
     * (Previously named 'index')
     */
    public function order_form()
    {
        // Branch ID handling (Default to 1 if not specified)
        $branch_id = (int) $this->input->get('perusahaan_id', true) ?: 1;

        // Fetch Branch Name
        $branch = $this->db->get_where('tbl_perusahaan', ['id_perusahaan' => $branch_id])->row();
        $branch_name = $branch ? $branch->nama_perusahaan : 'TGM Print';

        $data['title'] = 'InstaPrint TGM Core - ' . $branch_name;
        $data['branch_id'] = $branch_id;
        $data['branch_name'] = $branch_name;
        $data['products'] = $this->M_kiosk->get_products();

        // Load a clean kiosk view without the standard sidebar
        $this->load->view('kiosk/order_form', $data);
    }

    /**
     * AJAX: Get variants for a selected product
     */
    public function get_variants()
    {
        $product_id = (int) $this->input->post('product_id', true);
        $variants = $this->M_kiosk->get_product_variants($product_id);
        echo json_encode($variants);
    }

    /**
     * AJAX: Search Konsumen for Autocomplete (Case-Insensitive)
     * All customers searchable regardless of status (for kiosk self-service)
     */
    public function search_konsumen()
    {
        $term = trim($this->input->get('term', true));

        if (empty($term)) {
            header('Content-Type: application/json');
            echo json_encode([]);
            return;
        }

        // Case-insensitive search by name or phone
        $this->db->select('konsumen_id, konsumen_nama, konsumen_nohp');
        $this->db->group_start();
        $this->db->where("LOWER(konsumen_nama) LIKE '%" . strtolower($term) . "%'", NULL, FALSE);
        $this->db->or_where("konsumen_nohp LIKE '%" . $term . "%'", NULL, FALSE);
        $this->db->group_end();
        $this->db->order_by('konsumen_nama', 'ASC');
        $this->db->limit(10);
        $results = $this->db->get('tbl_konsumen')->result();

        $output = [];
        foreach ($results as $k) {
            $output[] = [
                'id' => $k->konsumen_id,
                'label' => $k->konsumen_nama . ' (' . $k->konsumen_nohp . ')',
                'value' => $k->konsumen_nama,
                'phone' => $k->konsumen_nohp
            ];
        }

        header('Content-Type: application/json');
        echo json_encode($output);
    }

    /**
     * AJAX: Calculate price estimate based on Quantity Range
     */
    public function get_price()
    {
        try {
            $product_id = (int) $this->input->post('product_id', true);
            $harga_id_ref = (int) $this->input->post('harga_id', true);
            $qty = (float) $this->input->post('qty', true) ?: 1;
            $panjang = (float) $this->input->post('panjang', true) ?: 0;
            $lebar = (float) $this->input->post('lebar', true) ?: 0;

            if (!$product_id || !$harga_id_ref) {
                echo json_encode(['status' => 'error', 'msg' => 'Parameter tidak lengkap']);
                return;
            }

            // Fetch reference data (Current Selection)
            $ref_data = $this->db->get_where('tbl_harga_product', ['harga_id' => $harga_id_ref])->row();

            if (!$ref_data) {
                echo json_encode(['status' => 'error', 'msg' => 'Varian tidak valid']);
                return;
            }

            $unit_price = 0;
            $final_harga_id = $ref_data->harga_id;
            $pricing_found = false;

            // STRATEGY 1: Check `tbl_detail_harga` (Explicit User Request)
            // Checked safely to prevent 500 error if table missing
            if ($this->db->table_exists('tbl_detail_harga')) {
                $this->db->where('harga_id', $ref_data->harga_id);
                $this->db->order_by('qty_min', 'ASC');
                $tiers = $this->db->get('tbl_detail_harga')->result();

                if (!empty($tiers)) {
                    foreach ($tiers as $tier) {
                        $max = ($tier->qty_max > 0) ? $tier->qty_max : 999999999;
                        if ($qty >= $tier->qty_min && $qty <= $max) {
                            $unit_price = $tier->harga;
                            $pricing_found = true;
                            break;
                        }
                    }
                    // Fallback for detail: Use max tier
                    if (!$pricing_found && !empty($tiers)) {
                        $last = end($tiers);
                        $unit_price = $last->harga;
                        $pricing_found = true;
                    }
                }
            }

            // STRATEGY 2: Dynamic Tier Search in `tbl_harga_product` (Simplified & Robust)
            // Finds the highest tier where `qty_min` is less than or equal to requested `qty`.
            if (!$pricing_found) {
                $this->db->from('tbl_harga_product');
                $this->db->where('product_id', $product_id);
                // Keep the detail_product lock as we want the specific variant
                $this->db->where('detail_product', $ref_data->detail_product);

                // Filter by Side/Ket_1 if set
                if (!empty($ref_data->ket_1) && $ref_data->ket_1 != '0') {
                    $this->db->where('ket_1', $ref_data->ket_1);
                }

                // Filter by Machine if set
                if (!empty($ref_data->jenisprint_id)) {
                    $this->db->where('jenisprint_id', $ref_data->jenisprint_id);
                }

                // Logic: price applies if qty >= qty_min
                // Order DESC to get the highest qualifying threshold (e.g. qty 100 vs tier 50 and 1) -> 50 wins
                // We simplify by ignoring qty_max to ensure we find a valid tier
                $lookup_qty = ($qty > 0 && $qty < 1) ? 1 : $qty;
                $this->db->where('qty_min <=', $lookup_qty);

                $this->db->order_by('qty_min', 'DESC');
                $this->db->limit(1);

                $param_match = $this->db->get()->row();

                if ($param_match) {
                    // Use harga_1 as price source
                    if (isset($param_match->harga_1) && $param_match->harga_1 > 0) {
                        $unit_price = (float) $param_match->harga_1;
                        $final_harga_id = $param_match->harga_id;
                        $pricing_found = true;
                    }
                }
            }

            // STRATEGY 3: Fallback to `harga_1` (Confirmed Data Source)
            if (!$pricing_found) {
                if (isset($ref_data->harga_1) && $ref_data->harga_1 > 0) {
                    $unit_price = (float) $ref_data->harga_1;
                    $pricing_found = true;
                } else {
                    // Log pricing issue for admin review
                    if (ENVIRONMENT !== 'production') {
                        log_message('error', "Kiosk: Price not found for product $product_id, harga_id $harga_id_ref");
                    }
                }
            }

            // Calculate
            // Calculate
            if ($panjang > 0 && $lebar > 0) {
                $area_m2 = ($panjang * $lebar) / 10000;
                $total = $area_m2 * $qty * $unit_price;
            } else {
                $total = $qty * $unit_price;
            }

            // Ensure only JSON is outputted
            ob_clean();
            header('Content-Type: application/json');
            echo json_encode([
                'status' => 'success',
                'harga_id' => $final_harga_id,
                'base_price' => $unit_price,
                'total_estimate' => $total,
                'formatted_total' => 'Rp ' . number_format($total, 0, ',', '.')
            ]);

        } catch (Exception $e) {
            log_message('error', 'Kiosk Price Calculation Error: ' . $e->getMessage());
            ob_clean();
            header('Content-Type: application/json');
            echo json_encode(['status' => 'error', 'msg' => 'Terjadi kesalahan saat menghitung harga. Silakan hubungi admin.']);
        }
    }

    /**
     * Submit CART (Array of Items)
     */
    public function submit_cart()
    {
        try {
            // Get Raw JSON Input
            $json = file_get_contents('php://input');
            $data = json_decode($json, true);

            if (!$data || !isset($data['customer']) || !isset($data['items'])) {
                echo json_encode(['status' => 'error', 'msg' => 'Invalid Data']);
                return;
            }

            $customer = $data['customer'];
            $items = $data['items'];

            // Debug log
            $konsumen_id = null;

            // 1. Priority: Use provided konsumen_id
            if (!empty($customer['konsumen_id'])) {
                $konsumen_id = $customer['konsumen_id'];
            } else {
                // 2. Fallback: Lookup
                $konsumen = null;

                // Try 1: By phone number
                if (!empty($customer['wa'])) {
                    $konsumen = $this->db->get_where('tbl_konsumen', ['konsumen_nohp' => $customer['wa']])->row();
                }

                // Try 2: By name if phone failed
                if (!$konsumen && !empty($customer['nama'])) {
                    $konsumen = $this->db->get_where('tbl_konsumen', ['konsumen_nama' => $customer['nama']])->row();
                }

                if ($konsumen) {
                    $konsumen_id = $konsumen->konsumen_id;
                }
            }

            if (!$konsumen_id) {
                // Create new konsumen as fallback
                $this->db->insert('tbl_konsumen', [
                    'konsumen_nama' => $customer['nama'] ?: 'Mobile Order Customer',
                    'konsumen_nohp' => $customer['wa'] ?: '-',
                    'konsumen_alamat' => '-',
                    'status' => '1'
                ]);
                $konsumen_id = $this->db->insert_id();

                if (!$konsumen_id) {
                    throw new Exception('Failed to create customer');
                }
            }

            // Prepare items with konsumen_id
            $insert_items = [];
            foreach ($items as $item) {
                $insert_items[] = [
                    'konsumen_id' => $konsumen_id,
                    'perusahaan_id' => $customer['branch_id'],
                    'product_id' => $item['product_id'],
                    'harga_id' => $item['harga_id'],
                    'qty' => $item['qty'],
                    'panjang' => $item['panjang'] ?: 0,
                    'lebar' => $item['lebar'] ?: 0,
                    'total_harga' => $item['subtotal'],
                    'catatan' => $item['catatan'] ?: '',
                    'status' => 'submitted'  // Sent to Deskprint, awaiting validation
                ];
            }

            if (empty($insert_items)) {
                echo json_encode(['status' => 'error', 'msg' => 'Cart is empty']);
                return;
            }

            // Check if M_kiosk model exists
            if (!method_exists($this->M_kiosk, 'save_batch_order')) {
                throw new Exception('Method M_kiosk::save_batch_order not found');
            }

            $ref = $this->M_kiosk->save_batch_order($insert_items);

            if (!$ref) {
                throw new Exception('Failed to save order: ' . $this->db->error()['message']);
            }

            echo json_encode(['status' => 'success', 'ref' => $ref]);

        } catch (Exception $e) {
            log_message('error', 'Submit Cart Error: ' . $e->getMessage());

            echo json_encode([
                'status' => 'error',
                'msg' => 'Terjadi kesalahan saat menyimpan pesanan. Silakan coba lagi atau hubungi admin.'
            ]);
        }
    }

    /**
     * Delete Draft Order Item
     */
    public function delete_draft_item()
    {
        try {
            $json = file_get_contents('php://input');
            $data = json_decode($json, true);
            $order_id = $data['order_id'] ?? null;

            if (!$order_id) {
                echo json_encode(['status' => 'error', 'msg' => 'Invalid ID']);
                return;
            }

            // Only allow deleting DRAFT status
            $this->db->where('id', $order_id);
            $this->db->where('status', 'draft');
            $this->db->delete('tbl_pemesanan_mandiri');

            if ($this->db->affected_rows() > 0) {
                echo json_encode(['status' => 'success']);
            } else {
                echo json_encode(['status' => 'error', 'msg' => 'Item not found or not draft']);
            }
        } catch (Exception $e) {
            log_message('error', 'Delete draft error: ' . $e->getMessage());
            echo json_encode(['status' => 'error', 'msg' => 'Gagal menghapus item']);
        }
    }

    /**
     * Deskprint: Fetch kiosk data and convert to SPK Step 2 (Updated for Multi-Item)
     */
    public function fetch_for_import()
    {
        // Must be logged in
        if (!$this->session->userdata('logged_in')) {
            echo json_encode(['status' => 'error', 'msg' => 'Session expired']);
            return;
        }

        $no_ref = $this->input->post('no_ref', true);
        $drafts = $this->M_kiosk->get_by_ref($no_ref); // Returns Array of Objects

        if (empty($drafts)) {
            echo json_encode(['status' => 'error', 'msg' => 'Data tidak ditemukan']);
            return;
        }

        // Check status on the FIRST item (assuming all share status)
        if ($drafts[0]->status == 'processed') {
            echo json_encode(['status' => 'error', 'msg' => 'Data sudah diproses sebelumnya']);
            return;
        }

        $user_id = $this->session->userdata('id');
        $perusahaan_id = $drafts[0]->perusahaan_id; // Assume same branch

        // 1. Get Konsumen ID (Now directly from tbl_pemesanan_mandiri)
        $konsumen_id = $drafts[0]->konsumen_id;

        // 2. Generate SPK Header (ONCE)
        // Get Last Number for this Month & Year & Branch
        $this->db->select('no_urut_spk');
        $this->db->where('perusahaan_id', $perusahaan_id);
        $this->db->where('MONTH(spk_time)', date('m'));
        $this->db->where('YEAR(spk_time)', date('Y'));
        $this->db->order_by('no_urut_spk', 'DESC');
        $last = $this->db->get('tbl_spk')->row();

        $noUrutSPK = ($last) ? $last->no_urut_spk + 1 : 1;

        // Generate Format: [BRANCH]-[USER]-[MMYY]-[SEQUENCE]
        $newSPK = $perusahaan_id . '-' . $user_id . '-' . date('my') . '-' . $noUrutSPK;

        // Insert SPK Header
        $this->db->insert('tbl_spk', [
            'nospk' => $newSPK,
            'user_id' => $user_id,
            'no_urut_spk' => $noUrutSPK,
            'konsumen_id' => $konsumen_id,
            'metode_pesan' => 1, // On Site Look
            'perusahaan_id' => $perusahaan_id,
            'spk_time' => date('Y-m-d H:i:s'), // Creation Time
        ]);

        // 3. Loop Insert Items to Temp Transaksi
        foreach ($drafts as $draft) {
            $data_temp = [
                'product_id' => $draft->product_id,
                'no_spk' => $newSPK,
                'konsumen_id' => $konsumen_id,
                'temp_qty' => $draft->qty,
                'temp_panjang' => ($draft->panjang > 0) ? $draft->panjang / 100 : 0, // Convert CM to M
                'temp_lebar' => ($draft->lebar > 0) ? $draft->lebar / 100 : 0,
                'user_id' => $user_id,
                'harga_id' => $draft->harga_id,
                'lokasi_cetak' => $perusahaan_id,
                'perusahaan_id' => $perusahaan_id,
                'approve' => 1,
                'dateline_tgl' => date('Y-m-d', strtotime('+1 day')),
                'dateline_jam' => '17:00:00',
                'keterangan' => $draft->catatan,
                // Store origin info for traceability
                'metadata' => json_encode(['origin' => 'kiosk', 'kiosk_ref' => $draft->no_ref])
            ];

            $this->db->insert('tbl_temp_transaksi', $data_temp);
        }

        // 4. Mark as 'processed' instead of deleting (for history tracking)
        $this->db->where('konsumen_id', $konsumen_id);
        $this->db->where('status', 'submitted');
        $this->db->like('no_ref', $no_ref, 'after');
        $this->db->update('tbl_pemesanan_mandiri', [
            'status' => 'processed',
            'spk_no' => $newSPK,
            'processed_at' => date('Y-m-d H:i:s')
        ]);

        echo json_encode([
            'status' => 'success',
            'redirect' => site_url('transaksi/createspk/' . $newSPK)
        ]);
    }

    /**
     * List Pending Kiosk Orders for Deskprint
     */
    public function list_pending_orders()
    {
        // Must be logged in
        if (!$this->session->userdata('logged_in')) {
            echo json_encode(['status' => 'error', 'msg' => 'Session expired']);
            return;
        }

        // SECURITY: Get branch from logged-in user session
        $branch_id = (int) $this->session->userdata('perusahaan_id');
        $where_branch = $branch_id ? "AND pm.perusahaan_id = $branch_id" : "";

        $sql = "
            SELECT 
                SUBSTRING_INDEX(pm.no_ref, '-', 1) as group_ref, 
                MAX(k.konsumen_nama) as nama, 
                MAX(k.konsumen_nohp) as wa,
                MAX(pm.created_at) as tgl,
                COUNT(*) as item_count,
                SUM(pm.total_harga) as est_total
            FROM tbl_pemesanan_mandiri pm
            LEFT JOIN tbl_konsumen k ON pm.konsumen_id = k.konsumen_id
            WHERE pm.status IN ('draft', 'submitted')
            $where_branch
            GROUP BY group_ref 
            ORDER BY tgl ASC
        ";

        $query = $this->db->query($sql);
        $result = $query->result();

        // Format dates
        foreach ($result as &$r) {
            $r->time_ago = $this->time_elapsed_string($r->tgl);
            $r->formatted_total = 'Rp ' . number_format($r->est_total, 0, ',', '.');
        }

        echo json_encode(['status' => 'success', 'data' => $result]);
    }

    private function time_elapsed_string($datetime, $full = false)
    {
        $now = new DateTime;
        $ago = new DateTime($datetime);
        $diff = $now->diff($ago);

        $w = floor($diff->d / 7);
        $d = $diff->d - ($w * 7);

        $string = array(
            'y' => 'thn',
            'm' => 'bln',
            'w' => 'mgg',
            'd' => 'hari',
            'h' => 'jam',
            'i' => 'mnt',
            's' => 'dtk',
        );
        $values = [
            'y' => $diff->y,
            'm' => $diff->m,
            'w' => (int) $w,
            'd' => (int) $d,
            'h' => $diff->h,
            'i' => $diff->i,
            's' => $diff->s,
        ];

        foreach ($string as $k => &$v) {
            if ($values[$k]) {
                $v = $values[$k] . ' ' . $v;
            } else {
                unset($string[$k]);
            }
        }

        if (!$full)
            $string = array_slice($string, 0, 1);
        return $string ? implode(', ', $string) . ' yang lalu' : 'baru saja';
    }

    /**
     * Page View: Data Order Queue for Deskprint
     */
    public function data_order()
    {
        // Must be logged in
        if (!$this->session->userdata('logged_in')) {
            redirect('auth');
        }

        // SECURITY: Get branch from logged-in user session
        $branch_id = (int) $this->session->userdata('perusahaan_id');
        $where_branch = $branch_id ? "AND pm.perusahaan_id = $branch_id" : "";

        // Fetch Data
        $sql = "
            SELECT 
                SUBSTRING_INDEX(pm.no_ref, '-', 1) as group_ref, 
                k.konsumen_nama as nama, 
                k.konsumen_nohp as wa,
                MAX(pm.created_at) as tgl,
                COUNT(*) as item_count,
                SUM(pm.total_harga) as est_total,
                GROUP_CONCAT(CONCAT('<b>', p.product_nama, '</b> <small class=\'text-muted\'>', hp.detail_product, '</small> <span class=\'badge bg-light text-dark border ms-1\'>', pm.qty, '</span>') SEPARATOR '<br>') as item_summary
            FROM tbl_pemesanan_mandiri pm
            LEFT JOIN tbl_konsumen k ON pm.konsumen_id = k.konsumen_id
            LEFT JOIN tbl_product p ON pm.product_id = p.product_id
            LEFT JOIN tbl_harga_product hp ON pm.harga_id = hp.harga_id
            WHERE pm.status IN ('draft', 'submitted')
            $where_branch
            GROUP BY group_ref, k.konsumen_nama, k.konsumen_nohp
            ORDER BY tgl ASC
        ";
        $result = $this->db->query($sql)->result();

        // Format dates
        foreach ($result as &$r) {
            $r->time_ago = $this->time_elapsed_string($r->tgl);
        }
        $data['orders'] = $result;

        // Load View with Admin Template (Assuming standard TGM ERP structure)
        // If template library exists
        if (isset($this->template)) {
            $this->template->load('template', 'kiosk/data_order', $data);
        } else {
            // Fallback: try manual header/footer or just clean page
            // Checking if 'template/header' exists is hard.
            // Let's assume standard 'admin/template' or just load view directly
            // Given Createspk is in admin/transaksi, simpler to just load a standalone view 
            // but styled like admin. Use 'data_order' view.
            $this->load->view('kiosk/data_order', $data);
        }
    }

    /**
     * AJAX: Register New Customer
     */
    public function register_customer()
    {
        // Clean output buffer and set JSON response
        if (ob_get_level() > 0) {
            ob_clean();
        } else {
            ob_start();
        }
        error_reporting(0);
        ini_set('display_errors', 0);
        header('Content-Type: application/json');

        $name = trim($this->input->post('name', true));
        $phone = trim($this->input->post('phone', true));
        $email = trim($this->input->post('email', true));
        $address = trim($this->input->post('address', true));

        if (empty($name) || empty($phone)) {
            echo json_encode(['status' => 'error', 'message' => 'Nama dan Nomor HP wajib diisi']);
            return;
        }

        // Check existing
        $existing = $this->db->get_where('tbl_konsumen', ['konsumen_nohp' => $phone])->row();

        if ($existing) {
            // Update existing data if provided to enrich profile
            $update_data = [];
            if (!empty($email) && empty($existing->konsumen_email)) {
                $update_data['konsumen_email'] = $email;
            }
            if (!empty($address) && ($existing->konsumen_alamat == '-' || empty($existing->konsumen_alamat))) {
                $update_data['konsumen_alamat'] = $address;
            }

            // Only update name if current is shorter (assuming fuller name provided)
            if (strlen($name) > strlen($existing->konsumen_nama)) {
                $update_data['konsumen_nama'] = $name;
            }

            if (!empty($update_data)) {
                $this->db->where('konsumen_id', $existing->konsumen_id);
                $this->db->update('tbl_konsumen', $update_data);
            }

            echo json_encode([
                'status' => 'success',
                'message' => 'Nomor WhatsApp sudah terdaftar. Data diperbarui.',
                'konsumen_id' => $existing->konsumen_id
            ]);
        } else {
            // Create New
            $data_insert = [
                'konsumen_nama' => $name,
                'konsumen_nohp' => $phone,
                'konsumen_alamat' => $address ?: '-',
                'konsumen_email' => $email,
                'status' => '1'
            ];

            $this->db->insert('tbl_konsumen', $data_insert);
            $id = $this->db->insert_id();

            if ($id) {
                echo json_encode(['status' => 'success', 'message' => 'Pendaftaran Berhasil', 'konsumen_id' => $id]);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Gagal menyimpan data database']);
            }
        }
    }

    /**
     * AJAX: Create Queue Number
     */
    public function create_queue()
    {
        $name = $this->input->post('customer_name', true);
        $phone = $this->input->post('customer_phone', true);
        $branch_id = (int) $this->input->post('branch_id', true);

        if (empty($name) || empty($phone)) {
            echo json_encode(['status' => 'error', 'message' => 'Nama dan nomor HP wajib diisi']);
            return;
        }

        // 1. Find or Create Konsumen
        $konsumen = $this->db->get_where('tbl_konsumen', ['konsumen_nohp' => $phone])->row();

        if ($konsumen) {
            $konsumen_id = $konsumen->konsumen_id;
            // Update name if different
            if ($konsumen->konsumen_nama != $name) {
                $this->db->where('konsumen_id', $konsumen_id);
                $this->db->update('tbl_konsumen', ['konsumen_nama' => $name]);
            }
        } else {
            // Create new konsumen
            $this->db->insert('tbl_konsumen', [
                'konsumen_nama' => $name,
                'konsumen_nohp' => $phone,
                'konsumen_alamat' => '-',
                'status' => '1'
            ]);
            $konsumen_id = $this->db->insert_id();
        }

        // 2. Generate queue number using MAX + 1 (Simpler & More Reliable)
        $date = date('Y-m-d');
        $prefix = 'A';

        // Start transaction
        $this->db->trans_start();

        // Get MAX queue number for today
        $this->db->select('MAX(CAST(SUBSTRING(queue_number, 3) AS UNSIGNED)) as max_num');
        $this->db->where('branch_id', $branch_id);
        $this->db->where("DATE(created_at) = '$date'", NULL, FALSE);
        $result = $this->db->get('tbl_queue')->row();

        $new_number = ($result && $result->max_num) ? $result->max_num + 1 : 1;
        $queue_number = $prefix . '-' . str_pad($new_number, 3, '0', STR_PAD_LEFT);

        // Insert queue record with konsumen_id
        $queue_data = [
            'queue_number' => $queue_number,
            'branch_id' => $branch_id,
            'konsumen_id' => $konsumen_id, // Link to konsumen for name and phone
            'status' => 'waiting'
        ];

        $this->db->insert('tbl_queue', $queue_data);
        $queue_id = $this->db->insert_id();

        // Complete transaction
        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE || !$queue_id) {
            log_message('error', "Queue creation failed | Branch: $branch_id");
            echo json_encode([
                'status' => 'error',
                'message' => 'Gagal membuat antrian. Silakan coba lagi.'
            ]);
        } else {
            echo json_encode([
                'status' => 'success',
                'queue_id' => $queue_id,
                'queue_number' => $queue_number,
                'konsumen_id' => $konsumen_id
            ]);
        }
    }

    /**
     * Print Queue Ticket
     */
    public function print_ticket($queue_id)
    {
        // Get queue with konsumen data
        $this->db->select('q.*, k.konsumen_nama, k.konsumen_nohp');
        $this->db->from('tbl_queue q');
        $this->db->join('tbl_konsumen k', 'q.konsumen_id = k.konsumen_id', 'left');
        $this->db->where('q.queue_id', $queue_id);
        $queue = $this->db->get()->row();

        if (!$queue) {
            show_404();
            return;
        }

        // Get existing draft orders for this customer
        $existing_orders = [];
        if ($queue->konsumen_id) {
            $this->db->select('pm.*, p.product_nama, h.detail_product, k.kategori_nama, s.satuan_nama');
            $this->db->from('tbl_pemesanan_mandiri pm');
            $this->db->join('tbl_product p', 'pm.product_id = p.product_id', 'left');
            $this->db->join('tbl_harga_product h', 'pm.harga_id = h.harga_id', 'left');
            $this->db->join('tbl_kategori k', 'p.kategori_id = k.kategori_id', 'left');
            $this->db->join('tbl_satuan s', 'p.satuan_id = s.satuan_id', 'left');
            $this->db->where('pm.konsumen_id', $queue->konsumen_id);
            $this->db->where_in('pm.status', ['draft', 'submitted']); // Draft + Submitted (awaiting validation)
            $this->db->order_by('pm.created_at', 'DESC');
            $existing_orders = $this->db->get()->result();
        }
        $branch = $this->db->get_where('tbl_perusahaan', ['id_perusahaan' => $queue->branch_id])->row();

        // SECURITY: Use queue_id instead of queue_number for QR code URL
        // This prevents cross-branch access and ensures uniqueness
        $base_url = base_url();
        $qr_url = $base_url . 'kiosk/mobile_order/' . $queue_id; // Changed from queue_number to queue_id

        $data['queue'] = $queue;
        $data['branch'] = $branch;
        $data['qr_url'] = $qr_url;

        $this->load->view('kiosk/print_ticket', $data);
    }

    /**
     * Mobile Order Page - Accessed via QR Code
     * SECURITY: Uses queue_id for uniqueness and cross-branch isolation
     */
    public function mobile_order($queue_id)
    {
        // Get queue with konsumen data using queue_id (more secure than queue_number)
        $this->db->select('q.*, k.konsumen_nama, k.konsumen_nohp');
        $this->db->from('tbl_queue q');
        $this->db->join('tbl_konsumen k', 'q.konsumen_id = k.konsumen_id', 'left');
        $this->db->where('q.queue_id', $queue_id); // Changed from queue_number to queue_id
        $queue = $this->db->get()->row();

        if (!$queue) {
            show_error('Nomor antrian tidak valid atau sudah kadaluarsa', 404);
            return;
        }

        // Get branch info
        $branch = $this->db->get_where('tbl_perusahaan', ['id_perusahaan' => $queue->branch_id])->row();
        $branch_name = $branch ? $branch->nama_perusahaan : 'TGM Print';

        // Get existing draft and submitted orders for this customer
        $existing_orders = [];
        $processed_spk = null;

        if ($queue->konsumen_id) {
            // Check if customer has processed orders (already handled by Deskprint)
            $this->db->select('spk_no, no_ref');
            $this->db->where('konsumen_id', $queue->konsumen_id);
            $this->db->where('status', 'processed');
            $this->db->order_by('processed_at', 'DESC');
            $this->db->limit(1);
            $processed = $this->db->get('tbl_pemesanan_mandiri')->row();

            if ($processed) {
                // Has processed orders (backward compatible: use spk_no if exists, else no_ref)
                $processed_spk = !empty($processed->spk_no) ? $processed->spk_no : $processed->no_ref;
            }

            // Get pending orders (draft + submitted)
            $this->db->select('pm.*, p.product_nama, h.detail_product, k.kategori_nama, s.satuan_nama');
            $this->db->from('tbl_pemesanan_mandiri pm');
            $this->db->join('tbl_product p', 'pm.product_id = p.product_id', 'left');
            $this->db->join('tbl_harga_product h', 'pm.harga_id = h.harga_id', 'left');
            $this->db->join('tbl_kategori k', 'p.kategori_id = k.kategori_id', 'left');
            $this->db->join('tbl_satuan s', 'p.satuan_id = s.satuan_id', 'left');
            $this->db->where('pm.konsumen_id', $queue->konsumen_id);
            $this->db->where_in('pm.status', ['draft', 'submitted']);
            $this->db->order_by('pm.created_at', 'DESC');
            $existing_orders = $this->db->get()->result();
        }

        $data['title'] = 'InstaPrint Mobile - ' . $queue->queue_number;
        $data['queue'] = $queue;
        $data['branch_id'] = $queue->branch_id;
        $data['branch_name'] = $branch_name;
        $data['existing_orders'] = $existing_orders;
        $data['products'] = $this->M_kiosk->get_products();

        // Check if should show thank you page
        if ($processed_spk && empty($existing_orders)) {
            // All orders processed, no pending orders
            $data['spk_no'] = $processed_spk;
            $data['tracking_url'] = site_url('cekpesanan/spk/' . urlencode(encrypt_spk($processed_spk)));
            $this->load->view('kiosk/thank_you', $data);
        } else {
            // Load mobile-optimized order form
            $this->load->view('kiosk/mobile_order', $data);
        }
    }

    /**
     * Display Board - Monitor untuk menampilkan antrian yang dipanggil
     */
    public function display_board()
    {
        // Default 0 to show all branches if not specified
        $branch_id = (int) $this->input->get('perusahaan_id', true);

        // Fetch company info. If branch_id is 0, fetch default company (ID 1) for general info
        $query_id = ($branch_id > 0) ? $branch_id : 1;
        $branch = $this->db->get_where('tbl_perusahaan', ['id_perusahaan' => $query_id])->row();

        $data['branch_id'] = $branch_id;
        $data['branch_name'] = $branch ? $branch->nama_perusahaan : 'TGM Print';
        $data['company'] = $branch;

        $this->load->view('kiosk/display_board', $data);
    }

    /**
     * Control Panel - Panel untuk kasir memanggil antrian
     * SECURITY: Branch from SESSION (user login), not URL
     */
    public function queue_control()
    {
        if (!$this->session->userdata('logged_in')) {
            redirect('auth');
        }

        // SECURITY: Get branch from logged-in user session
        $branch_id = (int) $this->session->userdata('perusahaan_id');
        if (!$branch_id) {
            show_error('Branch ID tidak ditemukan dalam session. Silakan login ulang.', 403);
            return;
        }

        $branch = $this->db->get_where('tbl_perusahaan', ['id_perusahaan' => $branch_id])->row();

        $data['branch_id'] = $branch_id;
        $data['branch_name'] = $branch ? $branch->nama_perusahaan : 'TGM Print';

        // Template load
        if (isset($this->template)) {
            $this->template->load('template', 'kiosk/queue_control', $data);
        } else {
            $this->load->view('kiosk/queue_control', $data);
        }
    }

    /**
     * AJAX: Get current queue status
     */
    public function get_queue_status()
    {
        // SECURITY: Get branch from session instead of request
        $branch_id = (int) $this->session->userdata('perusahaan_id');

        if (!$branch_id) {
            echo json_encode(['status' => 'error', 'msg' => 'Unauthorized - Branch ID not found in session']);
            return;
        }

        $today = date('Y-m-d');

        // Get current called queue
        $this->db->select('q.*, k.konsumen_nama');
        $this->db->from('tbl_queue q');
        $this->db->join('tbl_konsumen k', 'q.konsumen_id = k.konsumen_id', 'left');
        $this->db->where('q.branch_id', $branch_id);
        $this->db->where("DATE(q.created_at) = '$today'", NULL, FALSE);
        $this->db->where('q.status', 'called');
        $this->db->order_by('q.called_at', 'DESC');
        $current = $this->db->get()->row();

        // Get waiting queues
        $this->db->select('q.*, k.konsumen_nama');
        $this->db->from('tbl_queue q');
        $this->db->join('tbl_konsumen k', 'q.konsumen_id = k.konsumen_id', 'left');
        $this->db->where('q.branch_id', $branch_id);
        $this->db->where("DATE(q.created_at) = '$today'", NULL, FALSE);
        $this->db->where('q.status', 'waiting');
        $this->db->order_by('q.created_at', 'ASC');
        $waiting = $this->db->get()->result();

        // Get served count
        $this->db->where('branch_id', $branch_id);
        $this->db->where("DATE(created_at) = '$today'", NULL, FALSE);
        $this->db->where('status', 'served');
        $served_count = $this->db->count_all_results('tbl_queue');

        echo json_encode([
            'status' => 'success',
            'current' => $current,
            'waiting' => $waiting,
            'waiting_count' => count($waiting),
            'served_count' => $served_count
        ]);
    }
    /**
     * AJAX: Call Queue (Mark as Called)
     * SECURITY: Branch from SESSION
     */
    public function call_queue()
    {
        $queue_number = $this->input->post('queue_number', true);

        // SECURITY: Get branch from session instead of request
        $branch_id = (int) $this->session->userdata('perusahaan_id');

        if (!$queue_number || !$branch_id) {
            echo json_encode(['status' => 'error', 'msg' => 'Unauthorized or invalid queue number']);
            return;
        }

        // SECURITY: Get target queue with branch validation
        $this->db->where('queue_number', $queue_number);
        $this->db->where('branch_id', $branch_id); // SECURITY: Ensure same branch from session
        $target = $this->db->get('tbl_queue')->row();

        if (!$target) {
            echo json_encode(['status' => 'error', 'msg' => 'Nomor antrian tidak ditemukan atau bukan milik cabang Anda']);
            return;
        }

        // Mark CURRENT 'called' queue AS 'served' (Only for this branch and today)
        $this->db->where('branch_id', $branch_id);
        $this->db->where("DATE(created_at) = '" . date('Y-m-d') . "'", NULL, FALSE);
        $this->db->where('status', 'called');
        $this->db->where('queue_number !=', $queue_number);
        $this->db->update('tbl_queue', ['status' => 'served']);

        // Update Target Status to 'called'
        $operator_name = $this->session->userdata('nama_lengkap') ?: ($this->session->userdata('username') ?: 'Operator');
        $custom_loket = $this->input->post('loket', true);
        $loket_name = !empty($custom_loket) ? $custom_loket : "Deskprint - {$operator_name}";

        if ($target->status !== 'called') {
            $data_update = [
                'status' => 'called',
                'called_at' => date('Y-m-d H:i:s'),
                'loket_name' => $loket_name
            ];

            $this->db->where('queue_id', $target->queue_id);
            $this->db->where('branch_id', $branch_id); // SECURITY: Extra validation
            $this->db->update('tbl_queue', $data_update);
        } else {
            // Update loket name only
            $this->db->where('queue_id', $target->queue_id);
            $this->db->where('branch_id', $branch_id); // SECURITY: Extra validation
            $this->db->update('tbl_queue', ['loket_name' => $loket_name]);
        }

        echo json_encode([
            'status' => 'success',
            'queue_number' => $queue_number,
            'loket' => $loket_name
        ]);
    }

    /**
     * AJAX: Mark queue as served/completed
     */
    /**
     * AJAX: Complete Queue (Mark as Served)
     * SECURITY: Branch from SESSION
     */
    public function complete_queue()
    {
        $queue_number = $this->input->post('queue_number', true);

        // SECURITY: Get branch from session instead of request
        $branch_id = (int) $this->session->userdata('perusahaan_id');

        if (!$queue_number || !$branch_id) {
            echo json_encode(['status' => 'error', 'msg' => 'Unauthorized or invalid queue number']);
            return;
        }

        // SECURITY: Update with branch validation from session
        $this->db->where('queue_number', $queue_number);
        $this->db->where('branch_id', $branch_id); // SECURITY: Ensure same branch from session
        $this->db->update('tbl_queue', [
            'status' => 'served',
            'completed_at' => date('Y-m-d H:i:s')
        ]);

        echo json_encode(['status' => 'success']);
    }
}
