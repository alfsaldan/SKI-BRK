<!-- ============================================================== -->
<!-- Start Page Content here -->
<!-- ============================================================== -->

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
                                <li class="breadcrumb-item"><a href="javascript:void(0);">SKI-BRKS</a></li>
                                <li class="breadcrumb-item"><a href="javascript:void(0);">Super Admin</a></li>
                                <li class="breadcrumb-item active">Indikator Kinerja</li>
                            </ol>
                        </div>
                        <h4 class="page-title">Indikator Kinerja Pegawai</h4>
                    </div>
                </div>
            </div>
            <!-- end page title -->

            <!-- start card content -->
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">

                            <!-- Form Tambah Sasaran Kerja -->
                            <h4 class="header-title mb-3">Form Indikator Kinerja</h4>
                            <form method="post" action="<?= base_url('SuperAdmin/addSasaranKerja'); ?>" class="mb-4">
                                <label>Jabatan</label>
                                <input type="text" name="jabatan" class="form-control" required>

                                <label>Perspektif</label>
                                <select name="perspektif" class="form-control" required>
                                    <?php foreach ($perspektif as $p): ?>
                                        <option value="<?= $p; ?>"><?= $p; ?></option>
                                    <?php endforeach; ?>
                                </select>

                                <label>Sasaran Kerja</label>
                                <input type="text" name="sasaran_kerja" class="form-control" required>

                                <button type="submit" class="btn btn-primary mt-2">Tambah Sasaran Kerja</button>
                            </form>

                            <!-- Form Tambah Indikator -->
                            <form method="post" action="<?= base_url('SuperAdmin/addIndikator'); ?>" class="mb-4">
                                <label>Sasaran Kerja</label>
                                <select name="sasaran_id" class="form-control" required>
                                    <?php foreach ($sasaran_kerja as $s): ?>
                                        <option value="<?= $s->id; ?>"><?= $s->sasaran_kerja; ?> (<?= $s->perspektif; ?>)
                                        </option>
                                    <?php endforeach; ?>
                                </select>

                                <div id="indikatorWrapper">
                                    <div class="row mt-2">
                                        <div class="col-md-6">
                                            <input type="text" name="indikator[]" class="form-control"
                                                placeholder="Indikator" required>
                                        </div>
                                        <div class="col-md-2">
                                            <input type="number" name="bobot[]" class="form-control"
                                                placeholder="Bobot (%)" required>
                                        </div>
                                        <div class="col-md-2">
                                            <button type="button" class="btn btn-success addIndikator">+</button>
                                        </div>
                                    </div>
                                </div>

                                <button type="submit" class="btn btn-primary mt-3">Finish</button>
                            </form>

                            <!-- Tabel Data -->
                            <h3>Data Indikator Kinerja</h3>
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead style="background-color:#2E7D32; color:#fff; text-align:center;">
                                        <tr>
                                            <th style="width:20%;">Perspektif</th>
                                            <th style="width:25%;">Sasaran Kerja</th>
                                            <th style="width:35%;">Indikator</th>
                                            <th style="width:10%;">Bobot (%)</th>
                                            <th style="width:10%;">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $grandTotal = 0; 
                                        foreach ($indikator as $perspektif => $sasaranList): ?>
                                            <!-- Perspektif -->
                                            <tr style="background-color:#C8E6C9; font-weight:bold;">
                                                <td colspan="5"><?= $perspektif; ?></td>
                                            </tr>

                                            <?php
                                            $subtotal = 0;
                                            foreach ($sasaranList as $sasaran => $indikatorList):
                                                $no = 1; ?>
                                                <!-- Sasaran Kerja -->
                                                <tr class="sasaran-row" data-id="<?= $indikatorList[0]->sasaran_id; ?>"
                                                    style="background-color:#BBDEFB; font-weight:bold;">
                                                    <td></td>
                                                    <td class="sasaran-text" colspan="4">
                                                        <?= $sasaran; ?>
                                                        <button type="button"
                                                            class="btn btn-sm btn-outline-warning editSasaranBtn">
                                                            <i class="mdi mdi-pencil"></i>
                                                        </button>
                                                    </td>
                                                </tr>

                                                <!-- Indikator -->
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
                                                            <a href="<?= base_url('SuperAdmin/deleteIndikator/' . $i->id); ?>"
                                                                class="btn btn-danger btn-sm"
                                                                onclick="return confirm('Yakin hapus?')">
                                                                <i class="mdi mdi-delete"></i>
                                                            </a>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            <?php endforeach; ?>

                                            <!-- Sub Total -->
                                            <tr style="background-color:#E0E0E0; font-weight:bold;">
                                                <td colspan="3">Sub Total Bobot <?= $perspektif; ?></td>
                                                <td colspan="2"><?= $subtotal; ?></td>
                                            </tr>

                                            <?php $grandTotal += $subtotal; ?>
                                        <?php endforeach; ?>

                                        <!-- Total Bobot Keseluruhan -->
                                        <tr style="background-color:#9CCC65; font-weight:bold;">
                                            <td colspan="3">TOTAL BOBOT KESELURUHAN</td>
                                            <td colspan="2"><?= $grandTotal; ?></td>
                                        </tr>
                                    </tbody>

                                </table>
                            </div>


                        </div> <!-- end card-body -->
                    </div> <!-- end card -->
                </div> <!-- end col -->
            </div> <!-- end row -->
            <!-- end card content -->

        </div> <!-- end container-fluid -->

    </div> <!-- end content -->
