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
                            <i class="mdi mdi-briefcase mr-2 text-primary"></i> Kelola Tingkatan Jabatan
                        </h3>
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="#">KPI Online-BRKS</a></li>
                            <li class="breadcrumb-item active">Kelola Jabatan</li>
                        </ol>
                    </div>
                </div>
            </div>

            <!-- STEP 1: Tabel Kode Cabang -->
            <div class="card mb-3" id="card-cabang">
                <div class="card-body">
                    <!-- Header dengan Icon + Search -->
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="mb-0">
                            <i class="mdi mdi-office-building mr-2 text-primary"></i>
                            Daftar Kode Cabang
                        </h5>
                    </div>

                    <!-- Tabel -->
                    <table class="table table-bordered" id="dt-cabang">
                        <thead>
                            <tr>
                                <th width="5%">No</th>
                                <th>Kode Cabang</th>
                                <th width="15%">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $no = 1; ?>
                            <?php foreach ($kode_cabang as $row): ?>
                                <tr>
                                    <td><?= $no++; ?></td>
                                    <td><?= htmlspecialchars($row->kode_cabang) . ' - ' . ($row->unit_kantor ?? '') ?></td>
                                    <td>
                                        <button class="btn btn-primary btn-sm btn-atur-cabang" data-cabang="<?= htmlspecialchars($row->kode_cabang) ?>">
                                            Atur
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- STEP 2: Tabel Kode Unit (AJAX) -->
            <div class="card mb-3 d-none" id="card-unit">
                <div class="card-body">
                    <h5 class="mb-3">Daftar Kode Unit di Cabang <span id="label-cabang"></span></h5>
                    <table class="table table-bordered" id="dt-unit">
                        <thead>
                            <tr>
                                <th width="5%">No</th>
                                <th>Kode Unit</th>
                                <th width="15%">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td colspan="3" class="text-center">Memuat...</td>
                            </tr>
                        </tbody>
                    </table>
                    <button class="btn btn-secondary mt-2" id="btn-back-cabang">Kembali ke Cabang</button>
                </div>
            </div>

            <!-- STEP 3: Tabel Mapping Jabatan (AJAX) -->
            <div class="card mb-3 d-none" id="card-mapping">
                <div class="card-body">
                    <h5 class="mb-3">Mapping Jabatan di Unit <span id="label-unit"></span></h5>
                    <button class="btn btn-success mb-2" id="btn-tambah-mapping">+ Tambah Mapping</button>
                    <table class="table table-bordered" id="dt-mapping">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Jabatan</th>
                                <th>Jenis Penilaian</th>
                                <th>Penilai I</th>
                                <th>Penilai II</th>
                                <th width="15%">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td colspan="6" class="text-center">Pilih unit terlebih dahulu</td>
                            </tr>
                        </tbody>
                    </table>
                    <button class="btn btn-secondary mt-2" id="btn-back-unit">Kembali ke Unit</button>
                </div>
            </div>

        </div>
    </div>
</div>

<!-- Modal Tambah/Edit Mapping -->
<div class="modal fade" id="modalMapping" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="formMapping" method="post">
                <div class="modal-header">
                    <h5 class="modal-title">Tambah/Edit Mapping Jabatan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="id" id="idMapping">
                    <input type="hidden" name="unit_kerja" id="unitKerja">
                    <input type="hidden" name="kode_cabang" id="kodeCabang">
                    <input type="hidden" name="kode_unit" id="kodeUnit">

                    <div class="mb-2">
                        <label>Jabatan</label>
                        <input type="text" name="jabatan" id="jabatan" class="form-control" required>
                    </div>

                    <div class="mb-2">
                        <label>Jenis Penilaian</label>
                        <select name="jenis_penilaian" id="jenis_penilaian" class="form-control" required>
                            <option value="">-- Pilih Jenis Penilaian --</option>
                            <option value="SKI">SKI</option>
                            <option value="KPI">KPI</option>
                        </select>
                    </div>

                    <div class="mb-2">
                        <label>Penilai I</label>
                        <select name="penilai1_jabatan" id="penilai1_jabatan" class="form-control">
                            <option value="">-- Pilih Penilai I --</option>
                        </select>
                    </div>

                    <div class="mb-2">
                        <label>Penilai II</label>
                        <select name="penilai2_jabatan" id="penilai2_jabatan" class="form-control">
                            <option value="">-- Pilih Penilai II --</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>


