<?php
defined('BASEPATH') or exit('No direct script access allowed');

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xls;
use PhpOffice\PhpSpreadsheet\IOFactory;

/**
 * @property Indikator_model $Indikator_model
 * @property Penilaian_model $Penilaian_model
 * @property DataPegawai_model $DataPegawai_model
 * @property RiwayatJabatan_model $RiwayatJabatan_model
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
        $jabatan = $this->db->get('pegawai')->result();
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
        $pegawai = $this->db->get_where('pegawai', ['nik' => $nik])->row();

        // Ambil periode dari GET atau POST
        $periode_awal = $this->input->get('awal') ?? $this->input->post('periode_awal') ?? date('Y-01-01');
        $periode_akhir = $this->input->get('akhir') ?? $this->input->post('periode_akhir') ?? date('Y-12-31');

        if ($pegawai) {
            $indikator = $this->Penilaian_model->get_indikator_by_jabatan_dan_unit(
                $pegawai->jabatan,
                $pegawai->unit_kerja,
                $nik,
                $periode_awal,
                $periode_akhir
            );

            $data['pegawai_detail'] = $pegawai;
            $data['indikator_by_jabatan'] = $indikator;

            if ($this->input->post('nik')) {
                $this->session->set_flashdata('message', [
                    'type' => 'success',
                    'text' => 'Data penilaian pegawai ditemukan!'
                ]);
            }
        } else {
            $data['pegawai_detail'] = null;
            $data['indikator_by_jabatan'] = [];
            $this->session->set_flashdata('message', [
                'type' => 'error',
                'text' => 'Pegawai dengan NIK tersebut tidak ditemukan.'
            ]);
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

        $header = $sheetData[1];
        $colA = strtolower(trim($header['A']));
        $colB = strtolower(trim($header['B']));
        $colC = strtolower(trim($header['C']));
        $colD = strtolower(trim($header['D']));
        $colE = strtolower(trim($header['E']));

        if (
            $colA !== 'nik' ||
            $colB !== 'nama' ||
            $colC !== 'jabatan' ||
            !in_array($colD, ['unit_kerja', 'unit kerja', 'unit kantor', 'unitkantor']) ||
            $colE !== 'password'
        ) {
            $this->session->set_flashdata('error', 'Header tidak sesuai. Gunakan template resmi (Nik, Nama, Jabatan, Unit Kerja, Password).');
            redirect('SuperAdmin/kelolaDataPegawai');
            return;
        }

        $rows = [];
        $errors = [];
        foreach ($sheetData as $i => $row) {
            if ($i == 1) continue; // skip header

            $nik = trim($row['A']);
            $nama = trim($row['B']);
            $jabatan = trim($row['C']);
            $unit_kerja = trim($row['D']);
            $password_plain = trim($row['E']);

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

            // Siapkan data untuk batch insert ke pegawai
            $rows[] = [
                'nik' => $nik,
                'nama' => $nama,
                'jabatan' => $jabatan,
                'unit_kerja' => $unit_kerja,
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
        $password_plain = $this->input->post('password'); // password asli dari form
        $password_hashed = password_hash($password_plain, PASSWORD_DEFAULT);

        // Insert ke tabel pegawai
        $data_pegawai = [
            'nik' => $nik,
            'nama' => $nama,
            'jabatan' => $jabatan,
            'unit_kerja' => $unit_kerja,
            'password' => $password_hashed,
        ];
        $this->DataPegawai_model->insertPegawai($data_pegawai);

        // Insert ke tabel riwayat_jabatan
        $this->DataPegawai_model->insertRiwayatAwal($nik, $jabatan, $unit_kerja);

        // 🔹 Insert otomatis ke tabel users (jika belum ada)
        $cek_user = $this->db->get_where('users', ['nik' => $nik])->row();
        if (!$cek_user) {
            $data_user = [
                'nik' => $nik,
                'password' => $password_hashed, // bisa pakai password default juga
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
        $tgl_mulai = $this->input->post('tgl_mulai');

        $this->DataPegawai_model->tambahRiwayatJabatan($nik, $jabatan, $unit_kerja, $tgl_mulai);

        $this->session->set_flashdata('success', 'Riwayat jabatan baru berhasil ditambahkan.');
        redirect('SuperAdmin/detailPegawai/' . $nik);
    }


    public function nonaktifPegawai($nik)
    {
        $this->RiwayatJabatan_model->updateStatusPegawai($nik, 'nonaktif');
        $this->session->set_flashdata('success', 'Pegawai berhasil dinonaktifkan');
        redirect('SuperAdmin/detailPegawai/' . $nik);
    }

    public function aktifkanPegawai($nik)
    {
        $this->RiwayatJabatan_model->updateStatusPegawai($nik, 'aktif');
        $this->session->set_flashdata('success', 'Pegawai berhasil diaktifkan kembali');
        redirect('SuperAdmin/detailPegawai/' . $nik);
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

        // Load library PhpSpreadsheet

        // $this->load->library('excel');

        // Load template excel
        $templatePath = FCPATH . "uploads/templatedatapenilaian.xls";
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
    }
}
