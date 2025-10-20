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
        $this->db->select('
        p.nik,
        p.nama,
        p.jabatan,
        p.unit_kerja,
        p.unit_kantor,
        pen1_peg.nik AS penilai1_nik,
        pen1_peg.nama AS penilai1_nama,
        pen1_peg.jabatan AS penilai1_jabatan_detail,
        pen2_peg.nik AS penilai2_nik,
        pen2_peg.nama AS penilai2_nama,
        pen2_peg.jabatan AS penilai2_jabatan_detail
    ');
        $this->db->from('pegawai p');

        // mapping pegawai sesuai jabatan & unit
        $this->db->join('penilai_mapping m', 'm.jabatan = p.jabatan AND m.unit_kerja = p.unit_kerja', 'left');

        // Penilai 1
        $this->db->join(
            'pegawai pen1_peg',
            'pen1_peg.jabatan = (SELECT jabatan FROM penilai_mapping WHERE `key` = m.penilai1_jabatan LIMIT 1)',
            'left'
        );

        // Penilai 2
        $this->db->join(
            'pegawai pen2_peg',
            'pen2_peg.jabatan = (SELECT jabatan FROM penilai_mapping WHERE `key` = m.penilai2_jabatan LIMIT 1)',
            'left'
        );

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

    public function getRekapNilaiTahunan($nik)
    {
        $this->db->where('nik', $nik);
        $this->db->order_by('periode_awal', 'ASC');
        $query = $this->db->get('nilai_akhir')->result();

        if (!$query) return [];

        // Kelompokkan berdasarkan tahun periode_awal
        $rekap = [];
        foreach ($query as $row) {
            $tahun = date('Y', strtotime($row->periode_awal));

            if (!isset($rekap[$tahun])) {
                $rekap[$tahun] = (object) [
                    'tahun' => $tahun,
                    'periode' => [],
                    'rata_nilai_sasaran' => 0,
                    'rata_nilai_budaya' => 0,
                    'rata_total_nilai' => 0,
                    'rata_nilai_akhir' => 0,
                    'rata_pencapaian' => 0,
                    'predikat_tahunan' => ''
                ];
            }

            $rekap[$tahun]->periode[] = (object) [
                'periode' => date('d M Y', strtotime($row->periode_awal)) . ' - ' . date('d M Y', strtotime($row->periode_akhir)),
                'nilai_sasaran' => $row->nilai_sasaran,
                'nilai_budaya' => $row->nilai_budaya,
                'total_nilai' => $row->total_nilai,
                'nilai_akhir' => $row->nilai_akhir,
                'pencapaian' => $row->pencapaian,
                'predikat' => $row->predikat
            ];
        }

        // Hitung rata-rata per tahun
        foreach ($rekap as $tahun => $r) {
            $jumlah = count($r->periode);
            $total_sasaran = $total_budaya = $total_total = $total_akhir = $total_pencapaian = 0;
            $predikat_list = [];

            foreach ($r->periode as $p) {
                $total_sasaran += $p->nilai_sasaran;
                $total_budaya += $p->nilai_budaya;
                $total_total += $p->total_nilai;
                $total_akhir += $p->nilai_akhir;
                $total_pencapaian += floatval(str_replace('%', '', $p->pencapaian));
                $predikat_list[] = $p->predikat;
            }

            $r->rata_nilai_sasaran = round($total_sasaran / $jumlah, 2);
            $r->rata_nilai_budaya = round($total_budaya / $jumlah, 2);
            $r->rata_total_nilai = round($total_total / $jumlah, 2);
            $r->rata_nilai_akhir = round($total_akhir / $jumlah, 2);
            $r->rata_pencapaian = round($total_pencapaian / $jumlah, 2) . '%';
            $r->predikat_tahunan = $this->getPredikatTahunan($predikat_list);
        }

        return array_values($rekap);
    }

    private function getPredikatTahunan($predikat_list)
    {
        $count = array_count_values($predikat_list);
        arsort($count);
        return key($count);
    }
}
