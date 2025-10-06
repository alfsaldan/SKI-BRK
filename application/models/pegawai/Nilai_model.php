<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Nilai_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Ambil daftar pegawai yang bisa dinilai oleh penilai tertentu
     */
    public function getPegawaiYangDinilai($nik_penilai)
    {
        // ambil jabatan penilai terlebih dahulu
        $row = $this->db->select('jabatan, unit_kerja')->get_where('pegawai', ['nik' => $nik_penilai])->row();
        if (!$row || empty($row->jabatan)) {
            return [];
        }
        $jabatan_penilai = $row->jabatan;
        $unit_penilai    = $row->unit_kerja;

        $this->db->select('p.nik, p.nama, p.jabatan, p.unit_kerja');
        $this->db->from('pegawai p');
        $this->db->join('penilai_mapping pm', 'p.jabatan = pm.jabatan AND p.unit_kerja = pm.unit_kerja');
        $this->db->group_start();
        $this->db->where('pm.penilai1_jabatan', $jabatan_penilai);
        $this->db->or_where('pm.penilai2_jabatan', $jabatan_penilai);
        $this->db->group_end();
        $this->db->group_by('p.nik');

        return $this->db->get()->result();
    }

    /**
     * Ambil detail pegawai + penilai1 & penilai2 (mirip Pegawai_model::getPegawaiWithPenilai)
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
}
