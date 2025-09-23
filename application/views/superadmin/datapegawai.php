<div class="content-page">
    <div class="content">
        <div class="container-fluid">

            <!-- Judul -->
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box">
                        <div class="page-title-right">
                            <ol class="breadcrumb m-0">
                                <li class="breadcrumb-item"><a href="javascript:void(0);">SKI-BRKS</a></li>
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
                            <form action="<?= base_url('SuperAdmin/cariDataPegawai'); ?>" method="post">
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


                                <a href="<?= base_url('SuperAdmin/downloadDataPegawai?nik=' . ($pegawai_detail->nik ?? '') . '&awal=' . $periode_awal . '&akhir=' . $periode_akhir) ?>"
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
                        <h5>Hasil Penilaian</h5>
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
                                            foreach ($arr as $items)
                                                $sum += count($items);
                                            return $sum;
                                        }
                                        ?>

                                        <?php foreach ($order as $persp): ?>
                                            <?php if (empty($grouped[$persp]))
                                                continue; ?>
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
                                                    $subtotal_bobot += $i->bobot;
                                                    $subtotal_nilai += $i->nilai_dibobot ?? 0;
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
                                                <td class="text-center"><?= number_format($subtotal_nilai, 2); ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="10" class="text-center">Belum ada data penilaian untuk pegawai
                                                ini</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                                <?php if (!empty($penilaian_pegawai)): ?>
                                    <tfoot style="background-color:#2E7D32;color:#fff;font-weight:bold;text-align:center;">
                                        <tr>
                                            <td colspan="3">Total</td>
                                            <td><?= array_sum(array_column($penilaian_pegawai, 'bobot')); ?></td>
                                            <td colspan="5">Total Nilai Dibobot</td>
                                            <td><?= number_format(array_sum(array_column($penilaian_pegawai, 'nilai_dibobot')), 2); ?>
                                            </td>
                                        </tr>
                                    </tfoot>
                                <?php endif; ?>
                            </table>
                        </div>
                    </div>
                </div>
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

        window.location.href = "<?= base_url('SuperAdmin/cariDataPegawai') ?>?nik=" + nik + "&awal=" + awal + "&akhir=" + akhir;
    });
</script>

<script>
    document.getElementById('btn-sesuaikan-periode').addEventListener('click', function() {
        let periode = document.getElementById('periode_select').value.split('|');
        let awal = periode[0];
        let akhir = periode[1];
        let nik = "<?= $pegawai_detail->nik ?? '' ?>";

        window.location.href = "<?= base_url('SuperAdmin/cariDataPegawai') ?>?nik=" + nik + "&awal=" + awal + "&akhir=" + akhir;
    });
</script>