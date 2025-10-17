<!-- ============================================================== -->
<!-- Start Page Content here -->
<!-- ============================================================== -->
<style>
    /* Area chat */
    #chat-box {
        height: 350px;
        overflow-y: auto;
        background: linear-gradient(135deg, #fdfdfd, #f4f6f9);
        padding: 20px;
        border-radius: 16px;
        display: flex;
        flex-direction: column;
        gap: 12px;
    }

    /* Bubble umum */
    .chat-message {
        max-width: 70%;
        padding: 12px 16px;
        border-radius: 18px;
        font-size: 14px;
        position: relative;
        word-wrap: break-word;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        animation: fadeIn 0.3s ease-in-out;
    }

    /* Bubble pengirim (saya) */
    .chat-message.me {
        background: linear-gradient(135deg, #626262ff, #797777ff);
        color: #fff;
        margin-left: auto;
        border-bottom-right-radius: 6px;
        text-align: left;
    }

    /* Bubble penerima (lain) */
    .chat-message.other {
        background: #fff;
        border: 1px solid #e5e7eb;
        color: #111827;
        margin-right: auto;
        border-bottom-left-radius: 6px;
        text-align: left;
    }

    .chat-name {
        font-weight: 600;
        font-size: 13px;
        margin-bottom: 4px;
        color: #374151;
    }

    .chat-message.me .chat-name {
        color: #e0e7ff;
    }

    .chat-meta {
        font-size: 11px;
        color: #9ca3af;
        margin-top: 6px;
        text-align: right;
    }

    /* Animasi muncul */
    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(10px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    /* Input chat modern */
    #form-chat {
        margin-top: 12px;
        background: #fff;
        border-radius: 9999px;
        padding: 6px 10px;
        display: flex;
        align-items: center;
        border: 1px solid #e5e7eb;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.05);
    }

    #input-pesan {
        border: none;
        flex: 1;
        padding: 8px 12px;
        border-radius: 9999px;
        outline: none;
        font-size: 14px;
    }

    #form-chat button {
        border-radius: 9999px;
        padding: 8px 18px;
        font-weight: 500;
        background: linear-gradient(135deg, #2563eb, #3b82f6);
        border: none;
    }

    #form-chat button:hover {
        background: linear-gradient(135deg, #1d4ed8, #2563eb);
    }
</style>

