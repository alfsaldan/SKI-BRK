<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Pegawai extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        // model Pegawai di subfolder models/pegawai/Pegawai_model.php
        $this->load->model('pegawai/Pegawai_model');
        $this->load->model('pegawai/Nilai_model');
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
     * Dashboard pegawai — gabungan welcome + penilaian kinerja milik pegawai yg login
     * Mendukung penerapan periode lewat POST (form) atau GET (?awal=...&akhir=...)
     */
    public function index()
    {
        $nik = $this->session->userdata('nik');
        $pegawai = $this->Pegawai_model->getPegawaiWithPenilai($nik);

        // periode: cek GET dulu (dari tombol sesuaikan periode), lalu POST (form), lalu default tahun berjalan
        $periode_awal = $this->input->get('awal') ?? $this->input->post('periode_awal') ?? date('Y') . "-01-01";
        $periode_akhir = $this->input->get('akhir') ?? $this->input->post('periode_akhir') ?? date('Y') . "-12-31";

        // ambil indikator & nilai (dibatasi dengan nik & periode)
        $indikator = $this->Pegawai_model->get_indikator_by_jabatan_dan_unit(
            $pegawai->jabatan,
            $pegawai->unit_kerja,
            $nik,
            $periode_awal,
            $periode_akhir
        );

        $data = [
            'judul' => "Dashboard Pegawai",
            'pegawai_detail' => $pegawai,
            'indikator_by_jabatan' => $indikator,
            'periode_awal' => $periode_awal,
            'periode_akhir' => $periode_akhir
        ];


        $this->load->view('layoutpegawai/header', $data);
        $this->load->view('pegawai/index', $data);
        $this->load->view('layoutpegawai/footer');
    }

    /**
     * Simpan 1 baris penilaian via AJAX (dipanggil dari JS di view)
     * Menerima sama payload seperti superadmin, tapi nik diambil dari session
     */
    public function simpanPenilaianBaris()
    {
        $nik = $this->session->userdata('nik');

        // terima beberapa kemungkinan nama parameter (supaya aman)
        $indikator_id = $this->input->post('indikator_id') ?? $this->input->post('id');
        $target = $this->input->post('target') ?? $this->input->post('targets') ?? null;
        $batas_waktu = $this->input->post('batas_waktu') ?? null;
        $realisasi = $this->input->post('realisasi') ?? null;

        $periode_awal = $this->input->post('periode_awal') ?? date('Y') . "-01-01";
        $periode_akhir = $this->input->post('periode_akhir') ?? date('Y') . "-12-31";

        // simpan (Penilaian_model->save_penilaian menangani insert/update sesuai nik+indikator+periode)
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
            echo json_encode([
                'status' => 'success',
                'message' => 'Penilaian berhasil disimpan!',
                'data' => compact('indikator_id', 'target', 'batas_waktu', 'realisasi', 'periode_awal', 'periode_akhir')
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
    /**
     * Halaman Nilai Pegawai (untuk penilai)
     */
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

        $data = [
            'judul' => "Form Penilaian Pegawai",
            'pegawai_detail' => $pegawai,
            'indikator_by_jabatan' => $indikator,
            'periode_awal' => $awal,
            'periode_akhir' => $akhir
        ];

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

        // Ambil data pegawai
        $data['pegawai'] = $this->DataDiri_model->getDataByNik($nik);

        // Proses update password
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
        $id = $this->input->post('id');
        $status = $this->input->post('status');

        $this->db->where('id', $id);
        $update = $this->db->update('penilaian', ['status' => $status]);

        if ($update) {
            echo json_encode(['success' => true, 'message' => 'Status berhasil diupdate']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Gagal update status']);
        }
    }
    public function updateStatusAll()
    {
        $ids = $this->input->post('ids'); // contoh: "1,2,3"
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


    // Simpan catatan via AJAX
    public function simpan_catatan()
    {
        $nik_pegawai = $this->input->post('nik_pegawai');
        $nik_penilai = $this->session->userdata('nik'); // penilai login
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
            // Ambil nama penilai dari DB supaya pasti benar
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
}
