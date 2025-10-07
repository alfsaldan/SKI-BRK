<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * @property SuperAdmin_model $SuperAdmin_model
 * @property CI_Input $input
 * @property CI_Session $session
 * @property CI_Form_validation $form_validation
 * @property PenilaiMapping_model $PenilaiMapping_model
 */
class SuperAdmin extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('SuperAdmin_model');
        $this->load->model('PenilaiMapping_model');
        $this->load->library('form_validation');

        // 🔒 Cek login SuperAdmin
        if (!$this->session->userdata('nik') || $this->session->userdata('role') != 'superadmin') {
            redirect('auth');
        }
    }

    public function index()
    {
        $users = $this->SuperAdmin_model->getAllUsers();

        // Hitung jumlah user berdasarkan role
        $roleCount = [];
        $statusCount = ['aktif' => 0, 'nonaktif' => 0];

        foreach ($users as $u) {
            $role = strtolower($u->role);
            if (!isset($roleCount[$role])) {
                $roleCount[$role] = 0;
            }
            $roleCount[$role]++;

            if ($u->is_active == 1) {
                $statusCount['aktif']++;
            } else {
                $statusCount['nonaktif']++;
            }
        }

        $data['title'] = "Dashboard SuperAdmin";
        $data['total_users'] = count($users);
        $data['roleCount'] = $roleCount;
        $data['statusCount'] = $statusCount;

        $this->load->view("layoutsuperadmin/header", $data);
        $this->load->view("superadmin/index", $data);
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

    // ========== Halaman Kelola Tingkatan Jabatan ==========
    public function kelolatingkatanjabatan_kpi()
    {
        $data['judul'] = 'Kelola Tingkatan Jabatan KPI';
        $data['list'] = $this->PenilaiMapping_model->getAll();

        $this->load->view('layoutsuperadmin/header', $data);
        $this->load->view('superadmin/kelolatingkatanjabatan_kpi', $data);
        $this->load->view('layoutsuperadmin/footer');
    }

    // Tambah data
    public function tambahPenilaiMapping()
    {
        if ($this->input->post()) {
            $insert = [
                'jabatan'          => $this->input->post('jabatan'),
                'jenis_penilaian'  => $this->input->post('jenis_penilaian'), // ✅ baru ditambahkan
                'unit_kerja'       => $this->input->post('unit_kerja'),
                'penilai1_jabatan' => $this->input->post('penilai1_jabatan'),
                'penilai2_jabatan' => $this->input->post('penilai2_jabatan'),
            ];

            $this->PenilaiMapping_model->insert($insert);
            $this->session->set_flashdata('success', 'Data mapping berhasil ditambahkan.');
            redirect('superadmin/kelolatingkatanjabatan_kpi');
        }
    }

    // Edit data
    public function editPenilaiMapping($id)
    {
        if ($this->input->post()) {
            $update = [
                'jabatan'          => $this->input->post('jabatan'),
                'jenis_penilaian'  => $this->input->post('jenis_penilaian'), // ✅ baru ditambahkan
                'unit_kerja'       => $this->input->post('unit_kerja'),
                'penilai1_jabatan' => $this->input->post('penilai1_jabatan'),
                'penilai2_jabatan' => $this->input->post('penilai2_jabatan'),
            ];

            $this->PenilaiMapping_model->update($id, $update);
            $this->session->set_flashdata('success', 'Data mapping berhasil diubah.');
            redirect('superadmin/kelolatingkatanjabatan_kpi');
        }
    }


    // Hapus data
    public function hapusPenilaiMapping($id)
    {
        if ($this->PenilaiMapping_model->delete($id)) {
            $this->session->set_flashdata('success', 'Data berhasil dihapus');
        }
        redirect('superadmin/kelolatingkatanjabatan_kpi');
    }

    //Kelola Rumus
    public function kelolarumus()
    {
        $data['title'] = "Kelola Rumus";
        $data['rumus'] = $this->SuperAdmin_model->getAllUsers();

        $this->load->view('layoutsuperadmin/header', $data);
        $this->load->view('superadmin/kelolarumus', $data);
        $this->load->view('layoutsuperadmin/footer');
    }
}
