<?php
defined('BASEPATH') or exit('No direct script access allowed');

class DataPegawai_model extends CI_Model
{

    public function getAllPegawai()
    {
        $this->db->select('p.*, r.status as riwayat_status');
        $this->db->from('pegawai p');
        $this->db->join(
            '(SELECT r1.nik, r1.status 
          FROM riwayat_jabatan r1
          INNER JOIN (
              SELECT nik, MAX(tgl_mulai) as tgl_terakhir
              FROM riwayat_jabatan
              GROUP BY nik
          ) r2 ON r1.nik = r2.nik AND r1.tgl_mulai = r2.tgl_terakhir
        ) r',
            'p.nik = r.nik',
            'left'
        );
        return $this->db->get()->result();
    }



    public function insertBatch($data)
    {
        $this->db->insert_batch('pegawai', $data);

        foreach ($data as $row) {
            $riwayat = [
                'nik' => $row['nik'],
                'jabatan' => $row['jabatan'],
                'unit_kerja' => $row['unit_kerja'],
                'unit_kantor' => $row['unit_kantor'],
                'tgl_mulai' => date('Y-m-d'),
                'tgl_selesai' => NULL,
                'status' => 'aktif'
            ];
            $this->db->insert('riwayat_jabatan', $riwayat);
        }

        return true;
    }


    public function getRiwayatJabatan($nik)
    {
        $this->db->from('riwayat_jabatan');
        $this->db->where('nik', $nik);
        $this->db->order_by('tgl_mulai', 'ASC');
        return $this->db->get()->result();
    }


    public function insertPegawai($data)
    {
        // Pastikan data sudah punya key 'unit_kantor'
        return $this->db->insert('pegawai', [
            'nik' => $data['nik'],
            'nama' => $data['nama'],
            'jabatan' => $data['jabatan'],
            'unit_kerja' => $data['unit_kerja'],
            'unit_kantor' => isset($data['unit_kantor']) ? $data['unit_kantor'] : 'Pekanbaru Sudirman',
            'password' => $data['password']
        ]);
    }


    public function getPegawaiByNik($nik)
    {
        return $this->db->get_where('pegawai', ['nik' => $nik])->row();
    }

    public function getPenilaianByNik($nik, $awal = null, $akhir = null)
    {
        $this->db->select('p.*, i.indikator, i.bobot, s.perspektif, s.sasaran_kerja');
        $this->db->from('penilaian p');
        $this->db->join('indikator i', 'p.indikator_id = i.id', 'left');
        $this->db->join('sasaran_kerja s', 'i.sasaran_id = s.id', 'left');
        $this->db->where('p.nik', $nik);

        if ($awal && $akhir) {
            $this->db->where('p.periode_awal', $awal);
            $this->db->where('p.periode_akhir', $akhir);
        }

        return $this->db->get()->result();
    }

    public function deletePegawai($nik)
    {
        return $this->db->delete('pegawai', ['nik' => $nik]);
    }
    // Tambah riwayat jabatan baru & tutup jabatan lama otomatis
    public function tambahRiwayatJabatan($nik, $jabatan_baru, $unit_baru, $unitkantor_baru, $tgl_mulai)
    {
        // Tutup jabatan lama (yang masih aktif)
        $tgl_selesai = date('Y-m-d', strtotime($tgl_mulai . ' -1 day'));
        $this->db->where('nik', $nik);
        $this->db->where('tgl_selesai IS NULL');
        $this->db->update('riwayat_jabatan', [
            'tgl_selesai' => $tgl_selesai,
            'status' => 'nonaktif'
        ]);

        // Tambah riwayat jabatan baru
        $data = [
            'nik' => $nik,
            'jabatan' => $jabatan_baru,
            'unit_kerja' => $unit_baru,
            'unit_kantor' => $unitkantor_baru,
            'tgl_mulai' => $tgl_mulai,
            'tgl_selesai' => NULL,
            'status' => 'aktif'
        ];
        $this->db->insert('riwayat_jabatan', $data);

        // 🔹 Update tabel pegawai agar ikut berubah
        $this->db->where('nik', $nik);
        $this->db->update('pegawai', [
            'jabatan' => $jabatan_baru,
            'unit_kerja' => $unit_baru,
            'unit_kantor' => $unitkantor_baru
        ]);

        return true;
    }



    // Tambah riwayat jabatan pertama saat pegawai baru dibuat
    public function insertRiwayatAwal($nik, $jabatan, $unit_kerja, $unit_kantor)
    {
        $data = [
            'nik' => $nik,
            'jabatan' => $jabatan,
            'unit_kerja' => $unit_kerja,
            'unit_kantor' => $unit_kantor,
            'tgl_mulai' => date('Y-m-d'), // otomatis hari ini
            'tgl_selesai' => NULL,
            'status' => 'aktif'
        ];
        return $this->db->insert('riwayat_jabatan', $data);
    }

    public function getAllJabatan()
    {
        $this->db->select('DISTINCT(jabatan) as jabatan');
        $query = $this->db->get('penilai_mapping');
        return $query->result();
    }

    public function getAllUnitKerja()
    {
        $this->db->select('DISTINCT(unit_kerja) as unit_kerja');
        $query = $this->db->get('penilai_mapping');
        return $query->result();
    }

    //   public function getAllUnitKantor()
    // {
    //     $this->db->select('DISTINCT(unit_kantor) as unit_kantor');
    //     $query = $this->db->get('penilai_mapping');
    //     return $query->result();
    // }
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

    public function getAvailablePeriode()
    {
        $this->db->select('periode_awal, periode_akhir');
        $this->db->from('penilaian');
        $this->db->group_by(['periode_awal', 'periode_akhir']);
        $this->db->order_by('periode_awal', 'DESC');
        return $this->db->get()->result();
    }

    // 🔹 Ambil nilai akhir pegawai dari tabel nilai_akhir
    public function getNilaiAkhirByNikPeriode($nik, $awal, $akhir)
    {
        $this->db->select('*');
        $this->db->from('nilai_akhir');
        $this->db->where('nik', $nik);
        $this->db->where('periode_awal >=', $awal);
        $this->db->where('periode_akhir <=', $akhir);
        return $this->db->get()->row(); // kembalikan object
    }

    // ✅ Ambil chat coaching antara pegawai dan semua penilainya (tidak duplikat)
    public function getCoachingChat($nikPegawai)
    {
        $this->db->select("ac.*, p.nama AS nama_pengirim, p.jabatan");
        $this->db->from("aktivitas_coaching ac");
        $this->db->join("pegawai p", "p.nik = ac.pengirim_nik", "left");
        $this->db->where("ac.nik_pegawai", $nikPegawai);
        $this->db->order_by("ac.id", "ASC");
        return $this->db->get()->result();
    }
    // Ambil chat antara pegawai & penilai
    public function getChat($nikPegawai, $lastId = 0)
    {
        $this->db->distinct();
        $this->db->select("ac.*, p.nama AS nama_pengirim, p.jabatan");
        $this->db->from("aktivitas_coaching ac");
        $this->db->join("pegawai p", "p.nik = ac.pengirim_nik", "left");
        $this->db->where("ac.nik_pegawai", $nikPegawai);

        if ($lastId > 0) {
            $this->db->where("ac.id >", $lastId);
        }

        $this->db->order_by("ac.id", "ASC");
        return $this->db->get()->result();
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
