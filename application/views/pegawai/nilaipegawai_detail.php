<!-- ============================================================== -->
<!-- Start Page Content here -->
<!-- ============================================================== -->
<style>
    /* Area chat */
    #chat-box-nilai {
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
    #form-chat-nilai {
        margin-top: 12px;
        background: #fff;
        border-radius: 9999px;
        padding: 6px 10px;
        display: flex;
        align-items: center;
        border: 1px solid #e5e7eb;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.05);
    }

    #input-pesan-nilai {
        border: none;
        flex: 1;
        padding: 8px 12px;
        border-radius: 9999px;
        outline: none;
        font-size: 14px;
    }

    #form-chat-nilai button {
        border-radius: 9999px;
        padding: 8px 18px;
        font-weight: 500;
        background: linear-gradient(135deg, #2563eb, #3b82f6);
        border: none;
    }

    #form-chat-nilai button:hover {
        background: linear-gradient(135deg, #1d4ed8, #2563eb);
    }
</style>

<div class="content-page">
    <div class="content">
        <div class="container-fluid">

            <!-- start page title -->
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box">
                        <div class="page-title-right">
                            <ol class="breadcrumb m-0">
                                <li class="breadcrumb-item"><a href="javascript:void(0);">KPI Online-BRKS</a></li>
                                <li class="breadcrumb-item active">Detail Nilai Pegawai</li>
                            </ol>
                        </div>
                        <h5 class="page-title">Detail Pegawai: <b><?= $pegawai_detail->nama; ?></b></h5>
                        <p class="text-muted">
                        <h5>Sistem Penilaian Kinerja Insani PT Bank Riau Kepri Syariah</h5>
                        </p>
                    </div>
                </div>
            </div>
            <!-- end page title -->

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
                                        <h5 class="text-primary mb-3 font-weight-bold">
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

                                <?php
                                // Ambil NIK user login
                                $nik_login = $this->session->userdata('nik');

                                // Tentukan peran user
                                $is_penilai1 = ($pegawai_detail->penilai1_nik ?? '') === $nik_login;
                                $is_penilai2 = ($pegawai_detail->penilai2_nik ?? '') === $nik_login;

                                // Warna tetap untuk header & badge
                                $warna_header_penilai1 = 'text-info';
                                $warna_header_penilai2 = 'text-warning';
                                $badge_penilai1 = 'badge-info';
                                $badge_penilai2 = 'badge-warning';

                                // Warna isi teks (dinamis tergantung siapa yang login)
                                $warna_text_penilai1 = $is_penilai1 ? 'text-info' : 'text-dark';
                                $warna_text_penilai2 = $is_penilai2 ? 'text-warning' : 'text-dark';
                                ?>

                                <!-- Penilai I & Penilai II -->
                                <div class="row">
                                    <!-- Penilai I -->
                                    <div class="col-md-6 mb-3">
                                        <h5 class="<?= $warna_header_penilai1 ?> mb-3 font-weight-bold">
                                            <i class="mdi mdi-account-check-outline mr-2"></i>Penilai I
                                        </h5>
                                        <ul class="list-group list-group-flush">
                                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                                <span class="<?= $warna_text_penilai1 ?> font-weight-medium">NIK</span>
                                                <span class="badge <?= $badge_penilai1 ?> badge-pill"><?= $pegawai_detail->penilai1_nik ?? '-'; ?></span>
                                            </li>
                                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                                <span class="<?= $warna_text_penilai1 ?> font-weight-medium">Nama</span>
                                                <span class="<?= $warna_text_penilai1 ?>"><?= $pegawai_detail->penilai1_nama ?? '-'; ?></span>
                                            </li>
                                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                                <span class="<?= $warna_text_penilai1 ?> font-weight-medium">Jabatan</span>
                                                <span class="<?= $warna_text_penilai1 ?>"><?= $pegawai_detail->penilai1_jabatan_detail ?? '-'; ?></span>
                                            </li>
                                        </ul>
                                    </div>

                                    <!-- Penilai II -->
                                    <div class="col-md-6 mb-3">
                                        <h5 class="<?= $warna_header_penilai2 ?> mb-3 font-weight-bold">
                                            <i class="mdi mdi-account-check-outline mr-2"></i>Penilai II
                                        </h5>
                                        <ul class="list-group list-group-flush">
                                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                                <span class="<?= $warna_text_penilai2 ?> font-weight-medium">NIK</span>
                                                <span class="badge <?= $badge_penilai2 ?> badge-pill"><?= $pegawai_detail->penilai2_nik ?? '-'; ?></span>
                                            </li>
                                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                                <span class="<?= $warna_text_penilai2 ?> font-weight-medium">Nama</span>
                                                <span class="<?= $warna_text_penilai2 ?>"><?= $pegawai_detail->penilai2_nama ?? '-'; ?></span>
                                            </li>
                                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                                <span class="<?= $warna_text_penilai2 ?> font-weight-medium">Jabatan</span>
                                                <span class="<?= $warna_text_penilai2 ?>"><?= $pegawai_detail->penilai2_jabatan_detail ?? '-'; ?></span>
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
                                                <th class="text-center" style="width: min-150px;">Status</th>
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

                                                        $statusClass = 'text-secondary';
                                                        $statusText = 'Belum Dinilai';
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
                                                            <!-- <td><input type="text" class="form-control target-input" value="<?= $i->target ?? ''; ?>" readonly></td>
                                                            <td><input type="date" class="form-control" value="<?= $i->batas_waktu ?? ''; ?>" readonly></td>
                                                            <td><input type="text" class="form-control realisasi-input" value="<?= $i->realisasi ?? ''; ?>" readonly></td>
                                                            <td class="text-center"><input type="text" class="form-control form-control-sm pencapaian-output" readonly></td>
                                                            <td class="text-center"><input type="text" class="form-control form-control-sm nilai-output" readonly></td>
                                                            <td class="text-center"><input type="text" class="form-control form-control-sm nilai-bobot-output" readonly></td> -->

                                                            <td class="text-center align-middle">
                                                                <input type="text" class="form-control text-center target-input"
                                                                    value="<?= $i->target ?? ''; ?>" readonly
                                                                    style="min-width:100px;">
                                                            </td>
                                                            <td class="text-center align-middle" style="min-width:120px;">
                                                                <?= $i->batas_waktu ? date('d-m-Y', strtotime($i->batas_waktu)) : '-'; ?>
                                                            </td>
                                                            <td class="text-center align-middle">
                                                                <?php if (!$is_locked): ?>
                                                                    <input type="text" class="form-control text-center realisasi-input"
                                                                        value="<?= $i->realisasi ?? ''; ?>"
                                                                        style="min-width:100px;">
                                                                <?php else: ?>
                                                                    <input type="text" class="form-control text-center realisasi-input"
                                                                        value="<?= $i->realisasi ?? ''; ?>"
                                                                        style="min-width:100px;" readonly>
                                                                <?php endif; ?>
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

                                                            <td class="text-center align-middle" style="min-width: 150px;">
                                                                <select class="form-select form-select-sm status-select"
                                                                    data-id="<?= $i->id; ?>"
                                                                    data-locked="<?= $is_locked ? '1' : '0'; ?>"
                                                                    <?= $is_locked ? 'disabled' : ''; ?>>
                                                                    <option value="Belum Dinilai" <?= ($i->status == 'Belum Dinilai') ? 'selected' : ''; ?>>Belum Dinilai</option>
                                                                    <option value="Ada Catatan" <?= ($i->status == 'Ada Catatan') ? 'selected' : ''; ?>>Ada Catatan</option>
                                                                    <option value="Disetujui" <?= ($i->status == 'Disetujui') ? 'selected' : ''; ?>>Disetujui</option>
                                                                </select>
                                                            </td>
                                                            <td class="text-center align-middle">
                                                                <button type="button" class="btn btn-sm btn-success simpan-status"
                                                                    data-id="<?= $i->id; ?>"
                                                                    <?= $is_locked ? 'disabled' : ''; ?>>
                                                                    Simpan
                                                                </button>
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
                                <div class="d-flex justify-content-end align-items-center mt-2 gap-2">
                                    <label for="status-semua" class="mb-0"><b>Ubah Semua Status:</b></label>
                                    <select id="status-semua" class="form-select form-select-sm"
                                        style="width: 180px; padding: 0.25rem 0.5rem;"
                                        data-locked="<?= $is_locked ? '1' : '0'; ?>"
                                        <?= $is_locked ? 'disabled' : ''; ?>>
                                        <option value="Belum Dinilai">Belum Dinilai</option>
                                        <option value="Ada Catatan">Ada Catatan</option>
                                        <option value="Disetujui">Disetujui</option>
                                    </select>
                                    <button type="button" id="btn-simpan-semua" class="btn btn-success btn-sm"
                                        style="padding: 0.35rem 0.75rem;"
                                        <?= $is_locked ? 'disabled' : ''; ?>>
                                        Simpan Semua
                                    </button>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>

                <!-- Modal Catatan -->
                <!-- Modal Catatan -->
                <div class="modal fade" id="modalCatatan" tabindex="-1">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <form id="form-catatan">
                                <div class="modal-header">
                                    <h5 class="modal-title">Tambah Catatan</h5>
                                </div>
                                <div class="modal-body">
                                    <input type="hidden" id="indikator_id" name="indikator_id">
                                    <div class="form-group">
                                        <label for="catatan">Catatan</label>
                                        <textarea class="form-control" id="catatan" name="catatan"></textarea>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                                    <button type="submit" id="btn-simpan-catatan" class="btn btn-primary">Simpan</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Form Penilaian Budaya -->
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
                                                <th colspan="8" style="vertical-align: middle;">Budaya Kerja</th>
                                            </tr>
                                            <tr class="bg-success-subtle text-dark fw-bold align-middle">
                                                <th style="width:50px; vertical-align: middle;">No</th>
                                                <th style="width:280px; vertical-align: middle;">Perilaku Utama</th>
                                                <th style="vertical-align: middle;">Panduan Perilaku</th>
                                                <th style="width:80px; vertical-align: middle;"><small>Sangat Jarang</small></th>
                                                <th style="width:80px; vertical-align: middle;"><small>Jarang</small></th>
                                                <th style="width:80px; vertical-align: middle;"><small>Kadang</small></th>
                                                <th style="width:80px; vertical-align: middle;"><small>Sering</small></th>
                                                <th style="width:80px; vertical-align: middle;"><small>Selalu</small></th>
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
                                                            $idRadio = 'budaya_' . $no . '_' . $pIndex;
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

                                                                <?php for ($i = 1; $i <= 5; $i++): ?>
                                                                    <?php
                                                                    $checked = '';
                                                                    if (isset($budaya_nilai[$idRadio]) && $budaya_nilai[$idRadio] == $i) {
                                                                        $checked = 'checked';
                                                                    }
                                                                    ?>
                                                                    <td class="text-center align-middle" style="width:80px; cursor:pointer;"
                                                                        onclick="this.querySelector('input').click();">
                                                                        <input type="radio"
                                                                            class="budaya-radio"
                                                                            name="<?= $idRadio; ?>"
                                                                            value="<?= $i; ?>"
                                                                            <?= $checked; ?>>
                                                                    </td>
                                                                <?php endfor; ?>
                                                            </tr>
                                            <?php
                                                        endforeach;
                                                        $no++;
                                                    endif;
                                                endforeach;
                                            else :
                                                echo '<tr><td colspan="8" class="text-center text-muted">Data budaya belum tersedia.</td></tr>';
                                            endif;
                                            ?>
                                        </tbody>

                                        <tfoot class="text-center fw-bold bg-success text-white">
                                            <tr>
                                                <td colspan="7" class="text-end align-middle">Rata-Rata Nilai Internalisasi Budaya</td>
                                                <td colspan="1">
                                                    <input type="text" id="rata-rata-budaya"
                                                        class="form-control form-control-sm text-center"
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
                                <td class="text-center" id="ratarata-budaya">
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
                                    <button id="btn-simpan-nilai-akhir" class="btn btn-primary"
                                        <?= $is_locked ? 'disabled' : ''; ?>>
                                        <i class="mdi mdi-content-save"></i> Simpan Nilai Akhir
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Form Catatan Penilai dengan DataTables -->
                <div class="row mt-3">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">Catatan Penilai</h5>
                                <!-- <form id="form-catatan" class="mb-3">
                                    <input type="hidden" name="nik_pegawai" value="<?= $pegawai_detail->nik ?>">
                                    <textarea name="catatan" id="catatan" class="form-control mb-2" rows="3" placeholder="Masukkan catatan"></textarea>
                                    <button type="submit" class="btn btn-primary btn-sm">Simpan Catatan</button>
                                </form> -->

                                <div class="table-responsive">
                                    <table class="table table-sm table-bordered" id="tabel-catatan">
                                        <thead class="table-light">
                                            <tr>
                                                <th>No</th>
                                                <th>Penilai</th>
                                                <th>Catatan</th>
                                                <th>Tanggal</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php $catatan_list = $this->Nilai_model->getCatatanByPegawai($pegawai_detail->nik); ?>
                                            <?php $no = 1;
                                            foreach ($catatan_list as $c):
                                                $tgl = new DateTime($c->tanggal, new DateTimeZone('UTC'));
                                                $tgl->setTimezone(new DateTimeZone('Asia/Jakarta'));
                                            ?>
                                                <tr>
                                                    <td><?= $no++ ?></td>
                                                    <td><?= $c->nama_penilai ?></td>
                                                    <td><?= $c->catatan ?></td>
                                                    <td><?= $tgl->format('d-m-Y H:i') ?></td>
                                                </tr>
                                            <?php endforeach; ?>
                                            <?php if (empty($catatan_list)) : ?>
                                                <tr>
                                                    <td colspan="4" class="text-center">Belum ada catatan</td>
                                                </tr>
                                            <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ================== ROOM CHAT ================== -->
                <div class="row mt-4">
                    <div class="col-12">
                        <div class="card shadow-sm">
                            <div class="card-body">
                                <h5 class="card-title mb-3"> <i class="fas fa-comments text-primary mr-2"></i>Coaching Kinerja</h5>

                                <!-- Kotak chat -->
                                <div id="chat-box-nilai">
                                    <!-- pesan akan di-load via AJAX -->
                                </div>

                                <!-- Form input pesan -->
                                <form id="form-chat-nilai" class="mt-3">
                                    <input type="hidden" name="nik_pegawai" value="<?= $pegawai_detail->nik ?>">
                                    <input type="hidden" name="nik_penilai" value="<?= $pegawai_detail->penilai1_nik ?? $pegawai_detail->penilai2_nik ?>">

                                    <input type="text" name="pesan" id="input-pesan-nilai" placeholder="Tulis pesan...">
                                    <button type="submit" class="btn btn-primary">Kirim</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>



            <?php } ?>
        </div>
    </div>
