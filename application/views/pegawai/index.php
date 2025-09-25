<!-- ============================================================== -->
<!-- Start Page Content here -->
<!-- ============================================================== -->

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
                                <li class="breadcrumb-item"><a href="javascript:void(0);">SKI-BRKS</a></li>
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

                                                            <td>
                                                                <input type="text" class="form-control text-center target-input"
                                                                    value="<?= $i->target ?? ''; ?>" readonly
                                                                    style="min-width:100px;">
                                                            </td>
                                                            <td class="text-center" style="min-width:120px;">
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


                                                            <td class="text-center">
                                                                <span class="<?= $statusClass; ?>"><?= $statusText; ?></span>
                                                            </td>
                                                            <td class="text-center">
                                                                <button type="button" class="btn btn-sm btn-primary simpan-penilaian">Simpan</button>
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

                <!-- ================== FORM CHATF ================== -->
                <div class="card mb-3">
                    <div class="card-body">
                        <h5 class="card-title">Aktivitas Coaching</h5>
                        <div id="chat-box" style="height:250px; overflow-y:auto; border:1px solid #ddd; padding:10px;">
                            <!-- pesan akan di-load via AJAX -->
                        </div>
                        <form id="form-chat" class="mt-2 d-flex">
                            <input type="hidden" name="nik_pegawai" value="<?= $pegawai_detail->nik ?>">
                            <input type="hidden" name="nik_penilai" value="<?= $pegawai_detail->penilai1_nik ?? $pegawai_detail->penilai2_nik ?>">
                            <input type="text" name="pesan" id="input-pesan" class="form-control mr-2" placeholder="Tulis pesan...">
                            <button type="submit" class="btn btn-primary">Kirim</button>
                        </form>
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
                totalNilai = 0;
            const subtotalMap = {};

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

        // trigger perhitungan saat input diubah
        document.querySelectorAll('.target-input, .realisasi-input').forEach(input => {
            input.addEventListener('input', hitungTotal);
        });
        hitungTotal();

        // Simpan penilaian per baris
        document.querySelectorAll('.simpan-penilaian').forEach(btn => {
            btn.addEventListener('click', function() {
                const row = this.closest('tr');
                const indikator_id = row.dataset.id;
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
                        body: `indikator_id=${indikator_id}&realisasi=${encodeURIComponent(realisasi)}&pencapaian=${encodeURIComponent(pencapaian)}&nilai=${encodeURIComponent(nilai)}&nilai_dibobot=${encodeURIComponent(nilai_dibobot)}&periode_awal=${encodeURIComponent(periode_awal)}&periode_akhir=${encodeURIComponent(periode_akhir)}`
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
                } // kolom nomor
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

                        const tanggal = new Date().toLocaleString('id-ID', {
                            day: '2-digit',
                            month: '2-digit',
                            year: 'numeric',
                            hour: '2-digit',
                            minute: '2-digit'
                        });

                        tableCatatanPegawai.row.add([
                            '', // nomor auto
                            catatan,
                            tanggal
                        ]).draw(false);

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
    });
</script>

<script>
    // Load chat setiap 5 detik (auto refresh ringan)
    function loadChat() {
        const nikPegawai = $('input[name="nik_pegawai"]').val();
        const nikPenilai = $('input[name="nik_penilai"]').val();

        $.getJSON("<?= base_url('Pegawai/getCoachingChat/') ?>" + nikPegawai + "/" + nikPenilai, function(data) {
            let html = '';
            data.forEach(function(row) {
                html += `
                    <div class="mb-2 p-2 border rounded ${row.pengirim_nik === "<?= $this->session->userdata('nik'); ?>" ? 'bg-primary text-white' : 'bg-light'}">
                        <div class="fw-bold">${row.nama_pengirim} (${row.jabatan})</div>
                        <div>${row.pesan}</div>
                        <div class="text-muted" style="font-size:12px;">${row.created_at}</div>
                    </div>`;
            });
            $('#chat-box').html(html);
            $('#chat-box').scrollTop($('#chat-box')[0].scrollHeight);
        });
    }

    // Load awal
    loadChat();
    // Refresh tiap 5 detik
    setInterval(loadChat, 5000);

    // Kirim pesan
    $('#form-chat').on('submit', function(e) {
        e.preventDefault(); // cegah reload
        const formData = $(this).serialize();

        $.ajax({
            url: "<?= base_url('Pegawai/kirimCoachingPesan') ?>",
            method: "POST",
            data: formData,
            dataType: "json",
            success: function(res) {
                if (res.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Terkirim!',
                        text: 'Pesan coaching berhasil dikirim',
                        timer: 1500,
                        showConfirmButton: false
                    });
                    $('#input-pesan').val('');
                    loadChat();
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