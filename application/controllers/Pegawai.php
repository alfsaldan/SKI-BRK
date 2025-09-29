
<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * @property Pegawai_model $Pegawai_model
 * @property Nilai_model $Nilai_model
 * @property DataDiri_model $DataDiri_model
 * @property Penilaian_model $Penilaian_model
 * @property Indikator_model $Indikator_model
 * @property Coaching_model $Coaching_model
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
        if (!$this->session->userdata('logged_in') || $this->session->userdata('role') !== 'pegawai') {
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

        $indikator = $this->Pegawai_model->get_indikator_by_jabatan_dan_unit(
            $pegawai->jabatan,
            $pegawai->unit_kerja,
            $nik,
            $periode_awal,
            $periode_akhir
        );

        $periode_list = $this->Pegawai_model->getPeriodePegawai($nik);

        $data = [
            'judul' => "Dashboard Pegawai",
            'pegawai_detail' => $pegawai,
            'indikator_by_jabatan' => $indikator,
            'periode_awal' => $periode_awal,
            'periode_akhir' => $periode_akhir,
            'periode_list' => $periode_list
        ];

        // 🔹 ambil nilai_akhir dari tabel nilai_akhir
        $nilai_akhir = $this->Pegawai_model->getNilaiAkhir($nik, $periode_awal, $periode_akhir);

        $data['nilai_akhir']         = $nilai_akhir;

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

    public function nilaiPegawai()
    {
        $nik = $this->session->userdata('nik');
        $this->load->model('pegawai/Nilai_model');

        $pegawai_dinilai = $this->Nilai_model->getPegawaiYangDinilai($nik);

        $data = [
            'judul' => 'Nilai Pegawai',
            'pegawai_dinilai' => $pegawai_dinilai
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

        $this->db->select('periode_awal, periode_akhir');
        $this->db->from('penilaian');
        $this->db->where('nik', $nik);
        $this->db->group_by(['periode_awal', 'periode_akhir']);
        $this->db->order_by('periode_awal', 'ASC');
        $periode_list = $this->db->get()->result();

        $data = [
            'judul' => "Form Penilaian Pegawai",
            'pegawai_detail' => $pegawai,
            'indikator_by_jabatan' => $indikator,
            'periode_awal' => $awal,
            'periode_akhir' => $akhir,
            'periode_list' => $periode_list
        ];

        // 🔹 ambil nilai_akhir dari tabel nilai_akhir
        $nilai_akhir = $this->Pegawai_model->getNilaiAkhir($nik, $awal, $akhir);

        $data['nilai_akhir']         = $nilai_akhir;

        $this->load->view('layoutpegawai/header', $data);
        $this->load->view('pegawai/nilaipegawai_detail', $data);
        $this->load->view('layoutpegawai/footer');
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
}
