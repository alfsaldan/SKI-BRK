<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<div class="content-page">
    <div class="content">
        <div class="container-fluid">

            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-flex justify-content-between align-items-center">
                        <h3 class="page-title">
                            <i class="mdi mdi-shield-alert-outline mr-2 text-danger"></i> Verifikasi PPK
                        </h3>
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="#">KPI Online-BRKS</a></li>
                            <li class="breadcrumb-item active">Verifikasi PPK</li>
                        </ol>
                    </div>
                </div>
            </div>

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
                                        <?php $def_awal = isset($selected_awal) ? $selected_awal : date('Y') . '-10-01'; $def_akhir = isset($selected_akhir) ? $selected_akhir : date('Y') . '-12-31'; ?>
                                        <option value="<?= $def_awal . '|' . $def_akhir ?>">Default (<?= $def_awal ?> - <?= $def_akhir ?>)</option>
                                    <?php endif; ?>
                                </select>
                                <button id="btn_refresh" class="btn btn-primary">Refresh</button>
                            </div>

                            <table id="table-verifikasi-ppk" class="table table-bordered table-striped" style="width:100%">
                                <thead>
                                    <tr>
                                        <th>NO</th>
                                        <th>NIK</th>
                                        <th>Nama</th>
                                        <th>Jabatan</th>
                                        <th>Unit Kerja</th>
                                        <th>Predikat</th>
                                        <th>Nama Penilai1</th>
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

                        <!-- PPK Check Modal (static) -->
                        <div class="modal fade" id="ppkModal" tabindex="-1" role="dialog" aria-labelledby="ppkModalLabel" aria-hidden="true">
                            <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
                                <div class="modal-content">
                                    <div class="modal-header" style="background:linear-gradient(90deg,#28a745,#12b35a);color:#fff;border-bottom:0">
                                        <h5 class="modal-title" id="ppkModalLabel"><i class="mdi mdi-clipboard-text-outline mr-2"></i> Program Peningkatan Kinerja (PPK)</h5>
                                        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        <div id="ppk-alert" class="alert alert-danger d-none" role="alert"></div>
                                        <form id="ppk-form">
                                            <input type="hidden" name="ppk_nik" id="ppk_nik" value="">
                                            <div class="mb-3">Jawablah pertanyaan berikut dengan memilih salah satu opsi:</div>
                                            <div class="row">
                                                <?php for ($i = 1; $i <= 7; $i++): ?>
                                                    <div class="col-12 mb-0">
                                                        <div class="card card-body p-2">
                                                            <div class="d-flex justify-content-between align-items-center">
                                                                <div><strong>Soal <?= $i ?>.</strong> Deskripsi singkat soal PPK nomor <?= $i ?>.</div>
                                                                <div>
                                                                    <div class="btn-group btn-group-toggle" data-toggle="buttons" role="group" aria-label="PPK opsi">
                                                                        <label class="btn btn-sm btn-outline-danger">
                                                                            <input type="radio" name="ppk_q<?= $i ?>" id="ppk_q<?= $i ?>_tidak" value="tidak" autocomplete="off"> Tidak
                                                                        </label>
                                                                        <label class="btn btn-sm btn-outline-success">
                                                                            <input type="radio" name="ppk_q<?= $i ?>" id="ppk_q<?= $i ?>_ya" value="ya" autocomplete="off"> Ya
                                                                        </label>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                <?php endfor; ?>
                                            </div>
                                        </form>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                                        <button type="button" id="ppk-save" class="btn btn-primary">Simpan</button>
                                    </div>
                                </div>
                            </div>
                        </div>

<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>

