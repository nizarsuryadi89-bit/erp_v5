<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Dashboard extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        // $this->load->model('Page_visit_model'); // Deprecated
        $this->load->model('M_transaksi');
        $this->load->model('M_laporan');
        $this->load->model('Produksi_model');
        $this->load->model('M_gudang');
        $this->load->model('Absensi_model');
    }

    /**
     * Main Entry Point for Dashboard
     * Handles role-based redirection and data preparation
     */
    public function index()
    {
        $this->_refresh_session_cache();
        $userid = $this->session->userdata('id');
        $role_id = (int) $this->session->userdata('level'); // Level = Role ID
        $perusahaan_id = $this->session->userdata('perusahaan_id');
        $username = $this->session->userdata('username');
        $is_developer = ($role_id === 22 || $username === 'nizarsuryadi');


        // DEVELOPER PORTAL REDIRECTION
        $preview_role = (int) $this->input->get('preview_role', true);
        if ($is_developer && $preview_role === 0) {
            $this->developer_portal();
            return;
        }

        // Apply Preview Role for Developer
        if ($is_developer && $preview_role > 0) {
            $role_id = $preview_role;
        }

        // 1. Administrator & Finance Access (Command Center)
        if (in_array($role_id, [1, 6])) {
            $is_super = $this->data['is_super_admin'] ?? false;
            $pid_to_pass = $is_super ? null : $perusahaan_id;

            $admin_data = $this->_prepare_admin_dashboard_data($pid_to_pass);
            $finance_data = $this->_prepare_finance_extra_data($pid_to_pass);

            $data = array_merge($this->data, $admin_data, $finance_data);
            $data['title'] = ($role_id == 6 ? 'Finance Command Center' : 'System Control Center') . ($is_developer ? ' [PREVIEW]' : '');
            $data['is_preview'] = $is_developer;
            $this->load_admin_view('admin/dashboard/system_admin', $data);
            return;
        }

        // 1.5. No Access / Observer Access
        if ($role_id == 18) {
            redirect('wip/produksi?readonly=1');
            return;
        }

        // 2. Owner Access (Executive Insights)
        if ($role_id == 17 || $role_id == 8) {
            $this->ringkasan_owner($is_developer);
            return;
        }

        // 2. Kasir Access (Cashier Summary)
        // 2. Kasir Access (Cashier Summary)
        if ($role_id == 3) {
            $data = array_merge($this->data, $this->_prepare_staff_data($role_id, $perusahaan_id, $userid));
            $data['title'] = 'Cashier Desk' . ($is_developer ? ' [PREVIEW]' : '');
            $data['is_preview'] = $is_developer;
            $data['quick_access'] = []; // Feature removed
            $this->load_admin_view('admin/dashboard/cashier_summary', $data);
            return;
        }

        // 3. Deskprint Access
        if ($role_id == 2) {
            $data = array_merge($this->data, $this->_prepare_deskprint_data($perusahaan_id, $userid));
            $data['title'] = 'Front-End Intake' . ($is_developer ? ' [PREVIEW]' : '');
            $data['is_preview'] = $is_developer;
            $data['quick_access'] = []; // Feature removed
            $this->load_admin_view('admin/dashboard/deskprint_summary', $data);
            return;
        }

        // 4. Operator Produksi Access
        // 4. Operator Produksi & Finishing Access
        if ($role_id == 4 || $role_id == 20) {
            $data = array_merge($this->data, $this->_prepare_operator_data($perusahaan_id, $userid));
            $data['title'] = 'Production Machine' . ($is_developer ? ' [PREVIEW]' : '');
            $data['is_preview'] = $is_developer;
            $data['quick_access'] = []; // Feature removed
            $this->load_admin_view('admin/dashboard/operator_summary', $data);
            return;
        }

        // 5. Human Resource Access
        if ($role_id == 21) {
            $data = array_merge($this->data, $this->_prepare_hrd_data(null)); // Pass null to see all branches
            $data['title'] = 'HR Command Center' . ($is_developer ? ' [PREVIEW]' : '');
            $data['is_preview'] = $is_developer;
            $data['quick_access'] = []; // Feature removed
            $this->load_admin_view('admin/dashboard/hrd_summary', $data);
            return;
        }

        // 6. Pengambilan Barang (PB) Hub Access
        if ($role_id == 19) {
            $data = array_merge($this->data, $this->_prepare_pb_data($perusahaan_id));
            $data['title'] = 'PB Hub & Logistics' . ($is_developer ? ' [PREVIEW]' : '');
            $data['is_preview'] = $is_developer;
            $data['quick_access'] = []; // Feature removed
            $this->load_admin_view('admin/dashboard/pb_summary', $data);
            return;
        }

        // 7. Warehouse (Gudang) Access - Redirect to dedicated controller
        if ($role_id == 5) {
            redirect('warehouse');
        }

        // 8. E-Commerce Access - Redirect to dedicated controller
        if ($role_id == 9) {
            redirect('marketplace/dashboard');
        }

        // 8. Operational Roles (Staff View)
        // Preparation of common data but strictly filtered by perusahaan_id
        $data = array_merge($this->data, $this->_prepare_staff_data($role_id, $perusahaan_id, $userid));

        $data['title'] = 'Dashboard';
        $data['quick_access'] = []; // Feature removed

        // Use the administrative status view for operational staff
        $this->load_admin_view('admin/dashboard/administrator', $data);
    }

    /**
     * Executive Dashboard (Consolidated View)
     */
    public function ringkasan_owner($is_preview = false)
    {
        $role_id = (int) $this->session->userdata('level');
        if (!in_array($role_id, [1, 17, 6])) {
            redirect('dashboard');
        }

        $data['is_preview'] = $is_preview;

        $hariini = date('Y-m-d');
        $bulanini = date('Y-m');

        // Handle Branch Filtering
        $selected_branch = $this->input->get('branch_id', true);
        $current_session_branch = (int) $this->session->userdata('perusahaan_id');

        // Check if current session branch is a holding
        $is_holding = $this->db->get_where('tbl_perusahaan', ['id_perusahaan' => $current_session_branch, 'is_holding' => 1])->num_rows() > 0;

        if ($selected_branch === null) {
            // Default behavior: If holding, show consolidated (null). If specific branch, show that branch.
            $selected_branch = $is_holding ? null : $current_session_branch;
        } elseif ($selected_branch === 'all') {
            $selected_branch = null; // Forced Consolidated
        }

        $data['selected_branch'] = $selected_branch;
        $data['is_holding_mode'] = $is_holding;

        // 1. Financial Summary (Filtered or Consolidated)
        $data['pemasukan_hariini'] = $this->_get_consolidated_income($hariini, $selected_branch);
        $data['pemasukan_bulanini'] = $this->M_laporan->pemasukan_bulanan($selected_branch, $bulanini)->row()->total_pemasukan_bulanan ?? 0;

        // Growth (Today vs Yesterday)
        $kemarin = date('Y-m-d', strtotime('-1 day'));
        $data['pemasukan_kemarin'] = $this->_get_consolidated_income($kemarin, $selected_branch);

        // 2. Liquidity & Receivables
        $q_piutang = "SELECT SUM(total_tagihan - total_bayar) as sisa FROM tbl_pembayaran WHERE status_lunas = '0'";
        if ($selected_branch)
            $q_piutang .= " AND perusahaan_id = $selected_branch";
        $data['total_piutang'] = $this->db->query($q_piutang)->row()->sisa ?? 0;

        // 3. Operational Pulse
        $data['stats_produksi'] = $this->Produksi_model->get_summary(null, $selected_branch); // Pass branch to Produksi_model if supported
        $pb_q = $this->db->where('status_produksi', 'on_pb');
        if ($selected_branch)
            $pb_q->where('perusahaan_id', $selected_branch);
        $data['antrian_pb'] = $pb_q->count_all_results('tbl_produksi');

        // 4. Analytics: Monthly Sales Trend (Jan - Dec Current Year) per Branch
        $y = date('Y');
        $indo_months = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agt', 'Sep', 'Okt', 'Nov', 'Des'];

        // Get all branches
        $all_branches = $this->db->get('tbl_perusahaan')->result();

        $chart_data_branches = [];
        foreach ($all_branches as $branch) {
            $branch_sales = [];
            for ($i = 1; $i <= 12; $i++) {
                $m = str_pad($i, 2, '0', STR_PAD_LEFT);
                $label = $indo_months[$i - 1];
                $branch_sales[] = $this->_get_consolidated_monthly_sales($m, $y, $branch->id_perusahaan);
            }
            $chart_data_branches[] = [
                'label' => $branch->nama_perusahaan,
                'data' => $branch_sales
            ];
        }
        $data['chart_labels'] = $indo_months;
        $data['chart_data_branches'] = $chart_data_branches;


        // 5. Analytics: Branch Performance
        $data['branch_performance'] = $this->db->query("
            SELECT p.nama_perusahaan, p.id_perusahaan, COUNT(tr.pembayaran_id) as total_trx, SUM(pd.jumlah) as revenue
            FROM tbl_perusahaan p
            LEFT JOIN tbl_pembayaran tr ON p.id_perusahaan = tr.perusahaan_id AND MONTH(tr.tgl_bayar) = MONTH(CURRENT_DATE())
            LEFT JOIN tbl_pembayaran_detail pd ON tr.pembayaran_id = pd.pembayaran_id
            GROUP BY p.id_perusahaan
            ORDER BY revenue DESC
        ")->result();

        // 6. Executive Insights: Profitability & Aging
        $laba_res = $this->M_laporan->getLabaRugi($selected_branch, $bulanini)->row();
        $data['laba_estimasi'] = $laba_res->laba ?? 0;
        $data['aging_piutang'] = $this->M_laporan->get_piutang_aging($selected_branch);

        $this->load->model('M_hutang');
        $data['summary_hutang'] = $this->M_hutang->getHutangSummary(); // Note: Add branch filter to M_hutang if needed

        // 7. Payment Distribution (Monthly for Owner)
        $data['metode_today'] = $this->_get_payment_methods_breakdown($bulanini, $selected_branch, true);

        // 8. Inventory Alerts: Low Stock Materials
        if ($selected_branch) {
            $this->db->select('m.varian, SUM(sg.qty) as total_qty, m.stok_min, p.nama_perusahaan, m.konversi, s.satuan_nama');
            $this->db->from('tbl_material m');
            $this->db->join('tbl_stok_gudang sg', 'm.material_id = sg.material_id', 'left');
            $this->db->join('tbl_perusahaan p', 'sg.gudang_id = p.id_perusahaan', 'left');
            $this->db->join('tbl_satuan s', 'm.satuan_gudang_id = s.satuan_id', 'left');
            $this->db->where('sg.gudang_id', $selected_branch);
            $this->db->group_by('m.material_id');
        } else {
            $this->db->select('m.varian, SUM(sg.qty) as total_qty, m.stok_min, "Akumulasi Semua Gudang" as nama_perusahaan, m.konversi, s.satuan_nama');
            $this->db->from('tbl_material m');
            $this->db->join('tbl_stok_gudang sg', 'm.material_id = sg.material_id', 'left');
            $this->db->join('tbl_satuan s', 'm.satuan_gudang_id = s.satuan_id', 'left');
            $this->db->group_by('m.material_id');
        }
        $this->db->having('(SUM(sg.qty) / IFNULL(NULLIF(m.konversi, 0), 1)) <= m.stok_min');
        $this->db->order_by('total_qty', 'ASC');
        $this->db->limit(5);
        $data['critical_materials'] = $this->db->get()->result();

        // 8. Human Capital Analytics (Discipline)
        $bulan_ini_nu = (int) date('m');
        $tahun_ini_nu = (int) date('Y');

        // [BI ANALYSIS] Calculate Attendance Rate (%)
        $total_karyawan = $this->db->count_all('tbl_karyawan');
        $total_hadir_today = $this->db->where(['tanggal' => $hariini, 'status' => 'Hadir'])->count_all_results('absensi');
        $data['attendance_rate'] = ($total_karyawan > 0) ? round(($total_hadir_today / $total_karyawan) * 100, 1) : 0;

        // Top 5 Diligent (Most hadir, least late)
        $q_rajin = "
            SELECT k.nama_lengkap, k.foto, COUNT(a.id) as total_hadir, 0 as total_telat
            FROM tbl_karyawan k
            JOIN absensi a ON k.karyawan_id = a.karyawan_id
            WHERE MONTH(a.tanggal) = $bulan_ini_nu AND YEAR(a.tanggal) = $tahun_ini_nu AND a.status = 'Hadir'
        ";
        if ($selected_branch)
            $q_rajin .= " AND k.perusahaan_id = $selected_branch";
        $q_rajin .= " GROUP BY k.karyawan_id ORDER BY total_hadir DESC, total_telat ASC LIMIT 5";
        $data['karyawan_rajin'] = $this->db->query($q_rajin)->result();

        // Top 5 Lazy (Most telat or alpha)
        $q_malas = "
            SELECT k.nama_lengkap, k.foto, 
                   (SELECT COUNT(id) FROM absensi WHERE karyawan_id = k.karyawan_id AND status = 'Alpha' AND MONTH(tanggal) = $bulan_ini_nu) as total_alpha,
                   0 as total_telat
            FROM tbl_karyawan k
            LEFT JOIN absensi a ON k.karyawan_id = a.karyawan_id AND MONTH(a.tanggal) = $bulan_ini_nu AND YEAR(a.tanggal) = $tahun_ini_nu
            WHERE 1=1
        ";
        if ($selected_branch)
            $q_malas .= " AND k.perusahaan_id = $selected_branch";
        $q_malas .= " GROUP BY k.karyawan_id ORDER BY total_alpha DESC, total_telat DESC LIMIT 5";
        $data['karyawan_malas'] = $this->db->query($q_malas)->result();

        // 9. Customer Insights (VIP Analytics)
        // Highest Spending Customers
        $q_vip_value = "
            SELECT k.konsumen_nama, SUM(p.total_bayar) as total_spent, COUNT(p.pembayaran_id) as count_trx
            FROM tbl_konsumen k
            JOIN tbl_pembayaran p ON k.konsumen_id = p.konsumen_id
            WHERE 1=1
        ";
        if ($selected_branch)
            $q_vip_value .= " AND p.perusahaan_id = $selected_branch";
        $q_vip_value .= " GROUP BY k.konsumen_id ORDER BY total_spent DESC LIMIT 5";
        $data['top_customers_value'] = $this->db->query($q_vip_value)->result();

        // Most Frequent Customers
        $q_vip_freq = "
            SELECT k.konsumen_nama, COUNT(p.pembayaran_id) as count_trx, SUM(p.total_bayar) as total_spent
            FROM tbl_konsumen k
            JOIN tbl_pembayaran p ON k.konsumen_id = p.konsumen_id
            WHERE 1=1
        ";
        if ($selected_branch)
            $q_vip_freq .= " AND p.perusahaan_id = $selected_branch";
        $q_vip_freq .= " GROUP BY k.konsumen_id ORDER BY count_trx DESC LIMIT 5";
        $data['top_customers_freq'] = $this->db->query($q_vip_freq)->result();

        // 9. Daily Material Consumption (Production Usage)
        $data['daily_consumption'] = $this->M_gudang->get_daily_consumption($hariini, $selected_branch);

        // 9b. Live Material Consumption (Real-time logs)
        $this->db->select('sl.*, m.varian as material_nama, m.konversi, b.bahan_nama, s.satuan_nama as satuan_gudang, s_prod.satuan_nama as satuan_produksi, p.nospk, pr.product_nama, k.nama_lengkap as operator_nama');
        $this->db->from('tbl_stok_log sl');
        $this->db->join('tbl_material m', 'm.material_id = sl.material_id', 'left');
        $this->db->join('tbl_bahan b', 'b.bahan_id = m.bahan_id', 'left');
        $this->db->join('tbl_satuan s', 's.satuan_id = m.satuan_gudang_id', 'left');
        $this->db->join('tbl_satuan s_prod', 's_prod.satuan_id = b.satuan_id', 'left');
        $this->db->join('tbl_produksi p', 'p.produksi_id = sl.trx_id', 'left');
        $this->db->join('tbl_product pr', 'pr.product_id = p.product_id', 'left');
        $this->db->join('users u', 'u.user_id = sl.user_id', 'left');
        $this->db->join('tbl_karyawan k', 'k.karyawan_id = u.karyawan_id', 'left');
        $this->db->where('sl.trx_type', 'Produksi');
        if ($selected_branch) {
            $this->db->where('p.perusahaan_id', $selected_branch);
        }
        $this->db->order_by('sl.created_at', 'DESC');
        $this->db->limit(10);
        $data['live_consumption'] = $this->db->get()->result();

        // 9c. System Updates (Roadmap)
        $this->db->select('*');
        $this->db->from('tbl_system_roadmap');
        $this->db->where('status', 'Live');
        $this->db->order_by('id', 'DESC');
        $this->db->limit(3);
        $data['roadmap_updates'] = $this->db->get()->result();

        // [BI ANALYSIS] 10. Category Performance Analysis (Profit Centers)
        $q_cat = "
            SELECT k.kategori_nama as label, SUM(od.qty * hp.harga_1) as value
            FROM tbl_pemesanan_detail od
            JOIN tbl_pemesanan o ON od.nospk = o.nospk
            JOIN tbl_product pr ON od.product_id = pr.product_id
            JOIN tbl_kategori k ON pr.kategori_id = k.kategori_id
            JOIN tbl_harga_product hp ON od.harga_id = hp.harga_id
            WHERE MONTH(o.created_at) = MONTH(CURRENT_DATE())
        ";
        if ($selected_branch)
            $q_cat .= " AND o.perusahaan_id = $selected_branch";
        $q_cat .= " GROUP BY k.kategori_id ORDER BY value DESC LIMIT 5";
        $data['category_contribution'] = $this->db->query($q_cat)->result();

        // [BI ANALYSIS] 11. Sales Target vs Reality
        $data['sales_target'] = 1000000000; // Default 1M target, ideally from a settings table
        $data['sales_actual'] = $data['pemasukan_bulanini'];
        $data['sales_percentage'] = min(100, round(($data['sales_actual'] / $data['sales_target']) * 100, 1));

        // [BI ANALYSIS] 12. Real-time Daily Income Report (Pemasukan Harian)
        $q_list = "
            SELECT p.tgl_bayar as tanggal, p.jam_bayar as jam, p.nospk, 
                   k.konsumen_nama as konsumen, p.total_tagihan, p.total_bayar as bayar_hari_ini,
                   u.username as kasir, p.status_lunas, tp.nama_perusahaan as cabang
            FROM tbl_pembayaran p
            LEFT JOIN tbl_konsumen k ON p.konsumen_id = k.konsumen_id
            LEFT JOIN users u ON p.kasir_id = u.user_id
            LEFT JOIN tbl_perusahaan tp ON p.perusahaan_id = tp.id_perusahaan
            WHERE p.tgl_bayar = ?
        ";
        if ($selected_branch)
            $q_list .= " AND p.perusahaan_id = $selected_branch";
        $q_list .= " ORDER BY p.jam_bayar DESC LIMIT 10";
        $data['pemasukan_hariini_list'] = $this->db->query($q_list, [$hariini])->result();

        // Daily Income by Method (Today)
        $data['metode_hariini'] = $this->_get_payment_methods_breakdown($hariini, $selected_branch, false);

        // General Stats / Asset
        $data['total_aset'] = 0;
        $data['total_nilai_aset'] = 0;
        if ($this->db->table_exists('tbl_aset')) {
            if ($selected_branch) {
                $this->db->where('perusahaan_id', $selected_branch);
            }
            $data['total_aset'] = $this->db->count_all_results('tbl_aset');

            if ($selected_branch) {
                $this->db->where('perusahaan_id', $selected_branch);
            }
            $data['total_nilai_aset'] = $this->db->select('SUM(aset_harga) as total')->get('tbl_aset')->row()->total ?? 0;
        }

        // List of Branches for Dropdown
        $data['branches'] = $this->db->get('tbl_perusahaan')->result();

        $data['title'] = 'Analisa Eksekutif';
        $final_data = array_merge($this->data, $data);
        $this->load_admin_view('admin/dashboard/owner_summary', $final_data);
    }

    /**
     * Financial Dashboard (Control & Reporting)
     */
    public function ringkasan_keuangan($is_preview = false)
    {
        $role_id = (int) $this->session->userdata('level');
        if (!in_array($role_id, [1, 17, 6])) {
            redirect('dashboard');
        }

        $data['is_preview'] = $is_preview;

        $perusahaan_id = $this->session->userdata('perusahaan_id');
        $hariini = date('Y-m-d');
        $bulanini = date('Y-m');

        // Handle Branch Filtering
        $selected_branch = $this->input->get('branch_id', true);
        if ($selected_branch === 'all')
            $selected_branch = null;
        $data['selected_branch'] = $selected_branch;

        // 1. Core Financial Metrics (Income vs Expense)
        $data['pemasukan_hariini'] = $this->_get_consolidated_income($hariini, $selected_branch);
        $data['pemasukan_bulanini'] = $this->M_laporan->pemasukan_bulanan($selected_branch, $bulanini)->row()->total_pemasukan_bulanan ?? 0;

        $data['pengeluaran_hariini'] = $this->M_laporan->pengeluaran($selected_branch)->row()->pengeluaran ?? 0;
        $data['pengeluaran_bulanini'] = $this->M_laporan->pengeluaran_bulanan($selected_branch, $bulanini)->row()->pengeluaran_bulanan ?? 0;

        // 2. Receivables & Payables (Liquidity Status)
        $q_piutang = "SELECT SUM(total_tagihan - total_bayar) as sisa FROM tbl_pembayaran WHERE status_lunas = '0'";
        if ($selected_branch)
            $q_piutang .= " AND perusahaan_id = $selected_branch";
        $data['total_piutang'] = $this->db->query($q_piutang)->row()->sisa ?? 0;

        $data['total_hutang'] = $this->db->select('SUM(sisa_hutang) as total')->from('tbl_hutang')->get()->row()->total ?? 0;

        // [PROMETHEUS] Added Profitability Metrics
        $data['profit_bulanini'] = $data['pemasukan_bulanini'] - $data['pengeluaran_bulanini'];
        $data['profit_rate'] = ($data['pemasukan_bulanini'] > 0) ? round(($data['profit_bulanini'] / $data['pemasukan_bulanini']) * 100, 1) : 0;

        // Count Pending Audit (Unpaid transactions older than 3 days)
        $data['pending_audit'] = $this->db->query("SELECT COUNT(*) as total FROM tbl_pembayaran WHERE status_lunas = '0' AND tgl_bayar < DATE_SUB(CURRENT_DATE, INTERVAL 3 DAY)")->row()->total;

        // 3. Payment Method Breakdown (Today)
        $data['metode_today'] = $this->_get_payment_methods_breakdown($hariini, $selected_branch);

        // 4. Latest Transactions (Financial Audit Trail)
        $this->db->select('p.*, k.konsumen_nama, u.username as kasir');
        $this->db->from('tbl_pembayaran p');
        $this->db->join('tbl_konsumen k', 'p.konsumen_id = k.konsumen_id', 'left');
        $this->db->join('users u', 'p.kasir_id = u.user_id', 'left');
        if ($selected_branch)
            $this->db->where('p.perusahaan_id', $selected_branch);
        $this->db->order_by('p.tgl_bayar', 'DESC');
        $this->db->order_by('p.jam_bayar', 'DESC');
        $this->db->limit(10);
        $data['recent_payments'] = $this->db->get()->result();

        // 5. Latest Expenses
        $this->db->select('e.*, c.kategori_pengeluaran');
        $this->db->from('tbl_pengeluaran e');
        $this->db->join('tbl_kategori_pengeluaran c', 'e.kategori_pengeluaran_id = c.id', 'left');
        if ($selected_branch)
            $this->db->where('e.perusahaan_id', $selected_branch);
        $this->db->order_by('e.pengeluaran_tgl', 'DESC');
        $this->db->limit(10);
        $data['recent_expenses'] = $this->db->get()->result();

        // 6. Cash Flow Trend (Last 12 Months)
        $months = [];
        for ($i = 11; $i >= 0; $i--) {
            $m = date('m', strtotime("-$i months"));
            $y = date('Y', strtotime("-$i months"));
            $label = date('M', strtotime("-$i months"));
            $income = $this->_get_consolidated_monthly_sales($m, $y, $selected_branch);

            // Get expense for that month
            $this->db->select('SUM(pengeluaran_jumlah) as total');
            $this->db->from('tbl_pengeluaran');
            if ($selected_branch)
                $this->db->where('perusahaan_id', $selected_branch);
            $this->db->where('MONTH(pengeluaran_tgl)', (int) $m);
            $this->db->where('YEAR(pengeluaran_tgl)', (int) $y);
            $expense = $this->db->get()->row()->total ?? 0;

            $months[$label] = [
                'income' => $income,
                'expense' => $expense
            ];
        }
        $data['chart_data'] = $months;

        // 7. General Stats
        if ($selected_branch) {
            $this->db->where('perusahaan_id', $selected_branch);
        }
        $data['total_rows'] = $this->db->count_all_results('tbl_pemesanan');
        $data['total_bahan'] = $this->db->count_all('tbl_material');
        $data['total_product'] = $this->db->count_all('tbl_product');
        $data['total_konsumen'] = $this->db->count_all('tbl_konsumen');

        $data['branches'] = $this->db->get('tbl_perusahaan')->result();
        $data['title'] = 'Kontrol Keuangan & Pelaporan';

        $final_data = array_merge($this->data, $data);
        $this->load_admin_view('admin/dashboard/keuangan', $final_data);
    }

    /**
     * Extra preparation for Finance dashboard
     */
    private function _prepare_finance_extra_data($pid = null)
    {
        $hariini = date('Y-m-d');
        $bulanini = date('Y-m');
        $range = $this->input->get('perf_range', true) ?? '7days';
        $data['perf_range'] = $range;

        // 1. Existing Top Metrics
        $data['income_today'] = $this->db->query("SELECT SUM(total_bayar) as total FROM tbl_pembayaran WHERE DATE(tgl_bayar) = ?" . ($pid ? " AND perusahaan_id = '$pid'" : ""), [$hariini])->row()->total ?? 0;
        $data['omzet_bulan'] = $this->db->query("SELECT SUM(total_bayar) as total FROM tbl_pembayaran WHERE DATE_FORMAT(tgl_bayar, '%Y-%m') = ?" . ($pid ? " AND perusahaan_id = '$pid'" : ""), [$bulanini])->row()->total ?? 0;
        $data['piutang_total'] = $this->db->query("SELECT SUM(total_tagihan - total_bayar) as sisa FROM tbl_pembayaran WHERE status_lunas = '0'" . ($pid ? " AND perusahaan_id = '$pid'" : ""))->row()->sisa ?? 0;
        $data['expense_bulan'] = $this->db->query("SELECT SUM(pengeluaran_jumlah) as total FROM tbl_pengeluaran WHERE DATE_FORMAT(pengeluaran_tgl, '%Y-%m') = ?" . ($pid ? " AND perusahaan_id = '$pid'" : ""), [$bulanini])->row()->total ?? 0;

        // 2. Performance Metrics Logic based on Range
        if ($range === 'today') {
            $curr_start = $hariini;
            $curr_end = $hariini;
            $prev_start = date('Y-m-d', strtotime('-1 day'));
            $prev_end = date('Y-m-d', strtotime('-1 day'));
        } elseif ($range === '30days') {
            $curr_start = date('Y-m-d', strtotime('-29 days'));
            $curr_end = $hariini;
            $prev_start = date('Y-m-d', strtotime('-59 days'));
            $prev_end = date('Y-m-d', strtotime('-30 days'));
        } else { // default 7days
            $curr_start = date('Y-m-d', strtotime('-6 days'));
            $curr_end = $hariini;
            $prev_start = date('Y-m-d', strtotime('-13 days'));
            $prev_end = date('Y-m-d', strtotime('-7 days'));
        }

        // Helper function for growth calculation
        $calc_growth = function ($current, $prev) {
            if ($prev == 0)
                return ($current > 0) ? 100 : 0;
            return round((($current - $prev) / $prev) * 100, 2);
        };

        // --- GMV Performance ---
        $gmv_curr = $this->db->query("SELECT SUM(total_bayar) as total FROM tbl_pembayaran WHERE tgl_bayar BETWEEN ? AND ?" . ($pid ? " AND perusahaan_id = '$pid'" : ""), [$curr_start, $curr_end])->row()->total ?? 0;
        $gmv_prev = $this->db->query("SELECT SUM(total_bayar) as total FROM tbl_pembayaran WHERE tgl_bayar BETWEEN ? AND ?" . ($pid ? " AND perusahaan_id = '$pid'" : ""), [$prev_start, $prev_end])->row()->total ?? 0;
        $data['perf_gmv'] = ['value' => $gmv_curr, 'growth' => $calc_growth($gmv_curr, $gmv_prev)];

        // --- Customer Performance (Unique Buyers) ---
        $cust_curr = $this->db->query("SELECT COUNT(DISTINCT konsumen_id) as total FROM tbl_pembayaran WHERE tgl_bayar BETWEEN ? AND ?" . ($pid ? " AND perusahaan_id = '$pid'" : ""), [$curr_start, $curr_end])->row()->total ?? 0;
        $cust_prev = $this->db->query("SELECT COUNT(DISTINCT konsumen_id) as total FROM tbl_pembayaran WHERE tgl_bayar BETWEEN ? AND ?" . ($pid ? " AND perusahaan_id = '$pid'" : ""), [$prev_start, $prev_end])->row()->total ?? 0;
        $data['perf_cust'] = ['value' => $cust_curr, 'growth' => $calc_growth($cust_curr, $cust_prev)];

        // --- Order/SKU Performance (Total Production Items) ---
        $sku_curr = $this->db->query("SELECT COUNT(p.produksi_id) as total FROM tbl_produksi p JOIN tbl_pemesanan o ON p.nospk = o.nospk WHERE o.created_at BETWEEN ? AND ?" . ($pid ? " AND (p.lokasi_cetak = '$pid' OR (p.lokasi_cetak = '' AND p.perusahaan_id = '$pid'))" : ""), [$curr_start . ' 00:00:00', $curr_end . ' 23:59:59'])->row()->total ?? 0;
        $sku_prev = $this->db->query("SELECT COUNT(p.produksi_id) as total FROM tbl_produksi p JOIN tbl_pemesanan o ON p.nospk = o.nospk WHERE o.created_at BETWEEN ? AND ?" . ($pid ? " AND (p.lokasi_cetak = '$pid' OR (p.lokasi_cetak = '' AND p.perusahaan_id = '$pid'))" : ""), [$prev_start . ' 00:00:00', $prev_end . ' 23:59:59'])->row()->total ?? 0;
        $data['perf_sku'] = ['value' => $sku_curr, 'growth' => $calc_growth($sku_curr, $sku_prev)];

        // --- Visitors (Active User Logs as Proxy) ---
        $visit_curr = $this->db->query("SELECT COUNT(DISTINCT user_id) as total FROM user_log WHERE login_time BETWEEN ? AND ?", [$curr_start . ' 00:00:00', $curr_end . ' 23:59:59'])->row()->total ?? 0;
        $visit_prev = $this->db->query("SELECT COUNT(DISTINCT user_id) as total FROM user_log WHERE login_time BETWEEN ? AND ?", [$prev_start . ' 00:00:00', $prev_end . ' 23:59:59'])->row()->total ?? 0;
        $data['perf_visit'] = ['value' => $visit_curr * 15, 'growth' => $calc_growth($visit_curr, $visit_prev)];

        return $data;
    }

    /**
     * Developer Control Center (God Mode Portal)
     */
    public function developer_portal()
    {
        $role_id = (int) $this->session->userdata('level');
        if ($role_id !== 22 && $this->session->userdata('username') !== 'nizarsuryadi') {
            redirect('dashboard');
        }

        $data['roles'] = $this->db->get('roles')->result();
        $data['branches'] = $this->db->get('tbl_perusahaan')->result();
        $data['title'] = 'Developer Control Portal';

        // 1. SYSTEM HEALTH & RUNTIME SNAPSHOT
        $data['server_stats'] = [
            'php_version' => phpversion(),
            'memory_limit' => ini_get('memory_limit'),
            'max_execution_time' => ini_get('max_execution_time') . 's',
            'upload_max_filesize' => ini_get('upload_max_filesize'),
            'post_max_size' => ini_get('post_max_size'),
            'server_ip' => $_SERVER['SERVER_ADDR'] ?? '127.0.0.1'
        ];

        // 2. DATABASE HEALTH (Heavy Tables)
        $data['db_stats'] = $this->db->query("
            SELECT table_name, 
                   ROUND(((data_length + index_length) / 1024 / 1024), 2) AS size_mb, 
                   table_rows 
            FROM information_schema.TABLES 
            WHERE table_schema = DATABASE() 
            ORDER BY (data_length + index_length) DESC 
            LIMIT 5
        ")->result();

        // Total DB Size
        // Total DB Size
        $data['db_size'] = $this->db->query("SELECT ROUND(SUM(data_length + index_length) / 1024 / 1024, 2) AS size FROM information_schema.TABLES WHERE table_schema = DATABASE()")->row()->size;

        // 3. OPERATIONAL PULSE
        // WA Gateway Status
        $wa = $this->db->get_where('tbl_wa_setting', ['id' => 1])->row();
        $data['wa_status'] = ($wa && $wa->status == '1' && !empty($wa->wa_token)) ? 'ONLINE' : 'OFFLINE';

        // Critical Errors Today
        $log_file = APPPATH . 'logs/log-' . date('Y-m-d') . '.php';
        $data['error_count'] = 0;
        if (file_exists($log_file)) {
            $content = file_get_contents($log_file);
            $data['error_count'] = substr_count($content, 'ERROR') + substr_count($content, 'Exception');
        }

        // Active Users (Online within last 30 mins)
        $data['active_users_now'] = $this->db->where('login_time >=', date('Y-m-d H:i:s', strtotime('-30 minutes')))->count_all_results('user_log');

        $data['total_users'] = $this->db->count_all('users');
        $data['total_roles'] = count($data['roles']);

        // ROADMAP LIVE FEATURES
        $data['roadmap_live'] = $this->db->where('status', 'Live')
            ->order_by('created_at', 'DESC')
            ->limit(5)
            ->get('tbl_system_roadmap')
            ->result();

        // 4. SECURITY AUDIT DATA (Matrix)
        $data['all_menus'] = $this->db->order_by('urutan', 'ASC')->get('menu')->result();

        $col_menu = $this->db->field_exists('menu_id', 'tbl_menu_access') ? 'menu_id' : 'id_menu';

        // Build Menu Access Map: role_id => [menu_id => true]
        $access_map = [];
        $raw_access = $this->db->get('tbl_menu_access')->result();
        foreach ($raw_access as $ra) {
            $m_id = $ra->menu_id ?? ($ra->id_menu ?? 0);
            $access_map[$ra->role_id][$m_id] = true;
        }
        $data['access_map'] = $access_map;

        // Build Read-Only Map: role_id => [controller_name => true]
        $readonly_map = [];
        $raw_access = $this->db->select("role_id, $col_menu as menu_id, is_readonly")
            ->from('tbl_menu_access')
            ->where('is_readonly', 1)
            ->get()->result();

        // Need to match controller name from menu link
        foreach ($data['all_menus'] as $m) {
            $m_id = $m->id ?? ($m->id_menu ?? 0);
            $link_parts = explode('/', $m->link);
            $ctrl_name = strtolower($link_parts[0]);
            if ($ctrl_name == '..')
                $ctrl_name = strtolower($link_parts[1] ?? '');

            foreach ($raw_access as $ra) {
                if ($ra->menu_id == $m_id) {
                    $readonly_map[$ra->role_id][$ctrl_name] = true;
                }
            }
        }
        $data['readonly_map'] = $readonly_map;

        // 5. SUBMENU DATA
        $data['all_submenus'] = $this->db->order_by('urutan', 'ASC')->get('submenu')->result();

        // Group submenus by menu_id
        $submenus_by_parent = [];
        foreach ($data['all_submenus'] as $sub) {
            $submenus_by_parent[$sub->id_menu][] = $sub;
        }
        $data['submenus_by_parent'] = $submenus_by_parent;

        // Build Submenu Access Map: role_id => [submenu_id => true]
        $submenu_access_map = [];
        $raw_sub_access = $this->db->get('tbl_submenu_access')->result();
        foreach ($raw_sub_access as $ra) {
            $submenu_access_map[$ra->role_id][$ra->submenu_id] = true;
        }
        $data['submenu_access_map'] = $submenu_access_map;

        // 6. LATEST AUDIT LOGS
        $data['recent_logs'] = $this->db->order_by('created_at', 'DESC')->limit(10)->get('tbl_audit_log')->result();

        $this->load_dev_view('admin/dashboard/developer_portal', $data);
    }

    /**
     * Mini Database Browser (Restricted)
     */
    public function view_table_data()
    {
        $role_id = (int) $this->session->userdata('level');
        if ($role_id !== 22 && $this->session->userdata('username') !== 'nizarsuryadi') {
            redirect('dashboard');
        }

        $table = $this->input->get('table', true);
        $offset = (int) $this->input->get('offset', true);
        $limit = 50;

        if (!$table || !$this->db->table_exists($table)) {
            show_error('Table not found or invalid.', 404);
        }

        // Get Schema
        $data['columns'] = $this->db->list_fields($table);
        $data['total_rows'] = $this->db->count_all($table);

        // Get Data
        $data['rows'] = $this->db->limit($limit, $offset)->get($table)->result_array();

        $data['table_name'] = $table;
        $data['offset'] = $offset;
        $data['limit'] = $limit;
        $data['title'] = 'DB Browser: ' . $table;

        $this->load_admin_view('admin/dashboard/database_browser', $data);
    }

    /**
     * AJAX: Terminate a user session (Admin Action)
     */
    public function ajax_terminate_session()
    {
        $role_id = (int) $this->session->userdata('level');
        if (!in_array($role_id, [1, 6, 22])) {
            echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
            return;
        }

        $log_id = $this->input->post('log_id', true);
        if ($log_id) {
            $this->db->update('user_log', [
                'logout_time' => date('Y-m-d H:i:s'),
                // 'status' => 'Terminated by Admin' // Optional if column exists
            ], ['id' => $log_id]);

            echo json_encode(['status' => 'ok']);
        } else {
            echo json_encode(['status' => 'error']);
        }
    }

    /**
     * AJAX: Toggle Menu Visibility for a role
     */
    public function ajax_toggle_visibility()
    {
        // Pengecekan Otorisasi: Hanya Developer (Level 22) atau nizarsuryadi yang diizinkan
        $session_role = (int) $this->session->userdata('level');
        if ($session_role !== 22 && $this->session->userdata('username') !== 'nizarsuryadi') {
            echo json_encode(['status' => 'error', 'message' => 'Unauthorized access']);
            return;
        }

        $role_id = (int) $this->input->post('role_id', true);
        $menu_id = (int) $this->input->post('menu_id', true);

        // Deteksi kolom secara dinamis
        $col_role = $this->db->field_exists('role_id', 'tbl_menu_access') ? 'role_id' : 'level_id';
        $col_menu = $this->db->field_exists('menu_id', 'tbl_menu_access') ? 'menu_id' : 'id_menu';

        $exists = $this->db->get_where('tbl_menu_access', [$col_role => $role_id, $col_menu => $menu_id])->row();

        if ($exists) {
            $this->db->delete('tbl_menu_access', ['id' => $exists->id]);
            echo json_encode(['status' => 'removed']);
        } else {
            $this->db->insert('tbl_menu_access', [$col_role => $role_id, $col_menu => $menu_id, 'is_readonly' => 0]);
            echo json_encode(['status' => 'added']);
        }
    }

    /**
     * AJAX: Toggle Read-Only (Edit Lock) for a role/menu
     */
    public function ajax_toggle_readonly()
    {
        // Pengecekan Otorisasi
        $session_role = (int) $this->session->userdata('level');
        if ($session_role !== 22 && $this->session->userdata('username') !== 'nizarsuryadi') {
            echo json_encode(['status' => 'error', 'message' => 'Unauthorized access']);
            return;
        }

        $role_id = (int) $this->input->post('role_id', true);
        $menu_id = (int) $this->input->post('menu_id', true);

        // Deteksi kolom secara dinamis
        $col_role = $this->db->field_exists('role_id', 'tbl_menu_access') ? 'role_id' : 'level_id';
        $col_menu = $this->db->field_exists('menu_id', 'tbl_menu_access') ? 'menu_id' : 'id_menu';

        $exists = $this->db->get_where('tbl_menu_access', [$col_role => $role_id, $col_menu => $menu_id])->row();

        if ($exists) {
            $new_status = ($exists->is_readonly == 1) ? 0 : 1;
            $this->db->update('tbl_menu_access', ['is_readonly' => $new_status], ['id' => $exists->id]);
            echo json_encode(['status' => $new_status == 1 ? 'locked' : 'unlocked']);
        } else {
            // If doesn't exist, we must add it first as readonly
            $this->db->insert('tbl_menu_access', [$col_role => $role_id, $col_menu => $menu_id, 'is_readonly' => 1]);
            echo json_encode(['status' => 'locked']);
        }
    }

    /**
     * AJAX: Toggle Submenu Access for a role
     */
    public function ajax_toggle_submenu_access()
    {
        // Pengecekan Otorisasi
        $session_role = (int) $this->session->userdata('level');
        if ($session_role !== 22 && $this->session->userdata('username') !== 'nizarsuryadi') {
            echo json_encode(['status' => 'error', 'message' => 'Unauthorized access']);
            return;
        }

        $role_id = (int) $this->input->post('role_id', true);
        $submenu_id = (int) $this->input->post('submenu_id', true);

        // Deteksi kolom secara dinamis
        $col_role = $this->db->field_exists('role_id', 'tbl_submenu_access') ? 'role_id' : 'level_id';

        $exists = $this->db->get_where('tbl_submenu_access', [$col_role => $role_id, 'submenu_id' => $submenu_id])->row();

        if ($exists) {
            $this->db->delete('tbl_submenu_access', ['id' => $exists->id]);
            echo json_encode(['status' => 'removed']);
        } else {
            $this->db->insert('tbl_submenu_access', [$col_role => $role_id, 'submenu_id' => $submenu_id]);
            echo json_encode(['status' => 'added']);
        }
    }

    /**
     * AJAX: Toggle Global Menu Status (Active/Inactive)
     */
    public function ajax_toggle_menu_status()
    {
        // Pengecekan Otorisasi
        $session_role = (int) $this->session->userdata('level');
        if ($session_role !== 22 && $this->session->userdata('username') !== 'nizarsuryadi') {
            echo json_encode(['status' => 'error', 'message' => 'Unauthorized access']);
            return;
        }

        $menu_id = (int) $this->input->post('menu_id', true);

        // Deteksi PK secara dinamis (Support id vs id_menu)
        $pk = $this->db->field_exists('id', 'menu') ? 'id' : 'id_menu';
        $menu = $this->db->get_where('menu', [$pk => $menu_id])->row();

        if ($menu) {
            $new_status = ($menu->status == 1) ? 0 : 1;
            $this->db->update('menu', ['status' => $new_status], [$pk => $menu_id]);

            // Log ke Audit System agar bisa dilacak siapa yang merubah
            $this->_log_manual("Toggle Status Menu: " . $menu->nama_menu, "Status berubah menjadi: " . ($new_status == 1 ? 'ACTIVE' : 'INACTIVE'), 'WRITE');

            // Bersihkan cache menu agar perubahan langsung terlihat
            $this->session->unset_userdata('app_session_cache');

            echo json_encode(['status' => $new_status == 1 ? 'active' : 'inactive']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Menu tidak ditemukan dengan PK: ' . $pk]);
        }
    }

    /**
     * Atomic Action: Force Activate All Menus
     */
    public function force_activate_all_menus()
    {
        $role_id = (int) $this->session->userdata('level');
        if ($role_id !== 22 && $this->session->userdata('username') !== 'nizarsuryadi') {
            echo "Access Denied";
            return;
        }

        $this->db->update('menu', ['status' => 1]);
        $this->db->update('submenu', ['status' => 1]);

        $this->session->set_flashdata('message', "<strong>System Recovery:</strong> Seluruh menu dan submenu telah dipaksa menjadi <strong>AKTIF</strong>.");
        redirect('dashboard/developer_portal');
    }

    /**
     * Database Browser Index (List All Tables)
     */
    public function database_browser_index()
    {
        $role_id = (int) $this->session->userdata('level');
        if ($role_id !== 22 && $this->session->userdata('username') !== 'nizarsuryadi') {
            redirect('dashboard');
        }

        // Get All Tables with Stats
        $data['tables'] = $this->db->query("
            SELECT table_name, 
                   ROUND(((data_length + index_length) / 1024 / 1024), 2) AS size_mb, 
                   table_rows 
            FROM information_schema.TABLES 
            WHERE table_schema = DATABASE() 
            ORDER BY table_name ASC
        ")->result();

        $data['total_db_size'] = $this->db->query("SELECT ROUND(SUM(data_length + index_length) / 1024 / 1024, 2) AS size FROM information_schema.TABLES WHERE table_schema = DATABASE()")->row()->size;
        $data['title'] = 'Full Database Index';

        $this->load_admin_view('admin/dashboard/database_browser_index', $data);
    }


    /**
     * Atomic Action: Prune System Logs (Keep last 30 days)
     * Cleans: user_log AND user_activity_log
     */
    public function prune_system_logs()
    {
        $role_id = (int) $this->session->userdata('level');
        if ($role_id !== 22 && $this->session->userdata('username') !== 'nizarsuryadi') {
            echo "Access Denied";
            return;
        }

        // Keep last 30 days
        $cutoff_date = date('Y-m-d H:i:s', strtotime('-30 days'));

        // 1. Prune User Login History
        $this->db->where('login_time <', $cutoff_date);
        $this->db->delete('user_log');
        $affected_login = $this->db->affected_rows();

        // 2. Prune User Activity Log (if exists)
        $affected_activity = 0;
        if ($this->db->table_exists('user_activity_log')) {
            $this->db->where('timestamp <', $cutoff_date);
            $this->db->delete('user_activity_log');
            $affected_activity = $this->db->affected_rows();
        }

        $total_cleaned = $affected_login + $affected_activity;

        $msg = "Sistem berhasil membersihkan total <strong>" . number_format($total_cleaned) . "</strong> baris log sampah.<br>";
        $msg .= "<small>(Login Log: " . number_format($affected_login) . ", Activity Log: " . number_format($affected_activity) . ")</small>";

        $this->session->set_flashdata('message', $msg);
        redirect('dashboard/developer_portal');
    }

    /**
     * Atomic Action: Wipe Warehouse Records (DANGER: FOR DEV ONLY)
     * Cleans all stock history and purchase/intake transactions
     */
    public function wipe_warehouse_data()
    {
        $role_id = (int) $this->session->userdata('level');
        if ($role_id !== 22 && $this->session->userdata('username') !== 'nizarsuryadi') {
            echo "Access Denied";
            return;
        }

        // Tables to be emptied
        $tables = [
            'tbl_stok_gudang',
            'tbl_stok_log',
            'tbl_pembelian',
            'tbl_pembelian_detail',
            'tbl_pengambilan',
            'tbl_pengambilan_detail',
            'tbl_stok_opname',
            'tbl_hutang',
            'tbl_return_pembelian'
        ];

        foreach ($tables as $table) {
            if ($this->db->table_exists($table)) {
                $this->db->empty_table($table);
            }
        }

        $msg = "<strong>Warehouse Reset Berhasil!</strong> Seluruh data stok, log, dan transaksi pembelian telah dikosongkan.";
        $this->session->set_flashdata('message', $msg);
        redirect('dashboard/developer_portal');
    }

    /**
     * Atomic Action: Initialize Warehouse SKUs
     * Ensures every material in tbl_material has an entry in tbl_stok_gudang for ID 1
     */
    public function init_warehouse_stock()
    {
        $role_id = (int) $this->session->userdata('level');
        if ($role_id !== 22 && $this->session->userdata('username') !== 'nizarsuryadi') {
            echo "Access Denied";
            return;
        }

        $gudang_id = 1; // Default Master Warehouse
        $materials = $this->db->get('tbl_material')->result();
        $count = 0;

        foreach ($materials as $m) {
            $exists = $this->db->where(['material_id' => $m->material_id, 'gudang_id' => $gudang_id])
                ->count_all_results('tbl_stok_gudang');

            if (!$exists) {
                $this->db->insert('tbl_stok_gudang', [
                    'material_id' => $m->material_id,
                    'gudang_id' => $gudang_id,
                    'qty' => 0,
                    'last_update' => date('Y-m-d H:i:s')
                ]);
                $count++;
            }
        }

        $msg = "<strong>Sinkronisasi SKU Berhasil!</strong> Total <strong>$count</strong> entri stok baru telah dibuat untuk Gudang ID $gudang_id.";
        $this->session->set_flashdata('message', $msg);
        redirect('dashboard/developer_portal');
    }

    /**
     * Full Menu View (Utility)
     */
    public function index_full()
    {
        $userid = $this->session->userdata('id');
        $data['title'] = 'All Menus';
        $data['quick_access'] = []; // Feature removed
        $this->load_admin_view('admin/dashboard/index', $data);
    }

    // --- PRIVATE HELPERS ---

    /**
     * Prepares data specifically for System Administrator
     */
    private function _prepare_admin_dashboard_data($pid = null)
    {
        $harian = date('Y-m-d');
        $data = [];

        // 1. Antrian Produksi Aktif (Global)
        if ($pid) {
            $this->db->group_start()->where('lokasi_cetak', $pid)->or_group_start()->where('lokasi_cetak', '')->where('perusahaan_id', $pid)->group_end()->group_end();
        }
        $data['pending_approvals'] = $this->db->where('approve', 1)
            ->where_not_in('status_produksi', ['done', 'on_pb', 'on_costumer'])
            ->count_all_results('tbl_produksi');
        $this->db->reset_query();

        // 2. Critical Stock Alerts (Urgent)
        $this->db->select('m.varian, SUM(sg.qty) as total_qty, m.stok_min');
        $this->db->from('tbl_material m');
        $this->db->join('tbl_stok_gudang sg', 'm.material_id = sg.material_id', 'left');
        $this->db->group_by('m.material_id');
        $this->db->having('total_qty <= m.stok_min');
        $data['low_stock_list'] = $this->db->get()->result();
        $this->db->reset_query();
        $data['low_stock_count'] = count($data['low_stock_list']);

        // 3. Daily Active Users
        $data['active_users_today'] = $this->db->where('DATE(login_time)', $harian)->count_all_results('user_log');
        $this->db->reset_query();

        // 5. System Indicators
        $data['db_version'] = $this->db->query("SELECT VERSION() as version")->row()->version;
        $this->db->reset_query();

        // 6. Real-time System Errors (Today)
        $data['today_errors'] = 0;
        $log_file = APPPATH . 'logs/log-' . date('Y-m-d') . '.php';
        if (file_exists($log_file)) {
            $content = file_get_contents($log_file);
            $data['today_errors'] = substr_count($content, "\nERROR");
            // If the first line is an error, count it too
            if (strpos($content, 'ERROR') === 0) {
                $data['today_errors']++;
            }
        }

        // 7. Latest System Updates (Dynamic from Roadmap)
        $data['system_updates'] = $this->db->where('status', 'Live')
            ->order_by('id', 'DESC')
            ->limit(4)
            ->get('tbl_system_roadmap')
            ->result();
        $this->db->reset_query();

        // 8. Daily Material Consumption
        $data['daily_consumption'] = $this->M_gudang->get_daily_consumption($harian, $pid);

        // 9. Asset Summary
        $data['total_aset'] = 0;
        $data['total_nilai_aset'] = 0;
        if ($this->db->table_exists('tbl_aset')) {
            if ($pid) {
                $this->db->where('perusahaan_id', $pid);
            }
            $data['total_aset'] = $this->db->count_all_results('tbl_aset');
            $this->db->reset_query();

            if ($pid) {
                $this->db->where('perusahaan_id', $pid);
            }
            $data['total_nilai_aset'] = $this->db->select('SUM(aset_harga) as total')->get('tbl_aset')->row()->total ?? 0;
            $this->db->reset_query();
        }

        return $data;
    }

    /**
     * Prepares data specifically for Production Operator
     */
    private function _prepare_operator_data($pid, $userid)
    {
        $harian = date('Y-m-d');
        $data = [];

        $operator_id = $this->get_operator_id_for_current_user();
        $allowed_cats = $this->get_operator_kategori_ids();

        // Helper for branch filter (Logic: My branch printed here OR Origin branch printed here)
        $branch_filter = "(lokasi_cetak = '$pid' OR ((lokasi_cetak IS NULL OR lokasi_cetak = '' OR lokasi_cetak = '0') AND perusahaan_id = '$pid'))";
        $p_branch_filter = "(p.lokasi_cetak = '$pid' OR ((p.lokasi_cetak IS NULL OR p.lokasi_cetak = '' OR p.lokasi_cetak = '0') AND p.perusahaan_id = '$pid'))";

        // 1. Production Stats for this branch
        if ($operator_id !== null) {
            // Stats for Operator: Filter by active step and category

            // Total Queue
            $this->db->from('tbl_produksi p');
            $this->db->join('tbl_product pr', 'p.product_id = pr.product_id', 'left');
            $this->db->join('(
                SELECT dp1.produksi_id, MIN(dp1.routing_urutan) as active_urutan 
                FROM detail_produksi dp1 
                WHERE dp1.status_produksi IN (\'\', \'0\', \'1\') 
                GROUP BY dp1.produksi_id
            ) active_step', 'p.produksi_id = active_step.produksi_id', 'inner');
            $this->db->join('detail_produksi dp', 'p.produksi_id = dp.produksi_id AND dp.routing_urutan = active_step.active_urutan', 'inner');
            $this->db->where($p_branch_filter, NULL, FALSE);
            $this->db->where('p.approve', 1);
            $this->db->where_not_in('p.status_produksi', ['done', 'on_pb', 'on_costumer']);
            $this->db->group_start();
            $this->db->where('dp.operator_id', $operator_id);
            $this->db->or_where('dp.operator_id', 0);
            $this->db->or_where('dp.operator_id', '');
            $this->db->or_where('dp.operator_id IS NULL');
            $this->db->group_end();
            if ($allowed_cats !== null) {
                if (!empty($allowed_cats)) {
                    $this->db->where_in('pr.kategori_id', $allowed_cats);
                } else {
                    $this->db->where('pr.kategori_id', 0);
                }
            }
            $total_queue = $this->db->select('COUNT(DISTINCT p.produksi_id) as total')->get()->row()->total ?? 0;
            $this->db->reset_query();

            // Processing
            $this->db->from('tbl_produksi p');
            $this->db->join('tbl_product pr', 'p.product_id = pr.product_id', 'left');
            $this->db->join('(
                SELECT dp1.produksi_id, MIN(dp1.routing_urutan) as active_urutan 
                FROM detail_produksi dp1 
                WHERE dp1.status_produksi IN (\'\', \'0\', \'1\') 
                GROUP BY dp1.produksi_id
            ) active_step', 'p.produksi_id = active_step.produksi_id', 'inner');
            $this->db->join('detail_produksi dp', 'p.produksi_id = dp.produksi_id AND dp.routing_urutan = active_step.active_urutan', 'inner');
            $this->db->where($p_branch_filter, NULL, FALSE);
            $this->db->where('p.status_produksi', 'proses');
            $this->db->where('dp.operator_id', $operator_id);
            if ($allowed_cats !== null) {
                if (!empty($allowed_cats)) {
                    $this->db->where_in('pr.kategori_id', $allowed_cats);
                } else {
                    $this->db->where('pr.kategori_id', 0);
                }
            }
            $processing = $this->db->select('COUNT(DISTINCT p.produksi_id) as total')->get()->row()->total ?? 0;
            $this->db->reset_query();

            // Done Today
            $this->db->from('tbl_produksi p');
            $this->db->join('tbl_product pr', 'p.product_id = pr.product_id', 'left');
            $this->db->join('detail_produksi dp', 'p.produksi_id = dp.produksi_id', 'inner');
            $this->db->where($p_branch_filter, NULL, FALSE);
            $this->db->where('dp.operator_id', $operator_id);
            $this->db->where('dp.status_produksi', '2');
            $this->db->where('DATE(dp.waktu_selesai)', $harian);
            if ($allowed_cats !== null) {
                if (!empty($allowed_cats)) {
                    $this->db->where_in('pr.kategori_id', $allowed_cats);
                } else {
                    $this->db->where('pr.kategori_id', 0);
                }
            }
            $done_today = $this->db->select('COUNT(DISTINCT p.produksi_id) as total')->get()->row()->total ?? 0;
            $this->db->reset_query();

            // On PB
            $this->db->from('tbl_produksi p');
            $this->db->join('tbl_product pr', 'p.product_id = pr.product_id', 'left');
            $this->db->where($p_branch_filter, NULL, FALSE);
            $this->db->where('p.status_produksi', 'on_pb');
            if ($allowed_cats !== null) {
                if (!empty($allowed_cats)) {
                    $this->db->where_in('pr.kategori_id', $allowed_cats);
                } else {
                    $this->db->where('pr.kategori_id', 0);
                }
            }
            $on_pb = $this->db->select('COUNT(DISTINCT p.produksi_id) as total')->get()->row()->total ?? 0;
            $this->db->reset_query();

            $data['stats_produksi'] = [
                'total_queue' => $total_queue,
                'processing' => $processing,
                'done_today' => $done_today,
                'on_pb' => $on_pb
            ];
        } else {
            // Stats for Admin: Original behavior
            $data['stats_produksi'] = [
                'total_queue' => $this->db->where($branch_filter, NULL, FALSE)
                    ->where('approve', 1)
                    ->where_not_in('status_produksi', ['done', 'on_pb', 'on_costumer'])
                    ->from('tbl_produksi')
                    ->count_all_results(),

                'processing' => $this->db->where($branch_filter, NULL, FALSE)
                    ->where('status_produksi', 'proses')
                    ->from('tbl_produksi')
                    ->count_all_results(),

                'done_today' => $this->db->where($branch_filter, NULL, FALSE)
                    ->where(['status_produksi' => 'done', 'DATE(tgl_selesai)' => $harian])
                    ->from('tbl_produksi')
                    ->count_all_results(),

                'on_pb' => $this->db->where($branch_filter, NULL, FALSE)
                    ->where('status_produksi', 'on_pb')
                    ->from('tbl_produksi')
                    ->count_all_results(),
            ];
        }

        // 2. Machine Status
        $data['machines'] = $this->db->where('perusahaan_id', $pid)->get('tbl_mesin')->result();

        // 3. Active Queue List - Matching Wip.php/produksi logic
        $this->db->select('p.*, k.konsumen_nama, pr.product_nama, hp.detail_product, tp.nama_perusahaan as asal_cabang, tr.nama_routing, dp.status_produksi as routing_status');
        $this->db->from('tbl_produksi p');
        $this->db->join('tbl_konsumen k', 'p.konsumen_id = k.konsumen_id', 'left');
        $this->db->join('tbl_product pr', 'p.product_id = pr.product_id', 'left');
        $this->db->join('tbl_harga_product hp', 'p.harga_id = hp.harga_id', 'left');
        $this->db->join('tbl_perusahaan tp', 'p.perusahaan_id = tp.id_perusahaan', 'left');
        $this->db->join('(
                SELECT dp1.produksi_id, MIN(dp1.routing_urutan) as active_urutan 
                FROM detail_produksi dp1 
                WHERE dp1.status_produksi IN (\'\', \'0\', \'1\') 
                GROUP BY dp1.produksi_id
            ) active_step', 'p.produksi_id = active_step.produksi_id', 'inner');
        $this->db->join('detail_produksi dp', 'p.produksi_id = dp.produksi_id AND dp.routing_urutan = active_step.active_urutan', 'inner');
        $this->db->join('tbl_routing tr', 'dp.routing_id = tr.id', 'left');

        if ($operator_id !== null) {
            $this->db->group_start();
            $this->db->where('dp.operator_id', $operator_id);
            $this->db->or_where('dp.operator_id', 0);
            $this->db->or_where('dp.operator_id', '');
            $this->db->or_where('dp.operator_id IS NULL');
            $this->db->group_end();
            if ($allowed_cats !== null) {
                if (!empty($allowed_cats)) {
                    $this->db->where_in('pr.kategori_id', $allowed_cats);
                } else {
                    $this->db->where('pr.kategori_id', 0);
                }
            }
        }

        $this->db->where($p_branch_filter, NULL, FALSE);
        $this->db->where('p.approve', 1);
        $this->db->where_not_in('p.status_produksi', ['done', 'on_pb', 'on_costumer']);
        $this->db->order_by('p.produksi_id', 'DESC'); // Latest first, matching WIP
        $this->db->limit(20); // Slightly more items
        $data['active_queue'] = $this->db->get()->result();

        // Fetch tasks for this operator
        if ($operator_id !== null) {
            $this->db->select('GROUP_CONCAT(DISTINCT routing_name SEPARATOR \', \') as tasks');
            $this->db->from('routing');
            $this->db->where('operator_id', $operator_id);
            $rt = $this->db->get()->row();
            $data['operator_tasks'] = $rt ? $rt->tasks : '';
        } else {
            $data['operator_tasks'] = '';
        }

        return $data;
    }

    /**
     * Prepares data specifically for Human Resource (HRD)
     */
    private function _prepare_hrd_data($pid = null)
    {
        $harian = date('Y-m-d');
        $data = [];

        // 1. Employee Stats
        if ($pid) {
            $this->db->where('perusahaan_id', $pid);
        }
        $this->db->where("nik NOT LIKE 'STATION%'");
        $this->db->where("nik NOT LIKE 'TERM%'");
        $data['total_karyawan'] = $this->db->count_all_results('tbl_karyawan');

        // 2. Attendance Stats for today
        $this->db->from('absensi');
        $this->db->join('tbl_karyawan', 'absensi.karyawan_id = tbl_karyawan.karyawan_id');
        if ($pid) {
            $this->db->where('tbl_karyawan.perusahaan_id', $pid);
        }
        $this->db->where("tbl_karyawan.nik NOT LIKE 'STATION%'");
        $this->db->where("tbl_karyawan.nik NOT LIKE 'TERM%'");
        $this->db->where('absensi.tanggal', $harian);

        $base_q = clone $this->db;
        $data['absensi_stats'] = [
            'hadir' => (clone $base_q)->where('status', 'Hadir')->count_all_results(),
            'izin' => (clone $base_q)->group_start()->where_in('status', ['Izin', 'Sakit'])->or_like('status', 'Cuti')->group_end()->count_all_results(),
            'alpha' => (clone $base_q)->where('status', 'Alpha')->count_all_results(),
            'telat' => (clone $base_q)->where('status', 'Late')->count_all_results(),
        ];

        // 2.1 Late Employees Today (Explicit List for Alerts)
        $data['late_employees_today'] = $this->Absensi_model->get_late_employees($harian, $pid);

        // 2.1. Attendance Health Rate (Simple percentage of presence vs total today)
        $total_today = array_sum($data['absensi_stats']);
        $data['attendance_health'] = ($total_today > 0) ? round(($data['absensi_stats']['hadir'] / $total_today) * 100, 1) : 0;

        // 3. Human Capital Analytics
        // 3.1 Gender Distribution
        $this->db->select('jenis_kelamin, COUNT(*) as count');
        $this->db->from('tbl_karyawan');
        if ($pid)
            $this->db->where('perusahaan_id', $pid);
        $this->db->group_by('jenis_kelamin');
        $data['gender_stats'] = $this->db->get()->result();

        // 3.2 Division Distribution
        $this->db->select('d.divisi_nama, COUNT(k.karyawan_id) as count');
        $this->db->from('tbl_divisi d');
        $this->db->join('tbl_karyawan k', 'd.divisi_id = k.divisi_id', 'left');
        if ($pid)
            $this->db->where('k.perusahaan_id', $pid);
        $this->db->group_by('d.divisi_id');
        $this->db->having('count > 0');
        $this->db->order_by('count', 'DESC');
        $data['division_stats'] = $this->db->get()->result();

        // 4. Activity Streams
        // 4.1 Upcoming Birthdays (Next 14 Days)
        $this->db->select('nama_lengkap, tanggal_lahir, foto, nik');
        $this->db->from('tbl_karyawan');
        if ($pid)
            $this->db->where('perusahaan_id', $pid);
        // Logic: Month and Day matching current or upcoming
        $this->db->where("DATE_FORMAT(tanggal_lahir, '%m-%d') BETWEEN DATE_FORMAT(CURDATE(), '%m-%d') AND DATE_FORMAT(DATE_ADD(CURDATE(), INTERVAL 14 DAY), '%m-%d')");
        $this->db->order_by("DATE_FORMAT(tanggal_lahir, '%m-%d') ASC", '', FALSE);
        $this->db->limit(5);
        $data['birthdays'] = $this->db->get()->result();

        // 4.2 Upcoming Anniversaries (Joined this month)
        $this->db->select('nama_lengkap, tanggal_masuk, foto');
        $this->db->from('tbl_karyawan');
        if ($pid)
            $this->db->where('perusahaan_id', $pid);
        $this->db->where("MONTH(tanggal_masuk) = MONTH(CURDATE())");
        $this->db->where("DAY(tanggal_masuk) >= DAY(CURDATE())");
        $this->db->order_by("DAY(tanggal_masuk) ASC", '', FALSE);
        $this->db->limit(5);
        $data['anniversaries'] = $this->db->get()->result();

        // 5. Recent Employees List
        $this->db->select('k.*, j.nama_jabatan, d.divisi_nama, p.nama_perusahaan as branch_name');
        $this->db->from('tbl_karyawan k');
        $this->db->join('tbl_jabatan j', 'k.jabatan_id = j.jabatan_id', 'left');
        $this->db->join('tbl_divisi d', 'k.divisi_id = d.divisi_id', 'left');
        $this->db->join('tbl_perusahaan p', 'k.perusahaan_id = p.id_perusahaan', 'left');
        if ($pid) {
            $this->db->where('k.perusahaan_id', $pid);
        }
        $this->db->order_by('k.created_at', 'DESC');
        $this->db->limit(10);
        $data['recent_employees'] = $this->db->get()->result();

        // 6. Today's Attendance Feed (Real-time Stream)
        $this->db->select('a.*, k.nama_lengkap, k.foto, k.nik');
        $this->db->from('absensi a');
        $this->db->join('tbl_karyawan k', 'a.karyawan_id = k.karyawan_id');
        if ($pid) {
            $this->db->where('k.perusahaan_id', $pid);
        }
        $this->db->where('a.tanggal', $harian);
        $this->db->order_by('a.id', 'DESC'); // Get absolute latest records
        $this->db->limit(10);
        $data['attendance_feed'] = $this->db->get()->result();

        // 6.5 Employees who are absent/sick/leave today
        $this->db->select('k.nama_lengkap, k.foto, a.status, a.keterangan');
        $this->db->from('absensi a');
        $this->db->join('tbl_karyawan k', 'k.karyawan_id = a.karyawan_id', 'left');
        $this->db->where('a.tanggal', $harian);
        $this->db->where_in('a.status', ['Sakit', 'Izin', 'Alpha', 'Cuti']);
        if ($pid) {
            $this->db->where('k.perusahaan_id', $pid);
        }
        $data['absen_hari_ini'] = $this->db->get()->result();

        // 7. System Audit Trail (Audit Log)
        $this->db->select('al.*, u.username');
        $this->db->from('user_activity_log al');
        $this->db->join('users u', 'al.user_id = u.user_id', 'left');
        $this->db->order_by('al.timestamp', 'DESC');
        $this->db->limit(10);
        $data['recent_logs'] = $this->db->get()->result();

        // 7.5 Employee Status Stats
        $this->db->select('status_karyawan, COUNT(*) as count');
        $this->db->from('tbl_karyawan');
        if ($pid)
            $this->db->where('perusahaan_id', $pid);
        $this->db->where("nik NOT LIKE 'STATION%'");
        $this->db->group_by('status_karyawan');
        $data['status_stats'] = $this->db->get()->result();

        // 7.6 Expiring Contracts (Exact calculation using akhir_kontrak within 30 days)
        $this->db->select('nama_lengkap, akhir_kontrak, status_karyawan, foto, nik, tanggal_masuk');
        $this->db->from('tbl_karyawan');
        if ($pid)
            $this->db->where('perusahaan_id', $pid);
        $this->db->where_in('status_karyawan', ['Kontrak', 'Magang']);
        $this->db->where('akhir_kontrak IS NOT NULL');
        $this->db->where("akhir_kontrak <= DATE_ADD(CURDATE(), INTERVAL 30 DAY)");
        $this->db->order_by("akhir_kontrak ASC");
        $data['expiring_contracts'] = $this->db->get()->result();

        // 8. Pending Approvals & Action Items (Command Center)
        // 8.1 Pending Izin / Cuti
        $this->db->where('status', 'Pending');
        if ($pid) {
            $this->db->join('tbl_karyawan', 'tbl_pengajuan_izin.karyawan_id = tbl_karyawan.karyawan_id');
            $this->db->where('tbl_karyawan.perusahaan_id', $pid);
        }
        $data['pending_izin_count'] = $this->db->count_all_results('tbl_pengajuan_izin');

        // 8.2 Pending Payroll (Slip Gaji Belum Paid)
        $this->db->where('status_pembayaran', 'pending');
        if ($pid) {
            $this->db->join('tbl_karyawan', 'tbl_slip_gaji.karyawan_id = tbl_karyawan.karyawan_id');
            $this->db->where('tbl_karyawan.perusahaan_id', $pid);
        }
        $data['pending_payroll_count'] = $this->db->count_all_results('tbl_slip_gaji');

        // 8.3 Pending Koreksi Absen
        $this->db->where('status', 'Pending');
        if ($pid) {
            $this->db->join('tbl_karyawan', 'tbl_pengajuan_koreksi_absen.karyawan_id = tbl_karyawan.karyawan_id');
            $this->db->where('tbl_karyawan.perusahaan_id', $pid);
        }
        $data['pending_koreksi_count'] = $this->db->count_all_results('tbl_pengajuan_koreksi_absen');

        // 9. Data Keuangan (Finance Integration for HR)
        $bulanini = date('Y-m');
        $data['pemasukan_hariini'] = $this->_get_consolidated_income($harian, $pid);
        $data['pemasukan_bulanini'] = $this->M_laporan->pemasukan_bulanan($pid, $bulanini)->row()->total_pemasukan_bulanan ?? 0;
        $data['pengeluaran_hariini'] = $this->M_laporan->pengeluaran($pid)->row()->pengeluaran ?? 0;
        $data['pengeluaran_bulanini'] = $this->M_laporan->pengeluaran_bulanan($pid, $bulanini)->row()->pengeluaran_bulanan ?? 0;
        $data['total_hutang'] = $this->db->select('SUM(sisa_hutang) as total')->from('tbl_hutang')->get()->row()->total ?? 0;

        return $data;
    }

    /**
     * Prepares data specifically for Pengambilan Barang (PB) Station
     */
    private function _prepare_pb_data($pid)
    {
        $harian = date('Y-m-d');
        $data = [];

        // 1. PB Hub Stats (Global across branches as per owner direction)
        $data['pb_stats'] = [
            'ready' => $this->db->where(['status_produksi' => 'on_pb'])->count_all_results('tbl_produksi'),
            'done_today' => $this->db->where(['status_produksi' => 'on_costumer', 'DATE(update_time)' => $harian])->count_all_results('tbl_produksi'),
            // Total items that should eventually reach PB (approved, proses, done status)
            'incoming' => $this->db->where_in('status_produksi', ['approved', 'proses', 'done'])->count_all_results('tbl_produksi'),
        ];

        // 2. Ready to Pickup List
        $this->db->select('p.*, k.konsumen_nama, pr.product_nama, hp.detail_product');
        $this->db->from('tbl_produksi p');
        $this->db->join('tbl_konsumen k', 'p.konsumen_id = k.konsumen_id', 'left');
        $this->db->join('tbl_product pr', 'p.product_id = pr.product_id', 'left');
        $this->db->join('tbl_harga_product hp', 'p.harga_id = hp.harga_id', 'left');
        $this->db->where(['p.status_produksi' => 'on_pb']);
        $this->db->order_by('p.tgl_kirim_pb', 'ASC'); // Oldest in PB first
        $this->db->limit(15);
        $data['ready_list'] = $this->db->get()->result();

        return $data;
    }

    /**
     * Prepares data specifically for Deskprint station
     */
    private function _prepare_deskprint_data($pid, $userid)
    {
        $pid = (int) $pid; // Ensure Integer to prevent SQL Syntax Error
        $harian = date('Y-m-d');
        $data = [];

        // 1. Personal Stats (Intake Task)
        $data['personal_spk_today'] = $this->db->where(['deskprint_id' => $userid, 'DATE(created_at)' => $harian])->count_all_results('tbl_pemesanan');

        // Personal Revenue Handling (Omzet from SPKs they created)
        $res = $this->db->query("
            SELECT SUM(pay.total_tagihan) as total 
            FROM tbl_pemesanan p 
            JOIN tbl_pembayaran pay ON p.nospk = pay.nospk
            WHERE p.deskprint_id = $userid 
            AND DATE(p.created_at) = '$harian'
            AND p.status_pembayaran = 'paid'
        ")->row();
        $data['personal_omzet_today'] = $res->total ?? 0;

        // 2. Branch Intake Stats
        $data['branch_spk_today'] = $this->db->where(['perusahaan_id' => $pid, 'DATE(created_at)' => $harian])->count_all_results('tbl_pemesanan');

        // Branch Omzet (Nominal)
        $res_branch = $this->db->query("
            SELECT SUM(pay.total_tagihan) as total 
            FROM tbl_pemesanan p 
            JOIN tbl_pembayaran pay ON p.nospk = pay.nospk
            WHERE p.perusahaan_id = $pid 
            AND DATE(p.created_at) = '$harian'
            AND p.status_pembayaran = 'paid'
        ")->row();
        $data['branch_omzet_today'] = $res_branch->total ?? 0;

        // 3. Recent SPKs handled by this Deskprint (Personal List)
        $this->db->select('p.*, k.konsumen_nama, (SELECT SUM(qty) FROM tbl_pemesanan_detail WHERE nospk = p.nospk) as total_item');
        $this->db->from('tbl_pemesanan p');
        $this->db->join('tbl_konsumen k', 'p.konsumen_id = k.konsumen_id', 'left');
        $this->db->where('p.deskprint_id', $userid);
        $this->db->order_by('p.created_at', 'DESC');
        $this->db->limit(10);
        $data['recent_spks'] = $this->db->get()->result();

        // 4. Check active queue (unfinished)
        $data['has_active_queue'] = $this->db->where('user_id', $userid)
            ->where_in('status', ['dipanggil', 'sedang_dilayani'])
            ->count_all_results('tbl_antrian');

        return $data;
    }

    /**
     * Prepares dashboard stats for operational staff with strict branch filtering
     */
    private function _prepare_staff_data($role_id, $perusahaan_id, $userid)
    {
        $harian = date('Y-m-d');
        $bulanan = date('Y-m');
        $kemarin = date('Y-m-d', strtotime('-1 day'));

        $data = [
            'total_belumbayar' => $this->db->where(['perusahaan_id' => $perusahaan_id, 'status_pembayaran' => 'pending'])->count_all_results('tbl_pemesanan'),
            'total_sudahbayar' => $this->db->where(['perusahaan_id' => $perusahaan_id, 'status_pembayaran' => 'paid'])->count_all_results('tbl_pemesanan'),
            'total_trxday' => ['total' => $this->_get_branch_sales($harian, $perusahaan_id)],
            'total_kemarin' => ['total' => $this->_get_branch_sales($kemarin, $perusahaan_id)],
            'total_penj_hariini' => ['total' => $this->db->where(['perusahaan_id' => $perusahaan_id, 'tgl_bayar' => $harian])->count_all_results('tbl_pembayaran')],
            'total_penj_kemarin' => ['total' => $this->db->where(['perusahaan_id' => $perusahaan_id, 'tgl_bayar' => $kemarin])->count_all_results('tbl_pembayaran')],
            'total_piutang' => $this->db->select('SUM(total_tagihan - total_bayar) as sisa')->from('tbl_piutang')->where('perusahaan_id', $perusahaan_id)->where('status !=', 'LUNAS')->get()->row()->sisa ?? 0,
            'total_titipan' => $this->db->from('tbl_pemesanan p')
                ->join('tbl_produksi pr', 'pr.nospk = p.nospk')
                ->where('pr.lokasi_cetak', $perusahaan_id)
                ->where('p.perusahaan_id !=', $perusahaan_id)
                ->where('p.status_pembayaran', 'pending')
                ->group_by('p.nospk')
                ->get()->num_rows(),
        ];

        // Monthly Stats for Chart
        $months = ['01', '02', '03', '04', '05', '06', '07', '08', '09', '10', '11', '12'];
        foreach ($months as $m) {
            $month_name = strtolower(date('M', mktime(0, 0, 0, $m, 1)));
            if ($month_name == 'aug')
                $month_name = 'ags';
            if ($month_name == 'sep')
                $month_name = 'sept';
            if ($month_name == 'may')
                $month_name = 'mei';
            if ($month_name == 'oct')
                $month_name = 'okt';
            if ($month_name == 'dec')
                $month_name = 'des';

            $data[$month_name] = ['total' => $this->_get_branch_monthly_sales($m, date('Y'), $perusahaan_id)];
        }
        // Fetch Live System Updates for the Dashboard Widget
        $data['system_updates'] = $this->db->where('status', 'Live')
            ->order_by('created_at', 'DESC')
            ->limit(3)
            ->get('tbl_system_roadmap')
            ->result();

        return $data;
    }

    private function _get_branch_sales($date, $pid)
    {
        // 1. Regular payments from branch transactions
        $base = $this->db->select('SUM(pd.jumlah) as total')
            ->from('tbl_pembayaran_detail pd')
            ->join('tbl_pembayaran p', 'pd.pembayaran_id = p.pembayaran_id')
            ->where(['p.perusahaan_id' => $pid, 'p.tgl_bayar' => $date])
            ->get()->row()->total ?? 0;

        // 2. Piutang payments for this branch
        $piutang = $this->db->select('SUM(pp.jumlah_bayar) as total')
            ->from('tbl_piutang_pembayaran pp')
            ->join('tbl_piutang p', 'pp.piutang_id = p.piutang_id')
            ->where(['p.perusahaan_id' => $pid, 'DATE(pp.tanggal_bayar)' => $date])
            ->get()->row()->total ?? 0;

        return $base + $piutang;
    }

    private function _get_branch_monthly_sales($month, $year, $pid)
    {
        // 1. Regular payments
        $base = $this->db->select('SUM(pd.jumlah) as total')
            ->from('tbl_pembayaran_detail pd')
            ->join('tbl_pembayaran p', 'pd.pembayaran_id = p.pembayaran_id')
            ->where('p.perusahaan_id', $pid)
            ->where('MONTH(p.tgl_bayar)', (int) $month)
            ->where('YEAR(p.tgl_bayar)', (int) $year)
            ->get()->row()->total ?? 0;

        // 2. Piutang payments
        $piutang = $this->db->select('SUM(pp.jumlah_bayar) as total')
            ->from('tbl_piutang_pembayaran pp')
            ->join('tbl_piutang p', 'pp.piutang_id = p.piutang_id')
            ->where('p.perusahaan_id', $pid)
            ->where('MONTH(pp.tanggal_bayar)', (int) $month)
            ->where('YEAR(pp.tanggal_bayar)', (int) $year)
            ->get()->row()->total ?? 0;

        return $base + $piutang;
    }

    private function _get_consolidated_income($date, $pid = null)
    {
        $where = "WHERE 1=1 AND p.tgl_bayar = '$date'";
        if ($pid)
            $where .= " AND p.perusahaan_id = $pid";
        $base = $this->db->query("SELECT SUM(pd.jumlah) as total FROM tbl_pembayaran_detail pd JOIN tbl_pembayaran p ON pd.pembayaran_id = p.pembayaran_id $where")->row()->total ?? 0;

        $where_p = "WHERE DATE(tanggal_bayar) = '$date'";
        if ($pid) {
            // Need to join for piutang if filter by branch
            $piutang = $this->db->query("SELECT SUM(pp.jumlah_bayar) as total FROM tbl_piutang_pembayaran pp JOIN tbl_piutang p ON pp.piutang_id = p.piutang_id WHERE DATE(pp.tanggal_bayar) = '$date' AND p.perusahaan_id = $pid")->row()->total ?? 0;
        } else {
            $piutang = $this->db->query("SELECT SUM(jumlah_bayar) as total FROM tbl_piutang_pembayaran WHERE DATE(tanggal_bayar) = '$date'")->row()->total ?? 0;
        }

        return $base + $piutang;
    }

    private function _get_payment_methods_breakdown($date, $pid = null, $is_monthly = false)
    {
        $where_pay = "";
        if ($pid)
            $where_pay = " AND p.perusahaan_id = $pid";

        $where_piu = "";
        if ($pid)
            $where_piu = " AND p_ref.perusahaan_id = $pid";
        $join_piu = "";
        if ($pid)
            $join_piu = " JOIN tbl_piutang p_ref ON pp.piutang_id = p_ref.piutang_id";

        $date_clause_pay = $is_monthly ? "DATE_FORMAT(p.tgl_bayar, '%Y-%m') = '$date'" : "p.tgl_bayar = '$date'";
        $date_clause_piu = $is_monthly ? "DATE_FORMAT(pp.tanggal_bayar, '%Y-%m') = '$date'" : "DATE(pp.tanggal_bayar) = '$date'";

        return $this->db->query("
            SELECT 
                CASE 
                    WHEN (metode LIKE '%tunai%' OR metode LIKE '%cash%') THEN 'TUNAI'
                    WHEN (metode LIKE '%transfer%') THEN 'TRANSFER'
                    WHEN (metode LIKE '%debit%' OR metode LIKE '%edc%') THEN 'EDC'
                    WHEN (metode LIKE '%ewallet%' OR metode LIKE '%ovo%' OR metode LIKE '%qris%') THEN 'E-WALLET/QRIS'
                    ELSE 'LAINNYA'
                END as metode,
                SUM(total) as total 
            FROM (
                SELECT pd.metode, SUM(pd.jumlah) as total 
                FROM tbl_pembayaran p 
                JOIN tbl_pembayaran_detail pd ON p.pembayaran_id = pd.pembayaran_id 
                WHERE $date_clause_pay $where_pay
                GROUP BY pd.metode
                UNION ALL
                SELECT pp.metode_bayar as metode, SUM(pp.jumlah_bayar) as total
                FROM tbl_piutang_pembayaran pp
                $join_piu
                WHERE $date_clause_piu $where_piu
                GROUP BY pp.metode_bayar
            ) combined
            GROUP BY 1
            ORDER BY total DESC
        ")->result();
    }
    private function _get_consolidated_monthly_sales($month, $year, $pid = null)
    {
        // 1. Regular payments
        $this->db->select('SUM(pd.jumlah) as total')
            ->from('tbl_pembayaran_detail pd')
            ->join('tbl_pembayaran p', 'pd.pembayaran_id = p.pembayaran_id')
            ->where('MONTH(p.tgl_bayar)', (int) $month)
            ->where('YEAR(p.tgl_bayar)', (int) $year);
        if ($pid)
            $this->db->where('p.perusahaan_id', $pid);
        $base = $this->db->get()->row()->total ?? 0;

        // 2. Piutang payments
        $this->db->select('SUM(pp.jumlah_bayar) as total')
            ->from('tbl_piutang_pembayaran pp');
        if ($pid) {
            $this->db->join('tbl_piutang p_ref', 'pp.piutang_id = p_ref.piutang_id');
            $this->db->where('p_ref.perusahaan_id', $pid);
        }
        $this->db->where('MONTH(pp.tanggal_bayar)', (int) $month)
            ->where('YEAR(pp.tanggal_bayar)', (int) $year);
        $piutang = $this->db->get()->row()->total ?? 0;

        return $base + $piutang;
    }

    /**
     * AJAX: Get Stock for Deskprint/Kasir Dashboard View
     */
    public function ajax_get_stock_deskprint()
    {
        $role_id = (int) $this->session->userdata('level');
        if (!in_array($role_id, [1, 2, 3, 4, 5, 6, 8, 17, 19, 21, 22])) {
            echo json_encode([]);
            return;
        }

        $perusahaan_id = $this->session->userdata('perusahaan_id');
        $allowed_cats = $this->get_operator_kategori_ids();
        $is_admin = in_array($role_id, [1, 8, 17, 21, 22], true);

        $where_clauses = [];
        // Tampilkan semua gudang (Pusat & Cabang) untuk semua role di Cek Stok Gudang
        // if (!$is_admin && $perusahaan_id) {
        //     $where_clauses[] = "sg.gudang_id = " . (int) $perusahaan_id;
        // }
        if (!$is_admin && $allowed_cats !== null) {
            if (!empty($allowed_cats)) {
                $cats_str = implode(',', array_map('intval', $allowed_cats));
                $where_clauses[] = "sg.material_id IN (
                    SELECT DISTINCT bl.bahan_id 
                    FROM bom_lines bl
                    JOIN tbl_product p_filter ON bl.product_id = p_filter.product_id
                    WHERE p_filter.kategori_id IN ($cats_str)
                )";
            } else {
                $where_clauses[] = "sg.material_id = 0";
            }
        }

        $where_sql = "";
        if (!empty($where_clauses)) {
            $where_sql = " WHERE " . implode(" AND ", $where_clauses);
        }

        // Fetch simplified stock grouped by material and branch
        $q = "SELECT b.bahan_nama, m.varian, SUM(sg.qty) as base_qty, m.konversi,
                     p.nama_perusahaan as cabang, 
                     s.satuan_nama as satuan_gudang,
                     sp.satuan_nama as satuan_dasar
              FROM tbl_stok_gudang sg
              JOIN tbl_material m ON sg.material_id = m.material_id
              JOIN tbl_bahan b ON m.bahan_id = b.bahan_id
              LEFT JOIN tbl_satuan s ON m.satuan_gudang_id = s.satuan_id
              LEFT JOIN tbl_satuan sp ON b.satuan_id = sp.satuan_id
              JOIN tbl_perusahaan p ON sg.gudang_id = p.id_perusahaan
              $where_sql
              GROUP BY sg.material_id, sg.gudang_id
              ORDER BY b.bahan_nama ASC, m.varian ASC";
        $data = $this->db->query($q)->result();
        echo json_encode($data);
    }

    /**
     * AJAX: Get Pivoted Stock for Deskprint/Kasir Dashboard View
     */
    public function ajax_get_stock_deskprint_pivoted()
    {
        $role_id = (int) $this->session->userdata('level');
        if (!in_array($role_id, [1, 2, 3, 4, 5, 6, 8, 17, 19, 21, 22])) {
            echo json_encode([]);
            return;
        }

        $allowed_cats = $this->get_operator_kategori_ids();
        $is_admin = in_array($role_id, [1, 8, 17, 21, 22], true);

        $where_clauses = [];
        if (!$is_admin && $allowed_cats !== null) {
            if (!empty($allowed_cats)) {
                $cats_str = implode(',', array_map('intval', $allowed_cats));
                $where_clauses[] = "sg.material_id IN (
                    SELECT DISTINCT bl.bahan_id 
                    FROM bom_lines bl
                    JOIN tbl_product p_filter ON bl.product_id = p_filter.product_id
                    WHERE p_filter.kategori_id IN ($cats_str)
                )";
            } else {
                $where_clauses[] = "sg.material_id = 0";
            }
        }

        $where_sql = "";
        if (!empty($where_clauses)) {
            $where_sql = " WHERE " . implode(" AND ", $where_clauses);
        }

        $q = "SELECT b.bahan_nama, m.varian, SUM(sg.qty) as base_qty, m.konversi,
                     p.id_perusahaan,
                     s.satuan_nama as satuan_gudang,
                     sp.satuan_nama as satuan_dasar
              FROM tbl_stok_gudang sg
              JOIN tbl_material m ON sg.material_id = m.material_id
              JOIN tbl_bahan b ON m.bahan_id = b.bahan_id
              LEFT JOIN tbl_satuan s ON m.satuan_gudang_id = s.satuan_id
              LEFT JOIN tbl_satuan sp ON b.satuan_id = sp.satuan_id
              JOIN tbl_perusahaan p ON sg.gudang_id = p.id_perusahaan
              $where_sql
              GROUP BY sg.material_id, sg.gudang_id
              ORDER BY b.bahan_nama ASC, m.varian ASC";

        $raw_data = $this->db->query($q)->result();

        $pivoted = [];
        foreach ($raw_data as $row) {
            $key = $row->bahan_nama . '|' . $row->varian;
            if (!isset($pivoted[$key])) {
                $pivoted[$key] = [
                    'bahan_nama' => $row->bahan_nama,
                    'varian' => $row->varian,
                    'konversi' => $row->konversi,
                    'satuan_gudang' => $row->satuan_gudang,
                    'satuan_dasar' => $row->satuan_dasar
                ];
            }
            $pivoted[$key]['branch_' . $row->id_perusahaan] = $row->base_qty;
        }

        echo json_encode(array_values($pivoted));
    }

    /**
     * AJAX: Get Customers for Deskprint/Kasir Dashboard View
     */
    public function konsumen_get_ajax()
    {
        $request = $_POST;
        $this->load->model('Konsumen_model');
        $data = $this->Konsumen_model->get_datatables($request);
        echo json_encode($data);
    }

    /**
     * AJAX: Add Customer from Dashboard
     */
    public function konsumen_add()
    {
        $data = array(
            'konsumen_nama' => $this->input->post('konsumen_nama', true),
            'konsumen_alamat' => $this->input->post('konsumen_alamat', true),
            'konsumen_nohp' => $this->input->post('konsumen_nohp', true),
            'konsumen_email' => $this->input->post('konsumen_email', true),
        );
        $this->load->model('M_master');
        $this->M_master->addData('tbl_konsumen', $data);

        if ($this->input->is_ajax_request()) {
            echo json_encode(['status' => 'success', 'message' => 'Konsumen baru berhasil ditambahkan!']);
        } else {
            $this->session->set_flashdata('success', 'Data Berhasil Disimpan');
            redirect('dashboard');
        }
    }

    /**
     * AJAX: Edit Customer from Dashboard
     */
    public function konsumen_edit()
    {
        $id = $this->input->post('konsumen_id', true);
        $data = [
            'konsumen_nama' => $this->input->post('konsumen_nama', true),
            'konsumen_alamat' => $this->input->post('konsumen_alamat', true),
            'konsumen_nohp' => $this->input->post('konsumen_nohp', true),
            'konsumen_email' => $this->input->post('konsumen_email', true),
            'status' => $this->input->post('status', true)
        ];

        if ($id && !empty($data['konsumen_nama'])) {
            $this->db->where('konsumen_id', $id)->update('tbl_konsumen', $data);
            if ($this->input->is_ajax_request()) {
                echo json_encode(['status' => 'success', 'message' => 'Data konsumen berhasil diperbarui!']);
            } else {
                $this->session->set_flashdata('success', 'Data konsumen berhasil diperbarui.');
                redirect('dashboard');
            }
        } else {
            if ($this->input->is_ajax_request()) {
                header('HTTP/1.1 400 Bad Request');
                echo json_encode(['status' => 'error', 'message' => 'Data tidak valid.']);
            } else {
                $this->session->set_flashdata('error', 'Data tidak valid.');
                redirect('dashboard');
            }
        }
    }

    /**
     * AJAX: Delete Customer from Dashboard
     */
    public function konsumen_delete($id)
    {
        $this->db->where('konsumen_id', $id)->delete('tbl_konsumen');
        if ($this->input->is_ajax_request()) {
            echo json_encode(['status' => 'success', 'message' => 'Data konsumen berhasil dihapus!']);
        } else {
            $this->session->set_flashdata('success', 'Data Berhasil Dihapus');
            redirect('dashboard');
        }
    }

    /**
     * AJAX: Get Material Consumption History for the Logged-in Operator
     */
    public function ajax_get_operator_consumption()
    {
        $userid = $this->session->userdata('id');
        $this->db->select('sl.created_at, sl.qty_out as qty, m.varian as material_nama, m.konversi, b.bahan_nama, s.satuan_nama as satuan_gudang, s_prod.satuan_nama as satuan_produksi, p.nospk, pr.product_nama');
        $this->db->from('tbl_stok_log sl');
        $this->db->join('tbl_material m', 'm.material_id = sl.material_id', 'left');
        $this->db->join('tbl_bahan b', 'b.bahan_id = m.bahan_id', 'left');
        $this->db->join('tbl_satuan s', 's.satuan_id = m.satuan_gudang_id', 'left');
        $this->db->join('tbl_satuan s_prod', 's_prod.satuan_id = b.satuan_id', 'left');
        $this->db->join('tbl_produksi p', 'p.produksi_id = sl.trx_id', 'left');
        $this->db->join('tbl_product pr', 'pr.product_id = p.product_id', 'left');
        $this->db->where('sl.trx_type', 'Produksi');
        $this->db->where('sl.user_id', $userid);
        $this->db->order_by('sl.created_at', 'DESC');
        $this->db->limit(100);
        $data = $this->db->get()->result();
        echo json_encode($data);
    }
}

