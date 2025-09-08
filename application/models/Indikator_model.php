<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Indikator_model extends CI_Model
{

    public function getSasaranKerja()
    {
        return $this->db->get('sasaran_kerja')->result();
    }

    public function insertSasaranKerja($jabatan, $perspektif, $sasaran_kerja)
    {
        $data = [
            'jabatan' => $jabatan,
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

    // Ambil indikator dengan grouping per perspektif → sasaran kerja
    public function getGroupedIndikator()
    {
        $this->db->select('indikator.*, sasaran_kerja.sasaran_kerja, sasaran_kerja.perspektif, sasaran_kerja.jabatan');
        $this->db->from('indikator');
        $this->db->join('sasaran_kerja', 'sasaran_kerja.id = indikator.sasaran_id');
        $this->db->order_by('sasaran_kerja.perspektif, sasaran_kerja.id, indikator.id');
        $result = $this->db->get()->result();

        $grouped = [];
        foreach ($result as $row) {
            $grouped[$row->perspektif][$row->sasaran_kerja][] = $row;
        }
        return $grouped;
    }




}