</div> <!-- end content-page -->

<!-- ============================================================== -->
<!-- End Page content -->
<!-- ============================================================== -->

<script>
    document.addEventListener('click', function (e) {
        if (e.target.classList.contains('addIndikator')) {
            let newRow = `
        <div class="row mt-2">
            <div class="col-md-6">
                <input type="text" name="indikator[]" class="form-control" placeholder="Indikator" required>
            </div>
            <div class="col-md-2">
                <input type="number" name="bobot[]" class="form-control" placeholder="Bobot (%)" required>
            </div>
            <div class="col-md-2">
                <button type="button" class="btn btn-danger removeIndikator">x</button>
            </div>
        </div>`;
            document.getElementById('indikatorWrapper').insertAdjacentHTML('beforeend', newRow);
        }

        if (e.target.classList.contains('removeIndikator')) {
            e.target.closest('.row').remove();
        }
    });
</script>

<script>
    document.addEventListener('click', function (e) {
        // Inline Edit Indikator
        if (e.target.closest('.editBtn')) {
            let row = e.target.closest('tr');
            let indikatorText = row.querySelector('.indikator-text').innerText;
            let bobotText = row.querySelector('.bobot-text').innerText;
            let id = row.dataset.id;

            row.dataset.original = row.innerHTML; 

            row.innerHTML = `
            <td></td>
            <td></td>
            <td><input type="text" class="form-control indikatorInput" value="${indikatorText.replace(/^\d+\.\s/, '')}"></td>
            <td><input type="number" class="form-control bobotInput" value="${bobotText}"></td>
            <td>
                <button class="btn btn-success btn-sm saveBtn"><i class="mdi mdi-content-save"></i></button>
                <button class="btn btn-secondary btn-sm cancelBtn"><i class="mdi mdi-close"></i></button>
            </td>
        `;
            row.querySelector('.indikatorInput').focus();
        }

        // Inline Edit Sasaran Kerja
        if (e.target.closest('.editSasaranBtn')) {
            let row = e.target.closest('tr');
            let sasaranText = row.querySelector('.sasaran-text').innerText.trim();
            let sasaranId = row.dataset.id;

            row.dataset.original = row.innerHTML;

            row.innerHTML = `
            <td></td>
            <td colspan="3">
                <input type="text" class="form-control sasaranInput" value="${sasaranText}">
            </td>
            <td>
                <button class="btn btn-success btn-sm saveSasaranBtn"><i class="mdi mdi-content-save"></i></button>
                <button class="btn btn-secondary btn-sm cancelBtn"><i class="mdi mdi-close"></i></button>
            </td>
        `;
            row.querySelector('.sasaranInput').focus();
        }

        // Cancel Edit
        if (e.target.closest('.cancelBtn')) {
            let row = e.target.closest('tr');
            row.innerHTML = row.dataset.original;
        }

        // Save Edit Indikator
        if (e.target.closest('.saveBtn')) {
            let row = e.target.closest('tr');
            let id = row.dataset.id;
            let indikator = row.querySelector('.indikatorInput').value;
            let bobot = row.querySelector('.bobotInput').value;

            fetch("<?= base_url('SuperAdmin/updateIndikator'); ?>", {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify({ id: id, indikator: indikator, bobot: bobot })
            })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        row.innerHTML = `
                    <td></td>
                    <td></td>
                    <td class="indikator-text">${indikator}</td>
                    <td class="bobot-text" style="text-align:center;">${bobot}</td>
                    <td class="text-center">
                        <button type="button" class="btn btn-warning btn-sm editBtn"><i class="mdi mdi-pencil"></i></button>
                        <a href="<?= base_url('SuperAdmin/deleteIndikator/'); ?>${id}" 
                           class="btn btn-danger btn-sm"
                           onclick="return confirm('Yakin hapus?')"><i class="mdi mdi-delete"></i></a>
                    </td>
                `;
                    } else {
                        alert("Gagal update data!");
                    }
                })
                .catch(err => {
                    alert("Terjadi error saat update.");
                    console.error(err);
                });
        }

        // Save Edit Sasaran Kerja
        if (e.target.closest('.saveSasaranBtn')) {
            let row = e.target.closest('tr');
            let id = row.dataset.id;
            let sasaran = row.querySelector('.sasaranInput').value;

            (async () => {
                try {
                    let response = await fetch("<?= base_url('SuperAdmin/updateSasaran'); ?>", {
                        method: "POST",
                        headers: { "Content-Type": "application/json" },
                        body: JSON.stringify({ id: id, sasaran: sasaran })
                    });

                    if (!response.ok) throw new Error("HTTP status " + response.status);

                    let data = await response.json();

                    if (data.success) {
                        row.innerHTML = `
                    <td></td>
                    <td colspan="4">
                        <div class="d-flex align-items-center justify-content-between">
                            <span class="sasaran-text">${sasaran}</span>
                            <button type="button" class="btn btn-sm btn-outline-warning editSasaranBtn"><i class="mdi mdi-pencil"></i></button>
                        </div>
                    </td>
                `;
                    } else {
                        throw new Error("Gagal update sasaran di server!");
                    }

                } catch (err) {
                    alert("Terjadi error saat update: " + err.message);
                    console.error(err);
                }
            })();
        }

    });
</script>
