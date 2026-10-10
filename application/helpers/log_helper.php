<?php
defined('BASEPATH') or exit('No direct script access allowed');
/** 
 * ########################################################################################
 * Herlper save log to Database
 * ########################################################################################
 * @package   creator
 * @author    DioClaude <diobagussaputra0852@gmail.com>
 * @copyright Copyright (c) 2019 - 2020
 * @since     1.0
 *
 * #######################################################################################
 */

    /**
     * Helper untuk menyimpan login / Logout ke database
     * @return void
     * @author DioClaude <diobagussaputra0852@gmail.com>
     */
    function helper_login($params)
    {
        $CI =& get_instance();
        /** @var MY_Controller $CI */

        $CI->load->model('Log_m');
        $CI->Log_m->saveHistoryUser($params);
    }
    /**
     * Helper untuk menyimpan log aktivitas ke database
     * @return void
     * @author DioClaude <diobagussaputra0852@gmail.com>
     */
    function helper_log($tipe = '', $desc = '', $ket = '')
    {
        date_default_timezone_set("Asia/jakarta");
        $CI =& get_instance();
        /** @var MY_Controller $CI */

        (strtolower($tipe) == 'add')    ? $log_tipe = 1 : NULL;  // add data 
        (strtolower($tipe) == 'edit')   ? $log_tipe = 2 : NULL;  // edit Data
        (strtolower($tipe) == 'delete') ? $log_tipe = 3 : NULL;  // delete Data
        (strtolower($tipe) == 'import') ? $log_tipe = 4 : NULL;  // import Data
        (strtolower($tipe) == 'export') ? $log_tipe = 5 : NULL;  // export Data
        (strtolower($tipe) == 'upload') ? $log_tipe = 6 : NULL;  // upload Data
        (strtolower($tipe) == 'bayar')  ? $log_tipe = 7 : NULL;  // bayar Data
        (strtolower($tipe) == 'backup') ? $log_tipe = 8 : NULL;  // backup Database
        (strtolower($tipe) == 'cetak')  ? $log_tipe = 9 : NULL;  // cetak Bukti
        (strtolower($tipe) == 'ppdb')   ? $log_tipe = 10 : NULL;  // cetak Bukti

        $data = array(
            'time' => date('Y:m:d H:i:s'),
            'user' => $CI->session->userdata('username'),
            'tipe' => $log_tipe,
            'desc' => $desc,
            'ket'  => $ket
        );

        $CI->load->model('Log_m');
        $CI->Log_m->saveLog($data);
    }



    /**
     * Helper untuk mendapatkan ip address Server
     * no params required
     * @return string 
     * @author DioClaude <diobagussaputra0852@gmail.com>
     */
    function get_client_ip_server()
    {
        return    (!empty($_SERVER['HTTP_CLIENT_IP'])) ? $_SERVER['HTTP_CLIENT_IP'] 
                : ((!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) ? $_SERVER['HTTP_X_FORWARDED_FOR']
                : ((!empty($_SERVER['HTTP_X_FORWARDED'])) ? $_SERVER['HTTP_X_FORWARDED']
                : ((!empty($_SERVER['HTTP_FORWARDED_FOR'])) ? $_SERVER['HTTP_FORWARDED_FOR']
                : ((!empty($_SERVER['HTTP_FORWARDED'])) ? $_SERVER['HTTP_FORWARDED']
                : ((!empty($_SERVER['REMOTE_ADDR'])) ? $_SERVER['REMOTE_ADDR']
                : 'UNKNOWN' )))));
    }

    /**
     * Helper untuk mendapatkan ip address Client
     * no params required
     * @return string
     * @author DioClaude <diobagussaputra0852@gmail.com>
     */
    function get_client_ip_env()
    {
        return    (getenv('HTTP_CLIENT_IP')) ? getenv('HTTP_CLIENT_IP') 
                : ((getenv('HTTP_X_FORWARDED_FOR')) ? getenv('HTTP_X_FORWARDED_FOR')
                : ((getenv('HTTP_X_FORWARDED')) ? getenv('HTTP_X_FORWARDED')
                : ((getenv('HTTP_FORWARDED_FOR')) ? getenv('HTTP_FORWARDED_FOR')
                : ((getenv('HTTP_FORWARDED')) ? getenv('HTTP_FORWARDED')
                : ((getenv('REMOTE_ADDR')) ? getenv('REMOTE_ADDR')
                : 'UNKNOWN' )))));
    }

    /**
     * Helper untuk Versi browser
     * no params required
     * @return string
     * @author DioClaude <diobagussaputra0852@gmail.com>
     */
    function agent() {
        $CI =& get_instance();
        /** @var MY_Controller $CI */

        return    ($CI->agent->is_browser()) ? $CI->agent->browser() . ' ' . $CI->agent->version()
                : (($CI->agent->is_robot()) ? $CI->agent->robot()
                : (($CI->agent->is_mobile()) ? $CI->agent->mobile() 
                : 'Unidentified User Agent' ));
    }

    function log_activity($user_id, $action, $module, $reference_id = null)
    {
        $CI =& get_instance();
        /** @var MY_Controller $CI */
        $CI->load->database();
        $CI->db->insert('user_activity_log', [
            'user_id' => $user_id,
            'action' => $action,
            'module' => $module,
            'reference_id' => $reference_id,
            'timestamp' => date('Y-m-d H:i:s')
        ]);
    }
