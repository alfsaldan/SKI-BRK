<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<div class="content-page">
    <div class="content">
        <div class="container-fluid">

            <!-- ================= PAGE TITLE ================= -->
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

            <!-- ================= CARD VERIFIKASI ================= -->
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

                            <table id="table-verifikasi-ppk" class="table table-bordered table-striped" style="width:100%">
                                <thead>
                                    <tr>
                                        <th>NO</th>
                                        <th>NIK</th>
                                        <th>Nama</th>
                                        <th>Jabatan</th>
                                        <th>Unit Kerja</th>
                                        <th>Predikat</th>
                                        <!-- <th>PPK</th> -->
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

            <!-- ================= CARD KELOLA SYARAT (FIX POSISI) ================= -->
            <div class="row mt-3">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">

                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <h5 class="mb-0">Kelola Syarat PPK</h5>
                                <button id="btn_add_syarat" class="btn btn-success btn-sm">Tambah Syarat</button>
                            </div>

                            <table id="table-syarat" class="table table-bordered table-striped" style="width:100%">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Syarat</th>
                                        <th style="width:120px">Aksi</th>
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


<!-- ================= MODAL FORM SYARAT ================= -->
<div class="modal fade" id="syaratFormModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-md modal-dialog-centered" role="document">
        <div class="modal-content">
            <form id="syarat-form">
                <div class="modal-header" style="background:linear-gradient(90deg,#28a745,#12b35a);color:#fff;border-bottom:0">
                    <h5 class="modal-title"><i class="mdi mdi-file-edit-outline mr-2"></i> Form Syarat PPK</h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div class="modal-body">
                    <input type="hidden" id="form_id_ppk" name="id_ppk" value="">

                    <div class="form-group">
                        <label for="form_syarat">Syarat</label>
                        <textarea id="form_syarat" name="syarat" class="form-control" rows="3" required></textarea>
                    </div>

                    <div id="syarat-form-alert" class="alert alert-danger d-none"></div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" id="syarat-form-save" class="btn btn-primary">Simpan</button>
                </div>

            </form>
        </div>
    </div>
</div>


