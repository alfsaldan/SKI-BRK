<div class="content-page">
    <div class="content">
        <div class="container-fluid">

            <!-- Judul Halaman -->
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box">
                        <div class="page-title-right">
                            <ol class="breadcrumb m-0">
                                <li class="breadcrumb-item"><a href="javascript:void(0);">KPI Online-BRKS</a></li>
                                <li class="breadcrumb-item active">Monitoring Kinerja Bulanan</li>
                            </ol>
                        </div>
                        <h4 class="page-title"><i class="mdi mdi-account-edit mr-2 text-primary"></i> Monitoring Kinerja Bulanan</h4>
                    </div>
                </div>
            </div>

            <!-- GRAFIK LINE CHART -->
            <div class="row mt-4">
                <div class="col-12">
                    <div class="card shadow-sm border-0">
                        <div class="card-body">
                            <h5 class="text-primary font-weight-bold mb-3">
                                <i class="mdi mdi-chart-line mr-2"></i> Grafik Pencapaian Bulanan
                            </h5>
                            <canvas id="grafikKinerja" height="100"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row d-flex align-items-stretch justify-content-start">
                <!-- Card Pilih Periode -->
                <div class="col-md-6 mb-3">
                    <div class="card h-100">
                        <div class="card-body">
                            <h5 class="text-primary font-weight-bold mb-3">
                                <i class="mdi mdi-calendar-month-outline mr-2"></i> Pilih Periode Bulanan
                            </h5>

                            <form id="formMonitoring"
                                action="<?= base_url('Pegawai/cariPenilaianBulanan'); ?>"
                                method="post" class="row">

                                <!-- Pilih Bulan -->
                                <div class="col-12 mb-3">
                                    <label class="text-dark font-weight-medium">Pilih Bulan:</label>
                                    <select id="periode_select" name="periode" class="form-control mb-2" required>
                                        <option value="">-- Pilih Bulan --</option>
                                        <?php
                                        $tahun = date('Y');
                                        $bulanList = [
                                            '01' => 'Januari',
                                            '02' => 'Februari',
                                            '03' => 'Maret',
                                            '04' => 'April',
                                            '05' => 'Mei',
                                            '06' => 'Juni',
                                            '07' => 'Juli',
                                            '08' => 'Agustus',
                                            '09' => 'September',
                                            '10' => 'Oktober',
                                            '11' => 'November',
                                            '12' => 'Desember'
                                        ];

                                        foreach ($bulanList as $bln => $namaBulan):
                                            $awal = "$tahun-$bln-01";
                                            $akhir = date('Y-m-t', strtotime($awal));
                                            $val = "$awal|$akhir";
                                            $selected = (isset($periode_awal) && isset($periode_akhir)
                                                && $periode_awal == $awal && $periode_akhir == $akhir)
                                                ? 'selected' : '';
                                        ?>
                                            <option value="<?= $val ?>" <?= $selected ?>>
                                                <?= $namaBulan . " " . $tahun ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <!-- Hidden input -->
                                <input type="hidden" name="nik" value="<?= $this->session->userdata('nik'); ?>">
                                <!-- Hidden periode agar JS dan autosave tahu bulan/tahun aktif -->
                                <input type="hidden" id="periode_awal" name="periode_awal" value="<?= htmlspecialchars($periode_awal ?? date('Y-m-01')) ?>">
                                <input type="hidden" id="periode_akhir" name="periode_akhir" value="<?= htmlspecialchars($periode_akhir ?? date('Y-m-t')) ?>">

                                <!-- Tombol disembunyikan -->
                                <div class="col-12 text-right" hidden>
                                    <button type="submit" class="btn btn-success">
                                        <i class="mdi mdi-magnify"></i> Tampilkan
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- jika ada data pegawai, tampilkan sampai tabel penilaian (sama struktur tabel seperti penilaiankinerja) -->
            <?php if (isset($pegawai_detail) && $pegawai_detail) { ?>




                <div class="row">
                    <div class="col-12">
                        <div class="card shadow-sm border-0 mt-3">
                            <div class="card-body">
                                <!-- Detail singkat Pegawai -->
                                <div class="row mb-3">
                                    <div class="col-md-6 mb-3">
                                        <h5 class="text-primary font-weight-bold mb-3">
                                            <i class="mdi mdi-account-circle-outline mr-2"></i>Detail Pegawai
                                        </h5>
                                        <ul class="list-group list-group-flush">
                                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                                <span class="text-dark font-weight-medium">NIK</span>
                                                <span class="badge badge-primary badge-pill"><?= $pegawai_detail->nik; ?></span>
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
                                                <span class="text-dark font-weight-medium">Unit Kantor</span>
                                                <span class="text-dark"><?= $pegawai_detail->unit_kerja; ?> <?= $pegawai_detail->unit_kantor ?? '-'; ?></span>
                                            </li>
                                        </ul>
                                        <input type="hidden" id="nik" value="<?= $pegawai_detail->nik ?>">
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <h5 class="text-success font-weight-bold mb-3">
                                            <i class="mdi mdi-file-document-outline mr-2"></i>Informasi Periode
                                        </h5>
                                        <ul class="list-group list-group-flush">
                                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                                <span class="text-dark font-weight-medium">Periode Penilaian</span>
                                                <span class="text-dark">
                                                    <?= date('d M Y', strtotime($periode_awal)) . " s/d " . date('d M Y', strtotime($periode_akhir)); ?>
                                                </span>
                                            </li>
                                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                            </li>
                                        </ul>
                                    </div>

                                </div>
                                <hr>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12">
                        <div class="card shadow-sm border-0 mt-3">
                            <div class="card-body">
                                <h5 class="text-success font-weight-bold mb-3">
                                    <i class="mdi mdi-star-circle mr-2"></i> Hasil Penilaian
                                </h5>
                                <!-- TABEL PENILAIAN -->
                                <div class="table-responsive">
                                    <table class="table table-bordered" id="tabel-penilaian">
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
                                                foreach ($penilaian_pegawai as $row) {
                                                    $p = $row->perspektif ?? '';
                                                    $s = $row->sasaran_kerja ?? '';
                                                    $grouped[$p][$s][] = $row;
                                                }

                                                function count_rows($arr)
                                                {
                                                    $sum = 0;
                                                    foreach ($arr as $items) {
                                                        $sum += count($items);
                                                    }
                                                    return $sum;
                                                }
                                                ?>

                                                <?php foreach ($order as $persp): ?>
                                                    <?php if (empty($grouped[$persp])) continue; ?>
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
                                                            $subtotal_bobot += (float) ($i->bobot ?? 0);
                                                            $subtotal_nilai += (float) ($i->nilai_dibobot ?? 0);
                                                            ?>
                                                            <tr data-id="<?= htmlspecialchars($i->id ?? $i->indikator_id ?? '') ?>"
                                                                data-indikator="<?= htmlspecialchars($i->indikator ?? '') ?>"
                                                                data-perspektif="<?= htmlspecialchars($persp ?? '') ?>">
                                                                <?php if ($first_persp_cell): ?>
                                                                    <td rowspan="<?= $persp_rows; ?>"
                                                                        style="vertical-align:middle;font-weight:600;background:#C8E6C9;">
                                                                        <?= htmlspecialchars($persp ?? '') ?>
                                                                    </td>
                                                                    <?php $first_persp_cell = false; ?>
                                                                <?php endif; ?>

                                                                <?php if ($first_sas_cell): ?>
                                                                    <td rowspan="<?= $sasaran_rows; ?>"
                                                                        style="vertical-align:middle;background:#E3F2FD;">
                                                                        <?= htmlspecialchars($sasaran ?? '') ?>
                                                                    </td>
                                                                    <?php $first_sas_cell = false; ?>
                                                                <?php endif; ?>

                                                                <td><?= htmlspecialchars($i->indikator ?? '-') ?></td>
                                                                <td class="text-center align-middle"><?= htmlspecialchars($i->bobot ?? 0) ?></td>
                                                                <td class="text-center align-middle" style="min-width:150px;">
                                                                    <?= ($i->target >= 1000) ? 'Rp. ' . number_format($i->target, 0, ',', '.') : htmlspecialchars($i->target ?? 0); ?>
                                                                </td>
                                                                <td class="text-center align-middle" style="min-width:110px;">
                                                                    <?= htmlspecialchars($i->batas_waktu ?? '-') ?>
                                                                </td>
                                                                <td class="text-center align-middle" style="min-width:150px;">
                                                                    <input type="number"
                                                                        class="form-control form-control-sm text-center realisasi-input"
                                                                        value="<?= htmlspecialchars($i->realisasi ?? 0) ?>"
                                                                        data-target="<?= htmlspecialchars($i->target ?? 0) ?>"
                                                                        data-bobot="<?= htmlspecialchars($i->bobot ?? 0) ?>"
                                                                        data-indikator="<?= htmlspecialchars($i->indikator ?? '') ?>"
                                                                        step="any" min="0">
                                                                </td>
                                                                <td class="text-center align-middle">
                                                                    <input type="text" class="form-control form-control-sm text-center pencapaian-output" readonly>
                                                                </td>
                                                                <td class="text-center align-middle">
                                                                    <input type="text" class="form-control form-control-sm text-center nilai-output" readonly>
                                                                </td>
                                                                <td class="text-center align-middle">
                                                                    <input type="text" class="form-control form-control-sm text-center nilai-bobot-output" readonly>
                                                                </td>
                                                            </tr>
                                                        <?php endforeach; ?>
                                                    <?php endforeach; ?>

                                                    <!-- Subtotal baris -->
                                                    <tr class="subtotal-row" data-perspektif="<?= htmlspecialchars($persp ?? '') ?>" style="font-weight:bold;background:#F1F8E9;">
                                                        <td colspan="3">Sub Total <?= htmlspecialchars($persp ?? '') ?></td>
                                                        <td class="text-center subtotal-bobot"><?= $subtotal_bobot; ?></td>
                                                        <td colspan="5" class="text-center">Sub Total Nilai Dibobot</td>
                                                        <td class="text-center subtotal-nilai-dibobot"><?= number_format(round($subtotal_nilai, 2), 2); ?></td>
                                                    </tr>

                                                <?php endforeach; ?>
                                            <?php else: ?>
                                                <tr>
                                                    <td colspan="10" class="text-center">Belum ada data penilaian untuk pegawai ini</td>
                                                </tr>
                                            <?php endif; ?>
                                        </tbody>

                                        <?php if (!empty($penilaian_pegawai)): ?>
                                            <tfoot style="background-color:#2E7D32;color:#fff;font-weight:bold;text-align:center;">
                                                <tr>
                                                    <td colspan="3">Total</td>
                                                    <td id="total-bobot"><?= array_sum(array_column($penilaian_pegawai, 'bobot')); ?></td>
                                                    <td colspan="5">Total Nilai Dibobot</td>
                                                    <?php
                                                    $total_nilai = array_sum(array_map('floatval', array_column($penilaian_pegawai, 'nilai_dibobot')));
                                                    ?>
                                                    <td id="total-nilai-dibobot"><?= number_format(round($total_nilai, 2), 2); ?></td>
                                                </tr>
                                            </tfoot>
                                        <?php endif; ?>
                                    </table>
                                </div>
                                <!-- akhir tabel -->
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Form Penilaian Budaya (Read-Only untuk Pegawai) -->
                <div class="row mt-4" hidden>
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

                                            if (!empty($budaya)) :
                                                foreach ($budaya as $b) :
                                                    // Pastikan $b adalah array jika diambil dari DB object
                                                    $b_data = is_object($b) ? (array)$b : $b;
                                                    $panduanList = json_decode($b_data['panduan_perilaku'], true);

                                                    if (is_array($panduanList)) :
                                                        foreach ($panduanList as $pIndex => $p) :
                                                            // Key sesuai format JSON nilai_budaya
                                                            $nilaiKey = "budaya_{$no}_{$pIndex}";
                                                            $nilai = isset($budaya_nilai[$nilaiKey]) ? (int)$budaya_nilai[$nilaiKey] : 0;

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
                                            else :
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
                <div class="card mt-4">
                    <div class="card-body">
                        <h5 class="text-success fw-bold mb-3">
                            <i class="mdi mdi-star-circle mr-2"></i> Nilai Akhir
                        </h5>

                        <?php
                        // 🔹 Pastikan data aman
                        $total_skor      = number_format(round($total_nilai, 2), 2);
                        $avg_budaya      = $rata_rata_budaya ?? 0;
                        $kontrib_sasaran = $total_skor * 0.95;
                        $kontrib_budaya  = $avg_budaya * 0.05;
                        $total_nilai     = number_format($kontrib_sasaran + $kontrib_budaya, 2);
                        $nilai           = $nilai_akhir->nilai_akhir ?? 0;
                        $pencapaian_pct  = floatval(str_replace('%', '', $nilai_akhir->pencapaian ?? 0));
                        $predikat        = $nilai_akhir->predikat ?? 'Minus (M)';
                        $fraud           = $nilai_akhir->fraud ?? 0;
                        $koefisien       = $nilai_akhir->koefisien ?? 100;
                        ?>

                        <!-- Bagian Atas: Perhitungan -->
                        <table class="table table-bordered mb-4">
                            <tr>
                                <th>Total Nilai Sasaran Kerja</th>
                                <td class="text-center"><?= number_format($total_skor, 2) ?></td>
                                <td>x Bobot % Sasaran Kerja</td>
                                <td class="text-center">95%</td>
                                <td class="text-center"><?= number_format($kontrib_sasaran, 2) ?></td>
                            </tr>
                            <tr>
                                <th>Rata-rata Nilai Internalisasi Budaya</th>
                                <td class="text-center"><?= number_format($avg_budaya, 2) ?></td>
                                <td>x Bobot % Budaya Perusahaan</td>
                                <td class="text-center">5%</td>
                                <td class="text-center"><?= number_format($kontrib_budaya, 2) ?></td>
                            </tr>
                            <tr>
                                <th colspan="4" class="text-end">Total Nilai</th>
                                <td class="text-center"><?= number_format($total_nilai, 2) ?></td>
                            </tr>
                            <tr>
                                <th colspan="4" class="text-end">
                                    Fraud<br>
                                    <small>(1 jika melakukan fraud, 0 jika tidak melakukan fraud)</small>
                                </th>
                                <td class="text-center"><?= $fraud ?></td>
                            </tr>
                            <tr>
                                <th colspan="4" class="text-end">Koefisien Penilaian</th>
                                <td class="text-center"><?= number_format($koefisien, 0) ?>%</td>
                        </table>

                        <?php
                        // Tentukan predikat & warna berdasarkan nilai akhir
                        $nilai_akhir_value = $nilai_akhir->nilai_akhir ?? 0; // pastikan ada nilai
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
            <?php } ?>

        </div>
    </div>
