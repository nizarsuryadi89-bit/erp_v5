<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Print_queue extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        // Load required models
        $this->load->model('Print_queue_model');
        $this->load->model('M_transaksi');
        $this->load->model('M_pembayaran');
    }

    /**
     * Display Operator Dashboard (Antrian Cetak)
     */
    public function dashboard()
    {
        $data['title'] = 'Dashboard Antrian Cetak SPK - Lantai 2';
        $data['queues'] = $this->Print_queue_model->get_all_queue(100);

        // Load custom styling and dashboard view
        $this->load->view('layout_admin/head', $data);
        $this->load->view('admin/print_queue/dashboard', $data);
        $this->load->view('layout_admin/footer');
    }

    /**
     * Add SPK to print queue
     */
    public function add_queue($nospk)
    {
        $jenis_dokumen = $this->input->get('jenis', true) ?: 'SPK';
        $printer_name = $this->input->get('printer', true) ?: 'Global';
        $jumlah_cetak = $this->input->get('jumlah', true) ?: 1;

        $result = $this->Print_queue_model->insert_queue($nospk, $jenis_dokumen, $printer_name, $jumlah_cetak);

        if ($result) {
            $response = ['status' => 'success', 'message' => "SPK #{$nospk} berhasil ditambahkan ke antrian."];
        } else {
            $response = ['status' => 'info', 'message' => "SPK #{$nospk} sudah ada di antrian pending/printing."];
        }

        echo json_encode($response);
    }

    /**
     * Get pending print queue items in JSON format (for polling / local printer client)
     */
    public function get_pending()
    {
        $pending = $this->Print_queue_model->get_pending_queue();

        // Inject PDF print URLs to make it extremely easy for the polling client
        foreach ($pending as &$item) {
            $item['print_url'] = base_url("print_queue/cetak_spk/{$item['nospk']}");
        }

        header('Content-Type: application/json');
        echo json_encode($pending);
    }

    /**
     * Mark print queue item as printing
     */
    public function mark_printing($print_id)
    {
        $result = $this->Print_queue_model->update_status($print_id, 'printing');
        echo json_encode(['status' => $result ? 'success' : 'error']);
    }

    /**
     * Mark print queue item as printed (done)
     */
    public function mark_printed($print_id)
    {
        $result = $this->Print_queue_model->update_status($print_id, 'printed');
        echo json_encode(['status' => $result ? 'success' : 'error']);
    }

    /**
     * Mark print queue item as failed with error message
     */
    public function mark_failed($print_id)
    {
        $error_message = $this->input->post('error_message', true) ?: $this->input->get('error_message', true) ?: 'Gagal mencetak (Local print client error)';
        $result = $this->Print_queue_model->update_status($print_id, 'failed', $error_message);
        echo json_encode(['status' => $result ? 'success' : 'error']);
    }

    /**
     * Get all history queue items in JSON format
     */
    public function get_history()
    {
        $history = $this->Print_queue_model->get_all_queue(100);
        header('Content-Type: application/json');
        echo json_encode($history);
    }

    /**
     * Get counts for dashboard metrics
     */
    public function get_counts()
    {
        $counts = [
            'pending' => $this->db->where('status', 'pending')->count_all_results('tbl_print_queue'),
            'printing' => $this->db->where('status', 'printing')->count_all_results('tbl_print_queue'),
            'printed' => $this->db->where('status', 'printed')->count_all_results('tbl_print_queue'),
            'failed' => $this->db->where('status', 'failed')->count_all_results('tbl_print_queue')
        ];
        header('Content-Type: application/json');
        echo json_encode($counts);
    }

    /**
     * View SPK print ticket (Endpoint to show the print layout)
     */
    public function cetak_spk($nospk)
    {
        // Decode SPK if necessary
        $nospk = urldecode($nospk);

        // Fetch transaction main info
        $pemesanan = $this->M_transaksi->getcekpesanan($nospk)->row();
        if (!$pemesanan) {
            show_error('Data SPK tidak ditemukan.', 404);
        }

        // Fetch detailed items
        $items = $this->M_transaksi->getDetailPesan($nospk)->result();

        // [NEW] Merge and fallback item metadata to product default metadata
        foreach ($items as &$item) {
            if (empty($item->metadata) && !empty($item->product_metadata)) {
                $item->metadata = $item->product_metadata;
            } else if (!empty($item->metadata) && !empty($item->product_metadata)) {
                $meta_arr = json_decode($item->metadata, true);
                $prod_meta = json_decode($item->product_metadata, true);
                if (is_array($meta_arr) && is_array($prod_meta)) {
                    $item->metadata = json_encode(array_merge($prod_meta, $meta_arr));
                }
            }
        }

        $data = [
            'title' => 'Surat Perintah Kerja (SPK) - #' . $nospk,
            'pemesanan' => $pemesanan,
            'items' => $items,
            'nospk' => $nospk
        ];

        // Load thermal/A4 optimized printable view
        $this->load->view('admin/print_queue/cetak_spk', $data);
    }
    /**
     * [NEW] Live Production Progress Board (TV Mode)
     * For Owners and Customers to see real-time WIP progress
     */
    public function progress_board()
    {
        $data['title'] = 'Live Production Progress';
        $this->load->view('admin/print_queue/progress_board', $data);
    }

    /**
     * [NEW] Fetch live production jobs for the progress board
     */
    public function get_live_progress()
    {
        // 1. Base Query
        $this->db->select('p.produksi_id, p.nospk, p.status_produksi, k.konsumen_nama, pr.product_nama, pm.created_at as tgl_spk, p.tgl_kirim_pb');
        $this->db->from('tbl_produksi p');
        $this->db->join('tbl_konsumen k', 'k.konsumen_id = p.konsumen_id', 'left');
        $this->db->join('tbl_product pr', 'pr.product_id = p.product_id', 'left');
        $this->db->join('tbl_pemesanan pm', 'pm.nospk = p.nospk', 'left');

        // 2. Filters (Paid, not given to costumer yet)
        $this->db->where('pm.status_pembayaran', 'paid');
        $this->db->group_start();
        $this->db->where('p.status_produksi !=', 'on_costumer');
        $this->db->or_where('p.status_produksi IS NULL');
        $this->db->group_end();

        // Limit results to today for completed items to avoid huge lists
        $this->db->group_start();
        $this->db->where('p.status_produksi !=', 'on_pb');
        $this->db->or_group_start();
        $this->db->where('p.tgl_kirim_pb >=', date('Y-m-d 00:00:00'));
        $this->db->where('p.tgl_kirim_pb <=', date('Y-m-d 23:59:59'));
        $this->db->group_end();
        $this->db->group_end();

        $this->db->order_by('p.produksi_id', 'DESC');
        $this->db->limit(100); // Prevent TV browser overload

        $items = $this->db->get()->result_array();

        // 3. Optional: Get current active step (for 'proses' status)
        $pids = array_column($items, 'produksi_id');
        $active_steps = [];
        if (!empty($pids)) {
            $this->db->select('dp.produksi_id, r.routing_name, dp.status_produksi, o.operator_nama');
            $this->db->from('detail_produksi dp');
            $this->db->join('routing r', 'r.routing_id = dp.routing_id', 'left');
            $this->db->join('tbl_operator o', 'o.operator_id = dp.operator_id', 'left');
            $this->db->where_in('dp.produksi_id', $pids);
            $this->db->where('dp.status_produksi', '1'); // In progress
            $steps = $this->db->get()->result_array();
            foreach ($steps as $s) {
                $active_steps[$s['produksi_id']] = [
                    'step' => $s['routing_name'] ?: 'PROSES',
                    'operator' => $s['operator_nama'] ?: 'Operator'
                ];
            }
        }

        // 4. Group data into columns
        $board = [
            'pending' => [],
            'proses' => [],
            'done' => []
        ];

        foreach ($items as $item) {
            $status = $item['status_produksi'];

            // Format Product Name (Shorten if too long)
            $prod = strtoupper($item['product_nama']);
            if (strlen($prod) > 25)
                $prod = substr($prod, 0, 22) . '...';

            // Format Consumer
            $cons = strtoupper($item['konsumen_nama']);
            if (strlen($cons) > 15)
                $cons = substr($cons, 0, 12) . '...';

            $node = [
                'id' => $item['produksi_id'],
                'spk' => $item['nospk'],
                'konsumen' => $cons,
                'product' => $prod,
                'time' => date('H:i', strtotime($item['tgl_spk'] ?: date('Y-m-d H:i:s')))
            ];

            if ($status === 'approved' || $status === 'pending' || $status === '0' || $status === '' || $status === null) {
                $board['pending'][] = $node;
            } elseif ($status === 'proses') {
                $step_info = $active_steps[$item['produksi_id']] ?? null;
                $node['step_name'] = $step_info ? strtoupper($step_info['step']) : 'PROSES PRODUKSI';
                $node['operator'] = $step_info ? strtoupper($step_info['operator']) : 'OPERATOR';
                $board['proses'][] = $node;
            } elseif ($status === 'done' || $status === 'on_pb') {
                $node['done_status'] = ($status === 'on_pb') ? 'SIAP AMBIL' : 'SELESAI CETAK';
                $board['done'][] = $node;
            }
        }

        header('Content-Type: application/json');
        echo json_encode(['status' => 'success', 'data' => $board]);
    }
}
