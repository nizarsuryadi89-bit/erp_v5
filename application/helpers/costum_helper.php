<?php
defined('BASEPATH') or exit('No direct script access allowed');

/** 
 * ########################################################################################
 * Herlper creator
 * ########################################################################################
 * @package   creator
 * @author    DioClaude <diobagussaputra0852@gmail.com>
 * @copyright Copyright (c) 2019 - 2020
 * @since     1.0
 *
 * #######################################################################################
 */
function spk()
{

}
/**
 * Helper untuk membuat timezone
 * @return string 
 * @author DioClaude <diobagussaputra0852@gmail.com>
 */
function set_zone()
{
    return date_default_timezone_set("Asia/Jakarta");
}

/**
 * Helper untuk membuat waktu
 * @return string 
 * @author DioClaude <diobagussaputra0852@gmail.com>
 */
function now()
{
    set_zone();
    return date('Y:m:d H:i:s');
}

/**
 * Helper untuk mengecek status login
 * @return void 
 * @author DioClaude <diobagussaputra0852@gmail.com>
 */
function is_login()
{

    $CI =& get_instance();
    /** @var MY_Controller $CI */

    if (!$CI->session->userdata('role_id')) {
        redirect('auth/login_required');
    }
    return;
}

/**
 * Helper memberikan akses pada admin
 * @return string 
 * @author DioClaude <diobagussaputra0852@gmail.com>
 */

/**
 * Helper memberikan Akses pada calon peserta didik
 * @return void 
 * @author DioClaude <diobagussaputra0852@gmail.com>
 */
function is_deskprint()
{

    $CI =& get_instance();
    /** @var MY_Controller $CI */

    if (!$CI->session->userdata('role_id')) {
        redirect('auth/login_required');
    } elseif ($CI->session->userdata('role_id') != 1) {
        redirect('auth/access_denied');
    }
    return;
}

/**
 * Helper memberikan akses ke Admin, Oprator
 * @return void 
 * @author DioClaude <diobagussaputra0852@gmail.com>
 */
function is_kasir()
{

    $CI =& get_instance();
    /** @var MY_Controller $CI */

    if (!$CI->session->userdata('role_id')) {
        redirect('auth/login_required');
    } elseif ($CI->session->userdata('role_id') != 2) {
        redirect('auth/login_required');
    }
    return;
}

/**
 * Helper memberikan akses ke Admin, Oprator, Guru
 * @return void 
 * @author DioClaude <diobagussaputra0852@gmail.com>
 */
function is_master()
{

    $CI =& get_instance();
    /** @var MY_Controller $CI */

    if (!$CI->session->userdata('role_id')) {
        redirect('auth/login_required');
    } elseif ($CI->session->userdata('role_id') != 5) {
        redirect('auth/login_required');
    }
    return;
}

/**
 * Helper memberikan akses ke Gudang
 * @return void 
 */
function is_gudang()
{

    $CI =& get_instance();
    /** @var MY_Controller $CI */

    if (!$CI->session->userdata('role_id')) {
        redirect('auth/login_required');
    } elseif ($CI->session->userdata('role_id') != 4) {
        redirect('auth/login_required');
    }
    return;
}

/**
 * Helper memberikan akses ke Admin
 * @return void 
 */
function is_admin()
{

    $CI =& get_instance();
    /** @var MY_Controller $CI */

    if (!$CI->session->userdata('role_id')) {
        redirect('auth/login_required');
    } elseif ($CI->session->userdata('role_id') != 3) {
        redirect('auth/login_required');
    }
    return;
}

/**
 * Helper untuk membuat chat whatsapp message
 * @param  integer $nomor default = 0
 * @return string  $chat
 * @author DioClaude <diobagussaputra0852@gmail.com>
 */
function sendWhatsapp($nomor = 0)
{

    $CI =& get_instance();
    /** @var MY_Controller $CI */

    date_default_timezone_set('Asia/Jakarta');
    $time = date("H");
    ($time < "11") ? $waktu = "Selamat Pagi" : NULL;
    ($time >= "11" && $time < "15") ? $waktu = "Selamat Siang" : NULL;
    ($time >= "15" && $time < "19") ? $waktu = "Selamat Sore" : NULL;
    ($time >= "19") ? $waktu = "Selamat Malam" : NULL;

    $isi = $CI->db->where('tipe', 'default')->get('tbl_chatwhatsapp')->row()->isi;
    $chat = 'https://api.whatsapp.com/send?phone=' . $nomor . '&text=' . $waktu . ',%0A' . $isi;

    return $chat;
}

function selisihWaktu($start, $end)
{
    set_zone();
    if ($start == 'now') {
        $start = new DateTime();
    } else {
        $start = new DateTime($start);
    }
    $end = new DateTime($end);
    $diff = date_diff($end, $start);
    if ($diff->y > 0) {
        return $diff->y . ' tahun ';
    } else {
        if ($diff->m > 0) {
            return $diff->m . ' bulan ';
        } else {
            if ($diff->d > 0) {
                return $diff->d . ' hari ';
            } else {
                if ($diff->h > 0) {
                    return $diff->h . ' jam ';
                } else {
                    if ($diff->i > 0) {
                        return $diff->i . ' menit ';
                    } else {
                        if ($diff->s > 0) {
                            return '1 > Menit';
                        } else {
                            return 'Baru Saja';
                        }
                    }
                }
            }
        }
    }
}

function perusahaan()
{

}