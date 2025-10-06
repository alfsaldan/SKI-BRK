<?php
defined('BASEPATH') or exit('No direct script access allowed');


// Load PhpSpreadsheet
require_once FCPATH . 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;


/**
 * @property KPI_Indikator_model $KPI_Indikator_model
 * @property KPI_Penilaian_model $KPI_Penilaian_model
 * @property Penilaian_model $Penilaian_model
 * @property DataPegawai_model $DataPegawai_model
 * @property RiwayatJabatan_model $RiwayatJabatan_model
 * @property PenilaiMapping_model $PenilaiMapping_model
 * @property DataDiri_model $DataDiri_model
 * @property Coaching_model $Coaching_model
 * @property CI_Input $input
 * @property CI_Session $session
 * @property CI_DB_query_builder $db
 */

class Administrator_Renstra extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('KPI_Indikator_model');
        $this->load->model('KPI_Penilaian_model');
        $this->load->model('DataPegawai_model');
        $this->load->model('PenilaiMapping_model');
        $this->load->library('session');

        if (!$this->session->userdata('logged_in') || $this->session->userdata('role') !== 'administrator') {
            redirect('auth');
        }
    }

    // =================== Halaman Kelola Indikator KPI ===================
    public function kpi_indikatorKinerja()
    {
        $data['judul'] = "Key Performance Indicator (KPI)";
        $data['perspektif'] = [
            'Keuangan (F)',
            'Pelanggan (C)',
            'Proses Internal (IP)',
            'Pembelajaran & Pertumbuhan (LG)'
        ];
        $data['sasaran_kpi'] = $this->KPI_Indikator_model->getSasaranKerja();
        $data['unit_kerja'] = $this->KPI_Indikator_model->getUnitKerja();

        $unit_kerja_filter = $this->input->get('unit_kerja');
        $jabatan_filter = $this->input->get('jabatan');

        if ($unit_kerja_filter && $jabatan_filter) {
            $data['kpi_indikator'] = $this->KPI_Indikator_model->getGroupedIndikator($unit_kerja_filter, $jabatan_filter);
            $data['unit_kerja_terpilih'] = $unit_kerja_filter;
            $data['jabatan_terpilih'] = $jabatan_filter;
        } else {
            $data['kpi_indikator'] = [];
            $data['unit_kerja_terpilih'] = null;
            $data['jabatan_terpilih'] = null;
        }

        $this->load->view("layout/header");
        $this->load->view('administrator/kpi_indikatorKinerja', $data);
        $this->load->view("layout/footer");
    }

    public function getJabatanByUnit()
    {
        $unit_kerja = $this->input->get('unit_kerja');
        $this->db->select('jabatan');
        $this->db->distinct();
        $this->db->where('unit_kerja', $unit_kerja);
        $this->db->where('jenis_penilaian', 'kpi'); // ✅ filter hanya KPI
        $jabatan = $this->db->get('penilai_mapping')->result();
        echo json_encode($jabatan);
    }


    public function addSasaranKerja()
    {
        $perspektif = $this->input->post('perspektif');
        $sasaran_kpi = $this->input->post('sasaran_kpi');
        $jabatan = $this->input->post('jabatan');
        $unit_kerja = $this->input->post('unit_kerja');

        $inserted = $this->KPI_Indikator_model->insertSasaranKerja($jabatan, $unit_kerja, $perspektif, $sasaran_kpi);

        if ($inserted) {
            $this->session->set_flashdata('message', ['type' => 'success', 'text' => 'Sasaran Kerja berhasil ditambahkan!']);
        } else {
            $this->session->set_flashdata('message', ['type' => 'error', 'text' => 'Gagal menambahkan Sasaran Kerja!']);
        }

        redirect('Administrator_Renstra/kpi_indikatorKinerja');
    }

    public function addIndikator()
    {
        $sasaran_id = $this->input->post('sasaran_id');
        $indikator = $this->input->post('indikator');
        $bobot = $this->input->post('bobot');

        $success = true;
        for ($i = 0; $i < count($indikator); $i++) {
            if (!$this->KPI_Indikator_model->insertIndikator($sasaran_id, $indikator[$i], $bobot[$i])) {
                $success = false;
                break;
            }
        }

        if ($success) {
            $this->session->set_flashdata('message', ['type' => 'success', 'text' => 'Indikator berhasil ditambahkan!']);
        } else {
            $this->session->set_flashdata('message', ['type' => 'error', 'text' => 'Gagal menambahkan indikator!']);
        }

        redirect('Administrator_Renstra/kpi_indikatorKinerja');
    }

    public function deleteIndikator($id)
    {
        if ($this->KPI_Indikator_model->deleteIndikator($id)) {
            $this->session->set_flashdata('message', ['type' => 'success', 'text' => 'Indikator berhasil dihapus!']);
        } else {
            $this->session->set_flashdata('message', ['type' => 'error', 'text' => 'Gagal menghapus indikator!']);
        }
        redirect('Administrator_Renstra/kpi_indikatorKinerja');
    }


    public function editIndikator($id)
    {
        $indikator = $this->input->post('indikator');
        $bobot = $this->input->post('bobot');

        $this->KPI_Indikator_model->updateIndikator($id, $indikator, $bobot);
        redirect('Administrator_Renstra/kpi_indikatorKinerja');
    }

    public function updateIndikator()
    {
        $id = $this->input->post('id');
        $indikator = $this->input->post('indikator');
        $bobot = $this->input->post('bobot');

        if (!$id || !$indikator || !$bobot) {
            echo json_encode([
                'success' => false,
                'message' => 'Data tidak lengkap.'
            ]);
            return;
        }

        $success = $this->KPI_Indikator_model->updateIndikator($id, $indikator, $bobot);

        if ($success) {
            echo json_encode([
                'success' => true,
                'message' => 'Indikator berhasil diupdate.'
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Gagal mengupdate indikator.'
            ]);
        }
    }

    public function updateSasaran()
    {
        $id = $this->input->post('id');
        $sasaran = $this->input->post('sasaran');

        if (!$id || !$sasaran) {
            echo json_encode([
                'success' => false,
                'message' => 'Data tidak lengkap.'
            ]);
            return;
        }

        $success = $this->KPI_Indikator_model->updateSasaranKerja($id, $sasaran);

        if ($success) {
            echo json_encode([
                'success' => true,
                'message' => 'Sasaran berhasil diupdate.'
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Gagal mengupdate sasaran.'
            ]);
        }
    }

    public function saveSasaranAjax()
    {
        $perspektif = $this->input->post('perspektif');
        $sasaran_kpi = $this->input->post('sasaran_kpi');
        $jabatan = $this->input->post('jabatan');
        $unit_kerja = $this->input->post('unit_kerja');

        $inserted = $this->KPI_Indikator_model->insertSasaranKerja($jabatan, $unit_kerja, $perspektif, $sasaran_kpi);

        if ($inserted) {
            echo json_encode(['success' => true, 'message' => 'Sasaran Kerja berhasil ditambahkan!']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Gagal menambahkan Sasaran Kerja!']);
        }
    }

    public function saveIndikatorAjax()
    {
        $sasaran_id = $this->input->post('sasaran_id');
        $indikator = $this->input->post('indikator');
        $bobot = $this->input->post('bobot');

        $success = true;
        for ($i = 0; $i < count($indikator); $i++) {
            if (!$this->KPI_Indikator_model->insertIndikator($sasaran_id, $indikator[$i], $bobot[$i])) {
                $success = false;
                break;
            }
        }

        if ($success) {
            echo json_encode(['success' => true, 'message' => 'Indikator berhasil ditambahkan!']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Gagal menambahkan indikator!']);
        }
    }

    public function deleteIndikatorAjax()
    {
        $id = $this->input->post('id');
        if ($this->KPI_Indikator_model->deleteIndikator($id)) {
            echo json_encode(['success' => true, 'message' => 'Indikator berhasil dihapus!']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Gagal menghapus indikator.']);
        }
    }

    // ==========================
    // 📊 HALAMAN UTAMA PENILAIAN KPI
    // ==========================
    public function kpi_penilaianKinerja()
    {
        $data['unit_kerja'] = $this->KPI_Penilaian_model->getUnitKerjaKPI();
        $data['judul'] = "Penilaian KPI";

        $this->load->view("layout/header");
        $this->load->view("administrator/kpi_penilaianKinerja", $data);
        $this->load->view("layout/footer");
    }

    // ==========================
    // 🔍 CARI PEGAWAI BERDASARKAN UNIT & JABATAN
    // ==========================
    public function cariPenilaian()
    {
        $unit_kerja = $this->input->post('unit_kerja');
        $jabatan = $this->input->post('jabatan');

        // Ambil data pegawai berdasarkan filter
        $data['pegawai_list'] = $this->KPI_Penilaian_model->getPegawaiByUnit($unit_kerja, $jabatan);
        $data['unit_kerja'] = $this->KPI_Penilaian_model->getUnitKerjaKPI();
        $data['judul'] = "Penilaian KPI";

        $this->load->view("layout/header");
        $this->load->view("administrator/kpi_penilaianKinerja", $data);
        $this->load->view("layout/footer");
    }

    // ==========================
    // 📋 API UNTUK AJAX PEGAWAI BERDASARKAN UNIT (Dropdown)
    // ==========================
    public function getPegawaiByUnit()
    {
        $unit_kerja = $this->input->get('unit_kerja');

        $this->db->select('p.nik, p.nama, p.jabatan, p.unit_kerja, p.unit_kantor');
        $this->db->from('pegawai p');
        $this->db->join('penilai_mapping pm', 'p.jabatan = pm.jabatan', 'inner');
        $this->db->where('p.unit_kerja', $unit_kerja);
        $this->db->where('p.status', 'aktif');
        $this->db->where('pm.jenis_penilaian', 'kpi'); // ✅ hanya KPI
        $this->db->order_by('p.jabatan', 'ASC');

        $pegawai = $this->db->get()->result();

        echo json_encode($pegawai);
    }

    // ==========================
    // 👁️ LIHAT PENILAIAN (DETAIL PER PEGAWAI)
    // ==========================
    public function lihatPenilaianRenstra()
    {
        $nik = $this->input->post('nik') ?: $this->input->get('nik');

        // 🔹 Default periode: tahun berjalan
        $periode_awal  = $this->input->get('awal') ?? date('Y-01-01');
        $periode_akhir = $this->input->get('akhir') ?? date('Y-12-31');

        // 🔹 Load model
        $this->load->model('KPI_Penilaian_model');

        // 🔹 Ambil data pegawai
        $pegawai = $this->KPI_Penilaian_model->getPegawaiWithPenilai($nik);

        if ($pegawai) {

            // 🔹 Ambil indikator KPI berdasarkan jabatan & unit kerja
            // Tetap tampil walau belum ada nilai di kpi_penilaian
            $indikator = $this->db->select("
                    ks.id AS id_sasaran,
                    ks.perspektif,
                    ks.sasaran_kpi,
                    ki.id AS id_indikator,
                    ki.indikator AS nama_indikator,
                    ki.bobot,
                    kn.target,
                    kn.batas_waktu,
                    kn.realisasi,
                    kn.pencapaian,
                    kn.nilai,
                    kn.nilai_dibobot,
                    kn.status
                ")
                ->from('kpi_sasaran ks')
                ->join('kpi_indikator ki', 'ki.sasaran_id = ks.id', 'left')
                ->join('kpi_penilaian kn', "kn.indikator_id = ki.id AND kn.nik = '$nik' AND kn.periode_awal = '$periode_awal' AND kn.periode_akhir = '$periode_akhir'", 'left')
                ->where('ks.jabatan', $pegawai->jabatan)
                ->where('ks.unit_kerja', $pegawai->unit_kerja)
                ->order_by('ks.perspektif', 'ASC')
                ->order_by('ks.sasaran_kpi', 'ASC')
                ->order_by('ki.indikator', 'ASC')
                ->get()
                ->result();


            // 🔹 Ambil nilai akhir KPI dari tabel nilai_akhir
            $nilai_akhir = $this->db->get_where('nilai_akhir', [
                'nik' => $nik,
                'jenis_penilaian' => 'KPI',
                'periode_awal' => $periode_awal,
                'periode_akhir' => $periode_akhir
            ])->row();

            $data['pegawai_detail'] = $pegawai;
            $data['indikator_by_jabatan'] = $indikator;
            $data['nilai_akhir'] = $nilai_akhir;
            $data['message'] = [
                'type' => 'success',
                'text' => 'Data penilaian KPI pegawai berhasil dimuat.'
            ];
        } else {
            $data['pegawai_detail'] = null;
            $data['indikator_by_jabatan'] = [];
            $data['nilai_akhir'] = null;
            $data['message'] = [
                'type' => 'error',
                'text' => 'Pegawai tidak ditemukan.'
            ];
        }

        $data['judul'] = "Detail Penilaian KPI Pegawai";
        $data['periode_awal'] = $periode_awal;
        $data['periode_akhir'] = $periode_akhir;

        // 🔹 Load view KPI
        $this->load->view("layout/header");
        $this->load->view("administrator/kpi_penilaiankinerja", $data);
        $this->load->view("layout/footer");
    }
}
