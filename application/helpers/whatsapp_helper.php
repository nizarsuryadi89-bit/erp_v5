<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * WhatsApp Helper using Fonnte API
 */

if (!function_exists('send_wa')) {
    /**
     * Send WhatsApp Message via Fonnte
     * 
     * @param string $target WhatsApp number (e.g., 08123456789 or 628123456789)
     * @param string $message Message content
     * @param string $token Optional Fonnte token (if null, fetches from DB)
     * @return array Response from Fonnte API
     */
    function send_wa($target, $message, $token = null)
    {
        $CI =& get_instance();
        
        // Cek settingan WA di DB
        $setting = $CI->db->get_where('tbl_wa_setting', ['id' => 1])->row();
        
        // Jika gateway dimatikan, jangan kirim
        if ($setting && $setting->status == 0) {
            return ['status' => false, 'reason' => 'WA Gateway is disabled in settings.'];
        }

        // Ambil token
        if ($token === null) {
            $token = $setting && !empty($setting->wa_token) ? $setting->wa_token : '';
        }

        if (empty($token)) {
            return ['status' => false, 'reason' => 'Token WA belum diatur.'];
        }

        // Fonnte request
        $postData = array(
            'target' => $target,
            'message' => $message
        );

        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://api.fonnte.com/send',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 5,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => json_encode($postData),
            CURLOPT_HTTPHEADER => array(
                'Authorization: ' . $token,
                'Content-Type: application/json'
            ),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);

        $result = [];
        $status_log = 'failed';
        if ($err) {
            $result = ['status' => false, 'reason' => "cURL Error: " . $err];
        } else {
            $res_decoded = json_decode($response, true);
            if (isset($res_decoded['status']) && $res_decoded['status'] == true) {
                $result = ['status' => true, 'response' => $res_decoded];
                $status_log = 'success';
            } else {
                $result = ['status' => false, 'reason' => $res_decoded['reason'] ?? 'Unknown Fonnte Error', 'response' => $res_decoded];
            }
        }

        // Simpan log ke tabel tbl_wa_logs
        $log_data = [
            'target' => $target,
            'message' => $message,
            'status' => $status_log,
            'response' => $err ? $err : $response,
            'created_at' => date('Y-m-d H:i:s')
        ];
        $CI->db->insert('tbl_wa_logs', $log_data);

        return $result;
    }
}

if (!function_exists('send_wa_notif')) {
    /**
     * Send automatic WhatsApp notification based on system settings
     * 
     * @param string $target
     * @param string $message
     * @param string $trigger_type ('notif_spk_baru', 'notif_pembayaran', 'notif_pesanan_selesai', 'notif_pengambilan')
     * @return bool
     */
    function send_wa_notif($target, $message, $trigger_type = null)
    {
        $CI =& get_instance();
        
        // Cek settingan WA di DB
        $setting = $CI->db->get_where('tbl_wa_setting', ['id' => 1])->row();
        
        if (!$setting || $setting->status == 0) {
            return false;
        }

        // Jika ada trigger_type yang dilempar, cek apakah fiturnya diaktifkan (1)
        if ($trigger_type && isset($setting->$trigger_type) && $setting->$trigger_type == 0) {
            return false; // Fitur otomatisasi ini dinonaktifkan
        }

        // Gunakan fungsi send_wa utama
        $result = send_wa($target, $message);
        
        return $result['status'] ?? false;
    }
}
