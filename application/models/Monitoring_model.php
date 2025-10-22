<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Monitoring_model extends CI_Model
{
    public function get_all_penilaian()
    {
        return $this->db->get('penilaian')->result();
    }

    public function get_indikator_by_jabatan_dan_unit($jabatan, $unit_kerja, $nik = null, $periode_awal = null, $periode_akhir = null)
    {
        // jika tidak diberikan periode, pakai bulan berjalan sebagai default
        if (!$periode_awal) $periode_awal = date('Y-m-01');
        if (!$periode_akhir) $periode_akhir = date('Y-m-t');

        $this->db->select('
            indikator.id,
            indikator.indikator,
            indikator.bobot,
            sasaran_kerja.perspektif,
            sasaran_kerja.sasaran_kerja,
            penilaian.target,
            penilaian.batas_waktu,
            penilaian.realisasi,
            penilaian.pencapaian,
            penilaian.nilai,
            penilaian.nilai_dibobot,
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

    public function getNilaiAkhir($nik, $periode_awal, $periode_akhir)
    {
        // kembalikan object row (sama pola dengan Penilaian_model)
        return $this->db->where('nik', $nik)
            ->where('periode_awal', $periode_awal)
            ->where('periode_akhir', $periode_akhir)
            ->get('nilai_akhir')
            ->row();
    }

    public function getPegawaiWithPenilai($nik)
    {
        $this->db->select('
        p.nik,
        p.nama,
        p.jabatan,
        p.unit_kerja,
        p.unit_kantor,
        pen1_peg.nik AS penilai1_nik,
        pen1_peg.nama AS penilai1_nama,
        pen1_peg.jabatan AS penilai1_jabatan,
        pen2_peg.nik AS penilai2_nik,
        pen2_peg.nama AS penilai2_nama,
        pen2_peg.jabatan AS penilai2_jabatan
    ');
        $this->db->from('pegawai p');

        // mapping pegawai sesuai jabatan & unit
        $this->db->join('penilai_mapping m', 'm.jabatan = p.jabatan AND m.unit_kerja = p.unit_kerja', 'left');

        // Penilai 1
        $this->db->join('pegawai pen1_peg', 'pen1_peg.jabatan = (SELECT jabatan FROM penilai_mapping WHERE `key` = m.penilai1_jabatan LIMIT 1)', 'left');

        // Penilai 2
        $this->db->join('pegawai pen2_peg', 'pen2_peg.jabatan = (SELECT jabatan FROM penilai_mapping WHERE `key` = m.penilai2_jabatan LIMIT 1)', 'left');

        $this->db->where('p.nik', $nik);
        return $this->db->get()->row();
    }


    public function updateStatus($id, $status)
    {
        $this->db->where('id', $id)->update('penilaian', ['status' => $status]);
        return $this->db->affected_rows();
    }

    // 🔹 Ambil semua budaya dari tabel budaya
    public function getAllBudaya()
    {
        return $this->db->get('budaya')->result();
    }

    public function getBudayaNilaiByNik($nik, $periode_awal = null, $periode_akhir = null)
    {
        $this->db->where('nik_pegawai', $nik);

        // 🔹 filter periode jika diberikan
        if ($periode_awal) {
            $this->db->where('periode_awal', $periode_awal);
        }
        if ($periode_akhir) {
            $this->db->where('periode_akhir', $periode_akhir);
        }

        $result = $this->db->get('budaya_nilai')->row(); // gunakan row() biar object

        if ($result) {
            $nilai_budaya = isset($result->nilai_budaya) ? json_decode($result->nilai_budaya, true) : [];
            $rata_rata = isset($result->rata_rata) ? $result->rata_rata : 0;

            return [
                'nilai_budaya' => $nilai_budaya,
                'rata_rata' => $rata_rata
            ];
        }

        return [
            'nilai_budaya' => [],
            'rata_rata' => 0
        ];
    }
}
