<?php
class Konsumen_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->_check_schema();
    }

    private function _check_schema()
    {
        if ($this->db->table_exists('tbl_konsumen')) {
            if (!$this->db->field_exists('is_blacklist', 'tbl_konsumen')) {
                $this->db->query("ALTER TABLE tbl_konsumen ADD COLUMN is_blacklist TINYINT(1) NOT NULL DEFAULT 0");
            }
            if (!$this->db->field_exists('catatan_blacklist', 'tbl_konsumen')) {
                $this->db->query("ALTER TABLE tbl_konsumen ADD COLUMN catatan_blacklist TEXT NULL");
            }
            // Auto-check and fix collation if binary to ensure case-insensitivity across environments
            $col_query = $this->db->query("SHOW FULL COLUMNS FROM tbl_konsumen WHERE Field = 'konsumen_nama'");
            if ($col_query && ($col_row = $col_query->row())) {
                if (isset($col_row->Collation) && strpos($col_row->Collation, '_bin') !== false) {
                    @$this->db->query("ALTER TABLE tbl_konsumen CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci");
                }
            }
        }
    }

    public function get_datatables($request)
    {
        $columns = ['konsumen_id', 'konsumen_nama', 'konsumen_nohp', 'konsumen_alamat', 'konsumen_email', 'status'];
        $search = trim((string) ($request['search']['value'] ?? ''));
        $limit = $request['length'];
        $start = $request['start'];

        if (!empty($request['order'])) {
            $order = $request['order']['0']['column'];
            $order_column = $columns[$order];
            $order_dir = $request['order']['0']['dir'];
            $this->db->order_by($order_column, $order_dir);
        } else {
            // Default order by konsumen_id DESC
            $this->db->order_by('konsumen_id', 'DESC');
        }

        $this->db->from('tbl_konsumen');

        if (!empty($search)) {
            $keywords = array_filter(preg_split('/\s+/', $search));
            $clean_phone = preg_replace('/[^0-9]/', '', $search);

            if (!empty($keywords)) {
                $this->db->group_start();
                foreach ($keywords as $kw) {
                    $kw_clean = strtolower($kw);
                    $this->db->group_start();
                    $this->db->like('LOWER(konsumen_nama)', $kw_clean);
                    $this->db->or_like('LOWER(konsumen_nohp)', $kw_clean);
                    $this->db->or_like('LOWER(konsumen_email)', $kw_clean);
                    $this->db->or_like('LOWER(konsumen_alamat)', $kw_clean);
                    if (strlen($clean_phone) >= 3) {
                        $this->db->or_like("REPLACE(REPLACE(konsumen_nohp, ' ', ''), '-', '')", $clean_phone);
                    }
                    $this->db->group_end();
                }
                $this->db->group_end();
            }
        }

        // [NEW] Add Filter by Status
        if (isset($request['columns'][5]['search']['value']) && $request['columns'][5]['search']['value'] !== '') {
            $this->db->where('status', $request['columns'][5]['search']['value']);
        }

        $totalFiltered = $this->db->count_all_results('', FALSE);

        $this->db->limit($limit, $start);
        $query = $this->db->get();

        $data = [];
        $no = $start + 1;
        foreach ($query->result() as $row) {
            $data[] = [
                'no' => $no++,
                'konsumen_id' => $row->konsumen_id,
                'konsumen_nama' => $row->konsumen_nama,
                'konsumen_nohp' => $row->konsumen_nohp,
                'konsumen_alamat' => $row->konsumen_alamat,
                'konsumen_email' => $row->konsumen_email,
                'status' => $row->status,
                'is_blacklist' => isset($row->is_blacklist) ? (int)$row->is_blacklist : 0,
                'catatan_blacklist' => $row->catatan_blacklist ?? ''
            ];
        }

        $totalData = $this->db->count_all('tbl_konsumen');

        return [
            "draw" => intval($request['draw']),
            "recordsTotal" => $totalData,
            "recordsFiltered" => $totalFiltered,
            "data" => $data
        ];
    }


}