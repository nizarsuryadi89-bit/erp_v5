<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class KasirShift extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('KasirShift_model');
        $this->load->library('session');
    }

    public function index()
    {
        $user_id = $this->session->userdata('id');
        $user_level = $this->session->userdata('level');
        $is_admin = ($user_level == 1);

        $data['title'] = 'Kasir Shift Management';
        $data['user'] = $this->session->userdata();

        // Get current shift status
        // If admin, show all open shifts; if kasir, show only their shift
        if ($is_admin) {
            // Admin can see all open shifts
            $data['current_shift'] = $this->KasirShift_model->get_all_open_shifts();
            $data['shift_history'] = $this->KasirShift_model->get_all_shift_history(20);
        } else {
            // Kasir sees only their shift
            $data['current_shift'] = $this->KasirShift_model->get_current_shift($user_id);
            $data['shift_history'] = $this->KasirShift_model->get_shift_history($user_id, 10);
        }

        // Pass model to view for real-time calculations
        $data['KasirShift_model'] = $this->KasirShift_model;
        $data['is_admin'] = $is_admin;

        $this->load_admin_view('admin/kasir_shift/index', $data);
    }

    /**
     * Admin function to close shift (if kasir forgot to close)
     */
    public function admin_close_shift($shift_id)
    {
        $user_level = $this->session->userdata('level');
        $is_admin = ($user_level == 1);

        // Only admin can access this function
        if (!$is_admin) {
            $this->session->set_flashdata('error', 'Akses ditolak. Hanya admin yang dapat menutup shift kasir.');
            redirect('KasirShift');
            return;
        }

        // Get shift data
        $shift = $this->KasirShift_model->get_shift_by_id($shift_id);
        if (!$shift) {
            $this->session->set_flashdata('error', 'Shift tidak ditemukan.');
            redirect('KasirShift');
            return;
        }

        // Check if shift is already closed
        if ($shift->status == 'closed') {
            $this->session->set_flashdata('error', 'Shift sudah ditutup.');
            redirect('KasirShift');
            return;
        }

        // If POST request, process the close
        if ($this->input->method() == 'post') {
            // Get form data
            $uang_fisik_tutup = $this->input->post('uang_fisik_tutup', true);
            $total_pengeluaran = $this->input->post('total_pengeluaran', true) ?: 0;
            $catatan_tutup = $this->input->post('catatan_tutup', true) ?: 'Ditutup oleh Admin';

            // Calculate totals from transactions
            $total_penjualan_tunai = $this->KasirShift_model->get_total_cash_sales($shift_id);
            $total_penjualan_non_tunai = $this->KasirShift_model->get_total_non_cash_sales($shift_id);

            // If uang_fisik_tutup is empty, calculate automatically
            if (empty($uang_fisik_tutup) || $uang_fisik_tutup == 0) {
                $expected_cash = $shift->modal_awal + $total_penjualan_tunai - $total_pengeluaran;
                $uang_fisik_tutup = $expected_cash;
            }

            // Calculate difference
            $expected_cash = $shift->modal_awal + $total_penjualan_tunai - $total_pengeluaran;
            $selisih = $uang_fisik_tutup - $expected_cash;

            // Get admin info
            $admin_username = $this->session->userdata('username') ?: 'Admin';

            $update_data = array(
                'jam_close' => date('Y-m-d H:i:s'),
                'total_penjualan_tunai' => $total_penjualan_tunai,
                'total_penjualan_non_tunai' => $total_penjualan_non_tunai,
                'total_pengeluaran' => $total_pengeluaran,
                'uang_fisik_tutup' => $uang_fisik_tutup,
                'selisih' => $selisih,
                'status' => 'closed',
                'catatan' => ($shift->catatan ? $shift->catatan . "\n\n" : '') .
                    "--- PENUTUPAN SHIFT OLEH ADMIN ---\n" .
                    "Ditutup oleh: " . $admin_username . "\n" .
                    "Tanggal: " . date('Y-m-d H:i:s') . "\n" .
                    "Catatan: " . $catatan_tutup
            );

            if ($this->KasirShift_model->update_shift($shift_id, $update_data)) {
                $this->session->set_flashdata('success', 'Shift #' . $shift_id . ' berhasil ditutup oleh admin!');
                redirect('KasirShift');
            } else {
                $db_error = $this->db->error();
                $error_msg = 'Gagal menutup shift. Silakan coba lagi.';
                if (!empty($db_error['message'])) {
                    log_message('error', 'Admin close shift error: ' . $db_error['message']);
                }
                $this->session->set_flashdata('error', $error_msg);
            }
        }

        // Load view for admin close shift
        $data['title'] = 'Tutup Shift (Admin)';
        $data['user'] = $this->session->userdata();
        $data['shift'] = $shift;
        $data['total_cash_sales'] = $this->KasirShift_model->get_total_cash_sales($shift_id);
        $data['total_non_cash_sales'] = $this->KasirShift_model->get_total_non_cash_sales($shift_id);

        $this->load_admin_view('admin/kasir_shift/admin_close_shift', $data);
    }

    function admin_shift_detail($shift_id)
    {
        $this->KasirShift_model->shift_detail($shift_id);
        redirect('KasirShift');
    }

    public function open_shift()
    {
        $user_id = $this->session->userdata('id');

        // Check if there's already an open shift
        $current_shift = $this->KasirShift_model->get_current_shift($user_id);
        if ($current_shift) {
            $this->session->set_flashdata('error', 'Anda masih memiliki shift yang terbuka. Silakan tutup shift terlebih dahulu.');
            redirect('KasirShift');
        }

        if ($this->input->method() == 'post') {
            $this->form_validation->set_rules('modal_awal', 'Modal Awal', 'required|numeric|greater_than[0]');
            $this->form_validation->set_rules('catatan', 'Catatan', 'trim');

            if ($this->form_validation->run() == TRUE) {
                $shift_data = array(
                    'user_id' => $user_id,
                    'tanggal_shift' => date('Y-m-d'),
                    'jam_open' => date('Y-m-d H:i:s'),
                    'modal_awal' => $this->input->post('modal_awal', true),
                    'catatan' => $this->input->post('catatan', true),
                    'status' => 'open'
                );

                $shift_id = $this->KasirShift_model->create_shift($shift_data);

                if ($shift_id) {
                    $this->session->set_flashdata('success', 'Shift berhasil dibuka! Anda sekarang dapat melakukan transaksi.');
                    redirect('KasirShift');
                } else {
                    $this->session->set_flashdata('error', 'Gagal membuka shift. Silakan coba lagi.');
                }
            }
        }

        $data['title'] = 'Buka Shift Kasir';
        $data['user'] = $this->session->userdata();
        $this->load_admin_view('admin/kasir_shift/open_shift', $data);
    }

    public function close_shift()
    {
        $user_id = $this->session->userdata('id');

        // Get current shift for this user
        $current_shift = $this->KasirShift_model->get_current_shift($user_id);
        if (!$current_shift) {
            $this->session->set_flashdata('error', 'Tidak ada shift yang aktif untuk ditutup.');
            redirect('KasirShift');
        }

        if ($current_shift->status == 'closed') {
            $this->session->set_flashdata('error', 'Shift sudah ditutup.');
            redirect('KasirShift');
        }

        $this->form_validation->set_rules('uang_fisik_tutup', 'Uang Fisik Saat Tutup', 'required|numeric|greater_than[0]');
        $this->form_validation->set_rules('total_pengeluaran', 'Total Pengeluaran', 'numeric|greater_than_equal_to[0]');
        $this->form_validation->set_rules('catatan_tutup', 'Catatan', 'trim');

        // Debug: Log form validation
        log_message('debug', 'Close shift - form validation running');
        log_message('debug', 'Close shift - POST data: ' . print_r($_POST, true));

        if ($this->form_validation->run() == TRUE) {
            // Calculate totals from transactions
            $total_penjualan_tunai = $this->KasirShift_model->get_total_cash_sales($current_shift->shift_id);
            $total_penjualan_non_tunai = $this->KasirShift_model->get_total_non_cash_sales($current_shift->shift_id);
            $total_pengeluaran = $this->input->post('total_pengeluaran', true) ?: 0;
            $uang_fisik_tutup = $this->input->post('uang_fisik_tutup', true);

            // Debug: Log the values
            log_message('debug', 'Close shift - uang_fisik_tutup: ' . $uang_fisik_tutup);
            log_message('debug', 'Close shift - total_pengeluaran: ' . $total_pengeluaran);
            log_message('debug', 'Close shift - modal_awal: ' . $current_shift->modal_awal);
            log_message('debug', 'Close shift - total_penjualan_tunai: ' . $total_penjualan_tunai);

            // Calculate difference
            $expected_cash = $current_shift->modal_awal + $total_penjualan_tunai - $total_pengeluaran;
            $selisih = $uang_fisik_tutup - $expected_cash;

            $update_data = array(
                'jam_close' => date('Y-m-d H:i:s'),
                'total_penjualan_tunai' => $total_penjualan_tunai,
                'total_penjualan_non_tunai' => $total_penjualan_non_tunai,
                'total_pengeluaran' => $total_pengeluaran,
                'uang_fisik_tutup' => $uang_fisik_tutup,
                'selisih' => $selisih,
                'status' => 'closed',
                'catatan' => $current_shift->catatan . "\n\n--- PENUTUPAN SHIFT ---\n" . $this->input->post('catatan_tutup', true)
            );

            if ($this->KasirShift_model->update_shift($current_shift->shift_id, $update_data)) {
                $this->session->set_flashdata('success', 'Shift berhasil ditutup!');
                redirect('KasirShift');
            } else {
                $this->session->set_flashdata('error', 'Gagal menutup shift. Silakan coba lagi.');
            }
        }

        $data['title'] = 'Tutup Shift Kasir';
        $data['user'] = $this->session->userdata();
        $data['current_shift'] = $current_shift;
        $data['total_cash_sales'] = $this->KasirShift_model->get_total_cash_sales($current_shift->shift_id);
        $data['total_non_cash_sales'] = $this->KasirShift_model->get_total_non_cash_sales($current_shift->shift_id);

        $this->load_admin_view('admin/kasir_shift/close_shift', $data);
    }

    public function shift_detail($shift_id)
    {
        $user_id = $this->session->userdata('id');

        $shift = $this->KasirShift_model->get_shift_by_id($shift_id);
        if (!$shift || $shift->user_id != $user_id) {
            $this->session->set_flashdata('error', 'Shift tidak ditemukan.');
            redirect('KasirShift');
        }

        $data['title'] = 'Detail Shift';
        $data['user'] = $this->session->userdata();
        $data['shift'] = $shift;
        $data['transactions'] = $this->KasirShift_model->get_shift_transactions($shift_id);
        $data['total_pengeluaran'] = $this->KasirShift_model->get_total_pengeluaran($shift_id);
        $data['pengeluaran_details'] = $this->KasirShift_model->get_shift_pengeluaran_details($shift_id);

        $this->load_admin_view('admin/kasir_shift/shift_detail', $data);
    }

    public function check_shift_status()
    {
        $user_id = $this->session->userdata('id');
        $current_shift = $this->KasirShift_model->get_current_shift($user_id);

        $response = array(
            'has_open_shift' => $current_shift ? true : false,
            'shift_id' => $current_shift ? $current_shift->shift_id : null,
            'modal_awal' => $current_shift ? $current_shift->modal_awal : 0
        );

        echo json_encode($response);
    }

    public function export_shift_report($shift_id)
    {
        $user_id = $this->session->userdata('id');

        $shift = $this->KasirShift_model->get_shift_by_id($shift_id);
        if (!$shift || $shift->user_id != $user_id) {
            $this->session->set_flashdata('error', 'Shift tidak ditemukan.');
            redirect('KasirShift');
        }

        $transactions = $this->KasirShift_model->get_shift_transactions($shift_id);

        // Load PHPExcel library
        $this->load->library('excel');

        $this->excel->setActiveSheetIndex(0);
        $this->excel->getActiveSheet()->setTitle('Laporan Shift Kasir');

        // Set headers
        $this->excel->getActiveSheet()->setCellValue('A1', 'LAPORAN SHIFT KASIR');
        $this->excel->getActiveSheet()->mergeCells('A1:H1');
        $this->excel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true)->setSize(16);

        $this->excel->getActiveSheet()->setCellValue('A3', 'Shift ID: ' . $shift->shift_id);
        $this->excel->getActiveSheet()->setCellValue('A4', 'Tanggal: ' . date('d/m/Y', strtotime($shift->tanggal_shift)));
        $this->excel->getActiveSheet()->setCellValue('A5', 'Jam Buka: ' . date('d/m/Y H:i', strtotime($shift->jam_open)));
        $this->excel->getActiveSheet()->setCellValue('A6', 'Jam Tutup: ' . ($shift->jam_close ? date('d/m/Y H:i', strtotime($shift->jam_close)) : 'Belum ditutup'));

        // Summary section
        $this->excel->getActiveSheet()->setCellValue('A8', 'RINGKASAN KEUANGAN');
        $this->excel->getActiveSheet()->getStyle('A8')->getFont()->setBold(true);

        $this->excel->getActiveSheet()->setCellValue('A9', 'Modal Awal');
        $this->excel->getActiveSheet()->setCellValue('B9', number_format($shift->modal_awal, 0, ',', '.'));

        $this->excel->getActiveSheet()->setCellValue('A10', 'Total Penjualan Tunai');
        $this->excel->getActiveSheet()->setCellValue('B10', number_format($shift->total_penjualan_tunai, 0, ',', '.'));

        $this->excel->getActiveSheet()->setCellValue('A11', 'Total Penjualan Non-Tunai');
        $this->excel->getActiveSheet()->setCellValue('B11', number_format($shift->total_penjualan_non_tunai, 0, ',', '.'));

        $this->excel->getActiveSheet()->setCellValue('A12', 'Total Pengeluaran');
        $this->excel->getActiveSheet()->setCellValue('B12', number_format($shift->total_pengeluaran, 0, ',', '.'));

        $this->excel->getActiveSheet()->setCellValue('A13', 'Uang Fisik Saat Tutup');
        $this->excel->getActiveSheet()->setCellValue('B13', number_format($shift->uang_fisik_tutup, 0, ',', '.'));

        $this->excel->getActiveSheet()->setCellValue('A14', 'Selisih');
        $this->excel->getActiveSheet()->setCellValue('B14', number_format($shift->selisih, 0, ',', '.'));

        // Transactions table
        $this->excel->getActiveSheet()->setCellValue('A16', 'DETAIL TRANSAKSI');
        $this->excel->getActiveSheet()->getStyle('A16')->getFont()->setBold(true);

        $this->excel->getActiveSheet()->setCellValue('A17', 'No');
        $this->excel->getActiveSheet()->setCellValue('B17', 'SPK');
        $this->excel->getActiveSheet()->setCellValue('C17', 'Konsumen');
        $this->excel->getActiveSheet()->setCellValue('D17', 'Tanggal');
        $this->excel->getActiveSheet()->setCellValue('E17', 'Total');
        $this->excel->getActiveSheet()->setCellValue('F17', 'Metode Pembayaran');
        $this->excel->getActiveSheet()->setCellValue('G17', 'Status');

        $row = 18;
        foreach ($transactions as $index => $transaction) {
            $this->excel->getActiveSheet()->setCellValue('A' . $row, $index + 1);
            $this->excel->getActiveSheet()->setCellValue('B' . $row, $transaction->nospk);
            $this->excel->getActiveSheet()->setCellValue('C' . $row, $transaction->konsumen_nama);
            $this->excel->getActiveSheet()->setCellValue('D' . $row, date('d/m/Y H:i', strtotime($transaction->pembayaran_tgl . ' ' . $transaction->pembayaran_jam)));
            $this->excel->getActiveSheet()->setCellValue('E' . $row, number_format($transaction->grand_total, 0, ',', '.'));
            $this->excel->getActiveSheet()->setCellValue('F' . $row, $transaction->metode);
            $this->excel->getActiveSheet()->setCellValue('G' . $row, $transaction->status_transaksi == '1' ? 'Selesai' : 'Pending');
            $row++;
        }

        // Auto-size columns
        foreach (range('A', 'G') as $col) {
            $this->excel->getActiveSheet()->getColumnDimension($col)->setAutoSize(true);
        }

        // Set filename
        $filename = 'Laporan_Shift_' . $shift->shift_id . '_' . date('Y-m-d_H-i-s') . '.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel2007');
        $objWriter->save('php://output');
    }
}
