<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Penilaian_model extends CI_Model
{
    public function get_all_penilaian()
    {
        return $this->db->get('penilaian')->result();
    }

    // 🔹 Tambahan: ambil indikator sesuai jabatan
    public function get_indikator_by_jabatan($jabatan)
    {
        $this->db->select('indikator.id, indikator.indikator, indikator.bobot, sasaran_kerja.perspektif, sasaran_kerja.sasaran_kerja');
        $this->db->from('indikator');
        $this->db->join('sasaran_kerja', 'indikator.sasaran_id = sasaran_kerja.id');
        $this->db->where('sasaran_kerja.jabatan', $jabatan);
        return $this->db->get()->result();
    }

    public function insertPenilaian($nik, $indikator_id, $target, $batas_waktu, $realisasi)
    {
        $data = [
            'nik' => $nik,
            'indikator_id' => $indikator_id,
            'target' => $target,
            'batas_waktu' => $batas_waktu,
            'realisasi' => $realisasi,
        ];
        $this->db->insert('penilaian', $data);
    }

    public function simpan_penilaian($data)
    {
        return $this->db->insert_batch('penilaian', $data);
    }
}

