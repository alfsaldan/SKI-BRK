<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Indikator_model extends CI_Model
{

    public function getSasaranKerja()
    {
        $this->db->select('id, sasaran_kerja, perspektif, jabatan, unit_kerja');
        return $this->db->get('sasaran_kerja')->result();
    }

    public function getUnitKerja()
    {
        $this->db->select('unit_kerja');
        $this->db->distinct();
        return $this->db->get('pegawai')->result();
    }

    public function insertSasaranKerja($jabatan, $unit_kerja, $perspektif, $sasaran_kerja)
    {
        $data = [
            'jabatan' => $jabatan,
            'unit_kerja' => $unit_kerja,
            'perspektif' => $perspektif,
            'sasaran_kerja' => $sasaran_kerja
        ];
        return $this->db->insert('sasaran_kerja', $data);
    }

    public function insertIndikator($sasaran_id, $indikator, $bobot)
    {
        $data = [
            'sasaran_id' => $sasaran_id,
            'indikator' => $indikator,
            'bobot' => $bobot
        ];
        return $this->db->insert('indikator', $data);
    }

    public function updateIndikator($id, $indikator, $bobot)
    {
        $this->db->where('id', $id);
        return $this->db->update('indikator', [
            'indikator' => $indikator,
            'bobot' => $bobot
        ]);
    }

    public function updateSasaranKerja($id, $sasaran)
    {
        $this->db->where('id', $id);
        return $this->db->update('sasaran_kerja', [
            'sasaran_kerja' => $sasaran
        ]);
    }

    public function deleteIndikator($id)
    {
        return $this->db->delete('indikator', ['id' => $id]);
    }

    public function getGroupedIndikator($unit_kerja = null, $jabatan = null)
    {
        $this->db->select('indikator.*, sasaran_kerja.sasaran_kerja, sasaran_kerja.perspektif, sasaran_kerja.jabatan, sasaran_kerja.unit_kerja');
        $this->db->from('indikator');
        $this->db->join('sasaran_kerja', 'sasaran_kerja.id = indikator.sasaran_id');
        
        if ($unit_kerja) {
            $this->db->where('sasaran_kerja.unit_kerja', $unit_kerja);
        }
        
        if ($jabatan) {
            $this->db->where('sasaran_kerja.jabatan', $jabatan);
        }

        $this->db->order_by('sasaran_kerja.perspektif, sasaran_kerja.id, indikator.id');
        $result = $this->db->get()->result();

        $grouped = [];
        foreach ($result as $row) {
            $grouped[$row->perspektif][$row->sasaran_kerja][] = $row;
        }
        return $grouped;
    }
}