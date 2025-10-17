<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * @property SuperAdmin_model $SuperAdmin_model
 * @property CI_Input $input
 * @property CI_Session $session
 * @property CI_Output $output
 * @property CI_DB_query_builder $db
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
        $data['kode_cabang'] = $this->SuperAdmin_model->getCabangWithUnitKantor();

        $this->load->view('layoutsuperadmin/header', $data);
        $this->load->view('superadmin/kelolatingkatanjabatan_kpi', $data);
        $this->load->view('layoutsuperadmin/footer');
    }

    // ========== CRUD PENILAI MAPPING ==========

    // Tambah data mapping
    public function tambahPenilaiMapping()
    {
        if (!$this->input->post()) {
            show_error('Metode tidak diizinkan', 405);
            return;
        }

        $kode_cabang = $this->input->post('kode_cabang');
        $kode_unit   = $this->input->post('kode_unit');
        $jabatan     = $this->input->post('jabatan');
        $jenis_penilaian = $this->input->post('jenis_penilaian');

        // ======================
        // Set unit_kantor & unit_kerja
        // ======================
        $unit_kantor = $this->SuperAdmin_model->getUnitKantorByKodeUnit($kode_unit) ?? 'Kantor Pusat';
        $unit_kerja = $this->SuperAdmin_model->getUnitKerjaByKode($kode_cabang, $kode_unit);

        // ======================
        // Generate key unik baru
        // ======================
        $last = $this->db->order_by('id', 'DESC')->limit(1)->get('penilai_mapping')->row();
        $newKey = $last ? ((int)$last->key + 1) : 1;

        // ======================
        // Ambil key dari penilai1 & penilai2 berdasarkan nama jabatan
        // ======================
        $penilai1_nama = $this->input->post('penilai1_jabatan');
        $penilai2_nama = $this->input->post('penilai2_jabatan');

        $penilai1_key = $penilai1_nama ? $this->SuperAdmin_model->getKeyByJabatanAndUnit($penilai1_nama, $kode_unit) : null;
        $penilai2_key = $penilai2_nama ? $this->SuperAdmin_model->getKeyByJabatanAndUnit($penilai2_nama, $kode_unit) : null;

        // ======================
        // Siapkan data insert
        // ======================
        $insert = [
            'kode_cabang'      => $kode_cabang,
            'kode_unit'        => $kode_unit,
            'unit_kantor'      => $unit_kantor,
            'unit_kerja'       => $unit_kerja,
            'jabatan'          => $jabatan,
            'jenis_penilaian'  => $jenis_penilaian,
            'penilai1_jabatan' => $penilai1_key,
            'penilai2_jabatan' => $penilai2_key,
            'key'              => $newKey,
        ];

        if ($this->SuperAdmin_model->saveMapping($insert)) {
            $this->session->set_flashdata('success', 'Data mapping berhasil ditambahkan.');
        } else {
            $this->session->set_flashdata('error', 'Gagal menambahkan data mapping.');
        }

        redirect('superadmin/kelolatingkatanjabatan_kpi');
    }


    // Edit data mapping
    public function editPenilaiMapping($id)
    {
        if ($this->input->post()) {
            $kode_unit   = $this->input->post('kode_unit');

            $penilai1_nama = $this->input->post('penilai1_jabatan');
            $penilai2_nama = $this->input->post('penilai2_jabatan');

            $penilai1_key = $penilai1_nama ? $this->SuperAdmin_model->getKeyByJabatanAndUnit($penilai1_nama, $kode_unit) : null;
            $penilai2_key = $penilai2_nama ? $this->SuperAdmin_model->getKeyByJabatanAndUnit($penilai2_nama, $kode_unit) : null;

            $update = [
                'kode_cabang'      => $this->input->post('kode_cabang'),
                'kode_unit'        => $kode_unit,
                'jabatan'          => $this->input->post('jabatan'),
                'jenis_penilaian'  => $this->input->post('jenis_penilaian'),
                'unit_kerja'       => $this->input->post('unit_kerja'),
                'penilai1_jabatan' => $penilai1_key,
                'penilai2_jabatan' => $penilai2_key,
                // key tetap dari database, tidak diubah
            ];

            if ($this->SuperAdmin_model->saveMapping($update, $id)) {
                $this->session->set_flashdata('success', 'Data mapping berhasil diubah.');
            } else {
                $this->session->set_flashdata('error', 'Gagal mengubah data mapping.');
            }

            redirect('superadmin/kelolatingkatanjabatan_kpi');
        } else {
            show_error('Metode tidak diizinkan', 405);
        }
    }

    // Hapus data mapping
    public function hapusPenilaiMapping($id)
    {
        if ($this->SuperAdmin_model->deleteMapping($id)) {
            $this->session->set_flashdata('success', 'Data mapping berhasil dihapus.');
        } else {
            $this->session->set_flashdata('error', 'Gagal menghapus data mapping.');
        }

        redirect('superadmin/kelolatingkatanjabatan_kpi');
    }

    // ========== AJAX: ambil data cabang / unit / mapping ==========

    // Ambil kode_unit berdasarkan kode_cabang
    public function getKodeUnit($kode_cabang)
    {
        $units = $this->SuperAdmin_model->getKodeUnitByCabang($kode_cabang);
        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($units));
    }

    // Ambil mapping jabatan berdasarkan kode_unit
    public function getMappingJabatan($kode_unit)
    {
        $list = $this->SuperAdmin_model->getMappingByKodeUnit($kode_unit);
        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($list));
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
