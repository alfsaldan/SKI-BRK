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
                            <h5>Masukkan NIK Pegawai</h5>
                            <form action="<?= base_url('Administrator/cariDataPegawai'); ?>" method="post">
                                <input type="text" name="nik" class="form-control" placeholder="Masukkan NIK Pegawai"
                                    required>
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
                        <div class="card mt-3 shadow-sm border-0">
                            <div class="card-body">
                                <div class="row">
                                    <!-- Detail Pegawai -->
                                    <div class="col-md-6 mb-3">
                                        <h5 class="text-primary mb-3 font-weight-bold"><i class="mdi mdi-account-circle-outline mr-2"></i>Detail Pegawai</h5>
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
                                                <span class="text-dark font-weight-medium">Jenis Unit</span>
                                                <span class="text-dark"><?= $pegawai_detail->unit_kerja; ?> <?= $pegawai_detail->unit_kantor ?? '-'; ?></span>
                                            </li>
                                        </ul>
                                        <input type="hidden" id="nik" value="<?= $pegawai_detail->nik ?>">
                                    </div>

                                    <!-- Informasi Penilaian -->
                                    <div class="col-md-6 mb-3">
                                        <h5 class="text-success mb-3 font-weight-bold"><i class="mdi mdi-file-document-outline mr-2"></i>Informasi Penilaian</h5>
                                        <div class="form-group">
                                            <label class="text-dark font-weight-medium"><b>Periode Penilaian:</b></label>
                                            <div class="input-group">
                                                <select id="periode_select" class="form-control text-dark">
                                                    <?php if (!empty($periode_list)): ?>
                                                        <?php foreach ($periode_list as $p): ?>
                                                            <?php
                                                            $label = date('d M Y', strtotime($p->periode_awal)) . " s/d " . date('d M Y', strtotime($p->periode_akhir));
                                                            $selected = ($periode_awal == $p->periode_awal && $periode_akhir == $p->periode_akhir) ? 'selected' : '';
                                                            ?>
                                                            <option value="<?= $p->periode_awal ?>|<?= $p->periode_akhir ?>" <?= $selected ?>>
                                                                <?= $label ?>
                                                            </option>
                                                        <?php endforeach; ?>
                                                    <?php else: ?>
                                                        <option disabled>Belum ada periode penilaian</option>
                                                    <?php endif; ?>
                                                </select>
                                                <div class="input-group-append">
                                                    <button type="button" id="btn-sesuaikan-periode" class="btn btn-success">
                                                        Terapkan
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                        <p class="mt-3 text-dark font-weight-medium"><b>Unit Kantor Penilai:</b> <span class="text-dark"><?= $pegawai_detail->unit_kerja; ?> <?= $pegawai_detail->unit_kantor ?? '-'; ?></span></p>
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
                                                <span class="text-dark font-weight-medium">NIK</span>
                                                <span class="badge badge-info badge-pill"><?= $pegawai_detail->penilai1_nik ?? '-'; ?></span>
                                            </li>
                                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                                <span class="text-dark font-weight-medium">Nama</span>
                                                <span class="text-dark"><?= $pegawai_detail->penilai1_nama ?? '-'; ?></span>
                                            </li>
                                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                                <span class="text-dark font-weight-medium">Jabatan</span>
                                                <span class="text-dark"><?= $pegawai_detail->penilai1_jabatan ?? '-'; ?></span>
                                            </li>
                                        </ul>
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <h5 class="text-warning mb-3 font-weight-bold">
                                            <i class="mdi mdi-account-check-outline mr-2"></i>Penilai II
                                        </h5>
                                        <ul class="list-group list-group-flush">
                                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                                <span class="text-dark font-weight-medium">NIK</span>
                                                <span class="badge badge-warning badge-pill"><?= $pegawai_detail->penilai2_nik ?? '-'; ?></span>
                                            </li>
                                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                                <span class="text-dark font-weight-medium">Nama</span>
                                                <span class="text-dark"><?= $pegawai_detail->penilai2_nama ?? '-'; ?></span>
                                            </li>
                                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                                <span class="text-dark font-weight-medium">Jabatan</span>
                                                <span class="text-dark"><?= $pegawai_detail->penilai2_jabatan ?? '-'; ?></span>
                                            </li>
                                        </ul>
                                    </div>
                                </div>


                                <a href="<?= base_url('Administrator/downloadDataPegawai?nik=' . ($pegawai_detail->nik ?? '') . '&awal=' . $periode_awal . '&akhir=' . $periode_akhir) ?>"
                                    class="btn btn-success mt-3 font-weight-bold" style="background-color:#217346; color:#fff;">
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
                <div class="card mt-3">
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
                                                        <td class="text-center"><?= $i->bobot; ?></td>
                                                        <td><?= $i->target; ?></td>
                                                        <td><?= $i->batas_waktu; ?></td>
                                                        <td><?= $i->realisasi; ?></td>
                                                        <td class="text-center"><?= $i->pencapaian ?? '-'; ?></td>
                                                        <td class="text-center"><?= $i->nilai ?? '-'; ?></td>
                                                        <td class="text-center"><?= $i->nilai_dibobot ?? '-'; ?></td>
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
                    </div>
                </div>
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


        <!-- Nilai Akhir & Catatan -->
        <div class="card mt-4">
            <div class="card-body">
                <h5 class="text-success font-weight-bold mb-3">
                    <i class="mdi mdi-star-circle mr-2"></i> Nilai Akhir (q)
                </h5>

                <!-- Bagian Atas: Perhitungan -->
                <table class="table table-bordered mb-4">
                    <tr>
                        <th>Total Nilai Sasaran Kerja</th>
                        <td class="text-center"><?= $nilai['nilai_sasaran'] ?? 0 ?></td>
                        <td>x Bobot % Sasaran Kerja</td>
                        <td class="text-center">
                            95%<!-- <input type="text" class="form-control form-control-sm text-center"
                                value="95%" readonly> -->
                        </td>
                        <td class="text-center"><?= $nilai['nilai_sasaran'] ?? 0 ?></td>
                    </tr>
                    <tr>
                        <th>Rata-rata Nilai Internalisasi Budaya</th>
                        <td class="text-center"><?= $nilai['nilai_budaya'] ?? 0 ?></td>
                        <td>x Bobot % Budaya Perusahaan</td>
                        <td class="text-center">
                            5%<!-- <input type="text" class="form-control form-control-sm text-center"
                                value="5%" readonly> -->
                        </td>
                        <td class="text-center"><?= $nilai['nilai_budaya'] ?? 0 ?></td>
                    </tr>
                    <tr>
                        <th colspan="4" class="text-right">Total Nilai</th>
                        <td class="text-center"><?= $nilai['total_nilai'] ?? 0 ?></td>
                    </tr>
                    <tr>
                        <th colspan="4" class="text-right">
                            Fraud<br>
                            <small>(1 jika melakukan fraud, 0 jika tidak melakukan fraud)</small>
                        </th>
                        <td class="text-center"><?= $nilai['fraud'] ?? 0 ?></td>
                    </tr>
                </table>

                <!-- Bagian Bawah: Kiri-Kanan -->
                <div class="row">
                    <div class="col-md-6">
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
                                        <h3 id="nilai-akhir">
                                            <?= (isset($nilai['nilai_akhir']) && $nilai['nilai_akhir'] > 0)
                                                ? $nilai['nilai_akhir']
                                                : 'Tidak ada nilai'; ?>
                                        </h3>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="card text-center">
                                    <div class="card-header bg-success text-white">Pencapaian Akhir</div>
                                    <div class="card-body">
                                        <h3 id="pencapaian-akhir"><?= $nilai['pencapaian'] ?? '0%' ?></h3>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Predikat -->
                        <div class="card text-center mb-3">
                            <div class="card-header bg-success text-white">Yudisium / Predikat</div>
                            <div class="card-body">
                                <h3 id="predikat"><?= $nilai['predikat'] ?? '-' ?></h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Aktivitas Coaching / Chat -->
        <div class="card mt-4 border-0 shadow-lg rounded-4 overflow-hidden">
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
                            <div class="chat-bubble p-3 rounded-4 shadow-sm"
                                style="
                            max-width:65%;
                            background: <?= $isPegawai ? 'rgba(13,110,253,0.1)' : 'rgba(255,255,255,0.6)'; ?>;
                            backdrop-filter: blur(10px);
                            border:1px solid rgba(255,255,255,0.4);
                         ">
                                <div class="d-flex align-items-center mb-2">
                                    <span class="fw-semibold me-2" style="font-size:0.9rem;">
                                        <?= $c->nama_pengirim ?? $c->pengirim_nik; ?>
                                    </span>
                                    <span class="badge rounded-pill <?= $isPegawai ? 'bg-success' : 'bg-primary'; ?> ms-auto" style="font-size:0.65rem;">
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
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

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
    document.getElementById('btn-sesuaikan-periode').addEventListener('click', function() {
        let awal = document.getElementById('periode_awal').value;
        let akhir = document.getElementById('periode_akhir').value;
        let nik = "<?= $pegawai_detail->nik ?? '' ?>"; // pastikan variabel ini ada

        if (!awal || !akhir) {
            Swal.fire({
                icon: 'warning',
                title: 'Oops...',
                text: 'Silakan pilih periode awal dan akhir terlebih dahulu'
            });
            return;
        }

        window.location.href = "<?= base_url('Administrator/cariDataPegawai') ?>?nik=" + nik + "&awal=" + awal + "&akhir=" + akhir;
    });
