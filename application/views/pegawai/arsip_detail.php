<!-- ============================================================== -->
<!-- Start Page Content here -->
<!-- ============================================================== -->
<?php
/**
 * @var object $pegawai_detail
 * @var array $penilaian
 * @var array $budaya
 * @var array $budaya_nilai
 * @var array $nilai_akhir
 * @var float $rata_rata_budaya
 * @var string $selected_awal
 * @var string $selected_akhir
 * @var string $status_penilaian
 */
?>
<div class="content-page">
    <div class="content">
        <div class="container-fluid">

            <!-- start page title -->
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-flex justify-content-between align-items-center">
                        <h3 class="page-title">
                            <i class="mdi mdi-file-chart mr-2 text-primary"></i> Detail Arsip Penilaian
                        </h3>
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="<?= base_url('Pegawai/rekap_nilai') ?>">Rekap Nilai</a></li>
                            <li class="breadcrumb-item active">
                                Detail Arsip
                            </li>
                        </ol>
                    </div>
                </div>
            </div>
            <!-- end page title -->

            <!-- Judul Halaman -->
            <div class="row mb-3">
                <div class="col-12 d-flex justify-content-between align-items-center">
                    <span class="badge bg-success px-3 py-2 shadow-sm fs-6">
                        <i class="mdi mdi-check-decagram"></i> Penilaian Selesai & Telah Diarsipkan
                    </span>
                    <a href="<?= base_url('Pegawai/rekapnilaipegawai') ?>" class="btn btn-secondary">
                        <i class="mdi mdi-arrow-left"></i> Kembali ke Rekap
                    </a>
                </div>
            </div>

            <!-- Data Pegawai -->
            <div class="card shadow-lg rounded-4 mb-4">
                <div class="card-header bg-primary text-white rounded-top-4 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 text-white">
                        <i class="mdi mdi-account-circle-outline"></i> Data Pegawai
                    </h5>
                    <div class="text-end">
                        <strong class="fw-normal">
                            <i class="mdi mdi-calendar-range"></i>
                            Periode: <?= date('d M Y', strtotime($selected_awal)) ?> s/d <?= date('d M Y', strtotime($selected_akhir)) ?>
                        </strong>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row gy-3">
                        <div class="col-md-6">
                            <label class="fw-bold text-secondary">Nama Pegawai</label>
                            <input type="text" class="form-control" readonly value="<?= htmlspecialchars($pegawai_detail->nama_pegawai ?? '-', ENT_QUOTES, 'UTF-8') ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="fw-bold text-secondary">NIK</label>
                            <input type="text" class="form-control" readonly value="<?= htmlspecialchars($pegawai_detail->nik ?? '-', ENT_QUOTES, 'UTF-8') ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="fw-bold text-secondary">Jabatan</label>
                            <input type="text" class="form-control" readonly value="<?= htmlspecialchars($pegawai_detail->jabatan ?? '-', ENT_QUOTES, 'UTF-8') ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="fw-bold text-secondary">Unit Kerja</label>
                            <input type="text" class="form-control" readonly value="<?= htmlspecialchars($pegawai_detail->unit_kerja ?? '-', ENT_QUOTES, 'UTF-8') ?>">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Daftar Penilaian -->
            <div class="card shadow-lg rounded-4 mb-4">
                <div class="card-header bg-success text-white rounded-top-4">
                    <h5 class="mb-0 text-white">
                        <i class="mdi mdi-format-list-bulleted"></i>
                        Daftar Indikator Penilaian
                    </h5>
                </div>
                <div class="card-body table-responsive">

                    <?php
                    // Group data by Perspektif & Sasaran
                    $grouped = [];
                    if (!empty($penilaian)) {
                        foreach ($penilaian as $row) {
                            $pers = $row->perspektif ?? 'Lainnya';
                            $sas = $row->sasaran_kerja ?? '-';
                            if (!isset($grouped[$pers])) $grouped[$pers] = [];
                            if (!isset($grouped[$pers][$sas])) $grouped[$pers][$sas] = [];
                            $grouped[$pers][$sas][] = $row;
                        }
                    }

                    // Hitung subtotal & total
                    $pers_totals = [];
                    $global_bobot_sum = 0;
                    $global_nilai_dibobot = 0;

                    foreach ($grouped as $pers => $sasList) {
                        $p_bobot = 0;
                        $p_nilai_dibobot = 0;
                        foreach ($sasList as $sas => $items) {
                            foreach ($items as $it) {
                                $p_bobot += floatval($it->bobot ?? 0);
                                $p_nilai_dibobot += floatval($it->nilai_dibobot ?? 0);
                            }
                        }
                        $p_bobot = round($p_bobot, 2);
                        $p_nilai_dibobot = round($p_nilai_dibobot, 2);
                        $pers_totals[$pers] = ['bobot' => $p_bobot, 'nilai_dibobot' => $p_nilai_dibobot];
                        $global_bobot_sum += $p_bobot;
                        $global_nilai_dibobot += $p_nilai_dibobot;
                    }
                    $global_bobot_sum = round($global_bobot_sum, 2);
                    $global_nilai_dibobot = round($global_nilai_dibobot, 2);

                    // fungsi helper warna status
                    if (!function_exists('statusColor')) {
                        function statusColor($s) {
                            $s = strtolower(trim((string)$s));
                            if ($s === 'disetujui') return 'text-success';
                            elseif ($s === 'ada catatan' || $s === 'catatan') return 'text-warning';
                            elseif ($s === 'belum dinilai' || $s === '' || $s === 'pending') return 'text-danger';
                            else return 'text-muted';
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
                                <th>Status Penilai 1</th>
                                <th>Status Penilai 2</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($grouped)): ?>
                                <?php $no = 1;
                                foreach ($grouped as $pers => $sasList):
                                    $pers_rowspan = 0;
                                    foreach ($sasList as $sas => $items) $pers_rowspan += count($items);
                                ?>
                                    <?php $firstPers = true; ?>
                                    <?php foreach ($sasList as $sas => $items): ?>
                                        <?php $sas_rowspan = count($items);
                                        $firstSas = true; ?>
                                        <?php foreach ($items as $it): ?>
                                            <tr>
                                                <td><?= $no++ ?></td>
                                                <?php if ($firstPers): ?>
                                                    <td rowspan="<?= $pers_rowspan ?>" class="text-start align-middle" style="background-color:#eaf6ea; color:#0a6b2b; font-weight:600;">
                                                        <?= htmlspecialchars($pers) ?>
                                                    </td>
                                                    <?php $firstPers = false; ?>
                                                <?php endif; ?>

                                                <?php if ($firstSas): ?>
                                                    <td rowspan="<?= $sas_rowspan ?>" class="text-start align-middle" style="background-color:#eef8ff;">
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

                                                <td class="text-center align-middle"><?= number_format($it->pencapaian ?? 0, 2) ?></td>
                                                <td class="text-center align-middle"><?= number_format($it->nilai ?? 0, 2) ?></td>
                                                <td class="text-center align-middle"><?= number_format($it->nilai_dibobot ?? 0, 2) ?></td>

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
                                        <td colspan="2"></td>
                                    </tr>
                                <?php endforeach; ?>

                                <!-- total akhir -->
                                <tr class="fw-bold" style="background-color:#1b722a; color:#fff;">
                                    <td colspan="3" class="text-center">Total</td>
                                    <td><?= number_format($global_bobot_sum, 2) ?></td>
                                    <td colspan="6" class="text-end">Total Nilai Dibobot</td>
                                    <td><?= number_format($global_nilai_dibobot, 2) ?></td>
                                    <td colspan="2"></td>
                                </tr>
                            <?php else: ?>
                                <tr>
                                    <td colspan="13" class="text-muted">Belum ada data penilaian pada periode arsip ini.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Penilaian Budaya (Read-Only) -->
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-body">
                    <h5 class="text-success fw-bold mb-3">
                        <i class="mdi mdi-account-star-outline me-2"></i> Penilaian Budaya
                    </h5>
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead class="text-center align-middle">
                                <tr class="bg-success text-white fw-bold">
                                    <th colspan="4">Budaya Kerja</th>
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
                                $budaya_nilai = $budaya_nilai ?? [];
                                if (!empty($budaya)) :
                                    foreach ($budaya as $b) :
                                        $b_data = is_object($b) ? (array)$b : $b;
                                        $panduanList = json_decode($b_data['panduan_perilaku'], true);
                                        if (is_array($panduanList)) :
                                            foreach ($panduanList as $pIndex => $p) :
                                                $nilaiKey = "budaya_{$no}_{$pIndex}";
                                                $nilai = isset($budaya_nilai[$nilaiKey]) ? (int)$budaya_nilai[$nilaiKey] : 0;
                                                $labelNilai = "<span class='text-muted fst-italic'>Belum Dinilai</span>";
                                                $color = "";
                                                switch ($nilai) {
                                                    case 1: $labelNilai = "1 - Sangat Jarang"; $color = "text-danger"; break;
                                                    case 2: $labelNilai = "2 - Jarang"; $color = "text-warning"; break;
                                                    case 3: $labelNilai = "3 - Kadang"; $color = "text-primary"; break;
                                                    case 4: $labelNilai = "4 - Sering"; $color = "text-success"; break;
                                                    case 5: $labelNilai = "5 - Selalu"; $color = "fw-bold"; break;
                                                }
                                ?>
                                                <tr>
                                                    <?php if ($pIndex === 0): ?>
                                                        <td class="text-center align-middle" rowspan="<?= count($panduanList); ?>"><?= $no; ?></td>
                                                        <td class="align-middle" rowspan="<?= count($panduanList); ?>"><?= htmlspecialchars($b_data['perilaku_utama']); ?></td>
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
                                else :
                                    echo '<tr><td colspan="4" class="text-center text-muted">Data penilaian budaya belum tersedia.</td></tr>';
                                endif;
                                ?>
                            </tbody>
                            <tfoot class="text-center fw-bold bg-success text-white">
                                <tr>
                                    <td colspan="3" class="text-end align-middle">Rata-Rata Nilai Internalisasi Budaya</td>
                                    <td>
                                        <input type="text" class="form-control form-control-sm text-center" value="<?= number_format($rata_rata_budaya ?? 0, 2); ?>" readonly>
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
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
                    $total_skor     = $nilai_akhir['nilai_sasaran'] ?? 0;
                    $avg_budaya     = number_format($rata_rata_budaya ?? 0, 2);
                    $kontrib_sasaran = $total_skor * 0.95;
                    $kontrib_budaya = $avg_budaya * 0.05;
                    $total_nilai    = $nilai_akhir['total_nilai'] ?? $kontrib_sasaran + $kontrib_budaya;
                    $pencapaian_pct = floatval(str_replace('%', '', $nilai_akhir['pencapaian'] ?? 0));
                    $predikat       = $nilai_akhir['predikat'] ?? 'Minus (M)';
                    $fraud          = $nilai_akhir['fraud'] ?? 0;
                    $koefisien      = $nilai_akhir['koefisien'] ?? 100;
                    ?>

                    <div class="row">
                        <div class="col-lg-8">
                            <table class="table table-borderless">
                                <tr>
                                    <td>Total Nilai Sasaran Kerja</td>
                                    <td style="width:140px; text-align:right;"><?= number_format($total_skor, 2) ?></td>
                                    <td style="width:160px; text-align:center;">x Bobot % Sasaran Kerja</td>
                                    <td style="width:100px; text-align:right;">95%</td>
                                    <td style="width:140px; text-align:right;"><?= number_format($kontrib_sasaran, 2) ?></td>
                                </tr>
                                <tr>
                                    <td>Rata-rata Nilai Internalisasi Budaya</td>
                                    <td style="text-align:right;"><?= number_format($avg_budaya, 2) ?></td>
                                    <td style="text-align:center;">x Bobot % Budaya Perusahaan</td>
                                    <td style="text-align:right;">5%</td>
                                    <td style="text-align:right;"><?= number_format($kontrib_budaya, 2) ?></td>
                                </tr>
                                <tr>
                                    <td colspan="4" class="text-end fw-bold">Total Nilai</td>
                                    <td class="fw-bold" style="text-align:right;"><?= number_format($total_nilai, 2) ?></td>
                                </tr>
                                <tr>
                                    <td colspan="5" class="text-muted small">
                                        Fraud<br>
                                        <span class="text-muted small">(1 jika melakukan fraud, 0 jika tidak melakukan fraud)</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="4" class="text-end fw-bold">Status Fraud</td>
                                    <td class="fw-bold text-danger" style="text-align:right;"><?= $fraud ?></td>
                                </tr>
                                <tr>
                                    <td colspan="4" class="text-end fw-bold">Koefisien Penilaian</td>
                                    <td class="fw-bold" style="text-align:right;"><?= number_format($koefisien, 0) ?>%</td>
                            </table>
                        </div>

                        <div class="col-lg-4">
                            <div class="mb-3">
                                <div class="border rounded p-3 bg-light text-center">
                                    <h6 class="fw-bold">Nilai Akhir</h6>
                                    <div class="display-6 text-success fw-bolder"><?= number_format($total_nilai, 2) ?></div>
                                </div>
                            </div>
                            <div class="mb-3">
                                <div class="border rounded p-3 bg-light text-center">
                                    <h6 class="fw-bold">Pencapaian Akhir</h6>
                                    <div class="display-6 text-success fw-bolder"><?= number_format($pencapaian_pct, 2) ?>%</div>
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


        </div> <!-- container -->
    </div> <!-- content -->
</div>
<!-- ============================================================== -->
<!-- End Page content -->
<!-- ============================================================== -->

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
    }
</style>