<script>
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

    // Opsi periode sekarang dikirim dari server (variabel $periode_list).
    // Sebelumnya ada augmentasi client-side untuk menambahkan Oct-Dec otomatis —
    // sudah dihapus supaya hanya periode yang benar-benar ada di DB yang ditampilkan.

    const table = $('#table-verifikasi-ppk').DataTable({
        processing: true,
        serverSide: false,
        ajax: {
            url: '<?= site_url('Administrator/getVerifikasiPPKData') ?>',
            data: function(d) {
                const v = document.getElementById('filter_periode').value || '';
                const parts = v.split('|');
                d.awal = parts[0] || '';
                d.akhir = parts[1] || '';
            }
        },
        columns: [
            {
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
                data: 'predikat',
                render: function(data) {
                    if (!data) return '';
                    try {
                        if (String(data).toLowerCase() === 'minus') {
                            return `<span class="badge bg-danger">${data}</span>`;
                        }
                    } catch (e) {
                        // fallback
                    }
                    return `<span class="badge bg-secondary">${data}</span>`;
                }
            },
            { data: 'penilai1' },
            {
                data: 'action',
                render: function(data, type, row, meta) {
                    const v = document.getElementById('filter_periode').value || '';
                    const parts = v.split('|');
                    const awal = encodeURIComponent(parts[0] || '');
                    const akhir = encodeURIComponent(parts[1] || '');
                    // Return a button that opens a modal with static PPK form
                    return `<button type="button" class="btn btn-sm btn-primary btn-cek-syarat" data-nik="${row.nik}" data-awal="${awal}" data-akhir="${akhir}" data-action="${data}">Cek Syarat</button>`;
                },
                orderable: false,
                searchable: false
            }
        ],
        pageLength: 25,
        lengthMenu: [[5,10,25,50,100,-1],[5,10,25,50,100,'Semua']],
        language: {
            search: "Cari:",
            lengthMenu: "Tampilkan _MENU_ data per halaman",
            info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
            infoEmpty: "Tidak ada data tersedia",
            infoFiltered: "(disaring dari total _MAX_ data)",
            paginate: { previous: "Sebelumnya", next: "Selanjutnya" },
            zeroRecords: "Tidak ada hasil ditemukan"
        }
    });

    document.getElementById('btn_refresh').addEventListener('click', function() {
        table.ajax.reload();
    });

    const selFilter = document.getElementById('filter_periode');
    if (selFilter) selFilter.addEventListener('change', function() { table.ajax.reload(); });
});
</script>

<script>
// PPK modal interactions (static)
document.addEventListener('DOMContentLoaded', function() {
    // delegate click for dynamic DataTable buttons
    $(document).on('click', '.btn-cek-syarat', function(e) {
        e.preventDefault();
        const nik = $(this).data('nik') || '';
        // reset form
        $('#ppk-form')[0].reset();
        $('#ppk-alert').addClass('d-none').text('');
        $('#ppk_nik').val(nik);
        $('#ppkModal').modal('show');
    });

    $('#ppk-save').on('click', function() {
        // validate that all 7 radio groups are answered
        let missing = [];
        for (let i = 1; i <= 7; i++) {
            if ($('input[name="ppk_q' + i + '"]:checked').length === 0) {
                missing.push(i);
            }
        }
        if (missing.length > 0) {
            $('#ppk-alert').removeClass('d-none').text('Silakan jawab semua pertanyaan. Soal yang belum dijawab: ' + missing.join(', '));
            $('html, body').animate({ scrollTop: $('#ppkModal .modal-body').offset().top - 100 }, 200);
            return;
        }

        // static save: show a brief success message then close modal
        // You can replace this with an AJAX call to save server-side later.
        var nik = $('#ppk_nik').val();
        // show temporary success using bootstrap alert inside modal body
        $('#ppk-alert').removeClass('d-none').removeClass('alert-danger').addClass('alert-success').text('Data PPK untuk NIK ' + nik + ' berhasil disimpan (statis).');
        setTimeout(function() {
            $('#ppkModal').modal('hide');
            // restore alert to danger for next open
            setTimeout(function(){
                $('#ppk-alert').removeClass('alert-success').addClass('alert-danger').addClass('d-none').text('');
            }, 300);
        }, 900);
    });
});
</script>

<script>
// Pastikan dropdown periode hanya menampilkan periode Oktober-Desember.
// Ini akan menghapus opsi lain yang mungkin ditambahkan oleh skrip global lain.
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
            // convert NodeList to array because we'll mutate
            const opts = Array.from(sel.options);
            for (const opt of opts) {
                if (!isOctDecOption(opt)) {
                    sel.removeChild(opt);
                }
            }
        }

        // run once immediately
        filterOnce();

        // observe future changes and re-filter if needed
        const mo = new MutationObserver(function(muts) {
            let changed = false;
            for (const m of muts) {
                if (m.type === 'childList' && m.addedNodes.length > 0) { changed = true; break; }
            }
            if (changed) {
                // small timeout to allow other scripts to finish adding nodes
                setTimeout(filterOnce, 20);
            }
        });
        mo.observe(sel, { childList: true });

    } catch (e) {
        console.warn('enforceOctDecOptions error', e);
    }
})();
</script>
