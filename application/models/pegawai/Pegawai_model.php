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

    /**
     * Ambil indikator dengan data penilaian yang diagregasi (SUM) selama satu tahun.
     */
    public function get_indikator_yearly_aggregated($jabatan, $unit_kerja, $nik, $tahun)
    {
        $periode_awal_tahun = $tahun . '-01-01';
        $periode_akhir_tahun = $tahun . '-12-31';

        $this->db->select('
            i.id,
            i.indikator,
            i.bobot,
            sk.perspektif,
            sk.sasaran_kerja,
            SUM(p.target) as target,
            MAX(p.batas_waktu) as batas_waktu,
            SUM(p.realisasi) as realisasi,
            "Rekap Tahunan" as status,
            "' . $periode_awal_tahun . '" as periode_awal,
            "' . $periode_akhir_tahun . '" as periode_akhir
        ', false); // false untuk mencegah escaping pada string periode

        $this->db->from('indikator i');
        $this->db->join('sasaran_kerja sk', 'i.sasaran_id = sk.id');

        // Join ke penilaian untuk mendapatkan data yang akan diagregasi
        $this->db->join(
            'penilaian p',
            "p.indikator_id = i.id 
            AND p.nik = " . $this->db->escape($nik) . " 
            AND YEAR(p.periode_awal) = " . $this->db->escape($tahun) . "
            AND NOT (DATE(p.periode_awal) = '" . $tahun . "-01-01' AND DATE(p.periode_akhir) = '" . $tahun . "-12-31')",
            // Kondisi di atas akan mengecualikan penilaian manual tahunan dari agregasi
            'left'
        );

        $this->db->where('sk.jabatan', $jabatan);
        $this->db->where('sk.unit_kerja', $unit_kerja);

        // Group berdasarkan indikator untuk SUM()
        $this->db->group_by('i.id, i.indikator, i.bobot, sk.perspektif, sk.sasaran_kerja');

        $this->db->order_by('sk.perspektif', 'ASC');
        $this->db->order_by('sk.sasaran_kerja', 'ASC');

        $result = $this->db->get()->result();

        // Karena nilai, pencapaian, dll tidak bisa di-SUM, kita akan hitung di PHP setelah query
        // Ini memberikan fleksibilitas jika rumus berubah.
        foreach ($result as $row) {
            $row->pencapaian = null;
            $row->nilai = null;
            $row->nilai_dibobot = null;
        }

        return $result;
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
        // Mengembalikan ke versi sederhana: hanya mengambil periode yang ada di database.
        // Tidak ada lagi pembuatan rekap tahunan otomatis.
        $this->db->select('periode_awal, periode_akhir');
        $this->db->from('penilaian');
        $this->db->where('nik', $nik);
        $this->db->group_by(['periode_awal', 'periode_akhir']);
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

        // Tambahkan kondisi untuk mengecualikan periode tahunan (1 Jan - 31 Des)
        $this->db->where("NOT (DATE_FORMAT(periode_awal, '%m-%d') = '01-01' AND DATE_FORMAT(periode_akhir, '%m-%d') = '12-31')");

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

        $rekap = [];

        // 1. Pisahkan data tahunan dan data periode
        foreach ($query as $row) {
            $tahun = date('Y', strtotime($row->periode_awal));
            $start = new DateTime($row->periode_awal);
            $end   = new DateTime($row->periode_akhir);

            // Inisialisasi rekap tahunan jika belum ada
            if (!isset($rekap[$tahun])) {
                $rekap[$tahun] = (object) [
                    'tahun' => $tahun,
                    'periode' => [],
                    'rata_nilai_sasaran' => '-',
                    'rata_nilai_budaya' => '-',
                    'rata_total_nilai' => '-',
                    'rata_nilai_akhir' => '-',
                    'rata_pencapaian' => '-',
                    'predikat_tahunan' => '-'
                ];
            }

            // 2. Cek apakah ini adalah data tahunan
            if ($start->format('m-d') == '01-01' && $end->format('m-d') == '12-31') {
                // Jika ya, langsung gunakan sebagai data rekapitulasi
                $rekap[$tahun]->rata_nilai_sasaran = round($row->nilai_sasaran, 2);
                $rekap[$tahun]->rata_nilai_budaya   = round($row->nilai_budaya, 2);
                $rekap[$tahun]->rata_total_nilai    = round($row->total_nilai, 2);
                $rekap[$tahun]->rata_nilai_akhir    = round($row->nilai_akhir, 2);
                $rekap[$tahun]->rata_pencapaian     = round(floatval(str_replace('%', '', $row->pencapaian)), 2) . '%';
                $rekap[$tahun]->predikat_tahunan    = $row->predikat;
            } else {
                // Jika bukan, masukkan ke dalam rincian periode
                $rekap[$tahun]->periode[] = (object) [
                    'periode'        => date('d M Y', strtotime($row->periode_awal)) . ' - ' . date('d M Y', strtotime($row->periode_akhir)),
                    'periode_awal'   => $row->periode_awal,
                    'periode_akhir'  => $row->periode_akhir,
                    'nilai_sasaran' => round($row->nilai_sasaran, 2),
                    'nilai_budaya'   => round($row->nilai_budaya, 2),
                    'total_nilai'    => round($row->total_nilai, 2),
                    'nilai_akhir'    => round($row->nilai_akhir, 2),
                    'pencapaian'     => round(floatval(str_replace('%', '', $row->pencapaian)), 2),
                    'predikat'       => $row->predikat,
                    'fraud'          => $row->fraud ?? '0'
                ];
            }
        }

        return array_values($rekap);
    }


    private function getPredikatTahunan($predikat_list)
    {
        $count = array_count_values($predikat_list);
        arsort($count);
        return key($count);
    }


    // Ubah Penilai
    /**
     * Ambil daftar pegawai aktif berdasarkan jabatan dan unit (opsional unit_kantor)
     */
    public function getPegawaiByJabatanAndUnit($jabatan, $unit_kerja, $unit_kantor = null, $exclude_nik = null)
    {
        $this->db->select('nik, nama, jabatan, unit_kerja, unit_kantor');
        $this->db->from('pegawai');
        $this->db->where('jabatan', $jabatan);
        $this->db->where('status', 'aktif');
        $this->db->where('unit_kerja', $unit_kerja);
        if ($unit_kantor !== null) $this->db->where('unit_kantor', $unit_kantor);
        if ($exclude_nik) $this->db->where('nik !=', $exclude_nik);
        $this->db->order_by('nama', 'ASC');
        return $this->db->get()->result();
    }

    /**
     * Update penilai (1 atau 2) untuk seorang pegawai
     */
    public function updatePenilaiForPegawai($nik_pegawai, $tipe_penilai, $penilai_nik)
    {
        if (empty($nik_pegawai) || empty($tipe_penilai)) return false;

        // Ambil data penilai
        $penilai = $this->db->get_where('pegawai', ['nik' => $penilai_nik])->row();
        if (!$penilai) return false;

        $data = [];
        if ($tipe_penilai == '1' || $tipe_penilai === 1) {
            $data['penilai1_nik'] = $penilai->nik;
            $data['penilai1_nama'] = $penilai->nama;
            $data['penilai1_jabatan_detail'] = $penilai->jabatan;
        } else {
            $data['penilai2_nik'] = $penilai->nik;
            $data['penilai2_nama'] = $penilai->nama;
            $data['penilai2_jabatan_detail'] = $penilai->jabatan;
        }

        $this->db->where('nik', $nik_pegawai);
        return $this->db->update('pegawai', $data);
    }
}
