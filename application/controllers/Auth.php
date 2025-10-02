<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * @property Pegawai_model $Pegawai_model
 * @property Nilai_model $Nilai_model
 * @property DataDiri_model $DataDiri_model
 * @property Penilaian_model $Penilaian_model
 * @property Indikator_model $Indikator_model
 * @property Coaching_model $Coaching_model
 * @property RiwayatJabatan_model $RiwayatJabatan_model
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

    // Tampilkan form login
    public function index()
    {
        $this->load->view('login');
    }

    // Proses login
    public function login()
    {
        $nik      = $this->input->post('nik', TRUE);
        $password = $this->input->post('password', TRUE);

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

        // Simpan session sesuai role dari DB
        $this->session->set_userdata([
            'nik'       => $user->nik,
            'role'      => $user->role,
            'logged_in' => TRUE
        ]);

        // Flashdata sukses login
        $this->session->set_flashdata('login_success', 'Selamat datang, ' . $user->nik);

        // Redirect sesuai role
        if ($user->role === 'administrator') {
            redirect('administrator'); // dashboard admin (menu lengkap)
        } else {
            redirect('pegawai'); // dashboard pegawai
        }
    }

    // Logout
    public function logout()
    {
        $this->session->set_flashdata('logout_success', 'Anda berhasil logout');
        $this->session->sess_destroy();
        redirect('auth');
    }

    // Cek role (opsional, bisa dipakai untuk AJAX)
    public function check_role()
    {
        $nik = $this->input->post('nik', TRUE);

        if (!$nik) {
            echo json_encode(['is_administrator' => false]);
            return;
        }

        $this->load->model('Auth_model');
        $user = $this->Auth_model->get_user($nik);

        if ($user && $user->role === 'administrator') {
            echo json_encode(['is_administrator' => true]);
        } else {
            echo json_encode(['is_administrator' => false]);
        }
    }

    // Buat administrator default (opsional)
    public function create_administrator()
    {
        $password = password_hash("admin123", PASSWORD_DEFAULT);
        $data = [
            'nik'      => '1234567890',
            'password' => $password,
            'role'     => 'administrator'
        ];
        $this->db->insert('users', $data);
        echo "administrator berhasil dibuat!";
    }
}
