<!-- ============================================================== -->
<!-- Start Page Content -->
<!-- ============================================================== -->
<div class="content-page">
    <div class="content">
        <div class="container-fluid">

            <!-- Judul Halaman -->
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-flex justify-content-between align-items-center">
                        <h3 class="page-title">
                            <i class="mdi mdi-account-edit mr-2 text-primary"></i> Nilai Pegawai
                        </h3>
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="#">KPI Online-BRKS</a></li>
                            <li class="breadcrumb-item active">Nilai Pegawai</li>
                        </ol>
                    </div>
                </div>
            </div>

            <!-- Filter Input -->
            <div class="row mb-3">
                <div class="col-md-4">
                    <input type="text" id="filter-nik" class="form-control" placeholder="Filter berdasarkan NIK...">
                </div>
            </div>

            <!-- ========================= -->
            <!-- Pegawai Penilai 1 -->
            <!-- ========================= -->
            <?php if (!empty($pegawai_penilai1)) { ?>
                <div class="row">
                    <div class="col-12">
                        <div class="card mb-4">
                            <div class="card-body">
                                <h5 class="text-info mb-3 font-weight-bold">
                                    <i class="mdi mdi-account-multiple-check mr-1"></i> Pegawai Dinilai sebagai Penilai 1
                                </h5>
                                <div class="table-responsive">
                                    <table class="table table-bordered tabel-pegawai" id="tabel-penilai1">
                                        <thead style="background-color:#17a2b8; color:#fff; text-align:center;">
                                            <tr>
                                                <th style="width:5%;">No</th>
                                                <th style="width:15%;">NIK</th>
                                                <th style="width:25%;">Nama</th>
                                                <th style="width:25%;">Jabatan</th>
                                                <th style="width:20%;">Unit Kerja</th>
                                                <th style="width:10%;">Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php $no = 1;
                                            foreach ($pegawai_penilai1 as $p) { ?>
                                                <tr>
                                                    <td class="text-center"><?= $no++; ?></td>
                                                    <td><?= $p->nik; ?></td>
                                                    <td><?= $p->nama; ?></td>
                                                    <td><?= $p->jabatan; ?></td>
                                                    <td><?= $p->unit_kerja; ?></td>
                                                    <td class="text-center">
                                                        <a href="<?= base_url('Pegawai/nilaiPegawaiDetail/' . $p->nik) ?>"
                                                            class="btn btn-sm btn-success">Nilai</a>
                                                    </td>
                                                </tr>
                                            <?php } ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php } ?>


            <!-- ========================= -->
            <!-- Pegawai Penilai 2 -->
            <!-- ========================= -->
            <?php if (!empty($pegawai_penilai2)) { ?>
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="text-warning mb-3 font-weight-bold">
                                    <i class="mdi mdi-account-multiple-check mr-1"></i> Pegawai Dinilai sebagai Penilai 2
                                </h5>
                                <div class="table-responsive">
                                    <table class="table table-bordered tabel-pegawai" id="tabel-penilai2">
                                        <thead style="background-color:#ffa407ff; color:#fff; text-align:center;">
                                            <tr>
                                                <th style="width:5%;">No</th>
                                                <th style="width:15%;">NIK</th>
                                                <th style="width:25%;">Nama</th>
                                                <th style="width:25%;">Jabatan</th>
                                                <th style="width:20%;">Unit Kerja</th>
                                                <th style="width:10%;">Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php $no = 1;
                                            foreach ($pegawai_penilai2 as $p) { ?>
                                                <tr>
                                                    <td class="text-center"><?= $no++; ?></td>
                                                    <td><?= $p->nik; ?></td>
                                                    <td><?= $p->nama; ?></td>
                                                    <td><?= $p->jabatan; ?></td>
                                                    <td><?= $p->unit_kerja; ?></td>
                                                    <td class="text-center">
                                                        <a href="<?= base_url('Pegawai/nilaiPegawaiDetail/' . $p->nik) ?>"
                                                            class="btn btn-sm btn-success">Nilai</a>
                                                    </td>
                                                </tr>
                                            <?php } ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php } ?>


            <?php if (empty($pegawai_penilai1) && empty($pegawai_penilai2)) { ?>
                <div class="alert alert-info">
                    Anda tidak memiliki daftar pegawai untuk dinilai.
                </div>
            <?php } ?>

        </div>
    </div>
</div>
<!-- ============================================================== -->
<!-- End Page Content -->
<!-- ============================================================== -->

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const filterInput = document.getElementById('filter-nik');
        filterInput.addEventListener('input', function() {
            const filter = this.value.toLowerCase();
            document.querySelectorAll('.tabel-pegawai tbody tr').forEach(row => {
                const nik = row.cells[1].innerText.toLowerCase();
                row.style.display = nik.includes(filter) ? '' : 'none';
            });
        });
    });
</script>