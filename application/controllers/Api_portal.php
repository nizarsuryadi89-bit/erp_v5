<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class Api_portal extends MY_Controller
{
    private $secret_key = 'RahasiaNegara123!@#';

    public function __construct()
    {
        parent::__construct();

        $this->load->helper('url');
        $this->output->set_content_type('application/json');

        // Load Database ERP
        $this->load->database();
        // Izinkan akses dari domain Next.js
        $allowed_origins = ['https://tgmprint.com', 'http://localhost:3000'];
        if (isset($_SERVER['HTTP_ORIGIN']) && in_array($_SERVER['HTTP_ORIGIN'], $allowed_origins)) {
            header('Access-Control-Allow-Origin: ' . $_SERVER['HTTP_ORIGIN']);
        }
        header('Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE');
        header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With, X-API-KEY');

        // Tangani Preflight Request (Sangat Penting!)
        if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
            header("HTTP/1.1 200 OK");
            exit;
        }
    }

    private function verify_token()
    {
        $token = null;
        $authHeader = $this->input->get_request_header('Authorization');
        if ($authHeader) {
            preg_match('/Bearer\s(\S+)/', $authHeader, $matches);
            if (isset($matches[1])) {
                $token = $matches[1];
            }
        }
        if (!$token) {
            $token = $this->input->get_post('token');
        }

        if (!$token) {
            $this->output->set_status_header(401);
            echo json_encode(['status' => 'error', 'message' => 'Token tidak ditemukan. Akses ditolak.']);
            exit;
        }

        try {
            return JWT::decode($token, new Key($this->secret_key, 'HS256'))->data;
        } catch (Exception $e) {
            $this->output->set_status_header(401);
            echo json_encode(['status' => 'error', 'message' => 'Sesi kedaluwarsa atau token tidak valid.']);
            exit;
        }
    }

    private function verify_admin_key()
    {
        $api_key = $this->input->get_request_header('X-API-KEY');
        if (!$api_key) {
            $api_key = $this->input->get_post('api_key');
        }
        if ($api_key !== $this->secret_key) {
            $this->output->set_status_header(403);
            echo json_encode(['status' => 'error', 'message' => 'API Key salah atau akses ditolak.']);
            exit;
        }
    }

    // --- SETUP: Jalankan url ini sekali untuk membuat tabel baru secara otomatis ---
    public function setup_db()
    {
        $sql = "CREATE TABLE IF NOT EXISTS tbl_portal_users (
            user_id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            nama_lengkap VARCHAR(255) NOT NULL,
            email VARCHAR(150) NOT NULL UNIQUE,
            no_hp VARCHAR(20) NOT NULL,
            password VARCHAR(255) NOT NULL,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8;";

        if ($this->db->query($sql)) {
            echo json_encode(['status' => 'success', 'message' => 'Tabel tbl_portal_users berhasil dibuat dan siap digunakan!']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Gagal membuat tabel']);
        }
    }

    // --- ENDPOINT: Proses Pendaftaran Konsumen ---
    public function register()
    {
        // Pastikan tabel ada
        $this->_ensure_user_table();

        // Ambil data JSON yang dikirim oleh React
        $input = json_decode(file_get_contents('php://input'), true);
        if (!$input) {
            return $this->output->set_output(json_encode(['status' => 'error', 'message' => 'Data tidak boleh kosong.']));
        }

        $nama_lengkap = $this->db->escape_str($input['nama_lengkap'] ?? '');
        $email = $this->db->escape_str($input['email'] ?? '');
        $no_hp = $this->db->escape_str($input['no_hp'] ?? '');
        $password = $input['password'] ?? '';

        if (empty($nama_lengkap) || empty($email) || empty($password)) {
            return $this->output->set_output(json_encode(['status' => 'error', 'message' => 'Mohon lengkapi semua data wajib.']));
        }

        $this->db->where('email', $email);
        $cek_email = $this->db->get('tbl_portal_users')->num_rows();

        if ($cek_email > 0) {
            return $this->output->set_output(json_encode(['status' => 'error', 'message' => 'Email ini sudah terdaftar.']));
        }

        $hashed_password = password_hash($password, PASSWORD_BCRYPT);
        $data_insert = [
            'nama_lengkap' => $nama_lengkap,
            'email' => $email,
            'no_hp' => $no_hp,
            'password' => $hashed_password,
            'created_at' => date('Y-m-d H:i:s')
        ];

        if ($this->db->insert('tbl_portal_users', $data_insert)) {
            return $this->output->set_output(json_encode(['status' => 'success', 'message' => 'Pendaftaran berhasil!']));
        } else {
            return $this->output->set_output(json_encode(['status' => 'error', 'message' => 'Gagal menyimpan data ke database.']));
        }
    }

    // --- ENDPOINT: Proses Login Konsumen ---
    public function login()
    {
        $this->_ensure_user_table();

        $input = json_decode(file_get_contents('php://input'), true);
        if (!$input) {
            return $this->output->set_output(json_encode(['status' => 'error', 'message' => 'Data tidak boleh kosong.']));
        }

        $email = $this->db->escape_str($input['email'] ?? '');
        $password = $input['password'] ?? '';

        $this->db->where('email', $email);
        $user = $this->db->get('tbl_portal_users')->row_array();

        if ($user && password_verify($password, $user['password'])) {
            // Hapus password dari response demi keamanan
            unset($user['password']);

            $payload = [
                "iss" => base_url(),
                "iat" => time(),
                "exp" => time() + (86400 * 30),
                "data" => [
                    "user_id" => $user['user_id'],
                    "nama_lengkap" => $user['nama_lengkap'],
                    "email" => $user['email'],
                    "no_hp" => $user['no_hp']
                ]
            ];
            $token = JWT::encode($payload, $this->secret_key, 'HS256');

            return $this->output->set_output(json_encode([
                'status' => 'success',
                'message' => 'Login berhasil!',
                'user' => $user,
                'token' => $token
            ]));
        } else {
            return $this->output->set_output(json_encode(['status' => 'error', 'message' => 'Email atau Password salah.']));
        }
    }

    // Helper internal untuk memastikan tabel user ada
    public function check_tables()
    {
        $tables = ['tbl_pembayaran', 'tbl_pembayaran_produk', 'tbl_piutang', 'tbl_produksi', 'tbl_pembelian', 'tbl_pembelian_detail'];
        $res = [];
        foreach ($tables as $t) {
            $res[$t] = $this->db->table_exists($t);
        }
        return $this->output->set_output(json_encode($res));
    }

    private function _ensure_user_table()
    {
        if (!$this->db->table_exists('tbl_portal_users')) {
            $this->load->dbforge();
            $fields = [
                'user_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => TRUE, 'auto_increment' => TRUE],
                'nama_lengkap' => ['type' => 'VARCHAR', 'constraint' => 255],
                'email' => ['type' => 'VARCHAR', 'constraint' => 150, 'unique' => TRUE],
                'no_hp' => ['type' => 'VARCHAR', 'constraint' => 20],
                'password' => ['type' => 'VARCHAR', 'constraint' => 255],
                'created_at' => ['type' => 'DATETIME', 'null' => TRUE],
                'updated_at' => ['type' => 'DATETIME', 'null' => TRUE]
            ];
            $this->dbforge->add_field($fields);
            $this->dbforge->add_key('user_id', TRUE);
            $this->dbforge->create_table('tbl_portal_users');
        }
    }

    // Endpoint tes koneksi
    public function index()
    {
        echo json_encode(['status' => 'success', 'message' => 'API Portal aktif.']);
    }

    // --- ENDPOINT: Daftar Konsumen Baru ---
    public function consumer_register()
    {
        $input = json_decode(file_get_contents('php://input'), true);
        $nama = $input['nama'] ?? '';
        $wa = $input['whatsapp'] ?? '';
        $alamat = $input['alamat'] ?? '-';

        if (empty($nama) || empty($wa)) {
            echo json_encode(['status' => 'error', 'message' => 'Nama dan WhatsApp wajib diisi.']);
            return;
        }

        // Cek apakah sudah ada
        $exist = $this->db->get_where('tbl_konsumen', ['konsumen_nohp' => $wa])->row();
        if ($exist) {
            echo json_encode(['status' => 'error', 'message' => 'Nomor WhatsApp sudah terdaftar. Silakan langsung login.']);
            return;
        }

        $data = [
            'konsumen_nama' => $nama,
            'konsumen_nohp' => $wa,
            'konsumen_alamat' => $alamat,
            'status' => '1'
        ];

        if ($this->db->insert('tbl_konsumen', $data)) {
            $data['konsumen_id'] = $this->db->insert_id();
            echo json_encode(['status' => 'success', 'data' => $data]);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Gagal mendaftar.']);
        }
    }

    // --- ENDPOINT: Login Konsumen (via WA) ---
    public function consumer_login()
    {
        $wa = $this->input->post('whatsapp', true);
        if (empty($wa)) {
            $input = json_decode(file_get_contents('php://input'), true);
            $wa = $input['whatsapp'] ?? null;
        }

        if (!$wa) {
            echo json_encode(['status' => 'error', 'message' => 'Nomor WhatsApp wajib diisi.']);
            return;
        }

        $this->db->where('konsumen_nohp', $wa);
        $konsumen = $this->db->get('tbl_konsumen')->row_array();

        if ($konsumen) {
            $payload = [
                "iss" => base_url(),
                "iat" => time(),
                "exp" => time() + (86400 * 30), // 30 days
                "data" => [
                    "konsumen_id" => $konsumen['konsumen_id'],
                    "konsumen_nama" => $konsumen['konsumen_nama'],
                    "konsumen_nohp" => $konsumen['konsumen_nohp']
                ]
            ];
            $token = JWT::encode($payload, $this->secret_key, 'HS256');
            echo json_encode(['status' => 'success', 'data' => $konsumen, 'token' => $token]);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Nomor WhatsApp tidak terdaftar. Silakan lakukan pemesanan pertama Anda untuk terdaftar otomatis.']);
        }
    }

    // --- ENDPOINT: Riwayat Pesanan Konsumen ---
    public function get_consumer_orders()
    {
        $u = $this->verify_token();
        $wa = $u->no_hp ?? $u->konsumen_nohp ?? '';
        if(empty($wa)) {
            echo json_encode(['status' => 'error', 'message' => 'Identitas tidak valid.']);
            return;
        }
        $konsumen = $this->db->get_where('tbl_konsumen', ['konsumen_nohp' => $wa])->row();
        if(!$konsumen) {
             echo json_encode(['status' => 'success', 'data' => [], 'total_count' => 0, 'total_debt' => 0]);
             return;
        }
        $konsumen_id = $konsumen->konsumen_id;

        // Debug mode: Tangkap error DB
        $this->db->db_debug = FALSE;

        // 1. Cek Tabel dan Kolom Piutang
        $total_debt = 0;
        $has_piutang_table = $this->db->table_exists('tbl_piutang');
        $col_sisa = 'piutang_sisa';

        if ($has_piutang_table) {
            // Cek nama kolom yang benar (beberapa versi ERP pakai sisa_piutang)
            if (!$this->db->field_exists('piutang_sisa', 'tbl_piutang')) {
                if ($this->db->field_exists('sisa_piutang', 'tbl_piutang')) {
                    $col_sisa = 'sisa_piutang';
                }
            }

            $sql_debt = "SELECT SUM($col_sisa) as total FROM tbl_piutang WHERE nospk IN (SELECT nospk FROM tbl_pembayaran WHERE konsumen_id = ?)";
            $q_debt = $this->db->query($sql_debt, [$konsumen_id]);

            if ($q_debt) {
                $res_debt = $q_debt->row();
                $total_debt = (float) ($res_debt->total ?? 0);
            }
        }

        // 2. Hitung total transaksi
        $total_all = $this->db->where('konsumen_id', $konsumen_id)->count_all_results('tbl_pembayaran');

        // 3. Ambil list pesanan
        $this->db->where('konsumen_id', $konsumen_id);
        $this->db->order_by('pembayaran_id', 'DESC');
        $this->db->limit(20);
        $q_orders = $this->db->get('tbl_pembayaran');

        if (!$q_orders) {
            $err = $this->db->error();
            echo json_encode(['status' => 'error', 'message' => 'DB Error: ' . $err['message']]);
            return;
        }

        $orders = $q_orders->result_array();

        foreach ($orders as &$order) {
            $spk = $order['nospk'];

            // Ambil piutang per SPK
            $order['sisa_bayar'] = 0;
            if ($has_piutang_table) {
                $piut = $this->db->get_where('tbl_piutang', ['nospk' => $spk])->row();
                $order['sisa_bayar'] = (float) ($piut->$col_sisa ?? 0);
            }

            $order['created_at'] = $order['tgl_bayar'] ?? $order['created_at'] ?? date('Y-m-d H:i:s');

            // 4. Item Produk
            $this->db->select('pp.*, prod.product_nama');
            $this->db->from('tbl_pembayaran_produk pp');
            $this->db->join('tbl_product prod', 'prod.product_id = pp.product_id', 'left');
            $this->db->where('pp.nospk', $spk);
            $items_raw = $this->db->get()->result_array();

            $items = [];
            foreach ($items_raw as $it) {
                $harga_satuan = (float) ($it['snapshot_harga'] ?? $it['harga'] ?? 0);
                $qty = (float) ($it['qty'] ?? 1);
                $it['total_harga'] = ($harga_satuan > 0) ? ($harga_satuan * $qty) : (float) ($it['subtotal'] ?? $it['snapshot_subtotal'] ?? 0);
                $items[] = $it;
            }
            $order['items'] = $items;

            // 5. Produksi
            $prod_status = $this->db->get_where('tbl_produksi', ['nospk' => $spk])->row();
            $order['status_produksi'] = $prod_status->status_produksi ?? '0';

            $order['status_pembayaran'] = ($order['sisa_bayar'] <= 0) ? 'paid' : 'pending';
        }

        echo json_encode([
            'status' => 'success',
            'data' => $orders,
            'total_count' => $total_all,
            'total_debt' => $total_debt
        ]);
    }

    public function upload_design()
    {
        $config['upload_path'] = './assets/uploads/designs/';
        $config['allowed_types'] = 'pdf|jpg|png|jpeg|zip|rar|ai|psd|cdr';
        $config['max_size'] = 51200; // 50MB
        $config['encrypt_name'] = TRUE;

        if (!is_dir($config['upload_path'])) {
            mkdir($config['upload_path'], 0777, TRUE);
        }

        $this->load->library('upload', $config);

        if (!$this->upload->do_upload('design_file')) {
            echo json_encode(['status' => 'error', 'message' => $this->upload->display_errors('', '')]);
        } else {
            $data = $this->upload->data();
            $file_url = base_url('assets/uploads/designs/' . $data['file_name']);
            echo json_encode(['status' => 'success', 'url' => $file_url]);
        }
    }

    // Contoh endpoint produk (Mengambil variasi produk, spesifikasi, dan jenis sisi Cetak)
    public function get_produk()
    {
        $kategori_id = $this->input->get('category_id', true);

        // Ambil data produk berdasarkan tier harga dasar (qty_min = 1) untuk dijadikan display awal
        $this->db->select('p.product_id as id_produk, p.product_nama, p.kategori_id, hp.detail_product, hp.ket_1, hp.harga_1 as harga, hp.harga_id');
        $this->db->from('tbl_harga_product hp');
        $this->db->join('tbl_product p', 'hp.product_id = p.product_id');
        $this->db->where('hp.harga_1 >', 0);

        if (!empty($kategori_id)) {
            $this->db->where('p.kategori_id', $kategori_id);
        }

        // Asumsi qty_min = 1 adalah patokan harga ecer/dasar
        $this->db->group_start();
        $this->db->where('hp.qty_min', 1);
        $this->db->or_where('hp.qty_min', 0); // beberapa data mungkin terisi 0
        $this->db->group_end();

        $this->db->group_by('p.product_id, hp.detail_product, hp.ket_1');
        $this->db->order_by('p.product_nama', 'ASC');
        $this->db->limit(500); // Naikkan limit agar katalog lebih lengkap

        $raw_data = $this->db->get()->result_array();

        $data_produk = [];
        foreach ($raw_data as $row) {
            $nama_lengkap = $row['product_nama'];

            // Tambahkan keterangan bahan jika ada
            if (!empty($row['detail_product']) && $row['detail_product'] != $row['product_nama']) {
                $nama_lengkap .= ' (' . $row['detail_product'] . ')';
            }

            // Tambahkan keterangan Sisi (1 Sisi / 2 Sisi)
            if (!empty($row['ket_1']) && $row['ket_1'] != '0') {
                $nama_lengkap .= ' - ' . $row['ket_1'] . ' Sisi';
            }

            $data_produk[] = [
                'id_produk' => $row['id_produk'],
                'harga_id' => $row['harga_id'],
                'category_id' => (int) $row['kategori_id'],
                'nama_produk' => $nama_lengkap,
                'harga' => (float) $row['harga']
            ];
        }

        return $this->output->set_output(json_encode([
            'status' => 'success',
            'data' => $data_produk
        ]));
    }

    // --- ENDPOINT: Mengambil Kategori Produk Whitelist ---
    public function get_categories()
    {
        $this->db->select('kategori_id as id, kategori_nama as name');
        $this->db->from('tbl_kategori');
        $this->db->order_by('kategori_nama', 'ASC');
        $data = $this->db->get()->result_array();

        foreach ($data as &$cat) {
            $cat['icon'] = "📄";
            $cat['color'] = "bg-brand";
            $cat['count'] = $this->db->where('kategori_id', $cat['id'])->count_all_results('tbl_product');
        }

        return $this->output->set_output(json_encode([
            'status' => 'success',
            'data' => $data
        ]));
    }

    // --- ENDPOINT: Mengambil Cabang Perusahaan ---
    public function get_branches()
    {
        $this->db->select('id_perusahaan as id, nama_perusahaan as name, alamat_perusahaan as alamat, no_wa, status');
        $this->db->from('tbl_perusahaan');
        $this->db->where('status', '1');
        $data = $this->db->get()->result_array();

        return $this->output->set_output(json_encode([
            'status' => 'success',
            'data' => $data
        ]));
    }


    // --- ENDPOINT: Mengambil Pengaturan Portal (Dinamis) ---
    public function get_settings()
    {
        // 1. Cek & Buat tabel settings jika belum ada
        if (!$this->db->table_exists('tbl_portal_settings')) {
            $this->load->dbforge();
            $fields = [
                'key' => ['type' => 'VARCHAR', 'constraint' => 100],
                'value' => ['type' => 'TEXT', 'null' => TRUE],
            ];
            $this->dbforge->add_field($fields);
            $this->dbforge->add_key('key', TRUE);
            $this->dbforge->create_table('tbl_portal_settings');

            // Insert Initial Default Data
            $initial_defaults = [
                ['key' => 'profile_title', 'value' => 'Solusi Percetakan Modern & Berkualitas'],
                ['key' => 'profile_desc', 'value' => 'TGM Print hadir untuk memberikan kualitas cetak terbaik dengan akses yang mudah bagi semua orang.'],
                ['key' => 'operational_hours', 'value' => 'Senin - Sabtu: 08:00 - 20:00'],
                ['key' => 'contact_phone', 'value' => '+62 812-3456-7890'],
                [
                    'key' => 'carousel_images',
                    'value' => json_encode([
                        'https://images.unsplash.com/photo-1562654501-a0ccc0fc3fb1?q=80&w=2000',
                        'https://images.unsplash.com/photo-1626785774573-4b799315345d?q=80&w=2000'
                    ])
                ]
            ];
            $this->db->insert_batch('tbl_portal_settings', $initial_defaults);
        }

        $settings = $this->db->get('tbl_portal_settings')->result_array();

        $data = [];
        foreach ($settings as $row) {
            $data[$row['key']] = $row['value'];
        }

        // Fallback Defaults
        $fallback = [
            'profile_title' => 'Solusi Percetakan Modern & Berkualitas',
            'profile_desc' => 'TGM Print hadir untuk memberikan kualitas cetak terbaik dengan akses yang mudah bagi semua orang.',
            'operational_hours' => 'Senin - Sabtu: 08.00 - 20.00',
            'contact_phone' => '+62 812-3456-7890',
            'carousel_images' => json_encode([
                'https://images.unsplash.com/photo-1562654501-a0ccc0fc3fb1?q=80&w=2000',
                'https://images.unsplash.com/photo-1626785774573-4b799315345d?q=80&w=2000'
            ])
        ];

        $final_data = array_merge($fallback, $data);
        return $this->output->set_output(json_encode([
            'status' => 'success',
            'data' => $final_data
        ]));
    }

    // --- ENDPOINT: Simpan Pengaturan ---
    public function save_setting()
    {
        $this->verify_admin_key();
        $key = $this->input->post('key', true);
        $value = $this->input->post('value', true);

        if (!$key) {
            echo json_encode(['status' => 'error', 'message' => 'Key is required']);
            return;
        }

        $exists = $this->db->get_where('tbl_portal_settings', ['key' => $key])->row();
        if ($exists) {
            $this->db->where('key', $key);
            $this->db->update('tbl_portal_settings', ['value' => $value]);
        } else {
            $this->db->insert('tbl_portal_settings', ['key' => $key, 'value' => $value]);
        }

        echo json_encode(['status' => 'success', 'message' => 'Setting saved']);
    }

    // --- ENDPOINT: Tracking Pesanan SPK ---
    public function track_order($nospk = null)
    {
        if (!$nospk) {
            $nospk = $this->input->get('spk', true);
        }

        if (empty($nospk)) {
            echo json_encode(['status' => 'error', 'message' => 'Nomor SPK wajib diisi.']);
            return;
        }

        $nospk = strtoupper(trim($nospk));

        // 1. Ambil data pesanan (Summary) menggunakan method ERP
        $this->load->model('M_transaksi');
        $order = $this->M_transaksi->getcekpesanan($nospk)->row_array();

        if (!$order) {
            echo json_encode(['status' => 'error', 'message' => 'Nomor SPK tidak ditemukan di sistem kami.']);
            return;
        }

        // 2. Ambil detail produk
        $items_raw = $this->M_transaksi->getDetailPesan($nospk)->result_array();
        $items = [];
        $total_tagihan = 0;
        foreach ($items_raw as $it) {
            $sub = $it['snapshot_subtotal'] > 0 ? $it['snapshot_subtotal'] : 0;
            $items[] = [
                'nama_produk' => $it['product_nama'] . ($it['detail_product'] ? ' (' . $it['detail_product'] . ')' : ''),
                'qty' => (float) $it['qty'],
                'subtotal' => (float) $sub
            ];
            $total_tagihan += $sub;
        }

        // 3. Ambil Status Produksi (Flow)
        $produksi = $this->M_transaksi->getcekflow($nospk)->row_array();

        $status_label = "Diterima";
        $progress = 15;
        $history = [];

        // Masukkan history awal (SPK Dibuat)
        $history[] = [
            'time' => date('d M Y, H:i', strtotime($order['created_at'])),
            'note' => 'Pesanan diterima oleh Deskprint: ' . ($order['username'] ?? 'Sistem'),
            'status' => 'Diterima'
        ];

        if ($produksi) {
            $st_prod = $produksi['status_produksi'];
            if (in_array($st_prod, ['proses', '1'])) {
                $status_label = "Sedang Diproses";
                $progress = 65;
                $history[] = [
                    'time' => date('d M Y, H:i', strtotime($produksi['tgl_mulai'] ?? $order['created_at'])),
                    'note' => 'Pesanan sedang diproses produksi',
                    'status' => 'Produksi'
                ];
            } elseif (in_array($st_prod, ['done', 'on_pb', '2'])) {
                $status_label = ($st_prod === 'on_pb') ? "Siap Diambil (Di PB)" : "Selesai Cetak";
                $progress = 100;
                $history[] = [
                    'time' => date('d M Y, H:i', strtotime($produksi['tgl_selesai'] ?? $produksi['tgl_kirim_pb'] ?? $order['created_at'])),
                    'note' => ($st_prod === 'on_pb') ? 'Pesanan sudah di meja PB dan siap diambil' : 'Produksi selesai dan siap diambil',
                    'status' => 'Selesai'
                ];
            } elseif ($st_prod === 'on_costumer') {
                $status_label = "Sudah Diambil";
                $progress = 100;
                $history[] = [
                    'time' => date('d M Y, H:i', strtotime($produksi['update_time'] ?? $order['created_at'])),
                    'note' => 'Pesanan telah berhasil diserahkan kepada konsumen',
                    'status' => 'Sudah Diambil'
                ];
            }
        }

        // 4. Hitung Pembayaran
        $total_bayar = (float) ($order['total_bayar'] ?? 0);
        $sisa = $total_tagihan - $total_bayar;
        $status_bayar = "Belum Bayar";
        $is_piutang = $this->db->get_where('tbl_piutang', ['nospk' => $nospk])->num_rows() > 0;

        if ($total_bayar >= $total_tagihan && $total_tagihan > 0) {
            $status_bayar = "Lunas";
        } elseif ($is_piutang) {
            $status_bayar = ($total_bayar > 0) ? "DP (Tempo)" : "Tempo / Piutang";
        } elseif ($total_bayar > 0) {
            $status_bayar = "DP / Cicil";
        }

        $result = [
            'no_spk' => $order['nospk'],
            'customer' => $order['konsumen_nama'],
            'status' => $status_label,
            'progress' => $progress,
            'last_update' => date('d M Y, H:i', strtotime($order['created_at'])),
            'order_info' => [
                'tgl_spk' => date('d/m/Y', strtotime($order['created_at'])),
                'total_tagihan' => (float) $total_tagihan,
                'total_bayar' => (float) $total_bayar,
                'sisa_bayar' => $sisa > 0 ? $sisa : 0,
                'status_bayar' => $status_bayar
            ],
            'items' => $items,
            'history' => array_reverse($history)
        ];

        echo json_encode(['status' => 'success', 'data' => $result]);
    }

    public function submit_pesanan()
    {
        $u = $this->verify_token();
        $input = json_decode(file_get_contents('php://input'), true);

        if (!$input) {
            echo json_encode(['status' => 'error', 'message' => 'Data pesanan kosong.']);
            return;
        }

        $order = $input['order'] ?? null;

        if (!$order) {
            echo json_encode(['status' => 'error', 'message' => 'Data pesanan tidak lengkap.']);
            return;
        }

        // 1. Hubungkan / Cari Konsumen di Tabel ERP (tbl_konsumen) berdasarkan Token (Aman dari Bypass)
        $wa = $u->no_hp ?? $u->konsumen_nohp ?? '';
        $nama = $u->nama_lengkap ?? $u->konsumen_nama ?? '';

        if(empty($wa) || empty($nama)) {
            echo json_encode(['status' => 'error', 'message' => 'Sesi login tidak valid.']);
            return;
        }

        $konsumen = $this->db->get_where('tbl_konsumen', ['konsumen_nohp' => $wa])->row();

        if ($konsumen) {
            $konsumen_id = $konsumen->konsumen_id;
        } else {
            // Jika belum pernah ada, buat data master konsumen baru di sistem ERP
            $this->db->insert('tbl_konsumen', [
                'konsumen_nama' => $nama,
                'konsumen_nohp' => $wa,
                'konsumen_alamat' => '-',
                'status' => '1'
            ]);
            $konsumen_id = $this->db->insert_id();
        }

        // 2. Format Items untuk disave ke module M_kiosk (tbl_pemesanan_mandiri)
        $insert_items = [];
        $raw_items = isset($input['order'][0]) ? $input['order'] : [$input['order']];

        foreach ($raw_items as $item) {
            $catatan_gabung = $item['catatan'] ?? '';
            if (!empty($item['file_url'])) {
                $catatan_gabung .= " | URL Berkas: " . $item['file_url'];
            }

            $insert_items[] = [
                'konsumen_id' => $konsumen_id,
                'perusahaan_id' => 1,
                'product_id' => $item['id_produk'],
                'harga_id' => $item['harga_id'],
                'qty' => $item['qty'],
                'panjang' => 0,
                'lebar' => 0,
                'total_harga' => $item['total_harga'],
                'catatan' => ltrim($catatan_gabung, " | "),
                'status' => 'submitted'
            ];
        }

        // 3. Gunakan Fungsi Kiosk ERP untuk generate Nomor Referensi dan Menyimpan Item
        $this->load->model('M_kiosk');
        $ref = $this->M_kiosk->save_batch_order($insert_items);

        if ($ref) {
            echo json_encode(['status' => 'success', 'message' => 'Pesanan berhasil dikirim ke antrian TGM!', 'no_ref' => $ref]);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Gagal menyimpan pesanan.']);
        }
    }

    // --- ENDPOINT: Mengambil Berita & Promo ---
    public function get_news()
    {
        // 1. Cek & Buat tabel jika belum ada
        if (!$this->db->table_exists('tbl_portal_news')) {
            $this->load->dbforge();
            $fields = [
                'id' => ['type' => 'INT', 'constraint' => 11, 'auto_increment' => TRUE],
                'title' => ['type' => 'VARCHAR', 'constraint' => 255],
                'content' => ['type' => 'TEXT', 'null' => TRUE],
                'type' => ['type' => 'VARCHAR', 'constraint' => 50, 'default' => 'news'],
                'image_url' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => TRUE],
                'created_at' => ['type' => 'DATETIME']
            ];
            $this->dbforge->add_field($fields);
            $this->dbforge->add_key('id', TRUE);
            $this->dbforge->create_table('tbl_portal_news');
        }

        $this->db->order_by('id', 'DESC');
        $this->db->limit(10);
        $query = $this->db->get('tbl_portal_news');
        $data = $query ? $query->result_array() : [];

        return $this->output->set_output(json_encode([
            'status' => 'success',
            'data' => $data
        ]));
    }

    // --- ENDPOINT: Simpan Berita Baru ---
    public function save_news()
    {
        $this->verify_admin_key();
        $input = json_decode(file_get_contents('php://input'), true);
        if (!$input || empty($input['title'])) {
            echo json_encode(['status' => 'error', 'message' => 'Data tidak lengkap']);
            return;
        }

        $data = [
            'title' => $input['title'],
            'content' => $input['content'] ?? '',
            'type' => $input['type'] ?? 'news', // news, promo, event
            'created_at' => date('Y-m-d H:i:s')
        ];

        $this->db->insert('tbl_portal_news', $data);
        echo json_encode(['status' => 'success', 'message' => 'Konten berhasil diterbitkan!']);
    }

    // --- ENDPOINT: Unggah Gambar Portal ---
    public function upload_portal_image()
    {
        $this->verify_admin_key();
        $config['upload_path'] = './uploads/portal/';
        $config['allowed_types'] = 'gif|jpg|png|jpeg|webp';
        $config['max_size'] = 5000; // 5MB
        $config['encrypt_name'] = TRUE;

        if (!is_dir($config['upload_path'])) {
            mkdir($config['upload_path'], 0777, true);
        }

        $this->load->library('upload', $config);

        if (!$this->upload->do_upload('image')) {
            echo json_encode(['status' => 'error', 'message' => $this->upload->display_errors('', '')]);
        } else {
            $data = $this->upload->data();
            $url = base_url('uploads/portal/' . $data['file_name']);
            echo json_encode(['status' => 'success', 'url' => $url]);
        }
    }
}
