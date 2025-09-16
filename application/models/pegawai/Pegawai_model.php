<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Pegawai_model extends CI_Model
{
    /**
     * Ambil data pegawai by NIK (pegawai yang login)
     */
    public function getPegawaiByNIK($nik)
    {
        return $this->db->get_where('pegawai', ['nik' => $nik])->row();
    }

    /**
     * Ambil indikator penilaian pegawai berdasarkan periode
     */
    public function getIndikatorByPeriode($nik, $periode_awal, $periode_akhir)
    {
        $this->db->select('p.*, i.perspektif, i.sasaran_kerja, i.indikator');
        $this->db->from('penilaian p');
        $this->db->join('indikator i', 'p.indikator_id = i.id');
        $this->db->where('p.nik', $nik);
        $this->db->where('p.batas_waktu >=', $periode_awal);
        $this->db->where('p.batas_waktu <=', $periode_akhir);
        $this->db->order_by('i.perspektif, i.sasaran_kerja', 'ASC');
        return $this->db->get()->result();
    }
    /**
     * Ambil data pegawai + info penilai 1 & 2
     */
    public function getPegawaiWithPenilai($nik)
    {
        $this->db->select("
            p.*,
            pm.penilai1_jabatan,
            pm.penilai2_jabatan,
            pen1.nik AS penilai1_nik,
            pen1.nama AS penilai1_nama,
            pen1.jabatan AS penilai1_jabatan_detail,
            pen2.nik AS penilai2_nik,
            pen2.nama AS penilai2_nama,
            pen2.jabatan AS penilai2_jabatan_detail
        ");
        $this->db->from('pegawai p');
        $this->db->join('penilai_mapping pm', 'p.jabatan = pm.jabatan AND p.unit_kerja = pm.unit_kerja', 'left');
        $this->db->join('pegawai pen1', 'pm.penilai1_jabatan = pen1.jabatan AND p.unit_kerja = pen1.unit_kerja', 'left');
        $this->db->join('pegawai pen2', 'pm.penilai2_jabatan = pen2.jabatan AND p.unit_kerja = pen2.unit_kerja', 'left');
        $this->db->where('p.nik', $nik);

        return $this->db->get()->row();
    }


    /**
     * Update realisasi + nilai indikator
     */
    public function updatePenilaian($id, $data)
    {
        $this->db->where('id', $id);
        return $this->db->update('penilaian', $data);
    }
}
