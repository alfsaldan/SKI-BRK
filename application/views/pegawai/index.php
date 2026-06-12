<!-- ============================================================== -->
<!-- Start Page Content here -->
<!-- ============================================================== -->
<?php
/**
 * @var array $periode_list
 * @var object $pegawai_detail
 * @var string $periode_awal
 * @var string $periode_akhir
 * @var array $indikator_by_jabatan
 */
?>
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

    /* Custom Scrollable Table untuk Form Penilaian */
    .table-scrollable {
        max-height: 600px;
        overflow-y: auto;
    }

    #tabel-penilaian thead th {
        position: sticky;
        top: 0;
        z-index: 15;
        background-color: #2E7D32 !important;
    }

    #tabel-penilaian tfoot td {
        position: sticky;
        bottom: 0;
        z-index: 15;
        background-color: #2E7D32 !important;
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
            <div class="card mt-0 shadow-sm border-0">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="text-success font-weight-bold mb-0">
                            <i class="mdi mdi-chart-line mr-2"></i> Grafik Pencapaian Nilai Akhir
                        </h5>
                        <div class="form-inline">
                            <label for="filterTahunGrafik" class="mr-2">Periode:</label>
                            <select id="filterTahunGrafik" class="form-control form-control-sm"
                                style="width: 150px;"></select>
                        </div>
                    </div>
                    <div id="grafikWrapper" style="position: relative; min-height: 120px;">
                        <canvas id="grafikPencapaian" height="80"></canvas>
                        <div id="grafikMessage" class="text-center text-muted p-4"
                            style="display: none; position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); width: 100%;">
                            <!-- Pesan akan diisi oleh JavaScript -->
                        </div>
                    </div>

                    <!-- Keterangan Predikat & Skala (Horizontal) -->
                    <div class="mt-1 pt-3 border-top">
                        <div class="d-flex flex-wrap align-items-center justify-content-center"
                            style="gap: 1rem; font-size: 0.8rem;">
                            <div class="d-flex align-items-center">
                                <span
                                    style="width: 12px; height: 12px; background-color: #dc3545; border-radius: 50%; display: inline-block; margin-right: 8px;"></span>
                                <small><strong>Minus</strong> (&lt;80%)</small>
                            </div>
                            <div class="d-flex align-items-center">
                                <span
                                    style="width: 12px; height: 12px; background-color: #ffc107; border-radius: 50%; display: inline-block; margin-right: 8px;"></span>
                                <small><strong>Fair</strong> (80% - &lt;90%)</small>
                            </div>
                            <div class="d-flex align-items-center">
                                <span
                                    style="width: 12px; height: 12px; background-color: #17a2b8; border-radius: 50%; display: inline-block; margin-right: 8px;"></span>
                                <small><strong>Good</strong> (90% - &lt;110%)</small>
                            </div>
                            <div class="d-flex align-items-center">
                                <span
                                    style="width: 12px; height: 12px; background-color: #28a745; border-radius: 50%; display: inline-block; margin-right: 8px;"></span>
                                <small><strong>Very Good</strong> (110% - &lt;120%)</small>
                            </div>
                            <div class="d-flex align-items-center">
                                <span
                                    style="width: 12px; height: 12px; background-color: #198754; border-radius: 50%; display: inline-block; margin-right: 8px;"></span>
                                <small><strong>Excellent</strong> (120% - 130%)</small>
                            </div>
                        </div>
                    </div>

                </div>

                <!-- ========== INSIGHT OTOMATIS ========== -->
                <div class="card-footer bg-light border-top p-3 animate-fade-delay" id="insightContainer"
                    style="display: none;">
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

                // urutkan $periode_list
                usort($periode_list, function ($a, $b) {
                    $year_a = date('Y', strtotime($a->periode_awal));
                    $year_b = date('Y', strtotime($b->periode_awal));

                    // 1. Urutkan berdasarkan tahun (terbaru di atas)
                    if ($year_a !== $year_b) {
                        return $year_b <=> $year_a;
                    }

                    // Cek apakah $a adalah rekap tahunan
                    $a_is_rekap = (
                        (isset($a->is_rekap_otomatis) && $a->is_rekap_otomatis) ||
                        (date('m-d', strtotime($a->periode_awal)) == '01-01' && date('m-d', strtotime($a->periode_akhir)) == '12-31')
                    );

                    // Cek apakah $b adalah rekap tahunan
                    $b_is_rekap = (
                        (isset($b->is_rekap_otomatis) && $b->is_rekap_otomatis) ||
                        (date('m-d', strtotime($b->periode_awal)) == '01-01' && date('m-d', strtotime($b->periode_akhir)) == '12-31')
                    );

                    // 2. Jika tahun sama, dahulukan yang rekap tahunan
                    if ($a_is_rekap !== $b_is_rekap) {
                        return $a_is_rekap ? -1 : 1;
                    }

                    // 3. Jika keduanya bukan rekap atau keduanya rekap, urutkan berdasarkan tanggal awal (terlama di atas)
                    return strtotime($a->periode_awal) <=> strtotime($b->periode_awal);
                });

                function formatTanggalIndonesia($tanggal, $bulan_indonesia)
                {
                    $tgl = date('d', strtotime($tanggal));
                    $bln = $bulan_indonesia[(int) date('m', strtotime($tanggal))];
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
                                        <h5 class="text-primary mb-3 font-weight-bold"><i
                                                class="mdi mdi-account-circle-outline mr-2"></i>Detail Pegawai</h5>
                                        <ul class="list-group list-group-flush">
                                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                                <span class="text-dark font-weight-medium">NIK</span>
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
                                                <span class="text-dark font-weight-medium">Unit Kantor</span>
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
                                        <div class="form-inline mb-2">
                                            <label class="mr-2 text-dark font-weight-medium"><b>Periode
                                                    Penilaian:</b></label>
                                            <!-- Input tanggal manual -->
                                            <input type="hidden" id="periode_awal" class="form-control mr-2"
                                                value="<?= $periode_awal ?? date('Y-01-01'); ?>">
                                            <span class="mr-2"></span>
                                            <input type="hidden" id="periode_akhir" class="form-control mr-2"
                                                value="<?= $periode_akhir ?? date('Y-12-31'); ?>">

                                            <!-- Dropdown periode history -->
                                            <div class="form-inline mb-2">
                                                <select id="periode_history" class="form-control w-auto ml-2">
                                                    <option value="">Pilih Periode</option>
                                                    <?php foreach ($periode_list as $p): ?>
                                                        <?php
                                                        // Abaikan item rekap otomatis
                                                        if (isset($p->is_rekap_otomatis) && $p->is_rekap_otomatis) {
                                                            continue;
                                                        }

                                                        $value = $p->periode_awal . '|' . $p->periode_akhir;
                                                        $selected = '';

                                                        $label = formatTanggalIndonesia($p->periode_awal, $bulan_indonesia) . ' s/d ' . formatTanggalIndonesia($p->periode_akhir, $bulan_indonesia);

                                                        // Logika pemilihan yang disederhanakan
                                                        if ($periode_awal === $p->periode_awal && $periode_akhir === $p->periode_akhir) {
                                                            $selected = 'selected';
                                                        }
                                                        ?>
                                                        <option value="<?= $value ?>" <?= $selected ?>>
                                                            <?= $label; ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                </select>

                                                <button type="button" id="btn-sesuaikan-periode"
                                                    class="btn btn-primary btn-sm ml-2">
                                                    Sesuaikan Periode
                                                </button>
                                            </div>

                                        </div>

                                        <p class="mt-2 text-dark font-weight-medium">
                                            <b>Unit Kantor Penilai:</b> <?= $pegawai_detail->unit_kantor ?? '-'; ?>
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
                                                <span class="badge badge-info badge-pill">
                                                    <?= $pegawai_detail->penilai1_nik ?? '-' ?>
                                                </span>
                                            </li>
                                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                                <span class="text-dark font-weight-medium">Nama</span>
                                                <span class="text-dark"><?= $pegawai_detail->penilai1_nama ?? '-' ?></span>
                                            </li>
                                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                                <span class="text-dark font-weight-medium">Jabatan</span>
                                                <span
                                                    class="text-dark"><?= $pegawai_detail->penilai1_jabatan_detail ?? '-' ?></span>
                                            </li>
                                            <li class="list-group-item d-flex justify-content-end">
                                                <button class="btn btn-sm btn-info" type="button" data-toggle="collapse"
                                                    data-target="#formPenilai1">
                                                    <i class="mdi mdi-pencil"></i> Ubah Penilai I
                                                </button>
                                            </li>
                                        </ul>

                                        <!-- Form ubah penilai I -->
                                        <div id="formPenilai1" class="collapse mt-2">
                                            <form action="<?= base_url('pegawai/updatePenilai') ?>" method="post">
                                                <input type="hidden" name="nik_pegawai" value="<?= $pegawai_detail->nik ?>">
                                                <input type="hidden" name="tipe_penilai" value="1">

                                                <div class="form-group">
                                                    <label for="penilai1_select" class="text-dark font-weight-medium mb-1">
                                                        Pilih Penilai I
                                                    </label>
                                                    <select name="penilai_nik" id="penilai1_select"
                                                        class="form-control select2" required>
                                                        <option value="">-- Memuat... --</option>
                                                    </select>
                                                </div>

                                                <button type="submit" class="btn btn-sm btn-success">
                                                    <i class="mdi mdi-content-save"></i> Simpan Penilai I
                                                </button>
                                            </form>
                                        </div>
                                    </div>

                                    <!-- Penilai II -->
                                    <div class="col-md-6 mb-3">
                                        <h5 class="text-warning mb-3 font-weight-bold">
                                            <i class="mdi mdi-account-check-outline mr-2"></i>Penilai II
                                        </h5>

                                        <ul class="list-group list-group-flush">
                                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                                <span class="text-dark font-weight-medium">NIK</span>
                                                <span class="badge badge-warning badge-pill">
                                                    <?= $pegawai_detail->penilai2_nik ?? '-' ?>
                                                </span>
                                            </li>
                                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                                <span class="text-dark font-weight-medium">Nama</span>
                                                <span class="text-dark"><?= $pegawai_detail->penilai2_nama ?? '-' ?></span>
                                            </li>
                                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                                <span class="text-dark font-weight-medium">Jabatan</span>
                                                <span
                                                    class="text-dark"><?= $pegawai_detail->penilai2_jabatan_detail ?? '-' ?></span>
                                            </li>
                                            <li class="list-group-item d-flex justify-content-end">
                                                <button class="btn btn-sm btn-warning" type="button" data-toggle="collapse"
                                                    data-target="#formPenilai2">
                                                    <i class="mdi mdi-pencil"></i> Ubah Penilai II
                                                </button>
                                            </li>
                                        </ul>

                                        <!-- Form ubah penilai II -->
                                        <div id="formPenilai2" class="collapse mt-2">
                                            <form action="<?= base_url('pegawai/updatePenilai') ?>" method="post">
                                                <input type="hidden" name="nik_pegawai" value="<?= $pegawai_detail->nik ?>">
                                                <input type="hidden" name="tipe_penilai" value="2">

                                                <div class="form-group">
                                                    <label for="penilai2_select" class="text-dark font-weight-medium mb-1">
                                                        Pilih Penilai II
                                                    </label>
                                                    <select name="penilai_nik" id="penilai2_select"
                                                        class="form-control select2" required>
                                                        <option value="">-- Memuat... --</option>
                                                    </select>
                                                </div>

                                                <button type="submit" class="btn btn-sm btn-success">
                                                    <i class="mdi mdi-content-save"></i> Simpan Penilai II
                                                </button>
                                            </form>
                                        </div>
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

                <!-- 🟢 BANNER STATUS PERSETUJUAN -->
                <?php if (isset($is_verified) && $is_verified): ?>
                    <div class="col-12">
                        <div class="alert alert-success bg-success text-white border-0" role="alert"
                            style="background-image: linear-gradient(to right, #28a745, #218838); box-shadow: 0 4px 15px rgba(40, 167, 69, 0.3);">
                            <div class="d-flex align-items-center">
                                <i class="mdi mdi-check-decagram mdi-24px mr-2"></i>
                                <div>
                                    <h5 class="alert-heading mb-0 text-white">STATUS SKI PERIODE
                                        <?= date('d M Y', strtotime($periode_awal)) ?> -
                                        <?= date('d M Y', strtotime($periode_akhir)) ?>
                                    </h5>
                                    <span class="font-weight-bold">SUDAH SELESAI DAN DISETUJUI</span>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
                <!-- Tabel Penilaian -->
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="text-success fw-bold mb-3">
                                    <i class="mdi mdi-account-outline me-2"></i> Form Penilaian
                                </h5>
                                <a href="<?= base_url('Pegawai/downloadDataPegawai?nik=' . ($pegawai_detail->nik ?? '') . '&awal=' . $periode_awal . '&akhir=' . $periode_akhir) ?>"
                                    class="btn btn-success mt-2 mb-2 font-weight-bold"
                                    style="background-color:#217346; color:#fff;">
                                    <i class="mdi mdi-file-excel"></i> Download Excel
                                </a>
                                <button type="button" class="btn btn-primary mt-2 mb-2 font-weight-bold ml-2" data-toggle="modal" data-target="#modalUploadBukti">
                                    <i class="mdi mdi-upload"></i> Upload Bukti PDF
                                </button>
                                <?php if (!$is_locked && !$is_verified): ?>
                                    <div class="mb-3">
                                        <button class="btn btn-info btn-sm mr-2" onclick="addSasaranRow()"><i
                                                class="mdi mdi-plus"></i> Tambah Sasaran</button>
                                        <button class="btn btn-secondary btn-sm" onclick="addIndikatorRow()"><i
                                                class="mdi mdi-plus"></i> Tambah Indikator</button>
                                        <button type="button" id="btnUploadExcelPegawai" class="btn btn-success btn-sm ml-2">
                                            <i class="mdi mdi-file-excel"></i> Upload Indikator & Sasaran Awal
                                        </button>
                                    </div>
                                <?php endif; ?>
                                <div class="table-responsive table-scrollable">
                                    <table class="table table-bordered" id="tabel-penilaian">
                                        <thead style="background-color:#2E7D32;color:#fff;text-align:center;">
                                            <tr>
                                                <th>Perspektif</th>
                                                <th>Sasaran Kerja</th>

                                                <th class="text-center" style="min-width: 120px;">Bobot (%)</th>
                                                <th>Indikator</th>
                                                <th class="text-center" style="width: 120px;">Target</th>
                                                <th class="text-center" style="width: 80px;">Batas Waktu</th>
                                                <th class="text-center" style="width: 120px;">Realisasi</th>
                                                <th class="text-center" style="width: 120px;">Pencapaian (%)</th>
                                                <th class="text-center" style="width: 120px;">Nilai</th>
                                                <th class="text-center" style="width: 120px;">Nilai Dibobot</th>
                                                <th class="text-center" style="width: 150px;">Status Penilai 1</th>
                                                <th class="text-center" style="width: 150px;">Status Penilai 2</th>
                                                <th class="text-center" style="width: 120px;">Aksi</th>
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

                                                        // 🟢 Ambil status untuk Penilai 1 dari kolom `status`
                                                        $status = strtolower(trim($i->status ?? ''));

                                                        // Logika untuk Status Penilai 1
                                                        $statusClass = 'badge badge-danger';
                                                        $statusText = 'Belum Dinilai';

                                                        switch ($status) {
                                                            case 'ada catatan':
                                                                $statusClass = 'badge badge-warning';
                                                                $statusText = 'Ada Catatan';
                                                                break;
                                                            case 'disetujui':
                                                                $statusClass = 'badge badge-success';
                                                                $statusText = 'Disetujui';
                                                                break;
                                                        }

                                                        // 🟢 Ambil status untuk Penilai 2 dari kolom `status2`
                                                        $status2 = strtolower(trim($i->status2 ?? ''));

                                                        // Logika untuk Status Penilai 2
                                                        $status2Class = 'badge badge-danger';
                                                        $status2Text = 'Belum Dinilai';

                                                        switch ($status2) {
                                                            case 'ada catatan':
                                                                $status2Class = 'badge badge-warning';
                                                                $status2Text = 'Ada Catatan';
                                                                break;
                                                            case 'disetujui':
                                                                $status2Class = 'badge badge-success';
                                                                $status2Text = 'Disetujui';
                                                                break;
                                                        }

                                                        $is_row_approved = ($status === 'disetujui');
                                                        ?>

                                                        <tr data-id="<?= $id; ?>" data-sasaran-id="<?= $i->sasaran_id ?? '' ?>"
                                                            data-sasaran-nama="<?= htmlspecialchars($sasaran ?? '', ENT_QUOTES, 'UTF-8') ?>"
                                                            data-bobot="<?= $bobot ?? '' ?>" data-perspektif="<?= $persp ?? '' ?>"
                                                            data-indikator="<?= htmlspecialchars($indik ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                                                            <?php if ($first_persp_cell) { ?>
                                                                <td rowspan="<?= $persp_rows; ?>"
                                                                    style="vertical-align:middle;font-weight:600;background:#C8E6C9;">
                                                                    <?= $persp; ?>
                                                                </td>
                                                                <?php $first_persp_cell = false;
                                                            } ?>

                                                            <?php if ($first_sas_cell) { ?>
                                                                <td rowspan="<?= $sasaran_rows; ?>"
                                                                    style="vertical-align:middle;background:#E3F2FD;">
                                                                    <div class="d-flex justify-content-between align-items-center">
                                                                        <span><?= $sasaran; ?></span>
                                                                        <?php if (!$is_locked && !$is_verified && !$is_row_approved && !empty($i->sasaran_id)): ?>
                                                                            <button type="button"
                                                                                class="btn btn-xs btn-outline-primary ml-1 p-1"
                                                                                onclick="editSasaran('<?= $i->sasaran_id ?>', this)"
                                                                                data-text="<?= htmlspecialchars($sasaran ?? '', ENT_QUOTES, 'UTF-8') ?>"
                                                                                data-toggle="tooltip" title="Edit Sasaran"><i
                                                                                    class="mdi mdi-pencil"></i></button>
                                                                        <?php endif; ?>
                                                                    </div>
                                                                </td>
                                                                <?php $first_sas_cell = false;
                                                            } ?>

                                                            <td class="text-center align-middle">
                                                                <input type="number"
                                                                    class="form-control form-control-sm text-center bobot"
                                                                    value="<?= $bobot; ?>" min="5" data-toggle="tooltip"
                                                                    data-prev-value="<?= $bobot; ?>" data-placement="bottom"
                                                                    data-html="true"
                                                                    data-template='<div class="tooltip tooltip-kuning" role="tooltip"><div class="arrow"></div><div class="tooltip-inner"></div></div>'
                                                                    title="<i class='mdi mdi-information-outline'></i><br>Minimal nilai 5."
                                                                    <?= ($is_locked || $is_verified || $is_row_approved) ? 'readonly' : ''; ?>>
                                                            </td>
                                                            <td>
                                                                <div class="d-flex justify-content-between align-items-center">
                                                                    <span><?= $indik; ?></span>
                                                                    <?php if (!$is_locked && !$is_verified && !$is_row_approved): ?>
                                                                        <div class="d-flex">
                                                                            <button type="button"
                                                                                class="btn btn-xs btn-outline-primary ml-1 p-1"
                                                                                onclick="editIndikator('<?= $id ?>', this)"
                                                                                data-text="<?= htmlspecialchars($indik ?? '', ENT_QUOTES, 'UTF-8') ?>"
                                                                                data-toggle="tooltip" title="Edit Indikator & Bobot"><i
                                                                                    class="mdi mdi-pencil"></i></button>
                                                                            <button type="button"
                                                                                class="btn btn-xs btn-outline-danger ml-1 p-1"
                                                                                onclick="deleteIndikator('<?= $id ?>')"
                                                                                data-toggle="tooltip" title="Hapus Indikator"><i
                                                                                    class="mdi mdi-trash-can"></i></button>
                                                                        </div>
                                                                    <?php endif; ?>
                                                                </div>
                                                            </td>

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
                                                                    <input type="text" class="form-control target-input text-center"
                                                                        style="min-width:150px;" value="<?= $i->target ?? ''; ?>"
                                                                        <?= ($is_locked || $is_verified || $is_row_approved) ? 'readonly' : ''; ?>>
                                                                    <div class="format-currency text-muted small"></div>
                                                                </div>
                                                            </td>
                                                            <td class="text-center align-middle">
                                                                <input type="date" class="form-control batas-waktu"
                                                                    style="min-width:120px;"
                                                                    value="<?= ($i->batas_waktu && $i->batas_waktu != '0000-00-00') ? $i->batas_waktu : ''; ?>"
                                                                    style="min-width:120px;"
                                                                    value="<?= (!empty($i->batas_waktu) && strpos($i->batas_waktu, '0000') === false) ? date('Y-m-d', strtotime($i->batas_waktu)) : ''; ?>"
                                                                    <?= ($is_locked || $is_verified || $is_row_approved) ? 'readonly' : ''; ?>>
                                                            </td>
                                                            <td class="text-center align-middle">
                                                                <div class="currency-wrapper">
                                                                    <?php if (!$is_locked && !$is_verified && !$is_row_approved): ?>
                                                                        <input type="text" class="form-control text-center realisasi-input"
                                                                            value="<?= $i->realisasi ?? ''; ?>" style="min-width:150px;">
                                                                    <?php else: ?>
                                                                        <input type="text" class="form-control text-center realisasi-input"
                                                                            value="<?= $i->realisasi ?? ''; ?>" style="min-width:150px;"
                                                                            readonly>
                                                                    <?php endif; ?>
                                                                    <div class="format-currency text-muted small"></div>
                                                                </div>
                                                            </td>

                                                            <td class="text-center align-middle">
                                                                <input type="text"
                                                                    class="form-control form-control-sm text-center pencapaian-output"
                                                                    readonly style="min-width:50px;">
                                                            </td>
                                                            <td class="text-center align-middle">
                                                                <input type="text"
                                                                    class="form-control form-control-sm text-center nilai-output"
                                                                    readonly style="min-width:60px;">
                                                            </td>
                                                            <td class="text-center align-middle">
                                                                <input type="text"
                                                                    class="form-control form-control-sm text-center nilai-bobot-output"
                                                                    readonly style="min-width:50px;">
                                                            </td>


                                                            <td class="text-center align-middle">
                                                                <?php if ($statusText === 'Ada Catatan'): ?>
                                                                    <a href="javascript:void(0);"
                                                                        class="<?= $statusClass; ?> px-2 py-1 shadow-sm badge-btn-catatan"
                                                                        data-toggle="tooltip" title="Klik untuk melihat catatan"
                                                                        onclick="lihatCatatan('<?= $id ?>', '<?= $pegawai_detail->penilai1_nik ?? '' ?>')">
                                                                        <i class="mdi mdi-eye-outline mr-1"></i><?= $statusText; ?>
                                                                    </a>
                                                                <?php else: ?>
                                                                    <span class="<?= $statusClass; ?>"><?= $statusText; ?></span>
                                                                <?php endif; ?>
                                                            </td>
                                                            <td class="text-center align-middle">
                                                                <?php if ($status2Text === 'Ada Catatan'): ?>
                                                                    <a href="javascript:void(0);"
                                                                        class="<?= $status2Class; ?> px-2 py-1 shadow-sm badge-btn-catatan"
                                                                        data-toggle="tooltip" title="Klik untuk melihat catatan"
                                                                        onclick="lihatCatatan('<?= $id ?>', '<?= $pegawai_detail->penilai2_nik ?? '' ?>')">
                                                                        <i class="mdi mdi-eye-outline mr-1"></i><?= $status2Text; ?>
                                                                    </a>
                                                                <?php else: ?>
                                                                    <span class="<?= $status2Class; ?>"><?= $status2Text; ?></span>
                                                                <?php endif; ?>
                                                            </td>
                                                            <td class="text-center align-middle">
                                                                <?php if (!$is_locked && !$is_verified && !$is_row_approved): ?>
                                                                    <button type="button"
                                                                        class="btn btn-sm btn-primary simpan-penilaian w-100">Simpan</button>
                                                                <?php else: ?>
                                                                    <button type="button" class="btn btn-sm btn-secondary w-100"
                                                                        disabled>Terkunci</button>
                                                                <?php endif; ?>

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
                                                    <td colspan="3"></td>
                                                </tr>
                                                <?php
                                            }
                                            if (!$printed_any) { ?>
                                                <tr>
                                                    <td colspan="13" class="text-center">Tidak ada indikator untuk jabatan ini
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
                                                <td colspan="3"></td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Modal Lihat Catatan Indikator -->
                <div class="modal fade" id="modalLihatCatatan" tabindex="-1">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header bg-warning">
                                <h5 class="modal-title text-white">Catatan Penilai</h5>
                                <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
                            </div>
                            <div class="modal-body">
                                <div class="table-responsive">
                                    <table class="table table-bordered" id="tabel-catatan-indikator">
                                        <thead class="bg-light">
                                            <tr>
                                                <th>Tanggal</th>
                                                <th>Penilai</th>
                                                <th>Catatan</th>
                                            </tr>
                                        </thead>
                                        <tbody></tbody>
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
                                            if (!empty($budaya)):
                                                foreach ($budaya as $b):
                                                    $panduanList = json_decode($b['panduan_perilaku'], true);

                                                    if (is_array($panduanList)):
                                                        foreach ($panduanList as $pIndex => $p):
                                                            // Key sesuai format JSON nilai_budaya
                                                            $nilaiKey = "budaya_{$no}_{$pIndex}";
                                                            $nilai = isset($budaya_nilai[$nilaiKey]) ? (int) $budaya_nilai[$nilaiKey] : 0;

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

                <!-- ================== FORM NILAI AKHIR ================== -->
                <!-- Nilai Akhir & Catatan -->
                <div class="card mt-0">
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
                                    <input type="text" id="bobot-sasaran" class="form-control form-control-sm text-center"
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
                                    <input type="text" id="bobot-budaya" class="form-control form-control-sm text-center"
                                        value="5%" readonly>
                                </td>
                                <td class="text-center" id="nilai-budaya">
                                    <?= $nilai_akhir['nilai_budaya'] ?? '-' ?>
                                </td>
                            </tr>
                            <tr>
                                <th>Share KPI</th>
                                <td class="text-center" id="share-kpi">
                                    <input type="text" id="share-kpi-value" class="form-control form-control-sm text-center"
                                        min="0" max="5" value="<?= $nilai_akhir['share_kpi_value'] ?? 0 ?>"
                                        oninput="if(this.value > 5) this.value = 5; if(this.value < 0) this.value = 0; hitungShareKPI()"
                                        data-toggle="tooltip" data-placement="bottom" data-html="true"
                                        data-template='<div class="tooltip tooltip-kuning" role="tooltip"><div class="arrow"></div><div class="tooltip-inner"></div></div>'
                                        title="<i class='mdi mdi-information-outline'></i><br>Pastikan nilai share KPI sesuai dengan data KPI Direksi">
                                </td>
                                <td>x Bobot % Share KPI</td>
                                <td>
                                    <div class="input-group input-group-sm" style="width: 100%;">
                                        <input type="number" id="bobot-share-kpi" class="form-control f
                                            orm-control-sm text-center"
                                            value="<?= $nilai_akhir['bobot_share_kpi'] ?? 0 ?>" min="0" max="95"
                                            style="height: 30px;"
                                            oninput="if(this.value > 95) this.value = 95; if(this.value < 0) this.value = 0; hitungShareKPI()"
                                            data-toggle="tooltip" data-placement="bottom" data-html="true"
                                            data-template='<div class="tooltip tooltip-kuning" role="tooltip"><div class="arrow"></div><div class="tooltip-inner"></div></div>'
                                            title="<i class='mdi mdi-information-outline'></i><br>Pastikan bobot share KPI sesuai dengan data KPI Direksi">
                                        <span class="input-group-text" style="height: 30px; line-height: 1;">%</span>
                                    </div>
                                </td>
                                <td class="text-center" id="share-kpi-nilai">
                                    <?= $nilai_akhir['share_kpi'] ?? '-' ?>
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
                                    <input type="number" min="0" max="1" class="form-control form-control-sm text-center"
                                        id="fraud-input" value="<?= $nilai_akhir['fraud'] ?? 0 ?>" readonly>
                                </td>
                            </tr>
                            <tr>
                                <th colspan="4" class="text-right">
                                    Koefisien Nilai<br>
                                </th>
                                <td>
                                    <div class="input-group input-group-sm">
                                        <input type="number" name="koefisien" id="koefisien-input"
                                            class="form-control text-center" max="100" min="70" step="5"
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
                                    <?php if (!$is_locked && !$is_verified): ?>
                                        <button id="btn-simpan-nilai-akhir" class="btn btn-primary">
                                            <i class="mdi mdi-content-save"></i> Simpan Nilai Akhir
                                        </button>
                                    <?php else: ?>
                                        <button class="btn btn-secondary" disabled data-toggle="tooltip"
                                            title="Penilaian sudah dikunci atau diverifikasi">
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
                        <h5 class="card-title">Formulir feedback
                            <small> (antara pegawai dengan administrator MSDI)</small>

                        </h5>
                        <form id="form-catatan-pegawai">
                            <input type="hidden" name="nik" id="nik" value="<?= $pegawai_detail->nik;
                            ; ?>">

                            <div class="mb-3">
                                <label for="catatan-pegawai" class="form-label"><b>Pegawai</b></label>
                                <textarea class="form-control" name="catatan" id="catatan-pegawai" rows="3"
                                    required></textarea>
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
                                    if (!empty($catatan_list)):
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
                                    endif; ?>
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
                            <small> (antara pegawai dengan penilai 1 dan penilai 2)</small>
                        </h5>

                        <!-- Box Chat -->
                        <div id="chat-box" style="max-height:400px; overflow-y:auto; border:1px solid #ddd; padding:10px;">
                            <!-- Pesan via AJAX -->
                        </div>

                        <!-- Form Kirim -->
                        <form id="form-chat" class="mt-2 d-flex">
                            <input type="hidden" name="nik_pegawai" value="<?= $pegawai_detail->nik ?>">
                            <input type="text" name="pesan" id="input-pesan" class="form-control mr-2"
                                placeholder="Tulis pesan..." autocomplete="off">
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

<!-- Modal Upload Excel -->
<div class="modal fade" id="modalUploadExcelPegawai" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Upload Indikator Kinerja Pegawai</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <!-- Step 1: Upload -->
                <div id="stepUploadPegawai">
                    <p>Silakan pilih file Excel (.xls atau .xlsx) sesuai dengan template yang disediakan.</p>
                    <!-- NOTE: Please adjust the template path -->
                    <a href="<?= base_url('uploads/templates/Template_UploadDataIndikator_Perunit_jabatan.xlsx') ?>"
                        class="btn btn-sm btn-info mb-3"><i class="mdi mdi-download"></i> Download Template</a>
                    <form id="formUploadExcelPegawai" enctype="multipart/form-data">
                        <input type="hidden" name="nik" value="<?= $pegawai_detail->nik ?? '' ?>">
                        <input type="hidden" name="unit_kerja" value="<?= $pegawai_detail->unit_kerja ?? '' ?>">
                        <input type="hidden" name="jabatan" value="<?= $pegawai_detail->jabatan ?? '' ?>">
                        <div class="form-group">
                            <label for="excel_file_pegawai">Pilih File Excel</label>
                            <input type="file" class="form-control-file" id="excel_file_pegawai" name="excel_file"
                                accept=".xls,.xlsx" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Upload dan Preview</button>
                    </form>
                </div>

                <!-- Step 2: Preview -->
                <div id="stepPreviewPegawai" style="display:none;">
                    <h5><i class="mdi mdi-file-find-outline"></i> Preview Data</h5>
                    <p>Berikut adalah data yang berhasil diparsing dari file Excel. Periksa kembali sebelum menyimpan.
                    </p>
                    <div id="preview-table-container-pegawai" class="table-responsive" style="max-height: 400px;"></div>
                    <div id="preview-summary-pegawai" class="mt-3"></div>
                    <div class="mt-4">
                        <button type="button" class="btn btn-secondary" id="btn-back-to-upload-pegawai">Kembali</button>
                        <button type="button" class="btn btn-success" id="btn-save-parsed-pegawai">Simpan Data ke
                            Database</button>
                    </div>
                </div>

                <!-- Step 3: Success -->
                <div id="stepSuccessPegawai" style="display:none;">
                    <div class="text-center">
                        <i class="mdi mdi-check-circle-outline text-success" style="font-size: 4rem;"></i>
                        <h4 class="mt-2">Upload Berhasil!</h4>
                        <p id="success-message-pegawai"></p>
                        <button type="button" class="btn btn-primary" onclick="location.reload()">Tutup dan Muat
                            Ulang</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- End Page Content here -->
<!-- ============================================================== -->

<script src="<?= base_url('assets/libs/sweetalert2/sweetalert2@11.js') ?>"></script>
<script src="<?= base_url('assets/libs/datatables/jquery.dataTables.min.js') ?>"></script>
<script src="<?= base_url('assets/libs/datatables/dataTables.responsive.min.js') ?>"></script>
<script src="<?= base_url('assets/js/jquery-3.6.0.min.js') ?>"></script>
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
    // JS for Excel Upload
    $(document).ready(function () {
        // Show modal on button click
        $('#btnUploadExcelPegawai').on('click', function () {
            // Reset modal to step 1
            $('#formUploadExcelPegawai')[0].reset();
            $('#stepUploadPegawai').show();
            $('#stepPreviewPegawai').hide();
            $('#stepSuccessPegawai').hide();
            $('#modalUploadExcelPegawai').modal('show');
        });

        // Back button from preview to upload
        $('#btn-back-to-upload-pegawai').on('click', function () {
            $('#stepUploadPegawai').show();
            $('#stepPreviewPegawai').hide();
        });

        // Handle form submission for upload and preview
        $('#formUploadExcelPegawai').on('submit', function (e) {
            e.preventDefault();
            var formData = new FormData(this);

            Swal.fire({
                title: 'Mengunggah file...',
                text: 'Mohon tunggu, file sedang diproses.',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            $.ajax({
                url: '<?= base_url("ExcelImport/uploadIndikatorKinerjaPegawai") ?>',
                type: 'POST',
                data: formData,
                contentType: false,
                processData: false,
                dataType: 'json',
                success: function (response) {
                    Swal.close();
                    if (response && response.data) {
                        // Store parsed data
                        $('#btn-save-parsed-pegawai').data('parsed-data', response);

                        // Build preview table
                        let tableHtml = '<table class="table table-bordered">';
                        tableHtml += '<thead style="background-color:#2E7D32; color:#fff; text-align:center;"><tr><th style="width:25%;">Perspektif</th><th style="width:30%;">Sasaran Kerja</th><th style="width:35%;">Indikator</th><th style="width:10%;">Bobot (%)</th></tr></thead><tbody>';

                        if (Object.keys(response.data).length === 0) {
                            tableHtml += '<tr><td colspan="4" class="text-center">Tidak ada data valid yang ditemukan di file.</td></tr>';
                        } else {
                            let grandTotal = 0;
                            const perspektif_order = [
                                "Keuangan (F)",
                                "Pelanggan (C)",
                                "Proses Internal (IP)",
                                "Pembelajaran & Pertumbuhan (LG)"
                            ];

                            const dataKeys = Object.keys(response.data);
                            dataKeys.sort((a, b) => {
                                const posA = perspektif_order.indexOf(a);
                                const posB = perspektif_order.indexOf(b);
                                return (posA === -1 ? 99 : posA) - (posB === -1 ? 99 : posB);
                            });

                            dataKeys.forEach(persp => {
                                const sasaranList = response.data[persp];
                                tableHtml += `<tr style="background-color:#C8E6C9; font-weight:bold;"><td colspan="4">${persp}</td></tr>`;

                                let subtotal = 0;
                                let noSasaran = 1;
                                for (const sasaran in sasaranList) {
                                    tableHtml += `<tr style="background-color:#BBDEFB; font-weight:bold;"><td></td><td colspan="3">${noSasaran++}. ${sasaran}</td></tr>`;

                                    const indikatorList = sasaranList[sasaran];
                                    let noIndikator = 1;
                                    indikatorList.forEach(ind => {
                                        subtotal += parseFloat(ind.bobot) || 0;
                                        tableHtml += `<tr>`;
                                        tableHtml += `<td></td>`; // empty for perspektif
                                        tableHtml += `<td></td>`; // empty for sasaran
                                        tableHtml += `<td>${noIndikator++}. ${ind.indikator}</td>`;
                                        tableHtml += `<td class="text-center">${ind.bobot}</td>`;
                                        tableHtml += `</tr>`;
                                    });
                                }
                                grandTotal += subtotal;
                                tableHtml += `<tr style="background-color:#E0E0E0; font-weight:bold;"><td colspan="3" class="text-right">Sub Total Bobot ${persp}</td><td class="text-center">${subtotal.toFixed(2)}</td></tr>`;
                            });

                            tableHtml += `<tr style="background-color:#9CCC65; font-weight:bold;"><td colspan="3" class="text-right">TOTAL BOBOT KESELURUHAN</td><td class="text-center">${grandTotal.toFixed(2)}</td></tr>`;
                        }
                        tableHtml += '</tbody></table>';
                        $('#preview-table-container-pegawai').html(tableHtml);

                        // Build summary
                        let summaryHtml = '<h6>Ringkasan:</h6>';
                        if (response.errors && response.errors.length > 0) {
                            summaryHtml += '<div class="alert alert-danger mt-2"><strong>Ditemukan Error saat parsing:</strong><br>' + response.errors.join('<br>') + '</div>';
                        } else {
                            summaryHtml += '<p class="text-success">Tidak ada error ditemukan. Data siap untuk disimpan.</p>';
                        }
                        $('#preview-summary-pegawai').html(summaryHtml);

                        // Switch to preview step
                        $('#stepUploadPegawai').hide();
                        $('#stepPreviewPegawai').show();
                    } else {
                        Swal.fire('Gagal', response.message || 'Gagal memproses file Excel.', 'error');
                    }
                },
                error: function (xhr, status, error) {
                    Swal.close();
                    Swal.fire('Error', 'Terjadi kesalahan saat mengunggah file. ' + error, 'error');
                }
            });
        });

        // Handle saving parsed data
        $('#btn-save-parsed-pegawai').on('click', function () {
            const parsedData = $(this).data('parsed-data');
            if (!parsedData) {
                Swal.fire('Error', 'Tidak ada data untuk disimpan.', 'error');
                return;
            }

            Swal.fire({
                title: 'Menyimpan data...',
                text: 'Mohon tunggu, data sedang disimpan ke database.',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            $.ajax({
                url: '<?= base_url("ExcelImport/saveParsedDataPegawai") ?>',
                type: 'POST',
                contentType: 'application/json',
                data: JSON.stringify(parsedData),
                dataType: 'json',
                success: function (response) {
                    Swal.close();
                    if (response && response.success) {
                        let result = response.result;
                        let msg = `Berhasil! ${result.sasaran} sasaran baru, ${result.indikator} indikator baru ditambahkan. Ditemukan ${result.duplicates} duplikat.`;
                        $('#success-message-pegawai').text(msg);
                        $('#stepPreviewPegawai').hide();
                        $('#stepSuccessPegawai').show();
                    } else {
                        Swal.fire('Gagal', response.message || 'Gagal menyimpan data.', 'error');
                    }
                },
                error: function (xhr, status, error) {
                    Swal.close();
                    Swal.fire('Error', 'Terjadi kesalahan saat menyimpan data. ' + error, 'error');
                }
            });
        });
    });

    document.addEventListener('DOMContentLoaded', function () {
        // Cek jika ada parameter periode_changed di URL untuk notifikasi
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.has('periode_changed')) {
            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: 'success',
                title: 'Periode berhasil diubah',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true
            });

            // Hapus parameter dari URL agar notifikasi tidak muncul lagi saat refresh manual
            const url = new URL(window.location);
            url.searchParams.delete('periode_changed');
            history.replaceState(null, '', url.toString());
        }

        const nik = document.getElementById('nik')?.value;
        const periodeAwal = document.getElementById('periode_awal');
        const periodeAkhir = document.getElementById('periode_akhir');
        const periodeHistory = document.getElementById('periode_history');
        const koefInput = document.getElementById('koefisien-input');

        // default dari server (jaga-jaga kalau kosong)
        if (!periodeAwal.value) periodeAwal.value = "<?= $periode_awal ?? date('Y-01-01'); ?>";
        if (!periodeAkhir.value) periodeAkhir.value = "<?= $periode_akhir ?? date('Y-12-31'); ?>";

        // validasi supaya periode akhir tidak lebih kecil dari awal
        periodeAwal.addEventListener('change', function () {
            if (periodeAkhir.value < this.value) periodeAkhir.value = this.value;
        });
        periodeAkhir.addEventListener('change', function () {
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
        // Simpan index terpilih saat halaman dimuat
        let previousPeriodeIndex = periodeHistory.selectedIndex;

        periodeHistory.addEventListener('change', function () {
            const selectedIndex = this.selectedIndex;
            const selectedOption = this.options[selectedIndex];

            // Jangan lakukan apa-apa jika memilih placeholder "Pilih Periode"
            if (!this.value) {
                previousPeriodeIndex = selectedIndex;
                return;
            }

            const periodeText = selectedOption.text;
            const val = this.value;
            const nik = "<?= $pegawai_detail->nik ?? '' ?>";

            Swal.fire({
                title: 'Ganti Periode Penilaian?',
                html: `Anda akan mengubah periode ke:<br><b>${periodeText}</b><br><br>Perubahan akan memuat ulang halaman.`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Ya, Ganti',
                cancelButtonText: 'Batal',
                confirmButtonColor: '#2E7D32', // Warna hijau BRK
                cancelButtonColor: '#d33'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Proses navigasi
                    let url;
                    const parts = val.split('|');
                    const awal = parts[0];
                    const akhir = parts[1];

                    // URL disederhanakan, tidak ada lagi parameter 'tahunan'
                    url = `<?= base_url("Pegawai/index") ?>?nik=${nik}&awal=${awal}&akhir=${akhir}&periode_changed=1`;

                    // Tampilkan loading sebelum navigasi
                    Swal.fire({
                        title: 'Memuat data...',
                        text: 'Mohon tunggu sebentar.',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    window.location.href = url;

                } else {
                    // Jika batal, kembalikan dropdown ke pilihan sebelumnya
                    this.selectedIndex = previousPeriodeIndex;
                }
            });
        });


        // Reset dropdown jika user ubah tanggal manual
        periodeAwal.addEventListener('input', () => periodeHistory.value = '');
        periodeAkhir.addEventListener('input', () => periodeHistory.value = '');

        // ===== Tombol Sesuaikan Periode =====
        document.getElementById('btn-sesuaikan-periode').addEventListener('click', function () {
            const awal = periodeAwal.value;
            const akhir = periodeAkhir.value;
            window.location.href = `<?= base_url("Pegawai") ?>?awal=${awal}&akhir=${akhir}&periode_changed=1`;
        });

        // format angka
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

            if (target === 0) {
                if (realisasi === 0) {
                    pencapaian = 130; 
                } else {
                    pencapaian = 0; 
                }
            } else if (target <= 0.999) {
                pencapaian = (realisasi / target) * 100;
            } else {
                if (containsKeyword(keywords.rumus1, indikatorText)) {
                    pencapaian = ((target + (target - realisasi)) / target) * 100;
                    if (pencapaian < 0) pencapaian = 0;
                } else if (containsKeyword(keywords.rumus3, indikatorText)) {
                    pencapaian = ((realisasi - target) / Math.abs(target) + 1) * 100;
                    if (pencapaian < 0) pencapaian = 0;
                } else {
                    pencapaian = (realisasi / target) * 100;
                    if (pencapaian < 0) pencapaian = 0;
                }
            }

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

            row.dataset.rawPencapaian = pencapaian === "" ? "" : pencapaian;
            row.dataset.rawNilai = nilai === "" ? "" : nilai;
            row.dataset.rawNilaiBobot = nilaiBobot === "" ? "" : nilaiBobot;

            return {
                bobot,
                nilaiBobot: nilaiBobot === "" ? 0 : nilaiBobot, // Kembalikan nilai raw agar total akurat
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
            const totalSasaranEl = document.getElementById('total-sasaran');
            if (totalSasaranEl) {
                totalSasaranEl.textContent = formatAngka(totalNilai);
                window.totalNilaiUnroundedGlobal = totalNilai; // Simpan untuk perhitungan raw
            }

            // Jika ada Share KPI di UI, hitung nilai sasaran & nilai akhir berdasarkan share
            if (document.getElementById('share-kpi-value')) {
                try { hitungShareKPI(); } catch (e) { console.error('hitungShareKPI error', e); }
            } else {
                hitungNilaiAkhir();
            }
        }

        function hitungNilaiAkhir() {
            // Ambil nilai yang sudah dihitung sebelumnya oleh hitungTotal() dan hitungShareKPI()
            const fraud = parseFloat(document.getElementById("fraud-input").value) || 0;
            const koef = koefInput ? (parseFloat(koefInput.value) || 100) / 100 : 1;

            // Gunakan nilai raw dari sasaran (bila tersedia dari window)
            const sasaranScoreRaw = window.totalNilaiUnroundedGlobal || (parseFloat(document.getElementById('total-sasaran')?.textContent) || 0);
            const bobotSasaranRaw = parseFloat(document.getElementById('bobot-sasaran')?.value) || 95;
            const nilaiSasaranRaw = sasaranScoreRaw * (bobotSasaranRaw / 100);
            const nilaiSasaran = parseFloat(nilaiSasaranRaw.toFixed(2)); // Display
            
            // Hitung nilai budaya = rata-rata * (bobot_budaya / 100)
            const rataBudaya = parseFloat(document.getElementById('rata-budaya')?.textContent) || 0;
            // ambil bobot-budaya (misal '5%') -> dapatkan angka 5
            const bobotBudayaRaw = (document.getElementById('bobot-budaya')?.value || '').toString().replace('%', '') || '0';
            const bobotBudaya = parseFloat(bobotBudayaRaw) || 0;
            const nilaiBudayaRaw = rataBudaya * (bobotBudaya / 100);
            const nilaiBudaya = parseFloat(nilaiBudayaRaw.toFixed(2)); // Display
            
            // Ambil nilai share yang sudah dihitung dan ditampilkan di 'share-kpi-nilai'
            const shareValueRaw = parseFloat(document.getElementById('share-kpi-nilai')?.innerText) || 0;
            const shareValue = parseFloat(shareValueRaw.toFixed(2));

            // Total nilai raw = nilaiSasaran + nilaiBudaya + shareValue
            const totalNilaiRaw = nilaiSasaranRaw + nilaiBudayaRaw + shareValueRaw;
            const totalNilai = parseFloat(totalNilaiRaw.toFixed(2));

            // Nilai akhir sesuai rumus Excel
            let nilaiAkhirRaw = totalNilaiRaw;
            let nilaiAkhir = totalNilai;
            if (fraud === 1) {
                nilaiAkhirRaw = totalNilaiRaw - 1;
                nilaiAkhir = parseFloat(nilaiAkhirRaw.toFixed(2));
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
                const v = parseFloat(nilaiAkhir); // Gunakan nilai yang dibulatkan agar hasil match dengan hitungan manual layar
                if (v < 0) pencapaian = 0;
                else if (v < 2 * koef) pencapaian = (v / (2 * koef)) * 80;
                else if (v < 3 * koef) pencapaian = 80 + ((v - 2 * koef) / (1 * koef)) * 10;
                else if (v < 3.5 * koef) pencapaian = 90 + ((v - 3 * koef) / (0.5 * koef)) * 20;
                else if (v < 4.5 * koef) pencapaian = 110 + ((v - 3.5 * koef) / (1 * koef)) * 10;
                else if (v < 5 * koef) pencapaian = 120 + ((v - 4.5 * koef) / (0.5 * koef)) * 10;
                else pencapaian = 130;
            } else {
                pencapaian = 0;
            }

            // Update ke tampilan
            // Pastikan tampilan konsisten
            document.getElementById("nilai-sasaran").textContent = (nilaiSasaran).toFixed(2);
            document.getElementById("nilai-budaya").textContent = (nilaiBudaya).toFixed(2);
            document.getElementById("total-nilai").textContent = (totalNilai).toFixed(2);
            document.getElementById("nilai-akhir").textContent =
                nilaiAkhir === "Tidak ada nilai" ? nilaiAkhir : nilaiAkhir.toFixed(2);
            document.getElementById("predikat").textContent = predikat;
            document.getElementById("predikat").className = predikatClass;
            document.getElementById("pencapaian-akhir").textContent =
                pencapaian === "" ? "" : pencapaian.toFixed(2) + "%";
        }

        // ===== Hitung Share KPI (disamakan dengan logic di halaman administrator) =====
        function hitungShareKPI() {
            var bobotShareInputEl = document.getElementById('bobot-share-kpi');
            var bobotShareInput = bobotShareInputEl ? bobotShareInputEl.value : '';
            var bobotShare = parseFloat(bobotShareInput) || 0;

            // Hitung sisa untuk Sasaran Kerja (95 - Share KPI)
            var bobotSasaran = 95 - bobotShare;
            if (bobotSasaran < 0) bobotSasaran = 0;
            var bobotSasaranEl = document.getElementById('bobot-sasaran');
            if (bobotSasaranEl) bobotSasaranEl.value = bobotSasaran + "%";

            // Hitung nilai share KPI
            var nilaiInput = document.getElementById('share-kpi-value') ? document.getElementById('share-kpi-value').value : '';
            var nilai = parseFloat(nilaiInput) || 0;
            var hasil = nilai * (bobotShare / 100);
            var hasilFormatted = parseFloat(hasil.toFixed(2));
            var shareEl = document.getElementById('share-kpi-nilai');
            if (shareEl) shareEl.innerText = hasilFormatted;

            // Hitung nilai sasaran berdasarkan bobot yang disesuaikan
            var totalSasaranEl = document.getElementById('total-sasaran');
            var totalSasaranRaw = totalSasaranEl ? totalSasaranEl.innerText : '';
            var totalSasaran = parseFloat(String(totalSasaranRaw).replace(/,/g, ''));
            if (isNaN(totalSasaran)) totalSasaran = 0;

            var nilaiSasaran = totalSasaran * (bobotSasaran / 100);
            var nilaiSasaranFormatted = parseFloat(nilaiSasaran.toFixed(2));
            var nilaiSasaranEl = document.getElementById('nilai-sasaran');
            if (nilaiSasaranEl) nilaiSasaranEl.innerText = nilaiSasaranFormatted;

            // Jangan hitung total di sini — biarkan hitungNilaiAkhir() yang menggabungkan nilaiSasaran + nilaiBudaya + share
            if (typeof hitungNilaiAkhir === 'function') {
                try { hitungNilaiAkhir(); } catch (e) { console.error('hitungNilaiAkhir error', e); }
            }
        }

        // Dengarkan perubahan pada input share KPI dan bobotnya
        var shareInputEl = document.getElementById('share-kpi-value');
        var bobotShareEl = document.getElementById('bobot-share-kpi');
        if (shareInputEl) shareInputEl.addEventListener('input', function () {
            try { hitungTotal(); } catch (e) { console.error(e); }
            try { hitungShareKPI(); } catch (e) { console.error(e); }
            try { autoSaveNilaiAkhir(); } catch (e) { }
        });
        if (bobotShareEl) bobotShareEl.addEventListener('input', function () {
            try { hitungTotal(); } catch (e) { console.error(e); }
            try { hitungShareKPI(); } catch (e) { console.error(e); }
            try { autoSaveNilaiAkhir(); } catch (e) { }
        });

        document.getElementById('fraud-input').addEventListener('input', hitungNilaiAkhir);

        // 🔹 trigger perhitungan saat input diubah
        document.querySelectorAll('.target-input, .realisasi-input').forEach(input => {
            input.addEventListener('input', hitungTotal);
        });
        // Pastikan total per-barang dihitung terlebih dahulu, lalu hitung Share KPI
        hitungTotal();
        if (document.getElementById('share-kpi-value')) {
            try { hitungShareKPI(); } catch (e) { console.error('hitungShareKPI error', e); }
        }

        // Simpan penilaian per baris
        document.querySelectorAll('.simpan-penilaian').forEach(btn => {
            btn.addEventListener('click', function () {
                const row = this.closest('tr');
                const indikator_id = row.dataset.id;
                const target = row.querySelector('.target-input').value;
                const batas_waktu = row.querySelector('input[type="date"]').value;
                const realisasi = row.querySelector('.realisasi-input').value;
                const pencapaian = row.dataset.rawPencapaian !== undefined ? row.dataset.rawPencapaian : (row.querySelector('.pencapaian-output').value || '');
                const nilai = row.dataset.rawNilai !== undefined ? row.dataset.rawNilai : (row.querySelector('.nilai-output').value || '');
                const nilai_dibobot = row.dataset.rawNilaiBobot !== undefined ? row.dataset.rawNilaiBobot : (row.querySelector('.nilai-bobot-output').value || '');

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
        document.getElementById('btn-sesuaikan-periode').addEventListener('click', function () {
            const awal = periodeAwal.value;
            const akhir = periodeAkhir.value;
            window.location.href = `<?= base_url("Pegawai") ?>?awal=${awal}&akhir=${akhir}`;
        });

        // ==== Custom sorting untuk tanggal DD-MM-YYYY HH:MM ====
        $.extend($.fn.dataTableExt.oSort, {
            "date-uk-pre": function (a) {
                if (!a) return 0;
                var parts = a.split(' '); // ["07-10-2025", "10:30"]
                var dateParts = parts[0].split('-'); // ["07","10","2025"]
                var timeParts = parts[1] ? parts[1].split(':') : ["00", "00"]; // ["10","30"]
                return (dateParts[2] + dateParts[1] + dateParts[0] + timeParts[0] + timeParts[1]) * 1;
            },
            "date-uk-asc": function (a, b) {
                return ((a < b) ? -1 : ((a > b) ? 1 : 0));
            },
            "date-uk-desc": function (a, b) {
                return ((a < b) ? 1 : ((a > b) ? -1 : 0));
            }
        });

        // ==== DataTables Catatan Pegawai (TANDAI)====
        var tableCatatanPegawai = null;
        if ($('#tabel-catatan-pegawai').length) {
            try {
                tableCatatanPegawai = $('#tabel-catatan-pegawai').DataTable({
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
                    drawCallback: function (settings) {
                        var api = this.api();
                        api.column(0, {
                            order: 'applied'
                        }).nodes().each(function (cell, i) {
                            cell.innerHTML = i + 1;
                        });
                    }
                });
            } catch (err) {
                console.error('DataTable init error (tabel-catatan-pegawai):', err);
                tableCatatanPegawai = null;
            }
        }

        // ==== AJAX Form Catatan Pegawai ====
        $('#form-catatan-pegawai').on('submit', function (e) {
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
                body: `nik=${encodeURIComponent(nik)}&catatan=${encodeURIComponent(catatan)}`
            })
                .then(res => {
                    // jika server merespon non-JSON, jangan biarkan error menghentikan script
                    return res.json().catch(() => ({
                        success: false,
                        message: 'Respon server tidak valid'
                    }));
                })
                .then(data => {
                    if (data.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil',
                            timer: 1500,
                            showConfirmButton: false
                        });

                        // tanggal sekarang DD-MM-YYYY HH:MM (Asia/Jakarta)
                        const now = new Date();
                        const tanggal = now.toLocaleString('en-GB', {
                            timeZone: 'Asia/Jakarta',
                            day: '2-digit', month: '2-digit', year: 'numeric',
                            hour: '2-digit', minute: '2-digit', hour12: false
                        }).replace(/,/g, '').replace(/\//g, '-');

                        // tambahkan row baru — kalau DataTable tersedia gunakan API, kalau tidak append manual
                        if (tableCatatanPegawai) {
                            tableCatatanPegawai.row.add([
                                '', // nomor otomatis
                                $('<div>').text(catatan).html(), // sanitize text
                                tanggal // tanggal
                            ]).draw();
                            tableCatatanPegawai.order([2, 'desc']).draw();
                        } else {
                            // fallback: append langsung ke tbody (pastikan elemen ada)
                            const $tbody = $('#tabel-catatan-pegawai tbody');
                            if ($tbody.length) {
                                $tbody.prepend(
                                    `<tr>
                                        <td></td>
                                        <td>${$('<div>').text(catatan).html()}</td>
                                        <td>${tanggal}</td>
                                    </tr>`
                                );
                            } else {
                                // jika tabel memang tidak ada, tambahkan console log saja
                                console.warn('Tabel catatan pegawai tidak ditemukan untuk ditambahkan row.');
                            }
                        }

                        $('#form-catatan-pegawai')[0].reset();
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal',
                            text: data.message || 'Gagal menyimpan'
                        });
                    }
                })
                .catch((err) => {
                    console.error('Error simpan_catatan_pegawai:', err);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Terjadi kesalahan server'
                    });
                });
        });

        $(document).ready(function () {
            const nikPegawai = "<?= $pegawai_detail->nik; ?>"; // pegawai yang sedang dilihat
            const penilai1_selected = "<?= $pegawai_detail->penilai1_nik ?? ''; ?>";
            const penilai2_selected = "<?= $pegawai_detail->penilai2_nik ?? ''; ?>";

            $.ajax({
                url: "<?= base_url('Pegawai/getPegawaiSatuUnit/'); ?>" + nikPegawai,
                method: "GET",
                dataType: "json",
                success: function (data) {
                    if (data.length > 0) {
                        // isi dropdown penilai 1
                        let opt1 = '<option value="">-- Pilih Penilai I --</option>';
                        data.forEach(function (p) {
                            let selected = (p.nik === penilai1_selected) ? "selected" : "";
                            opt1 += `<option value="${p.nik}" ${selected}>${p.nama} (${p.jabatan})</option>`;
                        });
                        $('#penilai1').html(opt1);

                        // isi dropdown penilai 2
                        let opt2 = '<option value="">-- Pilih Penilai II --</option>';
                        data.forEach(function (p) {
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
            input.addEventListener('input', function () {
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
            const share_kpi_value = document.getElementById('share-kpi-value') ? document.getElementById('share-kpi-value').value : '';
            const bobot_share_kpi = document.getElementById('bobot-share-kpi') ? document.getElementById('bobot-share-kpi').value : '';
            const bobot_sasaran = (document.getElementById('bobot-sasaran')?.value || '').toString().replace('%', '') || '';
            const bobot_budaya = (document.getElementById('bobot-budaya')?.value || '').toString().replace('%', '') || '';
            const koefisienElem = document.getElementById('koefisien-input');
            const koefisien = koefisienElem ? (koefisienElem.value || '').toString() : '100';

            fetch('<?= base_url("Pegawai/simpanNilaiAkhir") ?>', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: `nik=${encodeURIComponent(nik)}&periode_awal=${encodeURIComponent(periode_awal)}&periode_akhir=${encodeURIComponent(periode_akhir)}&nilai_sasaran=${encodeURIComponent(nilai_sasaran)}&bobot_sasaran=${encodeURIComponent(bobot_sasaran)}&nilai_budaya=${encodeURIComponent(nilai_budaya)}&bobot_budaya=${encodeURIComponent(bobot_budaya)}&total_nilai=${encodeURIComponent(total_nilai)}&fraud=${encodeURIComponent(fraud)}&nilai_akhir=${encodeURIComponent(nilai_akhir)}&pencapaian=${encodeURIComponent(pencapaian)}&predikat=${encodeURIComponent(predikat)}&koefisien=${encodeURIComponent(koefisien)}&share_kpi_value=${encodeURIComponent(share_kpi_value)}&bobot_share_kpi=${encodeURIComponent(bobot_share_kpi)}`
            })
                .then(res => res.json())
                .then(res => {
                    // Optional: tampilkan notifikasi kecil (atau silent)
                })
                .catch(err => {
                    console.error(err);
                });
        }

        document.getElementById('btn-simpan-nilai-akhir').addEventListener('click', function () {
            const btn = this;
            const nik = document.getElementById('nik').value;
            const periode_awal = document.getElementById('periode_awal').value;
            const periode_akhir = document.getElementById('periode_akhir').value;

            // Tampilkan loading
            btn.disabled = true;
            btn.innerHTML = '<i class="mdi mdi-spin mdi-loading"></i> Menyimpan...';

            // 1. Kumpulkan semua promise untuk menyimpan setiap baris
            const savePromises = [];
            document.querySelectorAll('#tabel-penilaian tbody tr[data-id]').forEach(row => {
                const indikator_id = row.dataset.id;
                const target = row.querySelector('.target-input').value;
                const batas_waktu = row.querySelector('input[type="date"]').value;
                const realisasi = row.querySelector('.realisasi-input').value;
                const pencapaian = row.dataset.rawPencapaian !== undefined ? row.dataset.rawPencapaian : (row.querySelector('.pencapaian-output').value || '');
                const nilai = row.dataset.rawNilai !== undefined ? row.dataset.rawNilai : (row.querySelector('.nilai-output').value || '');
                const nilai_dibobot = row.dataset.rawNilaiBobot !== undefined ? row.dataset.rawNilaiBobot : (row.querySelector('.nilai-bobot-output').value || '');

                const body = `indikator_id=${indikator_id}&target=${encodeURIComponent(target)}&batas_waktu=${encodeURIComponent(batas_waktu)}&realisasi=${encodeURIComponent(realisasi)}&pencapaian=${encodeURIComponent(pencapaian)}&nilai=${encodeURIComponent(nilai)}&nilai_dibobot=${encodeURIComponent(nilai_dibobot)}&periode_awal=${encodeURIComponent(periode_awal)}&periode_akhir=${encodeURIComponent(periode_akhir)}`;

                const promise = fetch('<?= base_url("Pegawai/simpanPenilaianBaris") ?>', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    body: body
                }).then(res => res.json());

                savePromises.push(promise);
            });

            // 2. Tunggu semua baris selesai disimpan
            Promise.all(savePromises).then(results => {
                const isAllSuccess = results.every(res => res.status === 'success');

                if (!isAllSuccess) {
                    throw new Error('Beberapa data baris gagal disimpan. Silakan cek kembali.');
                }

                // 3. Jika semua baris sukses, simpan nilai akhir
                const nilai_sasaran = document.getElementById('total-sasaran').textContent;
                const nilai_budaya = document.getElementById('rata-budaya').textContent;
                const total_nilai = document.getElementById('total-nilai').textContent;
                const fraud = document.getElementById('fraud-input').value;
                const nilai_akhir = document.getElementById('nilai-akhir').textContent;
                const predikat = document.getElementById('predikat').textContent;
                const pencapaian = document.getElementById('pencapaian-akhir').textContent;
                const share_kpi_value = document.getElementById('share-kpi-value') ? document.getElementById('share-kpi-value').value : '';
                const bobot_share_kpi = document.getElementById('bobot-share-kpi') ? document.getElementById('bobot-share-kpi').value : '';
                const bobot_sasaran = (document.getElementById('bobot-sasaran')?.value || '').toString().replace('%', '') || '';
                const bobot_budaya = (document.getElementById('bobot-budaya')?.value || '').toString().replace('%', '') || '';
                const koefisienElem = document.getElementById('koefisien-input');
                const koefisien = koefisienElem ? (koefisienElem.value || '').toString() : '100';

                const bodyNilaiAkhir = `nik=${encodeURIComponent(nik)}&periode_awal=${encodeURIComponent(periode_awal)}&periode_akhir=${encodeURIComponent(periode_akhir)}&nilai_sasaran=${encodeURIComponent(nilai_sasaran)}&bobot_sasaran=${encodeURIComponent(bobot_sasaran)}&nilai_budaya=${encodeURIComponent(nilai_budaya)}&bobot_budaya=${encodeURIComponent(bobot_budaya)}&total_nilai=${encodeURIComponent(total_nilai)}&fraud=${encodeURIComponent(fraud)}&nilai_akhir=${encodeURIComponent(nilai_akhir)}&pencapaian=${encodeURIComponent(pencapaian)}&predikat=${encodeURIComponent(predikat)}&koefisien=${encodeURIComponent(koefisien)}&share_kpi_value=${encodeURIComponent(share_kpi_value)}&bobot_share_kpi=${encodeURIComponent(bobot_share_kpi)}`;

                return fetch('<?= base_url("Pegawai/simpanNilaiAkhir") ?>', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    body: bodyNilaiAkhir
                }).then(res => res.json());

            }).then(finalResult => {
                if (finalResult.status === 'success') {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil',
                        text: 'Semua data penilaian dan nilai akhir berhasil disimpan!',
                        timer: 2000,
                        showConfirmButton: false
                    });
                } else {
                    throw new Error(finalResult.message || 'Gagal menyimpan nilai akhir.');
                }
            }).catch(err => {
                console.error(err);
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal',
                    text: err.message || 'Terjadi kesalahan server.',
                    confirmButtonColor: '#d33'
                });
            }).finally(() => {
                // Kembalikan tombol ke state normal
                btn.disabled = false;
                btn.innerHTML = '<i class="mdi mdi-content-save"></i> Simpan Nilai Akhir';
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

        // Validasi input bobot
        document.querySelectorAll('.bobot').forEach(input => {
            $(input).tooltip(); // Init tooltip
            input.addEventListener('change', function () {
                let val = parseInt(this.value);
                if (isNaN(val) || val < 5) {
                    this.value = 5;
                    Swal.fire({
                        icon: 'warning',
                        title: 'Perhatian',
                        text: 'Bobot harus minimal 5',
                        timer: 2000,
                        showConfirmButton: false
                    });
                }

                // Cek total bobot
                let totalBobot = 0;
                document.querySelectorAll('#tabel-penilaian tbody tr[data-id]').forEach(row => {
                    totalBobot += parseFloat(row.querySelector('.bobot').value) || 0;
                });

                if (totalBobot > 100) {
                    this.value = this.dataset.prevValue || 5; // Kembalikan ke nilai sebelumnya
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal',
                        text: 'Total bobot keseluruhan tidak boleh lebih dari 100%.'
                    });
                    return;
                }

                this.dataset.prevValue = this.value;
                hitungTotal();

                // Auto-save sama seperti kolom lain
                const row = this.closest('tr');
                const indikator_id = row.dataset.id;
                const bobot = this.value;
                const realisasi = row.querySelector('.realisasi-input').value;
                const target = row.querySelector('.target-input').value;
                const batas_waktu = row.querySelector('input[type="date"]').value;
                const pencapaian = row.querySelector('.pencapaian-output').value;
                const nilai = row.querySelector('.nilai-output').value;
                const nilai_dibobot = row.querySelector('.nilai-bobot-output').value;
                const periode_awal = document.getElementById('periode_awal').value;
                const periode_akhir = document.getElementById('periode_akhir').value;

                const body = `indikator_id=${indikator_id}&bobot=${encodeURIComponent(bobot)}&target=${encodeURIComponent(target)}&batas_waktu=${encodeURIComponent(batas_waktu)}&realisasi=${encodeURIComponent(realisasi)}&pencapaian=${encodeURIComponent(pencapaian)}&nilai=${encodeURIComponent(nilai)}&nilai_dibobot=${encodeURIComponent(nilai_dibobot)}&periode_awal=${encodeURIComponent(periode_awal)}&periode_akhir=${encodeURIComponent(periode_akhir)}`;

                fetch('<?= base_url("Pegawai/simpanPenilaianBaris") ?>', { method: 'POST', headers: { 'Content-Type': 'application/x-www-form-urlencoded' }, body: body })
                    .then(res => res.json()).then(res => { if (res.status !== 'success') console.error('Autosave bobot gagal', res); });

                autoSaveNilaiAkhir();
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

        $.getJSON("<?= base_url('Pegawai/getCoachingChat/') ?>" + nikPegawai + "?lastId=" + lastId, function (data) {
            if (data.length > 0) {
                data.forEach(function (row) {
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
    $('#form-chat').on('submit', function (e) {
        e.preventDefault();
        const formData = $(this).serialize();

        $.ajax({
            url: "<?= base_url('Pegawai/kirimCoachingPesan') ?>",
            method: "POST",
            data: formData,
            dataType: "json",
            success: function (res) {
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
            error: function (xhr, status, error) {
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
    document.addEventListener('DOMContentLoaded', function () {
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
    document.addEventListener('DOMContentLoaded', function () {
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
<script src="<?= base_url('assets/libs/chart-js/chart.js') ?>"></script>
<script>
    // Fungsi untuk mendapatkan warna berdasarkan predikat
    function getPredikatColor(predikat) {
        if (!predikat) return '#9e9e9e'; // Abu-abu jika tidak ada predikat
        const p = predikat.toLowerCase();
        if (p.includes('excellent')) return '#198754'; // Hijau tua
        if (p.includes('very good')) return '#28a745'; // Hijau
        if (p.includes('good')) return '#17a2b8'; // Biru-hijau
        if (p.includes('fair')) return '#ffc107'; // Kuning
        if (p.includes('minus')) return '#dc3545'; // Merah
        return '#6c757d'; // Default 
    }

    const ctx = document.getElementById('grafikPencapaian').getContext('2d');
    let chartInstance = null;
    const allGrafikData = <?= json_encode($grafik_pencapaian, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>;
    const filterSelect = document.getElementById('filterTahunGrafik');

    function renderChart(filteredData) {
        if (chartInstance) {
            chartInstance.destroy();
        }

        const canvas = document.getElementById('grafikPencapaian');
        const messageDiv = document.getElementById('grafikMessage');

        if (!filteredData || filteredData.length <= 1) {
            canvas.style.display = 'none';
            messageDiv.style.display = 'block';
            if (filteredData && filteredData.length === 1) {
                const capaian = filteredData[0].pencapaian || 0;
                messageDiv.innerHTML = `<i class="mdi mdi-information-outline mdi-24px mb-2 text-danger"></i><br>Anda baru memiliki data penilaian untuk 1 periode dengan pencapaian <strong>${capaian}%</strong>.<br>Grafik akan muncul jika sudah ada lebih dari satu periode penilaian.`;
            } else {
                messageDiv.innerHTML = `<i class="mdi mdi-chart-bar-stacked mdi-24px mb-2"></i><br>Tidak ada data penilaian yang dapat ditampilkan untuk periode ini.`;
            }
            return; // Hentikan render chart
        }

        canvas.style.display = 'block';
        messageDiv.style.display = 'none';

        const dataPeriode = filteredData.map(g => {
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

        const dataPencapaian = filteredData.map(g => g.pencapaian);
        const pointColors = filteredData.map(g => getPredikatColor(g.predikat));
        const segmentColors = dataPencapaian.map((value, index, arr) => {
            if (index === 0) return 'rgba(40, 167, 69, 1)';
            return value >= arr[index - 1] ? 'rgba(40, 167, 69, 1)' : 'rgba(220, 53, 69, 1)';
        });

        chartInstance = new Chart(ctx, {
            type: 'line',
            data: {
                labels: dataPeriode,
                datasets: [{
                    label: 'Pencapaian SKI (%)',
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
                    borderWidth: 4,
                    fill: true,
                    tension: 0.3,
                    pointRadius: 5,
                    pointBackgroundColor: pointColors,
                    pointBorderColor: 'rgba(40, 167, 69, 0.08)',
                    pointHoverRadius: 7
                }, {
                    label: 'Target SKI',
                    data: Array(dataPeriode.length).fill(100),
                    borderColor: '#348cd4',
                    borderWidth: 3,
                    pointRadius: 3,
                    pointHoverRadius: 5,
                    pointBackgroundColor: '#348cd4',
                    fill: false,
                    tension: 0,
                    order: 1
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
                        max: 130,
                        title: {
                            display: true,
                            text: 'Pencapaian (%)'
                        },
                        ticks: {
                            callback: value => value + '%',
                            stepSize: 10
                        }
                    }
                }
            }
        });
    }

    function filterAndRender() {
        const selectedYear = filterSelect.value;
        let dataToShow;

        if (selectedYear === 'semua') {
            dataToShow = allGrafikData;
        } else {
            dataToShow = allGrafikData.filter(item => new Date(item.periode_awal).getFullYear() == selectedYear);
        }
        renderChart(dataToShow);

        // Perbarui insight berdasarkan data yang ditampilkan di grafik
        updateInsight(dataToShow);
    }

    function initializeFilterAndChart() {
        const years = [...new Set(allGrafikData.map(item => new Date(item.periode_awal).getFullYear()))].sort((a, b) => b - a);
        const currentYear = new Date().getFullYear();

        // Populate filter dropdown
        filterSelect.innerHTML = '<option value="semua">Semua Tahun</option>';
        years.forEach(year => {
            const selected = (year === currentYear) ? 'selected' : '';
            filterSelect.innerHTML += `<option value="${year}" ${selected}>${year}</option>`;
        });

        // Initial render
        filterAndRender();

        // Add event listener for filter changes
        filterSelect.addEventListener('change', filterAndRender);
    }

    initializeFilterAndChart();

    // fallback predikat
    function predikatDariNilaiAkhir(nilai) {
        if (nilai === null || nilai === undefined || isNaN(nilai)) return null;
        if (nilai >= 4.5) return 'Excellent';
        if (nilai >= 3.5) return 'Very Good';
        if (nilai >= 3.0) return 'Good';
        if (nilai >= 2.0) return 'Fair';
        return 'Minus';
    }

    function updateInsight(data) {
        const insightContainer = document.getElementById('insightContainer');
        const insightBox = insightContainer.querySelector('.insight-box');
        const insightText = document.getElementById('insightText');
        const icon = insightBox.querySelector('.icon');

        // Reset classes
        insightBox.className = 'insight-box mb-0 shadow-sm';
        icon.className = 'mdi mr-1 icon';

        if (data && data.length > 0) {
            const lastIndex = data.length - 1;
            const gNow = data[lastIndex];
            const periodeNowText = (new Date(gNow.periode_awal)).toLocaleDateString('id-ID', {
                day: '2-digit',
                month: 'short'
            }) +
                ' - ' + (new Date(gNow.periode_akhir)).toLocaleDateString('id-ID', {
                    day: '2-digit',
                    month: 'short'
                });
            const predikatNow = gNow.predikat || predikatDariNilaiAkhir(gNow.nilai_akhir) || 'N/A';

            let message = '';

            if (data.length > 1) {
                // Logika untuk 2 data atau lebih (membandingkan)
                const gPrev = data[lastIndex - 1];
                const periodePrevText = (new Date(gPrev.periode_awal)).toLocaleDateString('id-ID', {
                    day: '2-digit',
                    month: 'short'
                }) +
                    ' - ' + (new Date(gPrev.periode_akhir)).toLocaleDateString('id-ID', {
                        day: '2-digit',
                        month: 'short'
                    });
                const predikatPrev = gPrev.predikat || predikatDariNilaiAkhir(gPrev.nilai_akhir) || 'N/A';

                const lastP = gNow.pencapaian;
                const prevP = gPrev.pencapaian;
                const diff = lastP - prevP;

                if (diff > 0) {
                    insightBox.classList.add('insight-success');
                    icon.classList.add('mdi-trending-up');
                    message = `🎯 <strong>Pencapaian meningkat ${diff.toFixed(1)}%</strong> dari <strong>${periodePrevText}</strong> ke <strong>${periodeNowText}</strong>.<br>Predikat: <strong>${predikatPrev}</strong> → <strong>${predikatNow}</strong>. Pertahankan performa ini!`;
                } else if (diff < 0) {
                    insightBox.classList.add('insight-danger');
                    icon.classList.add('mdi-alert-circle-outline');
                    message = `⚠️ <strong>Pencapaian menurun ${Math.abs(diff).toFixed(1)}%</strong> dari <strong>${periodePrevText}</strong> ke <strong>${periodeNowText}</strong>.<br>Predikat: <strong>${predikatPrev}</strong> → <strong>${predikatNow}</strong>. Segera evaluasi dan perbaiki strategi kerja Anda.`;
                } else {
                    insightBox.classList.add('insight-info');
                    icon.classList.add('mdi-information-outline');
                    message = `ℹ️ <strong>Pencapaian stabil</strong> antara periode <strong>${periodePrevText}</strong> dan <strong>${periodeNowText}</strong>.<br>Predikat tetap: <strong>${predikatNow}</strong>. Jaga konsistensi.`;
                }
            } else {
                // Logika untuk hanya 1 data
                insightBox.classList.add('insight-info');
                icon.classList.add('mdi-information-outline');
                message = `Penilaian untuk periode <strong>${periodeNowText}</strong> memiliki pencapaian <strong>${gNow.pencapaian}%</strong> dengan predikat <strong>${predikatNow}</strong>.`;
            }
            insightText.innerHTML = message;
            insightContainer.style.display = 'block';
        } else {
            insightBox.classList.add('insight-info');
            icon.classList.add('mdi-information-outline');
            insightText.innerHTML = 'Tidak ada data penilaian untuk periode yang dipilih.';
            insightContainer.style.display = 'block';
        }
    }
</script>

<script>
    // AJAX helper untuk isi form Ubah Penilai I/II
    (function () {
        const nik = "<?= $pegawai_detail->nik ?? '' ?>";

        if (!nik) return;

        function loadPenilaiCandidates() {
            $.ajax({
                url: "<?= base_url('Pegawai/getPenilaiCandidates/') ?>" + encodeURIComponent(nik),
                method: 'GET',
                dataType: 'json'
            }).done(function (res) {
                if (!res || res.status !== 'ok') return;

                // isi penilai1_select (formPenilai1)
                if ($('#penilai1_select').length) {
                    let opt1 = '<option value="">-- Pilih Penilai I --</option>';
                    if (res.candidates1 && res.candidates1.length) {
                        res.candidates1.forEach(function (p) {
                            // value is mapping key (pm_key). Compare with mapping.penilai1_jabatan to mark selected
                            const sel = (res.mapping && res.mapping.penilai1_jabatan && p.pm_key === res.mapping.penilai1_jabatan) ? 'selected' : '';
                            // show name and jabatan; keep nik visible in label
                            opt1 += `<option value="${p.pm_key}" data-nik="${p.nik}" ${sel}>${p.nama} (${p.jabatan}) — ${p.nik}</option>`;
                        });
                    } else {
                        opt1 += '<option value="">(tidak ada kandidat)</option>';
                    }
                    $('#penilai1_select').html(opt1);
                    if ($.fn.select2) try {
                        $('#penilai1_select').select2({
                            width: '100%'
                        });
                    } catch (e) { }
                }

                // isi penilai2_select (formPenilai2)
                if ($('#penilai2_select').length) {
                    let opt2 = '<option value="">-- Pilih Penilai II --</option>';
                    if (res.candidates2 && res.candidates2.length) {
                        res.candidates2.forEach(function (p) {
                            const sel = (res.mapping && res.mapping.penilai2_jabatan && p.pm_key === res.mapping.penilai2_jabatan) ? 'selected' : '';
                            opt2 += `<option value="${p.pm_key}" data-nik="${p.nik}" ${sel}>${p.nama} (${p.jabatan}) — ${p.nik}</option>`;
                        });
                    } else {
                        opt2 += '<option value="">(tidak ada kandidat)</option>';
                    }
                    $('#penilai2_select').html(opt2);
                    if ($.fn.select2) try {
                        $('#penilai2_select').select2({
                            width: '100%'
                        });
                    } catch (e) { }
                }

            }).fail(function () {
                console.error('Gagal mengambil kandidat penilai');
            });
        }

        // load once and also on collapse open
        $(function () {
            loadPenilaiCandidates();
            $('#formPenilai1, #formPenilai2').on('show.bs.collapse', function () {
                loadPenilaiCandidates();
            });
        });
    })();

    // ==========================================
    // LOGIC TAMBAH SASARAN & INDIKATOR
    // ==========================================

    window.addSasaranRow = async function () {
        let totalBobot = 0;
        document.querySelectorAll('#tabel-penilaian tbody tr[data-id]').forEach(r => {
            totalBobot += parseFloat(r.querySelector('.bobot').value) || 0;
        });

        if (totalBobot + 5 > 100) {
            Swal.fire('Peringatan', 'Tidak bisa menambahkan sasaran baru, total bobot sudah maksimal. Kurangi bobot yang sudah ada terlebih dahulu.', 'warning');
            return;
        }

        // 1. Pilih Perspektif
        const { value: perspektif } = await Swal.fire({
            title: 'Pilih Perspektif',
            input: 'select',
            inputOptions: {
                'Keuangan (F)': 'Keuangan (F)',
                'Pelanggan (C)': 'Pelanggan (C)',
                'Proses Internal (IP)': 'Proses Internal (IP)',
                'Pembelajaran & Pertumbuhan (LG)': 'Pembelajaran & Pertumbuhan (LG)'
            },
            inputPlaceholder: 'Pilih Perspektif',
            showCancelButton: true
        });

        if (!perspektif) return;

        // Cari baris terakhir dari perspektif ini untuk insert after
        let lastRow = $(`tr[data-perspektif="${perspektif}"]`).last();

        // Buat baris baru (Form Kosong)
        let newRowHtml = `
        <tr class="new-row-input bg-light">
            <td colspan="2">
                <small class="text-muted">Perspektif: ${perspektif}</small><br>
                <input type="text" class="form-control form-control-sm new-sasaran" placeholder="Nama Sasaran Baru">
            </td>
            <td><input type="number" class="form-control form-control-sm new-bobot" placeholder="Bobot" min="5" step="5"></td>
            <td><input type="text" class="form-control form-control-sm new-indikator" placeholder="Nama Indikator"></td>
            <td><input type="text" class="form-control form-control-sm new-target" placeholder="Target"></td>
            <td><input type="date" class="form-control form-control-sm new-batas-waktu"></td>
            <td colspan="7" class="text-center">
                <button class="btn btn-sm btn-success" onclick="saveNewSasaran(this, '${perspektif}')">Simpan</button>
                <button class="btn btn-sm btn-danger" onclick="$(this).closest('tr').remove()">Batal</button>
            </td>
        </tr>
    `;

        if (lastRow.length > 0) {
            lastRow.after(newRowHtml);
        } else {
            // Jika perspektif belum ada, taruh di paling bawah tbody
            $('#tabel-penilaian tbody').append(newRowHtml);
        }
    };

    window.saveNewSasaran = function (btn, perspektif) {
        let row = $(btn).closest('tr');
        let sasaran = row.find('.new-sasaran').val();
        let bobot = row.find('.new-bobot').val();
        let indikator = row.find('.new-indikator').val();
        let target = row.find('.new-target').val();
        let batas_waktu = row.find('.new-batas-waktu').val();

        if (!sasaran || !indikator || !bobot) {
            Swal.fire('Error', 'Sasaran, Indikator, dan Bobot wajib diisi', 'error');
            return;
        }

        let totalBobot = 0;
        document.querySelectorAll('#tabel-penilaian tbody tr[data-id]').forEach(r => {
            totalBobot += parseFloat(r.querySelector('.bobot').value) || 0;
        });
        totalBobot += parseFloat(bobot) || 0;

        if (totalBobot > 100) {
            Swal.fire('Gagal', 'Total bobot keseluruhan tidak boleh lebih dari 100%. Kurangi bobot yang sudah ada terlebih dahulu.', 'error');
            return;
        }

        $.ajax({
            url: '<?= base_url("Pegawai/simpan_sasaran_baru") ?>',
            method: 'POST',
            data: {
                perspektif: perspektif,
                sasaran: sasaran,
                indikator: indikator,
                bobot: bobot,
                target: target,
                batas_waktu: batas_waktu,
                periode_awal: $('#periode_awal').val(),
                periode_akhir: $('#periode_akhir').val()
            },
            success: function (res) {
                let data = JSON.parse(res);
                if (data.status == 'success') {
                    Swal.fire('Berhasil', 'Sasaran berhasil ditambahkan', 'success').then(() => location.reload());
                } else {
                    Swal.fire('Gagal', 'Gagal menyimpan', 'error');
                }
            }
        });
    };

    window.addIndikatorRow = async function () {
        let totalBobot = 0;
        document.querySelectorAll('#tabel-penilaian tbody tr[data-id]').forEach(r => {
            totalBobot += parseFloat(r.querySelector('.bobot').value) || 0;
        });

        if (totalBobot + 5 > 100) {
            Swal.fire('Peringatan', 'Tidak bisa menambahkan indikator baru, total bobot sudah maksimal. Kurangi bobot yang sudah ada terlebih dahulu.', 'warning');
            return;
        }

        // Ambil daftar sasaran unik dari tabel
        let sasarans = {};
        $('tr[data-sasaran-id]').each(function () {
            let id = $(this).data('sasaran-id');
            let nama = $(this).data('sasaran-nama');
            if (id && nama) sasarans[id] = nama;
        });

        if (Object.keys(sasarans).length === 0) {
            Swal.fire('Info', 'Tidak ada sasaran tersedia. Tambahkan sasaran dulu.', 'info');
            return;
        }

        const { value: sasaranId } = await Swal.fire({
            title: 'Pilih Sasaran',
            input: 'select',
            inputOptions: sasarans,
            inputPlaceholder: 'Pilih Sasaran',
            showCancelButton: true
        });

        if (!sasaranId) return;

        let lastRow = $(`tr[data-sasaran-id="${sasaranId}"]`).last();
        let sasaranNama = sasarans[sasaranId];

        let newRowHtml = `
        <tr class="new-row-input bg-light">
            <td colspan="2" class="text-right"><small>Indikator Baru untuk: ${sasaranNama}</small></td>
            <td><input type="number" class="form-control form-control-sm new-bobot" placeholder="Bobot" min="5" step="5"></td>
            <td><input type="text" class="form-control form-control-sm new-indikator" placeholder="Nama Indikator"></td>
            <td><input type="text" class="form-control form-control-sm new-target" placeholder="Target"></td>
            <td><input type="date" class="form-control form-control-sm new-batas-waktu"></td>
            <td colspan="7" class="text-center">
                <button class="btn btn-sm btn-success" onclick="saveNewIndikator(this, '${sasaranId}')">Simpan</button>
                <button class="btn btn-sm btn-danger" onclick="$(this).closest('tr').remove()">Batal</button>
            </td>
        </tr>
    `;
        lastRow.after(newRowHtml);
    };

    window.saveNewIndikator = function (btn, sasaranId) {
        let row = $(btn).closest('tr');
        let bobot = row.find('.new-bobot').val();
        let indikator = row.find('.new-indikator').val();
        let target = row.find('.new-target').val();
        let batas_waktu = row.find('.new-batas-waktu').val();

        if (!indikator || !bobot) {
            Swal.fire('Error', 'Indikator dan Bobot wajib diisi', 'error');
            return;
        }

        let totalBobot = 0;
        document.querySelectorAll('#tabel-penilaian tbody tr[data-id]').forEach(r => {
            totalBobot += parseFloat(r.querySelector('.bobot').value) || 0;
        });
        totalBobot += parseFloat(bobot) || 0;

        if (totalBobot > 100) {
            Swal.fire('Gagal', 'Total bobot keseluruhan tidak boleh lebih dari 100%. Kurangi bobot yang sudah ada terlebih dahulu.', 'error');
            return;
        }

        $.ajax({
            url: '<?= base_url("Pegawai/simpan_indikator_baru") ?>',
            method: 'POST',
            data: {
                sasaran_id: sasaranId,
                indikator: indikator,
                bobot: bobot,
                target: target,
                batas_waktu: batas_waktu,
                periode_awal: $('#periode_awal').val(),
                periode_akhir: $('#periode_akhir').val()
            },
            success: function (res) {
                let data = JSON.parse(res);
                if (data.status == 'success') {
                    Swal.fire('Berhasil', 'Indikator berhasil ditambahkan', 'success').then(() => location.reload());
                } else {
                    Swal.fire('Gagal', 'Gagal menyimpan', 'error');
                }
            }
        });
    };

    window.editSasaran = async function (id, btn) {
        let textLama = btn.getAttribute('data-text');
        if (!id) {
            Swal.fire('Error', 'ID Sasaran tidak ditemukan', 'error');
            return;
        }
        const { value: sasaranBaru } = await Swal.fire({
            title: 'Edit Sasaran Kerja',
            input: 'textarea',
            inputValue: textLama,
            showCancelButton: true,
            confirmButtonText: 'Simpan',
            cancelButtonText: 'Batal',
            inputValidator: (value) => {
                if (!value) {
                    return 'Sasaran kerja tidak boleh kosong!'
                }
            }
        });

        if (sasaranBaru) {
            $.ajax({
                url: '<?= base_url("Pegawai/updateSasaran") ?>',
                method: 'POST',
                data: { id: id, sasaran: sasaranBaru },
                success: function (res) {
                    let data = JSON.parse(res);
                    if (data.success) {
                        Swal.fire('Berhasil', data.message, 'success').then(() => location.reload());
                    } else {
                        Swal.fire('Gagal', data.message, 'error');
                    }
                }
            });
        }
    };

    window.editIndikator = async function (id, btn) {
        let textLama = btn.getAttribute('data-text');
        let row = $(btn).closest('tr');
        let bobotLama = row.find('.bobot').val();

        const { value: formValues } = await Swal.fire({
            title: 'Edit Indikator & Bobot',
            html:
                '<div class="form-group text-left"><label>Indikator</label><textarea id="swal-input-indikator" class="form-control" rows="3" placeholder="Nama Indikator"></textarea></div>' +
                '<div class="form-group text-left mt-2"><label>Bobot (%)</label><input id="swal-input-bobot" class="form-control" type="number" placeholder="Bobot" value="' + bobotLama + '" min="5" step="5"></div>',
            focusConfirm: false,
            showCancelButton: true,
            confirmButtonText: 'Simpan',
            cancelButtonText: 'Batal',
            didOpen: () => {
                document.getElementById('swal-input-indikator').value = textLama;
            },
            preConfirm: () => {
                return [
                    document.getElementById('swal-input-indikator').value,
                    document.getElementById('swal-input-bobot').value
                ]
            }
        });

        if (formValues) {
            let [indikatorBaru, bobotBaru] = formValues;

            if (!indikatorBaru || !bobotBaru) {
                Swal.fire('Error', 'Indikator dan Bobot wajib diisi', 'error');
                return;
            }

            // Hitung total bobot selain yang sedang diedit untuk validasi
            let totalBobotLain = 0;
            document.querySelectorAll('#tabel-penilaian tbody tr[data-id]').forEach(r => {
                if (r.dataset.id != id) {
                    totalBobotLain += parseFloat(r.querySelector('.bobot').value) || 0;
                }
            });

            if (totalBobotLain + parseFloat(bobotBaru) > 100) {
                Swal.fire('Gagal', 'Total bobot keseluruhan tidak boleh lebih dari 100%', 'error');
                return;
            }

            $.ajax({
                url: '<?= base_url("Pegawai/updateIndikator") ?>',
                method: 'POST',
                data: { id: id, indikator: indikatorBaru, bobot: bobotBaru },
                success: function (res) {
                    let data = JSON.parse(res);
                    if (data.success) {
                        Swal.fire('Berhasil', data.message, 'success').then(() => location.reload());
                    } else {
                        Swal.fire('Gagal', data.message, 'error');
                    }
                }
            });
        }
    };

    window.deleteIndikator = function (id) {
        Swal.fire({
            title: 'Hapus Indikator?',
            text: "Indikator beserta penilaiannya akan dihapus dan tidak dapat dikembalikan!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '<?= base_url("Pegawai/deleteIndikatorAjax") ?>',
                    method: 'POST',
                    data: { id: id },
                    success: function (res) {
                        let data = JSON.parse(res);
                        if (data.success) {
                            Swal.fire('Terhapus!', data.message, 'success').then(() => location.reload());
                        } else {
                            Swal.fire('Gagal!', data.message, 'error');
                        }
                    }
                });
            }
        });
    };

    window.lihatCatatan = function (indikatorId, nikPenilai = '') {
        $.ajax({
            url: '<?= base_url("Pegawai/getCatatanIndikator") ?>',
            method: 'POST',
            data: { indikator_id: indikatorId, nik_penilai: nikPenilai },
            dataType: 'json',
            success: function (res) {
                let tbody = $('#tabel-catatan-indikator tbody');
                tbody.empty();
                if (res.success && res.data.length > 0) {
                    res.data.forEach(function (c) {
                        // Format date ke WIB (Asia/Jakarta)
                        let tgl = c.tanggal;
                        if (tgl) {
                            let parts = tgl.split(' ');
                            if (parts.length === 2) {
                                let dateParts = parts[0].split('-');
                                let timeParts = parts[1].split(':');
                                let utcDate = new Date(Date.UTC(dateParts[0], dateParts[1] - 1, dateParts[2], timeParts[0], timeParts[1], timeParts[2] || 0));

                                tgl = utcDate.toLocaleString('en-GB', {
                                    timeZone: 'Asia/Jakarta',
                                    day: '2-digit', month: '2-digit', year: 'numeric',
                                    hour: '2-digit', minute: '2-digit', hour12: false
                                }).replace(/,/g, '').replace(/\//g, '-');
                            }
                        }

                        tbody.append(`
                            <tr>
                                <td>${tgl}</td>
                                <td>${c.nama_penilai || '-'}</td>
                                <td>${c.catatan}</td>
                            </tr>
                        `);
                    });
                } else {
                    tbody.append('<tr><td colspan="3" class="text-center">Tidak ada catatan ditemukan.</td></tr>');
                }
                $('#modalLihatCatatan').modal('show');
            }
        });
    };

    // Script for Upload Bukti
    $(document).ready(function() {
        $('#btnSubmitUploadBukti').click(function(e) {
            e.preventDefault();
            
            var formData = new FormData();
            var file = $('#bukti_pdf')[0].files[0];
            
            if (!file) {
                Swal.fire('Peringatan', 'Silakan pilih file PDF terlebih dahulu.', 'warning');
                return;
            }
            
            formData.append('bukti_pdf', file);
            formData.append('periode_awal', $('#periode_awal').val());
            formData.append('periode_akhir', $('#periode_akhir').val());
            formData.append('unit_kantor', '<?= $pegawai_detail->unit_kantor ?? "unit" ?>');
            
            Swal.fire({
                title: 'Mengupload...',
                text: 'Mohon tunggu sebentar',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
            
            $.ajax({
                url: '<?= base_url("Pegawai/uploadBukti") ?>',
                type: 'POST',
                data: formData,
                contentType: false,
                processData: false,
                dataType: 'json',
                success: function(res) {
                    if (res.status === 'success') {
                        Swal.fire('Berhasil', res.message, 'success').then(() => {
                            $('#modalUploadBukti').modal('hide');
                            location.reload();
                        });
                    } else {
                        Swal.fire('Gagal', res.message, 'error');
                    }
                },
                error: function() {
                    Swal.fire('Error', 'Terjadi kesalahan saat mengupload file.', 'error');
                }
            });
        });
    });
</script>

<!-- Modal Upload Bukti -->
<div class="modal fade" id="modalUploadBukti" tabindex="-1" role="dialog" aria-labelledby="modalUploadBuktiLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 12px; overflow: hidden;">
            <div class="modal-header bg-primary text-white text-center d-block position-relative pb-4">
                <h5 class="modal-title font-weight-bold text-white mb-2" id="modalUploadBuktiLabel">
                    <i class="mdi mdi-upload d-block mb-1" style="font-size: 36px; opacity: 0.9;"></i>
                    Upload Bukti PDF
                </h5>
                <small class="text-white-50">Upload bukti penilaian (Format: PDF, Maks 10MB)</small>
                <button type="button" class="close position-absolute" data-dismiss="modal" aria-label="Close" style="top: 15px; right: 20px; color: #fff; text-shadow: none; opacity: 0.8;">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body p-4 bg-light">
                <form id="formUploadBukti">
                    <div class="form-group text-center p-4 bg-white rounded shadow-sm border border-primary">
                        <label for="bukti_pdf" class="font-weight-bold text-dark d-block mb-3">Pilih File Bukti</label>
                        <input type="file" class="form-control-file d-none" id="bukti_pdf" name="bukti_pdf" accept=".pdf">
                        
                        <div class="upload-area cursor-pointer" onclick="$('#bukti_pdf').click();">
                            <div class="upload-icon-wrapper mb-3">
                                <div class="icon-circle bg-primary-light d-inline-flex align-items-center justify-content-center rounded-circle" style="width: 64px; height: 64px; background-color: rgba(0, 123, 255, 0.1);">
                                    <i class="mdi mdi-cloud-upload text-primary" style="font-size: 32px;"></i>
                                </div>
                            </div>
                            <p class="mb-1 text-secondary" id="file-name-display">Klik disini untuk memilih file</p>
                            <small class="text-muted d-block">Atau drag & drop file PDF kesini</small>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer bg-white border-top p-3 d-flex justify-content-between">
                <button type="button" class="btn btn-outline-secondary px-4 py-2 font-weight-bold" data-dismiss="modal" style="border-radius: 8px;">
                    <i class="mdi mdi-close mr-1"></i> Batal
                </button>
                <button type="button" class="btn btn-primary px-4 py-2 font-weight-bold shadow-sm" id="btnSubmitUploadBukti" style="border-radius: 8px;">
                    <i class="mdi mdi-content-save mr-1"></i> Upload Sekarang
                </button>
            </div>
        </div>
    </div>
</div>
<script>
    document.getElementById('bukti_pdf').addEventListener('change', function() {
        var fileName = this.files[0] ? this.files[0].name : "Klik disini untuk memilih file";
        document.getElementById('file-name-display').innerHTML = "<strong>" + fileName + "</strong>";
    });
</script>

<style>
    /* Kustomisasi untuk tooltip kuning */
    .tooltip-kuning .tooltip-inner {
        background-color: #ffc800ff !important;
        /* Warna kuning muda, !important untuk prioritas */
        color: #ffffffff;
        border: 1px solid #ffc800ff;
        max-width: 300px;
        /* Lebar maksimal tooltip */
        padding: 8px 12px;
        /* Sedikit padding */
        text-align: center;
        /* Rata kiri agar ikon dan teks rapi */
        border-radius: .2rem;
        /* Menyamakan radius sudut */
    }

    /* Mengatur warna panah tooltip */
    .tooltip-kuning.bs-tooltip-bottom .arrow::before {
        border-bottom-color: #ffc800ff !important;
    }

    /* Jarak antara ikon dan teks */
    .tooltip-kuning .tooltip-inner i {
        margin-right: 8px;
    }

    /* Animasi tombol lihat catatan */
    .badge-btn-catatan {
        transition: transform 0.2s ease, box-shadow 0.2s ease;
        display: inline-block;
        text-decoration: none !important;
    }

    .badge-btn-catatan:hover {
        transform: scale(1.1);
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.15) !important;
        cursor: pointer;
    }
</style>