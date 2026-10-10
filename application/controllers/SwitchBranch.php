<?php
defined('BASEPATH') or exit('No direct script access allowed');

class SwitchBranch extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Change current active branch for Holding/Admin users
     */
    public function index()
    {
        $id_perusahaan = (int) $this->input->get('id', true);
        $user_role_id = (int) $this->session->userdata('level');
        $is_developer = ($this->session->userdata('username') === 'nizarsuryadi');

        $can_switch = false;
        if ($is_developer || in_array($user_role_id, [1, 8, 17, 21])) {
            $can_switch = true;
        } else {
            $cache = $this->session->userdata('app_session_cache');
            if (!empty($cache['is_super_admin'])) {
                $can_switch = true;
            } elseif (!empty($cache['level_info']['is_super_admin'])) {
                $can_switch = true;
            }
        }

        // Security Check: Only authorized roles can switch
        if ($can_switch) {

            // Verify if the target branch exists
            $branch = $this->db->get_where('tbl_perusahaan', ['id_perusahaan' => $id_perusahaan])->row();

            if ($branch) {
                // Update session
                $this->session->set_userdata('perusahaan_id', $id_perusahaan);

                // IMPORTANT: Refresh the session cache so the UI reflects the new branch
                $this->_refresh_session_cache();

                $this->session->set_flashdata('message', '<div class="alert alert-success">Berhasil pindah ke: <b>' . $branch->nama_perusahaan . '</b></div>');
            } else {
                $this->session->set_flashdata('message', '<div class="alert alert-danger">Cabang tidak ditemukan!</div>');
            }
        } else {
            $this->session->set_flashdata('message', '<div class="alert alert-danger">Anda tidak memiliki akses untuk pindah cabang!</div>');
        }

        // Redirect back to previous page or dashboard
        if ($this->input->server('HTTP_REFERER')) {
            $referer = $this->input->server('HTTP_REFERER');
            $parsed_url = parse_url($referer);
            
            if (isset($parsed_url['query'])) {
                parse_str($parsed_url['query'], $query_params);
                if (isset($query_params['branch_id'])) unset($query_params['branch_id']);
                if (isset($query_params['perusahaan_id'])) unset($query_params['perusahaan_id']);
                
                $new_query = http_build_query($query_params);
                
                $referer = $parsed_url['scheme'] . '://' . $parsed_url['host'] . (isset($parsed_url['port']) ? ':' . $parsed_url['port'] : '') . $parsed_url['path'];
                if (!empty($new_query)) {
                    $referer .= '?' . $new_query;
                }
            }
            
            redirect($referer);
        } else {
            redirect('dashboard');
        }
    }
}
