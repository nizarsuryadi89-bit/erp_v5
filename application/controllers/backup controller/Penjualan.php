<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Penjualan extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();
        date_default_timezone_set("Asia/Jakarta");

        if ($this->session->userdata('logged_in') !== TRUE) {
            redirect('auth');
        }
        $this->load->model('M_master');
        $this->load->model('M_transaksi');
        $this->load->model('M_penjualan');
        $this->load->model('Penjualan_manual_model', 'manual_sales');
        $this->load->library('form_validation');
        $this->load->helper('whatsapp');
    }

    public function send_invoice_wa()
    {
        header('Content-Type: application/json');
        $nospk = $this->input->post('nospk');

        $this->db->select('p.*, k.konsumen_nama, k.konsumen_nohp, pr.nama_perusahaan');
        $this->db->from('tbl_pembayaran p');
        $this->db->join('tbl_konsumen k', 'p.konsumen_id = k.konsumen_id', 'left');
        $this->db->join('tbl_perusahaan pr', 'p.perusahaan_id = pr.id_perusahaan', 'left');
        $this->db->where('p.nospk', $nospk);
        $trx = $this->db->get()->row();

        if (!$trx || empty($trx->konsumen_nohp)) {
            echo json_encode(['status' => 'error', 'message' => 'Nomor WhatsApp konsumen tidak ditemukan.']);
            return;
        }

        $nama_toko = $trx->nama_perusahaan ?: 'TGM Print';
        $total = number_format($trx->total_bayar, 0, ',', '.');

        $pesan = "*NOTA PEMBAYARAN - $nama_toko*\n\n";
        $pesan .= "Halo *" . ($trx->konsumen_nama ?: 'Pelanggan') . "*,\n";
        $pesan .= "Terima kasih telah bertransaksi di TGM Print.\n\n";
        $pesan .= "No. SPK: *$nospk*\n";
        $pesan .= "Total Tagihan: *Rp $total*\n";
        $pesan .= "Status: *" . ($trx->status_lunas == 1 ? 'LUNAS' : 'PENDING') . "*\n\n";
        $pesan .= "Detail pesanan Anda sedang diproses. Silakan simpan nota ini sebagai bukti transaksi.\n\n";
        $pesan .= "_Pesan otomatis via ERP TGM Print_";

        $response = send_wa($trx->konsumen_nohp, $pesan);

        if ($response && isset($response['status']) && $response['status'] == true) {
            echo json_encode(['status' => 'success', 'message' => 'Invoice berhasil dikirim ke ' . $trx->konsumen_nohp]);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Gagal kirim WA: ' . ($response['reason'] ?? 'Unknown Error')]);
        }
    }

    public function index()
    {
        $user_level = $this->session->userdata('level');
        $controller = $this->uri->segment(1);
        $function = $this->uri->segment(2);
        $userid = $this->session->userdata('id');
        $perusahaan_id = $this->session->userdata('perusahaan_id');

        $year = $this->input->get('year') ?: date('Y');
        $month = $this->input->get('month') ?: date('m');
        $selected_date = $this->input->get('date') ?: date('Y-m-d');

        $access_params = $this->getAccessControlParams($user_level, $userid);

        $conf = array(
            'hakakses' => $this->db->get_where('tbl_divisi', array('divisi_id' => $user_level))->row_array(),
            'perusahaan' => $this->db->get_where('tbl_perusahaan', array('id_perusahaan' => $perusahaan_id))->row_array(),
        );

        // Get company data based on filter
        $display_id = ($access_params['filter_branch']) ? $access_params['branch_id'] : null;
        $active_perusahaan = $display_id ? $this->db->get_where('tbl_perusahaan', array('id_perusahaan' => $display_id))->row_array() : null;

        $data = array(
            'controller' => $controller,
            'function' => $function,
            'title' => 'Laporan Penjualan Harian & Bulanan',
            'table' => 'tbl_pembayaran',
            'perusahaan' => $active_perusahaan['nama_perusahaan'] ?? 'SELURUH CABANG',
            'hakakses' => $conf['hakakses']['divisi_nama'],
            'logo' => $active_perusahaan['logo'] ?? $conf['perusahaan']['logo'],
            'level' => $this->db->get_where('users', array('user_id' => $userid))->row_array(),
            'menu' => $this->db->get('menu')->result(),
            'submenu' => $this->M_master->getmenu($controller)->result(),

            'selected_year' => $year,
            'selected_month' => $month,
            'selected_date' => $selected_date,
            'calendar_events' => $this->M_penjualan->getSalesCalendarData($year, $month, $access_params),

            'sales_data' => $sales_data = $this->M_penjualan->getSalesByDate($selected_date, $access_params),
            'sales_detail' => $this->M_penjualan->getSalesDetailByDate($selected_date, $access_params),
            'daily_transactions' => count($sales_data),
            'daily_sales' => array_sum(array_column($sales_data, 'total_bayar')),

            'total_sales' => $this->M_penjualan->getTotalSales($access_params),
            'total_transactions' => $this->M_penjualan->getTotalTransactions($access_params),
            'total_customers' => $this->M_penjualan->getTotalCustomers($access_params),
            'monthly_sales' => $this->M_penjualan->getMonthlySales($year, $month, $access_params),

            'unpaid_transactions' => $this->M_transaksi->getDataPemesanankasir('0', $display_id)->result(),
            'paid_transactions' => $this->M_transaksi->getbayarsukses('1', $display_id)->result(),
            'total_unpaid' => $this->M_transaksi->getDataPemesanankasir('0', $display_id)->num_rows(),
            'total_paid' => $this->M_transaksi->getbayarsukses('1', $display_id)->num_rows(),

            'user_level' => $user_level,
            'access_params' => $access_params,
            'list_perusahaan' => $this->db->get('tbl_perusahaan')->result(),
            'selected_perusahaan' => $this->input->get('perusahaan_id') ?: 'all',
        );

        $this->load_admin_view('admin/penjualan/index', $data);
    }

    private function getAccessControlParams($user_level, $userid)
    {
        $selected_perusahaan = $this->input->get('perusahaan_id') ?: $this->input->post('perusahaan_id');

        $params = array(
            'user_level' => $user_level,
            'user_id' => $userid,
            'perusahaan_id' => $this->session->userdata('perusahaan_id'),
            'filter_branch' => false,
            'branch_id' => null
        );

        // Role 1 (Super Admin), 17 (Owner), 21 (HR), 2 (Manager) have visibility permissions
        $privileged_roles = [1, 2, 17, 21];

        if (in_array($user_level, $privileged_roles)) {
            if ($selected_perusahaan && $selected_perusahaan != 'all') {
                $params['filter_branch'] = true;
                $params['branch_id'] = $selected_perusahaan;
                $params['filter_by_branch'] = true;
            } else {
                $params['filter_branch'] = false; // Show all
            }
        } else if ($user_level == 3) {
            // Kasir remains restricted to their own transactions
            $params['filter_branch'] = true;
            $params['branch_id'] = $userid;
            $params['filter_by_user'] = true;
        } else {
            // Other staff restricted to their own branch
            $params['filter_branch'] = true;
            $params['branch_id'] = $this->session->userdata('perusahaan_id');
            $params['filter_by_branch'] = true;
        }

        return $params;
    }

    public function get_sales_by_date()
    {
        if (!$this->session->userdata('logged_in')) {
            header('Content-Type: application/json');
            echo json_encode(['status' => 'error', 'message' => 'Silakan login kembali']);
            return;
        }

        header('Content-Type: application/json');

        $date = $this->input->post('date');
        if (!$date) {
            echo json_encode(['status' => 'error', 'message' => 'Tanggal tidak ditemukan']);
            return;
        }

        $user_level = $this->session->userdata('level');
        $userid = $this->session->userdata('id');
        $access_params = $this->getAccessControlParams($user_level, $userid);

        $sales_data = $this->M_penjualan->getSalesByDate($date, $access_params);
        $sales_detail = $this->M_penjualan->getSalesDetailByDate($date, $access_params);
        $total_transactions = count($sales_data);
        $total_sales = array_sum(array_column($sales_data, 'total_bayar'));

        echo json_encode([
            'status' => 'success',
            'data' => [
                'sales_data' => $sales_data,
                'sales_detail' => $sales_detail,
                'total_transactions' => $total_transactions,
                'total_sales' => $total_sales
            ]
        ]);
    }

    public function manual_list()
    {
        $controller = $this->uri->segment(1);
        $perusahaan_id = $this->session->userdata('perusahaan_id');
        $userid = $this->session->userdata('id');
        $user_level = $this->session->userdata('level');

        $conf = array(
            'perusahaan' => $this->db->get_where('tbl_perusahaan', array('id_perusahaan' => $perusahaan_id))->row_array(),
            'hakakses' => $this->db->get_where('tbl_divisi', array('divisi_id' => $user_level))->row_array(),
        );

        $filters = array(
            'search' => $this->input->get('search'),
            'status' => $this->input->get('status'),
            'payment_status' => $this->input->get('payment_status'),
            'date_from' => $this->input->get('date_from'),
            'date_to' => $this->input->get('date_to'),
            'perusahaan_id' => $perusahaan_id
        );

        // Fetch Data
        $transactions = $this->manual_sales->get_transactions($filters);
        // Note: get_transaction_stats might exist in model, adding if available
        // $stats = $this->manual_sales->get_transaction_stats($perusahaan_id);

        $data = array(
            'controller' => $controller,
            'function' => 'manual_list',
            'title' => 'Daftar Transaksi Manual',
            'perusahaan' => $conf['perusahaan']['nama_perusahaan'],
            'hakakses' => $conf['hakakses']['divisi_nama'],
            'logo' => $conf['perusahaan']['logo'],
            'level' => $this->db->get_where('users', array('user_id' => $userid))->row_array(),
            'transactions' => $transactions,
            'filters' => $filters,
            // 'stats' => $stats ?? []
        );

        $this->load_admin_view('admin/transaksi/manual_list', $data);
    }

    public function pembayaran()
    {
        $controller = $this->uri->segment(1);
        $bayar = $this->input->get('metode') ?: 'all';

        $conf = array(
            'perusahaan' => $this->db->get_where('tbl_perusahaan', array('id_perusahaan' => $this->session->userdata('perusahaan_id')))->row_array(),
            'hakakses' => $this->db->get_where('tbl_divisi', array('divisi_id' => $this->session->userdata('level')))->row_array(),
        );

        $data = array(
            'controller' => $controller,
            'function' => $this->uri->segment(2),
            'title' => 'Analisis Metode Pembayaran',
            'table' => 'tbl_penjualan',
            'tampilData' => $this->M_transaksi->getBayarmetode($bayar)->result(),
            'total_rows' => $this->M_transaksi->getBayarmetode($bayar)->num_rows(),
            'total_trx' => $this->M_transaksi->getTotalTrxmetode($bayar)->row_array(),
            'perusahaan' => $conf['perusahaan']['nama_perusahaan'],
            'hakakses' => $conf['hakakses']['divisi_nama'],
            'logo' => $conf['perusahaan']['logo'],
            'payment_methods' => $this->M_penjualan->getPaymentMethods(),
            'selected_method' => $bayar,
        );

        $this->load_admin_view('admin/penjualan/pembayaran', $data);
    }

    public function onday($bayar = null)
    {
        $controller = $this->uri->segment(1);
        $date = $this->input->get('date') ?: date('Y-m-d');
        $conf = array(
            'perusahaan' => $this->db->get_where('tbl_perusahaan', array('id_perusahaan' => $this->session->userdata('perusahaan_id')))->row_array(),
            'hakakses' => $this->db->get_where('tbl_divisi', array('divisi_id' => $this->session->userdata('level')))->row_array(),
        );
        $data = array(
            'controller' => $controller,
            'function' => $this->uri->segment(2),
            'title' => 'Laporan Penjualan Harian',
            'table' => 'tbl_penjualan',
            'tampilData' => $this->M_transaksi->getPenjualanday($bayar)->result(),
            'total_trx' => $this->M_transaksi->getTotalday()->row_array(),
            'perusahaan' => $conf['perusahaan']['nama_perusahaan'],
            'hakakses' => $conf['hakakses']['divisi_nama'],
            'logo' => $conf['perusahaan']['logo'],
            'selected_date' => $date,
            'daily_stats' => $this->M_penjualan->getDailyStats($date),
        );
        $this->load_admin_view('admin/penjualan/onday', $data);
    }

    public function best_selling()
    {

        $controller = $this->uri->segment(1);
        $period = $this->input->get('period') ?: 'month';
        $conf = array(
            'perusahaan' => $this->db->get_where('tbl_perusahaan', array('id_perusahaan' => $this->session->userdata('perusahaan_id')))->row_array(),
            'hakakses' => $this->db->get_where('tbl_divisi', array('divisi_id' => $this->session->userdata('level')))->row_array(),
        );
        $data = array(
            'controller' => $controller,
            'function' => $this->uri->segment(2),
            'title' => 'Produk Terlaris',
            'table' => 'tbl_penjualan',
            'tampilData' => $this->M_penjualan->getBestSellingProducts($period),
            'total_rows' => count($this->M_penjualan->getBestSellingProducts($period)),
            'perusahaan' => $conf['perusahaan']['nama_perusahaan'],
            'hakakses' => $conf['hakakses']['divisi_nama'],
            'logo' => $conf['perusahaan']['logo'],
            'selected_period' => $period,
            'current_perusahaan_id' => $this->session->userdata('perusahaan_id'),
            'top_products' => $this->M_penjualan->getTopProducts(10, $period),
        );
        $this->load_admin_view('admin/laporan/produk_terlaris', $data);
    }

    // public function export_sales()
    // {
    //     $user_level = $this->session->userdata('level');
    //     if ($user_level != 3) {
    //         $this->session->set_flashdata('error', 'Akses ditolak. Laporan Penjualan Kasir hanya dapat diakses oleh Kasir.');
    //         redirect('dashboard');
    //     }

    //     $user_level = $this->session->userdata('level');
    //     $userid = $this->session->userdata('id');
    //     $access_params = $this->getAccessControlParams($user_level, $userid);

    //     $sales_data = $this->M_penjualan->getSalesByDate($date, $access_params);
    //     $sales_detail = $this->M_penjualan->getSalesDetailByDate($date, $access_params);

    //     if ($format === 'pdf') {
    //         $this->export_to_pdf($sales_data, $sales_detail, $date);
    //     } else {
    //         $this->export_to_excel($sales_data, $sales_detail, $date);
    //     }
    // }

    private function export_to_excel($sales_data, $sales_detail, $date)
    {
        require_once APPPATH . 'third_party/PHPExcel/PHPExcel.php';

        $excel = new PHPExcel();
        $excel->getProperties()->setTitle("Laporan Penjualan - " . $date);

        // Sales Summary Sheet
        $excel->setActiveSheetIndex(0);
        $excel->getActiveSheet()->setTitle('Ringkasan Penjualan');

        $excel->getActiveSheet()->setCellValue('A1', 'LAPORAN PENJUALAN - ' . $date);
        $excel->getActiveSheet()->mergeCells('A1:D1');

        $excel->getActiveSheet()->setCellValue('A3', 'No');
        $excel->getActiveSheet()->setCellValue('B3', 'No. SPK');
        $excel->getActiveSheet()->setCellValue('C3', 'Customer');
        $excel->getActiveSheet()->setCellValue('D3', 'Total');
        $excel->getActiveSheet()->setCellValue('E3', 'Status');

        $row = 4;
        foreach ($sales_data as $index => $sale) {
            $excel->getActiveSheet()->setCellValue('A' . $row, $index + 1);
            $excel->getActiveSheet()->setCellValue('B' . $row, $sale->nospk);
            $excel->getActiveSheet()->setCellValue('C' . $row, $sale->konsumen_nama ?: 'Guest');
            $excel->getActiveSheet()->setCellValue('D' . $row, $sale->total_bayar);
            $excel->getActiveSheet()->setCellValue('E' . $row, $this->M_penjualan->getStatusText($sale->status_transaksi));
            $row++;
        }

        $excel->createSheet();
        $excel->setActiveSheetIndex(1);
        $excel->getActiveSheet()->setTitle('Detail Penjualan');

        $excel->getActiveSheet()->setCellValue('A1', 'DETAIL PENJUALAN - ' . $date);
        $excel->getActiveSheet()->mergeCells('A1:F1');

        $excel->getActiveSheet()->setCellValue('A3', 'No');
        $excel->getActiveSheet()->setCellValue('B3', 'No. SPK');
        $excel->getActiveSheet()->setCellValue('C3', 'Produk');
        $excel->getActiveSheet()->setCellValue('D3', 'Qty');
        $excel->getActiveSheet()->setCellValue('E3', 'Harga');
        $excel->getActiveSheet()->setCellValue('F3', 'Subtotal');

        $row = 4;
        foreach ($sales_detail as $index => $detail) {
            $excel->getActiveSheet()->setCellValue('A' . $row, $index + 1);
            $excel->getActiveSheet()->setCellValue('B' . $row, $detail->nospk);
            $excel->getActiveSheet()->setCellValue('C' . $row, $detail->product_nama ?: 'Unknown Product');
            $excel->getActiveSheet()->setCellValue('D' . $row, $detail->qty);
            $excel->getActiveSheet()->setCellValue('E' . $row, $detail->harga_1);
            $excel->getActiveSheet()->setCellValue('F' . $row, $detail->qty * $detail->harga_1);
            $row++;
        }

        $excel->setActiveSheetIndex(0);

        $filename = 'Laporan_Penjualan_' . $date . '.xlsx';
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer = PHPExcel_IOFactory::createWriter($excel, 'Excel2007');
        $writer->save('php://output');
        exit;
    }

    private function export_to_pdf($sales_data, $sales_detail, $date)
    {
        echo "PDF export functionality can be implemented here";
    }

    public function get_sales_stats()
    {
        $user_level = $this->session->userdata('level');
        if ($user_level != 3) {
            header('Content-Type: application/json');
            echo json_encode(['status' => 'error', 'message' => 'Akses ditolak.']);
            return;
        }

        header('Content-Type: application/json');
        $period = $this->input->get('period') ?: 'month';

        $user_level = $this->session->userdata('level');
        $userid = $this->session->userdata('id');
        $access_params = $this->getAccessControlParams($user_level, $userid);

        $stats = $this->M_penjualan->getSalesStatistics($period, $access_params);

        echo json_encode(['status' => 'success', 'data' => $stats]);
    }

    public function get_sales_chart()
    {
        $user_level = $this->session->userdata('level');
        if ($user_level != 3) {
            header('Content-Type: application/json');
            echo json_encode(['status' => 'error', 'message' => 'Akses ditolak.']);
            return;
        }

        header('Content-Type: application/json');
        $period = $this->input->get('period') ?: 'week';

        $user_level = $this->session->userdata('level');
        $userid = $this->session->userdata('id');
        $access_params = $this->getAccessControlParams($user_level, $userid);

        $chart_data = $this->M_penjualan->getSalesChartData($period, $access_params);

        echo json_encode(['status' => 'success', 'data' => $chart_data]);
    }

    public function get_transaction_detail($nospk)
    {
        $user_level = $this->session->userdata('level');

        header('Content-Type: application/json');

        if (!$nospk) {
            echo json_encode(['status' => 'error', 'message' => 'ID Transaksi tidak ditemukan']);
            return;
        }

        $this->db->select('p.*, k.konsumen_nama, k.konsumen_email, k.konsumen_nohp, pr.nama_perusahaan');
        $this->db->from('tbl_pembayaran p');
        $this->db->join('tbl_konsumen k', 'p.konsumen_id = k.konsumen_id', 'left');
        $this->db->join('tbl_perusahaan pr', 'p.perusahaan_id = pr.id_perusahaan', 'left');
        $this->db->where('p.nospk', $nospk);
        $transaction = $this->db->get()->row();

        if (!$transaction) {
            echo json_encode(['status' => 'error', 'message' => 'Transaksi tidak ditemukan']);
            return;
        }

        if (strpos($nospk, 'MAN-') === 0) {
            // Manual Sales use tbl_pembayaran_produk
            $this->db->select('pp.*, pr.product_nama, pr.barcode_product, hd.harga_1, s.satuan_nama');
            $this->db->from('tbl_pembayaran_produk pp');
            $this->db->join('tbl_product pr', 'pp.product_id = pr.product_id', 'left');
            $this->db->join('tbl_harga_product hd', 'pp.harga_id = hd.harga_id', 'left');
            $this->db->join('tbl_satuan s', 'pr.satuan_id = s.satuan_id', 'left');
            $this->db->where('pp.nospk', $nospk);
            $this->db->order_by('pp.id', 'ASC');
            $details = $this->db->get()->result();

            // Handle metadata price for manual POS
            foreach ($details as $d) {
                if ($d->metadata) {
                    $meta = json_decode($d->metadata, true);
                    if (isset($meta['unit_price'])) {
                        $d->harga_1 = $meta['unit_price'];
                    }
                    if (!empty($meta['product_name'])) {
                        $d->product_nama = $meta['product_name'];
                    }
                }
            }
        } else {
            // Standard SPK uses tbl_pemesanan_detail
            $this->db->select('pd.*, pr.product_nama, pr.barcode_product, hd.harga_1, s.satuan_nama');
            $this->db->from('tbl_pemesanan_detail pd');
            $this->db->join('tbl_product pr', 'pd.product_id = pr.product_id', 'left');
            $this->db->join('tbl_harga_product hd', 'pd.harga_id = hd.harga_id', 'left');
            $this->db->join('tbl_satuan s', 'pr.satuan_id = s.satuan_id', 'left');
            $this->db->where('pd.nospk', $nospk);
            $this->db->order_by('pd.id', 'ASC');
            $details = $this->db->get()->result();
        }

        $html = $this->generateTransactionDetailHTML($transaction, $details);

        echo json_encode(['status' => 'success', 'html' => $html]);
    }

    private function generateTransactionDetailHTML($transaction, $details)
    {
        $status_map = [
            '1' => ['label' => 'SUCCESS', 'class' => 'bg-success'],
            '0' => ['label' => 'PENDING', 'class' => 'bg-warning text-dark'],
        ];
        $status = $status_map[$transaction->status_lunas] ?? ['label' => 'PENDING', 'class' => 'bg-secondary'];

        $html = '
        <div class="p-2">
            <div class="text-center mb-5 pb-4 border-bottom">
                <div class="small fw-bold text-muted text-uppercase mb-2">IDENTITAS MANIFEST</div>
                <h2 class="fw-900 text-dark mb-2" style="letter-spacing: -1px;">' . $transaction->nospk . '</h2>
                <div class="d-flex justify-content-center align-items-center gap-2">
                    <span class="badge rounded-pill ' . $status['class'] . ' px-4 py-2 fw-800" style="font-size: 0.8rem;">
                        ' . $status['label'] . '
                    </span>
                    <button onclick="sendInvoiceWA(\'' . $transaction->nospk . '\')" class="btn btn-success btn-sm rounded-pill px-3 fw-bold">
                        <i class="fab fa-whatsapp me-1"></i> KIRIM NOTA WA
                    </button>
                </div>
            </div>

            <div class="row g-4">
                <div class="col-md-6">
                    <div class="p-4 rounded-4 bg-light h-100 border shadow-sm">
                        <h6 class="fw-800 text-dark mb-4 d-flex align-items-center gap-2">
                            <i class="fas fa-file-invoice text-primary"></i> DETAIL LOGISTIK
                        </h6>
                        <table class="table table-sm table-borderless mb-0" style="font-size: 0.9rem;">
                            <tr><td class="text-muted" width="40%">Tgl Transaksi</td><td class="fw-700 text-dark">' . date('d F Y', strtotime($transaction->tgl_bayar)) . '</td></tr>
                            <tr><td class="text-muted">Jam Input</td><td class="fw-700 text-dark">' . date('H:i', strtotime($transaction->jam_bayar)) . ' WIB</td></tr>
                            <tr><td class="text-muted">Metode Utama</td><td><span class="badge bg-dark px-3">' . strtoupper($transaction->metode ?? 'N/A') . '</span></td></tr>
                            <tr><td class="text-muted">Terminal</td><td class="fw-700 text-primary">' . ($transaction->nama_perusahaan ?: '-') . '</td></tr>
                        </table>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="p-4 rounded-4 bg-light h-100 border shadow-sm" style="border-left: 5px solid #27ae60 !important;">
                        <h6 class="fw-800 text-dark mb-4 d-flex align-items-center gap-2">
                            <i class="fas fa-user-circle text-success"></i> PROFIL KONSUMEN
                        </h6>
                        <div class="fw-900 text-dark fs-5 mb-1">' . ($transaction->konsumen_nama ?: 'PENGUNJUNG UMUM') . '</div>
                        <div class="text-muted small mb-3"><i class="fas fa-envelope me-2"></i>' . ($transaction->konsumen_email ?: 'N/A') . '</div>
                        <div class="d-inline-flex align-items-center gap-2 px-3 py-2 rounded-3 bg-success bg-opacity-10 text-success fw-700">
                            <i class="fas fa-phone-alt"></i> ' . ($transaction->konsumen_nohp ?: '-') . '
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-4 p-4 rounded-4 border bg-white shadow-lg">
                <h6 class="fw-800 text-dark mb-4 d-flex align-items-center gap-2">
                    <i class="fas fa-shopping-cart text-warning"></i> RINCIAN ITEM PESANAN
                </h6>
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="bg-dark text-white">
                            <tr>
                                <th class="py-3 px-4 border-0" width="5%">#</th>
                                <th class="py-3 border-0">PRODUK</th>
                                <th class="py-3 text-center border-0">QTY</th>
                                <th class="py-3 text-end border-0">HARGA</th>
                                <th class="py-3 text-end px-4 border-0">SUBTOTAL</th>
                            </tr>
                        </thead>
                        <tbody class="border-top-0">';

        if ($details) {
            foreach ($details as $index => $detail) {
                $subtotal = $detail->qty * $detail->harga_1;
                $html .= '
                <tr>
                    <td class="text-center text-muted fw-bold px-4">' . ($index + 1) . '</td>
                    <td class="py-3">
                        <div class="fw-800 text-dark">' . $detail->product_nama . '</div>
                        <div class="small text-muted fw-bold">' . ($detail->barcode_product ?: 'SKU-N/A') . '</div>
                    </td>
                    <td class="text-center">
                        <span class="badge bg-light text-dark border fw-800 px-3 py-2">' . $detail->qty . ' ' . $detail->satuan_nama . '</span>
                    </td>
                    <td class="text-end">Rp ' . number_format($detail->harga_1, 0, ',', '.') . '</td>
                    <td class="text-end fw-900 text-dark px-4">Rp ' . number_format($subtotal, 0, ',', '.') . '</td>
                </tr>';
            }
        }

        $html .= '
                        </tbody>
                        <tfoot class="bg-light fw-900">
                            <tr>
                                <td colspan="4" class="text-end py-3">SUBTOTAL BRUTO</td>
                                <td class="text-end px-4">Rp ' . number_format($transaction->sub_total, 0, ',', '.') . '</td>
                            </tr>
                            ' . ($transaction->diskon > 0 ? '<tr><td colspan="4" class="text-end text-danger py-2">POTONGAN DISKON</td><td class="text-end text-danger px-4">- Rp ' . number_format($transaction->diskon, 0, ',', '.') . '</td></tr>' : '') . '
                            ' . ($transaction->ongkir > 0 ? '<tr><td colspan="4" class="text-end text-primary py-2">BIAYA PENGIRIMAN</td><td class="text-end text-primary px-4">+ Rp ' . number_format($transaction->ongkir, 0, ',', '.') . '</td></tr>' : '') . '
                            <tr class="fs-4 text-success border-top border-dark border-opacity-10">
                                <td colspan="4" class="text-end py-4">NET REVENUE</td>
                                <td class="text-end px-4">Rp ' . number_format($transaction->total_bayar, 0, ',', '.') . '</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            <div class="mt-4 row g-3">
                <div class="col-md-12">
                     <div class="p-3 rounded-4 bg-dark text-white d-flex justify-content-between align-items-center px-4">
                        <div class="small fw-bold opacity-50"><i class="fas fa-clock me-2"></i>LOG SYSTEM TIMESTAMP</div>
                        <div class="small fw-800">' . date('d/m/Y H:i:s') . ' WIB</div>
                     </div>
                </div>
            </div>
        </div>';

        return $html;
    }

    public function delete_transaction()
    {
        header('Content-Type: application/json');
        $user_level = $this->session->userdata('level');
        $is_admin = ($user_level == 1);
        $is_owner = ($user_level == 17);

        if (!$is_admin && !$is_owner) {
            echo json_encode(['status' => 'error', 'message' => 'Akses ditolak.']);
            return;
        }

        $nospk = $this->input->post('nospk');
        if (!$nospk) {
            echo json_encode(['status' => 'error', 'message' => 'No. SPK tidak ditemukan']);
            return;
        }

        $pembayaran = $this->db->get_where('tbl_pembayaran', ['nospk' => $nospk])->row();
        if (!$pembayaran) {
            echo json_encode(['status' => 'error', 'message' => 'Transaksi tidak ditemukan']);
            return;
        }

        $this->db->trans_start();
        try {
            $pembayaran_id = $pembayaran->pembayaran_id;
            $this->db->delete('tbl_pembayaran_produk', ['nospk' => $nospk]);
            $this->db->delete('tbl_pemesanan_detail', ['nospk' => $nospk]);
            $this->db->delete('tbl_pembayaran_detail', ['pembayaran_id' => $pembayaran_id]);
            $this->db->delete('tbl_pembayaran', ['pembayaran_id' => $pembayaran_id]);
            $this->db->delete('tbl_pemesanan', ['nospk' => $nospk]);

            $this->db->trans_complete();

            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                echo json_encode(['status' => 'error', 'message' => 'Gagal menghapus transaksi.']);
                return;
            }

            echo json_encode(['status' => 'success', 'message' => 'Transaksi berhasil dihapus']);

        } catch (Exception $e) {
            $this->db->trans_rollback();
            echo json_encode(['status' => 'error', 'message' => 'Terjadi kesalahan sistem: ' . $e->getMessage()]);
        }
    }
    // ===========================================
    // Manual Sales (Migrated from Penjualan_manual)
    // ===========================================

    public function create()
    {
        // Get data from model
        $data = array(
            'controller' => 'penjualan',
            'function' => 'create',
            'title' => 'Transaksi Penjualan Manual',
            // We no longer pass $konsumen here to avoid hanging the browser with 16k records
            'konsumen' => [],
            'produk' => $this->manual_sales->get_produk(),
            'metode_pembayaran' => $this->manual_sales->get_metode_pembayaran(),
            'rekening' => $this->manual_sales->get_rekening(),
            'ewallet' => $this->manual_sales->get_ewallet(),
            'edc' => $this->manual_sales->get_edc(),
        );

        // Load admin view with common data automatically included
        $this->load_admin_view('admin/transaksi/manual', $data);
    }

    public function search_konsumen_ajax()
    {
        $term = $this->input->get('q');
        $this->db->select('konsumen_id as id, konsumen_nama as text, konsumen_nohp as phone');
        $this->db->from('tbl_konsumen');
        if (!empty($term)) {
            $this->db->group_start();
            $this->db->like('LOWER(konsumen_nama)', strtolower($term));
            $this->db->or_like('konsumen_nohp', $term);
            $this->db->group_end();
        }
        $this->db->limit(30);
        $data = $this->db->get()->result();

        // Format for Select2
        foreach ($data as $row) {
            $row->text = $row->text . " (" . ($row->phone ?: '-') . ")";
        }

        echo json_encode(['results' => $data]);
    }

    public function simpan()
    {
        // Set validation rules
        $this->form_validation->set_rules('tanggal_transaksi', 'Tanggal Transaksi', 'required');
        $this->form_validation->set_rules('konsumen_id', 'Konsumen', 'required');
        $this->form_validation->set_rules('metode_bayar', 'Metode Pembayaran', 'required');

        // Validate products array
        $products = $this->input->post('products');
        if (empty($products) || !is_array($products)) {
            $response = array(
                'status' => 'error',
                'message' => 'Tidak ada produk yang dipilih'
            );
        } else {
            // Validate each product
            $valid = true;
            foreach ($products as $index => $product) {
                if (empty($product['product_id']) || empty($product['jumlah']) || empty($product['harga_satuan'])) {
                    $valid = false;
                    break;
                }
            }

            if (!$valid) {
                $response = array(
                    'status' => 'error',
                    'message' => 'Data produk tidak lengkap'
                );
            } else {
                // Additional validation for specific payment methods
                if ($this->input->post('metode_bayar') == 'transfer') {
                    $this->form_validation->set_rules('rekening_id', 'Rekening', 'required');
                }
                if ($this->input->post('metode_bayar') == 'ewallet') {
                    $this->form_validation->set_rules('ewallet_id', 'E-Wallet', 'required');
                }
                if ($this->input->post('metode_bayar') == 'debit') {
                    $this->form_validation->set_rules('edc_id', 'EDC', 'required');
                }

                if ($this->form_validation->run() == FALSE) {
                    $response = array(
                        'status' => 'error',
                        'message' => validation_errors(),
                        'errors' => $this->form_validation->error_array()
                    );
                } else {
                    try {
                        $insert = $this->manual_sales->simpan_transaksi($this->input->post());
                        if ($insert) {
                            $response = array(
                                'status' => 'success',
                                'message' => 'Transaksi berhasil disimpan!',
                                'nospk' => $insert['nospk'],
                                'jumlah_produk' => $insert['jumlah_produk'],
                                'print_url' => base_url('penjualan/cetak_invoice/' . $insert['nospk'])
                            );
                        } else {
                            $response = array(
                                'status' => 'error',
                                'message' => 'Gagal menyimpan transaksi. Silakan coba lagi.'
                            );
                        }
                    } catch (Exception $e) {
                        $response = array(
                            'status' => 'error',
                            'message' => 'Terjadi kesalahan: ' . $e->getMessage()
                        );
                    }
                }
            }
        }

        // Return JSON response for AJAX
        if ($this->input->is_ajax_request()) {
            echo json_encode($response);
            return;
        }

        // Redirect response for non-AJAX
        if (isset($response['status']) && $response['status'] == 'success') {
            $this->session->set_flashdata('success', $response['message']);
        } else {
            $this->session->set_flashdata('error', $response['message']);
        }
        redirect('penjualan/create');
    }

    public function cetak_invoice($nospk = null)
    {
        if (!$nospk) {
            show_404();
            return;
        }

        // Get transaction data
        $transaksi = $this->manual_sales->get_transaksi_by_nospk($nospk);
        if (!$transaksi) {
            show_404();
            return;
        }

        // Get company data
        $perusahaan_id = $this->session->userdata('perusahaan_id');
        $perusahaan = $this->db->get_where('tbl_perusahaan', ['id_perusahaan' => $perusahaan_id])->row();

        $data = array(
            'transaksi' => $transaksi,
            'perusahaan' => $perusahaan,
            'nospk' => $nospk
        );

        // Load invoice view
        $this->load->view('admin/transaksi/invoice', $data);
    }
    public function manual_detail($transaction_id)
    {
        $controller = $this->uri->segment(1);
        $perusahaan_id = $this->session->userdata('perusahaan_id');
        $userid = $this->session->userdata('id');
        $user_level = $this->session->userdata('level');

        $conf = array(
            'perusahaan' => $this->db->get_where('tbl_perusahaan', array('id_perusahaan' => $perusahaan_id))->row_array(),
            'hakakses' => $this->db->get_where('tbl_divisi', array('divisi_id' => $user_level))->row_array(),
        );

        $transaction = $this->manual_sales->get_transaction_by_id($transaction_id);
        if (!$transaction) {
            $this->session->set_flashdata('error', 'Transaksi tidak ditemukan.');
            redirect('penjualan/manual_list');
        }

        $data = array(
            'controller' => $controller,
            'function' => 'manual_detail',
            'title' => 'Detail Transaksi Manual',
            'perusahaan' => $conf['perusahaan']['nama_perusahaan'],
            'hakakses' => $conf['hakakses']['divisi_nama'],
            'logo' => $conf['perusahaan']['logo'],
            'level' => $this->db->get_where('users', array('user_id' => $userid))->row_array(),
            'transaction' => (array) $transaction,
            'customer' => [
                'customer_name' => $transaction->customer_name,
                'customer_phone' => $transaction->customer_phone,
                'customer_email' => $transaction->customer_email,
                'customer_address' => $transaction->customer_address,
                'customer_type' => $transaction->customer_type ?? 'umum'
            ],
            'items' => $this->manual_sales->get_transaction_items($transaction_id),
            'payments' => $this->manual_sales->get_payment_history($transaction_id)
        );

        $this->load_admin_view('admin/transaksi/manual_detail', $data);
    }

    public function manual_delete($transaction_id)
    {
        $user_id = $this->session->userdata('id');

        // Check if transaction exists
        $transaction = $this->db->get_where('manual_transactions', array('transaction_id' => $transaction_id))->row();
        if (!$transaction) {
            $this->session->set_flashdata('error', 'Transaksi tidak ditemukan.');
            redirect('penjualan/manual_list');
        }

        // Start transaction
        $this->db->trans_start();

        try {
            // Delete related records
            $this->db->delete('manual_payment_history', array('transaction_id' => $transaction_id));
            $this->db->delete('manual_transaction_items', array('transaction_id' => $transaction_id));
            $this->db->delete('manual_transactions', array('transaction_id' => $transaction_id));

            // Log activity
            // log_activity($user_id, 'deleted', 'manual_transaction', $transaction_id);

            $this->db->trans_complete();

            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                $this->session->set_flashdata('error', 'Gagal menghapus transaksi.');
            } else {
                $this->session->set_flashdata('success', 'Transaksi berhasil dihapus.');
            }

        } catch (Exception $e) {
            $this->db->trans_rollback();
            log_message('error', 'Manual transaction delete error: ' . $e->getMessage());
            $this->session->set_flashdata('error', 'Terjadi kesalahan sistem.');
        }

        redirect('penjualan/manual_list');
    }

    public function manual_export()
    {
        // Get filter parameters (same as manual_list)
        $search = $this->input->get('search');
        $status = $this->input->get('status');
        $payment_status = $this->input->get('payment_status');
        $date_from = $this->input->get('date_from');
        $date_to = $this->input->get('date_to');
        $priority = $this->input->get('priority');

        // Build query
        $this->db->select('mt.*, mc.customer_name, mc.customer_phone, mc.customer_email');
        $this->db->from('manual_transactions mt');
        $this->db->join('manual_customers mc', 'mt.customer_id = mc.customer_id', 'left');

        // Apply filters
        if (!empty($search)) {
            $this->db->group_start();
            $this->db->like('mt.transaction_number', $search);
            $this->db->or_like('mc.customer_name', $search);
            $this->db->or_like('mc.customer_phone', $search);
            $this->db->group_end();
        }

        if (!empty($status))
            $this->db->where('mt.status', $status);
        if (!empty($payment_status))
            $this->db->where('mt.payment_status', $payment_status);
        if (!empty($date_from))
            $this->db->where('mt.transaction_date >=', $date_from);
        if (!empty($date_to))
            $this->db->where('mt.transaction_date <=', $date_to);
        if (!empty($priority))
            $this->db->where('mt.priority', $priority);

        $this->db->order_by('mt.created_at', 'DESC');
        $transactions = $this->db->get()->result();

        // Load PHPExcel library
        require_once APPPATH . 'third_party/PHPExcel/PHPExcel.php';

        $excel = new PHPExcel();
        $excel->setActiveSheetIndex(0);
        $sheet = $excel->getActiveSheet();

        // Set title
        $sheet->setTitle('Manual Transactions');

        // Set headers
        $headers = array(
            'No',
            'Transaction Number',
            'Customer Name',
            'Phone',
            'Email',
            'Transaction Date',
            'Payment Method',
            'Payment Status',
            'Priority',
            'Subtotal',
            'Discount',
            'Grand Total',
            'Sales Person',
            'Notes',
            'Created At'
        );

        $col = 'A';
        foreach ($headers as $header) {
            $sheet->setCellValue($col . '1', $header);
            $sheet->getStyle($col . '1')->getFont()->setBold(true);
            $col++;
        }

        // Add data
        $row = 2;
        foreach ($transactions as $index => $transaction) {
            $sheet->setCellValue('A' . $row, $index + 1);
            $sheet->setCellValue('B' . $row, $transaction->transaction_number);
            $sheet->setCellValue('C' . $row, $transaction->customer_name);
            $sheet->setCellValue('D' . $row, $transaction->customer_phone);
            $sheet->setCellValue('E' . $row, $transaction->customer_email);
            $sheet->setCellValue('F' . $row, $transaction->transaction_date);
            $sheet->setCellValue('G' . $row, ucfirst($transaction->payment_method));
            $sheet->setCellValue('H' . $row, ucfirst($transaction->payment_status));
            $sheet->setCellValue('I' . $row, ucfirst($transaction->priority));
            $sheet->setCellValue('J' . $row, number_format($transaction->subtotal, 0, ',', '.'));
            $sheet->setCellValue('K' . $row, number_format($transaction->discount_total, 0, ',', '.'));
            $sheet->setCellValue('L' . $row, number_format($transaction->grand_total, 0, ',', '.'));
            $sheet->setCellValue('M' . $row, $transaction->sales_person);
            $sheet->setCellValue('N' . $row, $transaction->notes);
            $sheet->setCellValue('O' . $row, $transaction->created_at);
            $row++;
        }

        // Auto size columns
        foreach (range('A', 'O') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Set filename
        $filename = 'manual_transactions_' . date('Y-m-d_H-i-s') . '.xlsx';

        // Set headers for download
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer = PHPExcel_IOFactory::createWriter($excel, 'Excel2007');
        $writer->save('php://output');
        exit;
    }
    public function get_produk_detail()
    {
        $produk_id = $this->input->post('produk_id');
        $produk = $this->manual_sales->get_produk_by_id($produk_id);

        if ($produk) {
            echo json_encode($produk);
        } else {
            echo json_encode(['error' => 'Produk tidak ditemukan']);
        }
    }

    public function get_konsumen_detail()
    {
        $konsumen_id = $this->input->post('konsumen_id');
        $konsumen = $this->manual_sales->get_konsumen_by_id($konsumen_id);

        if ($konsumen) {
            echo json_encode($konsumen);
        } else {
            echo json_encode(['error' => 'Konsumen tidak ditemukan']);
        }
    }
}
