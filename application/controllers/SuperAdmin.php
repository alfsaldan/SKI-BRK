<?php
defined('BASEPATH') or exit('No direct script access allowed');

class SuperAdmin extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Indikator_model');
        $this->load->model('Penilaian_model');
        $this->load->library('session');

        // Proteksi akses hanya untuk role superadmin
        if (!$this->session->userdata('logged_in') || $this->session->userdata('role') !== 'superadmin') {
            redirect('auth');
        }
    }

    public function index()
    {
        $data['judul'] = "Halaman Dashboard Super Admin";
        $this->load->view("layout/header");
        $this->load->view("superadmin/index", $data);
        $this->load->view("layout/footer");
    }

    // Halaman indikator kinerja
    public function indikatorKinerja()
    {
        $data['judul'] = "Indikator Kinerja";
        $data['perspektif'] = [
            'Keuangan (F)',
            'Pelanggan (C)',
            'Proses Internal (IP)',
            'Pembelajaran & Pertumbuhan (LG)'
        ];
        $data['sasaran_kerja'] = $this->Indikator_model->getSasaranKerja();
        $data['indikator'] = $this->Indikator_model->getGroupedIndikator();

        $this->load->view("layout/header");
        $this->load->view('superadmin/indikatorKinerja', $data);
        $this->load->view("layout/footer");
    }

    public function addSasaranKerja()
    {
        $perspektif = $this->input->post('perspektif');
        $sasaran_kerja = $this->input->post('sasaran_kerja');
        $jabatan = $this->input->post('jabatan');

        $inserted = $this->Indikator_model->insertSasaranKerja($jabatan, $perspektif, $sasaran_kerja);

        if ($inserted) {
            $this->session->set_flashdata('message', ['type' => 'success', 'text' => 'Sasaran Kerja berhasil ditambahkan!']);
        } else {
            $this->session->set_flashdata('message', ['type' => 'error', 'text' => 'Gagal menambahkan Sasaran Kerja!']);
        }

        redirect('SuperAdmin/indikatorKinerja');
    }

    public function addIndikator()
    {
        $sasaran_id = $this->input->post('sasaran_id');
        $indikator = $this->input->post('indikator');
        $bobot = $this->input->post('bobot');

        $success = true;
        for ($i = 0; $i < count($indikator); $i++) {
            if (!$this->Indikator_model->insertIndikator($sasaran_id, $indikator[$i], $bobot[$i])) {
                $success = false;
                break;
            }
        }

        if ($success) {
            $this->session->set_flashdata('message', ['type' => 'success', 'text' => 'Indikator berhasil ditambahkan!']);
        } else {
            $this->session->set_flashdata('message', ['type' => 'error', 'text' => 'Gagal menambahkan indikator!']);
        }

        redirect('SuperAdmin/indikatorKinerja');
    }

    public function deleteIndikator($id)
    {
        if ($this->Indikator_model->deleteIndikator($id)) {
            $this->session->set_flashdata('message', ['type' => 'success', 'text' => 'Indikator berhasil dihapus!']);
        } else {
            $this->session->set_flashdata('message', ['type' => 'error', 'text' => 'Gagal menghapus indikator!']);
        }
        redirect('SuperAdmin/indikatorKinerja');
    }


    public function editIndikator($id)
    {
        $indikator = $this->input->post('indikator');
        $bobot = $this->input->post('bobot');

        $this->Indikator_model->updateIndikator($id, $indikator, $bobot);
        redirect('SuperAdmin/indikatorKinerja');
    }

    // Update indikator / sasaran dengan fetch()
    public function updateIndikator()
    {
        $data = json_decode(file_get_contents("php://input"), true);
        $id = $data['id'];

        if (isset($data['sasaran_kerja'])) {
            // Update sasaran kerja
            $sasaranKerja = $data['sasaran_kerja'];
            $success = $this->Indikator_model->updateSasaranKerja($id, $sasaranKerja);
        } else {
            // Update indikator
            $indikator = $data['indikator'];
            $bobot = $data['bobot'];
            $success = $this->Indikator_model->updateIndikator($id, $indikator, $bobot);
        }

        echo json_encode(['success' => $success]);
    }

    public function updateSasaran()
    {
        $data = json_decode(file_get_contents("php://input"), true);
        $id = $data['id'];
        $sasaran = $data['sasaran'];

        $success = $this->Indikator_model->updateSasaranKerja($id, $sasaran);

        echo json_encode(['success' => $success]);
    }


    // Halaman Penilaian Kinerja
    public function penilaiankinerja()
    {
        $data['pegawai']   = $this->db->get('pegawai')->result();
        $data['indikator'] = $this->db->get('indikator')->result();
        $data['penilaian'] = $this->Penilaian_model->get_all_penilaian();

        $this->load->view("layout/header");
        $this->load->view("superadmin/penilaiankinerja", $data);
        $this->load->view("layout/footer");
    }

    // Add Penilaian
    public function addPenilaian()
    {
        $nik         = $this->input->post('nik');
        $indikator_id = $this->input->post('indikator_id');
        $target      = $this->input->post('target');
        $batas_waktu = $this->input->post('batas_waktu');
        $realisasi   = $this->input->post('realisasi');

        $this->Penilaian_model->insertPenilaian($nik, $indikator_id, $target, $batas_waktu, $realisasi);

        redirect('SuperAdmin/penilaian');
    }

    public function cariPenilaian()
    {
        $nik = $this->input->post('nik');
        $pegawai = $this->db->get_where('pegawai', ['nik' => $nik])->row();

        if ($pegawai) {
            // Ambil indikator berdasarkan jabatan
            $this->load->model('Penilaian_model');
            $indikator = $this->Penilaian_model->get_indikator_by_jabatan($pegawai->jabatan);

            $data['pegawai_detail'] = $pegawai;
            $data['indikator_by_jabatan'] = $indikator;
        } else {
            $data['pegawai_detail'] = null;
            $data['indikator_by_jabatan'] = [];
        }

        $this->load->view("layout/header");
        $this->load->view("superadmin/penilaiankinerja", $data);
        $this->load->view("layout/footer");
    }

    // Simpan hasil penilaian
    public function simpanPenilaian()
    {
        $nik         = $this->input->post('nik');
        $targets     = $this->input->post('target');
        $batas_waktu = $this->input->post('batas_waktu');
        $realisasi   = $this->input->post('realisasi');

        $data = [];
        if ($targets && $realisasi) {
            foreach ($targets as $indikator_id => $t) {
                $data[] = [
                    'nik'         => $nik,
                    'indikator_id' => $indikator_id,
                    'target'      => $t,
                    'batas_waktu' => $batas_waktu[$indikator_id],
                    'realisasi'   => $realisasi[$indikator_id],
                ];
            }

            // Simpan sekaligus (lebih efisien)
            $this->Penilaian_model->simpan_penilaian($data);
        }

        $this->session->set_flashdata('success', 'Penilaian berhasil disimpan.');
        redirect('SuperAdmin/penilaiankinerja');
    }
}