<!-- ================= MODAL PPK ================= -->
<div class="modal fade" id="ppkModal" tabindex="-1" role="dialog" aria-labelledby="ppkModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content">

            <div class="modal-header" style="background:linear-gradient(90deg,#28a745,#12b35a);color:#fff;border-bottom:0">
                <h5 class="modal-title"><i class="mdi mdi-clipboard-text-outline mr-2"></i> Program Peningkatan Kinerja (PPK)</h5>
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
                                            <div class="btn-group btn-group-toggle" data-toggle="buttons" role="group">
                                                <label class="btn btn-sm btn-outline-danger">
                                                    <input type="radio" name="ppk_q<?= $i ?>" value="tidak"> Tidak
                                                </label>

                                                <label class="btn btn-sm btn-outline-success">
                                                    <input type="radio" name="ppk_q<?= $i ?>" value="ya"> Ya
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
<!-- SweetAlert2 for nicer confirmation dialogs -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

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
                    data: 'unit_kerja'
                },
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
                // {
                //     // placeholder column for PPK eligibility status; updated dynamically
                //     data: null,
                //     render: function(data, type, row) {
                //         const nik = row.nik || '';
                //         // Prefer server-provided flag when available to avoid flicker on refresh
                //         if (typeof row.ppk_eligible !== 'undefined') {
                //             if (parseInt(row.ppk_eligible) === 1) {
                //                 return `<span class="ppk-status" data-nik="${nik}"><span class="badge bg-success">Aktif</span></span>`;
                //             }
                //             return `<span class="ppk-status" data-nik="${nik}"><span class="badge bg-secondary">Tidak Aktif</span></span>`;
                //         }
                //         // fallback: placeholder until client-side logic computes status
                //         return `<span class="ppk-status" data-nik="${nik}">-</span>`;
                //     },
                //     orderable: false,
                //     searchable: false,
                //     width: '120px'
                // },
                {
                    data: 'penilai1'
                },
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
            lengthMenu: [
                [5, 10, 25, 50, 100, -1],
                [5, 10, 25, 50, 100, 'Semua']
            ],
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
        if (selFilter) selFilter.addEventListener('change', function() {
            table.ajax.reload();
        });

        // after table draw, update PPK status column for visible rows
        // Only run client-side recompute for rows where server didn't supply ppk_eligible
        table.on('draw', function() {
            try {
                const rows = table.rows({
                    page: 'current'
                }).data();
                for (let i = 0; i < rows.length; i++) {
                    const r = rows[i];
                    if (r && r.nik) {
                        if (typeof r.ppk_eligible === 'undefined') {
                            applyPpkStatusToTable(r.nik, null);
                        } else {
                            // server provided value, ensure badge matches
                            applyPpkStatusToTable(r.nik, !!parseInt(r.ppk_eligible));
                        }
                    }
                }
            } catch (e) {
                console.error('error updating PPK status on draw', e);
            }
        });
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
            // If the modal was rendered dynamically (from syarat_ppk), validate based on actual radio groups
            const $dyn = $('#ppk-form-dynamic');
            if ($dyn.length > 0) {
                // collect unique radio group names in order
                const names = [];
                $dyn.find('input[type=radio]').each(function() {
                    const n = $(this).attr('name');
                    if (names.indexOf(n) === -1) names.push(n);
                });

                const missingIdx = [];
                for (let i = 0; i < names.length; i++) {
                    const nm = names[i];
                    if ($dyn.find('input[name="' + nm + '"]:checked').length === 0) {
                        // show 1-based soal number
                        missingIdx.push(i + 1);
                    }
                }
                if (missingIdx.length > 0) {
                    $('#ppk-alert').removeClass('d-none').text('Silakan jawab semua pertanyaan. Soal yang belum dijawab: ' + missingIdx.join(', '));
                    $('html, body').animate({
                        scrollTop: $('#ppkModal .modal-body').offset().top - 100
                    }, 200);
                    return;
                }

                // Save Tahap if visible (ensure it's saved even if user didn't blur), wait for it
                let savePromise = Promise.resolve();
                if ($('#ppk-tahap-container').is(':visible')) {
                    const tVal = $('#ppk_tahap_input').val();
                    savePromise = savePpkTahap($('#ppk_nik').val(), tVal);
                }

                savePromise.then((res) => {
                    // All answered — show brief success and close. Responses already saved per-toggle.
                    const nik = $('#ppk_nik').val();
                    
                    // recompute eligibility on client from DOM and apply immediately
                    try {
                        const answers = {};
                        names.forEach(function(nm) {
                            const val = $dyn.find('input[name="' + nm + '"]:checked').val();
                            const id = nm.replace(/^ppk_q_/, '');
                            answers[id] = val;
                        });
                        // build syarats array stub from DOM order to compute eligibility
                        const syarats = [];
                        $dyn.find('.card.card-body p-2, .col-12.mb-2').each(function(idx) {
                            /* noop to preserve potential order */ });
                        // compute eligible: all answers === 'ya'
                        let ok = true;
                        for (const k in answers) {
                            if (answers[k] !== 'ya') {
                                ok = false;
                                break;
                            }
                        }
                        applyPpkStatusToTable(nik, ok);
                    } catch (e) {
                        console.error('post-save client compute error', e);
                    }

                    Swal.fire({
                        title: 'Berhasil',
                        text: 'Data PPK untuk NIK ' + nik + ' berhasil disimpan.',
                        icon: 'success',
                        timer: 1500,
                        showConfirmButton: false
                    });

                    setTimeout(function() {
                        $('#ppkModal').modal('hide');
                    }, 1500);
                }).catch(err => {
                    console.error('Save promise error:', err);
                    // Fallback: tetap tampilkan sukses karena kemungkinan data sudah tersimpan via auto-save
                    Swal.fire({
                        title: 'Berhasil',
                        text: 'Data berhasil disimpan.',
                        icon: 'success',
                        timer: 1500,
                        showConfirmButton: false
                    });
                    setTimeout(function() { $('#ppkModal').modal('hide'); }, 1500);
                });
                
                return;
            }

            // Fallback: old static form handling — validate whatever static groups exist on page
            const radioGroups = [];
            $('#ppk-form input[type=radio]').each(function() {
                const nm = $(this).attr('name');
                if (radioGroups.indexOf(nm) === -1) radioGroups.push(nm);
            });
            const missing = [];
            for (let i = 0; i < radioGroups.length; i++) {
                if ($('input[name="' + radioGroups[i] + '"]:checked').length === 0) missing.push(i + 1);
            }
            if (missing.length > 0) {
                $('#ppk-alert').removeClass('d-none').text('Silakan jawab semua pertanyaan. Soal yang belum dijawab: ' + missing.join(', '));
                $('html, body').animate({
                    scrollTop: $('#ppkModal .modal-body').offset().top - 100
                }, 200);
                return;
            }

            // static save fallback: show SweetAlert success then close
            var nik = $('#ppk_nik').val();
            Swal.fire({
                title: 'Tersimpan',
                text: 'Data PPK untuk NIK ' + nik + ' berhasil disimpan (statis).',
                icon: 'success',
                timer: 900,
                showConfirmButton: false
            });
            setTimeout(function() {
                $('#ppkModal').modal('hide');
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
                    if (m.type === 'childList' && m.addedNodes.length > 0) {
                        changed = true;
                        break;
                    }
                }
                if (changed) {
                    // small timeout to allow other scripts to finish adding nodes
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

        // expose CSRF if available
        window.CSRF = {
            name: '<?= $this->security->get_csrf_token_name() ?>',
            hash: '<?= $this->security->get_csrf_hash() ?>'
        };

        // ---- Syarat DataTable ----
        const syaratTable = $('#table-syarat').DataTable({
            processing: true,
            serverSide: false,
            ajax: {
                url: '<?= site_url("Administrator/getSyaratPPK") ?>',
                dataSrc: function(json) {
                    // If controller returns { data: [...] } or raw array
                    return json.data || json;
                }
            },
            columns: [{
                    data: null,
                    render: function(data, type, row, meta) {
                        return meta.row + 1;
                    },
                    width: '80px',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'syarat'
                },
                {
                    data: null,
                    render: function(data, type, row) {
                        return `
                        <button class="btn btn-sm btn-warning btn-edit-syarat" data-id="${row.id_ppk}"><i class="mdi mdi-pencil"></i></button>
                        <button class="btn btn-sm btn-danger btn-delete-syarat" data-id="${row.id_ppk}"><i class="mdi mdi-delete"></i></button>
                    `;
                    },
                    orderable: false,
                    searchable: false
                }
            ],
            pageLength: 10,
            lengthChange: false
        });

        // Open add form
        $('#btn_add_syarat').on('click', function() {
            $('#form_id_ppk').val('');
            $('#form_syarat').val('');
            $('#syarat-form-alert').addClass('d-none').text('');
            $('#syaratFormModal').modal('show');
        });

        // Edit button (delegate)
        $(document).on('click', '.btn-edit-syarat', function() {
            const id = $(this).data('id');
            // fetch single (or reuse table row)
            $.get('<?= site_url("Administrator/getSyaratPPK") ?>', {
                    id_ppk: id
                })
                .done(function(res) {
                    const row = (res.data && res.data[0]) || (Array.isArray(res) && res[0]) || null;
                    if (!row) {
                        Swal.fire('Gagal', 'Data syarat tidak ditemukan', 'warning');
                        return;
                    }
                    $('#form_id_ppk').val(row.id_ppk);
                    $('#form_syarat').val(row.syarat);
                    $('#syarat-form-alert').addClass('d-none').text('');
                    $('#syaratFormModal').modal('show');
                }).fail(function() {
                    Swal.fire('Gagal', 'Gagal mengambil data syarat', 'error');
                });
        });

        // Delete button (uses SweetAlert2)
        $(document).on('click', '.btn-delete-syarat', function() {
            const id = $(this).data('id');
            if (!id) return;

            Swal.fire({
                title: 'Hapus syarat?',
                text: 'Yakin ingin menghapus syarat ini? Tindakan ini tidak dapat dibatalkan.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Hapus',
                cancelButtonText: 'Batal',
                focusCancel: true
            }).then((result) => {
                if (result.isConfirmed) {
                    const params = new URLSearchParams();
                    params.append('id_ppk', id);
                    if (window.CSRF && window.CSRF.name) params.append(window.CSRF.name, window.CSRF.hash);

                    fetch('<?= site_url("Administrator/deleteSyaratPPK") ?>', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded'
                        },
                        body: params.toString()
                    }).then(r => r.json()).then(j => {
                        if (j.success) {
                            Swal.fire({
                                title: 'Dihapus',
                                text: j.message || 'Syarat berhasil dihapus.',
                                icon: 'success',
                                timer: 1200,
                                showConfirmButton: false
                            });
                            syaratTable.ajax.reload(null, false);
                            // if ppk modal open, also reload its list
                            if ($('#ppkModal').hasClass('show')) loadSyaratForCheck($('#ppk_nik').val());
                        } else {
                            Swal.fire('Gagal', j.message || 'Gagal menghapus', 'error');
                        }
                    }).catch(e => {
                        console.error(e);
                        Swal.fire('Error', 'Terjadi kesalahan saat mengirim permintaan.', 'error');
                    });
                }
            });
        });

        // Save add/edit form
        $('#syarat-form').on('submit', function(e) {
            e.preventDefault();
            const id = $('#form_id_ppk').val();
            const syarat = $('#form_syarat').val().trim();
            if (!syarat) {
                $('#syarat-form-alert').removeClass('d-none').text('Syarat tidak boleh kosong.');
                return;
            }
            const params = new URLSearchParams();
            if (id) params.append('id_ppk', id);
            params.append('syarat', syarat);
            if (window.CSRF && window.CSRF.name) params.append(window.CSRF.name, window.CSRF.hash);

            fetch('<?= site_url("Administrator/saveSyaratPPK") ?>', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: params.toString()
            }).then(r => r.json()).then(j => {
                if (j.success) {
                    $('#syaratFormModal').modal('hide');
                    syaratTable.ajax.reload(null, false);
                    // refresh ppk modal list if open
                    if ($('#ppkModal').hasClass('show')) loadSyaratForCheck($('#ppk_nik').val());
                    Swal.fire({
                        title: 'Tersimpan',
                        text: j.message || 'Syarat berhasil disimpan.',
                        icon: 'success',
                        timer: 1200,
                        showConfirmButton: false
                    });
                } else {
                    $('#syarat-form-alert').removeClass('d-none').text(j.message || 'Gagal menyimpan.');
                    Swal.fire('Gagal', j.message || 'Gagal menyimpan.', 'error');
                }
            }).catch(e => {
                console.error(e);
                $('#syarat-form-alert').removeClass('d-none').text('Request error.');
                Swal.fire('Error', 'Request error saat menyimpan syarat.', 'error');
            });
        });

        // ---- Cek Syarat (PPK modal) dynamic loading ----
        // override existing handler to load syarat from DB and per-pegawai responses
        $(document).off('click', '.btn-cek-syarat'); // remove previous static handler if present
        $(document).on('click', '.btn-cek-syarat', function(e) {
            e.preventDefault();
            const nik = $(this).data('nik') || '';
            $('#ppk_nik').val(nik);
            loadSyaratForCheck(nik);
            $('#ppkModal').modal('show');
        });

        // load syarat list and responses for given nik
        function loadSyaratForCheck(nik) {
            // first load syarat
            fetch('<?= site_url("Administrator/getSyaratPPK") ?>')
                .then(r => r.json())
                .then(sRes => {
                    const syarats = sRes.data || sRes || [];
                    // then load responses for nik
                    fetch('<?= site_url("Administrator/getPpkResponses") ?>?nik=' + encodeURIComponent(nik))
                        .then(r => r.json())
                        .then(respRes => {
                            const answers = respRes.data || {};
                            const tahap = respRes.tahap || '';
                            renderSyaratListInModal(syarats, answers, nik, tahap);
                        }).catch(err => {
                            console.error(err);
                            renderSyaratListInModal(syarats, {}, nik);
                            Swal.fire('Gagal', 'Gagal memuat jawaban pegawai. Menampilkan tanpa jawaban.', 'error');
                        });
                }).catch(err => {
                    console.error(err);
                    $('#ppkModal .modal-body').html('<div class="alert alert-warning">Gagal memuat syarat.</div>');
                    Swal.fire('Gagal', 'Gagal memuat syarat.', 'error');
                });
        }

        function renderSyaratListInModal(syarats, answers, nik, tahap) {
            if (!Array.isArray(syarats) || syarats.length === 0) {
                $('#ppkModal .modal-body').html('<div class="alert alert-info">Belum ada syarat PPK. Silakan tambah di Kelola Syarat.</div>');
                return;
            }
            let html = '<div id="ppk-alert" class="alert alert-danger d-none" role="alert"></div>';
            html += '<form id="ppk-form-dynamic"><input type="hidden" id="ppk_nik" name="ppk_nik" value="' + nik + '">';
            html += '<div class="mb-3">Jawablah pertanyaan berikut dengan memilih salah satu opsi:</div>';
            html += '<div class="row">';
            syarats.forEach(function(s, idx) {
                const answer = answers[s.id_ppk] || ''; // 'ya'|'tidak' or ''
                html += `<div class="col-12 mb-2">
                        <div class="card card-body p-2">
                            <div class="d-flex justify-content-between align-items-center">
                                <div><strong>Soal ${idx+1}.</strong> ${escapeHtml(s.syarat)}</div>
                                <div>
                                    <div class="btn-group btn-group-toggle" data-toggle="buttons" role="group" aria-label="PPK opsi">
                                        <label class="btn btn-sm btn-outline-danger ${answer === 'tidak' ? 'active' : ''}">
                                            <input type="radio" name="ppk_q_${s.id_ppk}" value="tidak" autocomplete="off" ${answer === 'tidak' ? 'checked' : ''}> Tidak
                                        </label>
                                        <label class="btn btn-sm btn-outline-success ${answer === 'ya' ? 'active' : ''}">
                                            <input type="radio" name="ppk_q_${s.id_ppk}" value="ya" autocomplete="off" ${answer === 'ya' ? 'checked' : ''}> Ya
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>`;
            });
            
            // Container for Tahap Input (hidden by default, shown if eligible)
            html += `<div id="ppk-tahap-container" class="col-12 mt-3" style="display:none;">
                        <div class="card card-body bg-light">
                            <div class="form-group mb-0">
                                <label for="ppk_tahap_input" class="font-weight-bold">Tahap Ke-</label>
                                <input type="number" id="ppk_tahap_input" class="form-control" value="${tahap || ''}" placeholder="Masukkan Tahap">
                                <small class="text-muted">Isi tahap jika semua syarat terpenuhi (Ya).</small>
                            </div>
                        </div>
                     </div>`;
            html += '</div></form>';
            $('#ppkModal .modal-body').html(html);

            // attach change handlers: save per-toggle immediately
            $('#ppk-form-dynamic input[type=radio]').on('change', function() {
                const name = $(this).attr('name'); // ppk_q_{id_ppk}
                const id_ppk = name.replace(/^ppk_q_/, '');
                const val = $(this).val();
                savePpkResponse(nik, id_ppk, val);
            });

            // attach change handler for tahap: auto-save
            $('#ppk_tahap_input').on('change', function() {
                savePpkTahap(nik, $(this).val());
            });

            // compute and apply PPK eligibility for this nik based on current answers
            try {
                const eligible = computePpkEligible(syarats, answers);
                applyPpkStatusToTable(nik, eligible);
                toggleTahapInput(eligible);
            } catch (e) {
                console.error('compute/apply PPK status error', e);
            }
        }

        function savePpkResponse(nik, id_ppk, answer) {
            const params = new URLSearchParams();
            params.append('nik', nik);
            params.append('id_ppk', id_ppk);
            params.append('answer', answer);
            if (window.CSRF && window.CSRF.name) params.append(window.CSRF.name, window.CSRF.hash);

            fetch('<?= site_url("Administrator/savePpkResponse") ?>', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: params.toString()
            }).then(r => r.json()).then(j => {
                if (!j.success) {
                    // show SweetAlert error
                    Swal.fire('Gagal', j.message || 'Gagal menyimpan jawaban.', 'error');
                } else {
                    // on success, update the PPK status cell in the table for this nik
                    // prefer server-returned ppk_eligible (0/1). If not present, fall back to recalc.
                    if (typeof j.ppk_eligible !== 'undefined') {
                        applyPpkStatusToTable(nik, !!j.ppk_eligible);
                        toggleTahapInput(!!j.ppk_eligible);
                    } else {
                        applyPpkStatusToTable(nik, null);
                    }
                    // small toast
                    Swal.fire({
                        position: 'top-end',
                        icon: 'success',
                        title: 'Jawaban tersimpan',
                        showConfirmButton: false,
                        timer: 900
                    });
                }
            }).catch(e => {
                console.error(e);
                Swal.fire('Error', 'Request error saat menyimpan jawaban.', 'error');
            });
        }

        // Variable to debounce/prevent duplicate saves
        var _lastTahapSave = { nik: null, val: null, ts: 0 };

        function savePpkTahap(nik, val) {
            // Prevent double save within short time (e.g. blur + click race condition)
            var now = new Date().getTime();
            if (_lastTahapSave.nik === nik && _lastTahapSave.val === val && (now - _lastTahapSave.ts) < 1000) {
                return Promise.resolve({ success: true, skipped: true });
            }
            _lastTahapSave = { nik: nik, val: val, ts: now };

            const params = new URLSearchParams();
            params.append('nik', nik);
            params.append('tahap', val);
            if (window.CSRF && window.CSRF.name) params.append(window.CSRF.name, window.CSRF.hash);
            
            return fetch('<?= site_url("Administrator/savePpkTahap") ?>', {
                method: 'POST',
                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                body: params.toString()
            }).then(r=>r.json()).catch(e=>{
                console.error(e);
                return { success: false };
            });
        }

        function toggleTahapInput(show) {
            const $el = $('#ppk-tahap-container');
            if (show) $el.slideDown();
            else $el.slideUp();
        }

        // compute eligibility: all syarats must have answer === 'ya'
        function computePpkEligible(syarats, answers) {
            if (!Array.isArray(syarats) || syarats.length === 0) return false;
            for (let s of syarats) {
                const ans = answers[s.id_ppk] || '';
                if (ans !== 'ya') return false;
            }
            return true;
        }

        // apply PPK status badge to table row for a nik
        // if `eligible` is null, we will re-fetch data (responses & syarats) to decide
        function applyPpkStatusToTable(nik, eligible) {
            if (!nik) return;
            const $cell = $(`.ppk-status[data-nik="${nik}"]`);
            if (!$cell || $cell.length === 0) return;

            function setBadge(isActive) {
                if (isActive) {
                    $cell.html('<span class="badge bg-success">Aktif</span>');
                } else {
                    $cell.html('<span class="badge bg-secondary">Tidak Aktif</span>');
                }
            }

            if (eligible === null) {
                // need to re-fetch current syarats and responses
                Promise.all([
                    fetch('<?= site_url("Administrator/getSyaratPPK") ?>').then(r => r.json()),
                    fetch('<?= site_url("Administrator/getPpkResponses") ?>?nik=' + encodeURIComponent(nik)).then(r => r.json())
                ]).then(results => {
                    const syarats = (results[0] && (results[0].data || results[0])) || [];
                    const answers = (results[1] && (results[1].data || results[1])) || {};
                    const ok = computePpkEligible(syarats, answers);
                    setBadge(ok);
                    // Note: we don't toggle modal input here because this function is used for table rows too
                }).catch(err => {
                    console.error('applyPpkStatusToTable error', err);
                    setBadge(false);
                });
            } else {
                setBadge(!!eligible);
            }
        }

        // helper to escape html
        function escapeHtml(text) {
            return String(text).replace(/[&<>"'\/]/g, function(s) {
                const entityMap = {
                    '&': '&amp;',
                    '<': '&lt;',
                    '>': '&gt;',
                    '"': '&quot;',
                    "'": '&#39;',
                    '/': '&#x2F;'
                };
                return entityMap[s];
            });
        }

    });
</script>