<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<div class="content-page">
    <div class="content">
        <div class="container-fluid">

            <!-- ================= PAGE TITLE ================= -->
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-flex justify-content-between align-items-center">
                        <h3 class="page-title">
                            <i class="mdi mdi-clipboard-pulse-outline mr-2 text-primary"></i> Monitoring PPK
                        </h3>
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="#">KPI Online-BRKS</a></li>
                            <li class="breadcrumb-item active">Monitoring PPK</li>
                        </ol>
                    </div>
                </div>
            </div>

            <!-- ================= CARD MONITORING ================= -->
            <div class="row mt-0">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">

                            <div class="mb-3 d-flex gap-2 align-items-center">
                                <label>Pilih Periode</label>
                                <select id="filter_periode" class="form-control">
                                    <?php if (!empty($periode_list)): ?>
                                        <?php foreach ($periode_list as $p):
                                            $label = date('d M Y', strtotime($p->periode_awal)) . ' - ' . date('d M Y', strtotime($p->periode_akhir));
                                            $val = $p->periode_awal . '|' . $p->periode_akhir;
                                            $sel = ((isset($selected_awal) && isset($selected_akhir)) && $selected_awal == $p->periode_awal && $selected_akhir == $p->periode_akhir) ? 'selected' : '';
                                        ?>
                                            <option value="<?= $val ?>" <?= $sel ?>><?= $label ?></option>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <?php $def_awal = isset($selected_awal) ? $selected_awal : date('Y') . '-10-01';
                                        $def_akhir = isset($selected_akhir) ? $selected_akhir : date('Y') . '-12-31'; ?>
                                        <option value="<?= $def_awal . '|' . $def_akhir ?>">Default (<?= $def_awal ?> - <?= $def_akhir ?>)</option>
                                    <?php endif; ?>
                                </select>

                                <button id="btn_refresh" class="btn btn-primary">Refresh</button>
                            </div>

                            <table id="table-monitoring-ppk" class="table table-bordered table-striped" style="width:100%">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>NIK</th>
                                        <th>Nama</th>
                                        <th>Jabatan</th>
                                        <th>Unit Kerja</th>
                                        <th>Tahapan PPK</th>
                                        <th>Predikat</th>
                                        <th>Predikat Periodik</th>
                                        <th>Status</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>

                        </div>
                    </div>
                </div>
            </div>

        </div> <!-- end container-fluid -->
    </div> <!-- end content -->
</div> <!-- end content-page -->

<!-- Load jQuery dari CDN untuk mengatasi error 404 file lokal -->
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {

        const table = $('#table-monitoring-ppk').DataTable({
            processing: true,
            serverSide: false,
            ajax: {
                url: '<?= site_url('Administrator/getMonitoringPPKData') ?>',
                data: function(d) {
                    const v = document.getElementById('filter_periode').value || '';
                    const parts = v.split('|');
                    d.awal = parts[0] || '';
                    d.akhir = parts[1] || '';
                }
            },
            columns: [{
                    data: null,
                    render: function(data, type, row, meta) {
                        return meta.row + 1;
                    },
                    orderable: false,
                    searchable: false
                },
                { data: 'nik' },
                { data: 'nama' },
                { data: 'jabatan' },
                { data: 'unit_kerja' },
                {
                    data: 'tahap',
                    render: function(data) {
                        return (data && data != '0') ? 'Tahap ' + data : '-';
                    }
                },
                {
                    data: 'predikat',
                    render: function(data) {
                        if (!data) return '';
                        if (String(data).toLowerCase() === 'minus') {
                            return `<span class="badge bg-danger">${data}</span>`;
                        }
                        return `<span class="badge bg-secondary">${data}</span>`;
                    }
                },
                {
                    data: null, // Predikat Periodik (Dummy)
                    render: function(data, type, row) {
                        // Data dummy acak untuk tampilan
                        const dummies = ['Baik', 'Cukup', 'Sangat Baik'];
                        const random = dummies[Math.floor(Math.random() * dummies.length)];
                        return `<span class="badge bg-info">${random}</span>`;
                    }
                },
                {
                    data: 'ppk_eligible', // Status
                    render: function(data, type, row) {
                        if (parseInt(data) === 1) {
                            return `<span class="badge bg-success">Aktif</span>`;
                        }
                        return `<span class="badge bg-secondary">Tidak Aktif</span>`;
                    }
                },
                {
                    data: null, // Aksi
                    render: function(data, type, row) {
                        return `<button class="btn btn-sm btn-info btn-detail" data-nik="${row.nik}"><i class="mdi mdi-information-outline"></i> Detail</button>`;
                    },
                    orderable: false,
                    searchable: false
                }
            ],
            pageLength: 25,
            language: {
                search: "Cari:",
                lengthMenu: "Tampilkan _MENU_ data per halaman",
                info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                infoEmpty: "Tidak ada data tersedia",
                infoFiltered: "(disaring dari total _MAX_ data)",
                paginate: {
                    previous: "Sebelumnya",
                    next: "Selanjutnya"
                },
                zeroRecords: "Tidak ada hasil ditemukan"
            }
        });

        document.getElementById('btn_refresh').addEventListener('click', function() {
            table.ajax.reload();
        });

        const selFilter = document.getElementById('filter_periode');
        if (selFilter) {
            selFilter.addEventListener('change', function() {
                table.ajax.reload();
            });
        }
        
        // Aksi tombol detail
        $('#table-monitoring-ppk tbody').on('click', '.btn-detail', function() {
            const nik = $(this).data('nik');
            // Redirect ke halaman detail pegawai
             window.location.href = '<?= site_url("Administrator/detailPegawai/") ?>' + nik;
        });
    });
</script>