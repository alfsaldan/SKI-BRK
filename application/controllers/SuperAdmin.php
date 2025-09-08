<?php
defined('BASEPATH') or exit('No direct script access allowed');

class SuperAdmin extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Indikator_model'); // Pastikan model ini dibuat
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
        $data['perspektif'] = ['Keuangan (F)', 'Pelanggan (C)', 'Proses Internal (IP)', 'Pembelajaran & Pertumbuhan (LG)'];
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

        $this->Indikator_model->insertSasaranKerja($jabatan, $perspektif, $sasaran_kerja);
        redirect('SuperAdmin/indikatorKinerja');
    }

    public function addIndikator()
    {
        $sasaran_id = $this->input->post('sasaran_id');
        $indikator = $this->input->post('indikator');
        $bobot = $this->input->post('bobot');

        for ($i = 0; $i < count($indikator); $i++) {
            $this->Indikator_model->insertIndikator($sasaran_id, $indikator[$i], $bobot[$i]);
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

    public function deleteIndikator($id)
    {
        $this->Indikator_model->deleteIndikator($id);
        redirect('SuperAdmin/indikatorKinerja');
    }

    public function updateIndikator()
{
    $data = json_decode(file_get_contents("php://input"), true);
    $id   = $data['id'];

    // Cek apakah request untuk sasaran kerja
    if (isset($data['sasaran_kerja'])) {
        $sasaranKerja = $data['sasaran_kerja'];
        $success = $this->Indikator_model->updateSasaranKerja($id, $sasaranKerja);
    } else {
        $indikator = $data['indikator'];
        $bobot     = $data['bobot'];
        $success = $this->Indikator_model->updateIndikator($id, $indikator, $bobot);
    }

    echo json_encode(['success' => $success]);
}

public function updateSasaran()
{
    $data = json_decode(file_get_contents("php://input"), true);
    $id   = $data['id'];
    $sasaran = $data['sasaran']; // dari JS

    $success = $this->Indikator_model->updateSasaranKerja($id, $sasaran);

    echo json_encode(['success' => $success]);
}




}