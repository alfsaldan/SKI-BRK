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
                        <h4 class="page-title">Data Kinerja Pegawai</h4>
                    </div>
                </div>
            </div>

            <!-- Form cari NIK -->
            <div class="row">
                <div class="col-6">
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
                        <div class="card mt-3">
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

                                        <!-- 🔹 Pilih Periode Penilaian -->
                                        <div class="form-inline mb-2">
                                            <label class="mr-2"><b>Periode Penilaian:</b></label>
                                            <input type="date" id="periode_awal" class="form-control mr-2"
                                                value="<?= $periode_awal ?? date('Y-01-01'); ?>">
                                            <span class="mr-2">s/d</span>
                                            <input type="date" id="periode_akhir" class="form-control mr-2"
                                                value="<?= $periode_akhir ?? date('Y-12-31'); ?>">
                                            <button type="button" id="btn-sesuaikan-periode" class="btn btn-primary btn-sm ml-2">Sesuaikan Periode</button>
                                        </div>





                                        <p><b>Unit Kantor Penilai:</b> <?= $pegawai_detail->unit_kerja; ?></p>
                                    </div>
                                </div>

                                <hr>

                                <!-- Penilai I & Penilai II -->
                                <div class="row">
                                    <div class="col-md-6">
                                        <h5>Penilai I</h5>
                                        <p><b>NIK:</b></p>
                                        <p><b>Nama:</b></p>
                                        <p><b>Jabatan:</b></p>
                                    </div>

                                    <div class="col-md-6">
                                        <h5>Penilai II</h5>
                                        <p><b>NIK:</b></p>
                                        <p><b>Nama:</b></p>
                                        <p><b>Jabatan:</b></p>
                                    </div>
                                </div>
                                <a href="<?= base_url('SuperAdmin/downloadDataPegawai/' . $pegawai_detail->nik); ?>"
                                    class="btn btn-primary mt-2">
                                    <i class="mdi mdi-file-excel"></i> Download Excel
                                </a>
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
                                            <tfoot
                                                style="background-color:#2E7D32;color:#fff;font-weight:bold;text-align:center;">
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