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
                                        <th>Unit Kantor</th>
                                        <th>Tahapan PPK</th>
                                        <th>Periode PPK</th> <!-- TAMBAHAN KOLOM -->
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
    // Pastikan dropdown periode hanya menampilkan periode Oktober-Desember.
    (function enforceOctDecOptions() {
        try {
            const sel = document.getElementById('filter_periode');
            if (!sel) return;

            function isOctDecOption(opt) {
                try {
                    const parts = opt.value.split('|');
                    if (parts.length !== 2) return false;
                    const awal = parts[0];
                    const akhir = parts[1];
                    const ma = new Date(awal).getMonth() + 1; // 1-12
                    const mb = new Date(akhir).getMonth() + 1;
                    return ma === 10 && mb === 12;
                } catch (e) {
                    return false;
                }
            }

            function filterOnce() {
                const opts = Array.from(sel.options);
                for (const opt of opts) {
                    if (!isOctDecOption(opt)) {
                        sel.removeChild(opt);
                    }
                }
            }

            filterOnce();

            const mo = new MutationObserver(function(muts) {
                let changed = false;
                for (const m of muts) {
                    if (m.type === 'childList' && m.addedNodes.length > 0) {
                        changed = true;
                        break;
                    }
                }
                if (changed) {
                    setTimeout(filterOnce, 20);
                }
            });
            mo.observe(sel, {
                childList: true
            });

        } catch (e) {
            console.warn('enforceOctDecOptions error', e);
        }
    })();

    document.addEventListener('DOMContentLoaded', function() {

        (function restoreSelectedPeriodFromUrl() {
            const params = new URLSearchParams(window.location.search);
            const awal = params.get('awal');
            const akhir = params.get('akhir');
            if (awal && akhir) {
                const sel = document.getElementById('filter_periode');
                const optionVal = awal + '|' + akhir;
                for (let i = 0; i < sel.options.length; i++) {
                    if (sel.options[i].value === optionVal) {
                        sel.selectedIndex = i;
                        break;
                    }
                }
            }
        })();

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
                },
                error: function(xhr, status, error) {
                    console.error('getMonitoringPPKData error', xhr.responseText || status || error);
                    try {
                        console.log(JSON.parse(xhr.responseText));
                    } catch (e) {}
                    alert('Ajax error saat memuat data. Buka console untuk detail.');
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
                {
                    data: 'nik'
                },
                {
                    data: 'nama'
                },
                {
                    data: 'jabatan'
                },
                {
                    data: 'unit_kantor'
                },
                {
                    data: 'tahap',
                    render: function(data) {
                        return (data && data != '0') ? 'Tahap ' + data : '-';
                    }
                },
                {
                    data: 'periode_ppk', // TAMBAHAN DATA
                    render: function(data) {
                        return data ? data : '-';
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
                    data: 'predikat_list', // Predikat Periodik (List)
                    render: function(data, type, row) {

                        if (!data || data.length === 0) return '<span class="text-muted small">Belum ada predikat terbaru</span>';
                        let html = '';
                        data.forEach(function(p) {

                            let badgeClass = 'badge bg-info';
                            if (String(p).toLowerCase() === 'minus') badgeClass = 'badge bg-danger';
                            html += `<span class="${badgeClass} mr-1">${p}</span>`;
                        });
                        return html;
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
                        const formulirBtn = `<button class="btn btn-sm btn-info btn-detail mr-1" style="min-width: 90px;" data-nik="${row.nik}"><i class="mdi mdi-file-document-outline"></i> Formulir</button>`;
                        const evaluasiBtn = `<a href="<?= site_url('administrator/evaluasi_ppk') ?>/${row.nik}" class="btn btn-sm btn-success" style="min-width: 90px;"><i class="mdi mdi-clipboard-check-outline"></i> Evaluasi</a>`;
                        return `<div class="d-flex justify-content-center">${formulirBtn}${evaluasiBtn}</div>`;
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
            window.location.href = '<?= site_url("Administrator/ppk_msdiformulir/") ?>' + nik;
        });
    });
</script>