<!-- =============================== -->
<!-- JAVASCRIPT -->
<!-- =============================== -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    (function() {
        // tunggu jQuery + DataTables tersedia. retry sampai 5s.
        var tryRun = function() {
            if (!window.jQuery || !window.jQuery.fn || !window.jQuery.fn.DataTable) return false;

            // semua kode asli di dalam $(function(){ ... }) dipindahkan ke sini
            $(function() {
                try {
                    // =================== DataTables Kode Cabang ===================
                    if ($('#dt-cabang').length) {
                        console.log("✅ DataTables dt-cabang Init Started...");
                        $('#dt-cabang').DataTable({
                            responsive: true,
                            pageLength: 5,
                            lengthMenu: [
                                [5, 10, 25, 50, -1],
                                [5, 10, 25, 50, "Semua"]
                            ],
                            order: [],
                            dom: '<"row mb-2"<"col-md-6 d-flex align-items-center"l><"col-md-6 text-right"f>>' +
                                'rt' +
                                '<"row mt-2"<"col-md-6"i><"col-md-6 d-flex justify-content-end"p>>',
                            language: {
                                search: "🔍 Pencarian:",
                                searchPlaceholder: "Cari Kode Cabang...",
                                lengthMenu: "Tampilkan _MENU_ data",
                                zeroRecords: "Data tidak ditemukan",
                                info: "Menampilkan _START_ - _END_ dari _TOTAL_ data",
                                infoEmpty: "Tidak ada data tersedia",
                                infoFiltered: "(difilter dari _MAX_ total data)",
                                paginate: {
                                    previous: "Sebelumnya",
                                    next: "Berikutnya"
                                }
                            }
                        });
                        console.log("✅ DataTables dt-cabang berhasil diinisialisasi");
                    }

                    // =================== DataTables Kode Unit ===================
                    if ($('#dt-unit').length) {
                        console.log("✅ DataTables dt-unit Init Started...");
                        $('#dt-unit').DataTable({
                            responsive: true,
                            pageLength: 5,
                            lengthMenu: [
                                [5, 10, 25, 50, -1],
                                [5, 10, 25, 50, "Semua"]
                            ],
                            order: [],
                            dom: '<"row mb-2"<"col-md-6 d-flex align-items-center"l><"col-md-6 text-right"f>>' +
                                'rt' +
                                '<"row mt-2"<"col-md-6"i><"col-md-6 d-flex justify-content-end"p>>',
                            language: {
                                search: "🔍 Pencarian:",
                                searchPlaceholder: "Cari Kode Unit...",
                                lengthMenu: "Tampilkan _MENU_ data",
                                zeroRecords: "Data tidak ditemukan",
                                info: "Menampilkan _START_ - _END_ dari _TOTAL_ data",
                                infoEmpty: "Tidak ada data tersedia",
                                infoFiltered: "(difilter dari _MAX_ total data)",
                                paginate: {
                                    previous: "Sebelumnya",
                                    next: "Berikutnya"
                                }
                            }
                        });
                        console.log("✅ DataTables dt-unit berhasil diinisialisasi");
                    }

                    // =================== DataTables Mapping Jabatan ===================
                    if ($('#dt-mapping').length) {
                        console.log("✅ DataTables dt-mapping Init Started...");
                        $('#dt-mapping').DataTable({
                            responsive: true,
                            pageLength: 5,
                            lengthMenu: [
                                [5, 10, 25, 50, -1],
                                [5, 10, 25, 50, "Semua"]
                            ],
                            order: [],
                            dom: '<"row mb-2"<"col-md-6 d-flex align-items-center"l><"col-md-6 text-right"f>>' +
                                'rt' +
                                '<"row mt-2"<"col-md-6"i><"col-md-6 d-flex justify-content-end"p>>',
                            language: {
                                search: "🔍 Pencarian:",
                                searchPlaceholder: "Cari Mapping Jabatan...",
                                lengthMenu: "Tampilkan _MENU_ data",
                                zeroRecords: "Data tidak ditemukan",
                                info: "Menampilkan _START_ - _END_ dari _TOTAL_ data",
                                infoEmpty: "Tidak ada data tersedia",
                                infoFiltered: "(difilter dari _MAX_ total data)",
                                paginate: {
                                    previous: "Sebelumnya",
                                    next: "Berikutnya"
                                }
                            }
                        });
                        console.log("✅ DataTables dt-mapping berhasil diinisialisasi");
                    }

                } catch (err) {
                    console.error('DataTables init gagal:', err);
                    // fallback: tetap pasang event handler tanpa DataTables
                    var dtCabang = null,
                        dtUnit = null,
                        dtMapping = null;
                }

                // ================= STEP 1: PILIH CABANG =================
                $(document).on('click', '.btn-atur-cabang', function() {
                    var kode_cabang = $(this).data('cabang');
                    if (!kode_cabang) return;

                    $('#label-cabang').text(kode_cabang);
                    $('#kodeCabang').val(kode_cabang);
                    $('#card-unit').removeClass('d-none');
                    $('#card-mapping').addClass('d-none');
                    $('#card-cabang').hide();

                    if (dtUnit && dtUnit.clear) {
                        dtUnit.clear().row.add(['', 'Memuat...', '']).draw();
                    } else {
                        $('#dt-unit tbody').html('<tr><td colspan="3" class="text-center">Memuat...</td></tr>');
                    }

                    $.getJSON('<?= base_url("administrator/getKodeUnit/") ?>' + encodeURIComponent(kode_cabang), function(data) {
                        if (dtUnit && dtUnit.row) {
                            dtUnit.clear();
                            if (data.length) {
                                data.forEach(function(row, i) {
                                    dtUnit.row.add([
                                        i + 1,
                                        row.kode_unit + ' - ' + (row.unit_kantor ?? ''),
                                        `<button class="btn btn-info btn-sm btn-atur-unit" data-unit="${row.kode_unit}" data-cabang="${kode_cabang}">Atur</button>`
                                    ]).draw(false);
                                });
                            } else {
                                dtUnit.row.add(['', 'Tidak ada unit', '']).draw();
                            }
                        } else {
                            var html = '';
                            if (data.length) {
                                data.forEach(function(row, i) {
                                    html += '<tr><td>' + (i + 1) + '</td><td>' + row.kode_unit + ' - ' + (row.unit_kantor || '') + '</td><td><button class="btn btn-info btn-sm btn-atur-unit" data-unit="' + row.kode_unit + '" data-cabang="' + kode_cabang + '">Atur</button></td></tr>';
                                });
                            } else {
                                html = '<tr><td colspan="3" class="text-center">Tidak ada unit</td></tr>';
                            }
                            $('#dt-unit tbody').html(html);
                        }
                    }).fail(function() {
                        if (dtUnit && dtUnit.clear) {
                            dtUnit.clear().row.add(['', 'Gagal memuat unit', '']).draw();
                        } else {
                            $('#dt-unit tbody').html('<tr><td colspan="3" class="text-center">Gagal memuat unit</td></tr>');
                        }
                    });
                });

                $('#btn-back-cabang').on('click', function() {
                    $('#card-unit').addClass('d-none');
                    $('#card-cabang').show();
                });

                // ================= STEP 2: PILIH UNIT =================
                $(document).on('click', '.btn-atur-unit', function() {
                    var kode_unit = $(this).data('unit');
                    var kode_cabang = $(this).data('cabang');
                    if (!kode_unit) return;

                    $('#label-unit').text(kode_unit);
                    $('#kodeUnit').val(kode_unit);
                    $('#kodeCabang').val(kode_cabang);
                    $('#card-mapping').removeClass('d-none');
                    $('#card-unit').hide();

                    loadMapping(encodeURIComponent(kode_unit));
                });

                // ================= STEP 3: LOAD MAPPING =================
                function loadMapping(kode_unit) {
                    if (dtMapping && dtMapping.clear) {
                        dtMapping.clear().row.add(['', 'Memuat...', '', '', '', '']).draw();
                    } else {
                        $('#dt-mapping tbody').html('<tr><td colspan="6" class="text-center">Memuat...</td></tr>');
                    }

                    $.getJSON('<?= base_url("administrator/getMappingJabatan/") ?>' + kode_unit, function(data) {
                        if (dtMapping && dtMapping.row) {
                            dtMapping.clear();
                            if (data.length) {
                                data.forEach(function(row, i) {
                                    dtMapping.row.add([
                                        i + 1,
                                        row.jabatan,
                                        row.jenis_penilaian,
                                        row.penilai1_jabatan ?? '-',
                                        row.penilai2_jabatan ?? '-',
                                        `<button class="btn btn-warning btn-sm btn-edit-mapping" 
                                    data-id="${row.id}" 
                                    data-jabatan="${row.jabatan}" 
                                    data-jenis="${row.jenis_penilaian}" 
                                    data-penilai1="${row.penilai1_jabatan}" 
                                    data-penilai2="${row.penilai2_jabatan}" 
                                    data-unit="${row.unit_kerja}" 
                                    data-cabang="${row.kode_cabang}">Edit</button>
                                 <button class="btn btn-danger btn-sm btn-hapus-mapping" data-id="${row.id}">Hapus</button>`
                                    ]).draw(false);
                                });
                            } else {
                                dtMapping.row.add(['', 'Tidak ada mapping jabatan', '', '', '', '']).draw();
                            }
                        } else {
                            var html = '';
                            if (data.length) {
                                data.forEach(function(row, i) {
                                    html += '<tr><td>' + (i + 1) + '</td><td>' + row.jabatan + '</td><td>' + row.jenis_penilaian + '</td><td>' + (row.penilai1_jabatan || '-') + '</td><td>' + (row.penilai2_jabatan || '-') + '</td><td><button class="btn btn-warning btn-sm btn-edit-mapping" data-id="' + row.id + '" data-jabatan="' + row.jabatan + '" data-jenis="' + row.jenis_penilaian + '" data-penilai1="' + (row.penilai1_jabatan || '') + '" data-penilai2="' + (row.penilai2_jabatan || '') + '" data-unit="' + row.unit_kerja + '" data-cabang="' + row.kode_cabang + '">Edit</button> <button class="btn btn-danger btn-sm btn-hapus-mapping" data-id="' + row.id + '">Hapus</button></td></tr>';
                                });
                            } else {
                                html = '<tr><td colspan="6" class="text-center">Tidak ada mapping jabatan</td></tr>';
                            }
                            $('#dt-mapping tbody').html(html);
                        }
                    }).fail(function() {
                        if (dtMapping && dtMapping.clear) {
                            dtMapping.clear().row.add(['', 'Gagal memuat data', '', '', '', '']).draw();
                        } else {
                            $('#dt-mapping tbody').html('<tr><td colspan="6" class="text-center">Gagal memuat data</td></tr>');
                        }
                    });
                }

                $('#btn-back-unit').on('click', function() {
                    $('#card-mapping').addClass('d-none');
                    $('#card-unit').show();
                });

                // ================= STEP 4: TAMBAH / EDIT MAPPING =================
                $('#btn-tambah-mapping').on('click', function() {
                    $('#formMapping')[0].reset();
                    $('#idMapping').val('');
                    $('#unitKerja').val($('#label-unit').text());
                    $('#kodeUnit').val($('#label-unit').text());
                    $('#kodeCabang').val($('#kodeCabang').val());

                    // Load penilai dari unit yang sama
                    var kode_unit = $('#kodeUnit').val();
                    $.getJSON('<?= base_url("administrator/getMappingJabatanEdit/") ?>' + kode_unit, function(data) {
                        var options = '<option value="">-- Pilih Penilai --</option>';
                        data.forEach(function(row) {
                            options += `<option value="${row.jabatan}">${row.jabatan}</option>`;
                        });
                        $('#penilai1_jabatan').html(options);
                        $('#penilai2_jabatan').html(options);
                    });

                    $('#modalMapping').modal('show');
                });


                // Edit Mapping (diperbaiki)
                $(document).on('click', '.btn-edit-mapping', function() {
                    var id = $(this).data('id');
                    var jabatan = $(this).data('jabatan');
                    var jenis = $(this).data('jenis');
                    var penilai1 = $(this).data('penilai1'); // nama penilai1
                    var penilai2 = $(this).data('penilai2'); // nama penilai2
                    var unitKerja = $(this).data('unit');
                    var kode_cabang = $(this).data('cabang');
                    var kode_unit = $('#label-unit').text();

                    $('#formMapping')[0].reset();
                    $('#idMapping').val(id);
                    $('#unitKerja').val(unitKerja);
                    $('#kodeUnit').val(kode_unit);
                    $('#kodeCabang').val(kode_cabang);
                    $('#jabatan').val(jabatan);
                    $('#jenis_penilaian').val(jenis);

                    // Load semua penilai untuk select
                    $.getJSON('<?= base_url("administrator/getMappingJabatanEdit/") ?>' + kode_unit, function(data) {
                        var options1 = '<option value="">-- Pilih Penilai I --</option>';
                        var options2 = '<option value="">-- Pilih Penilai II --</option>';

                        data.forEach(function(row) {
                            var sel1 = (row.jabatan === penilai1) ? ' selected' : '';
                            var sel2 = (row.jabatan === penilai2) ? ' selected' : '';
                            options1 += `<option value="${row.jabatan}"${sel1}>${row.jabatan}</option>`;
                            options2 += `<option value="${row.jabatan}"${sel2}>${row.jabatan}</option>`;
                        });

                        $('#penilai1_jabatan').html(options1);
                        $('#penilai2_jabatan').html(options2);
                    });

                    $('#modalMapping').modal('show');
                });


                // Hapus Mapping
                $(document).on('click', '.btn-hapus-mapping', function() {
                    var id = $(this).data('id');
                    Swal.fire({
                        title: 'Yakin ingin menghapus?',
                        text: "Data tidak bisa dikembalikan!",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Ya, hapus!',
                        cancelButtonText: 'Batal'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            $.post('<?= base_url("administrator/hapusPenilaiMapping/") ?>' + id, function() {
                                Swal.fire(
                                    'Terhapus!',
                                    'Data berhasil dihapus.',
                                    'success'
                                );
                                loadMapping($('#label-unit').text());
                            }).fail(function() {
                                Swal.fire(
                                    'Gagal!',
                                    'Gagal menghapus data.',
                                    'error'
                                );
                            });
                        }
                    });
                });

                // Simpan Mapping (Tambah/Edit)
                $('#formMapping').on('submit', function(e) {
                    e.preventDefault();
                    var id = $('#idMapping').val();
                    var url = id ? '<?= base_url("administrator/editPenilaiMapping/") ?>' + id : '<?= base_url("administrator/tambahPenilaiMapping") ?>';
                    $.post(url, $(this).serialize(), function() {
                        $('#modalMapping').modal('hide');
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil',
                            text: 'Data berhasil disimpan',
                            timer: 1500,
                            showConfirmButton: false
                        });
                        loadMapping($('#label-unit').text());
                    }).fail(function() {
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal',
                            text: 'Gagal menyimpan data'
                        });
                    });
                });
            }); // end jQuery ready

            return true;
        };

        if (!tryRun()) {
            var iv = setInterval(function() {
                if (tryRun()) clearInterval(iv);
            }, 50);
            setTimeout(function() {
                clearInterval(iv);
            }, 5000);
        }
    })();
</script>