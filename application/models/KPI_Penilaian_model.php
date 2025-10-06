<?php
defined('BASEPATH') or exit('No direct script access allowed');

class KPI_Penilaian_model extends CI_Model
{
    // ==========================
    // 🔹 Ambil semua Unit Kerja yang punya data KPI aktif
    // ==========================
    public function getUnitKerjaKPI()
    {
        $this->db->distinct();
        $this->db->select('p.unit_kerja');
        $this->db->from('pegawai p');
        $this->db->join('penilai_mapping pm', 'p.jabatan = pm.jabatan', 'inner');
        $this->db->where('pm.jenis_penilaian', 'kpi');
        $this->db->where('p.status', 'aktif');
        $this->db->order_by('p.unit_kerja', 'ASC');

        return $this->db->get()->result();
    }

    // ==========================
    // 🔹 Ambil pegawai berdasarkan unit & jabatan
    // ==========================
    public function getPegawaiByUnit($unit_kerja = null, $jabatan = null)
    {
        $this->db->select('p.nik, p.nama, p.jabatan, p.unit_kerja, p.unit_kantor');
        $this->db->from('pegawai p');
        $this->db->join('penilai_mapping pm', 'p.jabatan = pm.jabatan', 'inner');
        $this->db->where('pm.jenis_penilaian', 'kpi');
        $this->db->where('p.status', 'aktif');

        if (!empty($unit_kerja)) {
            $this->db->where('p.unit_kerja', $unit_kerja);
        }
        if (!empty($jabatan)) {
            $this->db->where('p.jabatan', $jabatan);
        }

        $this->db->order_by('p.jabatan', 'ASC');
        return $this->db->get()->result();
    }

    // ==========================
    // 🔹 Ambil data pegawai + penilai (detail 1 pegawai)
    // ==========================
    public function getPegawaiWithPenilai($nik)
    {
        $this->db->select('
        p.*,
        pm.penilai1_jabatan,
        pm.penilai2_jabatan,
        pen1.nama as nama_penilai1,
        pen2.nama as nama_penilai2
    ');
        $this->db->from('pegawai p');
        $this->db->join('penilai_mapping pm', 'p.jabatan = pm.jabatan', 'left');
        $this->db->join('pegawai pen1', 'pen1.jabatan = pm.penilai1_jabatan', 'left');
        $this->db->join('pegawai pen2', 'pen2.jabatan = pm.penilai2_jabatan', 'left');
        $this->db->where('p.nik', $nik);
        $this->db->where('p.status', 'aktif');
        $this->db->where('pm.jenis_penilaian', 'kpi');

        return $this->db->get()->row();
    }


    // ==========================
    // 🔹 Ambil indikator KPI berdasarkan jabatan & unit kerja
    // ==========================
    public function get_indikator_by_jabatan_dan_unit($jabatan, $unit_kerja, $nik, $awal, $akhir)
    {
        $this->db->select('
        ki.id AS id_indikator,
        ki.indikator AS nama_indikator,
        ki.bobot,
        kp.target,
        kp.realisasi,
        kp.pencapaian,
        kp.nilai,
        kp.nilai_dibobot,
        kp.status,
        kp.periode_awal,
        kp.periode_akhir
    ');
        $this->db->from('kpi_indikator ki');
        $this->db->join('kpi_sasaran ks', 'ki.sasaran_id = ks.id', 'inner');
        $this->db->join('kpi_penilaian kp', 'ki.id = kp.indikator_id AND kp.nik = "' . $nik . '"', 'left');
        $this->db->where('ks.jabatan', $jabatan);
        $this->db->where('ks.unit_kerja', $unit_kerja);
        $this->db->where('(kp.periode_awal >= "' . $awal . '" AND kp.periode_akhir <= "' . $akhir . '")', null, false);
        $this->db->order_by('ki.id', 'ASC');

        return $this->db->get()->result();
    }


    // ==========================
    // 🔹 Ambil Nilai Akhir dari tabel nilai_akhir
    // ==========================
    public function getNilaiAkhir($nik, $awal, $akhir)
    {
        $this->db->select('*');
        $this->db->from('nilai_akhir');
        $this->db->where('nik', $nik);
        $this->db->where('periode_awal >=', $awal);
        $this->db->where('periode_akhir <=', $akhir);
        $this->db->where('jenis_penilaian', 'kpi');
        $query = $this->db->get();

        return $query->row();
    }
}