<div class="content-page">
    <div class="content">

        <!-- Start Content-->
        <div class="container-fluid">

            <!-- start page title -->
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box mb-3">
                        <div class="page-title-right">
                            <ol class="breadcrumb m-0">
                                <li class="breadcrumb-item"><a href="javascript:void(0);">KPI Online-BRKS</a></li>
                                <li class="breadcrumb-item active">Dashboard Pegawai</li>
                            </ol>
                        </div>
                        <h5 class="page-title">Selamat Datang, <b><?= $pegawai_detail->nama; ?></b>!</h5>
                        <p class="text-muted mb-0">
                        <h5>Sistem Penilaian Kinerja Insani PT Bank Riau Kepri Syariah</h5>
                        </p>
                    </div>
                </div>
            </div>
            <!-- end page title -->

            <!-- Grafik Pencapaian Nilai Akhir -->
            <div class="card mt-4 shadow-sm border-0">
                <div class="card-body">
                    <h5 class="text-success font-weight-bold mb-3">
                        <i class="mdi mdi-chart-line mr-2"></i> Grafik Pencapaian Nilai Akhir
                    </h5>
                    <canvas id="grafikPencapaian" height="100"></canvas>
                </div>

                <!-- ========== INSIGHT OTOMATIS ========== -->
                <div class="card-footer bg-light border-top p-3 animate-fade-delay" id="insightContainer" style="display: none;">
                    <div class="insight-box mb-0 shadow-sm">
                        <h6 class="mb-1 d-flex align-items-center">
                            <i class="mdi mdi-lightbulb-on-outline mr-1 icon"></i>
                            <span>Insight Otomatis</span>
                        </h6>
                        <p class="mb-0 small text-dark" id="insightText"></p>
                    </div>
                </div>
            </div>

            <?php if (isset($pegawai_detail) && $pegawai_detail) { ?>

                <?php
                // ===== Tetap gunakan format tanggal Indonesia =====
                $bulan_indonesia = [
                    1 => 'Januari',
                    2 => 'Februari',
                    3 => 'Maret',
                    4 => 'April',
                    5 => 'Mei',
                    6 => 'Juni',
                    7 => 'Juli',
                    8 => 'Agustus',
                    9 => 'September',
                    10 => 'Oktober',
                    11 => 'November',
                    12 => 'Desember'
                ];

                // urutkan $periode_list berdasarkan periode_awal ascending
                usort($periode_list, function ($a, $b) {
                    return strtotime($a->periode_awal) - strtotime($b->periode_awal);
                });

                function formatTanggalIndonesia($tanggal, $bulan_indonesia)
                {
                    $tgl = date('d', strtotime($tanggal));
                    $bln = $bulan_indonesia[(int)date('m', strtotime($tanggal))];
                    $thn = date('Y', strtotime($tanggal));
                    return "$tgl $bln $thn";
                }
                ?>

                <div class="row">
                    <div class="col-12">
                        <div class="card shadow-sm border-0">
                            <div class="card-body">

                                <!-- Detail Pegawai & Informasi Penilaian -->
                                <div class="row mb-3">
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
                                                <span class="text-dark font-weight-medium">Unit Kantor</span>
                                                <span class="text-dark"><?= $pegawai_detail->unit_kerja; ?> <?= $pegawai_detail->unit_kantor ?? '-'; ?></span>
                                            </li>
                                        </ul>
                                        <input type="hidden" id="nik" value="<?= $pegawai_detail->nik ?>">
                                    </div>

                                    <!-- Informasi Penilaian -->
                                    <div class="col-md-6 mb-3">
                                        <h5 class="text-success mb-3 font-weight-bold">
                                            <i class="mdi mdi-file-document-outline mr-2"></i>Informasi Penilaian
                                        </h5>
                                        <div class="form-inline mb-2">
                                            <label class="mr-2 text-dark font-weight-medium"><b>Periode Penilaian:</b></label>
                                            <!-- Input tanggal manual -->
                                            <input type="hidden" id="periode_awal" class="form-control mr-2" value="<?= $periode_awal ?? date('Y-01-01'); ?>">
                                            <span class="mr-2"></span>
                                            <input type="hidden" id="periode_akhir" class="form-control mr-2" value="<?= $periode_akhir ?? date('Y-12-31'); ?>">

                                            <!-- Dropdown periode history -->

                                            <div class="form-inline mb-2">
                                                <select id="periode_history" class="form-control w-auto ml-2">
                                                    <option value="">Pilih Periode</option>
                                                    <?php foreach ($periode_list as $p): ?>
                                                        <option value="<?= $p->periode_awal . '|' . $p->periode_akhir ?>"
                                                            <?= (isset($periode_awal) && $periode_awal == $p->periode_awal) ? 'selected' : '' ?>>
                                                            <?= formatTanggalIndonesia($p->periode_awal, $bulan_indonesia) ?> s/d <?= formatTanggalIndonesia($p->periode_akhir, $bulan_indonesia) ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                </select>

                                                <!-- Tombol sesuaikan -->
                                                <button type="button" id="btn-sesuaikan-periode" class="btn btn-primary btn-sm ml-2">
                                                    Sesuaikan Periode
                                                </button>
                                            </div>
                                        </div>

                                        <p class="mt-2 text-dark font-weight-medium">
                                            <b>Unit Kantor Penilai:</b> <?= $pegawai_detail->unit_kerja; ?> <?= $pegawai_detail->unit_kantor ?? '-'; ?>
                                        </p>
                                    </div>
                                </div>

                                <hr>

                                <div class="row">
                                    <!-- Penilai I -->
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
                                                <span class="text-dark"><?= $pegawai_detail->penilai1_jabatan_detail ?? '-'; ?></span>
                                            </li>
                                            <!-- Tombol kanan -->
                                            <li class="list-group-item d-flex justify-content-end">
                                                <button class="btn btn-sm btn-info" type="button" data-toggle="collapse" data-target="#ubahPenilai1">
                                                    Ubah Penilai I
                                                </button>
                                            </li>
                                            <!-- Dropdown ubah penilai (default hidden) -->
                                            <li class="list-group-item collapse" id="ubahPenilai1">
                                                <label for="penilai1" class="text-dark font-weight-medium mb-1">Pilih Penilai I</label>
                                                <select name="penilai1_nik" id="penilai1" class="form-control select2">
                                                    <option value="">-- Pilih Penilai I --</option>
                                                </select>
                                            </li>

                                        </ul>
                                    </div>

                                    <!-- Penilai II -->
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
                                                <span class="text-dark"><?= $pegawai_detail->penilai2_jabatan_detail ?? '-'; ?></span>
                                            </li>
                                            <!-- Tombol kanan -->
                                            <li class="list-group-item d-flex justify-content-end">
                                                <button class="btn btn-sm btn-warning" type="button" data-toggle="collapse" data-target="#ubahPenilai2">
                                                    Ubah Penilai II
                                                </button>
                                            </li>
                                            <!-- Dropdown ubah penilai (default hidden) -->
                                            <li class="list-group-item collapse" id="ubahPenilai2">
                                                <label for="penilai2" class="text-dark font-weight-medium mb-1">Pilih Penilai II</label>
                                                <select name="penilai2_nik" id="penilai2" class="form-control select2">
                                                    <option value="">-- Pilih Penilai II --</option>
                                                    <?php foreach ($list_pegawai as $p): ?>
                                                        <?php if ($p->unit_kerja == $pegawai_detail->unit_kerja && $p->unit_kantor == $pegawai_detail->unit_kantor): ?>
                                                            <option value="<?= $p->nik; ?>"
                                                                <?= isset($pegawai_detail->penilai2_nik) && $pegawai_detail->penilai2_nik == $p->nik ? 'selected' : ''; ?>>
                                                                <?= $p->nama . " (" . $p->jabatan_detail . ")"; ?>
                                                            </option>
                                                        <?php endif; ?>
                                                    <?php endforeach; ?>
                                                </select>
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
                    foreach ($arr as $items) $sum += count($items);
                    return $sum;
                }
                ?>

                <!-- Tabel Penilaian -->
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="text-success fw-bold mb-3">
                                    <i class="mdi mdi-account-outline me-2"></i> Form Penilaian
                                </h5>
                                <a href="<?= base_url('Pegawai/downloadDataPegawai?nik=' . ($pegawai_detail->nik ?? '') . '&awal=' . $periode_awal . '&akhir=' . $periode_akhir) ?>"
                                    class="btn btn-success mt-2 mb-2 font-weight-bold" style="background-color:#217346; color:#fff;">
                                    <i class="mdi mdi-file-excel"></i> Download Excel
                                </a>
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
                                                if (empty($grouped[$persp])) continue;
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
                                                        <tr data-id="<?= $id; ?>" data-bobot="<?= $bobot; ?>" data-perspektif="<?= $persp; ?>"
                                                            data-indikator="<?= htmlspecialchars($indik, ENT_QUOTES, 'UTF-8'); ?>">
                                                            <?php if ($first_persp_cell) { ?>
                                                                <td rowspan="<?= $persp_rows; ?>" style="vertical-align:middle;font-weight:600;background:#C8E6C9;"><?= $persp; ?></td>
                                                            <?php $first_persp_cell = false;
                                                            } ?>

                                                            <?php if ($first_sas_cell) { ?>
                                                                <td rowspan="<?= $sasaran_rows; ?>" style="vertical-align:middle;background:#E3F2FD;"><?= $sasaran; ?></td>
                                                            <?php $first_sas_cell = false;
                                                            } ?>

                                                            <td class="text-center align-middle"><?= $bobot; ?>
                                                                <input type="hidden" class="bobot" value="<?= $bobot ?>">
                                                            </td>
                                                            <td><?= $indik; ?></td>

                                                            <style>
                                                                .currency-wrapper {
                                                                    position: relative;
                                                                    display: inline-block;
                                                                    width: 100%;
                                                                }

                                                                .currency-wrapper .format-currency {
                                                                    position: absolute;
                                                                    top: 0;
                                                                    left: 0;
                                                                    width: 100%;
                                                                    height: 100%;
                                                                    display: flex;
                                                                    justify-content: center;
                                                                    align-items: center;
                                                                    color: #000;
                                                                    font-weight: 550;
                                                                    pointer-events: none;
                                                                }

                                                                .currency-wrapper input.hide-text {
                                                                    color: transparent;
                                                                    caret-color: black;
                                                                }
                                                            </style>
                                                            <!-- Target -->
                                                            <td class="text-center align-middle">
                                                                <div class="currency-wrapper">
                                                                    <input type="text"
                                                                        class="form-control target-input text-center"
                                                                        style="min-width:150px;"
                                                                        value="<?= $i->target ?? ''; ?>">
                                                                    <div class="format-currency text-muted small"></div>
                                                                </div>
                                                            </td>
                                                            <td class="text-center align-middle">
                                                                <input type="date" class="form-control batas-waktu" style="min-width:120px;"
                                                                    value="<?= $i->batas_waktu ?? ''; ?>">
                                                            </td>
                                                            <td class="text-center align-middle">
                                                                <div class="currency-wrapper">
                                                                    <?php if (!$is_locked): ?>
                                                                        <input type="text" class="form-control text-center realisasi-input"
                                                                            value="<?= $i->realisasi ?? ''; ?>"
                                                                            style="min-width:150px;">
                                                                    <?php else: ?>
                                                                        <input type="text" class="form-control text-center realisasi-input"
                                                                            value="<?= $i->realisasi ?? ''; ?>"
                                                                            style="min-width:150px;" readonly>
                                                                    <?php endif; ?>
                                                                    <div class="format-currency text-muted small"></div>
                                                                </div>
                                                            </td>

                                                            <td class="text-center align-middle">
                                                                <input type="text" class="form-control form-control-sm text-center pencapaian-output"
                                                                    readonly style="min-width:50px;">
                                                            </td>
                                                            <td class="text-center align-middle">
                                                                <input type="text" class="form-control form-control-sm text-center nilai-output"
                                                                    readonly style="min-width:60px;">
                                                            </td>
                                                            <td class="text-center align-middle">
                                                                <input type="text" class="form-control form-control-sm text-center nilai-bobot-output"
                                                                    readonly style="min-width:50px;">
                                                            </td>


                                                            <td class="text-center align-middle">
                                                                <span class="<?= $statusClass; ?>"><?= $statusText; ?></span>
                                                            </td>
                                                            <td class="text-center align-middle">
                                                                <?php if (!$is_locked): ?>
                                                                    <button type="button" class="btn btn-sm btn-primary simpan-penilaian">Simpan</button>
                                                                <?php else: ?>
                                                                    <button type="button" class="btn btn-sm btn-secondary" disabled>Terkunci</button>
                                                                <?php endif; ?>

                                                            </td>
                                                        </tr>
                                                <?php
                                                    }
                                                }
                                                ?>
                                                <tr class="subtotal-row" data-perspektif="<?= $persp; ?>" style="font-weight:bold;background:#F1F8E9;">
                                                    <td colspan="2">Sub Total Bobot <?= $persp; ?></td>
                                                    <td class="text-center"><span class="subtotal-bobot"><?= $subtotal_bobot_perspektif; ?></span></td>
                                                    <td colspan="6" class="text-center">Sub Total Nilai <?= $persp; ?> Dibobot</td>
                                                    <td class="text-center"><span class="subtotal-nilai-bobot">0.00</span></td>
                                                    <td colspan="2"></td>
                                                </tr>
                                            <?php
                                            }
                                            if (!$printed_any) { ?>
                                                <tr>
                                                    <td colspan="12" class="text-center">Tidak ada indikator untuk jabatan ini</td>
                                                </tr>
                                            <?php } ?>
                                        </tbody>
                                        <tfoot style="background-color:#2E7D32;color:#fff;font-weight:bold;text-align:center;">
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
                                            if (!empty($budaya)) :
                                                foreach ($budaya as $b) :
                                                    $panduanList = json_decode($b['panduan_perilaku'], true);

                                                    if (is_array($panduanList)) :
                                                        foreach ($panduanList as $pIndex => $p) :
                                                            // Key sesuai format JSON nilai_budaya
                                                            $nilaiKey = "budaya_{$no}_{$pIndex}";
                                                            $nilai = isset($budaya_nilai[$nilaiKey]) ? (int)$budaya_nilai[$nilaiKey] : 0;

                                                            // Mapping label dan warna
                                                            switch ($nilai) {
                                                                case 1:
                                                                    $labelNilai = "1 - Sangat Jarang";
                                                                    $color = "text-danger"; // merah
                                                                    break;
                                                                case 2:
                                                                    $labelNilai = "2 - Jarang";
                                                                    $color = "text-warning"; // kuning
                                                                    break;
                                                                case 3:
                                                                    $labelNilai = "3 - Kadang";
                                                                    $color = "text-primary"; // biru
                                                                    break;
                                                                case 4:
                                                                    $labelNilai = "4 - Sering";
                                                                    $color = "text-success"; // hijau muda
                                                                    break;
                                                                case 5:
                                                                    $labelNilai = "5 - Selalu";
                                                                    $color = "fw-bold"; // hijau tua (nanti pakai inline)
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
                                                                        <?= htmlspecialchars($b['perilaku_utama']); ?>
                                                                    </td>
                                                                <?php endif; ?>
                                                                <td><?= chr(97 + $pIndex) . ". " . htmlspecialchars($p); ?></td>
                                                                <td class="text-center align-middle">
                                                                    <?php
                                                                    if ($nilai >= 1 && $nilai <= 5) {
                                                                        // Warna hijau tua (lebih gelap dari default Bootstrap)
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

                <!-- ================== FORM NILAI AKHIR ================== -->
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
                                        value="<?= $nilai_akhir['fraud'] ?? 0 ?>" readonly>
                                </td>
                            </tr>
                            <tr>
                                <th colspan="4" class="text-right">
                                    Koefisien Nilai<br>
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
                                            readonly>
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
                                    <?php if (!$is_locked): ?>
                                        <button id="btn-simpan-nilai-akhir" class="btn btn-primary">
                                            <i class="mdi mdi-content-save"></i> Simpan Nilai Akhir
                                        </button>
                                    <?php else: ?>
                                        <button class="btn btn-secondary" disabled>
                                            <i class="mdi mdi-lock"></i> Terkunci
                                        </button>
                                    <?php endif; ?>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ================== FORM TAMBAH CATATAN ================== -->
                <div class="card mb-3">
                    <div class="card-body">
                        <h5 class="card-title">Formulir feedback</h5>
                        <form id="form-catatan-pegawai">
                            <input type="hidden" name="nik" id="nik" value="<?= $pegawai_detail->nik;; ?>">

                            <div class="mb-3">
                                <label for="catatan-pegawai" class="form-label"><b>Pegawai</b></label>
                                <textarea class="form-control" name="catatan" id="catatan-pegawai" rows="3" required></textarea>
                            </div>

                            <button type="submit" class="btn btn-primary" id="btnSimpanCatatan">Simpan</button>
                        </form>


                        <!-- ================== TABEL CATATAN ================== -->

                        <div class="table-responsive">
                            <table class="table table-sm table-bordered" id="tabel-catatan-pegawai">
                                <thead class="table-light">
                                    <tr>
                                        <th>No</th>
                                        <th>Catatan</th>
                                        <th>Tanggal</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $catatan_list = $this->Pegawai_model->getCatatanPegawai($pegawai_detail->nik);
                                    $no = 1;
                                    if (!empty($catatan_list)) :
                                        foreach ($catatan_list as $c):
                                            $tgl = new DateTime($c->tanggal, new DateTimeZone('UTC'));
                                            $tgl->setTimezone(new DateTimeZone('Asia/Jakarta'));
                                    ?>
                                            <tr>
                                                <td><?= $no++ ?></td>
                                                <td><?= $c->catatan ?></td>
                                                <td><?= $tgl->format('d-m-Y H:i') ?></td>
                                            </tr>
                                        <?php
                                        endforeach;
                                    else: ?>
                                        <tr>
                                            <td colspan="3" class="text-center">Belum ada catatan</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- ================== FORM CHAT ================== -->
                <div class="card mb-3 shadow-sm border-0">
                    <div class="card-body">
                        <h5 class="card-title mb-3">
                            <i class="fas fa-comments text-primary mr-2"></i> Aktivitas Coaching Kinerja
                        </h5>

                        <!-- Box Chat -->
                        <div id="chat-box" style="max-height:400px; overflow-y:auto; border:1px solid #ddd; padding:10px;">
                            <!-- Pesan via AJAX -->
                        </div>

                        <!-- Form Kirim -->
                        <form id="form-chat" class="mt-2 d-flex">
                            <input type="hidden" name="nik_pegawai" value="<?= $pegawai_detail->nik ?>">
                            <input type="text" name="pesan" id="input-pesan" class="form-control mr-2" placeholder="Tulis pesan..." autocomplete="off">
                            <button type="submit" class="btn btn-primary">Kirim</button>
                        </form>
                    </div>
                </div>


            <?php } ?>
        </div>
    </div>
</div>
<!-- ============================================================== -->
<!-- End Page Content here -->
<!-- ============================================================== -->

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<?php
// flash message (jika ada)
$message = $this->session->flashdata('message');
if ($message): ?>
    <script>
        Swal.fire({
            icon: '<?= $message['type']; ?>',
            title: 'Informasi',
            text: '<?= $message['text']; ?>',
            confirmButtonColor: '#2E7D32'
        });
    </script>
<?php endif; ?>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const nik = document.getElementById('nik')?.value;
        const periodeAwal = document.getElementById('periode_awal');
        const periodeAkhir = document.getElementById('periode_akhir');
        const periodeHistory = document.getElementById('periode_history');
        const koefInput = document.getElementById('koefisien-input');

        // default dari server (jaga-jaga kalau kosong)
        if (!periodeAwal.value) periodeAwal.value = "<?= $periode_awal ?? date('Y-01-01'); ?>";
        if (!periodeAkhir.value) periodeAkhir.value = "<?= $periode_akhir ?? date('Y-12-31'); ?>";

        // validasi supaya periode akhir tidak lebih kecil dari awal
        periodeAwal.addEventListener('change', function() {
            if (periodeAkhir.value < this.value) periodeAkhir.value = this.value;
        });
        periodeAkhir.addEventListener('change', function() {
            if (this.value < periodeAwal.value) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Periode salah',
                    text: 'Tanggal akhir tidak boleh lebih kecil dari tanggal awal',
                    confirmButtonColor: '#d33'
                });
                this.value = periodeAwal.value;
            }
        });

        // ===== Dropdown Periode History =====
        periodeHistory.addEventListener('change', function() {
            if (!this.value) return;
            const [awal, akhir] = this.value.split('|');
            periodeAwal.value = awal;
            periodeAkhir.value = akhir;
        });

        // Reset dropdown jika user ubah tanggal manual
        periodeAwal.addEventListener('input', () => periodeHistory.value = '');
        periodeAkhir.addEventListener('input', () => periodeHistory.value = '');

        // ===== Tombol Sesuaikan Periode =====
        document.getElementById('btn-sesuaikan-periode').addEventListener('click', function() {
            const awal = periodeAwal.value;
            const akhir = periodeAkhir.value;
            window.location.href = `<?= base_url("Pegawai") ?>?awal=${awal}&akhir=${akhir}`;
        });

        // format angka
        function formatAngka(nilai) {
            let num = parseFloat(nilai);
            if (isNaN(num)) return '';
            return Number.isInteger(num) ? num.toString() : num.toFixed(2);
        }

        function hitungPencapaianOtomatis(target, realisasi, indikatorText = "") {
            let pencapaian = 0;

            // Normalisasi teks
            indikatorText = indikatorText.toLowerCase();

            // 🔹 Daftar keyword
            const keywords = {
                rumus1: ["biaya", "beban", "efisiensi", "npf pembiayaan"], // indikator biaya / beban
                rumus3: ["outstanding", "pertumbuhan"] // indikator outstanding / pertumbuhan
            };

            // Fungsi cek keyword pakai regex \b...\b
            const containsKeyword = (list, text) => {
                return list.some(k => new RegExp(`\\b${k}\\b`, "i").test(text));
            };

            if (target <= 999) {
                // 🔹 Rumus 2 (default untuk target ≤ 3 digit)
                pencapaian = (realisasi / target) * 100;
            } else {
                // 🔹 Target > 3 digit → pilih rumus 1 atau 3 berdasarkan kata kunci indikator
                if (containsKeyword(keywords.rumus1, indikatorText)) {
                    // Rumus 1 → biasanya indikator biaya/beban
                    pencapaian = ((target + (target - realisasi)) / target) * 100;
                } else if (containsKeyword(keywords.rumus3, indikatorText)) {
                    // Rumus 3 → biasanya indikator outstanding/pertumbuhan
                    pencapaian = ((realisasi - target) / Math.abs(target) + 1) * 100;
                } else {
                    // fallback default (anggap rumus 2)
                    pencapaian = (realisasi / target) * 100;
                }
            }
            // 🔹 Batas maksimal 130%
            return Math.min(pencapaian, 130);
        }

        function hitungNilai(pencapaian) {
            let nilai = 0;

            if (pencapaian < 0) {
                nilai = 0;
            } else if (pencapaian < 80) {
                nilai = (pencapaian / 80) * 2;
            } else if (pencapaian < 90) {
                nilai = 2 + ((pencapaian - 80) / 10);
            } else if (pencapaian < 110) {
                nilai = 3 + ((pencapaian - 90) / 20 * 0.5);
            } else if (pencapaian < 120) {
                nilai = 3.5 + ((pencapaian - 110) / 10 * 1);
            } else if (pencapaian < 130) {
                nilai = 4.5 + ((pencapaian - 120) / 10 * 0.5);
            } else {
                nilai = 5;
            }

            return nilai;
        }


        function hitungRow(row, totalBobot) {
            const targetVal = row.querySelector('.target-input').value;
            const realisasiVal = row.querySelector('.realisasi-input').value;
            const bobot = parseFloat(row.querySelector('.bobot').value) || 0;

            // 🔹 Ambil teks indikator dari atribut data-indikator
            const indikatorText = row.dataset.indikator || "";

            let pencapaian = "";
            let nilai = "";
            let nilaiBobot = "";

            if (targetVal !== "" && realisasiVal !== "") {
                const target = parseFloat(targetVal) || 0;
                const realisasi = parseFloat(realisasiVal) || 0;

                pencapaian = hitungPencapaianOtomatis(target, realisasi, indikatorText);
                nilai = hitungNilai(pencapaian);

                if (totalBobot > 0) {
                    nilaiBobot = (nilai * bobot) / totalBobot;
                }
            }

            row.querySelector('.pencapaian-output').value = pencapaian === "" ? "" : formatAngka(pencapaian);
            row.querySelector('.nilai-output').value = nilai === "" ? "" : formatAngka(nilai);
            row.querySelector('.nilai-bobot-output').value = nilaiBobot === "" ? "" : formatAngka(nilaiBobot);

            return {
                bobot,
                nilaiBobot: nilaiBobot === "" ? 0 : parseFloat(formatAngka(nilaiBobot)),
                perspektif: row.dataset.perspektif
            };
        }

        function hitungTotal() {
            let totalBobot = 0,
                totalNilai = 0;
            const subtotalMap = {};

            // 🔹 hitung total bobot dulu
            document.querySelectorAll('#tabel-penilaian tbody tr[data-id]').forEach(row => {
                totalBobot += parseFloat(row.querySelector('.bobot').value) || 0;
            });

            // 🔹 lalu panggil hitungRow dengan totalBobot
            document.querySelectorAll('#tabel-penilaian tbody tr[data-id]').forEach(row => {
                const {
                    bobot,
                    nilaiBobot,
                    perspektif
                } = hitungRow(row, totalBobot);
                totalNilai += nilaiBobot;

                if (!subtotalMap[perspektif]) subtotalMap[perspektif] = 0;
                subtotalMap[perspektif] += nilaiBobot;
            });

            document.getElementById('total-bobot').innerText = formatAngka(totalBobot);
            document.getElementById('total-nilai-bobot').innerText = formatAngka(totalNilai);

            document.querySelectorAll('.subtotal-row').forEach(row => {
                const perspektif = row.dataset.perspektif;
                row.querySelector('.subtotal-nilai-bobot').innerText = formatAngka(subtotalMap[perspektif] || 0);
            });

            // Tambahkan baris ini agar total-sasaran sama dengan total-nilai-bobot
            document.getElementById('total-sasaran').textContent = formatAngka(totalNilai);

            hitungNilaiAkhir();
        }

        function hitungNilaiAkhir() {
            const bobotSasaran = 0.95;
            const bobotBudaya = 0.05;

            const fraud = parseFloat(document.getElementById("fraud-input").value) || 0;
            const koef = koefInput ? (parseFloat(koefInput.value) || 100) / 100 : 1;

            // Ambil nilai sasaran dari total-nilai-bobot
            const totalSasaran = parseFloat(document.getElementById("total-nilai-bobot").textContent) || 0;
            const rataBudaya = parseFloat(document.getElementById("rata-budaya").textContent) || 0;

            // Total nilai sasaran kerja 
            const nilaiSasaran = totalSasaran * bobotSasaran;

            // Nilai budaya
            const nilaiBudaya = rataBudaya * bobotBudaya;

            // Total nilai
            const totalNilai = nilaiSasaran + nilaiBudaya;

            // Nilai akhir sesuai rumus Excel
            let nilaiAkhir;
            if (fraud === 1) {
                nilaiAkhir = totalNilai - fraud;
            } else {
                nilaiAkhir = totalNilai;
            }

            // Predikat
            let predikat;
            let predikatClass = "";

            if (nilaiAkhir === "Tidak ada nilai") {
                predikat = "Tidak ada yudisium/predikat";
                predikatClass = "text-dark";
            } else if (nilaiAkhir === 0) {
                predikat = "Belum Ada Nilai";
                predikatClass = "text-dark";
            } else if (nilaiAkhir < 2 * koef) {
                predikat = "Minus";
                predikatClass = "text-danger"; // merah
            } else if (nilaiAkhir < 3 * koef) {
                predikat = "Fair";
                predikatClass = "text-warning"; // jingga
            } else if (nilaiAkhir < 3.5 * koef) {
                predikat = "Good";
                predikatClass = "text-primary"; // biru
            } else if (nilaiAkhir < 4.5 * koef) {
                predikat = "Very Good";
                predikatClass = "text-success"; // hijau muda
            } else {
                predikat = "Excellent";
                predikatClass = "text-success font-weight-bold"; // hijau tua (lebih tebal)
            }

            // Pencapaian Akhir
            let pencapaian = "";
            if (nilaiAkhir !== "Tidak ada nilai") {
                const v = parseFloat(nilaiAkhir) || 0;
                if (v < 0) pencapaian = 0;
                else if (v < 2 * koef) pencapaian = (v / 2) * 0.8 * 100;
                else if (v < 3 * koef) pencapaian = 80 + ((v - 2) / 1) * 10;
                else if (v < 3.5 * koef) pencapaian = 90 + ((v - 3) / 0.5) * 20;
                else if (v < 4.5 * koef) pencapaian = 110 + ((v - 3.5) / 1) * 10;
                else if (v < 5 * koef) pencapaian = 120 + ((v - 4.5) / 0.5) * 10;
                else pencapaian = 130;
            } else {
                pencapaian = 0;
            }

            // Update ke tampilan
            document.getElementById("nilai-sasaran").textContent = nilaiSasaran.toFixed(2);
            document.getElementById("nilai-budaya").textContent = nilaiBudaya.toFixed(2);
            document.getElementById("total-nilai").textContent = totalNilai.toFixed(2);
            document.getElementById("nilai-akhir").textContent =
                nilaiAkhir === "Tidak ada nilai" ? nilaiAkhir : nilaiAkhir.toFixed(2);
            document.getElementById("predikat").textContent = predikat;
            document.getElementById("predikat").className = predikatClass;
            document.getElementById("pencapaian-akhir").textContent =
                pencapaian === "" ? "" : pencapaian.toFixed(2) + "%";
        }

        document.getElementById('fraud-input').addEventListener('input', hitungNilaiAkhir);

        // 🔹 trigger perhitungan saat input diubah
        document.querySelectorAll('.target-input, .realisasi-input').forEach(input => {
            input.addEventListener('input', hitungTotal);
        });
        hitungTotal();

        // Simpan penilaian per baris
        document.querySelectorAll('.simpan-penilaian').forEach(btn => {
            btn.addEventListener('click', function() {
                const row = this.closest('tr');
                const indikator_id = row.dataset.id;
                const target = row.querySelector('.target-input').value;
                const batas_waktu = row.querySelector('input[type="date"]').value;
                const realisasi = row.querySelector('.realisasi-input').value;
                const pencapaian = row.querySelector('.pencapaian-output').value;
                const nilai = row.querySelector('.nilai-output').value;
                const nilai_dibobot = row.querySelector('.nilai-bobot-output').value;

                const periode_awal = periodeAwal.value;
                const periode_akhir = periodeAkhir.value;

                console.log("DEBUG: nik=", nik, "indikator_id=", indikator_id, "periode_awal=", periode_awal, "periode_akhir=", periode_akhir);

                fetch('<?= base_url("Pegawai/simpanPenilaianBaris") ?>', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded'
                        },
                        body: `indikator_id=${indikator_id}&target=${encodeURIComponent(target)}&batas_waktu=${encodeURIComponent(batas_waktu)}&realisasi=${encodeURIComponent(realisasi)}&pencapaian=${encodeURIComponent(pencapaian)}&nilai=${encodeURIComponent(nilai)}&nilai_dibobot=${encodeURIComponent(nilai_dibobot)}&periode_awal=${encodeURIComponent(periode_awal)}&periode_akhir=${encodeURIComponent(periode_akhir)}`
                    })
                    .then(res => res.json())
                    .then(res => {
                        if (res.status === 'success') {
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil',
                                text: res.message,
                                timer: 2000,
                                showConfirmButton: false
                            });
                            hitungTotal();
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal',
                                text: res.message || 'Gagal menyimpan',
                                confirmButtonColor: '#d33'
                            });
                        }
                    })
                    .catch(err => {
                        console.error(err);
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Terjadi kesalahan server',
                            confirmButtonColor: '#d33'
                        });
                    });
            });
        });

        // tombol sesuaikan periode -> reload page dengan query params (tanpa NIK karena pakai session)
        document.getElementById('btn-sesuaikan-periode').addEventListener('click', function() {
            const awal = periodeAwal.value;
            const akhir = periodeAkhir.value;
            window.location.href = `<?= base_url("Pegawai") ?>?awal=${awal}&akhir=${akhir}`;
        });

        // ==== Custom sorting untuk tanggal DD-MM-YYYY HH:MM ====
        $.extend($.fn.dataTableExt.oSort, {
            "date-uk-pre": function(a) {
                if (!a) return 0;
                var parts = a.split(' '); // ["07-10-2025", "10:30"]
                var dateParts = parts[0].split('-'); // ["07","10","2025"]
                var timeParts = parts[1] ? parts[1].split(':') : ["00", "00"]; // ["10","30"]
                return (dateParts[2] + dateParts[1] + dateParts[0] + timeParts[0] + timeParts[1]) * 1;
            },
            "date-uk-asc": function(a, b) {
                return ((a < b) ? -1 : ((a > b) ? 1 : 0));
            },
            "date-uk-desc": function(a, b) {
                return ((a < b) ? 1 : ((a > b) ? -1 : 0));
            }
        });

        // ==== DataTables Catatan Pegawai ====
        var tableCatatanPegawai = $('#tabel-catatan-pegawai').DataTable({
            responsive: false,
            paging: true,
            searching: true,
            ordering: true,
            order: [
                [2, 'desc']
            ], // kolom tanggal
            columnDefs: [{
                    orderable: false,
                    targets: [0]
                }, // kolom nomor
                {
                    type: 'date-uk',
                    targets: 2
                } // kolom tanggal pakai custom sorting
            ],
            language: {
                search: "Cari:",
                lengthMenu: "Tampilkan _MENU_ baris",
                info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ catatan",
                infoEmpty: "Menampilkan 0 sampai 0 dari 0 catatan",
                zeroRecords: "Tidak ada catatan yang ditemukan",
                paginate: {
                    first: "Pertama",
                    last: "Terakhir",
                    next: "Berikut",
                    previous: "Sebelumnya"
                }
            },
            dom: '<"row mb-2"<"col-md-6"l><"col-md-6 text-right"f>>rt<"row mt-2"<"col-md-6"i><"col-md-6 d-flex justify-content-end"p>>',
            drawCallback: function(settings) {
                var api = this.api();
                api.column(0, {
                    order: 'applied'
                }).nodes().each(function(cell, i) {
                    cell.innerHTML = i + 1;
                });
            }
        });

        // ==== AJAX Form Catatan Pegawai ====
        $('#form-catatan-pegawai').on('submit', function(e) {
            e.preventDefault();

            const nik = $('#nik').val();
            const catatan = $('#catatan-pegawai').val().trim();

            if (catatan === '') {
                Swal.fire({
                    icon: 'warning',
                    title: 'Catatan kosong'
                });
                return;
            }

            fetch("<?= base_url('pegawai/simpan_catatan_pegawai'); ?>", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/x-www-form-urlencoded"
                    },
                    body: `nik=${nik}&catatan=${encodeURIComponent(catatan)}`
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil',
                            timer: 1500,
                            showConfirmButton: false
                        });

                        // tanggal sekarang DD-MM-YYYY HH:MM
                        const now = new Date();
                        const tanggal =
                            String(now.getDate()).padStart(2, '0') + '-' +
                            String(now.getMonth() + 1).padStart(2, '0') + '-' +
                            now.getFullYear() + ' ' +
                            String(now.getHours()).padStart(2, '0') + ':' +
                            String(now.getMinutes()).padStart(2, '0');

                        // tambahkan row baru
                        tableCatatanPegawai.row.add([
                            '', // nomor otomatis
                            catatan, // catatan
                            tanggal // tanggal
                        ]).draw();

                        // paksa sorting ulang agar row baru langsung diurutkan
                        tableCatatanPegawai.order([2, 'desc']).draw();

                        $('#form-catatan-pegawai')[0].reset();
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
                        title: 'Error',
                        text: 'Terjadi kesalahan server'
                    });
                });
        });

        $(document).ready(function() {
            const nikPegawai = "<?= $pegawai_detail->nik; ?>"; // pegawai yang sedang dilihat
            const penilai1_selected = "<?= $pegawai_detail->penilai1_nik ?? ''; ?>";
            const penilai2_selected = "<?= $pegawai_detail->penilai2_nik ?? ''; ?>";

            $.ajax({
                url: "<?= base_url('Pegawai/getPegawaiSatuUnit/'); ?>" + nikPegawai,
                method: "GET",
                dataType: "json",
                success: function(data) {
                    if (data.length > 0) {
                        // isi dropdown penilai 1
                        let opt1 = '<option value="">-- Pilih Penilai I --</option>';
                        data.forEach(function(p) {
                            let selected = (p.nik === penilai1_selected) ? "selected" : "";
                            opt1 += `<option value="${p.nik}" ${selected}>${p.nama} (${p.jabatan})</option>`;
                        });
                        $('#penilai1').html(opt1);

                        // isi dropdown penilai 2
                        let opt2 = '<option value="">-- Pilih Penilai II --</option>';
                        data.forEach(function(p) {
                            let selected = (p.nik === penilai2_selected) ? "selected" : "";
                            opt2 += `<option value="${p.nik}" ${selected}>${p.nama} (${p.jabatan})</option>`;
                        });
                        $('#penilai2').html(opt2);
                    }
                }
            });
        });


        // 2. Ganti event input pada .realisasi-input:
        document.querySelectorAll('.realisasi-input, .target-input, input[type="date"]').forEach(input => {
            input.addEventListener('input', function() {
                hitungTotal();

                // Auto-save penilaian baris
                const row = this.closest('tr');
                const indikator_id = row.dataset.id;
                const realisasi = row.querySelector('.realisasi-input').value;
                const target = row.querySelector('.target-input').value;
                const batas_waktu = row.querySelector('input[type="date"]').value;
                const pencapaian = row.querySelector('.pencapaian-output').value;
                const nilai = row.querySelector('.nilai-output').value;
                const nilai_dibobot = row.querySelector('.nilai-bobot-output').value;
                const periode_awal = document.getElementById('periode_awal').value;
                const periode_akhir = document.getElementById('periode_akhir').value;

                fetch('<?= base_url("Pegawai/simpanPenilaianBaris") ?>', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded'
                        },
                        body: `indikator_id=${indikator_id}&target=${encodeURIComponent(target)}&batas_waktu=${encodeURIComponent(batas_waktu)}&realisasi=${encodeURIComponent(realisasi)}&pencapaian=${encodeURIComponent(pencapaian)}&nilai=${encodeURIComponent(nilai)}&nilai_dibobot=${encodeURIComponent(nilai_dibobot)}&periode_awal=${encodeURIComponent(periode_awal)}&periode_akhir=${encodeURIComponent(periode_akhir)}`
                    })
                    .then(res => res.json())
                    .then(res => {
                        // Optional: tampilkan notifikasi kecil (atau silent)
                        if (res.status !== 'success') {
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal',
                                text: res.message || 'Gagal menyimpan',
                                confirmButtonColor: '#d33'
                            });
                        }
                    })
                    .catch(err => {
                        console.error(err);
                    });

                // Setelah simpan baris, auto-save nilai akhir juga
                autoSaveNilaiAkhir();
            });
        });

        // 3. Fungsi auto-save nilai akhir
        function autoSaveNilaiAkhir() {
            const nik = document.getElementById('nik').value;
            const periode_awal = document.getElementById('periode_awal').value;
            const periode_akhir = document.getElementById('periode_akhir').value;
            const nilai_sasaran = document.getElementById('total-sasaran').textContent;
            const nilai_budaya = document.getElementById('rata-budaya').textContent;
            const total_nilai = document.getElementById('total-nilai').textContent;
            const fraud = document.getElementById('fraud-input').value;
            const nilai_akhir = document.getElementById('nilai-akhir').textContent;
            const predikat = document.getElementById('predikat').textContent;
            const pencapaian = document.getElementById('pencapaian-akhir').textContent;

            fetch('<?= base_url("Pegawai/simpanNilaiAkhir") ?>', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    body: `nik=${encodeURIComponent(nik)}&periode_awal=${encodeURIComponent(periode_awal)}&periode_akhir=${encodeURIComponent(periode_akhir)}&nilai_sasaran=${encodeURIComponent(nilai_sasaran)}&nilai_budaya=${encodeURIComponent(nilai_budaya)}&total_nilai=${encodeURIComponent(total_nilai)}&fraud=${encodeURIComponent(fraud)}&nilai_akhir=${encodeURIComponent(nilai_akhir)}&pencapaian=${encodeURIComponent(pencapaian)}&predikat=${encodeURIComponent(predikat)}`
                })
                .then(res => res.json())
                .then(res => {
                    // Optional: tampilkan notifikasi kecil (atau silent)
                })
                .catch(err => {
                    console.error(err);
                });
        }

        document.getElementById('btn-simpan-nilai-akhir').addEventListener('click', function() {
            const nik = document.getElementById('nik').value;
            const periode_awal = document.getElementById('periode_awal').value;
            const periode_akhir = document.getElementById('periode_akhir').value;
            const nilai_sasaran = document.getElementById('total-sasaran').textContent;
            const nilai_budaya = document.getElementById('rata-budaya').textContent;
            const total_nilai = document.getElementById('total-nilai').textContent;
            const fraud = document.getElementById('fraud-input').value;
            const nilai_akhir = document.getElementById('nilai-akhir').textContent;
            const predikat = document.getElementById('predikat').textContent;
            const pencapaian = document.getElementById('pencapaian-akhir').textContent;

            fetch('<?= base_url("Pegawai/simpanNilaiAkhir") ?>', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    body: `nik=${encodeURIComponent(nik)}&periode_awal=${encodeURIComponent(periode_awal)}&periode_akhir=${encodeURIComponent(periode_akhir)}&nilai_sasaran=${encodeURIComponent(nilai_sasaran)}&nilai_budaya=${encodeURIComponent(nilai_budaya)}&total_nilai=${encodeURIComponent(total_nilai)}&fraud=${encodeURIComponent(fraud)}&nilai_akhir=${encodeURIComponent(nilai_akhir)}&pencapaian=${encodeURIComponent(pencapaian)}&predikat=${encodeURIComponent(predikat)}`
                })
                .then(res => res.json())
                .then(res => {
                    if (res.status === 'success') {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil',
                            text: res.message,
                            timer: 2000,
                            showConfirmButton: false
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal',
                            text: res.message || 'Gagal menyimpan',
                            confirmButtonColor: '#d33'
                        });
                    }
                })
                .catch(err => {
                    console.error(err);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Terjadi kesalahan server',
                        confirmButtonColor: '#d33'
                    });
                });
        });
        // ================ Format Rupiah ===================
        function formatRp(num) {
            if (num === null || num === undefined || num === '') return '';
            var n = ('' + num).replace(/[^0-9]/g, '');
            if (n === '') return '';
            return 'Rp. ' + n.replace(/\B(?=(\d{3})+(?!\d))/g, ".");
        }

        function updateFormatDisplayForInput(input, show) {
            const display = input.parentElement.querySelector('.format-currency');
            if (!display) return;
            const val = input.value.replace(/[^0-9]/g, '');
            if (!val) {
                display.textContent = '';
                input.classList.remove('hide-text');
                return;
            }

            if (show && parseFloat(val) >= 1000) {
                display.textContent = formatRp(val);
                input.classList.add('hide-text');
            } else {
                display.textContent = '';
                input.classList.remove('hide-text');
            }
        }

        // apply to all
        document.querySelectorAll('.target-input, .realisasi-input').forEach(input => {
            // awal halaman, tampilkan format
            updateFormatDisplayForInput(input, true);

            input.addEventListener('focus', () => {
                // saat edit, tampil angka mentah
                updateFormatDisplayForInput(input, false);
            });
            input.addEventListener('blur', () => {
                // saat selesai edit, tampil Rp.
                updateFormatDisplayForInput(input, true);
            });
        });
    });
