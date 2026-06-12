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
                                <li class="breadcrumb-item"><a href="<?= base_url('Administrator') ?>">Dashboard</a></li>
                                <li class="breadcrumb-item active">Laporan Kinerja</li>
                            </ol>
                        </div>
                        <h4 class="page-title text-primary"><i class="mdi mdi-file-document-box-multiple-outline mr-2 text-primary"></i> Laporan Kinerja Tahunan</h4>
                    </div>
                </div>
            </div>
            <!-- end page title -->

            <div class="row">
                <div class="col-12">
                    <div class="card border-top border-primary shadow-sm" style="border-width: 3px !important;">
                        <div class="card-body">
                            <form action="<?= base_url('administrator/laporankinerja'); ?>" method="get">
                                <div class="row mb-3 align-items-end">
                                    <div class="col-md-3">
                                        <label for="tahun">Pilih Tahun Laporan</label>
                                        <select class="form-control" name="tahun" id="tahun">
                                            <?php if (!empty($tahun_list)): ?>
                                                <?php foreach ($tahun_list as $thn): ?>
                                                    <option value="<?= $thn->tahun ?>" <?= ($thn->tahun == $tahun_selected) ? 'selected' : '' ?>><?= $thn->tahun ?></option>
                                                <?php endforeach; ?>
                                            <?php else: ?>
                                                <option value="<?= date('Y') ?>"><?= date('Y') ?></option>
                                            <?php endif; ?>
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <label for="unit_kantor">Pilih Unit Kantor</label>
                                        <select class="form-control select2" name="unit_kantor" id="unit_kantor">
                                            <option value="all" <?= (empty($unit_kantor_selected) || $unit_kantor_selected == 'all') ? 'selected' : '' ?>>Semua Unit Kantor</option>
                                            <?php if (!empty($unit_kantor_list)): ?>
                                                <?php foreach ($unit_kantor_list as $uk): ?>
                                                    <option value="<?= htmlspecialchars($uk->unit_kantor) ?>" <?= (isset($unit_kantor_selected) && $uk->unit_kantor == $unit_kantor_selected) ? 'selected' : '' ?>><?= htmlspecialchars($uk->unit_kantor) ?></option>
                                                <?php endforeach; ?>
                                            <?php endif; ?>
                                        </select>
                                    </div>
                                    <div class="col-md-2">
                                        <button type="submit" class="btn btn-primary w-100"><i class="mdi mdi-filter"></i> Filter</button>
                                    </div>
                                </div>
                            </form>

                            <hr>

                            <div class="table-responsive">
                                <table id="datatable-laporan" class="table table-hover table-striped table-bordered nowrap w-100">
                                    <thead class="bg-primary text-white text-center">
                                        <tr>
                                            <th>No</th>
                                            <th>NIK</th>
                                            <th>Nama</th>
                                            <th>Jabatan</th>
                                            <th>Unit Kantor</th>
                                            <th>Nilai Akhir</th>
                                            <th>Yudisium SKI <?= htmlspecialchars($tahun_selected) ?></th>
                                            <th>Persentase Pencapaian</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (!empty($laporan)): ?>
                                            <?php $no = 1; foreach ($laporan as $row): ?>
                                                <tr>
                                                    <td class="text-center"><?= $no++ ?></td>
                                                    <td class="text-center"><?= htmlspecialchars($row->nik) ?></td>
                                                    <td><?= htmlspecialchars($row->nama) ?></td>
                                                    <td><?= htmlspecialchars($row->jabatan) ?></td>
                                                    <td><?= htmlspecialchars($row->unit_kantor) ?></td>
                                                    <td class="text-center font-weight-bold text-primary"><?= number_format((float)$row->nilai_akhir, 2) ?></td>
                                                    <td class="text-center">
                                                        <?php
                                                            $yudisium = $row->yudisium;
                                                            $badge_class = 'badge-secondary';
                                                            if ($yudisium == 'Excellent') $badge_class = 'badge-success';
                                                            elseif ($yudisium == 'Very Good') $badge_class = 'badge-primary';
                                                            elseif ($yudisium == 'Good') $badge_class = 'badge-info';
                                                            elseif ($yudisium == 'Fair') $badge_class = 'badge-warning';
                                                            elseif ($yudisium == 'Minus') $badge_class = 'badge-danger';
                                                        ?>
                                                        <span class="badge <?= $badge_class ?> p-1" style="font-size:12px;"><?= htmlspecialchars($yudisium ?? '') ?></span>
                                                    </td>
                                                    <td class="text-center font-weight-bold"><?= number_format((float)$row->persentase_pencapaian, 2) ?>%</td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div> <!-- container -->
    </div> <!-- content -->
</div>

<!-- Tambahkan script DataTables Buttons (Excel, dll) -->
<script>
    document.addEventListener("DOMContentLoaded", function() {
        if ($('.select2').length > 0) {
            $('.select2').select2({
                width: '100%',
                placeholder: 'Pilih Unit Kantor',
                allowClear: true
            });
        }

        if ($.fn.DataTable.isDataTable('#datatable-laporan')) {
            $('#datatable-laporan').DataTable().destroy();
        }

        $('#datatable-laporan').DataTable({
            responsive: true,
            lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "All"]],
            pageLength: 25,
            dom: '<"row"<"col-sm-12 col-md-4"l><"col-sm-12 col-md-4 text-center"B><"col-sm-12 col-md-4"f>>' +
                 '<"row"<"col-sm-12"tr>>' +
                 '<"row"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>',
            buttons: [
                {
                    extend: 'excelHtml5',
                    text: '<i class="mdi mdi-file-excel"></i> Export Excel',
                    className: 'btn btn-success btn-sm',
                    title: 'Laporan Kinerja SKI Tahun <?= htmlspecialchars($tahun_selected) ?>',
                    exportOptions: {
                        columns: [0, 1, 2, 3, 4, 5, 6, 7]
                    }
                }
            ],
            language: {
                search: "Cari:",
                lengthMenu: "Tampilkan _MENU_ data",
                emptyTable: "Tidak ada data yang tersedia pada tabel ini",
                info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                paginate: {
                    first: "Awal",
                    last: "Akhir",
                    next: "Lanjut",
                    previous: "Kembali"
                }
            }
        });
    });
</script>
