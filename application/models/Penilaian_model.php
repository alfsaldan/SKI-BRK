<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Penilaian_model extends CI_Model
{
    public function get_all_penilaian()
    {
        return $this->db->get('penilaian')->result();
    }

    public function get_indikator_by_jabatan_dan_unit($jabatan, $unit_kerja, $nik = null, $periode_awal = null, $periode_akhir = null)
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



    public function save_penilaian($nik, $indikator_id, $target, $batas_waktu, $realisasi, $pencapaian, $nilai, $nilaidibobot, $periode_awal = null, $periode_akhir = null)
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
            'pencapaian' => $pencapaian,
            'nilai' => $nilai,
            'nilai_dibobot' => $nilaidibobot,
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

    public function save_nilai_akhir(
        $nik,
        $nilai_sasaran,
        $nilai_budaya,
        $total_nilai,
        $fraud,
        $nilai_akhir,
        $pencapaian,
        $predikat,
        $periode_awal,
        $periode_akhir,
        $koefisien // ✅ tambahan baru
    ) {
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
            'koefisien'     => $koefisien, // ✅ disimpan ke database
            'updated_at'    => date('Y-m-d H:i:s')
        ];

        // cek apakah data sudah ada
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
                $d['pencapaian'] ?? null,
                $d['nilai'] ?? null,
                $d['nilai_dibobot'] ?? null,
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

    // 🔹 Untuk catatan penilai
    public function countAllCatatanByPegawai($nik)
    {
        return $this->db->where('c.nik_pegawai', $nik)
            ->from('catatan_penilai c')
            ->count_all_results();
    }

    public function countFilteredCatatanByPegawai($nik, $search = '')
    {
        $this->db->from('catatan_penilai c');
        $this->db->join('pegawai p', 'p.nik = c.nik_penilai', 'left');
        $this->db->where('c.nik_pegawai', $nik);
        if ($search) {
            $this->db->group_start();
            $this->db->like('p.nama', $search);
            $this->db->or_like('c.catatan', $search);
            $this->db->or_like('c.tanggal', $search);
            $this->db->group_end();
        }
        return $this->db->count_all_results();
    }

    public function getCatatanByPegawaiFiltered($nik, $start, $length, $search = '', $orderColumn = 'tanggal', $sortDir = 'desc')
    {
        $this->db->select('c.*, p.nama as penilai_nama');
        $this->db->from('catatan_penilai c');
        $this->db->join('pegawai p', 'p.nik = c.nik_penilai', 'left');
        $this->db->where('c.nik_pegawai', $nik);
        if ($search) {
            $this->db->group_start();
            $this->db->like('p.nama', $search);
            $this->db->or_like('c.catatan', $search);
            $this->db->or_like('c.tanggal', $search);
            $this->db->group_end();
        }
        $this->db->order_by('c.' . $orderColumn, $sortDir);
        $this->db->limit($length, $start);
        return $this->db->get()->result();
    }

    // 🔹 Untuk catatan pegawai
    public function countAllCatatanPegawai($nik)
    {
        return $this->db->where('c.nik', $nik)
            ->from('catatan_pegawai c')
            ->count_all_results();
    }

    public function countFilteredCatatanPegawai($nik, $search = '')
    {
        $this->db->from('catatan_pegawai c');
        $this->db->where('c.nik', $nik);
        if ($search) {
            $this->db->group_start();
            $this->db->like('c.catatan', $search);
            $this->db->or_like('c.tanggal', $search);
            $this->db->group_end();
        }
        return $this->db->count_all_results();
    }

    public function getCatatanPegawaiFiltered($nik, $start, $length, $search = '', $orderColumn = 'tanggal', $sortDir = 'desc')
    {
        $this->db->select('c.*');
        $this->db->from('catatan_pegawai c');
        $this->db->where('c.nik', $nik);
        if ($search) {
            $this->db->group_start();
            $this->db->like('c.catatan', $search);
            $this->db->or_like('c.tanggal', $search);
            $this->db->group_end();
        }
        $this->db->order_by('c.' . $orderColumn, $sortDir);
        $this->db->limit($length, $start);
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

    public function getNilaiAkhir($nik, $periode_awal, $periode_akhir)
    {
        return $this->db->where('nik', $nik)
            ->where('periode_awal', $periode_awal)
            ->where('periode_akhir', $periode_akhir)
            ->get('nilai_akhir')
            ->row_array();
    }

    public function getLockStatus($periode_awal, $periode_akhir)
    {
        $row = $this->db
            ->select('lock_input')
            ->where('periode_awal', $periode_awal)
            ->where('periode_akhir', $periode_akhir)
            ->limit(1)
            ->get('penilaian')
            ->row();

        // Jika tidak ada data, anggap belum terkunci (false)
        return $row ? (bool)$row->lock_input : false;
    }

    public function setLockStatus($periode_awal, $periode_akhir, $lock)
    {
        $exists = $this->db
            ->where('periode_awal', $periode_awal)
            ->where('periode_akhir', $periode_akhir)
            ->get('penilaian')
            ->num_rows() > 0;

        if ($exists) {
            $this->db->where('periode_awal', $periode_awal)
                ->where('periode_akhir', $periode_akhir)
                ->update('penilaian', ['lock_input' => $lock]);
        } else {
            $this->db->insert('penilaian', [
                'periode_awal' => $periode_awal,
                'periode_akhir' => $periode_akhir,
                'lock_input' => $lock
            ]);
        }

        return $this->db->affected_rows() > 0;
    }

    public function tambahPeriode($periode_awal, $periode_akhir)
    {
        $data = [
            'periode_awal' => $periode_awal,
            'periode_akhir' => $periode_akhir
        ];

        // Cek apakah periode sudah ada
        $cek = $this->db->get_where('periode', $data)->row();
        if ($cek) {
            return ['status' => 'error', 'message' => 'Periode sudah ada'];
        }

        $this->db->insert('periode', $data);
        if ($this->db->affected_rows() > 0) {
            return ['status' => 'success', 'message' => 'Periode berhasil ditambahkan'];
        } else {
            return ['status' => 'error', 'message' => 'Gagal menambahkan periode'];
        }
    }

    public function getAllPeriode()
    {
        return $this->db->get('periode')->result();
    }


    // ===============================================
    // ✅ VERIFIKASI PENILAIAN PEGAWAI
    // ===============================================
    public function getPenilaianByNik($nik)
    {
        return $this->db
            ->select('p.*, i.indikator AS nama_indikator, i.bobot')
            ->from('penilaian p')
            ->join('indikator i', 'p.indikator_id = i.id', 'left')
            ->where('p.nik', $nik)
            ->order_by('i.id', 'ASC')
            ->get()
            ->result();
    }


    public function updateStatusPenilaian($nik, $status)
    {
        // Backwards compatible: if $status is actually $status and next params provided, handle later
        $args = func_get_args();
        if (count($args) >= 4) {
            $nik = $args[0];
            $status = $args[1];
            $awal = $args[2];
            $akhir = $args[3];
            return $this->db->where('nik', $nik)
                ->where('periode_awal', $awal)
                ->where('periode_akhir', $akhir)
                ->update('penilaian', ['status_penilaian' => $status]);
        }

        // Default: update all records for nik
        return $this->db->where('nik', $nik)->update('penilaian', ['status_penilaian' => $status]);
    }

    public function getStatusPenilaian($nik, $awal, $akhir)
    {
        $row = $this->db->select('status_penilaian')
            ->from('penilaian')
            ->where('nik', $nik)
            ->where('periode_awal', $awal)
            ->where('periode_akhir', $akhir)
            ->limit(1)
            ->get()->row();
        return $row->status_penilaian ?? 'pending';
    }

    public function getPegawaiPenilaian($awal, $akhir)
    {
        return $this->db->select('p.nik, pg.nama_pegawai, pg.jabatan, p.status_penilaian')
            ->from('penilaian p')
            ->join('pegawai pg', 'pg.nik = p.nik', 'left')
            ->where('p.periode_awal', $awal)
            ->where('p.periode_akhir', $akhir)
            ->group_by('p.nik')
            ->get()->result();
    }

    public function getPegawaiDetail($nik)
    {
        return $this->db->select('pg.*, a.nama_pegawai AS penilai1_nama, b.nama_pegawai AS penilai2_nama')
            ->from('pegawai pg')
            ->join('pegawai a', 'a.nik = pg.penilai1', 'left')
            ->join('pegawai b', 'b.nik = pg.penilai2', 'left')
            ->where('pg.nik', $nik)
            ->get()->row();
    }

    public function getPenilaianDetail($nik, $awal, $akhir)
    {
        return $this->db->select('sk.perspektif, sk.sasaran_kerja, i.indikator, i.bobot, 
                              p.target, p.batas_waktu, p.realisasi, p.pencapaian, 
                              p.nilai, p.nilai_dibobot')
            ->from('penilaian p')
            ->join('indikator i', 'i.id = p.indikator_id', 'left')
            ->join('sasaran_kerja sk', 'sk.id = i.sasaran_id', 'left')
            ->where('p.nik', $nik)
            ->where('p.periode_awal', $awal)
            ->where('p.periode_akhir', $akhir)
            ->order_by('sk.perspektif, sk.id, i.id')
            ->get()->result();
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

    public function getLockStatus2($periode_awal, $periode_akhir)
    {
        $row = $this->db
            ->select('lock_input2')
            ->where('periode_awal', $periode_awal)
            ->where('periode_akhir', $periode_akhir)
            ->limit(1)
            ->get('penilaian')
            ->row();

        return $row ? (bool)$row->lock_input2 : false;
    }

    public function setLockStatus2($periode_awal, $periode_akhir, $lock)
    {
        $exists = $this->db
            ->where('periode_awal', $periode_awal)
            ->where('periode_akhir', $periode_akhir)
            ->get('penilaian')
            ->num_rows() > 0;

        if ($exists) {
            $this->db->where('periode_awal', $periode_awal)
                ->where('periode_akhir', $periode_akhir)
                ->update('penilaian', ['lock_input2' => $lock]);
        } else {
            $this->db->insert('penilaian', [
                'periode_awal' => $periode_awal,
                'periode_akhir' => $periode_akhir,
                'lock_input2' => $lock
            ]);
        }

        return $this->db->affected_rows() > 0;
    }
}
