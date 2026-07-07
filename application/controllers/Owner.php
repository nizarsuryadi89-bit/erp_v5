<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Owner extends CI_Controller
{

    // Token statis untuk demo/kemudahan. Sebaiknya diganti atau disimpan di DB di production.
    private $SECRET_TOKEN = 'TGM2026';

    public function __construct()
    {
        parent::__construct();
        // Set timezone to Indonesia (WIB) so that date/time operations are real-time
        date_default_timezone_set('Asia/Jakarta');

        $this->load->database();
        $this->load->helper(['url', 'date']);
        $this->load->library('session');
        $this->load->model([
            'M_laporan',
            'M_pembayaran',
            'M_hutang',
            'M_penjualan',
            'M_transaksi',
            'M_master'
        ]);
    }

    /**
     * Render view dalam layout owner_mobile
     */
    private function _owner_view($token, $view, $data)
    {
        $this->load->view('owner_mobile/layout', $data);
        $this->load->view($view, $data);
        $this->load->view('owner_mobile/footer', $data);
    }

    /**
     * Build common data array for owner report pages
     */
    private function _owner_data($token, $perusahaan_id, $title)
    {
        $perusahaan = $this->db->get_where('tbl_perusahaan', ['id_perusahaan' => $perusahaan_id])->row();
        return [
            'token' => $token,
            'perusahaan_id' => $perusahaan_id,
            'perusahaan' => $perusahaan ? $perusahaan->nama_perusahaan : 'PT. Trigemilang Mandiri',
            'all_perusahaan' => $this->db->get_where('tbl_perusahaan', ['status' => '1'])->result(),
            'title' => $title,
        ];
    }

    private function _check_token($token = null)
    {
        $login_time = $this->session->userdata('owner_login_time');
        $session_duration = 3600; // 1 jam = 3600 detik

        if (!$this->session->userdata('owner_logged_in') || !$login_time || (time() - $login_time) > $session_duration) {
            $this->session->unset_userdata('owner_logged_in');
            $this->session->unset_userdata('owner_login_time');
            redirect('Owner/login');
            exit;
        }
    }

    public function login()
    {
        $login_time = $this->session->userdata('owner_login_time');
        $session_duration = 3600;

        if ($this->session->userdata('owner_logged_in') && $login_time && (time() - $login_time) <= $session_duration) {
            redirect('Owner/dashboard');
        }
        $this->load->view('owner_mobile/login');
    }

    public function verify_pin()
    {
        $pin = $this->input->post('pin');

        // Ambil PIN dari tbl_karyawan yang terhubung dengan akun users dengan role_id = 17 (Owner)
        $this->db->select('k.nama_lengkap, u.username');
        $this->db->from('tbl_karyawan k');
        $this->db->join('users u', 'u.karyawan_id = k.karyawan_id');
        $this->db->where('k.pin_produksi', $pin);
        $this->db->where('u.role_id', 17); // Owner Role
        $owner = $this->db->get()->row();

        if ($owner) {
            $this->session->set_userdata('owner_logged_in', true);
            $this->session->set_userdata('owner_login_time', time());
            echo json_encode(['status' => 'success']);
        } else {
            echo json_encode(['status' => 'error', 'msg' => 'PIN salah atau Anda tidak memiliki akses Owner. Coba lagi.']);
        }
    }

    public function logout()
    {
        $this->session->unset_userdata('owner_logged_in');
        $this->session->unset_userdata('owner_login_time');
        $this->session->unset_userdata('is_owner_proxy');
        redirect('Owner/login');
    }

    public function index()
    {
        show_404();
    }

    private function _get_ids($perusahaan_id)
    {
        if ($perusahaan_id == 1 || empty($perusahaan_id) || $perusahaan_id === 'all') {
            $companies = $this->db->select('id_perusahaan')->get_where('tbl_perusahaan', ['status' => '1'])->result();
            $ids = [];
            foreach ($companies as $c) {
                $ids[] = $c->id_perusahaan;
            }
            return !empty($ids) ? $ids : [1, 2, 3];
        }
        return [(int) $perusahaan_id];
    }

    public function dashboard($token = null)
    {
        $this->_check_token($token);

        $perusahaan_id = $this->input->get('perusahaan_id', true) ?: 1;

        $hariini = $this->input->get('tanggal', true) ?: date('Y-m-d');
        $bulanini = date('Y-m', strtotime($hariini));
        $start_date = $bulanini . '-01';
        $end_date = date('Y-m-t', strtotime($hariini));

        $ids = $this->_get_ids($perusahaan_id);
        $ids_sql = implode(',', $ids);

        // Total Pendapatan / Omset Hari Ini
        $omset_hariini = $this->db->query("
            SELECT SUM(total_tagihan) as total 
            FROM tbl_pembayaran 
            WHERE tgl_bayar = ? AND perusahaan_id IN ($ids_sql)
        ", [$hariini])->row()->total ?? 0;

        // Total Pendapatan / Omset Bulan Ini
        $omset_bulanini = $this->db->query("
            SELECT SUM(total_tagihan) as total 
            FROM tbl_pembayaran 
            WHERE DATE_FORMAT(tgl_bayar,'%Y-%m') = ? AND perusahaan_id IN ($ids_sql)
        ", [$bulanini])->row()->total ?? 0;

        // Uang Masuk Fisik (Cash) & Non-Tunai Hari Ini
        $kas_hariini = $this->db->query("
            SELECT 
                SUM(CASE WHEN pd.metode = 'tunai' THEN pd.jumlah ELSE 0 END) as tunai,
                SUM(CASE WHEN pd.metode != 'tunai' THEN pd.jumlah ELSE 0 END) as non_tunai
            FROM tbl_pembayaran_detail pd
            JOIN tbl_pembayaran p ON pd.pembayaran_id = p.pembayaran_id
            WHERE DATE(pd.created_at) = ? AND p.perusahaan_id IN ($ids_sql)
        ", [$hariini])->row_array();

        // Pengeluaran Hari Ini
        $pengeluaran_hariini = $this->db->query("
            SELECT 
                SUM(pengeluaran_jumlah) as total,
                SUM(CASE WHEN metode_bayar = 'tunai' THEN pengeluaran_jumlah ELSE 0 END) as tunai
            FROM tbl_pengeluaran 
            WHERE pengeluaran_tgl = ? AND perusahaan_id IN ($ids_sql)
        ", [$hariini])->row_array();

        $saldo_kas = ($kas_hariini['tunai'] ?? 0) - ($pengeluaran_hariini['tunai'] ?? 0);

        // Piutang Baru Hari Ini
        $piutang_hariini = $this->db->query("
            SELECT SUM(total_tagihan - total_bayar) as total 
            FROM tbl_pembayaran 
            WHERE tgl_bayar = ? AND perusahaan_id IN ($ids_sql) AND status_lunas = '0'
        ", [$hariini])->row()->total ?? 0;

        // Total Piutang Belum Lunas Keseluruhan
        $total_piutang_outstanding = $this->db->query("
            SELECT SUM(total_tagihan - total_bayar) as total 
            FROM tbl_pembayaran 
            WHERE status_lunas = '0' AND perusahaan_id IN ($ids_sql)
        ")->row()->total ?? 0;

        // Laba Kotor Bulan Ini
        $laba_rugi = $this->db->query("
            SELECT SUM(a.qty*(b.harga_1-b.hpp)) AS laba
            FROM tbl_pemesanan_detail a
            INNER JOIN tbl_harga_product b ON b.harga_id=a.harga_id
            INNER JOIN tbl_pembayaran c ON a.nospk=c.nospk
            WHERE c.perusahaan_id IN ($ids_sql) AND DATE_FORMAT(c.tgl_bayar,'%Y-%m') = ?
        ", [$bulanini])->row()->laba ?? 0;

        $pengeluaran_bulanini = $this->db->query("
            SELECT SUM(pengeluaran_jumlah) as total 
            FROM tbl_pengeluaran 
            WHERE DATE_FORMAT(pengeluaran_tgl,'%Y-%m') = ? AND perusahaan_id IN ($ids_sql)
        ", [$bulanini])->row()->total ?? 0;

        $laba_bersih = $laba_rugi - $pengeluaran_bulanini;

        // Get perusahaan info
        $perusahaan = $this->db->get_where('tbl_perusahaan', ['id_perusahaan' => $perusahaan_id])->row();

        // Total hutang supplier (global)
        $total_hutang = $this->db->query("
            SELECT SUM(total_tagihan - total_bayar) as total
            FROM tbl_hutang
            WHERE status != 'lunas'
        ")->row()->total ?? 0;

        $data = [
            'token' => $token,
            'hariini' => $hariini,
            'bulanini' => $bulanini,
            'perusahaan_id' => $perusahaan_id,
            'perusahaan' => $perusahaan,
            'omset_hariini' => $omset_hariini,
            'omset_bulanini' => $omset_bulanini,
            'kas_masuk_hariini' => ($kas_hariini['tunai'] ?? 0) + ($kas_hariini['non_tunai'] ?? 0),
            'saldo_kas' => $saldo_kas,
            'piutang_hariini' => $piutang_hariini,
            'total_piutang_outstanding' => $total_piutang_outstanding,
            'laba_bersih' => $laba_bersih,
            'total_hutang' => $total_hutang,
            'all_perusahaan' => $this->db->get_where('tbl_perusahaan', ['status' => '1'])->result(),
            'title' => 'Dashboard Owner'
        ];

        $this->load->view('owner_mobile/layout', $data);
        $this->load->view('owner_mobile/index', $data);
        $this->load->view('owner_mobile/footer', $data);
    }
    public function rpt_pemasukan_harian($token = null)
    {
        $this->_check_token($token);

        $perusahaan_id = $this->input->get('perusahaan_id', true) ?: 1;
        $hariini = $this->input->get('tanggal', true) ?: date('Y-m-d');

        $ids = $this->_get_ids($perusahaan_id);
        $ids_sql = implode(',', $ids);

        $pemasukan_list = $this->db->query("
                SELECT p.tgl_bayar as tanggal, p.jam_bayar as jam, p.nospk, 
                       k.konsumen_nama as konsumen, k.konsumen_nohp as telepon,
                       p.total_tagihan, 
                       p.total_bayar as bayar_hari_ini,
                       (p.total_tagihan - p.total_bayar) as sisa_piutang,
                       u.username as kasir,
                       CASE WHEN p.status_lunas = '1' THEN 'Lunas' ELSE 'DP/Termin' END as status_transaksi,
                       CASE WHEN p.nospk LIKE 'MAN-%' THEN 'Manual' ELSE 'SPK' END as jenis_label
                FROM tbl_pembayaran p
                LEFT JOIN tbl_konsumen k ON p.konsumen_id = k.konsumen_id
                LEFT JOIN users u ON p.kasir_id = u.user_id
                WHERE p.tgl_bayar = ? AND p.perusahaan_id IN ($ids_sql)
                ORDER BY p.tgl_bayar DESC, p.jam_bayar DESC
            ", [$hariini])->result_array();

        $transaksi_langsung = $this->db->query("
                SELECT pd.metode, SUM(pd.jumlah) as total 
                FROM tbl_pembayaran_detail pd
                JOIN tbl_pembayaran p ON pd.pembayaran_id = p.pembayaran_id
                WHERE DATE(pd.created_at) = ? AND p.perusahaan_id IN ($ids_sql)
                GROUP BY pd.metode
            ", [$hariini])->result_array();

        $breakdown_transfer = $this->db->query("
                SELECT 
                    COALESCE(r.nama_bank, 'Tidak Terdeteksi') as nama_bank, 
                    COALESCE(r.no_rekening, '-') as no_rekening, 
                    SUM(pd.jumlah) as total 
                FROM tbl_pembayaran_detail pd
                JOIN tbl_pembayaran p ON pd.pembayaran_id = p.pembayaran_id
                LEFT JOIN tbl_rekening r ON pd.bank_id = r.id
                WHERE DATE(pd.created_at) = ? 
                  AND p.perusahaan_id IN ($ids_sql) AND pd.metode = 'transfer'
                GROUP BY pd.bank_id
            ", [$hariini])->result_array();

        $breakdown_edc = $this->db->query("
                SELECT me.edc_nama, SUM(pd.jumlah) as total
                FROM tbl_pembayaran_detail pd
                JOIN tbl_pembayaran p ON pd.pembayaran_id = p.pembayaran_id
                LEFT JOIN tbl_mesin_edc me ON pd.edc_id = me.edc_id
                WHERE DATE(pd.created_at) = ? 
                  AND p.perusahaan_id IN ($ids_sql) AND pd.metode = 'debit'
                GROUP BY pd.edc_id
            ", [$hariini])->result_array();

        $breakdown_ewallet = $this->db->query("
                SELECT e.ewallet_nama, SUM(pd.jumlah) as total
                FROM tbl_pembayaran_detail pd
                JOIN tbl_pembayaran p ON pd.pembayaran_id = p.pembayaran_id
                LEFT JOIN tbl_ewallet e ON pd.ewallet_id = e.id
                WHERE DATE(pd.created_at) = ? 
                  AND p.perusahaan_id IN ($ids_sql) AND pd.metode = 'ewallet'
                GROUP BY pd.ewallet_id
            ", [$hariini])->result_array();

        $rekap_piutang = $this->db->query("
                SELECT pd.metode, SUM(pd.jumlah) as total 
                FROM tbl_pembayaran_detail pd
                JOIN tbl_pembayaran p ON pd.pembayaran_id = p.pembayaran_id
                WHERE DATE(pd.created_at) = ? AND DATE(pd.created_at) > p.tgl_bayar 
                  AND p.perusahaan_id IN ($ids_sql)
                GROUP BY pd.metode
            ", [$hariini])->result_array();

        $pengeluaran_harian = $this->db->query("
                SELECT 
                    SUM(pengeluaran_jumlah) as total,
                    SUM(CASE 
                        WHEN metode_bayar = 'tunai' THEN pengeluaran_jumlah
                        WHEN metode_bayar IS NULL AND (rekening_id IS NULL OR rekening_id = 0) THEN pengeluaran_jumlah 
                        ELSE 0 
                    END) as total_tunai
                FROM tbl_pengeluaran 
                WHERE pengeluaran_tgl = ? AND perusahaan_id IN ($ids_sql)
            ", [$hariini])->row_array();

        $list_pengeluaran = $this->db->query("
            SELECT 
                p.id as pengeluaran_id,
                p.pengeluaran_tgl,
                p.pengeluaran_jumlah,
                p.pengeluaran_detail,
                p.pengeluaran_penerima,
                p.metode_bayar,
                p.rekening_tujuan,
                r.nama_bank as bank_asal,
                r.no_rekening as norek_asal
            FROM tbl_pengeluaran p
            LEFT JOIN tbl_rekening r ON p.rekening_id = r.id
            WHERE p.pengeluaran_tgl = ? AND p.perusahaan_id IN ($ids_sql)
            ORDER BY p.id ASC
        ", [$hariini])->result_array();

        $summary = [
            'cash' => ['transaksi' => 0, 'piutang' => 0, 'total' => 0],
            'transfer' => ['transaksi' => 0, 'piutang' => 0, 'total' => 0],
            'debit' => ['transaksi' => 0, 'piutang' => 0, 'total' => 0],
            'ewallet' => ['transaksi' => 0, 'piutang' => 0, 'total' => 0],
            'lainnya' => ['transaksi' => 0, 'piutang' => 0, 'total' => 0],
        ];

        foreach ($transaksi_langsung as $t) {
            $key = strtolower($t['metode'] ?? 'lainnya');
            if ($key == 'tunai')
                $key = 'cash';
            if ($key == 'edc')
                $key = 'debit';
            if (isset($summary[$key])) {
                $summary[$key]['transaksi'] += $t['total'];
                $summary[$key]['total'] += $t['total'];
            }
        }

        foreach ($rekap_piutang as $p) {
            $key = strtolower($p['metode'] ?? 'lainnya');
            if ($key == 'tunai')
                $key = 'cash';
            if ($key == 'edc')
                $key = 'debit';
            if (isset($summary[$key])) {
                $summary[$key]['piutang'] += $p['total'];
            }
        }

        $total_cash_in_hand = $summary['cash']['total'] - ($pengeluaran_harian['total_tunai'] ?? 0);

        $total_omset_hari_ini = 0;
        $total_omset_manual = 0;
        foreach ($pemasukan_list as $pl) {
            $total_omset_hari_ini += $pl['total_tagihan'];
            if ($pl['jenis_label'] == 'Manual') {
                $total_omset_manual += $pl['total_tagihan'];
            }
        }

        $perusahaan = $this->db->get_where('tbl_perusahaan', ['id_perusahaan' => $perusahaan_id])->row();

        $data = [
            'token' => $token,
            'hariini' => $hariini,
            'perusahaan_id' => $perusahaan_id,
            'perusahaan' => $perusahaan,
            'all_perusahaan' => $this->db->get_where('tbl_perusahaan', ['status' => '1'])->result(),
            'pemasukan_list' => $pemasukan_list,
            'list_pengeluaran' => $list_pengeluaran,
            'summary' => $summary,
            'breakdown_transfer' => $breakdown_transfer,
            'breakdown_edc' => $breakdown_edc,
            'breakdown_ewallet' => $breakdown_ewallet,
            'stat_keuangan' => [
                'omset_penjualan' => $total_omset_hari_ini,
                'omset_manual' => $total_omset_manual,
                'total_uang_masuk' => ($summary['cash']['total'] + $summary['transfer']['total'] + $summary['debit']['total'] + $summary['ewallet']['total']),
                'total_pengeluaran' => ($pengeluaran_harian['total'] ?? 0),
                'saldo_cash_fisik' => $total_cash_in_hand,
            ],
            'title' => 'Pemasukan Harian'
        ];

        $this->load->view('owner_mobile/layout', $data);
        $this->load->view('owner_mobile/pemasukan_harian', $data);
        $this->load->view('owner_mobile/footer', $data);
    }

    public function financial_hub($token = null)
    {
        $this->_check_token($token);

        // Cek apakah ada parameter perusahaan_id di URL
        $qs = $_SERVER['QUERY_STRING'] ? '?' . $_SERVER['QUERY_STRING'] : '';

        // Redirect ke dashboard karena tampilannya sudah disatukan
        redirect('Owner/dashboard/' . $token . $qs);
    }

    /* =======================================================
     *  LAPORAN REPORT METHODS (tanpa login, berbasis token)
     * ======================================================= */

    public function rpt_pemasukan_bulanan($token = null)
    {
        $this->_check_token($token);
        $perusahaan_id = $this->input->get('perusahaan_id', true) ?: 1;
        $ids = $this->_get_ids($perusahaan_id);
        $ids_sql = implode(',', $ids);
        $bulan_ini = $this->input->get('bulan', true) ?: date('Y-m');
        $start_date = $bulan_ini . '-01';
        $end_date = date('Y-m-t', strtotime($start_date));

        $pemasukan_list = $this->db->query("
            SELECT p.tgl_bayar as tanggal, p.jam_bayar as jam, p.nospk,
                   k.konsumen_nama as konsumen, k.konsumen_nohp as telepon,
                   p.total_tagihan, p.total_bayar as bayar_hari_ini,
                   (p.total_tagihan - p.total_bayar) as sisa_piutang, u.username as kasir,
                   CASE WHEN p.status_lunas='1' THEN 'Lunas' ELSE 'DP/Termin' END as status_transaksi
            FROM tbl_pembayaran p
            LEFT JOIN tbl_konsumen k ON p.konsumen_id=k.konsumen_id
            LEFT JOIN users u ON p.kasir_id=u.user_id
            WHERE (p.tgl_bayar BETWEEN ? AND ?) AND p.perusahaan_id IN ($ids_sql)
            ORDER BY p.tgl_bayar DESC, p.jam_bayar DESC
        ", [$start_date, $end_date])->result_array();

        $transaksi_langsung = $this->db->query("
            SELECT pd.metode, SUM(pd.jumlah) as total
            FROM tbl_pembayaran_detail pd
            JOIN tbl_pembayaran p ON pd.pembayaran_id=p.pembayaran_id
            WHERE (DATE(pd.created_at) BETWEEN ? AND ?) AND p.perusahaan_id IN ($ids_sql)
            GROUP BY pd.metode
        ", [$start_date, $end_date])->result_array();

        $breakdown_transfer = $this->db->query("
            SELECT r.nama_bank, r.no_rekening, SUM(pd.jumlah) as total
            FROM tbl_pembayaran_detail pd
            JOIN tbl_pembayaran p ON pd.pembayaran_id=p.pembayaran_id
            LEFT JOIN tbl_rekening r ON pd.bank_id=r.id
            WHERE (DATE(pd.created_at) BETWEEN ? AND ?) AND p.perusahaan_id IN ($ids_sql) AND pd.metode='transfer'
            GROUP BY pd.bank_id
        ", [$start_date, $end_date])->result_array();

        $breakdown_edc = $this->db->query("
            SELECT me.edc_nama, SUM(pd.jumlah) as total
            FROM tbl_pembayaran_detail pd
            JOIN tbl_pembayaran p ON pd.pembayaran_id=p.pembayaran_id
            LEFT JOIN tbl_mesin_edc me ON pd.edc_id=me.edc_id
            WHERE (DATE(pd.created_at) BETWEEN ? AND ?) AND p.perusahaan_id IN ($ids_sql) AND pd.metode='debit'
            GROUP BY pd.edc_id
        ", [$start_date, $end_date])->result_array();

        $breakdown_ewallet = $this->db->query("
            SELECT e.ewallet_nama, SUM(pd.jumlah) as total
            FROM tbl_pembayaran_detail pd
            JOIN tbl_pembayaran p ON pd.pembayaran_id=p.pembayaran_id
            LEFT JOIN tbl_ewallet e ON pd.ewallet_id=e.id
            WHERE (DATE(pd.created_at) BETWEEN ? AND ?) AND p.perusahaan_id IN ($ids_sql) AND pd.metode='ewallet'
            GROUP BY pd.ewallet_id
        ", [$start_date, $end_date])->result_array();

        $pengeluaran_bulanan = $this->db->query("
            SELECT SUM(pengeluaran_jumlah) as total,
                   SUM(CASE WHEN metode_bayar='tunai' OR (metode_bayar IS NULL AND (rekening_id IS NULL OR rekening_id=0)) THEN pengeluaran_jumlah ELSE 0 END) as total_tunai
            FROM tbl_pengeluaran
            WHERE (pengeluaran_tgl BETWEEN ? AND ?) AND perusahaan_id IN ($ids_sql)
        ", [$start_date, $end_date])->row_array();

        $summary = [
            'cash' => ['transaksi' => 0, 'piutang' => 0, 'total' => 0],
            'transfer' => ['transaksi' => 0, 'piutang' => 0, 'total' => 0],
            'debit' => ['transaksi' => 0, 'piutang' => 0, 'total' => 0],
            'ewallet' => ['transaksi' => 0, 'piutang' => 0, 'total' => 0],
            'lainnya' => ['transaksi' => 0, 'piutang' => 0, 'total' => 0]
        ];
        foreach ($transaksi_langsung as $t) {
            $key = strtolower($t['metode'] ?? 'lainnya');
            if ($key == 'tunai')
                $key = 'cash';
            if ($key == 'edc')
                $key = 'debit';
            if (isset($summary[$key])) {
                $summary[$key]['transaksi'] += $t['total'];
                $summary[$key]['total'] += $t['total'];
            }
        }
        $total_omset = 0;
        foreach ($pemasukan_list as $pl)
            $total_omset += $pl['total_tagihan'];

        $data = array_merge($this->_owner_data($token, $perusahaan_id, 'Laporan Bulanan'), [
            'bulan_ini' => $bulan_ini,
            'start_date' => $start_date,
            'end_date' => $end_date,
            'pemasukan_list' => $pemasukan_list,
            'summary' => $summary,
            'breakdown_transfer' => $breakdown_transfer,
            'breakdown_edc' => $breakdown_edc,
            'breakdown_ewallet' => $breakdown_ewallet,
            'stat_keuangan' => [
                'omset_penjualan' => $total_omset,
                'total_uang_masuk' => array_sum(array_column($summary, 'total')),
                'total_pengeluaran' => $pengeluaran_bulanan['total'] ?? 0,
                'saldo_cash_fisik' => ($summary['cash']['total'] - ($pengeluaran_bulanan['total_tunai'] ?? 0))
            ],
        ]);
        $this->_owner_view($token, 'owner_mobile/rpt_pemasukan_bulanan', $data);
    }

    public function rpt_rekap_metode($token = null)
    {
        $this->_check_token($token);
        $perusahaan_id = $this->input->get('perusahaan_id', true) ?: 1;
        $ids = $this->_get_ids($perusahaan_id);
        $ids_sql = implode(',', $ids);
        $hariini = $this->input->get('tanggal', true) ?: date('Y-m-d');

        $this->db->select('p.pembayaran_id, p.nospk, p.tgl_bayar, p.jam_bayar, p.total_tagihan, p.total_bayar, p.status_lunas, k.konsumen_nama, k.konsumen_nohp');
        $this->db->from('tbl_pembayaran p');
        $this->db->join('tbl_konsumen k', 'k.konsumen_id = p.konsumen_id', 'left');
        $this->db->where('p.tgl_bayar', $hariini);
        $this->db->where_in('p.perusahaan_id', $ids);
        $pemasukan = $this->db->get()->result();

        $this->db->select('pd.metode, pd.jumlah, pd.bank_id, pd.edc_id, pd.ewallet_id, r.nama_bank, r.no_rekening, e.edc_nama, ew.ewallet_nama, p.nospk, p.tgl_bayar, p.total_tagihan, p.status_lunas');
        $this->db->from('tbl_pembayaran_detail pd');
        $this->db->join('tbl_pembayaran p', 'p.pembayaran_id = pd.pembayaran_id');
        $this->db->join('tbl_rekening r', 'r.id = pd.bank_id', 'left');
        $this->db->join('tbl_mesin_edc e', 'e.edc_id = pd.edc_id', 'left');
        $this->db->join('tbl_ewallet ew', 'ew.id = pd.ewallet_id', 'left');
        $this->db->where('p.tgl_bayar', $hariini);
        $this->db->where_in('p.perusahaan_id', $ids);
        $payment_methods_data = $this->db->get()->result();

        $methods = ['tunai' => ['total' => 0, 'details' => []], 'transfer' => ['total' => 0, 'details' => []], 'debit' => ['total' => 0, 'details' => []], 'ewallet' => ['total' => 0, 'details' => []], 'lainnya' => ['total' => 0, 'details' => []]];
        $total_all = 0;
        foreach ($payment_methods_data as $pm) {
            $m = strtolower($pm->metode);
            if (!isset($methods[$m]))
                $m = 'lainnya';
            $methods[$m]['total'] += $pm->jumlah;
            $total_all += $pm->jumlah;
            if ($m == 'transfer' && $pm->bank_id) {
                $k = $pm->nama_bank . ' - ' . $pm->no_rekening;
                if (!isset($methods[$m]['details'][$k]))
                    $methods[$m]['details'][$k] = ['nama' => $k, 'total' => 0];
                $methods[$m]['details'][$k]['total'] += $pm->jumlah;
            }
            if ($m == 'debit' && $pm->edc_id) {
                if (!isset($methods[$m]['details'][$pm->edc_nama]))
                    $methods[$m]['details'][$pm->edc_nama] = ['nama' => $pm->edc_nama, 'total' => 0];
                $methods[$m]['details'][$pm->edc_nama]['total'] += $pm->jumlah;
            }
            if ($m == 'ewallet' && $pm->ewallet_id) {
                if (!isset($methods[$m]['details'][$pm->ewallet_nama]))
                    $methods[$m]['details'][$pm->ewallet_nama] = ['nama' => $pm->ewallet_nama, 'total' => 0];
                $methods[$m]['details'][$pm->ewallet_nama]['total'] += $pm->jumlah;
            }
        }
        foreach ($methods as &$method) {
            $method['percentage'] = $total_all > 0 ? ($method['total'] / $total_all) * 100 : 0;
            $method['details'] = array_values($method['details']);
        }
        $payment_methods = ['cash' => $methods['tunai'], 'transfer' => $methods['transfer'], 'edc' => $methods['debit'], 'ewallet' => $methods['ewallet'], 'lainnya' => $methods['lainnya'], 'total_all' => $total_all];

        $data = array_merge($this->_owner_data($token, $perusahaan_id, 'Rekap Metode Bayar'), [
            'hariini' => $hariini,
            'pemasukan' => $pemasukan,
            'payment_methods_data' => $payment_methods_data,
            'payment_methods' => $payment_methods,
        ]);
        $this->_owner_view($token, 'owner_mobile/rpt_rekap_metode', $data);
    }

    public function rpt_legger($token = null)
    {
        $this->_check_token($token);
        $perusahaan_id = $this->input->get('perusahaan_id', true) ?: 1;
        $ids = $this->_get_ids($perusahaan_id);
        $ids_sql = implode(',', $ids);
        $mulai = $this->input->get('mulai', true) ?: date('Y-m-01');
        $selesai = $this->input->get('selesai', true) ?: date('Y-m-t');

        $legger = $this->M_transaksi->get_legger($mulai, $selesai, $ids_sql)->result();
        $total_cash = $total_transfer = $total_edc = $total_ewallet = $total_piutang = $grand_total = 0;
        foreach ($legger as $row) {
            $total_cash += $row->total_cash;
            $total_transfer += $row->total_transfer;
            $total_edc += $row->total_edc;
            $total_ewallet += $row->total_ewallet;
            $total_piutang += $row->total_piutang;
            $grand_total += $row->total_harian;
        }
        $data = array_merge($this->_owner_data($token, $perusahaan_id, 'Legger / Buku Besar'), [
            'mulai' => $mulai,
            'selesai' => $selesai,
            'bulan' => date('F Y', strtotime($mulai)),
            'legger' => $legger,
            'cash' => $total_cash,
            'transfer' => $total_transfer,
            'edc' => $total_edc,
            'ewallet' => $total_ewallet,
            'piutang' => $total_piutang,
            'grandtotal' => $grand_total,
        ]);
        $this->_owner_view($token, 'owner_mobile/rpt_legger', $data);
    }

    public function rpt_laba_rugi($token = null)
    {
        $this->_check_token($token);
        $perusahaan_id = $this->input->get('perusahaan_id', true) ?: 1;
        $ids = $this->_get_ids($perusahaan_id);
        $bulanini = $this->input->get('bulan', true) ?: date('Y-m');

        $data = array_merge($this->_owner_data($token, $perusahaan_id, 'Laba Rugi Bulanan'), [
            'laba_rugi' => $this->M_laporan->getLabaRugi($ids, $bulanini)->row_array(),
            'pendapatan' => $this->M_laporan->total_pendapatan($ids, $bulanini)->row_array(),
            'pengeluaran_bulanan' => $this->M_laporan->pengeluaran_bulanan($ids, $bulanini)->row_array(),
            'pengeluaran_harian' => $this->M_laporan->pengeluaran($ids)->row_array(),
            'pemasukan_bulanan' => $this->M_laporan->pemasukan_bulanan($ids, $bulanini)->row_array(),
            'pengeluaran_harian_list' => $this->M_laporan->pengeluaran_harian_list($ids, $bulanini)->result(),
            'pengeluaran_bulanan_list' => $this->M_laporan->pengeluaran_bulanan_list($ids, $bulanini)->result(),
            'chart_data' => $this->M_laporan->get_daily_laba_rugi($ids, $bulanini)->result(),
            'periode' => $bulanini,
        ]);
        $this->_owner_view($token, 'owner_mobile/rpt_laba_rugi', $data);
    }

    public function rpt_tren_keuangan($token = null)
    {
        $this->_check_token($token);
        $perusahaan_id = $this->input->get('perusahaan_id', true) ?: 1;
        $ids = $this->_get_ids($perusahaan_id);
        $ids_sql = implode(',', $ids);

        $tren = [];
        for ($i = 11; $i >= 0; $i--) {
            $bulan = date('Y-m', strtotime("-$i months"));
            $pend = $this->db->query("SELECT SUM(total_tagihan) as total FROM tbl_pembayaran WHERE DATE_FORMAT(tgl_bayar,'%Y-%m')='$bulan' AND perusahaan_id IN ($ids_sql)")->row()->total ?? 0;
            $peng = $this->db->query("SELECT SUM(pengeluaran_jumlah) as total FROM tbl_pengeluaran WHERE DATE_FORMAT(pengeluaran_tgl,'%Y-%m')='$bulan' AND perusahaan_id IN ($ids_sql)")->row()->total ?? 0;
            $tren[] = ['bulan' => $bulan, 'total_pendapatan' => (float) $pend, 'total_pengeluaran' => (float) $peng];
        }
        $data = array_merge($this->_owner_data($token, $perusahaan_id, 'Tren Keuangan 12 Bulan'), ['tren_data' => $tren]);
        $this->_owner_view($token, 'owner_mobile/rpt_tren_keuangan', $data);
    }

    public function rpt_hpp_margin($token = null)
    {
        $this->_check_token($token);
        $perusahaan_id = $this->input->get('perusahaan_id', true) ?: 1;
        $ids = $this->_get_ids($perusahaan_id);
        $ids_sql = implode(',', $ids);
        $bulan = $this->input->get('bulan', true) ?: date('Y-m');

        $hpp_data = $this->db->query("
            SELECT pr.product_nama, SUM(pd.qty) as total_qty,
                   SUM(pd.qty * hp.harga_1) as total_pendapatan, SUM(pd.qty * hp.hpp) as total_hpp,
                   SUM(pd.qty * (hp.harga_1 - hp.hpp)) as total_margin,
                   AVG((hp.harga_1 - hp.hpp) / NULLIF(hp.harga_1,0) * 100) as avg_margin_pct
            FROM tbl_pemesanan_detail pd
            JOIN tbl_harga_product hp ON pd.harga_id=hp.harga_id
            JOIN tbl_product pr ON hp.product_id=pr.product_id
            JOIN tbl_pembayaran p ON pd.nospk=p.nospk
            WHERE p.perusahaan_id IN ($ids_sql) AND DATE_FORMAT(p.tgl_bayar,'%Y-%m') = " . $this->db->escape($bulan) . "
            GROUP BY pr.product_id ORDER BY total_margin DESC
        ")->result();
        $data = array_merge($this->_owner_data($token, $perusahaan_id, 'Margin & HPP Produk'), ['hpp_data' => $hpp_data, 'bulan' => $bulan]);
        $this->_owner_view($token, 'owner_mobile/rpt_hpp_margin', $data);
    }

    public function rpt_produk_terlaris($token = null)
    {
        $this->_check_token($token);
        $perusahaan_id = $this->input->get('perusahaan_id', true) ?: 1;
        $ids = $this->_get_ids($perusahaan_id);
        $period = $this->input->get('period', true) ?: 'month';

        $data = array_merge($this->_owner_data($token, $perusahaan_id, 'Produk Terlaris'), [
            'top_products' => $this->M_penjualan->getBestSellingProducts($period, $ids),
            'selected_period' => $period,
        ]);
        $this->_owner_view($token, 'owner_mobile/rpt_produk_terlaris', $data);
    }

    public function rpt_penjualan_kategori($token = null)
    {
        $this->_check_token($token);
        $perusahaan_id = $this->input->get('perusahaan_id', true) ?: 1;
        $ids = $this->_get_ids($perusahaan_id);
        $ids_sql = implode(',', $ids);
        $start_date = $this->input->get('start_date', true) ?: date('Y-m-01');
        $end_date = $this->input->get('end_date', true) ?: date('Y-m-t');

        $details = $this->db->query("
            SELECT p.nospk, p.created_at as tgl_pesan, pr.product_nama,
                   COALESCE(pd.snapshot_detail_product, hp.detail_product) as detail_product,
                   COALESCE(k.kategori_nama, 'Tanpa Kategori') as kategori_nama, k.kategori_id,
                   pd.qty, pd.panjang, pd.lebar, pd.snapshot_subtotal, pd.snapshot_harga, pd.metadata,
                   pr.satuan_id, hp.harga_1, hp.ket_1
            FROM tbl_pemesanan p
            JOIN tbl_pemesanan_detail pd ON p.nospk=pd.nospk
            JOIN tbl_product pr ON pd.product_id=pr.product_id
            LEFT JOIN tbl_kategori k ON pr.kategori_id=k.kategori_id
            LEFT JOIN tbl_harga_product hp ON pd.harga_id=hp.harga_id
            WHERE DATE(p.created_at) >= ? AND DATE(p.created_at) <= ?
              AND p.perusahaan_id IN ($ids_sql) AND p.status_pembayaran != 'canceled'
        ", [$start_date, $end_date])->result();

        $map = [];
        foreach ($details as $dt) {
            $cat = $dt->kategori_nama;
            if (!isset($map[$cat]))
                $map[$cat] = ['kategori_nama' => $cat, 'total_qty' => 0, 'total_omset' => 0, 'transactions' => []];
            $subtotal = is_numeric($dt->snapshot_subtotal) ? (float) $dt->snapshot_subtotal : (float) $dt->harga_1 * (float) $dt->qty;
            $map[$cat]['total_qty'] += (float) $dt->qty;
            $map[$cat]['total_omset'] += $subtotal;
            $map[$cat]['transactions'][] = ['nospk' => $dt->nospk, 'tgl_pesan' => date('d M Y H:i', strtotime($dt->tgl_pesan)), 'product_nama' => $dt->product_nama, 'qty' => $dt->qty, 'subtotal' => $subtotal];
        }
        usort($map, fn($a, $b) => $b['total_omset'] <=> $a['total_omset']);
        $data = array_merge($this->_owner_data($token, $perusahaan_id, 'Penjualan Kategori'), [
            'penjualan_kategori' => $map,
            'start_date' => $start_date,
            'end_date' => $end_date,
        ]);
        $this->_owner_view($token, 'owner_mobile/rpt_penjualan_kategori', $data);
    }

    public function rpt_aging_piutang($token = null)
    {
        $this->_check_token($token);
        $perusahaan_id = $this->input->get('perusahaan_id', true) ?: 1;
        $ids = $this->_get_ids($perusahaan_id);
        $ids_sql = implode(',', $ids);

        $aging_summary = $this->db->query("
            SELECT
                SUM(CASE WHEN DATEDIFF(CURDATE(), tgl_bayar) <= 15  THEN (total_tagihan-total_bayar) ELSE 0 END) as age_0_15,
                SUM(CASE WHEN DATEDIFF(CURDATE(), tgl_bayar) BETWEEN 16 AND 30 THEN (total_tagihan-total_bayar) ELSE 0 END) as age_16_30,
                SUM(CASE WHEN DATEDIFF(CURDATE(), tgl_bayar) BETWEEN 31 AND 60 THEN (total_tagihan-total_bayar) ELSE 0 END) as age_31_60,
                SUM(CASE WHEN DATEDIFF(CURDATE(), tgl_bayar) > 60  THEN (total_tagihan-total_bayar) ELSE 0 END) as age_over_60
            FROM tbl_pembayaran WHERE status_lunas='0' AND perusahaan_id IN ($ids_sql)
        ")->row();

        $aging_detail = $this->db->query("
            SELECT p.nospk, p.tgl_bayar, p.total_tagihan, p.total_bayar,
                   k.konsumen_nama, k.konsumen_nohp, DATEDIFF(CURDATE(), p.tgl_bayar) as umur_hari
            FROM tbl_pembayaran p LEFT JOIN tbl_konsumen k ON p.konsumen_id=k.konsumen_id
            WHERE p.status_lunas='0' AND p.perusahaan_id IN ($ids_sql) ORDER BY umur_hari DESC
        ")->result();

        $data = array_merge($this->_owner_data($token, $perusahaan_id, 'Aging Piutang'), [
            'aging_summary' => $aging_summary,
            'aging_detail' => $aging_detail,
        ]);
        $this->_owner_view($token, 'owner_mobile/rpt_aging_piutang', $data);
    }

    public function rpt_piutang($token = null)
    {
        $this->_check_token($token);
        $perusahaan_id = $this->input->get('perusahaan_id', true) ?: 1;
        $ids = $this->_get_ids($perusahaan_id);
        $status = $this->input->get('status', true);
        $limit = $this->input->get('limit', true) ?: 50;

        $ids_sql = implode(',', $ids);
        
        $where_status = "";
        if ($status == 'LUNAS') {
            $where_status = "AND (p.total_tagihan - p.total_bayar) <= 0.5";
        } else if ($status == 'SEBAGIAN') {
            $where_status = "AND (p.total_tagihan - p.total_bayar) > 0.5 AND p.total_bayar > 0";
        } else if ($status == 'BELUM LUNAS') {
            $where_status = "AND (p.total_tagihan - p.total_bayar) > 0.5 AND p.total_bayar = 0";
        } else {
            // Default "Semua" status for active list: show only unpaid/active piutang like standard ERP
            $where_status = "AND (p.total_tagihan - p.total_bayar) > 0.5";
        }

        $piutang_list = $this->db->query("
            SELECT 
                p.nospk,
                p.total_tagihan,
                p.total_bayar,
                (p.total_tagihan - p.total_bayar) as sisa_piutang,
                p.tgl_bayar,
                p.jam_bayar,
                p.status_lunas,
                k.konsumen_nama,
                k.konsumen_nohp,
                k.konsumen_id,
                CASE 
                    WHEN (p.total_tagihan - p.total_bayar) <= 0.5 THEN 'LUNAS'
                    WHEN p.total_bayar > 0 THEN 'SEBAGIAN'
                    ELSE 'BELUM LUNAS'
                END as status,
                DATE_ADD(p.tgl_bayar, INTERVAL 30 DAY) as jatuh_tempo
            FROM tbl_pembayaran p
            JOIN tbl_pemesanan pm ON pm.nospk = p.nospk
            JOIN tbl_konsumen k ON k.konsumen_id = pm.konsumen_id
            WHERE pm.perusahaan_id IN ($ids_sql) $where_status
            ORDER BY k.konsumen_nama ASC, p.tgl_bayar ASC
            LIMIT ?
        ", [$limit])->result();

        $piutang_summary = $this->db->query("
            SELECT 
                COUNT(p.nospk) as total_piutang,
                COALESCE(SUM(p.total_tagihan - p.total_bayar), 0) as total_sisa_piutang,
                COALESCE(SUM(CASE WHEN (p.total_tagihan - p.total_bayar) > 0.5 AND p.total_bayar = 0 THEN 1 ELSE 0 END), 0) as belum_lunas,
                COALESCE(SUM(CASE WHEN (p.total_tagihan - p.total_bayar) > 0.5 AND p.total_bayar > 0 THEN 1 ELSE 0 END), 0) as sebagian,
                COALESCE(SUM(CASE WHEN (p.total_tagihan - p.total_bayar) <= 0.5 THEN 1 ELSE 0 END), 0) as lunas
            FROM tbl_pembayaran p
            JOIN tbl_pemesanan pm ON pm.nospk = p.nospk
            WHERE pm.perusahaan_id IN ($ids_sql) AND (p.total_tagihan - p.total_bayar) > 0.5
        ")->row();

        $data = array_merge($this->_owner_data($token, $perusahaan_id, 'Piutang Konsumen'), [
            'piutang_list' => $piutang_list,
            'piutang_summary' => $piutang_summary,
            'rekening_list' => $this->db->where('active', '1')->get('tbl_rekening')->result(),
            'ewallet_list' => $this->db->get('tbl_ewallet')->result(),
            'edc_list' => $this->db->get('tbl_mesin_edc')->result(),
            'status_filter' => $status,
            'limit_filter' => $limit,
        ]);
        $this->_owner_view($token, 'owner_mobile/rpt_piutang', $data);
    }

    public function rpt_hutang($token = null)
    {
        $this->_check_token($token);
        $perusahaan_id = $this->input->get('perusahaan_id', true) ?: 1;
        $status = $this->input->get('status', true);
        $limit = $this->input->get('limit', true) ?: 50;

        $hutang_summary = $this->M_hutang->getHutangSummary();
        $data = array_merge($this->_owner_data($token, $perusahaan_id, 'Hutang Supplier'), [
            'tampilData' => $this->M_hutang->getAllHutang($limit, $status),
            'hutang_summary' => $hutang_summary,
            'total_hutang' => $hutang_summary->total_tagihan ?? 0,
            'total_bayar' => $hutang_summary->total_bayar ?? 0,
            'total_sisa' => $hutang_summary->total_sisa_hutang ?? 0,
            'jatuh_tempo_count' => $hutang_summary->jatuh_tempo ?? 0,
            'status_filter' => $status,
            'limit_filter' => $limit,
            'all_supplier' => $this->db->get('tbl_supplier')->result(),
        ]);
        $this->_owner_view($token, 'owner_mobile/rpt_hutang', $data);
    }

    public function rpt_pajak($token = null)
    {
        $this->_check_token($token);
        $perusahaan_id = $this->input->get('perusahaan_id', true) ?: 1;
        $ids = $this->_get_ids($perusahaan_id);
        $ids_sql = implode(',', $ids);
        $bulanini = $this->input->get('bulan', true) ?: date('Y-m');
        $tgl_awal = $this->input->get('tgl_awal', true);
        $tgl_akhir = $this->input->get('tgl_akhir', true);
        $search = $this->input->get('search', true);

        $where = "pb.perusahaan_id IN ($ids_sql)";
        if ($tgl_awal && $tgl_akhir) {
            $where .= " AND pb.tgl_bayar BETWEEN " . $this->db->escape($tgl_awal) . " AND " . $this->db->escape($tgl_akhir);
        } else {
            $sd = $bulanini . '-01';
            $ed = date('Y-m-t', strtotime($sd));
            $where .= " AND pb.tgl_bayar BETWEEN " . $this->db->escape($sd) . " AND " . $this->db->escape($ed);
        }
        $where .= " AND pb.status_lunas='1'";
        if ($search) {
            $se = $this->db->escape_like_str($search);
            $where .= " AND (k.konsumen_nama LIKE '%$se%' OR pb.nospk LIKE '%$se%')";
        }

        $lap_pajak = $this->db->query("
            SELECT pb.pembayaran_id, pb.nospk, pb.tgl_bayar as tgl, pb.jam_bayar as jam, pb.total_bayar,
                   k.konsumen_nama, k.konsumen_nohp,
                   ROUND(pb.total_bayar/1.11,0) AS dpp, ROUND(pb.total_bayar-(pb.total_bayar/1.11),0) AS ppn,
                   COALESCE(GROUP_CONCAT(DISTINCT pd.metode SEPARATOR ', '),'-') AS metode
            FROM tbl_pembayaran pb LEFT JOIN tbl_konsumen k ON pb.konsumen_id=k.konsumen_id
            LEFT JOIN tbl_pembayaran_detail pd ON pd.pembayaran_id=pb.pembayaran_id
            WHERE $where GROUP BY pb.pembayaran_id ORDER BY pb.tgl_bayar DESC
        ")->result();

        $summary = $this->db->query("SELECT COUNT(nospk) AS jumlah_transaksi, SUM(total_bayar) AS total_penjualan,
            SUM(ROUND(total_bayar/1.11,0)) AS total_dpp, SUM(ROUND(total_bayar-(total_bayar/1.11),0)) AS total_ppn
            FROM tbl_pembayaran pb LEFT JOIN tbl_konsumen k ON pb.konsumen_id=k.konsumen_id WHERE $where")->row();

        $data = array_merge($this->_owner_data($token, $perusahaan_id, 'Laporan Pajak PPN'), [
            'lap_pajak' => $lap_pajak,
            'summary' => $summary,
            'bulanini' => $bulanini,
            'tgl_awal' => $tgl_awal,
            'tgl_akhir' => $tgl_akhir,
            'search' => $search,
        ]);
        $this->_owner_view($token, 'owner_mobile/rpt_pajak', $data);
    }

    public function rpt_arus_kas($token = null)
    {
        $this->_check_token($token);
        $perusahaan_id = $this->input->get('perusahaan_id', true) ?: 1;
        $ids = $this->_get_ids($perusahaan_id);
        $ids_sql = implode(',', $ids);
        $mulai = $this->input->get('mulai', true) ?: date('Y-m-01');
        $selesai = $this->input->get('selesai', true) ?: date('Y-m-t');

        $masuk_tunai = $this->db->query("SELECT SUM(pd.jumlah) as total FROM tbl_pembayaran_detail pd JOIN tbl_pembayaran p ON pd.pembayaran_id=p.pembayaran_id WHERE DATE(pd.created_at) BETWEEN ? AND ? AND p.perusahaan_id IN ($ids_sql) AND pd.metode='tunai'", [$mulai, $selesai])->row()->total ?? 0;
        $masuk_transfer = $this->db->query("SELECT SUM(pd.jumlah) as total FROM tbl_pembayaran_detail pd JOIN tbl_pembayaran p ON pd.pembayaran_id=p.pembayaran_id WHERE DATE(pd.created_at) BETWEEN ? AND ? AND p.perusahaan_id IN ($ids_sql) AND pd.metode='transfer'", [$mulai, $selesai])->row()->total ?? 0;
        $masuk_debit = $this->db->query("SELECT SUM(pd.jumlah) as total FROM tbl_pembayaran_detail pd JOIN tbl_pembayaran p ON pd.pembayaran_id=p.pembayaran_id WHERE DATE(pd.created_at) BETWEEN ? AND ? AND p.perusahaan_id IN ($ids_sql) AND pd.metode='debit'", [$mulai, $selesai])->row()->total ?? 0;
        $masuk_ewallet = $this->db->query("SELECT SUM(pd.jumlah) as total FROM tbl_pembayaran_detail pd JOIN tbl_pembayaran p ON pd.pembayaran_id=p.pembayaran_id WHERE DATE(pd.created_at) BETWEEN ? AND ? AND p.perusahaan_id IN ($ids_sql) AND pd.metode='ewallet'", [$mulai, $selesai])->row()->total ?? 0;
        $keluar_tunai = $this->db->query("SELECT SUM(pengeluaran_jumlah) as total FROM tbl_pengeluaran WHERE pengeluaran_tgl BETWEEN ? AND ? AND perusahaan_id IN ($ids_sql) AND (metode_bayar='tunai' OR metode_bayar IS NULL)", [$mulai, $selesai])->row()->total ?? 0;
        $keluar_transfer = $this->db->query("SELECT SUM(pengeluaran_jumlah) as total FROM tbl_pengeluaran WHERE pengeluaran_tgl BETWEEN ? AND ? AND perusahaan_id IN ($ids_sql) AND metode_bayar='transfer'", [$mulai, $selesai])->row()->total ?? 0;
        $keluar_lain = $this->db->query("SELECT SUM(pengeluaran_jumlah) as total FROM tbl_pengeluaran WHERE pengeluaran_tgl BETWEEN ? AND ? AND perusahaan_id IN ($ids_sql) AND metode_bayar NOT IN ('tunai','transfer') AND metode_bayar IS NOT NULL", [$mulai, $selesai])->row()->total ?? 0;

        $daily_trend = $this->db->query("
            SELECT d.tgl, COALESCE(m.total_masuk,0) as total_masuk, COALESCE(k.total_keluar,0) as total_keluar
            FROM (
                SELECT DISTINCT tgl_bayar as tgl FROM tbl_pembayaran WHERE tgl_bayar BETWEEN ? AND ? AND perusahaan_id IN ($ids_sql)
                UNION SELECT DISTINCT pengeluaran_tgl FROM tbl_pengeluaran WHERE pengeluaran_tgl BETWEEN ? AND ? AND perusahaan_id IN ($ids_sql)
            ) d
            LEFT JOIN (SELECT DATE(pd.created_at) as tgl, SUM(pd.jumlah) as total_masuk FROM tbl_pembayaran_detail pd JOIN tbl_pembayaran p ON pd.pembayaran_id=p.pembayaran_id WHERE p.perusahaan_id IN ($ids_sql) GROUP BY DATE(pd.created_at)) m ON d.tgl=m.tgl
            LEFT JOIN (SELECT pengeluaran_tgl as tgl, SUM(pengeluaran_jumlah) as total_keluar FROM tbl_pengeluaran WHERE perusahaan_id IN ($ids_sql) GROUP BY pengeluaran_tgl) k ON d.tgl=k.tgl
            ORDER BY d.tgl ASC
        ", [$mulai, $selesai, $mulai, $selesai])->result_array();

        $total_masuk = (float) $masuk_tunai + (float) $masuk_transfer + (float) $masuk_debit + (float) $masuk_ewallet;
        $total_keluar = (float) $keluar_tunai + (float) $keluar_transfer + (float) $keluar_lain;

        $data = array_merge($this->_owner_data($token, $perusahaan_id, 'Arus Kas'), [
            'mulai' => $mulai,
            'selesai' => $selesai,
            'masuk_tunai' => (float) $masuk_tunai,
            'masuk_transfer' => (float) $masuk_transfer,
            'masuk_debit' => (float) $masuk_debit,
            'masuk_ewallet' => (float) $masuk_ewallet,
            'keluar_tunai' => (float) $keluar_tunai,
            'keluar_transfer' => (float) $keluar_transfer,
            'keluar_lain' => (float) $keluar_lain,
            'total_masuk' => $total_masuk,
            'total_keluar' => $total_keluar,
            'net_arus_kas' => $total_masuk - $total_keluar,
            'daily_trend' => $daily_trend,
        ]);
        $this->_owner_view($token, 'owner_mobile/rpt_arus_kas', $data);
    }

    public function rpt_mutasi_bank($token = null)
    {
        $this->_check_token($token);
        $perusahaan_id = $this->input->get('perusahaan_id', true) ?: 1;
        $ids = $this->_get_ids($perusahaan_id);
        $ids_sql = implode(',', $ids);
        $start_date = $this->input->get('start_date', true) ?: date('Y-m-01');
        $end_date = $this->input->get('end_date', true) ?: date('Y-m-d');
        $bank_id = $this->input->get('bank_id', true);

        $rekening_list = $this->db->get('tbl_rekening')->result();
        $mutasi = [];
        $total_masuk = $total_keluar = 0;
        if ($bank_id) {
            $q1 = $this->db->query("SELECT DATE(pd.created_at) as tanggal, TIME(pd.created_at) as jam, p.nospk, k.konsumen_nama as konsumen, pd.jumlah, CASE WHEN DATE(pd.created_at)>p.tgl_bayar THEN 'piutang' ELSE 'transaksi' END as tipe, 'masuk' as arus FROM tbl_pembayaran_detail pd JOIN tbl_pembayaran p ON pd.pembayaran_id=p.pembayaran_id LEFT JOIN tbl_konsumen k ON p.konsumen_id=k.konsumen_id WHERE pd.bank_id=? AND (DATE(pd.created_at) BETWEEN ? AND ?) AND p.perusahaan_id IN ($ids_sql)", [$bank_id, $start_date, $end_date])->result_array();
            $q2 = $this->db->query("SELECT pengeluaran_tgl as tanggal,'00:00:00' as jam,'-' as nospk,pengeluaran_penerima as konsumen,pengeluaran_jumlah as jumlah,'pengeluaran' as tipe,'keluar' as arus FROM tbl_pengeluaran WHERE rekening_id=? AND (pengeluaran_tgl BETWEEN ? AND ?) AND perusahaan_id IN ($ids_sql)", [$bank_id, $start_date, $end_date])->result_array();
            $mutasi = array_merge($q1, $q2);
            foreach ($mutasi as $m) {
                if ($m['arus'] == 'masuk')
                    $total_masuk += $m['jumlah'];
                else
                    $total_keluar += $m['jumlah'];
            }
        }
        $data = array_merge($this->_owner_data($token, $perusahaan_id, 'Mutasi Bank'), [
            'start_date' => $start_date,
            'end_date' => $end_date,
            'selected_bank' => $bank_id,
            'rekening_list' => $rekening_list,
            'mutasi' => $mutasi,
            'total_masuk' => $total_masuk,
            'total_keluar' => $total_keluar,
        ]);
        $this->_owner_view($token, 'owner_mobile/rpt_mutasi_bank', $data);
    }

    public function rpt_mutasi_cash($token = null)
    {
        $this->_check_token($token);
        $perusahaan_id = $this->input->get('perusahaan_id', true) ?: 1;
        $ids = $this->_get_ids($perusahaan_id);
        $ids_sql = implode(',', $ids);
        $tgl_awal = $this->input->get('tgl_awal', true) ?: date('Y-m-01');
        $tgl_akhir = $this->input->get('tgl_akhir', true) ?: date('Y-m-d');

        $saldo_awal_trx = $this->db->query("SELECT SUM(pd.jumlah) as total FROM tbl_pembayaran_detail pd JOIN tbl_pembayaran p ON pd.pembayaran_id=p.pembayaran_id WHERE DATE(pd.created_at)<? AND pd.metode='tunai' AND p.perusahaan_id IN ($ids_sql)", [$tgl_awal])->row()->total ?? 0;
        $saldo_awal_peng = $this->db->query("SELECT SUM(pengeluaran_jumlah) as total FROM tbl_pengeluaran WHERE pengeluaran_tgl<? AND metode_bayar='tunai' AND perusahaan_id IN ($ids_sql)", [$tgl_awal])->row()->total ?? 0;
        $saldo_awal = $saldo_awal_trx - $saldo_awal_peng;

        $mutasi = $this->db->query("
            SELECT * FROM (
                SELECT CASE WHEN DATE(pd.created_at)>p.tgl_bayar THEN 'PIUTANG' ELSE 'TRANSAKSI' END as sumber,
                       DATE(pd.created_at) as tanggal, TIME(pd.created_at) as jam, p.nospk, k.konsumen_nama as konsumen,
                       pd.jumlah as masuk, 0 as keluar,
                       CASE WHEN DATE(pd.created_at)>p.tgl_bayar THEN CONCAT('Pelunasan Piutang SPK: ',p.nospk) ELSE CONCAT('Pembayaran SPK: ',p.nospk) END as keterangan
                FROM tbl_pembayaran_detail pd JOIN tbl_pembayaran p ON pd.pembayaran_id=p.pembayaran_id LEFT JOIN tbl_konsumen k ON p.konsumen_id=k.konsumen_id
                WHERE DATE(pd.created_at) BETWEEN ? AND ? AND pd.metode='tunai' AND p.perusahaan_id IN ($ids_sql)
                UNION ALL
                SELECT 'PENGELUARAN',pengeluaran_tgl,'00:00:00','-','-',0,pengeluaran_jumlah,pengeluaran_detail
                FROM tbl_pengeluaran WHERE pengeluaran_tgl BETWEEN ? AND ? AND metode_bayar='tunai' AND perusahaan_id IN ($ids_sql)
            ) as combined ORDER BY tanggal ASC, jam ASC
        ", [$tgl_awal, $tgl_akhir, $tgl_awal, $tgl_akhir])->result_array();

        $data = array_merge($this->_owner_data($token, $perusahaan_id, 'Mutasi Kas Tunai'), [
            'tgl_awal' => $tgl_awal,
            'tgl_akhir' => $tgl_akhir,
            'saldo_awal' => $saldo_awal,
            'mutasi' => $mutasi,
        ]);
        $this->_owner_view($token, 'owner_mobile/rpt_mutasi_cash', $data);
    }

    public function rpt_mutasi_ewallet($token = null)
    {
        $this->_check_token($token);
        $perusahaan_id = $this->input->get('perusahaan_id', true) ?: 1;
        $ids = $this->_get_ids($perusahaan_id);
        $ids_sql = implode(',', $ids);
        $start_date = $this->input->get('start_date', true) ?: date('Y-m-01');
        $end_date = $this->input->get('end_date', true) ?: date('Y-m-d');
        $ewallet_id = $this->input->get('ewallet_id', true);

        $ewallet_list = $this->db->get('tbl_ewallet')->result();
        $mutasi = [];
        $total_masuk = $total_keluar = 0;
        if ($ewallet_id) {
            $q1 = $this->db->query("SELECT DATE(pd.created_at) as tanggal,TIME(pd.created_at) as jam,p.nospk,k.konsumen_nama as konsumen,pd.jumlah,CASE WHEN DATE(pd.created_at)>p.tgl_bayar THEN 'piutang' ELSE 'transaksi' END as tipe,'masuk' as arus FROM tbl_pembayaran_detail pd JOIN tbl_pembayaran p ON pd.pembayaran_id=p.pembayaran_id LEFT JOIN tbl_konsumen k ON p.konsumen_id=k.konsumen_id WHERE pd.ewallet_id=? AND (DATE(pd.created_at) BETWEEN ? AND ?) AND p.perusahaan_id IN ($ids_sql)", [$ewallet_id, $start_date, $end_date])->result_array();
            $q2 = $this->db->query("SELECT pengeluaran_tgl as tanggal,'00:00:00' as jam,'-' as nospk,pengeluaran_penerima as konsumen,pengeluaran_jumlah as jumlah,'pengeluaran' as tipe,'keluar' as arus FROM tbl_pengeluaran WHERE ewallet_id=? AND (pengeluaran_tgl BETWEEN ? AND ?) AND perusahaan_id IN ($ids_sql)", [$ewallet_id, $start_date, $end_date])->result_array();
            $mutasi = array_merge($q1, $q2);
            foreach ($mutasi as $m) {
                if ($m['arus'] == 'masuk')
                    $total_masuk += $m['jumlah'];
                else
                    $total_keluar += $m['jumlah'];
            }
        }
        $data = array_merge($this->_owner_data($token, $perusahaan_id, 'Mutasi E-Wallet / EDC'), [
            'start_date' => $start_date,
            'end_date' => $end_date,
            'selected_ewallet' => $ewallet_id,
            'ewallet_list' => $ewallet_list,
            'mutasi' => $mutasi,
            'total_masuk' => $total_masuk,
            'total_keluar' => $total_keluar,
        ]);
        $this->_owner_view($token, 'owner_mobile/rpt_mutasi_ewallet', $data);
    }

    public function rpt_marketplace($token = null)
    {
        $this->_check_token($token);
        $perusahaan_id = $this->input->get('perusahaan_id', true) ?: 1;
        $ids = $this->_get_ids($perusahaan_id);
        $ids_sql = implode(',', $ids);
        $bulan_ini = $this->input->get('bulan', true) ?: date('Y-m');
        $start_date = $bulan_ini . '-01';
        $end_date = date('Y-m-t', strtotime($start_date));
        $market_id = $this->input->get('marketplace_id', true);
        $mf = $market_id ? 'AND pm.marketplace_id=' . (int) $market_id : '';

        $pemasukan_list = $this->db->query("
            SELECT DATE(pm.created_at) AS tanggal, TIME(pm.created_at) AS jam, pm.nospk,
                   COALESCE(k.konsumen_nama, pm.resi_marketplace) AS konsumen, k.konsumen_nohp AS telepon,
                   pm.resi_marketplace AS resi, COALESCE(mk.marketplace_nama,'Lainnya') AS platform,
                   pm.status_pembayaran, COALESCE(SUM(pp.snapshot_subtotal),0) AS total_tagihan,
                   COALESCE(SUM(pp.snapshot_subtotal),0) AS total_bayar, 0 AS sisa_piutang,
                   '-' AS kasir, 'Lunas (Marketplace)' AS status_transaksi
            FROM tbl_pemesanan pm LEFT JOIN tbl_konsumen k ON pm.konsumen_id=k.konsumen_id
            LEFT JOIN tbl_marketplace mk ON pm.marketplace_id=mk.marketplace_id
            LEFT JOIN tbl_pembayaran_produk pp ON pm.nospk=pp.nospk
            WHERE pm.id_jenis_transaksi=2 AND pm.status_pembayaran='paid'
              AND (DATE(pm.created_at) BETWEEN ? AND ?) AND pm.perusahaan_id IN ($ids_sql) $mf
            GROUP BY pm.nospk ORDER BY pm.created_at DESC
        ", [$start_date, $end_date])->result_array();

        $by_platform = $this->db->query("
            SELECT COALESCE(mk.marketplace_nama,'Lainnya') AS platform, COUNT(pm.nospk) AS jumlah_transaksi,
                   COALESCE(SUM(pp.snapshot_subtotal),0) AS total_omset
            FROM tbl_pemesanan pm LEFT JOIN tbl_marketplace mk ON pm.marketplace_id=mk.marketplace_id
            LEFT JOIN tbl_pembayaran_produk pp ON pm.nospk=pp.nospk
            WHERE pm.id_jenis_transaksi=2 AND pm.status_pembayaran='paid'
              AND (DATE(pm.created_at) BETWEEN ? AND ?) AND pm.perusahaan_id IN ($ids_sql)
            GROUP BY pm.marketplace_id ORDER BY total_omset DESC
        ", [$start_date, $end_date])->result_array();

        $total_omset = 0;
        foreach ($pemasukan_list as $pl)
            $total_omset += $pl['total_tagihan'];

        $data = array_merge($this->_owner_data($token, $perusahaan_id, 'Laporan Marketplace'), [
            'bulan_ini' => $bulan_ini,
            'start_date' => $start_date,
            'end_date' => $end_date,
            'market_id_filter' => $market_id,
            'all_marketplace' => $this->db->get('tbl_marketplace')->result(),
            'pemasukan_list' => $pemasukan_list,
            'by_platform' => $by_platform,
            'stat_keuangan' => ['total_transaksi' => count($pemasukan_list), 'omset_penjualan' => $total_omset],
        ]);
        $this->_owner_view($token, 'owner_mobile/rpt_marketplace', $data);
    }

    public function rpt_kinerja_operator($token = null)
    {
        $this->_check_token($token);
        $perusahaan_id = $this->input->get('perusahaan_id', true) ?: 1;
        $bulan_ini = $this->input->get('bulan') ?: date('Y-m');
        $start_date = $bulan_ini . '-01';
        $end_date = date('Y-m-t', strtotime($start_date));

        $this->load->model('Produksi_model');
        $kinerja = $this->Produksi_model->get_kinerja_operator($start_date, $end_date);

        $data = array_merge($this->_owner_data($token, $perusahaan_id, 'Kinerja Operator'), [
            'bulan_ini' => $bulan_ini,
            'start_date' => $start_date,
            'end_date' => $end_date,
            'kinerja' => $kinerja
        ]);
        $this->_owner_view($token, 'owner_mobile/rpt_kinerja_operator', $data);
    }

    // ==========================================
    // MODULE: SUMBER DAYA MANUSIA (SDM)
    // ==========================================

    public function rpt_kehadiran($token = null)
    {
        $this->_check_token($token);

        $perusahaan_id = $this->input->get('perusahaan_id', true) ?: 1;
        $bulan_ini = $this->input->get('bulan') ?: date('Y-m');
        $start_date = $bulan_ini . '-01';
        $end_date = date('Y-m-t', strtotime($start_date));

        $this->load->model('Absensi_model');
        $rekap_all_raw = $this->Absensi_model->get_rekap_all_karyawan($start_date, $end_date);

        $rekap_all = [];
        if ($perusahaan_id != 1) {
            foreach ($rekap_all_raw as $r) {
                if (isset($r->perusahaan_id) && $r->perusahaan_id == $perusahaan_id) {
                    $rekap_all[] = $r;
                }
            }
        } else {
            $rekap_all = $rekap_all_raw;
        }

        // Count global stats
        $global_stats = ['hadir' => 0, 'telat' => 0, 'alpha' => 0, 'izin' => 0, 'sakit' => 0, 'cuti' => 0, 'total' => count($rekap_all)];
        foreach ($rekap_all as $r) {
            $global_stats['hadir'] += $r->total_hadir;
            $global_stats['telat'] += $r->total_terlambat;
            $global_stats['alpha'] += $r->total_alpha;
            $global_stats['izin'] += $r->total_izin;
            $global_stats['sakit'] += $r->total_sakit;
            $global_stats['cuti'] += $r->total_cuti;
        }

        $data = array_merge($this->_owner_data($token, $perusahaan_id, 'Kehadiran & Absensi'), [
            'bulan_ini' => $bulan_ini,
            'start_date' => $start_date,
            'end_date' => $end_date,
            'rekap_all' => $rekap_all,
            'stats' => $global_stats
        ]);

        $this->_owner_view($token, 'owner_mobile/rpt_kehadiran', $data);
    }

    public function rpt_kpi_karyawan($token = null)
    {
        $this->_check_token($token);

        $perusahaan_id = $this->input->get('perusahaan_id', true) ?: 1;
        $bulan_ini = $this->input->get('bulan') ?: date('Y-m');
        $start_date = $bulan_ini . '-01';
        $end_date = date('Y-m-t', strtotime($start_date));

        $this->load->model('Absensi_model');
        $rekap_all_raw = $this->Absensi_model->get_rekap_all_karyawan($start_date, $end_date);

        $rekap_all = [];
        if ($perusahaan_id != 1) {
            foreach ($rekap_all_raw as $r) {
                if (isset($r->perusahaan_id) && $r->perusahaan_id == $perusahaan_id) {
                    $rekap_all[] = $r;
                }
            }
        } else {
            $rekap_all = $rekap_all_raw;
        }

        // Calculate KPI Score for each employee
        foreach ($rekap_all as $k) {
            $score = 100 - ($k->total_terlambat * 2) - ($k->total_alpha * 5) - ($k->total_izin * 1) - ($k->total_sakit * 1) - ($k->total_cuti * 1);
            $k->score = max(0, $score);
            $k->predicate = ($score >= 90) ? 'ISTIMEWA' : (($score >= 80) ? 'BAIK' : 'CUKUP');
        }

        // Sort by score DESC
        usort($rekap_all, function ($a, $b) {
            return $b->score <=> $a->score;
        });

        $data = array_merge($this->_owner_data($token, $perusahaan_id, 'Kinerja & KPI Karyawan'), [
            'bulan_ini' => $bulan_ini,
            'start_date' => $start_date,
            'end_date' => $end_date,
            'kpi_karyawan' => $rekap_all
        ]);

        $this->_owner_view($token, 'owner_mobile/rpt_kpi_karyawan', $data);
    }

    public function rpt_penggajian($token = null)
    {
        $this->_check_token($token);

        $perusahaan_id = $this->input->get('perusahaan_id', true) ?: 1;
        $bulan_ini = $this->input->get('bulan') ?: date('Y-m');

        $this->load->model('Payroll_model');
        $salary_data = $this->Payroll_model->get_salary_data($bulan_ini);

        // Filter by perusahaan if not 'Semua' (1)
        if ($perusahaan_id != 1) {
            $salary_data_filtered = [];
            foreach ($salary_data as $sd) {
                // If the array returned by get_salary_data is objects/arrays
                $pid = is_array($sd) ? ($sd['perusahaan_id'] ?? null) : ($sd->perusahaan_id ?? null);

                if ($pid == $perusahaan_id) {
                    $salary_data_filtered[] = $sd;
                } elseif ($pid === null) {
                    $salary_data_filtered[] = $sd;
                }
            }
            $salary_data = $salary_data_filtered;
        }

        $total_gaji_pokok = 0;
        $total_lembur = 0;
        $total_thp = 0;
        $total_kasbon = 0;

        foreach ($salary_data as $sd) {
            $total_gaji_pokok += is_array($sd) ? ($sd['gaji_pokok'] ?? 0) : ($sd->gaji_pokok ?? 0);
            $total_lembur += is_array($sd) ? ($sd['uang_lembur'] ?? 0) : ($sd->uang_lembur ?? 0);
            $total_kasbon += is_array($sd) ? ($sd['kasbon'] ?? 0) : ($sd->kasbon ?? 0);
            $total_thp += is_array($sd) ? ($sd['total_gaji'] ?? 0) : ($sd->total_gaji ?? 0);
        }

        $data = array_merge($this->_owner_data($token, $perusahaan_id, 'Penggajian (Payroll)'), [
            'bulan_ini' => $bulan_ini,
            'salary_data' => $salary_data,
            'total_gaji_pokok' => $total_gaji_pokok,
            'total_lembur' => $total_lembur,
            'total_kasbon' => $total_kasbon,
            'total_thp' => $total_thp
        ]);

        $this->_owner_view($token, 'owner_mobile/rpt_penggajian', $data);
    }

    public function rpt_gudang_stok($token = null)
    {
        $this->_check_token($token);

        $perusahaan_id = $this->input->get('perusahaan_id', true) ?: 'all';

        // Tentukan gudang yang aktif berdasarkan cabang terpilih
        // 1 atau 'all' -> [1, 2, 3] (semua gudang)
        // 2 -> [1, 2] (Gudang Utama & Gudang Produksi 1)
        // 3 -> [3] (Gudang Produksi 2)
        $wh_ids = [];
        if ($perusahaan_id === 'all' || $perusahaan_id == 1) {
            $wh_ids = [1, 2, 3];
        } elseif ($perusahaan_id == 2) {
            $wh_ids = [1, 2];
        } elseif ($perusahaan_id == 3) {
            $wh_ids = [3];
        } else {
            $wh_ids = [1, 2, 3]; // fallback
        }

        // 1. Get warehouses details
        $this->db->select('gudang_id, gudang_nama, gudang_lokasi');
        $this->db->where_in('gudang_id', $wh_ids);
        $warehouses = $this->db->get('tbl_gudang')->result();

        // 2. Build Dynamic Pivot Query
        $this->db->select('m.material_id, CONCAT(b.bahan_nama, " - ", m.varian) as name, s.satuan_nama as unit, m.konversi, sp.satuan_nama as satuan_produksi');

        // Add dynamic warehouse columns
        foreach ($warehouses as $w) {
            $this->db->select("MAX(CASE WHEN sg.gudang_id = {$w->gudang_id} THEN sg.qty ELSE 0 END) as wh_{$w->gudang_id}");
        }

        if (!empty($wh_ids)) {
            $wh_list_str = implode(',', $wh_ids);
            $this->db->select("SUM(CASE WHEN sg.gudang_id IN ($wh_list_str) THEN COALESCE(sg.qty, 0) ELSE 0 END) as total_qty");
        } else {
            $this->db->select("0 as total_qty");
        }

        $this->db->from('tbl_material m');
        $this->db->join('tbl_bahan b', 'm.bahan_id = b.bahan_id');
        $this->db->join('tbl_satuan s', 'm.satuan_gudang_id = s.satuan_id');
        $this->db->join('tbl_satuan sp', 'b.satuan_id = sp.satuan_id', 'left');
        $this->db->join('tbl_stok_gudang sg', 'm.material_id = sg.material_id', 'left');
        $this->db->where_in('m.status', [1, '1']);
        $this->db->group_by('m.material_id, b.bahan_nama, m.varian, s.satuan_nama, m.konversi, sp.satuan_nama');
        $this->db->order_by('b.bahan_nama', 'ASC');

        $query_results = $this->db->get()->result();

        // Format data
        $data_list = [];
        foreach ($query_results as $row) {
            $item = [
                'material_id' => $row->material_id,
                'name' => $row->name,
                'unit' => $row->unit,
                'konversi' => (float)$row->konversi,
                'satuan_produksi' => $row->satuan_produksi ?: 'Pcs',
                'total_qty' => (float) $row->total_qty,
                'warehouses' => []
            ];

            foreach ($warehouses as $w) {
                $col = "wh_{$w->gudang_id}";
                $item['warehouses'][$w->gudang_id] = (float) $row->$col;
            }
            $data_list[] = $item;
        }

        $data = array_merge($this->_owner_data($token, $perusahaan_id, 'Stok & Inventaris'), [
            'warehouses' => $warehouses,
            'data_list' => $data_list
        ]);

        $this->_owner_view($token, 'owner_mobile/rpt_gudang_stok', $data);
    }

    public function rpt_gudang_kritis($token = null)
    {
        $this->_check_token($token);

        $perusahaan_id = $this->input->get('perusahaan_id', true) ?: 'all';

        $wh_ids = [];
        if ($perusahaan_id === 'all' || $perusahaan_id == 1) {
            $wh_ids = [1, 2, 3];
        } elseif ($perusahaan_id == 2) {
            $wh_ids = [1, 2];
        } elseif ($perusahaan_id == 3) {
            $wh_ids = [3];
        } else {
            $wh_ids = [1, 2, 3];
        }

        $this->db->select('
            b.bahan_nama, m.varian, s.satuan_nama as unit,
            g.gudang_nama, g.gudang_id,
            sg.qty as stok_aktif,
            m.stok_min,
            (m.stok_min - sg.qty) as kekurangan,
            m.konversi,
            sp.satuan_nama as satuan_produksi
        ');
        $this->db->from('tbl_stok_gudang sg');
        $this->db->join('tbl_material m', 'sg.material_id = m.material_id');
        $this->db->join('tbl_bahan b', 'm.bahan_id = b.bahan_id');
        $this->db->join('tbl_satuan s', 'm.satuan_gudang_id = s.satuan_id', 'left');
        $this->db->join('tbl_satuan sp', 'b.satuan_id = sp.satuan_id', 'left');
        $this->db->join('tbl_gudang g', 'sg.gudang_id = g.gudang_id', 'left');
        $this->db->where_in('sg.gudang_id', $wh_ids);
        $this->db->where('sg.qty <= m.stok_min');
        $this->db->where_in('m.status', [1, '1']);
        $this->db->order_by('kekurangan', 'DESC');
        $this->db->order_by('b.bahan_nama', 'ASC');

        $kritis_list = $this->db->get()->result();

        $data = array_merge($this->_owner_data($token, $perusahaan_id, 'Stok Kritis (Minim)'), [
            'kritis_list' => $kritis_list
        ]);

        $this->_owner_view($token, 'owner_mobile/rpt_gudang_kritis', $data);
    }

    public function rpt_gudang_nilai($token = null)
    {
        $this->_check_token($token);

        $perusahaan_id = $this->input->get('perusahaan_id', true) ?: 'all';

        $wh_ids = [];
        if ($perusahaan_id === 'all' || $perusahaan_id == 1) {
            $wh_ids = [1, 2, 3];
        } elseif ($perusahaan_id == 2) {
            $wh_ids = [1, 2];
        } elseif ($perusahaan_id == 3) {
            $wh_ids = [3];
        } else {
            $wh_ids = [1, 2, 3];
        }

        $this->db->select('
            b.bahan_nama, m.varian, s.satuan_nama as unit,
            g.gudang_nama, g.gudang_id,
            sg.qty as stok_aktif,
            m.konversi,
            sp.satuan_nama as satuan_produksi,
            (SELECT pd.harga_beli 
             FROM tbl_pembelian_detail pd 
             WHERE pd.material_id = m.material_id 
             ORDER BY pd.pembelian_id DESC LIMIT 1) as harga_beli_terakhir,
            (sg.qty * (SELECT pd.harga_beli 
             FROM tbl_pembelian_detail pd 
             WHERE pd.material_id = m.material_id 
             ORDER BY pd.pembelian_id DESC LIMIT 1)) as nilai_stok
        ');
        $this->db->from('tbl_stok_gudang sg');
        $this->db->join('tbl_material m', 'sg.material_id = m.material_id');
        $this->db->join('tbl_bahan b', 'm.bahan_id = b.bahan_id');
        $this->db->join('tbl_satuan s', 'm.satuan_gudang_id = s.satuan_id', 'left');
        $this->db->join('tbl_satuan sp', 'b.satuan_id = sp.satuan_id', 'left');
        $this->db->join('tbl_gudang g', 'sg.gudang_id = g.gudang_id', 'left');
        $this->db->where_in('sg.gudang_id', $wh_ids);
        $this->db->where('sg.qty >', 0);
        $this->db->where_in('m.status', [1, '1']);
        $this->db->order_by('nilai_stok', 'DESC');

        $nilai_list = $this->db->get()->result();

        $total_aset = 0;
        foreach ($nilai_list as $n) {
            $total_aset += (float) $n->nilai_stok;
        }

        $data = array_merge($this->_owner_data($token, $perusahaan_id, 'Nilai Aset Inventaris'), [
            'nilai_list' => $nilai_list,
            'total_aset' => $total_aset
        ]);

        $this->_owner_view($token, 'owner_mobile/rpt_gudang_nilai', $data);
    }
}


