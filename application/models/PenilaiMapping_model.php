<?php
defined('BASEPATH') or exit('No direct script access allowed');

class PenilaiMapping_model extends CI_Model
{
    private $table = 'penilai_mapping';

    // Ambil semua data, urut berdasarkan ID ASC
    public function getAll()
    {
        return $this->db->order_by('id', 'ASC')->get($this->table)->result();
    }

    // Ambil data berdasarkan ID
    public function getById($id)
    {
        return $this->db->get_where($this->table, ['id' => $id])->row();
    }

    // Tambah data baru
    public function insert($data)
    {
        $data['created_at'] = date('Y-m-d H:i:s');
        $data['updated_at'] = date('Y-m-d H:i:s');
        return $this->db->insert($this->table, $data);
    }

    // Update data berdasarkan ID
    public function update($id, $data)
    {
        $data['updated_at'] = date('Y-m-d H:i:s');
        return $this->db->where('id', $id)->update($this->table, $data);
    }

    // Hapus data berdasarkan ID
    public function delete($id)
    {
        return $this->db->where('id', $id)->delete($this->table);
    }
}
