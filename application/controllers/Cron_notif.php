<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Cron_notif extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->library('firebase_cloud_messaging');
    }

    public function absen_masuk() {
        // Find employees who haven't clocked in today
        $tanggal = date('Y-m-d');
        
        $this->db->select('k.karyawan_id, k.fcm_token, k.nama_lengkap');
        $this->db->from('tbl_karyawan k');
        $this->db->where('k.status_karyawan !=', 'Berhenti'); // Assuming 'status_karyawan' instead of 'status' as 'status' column doesn't exist
        $this->db->where("k.fcm_token IS NOT NULL AND k.fcm_token != ''", null, false);
        $this->db->where("NOT EXISTS (SELECT 1 FROM absensi a WHERE a.karyawan_id = k.karyawan_id AND a.tanggal = '$tanggal')", null, false);
        
        $karyawan = $this->db->get()->result();
        
        $count = 0;
        foreach ($karyawan as $k) {
            $title = "Waktunya Absen Masuk!";
            $body = "Halo {$k->nama_lengkap}, jangan lupa untuk melakukan absen masuk hari ini ya.";
            
            if ($this->firebase_cloud_messaging->send_notification($k->fcm_token, $title, $body)) {
                $count++;
            }
        }
        
        $this->_success_view($count, 'Masuk');
    }

    public function absen_pulang() {
        // Find employees who clocked in today but haven't clocked out
        $tanggal = date('Y-m-d');
        
        $this->db->select('k.karyawan_id, k.fcm_token, k.nama_lengkap');
        $this->db->from('tbl_karyawan k');
        $this->db->join('absensi a', 'a.karyawan_id = k.karyawan_id');
        $this->db->where('k.status_karyawan !=', 'Berhenti');
        $this->db->where('a.tanggal', $tanggal);
        $this->db->where("(a.jam_pulang IS NULL OR a.jam_pulang = '00:00:00')", null, false);
        $this->db->where("k.fcm_token IS NOT NULL AND k.fcm_token != ''", null, false);
        
        $karyawan = $this->db->get()->result();
        
        $count = 0;
        foreach ($karyawan as $k) {
            $title = "Waktunya Absen Pulang!";
            $body = "Halo {$k->nama_lengkap}, waktunya pulang! Jangan lupa untuk absen pulang ya.";
            
            if ($this->firebase_cloud_messaging->send_notification($k->fcm_token, $title, $body)) {
                $count++;
            }
        }
        
        $this->_success_view($count, 'Pulang');
    }

    private function _success_view($count, $type) {
        $url = base_url('absensi');
        echo '<!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Broadcast Success</title>
            <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;800&display=swap" rel="stylesheet">
            <style>
                body {
                    margin: 0;
                    padding: 0;
                    display: flex;
                    justify-content: center;
                    align-items: center;
                    height: 100vh;
                    background-color: #f8fafc;
                    font-family: \'Inter\', sans-serif;
                }
                .card {
                    background: white;
                    padding: 3rem;
                    border-radius: 24px;
                    box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
                    text-align: center;
                    max-width: 400px;
                    width: 90%;
                    animation: slideUp 0.5s ease-out forwards;
                }
                .icon-container {
                    width: 80px;
                    height: 80px;
                    background: #d1fae5;
                    border-radius: 50%;
                    display: flex;
                    justify-content: center;
                    align-items: center;
                    margin: 0 auto 1.5rem;
                    color: #059669;
                    font-size: 40px;
                    animation: popIn 0.5s cubic-bezier(0.175, 0.885, 0.32, 1.275) 0.2s forwards;
                    transform: scale(0);
                }
                h2 { margin: 0 0 10px; color: #1e293b; font-weight: 800; font-size: 1.5rem; }
                p { margin: 0 0 20px; color: #64748b; line-height: 1.5; font-size: 1rem; }
                .btn {
                    display: inline-block;
                    background: #2563eb;
                    color: white;
                    text-decoration: none;
                    padding: 12px 24px;
                    border-radius: 12px;
                    font-weight: 600;
                    transition: all 0.3s;
                }
                .btn:hover { background: #1d4ed8; transform: translateY(-2px); box-shadow: 0 10px 15px -3px rgba(37, 99, 235, 0.3); }
                .progress-bar {
                    width: 100%; height: 4px; background: #e2e8f0; border-radius: 2px; margin-top: 20px; overflow: hidden;
                }
                .progress-fill {
                    height: 100%; background: #2563eb; width: 100%;
                    animation: progress 3s linear forwards;
                }
                @keyframes slideUp { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
                @keyframes popIn { from { transform: scale(0); } to { transform: scale(1); } }
                @keyframes progress { from { width: 0%; } to { width: 100%; } }
            </style>
            <script>
                setTimeout(function() { window.location.href = \''.$url.'\'; }, 3000);
            </script>
        </head>
        <body>
            <div class="card">
                <div class="icon-container">✓</div>
                <h2>Berhasil!</h2>
                <p>Telah mengirim <b>'.$count.'</b> notifikasi reminder Absen '.$type.' ke perangkat karyawan.</p>
                <a href="'.$url.'" class="btn">Kembali Sekarang</a>
                <div class="progress-bar"><div class="progress-fill"></div></div>
            </div>
        </body>
        </html>';
    }
}
