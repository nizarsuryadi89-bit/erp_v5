<?php
class M_routing extends CI_Model
{
    private $table = 'routing';

    public function getById($id)
    {
        return $this->db->get_where($this->table, ['routing_id' => $id])->row();
    }

    public function update($id, $data)
    {
        return $this->db->where('routing_id', $id)->update($this->table, $data);
    }

    public function insert($data)
    {
        return $this->db->insert($this->table, $data);
    }

    
}
