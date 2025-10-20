<?php
defined('BASEPATH') or exit('No direct script access allowed');

class PenilaiMapping_model extends CI_Model
{
    private $table = 'penilai_mapping';

    /* ===============================
       PENILAI MAPPING
    =============================== */

    // Ambil semua cabang unik
    public function getAllKodeCabang()
    {
        $this->db->select('kode_cabang');
        $this->db->group_by('kode_cabang');
        $this->db->order_by('kode_cabang');
        return $this->db->get('penilai_mapping')->result();
    }

    public function getCabangWithUnitKantor()
    {
        $this->db->select('pm.kode_cabang, pm.unit_kantor, pm.kode_unit');
        $this->db->from('penilai_mapping pm');

        // Ambil normal: kode_unit = kode_cabang OR cabang 100 ambil unit 1A
        $this->db->group_start();
        $this->db->where('pm.kode_unit = pm.kode_cabang');
        $this->db->or_where('pm.kode_unit', '1A');
        $this->db->group_end();

        $this->db->group_by('pm.kode_cabang, pm.unit_kantor, pm.kode_unit');
        $this->db->order_by('pm.kode_cabang');

        $result = $this->db->get()->result();

        // Override kode_unit jadi 1A jika kode_cabang 100
        foreach ($result as $r) {
            if ($r->kode_cabang == '100') {
                $r->kode_unit = '1A';
            }
        }

        return $result;
    }


    // Ambil daftar unit per cabang
    public function getKodeUnitByCabang($kode_cabang)
    {
        $this->db->select('kode_unit, unit_kantor, unit_kerja');
        $this->db->where('kode_cabang', $kode_cabang);
        $this->db->group_by('kode_unit, unit_kantor, unit_kerja');
        $this->db->order_by('kode_unit');
        return $this->db->get('penilai_mapping')->result();
    }

    // Ambil semua mapping jabatan berdasarkan unit
    public function getMappingByKodeUnit($kode_unit)
    {
        $list = $this->db->where('kode_unit', $kode_unit)
            ->get('penilai_mapping')
            ->result();

        // Loop untuk ubah penilai1_jabatan & penilai2_jabatan dari key ke nama jabatan
        foreach ($list as $item) {
            if ($item->penilai1_jabatan) {
                $item->penilai1_jabatan = $this->getJabatanByKey($item->penilai1_jabatan);
            }
            if ($item->penilai2_jabatan) {
                $item->penilai2_jabatan = $this->getJabatanByKey($item->penilai2_jabatan);
            }
        }

        return $list;
    }

    // Ambil semua mapping jabatan berdasarkan unit
    public function getMappingByKodeUnitEdit($kode_unit)
    {
        // Ambil data utama berdasarkan kode_unit
        $list = $this->db->where('kode_unit', $kode_unit)
            ->get('penilai_mapping')
            ->result();

        // Ambil tambahan jabatan dengan key tertentu
        $tambahan = $this->db->where_in('`key`', ['3', '3a', '3b', '3c', '3d', '15'])
            ->get('penilai_mapping')
            ->result();

        // Gabungkan hasilnya
        $list = array_merge($tambahan, $list);

        // Loop untuk ubah penilai1_jabatan & penilai2_jabatan dari key ke nama jabatan
        foreach ($list as $item) {
            if ($item->penilai1_jabatan) {
                $item->penilai1_jabatan = $this->getJabatanByKey($item->penilai1_jabatan);
            }
            if ($item->penilai2_jabatan) {
                $item->penilai2_jabatan = $this->getJabatanByKey($item->penilai2_jabatan);
            }
        }

        return $list;
    }


    // Ambil mapping lengkap (bisa dipakai untuk ekspor atau debugging)
    public function getAllMapping()
    {
        $this->db->order_by('kode_cabang, kode_unit, jabatan');
        return $this->db->get('penilai_mapping')->result();
    }

    // Ambil nama jabatan dari key
    public function getJabatanByKey($key)
    {
        $row = $this->db->select('jabatan')
            ->from('penilai_mapping')
            ->where('key', $key)
            ->get()
            ->row();
        return $row ? $row->jabatan : null;
    }


    // Tambah atau update mapping jabatan
    public function saveMapping($data, $id = null)
    {
        if ($id) {
            // update berdasarkan ID
            $this->db->where('id', $id);
            return $this->db->update('penilai_mapping', $data);
        } else {
            // insert baru jika belum ada
            $exists = $this->db->get_where('penilai_mapping', [
                'kode_cabang' => $data['kode_cabang'],
                'kode_unit'   => $data['kode_unit'],
                'jabatan'     => $data['jabatan']
            ])->row();

            if ($exists) {
                $this->db->where('id', $exists->id);
                return $this->db->update('penilai_mapping', $data);
            } else {
                return $this->db->insert('penilai_mapping', $data);
            }
        }
    }


    public function getKeyByJabatanAndUnit($jabatan, $kode_unit)
    {
        // Coba cari jabatan di unit yang sama dulu
        $row = $this->db->select('`key`')
            ->from('penilai_mapping')
            ->where('jabatan', $jabatan)
            ->where('kode_unit', $kode_unit)
            ->get()
            ->row();

        // Jika tidak ketemu di unit tersebut, ambil dari daftar global (3–15)
        if (!$row) {
            $row = $this->db->select('`key`')
                ->from('penilai_mapping')
                ->where('jabatan', $jabatan)
                ->where_in('`key`', ['3', '3a', '3b', '3c', '3d', '15'])
                ->get()
                ->row();
        }

        return $row ? $row->key : null;
    }

    // Ambil unit_kantor berdasarkan kode_unit
    public function getUnitKantorByKodeUnit($kode_unit)
    {
        $row = $this->db
            ->select('unit_kantor')
            ->where('kode_unit', $kode_unit)
            ->limit(1)
            ->get('penilai_mapping')
            ->row();

        return $row ? $row->unit_kantor : null;
    }

    public function getUnitKerjaByKode($kode_cabang, $kode_unit)
    {
        $row = $this->db
            ->select('unit_kerja')
            ->where('kode_cabang', $kode_cabang)
            ->where('kode_unit', $kode_unit)
            ->get('penilai_mapping')
            ->row();

        return $row ? $row->unit_kerja : 'Kantor Pusat'; // fallback
    }

    // Hapus mapping berdasarkan ID
    public function deleteMapping($id)
    {
        return $this->db->delete('penilai_mapping', ['id' => $id]);
    }
}
