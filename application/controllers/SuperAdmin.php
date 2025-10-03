<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * @property SuperAdmin_model $SuperAdmin_model
 * @property CI_Input $input
 * @property CI_Session $session
 * @property CI_Form_validation $form_validation
 */
class SuperAdmin extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('SuperAdmin_model');
        $this->load->library('form_validation');

        // 🔒 Cek login SuperAdmin
        if (!$this->session->userdata('nik') || $this->session->userdata('role') != 'superadmin') {
            redirect('auth');
        }
    }

   public function index()
    {
        $this->load->view("layoutsuperadmin/header");
        $this->load->view("superadmin/index");
        $this->load->view("layoutsuperadmin/footer");
    }

    public function kelolaRoleUser()
    {
        $data['title'] = "Kelola Role User";
        $data['users'] = $this->SuperAdmin_model->getAllUsers();

        $this->load->view('layoutsuperadmin/header', $data);
        $this->load->view('superadmin/kelolaroleuser', $data);
        $this->load->view('layoutsuperadmin/footer');
    }

    public function tambahRoleUser()
    {
        $this->form_validation->set_rules('nik', 'NIK', 'required|trim|is_unique[users.nik]', [
            'is_unique' => 'NIK sudah terdaftar!'
        ]);
        $this->form_validation->set_rules('password', 'Password', 'required|min_length[5]');
        $this->form_validation->set_rules('role', 'Role', 'required');

        if ($this->form_validation->run() == false) {
            $this->session->set_flashdata('error', strip_tags(validation_errors()));
        } else {
            $data = [
                'nik'       => $this->input->post('nik', true),
                'password'  => password_hash($this->input->post('password'), PASSWORD_DEFAULT),
                'role'      => strtolower($this->input->post('role')),
                'is_active' => $this->input->post('is_active') ? 1 : 0
            ];
            $this->SuperAdmin_model->insertUser($data);
            $this->session->set_flashdata('success', 'User baru berhasil ditambahkan.');
        }
        redirect('superadmin/kelolaroleuser');
    }

    public function editRoleUser()
    {
        $id = $this->input->post('id');
        $update = [
            'role'      => strtolower($this->input->post('role')),
            'is_active' => $this->input->post('is_active') ? 1 : 0
        ];

        if (!empty($this->input->post('password'))) {
            $update['password'] = password_hash($this->input->post('password'), PASSWORD_DEFAULT);
        }

        $this->SuperAdmin_model->updateUser($id, $update);
        $this->session->set_flashdata('success', 'User berhasil diperbarui.');
        redirect('superadmin/kelolaroleuser');
    }

    public function hapusRoleUser($id = null)
    {
        if (!$id) {
            $this->session->set_flashdata('error', 'ID user tidak ditemukan.');
            redirect('superadmin/kelolaroleuser');
        }
        $this->SuperAdmin_model->deleteUser($id);
        $this->session->set_flashdata('success', 'User berhasil dihapus.');
        redirect('superadmin/kelolaroleuser');
    }
}
