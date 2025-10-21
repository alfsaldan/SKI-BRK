<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Administrator_model extends CI_Model
{
    public function getTotalPegawai()
    {
        return $this->db->count_all('pegawai');
    }

    public function getDashboardStats($awal = null, $akhir = null)
    {
        // Jika periode tertentu dipilih, filter data
        if ($awal && $akhir) {
            $this->db->where('periode_awal', $awal);
            $this->db->where('periode_akhir', $akhir);
        }

        // Ambil data per pegawai (per NIK)
        $this->db->select('nik,
        SUM(CASE 
            WHEN LOWER(status_penilaian)="disetujui"
             AND LOWER(status)="disetujui"
             AND LOWER(status2)="disetujui" 
            THEN 1 ELSE 0 END) AS selesai,
        SUM(CASE 
            WHEN (LOWER(status)="ada catatan" OR LOWER(status2)="ada catatan" 
                  OR LOWER(status)="belum dinilai" OR LOWER(status2)="belum dinilai")
            THEN 1 ELSE 0 END) AS proses,
        SUM(CASE 
            WHEN LOWER(status)="belum dinilai" 
             AND LOWER(status2)="belum dinilai" 
            THEN 1 ELSE 0 END) AS belum')
            ->from('penilaian')
            ->group_by('nik');
        $result = $this->db->get()->result();

        $selesai = 0;
        $proses = 0;
        $belum = 0;

        // Hitung status akhir per pegawai
        foreach ($result as $r) {
            if ($r->selesai > 0 && $r->proses == 0 && $r->belum == 0) {
                $selesai++;
            } elseif ($r->proses > 0) {
                $proses++;
            } elseif ($r->belum > 0 && $r->selesai == 0) {
                $belum++;
            }
        }

        // Pegawai tanpa data penilaian sama sekali = Belum Dinilai
        $totalPegawai = $this->db->count_all('pegawai');

        if ($awal && $akhir) {
            $this->db->where('periode_awal', $awal);
            $this->db->where('periode_akhir', $akhir);
        }
        $this->db->distinct();
        $this->db->select('nik');
        $nikAda = $this->db->get('penilaian')->result_array();

        $nikSudahAda = array_unique(array_column($nikAda, 'nik'));
        $pegawaiTanpaPenilaian = $totalPegawai - count($nikSudahAda);

        if ($pegawaiTanpaPenilaian > 0) {
            $belum += $pegawaiTanpaPenilaian;
        }

        return [
            'selesai' => $selesai,
            'proses'  => $proses,
            'belum'   => $belum
        ];
    }


    public function getPeriodeList()
    {
        return $this->db->distinct()
            ->select('periode_awal, periode_akhir')
            ->from('penilaian')
            ->order_by('periode_awal', 'DESC')
            ->get()
            ->result();
    }

    // --- Tambahan untuk data grafik per cabang & unit kantor --- //

    public function getCabangList()
    {
        // Ambil hanya 1 baris paling atas untuk setiap kode_cabang (id terkecil)
        $subquery = $this->db->select('kode_cabang, MIN(id) as min_id')
            ->from('penilai_mapping')
            ->where('kode_cabang IS NOT NULL')
            ->group_by('kode_cabang')
            ->get_compiled_select();

        return $this->db->select('pm.kode_cabang, pm.unit_kantor')
            ->from('penilai_mapping pm')
            ->join("($subquery) as uniq", 'pm.id = uniq.min_id')
            ->order_by('pm.kode_cabang', 'ASC')
            ->get()
            ->result(); // ubah dari result_array() ke result()
    }

    public function getUnitByCabang($kode_cabang)
    {
        return $this->db->query("
        SELECT DISTINCT kode_unit, unit_kantor
        FROM penilai_mapping
        WHERE kode_cabang = ?
        ORDER BY unit_kantor ASC
    ", [$kode_cabang])->result();
    }

    // Grafik semua data (default saat pertama buka)
    public function getGrafikAll()
    {
        return [
            ["Minus", rand(1, 3)],
            ["Fair", rand(5, 15)],
            ["Good", rand(25, 40)],
            ["Very Good", rand(15, 25)],
            ["Excellent", rand(5, 10)],
        ];
    }

    // Grafik per cabang (semua unit dalam cabang)
    public function getGrafikByCabang($kode_cabang)
    {
        return [
            ["Minus", rand(1, 3)],
            ["Fair", rand(5, 15)],
            ["Good", rand(25, 40)],
            ["Very Good", rand(15, 25)],
            ["Excellent", rand(5, 10)],
        ];
    }
    public function getGrafikByUnit($kode_unit)
    {
        // Contoh dummy data untuk grafik (nanti bisa disesuaikan dari tabel penilaian)
        return [
            ["Minus", rand(0, 3)],
            ["Fair", rand(5, 15)],
            ["Good", rand(25, 40)],
            ["Very Good", rand(15, 25)],
            ["Excellent", rand(5, 10)],
        ];
    }
}