</script>

<script>
    let lastId = 0; // simpan id pesan terakhir

    // Format tanggal ke WIB
    function formatToJakartaTime(dateStr) {
        if (!dateStr) return '';
        const [date, time] = dateStr.split(' ');
        const [year, month, day] = date.split('-');
        const [hour, minute, second] = time.split(':');
        const utcDate = new Date(Date.UTC(year, month - 1, day, hour, minute, second));
        return utcDate.toLocaleString('id-ID', {
            timeZone: 'Asia/Jakarta',
            hour: '2-digit',
            minute: '2-digit',
            second: '2-digit',
            day: '2-digit',
            month: '2-digit',
            year: 'numeric'
        });
    }

    // Ambil pesan baru
    function loadChat() {
        const nikPegawai = $('input[name="nik_pegawai"]').val();

        $.getJSON("<?= base_url('Pegawai/getCoachingChat/') ?>" + nikPegawai + "?lastId=" + lastId, function(data) {
            if (data.length > 0) {
                data.forEach(function(row) {
                    let isMe = row.pengirim_nik === "<?= $this->session->userdata('nik'); ?>";
                    let jamWIB = formatToJakartaTime(row.created_at);

                    $('#chat-box').append(`
                        <div class="chat-message ${isMe ? 'me text-right' : 'other text-left'} mb-2">
                            <div class="chat-name font-weight-bold">${row.nama_pengirim} (${row.jabatan})</div>
                            <div class="chat-text">${row.pesan}</div>
                            <div class="chat-meta text-muted small">${jamWIB}</div>
                        </div>
                    `);

                    lastId = row.id; // update id terakhir
                });

                $('#chat-box').scrollTop($('#chat-box')[0].scrollHeight);
            } else {
                if (lastId === 0) {
                    $('#chat-box').html('<div class="text-center text-muted">Belum ada pesan. Mulai percakapan dengan mengirim pesan.</div>');
                }
            }
        });
    }

    // Load awal
    loadChat();

    // Polling tiap 5 detik
    setInterval(loadChat, 5000);

    // Kirim pesan
    $('#form-chat').on('submit', function(e) {
        e.preventDefault();
        const formData = $(this).serialize();

        $.ajax({
            url: "<?= base_url('Pegawai/kirimCoachingPesan') ?>",
            method: "POST",
            data: formData,
            dataType: "json",
            success: function(res) {
                if (res.success) {
                    $('#input-pesan').val('');
                    loadChat(); // langsung cek ada pesan baru
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal',
                        text: res.message || 'Pesan gagal disimpan!'
                    });
                }
            },
            error: function(xhr, status, error) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Terjadi kesalahan server: ' + error
                });
            }
        });
    });
