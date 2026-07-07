<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use Mike42\Escpos\Printer;
use Mike42\Escpos\PrintConnectors\FilePrintConnector;
use Mike42\Escpos\PrintConnectors\NetworkPrintConnector;
use Mike42\Escpos\CapabilityProfile;

class Antrian extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Antrian_model');
        $this->load->model('Produksi_model');
    }

    /**
     * Public: Ambil Nomor Antrian Interface
     */
    public function index()
    {
        // Check if user logged in
        if ($this->session->userdata('logged_in') == TRUE) {
            // Jika sudah login, tampilkan dashboard
            $data = $this->data;
            $data['title'] = 'Sistem Antrian';

            // Get semua counter
            $data['counters'] = $this->Antrian_model->get_all_counter();

            // Get statistik hari ini
            $data['stats'] = $this->Antrian_model->get_statistik_harian();

            $this->load_admin_view('antrian/dashboard', $data);
        } else {
            // Jika belum login, tampilkan interface ambil nomor
            $this->ambil_nomor_interface();
        }
    }

    /**
     * Public: Display Interface Ambil Nomor (tanpa login)
     */
    public function ambil_nomor_interface()
    {
        $data['perusahaan'] = $this->db->get_where('tbl_perusahaan', ['status' => 'aktif'])->row_array();
        $data['counters'] = $this->Antrian_model->get_all_counter();

        $this->load->view('antrian/ambil_nomor', $data);
    }

    /**
     * Ambil Nomor Antrian (Public - tanpa login)
     * AJAX request
     */
    public function ambil_nomor_ajax()
    {
        if (!$this->input->is_ajax_request()) {
            show_error('Invalid request', 400);
        }

        $counter_id = $this->input->post('counter_id');

        // If counter_id not provided, choose the best available counter automatically
        if (!$counter_id) {
            $best = $this->Antrian_model->get_best_counter();
            if (!$best) {
                return $this->output
                    ->set_content_type('application/json')
                    ->set_output(json_encode([
                        'status' => 'error',
                        'message' => 'Tidak ada counter tersedia'
                    ]));
            }
            $counter_id = $best['counter_id'];
        }

        $antrian = $this->Antrian_model->ambil_nomor_antrian($counter_id);

        if ($antrian) {
            return $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'status' => 'success',
                    'data' => $antrian,
                    'message' => 'Nomor antrian berhasil diambil'
                ]));
        } else {
            return $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'status' => 'error',
                    'message' => 'Gagal mengambil nomor antrian'
                ]));
        }
    }

    /**
     * Operator - Panggil Antrian
     */
    public function operator($counter_id = null)
    {
        // Check operator access
        $level_id = $this->session->userdata('level');
        if ($level_id != 2 && $level_id != 1) { // 2 = Deskprint, 1 = Admin
            $this->session->set_flashdata('error', 'Akses ditolak');
            redirect('antrian');
        }

        $data = $this->data;
        $data['title'] = 'Operator Antrian';

        if (!$counter_id) {
            $counter_id = $this->input->post('counter_id') ?: 1;
        }

        $data['selected_counter'] = $counter_id;
        $data['counter'] = $this->db->get_where('tbl_antrian_counter', ['counter_id' => $counter_id])->row_array();
        $data['antrian_menunggu'] = $this->Antrian_model->get_antrian_menunggu($counter_id);
        $data['antrian_sekarang'] = $this->Antrian_model->get_antrian_display($counter_id);

        $this->load->view('antrian/operator', $data);
    }

    /**
     * Panggil Antrian (AJAX)
     */
    public function panggil_antrian_ajax()
    {
        if (!$this->input->is_ajax_request()) {
            show_error('Invalid request', 400);
        }

        $antrian_id = $this->input->post('antrian_id');
        $user_id = $this->session->userdata('id');

        $result = $this->Antrian_model->panggil_antrian($antrian_id, $user_id);

        return $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($result));
    }

    /**
     * Mulai Layani (AJAX)
     */
    public function mulai_layani_ajax()
    {
        if (!$this->input->is_ajax_request()) {
            show_error('Invalid request', 400);
        }

        $antrian_id = $this->input->post('antrian_id');
        $user_id = $this->session->userdata('id');

        $result = $this->Antrian_model->mulai_layani($antrian_id, $user_id);

        return $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($result));
    }

    /**
     * Selesaikan Layanan (AJAX)
     */
    public function selesai_layani_ajax()
    {
        if (!$this->input->is_ajax_request()) {
            show_error('Invalid request', 400);
        }

        $antrian_id = $this->input->post('antrian_id');
        $user_id = $this->session->userdata('id');

        $result = $this->Antrian_model->selesai_antrian($antrian_id, $user_id);

        return $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($result));
    }

    /**
     * Batalkan Antrian (AJAX)
     */
    public function batalkan_antrian_ajax()
    {
        if (!$this->input->is_ajax_request()) {
            show_error('Invalid request', 400);
        }

        $antrian_id = $this->input->post('antrian_id');
        $user_id = $this->session->userdata('id');
        $keterangan = $this->input->post('keterangan') ?: '';

        $result = $this->Antrian_model->batalkan_antrian($antrian_id, $user_id, $keterangan);

        return $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($result));
    }

    /**
     * Display Antrian (Public)
     * Untuk ditampilkan di monitor/layar
     */
    public function display($counter_id = null)
    {


        $data['counter'] = $this->db->get_where('tbl_antrian_counter', ['counter_id' => $counter_id])->row_array();
        // Fetch company logo/data
        $data['perusahaan'] = $this->db->get_where('tbl_perusahaan', ['id_perusahaan' => 1])->row_array();

        $data['antrian_sekarang'] = $this->Antrian_model->get_antrian_display($counter_id);
        $data['antrian_menunggu'] = $this->Antrian_model->get_antrian_menunggu($counter_id, 5);

        $this->load->view('antrian/display', $data);
    }

    /**
     * Get data antrian (Real-time AJAX untuk display)
     */
    public function get_data_display()
    {
        if (!$this->input->is_ajax_request()) {
            show_error('Invalid request', 400);
        }

        $counter_id = $this->input->get('counter_id') ?: 1;

        $data = [
            'counter' => $this->db->get_where('tbl_antrian_counter', ['counter_id' => $counter_id])->row_array(),
            'antrian_sekarang' => $this->Antrian_model->get_antrian_display($counter_id),
            'antrian_menunggu' => $this->Antrian_model->get_antrian_menunggu($counter_id, 5),
            'production_wip' => $this->Produksi_model->get_wip_display(10)
        ];

        return $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($data));
    }

    /**
     * Laporan Antrian
     */
    public function laporan()
    {
        $data = $this->data;
        $data['title'] = 'Laporan Antrian';

        $tanggal = $this->input->get('tanggal') ?: date('Y-m-d');
        $counter_id = $this->input->get('counter_id') ?: null;

        // Get semua counter untuk dropdown
        $data['counters'] = $this->Antrian_model->get_all_counter();
        $data['tanggal_filter'] = $tanggal;
        $data['counter_filter'] = $counter_id;

        // Query antrian sesuai filter
        $this->db->select('ta.*, tac.counter_nama, u.username')
            ->from('tbl_antrian ta')
            ->join('tbl_antrian_counter tac', 'tac.counter_id = ta.counter_id')
            ->join('users u', 'u.user_id = ta.user_id', 'left')
            ->where('DATE(ta.waktu_ambil)', $tanggal);

        if ($counter_id) {
            $this->db->where('ta.counter_id', $counter_id);
        }

        $this->db->order_by('ta.nomor_antrian', 'ASC');
        $data['antrian_list'] = $this->db->get()->result_array();

        // Get statistik
        $data['stats'] = $this->Antrian_model->get_statistik_harian($counter_id, $tanggal);

        $this->load_admin_view('antrian/laporan', $data);
    }

    /**
     * Print Karcis Antrian
     */
    public function print_karcis($antrian_id)
    {
        $antrian = $this->Antrian_model->get_antrian($antrian_id);

        if (!$antrian) {
            show_error('Antrian tidak ditemukan', 404);
        }

        $data['antrian'] = $antrian;
        $data['perusahaan'] = $this->conf['perusahaan'];

        $this->load->view('antrian/karcis', $data);
    }

    /**
     * Print Karcis Direct (Thermal Printer via PHP)
     */
    public function print_karcis_direct($antrian_id)
    {
        $antrian = $this->Antrian_model->get_antrian($antrian_id);
        if (!$antrian) {
            echo json_encode(['status' => 'error', 'message' => 'Data tidak ditemukan']);
            return;
        }

        try {
            // KONFIGURASI PRINTER DISINI
            // Ganti IP sesuai IP Printer Thermal Anda
            $printer_ip = "[IP_ADDRESS]";
            $printer_port = 9100;

            // Gunakan Network Connector untuk production
            $connector = new NetworkPrintConnector($printer_ip, $printer_port);

            $printer = new Printer($connector);

            // INITIALIZE
            $printer->initialize();
            $printer->setJustification(Printer::JUSTIFY_CENTER);

            // HEADER
            $printer->selectPrintMode(Printer::MODE_DOUBLE_WIDTH | Printer::MODE_FONT_B);
            $printer->text("TGM ERP\n");
            $printer->selectPrintMode(); // Reset
            $printer->text("Sistem Antrian Terpadu\n");
            $printer->text("--------------------------------\n");

            // BODY
            $printer->text("Nomor Antrian\n");
            $printer->feed(1);

            // CETAK NOMOR BESAR
            $printer->selectPrintMode(Printer::MODE_DOUBLE_WIDTH | Printer::MODE_DOUBLE_HEIGHT | Printer::MODE_EMPHASIZED);
            $printer->text($antrian['nomor_antrian'] . "\n");
            $printer->selectPrintMode(); // Reset

            // INFO
            $printer->feed(1);
            $printer->text("Lokasi: " . $antrian['counter_nama'] . "\n");
            $printer->text(date('d-m-Y H:i', strtotime($antrian['waktu_ambil'])) . "\n");

            // FOOTER
            $printer->feed(2);
            $printer->text("Silakan Menunggu\n");
            $printer->text("Terima Kasih\n");
            $printer->feed(3);

            // CUT
            $printer->cut();
            $printer->close();

            echo json_encode(['status' => 'success', 'message' => 'Mencetak karcis...']);

        } catch (Exception $e) {
            log_message('error', "Print Error: " . $e->getMessage());
            echo json_encode(['status' => 'error', 'message' => 'Gagal koneksi ke printer: ' . $e->getMessage()]);
        }
    }
}
