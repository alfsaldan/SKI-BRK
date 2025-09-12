<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Auth extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('Auth_model');
        $this->load->library('session');
    }

    // Tampilkan form login 
    public function index()
    {
        $this->load->view('login');
    }

    // Proses login
    public function login()
    {
        $nik = $this->input->post('nik', TRUE);
        $password = $this->input->post('password', TRUE);

        $user = $this->Auth_model->get_user($nik);

        if ($user) {
            if (password_verify($password, $user->password)) {
                // Simpan session
                $userdata = [
                    'nik' => $user->nik,
                    'role' => $user->role,
                    'logged_in' => TRUE
                ];
                $this->session->set_userdata($userdata);

                // Arahkan sesuai role
                if ($user->role === 'superadmin') {
                    redirect('superadmin');
                } else {
                    redirect('pegawai');
                }
            } else {
                $this->session->set_flashdata('error', 'Password salah');
                redirect('auth');
            }
        } else {
            $this->session->set_flashdata('error', 'NIK tidak ditemukan');
            redirect('auth');
        }
    }

    // Logout
    public function logout()
    {
        $this->session->sess_destroy();
        redirect('auth');
    }

    public function create_superadmin()
    {
        $password = password_hash("admin123", PASSWORD_DEFAULT); // password default superadmin
        $data = [
            'nik' => '1234567890',
            'password' => $password,
            'role' => 'superadmin'
        ];
        $this->db->insert('users', $data);
        echo "Superadmin berhasil dibuat!";
    }

}
