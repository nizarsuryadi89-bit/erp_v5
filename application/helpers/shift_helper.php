<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Check if cashier has an active shift
 * @param int $user_id User ID of the cashier
 * @return bool True if has active shift, false otherwise
 */
function check_cashier_shift($user_id)
{
    $CI =& get_instance();

    // Only check for kasir (level 3 or 4)
    $user_level = $CI->session->userdata('level');
    if ($user_level != 3 && $user_level != 4) {
        return true; // Allow non-kasir users
    }

    $CI->db->where('user_id', $user_id);
    $CI->db->where('status', 'open');
    $CI->db->where('tanggal_shift', date('Y-m-d'));
    $CI->db->limit(1);

    $query = $CI->db->get('kasir_shift');
    return $query->num_rows() > 0;
}

/**
 * Get current shift information for cashier
 * @param int $user_id User ID of the cashier
 * @return object|null Shift object or null if no active shift
 */
function get_current_shift($user_id)
{
    $CI =& get_instance();

    $CI->db->where('user_id', $user_id);
    $CI->db->where('status', 'open');
    $CI->db->where('tanggal_shift', date('Y-m-d'));
    $CI->db->limit(1);

    $query = $CI->db->get('kasir_shift');
    return $query->num_rows() > 0 ? $query->row() : null;
}

/**
 * Redirect to shift management if no active shift
 * @param int $user_id User ID of the cashier
 */
function redirect_if_no_shift($user_id)
{
    if (!check_cashier_shift($user_id)) {
        $CI =& get_instance();
        $CI->session->set_flashdata('error', 'Anda harus membuka shift terlebih dahulu sebelum dapat melakukan transaksi. Silakan buka shift di menu Kasir Shift Management.');
        redirect('KasirShift');
    }
}

/**
 * Get shift status message for display
 * @param int $user_id User ID of the cashier
 * @return array Array with status and message
 */
function get_shift_status_message($user_id)
{
    $CI =& get_instance();

    // Only check for kasir (level 3 or 4)
    $user_level = $CI->session->userdata('level');
    if ($user_level != 3 && $user_level != 4) {
        return [
            'has_shift' => true,
            'message' => '',
            'shift_id' => null
        ];
    }

    $current_shift = get_current_shift($user_id);

    if ($current_shift) {
        return [
            'has_shift' => true,
            'message' => 'Shift aktif sejak ' . date('H:i', strtotime($current_shift->jam_open)) . ' dengan modal Rp ' . number_format($current_shift->modal_awal, 0, ',', '.'),
            'shift_id' => $current_shift->shift_id
        ];
    } else {
        return [
            'has_shift' => false,
            'message' => 'Tidak ada shift aktif. Silakan buka shift terlebih dahulu.',
            'shift_id' => null
        ];
    }
}
