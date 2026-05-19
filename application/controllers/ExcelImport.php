<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use PhpOffice\PhpSpreadsheet\IOFactory;

class ExcelImport extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->model('Indikator_model');
        $this->load->library('session');
        $this->load->helper('file');
    }

    /**
     * AJAX endpoint: upload Excel, parse and return preview JSON
     */
    public function uploadIndikatorKinerja()
    {
        if (!isset($_FILES['excel_file']) || $_FILES['excel_file']['error'] !== UPLOAD_ERR_OK) {
            return $this->jsonError('File tidak ditemukan atau gagal diupload.');
        }

        $unit    = $this->input->post('unit_kerja') ?? $this->input->post('unit') ?? null;
        $jabatan = $this->input->post('jabatan')    ?? $this->input->post('jab')  ?? null;

        $fileTmp  = $_FILES['excel_file']['tmp_name'];
        $fileName = $_FILES['excel_file']['name'];
        $ext      = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        if (!in_array($ext, ['xls', 'xlsx'])) {
            return $this->jsonError('Format file tidak didukung. Gunakan .xls atau .xlsx');
        }

        $tmpDir  = FCPATH . 'uploads/temp/';
        if (!is_dir($tmpDir)) @mkdir($tmpDir, 0755, true);
        $tmpName = 'indikator_upload_' . time() . '_' . rand(1000, 9999) . '.' . $ext;
        $dest    = $tmpDir . $tmpName;
        if (!move_uploaded_file($fileTmp, $dest)) {
            return $this->jsonError('Gagal menyimpan file sementara.');
        }

        try {
            $reader      = IOFactory::createReaderForFile($dest);
            $spreadsheet = $reader->load($dest);
        } catch (Exception $e) {
            @unlink($dest);
            return $this->jsonError('File Excel gagal dibaca: ' . $e->getMessage());
        }

        $sheet  = $spreadsheet->getActiveSheet();
        $parsed = $this->parseIndikatorSheet($sheet);

        $totalBobot = array_sum($parsed['summary'] ?? []);
        if ($totalBobot > 100) {
            @unlink($dest);
            return $this->jsonError("Total bobot keseluruhan ({$totalBobot}%) tidak boleh melebihi 100%. Silakan sesuaikan ulang.");
        }

        $parsed['context']  = ['unit_kerja' => $unit, 'jabatan' => $jabatan];
        $parsed['tmp_file'] = 'uploads/temp/' . $tmpName;

        echo json_encode($parsed);
    }

    /**
     * AJAX endpoint: upload Excel for a specific employee, parse and return preview JSON
     */
    public function uploadIndikatorKinerjaPegawai()
    {
        if (!isset($_FILES['excel_file']) || $_FILES['excel_file']['error'] !== UPLOAD_ERR_OK) {
            return $this->jsonError('File tidak ditemukan atau gagal diupload.');
        }

        // Get context from POST data
        $unit    = $this->input->post('unit_kerja');
        $jabatan = $this->input->post('jabatan');
        $nik     = $this->input->post('nik');

        if (empty($nik) || empty($unit) || empty($jabatan)) {
            return $this->jsonError('Konteks pegawai (NIK, Unit, Jabatan) tidak lengkap.');
        }

        $fileTmp  = $_FILES['excel_file']['tmp_name'];
        $fileName = $_FILES['excel_file']['name'];
        $ext      = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        if (!in_array($ext, ['xls', 'xlsx'])) {
            return $this->jsonError('Format file tidak didukung. Gunakan .xls atau .xlsx');
        }

        $tmpDir  = FCPATH . 'uploads/temp/';
        if (!is_dir($tmpDir)) @mkdir($tmpDir, 0755, true);
        $tmpName = 'indikator_upload_pegawai_' . time() . '_' . rand(1000, 9999) . '.' . $ext;
        $dest    = $tmpDir . $tmpName;
        if (!move_uploaded_file($fileTmp, $dest)) {
            return $this->jsonError('Gagal menyimpan file sementara.');
        }

        try {
            $reader      = IOFactory::createReaderForFile($dest);
            $spreadsheet = $reader->load($dest);
        } catch (Exception $e) {
            @unlink($dest);
            return $this->jsonError('File Excel gagal dibaca: ' . $e->getMessage());
        }

        $sheet  = $spreadsheet->getActiveSheet();
        $parsed = $this->parseIndikatorSheet($sheet);

        $totalBobot = array_sum($parsed['summary'] ?? []);
        if ($totalBobot > 100) {
            @unlink($dest);
            return $this->jsonError("Total bobot keseluruhan ({$totalBobot}%) tidak boleh melebihi 100%. Silakan sesuaikan ulang.");
        }

        // Add employee context to the parsed data
        $parsed['context']  = ['unit_kerja' => $unit, 'jabatan' => $jabatan, 'nik' => $nik];
        $parsed['tmp_file'] = 'uploads/temp/' . $tmpName;

        echo json_encode($parsed);
    }

    /**
     * Save parsed data to DB for a specific employee (owner_nik).
     */
    public function saveParsedDataPegawai()
    {
        $input = file_get_contents('php://input');
        if (empty($input)) return $this->jsonError('Tidak ada data untuk disimpan.');

        $payload = json_decode($input, true);
        if (!$payload || !isset($payload['data'])) {
            return $this->jsonError('Format data tidak valid.');
        }

        $context = $payload['context'] ?? [];
        $unit    = $context['unit_kerja'] ?? null;
        $jabatan = $context['jabatan']    ?? null;
        $nik     = $context['nik']        ?? null;

        if (empty($nik) || empty($unit) || empty($jabatan)) {
            return $this->jsonError('Konteks pegawai (NIK, Unit, Jabatan) tidak lengkap untuk menyimpan data.');
        }

        $data     = $payload['data'];
        $errors   = [];
        $inserted = ['sasaran' => 0, 'indikator' => 0, 'duplicates' => 0];

        $this->db->trans_start();

        foreach ($data as $perspektif => $sasaranList) {
            foreach ($sasaranList as $sasaranText => $indikators) {
                // Check for existing sasaran specific to this employee
                $sasaranRow = $this->db->get_where('sasaran_kerja', [
                    'unit_kerja'    => $unit,
                    'jabatan'       => $jabatan,
                    'perspektif'    => $perspektif,
                    'sasaran_kerja' => $sasaranText,
                    'owner_nik'     => $nik, // Employee specific
                ])->row();

                if ($sasaranRow) {
                    $sasaran_id = $sasaranRow->id;
                } else {
                    $this->db->insert('sasaran_kerja', [
                        'unit_kerja'    => $unit,
                        'jabatan'       => $jabatan,
                        'perspektif'    => $perspektif,
                        'sasaran_kerja' => $sasaranText,
                        'owner_nik'     => $nik, // Employee specific
                    ]);
                    $sasaran_id = $this->db->insert_id();
                    $inserted['sasaran']++;
                }

                foreach ($indikators as $ind) {
                    // Check for existing indicator under this sasaran
                    $exists = $this->db->get_where('indikator', [
                        'sasaran_id' => $sasaran_id,
                        'indikator'  => $ind['indikator'],
                        'owner_nik'  => $nik, // Employee specific
                    ])->row();

                    if ($exists) { $inserted['duplicates']++; continue; }

                    $ok = $this->db->insert('indikator', [
                        'sasaran_id'  => $sasaran_id,
                        'indikator'   => $ind['indikator'],
                        'bobot'       => $ind['bobot'],
                        'owner_nik'   => $nik, // Employee specific
                    ]);

                    if ($ok) $inserted['indikator']++;
                    else $errors[] = "Gagal menyimpan indikator: {$ind['indikator']}";
                }
            }
        }

        $this->db->trans_complete();

        if (!$this->db->trans_status()) {
            return $this->jsonError('Terjadi kesalahan saat menyimpan data ke database.');
        }

        // Clean up temp file
        if (isset($payload['tmp_file']) && file_exists(FCPATH . $payload['tmp_file'])) {
            @unlink(FCPATH . $payload['tmp_file']);
        }

        echo json_encode([
            'success' => true,
            'message' => 'Data berhasil disimpan.',
            'result'  => $inserted,
            'errors'  => $errors,
        ]);
    }

    /**
     * Save parsed data to DB.
     */
    public function saveParsedData()
    {
        // Prioritaskan form POST field 'parsed' (dikirim sebagai application/x-www-form-urlencoded)
        // Fallback ke raw JSON body (application/json) jika field tidak ada
        $input = $this->input->post('parsed');
        if (empty($input)) {
            $input = file_get_contents('php://input');
        }
        if (empty($input)) return $this->jsonError('Tidak ada data untuk disimpan.');

        $payload = json_decode($input, true);
        if (!$payload || !isset($payload['data'])) {
            return $this->jsonError('Format data tidak valid.');
        }

        $context = $payload['context'] ?? [];
        $unit    = $context['unit_kerja'] ?? $this->input->post('unit_kerja');
        $jabatan = $context['jabatan']    ?? $this->input->post('jabatan');

        $data     = $payload['data'];
        $errors   = [];
        $inserted = ['sasaran' => 0, 'indikator' => 0, 'duplicates' => 0];

        $this->db->trans_start();

        foreach ($data as $perspektif => $sasaranList) {
            foreach ($sasaranList as $sasaranText => $indikators) {
                $sasaranRow = $this->db->get_where('sasaran_kerja', [
                    'unit_kerja'    => $unit,
                    'jabatan'       => $jabatan,
                    'perspektif'    => $perspektif,
                    'sasaran_kerja' => $sasaranText,
                ])->row();

                if ($sasaranRow) {
                    $sasaran_id = $sasaranRow->id;
                } else {
                    $this->db->insert('sasaran_kerja', [
                        'unit_kerja'    => $unit,
                        'jabatan'       => $jabatan,
                        'perspektif'    => $perspektif,
                        'sasaran_kerja' => $sasaranText,
                    ]);
                    $sasaran_id = $this->db->insert_id();
                    $inserted['sasaran']++;
                }

                foreach ($indikators as $ind) {
                    $exists = $this->db->get_where('indikator', [
                        'sasaran_id' => $sasaran_id,
                        'indikator'  => $ind['indikator'],
                    ])->row();

                    if ($exists) { $inserted['duplicates']++; continue; }

                    $ok = $this->db->insert('indikator', [
                        'sasaran_id'  => $sasaran_id,
                        'indikator'   => $ind['indikator'],
                        'bobot'       => $ind['bobot'],
                    ]);

                    if ($ok) $inserted['indikator']++;
                    else $errors[] = "Gagal menyimpan indikator: {$ind['indikator']}";
                }
            }
        }

        $this->db->trans_complete();

        if (!$this->db->trans_status()) {
            return $this->jsonError('Terjadi kesalahan saat menyimpan data ke database.');
        }

        echo json_encode([
            'success' => true,
            'message' => 'Data berhasil disimpan.',
            'result'  => $inserted,
            'errors'  => $errors,
        ]);
    }

    // ─────────────────────────────────────────────────────────────
    //  CORE PARSER  –  sesuai format template Book1.xlsx
    // ─────────────────────────────────────────────────────────────

    /**
     * Parse sheet dengan format baku template indikator kinerja.
     *
     * Struktur kolom template:
     *   A        – Perspektif  (muncul di baris awal perspektif & baris Sub Total)
     *   C        – Nomor sasaran  (opsional, bisa kosong jika merged dari atas)
     *   D        – Teks sasaran   (kadang di C bila merged C:F)
     *   G        – Bobot (%)      (angka atau formula SUM untuk sub-total)
     *   H        – Nomor indikator  (opsional)
     *   I        – Teks indikator
     *
     * Baris Sub Total / Total dikenali dari kolom A yang mengandung
     * kata "total" (case-insensitive).
     *
     * Kasus khusus yang ditangani:
     *  - Perspektif Keuangan: baris 4 hanya punya perspektif + sasaran,
     *    tanpa bobot & indikator (kolom G/I kosong) → dilewati (skip).
     *  - Perspektif LG: sasaran & indikator ada di baris yang sama (satu baris).
     *  - Sasaran bisa muncul di kolom C saja (merged C:F) ATAU di kolom D
     *    dengan nomor di C.
     *  - Baris indikator tanpa sasaran baru = lanjutan sasaran sebelumnya
     *    (ditandai dengan kolom C & D kosong, kolom I berisi teks indikator).
     */
    private function parseIndikatorSheet($sheet)
    {
        $result = ['data' => [], 'errors' => [], 'summary' => []];

        // Ambil nilai sel dengan resolusi merged-cell
        // PhpSpreadsheet sudah menangani merged cell otomatis lewat getCellByColumnAndRow,
        // tapi nilai hanya ada di sel kiri-atas. Kita baca langsung per kolom-huruf.

        $maxRow = $sheet->getHighestDataRow();

        // --- Temukan baris header (baris 1 adalah header default template) ---
        // Kita scan baris 1–5 untuk mencari baris yang mengandung "Perspektif"
        $dataStartRow = 1;
        for ($r = 1; $r <= min(5, $maxRow); $r++) {
            $a = $this->cellVal($sheet, 'A', $r);
            if (stripos($a, 'perspektif') !== false) {
                $dataStartRow = $r + 1; // data mulai setelah baris header utama
                break;
            }
        }

        // Lewati juga baris sub-header seperti (a), (b), (c)…
        // Jika baris dataStartRow berisi "(a)" atau sejenisnya, skip
        while ($dataStartRow <= $maxRow) {
            $a = trim($this->cellVal($sheet, 'A', $dataStartRow));
            if (preg_match('/^\([a-zA-Z]\)$/', $a)) {
                $dataStartRow++;
            } else {
                break;
            }
        }

        $currentPerspektif = null;
        $currentSasaran    = null;

        for ($r = $dataStartRow; $r <= $maxRow; $r++) {

            $valA = trim($this->cellVal($sheet, 'A', $r));
            $valC = trim($this->cellVal($sheet, 'C', $r));
            $valD = trim($this->cellVal($sheet, 'D', $r));
            $valG = trim($this->cellVal($sheet, 'G', $r));
            $valH = trim($this->cellVal($sheet, 'H', $r));
            $valI = trim($this->cellVal($sheet, 'I', $r));

            // ── 1. Skip baris kosong total ──────────────────────────────
            if ($valA === '' && $valC === '' && $valD === '' && $valG === '' && $valI === '') {
                continue;
            }

            // ── 2. Skip baris Sub Total / Total ────────────────────────
            //    Tandanya: kolom A mengandung "total" (case-insensitive)
            if ($valA !== '' && preg_match('/total/i', $valA)) {
                continue;
            }

            // ── 3. Deteksi baris Perspektif baru ───────────────────────
            //    Kolom A berisi nama perspektif dan bukan baris Sub Total
            if ($valA !== '') {
                $currentPerspektif = $this->normalize($valA);
                // Reset sasaran saat perspektif baru
                // (Tapi jangan reset jika baris ini sekaligus punya sasaran/indikator)
            }

            // ── 4. Deteksi Sasaran Kerja ────────────────────────────────
            //    Sasaran ada di:
            //      a) Kolom D  (dengan nomor di C)  → lebih prioritas
            //      b) Kolom C  (bila merged C:F, tidak ada nilai D)
            $sasaranRaw = '';
            if ($valD !== '') {
                // Ada teks di D → sasaran = D, C hanyalah nomor urut
                $sasaranRaw = $valD;
            } elseif ($valC !== '' && !preg_match('/^\d+\.?\s*$/', $valC)) {
                // C bukan sekadar nomor (1. / 2.) → C adalah sasaran
                $sasaranRaw = $valC;
            }

            if ($sasaranRaw !== '') {
                $clean = $this->stripLeadingNumbering($this->normalize($sasaranRaw));
                if ($clean !== '') {
                    $currentSasaran = $clean;
                }
            }

            // ── 5. Deteksi Indikator (kolom I) dan Bobot (kolom G) ─────
            $bobot        = $this->parseBobot($valG);
            $indikatorRaw = $valI;

            // Kasus khusus perspektif LG: sasaran & indikator di baris yang sama,
            // indikator ada di kolom H (bukan I), dan kolom C berisi sasaran.
            // Kita deteksi: kolom I kosong tapi kolom H bukan nomor → H adalah indikator
            if ($indikatorRaw === '' && $valH !== '' && !preg_match('/^\d+\.?\s*$/', $valH)) {
                $indikatorRaw = $valH;
            }

            // Jika masih kosong → tidak ada indikator di baris ini, lewati
            if ($indikatorRaw === '' || $bobot === null) {
                continue;
            }

            // Validasi konteks
            if (empty($currentPerspektif)) {
                $result['errors'][] = "Baris {$r}: indikator ditemukan tapi perspektif belum diketahui.";
                continue;
            }
            if (empty($currentSasaran)) {
                $result['errors'][] = "Baris {$r}: indikator ditemukan tapi sasaran belum diketahui.";
                continue;
            }

            $indikatorText = $this->stripLeadingNumbering($this->normalize($indikatorRaw));
            if ($indikatorText === '') continue;

            // Simpan ke struktur hasil
            $p = $currentPerspektif;
            $s = $currentSasaran;
            if (!isset($result['data'][$p]))       $result['data'][$p]    = [];
            if (!isset($result['data'][$p][$s]))   $result['data'][$p][$s] = [];

            $result['data'][$p][$s][] = [
                'indikator' => $indikatorText,
                'bobot'     => $bobot,
            ];
        }

        // ── 6. Hitung total bobot per perspektif ───────────────────────
        foreach ($result['data'] as $persp => $sasList) {
            $tot = 0;
            foreach ($sasList as $inds) {
                foreach ($inds as $it) $tot += (float) $it['bobot'];
            }
            $result['summary'][$persp] = $tot;
        }

        return $result;
    }

    // ─────────────────────────────────────────────────────────────
    //  HELPERS
    // ─────────────────────────────────────────────────────────────

    /**
     * Ambil nilai sel; untuk merged cell PhpSpreadsheet menyimpan nilai
     * di sel kiri-atas, sel lain bertipe MergedCell dan nilainya null.
     * Kita resolve dengan getCell() → getValue().
     */
    private function cellVal($sheet, string $col, int $row): string
    {
        try {
            $cell = $sheet->getCell($col . $row);
            $val  = $cell->getValue();
            if ($val === null) return '';
            // Jangan evaluasi formula – kita butuh nilai statis
            if (is_string($val) && strpos($val, '=') === 0) return '';
            return (string) $val;
        } catch (Exception $e) {
            return '';
        }
    }

    /**
     * Parse nilai bobot: angka atau angka dengan tanda %.
     * Mengembalikan float atau null jika bukan angka valid.
     */
    private function parseBobot($text): ?float
    {
        $t = trim((string) $text);
        if ($t === '') return null;
        // Formula SUM → bukan bobot data
        if (strpos($t, '=') === 0) return null;
        $t = str_replace('%', '', $t);
        $t = preg_replace('/[^0-9.,-]/', '', $t);
        if ($t === '') return null;
        // Ganti koma desimal
        if (substr_count($t, ',') > 0 && substr_count($t, '.') === 0) {
            $t = str_replace(',', '.', $t);
        }
        if (!is_numeric($t)) return null;
        return (float) $t;
    }

    /** Hapus penomoran awal seperti "1.", "1)", "i.", "II." */
    private function stripLeadingNumbering(string $s): string
    {
        $s = preg_replace('/^\s*\d+[\.)\s]+/', '', $s);
        $s = preg_replace('/^\s*[ivxlcdmIVXLCDM]+[\.)\s]+/', '', $s);
        return trim($s);
    }

    /** Normalisasi whitespace dan newline */
    private function normalize(string $v): string
    {
        $s = trim($v);
        $s = preg_replace('/[\r\n]+/', ' ', $s);  // newline → spasi
        $s = preg_replace('/\s{2,}/', ' ', $s);   // multi-spasi → satu spasi
        return trim($s);
    }

    private function jsonError(string $msg)
    {
        echo json_encode(['success' => false, 'message' => $msg]);
    }
}