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
                        <h4 class="page-title"><i class="mdi mdi-clipboard-pulse-outline mr-2 text-primary"></i> Monitoring Kinerja Bulanan</h4>
                    </div>
                </div>
            </div>

            <div class="row d-flex align-items-stretch">
                <div class="col-12 mb-3 d-flex">
                    <div class="card w-100">
                        <div class="card-body">
                            <h5 class="text-primary font-weight-bold mb-3">
                                <i class="mdi mdi-calendar-month-outline mr-2"></i> Pilih Periode Bulanan & Pegawai
                            </h5>
                            <form id="formMonitoring" action="<?= base_url('Administrator/cariPenilaianBulanan'); ?>" method="post" class="row">

                                <!-- Pilih Bulan -->
                                <div class="col-md-4 mb-3">
                                    <label class="text-dark font-weight-medium">Pilih Bulan:</label>
                                    <select id="periode_select" name="periode" class="form-control mb-2" required>
                                        <option value="">-- Pilih Bulan --</option>
                                        <?php
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
                                        $tahunNow = isset($tahun_dipilih) ? $tahun_dipilih : date('Y');
                                        foreach ($bulanList as $bln => $namaBulan):
                                            $awal = "$tahunNow-$bln-01";
                                            $akhir = date('Y-m-t', strtotime($awal));
                                            $val = "$awal|$akhir";
                                            $selected = (isset($periode_awal) && isset($periode_akhir) && $periode_awal == $awal && $periode_akhir == $akhir) ? 'selected' : '';
                                        ?>
                                            <option value="<?= $val ?>" <?= $selected ?>>
                                                <?= $namaBulan ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <!-- Pilih Tahun -->
                                <div class="col-md-4 mb-3">
                                    <label class="text-dark font-weight-medium">Pilih Tahun:</label>
                                    <select name="tahun" class="form-control mb-2" required>
                                        <option value="">-- Pilih Tahun --</option>
                                        <?php foreach ($tahun_list as $t): ?>
                                            <option value="<?= $t->tahun ?>" <?= (isset($tahun_dipilih) && $tahun_dipilih == $t->tahun) ? 'selected' : '' ?>>
                                                <?= $t->tahun ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <!-- Input NIK -->
                                <div class="col-md-4 mb-3">
                                    <label class="text-dark font-weight-medium">Masukkan NIK Pegawai:</label>
                                    <input type="text" id="nik_input" name="nik" class="form-control mb-2"
                                        placeholder="Masukkan NIK Pegawai"
                                        value="<?= isset($nik) ? htmlspecialchars($nik) : '' ?>" required>
                                </div>

                                <div class="col-12 text-right">
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

                <!-- GRAFIK LINE CHART -->
                <div class="row mt-0">
                    <div class="col-12">
                        <div class="card shadow-sm border-0">
                            <div class="card-body">
                                <h5 class="text-primary font-weight-bold mb-3">
                                    <i class="mdi mdi-chart-line mr-2"></i> Grafik Pencapaian Bulanan
                                </h5>
                                <canvas id="grafikKinerja" height="70"></canvas>
                            </div>
                        </div>
                    </div>
                </div>


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
                                                            $subtotal_bobot += (float) $i->bobot;
                                                            $subtotal_nilai += (float) ($i->nilai_dibobot ?? 0);
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

                                                                <td class="text-center align-middle"><?= $i->pencapaian ?? '-'; ?></td>
                                                                <td class="text-center align-middle"><?= $i->nilai ?? '-'; ?></td>
                                                                <td class="text-center align-middle"><?= $i->nilai_dibobot ?? '-'; ?></td>
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
                                                    <td colspan="10" class="text-center">Belum ada data penilaian untuk pegawai ini</td>
                                                </tr>
                                            <?php endif; ?>
                                        </tbody>
                                        <?php if (!empty($penilaian_pegawai)): ?>
                                            <tfoot style="background-color:#2E7D32;color:#fff;font-weight:bold;text-align:center;">
                                                <tr>
                                                    <td colspan="3">Total</td>
                                                    <td><?= array_sum(array_column($penilaian_pegawai, 'bobot')); ?></td>
                                                    <td colspan="5">Total Nilai Dibobot</td>
                                                    <?php
                                                    $total_nilai = array_sum(array_map('floatval', array_column($penilaian_pegawai, 'nilai_dibobot')));
                                                    ?>
                                                    <td><?= number_format(round($total_nilai, 2), 2); ?></td>
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
                        $total_skor      = number_format(round(floatval($total_nilai ?? 0), 2), 2);
                        $avg_budaya      = $nilai_budaya ?? 0;
                        $kontrib_sasaran = $total_skor * 0.95;
                        $kontrib_budaya  = $avg_budaya * 0.05;
                        $total_nilai     = number_format($kontrib_sasaran + $kontrib_budaya, 2);
                        $nilai           = $nilai_akhir->nilai_akhir ?? 0;
                        $pencapaian_pct  = floatval(str_replace('%', '', $monitoring_bulanan->pencapaian_akhir ?? 0));
                        $predikat        = $nilai_akhir->predikat ?? 'Minus (M)';
                        $fraud           = $fraud ?? 0;
                        $koefisien       = $koefisien ?? 100;
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
                        $nilai_akhir_value = $monitoring_bulanan->nilai_akhir ?? 0; // pastikan ada nilai
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

