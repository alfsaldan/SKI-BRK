<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Ppk_model extends CI_Model
{
    public function get_ppk_by_nik($nik)
    {
        // Menggunakan tabel nilai_akhir sebagai sumber data utama
        // Filter hanya yang predikat 'Minus'

        $this->db->select('na.id, na.periode_awal, na.periode_akhir, na.predikat');
        // Ambil data status dari tabel ppk
        $this->db->select('ppk.id as id_ppk');
        $this->db->select('COALESCE(ppk.tahap, ppk_responses.tahap) as tahap, ppk.status_pegawai, ppk.status_penilai1, ppk.status_msdi, ppk.status_pimpinanunit', FALSE);

        // Format Periode
        $this->db->select("CONCAT(DATE_FORMAT(DATE_ADD(na.periode_akhir, INTERVAL 1 DAY), '%d %M %Y'), ' - ', DATE_FORMAT(DATE_SUB(DATE_ADD(DATE_ADD(na.periode_akhir, INTERVAL 1 DAY), INTERVAL 6 MONTH), INTERVAL 1 DAY), '%d %M %Y')) as periode_ppk", FALSE);

        // Predikat Periodik (Subquery dari nilai_akhir - ambil yang terakhir untuk user ini)
        $this->db->select('(SELECT predikat FROM nilai_akhir na2 WHERE na2.nik COLLATE utf8mb4_unicode_ci = na.nik COLLATE utf8mb4_unicode_ci ORDER BY na2.periode_akhir DESC LIMIT 1) as predikat_periodik', FALSE);

        $this->db->from('nilai_akhir na');
        // Join ke tabel ppk, dengan asumsi nama tabel 'ppk' dan FK 'id_nilai_akhir'
        $this->db->join('ppk', "ppk.nik COLLATE utf8mb4_unicode_ci = na.nik COLLATE utf8mb4_unicode_ci AND ppk.periode_ppk = CONCAT(DATE_FORMAT(DATE_ADD(na.periode_akhir, INTERVAL 1 DAY), '%d %M %Y'), ' - ', DATE_FORMAT(DATE_SUB(DATE_ADD(DATE_ADD(na.periode_akhir, INTERVAL 1 DAY), INTERVAL 6 MONTH), INTERVAL 1 DAY), '%d %M %Y'))", 'left', FALSE);
        $this->db->join('ppk_responses', "ppk_responses.nik COLLATE utf8mb4_unicode_ci = na.nik COLLATE utf8mb4_unicode_ci", 'left', FALSE);

        $this->db->where('na.nik', $nik);
        $this->db->where('na.predikat', 'Minus');
        $this->db->order_by('na.periode_akhir', 'DESC');

        return $this->db->get()->result();
    }

    public function get_ppk_by_id($id)
    {
        // $id di sini adalah id dari nilai_akhir
        // Mengambil data dari ppk yang berelasi dengan nilai_akhir
        $this->db->select('na.id as id_nilai_akhir, na.nik, na.periode_awal, na.periode_akhir, na.predikat');
        $this->db->select('ppk.*'); // Ambil semua kolom dari tabel ppk

        $this->db->from('nilai_akhir na');
        $this->db->join('ppk', "ppk.nik = na.nik AND ppk.periode_ppk = CONCAT(DATE_FORMAT(DATE_ADD(na.periode_akhir, INTERVAL 1 DAY), '%d %M %Y'), ' - ', DATE_FORMAT(DATE_SUB(DATE_ADD(DATE_ADD(na.periode_akhir, INTERVAL 1 DAY), INTERVAL 6 MONTH), INTERVAL 1 DAY), '%d %M %Y'))", 'left');

        $this->db->where('na.id', $id);
        return $this->db->get()->row();
    }

    public function get_sasaran_by_ppk($id_ppk)
    {
        // Asumsi nama tabel sasaran adalah 'ppk_sasaran'
        if ($this->db->table_exists('ppk_sasaran')) {
            return $this->db->get_where('ppk_sasaran', ['id_ppk' => $id_ppk])->result();
        }
        return [];
    }

    public function save_or_update_ppk($data)
    {
        // Cek apakah record dengan NIK dan periode_ppk yang sama sudah ada
        $this->db->where('nik', $data['nik']);
        $this->db->where('periode_ppk', $data['periode_ppk']);
        $query = $this->db->get('ppk');
        $existing_ppk = $query->row();

        if ($existing_ppk) {
            // Jika ada, lakukan UPDATE
            $this->db->where('id', $existing_ppk->id);
            return $this->db->update('ppk', $data);
        } else {
            // Jika tidak ada, lakukan INSERT
            return $this->db->insert('ppk', $data);
        }
    }

    public function get_ppk_list_by_niks($niks)
    {
        if (empty($niks)) return [];

        $this->db->select('na.id as id_nilai_akhir, na.nik, p.nama, p.jabatan, p.unit_kerja, na.periode_awal, na.periode_akhir, na.predikat');
        $this->db->select('COALESCE(ppk.tahap, ppk_responses.tahap) as tahap, ppk.id as id_ppk, ppk.status_pegawai, ppk.status_penilai1, ppk.status_msdi, ppk.status_pimpinanunit', FALSE);

        // Format Periode
        $this->db->select("CONCAT(DATE_FORMAT(DATE_ADD(na.periode_akhir, INTERVAL 1 DAY), '%d %M %Y'), ' - ', DATE_FORMAT(DATE_SUB(DATE_ADD(DATE_ADD(na.periode_akhir, INTERVAL 1 DAY), INTERVAL 6 MONTH), INTERVAL 1 DAY), '%d %M %Y')) as periode_ppk", FALSE);

        // Predikat Periodik
        $this->db->select('(SELECT predikat FROM nilai_akhir na2 WHERE na2.nik COLLATE utf8mb4_unicode_ci = na.nik COLLATE utf8mb4_unicode_ci ORDER BY na2.periode_akhir DESC LIMIT 1) as predikat_periodik', FALSE);

        $this->db->from('nilai_akhir na');
        $this->db->join('pegawai p', 'p.nik = na.nik');
        $this->db->join('ppk', "ppk.nik COLLATE utf8mb4_unicode_ci = na.nik COLLATE utf8mb4_unicode_ci AND ppk.periode_ppk = CONCAT(DATE_FORMAT(DATE_ADD(na.periode_akhir, INTERVAL 1 DAY), '%d %M %Y'), ' - ', DATE_FORMAT(DATE_SUB(DATE_ADD(DATE_ADD(na.periode_akhir, INTERVAL 1 DAY), INTERVAL 6 MONTH), INTERVAL 1 DAY), '%d %M %Y'))", 'left', FALSE);
        $this->db->join('ppk_responses', "ppk_responses.nik COLLATE utf8mb4_unicode_ci = na.nik COLLATE utf8mb4_unicode_ci", 'left', FALSE);

        $this->db->where_in('na.nik', $niks);
        $this->db->where('na.predikat', 'Minus');
        $this->db->order_by('na.periode_akhir', 'DESC');

        return $this->db->get()->result();
    }

    // --- Tambahan untuk PPK Evaluasi ---

    public function get_ppk_row($id_ppk)
    {
        return $this->db->get_where('ppk', ['id' => $id_ppk])->row();
    }

    public function get_evaluasi_by_ppk($id_ppk)
    {
        return $this->db->get_where('ppk_evaluasi', ['id_ppk' => $id_ppk])->row();
    }

    public function save_evaluasi($data)
    {
        $this->db->where('id_ppk', $data['id_ppk']);
        $query = $this->db->get('ppk_evaluasi');

        if ($query->num_rows() > 0) {
            $id = $query->row()->id;
            $this->db->where('id', $id);
            $data['updated_at'] = date('Y-m-d H:i:s');
            return $this->db->update('ppk_evaluasi', $data);
        } else {
            return $this->db->insert('ppk_evaluasi', $data);
        }
    }
}
