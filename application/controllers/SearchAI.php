<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Controller SearchAI
 * Berfungsi sebagai Search Engine berbasis AI (Text-to-SQL) untuk Dashboard Owner.
 */
class SearchAI extends MY_Controller
{
    private $api_key;

    public function __construct()
    {
        parent::__construct();
        $this->load->model('Search_model');

        // Memuat Konfigurasi AI & Sanitasi Input
        $this->config->load('ai', TRUE);
        $this->api_key = trim($this->config->item('gemini_api_key', 'ai'));
    }

    public function index()
    {
        $data['title'] = 'AI Global Search';
        $data['breadcrumb'] = 'Global Search';
        $this->load_admin_view('admin/search_ai/index', $data);
    }

    public function process_query()
    {
        $user_query = $this->input->post('query', TRUE);
        if (empty($user_query)) {
            echo json_encode(['status' => 'error', 'message' => 'Query tidak boleh kosong.']);
            return;
        }

        $level = $this->session->userdata('level');
        $karyawan_id = $this->session->userdata('karyawan_id');
        $is_logged_in = $this->session->userdata('logged_in');

        // Dynamic Schema Context & System Instruction based on Role
        if ($level == 1) {
            // 1. Owner (Full Access)
            $schema_context = "
                MODUL 1: SDM & ABSENSI
                - tbl_karyawan: [karyawan_id, nama_lengkap, nik, divisi_id, jabatan_id]
                - absensi: [karyawan_id, tanggal, status ('Hadir', 'Alfa', 'Izin', 'Sakit', 'Late', 'Cuti')]

                MODUL 2: PRODUK & HARGA
                - tbl_product: [product_id, product_nama, product_deskripsi]
                - tbl_harga_product: [harga_id, product_id, harga_1 (harga jual), hpp (modal), is_active]

                MODUL 3: PENJUALAN & KONSUMEN
                - tbl_konsumen: [konsumen_id, konsumen_nama, konsumen_nohp]
                - tbl_pemesanan: [nospk, konsumen_id, status_pembayaran ('pending','paid','canceled'), created_at]
                - tbl_pemesanan_detail: [nospk, product_id, qty (jumlah produk_terjual), harga_id, panjang, lebar]
                - tbl_pembayaran: [nospk, tgl_bayar, total_tagihan, total_bayar, status_lunas ('0','1')]

                MODUL 4: OPERASIONAL & STOK
                - tbl_material: [material_id, varian (nama bahan), harga_jual, stok_min]
                - tbl_stok_gudang: [material_id, qty (stok saat ini), gudang_id]
                - tbl_spk: [nospk, spk_time, konsumen_id]

                MODUL 5: PENGELUARAN (BIAYA)
                - tbl_pengeluaran: [pengeluaran_tgl, pengeluaran_detail, pengeluaran_jumlah, pengeluaran_penerima]
            ";

            $system_instruction = "
                Anda adalah AI ERP TGM. Ubah pertanyaan menjadi SQL MariaDB (SELECT ONLY).
                RULES:
                - Output HANYA SQL mentah (TANPA markdown).
                - Untuk stok habis: tbl_stok_gudang.qty <= tbl_material.stok_min.
                - Untuk omset: SUM(total_tagihan) di tbl_pembayaran.
                - Untuk pengeluaran: SUM(pengeluaran_jumlah) di tbl_pengeluaran.
                - Selalu gunakan JOIN yang tepat antar tabel.
            ";
        } elseif ($is_logged_in && $level > 1) {
            // 2. Karyawan (Limited Access)
            $schema_context = "
                MODUL 1: ABSENSI (ID Karyawan Saya: " . ($karyawan_id ?? 0) . ")
                - absensi: [karyawan_id, tanggal, status ('Hadir', 'Alfa', 'Izin', 'Sakit', 'Late', 'Cuti')]

                MODUL 2: PRODUK & HARGA
                - tbl_product: [product_id, product_nama, product_deskripsi]
                - tbl_harga_product: [harga_id, product_id, harga_1 (harga jual), is_active]

                MODUL 3: OPERASIONAL & STOK
                - tbl_material: [material_id, varian (nama bahan)]
                - tbl_stok_gudang: [material_id, qty (stok saat ini), gudang_id]
                - tbl_spk: [nospk, spk_time]
                - tbl_pemesanan: [nospk, status_pembayaran, created_at]
            ";

            $system_instruction = "
                Anda adalah AI ERP TGM. Ubah pertanyaan menjadi SQL MariaDB (SELECT ONLY).
                RULES:
                - Output HANYA SQL mentah (TANPA markdown).
                - DILARANG query data omzet, profit, modal (hpp), gaji, atau pengeluaran.
                - Jika bertanya tentang absensi, pastikan SELALU filter WHERE karyawan_id = " . ($karyawan_id ?? 0) . ".
                - Selalu gunakan JOIN yang tepat antar tabel.
            ";
        } else {
            // 3. Konsumen / Public (Very Limited Access)
            $schema_context = "
                MODUL 1: PRODUK & HARGA JUAL
                - tbl_product: [product_id, product_nama, product_deskripsi]
                - tbl_harga_product: [harga_id, product_id, harga_1 (harga jual satuan), is_active]

                MODUL 2: STATUS PESANAN (KONSUMEN)
                - tbl_pemesanan: [nospk, status_pembayaran ('pending','paid','canceled'), created_at]
            ";

            $system_instruction = "
                Anda adalah asisten virtual TGM Print. Ubah pertanyaan menjadi SQL MariaDB (SELECT ONLY).
                RULES:
                - Output HANYA SQL mentah (TANPA markdown).
                - DILARANG query data modal (hpp), stok gudang, omzet, pengeluaran, absensi, atau informasi internal.
                - Pastikan hanya bisa menampilkan nama produk, harga jual, atau status pesanan.
                - Jika konsumen mengecek pesanan (nospk), filter dari tbl_pemesanan.
                - Selalu gunakan JOIN yang tepat antar tabel.
            ";
        }

        $schema_context .= "\n\nWAKTU SEKARANG: Hari ini " . date('Y-m-d') . ", Bulan " . date('m') . ", Tahun " . date('Y');

        $ai_response = $this->_call_ai_api($user_query, $schema_context, $system_instruction);

        if ($ai_response['status'] === 'error') {
            echo json_encode(['status' => 'error', 'message' => $ai_response['message']]);
            return;
        }

        $sql = $ai_response['sql'];

        try {
            $results = $this->Search_model->execute_ai_sql($sql);
            if ($this->session->userdata('level') == 1) {
                $results['debug_sql'] = $sql;
                $results['raw_ai_response'] = $ai_response['raw_data'] ?? null;
            }
            echo json_encode($results);
        } catch (Throwable $e) {
            echo json_encode(['status' => 'error', 'message' => 'SQL Error: ' . $e->getMessage()]);
        }
    }

