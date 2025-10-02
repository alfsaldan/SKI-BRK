<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * @property Auth_model $Auth_model
 * @property CI_Input $input
 * @property CI_Session $session
 * @property CI_DB_query_builder $db
 */
class Auth extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Auth_model');
        $this->load->library('session');
    }

    // Form login
    public function index()
    {
        $this->load->view('login');
    }

    // Proses login
    public function login()
    {
        $nik      = $this->input->post('nik', TRUE);
        $password = $this->input->post('password', TRUE);
        $role     = $this->input->post('role', TRUE);

        $user = $this->Auth_model->get_user($nik);

        if (!$user) {
            $this->session->set_flashdata('error', 'NIK tidak ditemukan');
            redirect('auth');
            return;
        }

        if (!password_verify($password, $user->password)) {
            $this->session->set_flashdata('error', 'Password salah');
            redirect('auth');
            return;
        }

        // set session
        $this->session->set_userdata([
            'nik'       => $user->nik,
            'role'      => $user->role,
            'logged_in' => TRUE
        ]);

        $this->session->set_flashdata('login_success', 'Selamat datang, ' . $user->nik);

        // arahkan sesuai role
        switch ($user->role) {
            case 'superadmin':
                redirect('superadmin');
                break;
            case 'administrator':
                redirect('administrator');
                break;
            default:
                redirect('pegawai');
        }
    }

    public function logout()
    {
        $this->session->set_flashdata('logout_success', 'Anda berhasil logout');
        $this->session->sess_destroy();
        redirect('auth');
    }

    // API untuk cek role by NIK
    public function check_role()
    {
        $nik = $this->input->post('nik', TRUE);
        if (!$nik) {
            echo json_encode(['status' => false, 'message' => 'NIK kosong']);
            return;
        }

        $user = $this->Auth_model->get_user($nik);

        if (!$user) {
            echo json_encode(['status' => false, 'message' => 'NIK tidak ditemukan']);
            return;
        }

        echo json_encode([
            'status' => true,
            'role'   => $user->role // "superadmin", "administrator", "pegawai"
        ]);
    }

    // buat superadmin default
    public function create_superadmin()
    {
        $password = password_hash("superadmin123", PASSWORD_DEFAULT);
        $data = [
            'nik'      => '9999999999',
            'password' => $password,
            'role'     => 'superadmin'
        ];
        $this->db->insert('users', $data);
        echo "Superadmin berhasil dibuat!";
    }
}
