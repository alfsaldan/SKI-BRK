<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Pegawai extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        // model Pegawai di subfolder models/pegawai/Pegawai_model.php
        $this->load->model('pegawai/Pegawai_model');
        $this->load->model('Penilaian_model');
        $this->load->model('Indikator_model');
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
        $pegawai = $this->Pegawai_model->getPegawaiByNIK($nik);

        // periode: cek GET dulu (dari tombol sesuaikan periode), lalu POST (form), lalu default tahun berjalan
        $periode_awal  = $this->input->get('awal') ?? $this->input->post('periode_awal') ?? date('Y') . "-01-01";
        $periode_akhir = $this->input->get('akhir') ?? $this->input->post('periode_akhir') ?? date('Y') . "-12-31";

        // ambil indikator & nilai (dibatasi dengan nik & periode)
        $indikator = $this->Penilaian_model->get_indikator_by_jabatan_dan_unit(
            $pegawai->jabatan,
            $pegawai->unit_kerja,
            $nik,
            $periode_awal,
            $periode_akhir
        );

        $data = [
            'judul'                 => "Dashboard Pegawai",
            'pegawai_detail'        => $pegawai,
            'indikator_by_jabatan'  => $indikator,
            'periode_awal'          => $periode_awal,
            'periode_akhir'         => $periode_akhir
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
        $target       = $this->input->post('target') ?? $this->input->post('targets') ?? null;
        $batas_waktu  = $this->input->post('batas_waktu') ?? null;
        $realisasi    = $this->input->post('realisasi') ?? null;

        $periode_awal  = $this->input->post('periode_awal') ?? date('Y') . "-01-01";
        $periode_akhir = $this->input->post('periode_akhir') ?? date('Y') . "-12-31";

        // simpan (Penilaian_model->save_penilaian menangani insert/update sesuai nik+indikator+periode)
        $save = $this->Penilaian_model->save_penilaian(
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
}
