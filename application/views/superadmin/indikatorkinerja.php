<div class="content-page">
    <div class="content">

        <div class="container-fluid">

            <!-- Judul Halaman -->
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box">
                        <div class="page-title-right">
                            <ol class="breadcrumb m-0">
                                <li class="breadcrumb-item"><a href="javascript:void(0);">SKI-BRKS</a></li>
                                <li class="breadcrumb-item active">Indikator Kinerja</li>
                            </ol>
                        </div>
                        <h4 class="page-title">Indikator Kinerja Pegawai</h4>
                    </div>
                </div>
            </div>

            <!-- Filter -->
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="header-title mb-3">Filter Indikator Kinerja</h4>
                            <form action="<?= base_url('SuperAdmin/indikatorKinerja'); ?>" method="get">
                                <label>Unit Kantor</label>
                                <select name="unit_kerja" id="unit_kerja_filter" class="form-control mb-2" required>
                                    <option value="">-- Pilih Unit Kantor --</option>
                                    <?php foreach ($unit_kerja as $uk): ?>
                                        <option value="<?= $uk->unit_kerja; ?>" <?= ($unit_kerja_terpilih == $uk->unit_kerja) ? 'selected' : ''; ?>>
                                            <?= $uk->unit_kerja; ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <label>Jabatan</label>
                                <select name="jabatan" id="jabatan_filter" class="form-control mb-2" required>
                                    <option value="">-- Pilih Jabatan --</option>
                                    <?php if ($jabatan_terpilih): ?>
                                        <option value="<?= $jabatan_terpilih; ?>" selected><?= $jabatan_terpilih; ?></option>
                                    <?php endif; ?>
                                </select>
                                <button type="submit" class="btn btn-info mt-2">Tampilkan Data</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Jika unit kerja sudah dipilih -->
            <?php if (isset($unit_kerja_terpilih) && $unit_kerja_terpilih): ?>
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <h4 class="header-title mb-3">
                                    Tambah Sasaran & Indikator untuk <?= $jabatan_terpilih; ?> di <?= $unit_kerja_terpilih; ?>
                                </h4>

                                <!-- Form Tambah Sasaran -->
                                <form id="formSasaran" class="mb-4">
                                    <input type="hidden" name="unit_kerja" value="<?= $unit_kerja_terpilih; ?>">
                                    <input type="hidden" name="jabatan" value="<?= $jabatan_terpilih; ?>">
                                    <label>Perspektif</label>
                                    <select name="perspektif" class="form-control" required>
                                        <?php foreach ($perspektif as $p): ?>
                                            <option value="<?= $p; ?>"><?= $p; ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                    <div id="sasaranWrapper" style="display:none;">
                                        <label>Sasaran Kerja</label>
                                        <input type="text" name="sasaran_kerja" class="form-control" required>
                                    </div>
                                    <button type="button" class="btn btn-primary mt-2" id="showSasaranBtn">Tambah Sasaran</button>
                                    <button type="button" class="btn btn-success mt-2" id="submitSasaranBtn" style="display:none;">Simpan Sasaran Kerja</button>
                                </form>


                                <!-- Form Tambah Indikator -->
                                <form id="formIndikator" class="mb-4">
                                    <label>Sasaran Kerja</label>
                                    <select name="sasaran_id" class="form-control" required>
                                        <?php foreach ($sasaran_kerja as $s): ?>
                                            <?php if ($s->unit_kerja == $unit_kerja_terpilih && $s->jabatan == $jabatan_terpilih): ?>
                                                <option value="<?= $s->id; ?>">
                                                    <?= $s->sasaran_kerja; ?> (<?= $s->perspektif; ?>)
                                                </option>
                                            <?php endif; ?>
                                        <?php endforeach; ?>
                                    </select>
                                    <div id="indikatorWrapper">
                                        <div class="row mt-2 indikator-row">
                                            <div class="col-md-6">
                                                <input type="text" name="indikator[]" class="form-control" placeholder="Indikator" required>
                                            </div>
                                            <div class="col-md-2">
                                                <input type="number" name="bobot[]" class="form-control" placeholder="Bobot (%)" required>
                                            </div>
                                            <div class="col-md-2">
                                                <button type="button" class="btn btn-success addIndikator">+</button>
                                            </div>
                                        </div>
                                    </div>
                                    <button type="button" class="btn btn-primary mt-3" id="submitIndikatorBtn">Simpan</button>
                                </form>

                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tabel Data Indikator -->
                <?php if (!empty($indikator)): ?>
                    <h3>Data Indikator Kinerja</h3>
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead style="background-color:#2E7D32; color:#fff; text-align:center;">
                                <tr>
                                    <th style="width:15%;">Perspektif</th>
                                    <th style="width:20%;">Sasaran Kerja</th>
                                    <th style="width:20%;">Indikator</th>
                                    <th style="width:5%;">Bobot (%)</th>
                                    <th style="width:10%;">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $grandTotal = 0;
                                foreach ($indikator as $perspektif => $sasaranList): ?>
                                    <tr style="background-color:#C8E6C9; font-weight:bold;">
                                        <td colspan="5"><?= $perspektif; ?></td>
                                    </tr>
                                    <?php
                                    $subtotal = 0;
                                    foreach ($sasaranList as $sasaran => $indikatorList):
                                        $no = 1; ?>
                                        <tr class="sasaran-row" data-id="<?= $indikatorList[0]->sasaran_id; ?>" style="background-color:#BBDEFB; font-weight:bold;">
                                            <td></td>
                                            <td class="sasaran-text" colspan="3">
                                                <?= $sasaran; ?>
                                                <button type="button" class="btn btn-sm btn-outline-warning editSasaranBtn">
                                                    <i class="mdi mdi-pencil"></i>
                                                </button>
                                            </td>
                                            <td></td>
                                        </tr>
                                        <?php foreach ($indikatorList as $i):
                                            $subtotal += $i->bobot; ?>
                                            <tr data-id="<?= $i->id; ?>">
                                                <td></td>
                                                <td></td>
                                                <td class="indikator-text"><?= $no++ . ". " . $i->indikator; ?></td>
                                                <td class="bobot-text" style="text-align:center;"><?= $i->bobot; ?></td>
                                                <td class="text-center">
                                                    <button type="button" class="btn btn-warning btn-sm editBtn">
                                                        <i class="mdi mdi-pencil"></i>
                                                    </button>
                                                    <a href="javascript:void(0);" class="btn btn-danger btn-sm deleteIndikatorBtn mt-0.5" data-id="<?= $i->id; ?>">
                                                        <i class="mdi mdi-delete"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endforeach; ?>
                                    <tr style="background-color:#E0E0E0; font-weight:bold;">
                                        <td colspan="3">Sub Total Bobot <?= $perspektif; ?></td>
                                        <td colspan="2"><?= $subtotal; ?></td>
                                    </tr>
                                    <?php $grandTotal += $subtotal; ?>
                                <?php endforeach; ?>
                                <tr style="background-color:#9CCC65; font-weight:bold;">
                                    <td colspan="3">TOTAL BOBOT KESELURUHAN</td>
                                    <td colspan="2"><?= $grandTotal; ?></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="alert alert-warning text-center">
                        Tidak ada data indikator yang ditemukan untuk jabatan ini.
                    </div>
                <?php endif; ?>

            <?php else: ?>
                <div class="alert alert-info text-center">
                    Silahkan pilih Unit Kantor dan Jabatan untuk menampilkan data indikator.
                </div>
            <?php endif; ?>

        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    const showBtn = document.getElementById('showSasaranBtn');
    const sasaranWrapper = document.getElementById('sasaranWrapper');
    const submitBtn = document.getElementById('submitSasaranBtn');

    if (showBtn) {
        showBtn.addEventListener('click', () => {
            sasaranWrapper.style.display = 'block';
            submitBtn.style.display = 'inline-block';
            showBtn.style.display = 'none';
        });
    }

    // 🔹 Load Jabatan by Unit
    document.getElementById('unit_kerja_filter').addEventListener('change', function() {
        const unit_kerja = this.value;
        const jabatanSelect = document.getElementById('jabatan_filter');

        jabatanSelect.innerHTML = '<option value="">-- Loading... --</option>';
        jabatanSelect.disabled = true;

        if (unit_kerja) {
            fetch(`<?= base_url('SuperAdmin/getJabatanByUnit?unit_kerja='); ?>${unit_kerja}`)
                .then(response => response.json())
                .then(data => {
                    jabatanSelect.innerHTML = '<option value="">-- Pilih Jabatan --</option>';
                    data.forEach(item => {
                        const option = document.createElement('option');
                        option.value = item.jabatan;
                        option.textContent = item.jabatan;
                        jabatanSelect.appendChild(option);
                    });
                    jabatanSelect.disabled = false;
                })
                .catch(error => {
                    console.error('Error:', error);
                    jabatanSelect.innerHTML = '<option value="">-- Gagal memuat data --</option>';
                    jabatanSelect.disabled = false;
                });
        } else {
            jabatanSelect.innerHTML = '<option value="">-- Pilih Jabatan --</option>';
            jabatanSelect.disabled = false;
        }
    });

    // 🔹 Fungsi hitung ulang subtotal & total bobot
    function recalcBobot() {
        let grandTotal = 0;

        $("tr.sasaran-row").each(function() {
            let perspektifRow = $(this).prevAll("tr")
                .filter(function() {
                    return $(this).css("background-color") === "rgb(200, 230, 201)"; // #C8E6C9
                }).first();

            let subtotalRow = perspektifRow.nextAll("tr")
                .filter(function() {
                    return $(this).css("background-color") === "rgb(224, 224, 224)"; // #E0E0E0
                }).first();

            let subtotal = 0;
            let indikatorRows = subtotalRow.prevUntil(perspektifRow, "tr[data-id]");
            indikatorRows.each(function() {
                let bobot = parseFloat($(this).find(".bobot-text").text()) || 0;
                subtotal += bobot;
            });

            subtotalRow.find("td:last").text(subtotal);
            grandTotal += subtotal;
        });

        $("tr:last td:last").text(grandTotal);
    }

    $(document).ready(function() {
        // 🔹 Tambah indikator baru
        $(document).on('click', '.addIndikator', function() {
            let newRow = `
                <div class="row mt-2 indikator-row">
                    <div class="col-md-6">
                        <input type="text" name="indikator[]" class="form-control" placeholder="Indikator" required>
                    </div>
                    <div class="col-md-2">
                        <input type="number" name="bobot[]" class="form-control" placeholder="Bobot (%)" required>
                    </div>
                    <div class="col-md-2">
                        <button type="button" class="btn btn-danger remove-row">-</button>
                    </div>
                </div>`;
            $('#indikatorWrapper').append(newRow);
        });

        // 🔹 Hapus indikator row (baru ditambahkan manual)
        $(document).on("click", ".remove-row", function() {
            $(this).closest(".indikator-row").remove();
        });

        // 🔹 Edit indikator
        $(document).on('click', '.editBtn', function(e) {
            let row = $(this).closest('tr');
            let indikatorText = row.find('.indikator-text').text().replace(/^\d+\.\s/, '');
            let bobotText = row.find('.bobot-text').text();
            let id = row.data('id');

            row.data('original', row.html());

            row.html(`
                <td></td>
                <td></td>
                <td class="indikator-edit-cell"><input type="text" class="form-control indikatorInput" value="${indikatorText}"></td>
                <td class="bobot-edit-cell"><input type="number" class="form-control bobotInput" value="${bobotText}"></td>
                <td class="text-center">
                    <button class="btn btn-success btn-sm saveBtn"><i class="mdi mdi-content-save"></i></button>
                    <button class="btn btn-secondary btn-sm cancelBtn mt-0.5"><i class="mdi mdi-close"></i></button>
                </td>
            `);
            row.find('.indikatorInput').focus();
        });

        // 🔹 Edit sasaran
        $(document).on('click', '.editSasaranBtn', function(e) {
            let row = $(this).closest('tr');
            let sasaranText = row.find('.sasaran-text').text().trim();
            let sasaranId = row.data('id');

            row.data('original', row.html());
            row.html(`
                <td></td>
                <td colspan="3">
                    <input type="text" class="form-control sasaranInput" value="${sasaranText}">
                </td>
                <td class="text-center">
                    <button class="btn btn-success btn-sm saveSasaranBtn"><i class="mdi mdi-content-save"></i></button>
                    <button class="btn btn-secondary btn-sm cancelBtn mt-0.5"><i class="mdi mdi-close"></i></button>
                </td>
            `);
            row.find('.sasaranInput').focus();
        });

        // 🔹 Cancel edit
        $(document).on('click', '.cancelBtn', function() {
            let row = $(this).closest('tr');
            row.html(row.data('original'));
            row.removeData('original');
        });

        // 🔹 Save indikator (pakai AJAX)
        $(document).on('click', '.saveBtn', function() {
            let row = $(this).closest('tr');
            let id = row.data('id');
            let indikator = row.find('.indikatorInput').val();
            let bobot = row.find('.bobotInput').val();

            $.ajax({
                url: "<?= base_url('SuperAdmin/updateIndikator'); ?>",
                method: "POST",
                data: {
                    id: id,
                    indikator: indikator,
                    bobot: bobot
                },
                dataType: "json",
                success: function(response) {
                    if (response.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil',
                            text: response.message,
                            timer: 2000,
                            showConfirmButton: false
                        }).then(() => {
                            row.html(`
                                <td></td>
                                <td></td>
                                <td class="indikator-text">${indikator}</td>
                                <td class="bobot-text" style="text-align:center;">${bobot}</td>
                                <td class="text-center">
                                    <button type="button" class="btn btn-warning btn-sm editBtn"><i class="mdi mdi-pencil"></i></button>
                                    <a href="<?= base_url('SuperAdmin/deleteIndikator/'); ?>${id}" class="btn btn-danger btn-sm deleteIndikatorBtn"><i class="mdi mdi-delete"></i></a>
                                </td>
                            `);

                            recalcBobot(); // 🔹 update subtotal & total bobot otomatis
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal',
                            text: response.message
                        });
                    }
                },
                error: function() {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Terjadi kesalahan saat menghubungi server.'
                    });
                }
            });
        });

        // 🔹 Save sasaran (pakai AJAX)
        $(document).on('click', '.saveSasaranBtn', function() {
            let row = $(this).closest('tr');
            let id = row.data('id');
            let sasaran = row.find('.sasaranInput').val();

            $.ajax({
                url: "<?= base_url('SuperAdmin/updateSasaran'); ?>",
                method: "POST",
                data: {
                    id: id,
                    sasaran: sasaran
                },
                dataType: "json",
                success: function(response) {
                    if (response.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil',
                            text: response.message,
                            timer: 2000,
                            showConfirmButton: false
                        }).then(() => {
                            row.html(`
                                <td></td>
                                <td class="sasaran-text" colspan="3">
                                    ${sasaran}
                                    <button type="button" class="btn btn-sm btn-outline-warning editSasaranBtn"><i class="mdi mdi-pencil"></i></button>
                                </td>
                                <td></td>
                            `);
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal',
                            text: response.message
                        });
                    }
                },
                error: function() {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Terjadi kesalahan saat menghubungi server.'
                    });
                }
            });
        });

        // 🔹 Hapus indikator pakai AJAX
        $(document).on("click", ".deleteIndikatorBtn", function(e) {
            e.preventDefault();
            let row = $(this).closest("tr");
            let id = row.data("id");

            Swal.fire({
                title: "Yakin hapus?",
                text: "Indikator ini akan dihapus permanen",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Ya, hapus!",
                cancelButtonText: "Batal"
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "<?= base_url('SuperAdmin/deleteIndikator/'); ?>" + id,
                        method: "POST",
                        dataType: "json",
                        success: function(res) {
                            if (res.success) {
                                row.remove();
                                recalcBobot();
                                Swal.fire("Berhasil", res.message, "success");
                            } else {
                                Swal.fire("Gagal", res.message, "error");
                            }
                        },
                        error: function() {
                            Swal.fire("Error", "Server tidak merespon", "error");
                        }
                    });
                }
            });
        });
    });

    // 🔹 Simpan Sasaran via AJAX
    $("#submitSasaranBtn").on("click", function() {
        $.ajax({
            url: "<?= base_url('SuperAdmin/saveSasaranAjax'); ?>",
            method: "POST",
            data: $("#formSasaran").serialize(),
            dataType: "json",
            success: function(res) {
                if (res.success) {
                    Swal.fire("Berhasil", res.message, "success").then(() => {
                        location.reload();
                    });
                } else {
                    Swal.fire("Gagal", res.message, "error");
                }
            },
            error: function() {
                Swal.fire("Error", "Tidak bisa menghubungi server", "error");
            }
        });
    });

    // 🔹 Simpan Indikator via AJAX
    $("#submitIndikatorBtn").on("click", function() {
        $.ajax({
            url: "<?= base_url('SuperAdmin/saveIndikatorAjax'); ?>",
            method: "POST",
            data: $("#formIndikator").serialize(),
            dataType: "json",
            success: function(res) {
                if (res.success) {
                    Swal.fire("Berhasil", res.message, "success").then(() => {
                        location.reload();
                    });
                } else {
                    Swal.fire("Gagal", res.message, "error");
                }
            },
            error: function() {
                Swal.fire("Error", "Tidak bisa menghubungi server", "error");
            }
        });
    });
</script>