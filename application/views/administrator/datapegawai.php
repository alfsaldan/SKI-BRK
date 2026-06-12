<div class="content-page">
    <div class="content">
        <div class="container-fluid">

            <!-- Judul -->
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box">
                        <div class="page-title-right">
                            <ol class="breadcrumb m-0">
                                <li class="breadcrumb-item"><a href="javascript:void(0);">KPI Online-BRKS</a></li>
                                <li class="breadcrumb-item active">Data Kinerja Pegawai</li>
                            </ol>
                        </div>
                        <h4 class="page-title"><i class="mdi mdi-account-card-details mr-2 text-primary"></i>Data
                            Kinerja Pegawai</h4>
                    </div>
                </div>
            </div>

            <!-- Form cari NIK -->
            <div class="row">
                <div class="col-12 col-md-6">
                    <div class="card">
                        <div class="card-body">
                            <h5>Masukkan NIP Pegawai</h5>
                            <form action="<?= base_url('Administrator/cariDataPegawai'); ?>" method="post">
                                <input type="text" name="nik" class="form-control" placeholder="Masukkan NIP Pegawai"
                                    required maxlength="6" oninput="this.value = this.value.replace(/[^0-9]/g, '');">
                                <button type="submit" class="btn btn-success mt-2">Cari</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <?php if (isset($pegawai_detail) && $pegawai_detail): ?>
                <!-- Detail Pegawai -->
                <div class="row">
                    <div class="col-12">
                        <div class="card mt-0 shadow-sm border-0">
                            <div class="card-body">
                                <div class="row">
                                    <!-- Detail Pegawai -->
                                    <div class="col-md-6 mb-3">
                                        <h5 class="text-primary mb-3 font-weight-bold"><i
                                                class="mdi mdi-account-circle-outline mr-2"></i>Detail Pegawai</h5>
                                        <ul class="list-group list-group-flush">
                                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                                <span class="text-dark font-weight-medium">NIP</span>
                                                <span
                                                    class="badge badge-primary badge-pill"><?= $pegawai_detail->nik; ?></span>
                                            </li>
                                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                                <span class="text-dark font-weight-medium">Nama</span>
                                                <span class="text-dark"><?= $pegawai_detail->nama; ?></span>
                                            </li>
                                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                                <span class="text-dark font-weight-medium">Jabatan</span>
                                                <span class="text-dark"><?= $pegawai_detail->jabatan; ?></span>
                                            </li>
                                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                                <span class="text-dark font-weight-medium">Jenis Unit</span>
                                                <span class="text-dark"><?= $pegawai_detail->unit_kantor ?? '-'; ?></span>
                                            </li>
                                        </ul>
                                        <input type="hidden" id="nik" value="<?= $pegawai_detail->nik ?>">
                                    </div>

                                    <!-- Informasi Penilaian -->
                                    <div class="col-md-6 mb-3">
                                        <h5 class="text-success mb-3 font-weight-bold">
                                            <i class="mdi mdi-file-document-outline mr-2"></i>Informasi Penilaian
                                        </h5>
                                        <ul class="list-group list-group-flush">
                                            <!-- Periode Penilaian -->
                                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                                <span class="text-dark font-weight-medium">Periode Penilaian</span>
                                                <div class="d-flex">
                                                    <select id="periode_select" class="form-control text-dark mr-2">
                                                        <?php if (!empty($periode_list)): ?>
                                                            <?php foreach ($periode_list as $p): ?>
                                                                <?php
                                                                $label = date('d M Y', strtotime($p->periode_awal)) . " s/d " . date('d M Y', strtotime($p->periode_akhir));
                                                                $selected = ($periode_awal == $p->periode_awal && $periode_akhir == $p->periode_akhir) ? 'selected' : '';
                                                                ?>
                                                                <option value="<?= $p->periode_awal ?>|<?= $p->periode_akhir ?>"
                                                                    <?= $selected ?>>
                                                                    <?= $label ?>
                                                                </option>
                                                            <?php endforeach; ?>
                                                        <?php else: ?>
                                                            <option disabled>Belum ada periode penilaian</option>
                                                        <?php endif; ?>
                                                    </select>

                                                    <!-- Hidden inputs agar JS/komponen lain tahu periode yg dipilih server -->
                                                    <input type="hidden" id="hidden_periode_awal"
                                                        value="<?= htmlspecialchars($periode_awal ?? '', ENT_QUOTES) ?>">
                                                    <input type="hidden" id="hidden_periode_akhir"
                                                        value="<?= htmlspecialchars($periode_akhir ?? '', ENT_QUOTES) ?>">

                                                    <button type="button" id="btn-sesuaikan-periode"
                                                        class="btn btn-success">Terapkan</button>
                                                </div>
                                            </li>

                                            <!-- Unit Kantor Penilai -->
                                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                                <span class="text-dark font-weight-medium">Unit Kantor Penilai</span>
                                                <span class="text-dark"><?= $pegawai_detail->unit_kantor ?? '-'; ?></span>
                                            </li>
                                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                            </li>
                                        </ul>
                                    </div>
                                </div>

                                <hr class="my-3">

                                <!-- Penilai I & Penilai II -->
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <h5 class="text-info mb-3 font-weight-bold">
                                            <i class="mdi mdi-account-check-outline mr-2"></i>Penilai I
                                        </h5>
                                        <ul class="list-group list-group-flush">
                                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                                <span class="text-dark font-weight-medium">NIP</span>
                                                <span
                                                    class="badge badge-info badge-pill"><?= $pegawai_detail->penilai1_nik ?? '-'; ?></span>
                                            </li>
                                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                                <span class="text-dark font-weight-medium">Nama</span>
                                                <span class="text-dark"><?= $pegawai_detail->penilai1_nama ?? '-'; ?></span>
                                            </li>
                                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                                <span class="text-dark font-weight-medium">Jabatan</span>
                                                <span
                                                    class="text-dark"><?= $pegawai_detail->penilai1_jabatan ?? '-'; ?></span>
                                            </li>
                                        </ul>
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <h5 class="text-warning mb-3 font-weight-bold">
                                            <i class="mdi mdi-account-check-outline mr-2"></i>Penilai II
                                        </h5>
                                        <ul class="list-group list-group-flush">
                                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                                <span class="text-dark font-weight-medium">NIP</span>
                                                <span
                                                    class="badge badge-warning badge-pill"><?= $pegawai_detail->penilai2_nik ?? '-'; ?></span>
                                            </li>
                                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                                <span class="text-dark font-weight-medium">Nama</span>
                                                <span class="text-dark"><?= $pegawai_detail->penilai2_nama ?? '-'; ?></span>
                                            </li>
                                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                                <span class="text-dark font-weight-medium">Jabatan</span>
                                                <span
                                                    class="text-dark"><?= $pegawai_detail->penilai2_jabatan ?? '-'; ?></span>
                                            </li>
                                        </ul>
                                    </div>
                                </div>


                                <a href="<?= base_url('Administrator/downloadDataPegawai?nik=' . ($pegawai_detail->nik ?? '') . '&awal=' . $periode_awal . '&akhir=' . $periode_akhir) ?>"
                                    class="btn btn-success mt-3 font-weight-bold"
                                    style="background-color:#217346; color:#fff;">
                                    <i class="mdi mdi-file-excel"></i> Download Excel
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            <!-- Tabel Penilaian -->
            <div class="row">
                <div class="col-12">
                    <div class="card mt-0">
                        <div class="card-body">
                            <h5 class="text-success font-weight-bold mb-3">
                                <i class="mdi mdi-star-circle mr-2"></i> Hasil Penilaian
                            </h5>
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead style="background-color:#2E7D32;color:#fff;text-align:center;">
                                        <tr>
                                            <th>Perspektif</th>
                                            <th>Sasaran Kerja</th>
                                            <th>Indikator</th>
                                            <th>Bobot (%)</th>
                                            <th>Target</th>
                                            <th>Batas Waktu</th>
                                            <th>Realisasi</th>
                                            <th>Pencapaian (%)</th>
                                            <th>Nilai</th>
                                            <th>Nilai Dibobot</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (!empty($penilaian_pegawai)): ?>
                                            <?php
                                            $order = ['Keuangan (F)', 'Pelanggan (C)', 'Proses Internal (IP)', 'Pembelajaran & Pertumbuhan (LG)'];
                                            $grouped = [];
                                            $total_bobot_all = 0;
                                            $total_skor_calculated = 0; // Tambahkan variabel ini untuk mengakumulasi skor yang dihitung ulang
                                            foreach ($penilaian_pegawai as $row) {
                                                $p = $row->perspektif ?? '';
                                                $s = $row->sasaran_kerja ?? '';
                                                $grouped[$p][$s][] = $row;
                                                $total_bobot_all += floatval($row->bobot ?? 0);
                                            }

                                            function count_rows($arr)
                                            {
                                                $sum = 0;
                                                foreach ($arr as $items) {
                                                    $sum += count($items);
                                                }
                                                return $sum;
                                            }

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
                                            ?>

                                            <?php foreach ($order as $persp): ?>
                                                <?php if (empty($grouped[$persp]))
                                                    continue; ?>
                                                <?php
                                                $persp_rows = count_rows($grouped[$persp]);
                                                $first_persp_cell = true;
                                                $subtotal_bobot = 0;
                                                $subtotal_nilai = 0;
                                                ?>
                                                <?php foreach ($grouped[$persp] as $sasaran => $items): ?>
                                                    <?php
                                                    $sasaran_rows = count($items);
                                                    $first_sas_cell = true;
                                                    ?>
                                                    <?php foreach ($items as $i): ?>
                                                        <?php
                                                        $subtotal_bobot += (float) $i->bobot;
                                                        $b = (float) $i->bobot;
                                                        $t = (float) $i->target;
                                                        $r = (float) $i->realisasi;
                                                        
                                                        // Gunakan indikator, jika null, coba fallback ke hal lain yang relevan jika diperlukan (tapi indikator selalu ada harusnya)
                                                        $calc_pencapaian = hitungPencapaianOtomatisPHP($t, $r, $i->indikator ?? '');
                                                        $calc_nilai = hitungNilaiPHP($calc_pencapaian);
                                                        
                                                        $calc_dibobot = ($total_bobot_all > 0) ? ($calc_nilai * $b / $total_bobot_all) : 0;
                                                        $subtotal_nilai += $calc_dibobot;
                                                        $total_skor_calculated += $calc_dibobot;
                                                        ?>
                                                        <tr>
                                                            <?php if ($first_persp_cell): ?>
                                                                <td rowspan="<?= $persp_rows; ?>"
                                                                    style="vertical-align:middle;font-weight:600;background:#C8E6C9;">
                                                                    <?= $persp; ?>
                                                                </td>
                                                                <?php $first_persp_cell = false; ?>
                                                            <?php endif; ?>

                                                            <?php if ($first_sas_cell): ?>
                                                                <td rowspan="<?= $sasaran_rows; ?>"
                                                                    style="vertical-align:middle;background:#E3F2FD;">
                                                                    <?= $sasaran; ?>
                                                                </td>
                                                                <?php $first_sas_cell = false; ?>
                                                            <?php endif; ?>

                                                            <td><?= $i->indikator; ?></td>
                                                            <td class="text-center align-middle"><?= $i->bobot; ?></td>
                                                            <td class="text-center align-middle" style="min-width:150px;">
                                                                <?= ($i->target >= 1000) ? 'Rp. ' . number_format($i->target, 0, ',', '.') : $i->target; ?>
                                                            </td>

                                                            <td class="text-center align-middle" style="min-width:110px;">
                                                                <?= $i->batas_waktu; ?>
                                                            </td>

                                                            <td class="text-center align-middle" style="min-width:150px;">
                                                                <?= ($i->realisasi >= 1000) ? 'Rp. ' . number_format($i->realisasi, 0, ',', '.') : $i->realisasi; ?>
                                                            </td>

                                                            <td class="text-center align-middle"><?= number_format($calc_pencapaian, 2); ?></td>
                                                            <td class="text-center align-middle"><?= number_format($calc_nilai, 2); ?></td>
                                                            <td class="text-center align-middle"><?= number_format($calc_dibobot, 2); ?></td>
                                                        </tr>
                                                    <?php endforeach; ?>
                                                <?php endforeach; ?>

                                                <!-- Subtotal baris -->
                                                <tr style="font-weight:bold;background:#F1F8E9;">
                                                    <td colspan="3">Sub Total <?= $persp; ?></td>
                                                    <td class="text-center"><?= $subtotal_bobot; ?></td>
                                                    <td colspan="5" class="text-center">Sub Total Nilai Dibobot</td>
                                                    <td class="text-center"><?= number_format(round($subtotal_nilai, 2), 2); ?></td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <tr>
                                                <td colspan="10" class="text-center">Belum ada data penilaian untuk pegawai ini
                                                </td>
                                            </tr>
                                        <?php endif; ?>
                                    </tbody>
                                    <?php if (!empty($penilaian_pegawai)): ?>
                                        <tfoot style="background-color:#2E7D32;color:#fff;font-weight:bold;text-align:center;">
                                            <tr>
                                                <td colspan="3">Total</td>
                                                <td><?= array_sum(array_column($penilaian_pegawai, 'bobot')); ?></td>
                                                <td colspan="5">Total Nilai Dibobot</td>
                                                <td><?= number_format(round($total_skor_calculated, 2), 2); ?></td>
                                            </tr>
                                        </tfoot>
                                    <?php endif; ?>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Form Penilaian Budaya (Read-Only untuk Pegawai) -->
            <div class="row mt-0">
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

            <!-- Nilai Akhir & Predikat -->
            <div class="card mt-0">
                <div class="card-body">
                    <h5 class="text-success fw-bold mb-3">
                        <i class="mdi mdi-star-circle mr-2"></i> Nilai Akhir
                    </h5>

                    <?php
                    // 🔹 Pastikan data aman
                    $total_skor_raw = floatval($total_skor_calculated ?? 0);
                    $total_skor = number_format(round($total_skor_raw, 2), 2); // Hanya untuk display
                    $avg_budaya_raw = floatval($rata_rata_budaya ?? 0);
                    $avg_budaya = number_format($avg_budaya_raw, 2); // Hanya untuk display
                    
                    $share_kpi_value = $nilai_akhir->share_kpi_value ?? 0;
                    $bobot_sasaran = $nilai_akhir->bobot_sasaran ?? 95;
                    $bobot_budaya = $nilai_akhir->bobot_budaya ?? 5;
                    $bobot_share_kpi = $nilai_akhir->bobot_share_kpi ?? 0;
                    
                    // Hitung unrounded untuk kalkulasi akurat
                    $nilai_sasaran_raw = $total_skor_raw * $bobot_sasaran / 100;
                    $nilai_budaya_raw = $avg_budaya_raw * $bobot_budaya / 100;
                    $nilai_kpi_raw = (float)$share_kpi_value * $bobot_share_kpi / 100;
                    
                    $total_nilai_raw = $nilai_sasaran_raw + $nilai_budaya_raw + $nilai_kpi_raw;
                    
                    // Nilai untuk display
                    $nilai_sasaran = round($nilai_sasaran_raw, 2);
                    $nilai_budaya = round($nilai_budaya_raw, 2);
                    $nilai_kpi = round($nilai_kpi_raw, 2);
                    $total_nilai = round($total_nilai_raw, 2);
                    
                    $fraud = $nilai_akhir->fraud ?? 0;
                    $koefisien = $nilai_akhir->koefisien ?? 100;
                    
                    $nilai_akhir_value_raw = ($fraud == 1) ? ($total_nilai_raw - 1) : $total_nilai_raw;
                    $nilai_akhir_value = round($nilai_akhir_value_raw, 2); // Display
                    
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
                    ?>

                    <!-- Bagian Atas: Perhitungan -->
                    <table class="table table-bordered mb-4">
                        <tr>
                            <th>Total Nilai Sasaran Kerja</th>
                            <td class="text-center"><?= number_format($total_skor, 2) ?></td>
                            <td>x Bobot % Sasaran Kerja</td>
                            <td class="text-center"><?= $bobot_sasaran ?>%</td>
                            <td class="text-center"><?= number_format($nilai_sasaran, 2) ?></td>
                        </tr>
                        <tr>
                            <th>Rata-rata Nilai Internalisasi Budaya</th>
                            <td class="text-center"><?= number_format($avg_budaya, 2) ?></td>
                            <td>x Bobot % Budaya Perusahaan</td>
                            <td class="text-center"><?= $bobot_budaya ?>%</td>
                            <td class="text-center"><?= number_format($nilai_budaya, 2) ?></td>
                        </tr>
                        <tr>
                            <th>Share KPI</th>
                            <td class="text-center"><?= number_format($share_kpi_value, 2) ?></td>
                            <td>x Bobot % Share KPI</td>
                            <td class="text-center"><?= $bobot_share_kpi ?>%</td>
                            <td class="text-center"><?= number_format($nilai_kpi, 2) ?></td>
                        </tr>
                        <tr>
                            <th colspan="4" class="text-right">Total Nilai</th>
                            <td class="text-center"><?= number_format($total_nilai, 2) ?></td>
                        </tr>
                        <tr>
                            <th colspan="4" class="text-right">
                                Fraud<br>
                                <small>(1 jika melakukan fraud, 0 jika tidak melakukan fraud)</small>
                            </th>
                            <td class="text-center"><?= $fraud ?></td>
                        </tr>
                        <tr>
                            <th colspan="4" class="text-right">Koefisien Penilaian</th>
                            <td class="text-center"><?= number_format($koefisien, 0) ?>%</td>
                    </table>

                    <?php
                    // Tentukan predikat & warna berdasarkan nilai akhir
                    $predikat = "";
                    $predikatClass = "";
                    $koef = $koefisien ? $koefisien / 100 : 1; // default 1 jika koefisien tidak ada
                
                    if ($nilai_akhir_value === "Tidak ada nilai") {
                        $predikat = "Tidak ada yudisium/predikat";
                        $predikatClass = "text-dark";
                    } elseif ($nilai_akhir_value == 0) {
                        $predikat = "Belum Ada Nilai";
                        $predikatClass = "text-dark";
                    } elseif ($nilai_akhir_value < 2 * $koef) {
                        $predikat = "Minus (M)";
                        $predikatClass = "text-danger"; // merah
                    } elseif ($nilai_akhir_value < 3 * $koef) {
                        $predikat = "Fair (F)";
                        $predikatClass = "text-warning"; // jingga
                    } elseif ($nilai_akhir_value < 3.5 * $koef) {
                        $predikat = "Good (G)";
                        $predikatClass = "text-primary"; // biru
                    } elseif ($nilai_akhir_value < 4.5 * $koef) {
                        $predikat = "Very Good (VG)";
                        $predikatClass = "text-success"; // hijau muda
                    } else {
                        $predikat = "Excellent (E)";
                        $predikatClass = "text-success fw-bold"; // hijau tua tebal
                    }
                    ?>

                    <!-- Bagian Bawah: Kiri-Kanan -->
                    <div class="row">
                        <div class="col-md-6">
                            <!-- Tabel Yudisium / Predikat -->
                            <table class="table table-bordered text-center">
                                <thead class="bg-success text-white">
                                    <tr>
                                        <th>Nilai Akhir</th>
                                        <th>Yudisium / Predikat</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>&ge; 4.50 - 5</td>
                                        <td><b>Excellent (E)</b></td>
                                    </tr>
                                    <tr>
                                        <td>3.50 - &lt; 4.50</td>
                                        <td><b>Very Good (VG)</b></td>
                                    </tr>
                                    <tr>
                                        <td>3.00 - &lt; 3.50</td>
                                        <td><b>Good (G)</b></td>
                                    </tr>
                                    <tr>
                                        <td>2.00 - &lt; 3.00</td>
                                        <td><b>Fair (F)</b></td>
                                    </tr>
                                    <tr>
                                        <td>&lt; 2.00</td>
                                        <td><b>Minus (M)</b></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <div class="col-md-6">
                            <!-- Kanan: Nilai Akhir & Predikat -->
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="card text-center mb-3">
                                        <div class="card-header bg-success text-white">Nilai Akhir</div>
                                        <div class="card-body">
                                            <h3 id="nilai-akhir"><?= number_format($nilai_akhir_value, 2) ?></h3>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="card text-center mb-3">
                                        <div class="card-header bg-success text-white">Pencapaian Akhir</div>
                                        <div class="card-body">
                                            <h3 id="pencapaian-akhir"><?= number_format($pencapaian_pct ?? 0, 2) ?>%</h3>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card text-center mb-3">
                                <div class="card-header bg-success text-white">Yudisium / Predikat</div>
                                <div class="card-body">
                                    <h3 id="predikat" class="<?= $predikatClass ?>"><?= $predikat ?></h3>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>


            <!-- Aktivitas Coaching / Chat -->
            <div class="card mt-0 border-0 shadow-lg rounded-4 overflow-hidden">
                <div class="card-header text-white fw-bold"
                    style="background: linear-gradient(135deg, #05b01cff, #027400ff); font-size:1.1rem;">
                    <i class="bi bi-chat-text-fill me-2"></i> Aktivitas Coaching
                </div>
                <div class="card-body p-4" id="chat-box"
                    style="max-height:450px; overflow-y:auto; background: linear-gradient(180deg,#f8f9fa,#e9ecef);">

                    <?php if (!empty($chat)): ?>
                        <?php foreach ($chat as $c): ?>
                            <?php
                            $isPegawai = ($c->pengirim_nik == $pegawai_detail->nik);

                            // Konversi timezone ke Asia/Jakarta
                            $dt = new DateTime($c->created_at, new DateTimeZone("UTC"));
                            $dt->setTimezone(new DateTimeZone("Asia/Jakarta"));
                            $tanggal = $dt->format("d M Y • H:i");
                            ?>

                            <div class="d-flex mb-4 <?= $isPegawai ? 'justify-content-end' : 'justify-content-start'; ?>">
                                <div class="chat-bubble p-3 rounded-4 shadow-sm" style="
                            max-width:65%;
                            background: <?= $isPegawai ? 'rgba(13,110,253,0.1)' : 'rgba(255,255,255,0.6)'; ?>;
                            backdrop-filter: blur(10px);
                            border:1px solid rgba(255,255,255,0.4);
                         ">
                                    <div class="d-flex align-items-center mb-2">
                                        <span class="fw-semibold me-2" style="font-size:0.9rem;">
                                            <?= $c->nama_pengirim ?? $c->pengirim_nik; ?>
                                        </span>
                                        <span class="badge rounded-pill <?= $isPegawai ? 'bg-success' : 'bg-primary'; ?> ms-auto"
                                            style="font-size:0.65rem;">
                                            <?= $isPegawai ? 'Pegawai' : 'Penilai'; ?>
                                        </span>
                                    </div>
                                    <div style="font-size:0.95rem; line-height:1.5;">
                                        <?= nl2br(htmlspecialchars($c->pesan)); ?>
                                    </div>
                                    <div class="text-end text-muted mt-2" style="font-size:0.75rem;">
                                        <?= $tanggal; ?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="text-center text-muted fst-italic p-5">
                            <i class="bi bi-inbox fs-3 d-block mb-2"></i>
                            Belum ada percakapan coaching.
                        </div>
                    <?php endif; ?>
                </div>
            </div>

        <?php endif; ?>
    </div>
