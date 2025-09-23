<?php
defined('BASEPATH') or exit('No direct script access allowed');


// Load PhpSpreadsheet
require_once FCPATH . 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet; //ini error
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;


/**
 * @property Indikator_model $Indikator_model
 * @property Penilaian_model $Penilaian_model
 * @property DataPegawai_model $DataPegawai_model
 * @property RiwayatJabatan_model $RiwayatJabatan_model
 * @property PenilaiMapping_model $PenilaiMapping_model
 * @property DataDiri_model $DataDiri_model
 * @property CI_Input $input
 * @property CI_Session $session
 * @property CI_DB_query_builder $db
 */



class SuperAdmin extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Indikator_model');
        $this->load->model('Penilaian_model');
        $this->load->model('RiwayatJabatan_model');
        $this->load->model('DataDiri_model');
        $this->load->model('PenilaiMapping_model');
        $this->load->library('session');

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
        $data['unit_kerja'] = $this->Indikator_model->getUnitKerja();

        $unit_kerja_filter = $this->input->get('unit_kerja');
        $jabatan_filter = $this->input->get('jabatan');

        if ($unit_kerja_filter && $jabatan_filter) {
            $data['indikator'] = $this->Indikator_model->getGroupedIndikator($unit_kerja_filter, $jabatan_filter);
            $data['unit_kerja_terpilih'] = $unit_kerja_filter;
            $data['jabatan_terpilih'] = $jabatan_filter;
        } else {
            $data['indikator'] = [];
            $data['unit_kerja_terpilih'] = null;
            $data['jabatan_terpilih'] = null;
        }

        $this->load->view("layout/header");
        $this->load->view('superadmin/indikatorKinerja', $data);
        $this->load->view("layout/footer");
    }

    public function getJabatanByUnit()
    {
        $unit_kerja = $this->input->get('unit_kerja');
        $this->db->select('jabatan');
        $this->db->distinct();
        $this->db->where('unit_kerja', $unit_kerja);
        $jabatan = $this->db->get('penilai_mapping')->result();
        echo json_encode($jabatan);
    }

    public function addSasaranKerja()
    {
        $perspektif = $this->input->post('perspektif');
        $sasaran_kerja = $this->input->post('sasaran_kerja');
        $jabatan = $this->input->post('jabatan');
        $unit_kerja = $this->input->post('unit_kerja');

        $inserted = $this->Indikator_model->insertSasaranKerja($jabatan, $unit_kerja, $perspektif, $sasaran_kerja);

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

        $success = $this->Indikator_model->updateIndikator($id, $indikator, $bobot);

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

        $success = $this->Indikator_model->updateSasaranKerja($id, $sasaran);

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
        $sasaran_kerja = $this->input->post('sasaran_kerja');
        $jabatan = $this->input->post('jabatan');
        $unit_kerja = $this->input->post('unit_kerja');

        $inserted = $this->Indikator_model->insertSasaranKerja($jabatan, $unit_kerja, $perspektif, $sasaran_kerja);

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
            if (!$this->Indikator_model->insertIndikator($sasaran_id, $indikator[$i], $bobot[$i])) {
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
        if ($this->Indikator_model->deleteIndikator($id)) {
            echo json_encode(['success' => true, 'message' => 'Indikator berhasil dihapus!']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Gagal menghapus indikator.']);
        }
    }


    public function penilaiankinerja()
    {
        $data['judul'] = "Penilaian Kinerja Pegawai";
        $data['pegawai'] = $this->db->get('pegawai')->result();
        $data['indikator'] = $this->db->get('indikator')->result();

        // Default periode (1 tahun penuh)
        $data['periode_awal'] = date('Y') . "-01-01";
        $data['periode_akhir'] = date('Y') . "-12-31";

        $data['penilaian'] = $this->Penilaian_model->get_all_penilaian();
        $data['pegawai_detail'] = null;
        $data['indikator_by_jabatan'] = [];

        $this->load->view("layout/header");
        $this->load->view("superadmin/penilaiankinerja", $data);
        $this->load->view("layout/footer");
    }


    public function cariPenilaian()
    {
        $nik = $this->input->post('nik') ?: $this->input->get('nik');

        $this->load->model('Penilaian_model');

        // 🔹 ambil daftar periode unik dari penilaian
        $data['periode_list'] = $this->Penilaian_model->getPeriodeList();

        // Ambil periode dari GET/POST, default tahun berjalan
        $periode_awal  = $this->input->get('awal') ?? $this->input->post('periode_awal') ?? date('Y-01-01');
        $periode_akhir = $this->input->get('akhir') ?? $this->input->post('periode_akhir') ?? date('Y-12-31');

        // pakai model yg sudah ada agar langsung dapat info penilai1 & penilai2
        $pegawai = $this->Penilaian_model->getPegawaiWithPenilai($nik);

        if ($pegawai) {
            $indikator = $this->Penilaian_model->get_indikator_by_jabatan_dan_unit(
                $pegawai->jabatan,
                $pegawai->unit_kerja,
                $pegawai->unit_kantor,
                $nik,
                $periode_awal,
                $periode_akhir
            );

            $data['pegawai_detail'] = $pegawai;
            $data['indikator_by_jabatan'] = $indikator;
            $data['message'] = [
                'type' => 'success',
                'text' => 'Data penilaian pegawai ditemukan!'
            ];
        } else {
            $data['pegawai_detail'] = null;
            $data['indikator_by_jabatan'] = [];
            $data['message'] = [
                'type' => 'error',
                'text' => 'Pegawai dengan NIK tersebut tidak ditemukan.'
            ];
        }

        $data['judul'] = "Penilaian Kinerja Pegawai";
        $data['periode_awal'] = $periode_awal;
        $data['periode_akhir'] = $periode_akhir;

        $this->load->view("layout/header");
        $this->load->view("superadmin/penilaiankinerja", $data);
        $this->load->view("layout/footer");
    }


    public function simpanPenilaian()
    {
        $nik = $this->input->post('nik');
        $targets = $this->input->post('target');
        $batas_waktu = $this->input->post('batas_waktu');
        $realisasi = $this->input->post('realisasi');

        // Ambil periode dari form, kalau kosong pakai default tahun ini
        $periode_awal = $this->input->post('periode_awal') ?? date('Y-01-01');
        $periode_akhir = $this->input->post('periode_akhir') ?? date('Y-12-31');

        $success = true;

        if ($targets) {
            foreach ($targets as $indikator_id => $t) {
                $btw = $batas_waktu[$indikator_id] ?? null;
                $rls = $realisasi[$indikator_id] ?? null;

                if (!$this->Penilaian_model->save_penilaian($nik, $indikator_id, $t, $btw, $rls, $periode_awal, $periode_akhir)) {
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

    public function simpanPenilaianBaris()
    {
        $nik = $this->input->post('nik');
        $indikator_id = $this->input->post('indikator_id');
        $target = $this->input->post('target');
        $batas_waktu = $this->input->post('batas_waktu');
        $realisasi = $this->input->post('realisasi');

        // Ambil periode dari POST
        $periode_awal = $this->input->post('periode_awal') ?? date('Y-01-01');
        $periode_akhir = $this->input->post('periode_akhir') ?? date('Y-12-31');

        $save = $this->Penilaian_model->save_penilaian($nik, $indikator_id, $target, $batas_waktu, $realisasi, $periode_awal, $periode_akhir);

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
                    'realisasi' => $realisasi,
                    'periode_awal' => $periode_awal,
                    'periode_akhir' => $periode_akhir
                ]
            ]);
        }
    }


    // ==============================
    // Kelola Data Pegawai
    // ==============================
    public function kelolaDataPegawai()
    {
        $this->load->model('DataPegawai_model');
        $data['judul'] = "Kelola Data Pegawai";
        $data['pegawai'] = $this->DataPegawai_model->getAllPegawai();

        $this->load->view("layout/header");
        $this->load->view("superadmin/keloladatapegawai", $data);
        $this->load->view("layout/footer");
    }



    // Download template Excel

    public function downloadTemplatePegawai()
    {
        $this->load->helper('download'); // Load helper disini
        $path = FCPATH . "uploads/template_pegawai.xlsx";
        if (file_exists($path)) {
            force_download($path, NULL);
        } else {
            $this->session->set_flashdata('error', 'Template tidak ditemukan.');
            redirect('SuperAdmin/kelolaDataPegawai');
        }
    }


    public function importPegawai()
    {
        $this->load->model('DataPegawai_model');

        if (!isset($_FILES['file_excel']['tmp_name']) || $_FILES['file_excel']['error'] !== UPLOAD_ERR_OK) {
            $this->session->set_flashdata('error', 'File tidak valid atau gagal diupload.');
            redirect('SuperAdmin/kelolaDataPegawai');
            return;
        }

        $fileTmp = $_FILES['file_excel']['tmp_name'];
        $fileName = $_FILES['file_excel']['name'];
        $ext = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

        if (!in_array($ext, ['xls', 'xlsx'])) {
            $this->session->set_flashdata('error', 'Format file salah. Hanya mendukung .xls atau .xlsx sesuai template.');
            redirect('SuperAdmin/kelolaDataPegawai');
            return;
        }

        $mime = mime_content_type($fileTmp);
        $allowedMimes = [
            'application/vnd.ms-excel',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'application/octet-stream'
        ];

        if (!in_array($mime, $allowedMimes)) {
            $this->session->set_flashdata('error', "File tidak valid. Pastikan menggunakan file Excel asli (MIME: $mime).");
            redirect('SuperAdmin/kelolaDataPegawai');
            return;
        }

        try {
            $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReaderForFile($fileTmp);
            $spreadsheet = $reader->load($fileTmp);
        } catch (\PhpOffice\PhpSpreadsheet\Reader\Exception $e) {
            $this->session->set_flashdata('error', 'File Excel tidak dapat dibaca: ' . $e->getMessage());
            redirect('SuperAdmin/kelolaDataPegawai');
            return;
        } catch (\Exception $e) {
            $this->session->set_flashdata('error', 'Terjadi error saat membuka file: ' . $e->getMessage());
            redirect('SuperAdmin/kelolaDataPegawai');
            return;
        }

        $sheetData = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);

        if (count($sheetData) <= 1) {
            $this->session->set_flashdata('error', 'File kosong atau tidak sesuai template.');
            redirect('SuperAdmin/kelolaDataPegawai');
            return;
        }

        // 🔹 Normalisasi header
        $normalize = function ($text) {
            return strtolower(str_replace(' ', '_', trim($text)));
        };

        $header = $sheetData[1];
        $colA = $normalize($header['A']); // NIK
        $colB = $normalize($header['B']); // Nama
        $colC = $normalize($header['C']); // Jabatan
        $colD = $normalize($header['D']); // Unit Kerja
        $colE = $normalize($header['E']); // Unit Kantor
        $colF = $normalize($header['F']); // Password

        // 🔹 Validasi header
        if (
            $colA !== 'nik' ||
            $colB !== 'nama' ||
            $colC !== 'jabatan' ||
            $colD !== 'unit_kerja' ||   // Unit Kerja dulu
            $colE !== 'unit_kantor' ||  // Unit Kantor setelahnya
            $colF !== 'password'
        ) {
            $this->session->set_flashdata(
                'error',
                'Header tidak sesuai. Gunakan template resmi (Nik, Nama, Jabatan, Unit Kerja, Unit Kantor, Password).'
            );
            redirect('SuperAdmin/kelolaDataPegawai');
            return;
        }

        $rows = [];
        $errors = [];

        foreach ($sheetData as $i => $row) {
            if ($i == 1)
                continue; // skip header

            $nik = trim($row['A']);
            $nama = trim($row['B']);
            $jabatan = trim($row['C']);
            $unit_kerja = trim($row['D']);
            $unit_kantor = trim($row['E']);
            $password_plain = trim($row['F']);

            if (empty($nik)) {
                $errors[] = "Baris $i: NIK kosong.";
                continue;
            }

            if ($this->db->get_where('pegawai', ['nik' => $nik])->row()) {
                $errors[] = "Baris $i: NIK $nik sudah ada.";
                continue;
            }

            if (empty($password_plain)) {
                $errors[] = "Baris $i: Password kosong.";
                continue;
            }

            $password_hashed = password_hash($password_plain, PASSWORD_DEFAULT);

            // 🔹 Siapkan data untuk batch insert
            $rows[] = [
                'nik' => $nik,
                'nama' => $nama,
                'jabatan' => $jabatan,
                'unit_kerja' => $unit_kerja,
                'unit_kantor' => $unit_kantor,
                'password' => $password_hashed,
            ];

            // 🔹 Tambahkan otomatis ke tabel users jika belum ada
            $cek_user = $this->db->get_where('users', ['nik' => $nik])->row();
            if (!$cek_user) {
                $data_user = [
                    'nik' => $nik,
                    'password' => $password_hashed,
                    'role' => 'pegawai',
                    'is_active' => 1
                ];
                $this->db->insert('users', $data_user);
            }
        }

        if (!empty($rows)) {
            $this->DataPegawai_model->insertBatch($rows);
            $this->session->set_flashdata('success', count($rows) . ' data berhasil diimport.');
        }

        if (!empty($errors)) {
            $this->session->set_flashdata('warning', implode("<br>", $errors));
        }

        if (empty($rows) && empty($errors)) {
            $this->session->set_flashdata('error', 'Tidak ada data valid yang bisa diimport.');
        }

        redirect('SuperAdmin/kelolaDataPegawai');
    }


    // Tambah Pegawai Manual
    public function tambahPegawai()
    {
        $this->load->model('DataPegawai_model');

        $nik = $this->input->post('nik');
        $nama = $this->input->post('nama');
        $jabatan = $this->input->post('jabatan');
        $unit_kerja = $this->input->post('unit_kerja');
        $unit_kantor = $this->input->post('unit_kantor'); // 🔹 Tambahan
        $password_plain = $this->input->post('password');
        $password_hashed = password_hash($password_plain, PASSWORD_DEFAULT);

        $data_pegawai = [
            'nik' => $nik,
            'nama' => $nama,
            'jabatan' => $jabatan,
            'unit_kerja' => $unit_kerja,
            'unit_kantor' => $unit_kantor, // 🔹 Tambahan
            'password' => $password_hashed,
        ];
        $this->DataPegawai_model->insertPegawai($data_pegawai);

        $this->DataPegawai_model->insertRiwayatAwal($nik, $jabatan, $unit_kerja, $unit_kantor); // 🔹 Tambahan

        $cek_user = $this->db->get_where('users', ['nik' => $nik])->row();
        if (!$cek_user) {
            $data_user = [
                'nik' => $nik,
                'password' => $password_hashed,
                'role' => 'pegawai',
                'is_active' => 1
            ];
            $this->db->insert('users', $data_user);
        }

        $this->session->set_flashdata('success', 'Pegawai berhasil ditambahkan.');
        redirect('SuperAdmin/kelolaDataPegawai');
    }




    // Hapus Pegawai
    public function deletePegawai($nik)
    {
        $this->load->model('DataPegawai_model');
        if ($this->DataPegawai_model->deletePegawai($nik)) {
            $this->session->set_flashdata('message', [
                'type' => 'success',
                'text' => 'Pegawai berhasil dihapus!'
            ]);
        } else {
            $this->session->set_flashdata('message', [
                'type' => 'error',
                'text' => 'Gagal menghapus pegawai.'
            ]);
        }

        redirect('SuperAdmin/kelolaDataPegawai');
    }

    // Detail Pegawai + Riwayat
    public function detailPegawai($nik)
    {
        $this->load->model('DataPegawai_model');
        $data['pegawai'] = $this->DataPegawai_model->getPegawaiByNik($nik);
        $data['riwayat'] = $this->DataPegawai_model->getRiwayatJabatan($nik);

        // ambil semua jabatan & unit kerja unik dari tabel riwayat_jabatan
        $data['jabatan_list'] = $this->DataPegawai_model->getAllJabatan();
        $data['unitkerja_list'] = $this->DataPegawai_model->getAllUnitKerja();
        // $data['unitkantor_list'] = $this->DataPegawai_model->getAllUnitKantor();

        $this->load->view("layout/header");
        $this->load->view("superadmin/detailpegawai", $data);
        $this->load->view("layout/footer");
    }



    // Tambah Jabatan Baru
    public function updateJabatan()
    {
        $this->load->model('DataPegawai_model');

        $nik = $this->input->post('nik');
        $jabatan = $this->input->post('jabatan');
        $unit_kerja = $this->input->post('unit_kerja');
        $unit_kantor = $this->input->post('unit_kantor');
        $tgl_mulai = $this->input->post('tgl_mulai');

        $this->DataPegawai_model->tambahRiwayatJabatan($nik, $jabatan, $unit_kerja, $unit_kantor, $tgl_mulai);

        $this->session->set_flashdata('success', 'Riwayat jabatan baru berhasil ditambahkan.');
        redirect('SuperAdmin/detailPegawai/' . $nik);
    }

    public function nonaktifPegawai($nik)
    {
        $this->db->where('nik', $nik)->update('pegawai', ['status' => 'nonaktif']);
        $this->session->set_flashdata('message', 'Pegawai berhasil dinonaktifkan.');
        redirect('SuperAdmin/detailPegawai/' . $nik);
    }

    public function aktifkanPegawai($nik)
    {
        $this->db->where('nik', $nik)->update('pegawai', ['status' => 'aktif']);
        $this->session->set_flashdata('message', 'Pegawai berhasil diaktifkan.');
        redirect('SuperAdmin/detailPegawai/' . $nik);
    }

    public function toggleStatusPegawai($nik, $action)
    {
        if (!in_array($action, ['aktif', 'nonaktif'])) {
            echo json_encode(['status' => 'error', 'message' => 'Aksi tidak valid']);
            return;
        }

        $status = $action;

        // Load model
        $this->load->model('RiwayatJabatan_model');
        $this->RiwayatJabatan_model->updateStatusPegawai($nik, $status);

        // Kirim respons JSON
        echo json_encode(['status' => 'success', 'message' => 'Pegawai berhasil ' . $status]);
    }



    // Halaman Cek Data Pegawai
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
        $nik   = $this->input->get('nik') ?? $this->input->post('nik');
        $awal  = $this->input->get('awal') ?? $this->input->post('periode_awal');
        $akhir = $this->input->get('akhir') ?? $this->input->post('periode_akhir');

        // default periode jika kosong
        if (!$awal || !$akhir) {
            $tahun = date('Y');
            $awal  = $tahun . '-01-01';
            $akhir = $tahun . '-12-31';
        }

        $this->load->model('DataPegawai_model');

        $pegawai   = $this->DataPegawai_model->getPegawaiWithPenilai($nik);
        $penilaian = $this->DataPegawai_model->getPenilaianByNik($nik, $awal, $akhir);

        // 🔹 ambil semua periode unik dari tabel penilaian
        $periode_list = $this->DataPegawai_model->getAvailablePeriode();

        $data['judul'] = "Data Pegawai";
        $data['pegawai_detail']    = $pegawai;
        $data['penilaian_pegawai'] = $penilaian;
        $data['periode_awal'] = $awal;
        $data['periode_akhir'] = $akhir;
        $data['periode_list']  = $periode_list;

        $this->load->view("layout/header");
        $this->load->view('superadmin/datapegawai', $data);
        $this->load->view("layout/footer");
    }


    public function downloadDataPegawai()
    {
        $nik = $this->input->get('nik');
        $periode_awal  = $this->input->get('awal') ?? date('Y-01-01');
        $periode_akhir = $this->input->get('akhir') ?? date('Y-12-31');

        $this->load->model('DataPegawai_model');

        // Ambil data pegawai beserta penilai
        $pegawai = $this->DataPegawai_model->getPegawaiWithPenilai($nik);
        $penilaian = $this->DataPegawai_model->getPenilaianByNik($nik, $periode_awal, $periode_akhir);

        if (!$pegawai) {
            $this->session->set_flashdata('error', 'Data pegawai tidak ditemukan.');
            redirect('SuperAdmin/dataPegawai');
        }

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // =======================
        // LOGO
        // =======================
        $drawing = new Drawing();
        $drawing->setName('Logo BRK Syariah');
        $drawing->setDescription('Logo BRK Syariah');
        $drawing->setPath(FCPATH . 'assets/images/Logo_BRK_Syariah.png');
        $drawing->setCoordinates('C1');
        $drawing->setHeight(60);
        $drawing->setWorksheet($sheet);

        // =======================
        // HEADER UTAMA
        // =======================
        $sheet->setCellValue('A1', 'Sasaran Kinerja Individu (SKI)');
        $sheet->mergeCells('A1:B1');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal('center');

        $sheet->setCellValue('A2', 'Periode: ' . date('d M Y', strtotime($periode_awal)) . ' s/d ' . date('d M Y', strtotime($periode_akhir)));
        $sheet->mergeCells('A2:B2');
        $sheet->getStyle('A2')->getAlignment()->setHorizontal('center');

        // =======================
        // DATA PEGAWAI
        // =======================
        $row = 4;
        $sheet->setCellValue("A{$row}", "DATA PEGAWAI");
        $sheet->mergeCells("A{$row}:D{$row}");
        $sheet->getStyle("A{$row}")->getFont()->setBold(true);
        $sheet->getStyle("A{$row}")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('2E7D32');
        $sheet->getStyle("A{$row}")->getFont()->getColor()->setRGB('FFFFFF');

        $row++;
        $sheet->setCellValue("A{$row}", "NIK");
        $sheet->setCellValue("B{$row}", $pegawai->nik);
        $sheet->setCellValue("C{$row}", "Periode Penilaian : ");
        $sheet->setCellValue("D{$row}", date('d M Y', strtotime($periode_awal)) . " s/d " . date('d M Y', strtotime($periode_akhir)));
        $row++;
        $sheet->setCellValue("A{$row}", "Nama Pegawai");
        $sheet->setCellValue("B{$row}", $pegawai->nama);
        $sheet->setCellValue("C{$row}", "Unit Kantor Penilai : ");
        $sheet->setCellValue("D{$row}", $pegawai->unit_kerja);
        $row++;
        $sheet->setCellValue("A{$row}", "Jabatan");
        $sheet->setCellValue("B{$row}", $pegawai->jabatan);
        $row++;
        $sheet->setCellValue("A{$row}", "Unit Kantor");
        $sheet->setCellValue("B{$row}", ($pegawai->unit_kerja ?? '-') . ' ' . ($pegawai->unit_kantor ?? '-'));

        $row += 2;

        // =======================
        // PENILAI I & II
        // =======================
        $sheet->setCellValue("A{$row}", "Penilai I");
        $sheet->mergeCells("A{$row}:B{$row}");
        $sheet->getStyle("A{$row}")->getFont()->setBold(true);
        $row++;
        $sheet->setCellValue("A{$row}", "NIK");
        $sheet->setCellValue("B{$row}", $pegawai->penilai1_nik ?? '-');
        $row++;
        $sheet->setCellValue("A{$row}", "Nama");
        $sheet->setCellValue("B{$row}", $pegawai->penilai1_nama ?? '-');
        $row++;
        $sheet->setCellValue("A{$row}", "Jabatan");
        $sheet->setCellValue("B{$row}", $pegawai->penilai1_jabatan ?? '-');

        $row += 2;
        $sheet->setCellValue("A{$row}", "Penilai II");
        $sheet->mergeCells("A{$row}:B{$row}");
        $sheet->getStyle("A{$row}")->getFont()->setBold(true);
        $row++;
        $sheet->setCellValue("A{$row}", "NIK");
        $sheet->setCellValue("B{$row}", $pegawai->penilai2_nik ?? '-');
        $row++;
        $sheet->setCellValue("A{$row}", "Nama");
        $sheet->setCellValue("B{$row}", $pegawai->penilai2_nama ?? '-');
        $row++;
        $sheet->setCellValue("A{$row}", "Jabatan");
        $sheet->setCellValue("B{$row}", $pegawai->penilai2_jabatan ?? '-');

        $row += 2;

        // =======================
        // SKALA NILAI
        // =======================
        $sheet->setCellValue("A{$row}", "Skala Nilai Sasaran Kerja");
        $sheet->mergeCells("A{$row}:F{$row}");
        $sheet->getStyle("A{$row}")->getFont()->setBold(true)->getColor()->setRGB('FFFFFF');
        $sheet->getStyle("A{$row}")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('2E7D32');
        $row++;

        $headers = ["Realisasi (%)", "< 80%", "80% sd < 90%", "90% sd < 110%", "110% sd < 120%", "120% sd 130%"];
        $col = 'A';
        foreach ($headers as $h) {
            $sheet->setCellValue("{$col}{$row}", $h);
            $col++;
        }
        $sheet->getStyle("A{$row}:F{$row}")->applyFromArray([
            'font' => ['bold' => true],
            'alignment' => ['horizontal' => 'center'],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '2E7D32']]
        ]);

        // Skala nilai detail
        $skalaDetail = [
            ["Kondisi", "Tidak memperlihatkan kinerja yang sesuai / diharapkan", "Perlu perbaikan untuk membantu meningkatkan kinerja", "Menunjukkan kinerja yang baik", "Menunjukkan kinerja yang sangat baik", "Menunjukkan kinerja yang luar biasa / istimewa"],
            ["Yudisium/Predikat", "Minus", "Fair", "Good", "Very Good", "Excellent"],
            ["Nilai", "<2.00", "2.00 - <3.00", "3.00 - <3.50", "3.50 - <4.50", "4.50 - 5.00"]
        ];

        foreach ($skalaDetail as $det) {
            $row++;
            $col = 'A';
            foreach ($det as $cell) {
                $sheet->setCellValue("{$col}{$row}", $cell);
                $col++;
            }
        }

        $row += 2;

        // =======================
        // HEADER HASIL PENILAIAN
        // =======================
        $sheet->setCellValue("A{$row}", "Perspektif");
        $sheet->setCellValue("B{$row}", "Sasaran Kerja");
        $sheet->setCellValue("C{$row}", "Indikator");
        $sheet->setCellValue("D{$row}", "Bobot (%)");
        $sheet->setCellValue("E{$row}", "Target");
        $sheet->setCellValue("F{$row}", "Batas Waktu");
        $sheet->setCellValue("G{$row}", "Realisasi");
        $sheet->setCellValue("H{$row}", "Pencapaian (%)");
        $sheet->setCellValue("I{$row}", "Nilai");
        $sheet->setCellValue("J{$row}", "Nilai Dibobot");

        // Header selain E & G
        $ranges = ["A{$row}:D{$row}", "F{$row}", "H{$row}:J{$row}"];
        foreach ($ranges as $range) {
            $sheet->getStyle($range)->applyFromArray([
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'alignment' => ['horizontal' => 'center', 'vertical' => 'center'],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'color' => ['rgb' => '2E7D32']],
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
            ]);
        }

        // Header Target & Realisasi
        $sheet->getStyle("E{$row}")->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'alignment' => ['horizontal' => 'center', 'vertical' => 'center'],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'color' => ['rgb' => 'FFA500']],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
        ]);
        $sheet->getStyle("G{$row}")->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'alignment' => ['horizontal' => 'center', 'vertical' => 'center'],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'color' => ['rgb' => 'FFA500']],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
        ]);

        $row++;


        // =======================
        // ISI DATA PENILAIAN
        // =======================
        $perspektifGroup = [];
        foreach ($penilaian as $p) {
            $perspektif = trim($p->perspektif);
            $sasaran = trim($p->sasaran_kerja);
            $perspektifGroup[$perspektif][$sasaran][] = $p;
        }

        foreach ($perspektifGroup as $perspektif => $sasaranArr) {
            $perspStartRow = $row;
            foreach ($sasaranArr as $sasaran => $items) {
                $sasaranStartRow = $row;
                foreach ($items as $i) {
                    $sheet->setCellValue("C{$row}", $i->indikator);
                    $sheet->setCellValue("D{$row}", $i->bobot);
                    $sheet->setCellValue("E{$row}", $i->target);
                    $sheet->setCellValue("F{$row}", $i->batas_waktu);
                    $sheet->setCellValue("G{$row}", $i->realisasi);
                    $sheet->setCellValue("H{$row}", $i->pencapaian ?? '-');
                    $sheet->setCellValue("I{$row}", $i->nilai ?? '-');
                    $sheet->setCellValue("J{$row}", $i->nilai_dibobot ?? '-');
                    $row++;
                }
                if ($row - $sasaranStartRow > 1) {
                    $sheet->mergeCells("B{$sasaranStartRow}:B" . ($row - 1));
                    $sheet->setCellValue("B{$sasaranStartRow}", $sasaran);
                } else {
                    $sheet->setCellValue("B{$sasaranStartRow}", $sasaran);
                }
            }
            if ($row - $perspStartRow > 1) {
                $sheet->mergeCells("A{$perspStartRow}:A" . ($row - 1));
                $sheet->setCellValue("A{$perspStartRow}", $perspektif);
            } else {
                $sheet->setCellValue("A{$perspStartRow}", $perspektif);
            }

            // Subtotal
            $sheet->setCellValue("A{$row}", "Sub Total {$perspektif}");
            $sheet->mergeCells("A{$row}:I{$row}");
            $sheet->setCellValue("J{$row}", "=SUM(J{$perspStartRow}:J" . ($row - 1) . ")");
            $sheet->getStyle("A{$row}:J{$row}")->applyFromArray([
                'font' => ['bold' => true],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'color' => ['rgb' => 'F1F8E9']]
            ]);
            $row++;
        }

        // =======================
        // TOTAL AKHIR
        // =======================
        $sheet->setCellValue("A{$row}", "TOTAL");
        $sheet->mergeCells("A{$row}:I{$row}");
        $sheet->setCellValue("J{$row}", "=SUM(J" . ($row - 1) . ")");
        $sheet->getStyle("A{$row}:J{$row}")->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'color' => ['rgb' => '2E7D32']]
        ]);

        // =======================
        // DOWNLOAD FILE
        // =======================
        $filename = "Data_Penilaian_{$pegawai->nama}_{$pegawai->nik}.xlsx";
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header("Content-Disposition: attachment;filename=\"{$filename}\"");
        header('Cache-Control: max-age=0');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }


    // Halaman Data Diri
    public function datadiri()
    {
        $this->load->model('DataDiri_model');

        $nik = $this->session->userdata('nik');

        if (!$nik) {
            $user_id = $this->session->userdata('id');
            if ($user_id) {
                $user = $this->db->get_where('users', ['id' => $user_id])->row_array();
                $nik = $user ? $user['nik'] : null;
            }
        }

        if (!$nik) {
            redirect('auth/login');
        }

        $data['pegawai'] = $this->DataDiri_model->getDataByNik($nik);

        if ($this->input->post('update_password')) {
            $password = $this->input->post('password');
            $konfirmasi = $this->input->post('konfirmasi_password');
            if (!empty($password) && $password === $konfirmasi) {
                if ($this->DataDiri_model->updatePassword($nik, $password)) {
                    $this->session->set_flashdata('success', 'Password berhasil diperbarui!');
                } else {
                    $this->session->set_flashdata('error', 'Gagal memperbarui password!');
                }
            } else {
                $this->session->set_flashdata('error', 'Password tidak sama atau kosong!');
            }
            redirect('superadmin/datadiri');
        }

        $this->load->view('layout/header');
        $this->load->view('superadmin/datadiri', $data);
        $this->load->view('layout/footer');
    }

    // ========== Halaman Kelola Tingkatan Jabatan ==========
    public function kelolatingkatanjabatan()
    {
        $data['judul'] = 'Kelola Tingkatan Jabatan';
        $data['list'] = $this->PenilaiMapping_model->getAll();

        $this->load->view('layout/header', $data);
        $this->load->view('superadmin/kelolatingkatanjabatan', $data);
        $this->load->view('layout/footer');
    }

    // Tambah data
    public function tambahPenilaiMapping()
    {
        if ($this->input->post()) {
            $insert = [
                'jabatan' => $this->input->post('jabatan'),
                'unit_kerja' => $this->input->post('unit_kerja'),
                'penilai1_jabatan' => $this->input->post('penilai1_jabatan'),
                'penilai2_jabatan' => $this->input->post('penilai2_jabatan'),
            ];

            $this->PenilaiMapping_model->insert($insert);
            $this->session->set_flashdata('success', 'Data mapping berhasil ditambahkan.');
            redirect('Superadmin/kelolatingkatanjabatan');
        }
    }

    // Edit data
    public function editPenilaiMapping($id)
    {
        if ($this->input->post()) {
            $update = [
                'jabatan' => $this->input->post('jabatan'),
                'unit_kerja' => $this->input->post('unit_kerja'),
                'penilai1_jabatan' => $this->input->post('penilai1_jabatan'),
                'penilai2_jabatan' => $this->input->post('penilai2_jabatan'),
            ];

            $this->PenilaiMapping_model->update($id, $update);
            $this->session->set_flashdata('success', 'Data mapping berhasil diubah.');
            redirect('Superadmin/kelolatingkatanjabatan');
        }
    }

    // Hapus data
    public function hapusPenilaiMapping($id)
    {
        if ($this->PenilaiMapping_model->delete($id)) {
            $this->session->set_flashdata('success', 'Data berhasil dihapus');
        }
        redirect('superadmin/kelolatingkatanjabatan');
    }


    // Catatan Penilai
    public function getCatatanPenilai()
    {
        $nik = $this->input->post('nik_pegawai');

        $list = $this->Penilaian_model->getCatatanByPegawai($nik);

        $data = [];
        $no = $_POST['start'] ?? 0;

        foreach ($list as $row) {
            $no++;
            $data[] = [
                'no' => $no,
                'nama_penilai' => $row->penilai_nama, // ambil nama penilai dari join ke tabel pegawai
                'catatan' => $row->catatan,
                'tanggal' => $row->tanggal
            ];
        }

        echo json_encode([
            "draw" => intval($_POST['draw']),
            "recordsTotal" => count($list),
            "recordsFiltered" => count($list),
            "data" => $data
        ]);
    }
    // Catatan Pegawai
    public function getCatatanPegawai()
    {
        $nik = $this->input->post('nik_pegawai');

        $list = $this->Penilaian_model->getCatatanPegawai($nik);

        $data = [];
        $no = $_POST['start'] ?? 0;

        foreach ($list as $row) {
            $no++;
            $data[] = [
                'no' => $no,
                'catatan' => $row->catatan,
                'tanggal' => $row->tanggal
            ];
        }

        echo json_encode([
            "draw" => intval($_POST['draw']),
            "recordsTotal" => count($list),
            "recordsFiltered" => count($list),
            "data" => $data
        ]);
    }
}
