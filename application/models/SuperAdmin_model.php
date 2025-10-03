<?php
defined('BASEPATH') or exit('No direct script access allowed');

class SuperAdmin_model extends CI_Model
{
    private $table = 'users';

    public function getAllUsers()
    {
        $this->db->select('users.*, pegawai.nama, pegawai.jabatan, pegawai.unit_kerja, pegawai.unit_kantor');
        $this->db->from($this->table);
        $this->db->join('pegawai', 'pegawai.nik = users.nik', 'left'); 
        $this->db->order_by('users.id', 'DESC');
        return $this->db->get()->result();
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
