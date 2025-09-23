<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Penilaian_model extends CI_Model
{
    public function get_all_penilaian()
    {
        return $this->db->get('penilaian')->result();
    }

    public function get_indikator_by_jabatan_dan_unit($jabatan, $unit_kerja, $unit_kantor = null, $nik = null, $periode_awal = null, $periode_akhir = null)
    {
        if (!$periode_awal) $periode_awal = '2025-01-01';
        if (!$periode_akhir) $periode_akhir = '2025-12-31';

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

        // ❌ JANGAN filter unit_kantor di sini, karena tabel sasaran_kerja tidak punya field itu
        // if ($unit_kantor) {
        //     $this->db->where('sasaran_kerja.unit_kantor', $unit_kantor);
        // }

        $this->db->order_by('sasaran_kerja.perspektif', 'ASC');
        $this->db->order_by('sasaran_kerja.sasaran_kerja', 'ASC');

        return $this->db->get()->result();
    }



    public function save_penilaian($nik, $indikator_id, $target, $batas_waktu, $realisasi, $periode_awal = null, $periode_akhir = null)
    {
        // 🔹 Set default periode jika null
        if (!$periode_awal)
            $periode_awal = date('Y-01-01');
        if (!$periode_akhir)
            $periode_akhir = date('Y-12-31');

        $data = [
            'nik' => $nik,
            'indikator_id' => $indikator_id,
            'target' => $target,
            'batas_waktu' => $batas_waktu,
            'realisasi' => $realisasi,
            'periode_awal' => $periode_awal,
            'periode_akhir' => $periode_akhir
        ];

        // 🔹 Cari record yang exact match dengan NIK, indikator, dan periode
        $this->db->where('nik', $nik);
        $this->db->where('indikator_id', $indikator_id);
        $this->db->where('periode_awal', $periode_awal);
        $this->db->where('periode_akhir', $periode_akhir);
        $exists = $this->db->get('penilaian')->row();

        if ($exists) {
            // 🔹 Update hanya kalau ada exact match
            $this->db->where('id', $exists->id);
            return $this->db->update('penilaian', $data);
        } else {
            // 🔹 Insert baru kalau periode berbeda
            return $this->db->insert('penilaian', $data);
        }
    }




    public function simpan_penilaian($arr_data)
    {
        $saved = true;
        foreach ($arr_data as $d) {
            $s = $this->save_penilaian(
                $d['nik'],
                $d['indikator_id'],
                $d['target'] ?? null,
                $d['batas_waktu'] ?? null,
                $d['realisasi'] ?? null,
                $d['periode_awal'] ?? null,
                $d['periode_akhir'] ?? null
            );
            if (!$s)
                $saved = false;
        }
        return $saved;
    }

    public function update_penilaian($id, $data)
    {
        // pastikan periode ada default
        if (empty($data['periode_awal']))
            $data['periode_awal'] = '2025-01-01';
        if (empty($data['periode_akhir']))
            $data['periode_akhir'] = '2025-12-31';

        return $this->db->where('id', $id)->update('penilaian', $data);
    }

    public function updatePeriodeByNik($nik, $awal, $akhir)
    {
        // default periode
        if (!$awal)
            $awal = '2025-01-01';
        if (!$akhir)
            $akhir = '2025-12-31';

        $this->db->where('nik', $nik)
            ->update('penilaian', [
                'periode_awal' => $awal,
                'periode_akhir' => $akhir
            ]);
    }

    public function getPegawaiWithPenilai($nik)
    {
        $this->db->select('
        p.nik,
        p.nama,
        p.jabatan,
        p.unit_kerja,
        p.unit_kantor,
        pen1.nik as penilai1_nik, pen1.nama as penilai1_nama, pen1.jabatan as penilai1_jabatan,
        pen2.nik as penilai2_nik, pen2.nama as penilai2_nama, pen2.jabatan as penilai2_jabatan
    ');
        $this->db->from('pegawai p');
        $this->db->join('penilai_mapping m', 'm.jabatan = p.jabatan AND m.unit_kerja = p.unit_kerja', 'left');
        $this->db->join('pegawai pen1', 'pen1.jabatan = m.penilai1_jabatan AND pen1.unit_kerja = m.unit_kerja', 'left');
        $this->db->join('pegawai pen2', 'pen2.jabatan = m.penilai2_jabatan AND pen2.unit_kerja = m.unit_kerja', 'left');
        $this->db->where('p.nik', $nik);
        return $this->db->get()->row();
    }

    public function updateStatus($id, $status)
    {
        $this->db->where('id', $id)->update('penilaian', ['status' => $status]);
        return $this->db->affected_rows();
    }
    public function getCatatanByPegawai($nik)
    {
        $this->db->select('c.*, p.nama as penilai_nama');
        $this->db->from('catatan_penilai c');
        $this->db->join('pegawai p', 'p.nik = c.nik_penilai', 'left');
        $this->db->where('c.nik_pegawai', $nik);
        $this->db->order_by('c.tanggal', 'DESC');
        return $this->db->get()->result();
    }
    public function getCatatanPegawai($nik)
    {
        $this->db->select('c.*, p.nama as penilai_nama');
        $this->db->from('catatan_pegawai c');
        $this->db->join('pegawai p', 'p.nik = c.nik', 'left');
        $this->db->where('c.nik', $nik);
        $this->db->order_by('c.tanggal', 'DESC');
        return $this->db->get()->result();
    }

    public function getPeriodeList()
    {
        return $this->db->select('periode_awal, periode_akhir')
            ->from('penilaian')
            ->group_by(['periode_awal', 'periode_akhir'])
            ->order_by('periode_awal', 'DESC')
            ->get()
            ->result();
    }
}
