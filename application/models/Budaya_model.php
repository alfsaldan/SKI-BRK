<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Budaya_model extends CI_Model
{
    public function getAll()
    {
        return $this->db->get('budaya')->result_array();
    }

    public function save($data)
    {
        $arr = array_values(array_filter(array_map('trim', $data['panduan_perilaku']), function($v) {
            return $v !== '';
        }));
        $data['panduan_perilaku'] = json_encode($arr, JSON_UNESCAPED_UNICODE);

        if (!empty($data['id_budaya'])) {
            $this->db->where('id_budaya', $data['id_budaya']);
            return $this->db->update('budaya', [
                'perilaku_utama' => $data['perilaku_utama'],
                'panduan_perilaku' => $data['panduan_perilaku']
            ]);
        } else {
            return $this->db->insert('budaya', [
                'perilaku_utama' => $data['perilaku_utama'],
                'panduan_perilaku' => $data['panduan_perilaku']
            ]);
        }
    }

    public function delete($id)
    {
        return $this->db->delete('budaya', ['id_budaya' => $id]);
    }
}
