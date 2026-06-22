<!-- ============================================================== -->
<!-- START: DETAIL VERIFIKASI PENILAIAN -->
<!-- ============================================================== -->
<div class="content-page">
    <div class="content">
        <div class="container-fluid">

            <!-- Judul Halaman -->
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-flex justify-content-between align-items-center">
                        <h3 class="page-title">
                            <i class="mdi mdi-clipboard-check-outline mr-2 text-primary"></i> Detail Verifikasi
                            Penilaian Pegawai
                        </h3>
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="#">KPI Online-BRKS</a></li>
                            <li class="breadcrumb-item active">Detail Verifikasi Penilaian Pegawai</li>
                        </ol>
                    </div>

                </div>
            </div>

            <!-- Pilih Periode (untuk menyesuaikan periode yang dilihat) -->
            <div class="row mb-3">
                <div class="col-12">
                    <form id="form-periode" method="get"
                        action="<?= base_url('Administrator/detailVerifikasi/' . ($pegawai_detail->nik ?? '')) ?>">
                        <div class="d-flex gap-2 align-items-center">
                            <label class="mb-0">Pilih Periode:</label>
                            <select id="select_periode" class="form-control" style="max-width:350px;">
                                <?php if (!empty($periode_list)): ?>
                                    <?php foreach ($periode_list as $p):
                                        $val = $p->periode_awal . '|' . $p->periode_akhir;
                                        $label = date('d M Y', strtotime($p->periode_awal)) . ' - ' . date('d M Y', strtotime($p->periode_akhir));
                                        $selected = ($selected_awal == $p->periode_awal && $selected_akhir == $p->periode_akhir) ? 'selected' : '';
                                        ?>
                                        <option value="<?= $val ?>" <?= $selected ?>><?= $label ?></option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                            <input type="hidden" name="awal" id="form_awal"
                                value="<?= htmlspecialchars($selected_awal ?? '', ENT_QUOTES) ?>">
                            <input type="hidden" name="akhir" id="form_akhir"
                                value="<?= htmlspecialchars($selected_akhir ?? '', ENT_QUOTES) ?>">
                            <button type="submit" class="btn btn-outline-primary">Terapkan</button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Judul Halaman -->
            <div class="row mb-3">
                <div class="col-12 d-flex justify-content-between align-items-center">
                    <?php if ($status_penilaian == 'disetujui'): ?>
                        <span class="badge bg-success px-3 py-2 shadow-sm fs-6">
                            ✅ Sudah Diverifikasi
                        </span>
                    <?php elseif ($status_penilaian == 'ditolak'): ?>
                        <span class="badge bg-danger px-3 py-2 shadow-sm fs-6">
                            ❌ Ditolak
                        </span>
                    <?php else: ?>
                        <span class="badge bg-warning text-dark px-3 py-2 shadow-sm fs-6">
                            ⏳ Belum Diverifikasi
                        </span>
                    <?php endif; ?>

                    <div>
                    <?php
                    $bukti_file = '';
                    if (!empty($penilaian)) {
                        foreach ($penilaian as $row) {
                            if (!empty($row->bukti)) {
                                $bukti_file = $row->bukti;
                                break;
                            }
                        }
                    }
                    ?>
                    <?php if (!empty($bukti_file)): ?>
                        <a href="<?= base_url('uploads/bukti/' . htmlspecialchars($bukti_file, ENT_QUOTES, 'UTF-8')) ?>" target="_blank" class="btn btn-outline-info btn-sm shadow-sm font-weight-bold">
                            <i class="mdi mdi-download"></i> Unduh Bukti Kinerja
                        </a>
                    <?php else: ?>
                        <button type="button" class="btn btn-outline-secondary btn-sm shadow-sm" disabled>
                            <i class="mdi mdi-file-hidden"></i> Belum Ada Bukti
                        </button>
                    <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Data Pegawai -->
            <div class="card shadow-lg rounded-4 mb-4">
                <div class="card-header bg-primary text-white rounded-top-4">
                    <h5 class="mb-0">
                        <i class="mdi mdi-account-circle-outline"></i> Data Pegawai
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row gy-3">
                        <div class="col-md-4">
                            <label class="fw-bold text-secondary">Nama Pegawai</label>
                            <input type="text" class="form-control" readonly
                                value="<?= htmlspecialchars($pegawai_detail->nama_pegawai ?? '-', ENT_QUOTES, 'UTF-8') ?>">
                        </div>
                        <div class="col-md-4">
                            <label class="fw-bold text-secondary">NIK</label>
                            <input type="text" class="form-control" readonly
                                value="<?= htmlspecialchars($pegawai_detail->nik ?? '-', ENT_QUOTES, 'UTF-8') ?>">
                        </div>
                        <div class="col-md-4">
                            <label class="fw-bold text-secondary">Jabatan</label>
                            <input type="text" class="form-control" readonly
                                value="<?= htmlspecialchars($pegawai_detail->jabatan ?? '-', ENT_QUOTES, 'UTF-8') ?>">
                        </div>
                        <div class="col-md-4">
                            <label class="fw-bold text-secondary">Unit Kantor</label>
                            <input type="text" class="form-control" readonly
                                value="<?= htmlspecialchars($pegawai_detail->unit_kantor ?? '-', ENT_QUOTES, 'UTF-8') ?>">
                        </div>
                        <div class="col-md-4">
                            <label class="fw-bold text-secondary">Penilai I</label>
                            <input type="text" class="form-control" readonly
                                value="<?= htmlspecialchars($pegawai_detail->penilai1_nama ?? '-', ENT_QUOTES, 'UTF-8') ?>">
                        </div>
                        <div class="col-md-4">
                            <label class="fw-bold text-secondary">Penilai II</label>
                            <input type="text" class="form-control" readonly
                                value="<?= htmlspecialchars($pegawai_detail->penilai2_nama ?? '-', ENT_QUOTES, 'UTF-8') ?>">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Daftar Penilaian -->
            <div class="card shadow-lg rounded-4 mb-4">
                <div class="card-header bg-success text-white rounded-top-4">
                    <h5 class="mb-0">
                        <i class="mdi mdi-format-list-bulleted"></i>
                        Daftar Indikator Penilaian
                    </h5>
                </div>
                <div class="card-body table-responsive">

                    <?php
                    // ======================
                    // Group data by Perspektif & Sasaran
                    // ======================
                    $grouped = [];
                    if (!empty($penilaian)) {
                        foreach ($penilaian as $row) {
                            $pers = trim($row->perspektif ?? '');
                            if (empty($pers)) {
                                continue; // Abaikan baris jika perspektif kosong
                            }
                            $sas = $row->sasaran_kerja ?? '-';
                            if (!isset($grouped[$pers]))
                                $grouped[$pers] = [];
                            if (!isset($grouped[$pers][$sas]))
                                $grouped[$pers][$sas] = [];
                            $grouped[$pers][$sas][] = $row;
                        }
                    }

                    // ======================
                    // Hitung total bobot keseluruhan untuk perhitungan nilai_dibobot
                    // ======================
                    $total_bobot_all = 0;
                    if (!empty($penilaian)) {
                        foreach ($penilaian as $row) {
                            $total_bobot_all += floatval($row->bobot ?? 0);
                        }
                    }

                    // ======================
                    // Hitung subtotal & total dari DB
                    // ======================
                    function hitungPencapaianOtomatisPHP($target, $realisasi, $indikatorText = "") {
                        $indikatorText = strtolower(trim($indikatorText ?? ""));
                        $keywords = [
                            'rumus1' => ["biaya", "beban", "efisiensi", "npf pembiayaan", "npf nominal"],
                            'rumus3' => ["outstanding", "pertumbuhan"]
                        ];

                        $containsKeyword = function($list, $text) {
                            foreach ($list as $k) {
                                if (preg_match("/\b" . preg_quote($k, '/') . "\b/i", $text)) {
                                    return true;
                                }
                            }
                            return false;
                        };

                        $pencapaian = 0;
                        $target = (float)$target;
                        $realisasi = (float)$realisasi;

                        if ($target === 0.0) {
                            if ($realisasi === 0.0) {
                                $pencapaian = 130; 
                            } else {
                                $pencapaian = 0; 
                            }
                        } else if ($target <= 0.999) {
                            $pencapaian = ($realisasi / $target) * 100;
                        } else {
                            if ($containsKeyword($keywords['rumus1'], $indikatorText)) {
                                $pencapaian = (($target + ($target - $realisasi)) / $target) * 100;
                                if ($pencapaian < 0) $pencapaian = 0;
                            } else if ($containsKeyword($keywords['rumus3'], $indikatorText)) {
                                $pencapaian = (($realisasi - $target) / abs($target) + 1) * 100;
                                if ($pencapaian < 0) $pencapaian = 0;
                            } else {
                                $pencapaian = ($realisasi / $target) * 100;
                                if ($pencapaian < 0) $pencapaian = 0;
                            }
                        }

                        return min($pencapaian, 130);
                    }

                    function hitungNilaiPHP($pencapaian) {
                        $nilai = 0;
                        if ($pencapaian < 0) $nilai = 0;
                        else if ($pencapaian < 80) $nilai = ($pencapaian / 80) * 2;
                        else if ($pencapaian < 90) $nilai = 2 + (($pencapaian - 80) / 10);
                        else if ($pencapaian < 110) $nilai = 3 + (($pencapaian - 90) / 20 * 0.5);
                        else if ($pencapaian < 120) $nilai = 3.5 + (($pencapaian - 110) / 10 * 1);
                        else if ($pencapaian < 130) $nilai = 4.5 + (($pencapaian - 120) / 10 * 0.5);
                        else $nilai = 5;
                        return $nilai;
                    }

                    $pers_totals = [];
                    $global_bobot_sum = 0;
                    $global_nilai_dibobot = 0;

                    foreach ($grouped as $pers => $sasList) {
                        $p_bobot = 0;
                        $p_nilai_dibobot = 0;

                        foreach ($sasList as $sas => $items) {
                            foreach ($items as $it) {
                                $b = floatval($it->bobot ?? 0);
                                $t = floatval($it->target ?? 0);
                                $r = floatval($it->realisasi ?? 0);
                                
                                $calc_pencapaian = hitungPencapaianOtomatisPHP($t, $r, $it->indikator ?? '');
                                $calc_nilai = hitungNilaiPHP($calc_pencapaian);
                                
                                $calc_dibobot = ($total_bobot_all > 0) ? ($calc_nilai * $b / $total_bobot_all) : 0;

                                $p_bobot += $b;
                                $p_nilai_dibobot += $calc_dibobot;
                                
                                // Simpan sementara agar bisa dirender ulang nanti
                                $it->calc_pencapaian = $calc_pencapaian;
                                $it->calc_nilai = $calc_nilai;
                                $it->calc_dibobot = $calc_dibobot;
                            }
                        }

                        $pers_totals[$pers] = [
                            'bobot' => round($p_bobot, 2),
                            'nilai_dibobot' => round($p_nilai_dibobot, 2)
                        ];

                        $global_bobot_sum += $p_bobot;
                        $global_nilai_dibobot += $p_nilai_dibobot;
                    }

                    // jangan bulatkan total akhir agar presisi (number_format akan menangani display)
                    // $global_bobot_sum = round($global_bobot_sum, 2);
                    // $global_nilai_dibobot = round($global_nilai_dibobot, 2);


                    // fungsi helper kecil untuk menentukan kelas warna status
                    if (!function_exists('statusColor')) {
                        function statusColor($s)
                        {
                            $s = strtolower(trim((string) $s));
                            if ($s === 'disetujui') {
                                return 'text-success';
                            } elseif ($s === 'ada catatan' || $s === 'catatan') {
                                return 'text-warning';
                            } elseif ($s === 'belum dinilai' || $s === '' || $s === 'pending') {
                                return 'text-danger';
                            } else {
                                return 'text-muted';
                            }
                        }
                    }
                    ?>

                    <table class="table table-bordered table-hover align-middle text-center">
                        <thead class="bg-light">
                            <tr>
                                <th width="5%">No</th>
                                <th>Perspektif</th>
                                <th>Sasaran Kerja</th>
                                <th>Bobot (%)</th>
                                <th>Indikator</th>
                                <th>Target</th>
                                <th>Batas Waktu</th>
                                <th>Realisasi</th>
                                <th>Pencapaian (%)</th>
                                <th>Nilai</th>
                                <th>Nilai Dibobot</th>
                                <th>Status</th>
                                <th>Status 2</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($grouped)): ?>
                                <?php $no = 1;
                                foreach ($grouped as $pers => $sasList):
                                    $pers_rowspan = 0;
                                    foreach ($sasList as $sas => $items)
                                        $pers_rowspan += count($items);
                                    ?>
                                    <?php $firstPers = true; ?>
                                    <?php foreach ($sasList as $sas => $items): ?>
                                        <?php $sas_rowspan = count($items);
                                        $firstSas = true; ?>
                                        <?php foreach ($items as $it): ?>
                                            <tr>
                                                <td><?= $no++ ?></td>
                                                <?php if ($firstPers): ?>
                                                    <td rowspan="<?= $pers_rowspan ?>" class="text-start align-middle"
                                                        style="background-color:#eaf6ea; color:#0a6b2b; font-weight:600;">
                                                        <?= htmlspecialchars($pers) ?>
                                                    </td>
                                                    <?php $firstPers = false; ?>
                                                <?php endif; ?>

                                                <?php if ($firstSas): ?>
                                                    <td rowspan="<?= $sas_rowspan ?>" class="text-start align-middle"
                                                        style="background-color:#eef8ff;">
                                                        <?= htmlspecialchars($sas) ?>
                                                    </td>
                                                    <?php $firstSas = false; ?>
                                                <?php endif; ?>

                                                <td class="text-center align-middle"><?= number_format($it->bobot ?? 0, 2) ?></td>
                                                <td class="text-start"><?= htmlspecialchars($it->indikator ?? '-') ?></td>
                                                <td class="text-center align-middle" style="min-width: 150px;">
                                                    <?php
                                                    $target = $it->target ?? '-';
                                                    echo ($target !== '-' && is_numeric($target) && $target >= 1000)
                                                        ? 'Rp. ' . number_format($target, 0, ',', '.')
                                                        : htmlspecialchars($target);
                                                    ?>
                                                </td>

                                                <td class="text-center align-middle" style="min-width: 110px;">
                                                    <?= htmlspecialchars($it->batas_waktu ?? '-') ?>
                                                </td>

                                                <td class="text-center align-middle" style="min-width: 150px;">
                                                    <?php
                                                    $realisasi = $it->realisasi ?? '-';
                                                    echo ($realisasi !== '-' && is_numeric($realisasi) && $realisasi >= 1000)
                                                        ? 'Rp. ' . number_format($realisasi, 0, ',', '.')
                                                        : htmlspecialchars($realisasi);
                                                    ?>
                                                </td>

                                                <td class="text-center align-middle"><?= number_format($it->calc_pencapaian ?? 0, 2) ?></td>
                                                <td class="text-center align-middle"><?= number_format($it->calc_nilai ?? 0, 2) ?></td>
                                                <td class="text-center align-middle"><?= number_format($it->calc_dibobot ?? 0, 2) ?></td>

                                                <td class="<?= statusColor($it->status ?? '') ?> text-center align-middle">
                                                    <?= htmlspecialchars(($it->status ?? 'Belum Dinilai')) ?>
                                                </td>
                                                <td class="<?= statusColor($it->status2 ?? '') ?> text-center align-middle">
                                                    <?= htmlspecialchars(($it->status2 ?? 'Belum Dinilai')) ?>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endforeach; ?>

                                    <!-- subtotal per perspektif -->
                                    <tr class="fw-bold" style="background-color:#f3f8f3;">
                                        <td colspan="3" class="text-end">Sub Total <?= htmlspecialchars($pers) ?></td>
                                        <td><?= number_format($pers_totals[$pers]['bobot'], 2) ?></td>
                                        <td colspan="6" class="text-end">Sub Total Nilai Dibobot</td>
                                        <td><?= number_format($pers_totals[$pers]['nilai_dibobot'], 2) ?></td>
                                        <td colspan="2" class="text-end"></td>
                                    </tr>
                                <?php endforeach; ?>

                                <!-- total akhir -->
                                <tr class="fw-bold" style="background-color:#1b722a; color:#fff;">
                                    <td colspan="3" class="text-center">Total</td>
                                    <td><?= number_format($global_bobot_sum, 2) ?></td>
                                    <td colspan="6" class="text-end">Total Nilai Dibobot</td>
                                    <td><?= number_format($global_nilai_dibobot, 2) ?></td>
                                    <td colspan="2" class="text-end"></td>
                                </tr>
                            <?php else: ?>
                                <tr>
                                    <td colspan="13" class="text-muted">Belum ada data penilaian.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Form Penilaian Budaya (Read-Only untuk Pegawai) -->
            <div class="row mt-4">
                <div class="col-12">
                    <div class="card shadow-sm border-0">
                        <div class="card-body">
                            <h5 class="text-success fw-bold mb-3">
                                <i class="mdi mdi-account-star-outline me-2"></i> Form Penilaian Budaya
                            </h5>

                            <div class="table-responsive">
                                <table class="table table-bordered" id="tabel-penilaian-budaya">
                                    <thead class="text-center align-middle">
                                        <tr class="bg-success text-white fw-bold">
                                            <th colspan="4" style="vertical-align: middle;">Budaya Kerja</th>
                                        </tr>
                                        <tr class="bg-success-subtle text-dark fw-bold align-middle">
                                            <th style="width:50px;">No</th>
                                            <th style="width:300px;">Perilaku Utama</th>
                                            <th>Panduan Perilaku</th>
                                            <th style="width:180px;">Nilai</th>
                                        </tr>
                                    </thead>

                                    <tbody>
                                        <?php
                                        $no = 1;
                                        // Pastikan $budaya_nilai selalu array
                                        $budaya_nilai = $budaya_nilai ?? [];

                                        if (!empty($budaya)):
                                            foreach ($budaya as $b):
                                                // Pastikan $b adalah array jika diambil dari DB object
                                                $b_data = is_object($b) ? (array) $b : $b;
                                                $panduanList = json_decode($b_data['panduan_perilaku'], true);

                                                if (is_array($panduanList)):
                                                    foreach ($panduanList as $pIndex => $p):
                                                        // Key sesuai format JSON nilai_budaya
                                                        $nilaiKey = "budaya_{$no}_{$pIndex}";
                                                        $nilai = isset($budaya_nilai[$nilaiKey]) ? (int) $budaya_nilai[$nilaiKey] : 0;

                                                        // Mapping label dan warna
                                                        switch ($nilai) {
                                                            case 1:
                                                                $labelNilai = "1 - Sangat Jarang";
                                                                $color = "text-danger";
                                                                break;
                                                            case 2:
                                                                $labelNilai = "2 - Jarang";
                                                                $color = "text-warning";
                                                                break;
                                                            case 3:
                                                                $labelNilai = "3 - Kadang";
                                                                $color = "text-primary";
                                                                break;
                                                            case 4:
                                                                $labelNilai = "4 - Sering";
                                                                $color = "text-success";
                                                                break;
                                                            case 5:
                                                                $labelNilai = "5 - Selalu";
                                                                $color = "fw-bold";
                                                                break;
                                                            default:
                                                                $labelNilai = "<span class='text-muted fst-italic'>Belum Dinilai</span>";
                                                                $color = "";
                                                        }
                                                        ?>
                                                        <tr>
                                                            <?php if ($pIndex === 0): ?>
                                                                <td class="text-center align-middle" rowspan="<?= count($panduanList); ?>">
                                                                    <?= $no; ?>
                                                                </td>
                                                                <td class="align-middle" rowspan="<?= count($panduanList); ?>">
                                                                    <?= htmlspecialchars($b_data['perilaku_utama']); ?>
                                                                </td>
                                                            <?php endif; ?>
                                                            <td><?= chr(97 + $pIndex) . ". " . htmlspecialchars($p); ?></td>
                                                            <td class="text-center align-middle">
                                                                <?php
                                                                if ($nilai >= 1 && $nilai <= 5) {
                                                                    $style = $nilai == 5 ? "color:#1e9c44;" : "";
                                                                    echo "<span class='$color' style='$style'>$labelNilai</span>";
                                                                } else {
                                                                    echo $labelNilai;
                                                                }
                                                                ?>
                                                            </td>
                                                        </tr>
                                                        <?php
                                                    endforeach;
                                                    $no++;
                                                endif;
                                            endforeach;
                                        else:
                                            echo '<tr><td colspan="4" class="text-center text-muted">Data penilaian budaya belum tersedia.</td></tr>';
                                        endif;
                                        ?>
                                    </tbody>

                                    <tfoot class="text-center fw-bold bg-success text-white">
                                        <tr>
                                            <td colspan="3" class="text-end align-middle">
                                                Rata-Rata Nilai Internalisasi Budaya
                                            </td>
                                            <td>
                                                <input type="text" class="form-control form-control-sm text-center"
                                                    value="<?= number_format($rata_rata_budaya ?? 0, 2); ?>" readonly>
                                            </td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Ringkasan Nilai Akhir -->
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-body">
                    <h4 class="card-title mb-3">
                        <i class="mdi mdi-chart-line text-success"></i>
                        Nilai Akhir
                    </h4>

                    <?php
                    // Pastikan variabel dari controller
                    $total_skor_raw = floatval($global_nilai_dibobot ?? 0); // Menggunakan skor dinamis, bukan dari DB
                    $total_skor = $total_skor_raw; // Untuk display
                    
                    $avg_budaya_raw = floatval($rata_rata_budaya ?? 0);
                    $avg_budaya = number_format($avg_budaya_raw, 2); // Display
                    
                    $share_kpi_value = floatval($nilai_akhir['share_kpi_value'] ?? 0);
                    $bobot_sasaran = floatval($nilai_akhir['bobot_sasaran'] ?? 95);
                    $bobot_budaya = floatval($nilai_akhir['bobot_budaya'] ?? 5);
                    $bobot_share_kpi = floatval($nilai_akhir['bobot_share_kpi'] ?? 0);
                    
                    // Hitung unrounded
                    $nilai_sasaran_raw = $total_skor_raw * $bobot_sasaran / 100;
                    $nilai_budaya_raw = $avg_budaya_raw * $bobot_budaya / 100;
                    $nilai_kpi_raw = $share_kpi_value * $bobot_share_kpi / 100;
                    
                    $total_nilai_raw = $nilai_sasaran_raw + $nilai_budaya_raw + $nilai_kpi_raw;
                    
                    // Display
                    $nilai_sasaran = round($nilai_sasaran_raw, 2);
                    $nilai_budaya = round($nilai_budaya_raw, 2);
                    $nilai_kpi = round($nilai_kpi_raw, 2);
                    $total_nilai = round($total_nilai_raw, 2);
                    
                    $fraud = $nilai_akhir['fraud'] ?? 0;
                    $koefisien = $nilai_akhir['koefisien'] ?? 100;
                    
                    $nilai_akhir_value_raw = ($fraud == 1) ? ($total_nilai_raw - 1) : $total_nilai_raw;
                    $nilai_akhir_value = round($nilai_akhir_value_raw, 2);
                    
                    // HITUNG PENCAPAIAN AKHIR DAN PREDIKAT SECARA OTOMATIS BERDASARKAN HASIL BARU
                    $v = round($nilai_akhir_value_raw, 2); // Gunakan nilai yang dibulatkan (sesuai layar)
                    $koef = $koefisien ? floatval($koefisien) / 100 : 1;
                    $pencapaian_akhir_calc = 0;
                    if ($v < 0) $pencapaian_akhir_calc = 0;
                    else if ($v < 2 * $koef) $pencapaian_akhir_calc = ($v / 2) * 0.8 * 100;
                    else if ($v < 3 * $koef) $pencapaian_akhir_calc = 80 + (($v - 2) / 1) * 10;
                    else if ($v < 3.5 * $koef) $pencapaian_akhir_calc = 90 + (($v - 3) / 0.5) * 20;
                    else if ($v < 4.5 * $koef) $pencapaian_akhir_calc = 110 + (($v - 3.5) / 1) * 10;
                    else if ($v < 5 * $koef) $pencapaian_akhir_calc = 120 + (($v - 4.5) / 0.5) * 10;
                    else $pencapaian_akhir_calc = 130;
                    $pencapaian_pct = min($pencapaian_akhir_calc, 130);

                    // Predikat
                    $predikatClass = "";
                    if ($nilai_akhir_value == 0) {
                        $predikat = "Belum Ada Nilai";
                        $predikatClass = "text-dark";
                    } elseif ($nilai_akhir_value < 2 * $koef) {
                        $predikat = "Minus (M)";
                        $predikatClass = "text-danger";
                    } elseif ($nilai_akhir_value < 3 * $koef) {
                        $predikat = "Fair (F)";
                        $predikatClass = "text-warning";
                    } elseif ($nilai_akhir_value < 3.5 * $koef) {
                        $predikat = "Good (G)";
                        $predikatClass = "text-primary";
                    } elseif ($nilai_akhir_value < 4.5 * $koef) {
                        $predikat = "Very Good (VG)";
                        $predikatClass = "text-success";
                    } else {
                        $predikat = "Excellent (E)";
                        $predikatClass = "text-success fw-bold";
                    }
                    ?>

                    <div class="row">
                        <div class="col-lg-8">
                            <table class="table table-borderless">
                                <tr>
                                    <td>Total Nilai Sasaran Kerja</td>
                                    <td style="width:140px; text-align:right;"><?= number_format($total_skor, 2) ?></td>
                                    <td style="width:160px; text-align:center;">x Bobot % Sasaran Kerja</td>
                                    <td style="width:100px; text-align:right;"><?= $bobot_sasaran ?>%</td>
                                    <td style="width:140px; text-align:right;"><?= number_format($nilai_sasaran, 2) ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Rata-rata Nilai Internalisasi Budaya</td>
                                    <td style="text-align:right;"><?= number_format($avg_budaya, 2) ?></td>
                                    <td style="text-align:center;">x Bobot % Budaya Perusahaan</td>
                                    <td style="text-align:right;"><?= $bobot_budaya ?>%</td>
                                    <td style="text-align:right;"><?= number_format($nilai_budaya, 2) ?></td>
                                </tr>
                                <tr>
                                    <td>Share KPI</td>
                                    <td style="text-align:right;"><?= number_format($share_kpi_value, 2) ?></td>
                                    <td style="text-align:center;">x Bobot % Share KPI</td>
                                    <td style="text-align:right;"><?= $bobot_share_kpi ?>%</td>
                                    <td style="text-align:right;"><?= number_format($nilai_kpi, 2) ?></td>
                                </tr>
                                <tr>
                                    <td colspan="4" class="text-right fw-bold">Total Nilai</td>
                                    <td class="fw-bold" style="text-align:right;"><?= number_format($total_nilai, 2) ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="4" class="text-right fw-bold">Status Fraud
                                        <br>
                                        <span class="text-muted small">(1 jika melakukan fraud, 0 jika tidak melakukan
                                            fraud)</span>
                                    </td>
                                    <td class="fw-bold text-danger" style="text-align:right;"><?= $fraud ?></td>
                                </tr>
                                <tr>
                                    <td colspan="4" class="text-right fw-bold">Koefisien Penilaian</td>
                                    <td class="fw-bold" style="text-align:right;"><?= number_format($koefisien, 0) ?>%
                                    </td>
                            </table>
                        </div>

                        <div class="col-lg-4">
                            <div class="mb-3">
                                <div class="border rounded p-3 bg-light text-center">
                                    <h6 class="fw-bold">Nilai Akhir</h6>
                                    <div class="display-6 text-success fw-bolder"><?= number_format($nilai_akhir_value, 2) ?>
                                    </div>
                                </div>
                            </div>
                            <div class="mb-3">
                                <div class="border rounded p-3 bg-light text-center">
                                    <h6 class="fw-bold">Pencapaian Akhir</h6>
                                    <div class="display-6 text-success fw-bolder">
                                        <?= number_format($pencapaian_pct, 2) ?>%</div>
                                </div>
                            </div>
                            <div class="bg-success text-white rounded p-3 text-center">
                                <h5>Yudisium / Predikat</h5>
                                <div class="mt-3 fw-bolder text-white" style="font-size:1.4rem;"><?= $predikat ?></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tombol Verifikasi -->
            <div class="text-end mt-4">
                <?php
                $verifLabel = ($status_penilaian === 'disetujui') ? 'Ubah Verifikasi' : 'Verifikasi Penilaian';
                ?>
                <button id="btn-verifikasi"
                    class="btn btn-lg <?= ($status_penilaian === 'disetujui') ? 'btn-warning' : 'btn-success' ?> shadow px-4 py-2 rounded-pill">
                    <i class="mdi mdi-check-circle-outline"></i> <?= $verifLabel ?>
                </button>
                <a href="<?= base_url('Administrator/verifikasi_penilaian') ?>"
                    class="btn btn-lg btn-secondary shadow px-4 py-2 rounded-pill ms-2">
                    <i class="mdi mdi-arrow-left"></i> Kembali
                </a>
            </div>

        </div>
    </div>
