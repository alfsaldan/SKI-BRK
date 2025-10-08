<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Auth_model extends CI_Model
{

    private $table = 'users';

    public function get_user($nik)
    {
        return $this->db->get_where($this->table, [
            'nik' => $nik,
            'is_active' => 1
        ])->row();
    }

    // Insert user baru (opsional)
    public function insert_user($data)
    {
        return $this->db->insert($this->table, $data);
    }

    public function check_nik_ajax()
    {
        $nik = $this->input->post('nik');
        $user = $this->Auth_model->get_user($nik);

        if (!$user) {
            echo json_encode(['status' => 'not_found']);
        } else if ($user->role == 'administrator') {
            echo json_encode(['status' => 'administrator']);
        } else if ($user->role == 'administrator_renstra') {
            echo json_encode(['status' => 'administrator_renstra']);
        } else {
            echo json_encode(['status' => 'pegawai']);
        }
    }
}
