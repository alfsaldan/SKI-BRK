<?php
defined('BASEPATH') or exit('No direct script access allowed');

class KPI_Indikator_model extends CI_Model
{

    public function getSasaranKerja()
    {
        $this->db->select('id, sasaran_kpi, perspektif, jabatan, unit_kerja');
        return $this->db->get('kpi_sasaran')->result();
    }

    public function getUnitKerja()
    {
        $this->db->select('unit_kerja');
        $this->db->distinct();
        return $this->db->get('penilai_mapping')->result();
    }

    public function insertSasaranKerja($jabatan, $unit_kerja, $perspektif, $sasaran_kpi)
    {
        $data = [
            'jabatan' => $jabatan,
            'unit_kerja' => $unit_kerja,
            'perspektif' => $perspektif,
            'sasaran_kpi' => $sasaran_kpi
        ];
        return $this->db->insert('kpi_sasaran', $data);
    }

    public function insertIndikator($sasaran_id, $indikator, $bobot)
    {
        $data = [
            'sasaran_id' => $sasaran_id,
            'indikator' => $indikator,
            'bobot' => $bobot
        ];
        return $this->db->insert('kpi_indikator', $data);
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
        return $this->db->update('kpi_sasaran', [
            'sasaran_kpi' => $sasaran
        ]);
    }

    public function deleteIndikator($id)
    {
        return $this->db->delete('kpi_indikator', ['id' => $id]);
    }

    public function getGroupedIndikator($unit_kerja = null, $jabatan = null)
    {
        $this->db->select('kpi_indikator.*, kpi_sasaran.sasaran_kpi, kpi_sasaran.perspektif, kpi_sasaran.jabatan, kpi_sasaran.unit_kerja');
        $this->db->from('kpi_indikator');
        $this->db->join('kpi_sasaran', 'kpi_sasaran.id = kpi_indikator.sasaran_id');
        
        if ($unit_kerja) {
            $this->db->where('kpi_sasaran.unit_kerja', $unit_kerja);
        }
        
        if ($jabatan) {
            $this->db->where('kpi_sasaran.jabatan', $jabatan);
        }

        $this->db->order_by('kpi_sasaran.perspektif, kpi_sasaran.id, kpi_indikator.id');
        $result = $this->db->get()->result();

        $grouped = [];
        foreach ($result as $row) {
            $grouped[$row->perspektif][$row->sasaran_kpi][] = $row;
        }
        return $grouped;
    }
}