<!-- minimal CSS/JS untuk formatting dan autosave per baris (view-only formatting, autosave tetap kirim angka mentah) -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const periodeSelect = document.getElementById('periode_select');
        const nikInput = document.getElementById('nik_input');
        const form = document.getElementById('formMonitoring');

        if (periodeSelect && form) {
            periodeSelect.addEventListener('change', function() {
                const nikInputAtas = document.getElementById('nik_input');
                const nikHidden = document.getElementById('nik'); // dari detail pegawai
                const nik = nikHidden ? nikHidden.value.trim() : (nikInputAtas ? nikInputAtas.value.trim() : '');

                if (nik !== '' && this.value !== '') {
                    // masukkan NIK ke form sebelum submit
                    if (nikInputAtas) nikInputAtas.value = nik;
                    form.submit();
                }
            });
        }


        // helper clean & format (koma sebagai thousand separator di display)
        function cleanNumericString(s) {
            return s == null ? '' : String(s).trim().replace(/[^0-9\.\-]/g, '');
        }

        function formatRpDisplay(num) {
            if (num == null || num === '') return '';
            var v = parseFloat(String(num).replace(/[^0-9\.\-]/g, ''));
            if (isNaN(v)) return '';
            var sign = v < 0 ? '-' : '';
            var intPart = Math.trunc(Math.abs(v)).toString();
            intPart = intPart.replace(/\B(?=(\d{3})+(?!\d))/g, ",");
            return sign + "Rp. " + intPart;
        }

        function getRawFromInput(el) {
            if (!el) return '';
            return (el.dataset.raw !== undefined ? el.dataset.raw : el.value) || '';
        }

        function setRaw(el, raw) {
            if (!el) return;
            el.dataset.raw = raw == null ? '' : String(raw);
        }

        // attach handlers
        function attach(el, row) {
            if (!el) return;
            if (el.dataset.raw === undefined) setRaw(el, el.value || '');
            // initial display
            var raw0 = getRawFromInput(el);
            if (raw0 && Math.abs(parseFloat(cleanNumericString(raw0) || 0)) >= 1000) {
                el.value = formatRpDisplay(raw0);
                el.classList.add('hide-text');
            }
            el.addEventListener('focus', function() {
                el.value = el.dataset.raw || '';
                el.classList.remove('hide-text');
                try {
                    el.setSelectionRange(el.value.length, el.value.length);
                } catch (e) {}
            });
            el.addEventListener('input', function() {
                var cleaned = cleanNumericString(el.value);
                setRaw(el, cleaned);
                // trigger calculation
                if (window.hitungTotal) window.hitungTotal();
            });
            el.addEventListener('blur', function() {
                var cleaned = cleanNumericString(el.value);
                setRaw(el, cleaned);
                var v = parseFloat(cleaned || 0);
                if (!isNaN(v) && Math.abs(v) >= 1000) {
                    el.value = formatRpDisplay(v);
                    el.classList.add('hide-text');
                } else {
                    el.value = cleaned;
                    el.classList.remove('hide-text');
                }
                // autosave per baris
                var rowEl = row || el.closest('tr[data-id]');
                if (rowEl) autosaveBarisBulanan(rowEl);
            });
        }

        // attach to all existing inputs
        document.querySelectorAll('#tabel-penilaian tbody tr[data-id]').forEach(function(row) {
            attach(row.querySelector('.target-input'), row);
            attach(row.querySelector('.realisasi-input'), row);
        });

        // observe new rows
        var tbody = document.querySelector('#tabel-penilaian tbody');
        if (tbody) {
            new MutationObserver(function(muts) {
                muts.forEach(function(m) {
                    m.addedNodes && m.addedNodes.forEach(function(n) {
                        if (n.nodeType === 1) {
                            attach(n.querySelector('.target-input'), n);
                            attach(n.querySelector('.realisasi-input'), n);
                        }
                    });
                });
            }).observe(tbody, {
                childList: true,
                subtree: true
            });
        }

        // autosave per baris (kirim raw values)
        function autosaveBarisBulanan(row) {
            var indikator_id = row.dataset.id || '';
            var target = cleanNumericString(getRawFromInput(row.querySelector('.target-input')) || '');
            var batas_waktu = (row.querySelector('input[type="date"]') || {}).value || '';
            var realisasi = cleanNumericString(getRawFromInput(row.querySelector('.realisasi-input')) || '');
            var pencapaian = (row.querySelector('.pencapaian-output') || {}).value || '';
            var nilai = (row.querySelector('.nilai-output') || {}).value || '';
            var nilai_dibobot = (row.querySelector('.nilai-bobot-output') || {}).value || '';
            var periode_awal = document.getElementById('periode_awal') ? document.getElementById('periode_awal').value : '';
            var periode_akhir = document.getElementById('periode_akhir') ? document.getElementById('periode_akhir').value : '';
            var nik = document.getElementById('nik') ? document.getElementById('nik').value : '';

            if ((target === '' && realisasi === '')) return;

            fetch('<?= base_url("Administrator/simpanPenilaianBarisBulanan") ?>', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: `nik=${encodeURIComponent(nik)}&indikator_id=${encodeURIComponent(indikator_id)}&target=${encodeURIComponent(target)}&batas_waktu=${encodeURIComponent(batas_waktu)}&realisasi=${encodeURIComponent(realisasi)}&pencapaian=${encodeURIComponent(pencapaian)}&nilai=${encodeURIComponent(nilai)}&nilai_dibobot=${encodeURIComponent(nilai_dibobot)}&periode_awal=${encodeURIComponent(periode_awal)}&periode_akhir=${encodeURIComponent(periode_akhir)}`
            });
        }

        // read-only view: format currency display for any displayed inputs (no autosave)
        document.querySelectorAll('#tabel-penilaian tbody tr[data-id]').forEach(function(row) {
            const display = row.querySelector('.format-currency');
            const input = row.querySelector('.realisasi-input');
            if (input && display) {
                const raw = (input.value || '').toString();
                if (raw && Math.abs(parseFloat(raw.replace(/[^0-9\.\-]/g, ''))) >= 1000) {
                    display.textContent = formatRpDisplay(raw);
                    input.classList.add('hide-text');
                } else {
                    display.textContent = '';
                    input.classList.remove('hide-text');
                }
            }
        });

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

        // =================================================================
        // GRAFIK LINE CHART (dengan warna gradasi dinamis)
        // =================================================================
        <?php
        $bulanList = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
        $pencapaian_bulanan = array_fill(0, 12, 0); // Gunakan null untuk data kosong

        if (!empty($monitoring_bulanan_tahun)) {
            foreach ($monitoring_bulanan_tahun as $mb) {
                $idx = intval($mb->bulan) - 1;
                if ($idx >= 0 && $idx <= 11) {
                    $pencapaian_bulanan[$idx] = floatval($mb->pencapaian_akhir ?? 0);
                }
            }
        }
        ?>

        const labelsBulan = <?= json_encode($bulanList) ?>;
        const dataPencapaian = <?= json_encode($pencapaian_bulanan) ?>;

        const ctx = document.getElementById('grafikKinerja')?.getContext('2d');

        if (ctx) {
            // 1. Tentukan warna untuk setiap segmen garis
            const segmentColors = dataPencapaian.map((current, i, arr) => {
                if (i === 0) return '#43A047'; // Titik awal selalu hijau
                const prev = arr[i - 1];
                if (prev === null || current === null) return '#cccccc'; // Abu-abu jika ada data kosong

                const diff = current - prev;
                if (diff > 0) return '#43A047'; // Naik -> Hijau
                if (diff < 0) return '#e53935'; // Turun -> Merah
                return '#fbc02d'; // Datar -> Kuning
            });

            // 2. Tentukan warna untuk setiap titik
            const bulanAktifIndex = labelsBulan.findIndex((_, i) => {
                const selectedPeriode = '<?= $periode_awal ?? '' ?>';
                if (!selectedPeriode) return false;
                return i === parseInt(selectedPeriode.split('-')[1], 10) - 1;
            });

            const pointColors = dataPencapaian.map((val, i) => {
                if (val === null) return 'transparent'; // Sembunyikan titik jika data kosong
                if (i === bulanAktifIndex) return '#005f29'; // Sorot bulan aktif
                return segmentColors[i]; // Warna titik sama dengan warna segmen
            });

            const pointBorderColors = dataPencapaian.map((val, i) => {
                if (val === null) return 'transparent';
                return i === bulanAktifIndex ? '#fff' : segmentColors[i];
            });

            // 3. Buat gradasi untuk area di bawah garis
            const areaGradient = ctx.createLinearGradient(0, 0, 0, 350);
            areaGradient.addColorStop(0, 'rgba(67, 160, 71, 0.3)');
            areaGradient.addColorStop(1, 'rgba(67, 160, 71, 0)');

            // 4. Inisialisasi Chart
            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: labelsBulan,
                    datasets: [{
                        label: 'Pencapaian (%)',
                        data: dataPencapaian,
                        // Buat gradasi warna garis
                        borderColor: (chartContext) => {
                            const chart = chartContext.chart;
                            const {
                                ctx,
                                chartArea
                            } = chart;
                            if (!chartArea) return null;

                            const gradient = ctx.createLinearGradient(chartArea.left, 0, chartArea.right, 0);
                            const totalPoints = dataPencapaian.length - 1;
                            if (totalPoints <= 0) return segmentColors[0] || '#43A047';

                            segmentColors.forEach((color, i) => {
                                gradient.addColorStop(i / totalPoints, color);
                            });

                            return gradient;
                        },
                        borderWidth: 3,
                        tension: 0.3,
                        spanGaps: true,
                        fill: true,
                        backgroundColor: areaGradient,
                        pointBackgroundColor: pointColors,
                        pointBorderColor: pointBorderColors,
                        pointBorderWidth: 2,
                        pointRadius: 6,
                        pointHoverRadius: 10,
                        pointHoverBackgroundColor: '#fff',
                    }]
                },
                options: {
                    responsive: true,
                    interaction: {
                        mode: 'index',
                        intersect: false,
                    },
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
                                label: (context) => {
                                    const label = context.dataset.label || '';
                                    const value = context.parsed.y;
                                    return value !== null ? `${label}: ${value.toFixed(2)}%` : `${label}: (Data Kosong)`;
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true, // Mulai dari 0 agar lebih mudah dibaca
                            max: 130, // Batas atas
                            title: {
                                display: true,
                                text: 'Pencapaian (%)'
                            },
                            ticks: {
                                stepSize: 25
                            }
                        }
                    }
                }
            });
        }
    });
</script>