<?php
class Konsumen_model extends CI_model
{
    public function get_datatables($request)
    {
        $columns = ['konsumen_id', 'konsumen_nama', 'konsumen_nohp', 'konsumen_alamat', 'konsumen_email', 'status'];
        $search = $request['search']['value'];
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
            $this->db->group_start();
            $this->db->like('konsumen_nama', $search);
            $this->db->or_like('konsumen_nohp', $search);
            $this->db->or_like('konsumen_email', $search);
            $this->db->or_like('konsumen_alamat', $search);
            $this->db->group_end();
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
                'status' => $row->status
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