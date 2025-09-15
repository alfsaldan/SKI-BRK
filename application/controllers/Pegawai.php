<?php
defined('BASEPATH') or exit('No direct script access allowed');

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xls;
use PhpOffice\PhpSpreadsheet\IOFactory;

class Pegawai extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->library('session');

        if (!$this->session->userdata('logged_in') || $this->session->userdata('role') !== 'pegawai') {
            redirect('auth');
        }
    }

    public function index()
    {
        $data['judul'] = "Halaman Dashboard Pegawai";
        $this->load->view("layoutpegawai/header");
        $this->load->view("pegawai/index", $data);
        $this->load->view("layoutpegawai/footer");
    }
}