</script>

<script>
    document.getElementById('btn-sesuaikan-periode').addEventListener('click', function() {
        let periode = document.getElementById('periode_select').value.split('|');
        let awal = periode[0];
        let akhir = periode[1];
        let nik = "<?= $pegawai_detail->nik ?? '' ?>";

        window.location.href = "<?= base_url('Administrator/cariDataPegawai') ?>?nik=" + nik + "&awal=" + awal + "&akhir=" + akhir;
    });
</script>

<script>
    // Auto scroll ke bawah saat halaman dibuka
    document.addEventListener("DOMContentLoaded", function() {
        var chatBox = document.getElementById("chat-box");
        if (chatBox) {
            chatBox.scrollTop = chatBox.scrollHeight;
        }

        // Ambil nilai dari PHP
        let nilaiAkhir = <?= isset($nilai['nilai_akhir']) ? (is_numeric($nilai['nilai_akhir']) ? $nilai['nilai_akhir'] : '"' . $nilai['nilai_akhir'] . '"') : 0 ?>;
        let pencapaian = <?= isset($nilai['pencapaian']) ? (is_numeric($nilai['pencapaian']) ? $nilai['pencapaian'] : '"' . $nilai['pencapaian'] . '"') : 0 ?>;

        function HitungNilaiAkhir() {
            // Predikat
            let predikat;
            let predikatClass = "";

            if (nilaiAkhir === "Tidak ada nilai") {
                predikat = "Tidak ada yudisium/predikat";
                predikatClass = "text-dark";
            } else if (nilaiAkhir === 0) {
                predikat = "Belum Ada Nilai";
                predikatClass = "text-dark";
            } else if (nilaiAkhir < 2) {
                predikat = "Minus";
                predikatClass = "text-danger"; // merah
            } else if (nilaiAkhir < 3) {
                predikat = "Fair";
                predikatClass = "text-warning"; // jingga
            } else if (nilaiAkhir < 3.5) {
                predikat = "Good";
                predikatClass = "text-primary"; // biru
            } else if (nilaiAkhir < 4.5) {
                predikat = "Very Good";
                predikatClass = "text-success"; // hijau muda
            } else {
                predikat = "Excellent";
                predikatClass = "text-success font-weight-bold"; // hijau tua (lebih tebal)
            }

            document.getElementById("nilai-akhir").textContent =
                nilaiAkhir === "Tidak ada nilai" ? nilaiAkhir : nilaiAkhir.toFixed(2);
            document.getElementById("predikat").textContent = predikat;
            document.getElementById("predikat").className = predikatClass;
            document.getElementById("pencapaian-akhir").textContent =
                pencapaian === "" ? "" : pencapaian.toFixed(2) + "%";
        }
        HitungNilaiAkhir();
    });
</script>