<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Nilai_model extends CI_Model
{
    /**
     * Ambil daftar pegawai yang bisa dinilai oleh penilai berdasarkan jabatan
     */
    public function getPegawaiYangDinilai($nik_penilai)
    {
        $subquery = "(SELECT jabatan FROM pegawai WHERE nik = '{$nik_penilai}')";
        $this->db->select('p.nik, p.nama, p.jabatan, p.unit_kerja');
        $this->db->from('pegawai p');
        $this->db->join('penilai_mapping pm', 'p.jabatan = pm.jabatan AND p.unit_kerja = pm.unit_kerja');
        $this->db->group_start();
        $this->db->where("pm.penilai1_jabatan = {$subquery}");
        $this->db->or_where("pm.penilai2_jabatan = {$subquery}");
        $this->db->group_end();
        $this->db->group_by('p.nik');
        return $this->db->get()->result();
    }

    public function getIndikatorPegawai($nik)
    {
        $this->db->select('p.*, i.jabatan, i.unit_kerja'); // pakai kolom yang ada
        $this->db->from('penilaian p');
        $this->db->join('indikator i', 'p.indikator_id = i.id', 'left');
        $this->db->where('p.nik', $nik);
        $this->db->order_by('i.jabatan', 'ASC'); // order by kolom yang ada
        $query = $this->db->get();

        return $query->result();
    }
}
