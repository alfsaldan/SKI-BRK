<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<div class="content-page">
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <h4 class="page-title">Verifikasi Penilaian Pegawai</h4>
                </div>
            </div>

            <div class="row mt-3">
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
                                        <?php $def_awal = $selected_awal ?? date('Y-01-01'); $def_akhir = $selected_akhir ?? date('Y-12-31'); ?>
                                        <option value="<?= $def_awal . '|' . $def_akhir ?>">Default (<?= $def_awal ?> - <?= $def_akhir ?>)</option>
                                    <?php endif; ?>
                                </select>
                                <button id="btn_refresh" class="btn btn-primary">Refresh</button>
                            </div>

                            <table id="table-verifikasi" class="table table-bordered table-striped" style="width:100%">
                                <thead>
                                    <tr>
                                        <th>NIK</th>
                                        <th>Nama</th>
                                        <th>Jabatan</th>
                                        <th>Status Penilaian</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>

                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<!-- DataTables JS/CSS harus sudah tersedia di template, jika belum tambahkan CDN -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Jika URL punya param awal/akhir (misal setelah klik 'Nilai'), set select sesuai
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

        const table = $('#table-verifikasi').DataTable({
            processing: true,
            serverSide: false,
                ajax: {
                    url: '<?= site_url('Administrator/getVerifikasiData') ?>',
                    data: function(d) {
                        const v = document.getElementById('filter_periode').value || '';
                        const parts = v.split('|');
                        d.awal = parts[0] || '';
                        d.akhir = parts[1] || '';
                    }
                },
            columns: [
                { data: 'nik' },
                { data: 'nama' },
                { data: 'jabatan' },
                { 
                    data: 'status_penilaian',
                    render: function(data, type, row) {
                        if (!data) return '';
                        var cls = 'badge bg-secondary';
                        if (data === 'Diverifikasi') cls = 'badge bg-success';
                        else if (data === 'Ditolak') cls = 'badge bg-danger';
                        else if (data === 'Dinilai') cls = 'badge bg-info text-white';
                        return '<span class="' + cls + '">' + data + '</span>';
                    }
                },
                {
                    data: 'action',
                    render: function(data, type, row) {
                        // tambahkan periode yang sedang dipilih ke URL action
                        const v = document.getElementById('filter_periode').value || '';
                        const parts = v.split('|');
                        const awal = encodeURIComponent(parts[0] || '');
                        const akhir = encodeURIComponent(parts[1] || '');
                        const sep = data.indexOf('?') === -1 ? '?' : '&';
                        return '<a class="btn btn-sm btn-success" href="' + data + sep + 'awal=' + awal + '&akhir=' + akhir + '">Nilai</a>';
                    },
                    orderable: false,
                    searchable: false
                }
            ],
            pageLength: 25,
            lengthMenu: [10, 25, 50, 100]
        });

        document.getElementById('btn_refresh').addEventListener('click', function() {
            table.ajax.reload();
        });

        // reload otomatis ketika periode berubah
        var selFilter = document.getElementById('filter_periode');
        if (selFilter) {
            selFilter.addEventListener('change', function() {
                table.ajax.reload();
            });
        }
    });
</script>
