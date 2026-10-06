<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Api_auth extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        date_default_timezone_set("Asia/Jakarta");
        
        // Setup CORS for external Next.js access
        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Methods: POST, OPTIONS");
        header("Access-Control-Allow-Headers: Content-Type, Authorization");
        
        if ($this->input->method() === 'options') {
            exit;
        }
    }

    public function login()
    {
        // Get JSON Input
        $input = json_decode(file_get_contents('php://input'), true);
        $wa = isset($input['wa']) ? trim($input['wa']) : '';

        if (empty($wa)) {
            echo json_encode(['status' => 'error', 'message' => 'No. WhatsApp harus diisi']);
            return;
        }

        // Format WA (optional, tapi baiknya dibiarkan apa adanya atau dibersihkan)
        // Kita cari di database
        $konsumen = $this->db->get_where('tbl_konsumen', ['konsumen_nohp' => $wa])->row_array();

        if ($konsumen) {
            echo json_encode([
                'status' => 'success',
                'message' => 'Berhasil Masuk',
                'data' => [
                    'id' => $konsumen['konsumen_id'],
                    'nama' => $konsumen['konsumen_nama'],
                    'wa' => $konsumen['konsumen_nohp']
                ]
            ]);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Nomor WhatsApp belum terdaftar. Silakan daftar terlebih dahulu.']);
        }
    }

    public function register()
    {
        // Get JSON Input
        $input = json_decode(file_get_contents('php://input'), true);
        $nama = isset($input['nama']) ? trim($input['nama']) : '';
        $wa = isset($input['wa']) ? trim($input['wa']) : '';

        if (empty($nama) || empty($wa)) {
            echo json_encode(['status' => 'error', 'message' => 'Nama dan No. WhatsApp harus diisi']);
            return;
        }

        // Cek apakah sudah terdaftar
        $exist = $this->db->get_where('tbl_konsumen', ['konsumen_nohp' => $wa])->row_array();
        if ($exist) {
            echo json_encode(['status' => 'error', 'message' => 'Nomor WhatsApp sudah terdaftar. Silakan langsung masuk (login).']);
            return;
        }

        // Insert ke tabel konsumen
        $data = [
            'konsumen_nama' => $nama,
            'konsumen_nohp' => $wa,
            'status' => '1',
            'konsumen_alamat' => '-'
        ];

        if ($this->db->insert('tbl_konsumen', $data)) {
            $insert_id = $this->db->insert_id();
            echo json_encode([
                'status' => 'success',
                'message' => 'Pendaftaran berhasil',
                'data' => [
                    'id' => $insert_id,
                    'nama' => $nama,
                    'wa' => $wa
                ]
            ]);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Gagal mendaftar, silakan coba lagi']);
        }
    }

    public function history()
    {
        $konsumen_id = $this->input->get('konsumen_id');
        
        if (empty($konsumen_id)) {
            echo json_encode(['status' => 'error', 'message' => 'ID Konsumen diperlukan.']);
            return;
        }

        $this->db->select('nospk, created_at, status_pembayaran, id_jenis_transaksi, (SELECT SUM(snapshot_subtotal) FROM tbl_pemesanan_detail WHERE tbl_pemesanan_detail.nospk = tbl_pemesanan.nospk) as total_harga');
        $this->db->from('tbl_pemesanan');
        $this->db->where('konsumen_id', $konsumen_id);
        $this->db->order_by('created_at', 'DESC');
        $this->db->limit(50);
        $orders = $this->db->get()->result_array();

        // Optional: format dates and statuses
        foreach ($orders as &$order) {
            $order['tanggal'] = date('d M Y H:i', strtotime($order['created_at']));
            $order['total_rp'] = 'Rp ' . number_format($order['total_harga'] ?? 0, 0, ',', '.');
            
            // Map payment status to Indonesian
            $status_map = [
                'paid' => 'Lunas',
                'pending' => 'Belum Lunas',
                'cancel' => 'Dibatalkan'
            ];
            $order['status_label'] = isset($status_map[$order['status_pembayaran']]) ? $status_map[$order['status_pembayaran']] : $order['status_pembayaran'];
        }

        echo json_encode([
            'status' => 'success',
            'data' => $orders
        ]);
    }

    public function cek_spk()
    {
        $nospk = $this->input->get('nospk');
        
        if (empty($nospk)) {
            echo json_encode(['status' => 'error', 'message' => 'Nomor SPK diperlukan.']);
            return;
        }

        $nospk = trim($nospk);

        // 1. Order Data & Customer & Staff
        $this->db->select('p.nospk, p.created_at, p.status_pembayaran, p.id_jenis_transaksi, 
                          k.konsumen_nama, k.konsumen_nohp, k.konsumen_email, k.konsumen_alamat,
                          u.username, kr.nama_lengkap as kasir_nama');
        $this->db->from('tbl_pemesanan p');
        $this->db->join('tbl_konsumen k', 'p.konsumen_id = k.konsumen_id', 'left');
        $this->db->join('users u', 'p.deskprint_id = u.user_id', 'left');
        $this->db->join('tbl_karyawan kr', 'u.karyawan_id = kr.karyawan_id', 'left');
        $this->db->where('p.nospk', $nospk);
        $order = $this->db->get()->row_array();

        if (!$order) {
            echo json_encode(['status' => 'error', 'message' => 'Pesanan dengan Nomor SPK tersebut tidak ditemukan.']);
            return;
        }

        // 2. Details (Items)
        $this->db->select('pd.product_id, pd.qty, pd.snapshot_subtotal as subtotal, pd.panjang, pd.lebar, pr.product_nama');
        $this->db->from('tbl_pemesanan_detail pd');
        $this->db->join('tbl_product pr', 'pd.product_id = pr.product_id', 'left');
        $this->db->where('pd.nospk', $nospk);
        $items = $this->db->get()->result_array();

        $total_harga = 0;
        foreach($items as $i) {
            $total_harga += (float)$i['subtotal'];
        }

        // 3. Payment
        $this->db->select('SUM(total_bayar) as total_bayar, MAX(total_tagihan) as total_tagihan, MAX(tgl_bayar) as tgl_bayar, MAX(jam_bayar) as jam_bayar');
        $this->db->from('tbl_pembayaran');
        $this->db->where('nospk', $nospk);
        $payment = $this->db->get()->row_array();
        
        $total_bayar = $payment['total_bayar'] ? (float)$payment['total_bayar'] : 0;
        $sisa_tagihan = $total_harga - $total_bayar;
        if($sisa_tagihan < 0) $sisa_tagihan = 0;

        // 4. Timeline
        $this->db->select('status_produksi, tgl_mulai, tgl_selesai, update_time');
        $this->db->from('tbl_produksi');
        $this->db->where('nospk', $nospk);
        $this->db->order_by('produksi_id', 'ASC');
        $produksi = $this->db->get()->row_array();

        $timeline = [];
        $timeline[] = ['status' => 'Pesanan Dibuat', 'time' => $order['created_at'], 'done' => true];
        
        if ($total_bayar > 0) {
            $time = $payment['tgl_bayar'] . ' ' . $payment['jam_bayar'];
            $timeline[] = ['status' => 'Pembayaran Diterima', 'time' => $time, 'done' => true];
        } else {
            $timeline[] = ['status' => 'Menunggu Pembayaran', 'time' => '', 'done' => false];
        }
        
        if ($produksi && $produksi['tgl_mulai']) {
            $timeline[] = ['status' => 'Sedang Diproses', 'time' => $produksi['tgl_mulai'], 'done' => true];
        } else {
            $timeline[] = ['status' => 'Sedang Diproses', 'time' => '', 'done' => false];
        }
        
        if ($produksi && $produksi['tgl_selesai']) {
            $timeline[] = ['status' => 'Selesai Produksi', 'time' => $produksi['tgl_selesai'], 'done' => true];
        } else {
            $timeline[] = ['status' => 'Selesai Produksi', 'time' => '', 'done' => false];
        }
        
        if ($produksi && $produksi['status_produksi'] == 'on_costumer') {
            $timeline[] = ['status' => 'Sudah Diambil', 'time' => $produksi['update_time'], 'done' => true];
        } else {
            $timeline[] = ['status' => 'Belum Diambil', 'time' => '', 'done' => false];
        }

        echo json_encode([
            'status' => 'success',
            'data' => [
                'customer' => [
                    'nama' => $order['konsumen_nama'] ? $order['konsumen_nama'] : '-',
                    'hp' => $order['konsumen_nohp'] ? $order['konsumen_nohp'] : '-',
                    'email' => $order['konsumen_email'] ? $order['konsumen_email'] : '-',
                    'alamat' => $order['konsumen_alamat'] ? $order['konsumen_alamat'] : '-',
                ],
                'order' => [
                    'nospk' => $order['nospk'],
                    'tanggal' => date('d F Y', strtotime($order['created_at'])),
                    'kasir' => $order['kasir_nama'] ? $order['kasir_nama'] : ($order['username'] ? $order['username'] : '-'),
                    'status_pembayaran' => $order['status_pembayaran'],
                    'total_harga' => $total_harga,
                    'total_bayar' => $total_bayar,
                    'sisa_tagihan' => $sisa_tagihan
                ],
                'items' => $items,
                'timeline' => $timeline
            ]
        ]);
    }
}
