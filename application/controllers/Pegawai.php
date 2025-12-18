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
 * @property MonitoringPegawai_model $MonitoringPegawai_model
 * @property DataDiri_model $DataDiri_model
 * @property Administrator_model $Administrator_model
 * @property Ppk_model $Ppk_model
 * @property CI_Input $input
 * @property CI_Loader $load
 * @property CI_Output $output
 * @property CI_Session $session
 * @property CI_DB_query_builder $db
 * @property form_validation $form_validation
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
        $this->load->model('pegawai/MonitoringPegawai_model');
        $this->load->model('Penilaian_model');
        $this->load->model('Indikator_model');
        $this->load->model('DataDiri_model');
        $this->load->model('Administrator_model'); // Diperlukan untuk get_pegawai_history_by_date
        $this->load->model('pegawai/Ppk_model');
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

        // 🔹 Ambil daftar periode yang tersedia terlebih dahulu
        $periode_list = $this->Pegawai_model->getPeriodePegawai($nik);

        // 🔹 Tentukan periode awal dan akhir.
        // Prioritas: GET -> POST -> Logika Periode Terdekat -> Default Tahunan
        $periode_awal = $this->input->get('awal') ?? $this->input->post('periode_awal') ?? date('Y') . "-01-01";
        $periode_akhir = $this->input->get('akhir') ?? $this->input->post('periode_akhir') ?? date('Y') . "-12-31";

        // Logika baru untuk menentukan periode default (dipindahkan ke atas)
        // Cek jika tidak ada periode yang dikirim via GET atau POST
        if (!$this->input->get('awal') && !$this->input->post('periode_awal')) {
            $today = date('Y-m-d');
            $found_current_periode = false;

            // Urutkan periode dari yang terbaru untuk prioritas
            usort($periode_list, function ($a, $b) {
                return strtotime($b->periode_awal) - strtotime($a->periode_awal);
            });

            foreach ($periode_list as $p) {
                if ($today >= $p->periode_awal && $today <= $p->periode_akhir) {
                    $periode_awal = $p->periode_awal;
                    $periode_akhir = $p->periode_akhir;
                    $found_current_periode = true;
                    break; // Ambil periode pertama yang cocok
                }
            }
        }

        // 🔹 Ambil status lock dari kolom lock_input
        $lock_status = $this->Pegawai_model->getLockStatus($periode_awal, $periode_akhir);
        $lock_status2 = $this->Pegawai_model->getLockStatus2($periode_awal, $periode_akhir);

        // 🔹 Ambil status verifikasi penilaian
        $status_penilaian = $this->Penilaian_model->getStatusPenilaian($nik, $periode_awal, $periode_akhir);

        // Selalu panggil fungsi biasa untuk periode spesifik, tidak ada lagi agregasi
        $indikator = $this->Pegawai_model->get_indikator_by_jabatan_dan_unit(
            $pegawai->jabatan,
            $pegawai->unit_kerja,
            $nik,
            $periode_awal,
            $periode_akhir
        );


        // 🟢 FIX: Ambil dan gabungkan data 'status2' yang hilang dari query model
        if (!empty($indikator)) {
            $indikator_ids = array_map(function ($item) {
                return $item->id;
            }, $indikator);

            $status2_data = $this->db->select('indikator_id, status2')
                ->from('penilaian')
                ->where('nik', $nik)
                ->where('periode_awal', $periode_awal)
                ->where('periode_akhir', $periode_akhir)
                ->where_in('indikator_id', $indikator_ids)
                ->get()->result_array();

            $status2_map = array_column($status2_data, 'status2', 'indikator_id');

            foreach ($indikator as $item) {
                $item->status2 = $status2_map[$item->id] ?? null;
            }
        }

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
        $nilai_akhir  = $this->Pegawai_model->getNilaiAkhir($nik, $periode_awal, $periode_akhir);

        // 🔹 Ambil data grafik pencapaian dari tabel nilai_akhir
        $grafik_pencapaian = $this->Pegawai_model->getGrafikPencapaian($nik);

        /**
         * @var array $data
         * @property object $pegawai_detail
         * @property array $indikator_by_jabatan
         * @property string $periode_awal
         * @property string $periode_akhir
         * @property array $periode_list
         * @property object|null $nilai_akhir
         * @property bool $is_locked
         */
        // 🔹 Kirim semua data ke view
        $data = [
            'judul' => "Dashboard Pegawai",
            'pegawai_detail' => $pegawai,
            'indikator_by_jabatan' => $indikator,
            'periode_awal' => $periode_awal,
            'periode_akhir' => $periode_akhir,
            'periode_list' => $periode_list,
            'nilai_akhir' => $nilai_akhir,
            'is_locked' => $lock_status,
            'is_locked2' => $lock_status2, // 🔒 Tambahan
            'is_verified' => ($status_penilaian === 'disetujui'), // 🔒 Tambahan untuk verifikasi
            'budaya' => $budaya, // dari tabel budaya (perilaku + panduan)
            'budaya_nilai' => $budaya_nilai, // dari tabel budaya_nilai (hasil penilaian pegawai)
            'rata_rata_budaya' => $rata_rata_budaya,
            'grafik_pencapaian' => $grafik_pencapaian // 🔹 untuk line chart
        ];

        // 🔹 Load view
        $this->load->view('layoutpegawai/header', $data);
        $this->load->view('pegawai/index', $data);
        $this->load->view('layoutpegawai/footer');
    }

    public function simpanPenilaianBaris()
    {
        $nik = $this->session->userdata('nik');

        // Ambil semua input dari fetch()
        $indikator_id = $this->input->post('indikator_id');
        $target = $this->input->post('target');
        $batas_waktu = $this->input->post('batas_waktu');
        $realisasi = $this->input->post('realisasi');
        $pencapaian = $this->input->post('pencapaian');
        $nilai = $this->input->post('nilai');
        $nilai_dibobot = $this->input->post('nilai_dibobot');
        $periode_awal = $this->input->post('periode_awal') ?? date('Y') . "-01-01";
        $periode_akhir = $this->input->post('periode_akhir') ?? date('Y') . "-12-31";

        // Pastikan variabel utama tidak kosong
        if (empty($indikator_id) || empty($nik)) {
            echo json_encode([
                'status' => 'error',
                'message' => 'Data tidak lengkap (indikator_id atau nik kosong).'
            ]);
            return;
        }

        // Simpan atau update penilaian
        $save = $this->Pegawai_model->save_penilaian(
            $nik,
            $indikator_id,
            $target,
            $batas_waktu,
            $realisasi,
            $periode_awal,
            $periode_akhir
        );

        if ($save) {
            // Opsional: update kolom tambahan (pencapaian, nilai, nilai_dibobot) jika memang ada di tabel penilaian
            $this->db->where('nik', $nik)
                ->where('indikator_id', $indikator_id)
                ->where('periode_awal', $periode_awal)
                ->where('periode_akhir', $periode_akhir)
                ->update('penilaian', [
                    'pencapaian' => $pencapaian,
                    'nilai' => $nilai,
                    'nilai_dibobot' => $nilai_dibobot
                ]);

            echo json_encode([
                'status' => 'success',
                'message' => 'Penilaian berhasil disimpan!',
                'data' => compact(
                    'indikator_id',
                    'target',
                    'batas_waktu',
                    'realisasi',
                    'pencapaian',
                    'nilai',
                    'nilai_dibobot',
                    'periode_awal',
                    'periode_akhir'
                )
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
        $share_kpi_value = $this->input->post('share_kpi_value');
        $bobot_sasaran  = $this->input->post('bobot_sasaran');
        $bobot_budaya   = $this->input->post('bobot_budaya');
        $bobot_share_kpi = $this->input->post('bobot_share_kpi');
        $total_nilai   = $this->input->post('total_nilai');
        $fraud         = $this->input->post('fraud');
        $nilai_akhir   = $this->input->post('nilai_akhir');
        $pencapaian    = $this->input->post('pencapaian');
        $predikat      = $this->input->post('predikat');
        $periode_awal  = $this->input->post('periode_awal');
        $periode_akhir = $this->input->post('periode_akhir');
        // Koefisien adalah readonly field di view, jadi tidak perlu update (biarkan null agar tidak mengubah nilai lama)
        $koefisien     = null;

        $save = $this->Pegawai_model->save_nilai_akhir(
            $nik,
            $nilai_sasaran,
            $nilai_budaya,
            $share_kpi_value,
            $total_nilai,
            $bobot_sasaran,
            $bobot_budaya,
            $bobot_share_kpi,
            $fraud,
            $nilai_akhir,
            $pencapaian,
            $predikat,
            $periode_awal,
            $periode_akhir,
            $koefisien
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

    // ============== Ubah Penilai ==============
    /**
     * AJAX endpoint: ambil kandidat penilai berdasarkan penilai_mapping untuk jabatan pegawai
     * URL: /Pegawai/getPenilaiCandidates/{nik}
     */
    public function getPenilaiCandidates($nik)
    {
        header('Content-Type: application/json');

        if (empty($nik)) {
            echo json_encode(['status' => 'error', 'message' => 'NIK tidak diberikan']);
            return;
        }

        $this->load->model('PenilaiMapping_model');
        $this->load->model('pegawai/Pegawai_model');

        $target = $this->Pegawai_model->getPegawaiByNIK($nik);
        if (!$target) {
            echo json_encode(['status' => 'error', 'message' => 'Pegawai tidak ditemukan']);
            return;
        }

        $mapping = $this->PenilaiMapping_model->getMappingByJabatanUnit($target->jabatan, $target->unit_kerja);

        $result = [
            'status' => 'ok',
            'target' => [
                'nik' => $target->nik,
                'nama' => $target->nama,
                'jabatan' => $target->jabatan,
                'unit_kerja' => $target->unit_kerja,
                'unit_kantor' => $target->unit_kantor
            ],
            'mapping' => null,
            'candidates1' => [],
            'candidates2' => []
        ];

        if ($mapping) {
            $result['mapping'] = $mapping;

            // Jika mapping ada, ambil semua pegawai aktif yang jabatan-nya ada di penilai_mapping
            // untuk kode_cabang yang sama dan unit_kerja yang sama. Sertakan pm.key.
            $kode_cabang = $mapping->kode_cabang ?? null;

            if ($kode_cabang) {
                // Tampilkan kandidat dari dua sumber:
                // 1) mapping yang sama kode_cabang + unit_kerja dan pm.key < key target
                // 2) mapping dengan special keys (3,3a,3b,3c,3d,15) dari manapun (walaupun beda kode_cabang/kode_unit)
                // Gunakan UNION untuk menggabungkan dan menghilangkan duplikat.
                $special_keys = ['3', '3a', '3b', '3c', '3d', '15'];
                $in_list = "'" . implode("','", $special_keys) . "'"; // select pm_key and helper columns pm_key_num (numeric prefix) and pm_key_len to allow numeric-aware ordering
                $sql = "(
                                                                        SELECT p.nik, p.nama, p.jabatan, p.unit_kerja, p.unit_kantor, pm.`key` as pm_key,
                                                                                     CAST(pm.`key` AS UNSIGNED) AS pm_key_num, LENGTH(pm.`key`) AS pm_key_len
                                                                        FROM pegawai p
                                                                        INNER JOIN penilai_mapping pm
                                                                                ON pm.unit_kerja = p.unit_kerja
                                                                             AND pm.jabatan = p.jabatan
                                                                        WHERE pm.kode_cabang = ?
                                                                            AND pm.`key` < ?
                                                                            AND p.status = 'aktif'
                                                                            AND p.unit_kerja = ?
                                                                )
                                                                UNION
                                                                (
                                                                        SELECT p.nik, p.nama, p.jabatan, p.unit_kerja, p.unit_kantor, pm.`key` as pm_key,
                                                                                     CAST(pm.`key` AS UNSIGNED) AS pm_key_num, LENGTH(pm.`key`) AS pm_key_len
                                                                        FROM pegawai p
                                                                        INNER JOIN penilai_mapping pm
                                                                                ON pm.unit_kerja = p.unit_kerja
                                                                             AND pm.jabatan = p.jabatan
                                                                        WHERE pm.`key` IN ($in_list)
                                                                            AND p.status = 'aktif'
                                                                )
                                                                ORDER BY pm_key_num ASC, pm_key_len ASC, pm_key ASC, jabatan, nama";

                // Bind params for the first SELECT: kode_cabang, mapping->key, unit_kerja
                $query = $this->db->query($sql, [$kode_cabang, $mapping->key, $target->unit_kerja]);
                $candidates = $query->result();

                // gunakan daftar kandidat yang sama untuk penilai1 & penilai2 (admin akan memilih siapa jadi 1/2)
                $result['candidates1'] = $candidates;
                $result['candidates2'] = $candidates;
            } else {
                // fallback: ambil semua pegawai di unit yang sama
                $result['candidates1'] = $this->Pegawai_model->getPegawaiByUnit($target->unit_kerja, $target->unit_kantor, $target->nik);
                $result['candidates2'] = $result['candidates1'];
            }
        } else {
            // fallback: ambil semua pegawai di unit yang sama
            $result['candidates1'] = $this->Pegawai_model->getPegawaiByUnit($target->unit_kerja, $target->unit_kantor, $target->nik);
            $result['candidates2'] = $result['candidates1'];
        }

        echo json_encode($result);
    }

    /**
     * Form post handler: update penilai 1 atau 2 untuk pegawai
     */
    public function updatePenilai()
    {
        $nik_pegawai = $this->input->post('nik_pegawai');
        $tipe = $this->input->post('tipe_penilai'); // '1' atau '2'
        // frontend may send either a mapping key (pm_key) or a pegawai.nik
        $penilai_nik = $this->input->post('penilai_nik');

        if (empty($nik_pegawai) || empty($tipe) || empty($penilai_nik)) {
            $this->session->set_flashdata('message', ['type' => 'error', 'text' => 'Data tidak lengkap']);
            redirect($_SERVER['HTTP_REFERER'] ?? base_url('Pegawai'));
            return;
        }

        $this->load->model('pegawai/Pegawai_model');
        $this->load->model('PenilaiMapping_model');

        // Ambil data pegawai target
        $target = $this->Pegawai_model->getPegawaiByNIK($nik_pegawai);
        if (!$target) {
            $this->session->set_flashdata('message', ['type' => 'error', 'text' => 'Pegawai target tidak ditemukan']);
            redirect($_SERVER['HTTP_REFERER'] ?? base_url('Pegawai'));
            return;
        }

        // Ambil mapping row untuk target pegawai
        $mapping = $this->PenilaiMapping_model->getMappingByJabatanUnit($target->jabatan, $target->unit_kerja);
        if (!$mapping) {
            $this->session->set_flashdata('message', ['type' => 'error', 'text' => 'Mapping penilai untuk jabatan/unit tidak ditemukan']);
            redirect($_SERVER['HTTP_REFERER'] ?? base_url('Pegawai'));
            return;
        }

        $selected_key = $penilai_nik; // may be pm_key or nik

        // Jika nilai yang dikirim adalah sebuah key yang valid di penilai_mapping, gunakan langsung
        $exists_key = $this->PenilaiMapping_model->getJabatanByKey($selected_key);

        if (!$exists_key) {
            // Bisa jadi yang dikirim adalah NIK pegawai; coba resolve menjadi mapping.key
            $sel_pegawai = $this->Pegawai_model->getPegawaiByNIK($penilai_nik);
            if ($sel_pegawai) {
                // Cari key berdasarkan jabatan pegawai yang dipilih dan kode_unit dari mapping target
                $kode_unit = $mapping->kode_unit ?? null;
                $resolved = $this->PenilaiMapping_model->getKeyByJabatanAndUnit($sel_pegawai->jabatan, $kode_unit);
                if ($resolved) {
                    $selected_key = $resolved;
                } else {
                    // fallback: tidak bisa resolve
                    $this->session->set_flashdata('message', ['type' => 'error', 'text' => 'Gagal menentukan key mapping untuk penilai yang dipilih']);
                    redirect($_SERVER['HTTP_REFERER'] ?? base_url('Pegawai'));
                    return;
                }
            } else {
                $this->session->set_flashdata('message', ['type' => 'error', 'text' => 'Data penilai tidak ditemukan']);
                redirect($_SERVER['HTTP_REFERER'] ?? base_url('Pegawai'));
                return;
            }
        }

        // Update penilai di tabel penilai_mapping untuk jabatan/unit target
        $ok = $this->PenilaiMapping_model->updatePenilaiForJabatanUnit($target->jabatan, $target->unit_kerja, $tipe, $selected_key);

        if ($ok) {
            $this->session->set_flashdata('message', ['type' => 'success', 'text' => 'Penilai mapping berhasil diupdate']);
        } else {
            $this->session->set_flashdata('message', ['type' => 'error', 'text' => 'Gagal mengupdate mapping penilai']);
        }

        redirect($_SERVER['HTTP_REFERER'] ?? base_url('Pegawai'));
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
        $this->load->model('pegawai/Nilai_model');
        $this->load->model('Pegawai_model');

        // 🔹 Ambil daftar periode yang tersedia terlebih dahulu
        $this->db->select('periode_awal, periode_akhir');
        $this->db->from('penilaian');
        $this->db->where('nik', $nik);
        $this->db->group_by(['periode_awal', 'periode_akhir']);
        $this->db->order_by('periode_awal', 'DESC'); // Terbaru dulu
        $periode_list = $this->db->get()->result();

        // 🔹 Tentukan periode awal dan akhir.
        $awal = $this->input->get('awal');
        $akhir = $this->input->get('akhir');

        // Logika baru untuk menentukan periode default jika tidak ada di URL
        if (!$awal || !$akhir) {
            $today = date('Y-m-d');
            $found_current_periode = false;

            foreach ($periode_list as $p) {
                if ($today >= $p->periode_awal && $today <= $p->periode_akhir) {
                    $awal = $p->periode_awal;
                    $akhir = $p->periode_akhir;
                    $found_current_periode = true;
                    break; // Ambil periode pertama yang cocok
                }
            }
            // Fallback jika tidak ada periode yang cocok
            if (!$found_current_periode) {
                $awal = date('Y-01-01');
                $akhir = date('Y-12-31');
            }
        }

        $pegawai = $this->Nilai_model->getPegawaiWithPenilai($nik);
        $indikator = $this->Nilai_model->getIndikatorPegawai($nik, $awal, $akhir);

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

    public function nilaiPegawaiDetail2($nik)
    {
        $this->load->model('pegawai/Nilai_model');
        $this->load->model('Pegawai_model');

        // 🔹 Ambil daftar periode yang tersedia terlebih dahulu
        $this->db->select('periode_awal, periode_akhir');
        $this->db->from('penilaian');
        $this->db->where('nik', $nik);
        $this->db->group_by(['periode_awal', 'periode_akhir']);
        $this->db->order_by('periode_awal', 'DESC'); // Terbaru dulu
        $periode_list = $this->db->get()->result();

        // 🔹 Tentukan periode awal dan akhir.
        $awal = $this->input->get('awal');
        $akhir = $this->input->get('akhir');

        // Logika baru untuk menentukan periode default jika tidak ada di URL
        if (!$awal || !$akhir) {
            $today = date('Y-m-d');
            $found_current_periode = false;

            foreach ($periode_list as $p) {
                if ($today >= $p->periode_awal && $today <= $p->periode_akhir) {
                    $awal = $p->periode_awal;
                    $akhir = $p->periode_akhir;
                    $found_current_periode = true;
                    break; // Ambil periode pertama yang cocok
                }
            }
            // Fallback jika tidak ada periode yang cocok
            if (!$found_current_periode) {
                $awal = date('Y-01-01');
                $akhir = date('Y-12-31');
            }
        }

        $pegawai = $this->Nilai_model->getPegawaiWithPenilai($nik);
        $indikator = $this->Nilai_model->getIndikatorPegawai($nik, $awal, $akhir);

        // 🟢 FIX: Ambil dan gabungkan data 'status2' yang hilang dari query model
        if (!empty($indikator)) {
            $indikator_ids = array_map(function ($item) {
                return $item->id;
            }, $indikator);

            $status2_data = $this->db->select('indikator_id, status2')
                ->from('penilaian')
                ->where('nik', $nik)
                ->where('periode_awal', $awal)
                ->where('periode_akhir', $akhir)
                ->where_in('indikator_id', $indikator_ids)
                ->get()->result_array();

            $status2_map = array_column($status2_data, 'status2', 'indikator_id');

            foreach ($indikator as $item) {
                if (isset($status2_map[$item->id])) $item->status2 = $status2_map[$item->id];
            }
        }

        $nilai_akhir = $this->Pegawai_model->getNilaiAkhir($nik, $awal, $akhir);
        $is_locked   = $this->Nilai_model->getLockStatus($nik, $awal, $akhir);
        $budaya      = $this->Nilai_model->getAllBudaya();
        $nilaiBudayaDB = $this->Nilai_model->getNilaiBudayaByPegawai($nik, $awal, $akhir);

        $budaya_nilai = [];
        if (!empty($nilaiBudayaDB) && !empty($nilaiBudayaDB->nilai_budaya)) {
            $budaya_nilai = json_decode($nilaiBudayaDB->nilai_budaya, true);
        }

        $data = [
            'judul' => "Form Penilaian Pegawai (Penilai 2)",
            'pegawai_detail' => $pegawai,
            'indikator_by_jabatan' => $indikator,
            'periode_awal' => $awal,
            'periode_akhir' => $akhir,
            'periode_list' => $periode_list,
            'nilai_akhir' => $nilai_akhir,
            'is_locked' => $is_locked,
            'budaya' => $budaya,
            'budaya_nilai' => $budaya_nilai,
            'rata_rata_budaya' => $nilaiBudayaDB->rata_rata ?? 0
        ];

        $this->load->view('layoutpegawai/header', $data);
        $this->load->view('pegawai/nilaipegawai_detail2', $data); // load view penilai2
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
                    $sheet->setCellValue(
                        "F{$row}",
                        ($i->target >= 1000) ? 'Rp. ' . number_format($i->target, 0, ',', '.') : $i->target
                    );
                    $sheet->setCellValue("G{$row}", $i->batas_waktu);
                    $sheet->setCellValue(
                        "H{$row}",
                        ($i->realisasi >= 1000) ? 'Rp. ' . number_format($i->realisasi, 0, ',', '.') : $i->realisasi
                    );
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
                'borders' => ['allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN]],
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

    public function updateStatusAllPenilai2()
    {
        header('Content-Type: application/json; charset=utf-8');

        $ids = $this->input->post('ids');
        $status = $this->input->post('status');

        if (empty($ids) || $status === null) {
            echo json_encode(['success' => false, 'message' => 'Data tidak lengkap']);
            return;
        }

        $ids_array = array_filter(array_map('trim', explode(',', $ids)));
        if (empty($ids_array)) {
            echo json_encode(['success' => false, 'message' => 'Tidak ada id valid']);
            return;
        }

        $this->load->model('pegawai/Nilai_model');

        $penilai2_nik = $this->session->userdata('nik') ?? null;

        $ok = $this->Nilai_model->updateStatusAllPenilai2($ids_array, $status, $penilai2_nik);

        if ($ok) {
            echo json_encode(['success' => true, 'message' => 'Semua status2 berhasil diupdate']);
        } else {
            $dbErr = $this->db->error();
            $msg = 'Gagal update status2';
            if (!empty($dbErr['message'])) $msg .= ': ' . $dbErr['message'];
            echo json_encode(['success' => false, 'message' => $msg, 'db' => $dbErr]);
        }
    }

    // ==== Halaman Rekap Nilai Pegawai per Tahun ====
    public function rekapNilaiPegawai()
    {
        $nik = $this->session->userdata('nik');

        if (!$nik) {
            show_error('Anda belum login sebagai pegawai.', 401);
        }

        // Ambil data rekap per tahun dari model Pegawai_model
        $rekap = $this->Pegawai_model->getRekapNilaiTahunan($nik);

        // Ambil SEMUA riwayat jabatan yang sudah tidak aktif
        $riwayat_jabatan = $this->Pegawai_model->getRiwayatJabatanNonAktif($nik);

        // Ambil data jabatan sekarang
        $jabatan_sekarang = $this->db->select('jabatan, unit_kerja, unit_kantor')
            ->from('pegawai')
            ->where('nik', $nik)
            ->get()->row();

        $data = [
            'judul' => 'Rekap Nilai Pegawai',
            'rekap' => $rekap,
            'riwayat_jabatan' => $riwayat_jabatan, // Kirim data riwayat ke view
            'jabatan_sekarang' => $jabatan_sekarang // Kirim data jabatan sekarang ke view
        ];

        // Load layout view
        $this->load->view('layoutpegawai/header', $data);
        $this->load->view('pegawai/rekap_nilai', $data);
        $this->load->view('layoutpegawai/footer');
    }

    // ==== Halaman Monitoring Kinerja Individu Bulanan ====
    public function monitoringIndividu()
    {
        $data['judul'] = "Monitoring Kinerja Bulanan";

        $nik   = $this->session->userdata('nik');
        $tahun = $this->input->get('tahun') ?? $this->input->post('tahun') ?? date('Y');
        $bulan = $this->input->get('bulan') ?? $this->input->post('bulan') ?? date('m');

        $data['periode_awal']  = "$tahun-$bulan-01";
        $data['periode_akhir'] = date('Y-m-t', strtotime($data['periode_awal']));
        $data['tahun_dipilih'] = $tahun;
        $data['bulan_dipilih'] = $bulan;
        $data['tahun_list'] = $this->MonitoringPegawai_model->getTahunList();

        // 🔹 Ambil data untuk chart: seluruh bulan
        $monitoring_bulanan_tahun = $this->MonitoringPegawai_model->getMonitoringBulananTahun($nik, $tahun);

        // 🔹 Hitung ulang pencapaian_akhir jika kosong atau 0
        foreach ($monitoring_bulanan_tahun as $mb) {
            if (empty($mb->pencapaian_akhir) || $mb->pencapaian_akhir == 0) {
                $json = json_decode($mb->data_json, true) ?: [];
                $totalBobot = 0;
                $totalNilaiDibobot = 0;
                foreach ($json as $item) {
                    $totalBobot += floatval($item['bobot'] ?? 0);
                    $totalNilaiDibobot += floatval($item['nilai_dibobot'] ?? $item['nilai'] ?? 0);
                }
                $mb->pencapaian_akhir = $totalBobot ? round($totalNilaiDibobot / $totalBobot, 2) : 0;
            }
        }

        $data['monitoring_bulanan_tahun'] = $monitoring_bulanan_tahun;

        if (!$nik) {
            $data += [
                'pegawai_detail' => null,
                'penilaian_pegawai' => [],
                'nilai_akhir' => null,
                'message' => ['type' => 'error', 'text' => 'NIK tidak ditemukan di session.']
            ];
        } else {
            $pegawai = $this->MonitoringPegawai_model->getPegawaiWithPenilai($nik);

            if ($pegawai) {
                $monitoring_bulanan = $this->MonitoringPegawai_model->getMonitoringBulanan($nik, $bulan, $tahun);

                $awal_tahun = "$tahun-10-01";
                $akhir_tahun = "$tahun-12-31";
                $bulanSekarang = (int)$bulan;

                if ($monitoring_bulanan) {
                    $stored = json_decode($monitoring_bulanan->data_json, true) ?: [];
                    $storedMap = [];
                    foreach ($stored as $s) {
                        $key = isset($s['indikator_id']) ? (string)$s['indikator_id'] : (isset($s['id']) ? (string)$s['id'] : null);
                        if ($key !== null) $storedMap[$key] = $s;
                    }

                    $indicators = $this->MonitoringPegawai_model->get_indikator_by_jabatan_dan_unit(
                        $pegawai->jabatan,
                        $pegawai->unit_kerja,
                        $nik,
                        $awal_tahun,
                        $akhir_tahun
                    );

                    $penilaian_bulanan = [];
                    foreach ($indicators as $ind) {
                        $idKey = (string)($ind->id ?? $ind->indikator_id ?? '');
                        $row = new stdClass();
                        $row->id = $ind->id ?? $ind->indikator_id ?? null;
                        $row->indikator = $ind->indikator ?? '';
                        $row->perspektif = $ind->perspektif ?? '';
                        $row->sasaran_kerja = $ind->sasaran_kerja ?? '';
                        $row->bobot = isset($ind->bobot) ? floatval($ind->bobot) : 0;

                        // ✅ Target akumulatif
                        $targetTahunan = isset($ind->target) ? (float)$ind->target : 0;
                        $row->target = $targetTahunan > 0 ? round(($targetTahunan / 12) * $bulanSekarang, 2) : 0;

                        // // Override target jika ada di stored data
                        // if (isset($storedMap[$idKey]['target'])) {
                        //     $row->target = floatval($storedMap[$idKey]['target']);
                        // }

                        $row->batas_waktu = $ind->batas_waktu ?? '';
                        $row->realisasi = isset($storedMap[$idKey]['realisasi']) ? floatval($storedMap[$idKey]['realisasi']) : 0;
                        $row->pencapaian = isset($storedMap[$idKey]['pencapaian']) ? floatval($storedMap[$idKey]['pencapaian']) : 0;
                        $row->nilai = isset($storedMap[$idKey]['nilai']) ? floatval($storedMap[$idKey]['nilai']) : 0;
                        $row->nilai_dibobot = isset($storedMap[$idKey]['nilai_dibobot'])
                            ? floatval($storedMap[$idKey]['nilai_dibobot'])
                            : $row->nilai;

                        $penilaian_bulanan[] = $row;
                    }

                    $nilai_akhir = $monitoring_bulanan->nilai_akhir;
                } else {
                    // 🔹 Jika bulan berjalan kosong, tetap gunakan target akumulatif
                    $penilaian_tahunan = $this->MonitoringPegawai_model->get_indikator_by_jabatan_dan_unit(
                        $pegawai->jabatan,
                        $pegawai->unit_kerja,
                        $nik,
                        $awal_tahun,
                        $akhir_tahun
                    );

                    $penilaian_bulanan = [];
                    foreach ($penilaian_tahunan as $p) {
                        $targetTahunan = $p->target ? (float)$p->target : 0;
                        $p->target = $targetTahunan > 0 ? round(($targetTahunan / 12) * $bulanSekarang, 2) : 0;
                        $p->realisasi = 0;
                        $p->pencapaian = 0;
                        $p->nilai = 0;
                        $p->nilai_dibobot = 0;
                        $penilaian_bulanan[] = $p;
                    }
                    $nilai_akhir = 0;
                }

                $budayaData = $this->MonitoringPegawai_model->getBudayaNilaiByNik($nik, $awal_tahun, $akhir_tahun);

                // ✅ Ambil data fraud & koefisien dari tabel nilai_akhir
                $tahun = date('Y', strtotime($awal_tahun));
                $nilaiAkhirData = $this->MonitoringPegawai_model->getNilaiAkhir($nik, "$tahun-10-01", "$tahun-12-31");
                $nilai_budaya = $nilaiAkhirData->nilai_budaya ?? 0;
                $share_kpi_value = $nilaiAkhirData->share_kpi_value ?? 0;
                $bobot_sasaran = $nilaiAkhirData->bobot_sasaran ?? 95;
                $bobot_budaya = $nilaiAkhirData->bobot_budaya ?? 5;
                $bobot_share_kpi = $nilaiAkhirData->bobot_share_kpi ?? 0;
                $fraud = $nilaiAkhirData->fraud ?? 0;
                $koefisien = $nilaiAkhirData->koefisien ?? 100;

                $data += [
                    'pegawai_detail' => $pegawai,
                    'penilaian_pegawai' => $penilaian_bulanan,
                    'nilai_akhir' => $nilai_akhir,
                    'nilai_budaya' => $nilai_budaya,
                    'share_kpi_value' => $share_kpi_value,
                    'bobot_sasaran' => $bobot_sasaran,
                    'bobot_budaya' => $bobot_budaya,
                    'bobot_share_kpi' => $bobot_share_kpi,
                    'fraud' => $fraud,
                    'koefisien' => $koefisien,
                    'budaya_nilai' => $budayaData['nilai_budaya'],
                    'rata_rata_budaya' => $budayaData['rata_rata'],
                    'budaya' => $this->MonitoringPegawai_model->getAllBudaya(),
                    'message' => ['type' => 'success', 'text' => 'Data monitoring bulanan siap ditampilkan!']
                ];
            }
        }

        $this->load->view("layout/header");
        $this->load->view('pegawai/monitoringindividu', $data);
        $this->load->view("layout/footer");
    }

    /**
     * Ajax: ambil penilaian berdasarkan periode yang dipilih
     */
    public function cariPenilaianBulanan()
    {
        $nik = $this->session->userdata('nik');
        if (!$nik) {
            show_error('NIK tidak ditemukan di session. Pastikan Anda sudah login.');
            return;
        }

        $tahun = $this->input->post('tahun') ?? $this->input->get('tahun') ?? date('Y');
        $bulan = $this->input->post('bulan') ?? $this->input->get('bulan') ?? date('m');

        $periode_awal = "$tahun-$bulan-01";
        $periode_akhir = date('Y-m-t', strtotime($periode_awal));
        $data['tahun_dipilih'] = $tahun;
        $data['bulan_dipilih'] = $bulan;

        $bulanSekarang = (int)$bulan;

        $pegawai = $this->MonitoringPegawai_model->getPegawaiWithPenilai($nik);

        if ($pegawai) {
            $monitoring_bulanan = $this->MonitoringPegawai_model->getMonitoringBulanan($nik, $bulan, $tahun);

            if ($monitoring_bulanan) {
                $stored = json_decode($monitoring_bulanan->data_json, true) ?: [];
                $storedMap = [];
                foreach ($stored as $s) {
                    $key = isset($s['indikator_id']) ? (string)$s['indikator_id'] : (isset($s['id']) ? (string)$s['id'] : null);
                    if ($key !== null) $storedMap[$key] = $s;
                }

                $awal_tahun  = "$tahun-10-01";
                $akhir_tahun = "$tahun-12-31";
                $indicators = $this->MonitoringPegawai_model->get_indikator_by_jabatan_dan_unit(
                    $pegawai->jabatan,
                    $pegawai->unit_kerja,
                    $nik,
                    $awal_tahun,
                    $akhir_tahun
                );

                // ✅ Ambil data fraud & koefisien dari tabel nilai_akhir
                $tahun = date('Y', strtotime($periode_awal));
                $nilaiAkhirData = $this->MonitoringPegawai_model->getNilaiAkhir($nik, "$tahun-01-01", "$tahun-12-31");
                $nilai_budaya = $nilaiAkhirData->nilai_budaya ?? 0;
                $fraud = $nilaiAkhirData->fraud ?? 0;
                $koefisien = $nilaiAkhirData->koefisien ?? 100;


                $penilaian_bulanan = [];
                foreach ($indicators as $ind) {
                    $idKey = (string)($ind->id ?? $ind->indikator_id ?? '');
                    $row = new stdClass();
                    $row->id = $ind->id ?? $ind->indikator_id ?? null;
                    $row->indikator = $ind->indikator ?? '';
                    $row->perspektif = $ind->perspektif ?? '';
                    $row->sasaran_kerja = $ind->sasaran_kerja ?? '';
                    $row->bobot = isset($ind->bobot) ? floatval($ind->bobot) : 0;

                    // ✅ Target akumulatif
                    $targetTahunan = isset($ind->target) ? (float)$ind->target : 0;
                    $row->target = $targetTahunan > 0 ? round(($targetTahunan / 12) * $bulanSekarang, 2) : 0;

                    // if (isset($storedMap[$idKey]['target'])) {
                    //     $row->target = floatval($storedMap[$idKey]['target']);
                    // }

                    $row->batas_waktu = $ind->batas_waktu ?? '';
                    $row->realisasi = isset($storedMap[$idKey]['realisasi']) ? floatval($storedMap[$idKey]['realisasi']) : 0;
                    $row->pencapaian = isset($storedMap[$idKey]['pencapaian']) ? floatval($storedMap[$idKey]['pencapaian']) : 0;
                    $row->nilai = isset($storedMap[$idKey]['nilai']) ? floatval($storedMap[$idKey]['nilai']) : 0;
                    $row->nilai_dibobot = isset($storedMap[$idKey]['nilai_dibobot'])
                        ? floatval($storedMap[$idKey]['nilai_dibobot'])
                        : $row->nilai;

                    $penilaian_bulanan[] = $row;
                }

                $nilai_akhir = $monitoring_bulanan->nilai_akhir;
            } else {
                // Jika belum ada data bulan itu, buat default 0 tapi tetap akumulatif
                $awal_tahun = "$tahun-10-01";
                $akhir_tahun = "$tahun-12-31";
                $penilaian_tahunan = $this->MonitoringPegawai_model->get_indikator_by_jabatan_dan_unit(
                    $pegawai->jabatan,
                    $pegawai->unit_kerja,
                    $nik,
                    $awal_tahun,
                    $akhir_tahun
                );

                $penilaian_bulanan = [];
                foreach ($penilaian_tahunan as $p) {
                    $targetTahunan = $p->target ? (float)$p->target : 0;
                    $p->target = $targetTahunan > 0 ? round(($targetTahunan / 12) * $bulanSekarang, 2) : 0;
                    $p->realisasi = 0;
                    $p->pencapaian = 0;
                    $p->nilai = 0;
                    $p->nilai_dibobot = 0;
                    $penilaian_bulanan[] = $p;
                }
                $nilai_akhir = 0;
            }

            $budayaData = $this->MonitoringPegawai_model->getBudayaNilaiByNik($nik, "$tahun-01-01", "$tahun-12-31");
            $monitoring_bulanan_tahun = $this->MonitoringPegawai_model->getMonitoringBulananTahun($nik, $tahun);

            foreach ($monitoring_bulanan_tahun as $mb) {
                if (empty($mb->pencapaian_akhir) || $mb->pencapaian_akhir == 0) {
                    $json = json_decode($mb->data_json, true) ?: [];
                    $totalBobot = 0;
                    $totalNilaiDibobot = 0;
                    foreach ($json as $item) {
                        $totalBobot += floatval($item['bobot'] ?? 0);
                        $totalNilaiDibobot += floatval($item['nilai_dibobot'] ?? $item['nilai'] ?? 0);
                    }
                    $mb->pencapaian_akhir = $totalBobot ? round($totalNilaiDibobot / $totalBobot, 2) : 0;
                }
            }

            // ✅ Ambil data fraud & koefisien dari tabel nilai_akhir
            $tahun = date('Y', strtotime($periode_awal));
            $nilaiAkhirData = $this->MonitoringPegawai_model->getNilaiAkhir($nik, "$tahun-10-01", "$tahun-12-31");
            $nilai_budaya = $nilaiAkhirData->nilai_budaya ?? 0;
            $share_kpi_value = $nilaiAkhirData->share_kpi_value ?? 0;
            $bobot_sasaran = $nilaiAkhirData->bobot_sasaran ?? 95;
            $bobot_budaya = $nilaiAkhirData->bobot_budaya ?? 5;
            $bobot_share_kpi = $nilaiAkhirData->bobot_share_kpi ?? 0;
            $fraud = $nilaiAkhirData->fraud ?? 0;
            $koefisien = $nilaiAkhirData->koefisien ?? 100;

            $data = [
                'judul' => 'Monitoring Kinerja Bulanan',
                'pegawai_detail' => $pegawai,
                'penilaian_pegawai' => $penilaian_bulanan,
                'nilai_akhir' => $nilai_akhir,
                'budaya_nilai' => $budayaData['nilai_budaya'],
                'rata_rata_budaya' => $budayaData['rata_rata'],
                'budaya' => $this->MonitoringPegawai_model->getAllBudaya(),
                'nilai_budaya' => $nilai_budaya,
                'share_kpi_value' => $share_kpi_value,
                'bobot_sasaran' => $bobot_sasaran,
                'bobot_budaya' => $bobot_budaya,
                'bobot_share_kpi' => $bobot_share_kpi,
                'fraud' => $fraud,
                'koefisien' => $koefisien,
                'tahun_dipilih' => $tahun,
                'bulan_dipilih' => $bulan,
                'tahun_list' => $this->MonitoringPegawai_model->getTahunList(),
                'periode_awal' => $periode_awal,
                'periode_akhir' => $periode_akhir,
                'monitoring_bulanan_tahun' => $monitoring_bulanan_tahun,
                'message' => ['type' => 'success', 'text' => "Data monitoring bulan {$bulan} berhasil ditampilkan!"]
            ];
        } else {
            $data = [
                'judul' => 'Monitoring Kinerja Bulanan',
                'pegawai_detail' => null,
                'penilaian_pegawai' => [],
                'nilai_akhir' => null,
                'tahun_list' => $this->MonitoringPegawai_model->getTahunList(),
                'bulan_dipilih' => $bulan,
                'tahun_dipilih' => $tahun,
                'message' => ['type' => 'error', 'text' => 'Data pegawai tidak ditemukan.']
            ];
        }

        $this->load->view("layout/header");
        $this->load->view('pegawai/monitoringindividu', $data);
        $this->load->view("layout/footer");
    }

    /**
     * ✅ Ajax autosave — dipanggil setiap kali user ubah realisasi
     */
    public function simpanMonitoringBulanan()
    {
        $json = file_get_contents('php://input');
        $data = json_decode($json, true);

        if (!is_array($data)) {
            echo json_encode(['status' => 'failed', 'msg' => 'Invalid payload']);
            return;
        }

        $nik       = $data['nik'] ?? null;
        $bulan     = $data['bulan'] ?? null;
        $tahun     = $data['tahun'] ?? null;
        $indikator = $data['indikator'] ?? null;
        $rowData   = $data['nilaiData'] ?? null;

        $nilai_akhir_value = floatval($data['nilai_akhir_value'] ?? 0);
        $pencapaian_akhir  = floatval($data['pencapaian_pct'] ?? 0);
        $predikat          = $data['predikat'] ?? null;

        if (!$nik || !$bulan || !$tahun || !$indikator || !is_array($rowData)) {
            echo json_encode(['status' => 'failed', 'msg' => 'Data tidak lengkap']);
            return;
        }

        // sanitize numeric
        $rowData['target'] = floatval($rowData['target'] ?? 0);
        $rowData['realisasi'] = floatval($rowData['realisasi'] ?? 0);
        $rowData['bobot'] = floatval($rowData['bobot'] ?? 0);
        $rowData['pencapaian'] = floatval($rowData['pencapaian'] ?? 0);
        $rowData['nilai'] = floatval($rowData['nilai'] ?? 0);
        $rowData['nilai_dibobot'] = floatval($rowData['nilai_dibobot'] ?? 0);

        // Ambil existing monitoring_bulanan
        $existing = $this->MonitoringPegawai_model->getMonitoringBulanan($nik, $bulan, $tahun);
        $data_json = [];

        if ($existing) {
            $data_json = json_decode($existing->data_json, true) ?: [];
            $found = false;
            foreach ($data_json as &$it) {
                if (($it['indikator_id'] ?? null) == $indikator) {
                    $it = array_merge($it, $rowData);
                    $it['indikator_id'] = $indikator;
                    $found = true;
                    break;
                }
            }
            if (!$found) {
                $rowData['indikator_id'] = $indikator;
                $data_json[] = $rowData;
            }
        } else {
            $rowData['indikator_id'] = $indikator;
            $data_json[] = $rowData;
        }

        // Simpan semua ke DB
        $this->MonitoringPegawai_model->saveOrUpdateMonitoringBulanan(
            $nik,
            $bulan,
            $tahun,
            $data_json,
            $nilai_akhir_value,
            $pencapaian_akhir,
            $predikat
        );

        echo json_encode([
            'status' => 'success',
            'nilai_akhir' => $nilai_akhir_value,
            'pencapaian_akhir' => $pencapaian_akhir,
            'predikat' => $predikat
        ]);
    }

    /**
     * Halaman detail untuk arsip penilaian (halaman kosong).
     */
    public function arsipDetail($awal = null, $akhir = null)
    {
        // 1. Validasi input dan sesi
        if (!$this->session->userdata('nik') || !$awal || !$akhir) {
            // Tampilkan error langsung, jangan redirect ke login
            show_error('Sesi tidak valid atau periode arsip tidak lengkap. Pastikan Anda mengakses halaman ini dari tautan yang benar.', 403, 'Akses Ditolak');
            return;
        }

        $nik_pegawai = $this->session->userdata('nik');
        $data['title'] = 'Detail Arsip Penilaian';

        // 2. Ambil data riwayat jabatan pegawai pada periode tersebut
        // Ini adalah langkah kunci untuk mendapatkan jabatan & penilai yang benar
        $pegawai_history = $this->Pegawai_model->get_pegawai_history_by_date($nik_pegawai, $awal, $akhir);

        if (!$pegawai_history) {
            // Jika tidak ada riwayat, coba ambil data pegawai saat ini sebagai fallback
            $pegawai_history = $this->Pegawai_model->getPegawaiWithPenilai($nik_pegawai);
            $this->session->set_flashdata('warning', 'Data riwayat jabatan untuk periode ini tidak ditemukan, menampilkan data saat ini.');
        }
        $data['pegawai_detail'] = $pegawai_history;

        // 3. Ambil data penilaian yang sudah selesai untuk periode tersebut
        $penilaian_selesai = $this->Pegawai_model->get_arsip_penilaian_by_periode($nik_pegawai, $awal, $akhir);

        if ($penilaian_selesai) {
            $data['penilaian'] = $penilaian_selesai['penilaian_items'];
            $data['budaya_nilai'] = $penilaian_selesai['budaya_nilai'];
            $data['rata_rata_budaya'] = $penilaian_selesai['rata_rata_budaya'];
            $data['nilai_akhir'] = $penilaian_selesai['nilai_akhir'];
            $data['status_penilaian'] = 'selesai';
        } else {
            // Jika tidak ada data penilaian yang selesai pada periode itu
            $data['penilaian'] = [];
            $data['budaya_nilai'] = [];
            $data['rata_rata_budaya'] = 0;
            $data['nilai_akhir'] = [];
            $data['status_penilaian'] = 'tidak ditemukan';
        }

        // Ambil master data budaya untuk ditampilkan di tabel
        $data['budaya'] = $this->db->get('budaya')->result_array();

        // Simpan periode yang dipilih untuk view
        $data['selected_awal'] = $awal;
        $data['selected_akhir'] = $akhir;

        // 4. Load view dengan semua data yang sudah disiapkan
        $this->load->view('layoutpegawai/header', $data);
        $this->load->view('pegawai/arsip_detail', $data); // View baru yang akan kita buat
        $this->load->view('layoutpegawai/footer');
    }

    public function ppk_pegawai()
    {
        $nik = $this->session->userdata('nik');
        $data['pegawai'] = $this->Pegawai_model->getPegawaiByNik($nik);
        // Ambil list PPK berdasarkan NIK
        $list_ppk = $this->Ppk_model->get_ppk_by_nik($nik);

        // Logika untuk mengambil semua predikat dalam periode PPK
        if (!empty($list_ppk)) {
            foreach ($list_ppk as $ppk) {
                $ppk->predikat_list = [];
                if (!empty($ppk->periode_ppk)) {
                    // Format string periode_ppk: "dd Month YYYY - dd Month YYYY"
                    $parts = explode(' - ', $ppk->periode_ppk);
                    if (count($parts) === 2) {
                        $start = date('Y-m-d', strtotime($parts[0]));
                        $end = date('Y-m-d', strtotime($parts[1]));

                        // Ambil predikat dari nilai_akhir yang periode_akhir-nya ada di dalam range PPK
                        $this->db->select('predikat');
                        $this->db->from('nilai_akhir');
                        $this->db->where('nik', $nik);
                        $this->db->where('periode_akhir >=', $start);
                        $this->db->where('periode_akhir <=', $end);
                        $this->db->order_by('periode_akhir', 'ASC');
                        $res = $this->db->get()->result();

                        foreach ($res as $r) {
                            if (!empty($r->predikat)) {
                                $ppk->predikat_list[] = $r->predikat;
                            }
                        }
                    }
                }
            }
        }
        $data['list_ppk'] = $list_ppk;

        $this->load->view('layoutpegawai/header');
        $this->load->view('pegawai/ppk_pegawai', $data);
        $this->load->view('layoutpegawai/footer');
    }

    public function ppk_pegawaiformulir($id = null)
    {
        $nik = $this->session->userdata('nik');
        $data['pegawai'] = $this->Pegawai_model->getPegawaiByNik($nik);

        $data['ppk'] = null;
        $data['nilai_akhir'] = null;
        $data['sasaran'] = [];
        $data['periode_ppk_response'] = null;

        if ($id) {
            // Strategi 1: Cek apakah ID adalah id_nilai_akhir
            $nilai_akhir = $this->db->get_where('nilai_akhir', ['id' => $id])->row();

            if ($nilai_akhir) {
                $data['nilai_akhir'] = $nilai_akhir;
                
                // Cari PPK berdasarkan NIK dan Periode (derived from ppk_responses)
                $ppk_resp = $this->db->select('periode_ppk')->where('nik', $nik)->order_by('id', 'DESC')->get('ppk_responses')->row();
                $periode_ppk = $ppk_resp ? $ppk_resp->periode_ppk : null;
                $data['periode_ppk_response'] = $periode_ppk;

                if ($periode_ppk) {
                    $this->db->where('nik', $nik);
                    $this->db->where('periode_ppk', $periode_ppk);
                    $data['ppk'] = $this->db->get('ppk')->row();
                }
            } else {
                // Strategi 2: Jika bukan nilai_akhir, cek apakah ID adalah id_ppk
                $ppk = $this->Ppk_model->get_ppk_by_id($id);
                if ($ppk) {
                    $data['ppk'] = $ppk;
                    $data['periode_ppk_response'] = $ppk->periode_ppk;
                    
                    // Cari nilai_akhir yang relevan (sebelum periode PPK)
                    if (!empty($ppk->periode_ppk)) {
                        $parts = explode(' - ', $ppk->periode_ppk);
                        if (count($parts) >= 1) {
                            $start_date = date('Y-m-d', strtotime($parts[0]));
                            $this->db->where('nik', $nik);
                            $this->db->where('periode_akhir <', $start_date);
                            $this->db->order_by('periode_akhir', 'DESC');
                            $data['nilai_akhir'] = $this->db->get('nilai_akhir')->row();
                        }
                    }
                }
            }

            // Decode sasaran jika data PPK ditemukan
            if ($data['ppk'] && isset($data['ppk']->detail_sasaran) && !empty($data['ppk']->detail_sasaran)) {
                $data['sasaran'] = json_decode($data['ppk']->detail_sasaran, true) ?? [];
            }
        }

        $this->load->view('layoutpegawai/header');
        $this->load->view('pegawai/ppk_pegawaiformulir', $data);
        $this->load->view('layoutpegawai/footer');
    }

    public function simpan_ppk()
    {
        $this->load->library('form_validation');

        $this->form_validation->set_rules('nik', 'NIK', 'required');
        $this->form_validation->set_rules('tahap', 'Tahap', 'required|numeric');
        $this->form_validation->set_rules('periode_ppk', 'Periode PPK', 'required');

        $id_nilai_akhir = $this->input->post('id_nilai_akhir');

        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata('error', validation_errors());
            redirect('pegawai/ppk_pegawaiformulir/' . $id_nilai_akhir);
        } else {
            // Kumpulkan data sasaran dari form
            $sasaran_bulan = $this->input->post('sasaran_bulan');
            $rincian_tindakan = $this->input->post('rincian_tindakan');
            $detail_sasaran = [];
            if (is_array($sasaran_bulan)) {
                for ($i = 0; $i < count($sasaran_bulan); $i++) {
                    if (!empty($sasaran_bulan[$i])) {
                        $detail_sasaran[] = [
                            'sasaran_bulan' => $sasaran_bulan[$i],
                            'rincian_tindakan' => $rincian_tindakan[$i] ?? ''
                        ];
                    }
                }
            }

            // Siapkan data untuk disimpan ke model
            $data = [
                'nik' => $this->input->post('nik'),
                'tahap' => $this->input->post('tahap'),
                'periode_ppk' => $this->input->post('periode_ppk'),
                'periode_coaching' => $this->input->post('periode_coaching'),
                'review_sebelum' => $this->input->post('review_sebelum'),
                'target' => $this->input->post('target'),
                'pencapaian' => $this->input->post('pencapaian'),
                'aktivitas' => $this->input->post('aktivitas'),
                'rencana' => $this->input->post('rencana'),
                'detail_sasaran' => json_encode($detail_sasaran),
                'status_pegawai' => $this->input->post('status_pegawai') ? 'Disetujui' : 'Belum Disetujui'
            ];

            $result = $this->Ppk_model->save_or_update_ppk($data);

            if ($result) {
                $this->session->set_flashdata('success', 'Formulir PPK berhasil disimpan.');
            } else {
                $this->session->set_flashdata('error', 'Gagal menyimpan formulir PPK.');
            }

            redirect('pegawai/ppk_pegawaiformulir/' . $id_nilai_akhir);
        }
    }

    public function ppk_penilaiformulir($id = null)
    {
        // Cek login
        if (!$this->session->userdata('nik')) {
            redirect('auth');
        }

        if ($id) {
            // Ambil data nilai_akhir untuk referensi
            $data['nilai_akhir'] = $this->db->get_where('nilai_akhir', ['id' => $id])->row();
            
            if (!$data['nilai_akhir']) {
                show_404();
            }

            // Ambil data pegawai yang dinilai (bukan yang login)
            $nik_pegawai = $data['nilai_akhir']->nik;
            $data['pegawai'] = $this->Pegawai_model->getPegawaiByNik($nik_pegawai);

            // Ambil data penilai yang sedang login
            $data['penilai'] = $this->Pegawai_model->getPegawaiByNik($this->session->userdata('nik'));

            // Ambil data PPK dan sasaran
            $data['ppk'] = $this->Ppk_model->get_ppk_by_id($id);
            
            // Decode detail_sasaran dari JSON jika ada
            $data['sasaran'] = [];
            if (isset($data['ppk']->detail_sasaran) && !empty($data['ppk']->detail_sasaran)) {
                $data['sasaran'] = json_decode($data['ppk']->detail_sasaran, true) ?? [];
            }

            // TAMBAHAN: Ambil periode_ppk dari ppk_responses
            $ppk_resp = $this->db->select('periode_ppk')->where('nik', $nik_pegawai)->get('ppk_responses')->row();
            $data['periode_ppk_response'] = $ppk_resp ? $ppk_resp->periode_ppk : null;
        } else {
            show_404();
        }

        $this->load->view('layoutpegawai/header');
        $this->load->view('pegawai/ppk_penilaiformulir', $data);
        $this->load->view('layoutpegawai/footer');
    }

    public function simpan_ppk_penilai()
    {
        $this->load->library('form_validation');
        $this->form_validation->set_rules('nik', 'NIK', 'required');
        $this->form_validation->set_rules('id_nilai_akhir', 'ID Nilai Akhir', 'required');

        $id_nilai_akhir = $this->input->post('id_nilai_akhir');

        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata('error', validation_errors());
            redirect('pegawai/ppk_penilaiformulir/' . $id_nilai_akhir);
        } else {
            // Hanya update status penilai 1, data lain dibiarkan tetap (atau ikut tersimpan jika diedit)
            // Kita asumsikan Penilai juga bisa mengedit konten jika perlu, atau setidaknya save form yang ada.
            // Namun yang krusial adalah status_penilai1.
            
            // Kumpulkan data sasaran dari form (jika penilai boleh edit sasaran)
            $sasaran_bulan = $this->input->post('sasaran_bulan');
            $rincian_tindakan = $this->input->post('rincian_tindakan');
            $detail_sasaran = [];
            if (is_array($sasaran_bulan)) {
                for ($i = 0; $i < count($sasaran_bulan); $i++) {
                    if (!empty($sasaran_bulan[$i])) {
                        $detail_sasaran[] = [
                            'sasaran_bulan' => $sasaran_bulan[$i],
                            'rincian_tindakan' => $rincian_tindakan[$i] ?? ''
                        ];
                    }
                }
            }

            $data = [
                'nik' => $this->input->post('nik'),
                'tahap' => $this->input->post('tahap'),
                'periode_ppk' => $this->input->post('periode_ppk'),
                'periode_coaching' => $this->input->post('periode_coaching'),
                'review_sebelum' => $this->input->post('review_sebelum'),
                'target' => $this->input->post('target'),
                'pencapaian' => $this->input->post('pencapaian'),
                'aktivitas' => $this->input->post('aktivitas'),
                'rencana' => $this->input->post('rencana'),
                'detail_sasaran' => json_encode($detail_sasaran),
                // Update status penilai 1
                'status_penilai1' => $this->input->post('status_penilai1') ? 'Disetujui' : 'Belum Disetujui'
            ];

            // Gunakan model yang sama, karena save_or_update_ppk melakukan UPDATE jika data sudah ada
            // dan hanya mengupdate field yang dikirim di $data. Status pegawai/msdi/pimpinan tidak akan berubah
            // karena tidak ada di array $data ini.
            $result = $this->Ppk_model->save_or_update_ppk($data);

            if ($result) {
                $this->session->set_flashdata('success', 'Penilaian PPK berhasil disimpan.');
            } else {
                $this->session->set_flashdata('error', 'Gagal menyimpan penilaian PPK.');
            }

            redirect('pegawai/ppk_penilaiformulir/' . $id_nilai_akhir);
        }
    }

    public function ppk_pimpinanformulir($id = null)
    {
        // Cek login
        if (!$this->session->userdata('nik')) {
            redirect('auth');
        }

        if ($id) {
            // Ambil data nilai_akhir untuk referensi
            $data['nilai_akhir'] = $this->db->get_where('nilai_akhir', ['id' => $id])->row();
            
            if (!$data['nilai_akhir']) {
                show_404();
            }

            // Ambil data pegawai yang dinilai (bukan yang login)
            $nik_pegawai = $data['nilai_akhir']->nik;
            $data['pegawai'] = $this->Pegawai_model->getPegawaiByNik($nik_pegawai);

            // Ambil data pimpinan yang sedang login untuk nama di tanda tangan
            $data['pimpinan'] = $this->Pegawai_model->getPegawaiByNik($this->session->userdata('nik'));

            // Ambil data PPK dan sasaran
            $data['ppk'] = $this->Ppk_model->get_ppk_by_id($id);
            
            // Decode detail_sasaran dari JSON jika ada
            $data['sasaran'] = [];
            if (isset($data['ppk']->detail_sasaran) && !empty($data['ppk']->detail_sasaran)) {
                $data['sasaran'] = json_decode($data['ppk']->detail_sasaran, true) ?? [];
            }

            // TAMBAHAN: Ambil periode_ppk dari ppk_responses
            $ppk_resp = $this->db->select('periode_ppk')->where('nik', $nik_pegawai)->get('ppk_responses')->row();
            $data['periode_ppk_response'] = $ppk_resp ? $ppk_resp->periode_ppk : null;
        } else {
            show_404();
        }

        $this->load->view('layoutpegawai/header');
        $this->load->view('pegawai/ppk_pimpinanformulir', $data);
        $this->load->view('layoutpegawai/footer');
    }

    public function simpan_ppk_pimpinan()
    {
        $this->load->library('form_validation');
        $this->form_validation->set_rules('nik', 'NIK', 'required');
        $this->form_validation->set_rules('id_nilai_akhir', 'ID Nilai Akhir', 'required');

        $id_nilai_akhir = $this->input->post('id_nilai_akhir');

        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata('error', validation_errors());
            redirect('pegawai/ppk_pimpinanformulir/' . $id_nilai_akhir);
        } else {
            $data = [
                'nik' => $this->input->post('nik'),
                'periode_ppk' => $this->input->post('periode_ppk'),
                'status_pimpinanunit' => $this->input->post('status_pimpinanunit') ? 'Disetujui' : 'Belum Disetujui'
            ];

            // Gunakan model yang sama, karena save_or_update_ppk melakukan UPDATE jika data sudah ada
            // dan hanya mengupdate field yang dikirim di $data.
            $result = $this->Ppk_model->save_or_update_ppk($data);

            if ($result) {
                $this->session->set_flashdata('success', 'Penilaian PPK oleh Pimpinan Unit berhasil disimpan.');
            } else {
                $this->session->set_flashdata('error', 'Gagal menyimpan penilaian PPK.');
            }

            redirect('pegawai/ppk_pimpinanformulir/' . $id_nilai_akhir);
        }
    }

    public function ppk_penilai()
    {
        $nik = $this->session->userdata('nik');
        $this->load->model('pegawai/Ppk_model');
        $this->load->model('pegawai/Nilai_model');
        $this->load->model('pegawai/Pegawai_model');

        // 1. Ambil data pegawai yang dinilai oleh user ini sebagai Penilai 1
        $bawahan1 = $this->Nilai_model->getPegawaiSebagaiPenilai1($nik);
        $niks1 = [];
        if (!empty($bawahan1)) {
            $niks1 = array_column($bawahan1, 'nik');
        }
        
        $list_ppk_penilai1 = [];
        if (!empty($niks1)) {
            $list_ppk_penilai1 = $this->Ppk_model->get_ppk_list_by_niks($niks1);
        }

        // 2. Cek apakah user ini Pimpinan Unit
        $pegawai = $this->Pegawai_model->getPegawaiByNIK($nik);
        $is_pimpinan = false;
        $list_ppk_pimpinan = [];

        if ($pegawai) {
            $jabatan = strtolower($pegawai->jabatan);
            // Cek jabatan apakah termasuk pimpinan unit/cabang/divisi
            if (strpos($jabatan, 'general manager') !== false || 
                strpos($jabatan, 'branch manager') !== false || 
                strpos($jabatan, 'pimpinan divisi') !== false ||
                strpos($jabatan, 'pemimpin divisi') !== false
               ) {
                $is_pimpinan = true;
                // Ambil semua pegawai di unit kerja yang sama
                $bawahan_unit = $this->Pegawai_model->getPegawaiByUnit($pegawai->unit_kerja, $pegawai->unit_kantor, $nik);
                $niks_unit = array_column($bawahan_unit, 'nik');
                
                if (!empty($niks_unit)) {
                    $list_ppk_pimpinan = $this->Ppk_model->get_ppk_list_by_niks($niks_unit);
                }
            }
        }

        $data['judul'] = 'Penilaian PPK';
        $data['list_ppk_penilai1'] = $list_ppk_penilai1;
        $data['list_ppk_pimpinan'] = $list_ppk_pimpinan;
        $data['is_pimpinan'] = $is_pimpinan;

        $this->load->view('layoutpegawai/header', $data);
        $this->load->view('pegawai/ppk_penilai', $data);
        $this->load->view('layoutpegawai/footer');
    }

    public function ppk_penilaievaluasi($id_ppk = null)
    {
        if (!$this->session->userdata('nik')) {
            redirect('auth');
        }

        if (!$id_ppk) { // $id_ppk bisa berupa id_ppk atau id_nilai_akhir
            show_404();
        }

        // 1. Coba ambil data PPK menganggap input adalah ID PPK
        $ppk = $this->Ppk_model->get_ppk_row($id_ppk);
        
        // 2. Jika tidak ditemukan, coba cari menganggap input adalah ID Nilai Akhir (Fallback)
        if (!$ppk) {
            $candidate = $this->Ppk_model->get_ppk_by_id($id_ppk); // get_ppk_by_id mencari via nilai_akhir.id
            if ($candidate && !empty($candidate->id)) {
                $ppk = $candidate;
            }
        }

        if (!$ppk) {
            show_404();
        }

        // Ambil data Pegawai
        $pegawai = $this->Pegawai_model->getPegawaiByNik($ppk->nik);

        // Identifikasi User Login (untuk tanda tangan)
        $nik_current = $this->session->userdata('nik');
        $current_user = $this->Pegawai_model->getPegawaiByNik($nik_current);
        $is_penilai = ($ppk->nik != $nik_current);

        // Ambil data Evaluasi
        // Gunakan $ppk->id (ID asli tabel PPK) karena $id_ppk dari URL mungkin adalah ID Nilai Akhir
        $evaluasi = $this->Ppk_model->get_evaluasi_by_ppk($ppk->id);

        // Decode JSON
        $detail_evaluasi = [];
        $detail_tindakan = [];

        if ($evaluasi) {
            $detail_evaluasi = json_decode($evaluasi->detail_evaluasi, true) ?? [];
            $detail_tindakan = json_decode($evaluasi->detail_tindakan, true) ?? [];
        }

        $data = [
            'judul' => 'Evaluasi PPK',
            'ppk' => $ppk,
            'pegawai' => $pegawai,
            'evaluasi' => $evaluasi,
            'detail_evaluasi' => $detail_evaluasi,
            'detail_tindakan' => $detail_tindakan,
            'current_user' => $current_user,
            'is_penilai' => $is_penilai
        ];

        $this->load->view('layoutpegawai/header', $data);
        $this->load->view('pegawai/ppk_penilaievaluasi', $data);
        $this->load->view('layoutpegawai/footer');
    }

    public function simpan_ppk_evaluasi()
    {
        $id_ppk = $this->input->post('id_ppk');
        $nik = $this->input->post('nik');

        if (!$id_ppk || !$nik) {
            show_error('Data tidak lengkap');
        }

        // Helper function to process arrays
        $process_array = function ($sasaran, $response) {
            $result = [];
            if ($sasaran) {
                foreach ($sasaran as $key => $val) {
                    if (!empty($val)) {
                        $result[] = [
                            'sasaran' => $val,
                            'response' => $response[$key] ?? ''
                        ];
                    }
                }
            }
            return $result;
        };

        $data = [
            'id_ppk' => $id_ppk,
            'nik' => $nik,
            'evaluasi_pelaksanaan' => $this->input->post('evaluasi_pelaksanaan'),
            'detail_evaluasi' => json_encode($process_array($this->input->post('sasaran_hasil'), $this->input->post('hasil_pencapaian'))),
            'komitmen_lanjutan' => $this->input->post('komitmen_lanjutan'),
            'detail_tindakan' => json_encode($process_array($this->input->post('sasaran_rincian'), $this->input->post('rincian_tindakan_eval'))),
            'kesimpulan' => $this->input->post('kesimpulan'),
        ];

        // Update Status berdasarkan Role Actor (Pegawai atau Penilai)
        $role_actor = $this->input->post('role_actor');
        if ($role_actor == 'pegawai') {
            $data['status_pegawai'] = $this->input->post('status_pegawai_eval') ? 'Disetujui' : 'Belum Disetujui';
        } elseif ($role_actor == 'penilai') {
            $data['status_penilai1'] = $this->input->post('status_penilai1_eval') ? 'Disetujui' : 'Belum Disetujui';
        }

        if ($this->Ppk_model->save_evaluasi($data)) {
            $this->session->set_flashdata('success', 'Evaluasi PPK berhasil disimpan.');
        } else {
            $this->session->set_flashdata('error', 'Gagal menyimpan evaluasi PPK.');
        }

        redirect('pegawai/ppk_penilaievaluasi/' . $id_ppk);
    }
}
