<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Whatsapp_bot extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->model('Whatsapp_bot_model');
        date_default_timezone_set("Asia/Jakarta");
    }

    /**
     * Webhook Endpoint (Menerima pesan dari Fonnte / API WA)
     * URL Endpoint: https://erp.tgmprint.com/whatsapp_bot/webhook
     */
    public function webhook()
    {
        // Mendapatkan data POST (mendukung raw JSON maupun form-data)
        $inputJSON = file_get_contents('php://input');
        $input = json_decode($inputJSON, TRUE);
        
        // Coba ambil dari form POST jika bukan raw JSON
        $sender  = isset($input['sender']) ? $input['sender'] : $this->input->post('sender', true);
        $message = isset($input['message']) ? $input['message'] : $this->input->post('message', true);
        $name    = isset($input['name']) ? $input['name'] : $this->input->post('name', true);
        $device  = isset($input['device']) ? $input['device'] : $this->input->post('device', true);

        if (empty($sender) || empty($message)) {
            echo json_encode(["status" => false, "reason" => "Invalid Payload"]);
            return;
        }

        // ANTI-LOOP 1: Abaikan pesan jika pengirim adalah bot itu sendiri
        if (!empty($device) && $sender == $device) {
            echo json_encode(["status" => false, "reason" => "Ignored outgoing message"]);
            return;
        }

        // ANTI-LOOP 2: Cegah Retry Fonnte (Abaikan pesan yang persis sama dalam 10 detik terakhir)
        $this->db->where('sender_number', $sender);
        $this->db->where('message_in', $message);
        $this->db->where('created_at >=', date('Y-m-d H:i:s', strtotime('-10 seconds')));
        $cek_retry = $this->db->get('whatsapp_logs')->row();

        if ($cek_retry) {
            echo json_encode(["status" => false, "reason" => "Duplicate webhook ignored to prevent loop"]);
            return;
        }

        // Parsing Command (Hilangkan spasi ganda, ubah ke uppercase)
        $clean_message = trim(preg_replace('/\s+/', ' ', strtoupper($message)));
        $parts = explode(' ', $clean_message);
        $command = $parts[0];
        $param = isset($parts[1]) ? $parts[1] : '';

        $reply = "";

        switch ($command) {
            case 'CEK':
            case 'STATUS':
                if (empty($param)) {
                    $reply = "Format salah. Silakan ketik: CEK [Nomor SPK]\nContoh: CEK SPK12345";
                } else {
                    $trx = $this->Whatsapp_bot_model->cek_transaksi($param);
                    if ($trx) {
                        $reply = "*STATUS PESANAN - TGM PRINT*\n\n";
                        $reply .= "Nama: *" . $trx->nama_konsumen . "*\n";
                        $reply .= "No SPK: *" . $trx->nospk . "*\n";
                        $reply .= "Status Bayar: *" . strtoupper($trx->status_bayar_trx) . "*\n";
                        
                        $status_prod = $trx->status_produksi ? strtoupper($trx->status_produksi) : "BELUM DIPROSES";
                        $reply .= "Status Produksi: *" . $status_prod . "*\n";
                        
                        $estimasi = ($trx->dateline_tgl) ? date('d M Y', strtotime($trx->dateline_tgl)) . " " . $trx->dateline_jam : "Belum ditentukan";
                        $reply .= "Estimasi Selesai: *" . $estimasi . "*\n";
                        
                        if ($trx->catatan) {
                            $reply .= "Catatan: " . $trx->catatan . "\n";
                        }
                        
                        $reply .= "\n_Terima kasih telah mempercayakan pesanan Anda kepada kami!_";
                    } else {
                        $reply = "Maaf, pesanan dengan Nomor SPK *$param* tidak ditemukan di sistem kami. Pastikan nomor sudah benar.";
                    }
                }
                break;

            case 'INVOICE':
                if (empty($param)) {
                    $reply = "Format salah. Silakan ketik: INVOICE [Nomor SPK]\nContoh: INVOICE SPK12345";
                } else {
                    $trx = $this->Whatsapp_bot_model->cek_transaksi($param);
                    if ($trx) {
                        $link = base_url("cekpesanan/cetak_invoice/" . urlencode(encrypt_spk($trx->nospk)));
                        $reply = "Halo *" . $trx->nama_konsumen . "*,\n\n";
                        $reply .= "Berikut adalah tautan (link) untuk melihat dan mengunduh Invoice pesanan Anda:\n";
                        $reply .= $link . "\n\n";
                        $reply .= "Total Tagihan: *Rp " . number_format($trx->total_tagihan,0,',','.') . "*\n";
                        $reply .= "Status: *" . strtoupper($trx->status_bayar_trx) . "*\n";
                    } else {
                        $reply = "Maaf, pesanan dengan Nomor SPK *$param* tidak ditemukan.";
                    }
                }
                break;

            case 'ADMIN':
                $reply = "*LAYANAN ADMIN TGM PRINT*\n\n";
                $reply .= "Jika Anda butuh bantuan lebih lanjut, silakan hubungi admin kami:\n";
                $reply .= "CS 1: 08xxxxxxxxxx\n";
                $reply .= "CS 2: 08xxxxxxxxxx\n\n";
                $reply .= "Jam Operasional: Senin - Sabtu (08.00 - 17.00)";
                break;

            case 'MENU':
                $reply = "Halo! Terima kasih telah menghubungi *TGM Print*.\n\n";
                $reply .= "Ada yang bisa saya bantu? Silakan balas dengan mengetik salah satu perintah di bawah ini:\n\n";
                $reply .= "*CEK [No SPK]*\n";
                $reply .= "Untuk mengecek status pesanan & produksi.\n_Contoh: CEK SPK123_\n\n";
                $reply .= "*INVOICE [No SPK]*\n";
                $reply .= "Untuk melihat dan mendownload tagihan pesanan Anda.\n\n";
                $reply .= "*ADMIN*\n";
                $reply .= "Untuk disambungkan dengan Customer Service kami.\n\n";
                $reply .= "Ketik perintah Anda sekarang, bot akan membalas otomatis.";
                break;

            default:
                $reply = "Maaf, sistem tidak mengerti perintah Anda. Silakan ketik *MENU* untuk melihat opsi layanan.";
        }

        // --- KIRIM BALASAN VIA CURL MANUAL (JSON PAYLOAD) ---
        // Metode ini terbukti berhasil pada pukul 22.47 untuk perintah 'test'
        $setting = $this->db->get_where('tbl_wa_setting', ['id' => 1])->row();
        $token = $setting && !empty($setting->wa_token) ? $setting->wa_token : '';

        if (!empty($token) && !empty($reply)) {
            $postData = array(
                'target' => $sender,
                'message' => $reply
            );

            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_URL => 'https://api.fonnte.com/send',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 5, // Cepat agar webhook Fonnte tidak timeout
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                // Mengirim sebagai JSON untuk mencegah string rusak
                CURLOPT_POSTFIELDS => json_encode($postData),
                CURLOPT_HTTPHEADER => array(
                    'Authorization: ' . $token,
                    'Content-Type: application/json'
                ),
            ));

            $response = curl_exec($curl);
            curl_close($curl);
        }

        // Simpan log ke database
        $this->Whatsapp_bot_model->save_log($sender, $name, $message, $reply, 'success');

        // Balas JSON valid ke Fonnte agar Fonnte tahu webhook sukses & tidak retry
        header('Content-Type: application/json');
        echo json_encode(["status" => true, "reason" => "Message handled perfectly via cURL"]);
    }

    /**
     * Contoh metode kirim pesan proaktif (Manual via cURL)
     */
    public function send_message()
    {
        // Hanya untuk testing manual API Send
        $target = $this->input->post('target', true);
        $message = $this->input->post('message', true);

        if(empty($target) || empty($message)) {
            echo json_encode(['status' => false, 'reason' => 'Target and message required']);
            return;
        }

        // Ambil token dari database setting TGM ERP
        $setting = $this->db->get_where('tbl_wa_setting', ['id' => 1])->row();
        $token = $setting && !empty($setting->wa_token) ? $setting->wa_token : 'TOKEN_ANDA';

        $curl = curl_init();
        curl_setopt_array($curl, array(
          CURLOPT_URL => 'https://api.fonnte.com/send',
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => '',
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 0,
          CURLOPT_FOLLOWLOCATION => true,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => 'POST',
          CURLOPT_POSTFIELDS => array(
            'target' => $target,
            'message' => $message,
          ),
          CURLOPT_HTTPHEADER => array(
            'Authorization: ' . $token
          ),
        ));

        $response = curl_exec($curl);
        curl_close($curl);
        echo $response;
    }
}
?>
