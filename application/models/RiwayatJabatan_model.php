<?php
defined('BASEPATH') or exit('No direct script access allowed');

class RiwayatJabatan_model extends CI_Model
{
    public function getByNik($nik)
    {
        return $this->db->order_by('tgl_mulai', 'DESC')
            ->get_where('riwayat_jabatan', ['nik' => $nik])
            ->result();
    }

    public function insertRiwayat($data)
    {
        return $this->db->insert('riwayat_jabatan', $data);
    }

    public function updateRiwayat($id, $data)
    {
        return $this->db->where('id', $id)->update('riwayat_jabatan', $data);
    }

    public function nonaktifkanPegawai($nik)
    {
        // Update status di riwayat
        $this->db->where('nik', $nik);
        $this->db->update('riwayat_jabatan', ['status' => 'nonaktif']);

        // Update ke tabel users
        $this->db->where('nik', $nik);
        $this->db->update('users', ['is_active' => 0]);
    }

    public function updateStatusPegawai($nik, $status)
    {
        // Update riwayat_jabatan terbaru
        $this->db->where('nik', $nik);
        $this->db->where('tgl_selesai IS NULL');
        $this->db->update('riwayat_jabatan', ['status' => $status]);

        // Update juga tabel pegawai (kolom status harus ada di tabel pegawai)
        $this->db->where('nik', $nik);
        $this->db->update('pegawai', ['status' => $status]);
    }

}
