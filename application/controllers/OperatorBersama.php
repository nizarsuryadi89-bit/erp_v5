<?php
defined('BASEPATH') or exit('No direct script access allowed');

class OperatorBersama extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('M_master');
    }

    public function index()
    {
        $data = $this->get_common_data();
        $data['title'] = 'Manajemen Operator Bersama';

        $this->db->select('users.*, tbl_karyawan.nama_lengkap, tbl_karyawan.nik, tbl_karyawan.pin_produksi, tbl_perusahaan.nama_perusahaan, tbl_divisi.divisi_nama, tbl_operator.operator_nama');
        $this->db->from('users');
        $this->db->join('tbl_karyawan', 'tbl_karyawan.karyawan_id = users.karyawan_id', 'left');
        $this->db->join('tbl_perusahaan', 'tbl_perusahaan.id_perusahaan = users.perusahaan_id1', 'left');
        $this->db->join('tbl_divisi', 'tbl_divisi.divisi_id = tbl_karyawan.divisi_id', 'left');
        $this->db->join('tbl_operator', 'tbl_operator.operator_id = users.operator_id', 'left');

        $this->db->group_start();
        $this->db->where_in('users.role_id', [4, 20]); // 4 = Operator Produksi, 20 = Finishing
        $this->db->or_like('users.username', 'produksi_', 'after');
        $this->db->or_like('users.username', 'op_', 'after');
        $this->db->or_like('users.username', 'operator_', 'after');
        $this->db->or_like('users.username', 'fn_', 'after');
        $this->db->or_like('users.username', 'pb_', 'after');
        $this->db->or_where('users.username', 'produksi');
        $this->db->group_end();

        // Apply Filters
        $filter_cabang = $this->input->get('cabang', true);
        $filter_operator = $this->input->get('operator', true);

        if (!empty($filter_cabang)) {
            $this->db->where('users.perusahaan_id1', $filter_cabang);
        }
        if (!empty($filter_operator)) {
            if ($filter_operator == 'none') {
                $this->db->group_start();
                $this->db->where('users.operator_id IS NULL', null, false);
                $this->db->or_where('users.operator_id', 0);
                $this->db->group_end();
            } else {
                $this->db->where('users.operator_id', $filter_operator);
            }
        }

        $this->db->order_by('users.username', 'ASC');

        $data['operators'] = $this->db->get()->result();
        $data['companies'] = $this->db->get('tbl_perusahaan')->result();
        $data['divisions'] = $this->db->get('tbl_divisi')->result();
        $data['list_operator'] = $this->db->get('tbl_operator')->result();
        $data['list_mesin'] = $this->db->get('tbl_mesin_printing')->result();

        // Map list_mesin for easy access
        $mesin_map = [];
        foreach ($data['list_mesin'] as $m) {
            $mesin_map[$m->id] = $m->mesin_nama;
        }

        // Attach mesin_nama_list to operators
        foreach ($data['operators'] as $op) {
            $op->mesin_nama_list = [];
            if (!empty($op->mesin_ids)) {
                $ids = explode(',', $op->mesin_ids);
                foreach ($ids as $mid) {
                    if (isset($mesin_map[$mid])) {
                        $op->mesin_nama_list[] = $mesin_map[$mid];
                    }
                }
            }
        }

        // Fetch tasks mapped by operator_id
        $this->db->select('operator_id, GROUP_CONCAT(DISTINCT routing_name SEPARATOR \', \') as tasks');
        $this->db->from('routing');
        $this->db->group_by('operator_id');
        $routing_data = $this->db->get()->result();

        $operator_tasks = [];
        foreach ($routing_data as $rt) {
            $operator_tasks[$rt->operator_id] = $rt->tasks;
        }
        $data['operator_tasks'] = $operator_tasks;

        $data['filter_cabang'] = $filter_cabang;
        $data['filter_operator'] = $filter_operator;

        $this->load_admin_view('admin/master/operator_bersama', $data);
    }

    public function get_operator_ajax($user_id)
    {
        $this->db->select('users.*, tbl_karyawan.nama_lengkap, tbl_karyawan.nik, tbl_karyawan.divisi_id, users.mesin_ids');
        $this->db->from('users');
        $this->db->join('tbl_karyawan', 'tbl_karyawan.karyawan_id = users.karyawan_id');
        $this->db->where('users.user_id', $user_id);
        $data = $this->db->get()->row();
        echo json_encode($data);
    }

    public function save_operator_ajax()
    {
        $user_id = $this->input->post('user_id', true);
        $karyawan_id = $this->input->post('karyawan_id', true);

        $username = $this->input->post('username', true);

        // Cek duplikat username
        $this->db->where('username', $username);
        $this->db->where('user_id !=', $user_id);
        $existing = $this->db->get('users')->row();
        if ($existing) {
            echo json_encode(['status' => 'error', 'message' => "Username '$username' sudah terdaftar! Silakan gunakan username lain."]);
            return;
        }

        $divisi_id = $this->input->post('divisi_id', true);
        $role_id = 4; // Default
        if ($divisi_id == 3)
            $role_id = 4; // Operator Produksi
        elseif ($divisi_id == 5)
            $role_id = 20; // Finishing
        elseif ($divisi_id == 7)
            $role_id = 19; // Pengambilan Barang

        $operator_id = $this->input->post('operator_id', true) ?: NULL;
        if (empty($operator_id)) {
            // Auto detect based on username keywords
            $username_lower = strtolower($username);

            // Find a profile in tbl_operator that matches
            $this->db->select('operator_id, operator_nama');
            $op_profiles = $this->db->get('tbl_operator')->result();
            foreach ($op_profiles as $profile) {
                $prof_name = strtolower($profile->operator_nama);

                // Match keywords
                if (
                    (strpos($username_lower, 'uv') !== false && (strpos($prof_name, 'uv') !== false || strpos($prof_name, 'flatbed') !== false || strpos($prof_name, 'platbed') !== false)) ||
                    (strpos($username_lower, 'platbed') !== false && (strpos($prof_name, 'uv') !== false || strpos($prof_name, 'flatbed') !== false || strpos($prof_name, 'platbed') !== false)) ||
                    (strpos($username_lower, 'flatbed') !== false && (strpos($prof_name, 'uv') !== false || strpos($prof_name, 'flatbed') !== false || strpos($prof_name, 'platbed') !== false)) ||
                    (strpos($username_lower, 'sublim') !== false && strpos($prof_name, 'sublim') !== false) ||
                    (strpos($username_lower, 'lf') !== false && (strpos($prof_name, 'large') !== false || strpos($prof_name, 'format') !== false)) ||
                    (strpos($username_lower, 'large') !== false && (strpos($prof_name, 'large') !== false || strpos($prof_name, 'format') !== false)) ||
                    (strpos($username_lower, 'dtf') !== false && strpos($prof_name, 'dtf') !== false) ||
                    (strpos($username_lower, 'dtg') !== false && strpos($prof_name, 'dtg') !== false) ||
                    (strpos($username_lower, 'laser') !== false && strpos($prof_name, 'laser') !== false) ||
                    (strpos($username_lower, 'merch') !== false && (strpos($prof_name, 'merch') !== false || strpos($prof_name, 'merchandise') !== false)) ||
                    (strpos($username_lower, 'tekstil') !== false && (strpos($prof_name, 'tekstil') !== false || strpos($prof_name, 'textile') !== false)) ||
                    (strpos($username_lower, 'textile') !== false && (strpos($prof_name, 'tekstil') !== false || strpos($prof_name, 'textile') !== false)) ||
                    (strpos($username_lower, 'a3') !== false && (strpos($prof_name, 'a3') !== false || strpos($prof_name, 'digital') !== false || strpos($prof_name, 'konika') !== false))
                ) {
                    $operator_id = $profile->operator_id;
                    break;
                }
            }
        }

        $mesin_ids = [];
        if (!empty($operator_id)) {
            $mesins = $this->db->get_where('tbl_mesin_printing', ['workcenter_id' => $operator_id])->result();
            foreach ($mesins as $m) {
                $mesin_ids[] = $m->id;
            }
        }
        $mesin_ids_str = !empty($mesin_ids) ? implode(',', $mesin_ids) : NULL;

        $data_user = [
            'username' => $username,
            'perusahaan_id1' => $this->input->post('perusahaan_id', true),
            'role_id' => $role_id,
            'operator_id' => $operator_id,
            'mesin_ids' => $mesin_ids_str,
            'updated_at' => date('Y-m-d H:i:s')
        ];

        $data_karyawan = [
            'nama_lengkap' => $this->input->post('nama_lengkap', true),
            'nik' => $this->input->post('nik', true),
            'perusahaan_id' => $this->input->post('perusahaan_id', true),
            'divisi_id' => $this->input->post('divisi_id', true),
            'updated_at' => date('Y-m-d H:i:s')
        ];

        $this->db->trans_start();
        $this->db->where('user_id', $user_id)->update('users', $data_user);
        $this->db->where('karyawan_id', $karyawan_id)->update('tbl_karyawan', $data_karyawan);
        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE) {
            echo json_encode(['status' => 'error', 'message' => 'Gagal memperbarui data database.']);
        } else {
            $this->_sync_operator_categories();
            echo json_encode(['status' => 'success', 'message' => 'Data operator berhasil diperbarui!']);
        }
    }

    public function update_pin()
    {
        $karyawan_id = $this->input->post('karyawan_id', true);
        $pin = $this->input->post('pin', true);

        if (strlen($pin) != 4) {
            echo json_encode(['status' => 'error', 'message' => 'PIN harus 4 digit!']);
            return;
        }

        $this->db->where('karyawan_id', $karyawan_id);
        $this->db->update('tbl_karyawan', ['pin_produksi' => $pin]);

        echo json_encode(['status' => 'success', 'message' => 'PIN operator berhasil diperbarui!']);
    }

    public function add_operator_ajax()
    {
        $prefix = $this->input->post('username_prefix', true);
        $suffix = preg_replace('/[^a-zA-Z0-9_]/', '', $this->input->post('username_suffix', true)); // Clean input
        $username = strtolower($prefix . $suffix);

        // 1. Cek duplikasi username
        $exists = $this->db->get_where('users', ['username' => $username])->row();
        if ($exists) {
            echo json_encode(['status' => 'error', 'message' => 'Username ' . $username . ' sudah digunakan!']);
            return;
        }

        // 2. Generate NIK Dummy (Misal: STATION-2026.001)
        $this->db->like('nik', 'STATION-', 'after');
        $last_nik = $this->db->order_by('karyawan_id', 'DESC')->get('tbl_karyawan')->row();
        $counter = 1;
        if ($last_nik) {
            $parts = explode('.', $last_nik->nik);
            $counter = (isset($parts[1]) ? (int) $parts[1] : 0) + 1;
        }
        $new_nik = 'STATION-' . date('Y') . '.' . str_pad($counter, 3, '0', STR_PAD_LEFT);

        // 3. Simpan ke tbl_karyawan
        $data_karyawan = [
            'nik' => $new_nik,
            'nama_lengkap' => strtoupper($this->input->post('nama_lengkap', true)),
            'perusahaan_id' => $this->input->post('perusahaan_id', true),
            'divisi_id' => $this->input->post('divisi_id', true),
            'pin_produksi' => '1234' // Default PIN
        ];

        $this->db->insert('tbl_karyawan', $data_karyawan);
        $karyawan_id = $this->db->insert_id();

        $divisi_id = $this->input->post('divisi_id', true);
        $role_id = 4; // Default
        if ($divisi_id == 3)
            $role_id = 4; // Operator Produksi
        elseif ($divisi_id == 5)
            $role_id = 20; // Finishing
        elseif ($divisi_id == 7)
            $role_id = 19; // Pengambilan Barang

        // 4. Simpan ke users
        $operator_id = $this->input->post('operator_id', true) ?: NULL;
        if (empty($operator_id)) {
            // Auto detect based on username keywords
            $username_lower = strtolower($username);

            // Find a profile in tbl_operator that matches
            $this->db->select('operator_id, operator_nama');
            $op_profiles = $this->db->get('tbl_operator')->result();
            foreach ($op_profiles as $profile) {
                $prof_name = strtolower($profile->operator_nama);

                // Match keywords
                if (
                    (strpos($username_lower, 'uv') !== false && (strpos($prof_name, 'uv') !== false || strpos($prof_name, 'flatbed') !== false || strpos($prof_name, 'platbed') !== false)) ||
                    (strpos($username_lower, 'platbed') !== false && (strpos($prof_name, 'uv') !== false || strpos($prof_name, 'flatbed') !== false || strpos($prof_name, 'platbed') !== false)) ||
                    (strpos($username_lower, 'flatbed') !== false && (strpos($prof_name, 'uv') !== false || strpos($prof_name, 'flatbed') !== false || strpos($prof_name, 'platbed') !== false)) ||
                    (strpos($username_lower, 'sublim') !== false && strpos($prof_name, 'sublim') !== false) ||
                    (strpos($username_lower, 'lf') !== false && (strpos($prof_name, 'large') !== false || strpos($prof_name, 'format') !== false)) ||
                    (strpos($username_lower, 'large') !== false && (strpos($prof_name, 'large') !== false || strpos($prof_name, 'format') !== false)) ||
                    (strpos($username_lower, 'dtf') !== false && strpos($prof_name, 'dtf') !== false) ||
                    (strpos($username_lower, 'dtg') !== false && strpos($prof_name, 'dtg') !== false) ||
                    (strpos($username_lower, 'laser') !== false && strpos($prof_name, 'laser') !== false) ||
                    (strpos($username_lower, 'merch') !== false && (strpos($prof_name, 'merch') !== false || strpos($prof_name, 'merchandise') !== false)) ||
                    (strpos($username_lower, 'tekstil') !== false && (strpos($prof_name, 'tekstil') !== false || strpos($prof_name, 'textile') !== false)) ||
                    (strpos($username_lower, 'textile') !== false && (strpos($prof_name, 'tekstil') !== false || strpos($prof_name, 'textile') !== false)) ||
                    (strpos($username_lower, 'a3') !== false && (strpos($prof_name, 'a3') !== false || strpos($prof_name, 'digital') !== false || strpos($prof_name, 'konika') !== false))
                ) {
                    $operator_id = $profile->operator_id;
                    break;
                }
            }
        }

        $mesin_ids = [];
        if (!empty($operator_id)) {
            $mesins = $this->db->get_where('tbl_mesin_printing', ['workcenter_id' => $operator_id])->result();
            foreach ($mesins as $m) {
                $mesin_ids[] = $m->id;
            }
        }
        $mesin_ids_str = !empty($mesin_ids) ? implode(',', $mesin_ids) : NULL;

        $data_user = [
            'username' => $username,
            'password' => md5($this->input->post('password', true)),
            'karyawan_id' => $karyawan_id,
            'perusahaan_id1' => $this->input->post('perusahaan_id', true),
            'role_id' => $role_id,
            'operator_id' => $operator_id,
            'mesin_ids' => $mesin_ids_str,
            'active' => '1'
        ];

        $this->db->insert('users', $data_user);

        $this->_sync_operator_categories();

        echo json_encode(['status' => 'success', 'message' => 'Akun stasiun berhasil dibuat! PIN default: 1234']);
    }

    public function update_password_ajax()
    {
        $user_id = $this->input->post('user_id', true);
        $new_password = $this->input->post('password', true);

        if (strlen($new_password) < 4) {
            echo json_encode(['status' => 'error', 'message' => 'Password minimal 4 karakter!']);
            return;
        }

        // Hash password menggunakan BCrypt (Standar ERP)
        $hashed_password = password_hash($new_password, PASSWORD_BCRYPT);

        $this->db->where('user_id', $user_id);
        $update = $this->db->update('users', [
            'password' => $hashed_password,
            'updated_at' => date('Y-m-d H:i:s')
        ]);

        if ($update) {
            echo json_encode(['status' => 'success', 'message' => 'Password operator berhasil diperbarui!']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Gagal memperbarui password di database.']);
        }
    }



    public function delete_shared_account($user_id)
    {
        // Safety: Jangan hapus akun admin
        $user = $this->db->get_where('users', ['user_id' => $user_id])->row();
        if ($user && !in_array($user->role_id, [1, 8, 17])) {
            $this->db->delete('users', ['user_id' => $user_id]);
            $this->session->set_flashdata('success', 'Akun berhasil dihapus.');
        }
        redirect('OperatorBersama');
    }

    private function _sync_operator_categories()
    {
        $operators = $this->db->get('tbl_operator')->result();
        foreach ($operators as $op) {
            $name_lower = strtolower($op->operator_nama);
            $kategori_id = null;

            if (strpos($name_lower, 'uv') !== false || strpos($name_lower, 'flatbed') !== false || strpos($name_lower, 'platbed') !== false || strpos($name_lower, 'rotary') !== false) {
                $kategori_id = '3';
            } elseif (strpos($name_lower, 'large') !== false || strpos($name_lower, 'format') !== false || strpos($name_lower, 'lf') !== false || strpos($name_lower, 'banner') !== false || strpos($name_lower, 'spanduk') !== false) {
                $kategori_id = '2';
            } elseif (strpos($name_lower, 'sublim') !== false) {
                $kategori_id = '4';
            } elseif (strpos($name_lower, 'tekstil') !== false || strpos($name_lower, 'textile') !== false) {
                $kategori_id = '5';
            } elseif (strpos($name_lower, 'laser') !== false || strpos($name_lower, 'cutting') !== false) {
                $kategori_id = '6';
            } elseif (strpos($name_lower, 'dtg') !== false) {
                $kategori_id = '8';
            } elseif (strpos($name_lower, 'dtf') !== false) {
                $kategori_id = '9';
            } elseif (strpos($name_lower, 'merch') !== false || strpos($name_lower, 'merchandise') !== false) {
                $kategori_id = '10';
            } elseif (strpos($name_lower, 'a3') !== false || strpos($name_lower, 'digital') !== false || strpos($name_lower, 'offset') !== false || strpos($name_lower, 'konika') !== false) {
                $kategori_id = '1';
            }

            if ($kategori_id !== null && $op->kategori_ids !== $kategori_id) {
                $this->db->where('operator_id', $op->operator_id);
                $this->db->update('tbl_operator', ['kategori_ids' => $kategori_id]);
            }
        }
    }
}
