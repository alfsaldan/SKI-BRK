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

    public function getPegawaiByUnit($unit_kerja, $unit_kantor, $exclude_nik = null)
    {
        $this->db->select('nik, nama, jabatan, unit_kerja, unit_kantor');
        $this->db->from('pegawai');
        $this->db->where('unit_kerja', $unit_kerja);
        $this->db->where('unit_kantor', $unit_kantor);

        if ($exclude_nik) {
            $this->db->where('nik !=', $exclude_nik); // biar gak muncul dirinya sendiri
        }

        $this->db->order_by('nama', 'ASC');
        return $this->db->get()->result();
    }
    public function getNilaiAkhir($nik, $periode_awal, $periode_akhir)
    {
        return $this->db->where('nik', $nik)
            ->where('periode_awal', $periode_awal)
            ->where('periode_akhir', $periode_akhir)
            ->get('nilai_akhir')
            ->row_array();
    }
    public function save_nilai_akhir($nik, $nilai_sasaran, $nilai_budaya, $total_nilai, $fraud, $nilai_akhir, $pencapaian, $predikat, $periode_awal, $periode_akhir)
    {
        $data = [
            'nik'           => $nik,
            'nilai_sasaran' => $nilai_sasaran,
            'nilai_budaya'  => $nilai_budaya,
            'total_nilai'   => $total_nilai,
            'fraud'         => $fraud,
            'nilai_akhir'   => $nilai_akhir,
            'pencapaian'    => $pencapaian,
            'predikat'      => $predikat,
            'periode_awal'  => $periode_awal,
            'periode_akhir' => $periode_akhir,
            'updated_at'    => date('Y-m-d H:i:s')
        ];

        // cek data existing
        $this->db->where('nik', $nik);
        $this->db->where('periode_awal', $periode_awal);
        $this->db->where('periode_akhir', $periode_akhir);
        $exists = $this->db->get('nilai_akhir')->row();

        if ($exists) {
            $this->db->where('id', $exists->id);
            return $this->db->update('nilai_akhir', $data);
        } else {
            $data['created_at'] = date('Y-m-d H:i:s');
            return $this->db->insert('nilai_akhir', $data);
        }
    }


    public function getLockStatus($periode_awal, $periode_akhir)
    {
        $this->db->where('periode_awal', $periode_awal);
        $this->db->where('periode_akhir', $periode_akhir);
        $query = $this->db->get('penilaian');

        // Jika tidak ada data, anggap terbuka
        if ($query->num_rows() == 0) {
            return false;
        }

        foreach ($query->result() as $row) {
            if (empty($row->lock_input) || $row->lock_input == 0) {
                // Jika ada 0 → masih terbuka
                return false;
            }
        }

        // Jika semua 1 → terkunci
        return true;
    }

    public function getLockStatus2($periode_awal, $periode_akhir)
    {
        $this->db->where('periode_awal', $periode_awal);
        $this->db->where('periode_akhir', $periode_akhir);
        $query = $this->db->get('penilaian');

        // Jika tidak ada data, anggap terbuka
        if ($query->num_rows() == 0) {
            return false;
        }

        foreach ($query->result() as $row) {
            if (empty($row->lock_input2) || $row->lock_input2 == 0) {
                // Jika ada yang belum terkunci → anggap masih terbuka
                return false;
            }
        }

        // Jika semua baris bernilai 1 → terkunci
        return true;
    }

public function getGrafikPencapaian($nik)
{
    $this->db->select('periode_awal, periode_akhir, pencapaian, nilai_akhir, predikat');
    $this->db->from('nilai_akhir');
    $this->db->where('nik', $nik);
    $this->db->order_by('periode_awal', 'ASC');
    $result = $this->db->get()->result_array();

    foreach ($result as &$row) {
        // pencapaian: "109.36%" -> 109.36 (float)
        $row['pencapaian'] = floatval(str_replace('%', '', $row['pencapaian']));
        // nilai_akhir: kalau string gunakan float
        $row['nilai_akhir'] = isset($row['nilai_akhir']) ? floatval($row['nilai_akhir']) : null;
        // predikat: biarkan apa adanya (string) atau null
        $row['predikat'] = isset($row['predikat']) ? $row['predikat'] : null;
    }
    return $result;
}

}
