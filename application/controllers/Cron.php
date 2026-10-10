<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Cron extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Membersihkan file foto absensi lama
     * @param int $months Batas umur foto dalam bulan (default 3)
     */
    public function cleanup_absensi($months = 3)
    {
        $is_cli = is_cli();
        $is_admin = ($this->session->userdata('level') == '1' || $this->session->userdata('level') == '17');
        $secret_key = $this->input->get('key', true);
        $valid_key = 'TGM_CLEANUP_99X';

        // Hanya bisa diakses via CLI (cPanel Cron), Admin Session, atau Secret Key
        if (!$is_cli && !$is_admin && $secret_key !== $valid_key) {
            show_error('Unauthorized Access', 403);
            return;
        }

        // Hitung batas waktu (lebih tua dari X bulan)
        // [SECURITY FIX] Pastikan umur bulan selalu positif dan minimal 1 bulan agar tidak menghapus foto masa depan
        $months = max(1, abs((int)$months));
        $limit_date = date('Y-m-d', strtotime("-{$months} months"));
        
        $this->db->where('tanggal <', $limit_date);
        $this->db->group_start();
        $this->db->where('foto_masuk !=', '');
        $this->db->or_where('foto_pulang !=', '');
        $this->db->group_end();
        
        $absensi_lama = $this->db->get('absensi')->result();
        
        $deleted_count = 0;
        $failed_count = 0;

        foreach ($absensi_lama as $row) {
            $update_data = [];
            
            // Hapus Foto Masuk
            if (!empty($row->foto_masuk)) {
                // [SECURITY FIX] Cegah Directory Traversal dan pastikan hanya menghapus dari folder uploads
                if (strpos($row->foto_masuk, '..') === false && preg_match('/^uploads\//i', ltrim($row->foto_masuk, '/'))) {
                    $path_masuk = FCPATH . ltrim($row->foto_masuk, '/');
                    if (file_exists($path_masuk)) {
                        if (unlink($path_masuk)) {
                            $update_data['foto_masuk'] = '';
                            $deleted_count++;
                        } else {
                            $failed_count++;
                        }
                    } else {
                        $update_data['foto_masuk'] = ''; // Sudah tidak ada fisiknya
                    }
                } else {
                    $update_data['foto_masuk'] = ''; // Path tidak valid/berbahaya, abaikan file fisik
                }
            }
            
            // Hapus Foto Pulang
            if (!empty($row->foto_pulang)) {
                // [SECURITY FIX] Cegah Directory Traversal dan pastikan hanya menghapus dari folder uploads
                if (strpos($row->foto_pulang, '..') === false && preg_match('/^uploads\//i', ltrim($row->foto_pulang, '/'))) {
                    $path_pulang = FCPATH . ltrim($row->foto_pulang, '/');
                    if (file_exists($path_pulang)) {
                        if (unlink($path_pulang)) {
                            $update_data['foto_pulang'] = '';
                            $deleted_count++;
                        } else {
                            $failed_count++;
                        }
                    } else {
                        $update_data['foto_pulang'] = ''; // Sudah tidak ada fisiknya
                    }
                } else {
                    $update_data['foto_pulang'] = ''; // Path tidak valid/berbahaya, abaikan file fisik
                }
            }

            // Update database untuk mengosongkan path foto (TAPI REKAP DATA ABSEN TETAP AMAN)
            if (!empty($update_data)) {
                $this->db->where('id', $row->id);
                $this->db->update('absensi', $update_data);
            }
        }

        $msg = "Cleanup Selesai. $deleted_count foto sebelum tanggal $limit_date berhasil dihapus. Gagal dihapus: $failed_count.";
        
        if ($is_cli) {
            echo $msg . "\n";
        } elseif ($is_admin && empty($secret_key)) {
            $this->session->set_flashdata('success', $msg);
            redirect('pengaturan'); // Redirect ke halaman pengaturan/sistem terdekat
        } else {
            echo json_encode(['status' => 'success', 'message' => $msg]);
        }
    }
}
