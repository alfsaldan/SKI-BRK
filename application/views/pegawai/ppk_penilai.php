<div class="content-page">
    <div class="content">
        <div class="container-fluid">

            <!-- Judul Halaman -->
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box">
                        <div class="page-title-right">
                            <ol class="breadcrumb m-0">
                                <li class="breadcrumb-item"><a href="javascript:void(0);">KPI Online-BRKS</a></li>
                                <li class="breadcrumb-item active">Penilaian PPK</li>
                            </ol>
                        </div>
                        <h4 class="page-title text-primary"><i class="mdi mdi-account-check-outline mr-1"></i> Penilaian Program Peningkatan Kinerja (PPK)</h4>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12">
                    <div class="card shadow-sm border-0">
                        <div class="card-body">
                            
                            <!-- Nav Tabs -->
                            <ul class="nav nav-tabs nav-bordered mb-3">
                                <li class="nav-item">
                                    <a href="#penilai1" data-toggle="tab" aria-expanded="true" class="nav-link active">
                                        <i class="mdi mdi-account-tie mr-1"></i> Sebagai Penilai I
                                    </a>
                                </li>
                                <?php if($is_pimpinan): ?>
                                <li class="nav-item">
                                    <a href="#pimpinan" data-toggle="tab" aria-expanded="false" class="nav-link">
                                        <i class="mdi mdi-domain mr-1"></i> Sebagai Pimpinan Unit
                                    </a>
                                </li>
                                <?php endif; ?>
                            </ul>

                            <div class="tab-content">
                                <!-- TAB PENILAI 1 -->
                                <div class="tab-pane show active" id="penilai1">
                                    <h5 class="text-info mb-3">Daftar Pegawai yang Dinilai (Penilai I)</h5>
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-hover display responsive nowrap" style="width:100%" id="tabel-ppk-penilai1">
                                            <thead class="thead-light text-center">
                                                <tr>
                                                    <th>No</th>
                                                    <th>NIK</th>
                                                    <th>Nama Pegawai</th>
                                                    <th>Jabatan</th>
                                                    <th>Tahap PPK</th>
                                                    <th>Periode</th>
                                                    <th>Status Penilai I</th>
                                                    <th>Aksi</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php if(!empty($list_ppk_penilai1)): ?>
                                                    <?php $no=1; foreach($list_ppk_penilai1 as $row): ?>
                                                    <tr>
                                                        <td class="text-center"><?= $no++ ?></td>
                                                        <td><?= $row->nik ?></td>
                                                        <td><?= $row->nama ?></td>
                                                        <td><?= $row->jabatan ?></td>
                                                        <td class="text-center">Tahap <?= $row->tahap ?? '-' ?></td>
                                                        <td class="text-center"><?= $row->periode_ppk ?></td>
                                                        <td class="text-center">
                                                            <?php
                                                                $st = $row->status_penilai1 ?? 'Belum Disetujui';
                                                                $cls = ($st == 'Disetujui') ? 'badge-success' : (($st == 'Ditolak') ? 'badge-danger' : 'badge-secondary');
                                                            ?>
                                                            <span class="badge <?= $cls ?>"><?= $st ?></span>
                                                        </td>
                                                        <td class="text-center">
                                                            <a href="<?= base_url('pegawai/ppk_penilaiformulir/'.$row->id_nilai_akhir) ?>" class="btn btn-sm btn-primary">
                                                                <i class="mdi mdi-file-document-edit-outline mr-1"></i> Formulir
                                                            </a>
                                                            <a href="<?= base_url('pegawai/ppk_evaluasi/'.$row->id_nilai_akhir) ?>" class="btn btn-sm btn-info">
                                                                <i class="mdi mdi-clipboard-check-outline mr-1"></i> Evaluasi
                                                            </a>
                                                        </td>
                                                    </tr>
                                                    <?php endforeach; ?>
                                                <?php else: ?>
                                                    <tr><td colspan="8" class="text-center">Tidak ada data PPK untuk dinilai.</td></tr>
                                                <?php endif; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                                <!-- TAB PIMPINAN UNIT -->
                                <?php if($is_pimpinan): ?>
                                <div class="tab-pane" id="pimpinan">
                                    <h5 class="text-warning mb-3">Daftar Pegawai Unit Kerja (Pimpinan Unit)</h5>
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-hover display responsive nowrap" style="width:100%" id="tabel-ppk-pimpinan">
                                            <thead class="thead-light text-center">
                                                <tr>
                                                    <th>No</th>
                                                    <th>NIK</th>
                                                    <th>Nama Pegawai</th>
                                                    <th>Jabatan</th>
                                                    <th>Tahap PPK</th>
                                                    <th>Periode</th>
                                                    <th>Status Pimpinan</th>
                                                    <th>Aksi</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php if(!empty($list_ppk_pimpinan)): ?>
                                                    <?php $no=1; foreach($list_ppk_pimpinan as $row): ?>
                                                    <tr>
                                                        <td class="text-center"><?= $no++ ?></td>
                                                        <td><?= $row->nik ?></td>
                                                        <td><?= $row->nama ?></td>
                                                        <td><?= $row->jabatan ?></td>
                                                        <td class="text-center">Tahap <?= $row->tahap ?? '-' ?></td>
                                                        <td class="text-center"><?= $row->periode_ppk ?></td>
                                                        <td class="text-center">
                                                            <?php
                                                                $st = $row->status_pimpinanunit ?? 'Belum Disetujui';
                                                                $cls = ($st == 'Disetujui') ? 'badge-success' : (($st == 'Ditolak') ? 'badge-danger' : 'badge-secondary');
                                                            ?>
                                                            <span class="badge <?= $cls ?>"><?= $st ?></span>
                                                        </td>
                                                        <td class="text-center">
                                                            <a href="<?= base_url('pegawai/ppk_pimpinanformulir/'.$row->id_nilai_akhir) ?>" class="btn btn-sm btn-primary">
                                                                <i class="mdi mdi-file-document-edit-outline mr-1"></i> Formulir
                                                            </a>
                                                            <a href="<?= base_url('pegawai/ppk_evaluasi/'.$row->id_nilai_akhir) ?>" class="btn btn-sm btn-info">
                                                                <i class="mdi mdi-clipboard-check-outline mr-1"></i> Evaluasi
                                                            </a>
                                                        </td>
                                                    </tr>
                                                    <?php endforeach; ?>
                                                <?php else: ?>
                                                    <tr><td colspan="8" class="text-center">Tidak ada data PPK di unit ini.</td></tr>
                                                <?php endif; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <?php endif; ?>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- DataTables JS & CSS (CDN) -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.dataTables.min.css">
<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>

<script>
    $(document).ready(function() {
        var dataTableOptions = {
            responsive: true,
            pageLength: 10,
            lengthMenu: [
                [10, 25, 50, 100, -1],
                [10, 25, 50, 100, "Semua"]
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
        };

        $('#tabel-ppk-penilai1').DataTable(dataTableOptions);
        
        <?php if($is_pimpinan): ?>
        $('#tabel-ppk-pimpinan').DataTable(dataTableOptions);
        <?php endif; ?>
    });
</script>