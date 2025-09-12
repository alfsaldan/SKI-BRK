<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Auth_model extends CI_Model {

    private $table = 'users'; // tabel yang dipakai

    public function get_user($nik) {
    return $this->db->get_where($this->table, [
        'nik' => $nik,
        'is_active' => 1
    ])->row();
}

    public function insert_user($data) {
        // data = ['nik', 'password', 'role']
        return $this->db->insert($this->table, $data);
    }

    
}
