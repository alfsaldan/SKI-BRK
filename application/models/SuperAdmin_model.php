<?php
defined('BASEPATH') or exit('No direct script access allowed');

class SuperAdmin_model extends CI_Model
{
    private $table = 'users';

    public function getAllUsers()
    {
        return $this->db->order_by('id', 'DESC')->get($this->table)->result();
    }

    public function insertUser($data)
    {
        return $this->db->insert($this->table, $data);
    }

    public function updateUser($id, $data)
    {
        return $this->db->where('id', $id)->update($this->table, $data);
    }

    public function deleteUser($id)
    {
        return $this->db->delete($this->table, ['id' => $id]);
    }
}