    private function _call_ai_api($query, $schema, $instruction)
    {
        $model_name = trim($this->config->item('gemini_model', 'ai') ?: 'gemini-2.5-flash');
        $url = "https://generativelanguage.googleapis.com/v1beta/models/{$model_name}:generateContent?key=" . $this->api_key;
        $prompt = "CONTEXT:\n$schema\n\nINSTRUCTION:\n$instruction\n\nUSER QUESTION: $query";
        $payload = ['contents' => [['parts' => [['text' => $prompt]]]], 'generationConfig' => ['temperature' => 0.1, 'topK' => 1]];

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json', 'Connection: Keep-Alive']);
        curl_setopt($ch, CURLOPT_ENCODING, "");
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        $res_data = json_decode($response, true);
        if ($http_code !== 200) {
            $err_msg = $res_data['error']['message'] ?? 'Unknown API Error';

            // Handle Rate Limit (429) gracefully
            if ($http_code === 429) {
                return ['status' => 'error', 'message' => 'Sistem AI sedang sibuk memproses banyak permintaan. Mohon tunggu sekitar 30 detik sebelum mencoba lagi.'];
            }

            return ['status' => 'error', 'message' => "Gemini ($http_code): $err_msg"];
        }

        if (isset($res_data['candidates'][0]['content']['parts'][0]['text'])) {
            $sql = $res_data['candidates'][0]['content']['parts'][0]['text'];
            return ['status' => 'success', 'sql' => trim(str_ireplace(['```sql', '```', 'sql'], '', $sql)), 'raw_data' => $res_data];
        }
        return ['status' => 'error', 'message' => 'AI Respon Kosong'];
    }
}
