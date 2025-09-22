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
    public function get_indikator_by_jabatan_dan_unit($jabatan, $unit_kerja, $nik = null, $periode_awal = null, $periode_akhir = null)
    {
        if (!$periode_awal) $periode_awal = date('Y') . '-01-01';
        if (!$periode_akhir) $periode_akhir = date('Y') . '-12-31';

        $this->db->select('
        indikator.id,
        indikator.indikator,
        indikator.bobot,
        sasaran_kerja.perspektif,
        sasaran_kerja.sasaran_kerja,
        penilaian.id as penilaian_id,
        penilaian.target,
        penilaian.batas_waktu,
        penilaian.realisasi,
        penilaian.nilai,
        penilaian.nilai_dibobot,
        penilaian.catatan,
        penilaian.status,
        penilaian.periode_awal,
        penilaian.periode_akhir
    ');
        $this->db->from('indikator');
        $this->db->join('sasaran_kerja', 'indikator.sasaran_id = sasaran_kerja.id');

        if ($nik) {
            $this->db->join(
                'penilaian',
                "penilaian.indikator_id = indikator.id
             AND penilaian.nik = " . $this->db->escape($nik) . "
             AND penilaian.periode_awal = " . $this->db->escape($periode_awal) . "
             AND penilaian.periode_akhir = " . $this->db->escape($periode_akhir),
                'left'
            );
        } else {
            $this->db->join('penilaian', 'penilaian.indikator_id = indikator.id', 'left');
        }

        $this->db->where('sasaran_kerja.jabatan', $jabatan);
        $this->db->where('sasaran_kerja.unit_kerja', $unit_kerja);

        $this->db->order_by('sasaran_kerja.perspektif', 'ASC');
        $this->db->order_by('sasaran_kerja.sasaran_kerja', 'ASC');

        return $this->db->get()->result();
    }
    public function save_penilaian($nik, $indikator_id, $target, $batas_waktu, $realisasi, $periode_awal = null, $periode_akhir = null)
    {
        if (!$periode_awal) $periode_awal = date('Y') . '-01-01';
        if (!$periode_akhir) $periode_akhir = date('Y') . '-12-31';

        $data = [
            'nik' => $nik,
            'indikator_id' => $indikator_id,
            'target' => $target,
            'batas_waktu' => $batas_waktu,
            'realisasi' => $realisasi,
            'periode_awal' => $periode_awal,
            'periode_akhir' => $periode_akhir
        ];

        $this->db->where('nik', $nik);
        $this->db->where('indikator_id', $indikator_id);
        $this->db->where('periode_awal', $periode_awal);
        $this->db->where('periode_akhir', $periode_akhir);
        $exists = $this->db->get('penilaian')->row();

        if ($exists) {
            $this->db->where('id', $exists->id);
            return $this->db->update('penilaian', $data);
        } else {
            return $this->db->insert('penilaian', $data);
        }
    }

    // Simpan catatan pegawai
    public function tambahCatatan($data)
    {
        return $this->db->insert('catatan_pegawai', $data);
    }

    // Ambil catatan pegawai
    public function getCatatanPegawai($nik)
    {
        return $this->db
            ->select('id, nik, catatan, tanggal')
            ->from('catatan_pegawai')
            ->where('nik', $nik)
            ->order_by('tanggal', 'DESC')
            ->get()
            ->result();
    }

    public function getPeriodePegawai($nik)
    {
        $this->db->distinct(); // pastikan hasil unik
        $this->db->select('periode_awal, periode_akhir');
        $this->db->from('penilaian');
        $this->db->where('nik', $nik);
        $this->db->order_by('periode_awal', 'DESC');
        return $this->db->get()->result();
    }
}
