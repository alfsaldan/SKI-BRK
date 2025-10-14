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
                                <li class="breadcrumb-item active">Penilaian Kinerja</li>
                            </ol>
                        </div>
                        <h4 class="page-title"><i class="mdi mdi-account-edit mr-2 text-primary"></i> Penilaian Kinerja Pegawai</h4>
                    </div>
                </div>
            </div>

            <div class="row d-flex align-items-stretch">
                <!-- Card Pilih Periode Penilaian -->
                <div class="col-12 col-md-6 mb-3 d-flex">
                    <div class="card w-100">
                        <div class="card-body">
                            <h5 class="text-primary font-weight-bold mb-3">
                                <i class="mdi mdi-calendar-range mr-2"></i> Pilih Periode Penilaian
                            </h5>

                            <div class="form-group">
                                <label class="text-dark font-weight-medium">Pilih Periode Penilaian:</label>
                                <select id="periode_select" class="form-control mb-2">
                                    <option value="">-- Pilih Periode --</option>
                                    <?php if (!empty($periode_list)): ?>
                                        <?php foreach ($periode_list as $p):
                                            $val = $p->periode_awal . "|" . $p->periode_akhir;
                                            $text = date('d M Y', strtotime($p->periode_awal)) . " s/d " . date('d M Y', strtotime($p->periode_akhir));
                                            $selected = ($periode_awal == $p->periode_awal && $periode_akhir == $p->periode_akhir) ? 'selected' : '';
                                        ?>
                                            <option value="<?= $val ?>" <?= $selected ?>><?= $text ?></option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                    <option value="baru">+ Tambah Periode Baru</option>
                                </select>
                            </div>

                            <div id="periode_manual" style="display: none;">
                                <div class="form-inline mb-2">
                                    <label class="mr-2 text-dark font-weight-medium">Periode Penilaian Baru:</label>
                                    <input type="date" id="periode_awal" class="form-control mr-2"
                                        value="<?= $periode_awal ?? date('Y-01-01'); ?>">
                                    <span class="mr-2">s/d</span>
                                    <input type="date" id="periode_akhir" class="form-control mr-2"
                                        value="<?= $periode_akhir ?? date('Y-12-31'); ?>">
                                    <button type="button" id="btn-sesuaikan-periode" class="btn btn-primary ml-2">
                                        Terapkan
                                    </button>
                                </div>
                            </div>

                            <!-- 🔒 LOCK INPUT GLOBAL -->
                            <div class="d-flex align-items-center mb-3">
                                <input type="hidden" id="hidden_periode_awal" value="<?= $periode_awal ?>">
                                <input type="hidden" id="hidden_periode_akhir" value="<?= $periode_akhir ?>">

                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="lock_input_checkbox">
                                    <label class="form-check-label font-weight-medium ms-2" for="lock_input_checkbox">
                                        🔐 Kunci Input Penilaian
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Card Masukkan NIK Pegawai -->
                <div class="col-12 col-md-6 mb-3 d-flex">
                    <div class="card w-100">
                        <div class="card-body">
                            <h5>Masukkan NIK Pegawai</h5>
                            <form action="<?= base_url('Administrator/cariPenilaian'); ?>" method="post">
                                <input type="text" name="nik" class="form-control" placeholder="Masukkan NIK Pegawai" required>
                                <button type="submit" class="btn btn-success mt-2">Nilai</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>


            <?php if (isset($pegawai_detail) && $pegawai_detail) { ?>
                <div class="row">
                    <div class="col-12">
                        <div class="card shadow-sm border-0 mt-3">
                            <div class="card-body">

                                <!-- Detail Pegawai & Informasi Penilaian -->
                                <div class="row mb-3">
                                    <!-- Detail Pegawai -->
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

                                    <!-- Informasi Penilaian -->
                                    <div class="col-md-6 mb-3">
                                        <h5 class="text-success font-weight-bold mb-3">
                                            <i class="mdi mdi-file-document-outline mr-2"></i>Informasi Penilaian
                                        </h5>

                                        <div class="form-group">
                                            <label class="text-dark font-weight-medium">Periode Penilaian:</label>
                                            <!-- Text yang berubah -->
                                            <p id="info_periode" class="text-dark font-weight-medium">
                                                <?= date('d M Y', strtotime($periode_awal)) . " s/d " . date('d M Y', strtotime($periode_akhir)); ?>
                                            </p>
                                        </div>


                                        <p class="text-dark font-weight-medium"><b>Unit Kantor Penilai:</b> <span class="text-dark"><?= $pegawai_detail->unit_kerja; ?> <?= $pegawai_detail->unit_kantor ?? '-'; ?></span></p>
                                    </div>
                                </div>
                                <hr>

                                <!-- Penilai I & Penilai II -->
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <h5 class="text-info font-weight-bold mb-3">
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
                                        <h5 class="text-warning font-weight-bold mb-3">
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
                            </div>
                        </div>
                    </div>
                </div>

                <?php
                $order = ['Keuangan (F)', 'Pelanggan (C)', 'Proses Internal (IP)', 'Pembelajaran & Pertumbuhan (LG)'];
                $grouped = [];
                if (!empty($indikator_by_jabatan)) {
                    foreach ($indikator_by_jabatan as $row) {
                        $p = $row->perspektif ?? '';
                        $s = $row->sasaran_kerja ?? '';
                        $grouped[$p][$s][] = $row;
                    }
                }

                function count_rows($arr)
                {
                    $sum = 0;
                    foreach ($arr as $items)
                        $sum += count($items);
                    return $sum;
                }
                ?>

                <!-- Tabel Penilaian -->
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="text-success font-weight-bold mb-3">
                                    <i class="mdi mdi-star-circle mr-2"></i> Form Penilaian Sasaran Kerja
                                </h5>
                                <div class="table-responsive">
                                    <table class="table table-bordered" id="tabel-penilaian">
                                        <thead style="background-color:#2E7D32;color:#fff;text-align:center;">
                                            <tr>
                                                <th>Perspektif</th>
                                                <th>Sasaran Kerja</th>
                                                <th class="text-center" style="width: 80px;">Bobot (%)</th>
                                                <th>Indikator</th>
                                                <th class="text-center" style="width: 120px;">Target</th>
                                                <th class="text-center" style="width: 80px;">Batas Waktu</th>
                                                <th class="text-center" style="width: 120px;">Realisasi</th>
                                                <th class="text-center" style="width: 120px;">Pencapaian (%)</th>
                                                <th class="text-center" style="width: 120px;">Nilai</th>
                                                <th class="text-center" style="width: 120px;">Nilai Dibobot</th>
                                                <th class="text-center" style="width: 100px;">Status</th>
                                                <th class="text-center" style="width: 100px;">Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $printed_any = false;
                                            foreach ($order as $persp) {
                                                if (empty($grouped[$persp]))
                                                    continue;
                                                $printed_any = true;
                                                $persp_rows = count_rows($grouped[$persp]);
                                                $first_persp_cell = true;
                                                $subtotal_bobot_perspektif = 0;

                                                foreach ($grouped[$persp] as $sasaran => $items) {
                                                    $sasaran_rows = count($items);
                                                    $first_sas_cell = true;

                                                    foreach ($items as $i) {
                                                        $id = $i->id;
                                                        $bobot = $i->bobot ?? 0;
                                                        $indik = $i->indikator ?? '';
                                                        $subtotal_bobot_perspektif += $bobot;

                                                        $status = strtolower(trim($i->status ?? ''));

                                                        $statusClass = 'badge badge-danger';
                                                        $statusText  = 'Belum Dinilai';

                                                        switch ($status) {
                                                            case 'ada catatan':
                                                                $statusClass = 'badge badge-warning';
                                                                $statusText  = 'Ada Catatan';
                                                                break;
                                                            case 'disetujui':
                                                                $statusClass = 'badge badge-success';
                                                                $statusText  = 'Disetujui';
                                                                break;
                                                        }
                                            ?>
                                                        <tr data-id="<?= $id; ?>" data-bobot="<?= $bobot; ?>"
                                                            data-perspektif="<?= $persp; ?>" data-indikator="<?= htmlspecialchars($indik, ENT_QUOTES, 'UTF-8'); ?>">
                                                            <?php if ($first_persp_cell) { ?>
                                                                <td rowspan="<?= $persp_rows; ?>"
                                                                    style="vertical-align:middle;font-weight:600;background:#C8E6C9;">
                                                                    <?= $persp; ?></td>
                                                            <?php $first_persp_cell = false;
                                                            } ?>

                                                            <?php if ($first_sas_cell) { ?>
                                                                <td rowspan="<?= $sasaran_rows; ?>"
                                                                    style="vertical-align:middle;background:#E3F2FD;"><?= $sasaran; ?></td>
                                                            <?php $first_sas_cell = false;
                                                            } ?>

                                                            <td class="text-center align-middle"><?= $bobot; ?>
                                                                <input type="hidden" class="bobot" value="<?= $bobot ?>">
                                                            </td>
                                                            <td><?= $indik; ?></td>

                                                            <!-- Target -->
                                                            <td class="text-center align-middle">
                                                                <input type="text"
                                                                    class="form-control target-input text-center"
                                                                    style="min-width:120px;"
                                                                    value="<?= $i->target ?? ''; ?>">
                                                            </td>

                                                            <td class="text-center align-middle">
                                                                <input type="date" class="form-control batas-waktu" style="min-width:120px;"
                                                                    value="<?= $i->batas_waktu ?? ''; ?>">
                                                            </td>

                                                            <td class="text-center align-middle">
                                                                <input type="text"
                                                                    class="form-control realisasi-input text-center"
                                                                    style="min-width:120px;"
                                                                    value="<?= $i->realisasi ?? ''; ?>">
                                                            </td>

                                                            <td class="text-center align-middle"><input type="text" style="min-width:60px;"
                                                                    class="form-control form-control-sm text-center pencapaian-output" readonly>
                                                            </td>
                                                            <td class="text-center align-middle"><input type="text" style="min-width:50px;"
                                                                    class="form-control form-control-sm text-center nilai-output" readonly></td>
                                                            <td class="text-center align-middle"><input type="text"
                                                                    class="form-control form-control-sm text-center nilai-bobot-output" readonly>
                                                            </td>

                                                            <td class="text-center align-middle">
                                                                <span class="<?= $statusClass; ?>"><?= $statusText; ?></span>
                                                            </td>
                                                            <td class="text-center align-middle">
                                                                <button type="button"
                                                                    class="btn btn-sm btn-primary simpan-penilaian">Simpan</button>
                                                            </td>
                                                        </tr>
                                                <?php
                                                    }
                                                }
                                                ?>
                                                <tr class="subtotal-row" data-perspektif="<?= $persp; ?>"
                                                    style="font-weight:bold;background:#F1F8E9;">
                                                    <td colspan="2">Sub Total Bobot <?= $persp; ?></td>
                                                    <td class="text-center"><span
                                                            class="subtotal-bobot"><?= $subtotal_bobot_perspektif; ?></span>
                                                    </td>
                                                    <td colspan="6" class="text-center">Sub Total Nilai <?= $persp; ?> Dibobot
                                                    </td>
                                                    <td class="text-center"><span class="subtotal-nilai-bobot">0.00</span></td>
                                                    <td colspan="2"></td>
                                                </tr>
                                            <?php
                                            }
                                            if (!$printed_any) { ?>
                                                <tr>
                                                    <td colspan="12" class="text-center">Tidak ada indikator untuk jabatan ini
                                                    </td>
                                                </tr>
                                            <?php } ?>
                                        </tbody>
                                        <tfoot
                                            style="background-color:#2E7D32;color:#fff;font-weight:bold;text-align:center;">
                                            <tr>
                                                <td colspan="2">Total</td>
                                                <td><span id="total-bobot">0</span></td>
                                                <td colspan="6" class="text-center">Total Nilai Kinerja</td>
                                                <td><span id="total-nilai-bobot">0.00</span></td>
                                                <td colspan="2"></td>
                                            </tr>
                                        </tfoot>
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
                                <td class="text-center" id="total-sasaran">
                                    <?= $nilai_akhir['total_sasaran'] ?? 0 ?>
                                </td>
                                <td>x Bobot % Sasaran Kerja</td>
                                <td>
                                    <input type="text" id="bobot-sasaran"
                                        class="form-control form-control-sm text-center"
                                        value="95%" readonly>
                                </td>
                                <td class="text-center" id="nilai-sasaran">
                                    <?= $nilai_akhir['nilai_sasaran'] ?? 0 ?>
                                </td>
                            </tr>
                            <tr>
                                <th>Rata-rata Nilai Internalisasi Budaya</th>
                                <td class="text-center" id="rata-budaya">
                                    <?= number_format($rata_rata_budaya ?? 0, 2); ?>
                                </td>
                                <td>x Bobot % Budaya Perusahaan</td>
                                <td>
                                    <input type="text" id="bobot-budaya"
                                        class="form-control form-control-sm text-center"
                                        value="5%" readonly>
                                </td>
                                <td class="text-center" id="nilai-budaya">
                                    <?= $nilai_akhir['nilai_budaya'] ?? '-' ?>
                                </td>
                            </tr>
                            <tr>
                                <th colspan="4" class="text-right">Total Nilai</th>
                                <td class="text-center" id="total-nilai">
                                    <?= $nilai_akhir['total_nilai'] ?? 0 ?>
                                </td>
                            </tr>
                            <tr>
                                <th colspan="4" class="text-right">
                                    Fraud<br>
                                    <small>(diisi 1 jika melakukan fraud, 0 jika tidak)</small>
                                </th>
                                <td>
                                    <input type="number" min="0" max="1"
                                        class="form-control form-control-sm text-center"
                                        id="fraud-input"
                                        value="<?= $nilai_akhir['fraud'] ?? 0 ?>">
                                </td>
                            </tr>
                            <tr>
                                <th colspan="4" class="text-right">
                                    Koefisien Nilai<br>
                                    <small>(wajib diisi)</small>
                                </th>
                                <td>
                                    <div class="input-group input-group-sm">
                                        <input
                                            type="number"
                                            name="koefisien"
                                            id="koefisien-input"
                                            class="form-control text-center"
                                            max="100"
                                            min="70"
                                            step="5"
                                            value="<?= isset($nilai_akhir['koefisien']) ? htmlspecialchars($nilai_akhir['koefisien']) : 100 ?>"
                                            required>
                                        <div class="input-group-append">
                                            <span class="input-group-text">%</span>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        </table>

                        <!-- Bagian Bawah: Kiri-Kanan -->
                        <div class="row">
                            <div class="col-md-6">
                                <!-- Tabel Predikat -->
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
                                <!-- Nilai Akhir & Pencapaian -->
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="card text-center mb-3">
                                            <div class="card-header bg-success text-white">Nilai Akhir</div>
                                            <div class="card-body">
                                                <h3 id="nilai-akhir">
                                                    <?= $nilai_akhir['nilai_akhir'] ?? 0 ?>
                                                </h3>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="card text-center">
                                            <div class="card-header bg-success text-white">Pencapaian Akhir</div>
                                            <div class="card-body">
                                                <h3 id="pencapaian-akhir">
                                                    <?= $nilai_akhir['pencapaian'] ?? '-' ?>
                                                </h3>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Predikat -->
                                <div class="card text-center mb-3">
                                    <div class="card-header bg-success text-white">Yudisium / Predikat</div>
                                    <div class="card-body">
                                        <h3 id="predikat">
                                            <?= $nilai_akhir['predikat'] ?? '-' ?>
                                        </h3>
                                    </div>
                                </div>

                                <div class="text-right mt-3">
                                    <button id="btn-simpan-nilai-akhir" class="btn btn-primary">
                                        <i class="mdi mdi-content-save"></i> Simpan Nilai Akhir
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Catatan Penilai & Pegawai -->
                <div class="row mt-3">
                    <!-- Catatan Penilai -->
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-body">
                                <h5>Catatan Penilai</h5>
                                <div class="table-responsive">
                                    <table id="tabel-catatan" class="table table-bordered table-striped">
                                        <thead>
                                            <tr>
                                                <th>No</th>
                                                <th>Penilai</th>
                                                <th>Catatan</th>
                                                <th>Tanggal</th>
                                            </tr>
                                        </thead>
                                        <tbody></tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Catatan Pegawai -->
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-body">
                                <h5>Catatan Pegawai</h5>
                                <div class="table-responsive">
                                    <table id="tabel-catatan-pegawai" class="table table-bordered table-striped">
                                        <thead>
                                            <tr>
                                                <th>No</th>
                                                <th>Catatan</th>
                                                <th>Tanggal</th>
                                            </tr>
                                        </thead>
                                        <tbody></tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php } ?>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<?php if (isset($message) && !empty($message)): ?>
    <script>
        Swal.fire({
            icon: '<?= $message['type']; ?>',
            title: 'Informasi',
            text: '<?= $message['text']; ?>',
            confirmButtonColor: '#2E7D32'
        });
    </script>