</div>

<!-- load sweetalert -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        document.getElementById('periode_select').addEventListener('change', function() {
            const val = this.value;
            if (!val) return;

            const [awal, akhir] = val.split('|');
            document.getElementById('periode_awal').value = awal;
            document.getElementById('periode_akhir').value = akhir;

            // submit form untuk reload halaman
            document.getElementById('formMonitoring').submit();
        });

        const nik = document.getElementById('nik')?.value?.trim() || '';
        const periodeAwalEl = document.getElementById('periode_awal');
        const periodeAkhirEl = document.getElementById('periode_akhir');

        const clean = s => s == null ? '' : String(s).trim().replace(/[^0-9.\-]/g, '');
        const formatNumber = n => isNaN(n) ? '-' : parseFloat(n).toFixed(2);

        function hitungPencapaian(target, real, indikator = '') {
            indikator = (indikator || '').toLowerCase();
            const rumus1 = ["biaya", "beban", "efisiensi", "npf pembiayaan", "npf nominal"];
            const rumus3 = ["outstanding", "pertumbuhan"];
            const contains = (arr, txt) => arr.some(k => new RegExp("\\b" + k + "\\b", "i").test(txt));
            let penc = 0;
            if (target <= 999) penc = target == 0 ? 0 : (real / target) * 100;
            else {
                if (contains(rumus1, indikator)) penc = target == 0 ? 0 : ((target + (target - real)) / target) * 100;
                else if (contains(rumus3, indikator)) penc = target == 0 ? 0 : ((real - target) / Math.abs(target) + 1) * 100;
                else penc = target == 0 ? 0 : (real / target) * 100;
            }
            return Math.min(penc, 130);
        }

        function hitungNilai(penc) {
            let nilai = 0;
            if (penc < 0) nilai = 0;
            else if (penc < 80) nilai = (penc / 80) * 2;
            else if (penc < 90) nilai = 2 + ((penc - 80) / 10);
            else if (penc < 110) nilai = 3 + ((penc - 90) / 20 * 0.5);
            else if (penc < 120) nilai = 3.5 + ((penc - 110) / 10 * 1);
            else if (penc < 130) nilai = 4.5 + ((penc - 120) / 10 * 0.5);
            else nilai = 5;
            return nilai;
        }

        function tentukanPredikat(nilai, koef = 1) {
            const n = nilai;
            if (n < 2 * koef) return {
                text: 'Minus (M)',
                class: 'text-danger'
            };
            else if (n < 3 * koef) return {
                text: 'Fair (F)',
                class: 'text-warning'
            };
            else if (n < 3.5 * koef) return {
                text: 'Good (G)',
                class: 'text-primary'
            };
            else if (n < 4.5 * koef) return {
                text: 'Very Good (VG)',
                class: 'text-success'
            };
            else return {
                text: 'Excellent (E)',
                class: 'text-success fw-bold'
            };
        }

        function updateTotals() {
            let totalNilaiDibobot = 0,
                totalBobot = 0;
            const perspMap = {};

            document.querySelectorAll('#tabel-penilaian tbody tr[data-id]').forEach(row => {
                const nd = parseFloat(row.querySelector('.nilai-bobot-output')?.value || 0);
                const b = parseFloat(row.querySelector('.realisasi-input')?.dataset.bobot || 0);
                totalNilaiDibobot += nd;
                totalBobot += b;

                const persp = row.dataset.perspektif || 'umum';
                if (!perspMap[persp]) perspMap[persp] = {
                    bobot: 0,
                    nilai: 0
                };
                perspMap[persp].bobot += b;
                perspMap[persp].nilai += nd;
            });

            // Update subtotal tiap perspektif
            Object.keys(perspMap).forEach(p => {
                const row = document.querySelector(`.subtotal-row[data-perspektif="${p}"]`);
                if (row) {
                    row.querySelector('.subtotal-bobot').textContent = perspMap[p].bobot.toFixed(2);
                    row.querySelector('.subtotal-nilai-dibobot').textContent = perspMap[p].nilai.toFixed(2);
                }
            });

            // Update total akhir di UI
            const koef = parseFloat(document.getElementById('koefisien')?.textContent || 100) / 100;
            const nilaiAkhir = totalNilaiDibobot * koef;
            const pencAkhir = Math.min((nilaiAkhir / 5) * 100, 100);
            const predObj = tentukanPredikat(nilaiAkhir, koef);

            document.getElementById('total-bobot') && (document.getElementById('total-bobot').textContent = totalBobot.toFixed(2));
            document.getElementById('total-nilai-dibobot') && (document.getElementById('total-nilai-dibobot').textContent = totalNilaiDibobot.toFixed(2));
            document.getElementById('nilai-akhir') && (document.getElementById('nilai-akhir').textContent = nilaiAkhir.toFixed(2));
            document.getElementById('pencapaian-akhir') && (document.getElementById('pencapaian-akhir').textContent = pencAkhir.toFixed(2) + '%');
            const predEl = document.getElementById('predikat');
            if (predEl) {
                predEl.textContent = predObj.text;
                predEl.className = predObj.class;
            }

            return {
                nilaiAkhir,
                pencAkhir,
                predObj
            };
        }

        function autosaveRow(row) {
            const indikatorId = row.dataset.id || '';
            const indikatorText = row.dataset.indikator || '';
            const target = parseFloat(row.querySelector('.realisasi-input').dataset.target || 0);
            const bobot = parseFloat(row.querySelector('.realisasi-input').dataset.bobot || 0);
            const realisasi = parseFloat(row.querySelector('.realisasi-input').value || 0);

            const penc = hitungPencapaian(target, realisasi, indikatorText);
            const nilai = hitungNilai(penc);
            const nilaiDibobot = parseFloat((nilai * bobot / 100).toFixed(2));

            row.querySelector('.pencapaian-output').value = penc.toFixed(2);
            row.querySelector('.nilai-output').value = nilai.toFixed(2);
            row.querySelector('.nilai-bobot-output').value = nilaiDibobot.toFixed(2);

            const totals = updateTotals();

            if (!nik || !indikatorId) return;

            const pAwal = periodeAwalEl.value;
            const dt = new Date(pAwal);
            const bulan = dt.getMonth() + 1;
            const tahun = dt.getFullYear();

            const payload = {
                nik,
                bulan,
                tahun,
                indikator: indikatorId,
                nilaiData: {
                    indikator_id: indikatorId,
                    indikator: indikatorText,
                    bobot,
                    target,
                    realisasi,
                    pencapaian: parseFloat(penc.toFixed(2)),
                    nilai: parseFloat(nilai.toFixed(2)),
                    nilai_dibobot: parseFloat(nilaiDibobot.toFixed(2))
                },
                periode_awal: pAwal,
                periode_akhir: periodeAkhirEl.value,
                nilai_akhir_value: parseFloat(totals.nilaiAkhir.toFixed(2)),
                pencapaian_pct: parseFloat(totals.pencAkhir.toFixed(2)),
                predikat: totals.predObj.text
            };

            if (row._autosaveTimer) clearTimeout(row._autosaveTimer);
            row._autosaveTimer = setTimeout(() => {
                fetch('<?= base_url("Pegawai/simpanMonitoringBulanan") ?>', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify(payload)
                    }).then(r => r.json())
                    .then(j => console.log('autosave', j))
                    .catch(err => console.error('autosave error', err));
            }, 300);
        }

        // Event listener input realisasi
        document.querySelectorAll('#tabel-penilaian tbody tr[data-id]').forEach((row) => {
            const input = row.querySelector('.realisasi-input');
            if (!input) return;
            input.addEventListener('input', () => autosaveRow(row));
            input.addEventListener('blur', () => autosaveRow(row));
        });

        // Hitung semua row saat load halaman
        document.querySelectorAll('#tabel-penilaian tbody tr[data-id]').forEach(r => autosaveRow(r));

        <?php if (isset($message) && !empty($message)) : ?>
            Swal.fire({
                icon: '<?= $message['type'] === 'success' ? 'success' : 'error' ?>',
                title: '<?= ucfirst($message['type']) ?>',
                text: '<?= $message['text'] ?>',
                showConfirmButton: false,
                timer: 2500,
                timerProgressBar: true
            });
        <?php endif; ?>



        // ========= Chart Grafik =========
        <?php
        // Labels bulan
        $labelsBulan = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];

        // Siapkan 12 bulan default 0
        $pencapaian_bulanan = array_fill(0, 12, 0);

        // Isi data dari DB
        if (!empty($monitoring_bulanan_tahun)) {
            foreach ($monitoring_bulanan_tahun as $row) {
                $blnIndex = intval($row->bulan) - 1;
                if ($blnIndex >= 0 && $blnIndex <= 11) {
                    // Pastikan numeric
                    $pencapaian_bulanan[$blnIndex] = floatval($row->pencapaian_akhir ?? 0);
                }
            }
        }

        $labelsBulanJson = json_encode($labelsBulan);
        $dataPencapaianJson = json_encode($pencapaian_bulanan);
        ?>

        const labelsBulan = <?= $labelsBulanJson ?>;
        const dataPencapaian = <?= $dataPencapaianJson ?>;

        const ctx = document.getElementById('grafikKinerja').getContext('2d');

        const gradient = ctx.createLinearGradient(0, 0, 0, 400);
        gradient.addColorStop(0, 'rgba(67,160,71,0.4)');
        gradient.addColorStop(1, 'rgba(67,160,71,0)');

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: labelsBulan,
                datasets: [{
                    label: 'Pencapaian (%)',
                    data: dataPencapaian,
                    borderColor: '#43A047',
                    backgroundColor: gradient,
                    borderWidth: 3,
                    tension: 0.3,
                    spanGaps: true,
                    pointBackgroundColor: '#43A047',
                    pointRadius: 6,
                    pointHoverRadius: 9,
                    pointHoverBorderWidth: 3
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    title: {
                        display: true,
                        text: 'Pencapaian (%) per Bulan',
                        font: {
                            size: 15
                        }
                    },
                    legend: {
                        position: 'top'
                    },
                    tooltip: {
                        callbacks: {
                            label: c => `${c.dataset.label}: ${c.parsed.y ?? 0}%`
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Pencapaian (%)'
                        },
                        ticks: {
                            stepSize: 10
                        }
                    }
                }
            }
        });

    });
</script>