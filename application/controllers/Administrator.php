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
 * @property Indikator_model $Indikator_model
 * @property Penilaian_model $Penilaian_model
 * @property DataPegawai_model $DataPegawai_model
 * @property RiwayatJabatan_model $RiwayatJabatan_model
 * @property PenilaiMapping_model $PenilaiMapping_model
 * @property DataDiri_model $DataDiri_model
 * @property Coaching_model $Coaching_model
 * @property CI_Form_validation $form_validation
 * @property Budaya_model $Budaya_model
 * @property CI_Input $input
 * @property CI_Session $session
 * @property CI_DB_query_builder $db
 */



class Administrator extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Indikator_model');
        $this->load->model('Penilaian_model');
        $this->load->model('RiwayatJabatan_model');
        $this->load->model('DataDiri_model');
        $this->load->model('DataPegawai_model');
        $this->load->model('PenilaiMapping_model');
        $this->load->library('session');

        if (!$this->session->userdata('logged_in') || $this->session->userdata('role') !== 'administrator') {
            redirect('auth');
        }
    }

    public function index()
    {
        $data['judul'] = "Halaman Dashboard Administrator";
        $this->load->view("layout/header");
        $this->load->view("administrator/index", $data);
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
        $this->load->view('administrator/indikatorKinerja', $data);
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

        redirect('Administrator/indikatorKinerja');
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

        redirect('Administrator/indikatorKinerja');
    }

    public function deleteIndikator($id)
    {
        if ($this->Indikator_model->deleteIndikator($id)) {
            $this->session->set_flashdata('message', ['type' => 'success', 'text' => 'Indikator berhasil dihapus!']);
        } else {
            $this->session->set_flashdata('message', ['type' => 'error', 'text' => 'Gagal menghapus indikator!']);
        }
        redirect('Administrator/indikatorKinerja');
    }


    public function editIndikator($id)
    {
        $indikator = $this->input->post('indikator');
        $bobot = $this->input->post('bobot');

        $this->Indikator_model->updateIndikator($id, $indikator, $bobot);
        redirect('Administrator/indikatorKinerja');
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

    // ========== Halaman Penilaian Kinerja ==========
    public function penilaiankinerja()
    {
        $this->load->model('Penilaian_model');

        // 🔹 Ambil periode dari query (GET) atau pakai default tahun berjalan
        $periode_awal  = $this->input->get('awal') ?: date('Y') . "-01-01";
        $periode_akhir = $this->input->get('akhir') ?: date('Y') . "-12-31";

        // 🔹 Ambil status lock dari DB
        $lock_status = $this->Penilaian_model->getLockStatus($periode_awal, $periode_akhir);

        $data = [
            'judul'         => "Penilaian Kinerja Pegawai",
            'periode_list'  => $this->Penilaian_model->getPeriodeList(),
            'periode_awal'  => $periode_awal,
            'periode_akhir' => $periode_akhir,
            'is_locked'     => $lock_status ?? false, // jika null, false
            'pegawai'       => $this->db->get('pegawai')->result(),
            'indikator'     => $this->db->get('indikator')->result(),
            'penilaian'     => $this->Penilaian_model->get_all_penilaian(),
            'budaya'        => $this->Penilaian_model->getAllBudaya(),
        ];

        $this->load->view("layout/header");
        $this->load->view("administrator/penilaiankinerja", $data);
        $this->load->view("layout/footer");
    }


    public function getLockStatus()
    {
        $awal = $this->input->get('awal');
        $akhir = $this->input->get('akhir');

        $this->load->model('Penilaian_model');
        $locked = $this->Penilaian_model->getLockStatus($awal, $akhir);

        echo json_encode(['locked' => (bool)$locked]);
    }

    public function setLockStatus()
    {
        $awal = $this->input->post('periode_awal');
        $akhir = $this->input->post('periode_akhir');
        $lock = $this->input->post('lock_input');

        $this->load->model('Penilaian_model');
        $updated = $this->Penilaian_model->setLockStatus($awal, $akhir, $lock);

        echo json_encode([
            'status' => $updated ? 'success' : 'error'
        ]);
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
                $nik,
                $periode_awal,
                $periode_akhir
            );

            // 🔹 ambil nilai_akhir dari tabel nilai_akhir
            $nilai_akhir = $this->Penilaian_model->getNilaiAkhir($nik, $periode_awal, $periode_akhir);

            // 🔹 ambil nilai budaya dari tabel budaya_nilai
            $budayaData = $this->Penilaian_model->getBudayaNilaiByNik($nik, $periode_awal, $periode_akhir);

            // pisahkan nilai_budaya & rata-rata
            $budaya_nilai = $budayaData['nilai_budaya'] ?? [];
            $rata_rata_budaya = $budayaData['rata_rata'] ?? 0;

            $data['pegawai_detail']       = $pegawai;
            $data['indikator_by_jabatan'] = $indikator;
            $data['nilai_akhir']          = $nilai_akhir;
            $data['budaya_nilai']         = $budaya_nilai;
            $data['rata_rata_budaya']     = $rata_rata_budaya;
            $data['budaya']               = $this->Penilaian_model->getAllBudaya();

            $data['message'] = [
                'type' => 'success',
                'text' => 'Data penilaian pegawai ditemukan!'
            ];
        } else {
            $data['pegawai_detail']       = null;
            $data['indikator_by_jabatan'] = [];
            $data['nilai_akhir']          = null;
            $data['budaya_nilai']         = [];
            $data['rata_rata_budaya']     = 0;
            $data['message'] = [
                'type' => 'error',
                'text' => 'Pegawai dengan NIK tersebut tidak ditemukan.'
            ];
        }

        $data['judul']        = "Penilaian Kinerja Pegawai";
        $data['periode_awal'] = $periode_awal;
        $data['periode_akhir'] = $periode_akhir;

        $this->load->view("layout/header");
        $this->load->view("administrator/penilaiankinerja", $data);
        $this->load->view("layout/footer");
    }

    public function simpanPenilaian()
    {
        $nik = $this->input->post('nik');
        $targets = $this->input->post('target');
        $batas_waktu = $this->input->post('batas_waktu');
        $realisasi = $this->input->post('realisasi');
        $pencapaian = $this->input->post('pencapaian');
        $nilai = $this->input->post('nilai');
        $nilaidibobot = $this->input->post('nilai_dibobot');

        // Ambil periode dari form, kalau kosong pakai default tahun ini
        $periode_awal = $this->input->post('periode_awal') ?? date('Y-01-01');
        $periode_akhir = $this->input->post('periode_akhir') ?? date('Y-12-31');

        $success = true;

        if ($targets) {
            foreach ($targets as $indikator_id => $t) {
                $btw = $batas_waktu[$indikator_id] ?? null;
                $rls = $realisasi[$indikator_id] ?? null;
                $pnc = $pencapaian[$indikator_id] ?? null;
                $nli = $nilai[$indikator_id] ?? null;
                $nld = $nilaidibobot[$indikator_id] ?? null;

                if (!$this->Penilaian_model->save_penilaian($nik, $indikator_id, $t, $btw, $rls, $pnc, $nli, $nld, $periode_awal, $periode_akhir)) {
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

        redirect('Administrator/penilaiankinerja');
    }

    public function simpanPenilaianBaris()
    {
        $nik = $this->input->post('nik');
        $indikator_id = $this->input->post('indikator_id');
        $target = $this->input->post('target');
        $batas_waktu = $this->input->post('batas_waktu');
        $realisasi = $this->input->post('realisasi');
        $pencapaian = $this->input->post('pencapaian');
        $nilai = $this->input->post('nilai');
        $nilaidibobot = $this->input->post('nilai_dibobot');

        // Ambil periode dari POST
        $periode_awal = $this->input->post('periode_awal') ?? date('Y-01-01');
        $periode_akhir = $this->input->post('periode_akhir') ?? date('Y-12-31');

        $save = $this->Penilaian_model->save_penilaian($nik, $indikator_id, $target, $batas_waktu, $realisasi, $pencapaian, $nilai, $nilaidibobot, $periode_awal, $periode_akhir);

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
                    'pencapaian' => $pencapaian,
                    'nilai' => $nilai,
                    'nilai_dibobot' => $nilaidibobot,
                    'periode_awal' => $periode_awal,
                    'periode_akhir' => $periode_akhir
                ]
            ]);
        }
    }

    public function simpanNilaiAkhir()
    {
        $nik           = $this->input->post('nik');
        $nilai_sasaran = $this->input->post('nilai_sasaran');
        $nilai_budaya  = $this->input->post('nilai_budaya');
        $total_nilai   = $this->input->post('total_nilai');
        $fraud         = $this->input->post('fraud');
        $nilai_akhir   = $this->input->post('nilai_akhir');
        $pencapaian    = $this->input->post('pencapaian');
        $predikat      = $this->input->post('predikat');
        $periode_awal  = $this->input->post('periode_awal');
        $periode_akhir = $this->input->post('periode_akhir');
        $koefisien     = $this->input->post('koefisien'); // ✅ tambahan baru

        $save = $this->Penilaian_model->save_nilai_akhir(
            $nik,
            $nilai_sasaran,
            $nilai_budaya,
            $total_nilai,
            $fraud,
            $nilai_akhir,
            $pencapaian,
            $predikat,
            $periode_awal,
            $periode_akhir,
            $koefisien // ✅ ikut dikirim ke model
        );

        if ($save) {
            echo json_encode([
                'status' => 'success',
                'message' => 'Nilai Akhir berhasil disimpan!'
            ]);
        } else {
            $error = $this->db->error();
            echo json_encode([
                'status' => 'error',
                'message' => 'Gagal menyimpan Nilai Akhir',
                'debug'   => $error
            ]);
        }
    }

    // ========== Halaman Kelola Data Pegawai ==========
    public function kelolaDataPegawai()
    {
        $this->load->model('DataPegawai_model');
        $data['judul'] = "Kelola Data Pegawai";
        $data['pegawai'] = $this->DataPegawai_model->getAllPegawai();

        $this->load->view("layout/header");
        $this->load->view("administrator/keloladatapegawai", $data);
        $this->load->view("layout/footer");
    }

    // Download template Excel Pegawai
    public function downloadTemplatePegawai()
    {
        $this->load->helper('download'); // Load helper disini
        $path = FCPATH . "uploads/template_pegawai.xlsx";
        if (file_exists($path)) {
            force_download($path, NULL);
        } else {
            $this->session->set_flashdata('error', 'Template tidak ditemukan.');
            redirect('Administrator/kelolaDataPegawai');
        }
    }


    public function importPegawai()
    {
        $this->load->model('DataPegawai_model');

        if (!isset($_FILES['file_excel']['tmp_name']) || $_FILES['file_excel']['error'] !== UPLOAD_ERR_OK) {
            $this->session->set_flashdata('error', 'File tidak valid atau gagal diupload.');
            redirect('Administrator/kelolaDataPegawai');
            return;
        }

        $fileTmp = $_FILES['file_excel']['tmp_name'];
        $fileName = $_FILES['file_excel']['name'];
        $ext = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

        if (!in_array($ext, ['xls', 'xlsx'])) {
            $this->session->set_flashdata('error', 'Format file salah. Hanya mendukung .xls atau .xlsx sesuai template.');
            redirect('Administrator/kelolaDataPegawai');
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
            redirect('Administrator/kelolaDataPegawai');
            return;
        }

        try {
            $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReaderForFile($fileTmp);
            $spreadsheet = $reader->load($fileTmp);
        } catch (\PhpOffice\PhpSpreadsheet\Reader\Exception $e) {
            $this->session->set_flashdata('error', 'File Excel tidak dapat dibaca: ' . $e->getMessage());
            redirect('Administrator/kelolaDataPegawai');
            return;
        } catch (\Exception $e) {
            $this->session->set_flashdata('error', 'Terjadi error saat membuka file: ' . $e->getMessage());
            redirect('Administrator/kelolaDataPegawai');
            return;
        }

        $sheetData = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);

        if (count($sheetData) <= 1) {
            $this->session->set_flashdata('error', 'File kosong atau tidak sesuai template.');
            redirect('Administrator/kelolaDataPegawai');
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
            redirect('Administrator/kelolaDataPegawai');
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

        redirect('Administrator/kelolaDataPegawai');
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
        redirect('Administrator/kelolaDataPegawai');
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

        redirect('Administrator/kelolaDataPegawai');
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
        $this->load->view("administrator/detailpegawai", $data);
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
        redirect('Administrator/detailPegawai/' . $nik);
    }

    public function nonaktifPegawai($nik)
    {
        $this->db->where('nik', $nik)->update('pegawai', ['status' => 'nonaktif']);
        $this->session->set_flashdata('message', 'Pegawai berhasil dinonaktifkan.');
        redirect('Administrator/detailPegawai/' . $nik);
    }

    public function aktifkanPegawai($nik)
    {
        $this->db->where('nik', $nik)->update('pegawai', ['status' => 'aktif']);
        $this->session->set_flashdata('message', 'Pegawai berhasil diaktifkan.');
        redirect('Administrator/detailPegawai/' . $nik);
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
        $this->load->view('administrator/datapegawai', $data);
        $this->load->view("layout/footer");
    }

    public function cariDataPegawai()
    {
        // Ambil input NIK dan periode
        $nik   = $this->input->get('nik') ?? $this->input->post('nik');
        $awal  = $this->input->get('awal') ?? $this->input->post('periode_awal');
        $akhir = $this->input->get('akhir') ?? $this->input->post('periode_akhir');

        // Default periode jika kosong
        if (!$awal || !$akhir) {
            $tahun = date('Y');
            $awal  = $tahun . '-01-01';
            $akhir = $tahun . '-12-31';
        }

        // Load model
        $this->load->model('DataPegawai_model');
        $this->load->model('Penilaian_model');

        // Ambil data pegawai
        $pegawai   = $this->DataPegawai_model->getPegawaiWithPenilai($nik);

        // Ambil penilaian detail
        $penilaian = $this->DataPegawai_model->getPenilaianByNik($nik, $awal, $akhir);

        // 🔹 Ambil nilai akhir langsung dari tabel nilai_akhir
        $nilaiAkhir = $this->DataPegawai_model->getNilaiAkhirByNikPeriode($nik, $awal, $akhir);

        // Ambil periode unik dari tabel penilaian
        $periode_list = $this->DataPegawai_model->getAvailablePeriode();

        // Ambil chat coaching (jika ada)
        $chat = [];
        if ($pegawai) {
            $chat = $this->DataPegawai_model->getCoachingChat($nik);
        }

        // Ambil nilai budaya
        $budayaData = $this->Penilaian_model->getBudayaNilaiByNik($nik, $awal, $akhir);
        $budaya_nilai = $budayaData['nilai_budaya'] ?? [];
        $rata_rata_budaya = $budayaData['rata_rata'] ?? 0;

        // Siapkan data untuk view
        $data = [
            'judul'               => "Data Pegawai",
            'pegawai_detail'      => $pegawai,
            'penilaian_pegawai'   => $penilaian,
            'nilai_akhir'         => $nilaiAkhir,   // 🔹 nilai akhir langsung dari DB
            'periode_awal'        => $awal,
            'periode_akhir'       => $akhir,
            'periode_list'        => $periode_list,
            'chat'                => $chat,
            'budaya_nilai'        => $budaya_nilai,
            'rata_rata_budaya'    => $rata_rata_budaya,
            'budaya'              => $this->Penilaian_model->getAllBudaya(),
        ];

        // Load view
        $this->load->view("layout/header");
        $this->load->view('administrator/datapegawai', $data);
        $this->load->view("layout/footer");
    }

    public function downloadDataPegawai()
    {
        $nik = $this->input->get('nik');
        $periode_awal  = $this->input->get('awal') ?? date('Y-01-01');
        $periode_akhir = $this->input->get('akhir') ?? date('Y-12-31');

        $this->load->model('DataPegawai_model');
        $this->load->model('pegawai/Coaching_model');

        // Ambil data pegawai beserta penilai
        $pegawai = $this->DataPegawai_model->getPegawaiWithPenilai($nik);
        $penilaian = $this->DataPegawai_model->getPenilaianByNik($nik, $periode_awal, $periode_akhir);

        if (!$pegawai) {
            $this->session->set_flashdata('error', 'Data pegawai tidak ditemukan.');
            redirect('Administrator/dataPegawai');
        }

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // =======================
        // LOGO
        // =======================
        $drawing = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
        $drawing->setName('Logo BRK Syariah');
        $drawing->setDescription('Logo BRK Syariah');
        $drawing->setPath(FCPATH . 'assets/images/Logo_BRK_Syariah.png');
        $drawing->setCoordinates('F1');
        $drawing->setHeight(40);
        $drawing->setWorksheet($sheet);

        // =======================
        // HEADER UTAMA
        // =======================
        $sheet->setCellValue('B1', 'Sasaran Kinerja Individu (SKI)');
        $sheet->mergeCells('B1:C1');
        $sheet->getStyle('B1')->getFont()->setBold(true)->setSize(16);
        $sheet->getStyle('B1')->getAlignment()->setHorizontal('left');

        $sheet->setCellValue('B2', 'Periode: ' . date('d M Y', strtotime($periode_awal)) . ' s/d ' . date('d M Y', strtotime($periode_akhir)));
        $sheet->mergeCells('B2:C2');
        $sheet->getStyle('B2')->getAlignment()->setHorizontal('left');
        // =======================
        // DATA PEGAWAI
        // =======================
        $row = 4;
        $sheet->setCellValue("B{$row}", "👤 DATA PEGAWAI");
        $sheet->mergeCells("B{$row}:G{$row}");
        $sheet->getStyle("B{$row}")->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF']
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT,
                'vertical'   => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
            ],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'color' => ['rgb' => '2E7D32'] // hijau elegan
            ]
        ]);

        // Isi data pegawai
        $row++;
        $sheet->setCellValue("B{$row}", "NIK");
        $sheet->setCellValue("C{$row}", ": " . ($pegawai->nik ?? '-'));
        $sheet->setCellValue("F{$row}", "Periode Penilaian");
        $sheet->setCellValue("G{$row}", ": " . date('d M Y', strtotime($periode_awal)) . " s/d " . date('d M Y', strtotime($periode_akhir)));

        $row++;
        $sheet->setCellValue("B{$row}", "Nama Pegawai");
        $sheet->setCellValue("C{$row}", ": " . ($pegawai->nama ?? '-'));
        $sheet->setCellValue("F{$row}", "Unit Kantor Penilai");
        $sheet->setCellValue("G{$row}", ": " . ($pegawai->unit_kerja ?? '-'));

        $row++;
        $sheet->setCellValue("B{$row}", "Jabatan");
        $sheet->setCellValue("C{$row}", ": " . ($pegawai->jabatan ?? '-'));

        $row++;
        $sheet->setCellValue("B{$row}", "Unit Kantor");
        $sheet->setCellValue("C{$row}", ": " . (($pegawai->unit_kerja ?? '-') . ' ' . ($pegawai->unit_kantor ?? '-')));

        // Alignment rata kiri isi data pegawai
        $sheet->getStyle("B5:G{$row}")
            ->getAlignment()
            ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT)
            ->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);

        $row += 2;

        // =======================
        // PENILAI I & II (2 Kolom)
        // =======================

        // Header Penilai I
        $sheet->setCellValue("B{$row}", "🧑‍💼 PENILAI I");
        $sheet->mergeCells("B{$row}:C{$row}");
        $sheet->getStyle("B{$row}")->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'color' => ['rgb' => '0288D1'] // biru toska
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical'   => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER
            ]
        ]);

        // Header Penilai II
        $sheet->setCellValue("E{$row}", "👨‍💼 PENILAI II");
        $sheet->mergeCells("E{$row}:G{$row}");
        $sheet->getStyle("E{$row}")->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'color' => ['rgb' => 'F57C00'] // oranye
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical'   => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER
            ]
        ]);

        $penilaiHeaderRow = $row;

        // Isi baris sejajar Penilai I & II
        $row++;
        $sheet->setCellValue("B{$row}", "NIK");
        $sheet->setCellValue("C{$row}", ": " . ($pegawai->penilai1_nik ?? '-'));
        $sheet->setCellValue("E{$row}", "NIK");
        $sheet->setCellValue("F{$row}", ": " . ($pegawai->penilai2_nik ?? '-'));

        $row++;
        $sheet->setCellValue("B{$row}", "Nama");
        $sheet->setCellValue("C{$row}", ": " . ($pegawai->penilai1_nama ?? '-'));
        $sheet->setCellValue("E{$row}", "Nama");
        $sheet->setCellValue("F{$row}", ": " . ($pegawai->penilai2_nama ?? '-'));

        $row++;
        $sheet->setCellValue("B{$row}", "Jabatan");
        $sheet->setCellValue("C{$row}", ": " . ($pegawai->penilai1_jabatan ?? '-'));
        $sheet->setCellValue("E{$row}", "Jabatan");
        $sheet->setCellValue("F{$row}", ": " . ($pegawai->penilai2_jabatan ?? '-'));

        // Pastikan alignment isi Penilai I & II benar-benar rata kiri
        $penilaiIsiStart = $penilaiHeaderRow + 1;
        $sheet->getStyle("B{$penilaiIsiStart}:C{$row}")
            ->getAlignment()
            ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT)
            ->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
        $sheet->getStyle("E{$penilaiIsiStart}:G{$row}")
            ->getAlignment()
            ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT)
            ->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);

        // =======================
        // BORDER BLOK DATA
        // =======================
        $blokAwal = 4;
        $blokAkhir = $row;
        $sheet->getStyle("B{$blokAwal}:G{$blokAkhir}")->applyFromArray([
            'borders' => [
                'outline' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THICK,
                    'color' => ['rgb' => '000000']
                ]
            ]
        ]);

        $row += 2;


        // =======================
        // SKALA NILAI
        // =======================
        $sheet->setCellValue("B{$row}", "Skala Nilai Sasaran Kerja");
        $sheet->mergeCells("B{$row}:G{$row}");
        $sheet->getStyle("B{$row}")->getFont()->setBold(true)->getColor()->setRGB('FFFFFF');
        $sheet->getStyle("B{$row}")->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setRGB('2E7D32');
        $row++;

        // Simpan baris awal tabel
        $skalaAwal = $row; // nanti akan dipakai untuk border

        $headers = ["Realisasi (%)", "< 80%", "80% sd < 90%", "90% sd < 110%", "110% sd < 120%", "120% sd 130%"];
        $col = 'B';
        foreach ($headers as $h) {
            $sheet->setCellValue("{$col}{$row}", $h);
            $col++;
        }
        $sheet->getStyle("B{$row}:G{$row}")->applyFromArray([
            'font' => ['bold' => true],
            'alignment' => ['horizontal' => 'center', 'vertical' => 'center'],
            'borders' => ['allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN]],
            'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => 'baff95']]
        ]);

        $skalaDetail = [
            ["Kondisi", "Tidak memperlihatkan kinerja yang sesuai / diharapkan", "Perlu perbaikan untuk membantu meningkatkan kinerja", "Menunjukkan kinerja yang baik", "Menunjukkan kinerja yang sangat baik", "Menunjukkan kinerja yang luar biasa / istimewa"],
            ["Yudisium/Predikat", "Minus", "Fair", "Good", "Very Good", "Excellent"],
            ["Nilai", "<2.00", "2.00 - <3.00", "3.00 - <3.50", "3.50 - <4.50", "4.50 - 5.00"]
        ];

        foreach ($skalaDetail as $det) {
            $row++;
            $col = 'B';
            foreach ($det as $i => $cell) {
                $sheet->setCellValue("{$col}{$row}", $cell);

                // Style border untuk semua cell
                $sheet->getStyle("{$col}{$row}")->applyFromArray([
                    'borders' => [
                        'allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN]
                    ],
                    'alignment' => [
                        'horizontal' => 'center',
                        'vertical' => 'center',
                        'wrapText' => true
                    ]
                ]);

                // Kalau kolom pertama (judul baris)
                if ($i == 0) {
                    $sheet->getStyle("{$col}{$row}")->getFill()
                        ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                        ->getStartColor()->setRGB('2E7D32');
                    $sheet->getStyle("{$col}{$row}")->getFont()->getColor()->setRGB('FFFFFF');
                    $sheet->getStyle("{$col}{$row}")->getFont()->setBold(true);
                }

                // Baris nilai → semua kolom hijau
                if ($det[0] == "Nilai" && $i > 0) {
                    $sheet->getStyle("{$col}{$row}")->getFill()
                        ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                        ->getStartColor()->setRGB('baff95');
                    $sheet->getStyle("{$col}{$row}")->getFont()->getColor()->setRGB('000000');
                }

                $col++;
            }
        }

        // Simpan baris akhir tabel
        $skalaAkhir = $row;

        // Tambahkan border tebal outline di luar blok tabel
        $sheet->getStyle("B" . ($skalaAwal - 1) . ":G{$skalaAkhir}")->applyFromArray([
            'borders' => [
                'outline' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THICK,
                    'color' => ['rgb' => '000000']
                ]
            ]
        ]);

        $row += 2;

        // ======================= 
        // HEADER HASIL PENILAIAN
        // =======================
        $sheet->setCellValue("B{$row}", "Perspektif");
        $sheet->setCellValue("C{$row}", "Sasaran Kerja");
        $sheet->setCellValue("D{$row}", "Indikator");
        $sheet->setCellValue("E{$row}", "Bobot (%)");
        $sheet->setCellValue("F{$row}", "Target");
        $sheet->setCellValue("G{$row}", "Batas Waktu");
        $sheet->setCellValue("H{$row}", "Realisasi");
        $sheet->setCellValue("I{$row}", "Pencapaian (%)");
        $sheet->setCellValue("J{$row}", "Nilai");
        $sheet->setCellValue("K{$row}", "Nilai Dibobot");

        // Gaya header utama
        $sheet->getStyle("B{$row}:K{$row}")->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 11],
            'alignment' => ['horizontal' => 'center', 'vertical' => 'center'],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb' => '2E7D32'] // hijau tua elegan
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['rgb' => '000000']
                ]
            ]
        ]);

        $tabelStartRow = $row;
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

        $subtotalRows = [];

        $warnaIsi1 = 'e5ffd7'; // perspektif 
        $warnaIsi2 = 'eeffe5'; // krem lembut
        $warnaPerspektif = 'a6de87'; // hijau pastel
        $warnaSasaran = 'baff95'; // hijau sangat muda

        foreach ($perspektifGroup as $perspektif => $sasaranArr) {
            $perspStartRow = $row;
            $noSasaran = 1;
            $bobotStartRow = $row;
            $bobotEndRow = $row - 1;

            foreach ($sasaranArr as $sasaran => $items) {
                $sasaranStartRow = $row;
                $noIndikator = 1;

                foreach ($items as $i) {
                    // Warna isi selang-seling
                    $fillColor = ($row % 2 == 0) ? $warnaIsi1 : $warnaIsi2;

                    $sheet->setCellValue("D{$row}", $noIndikator . ". " . $i->indikator);
                    $sheet->setCellValue("E{$row}", $i->bobot);
                    $sheet->setCellValue("F{$row}", $i->target);
                    $sheet->setCellValue("G{$row}", $i->batas_waktu);
                    $sheet->setCellValue("H{$row}", $i->realisasi);
                    $sheet->setCellValue("I{$row}", $i->pencapaian ?? '-');
                    $sheet->setCellValue("J{$row}", $i->nilai ?? '-');
                    $sheet->setCellValue("K{$row}", $i->nilai_dibobot ?? '-');

                    // Terapkan gaya isi baris
                    $sheet->getStyle("B{$row}:K{$row}")->applyFromArray([
                        'fill' => [
                            'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                            'startColor' => ['rgb' => $fillColor]
                        ],
                        'alignment' => [
                            'vertical' => 'center',
                            'horizontal' => 'center',
                            'wrapText' => true
                        ],
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                                'color' => ['rgb' => '000000']
                            ]
                        ]
                    ]);

                    $noIndikator++;
                    $bobotEndRow = $row;
                    $row++;
                }

                // Merge Sasaran
                if ($row - $sasaranStartRow > 1) {
                    $sheet->mergeCells("C{$sasaranStartRow}:C" . ($row - 1));
                }
                $sheet->setCellValue("C{$sasaranStartRow}", $noSasaran . ". " . $sasaran);
                $sheet->getStyle("C{$sasaranStartRow}")->applyFromArray([
                    'fill' => [
                        'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                        'startColor' => ['rgb' => $warnaSasaran]
                    ],
                    'alignment' => ['horizontal' => 'left', 'vertical' => 'center'],
                    'font' => ['bold' => true]
                ]);

                $noSasaran++;
            }

            // Merge Perspektif
            if ($row - $perspStartRow > 1) {
                $sheet->mergeCells("B{$perspStartRow}:B" . ($row - 1));
            }
            $sheet->setCellValue("B{$perspStartRow}", $perspektif);
            $sheet->getStyle("B{$perspStartRow}")->applyFromArray([
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => $warnaPerspektif]
                ],
                'alignment' => ['horizontal' => 'left', 'vertical' => 'center'],
                'font' => ['bold' => true]
            ]);

            // Subtotal
            $sheet->setCellValue("B{$row}", "Sub Total {$perspektif}");
            $sheet->mergeCells("B{$row}:D{$row}");
            // Subtotal Bobot (kolom E)
            $sheet->setCellValue("E{$row}", "=SUM(E{$bobotStartRow}:E{$bobotEndRow})");
            // Subtotal Nilai Dibobot (kolom K)
            $sheet->mergeCells("F{$row}:J{$row}");
            $sheet->setCellValue("K{$row}", "=SUM(K{$perspStartRow}:K" . ($row - 1) . ")");
            $sheet->getStyle("B{$row}:K{$row}")->applyFromArray([
                'font' => ['bold' => true],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '58a35c']
                ],
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                        'color' => ['rgb' => '000000']
                    ]
                ]
            ]);
            $subtotalRows[] = $row;
            $subtotalBobotRows[] = $row; // simpan juga baris subtotal bobot
            $row++;
        }

        // Total Akhir
        $formulaNilai = "=SUM(" . implode(",", array_map(function ($r) {
            return "K{$r}";
        }, $subtotalRows)) . ")";

        $formulaBobot = "=SUM(" . implode(",", array_map(function ($r) {
            return "E{$r}";
        }, $subtotalBobotRows)) . ")";

        $sheet->setCellValue("B{$row}", "TOTAL");
        $sheet->mergeCells("B{$row}:D{$row}");
        $sheet->setCellValue("E{$row}", $formulaBobot); // 🔹 total bobot
        $sheet->mergeCells("F{$row}:J{$row}");
        $sheet->setCellValue("K{$row}", $formulaNilai); // 🔹 total nilai dibobot
        $sheet->getStyle("B{$row}:K{$row}")->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 12],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb' => '2E7D32']
            ],
            'alignment' => ['horizontal' => 'center', 'vertical' => 'center'],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['rgb' => '000000']
                ]
            ]
        ]);

        $tabelEndRow = $row;

        // =======================
        // BORDER & LAYOUT
        // =======================
        $sheet->getStyle("B{$tabelStartRow}:K{$tabelEndRow}")->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['rgb' => '000000']
                ]
            ]
        ]);
        $sheet->getStyle("B{$tabelStartRow}:K{$tabelEndRow}")
            ->getBorders()->getOutline()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM)
            ->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('000000'));

        // ======================= 
        // SUMMARY NILAI AKHIR (q)
        // =======================
        $row += 2;

        // Ambil nilai akhir dari model
        $nilai = $this->DataPegawai_model->getNilaiAkhirByNikPeriode($nik, $periode_awal, $periode_akhir);
        if (!$nilai) {
            $nilai = [
                'nilai_sasaran' => 0,
                'total_nilai' => 0,
                'nilai_budaya' => 0,
                'fraud' => 0,
                'nilai_akhir' => 0,
                'pencapaian' => '0%',
                'predikat' => '-',
            ];
        }
        // ======================= 
        // SUMMARY NILAI AKHIR (Q)
        // =======================
        $row += 2; // spasi 2 baris
        $startRow = $row;

        // 🎯 Judul Besar
        $sheet->setCellValue("B{$row}", "🎯 NILAI AKHIR (Q)");
        $sheet->mergeCells("B{$row}:F{$row}");
        $sheet->getStyle("B{$row}:F{$row}")->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 18,
                'color' => ['rgb' => 'FFFFFF']
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
            ],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_GRADIENT_LINEAR,
                'rotation' => 90,
                'startColor' => ['rgb' => '215d01'], // Navy klasik
                'endColor' => ['rgb' => '2E7D32'],   // Abu kebiruan elegan
            ],
            'borders' => [
                'outline' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM]
            ]
        ]);
        $sheet->getRowDimension($row)->setRowHeight(36);
        $row++;

        // Ambil nilai awal, pastikan ada default 0
        $nilaiSasaran = round($nilai->nilai_sasaran ?? 0, 2);
        $nilaiBudaya  = round($nilai->nilai_budaya ?? 0, 2);

        // Hitung kontribusi dengan pembobot
        $kontribSasaran = round($nilaiSasaran * 0.95, 2); // 95%
        $kontribBudaya  = round($nilaiBudaya * 0.05, 2);   // 5%

        // 📋 Data tabel nilai
        $dataRows = [
            ["Total Nilai Sasaran Kerja", $nilaiSasaran, "x Bobot % Sasaran Kerja", "95%", $kontribSasaran],
            ["Rata-rata Nilai Internalisasi Budaya", $nilaiBudaya, "x Bobot % Budaya Perusahaan", "5%", $kontribBudaya],
            ["Total Nilai", "", "", "", round($kontribSasaran + $kontribBudaya, 2)],
            ["Fraud (1 jika fraud, 0 jika tidak)", "", "", "", $nilai->fraud ?? 0],
        ];

        $warnaZebra1 = 'F9FAFB'; // abu muda
        $warnaZebra2 = 'FFFFFF'; // putih
        foreach ($dataRows as $r) {
            $sheet->setCellValue("B{$row}", $r[0]);
            $sheet->setCellValue("C{$row}", $r[1]);
            $sheet->setCellValue("D{$row}", $r[2]);
            $sheet->setCellValue("E{$row}", $r[3]);
            $sheet->setCellValue("F{$row}", $r[4]);

            $warnaBg = ($row % 2 == 0) ? $warnaZebra1 : $warnaZebra2;

            $sheet->getStyle("B{$row}:F{$row}")->applyFromArray([
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                    'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER
                ],
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                        'color' => ['rgb' => 'D0D0D0']
                    ]
                ],
                'font' => [
                    'size' => 11,
                    'color' => ['rgb' => '333333']
                ],
                'fill' => [
                    'fillType' => 'solid',
                    'startColor' => ['rgb' => $warnaBg]
                ]
            ]);
            $sheet->getRowDimension($row)->setRowHeight(22);
            $row++;
        }

        // Spasi sebelum total akhir
        $row++;

        // 🏆 Tentukan warna predikat
        $predikat = strtoupper($nilai->predikat ?? '-');
        $warnaPredikat = 'B0B0B0';
        $emojiPredikat = '❔';

        switch (true) {
            case str_contains($predikat, 'EXCELLENT'):
                $warnaPredikat = '348cd4'; // hijau klasik elegan
                $emojiPredikat = '🏅';
                break;
            case str_contains($predikat, 'VERY'):
                $warnaPredikat = '62bce7'; // hijau olive lembut
                $emojiPredikat = '🎖️';
                break;
            case str_contains($predikat, 'GOOD'):
                $warnaPredikat = '78c350'; // biru formal
                $emojiPredikat = '🥇';
                break;
            case str_contains($predikat, 'FAIR'):
                $warnaPredikat = 'f9982c'; // gold klasik
                $emojiPredikat = '🥈';
                break;
            case str_contains($predikat, 'MINUS'):
                $warnaPredikat = 'f92c2c'; // merah tua elegan
                $emojiPredikat = '🥉';
                break;
        }

        // ⭐ TOTAL NILAI AKHIR
        $sheet->setCellValue("B{$row}", "⭐ TOTAL NILAI AKHIR");
        $sheet->mergeCells("B{$row}:E{$row}");
        $sheet->setCellValue("F{$row}", $nilai->total_nilai ?? 0);
        $sheet->getStyle("B{$row}:F{$row}")->applyFromArray([
            'font' => ['bold' => true, 'size' => 16, 'color' => ['rgb' => 'FFFFFF']],
            'alignment' => ['horizontal' => 'center', 'vertical' => 'center'],
            'fill' => [
                'fillType' => 'solid',
                'startColor' => ['rgb' => $warnaPredikat]
            ],
            'borders' => [
                'outline' => ['borderStyle' => 'medium']
            ]
        ]);
        $sheet->getRowDimension($row)->setRowHeight(32);
        $row++;

        // 🏅 PREDIKAT
        $sheet->setCellValue("B{$row}", "🏆 PREDIKAT");
        $sheet->mergeCells("B{$row}:E{$row}");
        $sheet->setCellValue("F{$row}", "{$emojiPredikat} {$predikat}");
        $sheet->getStyle("B{$row}:F{$row}")->applyFromArray([
            'font' => ['bold' => true, 'size' => 16, 'color' => ['rgb' => 'FFFFFF']],
            'alignment' => ['horizontal' => 'center', 'vertical' => 'center'],
            'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => $warnaPredikat]],
            'borders' => ['outline' => ['borderStyle' => 'medium']]
        ]);
        $sheet->getRowDimension($row)->setRowHeight(32);
        $row += 3;

        // =======================
        // 📊 TABEL SKALA NILAI
        // =======================
        $sheet->setCellValue("B{$row}", "Skala Nilai Akhir");
        $sheet->setCellValue("C{$row}", "Yudisium / Predikat");
        $sheet->mergeCells("B{$row}:C{$row}");
        $sheet->getStyle("B{$row}:C{$row}")->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 12],
            'alignment' => ['horizontal' => 'center', 'vertical' => 'center'],
            'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => '2E7D32']],
        ]);
        $sheet->getRowDimension($row)->setRowHeight(26);
        $row++;

        // 🌈 Skala nilai formal klasik
        $skala = [
            ['≥ 4.50 - 5.00', '🏅 Excellent (E)', '348cd4'],
            ['3.50 - < 4.50', '🎖️ Very Good (VG)', '62bce7'],
            ['3.00 - < 3.50', '🥇 Good (G)', '78c350'],
            ['2.00 - < 3.00', '🥈 Fair (F)', 'f9982c'],
            ['< 2.00', '🥉 Minus (M)', 'f92c2c'],
        ];

        foreach ($skala as $s) {
            $sheet->setCellValue("B{$row}", $s[0]);
            $sheet->setCellValue("C{$row}", $s[1]);
            $sheet->mergeCells("B{$row}:C{$row}");
            $sheet->getStyle("B{$row}:C{$row}")->applyFromArray([
                'alignment' => ['horizontal' => 'center', 'vertical' => 'center'],
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => $s[2]]],
                'borders' => ['allBorders' => ['borderStyle' => 'thin', 'color' => ['rgb' => 'FFFFFF']]],
            ]);
            $sheet->getRowDimension($row)->setRowHeight(24);
            $row++;
        }

        // 🎯 Summary Kanan
        $summaryStart = $row - count($skala);
        $summaryCol = 'E';

        $labels = [
            ['Nilai Akhir', $nilai->nilai_akhir ?? '0'],
            ['Pencapaian Akhir', $nilai->pencapaian ?? '0%'],
            ['Yudisium / Predikat', "{$emojiPredikat} {$predikat}"],
        ];

        $current = $summaryStart;
        foreach ($labels as $index => [$label, $val]) {
            $mergeEnd = $current + 1;

            // Label
            $sheet->mergeCells("{$summaryCol}{$current}:{$summaryCol}{$mergeEnd}");
            $sheet->setCellValue("{$summaryCol}{$current}", $label);
            $sheet->getStyle("{$summaryCol}{$current}:{$summaryCol}{$mergeEnd}")->applyFromArray([
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 12],
                'alignment' => ['horizontal' => 'center', 'vertical' => 'center', 'wrapText' => true],
                'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => '2E7D32']],
                'borders' => ['allBorders' => ['borderStyle' => 'thin', 'color' => ['rgb' => 'FFFFFF']]],
            ]);

            // Nilai
            $sheet->mergeCells("F{$current}:F{$mergeEnd}");
            $sheet->setCellValue("F{$current}", $val);
            $sheet->getStyle("F{$current}:F{$mergeEnd}")->applyFromArray([
                'font' => [
                    'bold' => true,
                    'size' => ($index == 0 ? 16 : 14),
                    'color' => ['rgb' => 'FFFFFF'],
                ],
                'alignment' => ['horizontal' => 'center', 'vertical' => 'center'],
                'fill' => [
                    'fillType' => 'solid',
                    'startColor' => ['rgb' => ($index == 2 ? $warnaPredikat : '78c350')]
                ],
                'borders' => ['allBorders' => ['borderStyle' => 'thin', 'color' => ['rgb' => 'FFFFFF']]],
            ]);

            $current = $mergeEnd + 1;
        }

        // 🔧 Set lebar kolom & tinggi baris
        $sheet->getColumnDimension('B')->setWidth(35);
        $sheet->getColumnDimension('C')->setWidth(30);
        $sheet->getColumnDimension('D')->setWidth(25);
        $sheet->getColumnDimension('E')->setWidth(22);
        $sheet->getColumnDimension('F')->setWidth(20);

        // =======================
        // BUAT DISINI UNTUK KOMENTAR
        // =======================
        // =======================
        // ✍️ KOMENTAR PEGAWAI DAN PENILAI (SAMPING)
        // =======================

        // Tentukan posisi baris awal sejajar nilai akhir
        $row = 44; // mulai di samping bagian "NILAI AKHIR"
        $colStart = 'H';
        $colEnd   = 'K';

        // Judul Bagian
        $sheet->setCellValue("{$colStart}{$row}", "III. KOMENTAR PEGAWAI DAN PENILAI");
        $sheet->mergeCells("{$colStart}{$row}:{$colEnd}{$row}");
        $sheet->getStyle("{$colStart}{$row}:{$colEnd}{$row}")->applyFromArray([
            'font' => ['bold' => true, 'size' => 11],
            'alignment' => ['horizontal' => 'left'],
        ]);
        $row++;

        // === Komentar Pegawai ===
        $sheet->setCellValue("{$colStart}{$row}", "Komentar Pegawai Yang Dinilai Tentang Hasil Kerja Selama Setahun");
        $sheet->mergeCells("{$colStart}{$row}:{$colEnd}{$row}");
        $sheet->getStyle("{$colStart}{$row}:{$colEnd}{$row}")->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => '33691E']],
            'borders' => ['outline' => ['borderStyle' => 'thin']],
        ]);
        $row++;

        // Area isi komentar pegawai
        $startIsi = $row;
        $row += 3;
        $sheet->mergeCells("{$colStart}{$startIsi}:{$colEnd}{$row}");
        $sheet->getStyle("{$colStart}{$startIsi}:{$colEnd}{$row}")->applyFromArray([
            'borders' => ['outline' => ['borderStyle' => 'thin']],
        ]);
        $row++;

        // === Komentar Penilai I ===
        $sheet->setCellValue("{$colStart}{$row}", "Komentar Penilai I");
        $sheet->mergeCells("{$colStart}{$row}:{$colEnd}{$row}");
        $sheet->getStyle("{$colStart}{$row}:{$colEnd}{$row}")->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => '33691E']],
            'borders' => ['outline' => ['borderStyle' => 'thin']],
        ]);
        $row++;

        // Area isi komentar penilai I
        $startIsi = $row;
        $row += 3;
        $sheet->mergeCells("{$colStart}{$startIsi}:{$colEnd}{$row}");
        $sheet->getStyle("{$colStart}{$startIsi}:{$colEnd}{$row}")->applyFromArray([
            'borders' => ['outline' => ['borderStyle' => 'thin']],
        ]);
        $row++;

        // === Komentar Penilai II ===
        $sheet->setCellValue("{$colStart}{$row}", "Komentar Penilai II");
        $sheet->mergeCells("{$colStart}{$row}:{$colEnd}{$row}");
        $sheet->getStyle("{$colStart}{$row}:{$colEnd}{$row}")->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => '33691E']],
            'borders' => ['outline' => ['borderStyle' => 'thin']],
        ]);
        $row++;

        // Area isi komentar penilai II
        $startIsi = $row;
        $row += 3;
        $sheet->mergeCells("{$colStart}{$startIsi}:{$colEnd}{$row}");
        $sheet->getStyle("{$colStart}{$startIsi}:{$colEnd}{$row}")->applyFromArray([
            'borders' => ['outline' => ['borderStyle' => 'thin']],
        ]);
        $row += 2;

        // =======================
        // ✍️ TABEL PERSETUJUAN
        // =======================
        $sheet->setCellValue("{$colStart}{$row}", "PERSETUJUAN RENCANA KINERJA AKHIR TAHUN");
        $sheet->mergeCells("{$colStart}{$row}:J{$row}");
        $sheet->getStyle("{$colStart}{$row}:J{$row}")->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => '33691E']],
            'alignment' => ['horizontal' => 'center'],
        ]);
        $sheet->setCellValue("K{$row}", "MENGETAHUI");
        $sheet->getStyle("K{$row}")->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => '33691E']],
            'alignment' => ['horizontal' => 'center'],
        ]);
        $row++;

        // Sub header kolom
        $sheet->setCellValue("{$colStart}{$row}", "Pegawai");
        $sheet->setCellValue("I{$row}", "Penilai I");
        $sheet->mergeCells("J{$row}:K{$row}");
        $sheet->setCellValue("J{$row}", "Penilai II");
        $sheet->getStyle("{$colStart}{$row}:{$colEnd}{$row}")->applyFromArray([
            'font' => ['bold' => true],
            'alignment' => ['horizontal' => 'center'],
            'borders' => ['allBorders' => ['borderStyle' => 'thin']],
        ]);
        $row++;

        // Area tanda tangan
        $startTTD = $row;
        $row += 3;
        $sheet->getStyle("{$colStart}{$startTTD}:{$colEnd}{$row}")->applyFromArray([
            'borders' => ['allBorders' => ['borderStyle' => 'thin']],
            'alignment' => ['horizontal' => 'center', 'vertical' => 'bottom'],
        ]);

        // Tambahkan titik-titik
        $sheet->setCellValue("{$colStart}{$row}", "....................");
        $sheet->setCellValue("I{$row}", "....................");
        $sheet->mergeCells("J{$row}:K{$row}");
        $sheet->setCellValue("J{$row}", "....................");


        // Tambahkan footer
        $row = $current + 3;
        $sheet->setCellValue("B{$row}", "📄 Laporan ini dihasilkan otomatis oleh Sistem Penilaian Kinerja");
        $sheet->mergeCells("B{$row}:F{$row}");
        $sheet->getStyle("B{$row}:F{$row}")->applyFromArray([
            'font' => ['italic' => true, 'size' => 10, 'color' => ['rgb' => '666666']],
            'alignment' => ['horizontal' => 'center'],
        ]);

        // =======================
        // BUAT DISINI UNTUK menampilkan laporan AKTIVITAS COACHING dengan periode range chat yang dilaporkan sesuai range periode yang didownload
        // =======================
        // =======================
        // 📄 SHEET 2: LAPORAN AKTIVITAS COACHING
        // =======================
        $sheet2 = $spreadsheet->createSheet();
        $sheet2->setTitle('Aktivitas Coaching');

        $row = 2;
        $sheet2->setCellValue("B{$row}", "📋 Laporan Aktivitas Coaching");
        $sheet2->mergeCells("B{$row}:F{$row}");
        $sheet2->getStyle("B{$row}:F{$row}")->applyFromArray([
            'font' => ['bold' => true, 'size' => 14, 'color' => ['rgb' => 'FFFFFF']],
            'alignment' => ['horizontal' => 'center', 'vertical' => 'center'],
            'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => '2E7D32']],
        ]);
        $sheet2->getRowDimension($row)->setRowHeight(30);
        $row += 2;

        // Header tabel
        $headers = ['No', 'Tanggal', 'Pengirim', 'Pesan', 'Penerima'];
        $cols = ['B', 'C', 'D', 'E', 'F'];
        foreach ($headers as $i => $h) {
            $sheet2->setCellValue("{$cols[$i]}{$row}", $h);
            $sheet2->getStyle("{$cols[$i]}{$row}")->applyFromArray([
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'alignment' => ['horizontal' => 'center', 'vertical' => 'center', 'wrapText' => true],
                'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => '4CAF50']],
                'borders' => ['allBorders' => ['borderStyle' => 'thin']],
            ]);
        }
        $sheet2->getRowDimension($row)->setRowHeight(24);
        $row++;

        // Ambil data coaching dari model
        $aktivitas = $this->Coaching_model->getLaporanCoaching($pegawai->nik, $periode_awal, $periode_akhir);

        if (empty($aktivitas)) {
            $sheet2->setCellValue("B{$row}", "Tidak ada data aktivitas coaching pada periode ini.");
            $sheet2->mergeCells("B{$row}:F{$row}");
            $sheet2->getStyle("B{$row}:F{$row}")->applyFromArray([
                'alignment' => ['horizontal' => 'center'],
                'font' => ['italic' => true, 'color' => ['rgb' => '777777']],
            ]);
            $row++;
        } else {
            $no = 1;
            foreach ($aktivitas as $item) {
                // Konversi UTC ke WIB
                $dt = new DateTime($item->created_at, new DateTimeZone('UTC'));
                $dt->setTimezone(new DateTimeZone('Asia/Jakarta'));
                $tanggal = $dt->format('d-m-Y H:i:s');

                $sheet2->setCellValue("B{$row}", $no++);
                $sheet2->setCellValue("C{$row}", $tanggal);
                $sheet2->setCellValue("D{$row}", $item->nama_pengirim ?? $item->pengirim_nik);
                $sheet2->setCellValue("E{$row}", $item->pesan);
                $sheet2->setCellValue("F{$row}", "Pegawai: {$pegawai->nama}");

                $sheet2->getStyle("B{$row}:F{$row}")->applyFromArray([
                    'alignment' => ['vertical' => 'top', 'wrapText' => true],
                    'borders' => ['allBorders' => ['borderStyle' => 'thin']],
                ]);

                $sheet2->getRowDimension($row)->setRowHeight(-1);
                $row++;
            }
        }

        // Set lebar kolom
        $sheet2->getColumnDimension('B')->setWidth(5);
        $sheet2->getColumnDimension('C')->setWidth(20);
        $sheet2->getColumnDimension('D')->setWidth(25);
        $sheet2->getColumnDimension('E')->setWidth(70);
        $sheet2->getColumnDimension('F')->setWidth(25);


        // =======================
        // WRAP TEXT & LAYOUT
        // =======================
        $sheet->getStyle('A1:J' . ($row - 1))->getAlignment()->setWrapText(true);
        $sheet->getStyle('A1:J' . ($row - 1))->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
        $sheet->getStyle('A1:J' . ($row - 1))->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        // Override agar header utama benar-benar align left
        $sheet->getStyle('B1')->getAlignment()->setHorizontal('left');
        $sheet->getStyle('B2')->getAlignment()->setHorizontal('left');
        // Override agar hanya ISI data kolom Sasaran Kerja (C) dan Indikator (D) align left, header tetap center
        $headerPenilaianRow = 0;
        // Cari baris header penilaian (dengan value "Sasaran Kerja" di C)
        for ($i = 1; $i <= $row; $i++) {
            if ($sheet->getCell('C' . $i)->getValue() === 'Sasaran Kerja') {
                $headerPenilaianRow = $i;
                break;
            }
        }
        if ($headerPenilaianRow > 0) {
            $sheet->getStyle('C' . ($headerPenilaianRow + 1) . ':C' . ($row - 1))->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
            $sheet->getStyle('D' . ($headerPenilaianRow + 1) . ':D' . ($row - 1))->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
        }
        // Override agar DATA PEGAWAI tetap align left
        $sheet->getStyle('B4:G4')->getAlignment()->setHorizontal('left');

        // Override khusus blok data pegawai dan penilai agar kolom B dan C rata kiri
        $sheet->getStyle('B5:C20')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
        $sheet->getStyle('B5:C20')->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_TOP);

        // ✅ Tambahkan override blok Penilai II
        $sheet->getStyle('E10:G13')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
        $sheet->getStyle('E10:G13')->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_TOP);

        $sheet->getStyle('F5:G6')->getAlignment()
            ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT)
            ->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);


        // set lebar kolom
        $sheet->getColumnDimension('A')->setWidth(20);
        $sheet->getColumnDimension('B')->setWidth(35);
        $sheet->getColumnDimension('C')->setWidth(40);
        $sheet->getColumnDimension('D')->setWidth(18);
        $sheet->getColumnDimension('E')->setWidth(18);
        $sheet->getColumnDimension('F')->setWidth(20);
        $sheet->getColumnDimension('G')->setWidth(20);
        $sheet->getColumnDimension('H')->setWidth(15);
        $sheet->getColumnDimension('I')->setWidth(15);
        $sheet->getColumnDimension('J')->setWidth(15);
        $sheet->getColumnDimension('K')->setWidth(18);

        // tinggi baris auto
        for ($r = 1; $r <= ($row - 1); $r++) {
            $sheet->getRowDimension($r)->setRowHeight(-1);
        }

        // =======================
        // DOWNLOAD FILE
        // =======================
        $filename = "Data_Penilaian_{$pegawai->nama}_{$pegawai->nik}.xlsx";
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header("Content-Disposition: attachment;filename=\"{$filename}\"");
        header('Cache-Control: max-age=0');

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
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
            redirect('administrator/datadiri');
        }

        $this->load->view('layout/header');
        $this->load->view('administrator/datadiri', $data);
        $this->load->view('layout/footer');
    }

    // ========== Halaman Kelola Tingkatan Jabatan ==========
    public function kelolatingkatanjabatan()
    {
        $data['judul'] = 'Kelola Tingkatan Jabatan';
        $data['list'] = $this->PenilaiMapping_model->getAll();

        $this->load->view('layout/header', $data);
        $this->load->view('administrator/kelolatingkatanjabatan', $data);
        $this->load->view('layout/footer');
    }

    // Tambah data
    public function tambahPenilaiMapping()
    {
        if ($this->input->post()) {
            $insert = [
                'jabatan' => $this->input->post('jabatan'),
                'jenis_penilaian'  => $this->input->post('jenis_penilaian'),
                'unit_kerja' => $this->input->post('unit_kerja'),
                'penilai1_jabatan' => $this->input->post('penilai1_jabatan'),
                'penilai2_jabatan' => $this->input->post('penilai2_jabatan'),
            ];

            $this->PenilaiMapping_model->insert($insert);
            $this->session->set_flashdata('success', 'Data mapping berhasil ditambahkan.');
            redirect('Administrator/kelolatingkatanjabatan');
        }
    }

    // Edit data
    public function editPenilaiMapping($id)
    {
        if ($this->input->post()) {
            $update = [
                'jabatan' => $this->input->post('jabatan'),
                'jenis_penilaian'  => $this->input->post('jenis_penilaian'),
                'unit_kerja' => $this->input->post('unit_kerja'),
                'penilai1_jabatan' => $this->input->post('penilai1_jabatan'),
                'penilai2_jabatan' => $this->input->post('penilai2_jabatan'),
            ];

            $this->PenilaiMapping_model->update($id, $update);
            $this->session->set_flashdata('success', 'Data mapping berhasil diubah.');
            redirect('Administrator/kelolatingkatanjabatan');
        }
    }

    // Hapus data
    public function hapusPenilaiMapping($id)
    {
        if ($this->PenilaiMapping_model->delete($id)) {
            $this->session->set_flashdata('success', 'Data berhasil dihapus');
        }
        redirect('administrator/kelolatingkatanjabatan');
    }


    // Catatan Penilai
    public function getCatatanPenilai()
    {
        header('Content-Type: application/json');

        $nik = $this->input->post('nik_pegawai');
        $draw = intval($this->input->post("draw"));
        $start = intval($this->input->post("start"));
        $length = intval($this->input->post("length"));
        $search = $this->input->post("search")['value'] ?? '';
        $order = $this->input->post("order")[0] ?? null;
        $columnIndex = $order['column'] ?? 3;
        $sortDir = $order['dir'] ?? 'desc';

        $columns = ['no', 'nama_penilai', 'catatan', 'tanggal'];
        $orderColumn = $columns[$columnIndex] ?? 'tanggal';

        $this->load->model('Penilaian_model');

        $recordsTotal = $this->Penilaian_model->countAllCatatanByPegawai($nik);
        $recordsFiltered = $this->Penilaian_model->countFilteredCatatanByPegawai($nik, $search);

        // ✅ Tangani $length = -1
        if ($length == -1) {
            $start = 0;
            $length = $recordsFiltered; // ambil semua data
        }

        $list = $this->Penilaian_model->getCatatanByPegawaiFiltered($nik, $start, $length, $search, $orderColumn, $sortDir);

        $data = [];
        $no = $start;
        foreach ($list as $row) {
            $no++;
            $data[] = [
                'no' => $no,
                'nama_penilai' => $row->penilai_nama,
                'catatan' => $row->catatan,
                'tanggal' => $row->tanggal
            ];
        }

        echo json_encode([
            "draw" => $draw,
            "recordsTotal" => $recordsTotal,
            "recordsFiltered" => $recordsFiltered,
            "data" => $data
        ]);
    }

    public function getCatatanPegawai()
    {
        header('Content-Type: application/json');

        $nik = $this->input->post('nik_pegawai');
        $draw = intval($this->input->post("draw"));
        $start = intval($this->input->post("start"));
        $length = intval($this->input->post("length"));
        $search = $this->input->post("search")['value'] ?? '';
        $order = $this->input->post("order")[0] ?? null;
        $columnIndex = $order['column'] ?? 2;
        $sortDir = $order['dir'] ?? 'desc';

        $columns = ['no', 'catatan', 'tanggal'];
        $orderColumn = $columns[$columnIndex] ?? 'tanggal';

        $this->load->model('Penilaian_model');

        $recordsTotal = $this->Penilaian_model->countAllCatatanPegawai($nik);
        $recordsFiltered = $this->Penilaian_model->countFilteredCatatanPegawai($nik, $search);

        // ✅ Tangani $length = -1
        if ($length == -1) {
            $start = 0;
            $length = $recordsFiltered; // ambil semua data
        }

        $list = $this->Penilaian_model->getCatatanPegawaiFiltered($nik, $start, $length, $search, $orderColumn, $sortDir);

        $data = [];
        $no = $start;
        foreach ($list as $row) {
            $no++;
            $data[] = [
                'no' => $no,
                'catatan' => $row->catatan,
                'tanggal' => $row->tanggal
            ];
        }

        echo json_encode([
            "draw" => $draw,
            "recordsTotal" => $recordsTotal,
            "recordsFiltered" => $recordsFiltered,
            "data" => $data
        ]);
    }

    public function lihatCoachingChat($nik, $penilai_nik)
    {
        $this->load->model('DataPegawai_model');

        $data['chat'] = $this->DataPegawai_model->getCoachingChat($nik, $penilai_nik);
        $data['nik_pegawai'] = $nik;
        $data['nik_penilai'] = $penilai_nik;

        $this->load->view('administrator/chat_coaching', $data);
    }


    // Halaman Verifikasi Penilaian - tabel list pegawai + status penilaian
    public function verifikasi_penilaian()
    {
        $this->load->model('Penilaian_model');

        // ambil periode dari query string jika ada, gunakan default tahun berjalan
        $periode_awal = $this->input->get('awal') ?? date('Y-01-01');
        $periode_akhir = $this->input->get('akhir') ?? date('Y-12-31');

        $data['periode_list'] = $this->Penilaian_model->getPeriodeList();
        $data['selected_awal'] = $periode_awal;
        $data['selected_akhir'] = $periode_akhir;
        $data['judul'] = 'Verifikasi Penilaian Pegawai';

        $this->load->view('layout/header', $data);
        $this->load->view('administrator/verifikasi_penilaian', $data);
        $this->load->view('layout/footer');
    }

    // Endpoint AJAX untuk mengembalikan JSON data pegawai + status penilaian
    public function getVerifikasiData()
    {
        $this->load->model('DataPegawai_model');
        $this->load->model('Penilaian_model');

        $periode_awal = $this->input->get('awal') ?? date('Y-01-01');
        $periode_akhir = $this->input->get('akhir') ?? date('Y-12-31');

        $pegawais = $this->DataPegawai_model->getAllPegawai();
        $result = [];
        foreach ($pegawais as $p) {
            // hitung apakah ada penilaian untuk periode ini
            $penilaian_count = $this->db->where('nik', $p->nik)
                ->where('periode_awal', $periode_awal)
                ->where('periode_akhir', $periode_akhir)
                ->from('penilaian')->count_all_results();

            // nama field bisa berbeda antar query (nama / nama_pegawai) -> fallback
            $namaPegawai = $p->nama ?? $p->nama_pegawai ?? $p->nama_peg ?? '';

            // default status
            if ($penilaian_count == 0) {
                $statusLabel = 'Belum Dinilai';
            } else {
                // Ambil status penilaian dari tabel penilaian untuk periode ini (db value: pending/disetujui/ditolak)
                $dbStatus = $this->Penilaian_model->getStatusPenilaian($p->nik, $periode_awal, $periode_akhir);
                switch ($dbStatus) {
                    case 'disetujui':
                        $statusLabel = 'Diverifikasi';
                        break;
                    case 'ditolak':
                        $statusLabel = 'Ditolak';
                        break;
                    case 'pending':
                    default:
                        // ada penilaian tapi belum diverifikasi
                        $statusLabel = 'Dinilai';
                        break;
                }
            }

            $result[] = [
                'nik' => $p->nik,
                'nama' => $namaPegawai,
                'jabatan' => $p->jabatan ?? ($p->jabatan ?? ''),
                'status_penilaian' => $statusLabel,
                'action' => site_url('administrator/detailverifikasi/' . $p->nik) . '?awal=' . $periode_awal . '&akhir=' . $periode_akhir
            ];
        }

        header('Content-Type: application/json');
        echo json_encode(['data' => $result]);
        exit;
    }
    // ===============================================================
    // ✅ DETAIL VERIFIKASI PENILAIAN PEGAWAI
    // ===============================================================
    public function detailVerifikasi($nik = null)
    {
        if (!$nik) {
            show_error("NIK pegawai tidak ditemukan.", 404);
        }

        $awal = $this->input->get('awal') ?? date('Y-01-01');
        $akhir = $this->input->get('akhir') ?? date('Y-12-31');

        $this->load->model('Penilaian_model');

        // Ambil daftar periode untuk select
        $data['periode_list'] = $this->Penilaian_model->getPeriodeList();

        // Ambil data pegawai (model mungkin mengembalikan field 'nama' atau 'nama_pegawai')
        $pegawai = $this->Penilaian_model->getPegawaiWithPenilai($nik);
        if (!$pegawai) {
            show_error("Data pegawai dengan NIK {$nik} tidak ditemukan.", 404);
        }

        // normalize fields expected by the view
        if (empty($pegawai->nama_pegawai) && !empty($pegawai->nama)) {
            $pegawai->nama_pegawai = $pegawai->nama;
        }
        // penilai1_nama / penilai2_nama assumed provided by model

        // Ambil data penilaian berdasarkan periode
        $penilaian = $this->Penilaian_model->getPenilaianDetail($nik, $awal, $akhir);
        $status_penilaian = $this->Penilaian_model->getStatusPenilaian($nik, $awal, $akhir);

        // Ambil nilai akhir (untuk nilai budaya dan predikat jika tersedia)
        $nilai_akhir = $this->Penilaian_model->getNilaiAkhir($nik, $awal, $akhir);

        // 🔹 Ambil nilai budaya pegawai berdasarkan periode
        $budayaData = $this->Penilaian_model->getBudayaNilaiByNik($nik, $awal, $akhir);
        $data['budaya_nilai'] = $budayaData['nilai_budaya'];
        $data['rata_rata_budaya'] = $budayaData['rata_rata'];

        // 🔹 Ambil daftar budaya utama & panduan
        $data['budaya'] = $this->Penilaian_model->getAllBudaya();

        $data['judul'] = "Detail Verifikasi Penilaian";
        $data['pegawai_detail'] = $pegawai;
        $data['penilaian'] = $penilaian;
        $data['status_penilaian'] = $status_penilaian;
        $data['nilai_akhir'] = $nilai_akhir;
        $data['selected_awal'] = $awal;
        $data['selected_akhir'] = $akhir;

        $this->load->view("layout/header");
        $this->load->view("administrator/detailverifikasi", $data);
        $this->load->view("layout/footer");
    }


    // ===============================================================
    // ✅ AKSI VERIFIKASI PENILAIAN (AJAX)
    // ===============================================================
    public function verifikasiPenilaian()
    {
        $nik = $this->input->post('nik');
        $status = $this->input->post('status');

        // periode yang dikirimkan dari halaman detail (opsional)
        $awal = $this->input->post('awal') ?? $this->input->post('awal') ?? $this->input->get('awal') ?? date('Y-01-01');
        $akhir = $this->input->post('akhir') ?? $this->input->post('akhir') ?? $this->input->get('akhir') ?? date('Y-12-31');

        if (!$nik || !$status) {
            echo json_encode([
                'status' => 'error',
                'message' => 'Data tidak lengkap.'
            ]);
            return;
        }

        $this->load->model('Penilaian_model');
        $update = $this->Penilaian_model->updateStatusPenilaian($nik, $status, $awal, $akhir);

        if ($update) {
            echo json_encode([
                'status' => 'success',
                'message' => 'Status penilaian berhasil diperbarui!',
                'new_status' => $status
            ]);
        } else {
            echo json_encode([
                'status' => 'error',
                'message' => 'Gagal memperbarui status penilaian.'
            ]);
        }
    }

    // ==================== KELOLA BUDAYA (AJAX) ====================

    public function kelolaBudaya()
    {
        $this->load->view('layout/header');
        $this->load->view('administrator/kelolabudaya');
        $this->load->view('layout/footer');
    }

    public function getBudaya()
    {
        $this->load->model('Budaya_model');
        echo json_encode($this->Budaya_model->getAll());
    }

    public function simpanBudayaAjax()
    {
        $this->load->model('Budaya_model');
        $data = [
            'id_budaya' => $this->input->post('id_budaya'),
            'perilaku_utama' => $this->input->post('perilaku_utama'),
            'panduan_perilaku' => $this->input->post('panduan_perilaku')
        ];

        if ($this->Budaya_model->save($data)) {
            echo json_encode(['status' => 'success', 'message' => 'Data budaya berhasil disimpan!']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Gagal menyimpan data!']);
        }
    }

    public function hapusBudayaAjax($id)
    {
        $this->load->model('Budaya_model');
        if ($this->Budaya_model->delete($id)) {
            echo json_encode(['status' => 'success', 'title' => 'Berhasil', 'message' => 'Data berhasil dihapus!']);
        } else {
            echo json_encode(['status' => 'error', 'title' => 'Gagal', 'message' => 'Data gagal dihapus!']);
        }
    }
}
