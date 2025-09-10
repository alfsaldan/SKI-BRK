<?php
defined('BASEPATH') or exit('No direct script access allowed');

class DataPegawai_model extends CI_Model
{
    // ambil detail pegawai by NIK
    public function getPegawaiByNik($nik)
    {
        return $this->db->get_where('pegawai', ['nik' => $nik])->row();
    }

    // ambil data penilaian by NIK (dengan join indikator + sasaran kerja)
    public function getPenilaianByNik($nik)
    {
        $this->db->select('p.*, i.indikator, i.bobot, s.perspektif, s.sasaran_kerja');
        $this->db->from('penilaian p');
        $this->db->join('indikator i', 'p.indikator_id = i.id', 'left');
        $this->db->join('sasaran_kerja s', 'i.sasaran_id = s.id', 'left');
        $this->db->where('p.nik', $nik);
        return $this->db->get()->result();
    }
}
