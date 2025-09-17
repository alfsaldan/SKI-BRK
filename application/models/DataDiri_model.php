<?php
defined('BASEPATH') or exit('No direct script access allowed');

class DataDiri_model extends CI_Model
{
    // Ambil data pegawai berdasarkan NIK
    public function getDataByNik($nik)
    {
        return $this->db->get_where('pegawai', ['nik' => $nik])->row_array();
    }

    // Update password di tabel pegawai & users
    public function updatePassword($nik, $password)
    {
        $hash = password_hash($password, PASSWORD_BCRYPT);

        // update tabel pegawai
        $this->db->where('nik', $nik);
        $updatePegawai = $this->db->update('pegawai', ['password' => $hash]);

        // update tabel users
        $this->db->where('nik', $nik);
        $updateUsers = $this->db->update('users', ['password' => $hash]);

        return $updatePegawai && $updateUsers;
    }
}
