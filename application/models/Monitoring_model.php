<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Monitoring_model extends CI_Model
{
    /* ============================
       🔹 PENILAIAN DASAR & UMUM
    ============================ */

    public function get_all_penilaian()
    {
        return $this->db->get('penilaian')->result();
    }

    public function get_indikator_by_jabatan_dan_unit($jabatan, $unit_kerja, $nik = null, $periode_awal = null, $periode_akhir = null)
    {
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
        return $this->db->where('nik', $nik)
            ->where('periode_awal', $periode_awal)
            ->where('periode_akhir', $periode_akhir)
            ->get('nilai_akhir')
            ->row();
    }

    public function getFraudDanKoefisien($nik, $periode_awal, $periode_akhir)
    {
        return $this->db->select('nilai_budaya, fraud, koefisien')
            ->from('nilai_akhir')
            ->where('nik', $nik)
            ->where('periode_awal', $periode_awal)
            ->where('periode_akhir', $periode_akhir)
            ->get()
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
        $this->db->join('penilai_mapping m', 'm.jabatan = p.jabatan AND m.unit_kerja = p.unit_kerja', 'left');
        $this->db->join('pegawai pen1_peg', 'pen1_peg.jabatan = (SELECT jabatan FROM penilai_mapping WHERE `key` = m.penilai1_jabatan LIMIT 1)', 'left');
        $this->db->join('pegawai pen2_peg', 'pen2_peg.jabatan = (SELECT jabatan FROM penilai_mapping WHERE `key` = m.penilai2_jabatan LIMIT 1)', 'left');
        $this->db->where('p.nik', $nik);

        return $this->db->get()->row();
    }

    public function updateStatus($id, $status)
    {
        $this->db->where('id', $id)->update('penilaian', ['status' => $status]);
        return $this->db->affected_rows();
    }

    /* ============================
       🔹 BUDAYA KERJA
    ============================ */

    public function getAllBudaya()
    {
        return $this->db->get('budaya')->result();
    }

    public function getBudayaNilaiByNik($nik, $periode_awal = null, $periode_akhir = null)
    {
        $this->db->where('nik_pegawai', $nik);
        if ($periode_awal) $this->db->where('periode_awal', $periode_awal);
        if ($periode_akhir) $this->db->where('periode_akhir', $periode_akhir);

        $result = $this->db->get('budaya_nilai')->row();

        if ($result) {
            return [
                'nilai_budaya' => json_decode($result->nilai_budaya ?? '[]', true),
                'rata_rata' => $result->rata_rata ?? 0
            ];
        }

        return ['nilai_budaya' => [], 'rata_rata' => 0];
    }

    /* ============================
       🔹 MONITORING BULANAN
    ============================ */

    public function getMonitoringBulanan($nik, $bulan, $tahun)
    {
        return $this->db->where('nik', $nik)
            ->where('bulan', $bulan)
            ->where('tahun', $tahun)
            ->get('monitoring_bulanan')
            ->row();
    }

    public function saveOrUpdateMonitoringBulanan($nik, $bulan, $tahun, $data_json, $nilai_akhir, $pencapaian_akhir = null, $predikat = null)
    {
        $data = [
            'nik' => $nik,
            'bulan' => $bulan,
            'tahun' => $tahun,
            'data_json' => json_encode($data_json),
            'nilai_akhir' => round($nilai_akhir, 2),
            'updated_at' => date('Y-m-d H:i:s')
        ];

        if ($pencapaian_akhir !== null) {
            $data['pencapaian_akhir'] = round($pencapaian_akhir, 2);
        }

        if ($predikat !== null) {
            $data['predikat'] = $predikat;
        }

        $existing = $this->getMonitoringBulanan($nik, $bulan, $tahun);

        if ($existing) {
            $this->db->where('id', $existing->id)->update('monitoring_bulanan', $data);
        } else {
            $data['created_at'] = date('Y-m-d H:i:s');
            $this->db->insert('monitoring_bulanan', $data);
        }

        return $this->db->affected_rows() > 0;
    }

    public function simpanPenilaianBarisBulanan()
    {
        $nik          = $this->input->post('nik');
        $indikator_id = $this->input->post('indikator_id');
        $target       = $this->input->post('target');
        $realisasi    = $this->input->post('realisasi');
        $pencapaian   = $this->input->post('pencapaian');
        $nilai        = $this->input->post('nilai');
        $nilai_dibobot = floatval($this->input->post('nilai_dibobot')) / 12; // dibagi 12
        $periode_awal = $this->input->post('periode_awal');

        if (!$nik || !$indikator_id) return "Invalid data";

        $bulan = date('n', strtotime($periode_awal));
        $tahun = date('Y', strtotime($periode_awal));

        $existing = $this->getMonitoringBulanan($nik, $bulan, $tahun);

        if ($existing) {
            $data_json = json_decode($existing->data_json, true) ?: [];
            $updated = false;

            foreach ($data_json as &$item) {
                if ($item['indikator_id'] == $indikator_id) {
                    $item = compact('indikator_id', 'target', 'realisasi', 'pencapaian', 'nilai', 'nilai_dibobot');
                    $updated = true;
                    break;
                }
            }

            if (!$updated) $data_json[] = compact('indikator_id', 'target', 'realisasi', 'pencapaian', 'nilai', 'nilai_dibobot');

            $total_nilai = array_sum(array_column($data_json, 'nilai_dibobot'));

            $this->db->where('id', $existing->id)->update('monitoring_bulanan', [
                'data_json' => json_encode($data_json),
                'nilai_akhir' => $total_nilai,
                'updated_at' => date('Y-m-d H:i:s')
            ]);
        } else {
            $data_json = [[
                'indikator_id' => $indikator_id,
                'target' => $target,
                'realisasi' => $realisasi,
                'pencapaian' => $pencapaian,
                'nilai' => $nilai,
                'nilai_dibobot' => $nilai_dibobot
            ]];

            $this->db->insert('monitoring_bulanan', [
                'nik' => $nik,
                'bulan' => $bulan,
                'tahun' => $tahun,
                'data_json' => json_encode($data_json),
                'nilai_akhir' => $nilai_dibobot
            ]);
        }

        return "OK";
    }

    public function getMonitoringBulananTahun($nik, $tahun)
    {
        return $this->db
            ->select('*')
            ->from('monitoring_bulanan')
            ->where('nik', $nik)
            ->where('tahun', $tahun)
            ->order_by('bulan', 'ASC')
            ->get()
            ->result();
    }
}
