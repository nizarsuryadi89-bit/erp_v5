<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Api extends MY_Controller {

    private $secret_key = 'RahasiaNegara123!@#';

    public function __construct() {
        parent::__construct();
        $this->load->model('Absensi_model');
        
        $api_key = $this->input->get_request_header('X-API-KEY');
        if (!$api_key) {
            $api_key = $this->input->get_post('api_key');
        }

        if ($api_key !== $this->secret_key) {
            $this->output->set_status_header(403);
            echo json_encode(['error' => 'API Key salah atau akses ditolak.']);
            exit;
        }
    }

    public function karyawan_get() {
        $data = $this->Absensi_model->get_all();
        echo json_encode($data);
    }

    public function absen_post() {
        $input = json_decode(file_get_contents("php://input"), true);
        if (!$input || empty($input['karyawan_id']) || empty($input['tanggal'])) {
            $this->output->set_status_header(400);
            echo json_encode(['error' => 'Data JSON tidak valid atau tidak lengkap (butuh karyawan_id dan tanggal)']);
            return;
        }

        $result = $this->Absensi_model->simpan_absen($input);
        echo json_encode(['success' => $result]);
    }
    
    public function notifications() {
        try {
            // Sample notifications data
            $notifications = [
                [
                    'id' => 1,
                    'title' => 'Transaksi Baru',
                    'message' => 'Pesanan #ORD-001 telah masuk dari deskprint',
                    'time' => '2 menit yang lalu',
                    'unread' => true,
                    'type' => 'transaction'
                ],
                [
                    'id' => 2,
                    'title' => 'Pembayaran Diterima',
                    'message' => 'Pembayaran untuk pesanan #ORD-002 telah dikonfirmasi',
                    'time' => '15 menit yang lalu',
                    'unread' => true,
                    'type' => 'payment'
                ],
                [
                    'id' => 3,
                    'title' => 'Stok Menipis',
                    'message' => 'Produk "Laptop Gaming" stok tersisa 5 unit',
                    'time' => '1 jam yang lalu',
                    'unread' => false,
                    'type' => 'stock'
                ]
            ];
            
            $unread_count = count(array_filter($notifications, function($n) {
                return $n['unread'];
            }));
            
            $response = [
                'status' => 'success',
                'notifications' => $notifications,
                'unread_count' => $unread_count
            ];
            
        } catch (Exception $e) {
            $response = [
                'status' => 'error',
                'message' => 'Gagal memuat notifikasi: ' . $e->getMessage(),
                'notifications' => [],
                'unread_count' => 0
            ];
        }
        
        // Ensure clean output
        ob_clean();
        $this->output->set_content_type('application/json');
        echo json_encode($response);
        exit;
    }
    
    public function notifications_update() {
        try {
            $notification_id = $this->input->post('notification_id', true);
            
            if (!$notification_id) {
                throw new Exception('ID notifikasi tidak valid');
            }
            
            // Here you would typically update the notification status in database
            // For now, just return success
            
            $response = [
                'status' => 'success',
                'message' => 'Notifikasi berhasil diupdate'
            ];
            
        } catch (Exception $e) {
            $response = [
                'status' => 'error',
                'message' => 'Gagal update notifikasi: ' . $e->getMessage()
            ];
        }
        
        // Ensure clean output
        ob_clean();
        $this->output->set_content_type('application/json');
        echo json_encode($response);
        exit;
    }
}
