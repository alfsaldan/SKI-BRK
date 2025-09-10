<?php
defined('BASEPATH') or exit('No direct script access allowed');

class DataPegawai_model extends CI_Model
{
    public function getAllPegawai()
    {
        return $this->db->get('pegawai')->result();
    }

    public function insertBatch($data)
    {
        return $this->db->insert_batch('pegawai', $data);
    }

    public function insertPegawai($data)
    {
        return $this->db->insert('pegawai', $data);
    }

    public function getPegawaiByNik($nik)
    {
        return $this->db->get_where('pegawai', ['nik' => $nik])->row();
    }

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