</div>

<!-- ============================== -->
<!-- 💬 JAVASCRIPT VERIFIKASI PENILAIAN -->
<!-- ============================== -->
<script src="<?= base_url('assets/libs/sweetalert2/sweetalert2@11.js') ?>"></script>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        // Custom Matcher Select2 untuk pencarian fleksibel dan prioritas tahun berjalan
        function matchCustomPeriode(params, data) {
            if ($.trim(params.term) === '') {
                var currentYear = "<?= date('Y') ?>";
                if (data.text.indexOf(currentYear) > -1 || data.id === '' || data.id === 'baru') {
                    return data;
                }
                if (data.element && data.element.selected) {
                    return data;
                }
                return null;
            }

            if (typeof data.text === 'undefined') {
                return null;
            }

            var term = params.term.toLowerCase();
            var text = data.text.toLowerCase();
            
            var termWords = term.split(' ');
            var matchesAll = true;
            for (var i = 0; i < termWords.length; i++) {
                if (text.indexOf(termWords[i]) === -1) {
                    matchesAll = false;
                    break;
                }
            }

            if (matchesAll) {
                return data;
            }

            return null;
        }

        if (typeof $ !== 'undefined') {
            $('#select_periode').select2({
                matcher: matchCustomPeriode,
                placeholder: "-- Pilih Periode --",
                width: '100%'
            });
        }

        const btnVerifikasi = document.getElementById("btn-verifikasi");
        if (!btnVerifikasi) return;

        const pegawaiNama = "<?= addslashes($pegawai_detail->nama_pegawai ?? '-') ?>";
        const pegawaiNik = "<?= addslashes($pegawai_detail->nik ?? '-') ?>";
        const statusBadge = document.querySelector(".badge");

        // Sync periode select into hidden awal/akhir inputs so the form submits separate params
        const selectPeriode = document.getElementById('select_periode');
        const inputAwal = document.getElementById('form_awal');
        const inputAkhir = document.getElementById('form_akhir');
        const formPeriode = document.getElementById('form-periode');

        function syncPeriodeInputs() {
            if (!selectPeriode || !inputAwal || !inputAkhir) return;
            const parts = ($(selectPeriode).val() || selectPeriode.value || '').split('|');
            if (parts.length === 2) {
                inputAwal.value = parts[0];
                inputAkhir.value = parts[1];
            } else {
                inputAwal.value = '';
                inputAkhir.value = '';
            }
        }

        // initialize and bind
        syncPeriodeInputs();
        if (typeof $ !== 'undefined') {
            $('#select_periode').on('change', syncPeriodeInputs);
        } else if (selectPeriode) {
            selectPeriode.addEventListener('change', syncPeriodeInputs);
        }
        if (formPeriode) formPeriode.addEventListener('submit', syncPeriodeInputs);

        btnVerifikasi.addEventListener("click", function () {
            Swal.fire({
                title: 'Setujui Penilaian Ini?',
                html: `
                <div class="text-start">
                    <p class="mb-1"><strong>Nama:</strong> ${pegawaiNama}</p>
                    <p><strong>NIP:</strong> ${pegawaiNik}</p>
                    <p class="text-muted small mb-0">
                        Apakah Anda yakin ingin menyetujui hasil penilaian ini?
                    </p>
                </div>
            `,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#198754',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, Setujui',
                cancelButtonText: 'Tidak',
                reverseButtons: true,
            }).then((result) => {
                if (result.isConfirmed) {
                    kirimVerifikasi('disetujui');
                } else if (result.dismiss === Swal.DismissReason.cancel) {
                    Swal.fire({
                        title: 'Tolak Penilaian?',
                        text: 'Apakah Anda ingin menandai penilaian ini sebagai ditolak?',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'Ya, Tolak',
                        cancelButtonText: 'Batal',
                        confirmButtonColor: '#dc3545',
                    }).then((tolak) => {
                        if (tolak.isConfirmed) {
                            kirimVerifikasi('ditolak');
                        }
                    });
                }
            });
        });

        function kirimVerifikasi(status) {
            Swal.fire({
                title: 'Memproses...',
                text: 'Mohon tunggu, sedang memperbarui status penilaian.',
                allowOutsideClick: false,
                didOpen: () => Swal.showLoading()
            });

            // sertakan periode yang sedang dipilih (jika ada)
            const sel = document.getElementById('select_periode');
            let awal = '<?= $selected_awal ?? date('Y-01-01') ?>';
            let akhir = '<?= $selected_akhir ?? date('Y-12-31') ?>';
            if (sel) {
                const parts = (sel.value || '').split('|');
                if (parts.length === 2) {
                    awal = parts[0];
                    akhir = parts[1];
                }
            }

            fetch("<?= base_url('administrator/verifikasiPenilaian') ?>", {
                method: "POST",
                headers: {
                    "Content-Type": "application/x-www-form-urlencoded"
                },
                body: `nik=${pegawaiNik}&status=${status}&awal=${encodeURIComponent(awal)}&akhir=${encodeURIComponent(akhir)}`
            })
                .then(res => res.json())
                .then(data => {
                    if (data.status === 'success') {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: data.message,
                            timer: 2000,
                            showConfirmButton: false
                        });
                        setTimeout(() => location.reload(), 2000);
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal',
                            text: data.message
                        });
                    }
                })
                .catch(() => {
                    Swal.fire({
                        icon: 'error',
                        title: 'Kesalahan',
                        text: 'Tidak dapat terhubung ke server.'
                    });
                });
        }
    });
</script>

<style>
    .card {
        animation: fadeIn 0.4s ease-in-out;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(8px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .table th {
        vertical-align: middle !important;
    }

    .badge {
        font-size: 0.95rem;
        transition: all 0.3s ease;
    }

    #btn-verifikasi:hover {
        transform: scale(1.05);
        transition: 0.2s;
    }
</style>