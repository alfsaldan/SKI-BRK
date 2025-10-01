<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Coaching_model extends CI_Model
{
    /**
     * Simpan pesan coaching ke database
     */
    public function simpanPesan($data)
    {
        $result = $this->db->insert('aktivitas_coaching', $data);
        if (!$result) {
            return ['success' => false, 'error' => $this->db->error()];
        }
        return ['success' => true];
    }

    /**
     * Ambil data chat coaching berdasarkan NIK pegawai
     */
    public function getChat($nikPegawai, $lastId = 0)
    {
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

    /**
     * Tandai semua pesan sebagai sudah dibaca berdasarkan peran user
     */
    public function clearUnread($nik)
    {
        // kalau user adalah pegawai
        $this->db->where('nik_pegawai', $nik)
                 ->where('is_read_pegawai', 0)
                 ->update('aktivitas_coaching', ['is_read_pegawai' => 1]);

        // kalau user adalah penilai1
        $this->db->where('nik_penilai1', $nik)
                 ->where('is_read_penilai1', 0)
                 ->update('aktivitas_coaching', ['is_read_penilai1' => 1]);

        // kalau user adalah penilai2
        $this->db->where('nik_penilai2', $nik)
                 ->where('is_read_penilai2', 0)
                 ->update('aktivitas_coaching', ['is_read_penilai2' => 1]);
    }

    /**
     * Ambil daftar pesan yang belum dibaca oleh user tertentu
     */
    public function getUnreadList($nik)
    {
        $this->db->select('ac.*, p.nama as nama_pengirim');
        $this->db->from('aktivitas_coaching ac');
        $this->db->join('pegawai p', 'p.nik = ac.pengirim_nik', 'left');

        // kondisi unread berdasarkan role
        $this->db->group_start()
            ->where('(ac.nik_pegawai = "'.$nik.'" AND ac.is_read_pegawai = 0)')
            ->or_where('(ac.nik_penilai1 = "'.$nik.'" AND ac.is_read_penilai1 = 0)')
            ->or_where('(ac.nik_penilai2 = "'.$nik.'" AND ac.is_read_penilai2 = 0)')
            ->group_end();

        $query = $this->db->get();
        $list = [];
        foreach ($query->result() as $row) {
            $dt = new DateTime($row->created_at, new DateTimeZone('UTC'));
            $dt->setTimezone(new DateTimeZone('Asia/Jakarta'));
            $created_at_jkt = $dt->format('d-m-Y H:i:s');

            $list[] = [
                'nama_pengirim' => $row->nama_pengirim ?? $row->pengirim_nik,
                'pesan'         => $row->pesan,
                'created_at'    => $created_at_jkt
            ];
        }
        return $list;
    }

    /**
     * Ambil laporan aktivitas coaching berdasarkan NIK Pegawai dan periode
     */
    public function getLaporanCoaching($nikPegawai, $periode_awal, $periode_akhir)
    {
        return $this->db
            ->select("ac.*, p.nama AS nama_pengirim")
            ->from("aktivitas_coaching ac")
            ->join("pegawai p", "p.nik = ac.pengirim_nik", "left")
            ->where("ac.nik_pegawai", $nikPegawai)
            ->where("ac.created_at >=", $periode_awal)
            ->where("ac.created_at <=", $periode_akhir)
            ->order_by("ac.created_at", "ASC")
            ->get()
            ->result();
    }
}
