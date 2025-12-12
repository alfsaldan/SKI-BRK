<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Syarat_ppk_model extends CI_Model
{
    protected $table = 'syarat_ppk';

    public function getAll()
    {
        return $this->db->order_by('id_ppk', 'ASC')->get($this->table)->result();
    }

    public function getById($id)
    {
        return $this->db->where('id_ppk', $id)->get($this->table)->row();
    }

    public function insert($data)
    {
        $data['created_at'] = date('Y-m-d H:i:s');
        $this->db->insert($this->table, $data);
        return $this->db->insert_id();
    }

    public function update($id, $data)
    {
        $data['updated_at'] = date('Y-m-d H:i:s');
        return $this->db->where('id_ppk', $id)->update($this->table, $data);
    }

    public function delete($id)
    {
        return $this->db->where('id_ppk', $id)->delete($this->table);
    }
}
