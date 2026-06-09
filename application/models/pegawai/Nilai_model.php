<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Nilai_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Ambil daftar pegawai yang dinilai oleh penilai tertentu (Penilai I)
     */
    public function getPegawaiSebagaiPenilai1($nik_penilai)
    {
        // Ambil jabatan penilai
        $penilai = $this->db->select('jabatan')->get_where('pegawai', ['nik' => $nik_penilai])->row();
        if (!$penilai) return [];

        $jabatan_penilai = $penilai->jabatan;

        $this->db->select('
        p.nik,
        p.nama,
        p.jabatan,
        p.unit_kerja,
        p.unit_kantor,
        MAX(pen1_peg.nik) AS penilai1_nik,
        MAX(pen1_peg.nama) AS penilai1_nama,
        MAX(pen1_peg.jabatan) AS penilai1_jabatan_detail,
        MAX(pen2_peg.nik) AS penilai2_nik,
        MAX(pen2_peg.nama) AS penilai2_nama,
        MAX(pen2_peg.jabatan) AS penilai2_jabatan_detail
    ');
        $this->db->from('pegawai p');
        $this->db->join('penilai_mapping m', 'm.jabatan = p.jabatan AND m.unit_kerja = p.unit_kerja', 'left');
        $this->db->join(
            'pegawai pen1_peg',
            'pen1_peg.jabatan = (SELECT jabatan FROM penilai_mapping WHERE `key` = m.penilai1_jabatan LIMIT 1)',
            'left'
        );
        $this->db->join(
            'pegawai pen2_peg',
            'pen2_peg.jabatan = (SELECT jabatan FROM penilai_mapping WHERE `key` = m.penilai2_jabatan LIMIT 1)',
            'left'
        );
        $this->db->where('pen1_peg.nik', $nik_penilai);
        $this->db->group_by('p.nik');

        return $this->db->get()->result();
    }

    /**
     * Ambil daftar pegawai yang dinilai oleh penilai tertentu (Penilai II)
     */
    public function getPegawaiSebagaiPenilai2($nik_penilai)
    {
        // Ambil jabatan penilai
        $penilai = $this->db->select('jabatan')->get_where('pegawai', ['nik' => $nik_penilai])->row();
        if (!$penilai) return [];

        $jabatan_penilai = $penilai->jabatan;

        $this->db->select('
        p.nik,
        p.nama,
        p.jabatan,
        p.unit_kerja,
        p.unit_kantor,
        MAX(pen1_peg.nik) AS penilai1_nik,
        MAX(pen1_peg.nama) AS penilai1_nama,
        MAX(pen1_peg.jabatan) AS penilai1_jabatan_detail,
        MAX(pen2_peg.nik) AS penilai2_nik,
        MAX(pen2_peg.nama) AS penilai2_nama,
        MAX(pen2_peg.jabatan) AS penilai2_jabatan_detail
    ');
        $this->db->from('pegawai p');
        $this->db->join('penilai_mapping m', 'm.jabatan = p.jabatan AND m.unit_kerja = p.unit_kerja', 'left');
        $this->db->join(
            'pegawai pen1_peg',
            'pen1_peg.jabatan = (SELECT jabatan FROM penilai_mapping WHERE `key` = m.penilai1_jabatan LIMIT 1)',
            'left'
        );
        $this->db->join(
            'pegawai pen2_peg',
            'pen2_peg.jabatan = (SELECT jabatan FROM penilai_mapping WHERE `key` = m.penilai2_jabatan LIMIT 1)',
            'left'
        );
        $this->db->where('pen2_peg.nik', $nik_penilai);
        $this->db->group_by('p.nik');

        return $this->db->get()->result();
    }

    /**
     * Ambil detail pegawai + penilai1 & penilai2 (mirip Pegawai_model::getPegawaiWithPenilai)
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
     * Ambil indikator/penilaian untuk seorang pegawai pada rentang periode.
     */
    // Ambil indikator / penilaian sesuai periode
    public function getIndikatorPegawai($nik, $periode_awal, $periode_akhir)
    {
        $this->db->select('
        p.*,
        s.perspektif,
        s.sasaran_kerja,
        i.indikator,
        i.bobot
    ');
        $this->db->from('penilaian p');
        $this->db->join('indikator i', 'p.indikator_id = i.id', 'left');
        $this->db->join('sasaran_kerja s', 'i.sasaran_id = s.id', 'left');
        $this->db->where('p.nik', $nik);

        // filter pakai periode (bukan batas_waktu)
        $this->db->where('p.periode_awal', $periode_awal);
        $this->db->where('p.periode_akhir', $periode_akhir);

        $this->db->order_by('s.perspektif', 'ASC');
        $this->db->order_by('s.sasaran_kerja', 'ASC');

        return $this->db->get()->result();
    }

    /**
     * Update status sebuah baris penilaian
     */
    public function updateStatus($id, $status)
    {
        if (empty($id) || empty($status)) return false;
        return $this->db->where('id', $id)->update('penilaian', ['status' => $status]);
    }

    // Update status + realisasi sekaligus
    public function updateStatusAndRealisasi($id, $status, $data)
    {
        if (empty($id)) return false;

        $updateData = ['status' => $status];

        // kalau ada data tambahan (realisasi, pencapaian, nilai, nilai_dibobot) ikut update
        if (!empty($data['realisasi'])) {
            $updateData['realisasi'] = $data['realisasi'];
        }
        if (!empty($data['pencapaian'])) {
            $updateData['pencapaian'] = $data['pencapaian'];
        }
        if (!empty($data['nilai'])) {
            $updateData['nilai'] = $data['nilai'];
        }
        if (!empty($data['nilai_dibobot'])) {
            $updateData['nilai_dibobot'] = $data['nilai_dibobot'];
        }

        return $this->db->where('id', $id)->update('penilaian', $updateData);
    }

    // Tambah catatan penilai
    public function tambahCatatan($data)
    {
        return $this->db->insert('catatan_penilai', $data);
    }

    // Ambil catatan per pegawai
    public function getCatatanByPegawai($nik_pegawai)
    {
        $this->db->select('c.*, p.nama as nama_penilai');
        $this->db->from('catatan_penilai c');
        $this->db->join('pegawai p', 'p.nik = c.nik_penilai', 'left');
        $this->db->where('c.nik_pegawai', $nik_pegawai);
        $this->db->order_by('c.tanggal', 'ASC');
        return $this->db->get()->result();
    }

    public function getLockStatus($nik, $periode_awal, $periode_akhir)
    {
        $this->db->where('nik', $nik);
        $this->db->where('periode_awal', $periode_awal);
        $this->db->where('periode_akhir', $periode_akhir);
        $query = $this->db->get('penilaian');

        if ($query->num_rows() == 0) {
            return false; // tidak ada data → dianggap terbuka
        }

        foreach ($query->result() as $row) {
            if (empty($row->lock_input) || $row->lock_input == 0) {
                return false; // ada yang terbuka
            }
        }

        return true; // semua terkunci
    }

    public function getAllBudaya()
    {
        return $this->db->get('budaya')->result_array();
    }

    public function getNilaiBudayaByPegawai($nik, $periode_awal, $periode_akhir)
    {
        $this->db->select('nilai_budaya, rata_rata');
        $this->db->from('budaya_nilai');
        $this->db->where([
            'nik_pegawai'   => $nik,
            'periode_awal'  => $periode_awal,
            'periode_akhir' => $periode_akhir
        ]);
        return $this->db->get()->row();
    }



    public function simpanNilaiBudayaSatuBaris($data)
    {
        $this->db->where([
            'nik_pegawai' => $data['nik_pegawai'],
            'periode_awal' => $data['periode_awal'],
            'periode_akhir' => $data['periode_akhir']
        ]);

        $cek = $this->db->get('budaya_nilai')->row();

        if ($cek) {
            // update JSON
            $nilai = json_decode($cek->nilai_budaya, true);
            $nilai[$data['key']] = $data['skor']; // update nilai spesifik

            $rata = $data['rata_rata'];
            $this->db->where('id', $cek->id);
            return $this->db->update('budaya_nilai', [
                'nilai_budaya' => json_encode($nilai),
                'rata_rata' => $rata
            ]);
        } else {
            // insert baru
            $nilai = [$data['key'] => $data['skor']];
            return $this->db->insert('budaya_nilai', [
                'nik_pegawai' => $data['nik_pegawai'],
                'periode_awal' => $data['periode_awal'],
                'periode_akhir' => $data['periode_akhir'],
                'nilai_budaya' => json_encode($nilai),
                'rata_rata' => $data['rata_rata']
            ]);
        }
    }

    public function updateStatusAllPenilai2(array $ids_array, $status, $penilai2_nik = null)
    {
        if (empty($ids_array) || $status === null) {
            return false;
        }

        $updateData = ['status2' => $status];

        // jika ada kolom untuk mencatat siapa yang mengubah status2
        if ($penilai2_nik && $this->db->field_exists('penilai2_nik', 'penilaian')) {
            $updateData['penilai2_nik'] = $penilai2_nik;
        }
        if ($this->db->field_exists('penilai2_updated_at', 'penilaian')) {
            $updateData['penilai2_updated_at'] = date('Y-m-d H:i:s');
        }

        $this->db->where_in('id', $ids_array);
        return (bool) $this->db->update('penilaian', $updateData);
    }
}
