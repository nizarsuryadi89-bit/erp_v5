<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * OwnerReport Controller - Public Read-Only Report Access via Token
 *
 * This controller is PUBLIC — no MY_Controller auth required.
 * All data is locked to the token's params stored in DB.
 * Tokens are time-limited, revocable, and read-only.
 */
class OwnerReport extends CI_Controller
{
    private $report_types = [
        'legger'        => 'Buku Besar Bulanan',
        'tren_keuangan' => 'Tren Keuangan 12 Bulan',
        'aging_piutang' => 'Aging Piutang',
        'arus_kas'      => 'Laporan Arus Kas',
        'hpp_margin'    => 'Analisis HPP & Margin',
        'laba_rugi'     => 'Laporan Laba Rugi',
    ];

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
        $this->load->library('session');
        $this->load->helper(['url', 'date', 'whatsapp']);
        // Ensure report_tokens table exists
        $this->_ensure_table();
    }

    /**
     * Fallback index — redirect to hub if user happens to land here
     */
    public function index()
    {
        redirect(base_url('laporan/financial_hub'));
    }

    /**
     * Main public view endpoint
     * URL: /owner-report/{token}   OR   /tgm_erp/OwnerReport/view/{token}
     */
    public function view($token = null)
    {
        if (empty($token)) {
            $this->_show_error('Link Tidak Valid', 'Token tidak ditemukan dalam URL.');
            return;
        }

        $token = preg_replace('/[^a-zA-Z0-9]/', '', $token);

        // Load token from DB
        $record = $this->db->get_where('tbl_report_tokens', ['token' => $token])->row();

        if (!$record) {
            $this->_show_error('Link Tidak Ditemukan', 'Link laporan ini tidak ada atau sudah dihapus dari sistem.', 'invalid');
            return;
        }

        if ((int)$record->is_revoked === 1) {
            $this->_show_error('Akses Dicabut', 'Link laporan ini telah dicabut oleh administrator. Hubungi staff untuk link baru.', 'revoked');
            return;
        }

        if (strtotime($record->expires_at) < time()) {
            $this->_show_error('Link Kedaluwarsa', 'Link laporan ini sudah berakhir masa berlakunya pada ' . date('d M Y H:i', strtotime($record->expires_at)) . '.', 'expired');
            return;
        }

        // Update view count
        $this->db->where('id', $record->id)->update('tbl_report_tokens', [
            'view_count'  => (int)$record->view_count + 1,
            'last_viewed' => date('Y-m-d H:i:s'),
        ]);

        $params      = json_decode($record->params, true) ?: [];
        $report_type = $record->report_type;

        if (!array_key_exists($report_type, $this->report_types)) {
            $this->_show_error('Laporan Tidak Dikenal', "Jenis laporan tidak dikenal: $report_type");
            return;
        }

        $data                  = $this->_build_report_data($report_type, $params, $record);
        $data['token_record']  = $record;
        $data['report_type']   = $report_type;
        $data['report_title']  = $this->report_types[$report_type];
        $data['expires_at']    = $record->expires_at;

        $this->load->view('owner_report/layout', $data);
    }

    /**
     * AJAX: Generate token (called from authenticated ERP pages)
     */
    public function generate_token()
    {
        header('Content-Type: application/json');

        // Must be logged in — CI_Controller uses 'logged_in' key set by MY_Controller
        if (!$this->session->userdata('logged_in')) {
            echo json_encode(['success' => false, 'message' => 'Sesi berakhir. Silakan login ulang.']);
            return;
        }

        $report_type = $this->input->post('report_type', true);
        $params      = $this->input->post('params', true);
        $expires_in  = max(1, (int)($this->input->post('expires_in', true) ?: 3));
        $owner_nohp  = $this->input->post('owner_nohp', true);
        $send_wa     = $this->input->post('send_wa', true) === '1';

        if (!array_key_exists($report_type, $this->report_types)) {
            echo json_encode(['success' => false, 'message' => 'Jenis laporan tidak valid.']);
            return;
        }

        // Generate secure random token
        $token      = bin2hex(random_bytes(32));
        $expires_at = date('Y-m-d H:i:s', strtotime("+{$expires_in} days"));
        $params_arr = json_decode($params, true) ?: [];
        $pid        = $params_arr['perusahaan_id'] ?? $this->session->userdata('perusahaan_id');

        $this->db->insert('tbl_report_tokens', [
            'token'         => $token,
            'report_type'   => $report_type,
            'params'        => $params,
            'perusahaan_id' => $pid,
            'created_by'    => (int)($this->session->userdata('id') ?: 0),
            'expires_at'    => $expires_at,
            'is_revoked'    => 0,
            'view_count'    => 0,
            'created_at'    => date('Y-m-d H:i:s'),
        ]);

        // Build the public link — handle both route styles
        $link = base_url('OwnerReport/view/' . $token);

        $result = ['success' => true, 'token' => $token, 'link' => $link, 'expires_at' => $expires_at];

        // Send WA if requested
        if ($send_wa && !empty($owner_nohp)) {
            $p_info      = $this->db->get_where('tbl_perusahaan', ['id_perusahaan' => $pid])->row();
            $nama_co     = $p_info ? $p_info->nama_perusahaan : 'TGM Print';
            $report_name = $this->report_types[$report_type];
            $expire_str  = date('d M Y H:i', strtotime($expires_at));
            $period_str  = '';
            if (!empty($params_arr['bulan']))   { $period_str = date('F Y', strtotime($params_arr['bulan'] . '-01')); }
            if (!empty($params_arr['mulai']))   { $period_str = date('d M Y', strtotime($params_arr['mulai'])) . ' s/d ' . date('d M Y', strtotime($params_arr['selesai'] ?? date('Y-m-t'))); }

            $pesan  = "*📊 LAPORAN KEUANGAN - {$nama_co}*\n\n";
            $pesan .= "Halo, berikut laporan keuangan yang telah disiapkan:\n\n";
            $pesan .= "📋 *Jenis:* {$report_name}\n";
            if ($period_str) $pesan .= "📅 *Periode:* {$period_str}\n";
            $pesan .= "🏢 *Cabang:* {$nama_co}\n\n";
            $pesan .= "🔗 *Link Laporan:*\n{$link}\n\n";
            $pesan .= "⏳ *Berlaku hingga:* {$expire_str}\n\n";
            $pesan .= "⚠️ _Link bersifat rahasia. Tidak perlu login._\n";
            $pesan .= "_Dikirim otomatis via ERP TGM Print_";

            $wa = send_wa($owner_nohp, $pesan);
            $result['wa_sent'] = isset($wa['status']) && $wa['status'] == true;
        }

        echo json_encode($result);
    }

    /**
     * AJAX: Get owner phone number from DB (level 17 = owner)
     */
    public function get_owner_nohp()
    {
        header('Content-Type: application/json');
        if (!$this->session->userdata('logged_in')) {
            echo json_encode(['nohp' => null]);
            return;
        }
        // Try tbl_karyawan joined with users where level = 17
        $owner = $this->db->query("
            SELECT k.nohp FROM users u
            LEFT JOIN tbl_karyawan k ON k.karyawan_id = u.karyawan_id
            WHERE u.level = 17 LIMIT 1
        ")->row();
        if (!$owner || empty($owner->nohp)) {
            // Fallback: check tbl_wa_setting for owner_nohp field
            $setting = $this->db->get_where('tbl_wa_setting', ['id' => 1])->row();
            $nohp    = $setting ? ($setting->owner_nohp ?? null) : null;
        } else {
            $nohp = $owner->nohp;
        }
        echo json_encode(['nohp' => $nohp]);
    }

    /**
     * AJAX: Revoke a token
     */
    public function revoke_token()
    {
        header('Content-Type: application/json');
        if (!$this->session->userdata('logged_in')) {
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            return;
        }
        $token = $this->input->post('token', true);
        $this->db->where('token', $token)->update('tbl_report_tokens', ['is_revoked' => 1]);
        echo json_encode(['success' => true]);
    }

    /**
     * Management page - list all tokens (requires auth, uses admin layout)
     */
    public function token_list()
    {
        if (!$this->session->userdata('logged_in')) {
            redirect('auth/login');
            return;
        }
        $records = $this->db->query("
            SELECT rt.*, u.username, p.nama_perusahaan
            FROM tbl_report_tokens rt
            LEFT JOIN users u ON u.user_id = rt.created_by
            LEFT JOIN tbl_perusahaan p ON p.id_perusahaan = rt.perusahaan_id
            ORDER BY rt.created_at DESC LIMIT 100
        ")->result();

        $data = [
            'title'        => 'Manajemen Report Link',
            'controller'   => 'OwnerReport',
            'function'     => 'token_list',
            'records'      => $records,
            'report_types' => $this->report_types,
        ];
        $this->load->view('layout_admin/head', $data);
        $this->load->view('layout_admin/sidebar');
        $this->load->view('admin/laporan/token_management', $data);
        $this->load->view('layout_admin/footer');
    }

    // ================================================================
    // PRIVATE: Build report data per type
    // ================================================================
    private function _build_report_data($type, $params, $record)
    {
        $pid     = $params['perusahaan_id'] ?? 1;
        $ids     = $this->_get_ids_public($pid);
        $ids_sql = implode(',', $ids);
        $p_info  = $this->db->get_where('tbl_perusahaan', ['id_perusahaan' => $pid])->row();

        $data = [
            'perusahaan_id' => $pid,
            'perusahaan'    => $p_info ? $p_info->nama_perusahaan : 'TGM Print',
            'params'        => $params,
        ];

        switch ($type) {
            case 'legger':
                $mulai   = $params['mulai']   ?? date('Y-m-01');
                $selesai = $params['selesai'] ?? date('Y-m-t');
                $legger  = $this->db->query("
                    SELECT tgl_bayar,
                        SUM(CASE WHEN pd.metode='tunai'    THEN pd.jumlah ELSE 0 END) as total_cash,
                        SUM(CASE WHEN pd.metode='transfer' THEN pd.jumlah ELSE 0 END) as total_transfer,
                        SUM(CASE WHEN pd.metode='debit'    THEN pd.jumlah ELSE 0 END) as total_edc,
                        SUM(CASE WHEN pd.metode='ewallet'  THEN pd.jumlah ELSE 0 END) as total_ewallet,
                        SUM(CASE WHEN p.status_lunas=0     THEN (p.total_tagihan-p.total_bayar) ELSE 0 END) as total_piutang,
                        SUM(p.total_tagihan) as total_harian
                    FROM tbl_pembayaran p
                    LEFT JOIN tbl_pembayaran_detail pd ON pd.pembayaran_id=p.pembayaran_id
                    WHERE p.perusahaan_id IN ($ids_sql)
                      AND p.tgl_bayar BETWEEN '$mulai' AND '$selesai'
                    GROUP BY tgl_bayar ORDER BY tgl_bayar ASC
                ")->result();
                $data = array_merge($data, [
                    'legger'     => $legger,
                    'mulai'      => $mulai,
                    'selesai'    => $selesai,
                    'grandtotal' => array_sum(array_column($legger, 'total_harian')),
                    'cash'       => array_sum(array_column($legger, 'total_cash')),
                    'transfer'   => array_sum(array_column($legger, 'total_transfer')),
                    'edc'        => array_sum(array_column($legger, 'total_edc')),
                    'ewallet'    => array_sum(array_column($legger, 'total_ewallet')),
                    'piutang'    => array_sum(array_column($legger, 'total_piutang')),
                ]);
                break;

            case 'tren_keuangan':
                $tren = [];
                for ($i = 11; $i >= 0; $i--) {
                    $bulan = date('Y-m', strtotime("-$i months"));
                    $pend  = $this->db->query("SELECT SUM(total_tagihan) as total FROM tbl_pembayaran WHERE DATE_FORMAT(tgl_bayar,'%Y-%m')='$bulan' AND perusahaan_id IN ($ids_sql)")->row()->total ?? 0;
                    $peng  = $this->db->query("SELECT SUM(pengeluaran_jumlah) as total FROM tbl_pengeluaran WHERE DATE_FORMAT(pengeluaran_tgl,'%Y-%m')='$bulan' AND perusahaan_id IN ($ids_sql)")->row()->total ?? 0;
                    $tren[] = ['bulan' => $bulan, 'total_pendapatan' => (float)$pend, 'total_pengeluaran' => (float)$peng];
                }
                $data['tren_data'] = $tren;
                break;

            case 'aging_piutang':
                $data['aging_summary'] = $this->db->query("
                    SELECT
                        SUM(CASE WHEN DATEDIFF(CURDATE(),tgl_bayar)<=15 THEN (total_tagihan-total_bayar) ELSE 0 END) as age_0_15,
                        SUM(CASE WHEN DATEDIFF(CURDATE(),tgl_bayar) BETWEEN 16 AND 30 THEN (total_tagihan-total_bayar) ELSE 0 END) as age_16_30,
                        SUM(CASE WHEN DATEDIFF(CURDATE(),tgl_bayar) BETWEEN 31 AND 60 THEN (total_tagihan-total_bayar) ELSE 0 END) as age_31_60,
                        SUM(CASE WHEN DATEDIFF(CURDATE(),tgl_bayar)>60 THEN (total_tagihan-total_bayar) ELSE 0 END) as age_over_60
                    FROM tbl_pembayaran WHERE status_lunas='0' AND perusahaan_id IN ($ids_sql)
                ")->row();
                $data['aging_detail'] = $this->db->query("
                    SELECT p.nospk, p.tgl_bayar, p.total_tagihan, p.total_bayar,
                           k.konsumen_nama, k.konsumen_nohp,
                           DATEDIFF(CURDATE(), p.tgl_bayar) as umur_hari
                    FROM tbl_pembayaran p
                    LEFT JOIN tbl_konsumen k ON p.konsumen_id = k.konsumen_id
                    WHERE p.status_lunas='0' AND p.perusahaan_id IN ($ids_sql)
                    ORDER BY umur_hari DESC
                ")->result();
                break;

            case 'arus_kas':
                $mulai   = $params['mulai']   ?? date('Y-m-01');
                $selesai = $params['selesai'] ?? date('Y-m-t');
                $mt  = (float)($this->db->query("SELECT SUM(pd.jumlah) as t FROM tbl_pembayaran_detail pd JOIN tbl_pembayaran p ON pd.pembayaran_id=p.pembayaran_id WHERE DATE(pd.created_at) BETWEEN '$mulai' AND '$selesai' AND p.perusahaan_id IN ($ids_sql) AND pd.metode='tunai'")->row()->t ?? 0);
                $mtf = (float)($this->db->query("SELECT SUM(pd.jumlah) as t FROM tbl_pembayaran_detail pd JOIN tbl_pembayaran p ON pd.pembayaran_id=p.pembayaran_id WHERE DATE(pd.created_at) BETWEEN '$mulai' AND '$selesai' AND p.perusahaan_id IN ($ids_sql) AND pd.metode='transfer'")->row()->t ?? 0);
                $md  = (float)($this->db->query("SELECT SUM(pd.jumlah) as t FROM tbl_pembayaran_detail pd JOIN tbl_pembayaran p ON pd.pembayaran_id=p.pembayaran_id WHERE DATE(pd.created_at) BETWEEN '$mulai' AND '$selesai' AND p.perusahaan_id IN ($ids_sql) AND pd.metode='debit'")->row()->t ?? 0);
                $me  = (float)($this->db->query("SELECT SUM(pd.jumlah) as t FROM tbl_pembayaran_detail pd JOIN tbl_pembayaran p ON pd.pembayaran_id=p.pembayaran_id WHERE DATE(pd.created_at) BETWEEN '$mulai' AND '$selesai' AND p.perusahaan_id IN ($ids_sql) AND pd.metode='ewallet'")->row()->t ?? 0);
                $kt  = (float)($this->db->query("SELECT SUM(pengeluaran_jumlah) as t FROM tbl_pengeluaran WHERE pengeluaran_tgl BETWEEN '$mulai' AND '$selesai' AND perusahaan_id IN ($ids_sql)")->row()->t ?? 0);
                $tm  = $mt + $mtf + $md + $me;
                $data = array_merge($data, [
                    'mulai'          => $mulai,   'selesai'        => $selesai,
                    'masuk_tunai'    => $mt,       'masuk_transfer' => $mtf,
                    'masuk_debit'    => $md,       'masuk_ewallet'  => $me,
                    'total_masuk'    => $tm,       'total_keluar'   => $kt,
                    'net_arus_kas'   => $tm - $kt,
                ]);
                break;

            case 'laba_rugi':
                $bulan = $params['bulan'] ?? date('Y-m');
                $lr    = $this->db->query("
                    SELECT SUM(a.qty*(b.harga_1-b.hpp)) AS laba,
                           SUM(a.qty*b.harga_1) AS total_pendapatan,
                           SUM(a.qty*b.hpp) AS total_hpp
                    FROM tbl_pemesanan_detail a
                    INNER JOIN tbl_harga_product b ON b.harga_id=a.harga_id
                    INNER JOIN tbl_pembayaran c ON a.nospk=c.nospk
                    WHERE c.perusahaan_id IN ($ids_sql) AND DATE_FORMAT(c.tgl_bayar,'%Y-%m')='$bulan'
                ")->row();
                $peng  = (float)($this->db->query("SELECT SUM(pengeluaran_jumlah) as t FROM tbl_pengeluaran WHERE perusahaan_id IN ($ids_sql) AND DATE_FORMAT(pengeluaran_tgl,'%Y-%m')='$bulan'")->row()->t ?? 0);
                $data  = array_merge($data, [
                    'laba_rugi'   => $lr,
                    'pengeluaran' => $peng,
                    'bulan'       => $bulan,
                    'laba_bersih' => ((float)($lr->laba ?? 0)) - $peng,
                ]);
                break;
        }

        return $data;
    }

    private function _get_ids_public($perusahaan_id)
    {
        if ($perusahaan_id == 1 || empty($perusahaan_id)) {
            $companies = $this->db->select('id_perusahaan')->get_where('tbl_perusahaan', ['id_perusahaan >=' => 1])->result();
            $ids = array();
            foreach ($companies as $c) { $ids[] = $c->id_perusahaan; }
            return !empty($ids) ? $ids : [1, 2, 3];
        }
        return [$perusahaan_id];
    }

    private function _show_error($title, $message, $type = 'invalid')
    {
        $data = ['err_title' => $title, 'err_message' => $message, 'err_type' => $type];
        $this->load->view('owner_report/error', $data);
    }

    /**
     * Create tbl_report_tokens if it doesn't exist yet.
     * Silently skips if DB user lacks CREATE TABLE privilege (shared hosting).
     * In that case, run the migration SQL manually via phpMyAdmin.
     */
    private function _ensure_table()
    {
        try {
            $exists = $this->db->query(
                "SHOW TABLES LIKE 'tbl_report_tokens'"
            )->num_rows() > 0;

            if (!$exists) {
                $this->db->query("
                    CREATE TABLE IF NOT EXISTS tbl_report_tokens (
                        id            INT AUTO_INCREMENT PRIMARY KEY,
                        token         VARCHAR(64) UNIQUE NOT NULL,
                        report_type   VARCHAR(50) NOT NULL,
                        params        TEXT NOT NULL,
                        perusahaan_id INT NOT NULL DEFAULT 1,
                        created_by    INT NOT NULL DEFAULT 0,
                        expires_at    DATETIME NOT NULL,
                        is_revoked    TINYINT(1) DEFAULT 0,
                        view_count    INT DEFAULT 0,
                        last_viewed   DATETIME NULL,
                        created_at    DATETIME DEFAULT CURRENT_TIMESTAMP,
                        INDEX idx_token   (token),
                        INDEX idx_expires (expires_at),
                        INDEX idx_pid     (perusahaan_id)
                    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
                ");
            }
        } catch (Exception $e) {
            log_message('error', '[OwnerReport] _ensure_table failed: ' . $e->getMessage());
        }
    }
}

