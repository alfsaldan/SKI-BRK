<?php
defined('BASEPATH') or exit('No direct script access allowed');
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xls;
use PhpOffice\PhpSpreadsheet\IOFactory;


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

    // ==============================
// Halaman Penilaian Kinerja
// ==============================
    public function penilaiankinerja()
    {
        $data['judul'] = "Penilaian Kinerja Pegawai";
        $data['pegawai'] = $this->db->get('pegawai')->result();
        $data['indikator'] = $this->db->get('indikator')->result();
        $data['penilaian'] = $this->Penilaian_model->get_all_penilaian();

        $this->load->view("layout/header");
        $this->load->view("superadmin/penilaiankinerja", $data);
        $this->load->view("layout/footer");
    }

    // ==============================
// Cari Penilaian Pegawai
// ==============================
    public function cariPenilaian()
    {
        $nik = $this->input->post('nik');
        $pegawai = $this->db->get_where('pegawai', ['nik' => $nik])->row();

        if ($pegawai) {
            $indikator = $this->Penilaian_model->get_indikator_by_jabatan($pegawai->jabatan, $nik);

            $data['pegawai_detail'] = $pegawai;
            $data['indikator_by_jabatan'] = $indikator;

            $this->session->set_flashdata('message', [
                'type' => 'success',
                'text' => 'Data penilaian pegawai ditemukan!'
            ]);
        } else {
            $data['pegawai_detail'] = null;
            $data['indikator_by_jabatan'] = [];

            $this->session->set_flashdata('message', [
                'type' => 'error',
                'text' => 'Pegawai dengan NIK tersebut tidak ditemukan.'
            ]);
        }

        $this->load->view("layout/header");
        $this->load->view("superadmin/penilaiankinerja", $data);
        $this->load->view("layout/footer");
    }

    // ==============================
// Simpan seluruh form penilaian
// ==============================
    public function simpanPenilaian()
    {
        $nik = $this->input->post('nik');
        $targets = $this->input->post('target');
        $batas_waktu = $this->input->post('batas_waktu');
        $realisasi = $this->input->post('realisasi');

        $success = true;

        if ($targets) {
            foreach ($targets as $indikator_id => $t) {
                $btw = $batas_waktu[$indikator_id] ?? null;
                $rls = $realisasi[$indikator_id] ?? null;
                if (!$this->Penilaian_model->save_penilaian($nik, $indikator_id, $t, $btw, $rls)) {
                    $success = false;
                }
            }
        }

        if ($success) {
            $this->session->set_flashdata('message', [
                'type' => 'success',
                'text' => 'Seluruh penilaian berhasil disimpan!'
            ]);
        } else {
            $this->session->set_flashdata('message', [
                'type' => 'error',
                'text' => 'Gagal menyimpan sebagian data penilaian.'
            ]);
        }

        redirect('SuperAdmin/penilaiankinerja');
    }

    // ==============================
// Simpan penilaian per baris (AJAX)
// ==============================
    public function simpanPenilaianBaris()
    {
        $nik = $this->input->post('nik');
        $indikator_id = $this->input->post('indikator_id');
        $target = $this->input->post('target');
        $batas_waktu = $this->input->post('batas_waktu');
        $realisasi = $this->input->post('realisasi');

        $save = $this->Penilaian_model->save_penilaian($nik, $indikator_id, $target, $batas_waktu, $realisasi);

        if (!$save) {
            $error = $this->db->error();
            echo json_encode([
                'status' => 'error',
                'message' => 'Gagal menyimpan data.',
                'debug' => $error
            ]);
        } else {
            echo json_encode([
                'status' => 'success',
                'message' => 'Penilaian berhasil disimpan!',
                'data' => [
                    'target' => $target,
                    'batas_waktu' => $batas_waktu,
                    'realisasi' => $realisasi
                ]
            ]);
        }
    }


    // Halaman Data Pegawai
    public function dataPegawai()
    {
        $data['judul'] = "Data Pegawai";
        $data['pegawai_detail'] = null;
        $data['penilaian_pegawai'] = [];

        $this->load->view("layout/header");
        $this->load->view('superadmin/datapegawai', $data);
        $this->load->view("layout/footer");
    }

    // Cari Data Pegawai berdasarkan NIK
    public function cariDataPegawai()
    {
        $nik = $this->input->post('nik');
        $this->load->model('DataPegawai_model');

        $pegawai = $this->DataPegawai_model->getPegawaiByNik($nik);
        $penilaian = $this->DataPegawai_model->getPenilaianByNik($nik);

        if (!$pegawai) {
            $this->session->set_flashdata('error', 'Data pegawai dengan NIK ' . $nik . ' tidak ditemukan.');
            redirect('SuperAdmin/dataPegawai');
        } else {
            $this->session->set_flashdata('success', 'Data pegawai berhasil ditemukan.');
        }

        $data['judul'] = "Data Pegawai";
        $data['pegawai_detail'] = $pegawai;
        $data['penilaian_pegawai'] = $penilaian;

        $this->load->view("layout/header");
        $this->load->view('superadmin/datapegawai', $data);
        $this->load->view("layout/footer");
    }

    // Download Data Penilaian ke Excel
    public function downloadDataPegawai($nik)
    {
        $this->load->model('DataPegawai_model');
        $pegawai = $this->DataPegawai_model->getPegawaiByNik($nik);
        $penilaian = $this->DataPegawai_model->getPenilaianByNik($nik);

        if (!$pegawai) {
            $this->session->set_flashdata('error', 'Data pegawai tidak ditemukan.');
            redirect('SuperAdmin/dataPegawai');
        }

        try {
            // Path template
            $templatePath = FCPATH . "uploads/templatedatapenilaian.xls";
            if (!file_exists($templatePath)) {
                $this->session->set_flashdata('error', 'Template Excel tidak ditemukan.');
                redirect('SuperAdmin/dataPegawai');
            }

            // Load template excel
            $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($templatePath);
            $sheet = $spreadsheet->getActiveSheet();

            // Isi data pegawai
            $sheet->setCellValue('B2', $pegawai->nik);
            $sheet->setCellValue('B3', $pegawai->nama);
            $sheet->setCellValue('B4', $pegawai->jabatan);

            // Isi data penilaian mulai row ke-7
            $row = 7;
            foreach ($penilaian as $p) {
                $sheet->setCellValue("A{$row}", $p->perspektif);
                $sheet->setCellValue("B{$row}", $p->sasaran_kerja);
                $sheet->setCellValue("C{$row}", $p->indikator);
                $sheet->setCellValue("D{$row}", $p->bobot);
                $sheet->setCellValue("E{$row}", $p->target);
                $sheet->setCellValue("F{$row}", $p->batas_waktu);
                $sheet->setCellValue("G{$row}", $p->realisasi);
                $row++;
            }

            // Download file
            $filename = "Data_Penilaian_{$pegawai->nama}_{$pegawai->nik}.xls";
            header('Content-Type: application/vnd.ms-excel');
            header("Content-Disposition: attachment;filename=\"{$filename}\"");
            header('Cache-Control: max-age=0');

            $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xls');
            $writer->save('php://output');
            exit;
        } catch (\Exception $e) {
            $this->session->set_flashdata('error', 'Gagal mengunduh data: ' . $e->getMessage());
            redirect('SuperAdmin/dataPegawai');
        }
    }

}
