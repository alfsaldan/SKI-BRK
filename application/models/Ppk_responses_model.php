<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Ppk_responses_model extends CI_Model
{
    protected $table = 'ppk_responses';
    // returns map [ id_ppk => answer ]
    public function getByNik($nik)
    {
        if (!$nik) return [];
        $row = $this->db->select('answers')->where('nik', $nik)->get($this->table)->row();
        if (!$row) return [];
        $answers = [];
        // answers stored as JSON in `answers` column (or TEXT)
        if (!empty($row->answers)) {
            $decoded = json_decode($row->answers, true);
            if (is_array($decoded)) {
                $answers = $decoded;
            }
        }
        // ensure keys are consistent (string/int)
        return $answers;
    }

    // upsert single response into JSON answers column for nik
    public function upsert($nik, $id_ppk, $answer)
    {
        if (!$nik || !$id_ppk) return false;
        $now = date('Y-m-d H:i:s');

        // fetch existing row for nik
        $row = $this->db->where('nik', $nik)->get($this->table)->row();
        if ($row) {
            $answers = [];
            if (!empty($row->answers)) {
                $decoded = json_decode($row->answers, true);
                if (is_array($decoded)) $answers = $decoded;
            }
            // set/overwrite answer for this id_ppk
            $answers[(string)$id_ppk] = $answer;
            $data = ['answers' => json_encode($answers), 'updated_at' => $now];
            return $this->db->where('id', $row->id)->update($this->table, $data);
        } else {
            $answers = [(string)$id_ppk => $answer];
            $data = ['nik' => $nik, 'answers' => json_encode($answers), 'created_at' => $now, 'updated_at' => $now];
            return $this->db->insert($this->table, $data);
        }
    }

    /**
     * Compute eligibility for a nik: returns true if every syarat_ppk has an answer 'ya' for this nik.
     * If there are no syarat defined, returns false.
     */
    public function computeEligibility($nik)
    {
        if (!$nik) return false;

        // get all syarat ids
        $syarats = $this->db->select('id_ppk')->order_by('id_ppk', 'ASC')->get('syarat_ppk')->result();
        if (!$syarats || count($syarats) === 0) return false;

        // get answers JSON for nik
        $row = $this->db->select('answers')->where('nik', $nik)->get($this->table)->row();
        $map = [];
        if ($row && !empty($row->answers)) {
            $decoded = json_decode($row->answers, true);
            if (is_array($decoded)) $map = $decoded;
        }

        // every syarat must be answered 'ya'
        foreach ($syarats as $s) {
            $key = (string)$s->id_ppk;
            $a = isset($map[$key]) ? $map[$key] : '';
            if ($a !== 'ya') return false;
        }
        return true;
    }
}