</div>
</div>

<!-- SweetAlert -->
<script src="<?= base_url('assets/libs/sweetalert2/sweetalert2@11.js') ?>"></script>

<?php if ($this->session->flashdata('success')): ?>
    <script>
        Swal.fire({
            icon: 'success',
            title: 'Berhasil',
            text: '<?= $this->session->flashdata('success'); ?>',
            confirmButtonColor: '#039046'
        });
    </script>
<?php endif; ?>

<?php if ($this->session->flashdata('error')): ?>
    <script>
        Swal.fire({
            icon: 'error',
            title: 'Gagal',
            text: '<?= $this->session->flashdata('error'); ?>',
            confirmButtonColor: '#d33'
        });
    </script>
<?php endif; ?>

<script>
    // Ganti handler lama dengan versi yang menampilkan SweetAlert sukses sebelum redirect
    document.getElementById('btn-sesuaikan-periode').addEventListener('click', function () {
        const select = document.getElementById('periode_select');
        const val = select ? select.value : '';
        if (!val) {
            Swal.fire({
                icon: 'warning',
                title: 'Pilih Periode',
                text: 'Silakan pilih periode terlebih dahulu.',
                confirmButtonColor: '#d33'
            });
            return;
        }

        // Jika pengguna memilih opsi "baru" (tambah periode), biarkan logika lama (buka manual)
        if (val === 'baru') {
            // tampilkan manual UI (jika ada) atau lakukan nothing
            // ...existing code may handle this case...
            return;
        }

        const [awal, akhir] = val.split('|');
        const nik = "<?= $pegawai_detail->nik ?? '' ?>";

        Swal.fire({
            icon: 'success',
            title: 'Berhasil',
            text: 'Periode penilaian telah diperbarui',
            timer: 2500,
            showConfirmButton: false,
            willClose: () => {
                // Redirect setelah alert close
                window.location.href = "<?= base_url('Administrator/cariDataPegawai') ?>?nik=" + encodeURIComponent(nik) + "&awal=" + encodeURIComponent(awal) + "&akhir=" + encodeURIComponent(akhir);
            }
        });
    });
    // Auto scroll ke bawah saat halaman dibuka
    document.addEventListener("DOMContentLoaded", function () {
        var chatBox = document.getElementById("chat-box");
        if (chatBox) {
            chatBox.scrollTop = chatBox.scrollHeight;
        }
    });
</script>