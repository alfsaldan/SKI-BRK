<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Penilaian_model extends CI_Model
{
    public function get_all_penilaian()
    {
        return $this->db->get('penilaian')->result();
    }

    public function get_indikator_by_jabatan_dan_unit($jabatan, $unit_kerja, $nik = null)
    {
        $this->db->select('
            indikator.id,
            indikator.indikator,
            indikator.bobot,
            sasaran_kerja.perspektif,
            sasaran_kerja.sasaran_kerja,
            penilaian.target,
            penilaian.batas_waktu,
            penilaian.realisasi,
            penilaian.nilai,
            penilaian.nilai_dibobot,
            penilaian.catatan
        ');
        $this->db->from('indikator');
        $this->db->join('sasaran_kerja', 'indikator.sasaran_id = sasaran_kerja.id');

        if ($nik) {
            $this->db->join('penilaian', "penilaian.indikator_id = indikator.id AND penilaian.nik = '$nik'", 'left');
        } else {
            $this->db->join('penilaian', 'penilaian.indikator_id = indikator.id', 'left');
        }

        $this->db->where('sasaran_kerja.jabatan', $jabatan);
        $this->db->where('sasaran_kerja.unit_kerja', $unit_kerja);
        $this->db->order_by('sasaran_kerja.perspektif', 'ASC');
        $this->db->order_by('sasaran_kerja.sasaran_kerja', 'ASC');

        return $this->db->get()->result();
    }

    public function save_penilaian($nik, $indikator_id, $target, $batas_waktu, $realisasi)
    {
        $data = [
            'nik'          => $nik,
            'indikator_id' => $indikator_id,
            'target'       => $target,
            'batas_waktu'  => $batas_waktu,
            'realisasi'    => $realisasi,
        ];

        $exists = $this->db->get_where('penilaian', [
            'nik' => $nik,
            'indikator_id' => $indikator_id
        ])->row();

        if ($exists) {
            $this->db->where('id', $exists->id);
            return $this->db->update('penilaian', $data);
        } else {
            return $this->db->insert('penilaian', $data);
        }
    }

    public function simpan_penilaian($arr_data)
    {
        $saved = true;
        foreach ($arr_data as $d) {
            $s = $this->save_penilaian($d['nik'], $d['indikator_id'], $d['target'] ?? null, $d['batas_waktu'] ?? null, $d['realisasi'] ?? null);
            if (!$s) $saved = false;
        }
        return $saved;
    }

    public function update_penilaian($id, $data)
    {
        return $this->db->where('id', $id)->update('penilaian', $data);
    }
}