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
        $this->load->view('login', ['role_options' => []]); // default kosong
    }

    // Proses login
    public function login()
    {
        $nik = $this->input->post('nik', TRUE);
        $password = $this->input->post('password', TRUE);
        $selected_role = $this->input->post('role', TRUE); // role pilihan superadmin

        $user = $this->Auth_model->get_user($nik);

        if (!$user) {
            $this->session->set_flashdata('error', 'NIK tidak ditemukan');
            redirect('auth');
            return;
        }

        // Cek password
        if (!password_verify($password, $user->password)) {
            $this->session->set_flashdata('error', 'Password salah');
            redirect('auth');
            return;
        }

        // Tentukan role login
        $role_options = [];
        if ($user->role == 'superadmin') {
            $role_options = ['superadmin', 'pegawai'];

            // Jika belum pilih role, tampilkan form kembali
            if (!$selected_role) {
                $this->load->view('login', ['role_options' => $role_options]);
                return;
            }

            $login_role = $selected_role;
        } else {
            $login_role = 'pegawai';
        }

        // Simpan session
        $this->session->set_userdata([
            'nik' => $user->nik,
            'role' => $login_role,
            'logged_in' => TRUE
        ]);

        // **Tambahkan flashdata sukses login**
        $this->session->set_flashdata('login_success', 'Selamat datang, ' . $user->nik);

        // Redirect sesuai role
        if ($login_role === 'superadmin') {
            redirect('superadmin'); // bisa tampilkan SweetAlert di halaman superadmin
        } else {
            redirect('pegawai'); // bisa tampilkan SweetAlert di halaman pegawai
        }
    }

    // Logout
    public function logout()
    {
        $this->session->set_flashdata('logout_success', 'Anda berhasil logout');
        $this->session->sess_destroy();
        redirect('auth');
    }

    // Auth.php
    public function check_role()
    {
        $nik = $this->input->post('nik', TRUE);

        if (!$nik) {
            echo json_encode(['is_superadmin' => false]);
            return;
        }

        $this->load->model('Auth_model');
        $user = $this->Auth_model->get_user($nik);

        if ($user && $user->role === 'superadmin') {
            echo json_encode(['is_superadmin' => true]);
        } else {
            echo json_encode(['is_superadmin' => false]);
        }
    }


    // Buat superadmin default (opsional)
    public function create_superadmin()
    {
        $password = password_hash("admin123", PASSWORD_DEFAULT);
        $data = [
            'nik' => '1234567890',
            'password' => $password,
            'role' => 'superadmin'
        ];
        $this->db->insert('users', $data);
        echo "Superadmin berhasil dibuat!";
    }
}