</div>

<!-- Script di bawah -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const nik = document.getElementById('nik').value;
        const periodeAwal = document.getElementById('periode_awal');
        const periodeAkhir = document.getElementById('periode_akhir');
        const periodeHistory = document.getElementById('periode_history');

        // Validasi periode
        periodeAwal.addEventListener('change', () => {
            if (periodeAkhir.value < periodeAwal.value) periodeAkhir.value = periodeAwal.value;
        });
        periodeAkhir.addEventListener('change', () => {
            if (periodeAkhir.value < periodeAwal.value) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Periode salah',
                    text: 'Tanggal akhir tidak boleh lebih kecil dari tanggal awal',
                    confirmButtonColor: '#d33'
                });
                periodeAkhir.value = periodeAwal.value;
            }
        });

        // Saat pilih dropdown, update input tanggal
        periodeHistory.addEventListener('change', function() {
            const val = this.value;
            if (!val) return;

            const [awal, akhir] = val.split('|');
            periodeAwal.value = awal;
            periodeAkhir.value = akhir;
        });

        // Tombol sesuaikan periode → redirect ke URL dengan periode
        document.getElementById('btn-sesuaikan-periode').addEventListener('click', () => {
            const awal = periodeAwal.value;
            const akhir = periodeAkhir.value;

            if (!awal || !akhir) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Periode belum lengkap',
                    text: 'Silakan pilih periode awal dan akhir.'
                });
                return;
            }

            // Redirect ke halaman nilai pegawai dengan periode terpilih
            window.location.href = `<?= base_url('Pegawai/nilaiPegawaiDetail/') ?>${nik}?awal=${awal}&akhir=${akhir}`;
        });

        function formatAngka(nilai) {
            let n = parseFloat(nilai);
            return isNaN(n) ? '' : Number.isInteger(n) ? n.toString() : n.toFixed(2);
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

            // Ambil nilai sasaran dari total-nilai-bobot
            const totalSasaran = parseFloat(document.getElementById("total-nilai-bobot").textContent) || 0;
            const rataBudaya = parseFloat(document.getElementById("ratarata-budaya").textContent) || 0;

            // Total nilai sasaran kerja 
            const nilaiSasaran = totalSasaran * bobotSasaran;

            // Nilai budaya
            const nilaiBudaya = rataBudaya * bobotBudaya;

            // Total nilai
            const totalNilai = nilaiSasaran + nilaiBudaya;

            // Nilai akhir sesuai rumus Excel
            // let nilaiAkhir;
            // if (fraud === 0) {
            //     nilaiAkhir = totalNilai - fraud;
            // } else {
            //     nilaiAkhir = "Tidak ada nilai";
            // }

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

            // Pencapaian Akhir
            let pencapaian = "";
            if (nilaiAkhir !== "Tidak ada nilai") {
                const v = parseFloat(nilaiAkhir) || 0;
                if (v < 0) pencapaian = 0;
                else if (v < 2) pencapaian = (v / 2) * 0.8 * 100;
                else if (v < 3) pencapaian = 80 + ((v - 2) / 1) * 10;
                else if (v < 3.5) pencapaian = 90 + ((v - 3) / 0.5) * 20;
                else if (v < 4.5) pencapaian = 110 + ((v - 3.5) / 1) * 10;
                else if (v < 5) pencapaian = 120 + ((v - 4.5) / 0.5) * 10;
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

        document.getElementById('btn-sesuaikan-periode').addEventListener('click', () => {
            window.location.href = `<?= base_url("Pegawai/nilaiPegawaiDetail/") ?>${nik}?awal=${periodeAwal.value}&akhir=${periodeAkhir.value}`;
        });

        // Gabungkan simpan status + realisasi
        document.querySelectorAll('.simpan-status').forEach(btn => {
            btn.addEventListener('click', function() {
                const row = this.closest('tr');
                const id = row.dataset.id;

                const status = row.querySelector('.status-select').value;
                const realisasi = row.querySelector('.realisasi-input')?.value || '';
                const pencapaian = row.querySelector('.pencapaian-output')?.value || '';
                const nilai = row.querySelector('.nilai-output')?.value || '';
                const nilai_dibobot = row.querySelector('.nilai-bobot-output')?.value || '';

                if (status === "Ada Catatan") {
                    // buka modal catatan
                    $('#modalCatatan').modal('show');

                    // simpan indikator_id di hidden input
                    document.getElementById('indikator_id').value = id;

                    return; // stop di sini, tunggu submit catatan
                }

                // selain "Ada Catatan" → simpan langsung
                simpanStatus(id, status, realisasi, pencapaian, nilai, nilai_dibobot);
            });
        });

        function simpanStatus(id, status, realisasi, pencapaian, nilai, nilai_dibobot) {
            fetch("<?= base_url('Pegawai/updateStatus'); ?>", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/x-www-form-urlencoded"
                    },
                    body: `id=${id}&status=${encodeURIComponent(status)}&realisasi=${encodeURIComponent(realisasi)}&pencapaian=${encodeURIComponent(pencapaian)}&nilai=${encodeURIComponent(nilai)}&nilai_dibobot=${encodeURIComponent(nilai_dibobot)}`
                })
                .then(res => res.json())
                .then(data => {
                    if (!data.success) throw new Error("Gagal update status");
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil simpan!'
                    });
                    hitungTotal();
                })
                .catch(err => Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: err.message
                }));
        }


        // Update semua status sekaligus
        document.getElementById('btn-simpan-semua').addEventListener('click', () => {
            const status = document.getElementById('status-semua').value;
            const ids = Array.from(document.querySelectorAll('#tabel-penilaian tbody tr[data-id]')).map(r => r.dataset.id);
            fetch("<?= base_url('Pegawai/updateStatusAll'); ?>", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/x-www-form-urlencoded"
                    },
                    body: `ids=${encodeURIComponent(ids.join(','))}&status=${encodeURIComponent(status)}`
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) document.querySelectorAll('.status-select').forEach(s => s.value = status);
                    Swal.fire({
                        icon: data.success ? 'success' : 'error',
                        title: data.message,
                        timer: 1500,
                        showConfirmButton: false
                    });
                });
        });

        // ==== Custom sorting untuk tanggal DD-MM-YYYY HH:MM ====
        $.extend($.fn.dataTableExt.oSort, {
            "date-uk-pre": function(a) {
                if (!a) return 0;
                // a = "07-10-2025 10:30"
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

        // ==== DataTables Catatan ====
        var tableCatatan = $('#tabel-catatan').DataTable({
            responsive: false,
            paging: true,
            searching: true,
            ordering: true,
            order: [
                [3, 'desc']
            ], // kolom tanggal
            columnDefs: [{
                    orderable: false,
                    targets: [2]
                }, // kolom catatan
                {
                    type: 'date-uk',
                    targets: 3
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

        // ==== AJAX Form Catatan ====
        $(document).ready(function() {
            $('#form-catatan').on('submit', function(e) {
                e.preventDefault();

                const catatan = $('#catatan').val().trim();
                const indikator_id = $('#indikator_id').val();

                if (catatan === '') {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Catatan kosong'
                    });
                    return;
                }

                fetch("<?= base_url('pegawai/simpan_catatan'); ?>", {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/x-www-form-urlencoded"
                        },
                        body: `indikator_id=${indikator_id}&nik_pegawai=${nik}&catatan=${encodeURIComponent(catatan)}`
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (data.success) {
                            // format tanggal konsisten DD-MM-YYYY HH:MM
                            const now = new Date();
                            const tanggal =
                                String(now.getDate()).padStart(2, '0') + '-' +
                                String(now.getMonth() + 1).padStart(2, '0') + '-' +
                                now.getFullYear() + ' ' +
                                String(now.getHours()).padStart(2, '0') + ':' +
                                String(now.getMinutes()).padStart(2, '0');

                            // tambahkan row baru
                            tableCatatan.row.add([
                                '', // nomor otomatis
                                data.nama_penilai, // nama penilai dari response
                                catatan, // catatan
                                tanggal // tanggal baru
                            ]).draw();

                            // paksa sorting ulang setelah row baru ditambahkan
                            tableCatatan.order([3, 'desc']).draw();

                            // update status indikator jika ada
                            simpanStatus(indikator_id, "Ada Catatan", "", "", "", "");

                            // reset form & tutup modal
                            $('#form-catatan')[0].reset();
                            $('#modalCatatan').modal('hide');
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal',
                                text: data.message
                            });
                        }
                    })
                    .catch(err => Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Server error'
                    }));
            });
        });



        // ================== CHAT COACHING ==================
        $(document).ready(function() {
            let lastId = 0; // id terakhir pesan

            // Format ke waktu Jakarta (WIB) dengan detik
            function formatToJakartaTime(dateStr) {
                if (!dateStr) return '';
                const [date, time] = dateStr.split(' ');
                if (!date || !time) return dateStr;
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
            function loadChatNilai() {
                var nikPegawai = $('input[name="nik_pegawai"]').val();
                var nikPenilai = $('input[name="nik_penilai"]').val();

                $.getJSON("<?= base_url('Pegawai/getCoachingChat/') ?>" + nikPegawai + "/" + nikPenilai + "?lastId=" + lastId, function(data) {
                    if (data.length > 0) {
                        data.forEach(function(row) {
                            let isMe = row.pengirim_nik === "<?= $this->session->userdata('nik'); ?>";
                            let jamWIB = formatToJakartaTime(row.created_at);

                            $('#chat-box-nilai').append(`
                        <div class="chat-message ${isMe ? 'me' : 'other'}">
                            <div class="chat-name">${row.nama_pengirim} (${row.jabatan})</div>
                            <div>${row.pesan}</div>
                            <div class="chat-meta">${jamWIB}</div>
                        </div>
                    `);

                            lastId = row.id; // update id terakhir
                        });

                        // auto scroll ke bawah
                        $('#chat-box-nilai').scrollTop($('#chat-box-nilai')[0].scrollHeight);
                    } else if (lastId === 0) {
                        // pertama kali load & tidak ada pesan
                        $('#chat-box-nilai').html('<div class="text-center text-muted">Belum ada pesan</div>');
                    }
                });
            }

            // Load awal
            loadChatNilai();

            // Reload tiap 5 detik (polling)
            setInterval(loadChatNilai, 5000);

            // Kirim pesan
            $('#form-chat-nilai').on('submit', function(e) {
                e.preventDefault();
                var formData = $(this).serialize();

                $.ajax({
                    url: "<?= base_url('Pegawai/kirimCoachingPesan') ?>",
                    method: "POST",
                    data: formData,
                    dataType: "json",
                    success: function(res) {
                        if (res.success) {
                            $('#input-pesan-nilai').val('');
                            loadChatNilai(); // langsung cek ada pesan baru
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
        });

        document.getElementById('btn-simpan-nilai-akhir').addEventListener('click', function() {
            const nik = document.getElementById('nik').value;
            const periode_awal = document.getElementById('periode_awal').value;
            const periode_akhir = document.getElementById('periode_akhir').value;

            const nilai_sasaran = document.getElementById('total-sasaran').textContent;
            const nilai_budaya = document.getElementById('ratarata-budaya').textContent;
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

        // =================== NILAI BUDAYA ==================
        const radios = document.querySelectorAll('.budaya-radio');
        radios.forEach(radio => {
            radio.addEventListener('change', function() {
                updateRataBudaya();
                simpanNilaiBudaya(this);
            });
        });

        // 🔹 Rehitung rata-rata awal (jika sudah ada nilai sebelumnya)
        updateRataBudaya();

        function simpanNilaiBudaya(radio) {
            const nikPegawai = "<?= $pegawai_detail->nik ?>";
            const periodeAwal = "<?= $periode_awal ?>";
            const periodeAkhir = "<?= $periode_akhir ?>";
            const rataRata = document.getElementById('rata-rata-budaya').value;
            const key = radio.name;

            $.ajax({
                url: "<?= base_url('Pegawai/simpanNilaiBudaya') ?>",
                method: "POST",
                data: {
                    nik_pegawai: nikPegawai,
                    key: key,
                    skor: radio.value,
                    periode_awal: periodeAwal,
                    periode_akhir: periodeAkhir,
                    rata_rata: rataRata
                },
                success: function(res) {
                    console.log("✅ Nilai budaya tersimpan:", res);
                },
                error: function() {
                    alert("Gagal menyimpan nilai budaya!");
                }
            });
        }

        function updateRataBudaya() {
            let total = 0,
                count = 0;
            document.querySelectorAll('.budaya-radio:checked').forEach(r => {
                total += parseInt(r.value);
                count++;
            });

            const rata = count > 0 ? (total / count).toFixed(2) : 0;
            document.getElementById('rata-rata-budaya').value = rata;

            // 🔹 Sinkronkan ke tampilan Nilai Akhir
            const targetNilaiAkhir = document.getElementById('ratarata-budaya');
            if (targetNilaiAkhir) {
                targetNilaiAkhir.textContent = rata;
            }

            // 🔹 Update tampilan nilai budaya (hasil kali 0.05)
            const nilaiBudayaCell = document.getElementById("nilai-budaya");
            if (nilaiBudayaCell) {
                const nilaiBudayaBaru = (rata * 0.05).toFixed(2);
                nilaiBudayaCell.textContent = nilaiBudayaBaru;
            }

            // 🔹 Rehitung Nilai Akhir biar langsung update total
            hitungNilaiAkhir();
        }
    });
</script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const isLocked = <?= $is_locked ? 'true' : 'false' ?>;
        if (isLocked) {
            document.querySelectorAll('.target-input, .realisasi-input, .simpan-penilaian, #btn-simpan-nilai-akhir')
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