</script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const isLocked = <?= $is_locked ? 'true' : 'false' ?>;
        if (isLocked) {
            document.querySelectorAll('.target-input, .batas-waktu, .realisasi-input, .simpan-penilaian, #btn-simpan-nilai-akhir')
                .forEach(el => el.disabled = true);

            Swal.fire({
                icon: 'info',
                title: 'Periode Dikunci',
                text: 'Anda tidak dapat mengubah data karena periode ini sudah dikunci oleh admin.',
                timer: 2500,
                showConfirmButton: false
            });
        }
    });
</script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const isLocked2 = <?= $is_locked2 ? 'true' : 'false' ?>;

        if (isLocked2) {
            document.querySelectorAll('.target-input, .batas-waktu')
                .forEach(el => el.disabled = true);

            Swal.fire({
                icon: 'info',
                title: 'Target Dikunci',
                text: 'Kolom Target dan Batas Waktu dikunci oleh admin.',
                timer: 2500,
                showConfirmButton: false
            });
        }
    });
</script>

<style>
    /* Animasi Fade */
    .animate-fade-delay {
        animation: fadeIn 0.8s ease-in-out;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(10px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    /* Insight Box */
    .insight-box {
        border-left: 6px solid #007bff;
        border-radius: 10px;
        padding: 15px 18px;
        background: #eef5ff;
        transition: all 0.3s ease;
    }

    /* Varian Warna Dinamis */
    .insight-danger {
        border-left-color: #dc3545;
        background: linear-gradient(90deg, #ffe1e1, #ffd6d6);
    }

    .insight-success {
        border-left-color: #28a745;
        background: linear-gradient(90deg, #e7f8ec, #d8f5e2);
    }

    .insight-info {
        border-left-color: #007bff;
        background: linear-gradient(90deg, #e8f1ff, #d7e7ff);
    }

    /* Ikon Dinamis */
    .insight-danger .icon {
        color: #dc3545;
    }

    .insight-success .icon {
        color: #28a745;
    }

    .insight-info .icon {
        color: #007bff;
    }
</style>

<!-- ======================= -->
<!-- SCRIPT -->
<!-- ======================= -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const grafikData = <?= json_encode($grafik_pencapaian, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>;

    const dataPeriode = grafikData.map(g => {
        return (new Date(g.periode_awal)).toLocaleDateString('id-ID', {
                day: '2-digit',
                month: 'short'
            }) +
            ' - ' +
            (new Date(g.periode_akhir)).toLocaleDateString('id-ID', {
                day: '2-digit',
                month: 'short'
            });
    });

    const dataPencapaian = grafikData.map(g => g.pencapaian);

    const segmentColors = dataPencapaian.map((value, index, arr) => {
        if (index === 0) return 'rgba(40, 167, 69, 1)';
        return value >= arr[index - 1] ? 'rgba(40, 167, 69, 1)' : 'rgba(220, 53, 69, 1)';
    });

    const ctx = document.getElementById('grafikPencapaian').getContext('2d');
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: dataPeriode,
            datasets: [{
                label: 'Pencapaian (%)',
                data: dataPencapaian,
                borderColor: ctx => {
                    const colors = segmentColors;
                    const gradient = ctx.chart.ctx.createLinearGradient(0, 0, ctx.chart.width, 0);
                    for (let i = 0; i < colors.length; i++) {
                        gradient.addColorStop(i / (colors.length - 1), colors[i]);
                    }
                    return gradient;
                },
                backgroundColor: 'rgba(40, 167, 69, 0.08)',
                borderWidth: 3,
                fill: true,
                tension: 0.3,
                pointRadius: 5,
                pointBackgroundColor: segmentColors,
                pointBorderColor: '#fff',
                pointHoverRadius: 7
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'top'
                },
                tooltip: {
                    callbacks: {
                        label: ctx => ctx.parsed.y + '%'
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Pencapaian (%)'
                    }
                }
            }
        }
    });

    // fallback predikat
    function predikatDariNilaiAkhir(nilai) {
        if (nilai === null || nilai === undefined || isNaN(nilai)) return null;
        if (nilai >= 4.5) return 'Excellent';
        if (nilai >= 3.5) return 'Very Good';
        if (nilai >= 3.0) return 'Good';
        if (nilai >= 2.0) return 'Fair';
        return 'Minus';
    }

    const insightContainer = document.getElementById('insightContainer');
    const insightBox = insightContainer.querySelector('.insight-box');
    const insightText = document.getElementById('insightText');
    const icon = insightBox.querySelector('.icon');

    if (grafikData.length > 1) {
        const lastIndex = grafikData.length - 1;
        const gNow = grafikData[lastIndex];
        const gPrev = grafikData[lastIndex - 1];

        const lastP = gNow.pencapaian;
        const prevP = gPrev.pencapaian;
        const diff = lastP - prevP;

        const periodeNow = dataPeriode[lastIndex];
        const periodePrev = dataPeriode[lastIndex - 1];

        const predikatNow = gNow.predikat || predikatDariNilaiAkhir(gNow.nilai_akhir);
        const predikatPrev = gPrev.predikat || predikatDariNilaiAkhir(gPrev.nilai_akhir);

        let message = '';

        if (diff > 0) {
            insightBox.classList.add('insight-success');
            icon.classList.add('mdi-trending-up');
            message = `
                🎯 <strong>Pencapaian meningkat</strong> sebesar <strong>${diff.toFixed(1)}%</strong>
                dari <strong>${periodePrev}</strong> ke <strong>${periodeNow}</strong>.<br>
                Predikat: <strong>${predikatPrev}</strong> → <strong>${predikatNow}</strong>.<br>
                Pertahankan performa ini!
            `;
        } else if (diff < 0) {
            insightBox.classList.add('insight-danger');
            icon.classList.add('mdi-alert-circle-outline');
            message = `
                ⚠️ <strong>Pencapaian menurun</strong> sebesar <strong>${Math.abs(diff).toFixed(1)}%</strong>
                dari <strong>${periodePrev}</strong> ke <strong>${periodeNow}</strong>.<br>
                Predikat: <strong>${predikatPrev}</strong> → <strong>${predikatNow}</strong>.<br>
                Segera evaluasi dan perbaiki strategi kerja Anda.
            `;
        } else {
            insightBox.classList.add('insight-info');
            icon.classList.add('mdi-information-outline');
            message = `
                ℹ️ <strong>Pencapaian stabil</strong> antara <strong>${periodePrev}</strong> dan <strong>${periodeNow}</strong>.<br>
                Predikat tetap: <strong>${predikatNow}</strong>. Jaga konsistensi.
            `;
        }

        insightText.innerHTML = message;
        insightContainer.style.display = 'block';
    } else {
        insightBox.classList.add('insight-info');
        icon.classList.add('mdi-information-outline');
        insightText.innerHTML = 'Belum cukup data untuk membandingkan periode.';
        insightContainer.style.display = 'block';
    }
</script>