<?php endif; ?>


<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // ---------------------------
        // Elemen (safe getters)
        // ---------------------------
        const nikInput = document.getElementById('nik');
        const periodeAwalInput = document.getElementById('periode_awal');
        const periodeAkhirInput = document.getElementById('periode_akhir');
        const periodeSelect = document.getElementById('periode_select');
        const periodeManual = document.getElementById('periode_manual');
        const infoPeriode = document.getElementById('info_periode');
        const lockCheckbox = document.getElementById('lock_input_checkbox');
        const hiddenAwal = document.getElementById('hidden_periode_awal');
        const hiddenAkhir = document.getElementById('hidden_periode_akhir');
        const btnSimpanNilaiAkhir = document.getElementById('btn-simpan-nilai-akhir');
        const btnSesuaikanPeriode = document.getElementById('btn-sesuaikan-periode');

        const koefInput = document.getElementById('koefisien-input');
        const fraudInput = document.getElementById('fraud-input');
        const nilaiAkhirEl = document.getElementById("nilai-akhir");

        const getNik = () => nikInput ? nikInput.value : '';

        // ---------------------------
        // Default periode jika kosong
        // ---------------------------
        if (periodeAwalInput && !periodeAwalInput.value) periodeAwalInput.value = "2025-01-01";
        if (periodeAkhirInput && !periodeAkhirInput.value) periodeAkhirInput.value = "2025-12-31";

        // ---------------------------
        // Util / perhitungan
        // ---------------------------
        function formatAngka(nilai) {
            let num = parseFloat(nilai);
            if (isNaN(num)) return '';
            return Number.isInteger(num) ? num.toString() : num.toFixed(2);
        }

        function hitungPencapaianOtomatis(target, realisasi, indikatorText = "") {
            indikatorText = (indikatorText || "").toLowerCase();
            const keywords = {
                rumus1: ["biaya", "beban", "efisiensi", "npf pembiayaan", "npf nominal"],
                rumus3: ["outstanding", "pertumbuhan"]
            };
            const containsKeyword = (list, text) => list.some(k => new RegExp(`\\b${k}\\b`, "i").test(text));
            let pencapaian = 0;
            if (target <= 999) {
                pencapaian = (realisasi / target) * 100;
            } else {
                if (containsKeyword(keywords.rumus1, indikatorText)) {
                    pencapaian = ((target + (target - realisasi)) / target) * 100;
                } else if (containsKeyword(keywords.rumus3, indikatorText)) {
                    pencapaian = ((realisasi - target) / Math.abs(target) + 1) * 100;
                } else {
                    pencapaian = (realisasi / target) * 100;
                }
            }
            return Math.min(pencapaian, 130);
        }

        function hitungNilai(pencapaian) {
            let nilai = 0;
            if (pencapaian < 0) nilai = 0;
            else if (pencapaian < 80) nilai = (pencapaian / 80) * 2;
            else if (pencapaian < 90) nilai = 2 + ((pencapaian - 80) / 10);
            else if (pencapaian < 110) nilai = 3 + ((pencapaian - 90) / 20 * 0.5);
            else if (pencapaian < 120) nilai = 3.5 + ((pencapaian - 110) / 10 * 1);
            else if (pencapaian < 130) nilai = 4.5 + ((pencapaian - 120) / 10 * 0.5);
            else nilai = 5;
            return nilai;
        }

        function hitungRow(row, totalBobot) {
            try {
                const targetEl = row.querySelector('.target-input');
                const realisasiEl = row.querySelector('.realisasi-input');
                const bobotEl = row.querySelector('.bobot');
                const pencapaianEl = row.querySelector('.pencapaian-output');
                const nilaiEl = row.querySelector('.nilai-output');
                const nilaiBobotEl = row.querySelector('.nilai-bobot-output');

                const targetVal = targetEl ? targetEl.value : "";
                const realisasiVal = realisasiEl ? realisasiEl.value : "";
                const bobot = parseFloat(bobotEl ? bobotEl.value : 0) || 0;
                const indikatorText = row.dataset.indikator || "";

                let pencapaian = "",
                    nilai = "",
                    nilaiBobot = "";

                if (targetVal !== "" && realisasiVal !== "") {
                    const target = parseFloat(targetVal) || 0;
                    const realisasi = parseFloat(realisasiVal) || 0;
                    pencapaian = hitungPencapaianOtomatis(target, realisasi, indikatorText);
                    nilai = hitungNilai(pencapaian);
                    if (totalBobot > 0) nilaiBobot = (nilai * bobot) / totalBobot;
                }

                if (pencapaianEl) pencapaianEl.value = pencapaian === "" ? "" : formatAngka(pencapaian);
                if (nilaiEl) nilaiEl.value = nilai === "" ? "" : formatAngka(nilai);
                if (nilaiBobotEl) nilaiBobotEl.value = nilaiBobot === "" ? "" : formatAngka(nilaiBobot);

                return {
                    bobot,
                    nilaiBobot: nilaiBobot === "" ? 0 : parseFloat(formatAngka(nilaiBobot)),
                    perspektif: row.dataset.perspektif
                };
            } catch (err) {
                console.error('hitungRow error', err);
                return {
                    bobot: 0,
                    nilaiBobot: 0,
                    perspektif: ''
                };
            }
        }

        function hitungTotal() {
            try {
                let totalBobot = 0,
                    totalNilai = 0;
                const subtotalMap = {};

                const rows = document.querySelectorAll('#tabel-penilaian tbody tr[data-id]');
                rows.forEach(row => {
                    const bobotEl = row.querySelector('.bobot');
                    totalBobot += parseFloat(bobotEl ? bobotEl.value : 0) || 0;
                });

                rows.forEach(row => {
                    const {
                        bobot,
                        nilaiBobot,
                        perspektif
                    } = hitungRow(row, totalBobot);
                    totalNilai += nilaiBobot;
                    if (!subtotalMap[perspektif]) subtotalMap[perspektif] = 0;
                    subtotalMap[perspektif] += nilaiBobot;
                });

                const totalBobotEl = document.getElementById('total-bobot');
                const totalNilaiBobotEl = document.getElementById('total-nilai-bobot');
                const totalSasaranEl = document.getElementById('total-sasaran');

                if (totalBobotEl) totalBobotEl.innerText = formatAngka(totalBobot);
                if (totalNilaiBobotEl) totalNilaiBobotEl.innerText = formatAngka(totalNilai);
                if (totalSasaranEl) totalSasaranEl.textContent = formatAngka(totalNilai);

                document.querySelectorAll('.subtotal-row').forEach(row => {
                    const p = row.dataset.perspektif;
                    const subEl = row.querySelector('.subtotal-nilai-bobot');
                    if (subEl) subEl.innerText = formatAngka(subtotalMap[p] || 0);
                });

                hitungNilaiAkhir();
                updatePredikatDanPencapaian();
            } catch (err) {
                console.error('hitungTotal error', err);
            }
        }

        function hitungNilaiAkhir() {
            try {
                const bobotSasaran = 0.95;
                const bobotBudaya = 0.05;
                const fraudEl = document.getElementById("fraud-input");
                const totalNilaiBobotEl = document.getElementById("total-nilai-bobot");
                const rataBudayaEl = document.getElementById("rata-budaya");

                const fraud = fraudEl ? (parseFloat(fraudEl.value) || 0) : 0;
                const totalSasaran = totalNilaiBobotEl ? (parseFloat(totalNilaiBobotEl.textContent) || 0) : 0;
                const rataBudaya = rataBudayaEl ? (parseFloat(rataBudayaEl.textContent) || 0) : 0;

                const nilaiSasaran = totalSasaran * bobotSasaran;
                const nilaiBudaya = rataBudaya * bobotBudaya;
                const totalNilai = nilaiSasaran + nilaiBudaya;
                const nilaiAkhir = (fraud === 1) ? totalNilai - fraud : totalNilai;

                let predikat = '',
                    predikatClass = '';
                if (nilaiAkhir === 0) {
                    predikat = "Belum Ada Nilai";
                    predikatClass = "text-dark";
                } else if (nilaiAkhir < 2) {
                    predikat = "Minus";
                    predikatClass = "text-danger";
                } else if (nilaiAkhir < 3) {
                    predikat = "Fair";
                    predikatClass = "text-warning";
                } else if (nilaiAkhir < 3.5) {
                    predikat = "Good";
                    predikatClass = "text-primary";
                } else if (nilaiAkhir < 4.5) {
                    predikat = "Very Good";
                    predikatClass = "text-success";
                } else {
                    predikat = "Excellent";
                    predikatClass = "text-success font-weight-bold";
                }

                const elNilaiSasaran = document.getElementById("nilai-sasaran");
                const elNilaiBudaya = document.getElementById("nilai-budaya");
                const elTotalNilai = document.getElementById("total-nilai");
                const elNilaiAkhir = document.getElementById("nilai-akhir");
                const elPredikat = document.getElementById("predikat");
                const elPencapaianAkhir = document.getElementById("pencapaian-akhir");

                if (elNilaiSasaran) elNilaiSasaran.textContent = (isNaN(nilaiSasaran) ? 0 : nilaiSasaran).toFixed(2);
                if (elNilaiBudaya) elNilaiBudaya.textContent = (isNaN(nilaiBudaya) ? 0 : nilaiBudaya).toFixed(2);
                if (elTotalNilai) elTotalNilai.textContent = (isNaN(totalNilai) ? 0 : totalNilai).toFixed(2);
                if (elNilaiAkhir) elNilaiAkhir.textContent = (isNaN(nilaiAkhir) ? 0 : nilaiAkhir).toFixed(2);
                if (elPredikat) {
                    elPredikat.textContent = predikat;
                    elPredikat.className = predikatClass;
                }

                // Hitung pencapaian akhir (persentase) — agar kompatibel dengan kode lama
                if (elPencapaianAkhir) {
                    let pencapaian = 0;
                    const v = parseFloat(nilaiAkhir) || 0;
                    if (v < 0) pencapaian = 0;
                    else if (v < 2) pencapaian = (v / 2) * 0.8 * 100;
                    else if (v < 3) pencapaian = 80 + ((v - 2) / 1) * 10;
                    else if (v < 3.5) pencapaian = 90 + ((v - 3) / 0.5) * 20;
                    else if (v < 4.5) pencapaian = 110 + ((v - 3.5) / 1) * 10;
                    else if (v < 5) pencapaian = 120 + ((v - 4.5) / 0.5) * 10;
                    else pencapaian = 130;
                    elPencapaianAkhir.textContent = isNaN(pencapaian) ? '' : pencapaian.toFixed(2) + '%';
                }
            } catch (err) {
                console.error('hitungNilaiAkhir error', err);
            }
        }

        if (koefInput) {
            koefInput.addEventListener('blur', function() {
                let val = parseFloat(this.value);
                if (isNaN(val)) val = 100; // jika kosong, default 100
                if (val < 70) val = 70;
                if (val > 100) val = 100;
                this.value = val; // set ulang nilainya
            });
        }

        function updatePredikatDanPencapaian() {
            const koef = koefInput ? (parseFloat(koefInput.value) || 100) / 100 : 1;
            const nilaiAkhir = nilaiAkhirEl ? (parseFloat(nilaiAkhirEl.textContent) || 0) : 0;

            // Predikat
            let predikat = '';
            if (nilaiAkhir === 0) predikat = "Belum Ada Nilai";
            else if (nilaiAkhir < 2 * koef) predikat = "Minus";
            else if (nilaiAkhir < 3 * koef) predikat = "Fair";
            else if (nilaiAkhir < 3.5 * koef) predikat = "Good";
            else if (nilaiAkhir < 4.5 * koef) predikat = "Very Good";
            else predikat = "Excellent";

            const elPredikat = document.getElementById("predikat");
            if (elPredikat) {
                elPredikat.textContent = predikat;
                elPredikat.className = (predikat === "Excellent") ? "text-success font-weight-bold" :
                    (predikat === "Very Good") ? "text-success" :
                    (predikat === "Good") ? "text-primary" :
                    (predikat === "Fair") ? "text-warning" :
                    (predikat === "Minus") ? "text-danger" : "text-dark";
            }

            // Pencapaian akhir
            let pencapaian = 0;
            if (nilaiAkhir < 0) pencapaian = 0;
            else if (nilaiAkhir < 2 * koef) pencapaian = (nilaiAkhir / 2) * 80;
            else if (nilaiAkhir < 3 * koef) pencapaian = 80 + ((nilaiAkhir - 2) / 1) * 10;
            else if (nilaiAkhir < 3.5 * koef) pencapaian = 90 + ((nilaiAkhir - 3) / 0.5) * 20;
            else if (nilaiAkhir < 4.5 * koef) pencapaian = 110 + ((nilaiAkhir - 3.5) / 1) * 10;
            else if (nilaiAkhir < 5 * koef) pencapaian = 120 + ((nilaiAkhir - 4.5) / 0.5) * 10;
            else pencapaian = 130;

            const elPencapaian = document.getElementById("pencapaian-akhir");
            if (elPencapaian) elPencapaian.textContent = pencapaian.toFixed(2) + "%";
        }

        // Event listener untuk koefisien
        if (koefInput) koefInput.addEventListener('input', updatePredikatDanPencapaian);

        // Event listener untuk fraud jika mau predikat ikut berubah
        if (fraudInput) fraudInput.addEventListener('input', updatePredikatDanPencapaian);

        // Hitung pertama kali saat halaman load
        updatePredikatDanPencapaian();

        // ---------------------------
        // Delegasi input (aman untuk elemen dinamis)
        // ---------------------------
        document.addEventListener('input', function(e) {
            if (e.target && (e.target.matches('.target-input') || e.target.matches('.realisasi-input') || e.target.id === 'fraud-input')) {
                hitungTotal();
            }
        });

        // ---------------------------
        // Delegasi klik untuk: simpan per baris, sesuaikan periode (button), dan simpan nilai akhir
        // ---------------------------
        document.addEventListener('click', function(e) {
            // 1) Simpan per baris (.simpan-penilaian)
            const simpanBtn = e.target.closest ? e.target.closest('.simpan-penilaian') : null;
            if (simpanBtn) {
                e.preventDefault();
                const row = simpanBtn.closest('tr[data-id]') || simpanBtn.closest('tr');
                if (!row) {
                    console.warn('Row not found for save');
                    return;
                }
                const indikator_id = row.dataset.id || row.getAttribute('data-id') || '';
                const target = (row.querySelector('.target-input') || {}).value || '';
                const batas_waktu = (row.querySelector('input[type="date"]') || {}).value || '';
                const realisasi = (row.querySelector('.realisasi-input') || {}).value || '';
                const pencapaian = (row.querySelector('.pencapaian-output') || {}).value || '';
                const nilai = (row.querySelector('.nilai-output') || {}).value || '';
                const nilai_dibobot = (row.querySelector('.nilai-bobot-output') || {}).value || '';
                const periode_awal = periodeAwalInput ? periodeAwalInput.value : '';
                const periode_akhir = periodeAkhirInput ? periodeAkhirInput.value : '';
                const nik = getNik();

                // Disable tombol sementara & simpan teks asli
                if (!simpanBtn.dataset.origHtml) simpanBtn.dataset.origHtml = simpanBtn.innerHTML;
                simpanBtn.disabled = true;
                simpanBtn.innerHTML = '<i class="mdi mdi-spin mdi-loading"></i> Menyimpan';

                const body = `nik=${encodeURIComponent(nik)}&indikator_id=${encodeURIComponent(indikator_id)}&target=${encodeURIComponent(target)}&batas_waktu=${encodeURIComponent(batas_waktu)}&realisasi=${encodeURIComponent(realisasi)}&pencapaian=${encodeURIComponent(pencapaian)}&nilai=${encodeURIComponent(nilai)}&nilai_dibobot=${encodeURIComponent(nilai_dibobot)}&periode_awal=${encodeURIComponent(periode_awal)}&periode_akhir=${encodeURIComponent(periode_akhir)}`;

                fetch('<?= base_url("Administrator/simpanPenilaianBaris") ?>', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded'
                        },
                        body
                    }).then(r => r.json())
                    .then(res => {
                        if (res && res.status === 'success') {
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil',
                                text: res.message || 'Tersimpan',
                                timer: 1500,
                                showConfirmButton: false
                            });
                            hitungTotal();
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal',
                                text: (res && res.message) || 'Gagal menyimpan'
                            });
                        }
                    })
                    .catch(err => {
                        console.error('save row error', err);
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Terjadi kesalahan server'
                        });
                    })
                    .finally(() => {
                        // restore tombol
                        simpanBtn.disabled = false;
                        if (simpanBtn.dataset.origHtml) {
                            simpanBtn.innerHTML = simpanBtn.dataset.origHtml;
                            delete simpanBtn.dataset.origHtml;
                        }
                    });

                return; // stop propagation for this click
            }

            // 2) Tombol sesuaikan periode (bisa berupa button/link)
            const sesBtn = e.target.closest ? e.target.closest('#btn-sesuaikan-periode') : null;
            if (sesBtn) {
                e.preventDefault();
                const nik = getNik();
                if (!nik) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'NIK kosong',
                        text: 'Masukkan NIK terlebih dahulu',
                        confirmButtonColor: '#d33'
                    });
                    return;
                }
                const awal = periodeAwalInput ? periodeAwalInput.value : '';
                const akhir = periodeAkhirInput ? periodeAkhirInput.value : '';
                window.location.href = `<?= base_url("Administrator/cariPenilaian") ?>?nik=${encodeURIComponent(nik)}&awal=${encodeURIComponent(awal)}&akhir=${encodeURIComponent(akhir)}`;
                return;
            }
        });

        // ---------------------------
        // Simpan nilai akhir (tombol khusus)
        // ---------------------------
        if (btnSimpanNilaiAkhir) {
            btnSimpanNilaiAkhir.addEventListener('click', function() {
                const nik = getNik();
                const periode_awal = periodeAwalInput ? periodeAwalInput.value : '';
                const periode_akhir = periodeAkhirInput ? periodeAkhirInput.value : '';

                const rows = document.querySelectorAll('#tabel-penilaian tbody tr[data-id]');
                const promises = [];

                rows.forEach(row => {
                    const indikator_id = row.dataset.id || '';
                    const target = (row.querySelector('.target-input') || {}).value || '';
                    const batas_waktu = (row.querySelector('input[type="date"]') || {}).value || '';
                    const realisasi = (row.querySelector('.realisasi-input') || {}).value || '';
                    const pencapaian = (row.querySelector('.pencapaian-output') || {}).value || '';
                    const nilai = (row.querySelector('.nilai-output') || {}).value || '';
                    const nilai_dibobot = (row.querySelector('.nilai-bobot-output') || {}).value || '';

                    // 🛑 Skip jika target atau realisasi masih kosong/null
                    if (target.trim() === '' || realisasi.trim() === '') {
                        console.warn(`Lewati indikator ${indikator_id} karena target/realisasi kosong`);
                        return;
                    }

                    const formData = `nik=${encodeURIComponent(nik)}&indikator_id=${encodeURIComponent(indikator_id)}&target=${encodeURIComponent(target)}&batas_waktu=${encodeURIComponent(batas_waktu)}&realisasi=${encodeURIComponent(realisasi)}&pencapaian=${encodeURIComponent(pencapaian)}&nilai=${encodeURIComponent(nilai)}&nilai_dibobot=${encodeURIComponent(nilai_dibobot)}&periode_awal=${encodeURIComponent(periode_awal)}&periode_akhir=${encodeURIComponent(periode_akhir)}`;

                    promises.push(fetch('<?= base_url("Administrator/simpanPenilaianBaris") ?>', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded'
                        },
                        body: formData
                    }).then(r => r.json()).catch(err => ({
                        status: 'error',
                        err
                    })));
                });

                Promise.all(promises).then(results => {
                        if (results.some(r => r.status !== 'success')) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal',
                                text: 'Ada baris yang gagal disimpan. Cek kembali inputnya.',
                                confirmButtonColor: '#d33'
                            });
                            return;
                        }

                        // 🔹 Ambil semua nilai akhir dari DOM
                        const nilai_sasaran = document.getElementById('total-sasaran')?.textContent || '';
                        const nilai_budaya = document.getElementById('rata-budaya')?.textContent || '';
                        const total_nilai = document.getElementById('total-nilai')?.textContent || '';
                        const fraud = document.getElementById('fraud-input')?.value || '';
                        const nilai_akhir = document.getElementById('nilai-akhir')?.textContent || '';
                        const predikat = document.getElementById('predikat')?.textContent || '';
                        const pencapaian = document.getElementById('pencapaian-akhir')?.textContent || '';
                        const koefisien = document.getElementById('koefisien-input')?.value || 100; // 🟢 Tambahan ini

                        // 🔹 Simpan nilai akhir + koefisien
                        fetch('<?= base_url("Administrator/simpanNilaiAkhir") ?>', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/x-www-form-urlencoded'
                                },
                                body: `nik=${encodeURIComponent(nik)}&periode_awal=${encodeURIComponent(periode_awal)}&periode_akhir=${encodeURIComponent(periode_akhir)}&nilai_sasaran=${encodeURIComponent(nilai_sasaran)}&nilai_budaya=${encodeURIComponent(nilai_budaya)}&total_nilai=${encodeURIComponent(total_nilai)}&fraud=${encodeURIComponent(fraud)}&nilai_akhir=${encodeURIComponent(nilai_akhir)}&pencapaian=${encodeURIComponent(pencapaian)}&predikat=${encodeURIComponent(predikat)}&koefisien=${encodeURIComponent(koefisien)}`
                            })
                            .then(r => r.json())
                            .then(res => {
                                if (res && res.status === 'success') {
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Berhasil',
                                        text: 'Semua nilai berhasil disimpan',
                                        timer: 2000,
                                        showConfirmButton: false
                                    });
                                } else {
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Gagal',
                                        text: (res && res.message) || 'Gagal menyimpan nilai akhir',
                                        confirmButtonColor: '#d33'
                                    });
                                }
                            })
                            .catch(err => {
                                console.error('simpanNilaiAkhir error', err);
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error',
                                    text: 'Terjadi kesalahan server'
                                });
                            });
                    })
                    .catch(err => {
                        console.error('Promise.all save rows error', err);
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Terjadi kesalahan saat menyimpan baris'
                        });
                    });
            });
        }
        // ---------------------------
        // Periode select -> tampilkan info atau manual
        // ---------------------------
        if (periodeSelect) {
            periodeSelect.addEventListener('change', function() {
                const value = this.value;
                if (value && value !== 'baru') {
                    const [awal, akhir] = value.split('|');
                    const opt = {
                        day: '2-digit',
                        month: 'short',
                        year: 'numeric'
                    };
                    const nik = getNik();
                    if (nik) {
                        window.location.href = `<?= base_url("Administrator/cariPenilaian") ?>?nik=${encodeURIComponent(nik)}&awal=${encodeURIComponent(awal)}&akhir=${encodeURIComponent(akhir)}`;
                    } else {
                        Swal.fire({
                            icon: 'warning',
                            title: 'NIK kosong',
                            text: 'Masukkan NIK terlebih dahulu!',
                        });
                    }

                    if (infoPeriode) infoPeriode.textContent = `${new Date(awal).toLocaleDateString('id-ID', opt)} s/d ${new Date(akhir).toLocaleDateString('id-ID', opt)}`;
                    if (periodeManual) periodeManual.style.display = 'none';
                } else {
                    if (periodeManual) periodeManual.style.display = 'block';
                }
            });
        }

        // ---------------------------
        // LOCK GLOBAL (cek & toggle)
        // ---------------------------
        function toggleInputLock(lock) {
            document.querySelectorAll('.target-input, .realisasi-input, .simpan-penilaian, #btn-simpan-nilai-akhir, .batas-waktu').forEach(el => {
                try {
                    el.disabled = !!lock;
                    if (lock) {
                        el.classList.add('locked-style');
                        if (!el.dataset.origHtml && (el.tagName === 'BUTTON' || el.type === 'button' || el.classList.contains('btn'))) {
                            el.dataset.origHtml = el.innerHTML;
                            el.innerHTML = `<i class="mdi mdi-lock-outline mr-1"></i> Terkunci`;
                        }
                    } else {
                        el.classList.remove('locked-style');
                        if (el.dataset.origHtml) {
                            el.innerHTML = el.dataset.origHtml;
                            delete el.dataset.origHtml;
                        }
                    }
                } catch (e) {
                    /* ignore single element errors */
                }
            });
        }

        // Ambil nilai untuk query lock: gunakan hidden jika ada, fallback ke periode input
        const lockAwalVal = hiddenAwal ? hiddenAwal.value : (periodeAwalInput ? periodeAwalInput.value : '');
        const lockAkhirVal = hiddenAkhir ? hiddenAkhir.value : (periodeAkhirInput ? periodeAkhirInput.value : '');

        if (lockCheckbox) {
            fetch(`<?= base_url("Administrator/getLockStatus") ?>?awal=${encodeURIComponent(lockAwalVal)}&akhir=${encodeURIComponent(lockAkhirVal)}`)
                .then(r => r.json())
                .then(data => {
                    const locked = data && (data.locked === true || data.locked === "1" || data.locked === 1);
                    lockCheckbox.checked = !!locked;
                    toggleInputLock(locked);
                })
                .catch(err => console.error('getLockStatus error', err));

            lockCheckbox.addEventListener('change', function() {
                const isLocked = this.checked ? 1 : 0;
                fetch('<?= base_url("Administrator/setLockStatus") ?>', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded'
                        },
                        body: `periode_awal=${encodeURIComponent(lockAwalVal)}&periode_akhir=${encodeURIComponent(lockAkhirVal)}&lock_input=${isLocked}`
                    }).then(r => r.json())
                    .then(data => {
                        if (data && data.status === 'success') {
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil',
                                text: isLocked ? 'Periode berhasil dikunci.' : 'Kunci periode telah dibuka.',
                                timer: 1500,
                                showConfirmButton: false
                            });
                            toggleInputLock(isLocked);
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal',
                                text: 'Tidak dapat mengubah status kunci.'
                            });
                            lockCheckbox.checked = !this.checked;
                        }
                    }).catch(err => {
                        console.error('setLockStatus error', err);
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Terjadi kesalahan koneksi ke server.'
                        });
                        lockCheckbox.checked = !this.checked;
                    });
            });
        }

        // ---------------------------
        // Trigger awal perhitungan
        // ---------------------------
        hitungTotal();

        // Fungsi autosave per baris indikator
        function autoSaveBaris(row) {
            const indikator_id = row.dataset.id || '';
            const target = (row.querySelector('.target-input') || {}).value || '';
            const batas_waktu = (row.querySelector('input[type="date"]') || {}).value || '';
            const realisasi = (row.querySelector('.realisasi-input') || {}).value || '';
            const pencapaian = (row.querySelector('.pencapaian-output') || {}).value || '';
            const nilai = (row.querySelector('.nilai-output') || {}).value || '';
            const nilai_dibobot = (row.querySelector('.nilai-bobot-output') || {}).value || '';
            const periode_awal = document.getElementById('periode_awal')?.value || '';
            const periode_akhir = document.getElementById('periode_akhir')?.value || '';
            const nik = document.getElementById('nik')?.value || '';

            // Jangan simpan jika target/realisasi kosong
            if (target.trim() === '' && realisasi.trim() === '') return;

            fetch('<?= base_url("Administrator/simpanPenilaianBaris") ?>', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `nik=${encodeURIComponent(nik)}&indikator_id=${encodeURIComponent(indikator_id)}&target=${encodeURIComponent(target)}&batas_waktu=${encodeURIComponent(batas_waktu)}&realisasi=${encodeURIComponent(realisasi)}&pencapaian=${encodeURIComponent(pencapaian)}&nilai=${encodeURIComponent(nilai)}&nilai_dibobot=${encodeURIComponent(nilai_dibobot)}&periode_awal=${encodeURIComponent(periode_awal)}&periode_akhir=${encodeURIComponent(periode_akhir)}`
            });
        }

        // Fungsi autosave nilai akhir
        function autoSaveNilaiAkhir() {
            const nik = document.getElementById('nik')?.value || '';
            const periode_awal = document.getElementById('periode_awal')?.value || '';
            const periode_akhir = document.getElementById('periode_akhir')?.value || '';
            const nilai_sasaran = document.getElementById('total-sasaran')?.textContent || '';
            const nilai_budaya = document.getElementById('rata-budaya')?.textContent || '';
            const total_nilai = document.getElementById('total-nilai')?.textContent || '';
            const fraud = document.getElementById('fraud-input')?.value || '';
            const nilai_akhir = document.getElementById('nilai-akhir')?.textContent || '';
            const predikat = document.getElementById('predikat')?.textContent || '';
            const pencapaian = document.getElementById('pencapaian-akhir')?.textContent || '';
            const koefisien = document.getElementById('koefisien-input')?.value || 100;

            fetch('<?= base_url("Administrator/simpanNilaiAkhir") ?>', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `nik=${encodeURIComponent(nik)}&periode_awal=${encodeURIComponent(periode_awal)}&periode_akhir=${encodeURIComponent(periode_akhir)}&nilai_sasaran=${encodeURIComponent(nilai_sasaran)}&nilai_budaya=${encodeURIComponent(nilai_budaya)}&total_nilai=${encodeURIComponent(total_nilai)}&fraud=${encodeURIComponent(fraud)}&nilai_akhir=${encodeURIComponent(nilai_akhir)}&pencapaian=${encodeURIComponent(pencapaian)}&predikat=${encodeURIComponent(predikat)}&koefisien=${encodeURIComponent(koefisien)}`
            });
        }

        // Event autosave untuk input target, batas waktu, realisasi
        document.querySelectorAll('#tabel-penilaian tbody tr[data-id]').forEach(row => {
            ['.target-input', '.realisasi-input', 'input[type="date"]'].forEach(selector => {
                const input = row.querySelector(selector);
                if (input) {
                    input.addEventListener('change', function() {
                        hitungTotal();
                        autoSaveBaris(row);
                        autoSaveNilaiAkhir();
                    });
                    // Untuk realisasi, juga autosave saat input (biar responsif)
                    if (selector === '.realisasi-input') {
                        input.addEventListener('input', function() {
                            hitungTotal();
                            autoSaveBaris(row);
                            autoSaveNilaiAkhir();
                        });
                    }
                }
            });
        });

        // Event autosave untuk fraud dan koefisien
        if (fraudInput) {
            fraudInput.addEventListener('input', function() {
                hitungNilaiAkhir();
                autoSaveNilaiAkhir();
            });
        }
        if (koefInput) {
            koefInput.addEventListener('input', function() {
                updatePredikatDanPencapaian();
                autoSaveNilaiAkhir();
            });
        }
        // Debug kecil (bisa dihapus nanti)
        console.log('Script KPI terpasang. nik=', getNik(), 'rows=', document.querySelectorAll('#tabel-penilaian tbody tr[data-id]').length);
    });
</script>