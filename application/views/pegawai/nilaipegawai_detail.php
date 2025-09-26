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
                                <li class="breadcrumb-item"><a href="javascript:void(0);">SKI-BRKS</a></li>
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
                                                <span class="text-dark"><?= $pegawai_detail->penilai1_jabatan_detail ?? '-'; ?></span>
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
                                                <span class="text-dark"><?= $pegawai_detail->penilai2_jabatan_detail ?? '-'; ?></span>
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
                                <h5>Form Penilaian</h5>
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
                                                        <tr data-id="<?= $id; ?>" data-bobot="<?= $bobot; ?>" data-perspektif="<?= $persp; ?>">
                                                            <?php if ($first_persp_cell) { ?>
                                                                <td rowspan="<?= $persp_rows; ?>" style="vertical-align:middle;font-weight:600;background:#C8E6C9;"><?= $persp; ?></td>
                                                            <?php $first_persp_cell = false;
                                                            } ?>
                                                            <?php if ($first_sas_cell) { ?>
                                                                <td rowspan="<?= $sasaran_rows; ?>" style="vertical-align:middle;background:#E3F2FD;"><?= $sasaran; ?></td>
                                                            <?php $first_sas_cell = false;
                                                            } ?>
                                                            <td class="text-center"><?= $bobot; ?>
                                                                <input type="hidden" class="bobot" value="<?= $bobot ?>">
                                                            </td>
                                                            <td><?= $indik; ?></td>
                                                            <!-- <td><input type="text" class="form-control target-input" value="<?= $i->target ?? ''; ?>" readonly></td>
                                                            <td><input type="date" class="form-control" value="<?= $i->batas_waktu ?? ''; ?>" readonly></td>
                                                            <td><input type="text" class="form-control realisasi-input" value="<?= $i->realisasi ?? ''; ?>" readonly></td>
                                                            <td class="text-center"><input type="text" class="form-control form-control-sm pencapaian-output" readonly></td>
                                                            <td class="text-center"><input type="text" class="form-control form-control-sm nilai-output" readonly></td>
                                                            <td class="text-center"><input type="text" class="form-control form-control-sm nilai-bobot-output" readonly></td> -->

                                                            <td>
                                                                <input type="text" class="form-control text-center target-input"
                                                                    value="<?= $i->target ?? ''; ?>" readonly
                                                                    style="min-width:100px;">
                                                            </td>
                                                            <td class="text-center" style="min-width:100px;">
                                                                <?= $i->batas_waktu ? date('d-m-Y', strtotime($i->batas_waktu)) : '-'; ?>
                                                            </td>
                                                            <td>
                                                                <input type="text" class="form-control text-center realisasi-input"
                                                                    value="<?= $i->realisasi ?? ''; ?>"
                                                                    style="min-width:100px;">
                                                            </td>

                                                            <td>
                                                                <input type="text" class="form-control form-control-sm text-center pencapaian-output"
                                                                    readonly style="min-width:50px;">
                                                            </td>
                                                            <td>
                                                                <input type="text" class="form-control form-control-sm text-center nilai-output"
                                                                    readonly style="min-width:60px;">
                                                            </td>
                                                            <td>
                                                                <input type="text" class="form-control form-control-sm text-center nilai-bobot-output"
                                                                    readonly style="min-width:50px;">
                                                            </td>

                                                            <td class="text-center" style="min-width: 150px;">
                                                                <select class="form-select form-select-sm status-select">
                                                                    <option value="Belum Dinilai" <?= ($i->status == 'Belum Dinilai') ? 'selected' : ''; ?>>Belum Dinilai</option>
                                                                    <option value="Ada Catatan" <?= ($i->status == 'Ada Catatan') ? 'selected' : ''; ?>>Ada Catatan</option>
                                                                    <option value="Disetujui" <?= ($i->status == 'Disetujui') ? 'selected' : ''; ?>>Disetujui</option>
                                                                </select>
                                                            </td>
                                                            <td class="text-center">
                                                                <button type="button" class="btn btn-sm btn-success simpan-status">Simpan</button>
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
                                    <select id="status-semua" class="form-select form-select-sm" style="width: 180px; padding: 0.25rem 0.5rem;">
                                        <option value="Belum Dinilai">Belum Dinilai</option>
                                        <option value="Ada Catatan">Ada Catatan</option>
                                        <option value="Disetujui">Disetujui</option>
                                    </select>
                                    <button type="button" id="btn-simpan-semua" class="btn btn-success btn-sm" style="padding: 0.35rem 0.75rem;">Simpan Semua</button>
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
                                <form id="form-catatan" class="mb-3">
                                    <input type="hidden" name="nik_pegawai" value="<?= $pegawai_detail->nik ?>">
                                    <textarea name="catatan" id="catatan" class="form-control mb-2" rows="3" placeholder="Masukkan catatan"></textarea>
                                    <button type="submit" class="btn btn-primary btn-sm">Simpan Catatan</button>
                                </form>

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

        function hitungRow(row) {
            const target = parseFloat(row.querySelector('.target-input').value) || 0;
            const realisasi = parseFloat(row.querySelector('.realisasi-input').value) || 0;
            const bobot = parseFloat(row.querySelector('.bobot').value) || 0;
            let pencapaian = 0,
                nilai = 0,
                nilaiBobot = 0;
            if (target > 0) {
                pencapaian = (realisasi / target) * 100;
                nilai = Math.min(pencapaian, 100);
                nilaiBobot = (nilai * bobot) / 100;
            }
            row.querySelector('.pencapaian-output').value = formatAngka(pencapaian);
            row.querySelector('.nilai-output').value = formatAngka(nilai);
            row.querySelector('.nilai-bobot-output').value = formatAngka(nilaiBobot);
            return {
                bobot,
                nilaiBobot,
                perspektif: row.dataset.perspektif
            };
        }

        function hitungTotal() {
            let totalBobot = 0,
                totalNilai = 0,
                subtotalMap = {};
            document.querySelectorAll('#tabel-penilaian tbody tr[data-id]').forEach(row => {
                const {
                    bobot,
                    nilaiBobot,
                    perspektif
                } = hitungRow(row);
                totalBobot += bobot;
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
        }

        hitungTotal();
        document.querySelectorAll('.target-input, .realisasi-input').forEach(input => input.addEventListener('input', hitungTotal));

        document.getElementById('btn-sesuaikan-periode').addEventListener('click', () => {
            window.location.href = `<?= base_url("Pegawai/nilaiPegawaiDetail/") ?>${nik}?awal=${periodeAwal.value}&akhir=${periodeAkhir.value}`;
        });

        // Gabungkan simpan status + realisasi
        document.querySelectorAll('.simpan-status').forEach(btn => {
            btn.addEventListener('click', function() {
                const row = this.closest('tr');
                const id = row.dataset.id;

                // ambil data status
                const status = row.querySelector('.status-select').value;

                // ambil data penilaian
                const indikator_id = row.dataset.id;
                const realisasi = row.querySelector('.realisasi-input')?.value || '';
                const pencapaian = row.querySelector('.pencapaian-output')?.value || '';
                const nilai = row.querySelector('.nilai-output')?.value || '';
                const nilai_dibobot = row.querySelector('.nilai-bobot-output')?.value || '';

                const periode_awal = periodeAwal.value;
                const periode_akhir = periodeAkhir.value;

                // 1️⃣ Simpan status dulu
                fetch("<?= base_url('Pegawai/updateStatus'); ?>", {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/x-www-form-urlencoded"
                        },
                        body: `id=${id}&status=${encodeURIComponent(status)}&realisasi=${encodeURIComponent(realisasi)}&pencapaian=${encodeURIComponent(pencapaian)}&nilai=${encodeURIComponent(nilai)}&nilai_dibobot=${encodeURIComponent(nilai_dibobot)}`
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (!data.success) {
                            throw new Error(data.message || "Gagal update status & realisasi");
                        }
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil',
                            text: 'Status & Realisasi berhasil disimpan!',
                            timer: 2000,
                            showConfirmButton: false
                        });
                        hitungTotal();
                    })
                    .catch(err => {
                        console.error(err);
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: err.message || 'Terjadi kesalahan server',
                            confirmButtonColor: '#d33'
                        });
                    });

            });
        });

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

        // ==== DataTables Catatan ====
        var tableCatatan = $('#tabel-catatan').DataTable({
            responsive: false,
            paging: true,
            searching: true,
            ordering: true,
            order: [
                [3, 'desc']
            ],
            columnDefs: [{
                orderable: false,
                targets: [2]
            }],
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
            // 🔹 Atur layout DOM
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


        // AJAX Form Catatan
        $('#form-catatan').on('submit', function(e) {
            e.preventDefault();
            const catatan = $('#catatan').val().trim();
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
                    body: `nik_pegawai=${nik}&catatan=${encodeURIComponent(catatan)}`
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

                        // Ambil tanggal sekarang, format dd-mm-yyyy HH:MM
                        const tanggal = new Date().toLocaleString('id-ID', {
                            day: '2-digit',
                            month: '2-digit',
                            year: 'numeric',
                            hour: '2-digit',
                            minute: '2-digit'
                        });

                        // Tambahkan baris baru, pastikan nama penilai dari server
                        tableCatatan.row.add([
                            '', // No otomatis oleh drawCallback
                            data.nama_penilai, // ambil dari server
                            catatan,
                            tanggal
                        ]).draw(false);


                        $('#form-catatan')[0].reset();
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal',
                            text: data.message
                        });
                    }
                })
                .catch(err => {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Terjadi kesalahan server'
                    });
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
    });
</script>