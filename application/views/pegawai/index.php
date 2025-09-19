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
                    <div class="page-title-box">
                        <div class="page-title-right">
                            <ol class="breadcrumb m-0">
                                <li class="breadcrumb-item"><a href="javascript:void(0);">SKI-BRKS</a></li>
                                <li class="breadcrumb-item active">Dashboard Pegawai</li>
                            </ol>
                        </div>
                        <h5 class="page-title">Selamat Datang, <b><?= $pegawai_detail->nama; ?></b>!</h5>
                        <p class="text-muted">
                        <h5>Sistem Penilaian Kinerja Insani PT Bank Riau Kepri Syariah</h5>
                        </p>
                    </div>
                </div>
            </div>
            <!-- end page title -->

            <?php if (isset($pegawai_detail) && $pegawai_detail) { ?>
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">

                                <!-- Detail Pegawai & Informasi Penilaian -->
                                <div class="row mb-0.25">
                                    <div class="col-md-6">
                                        <h5>Detail Pegawai</h5>
                                        <p><b>NIK:</b> <?= $pegawai_detail->nik; ?></p>
                                        <p><b>Nama:</b> <?= $pegawai_detail->nama; ?></p>
                                        <p><b>Jabatan:</b> <?= $pegawai_detail->jabatan; ?></p>
                                        <p><b>Unit Kantor:</b> <?= $pegawai_detail->unit_kerja; ?></p>

                                        <input type="hidden" id="nik" value="<?= $pegawai_detail->nik ?>">
                                    </div>

                                    <div class="col-md-6">
                                        <h5>Informasi Penilaian</h5>
                                        <!-- Pilih Periode Penilaian -->
                                        <div class="form-inline mb-2">
                                            <label class="mr-2"><b>Periode Penilaian:</b></label>
                                            <input type="date" id="periode_awal" class="form-control mr-2"
                                                value="<?= $periode_awal ?? date('Y-01-01'); ?>">
                                            <span class="mr-2">s/d</span>
                                            <input type="date" id="periode_akhir" class="form-control mr-2"
                                                value="<?= $periode_akhir ?? date('Y-12-31'); ?>">
                                        </div>
                                        <div class="d-flex justify-content-end mb-2">
                                            <button type="button" id="btn-sesuaikan-periode" class="btn btn-primary btn-sm">Sesuaikan Periode</button>
                                        </div>
                                        <p><b>Unit Kantor Penilai:</b> <?= $pegawai_detail->unit_kerja; ?></p>
                                    </div>
                                </div>

                                <hr>

                                <!-- Penilai I & Penilai II -->
                                <!-- Penilai I & Penilai II -->
                                <div class="row">
                                    <div class="col-md-6">
                                        <h5>Penilai I</h5>
                                        <p><b>NIK:</b> <?= $pegawai_detail->penilai1_nik ?? '-'; ?></p>
                                        <p><b>Nama:</b> <?= $pegawai_detail->penilai1_nama ?? '-'; ?></p>
                                        <p><b>Jabatan:</b> <?= $pegawai_detail->penilai1_jabatan_detail ?? '-'; ?></p>
                                    </div>

                                    <div class="col-md-6">
                                        <h5>Penilai II</h5>
                                        <p><b>NIK:</b> <?= $pegawai_detail->penilai2_nik ?? '-'; ?></p>
                                        <p><b>Nama:</b> <?= $pegawai_detail->penilai2_nama ?? '-'; ?></p>
                                        <p><b>Jabatan:</b> <?= $pegawai_detail->penilai2_jabatan_detail ?? '-'; ?></p>
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

                                                            <td><input type="text" class="form-control target-input" value="<?= $i->target ?? ''; ?>"></td>
                                                            <td><input type="date" class="form-control" value="<?= $i->batas_waktu ?? ''; ?>"></td>
                                                            <td><input type="text" class="form-control realisasi-input" value="<?= $i->realisasi ?? ''; ?>"></td>

                                                            <td class="text-center"><input type="text" class="form-control form-control-sm pencapaian-output" readonly></td>
                                                            <td class="text-center"><input type="text" class="form-control form-control-sm nilai-output" readonly></td>
                                                            <td class="text-center"><input type="text" class="form-control form-control-sm nilai-bobot-output" readonly></td>

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

            <?php } ?>
        </div>
    </div>
</div>
<!-- ============================================================== -->
<!-- End Page Content here -->
<!-- ============================================================== -->

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

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
    });
</script>