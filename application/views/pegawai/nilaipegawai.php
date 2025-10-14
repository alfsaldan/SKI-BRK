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

            <!-- Filter Input Manual (opsional tambahan filter by NIK) -->
            <!-- <div class="row mb-3">
                <div class="col-md-4">
                    <input type="text" id="filter-nik" class="form-control" placeholder="Filter berdasarkan NIK...">
                </div>
            </div> -->

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
                                    <table class="table table-bordered tabel-pegawai display nowrap" id="tabel-penilai1" style="width:100%">
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
                                                        <a href="<?= base_url('Pegawai/nilaiPegawaiDetail/' . $p->nik) ?>" class="btn btn-sm btn-success">Nilai</a>
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
                        <div class="card mb-4">
                            <div class="card-body">
                                <h5 class="text-warning mb-3 font-weight-bold">
                                    <i class="mdi mdi-account-multiple-check mr-1"></i> Pegawai Dinilai sebagai Penilai 2
                                </h5>
                                <div class="table-responsive">
                                    <table class="table table-bordered tabel-pegawai display nowrap" id="tabel-penilai2" style="width:100%">
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
                                                        <a href="<?= base_url('Pegawai/nilaiPegawaiDetail2/' . $p->nik) ?>" class="btn btn-sm btn-success">Nilai</a>
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

<!-- DataTables JS & CSS -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.dataTables.min.css">
<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    $(document).ready(function() {
        // Inisialisasi DataTable untuk Penilai 1
        $('#tabel-penilai1').DataTable({
            responsive: true,
            pageLength: 10,
            lengthMenu: [
                [5, 10, 25, 50, -1],
                [5, 10, 25, 50, "Semua"]
            ],
            language: {
                search: "Cari:",
                lengthMenu: "Tampilkan _MENU_ data per halaman",
                info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                infoEmpty: "Tidak ada data tersedia",
                zeroRecords: "Tidak ditemukan data yang sesuai",
                paginate: {
                    first: "Awal",
                    last: "Akhir",
                    next: "Selanjutnya",
                    previous: "Sebelumnya"
                }
            }
        });

        // Inisialisasi DataTable untuk Penilai 2
        // Inisialisasi DataTable untuk Penilai 2
        $('#tabel-penilai2').DataTable({
            responsive: true,
            pageLength: 10,
            lengthMenu: [
                [5, 10, 25, 50, -1],
                [5, 10, 25, 50, "Semua"]
            ],
            language: {
                search: "Cari:",
                lengthMenu: "Tampilkan _MENU_ data per halaman",
                info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                paginate: {
                    previous: "Sebelumnya",
                    next: "Selanjutnya"
                }
            },
            initComplete: function() {
                // Geser label "Cari:" sedikit ke kiri
                $('div.dataTables_filter label').css({
                    'margin-right': '5px', // jarak antar label dan input
                    'margin-left': '-5px' // geser label ke kiri 5px
                });

                // Atur agar input search tidak terpotong
                $('div.dataTables_filter input').css({
                    'width': '160px',
                    'display': 'inline-block'
                });

                // Rapatkan pagination agar tidak terlalu jauh
                $('div.dataTables_paginate').css({
                    'margin-top': '5px',
                    'margin-bottom': '5px'
                });
            }
        });

    });

    // Filter manual by NIK tetap jalan
    $('#filter-nik').on('input', function() {
        var filterValue = this.value.toLowerCase();
        $('.tabel-pegawai').each(function() {
            $(this).DataTable().search(filterValue).draw();
        });
    });
</script>