
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
 * @property Pegawai_model $Pegawai_model
 * @property Nilai_model $Nilai_model
 * @property DataDiri_model $DataDiri_model
 * @property Penilaian_model $Penilaian_model
 * @property Indikator_model $Indikator_model
 * @property Coaching_model $Coaching_model
 * @property DataPegawai_model $DataPegawai_model
 * @property RiwayatJabatan_model $RiwayatJabatan_model
 * @property PenilaiMapping_model $PenilaiMapping_model
 * @property DataDiri_model $DataDiri_model
 * @property CI_Input $input
 * @property CI_Session $session
 * @property CI_DB_query_builder $db
 */

class Pegawai extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        // model Pegawai di subfolder models/pegawai/Pegawai_model.php
        $this->load->model('pegawai/Pegawai_model');
        $this->load->model('pegawai/Nilai_model');
        $this->load->model('pegawai/Coaching_model');
        $this->load->model('Penilaian_model');
        $this->load->model('Indikator_model');
        $this->load->model('DataDiri_model');
        $this->load->library('session');

        // Pastikan hanya pegawai yang bisa akses
        if (
            !$this->session->userdata('logged_in') ||
            !in_array($this->session->userdata('role'), ['pegawai', 'administrator', 'administrator_renstra'])
        ) {
            redirect('auth');
        }
    }

    /**
     * Dashboard pegawai
     */
    public function index()
    {
        $nik = $this->session->userdata('nik');
        $pegawai = $this->Pegawai_model->getPegawaiWithPenilai($nik);

        $periode_awal = $this->input->get('awal') ?? $this->input->post('periode_awal') ?? date('Y') . "-01-01";
        $periode_akhir = $this->input->get('akhir') ?? $this->input->post('periode_akhir') ?? date('Y') . "-12-31";

        // 🔹 Ambil status lock dari kolom lock_input
        $lock_status = $this->Pegawai_model->getLockStatus($periode_awal, $periode_akhir);

        // 🔹 Ambil indikator kerja
        $indikator = $this->Pegawai_model->get_indikator_by_jabatan_dan_unit(
            $pegawai->jabatan,
            $pegawai->unit_kerja,
            $nik,
            $periode_awal,
            $periode_akhir
        );

        // 🔹 Ambil daftar budaya utama & panduan dari tabel `budaya`
        $budaya = $this->Nilai_model->getAllBudaya();

        // 🔹 Ambil nilai budaya pegawai dari tabel `budaya_nilai`
        $nilaiBudayaRow = $this->Nilai_model->getNilaiBudayaByPegawai($nik, $periode_awal, $periode_akhir);

        $budaya_nilai = [];
        $rata_rata_budaya = 0;
        if ($nilaiBudayaRow) {
            $budaya_nilai = json_decode($nilaiBudayaRow->nilai_budaya, true);
            $rata_rata_budaya = $nilaiBudayaRow->rata_rata ?? 0;
        }

        // 🔹 Data tambahan
        $periode_list = $this->Pegawai_model->getPeriodePegawai($nik);
        $nilai_akhir  = $this->Pegawai_model->getNilaiAkhir($nik, $periode_awal, $periode_akhir);

        $data = [
            'judul' => "Dashboard Pegawai",
            'pegawai_detail' => $pegawai,
            'indikator_by_jabatan' => $indikator,
            'periode_awal' => $periode_awal,
            'periode_akhir' => $periode_akhir,
            'periode_list' => $periode_list,
            'nilai_akhir' => $nilai_akhir,
            'is_locked' => $lock_status,
            'budaya' => $budaya, // dari tabel budaya (perilaku + panduan)
            'budaya_nilai' => $budaya_nilai, // dari tabel budaya_nilai (hasil penilaian pegawai)
            'rata_rata_budaya' => $rata_rata_budaya
        ];

        $this->load->view('layoutpegawai/header', $data);
        $this->load->view('pegawai/index', $data);
        $this->load->view('layoutpegawai/footer');
    }


    public function simpanPenilaianBaris()
    {
        $nik = $this->session->userdata('nik');

        $indikator_id = $this->input->post('indikator_id') ?? $this->input->post('id');
        $realisasi = $this->input->post('realisasi') ?? null;

        $periode_awal = $this->input->post('periode_awal') ?? date('Y') . "-01-01";
        $periode_akhir = $this->input->post('periode_akhir') ?? date('Y') . "-12-31";

        $save = $this->Pegawai_model->save_penilaian(
            $nik,
            $indikator_id,
            $realisasi,
            $periode_awal,
            $periode_akhir
        );

        if ($save) {
            echo json_encode([
                'status' => 'success',
                'message' => 'Penilaian berhasil disimpan!',
                'data' => compact('indikator_id', 'realisasi', 'periode_awal', 'periode_akhir')
            ]);
        } else {
            $error = $this->db->error();
            echo json_encode([
                'status' => 'error',
                'message' => 'Gagal menyimpan data.',
                'debug' => $error
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

        $save = $this->Pegawai_model->save_nilai_akhir(
            $nik,
            $nilai_sasaran,
            $nilai_budaya,
            $total_nilai,
            $fraud,
            $nilai_akhir,
            $pencapaian,
            $predikat,
            $periode_awal,
            $periode_akhir
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
    // ========== Halaman Penilaian (Nilai Pegawai) ==========
    public function nilaiPegawai()
    {
        $nik = $this->session->userdata('nik');
        $this->load->model('pegawai/Nilai_model');

        $pegawai_penilai1 = $this->Nilai_model->getPegawaiSebagaiPenilai1($nik);
        $pegawai_penilai2 = $this->Nilai_model->getPegawaiSebagaiPenilai2($nik);

        $data = [
            'judul' => 'Nilai Pegawai',
            'pegawai_penilai1' => $pegawai_penilai1,
            'pegawai_penilai2' => $pegawai_penilai2
        ];

        $this->load->view('layoutpegawai/header', $data);
        $this->load->view('pegawai/nilaipegawai', $data);
        $this->load->view('layoutpegawai/footer');
    }

    public function nilaiPegawaiDetail($nik)
    {
        $awal = $this->input->get('awal');
        $akhir = $this->input->get('akhir');

        if (!$awal || !$akhir) {
            $awal = date('Y-01-01');
            $akhir = date('Y-12-31');
            redirect("Pegawai/nilaiPegawaiDetail/$nik?awal=$awal&akhir=$akhir");
        }

        $this->load->model('pegawai/Nilai_model');
        $this->load->model('Pegawai_model');

        $pegawai = $this->Nilai_model->getPegawaiWithPenilai($nik);
        $indikator = $this->Nilai_model->getIndikatorPegawai($nik, $awal, $akhir);

        // 🔹 Ambil daftar periode
        $this->db->select('periode_awal, periode_akhir');
        $this->db->from('penilaian');
        $this->db->where('nik', $nik);
        $this->db->group_by(['periode_awal', 'periode_akhir']);
        $this->db->order_by('periode_awal', 'ASC');
        $periode_list = $this->db->get()->result();

        // 🔹 Ambil nilai akhir
        $nilai_akhir = $this->Pegawai_model->getNilaiAkhir($nik, $awal, $akhir);

        // 🔹 Ambil status lock
        $is_locked = $this->Nilai_model->getLockStatus($nik, $awal, $akhir);

        // 🔹 Ambil data budaya dari tabel master
        $budaya = $this->Nilai_model->getAllBudaya();

        // 🔹 Ambil nilai budaya (hasil penilaian sebelumnya)
        $nilaiBudayaDB = $this->Nilai_model->getNilaiBudayaByPegawai($nik, $awal, $akhir);
        $budaya_nilai = [];
        if (!empty($nilaiBudayaDB) && !empty($nilaiBudayaDB->nilai_budaya)) {
            $budaya_nilai = json_decode($nilaiBudayaDB->nilai_budaya, true);
        }


        $data = [
            'judul' => "Form Penilaian Pegawai",
            'pegawai_detail' => $pegawai,
            'indikator_by_jabatan' => $indikator,
            'periode_awal' => $awal,
            'periode_akhir' => $akhir,
            'periode_list' => $periode_list,
            'nilai_akhir' => $nilai_akhir,
            'is_locked' => $is_locked,
            'budaya' => $budaya,
            'budaya_nilai' => $budaya_nilai, // ✅ kirim ke view
            'rata_rata_budaya' => $nilaiBudayaDB->rata_rata ?? 0
        ];

        $this->load->view('layoutpegawai/header', $data);
        $this->load->view('pegawai/nilaipegawai_detail', $data);
        $this->load->view('layoutpegawai/footer');
    }


    public function simpanNilaiBudaya()
    {
        $this->load->model('pegawai/Nilai_model');

        $data = [
            'nik_pegawai' => $this->input->post('nik_pegawai'),
            'periode_awal' => $this->input->post('periode_awal'),
            'periode_akhir' => $this->input->post('periode_akhir'),
            'key' => $this->input->post('key'),  // contoh: budaya_3_1
            'skor' => $this->input->post('skor'),
            'rata_rata' => $this->input->post('rata_rata')
        ];

        $this->Nilai_model->simpanNilaiBudayaSatuBaris($data);
        echo json_encode(['status' => 'success']);
    }


    public function datadiriPegawai()
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
            redirect('pegawai/datadiriPegawai');
        }

        $this->load->view('layoutpegawai/header', $data);
        $this->load->view('pegawai/datadiripegawai', $data);
        $this->load->view('layoutpegawai/footer');
    }

    public function updateStatus()
    {
        $id            = $this->input->post('id');
        $status        = $this->input->post('status');
        $realisasi     = $this->input->post('realisasi');
        $pencapaian    = $this->input->post('pencapaian');
        $nilai         = $this->input->post('nilai');
        $nilai_dibobot = $this->input->post('nilai_dibobot');

        $data = [
            'realisasi'     => $realisasi,
            'pencapaian'    => $pencapaian,
            'nilai'         => $nilai,
            'nilai_dibobot' => $nilai_dibobot
        ];

        $update = $this->Nilai_model->updateStatusAndRealisasi($id, $status, $data);

        if ($update) {
            echo json_encode([
                'success' => true,
                'message' => 'Status & Realisasi berhasil disimpan!'
            ]);
        } else {
            $error = $this->db->error();
            echo json_encode([
                'success' => false,
                'message' => 'Gagal menyimpan data',
                'debug'   => $error
            ]);
        }
    }

    public function updateStatusAll()
    {
        $ids = $this->input->post('ids');
        $status = $this->input->post('status');

        if (empty($ids) || empty($status)) {
            echo json_encode(['success' => false, 'message' => 'Data tidak lengkap']);
            return;
        }

        $ids_array = explode(',', $ids);

        $this->db->where_in('id', $ids_array);
        $update = $this->db->update('penilaian', ['status' => $status]);

        if ($update) {
            echo json_encode(['success' => true, 'message' => 'Semua status berhasil diupdate']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Gagal update status']);
        }
    }
    public function simpan_catatan()
    {
        $nik_pegawai = $this->input->post('nik_pegawai');
        $nik_penilai = $this->session->userdata('nik');
        $catatan = $this->input->post('catatan');

        if (!$catatan) {
            echo json_encode(['success' => false, 'message' => 'Catatan kosong']);
            return;
        }

        $data = [
            'nik_pegawai' => $nik_pegawai,
            'nik_penilai' => $nik_penilai,
            'catatan' => $catatan,
            'tanggal' => date('Y-m-d H:i:s')
        ];

        $insert = $this->Nilai_model->tambahCatatan($data);

        if ($insert) {
            $penilai = $this->db->get_where('pegawai', ['nik' => $nik_penilai])->row();
            $nama_penilai = $penilai ? $penilai->nama : 'Penilai';

            echo json_encode([
                'success' => true,
                'nama_penilai' => $nama_penilai,
                'message' => 'Catatan berhasil disimpan!'
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Gagal menyimpan catatan!']);
        }
    }

    public function simpan_catatan_pegawai()
    {
        $nik = $this->input->post('nik') ?? $this->session->userdata('nik');
        $catatan = $this->input->post('catatan');

        if (empty(trim($catatan))) {
            echo json_encode(['success' => false, 'message' => 'Catatan kosong']);
            return;
        }

        $data = [
            'nik'     => $nik,
            'catatan' => $catatan,
            'tanggal' => date('Y-m-d H:i:s')
        ];

        $insert = $this->Pegawai_model->tambahCatatan($data);

        echo json_encode([
            'success' => (bool)$insert,
            'message' => $insert ? 'Catatan berhasil ditambahkan' : 'Gagal menyimpan catatan'
        ]);
    }

    public function getPegawaiSatuUnit($nik)
    {
        $pegawai = $this->Pegawai_model->getPegawaiByNIK($nik);

        if (!$pegawai) {
            show_404();
        }

        $list = $this->Pegawai_model->getPegawaiByUnit(
            $pegawai->unit_kerja,
            $pegawai->unit_kantor,
            $pegawai->nik
        );

        echo json_encode($list);
    }

    public function getCoachingChat($nikPegawai)
    {
        $lastId = $this->input->get('lastId') ?? 0;
        $this->load->model('pegawai/Coaching_model');

        $data = $this->Coaching_model->getChat($nikPegawai, (int)$lastId);
        echo json_encode($data);
    }

    public function kirimCoachingPesan()
    {
        header('Content-Type: application/json');
        $this->load->model('pegawai/Coaching_model');

        $nik_pegawai = $this->input->post('nik_pegawai');
        $pesan = $this->input->post('pesan');
        $pengirim_nik = $this->session->userdata('nik');

        if (empty($nik_pegawai) || empty($pesan) || empty($pengirim_nik)) {
            echo json_encode(['success' => false, 'message' => 'Data tidak lengkap']);
            return;
        }

        // ambil mapping penilai
        $this->load->model('pegawai/Pegawai_model');
        $pegawaiDetail = $this->Pegawai_model->getPegawaiWithPenilai($nik_pegawai);

        $data = [
            'nik_pegawai'  => $nik_pegawai,
            'nik_penilai1' => $pegawaiDetail->penilai1_nik ?? null,
            'nik_penilai2' => $pegawaiDetail->penilai2_nik ?? null,
            'pengirim_nik' => $pengirim_nik,
            'pesan'        => $pesan,
            'created_at'   => date('Y-m-d H:i:s'),
            'is_read'      => 0
        ];

        $result = $this->Coaching_model->simpanPesan($data);
        if ($result['success']) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => $result['error']['message'] ?? 'Database error']);
        }
    }

    public function getUnreadCoachingCount()
    {
        header('Content-Type: application/json');
        $this->load->model('pegawai/Coaching_model');
        $nik = $this->session->userdata('nik');
        if (empty($nik)) {
            echo json_encode(['count' => 0, 'list' => []]);
            return;
        }

        $list = $this->Coaching_model->getUnreadList($nik);
        echo json_encode(['count' => count($list), 'list' => $list]);
    }

    public function clearUnreadCoaching()
    {
        header('Content-Type: application/json');
        $nik = $this->session->userdata('nik');
        if (empty($nik)) {
            echo json_encode(['success' => false]);
            return;
        }

        $this->load->model('pegawai/Coaching_model');
        $this->Coaching_model->clearUnread($nik);

        echo json_encode(['success' => true]);
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

        // Bersihkan buffer agar tidak ada output selain Excel
        ob_end_clean();
        ob_start();

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

        $row += 2;
        // =======================
        // SKALA NILAI BUDAYA PERUSAHAAN (O)
        // =======================
        $sheet->setCellValue("B{$row}", "Skala Nilai Internalisasi Budaya");
        $sheet->mergeCells("B{$row}:G{$row}");
        $sheet->getStyle("B{$row}")->getFont()->setBold(true)->getColor()->setRGB('FFFFFF');
        $sheet->getStyle("B{$row}")->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()->setRGB('2E7D32');
        $row++;

        // Simpan baris awal tabel
        $skalaAwal = $row;

        // $headers = ["Realisasi (%)", "< 80%", "80% sd < 90%", "90% sd < 110%", "110% sd < 120%", "120% sd ≤ 130%"];
        // $col = 'B';
        // foreach ($headers as $h) {
        //     $sheet->setCellValue("{$col}{$row}", $h);
        //     $col++;
        // }
        $sheet->getStyle("B{$row}:G{$row}")->applyFromArray([
            'font' => ['bold' => true],
            'alignment' => ['horizontal' => 'center', 'vertical' => 'center'],
            'borders' => ['allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN]],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'baff95']
            ]
        ]);

        $skalaDetail = [
            ["Kondisi", "Tidak pernah menunjukkan perilaku ini", "Jarang menunjukkan perilaku ini", "Sering menunjukkan perilaku ini", "Sangat sering menunjukkan perilaku ini", "Setiap saat menunjukkan perilaku ini"],
            ["Yudisium/Predikat", "Minus", "Fair", "Good", "Very Good", "Excellent"],
            ["Nilai", "1", "2", "3", "4", "5"]
        ];

        foreach ($skalaDetail as $det) {
            $row++;
            $col = 'B';
            foreach ($det as $i => $cell) {
                $sheet->setCellValue("{$col}{$row}", $cell);

                // Style border + alignment
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

                // Kolom pertama diberi warna hijau dan font putih tebal
                if ($i == 0) {
                    $sheet->getStyle("{$col}{$row}")
                        ->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                        ->getStartColor()->setRGB('2E7D32');

                    $sheet->getStyle("{$col}{$row}")
                        ->getFont()->getColor()->setRGB('FFFFFF');

                    $sheet->getStyle("{$col}{$row}")
                        ->getFont()->setBold(true);
                }

                // Baris nilai → warna hijau muda
                if ($det[0] == "Nilai" && $i > 0) {
                    $sheet->getStyle("{$col}{$row}")
                        ->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                        ->getStartColor()->setRGB('baff95');
                }

                $col++;
            }
        }

        $skalaAkhir = $row;

        // =======================
        // NILAI BUDAYA PERUSAHAAN (O)
        // =======================
        $row += 2;
        $sheet->setCellValue("B{$row}", "Penilaian Budaya Perusahaan");
        $sheet->mergeCells("B{$row}:G{$row}");
        $sheet->getStyle("B{$row}")->applyFromArray([
            'font' => ['bold' => true, 'size' => 12, 'color' => ['rgb' => 'FFFFFF']],
            'alignment' => ['horizontal' => 'center', 'vertical' => 'center'],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb' => '2E7D32']
            ]
        ]);
        $row++;

        // Header tabel
        $sheet->mergeCells("D{$row}:E{$row}");
        $sheet->mergeCells("F{$row}:G{$row}");
        $sheet->setCellValue("B{$row}", "No");
        $sheet->setCellValue("C{$row}", "Perilaku Utama");
        $sheet->setCellValue("D{$row}", "Panduan Perilaku");
        $sheet->setCellValue("F{$row}", "Nilai");

        $sheet->getStyle("B{$row}:G{$row}")->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'alignment' => ['horizontal' => 'center', 'vertical' => 'center', 'wrapText' => true],
            'borders' => ['allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN]],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb' => '2E7D32']
            ]
        ]);

        $row++;
        $startTable = $row;
        $no = 1;

        // Ambil data budaya & nilai
        $budaya = $this->Nilai_model->getAllBudaya();
        $nilaiBudayaRow = $this->Nilai_model->getNilaiBudayaByPegawai($pegawai->nik, $periode_awal, $periode_akhir);
        $budaya_nilai = [];
        $rata_rata_budaya = 0;

        if ($nilaiBudayaRow) {
            $budaya_nilai = json_decode($nilaiBudayaRow->nilai_budaya, true);
            $rata_rata_budaya = $nilaiBudayaRow->rata_rata ?? 0;
        }

        if (!empty($budaya)) {
            foreach ($budaya as $b) {
                $panduanList = json_decode($b['panduan_perilaku'], true);

                if (is_array($panduanList)) {
                    foreach ($panduanList as $pIndex => $p) {
                        $nilaiKey = "budaya_{$no}_{$pIndex}";
                        $nilai = isset($budaya_nilai[$nilaiKey]) ? (int)$budaya_nilai[$nilaiKey] : 0;

                        switch ($nilai) {
                            case 1:
                                $labelNilai = "1 - Sangat Jarang";
                                $color = "FF0000";
                                break;
                            case 2:
                                $labelNilai = "2 - Jarang";
                                $color = "FFA500";
                                break;
                            case 3:
                                $labelNilai = "3 - Kadang";
                                $color = "1E88E5";
                                break;
                            case 4:
                                $labelNilai = "4 - Sering";
                                $color = "43A047";
                                break;
                            case 5:
                                $labelNilai = "5 - Selalu";
                                $color = "1E5631";
                                break;
                            default:
                                $labelNilai = "Belum Dinilai";
                                $color = "808080";
                        }

                        if ($pIndex === 0) {
                            $rowspan = count($panduanList);
                            $sheet->setCellValue("B{$row}", $no);
                            $sheet->setCellValue("C{$row}", $b['perilaku_utama']);

                            $sheet->getStyle("B{$row}")->getAlignment()
                                ->setHorizontal('center')->setVertical('center');

                            if ($rowspan > 1) {
                                $sheet->mergeCells("B{$row}:B" . ($row + $rowspan - 1));
                                $sheet->mergeCells("C{$row}:C" . ($row + $rowspan - 1));
                            }
                        }

                        $sheet->mergeCells("D{$row}:E{$row}");
                        $sheet->setCellValue("D{$row}", chr(97 + $pIndex) . ". " . $p);

                        $sheet->mergeCells("F{$row}:G{$row}");
                        $sheet->setCellValue("F{$row}", $labelNilai);
                        $sheet->getStyle("F{$row}")->getFont()->getColor()->setRGB($color);

                        $sheet->getStyle("B{$row}:G{$row}")->applyFromArray([
                            'borders' => [
                                'allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN]
                            ],
                            'alignment' => [
                                'horizontal' => 'left',
                                'vertical' => 'center',
                                'wrapText' => true
                            ]
                        ]);

                        $row++;
                    }
                    $no++;
                }
            }

            $sheet->getStyle("B{$startTable}:B" . ($row - 1))
                ->getAlignment()->setHorizontal('center')->setVertical('center');
        } else {
            $sheet->mergeCells("B{$row}:G{$row}");
            $sheet->setCellValue("B{$row}", "Data penilaian budaya belum tersedia.");
            $sheet->getStyle("B{$row}")->getAlignment()->setHorizontal('center');
            $sheet->getStyle("B{$row}")->getFont()->setItalic(true)->getColor()->setRGB('808080');
            $row++;
        }



        // =======================
        // RATA-RATA NILAI BUDAYA
        // =======================
        $sheet->mergeCells("B{$row}:E{$row}");
        $sheet->mergeCells("F{$row}:G{$row}");
        $sheet->setCellValue("B{$row}", "Rata-Rata Nilai Internalisasi Budaya");
        $sheet->setCellValue("F{$row}", number_format($rata_rata_budaya ?? 0, 2));

        $sheet->getStyle("B{$row}:G{$row}")->applyFromArray([
            'font' => ['bold' => true],
            'borders' => [
                'allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN]
            ],
            'alignment' => ['horizontal' => 'center', 'vertical' => 'center']
        ]);
        $sheet->getStyle("B{$row}:G{$row}")
            ->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()->setRGB('2E7D32');
        $sheet->getStyle("B{$row}:G{$row}")->getFont()->getColor()->setRGB('FFFFFF');

        $endTable = $row;

        // Border tebal luar
        $sheet->getStyle("B" . ($startTable - 2) . ":G{$endTable}")->applyFromArray([
            'borders' => [
                'outline' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THICK,
                    'color' => ['rgb' => '000000']
                ]
            ]
        ]);
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
                'startColor' => ['rgb' => '215d01'], // hijau tua klasik
                'endColor' => ['rgb' => '2E7D32'],   // hijau lembut elegan
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
        $kontribBudaya  = round($nilaiBudaya * 0.05, 2);  // 5%

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
                $warnaPredikat = '348cd4';
                $emojiPredikat = '🏅';
                break;
            case str_contains($predikat, 'VERY'):
                $warnaPredikat = '62bce7';
                $emojiPredikat = '🎖️';
                break;
            case str_contains($predikat, 'GOOD'):
                $warnaPredikat = '78c350';
                $emojiPredikat = '🥇';
                break;
            case str_contains($predikat, 'FAIR'):
                $warnaPredikat = 'f9982c';
                $emojiPredikat = '🥈';
                break;
            case str_contains($predikat, 'MINUS'):
                $warnaPredikat = 'f92c2c';
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

        // ✅ Tambahkan BORDER HITAM di seluruh blok (judul sampai total nilai akhir)
        $endRow = $row; // baris terakhir total nilai akhir
        $sheet->getStyle("B{$startRow}:F{$endRow}")->applyFromArray([
            'borders' => [
                'outline' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM,
                    'color' => ['rgb' => '000000']
                ]
            ]
        ]);

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


        // Tentukan posisi baris awal sejajar nilai akhir
        $row = 72; // mulai di samping bagian "NILAI AKHIR"
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
        $row = $current + 4;
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
}
