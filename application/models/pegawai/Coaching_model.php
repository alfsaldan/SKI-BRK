<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Coaching_model extends CI_Model
{
    // Simpan pesan baru
    public function simpanPesan($data)
    {
        $result = $this->db->insert('aktivitas_coaching', $data);
        if (!$result) {
            return [
                'success' => false,
                'error' => $this->db->error()
            ];
        }
        return [
            'success' => true
        ];
    }

    // Ambil semua pesan antara pegawai & penilai tertentu
    public function getChat($nikPegawai, $nikPenilai)
    {
        $this->db->select("ac.*, p.nama AS nama_pengirim, p.jabatan");
        $this->db->from("aktivitas_coaching ac");
        $this->db->join("pegawai p", "p.nik = ac.pengirim_nik", "left");
        $this->db->where("ac.nik_pegawai", $nikPegawai);
        $this->db->where("ac.nik_penilai", $nikPenilai);
        $this->db->order_by("ac.created_at", "ASC");
        return $this->db->get()->result();
    }
}
