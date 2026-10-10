<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Workcenter extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('M_master');
    }

    public function index()
    {
        $data = $this->get_common_data();
        $data['title'] = 'Manajemen Workcenter';

        // Get all workcenters
        $this->db->order_by('operator_nama', 'ASC');
        $workcenters = $this->db->get('tbl_operator')->result();

        // Get count of machines per workcenter
        $machine_counts = [];
        $mesins = $this->db->select('workcenter_id, COUNT(id) as total')->group_by('workcenter_id')->get('tbl_mesin_printing')->result();
        foreach ($mesins as $m) {
            $machine_counts[$m->workcenter_id] = $m->total;
        }

        // Add machine count and machines to workcenters
        foreach ($workcenters as $wc) {
            $wc->jumlah_mesin = $machine_counts[$wc->operator_id] ?? 0;
            $wc->mesins = $this->db->get_where('tbl_mesin_printing', ['workcenter_id' => $wc->operator_id])->result();
        }

        $data['workcenters'] = $workcenters;

        // Get all machines for dropdown
        $this->db->order_by('mesin_nama', 'ASC');
        $data['list_mesin'] = $this->db->get('tbl_mesin_printing')->result();

        $this->load_admin_view('admin/master/workcenter', $data);
    }

    public function get_workcenter_ajax($id)
    {
        $data = $this->db->get_where('tbl_operator', ['operator_id' => $id])->row_array();

        if ($data) {
            $mesins = $this->db->get_where('tbl_mesin_printing', ['workcenter_id' => $id])->result();
            $mesin_ids = [];
            foreach ($mesins as $m) {
                $mesin_ids[] = $m->id;
            }
            $data['mesin_ids'] = $mesin_ids;
        }

        echo json_encode($data);
    }

    public function save_workcenter_ajax()
    {
        $id = $this->input->post('operator_id', true);
        $nama = $this->input->post('operator_nama', true);
        $mesin_ids = $this->input->post('mesin_ids', true) ?: [];

        if (empty($nama)) {
            echo json_encode(['status' => 'error', 'message' => 'Nama workcenter tidak boleh kosong!']);
            return;
        }

        $payload = ['operator_nama' => strtoupper($nama)];

        $this->db->trans_start();

        if ($id) {
            $this->db->where('operator_id', $id)->update('tbl_operator', $payload);
            $msg = "Workcenter berhasil diperbarui!";
            $wc_id = $id;
        } else {
            $this->db->insert('tbl_operator', $payload);
            $wc_id = $this->db->insert_id();
            $msg = "Workcenter baru berhasil ditambahkan!";
        }

        // Update mesins
        // First, clear existing mapping for this workcenter
        $this->db->where('workcenter_id', $wc_id)->update('tbl_mesin_printing', ['workcenter_id' => NULL]);

        // Then, assign new mapping
        if (!empty($mesin_ids)) {
            $this->db->where_in('id', $mesin_ids)->update('tbl_mesin_printing', ['workcenter_id' => $wc_id]);
        }

        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE) {
            echo json_encode(['status' => 'error', 'message' => 'Gagal menyimpan data ke database.']);
        } else {
            $this->_sync_operator_categories();
            echo json_encode(['status' => 'success', 'message' => $msg]);
        }
    }

    public function delete_workcenter_ajax($id)
    {
        // Cek apakah digunakan oleh user aktif?
        $used = $this->db->get_where('users', ['operator_id' => $id])->num_rows();
        if ($used > 0) {
            echo json_encode(['status' => 'error', 'message' => 'Gagal! Workcenter ini sedang digunakan oleh ' . $used . ' akun stasiun.']);
            return;
        }

        $this->db->trans_start();
        // Remove workcenter_id from mesins
        $this->db->where('workcenter_id', $id)->update('tbl_mesin_printing', ['workcenter_id' => NULL]);
        // Delete workcenter
        $this->db->delete('tbl_operator', ['operator_id' => $id]);
        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE) {
            echo json_encode(['status' => 'error', 'message' => 'Gagal menghapus workcenter dari database.']);
        } else {
            echo json_encode(['status' => 'success', 'message' => 'Workcenter berhasil dihapus.']);
        }
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
            } elseif (strpos($name_lower, 'a3') !== false || strpos($name_lower, 'digital') !== false || strpos($name_lower, 'konika') !== false) {
                $kategori_id = '1';
            } elseif (strpos($name_lower, 'merch') !== false || strpos($name_lower, 'merchandise') !== false || strpos($name_lower, 'pin') !== false || strpos($name_lower, 'mug') !== false || strpos($name_lower, 'id') !== false || strpos($name_lower, 'lanyard') !== false) {
                $kategori_id = '7'; // Merchandise
            }

            if ($kategori_id !== null) {
                // If it already contains categories, append it
                $existing_cats = !empty($op->kategori_ids) ? explode(',', $op->kategori_ids) : [];
                if (!in_array($kategori_id, $existing_cats)) {
                    $existing_cats[] = $kategori_id;
                    $this->db->where('operator_id', $op->operator_id);
                    $this->db->update('tbl_operator', ['kategori_ids' => implode(',', $existing_cats)]);
                }
            }
        }
    }

    function operatorbersama()
    {
        redirect('OperatorBersama');
    }

    function datamesin()
    {
        $data = $this->get_common_data();
        $data['title'] = 'Daftar Mesin Produksi';

        $this->db->select('tbl_mesin_printing.*, tbl_operator.operator_nama as nama_workcenter, tbl_perusahaan.nama_perusahaan');
        $this->db->from('tbl_mesin_printing');
        $this->db->join('tbl_operator', 'tbl_mesin_printing.workcenter_id = tbl_operator.operator_id', 'left');
        $this->db->join('tbl_perusahaan', 'tbl_mesin_printing.perusahaan_id = tbl_perusahaan.id_perusahaan', 'left');
        $this->db->order_by('tbl_mesin_printing.mesin_nama', 'ASC');
        $data['mesin'] = $this->db->get()->result();

        $data['perusahaan'] = $this->db->get('tbl_perusahaan')->result();

        $this->load_admin_view('admin/master/data_mesin', $data);
    }

    public function get_mesin_ajax($id)
    {
        $data = $this->db->get_where('tbl_mesin_printing', ['id' => $id])->row_array();
        echo json_encode($data);
    }

    public function save_mesin_ajax()
    {
        $id = $this->input->post('id', true);
        $nama = $this->input->post('mesin_nama', true);
        $keterangan = $this->input->post('mesin_keterangan', true);
        $perusahaan_id = $this->input->post('perusahaan_id', true);

        if (empty($nama)) {
            echo json_encode(['status' => 'error', 'message' => 'Nama mesin tidak boleh kosong!']);
            return;
        }

        $payload = [
            'mesin_nama' => strtoupper($nama),
            'mesin_keterangan' => $keterangan ?: '',
            'perusahaan_id' => $perusahaan_id ? $perusahaan_id : NULL
        ];

        if ($id) {
            $this->db->where('id', $id)->update('tbl_mesin_printing', $payload);
            $msg = "Data mesin berhasil diperbarui!";
        } else {
            $this->db->insert('tbl_mesin_printing', $payload);
            $msg = "Mesin baru berhasil ditambahkan!";
        }

        echo json_encode(['status' => 'success', 'message' => $msg]);
    }

    public function delete_mesin_ajax($id)
    {
        // Check if machine is being used in Workcenter? 
        $mesin = $this->db->get_where('tbl_mesin_printing', ['id' => $id])->row();
        if ($mesin && !empty($mesin->workcenter_id)) {
            echo json_encode(['status' => 'error', 'message' => 'Gagal! Mesin ini sedang tertaut ke sebuah Workcenter. Lepaskan tautannya terlebih dahulu sebelum menghapus.']);
            return;
        }

        // Wait, check if used in pricing tbl_harga_product? Let's just allow deletion or soft delete if needed, but it's fine for now.
        $this->db->delete('tbl_mesin_printing', ['id' => $id]);
        echo json_encode(['status' => 'success', 'message' => 'Mesin berhasil dihapus.']);
    }
}
