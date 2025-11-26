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
     * Simpan atau update data penilaian untuk satu indikator.
     */
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
        return $this->db->distinct()
            ->select('periode_awal, periode_akhir')
            ->from('penilaian')
            ->where("LOWER(status_penilaian) !=", 'selesai')
            ->order_by('periode_awal', 'DESC')
            ->get()
            ->result();
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
        $this->db->order_by('periode_awal', 'DESC'); // Urutkan dari yang terbaru
        $query = $this->db->get('nilai_akhir')->result();

        if (!$query) return [];

        $rekap = [];

        // 1. Pisahkan data tahunan dan data periode
        foreach ($query as $row) {
            $tahun = date('Y', strtotime($row->periode_awal));
            $start = new DateTime($row->periode_awal);
            $koefisien_tahunan = $row->koefisien ?? 100; // Ambil koefisien dari data terbaru
            $end   = new DateTime($row->periode_akhir);

            // Inisialisasi rekap tahunan jika belum ada
            if (!isset($rekap[$tahun])) {
                $rekap[$tahun] = (object) [
                    'tahun' => $tahun,
                    'periode_aktif' => [],   // Untuk data yang masih berjalan
                    'periode_selesai' => [], // Untuk data yang sudah selesai
                    'rata_nilai_sasaran' => '-',
                    'rata_nilai_budaya' => '-',
                    'rata_total_nilai' => '-',
                    'rata_nilai_akhir' => '-',
                    'rata_pencapaian' => '-',
                    'predikat_tahunan' => '-'
                ];
            }

            // 2. Cek apakah ini adalah data tahunan -- ubah menjadi periode 1 Okt hingga 31 Des
            $isTahunan = ($start->format('m-d') == '10-01' && $end->format('m-d') == '12-31');

            if ($isTahunan) {
                // Jika ya, simpan juga sebagai data rekapitulasi tahunan
                $rekap[$tahun]->rata_nilai_sasaran = round($row->nilai_sasaran, 2);
                $rekap[$tahun]->rata_nilai_budaya   = round($row->nilai_budaya, 2);
                $rekap[$tahun]->rata_total_nilai    = round($row->total_nilai, 2);
                $rekap[$tahun]->rata_nilai_akhir    = round($row->nilai_akhir, 2);
                $rekap[$tahun]->rata_pencapaian     = round(floatval(str_replace('%', '', $row->pencapaian)), 2) . '%';
                $rekap[$tahun]->predikat_tahunan    = $row->predikat;

                // Selain itu, tetap masukkan ke dalam rincian periode agar tercatat juga di list periode
                $periode_data = (object) [
                    'periode'        => date('d M Y', strtotime($row->periode_awal)) . ' - ' . date('d M Y', strtotime($row->periode_akhir)),
                    'periode_awal'   => $row->periode_awal,
                    'periode_akhir'  => $row->periode_akhir,
                    'nilai_sasaran' => round($row->nilai_sasaran, 2),
                    'nilai_budaya'   => round($row->nilai_budaya, 2),
                    'total_nilai'    => round($row->total_nilai, 2),
                    'nilai_akhir'    => round($row->nilai_akhir, 2),
                    'pencapaian'     => round(floatval(str_replace('%', '', $row->pencapaian)), 2),
                    'predikat'       => $row->predikat,
                    'fraud'          => $row->fraud ?? '0',
                    'is_tahunan'     => true
                ];
            } else {
                // Jika bukan data tahunan, masukkan ke dalam rincian periode
                $periode_data = (object) [
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

            // Cek apakah ada periode yang selesai (diarsip) dalam tahun ini
            $has_arsip = !empty($rekap[$tahun]->periode_selesai);

            // Pisahkan berdasarkan status penilaian
            if (isset($periode_data)) {
                if (strtolower($row->status_penilaian) === 'selesai') {
                    $rekap[$tahun]->periode_selesai[] = $periode_data;
                    $has_arsip = true; // Tandai bahwa ada arsip
                } else {
                    $rekap[$tahun]->periode_aktif[] = $periode_data;
                }
            }

        }

        // Proses perhitungan rata-rata tertimbang SETELAH semua data periode dikumpulkan
        foreach ($rekap as $tahun => &$data_tahun) {
            // Jika ada riwayat perpindahan jabatan (ada arsip), hitung ulang rekap tahunan
            if (!empty($data_tahun->periode_selesai)) {
                $semua_periode_tahun_ini = array_merge($data_tahun->periode_selesai, $data_tahun->periode_aktif);

                // Urutkan semua periode berdasarkan tanggal mulai untuk menangani overlap
                usort($semua_periode_tahun_ini, function ($a, $b) {
                    return strtotime($a->periode_awal) <=> strtotime($b->periode_awal);
                });

                $last_end_date = null;
                $total_hari = 0;
                $total_bobot_nilai_sasaran = 0;
                $total_bobot_nilai_budaya = 0;
                $total_bobot_total_nilai = 0;
                $total_bobot_nilai_akhir = 0;
                $total_bobot_pencapaian = 0;

                foreach ($semua_periode_tahun_ini as $p) {
                    $tgl_awal = new DateTime($p->periode_awal);

                    // Jika ada overlap, sesuaikan tanggal mulai periode saat ini
                    if ($last_end_date && $tgl_awal <= $last_end_date) {
                        $tgl_awal = (clone $last_end_date)->modify('+1 day');
                    }

                    $tgl_akhir = new DateTime($p->periode_akhir);

                    // Hitung durasi periode dalam hari
                    $durasi_hari = 0;
                    if ($tgl_akhir >= $tgl_awal) {
                        $durasi_hari = $tgl_akhir->diff($tgl_awal)->days + 1;
                    }

                    if ($durasi_hari <= 0) continue; // Lewati jika durasi tidak valid

                    $total_hari += $durasi_hari;

                    // Akumulasi nilai yang sudah dikalikan dengan bobot durasi
                    $total_bobot_nilai_sasaran += $p->nilai_sasaran * $durasi_hari;
                    $total_bobot_nilai_budaya += $p->nilai_budaya * $durasi_hari;
                    $total_bobot_total_nilai += $p->total_nilai * $durasi_hari;
                    $total_bobot_nilai_akhir += $p->nilai_akhir * $durasi_hari;
                    $total_bobot_pencapaian += $p->pencapaian * $durasi_hari;

                    // Simpan tanggal akhir periode ini untuk pengecekan overlap berikutnya
                    $last_end_date = $tgl_akhir;
                }

                // Hitung rata-rata tertimbang
                if ($total_hari > 0) {
                    $data_tahun->rata_nilai_sasaran = round($total_bobot_nilai_sasaran / $total_hari, 2);
                    $data_tahun->rata_nilai_budaya  = round($total_bobot_nilai_budaya / $total_hari, 2);
                    $data_tahun->rata_total_nilai   = round($total_bobot_total_nilai / $total_hari, 2);
                    $nilai_akhir_rata_rata          = round($total_bobot_nilai_akhir / $total_hari, 2);
                    $data_tahun->rata_nilai_akhir   = $nilai_akhir_rata_rata;
                    $data_tahun->rata_pencapaian    = $this->_hitungPencapaianTahunan($nilai_akhir_rata_rata, $koefisien_tahunan);
                    $data_tahun->predikat_tahunan   = $this->_hitungPredikatTahunan($nilai_akhir_rata_rata, $koefisien_tahunan);
                }
            }

            // Hanya urutkan jika ada isinya
            if (!empty($data_tahun->periode_selesai)) {
                usort($data_tahun->periode_selesai, function ($a, $b) {
                    return strtotime($a->periode_awal) <=> strtotime($b->periode_awal);
                });
            }
            if (!empty($data_tahun->periode_aktif)) {
                usort($data_tahun->periode_aktif, function ($a, $b) {
                    return strtotime($a->periode_awal) <=> strtotime($b->periode_awal);
                });
            }
        }

        return array_values($rekap);
    }

    /**
     * Menghitung predikat tahunan berdasarkan nilai akhir rata-rata tertimbang.
     */
    private function _hitungPredikatTahunan($nilaiAkhir, $koefisien = 100)
    {
        if ($nilaiAkhir === null || $nilaiAkhir === 0) return "Belum Ada Nilai";

        $koef = ($koefisien ?: 100) / 100;

        if ($nilaiAkhir < 2 * $koef) return "Minus";
        if ($nilaiAkhir < 3 * $koef) return "Fair";
        if ($nilaiAkhir < 3.5 * $koef) return "Good";
        if ($nilaiAkhir < 4.5 * $koef) return "Very Good";
        return "Excellent";
    }

    /**
     * Menghitung pencapaian tahunan berdasarkan nilai akhir rata-rata tertimbang.
     */
    private function _hitungPencapaianTahunan($nilaiAkhir, $koefisien = 100)
    {
        if ($nilaiAkhir === null) return "0%";

        $koef = ($koefisien ?: 100) / 100;
        $v = (float) $nilaiAkhir;
        $pencapaian = 0;

        if ($v < 0) {
            $pencapaian = 0;
        } else if ($v < 2 * $koef) {
            $pencapaian = ($v / 2) * 0.8 * 100;
        } else if ($v < 3 * $koef) {
            $pencapaian = 80 + (($v - 2) / 1) * 10;
        } else if ($v < 3.5 * $koef) {
            $pencapaian = 90 + (($v - 3) / 0.5) * 20;
        } else if ($v < 4.5 * $koef) {
            $pencapaian = 110 + (($v - 3.5) / 1) * 10;
        } else if ($v < 5 * $koef) {
            $pencapaian = 120 + (($v - 4.5) / 0.5) * 10;
        } else {
            $pencapaian = 130;
        }

        return round(min($pencapaian, 130), 2) . '%';
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

    /**
     * Ambil data jabatan terakhir yang nonaktif dari riwayat jabatan.
     */
    public function getJabatanSebelumnya($nik)
    {
        if (empty($nik)) return null;

        return $this->db
            ->select('jabatan, unit_kerja, unit_kantor, tgl_selesai')
            ->from('riwayat_jabatan')
            ->where('nik', $nik)
            ->where('status', 'nonaktif')
            ->order_by('tgl_selesai', 'DESC') // Ambil yang paling baru selesai
            ->limit(1)
            ->get()->row();
    }

    /**
     * Mengambil detail data pegawai (termasuk jabatan, unit, dan penilai)
     * berdasarkan NIK dan tanggal tertentu dari tabel riwayat jabatan.
     *
     * @param string $nik NIK Pegawai
     * @param string $awal Tanggal awal periode penilaian (Y-m-d)
     * @param string $akhir Tanggal akhir periode penilaian (Y-m-d)
     * @return object|null
     */
    public function get_pegawai_history_by_date($nik, $awal, $akhir)
    {
        $this->db->select('
            p.nama AS nama_pegawai,
            rj.nik,
            rj.jabatan,
            rj.unit_kantor,
            NULL AS penilai1_nama,
            NULL AS penilai2_nama
        ', FALSE);
        $this->db->from('pegawai p');
        // Join ke riwayat jabatan
        $this->db->join('riwayat_jabatan rj', 'p.nik = rj.nik', 'left');
        $this->db->where('rj.nik', $nik);
        $this->db->where('rj.status', 'nonaktif');

        // Ambil riwayat jabatan yang relevan dengan periode arsip.
        // Logikanya: cari riwayat jabatan yang tanggal selesainya paling mendekati (tapi setelah) tanggal akhir periode penilaian.
        $this->db->where('DATE(rj.tgl_selesai) >=', $akhir);

        $this->db->order_by('rj.tgl_selesai', 'ASC'); // Urutkan dari yang paling mendekati
        $this->db->limit(1);

        $query = $this->db->get();
        return $query->row();
    }

    /**
     * Mengambil semua data penilaian yang sudah selesai untuk seorang pegawai pada periode tertentu.
     *
     * @param string $nik
     * @param string $awal
     * @param string $akhir
     * @return array|null
     */
    public function get_arsip_penilaian_by_periode($nik, $awal, $akhir)
    {
        // Cek dulu apakah ada penilaian 'selesai' di tabel nilai_akhir
        $nilai_akhir_check = $this->db->get_where('nilai_akhir', [
            'nik' => $nik,
            'periode_awal' => $awal,
            'periode_akhir' => $akhir,
            'status_penilaian' => 'selesai'
        ])->row();

        if (!$nilai_akhir_check) {
            return null; // Tidak ada data arsip yang selesai untuk periode ini
        }

        // Ambil detail item penilaian dari tabel 'penilaian'
        $penilaian_items = $this->db->select('p.*, i.indikator, i.bobot, sk.sasaran_kerja, sk.perspektif')
            ->from('penilaian p')
            ->join('indikator i', 'p.indikator_id = i.id', 'left')
            ->join('sasaran_kerja sk', 'i.sasaran_id = sk.id', 'left')
            ->where('p.nik', $nik)
            ->where('p.periode_awal', $awal)
            ->where('p.periode_akhir', $akhir)
            ->get()->result();

        // Ambil nilai budaya
        $budaya_row = $this->db->get_where('budaya_nilai', ['nik_pegawai' => $nik, 'periode_awal' => $awal, 'periode_akhir' => $akhir])->row();

        return [
            'penilaian_items' => $penilaian_items,
            'budaya_nilai' => $budaya_row ? json_decode($budaya_row->nilai_budaya, true) : [],
            'rata_rata_budaya' => $budaya_row ? $budaya_row->rata_rata : 0,
            'nilai_akhir' => (array) $nilai_akhir_check,
        ];
    }

    /**
     * Mengambil semua riwayat jabatan yang sudah tidak aktif untuk seorang pegawai.
     * Riwayat ini diurutkan dari yang paling baru (tanggal selesai terbaru).
     *
     * @param string $nik NIK Pegawai
     * @return array
     */
    public function getRiwayatJabatanNonAktif($nik)
    {
        return $this->db->select('jabatan, unit_kerja, unit_kantor, tgl_mulai, tgl_selesai')
            ->from('riwayat_jabatan')
            ->where('nik', $nik)
            ->where('status', 'nonaktif') // Hanya ambil yang sudah tidak aktif
            ->order_by('tgl_selesai', 'ASC') // Urutkan dari yang terlama
            ->get()
            ->result();
    }
}
