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
                                <li class="breadcrumb-item"><a href="<?= base_url('pegawai/ppk_penilai') ?>">PPK</a></li>
                                <li class="breadcrumb-item active">Evaluasi PPK</li>
                            </ol>
                        </div>
                        <h4 class="page-title text-primary"><i class="mdi mdi-clipboard-check-outline mr-1"></i> Evaluasi Program Peningkatan Kinerja (PPK)</h4>
                    </div>
                </div>
            </div>

            <!-- Alert Messages -->
            <?php if ($this->session->flashdata('success')): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <?= $this->session->flashdata('success'); ?>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            <?php endif; ?>
            <?php if ($this->session->flashdata('error')): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?= $this->session->flashdata('error'); ?>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            <?php endif; ?>

            <div class="row">
                <div class="col-12">
                    <div class="card shadow-sm border-0">
                        <div class="card-body">
                            <h4 class="header-title mb-4 text-primary text-center font-weight-bold">Formulir Evaluasi PPK</h4>

                            <form action="<?= base_url('pegawai/simpan_ppk_evaluasi') ?>" method="post">
                                <input type="hidden" name="id_ppk" value="<?= $ppk->id ?>">
                                <input type="hidden" name="nik" value="<?= $ppk->nik ?>">
                                <input type="hidden" name="role_actor" value="<?= $is_penilai ? 'penilai' : 'pegawai' ?>">

                                <!-- 1. Evaluasi Pelaksanaan PPK -->
                                <div class="form-group mb-4">
                                    <h5 class="text-primary font-weight-bold mb-3">
                                        <i class="mdi mdi-clipboard-check-outline mr-1"></i>
                                        Evaluasi Pelaksanaan PPK
                                    </h5>
                                    <p class="text-muted mb-2">Berisi hasil pencapaian sasaran/ sasaran baru yang telah disepakati</p>
                                    <textarea class="form-control" rows="5" name="evaluasi_pelaksanaan" placeholder="Deskripsikan hasil pencapaian sasaran..."><?= isset($evaluasi->evaluasi_pelaksanaan) ? $evaluasi->evaluasi_pelaksanaan : '' ?></textarea>
                                </div>

                                <!-- 2. Tabel Sasaran Bulan ini | Hasil -->
                                <div class="form-group mb-4">
                                    <h5 class="text-primary font-weight-bold mb-3"><i class="mdi mdi-chart-line mr-1"></i>Hasil Pencapaian Sasaran</h5>
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-hover" id="table-hasil">
                                            <thead class="thead-light text-center">
                                                <tr>
                                                    <th style="width: 50px;">No</th>
                                                    <th>Sasaran Bulan ini</th>
                                                    <th>Hasil</th>
                                                    <th style="width: 100px;">Aksi</th>
                                                </tr>
                                            </thead>
                                            <tbody id="hasil-body">
                                                <?php if (!empty($detail_evaluasi)): ?>
                                                    <?php foreach ($detail_evaluasi as $i => $row): ?>
                                                        <tr>
                                                            <td class="text-center row-number"><?= $i + 1 ?></td>
                                                            <td><textarea class="form-control" name="sasaran_hasil[]" rows="2" placeholder="Tuliskan sasaran bulan ini..." required><?= $row['sasaran'] ?? '' ?></textarea></td>
                                                            <td><textarea class="form-control" name="hasil_pencapaian[]" rows="2" placeholder="Tuliskan hasil pencapaian..." required><?= $row['response'] ?? $row['hasil'] ?? '' ?></textarea></td>
                                                            <td class="text-center">
                                                                <button type="button" class="btn btn-danger btn-sm btn-remove" <?= count($detail_evaluasi) == 1 ? 'disabled' : '' ?>><i class="mdi mdi-trash-can"></i></button>
                                                            </td>
                                                        </tr>
                                                    <?php endforeach; ?>
                                                <?php else: ?>
                                                    <tr>
                                                        <td class="text-center row-number">1</td>
                                                        <td><textarea class="form-control" name="sasaran_hasil[]" rows="2" placeholder="Tuliskan sasaran bulan ini..." required></textarea></td>
                                                        <td><textarea class="form-control" name="hasil_pencapaian[]" rows="2" placeholder="Tuliskan hasil pencapaian..." required></textarea></td>
                                                        <td class="text-center">
                                                            <button type="button" class="btn btn-danger btn-sm btn-remove" disabled><i class="mdi mdi-trash-can"></i></button>
                                                        </td>
                                                    </tr>
                                                <?php endif; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="text-right">
                                        <button type="button" class="btn btn-info btn-sm" id="btn-add-hasil">
                                            <i class="mdi mdi-plus"></i> Tambah Baris
                                        </button>
                                    </div>
                                </div>

                                <!-- 3. Komitmen Lanjutan -->
                                <div class="form-group mb-4">
                                    <h5 class="text-primary font-weight-bold mb-3"><i class="mdi mdi-account-check-outline mr-1"></i>
                                        </i>Komitmen Lanjutan</h5>
                                    <p class="text-muted mb-2">Target kerja selanjutnya serta aktivitas atau perilaku yang perlu diperbaiki</p>
                                    <textarea class="form-control" rows="5" name="komitmen_lanjutan" placeholder="Tuliskan komitmen lanjutan..."><?= isset($evaluasi->komitmen_lanjutan) ? $evaluasi->komitmen_lanjutan : '' ?></textarea>
                                </div>

                                <!-- 4. Rincian tindakan -->
                                <div class="form-group mb-4">
                                    <h5 class="text-primary font-weight-bold mb-3"><i class="mdi mdi-format-list-checks mr-1"></i>Rincian Tindakan</h5>
                                    <p class="text-muted mb-2">Rincian tindakan masih sama dengan target bulan ini, yaitu:</p>
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-hover" id="table-rincian">
                                            <thead class="thead-light text-center">
                                                <tr>
                                                    <th style="width: 50px;">No</th>
                                                    <th>Sasaran Bulan ini</th>
                                                    <th>Rincian Tindakan</th>
                                                    <th style="width: 100px;">Aksi</th>
                                                </tr>
                                            </thead>
                                            <tbody id="rincian-body">
                                                <?php if (!empty($detail_tindakan)): ?>
                                                    <?php foreach ($detail_tindakan as $i => $row): ?>
                                                        <tr>
                                                            <td class="text-center row-number"><?= $i + 1 ?></td>
                                                            <td><textarea class="form-control" name="sasaran_rincian[]" rows="2" placeholder="Tuliskan sasaran bulan ini..." required><?= $row['sasaran'] ?? '' ?></textarea></td>
                                                            <td><textarea class="form-control" name="rincian_tindakan_eval[]" rows="2" placeholder="Tuliskan rincian tindakan..." required><?= $row['response'] ?? $row['rincian'] ?? '' ?></textarea></td>
                                                            <td class="text-center">
                                                                <button type="button" class="btn btn-danger btn-sm btn-remove" <?= count($detail_tindakan) == 1 ? 'disabled' : '' ?>><i class="mdi mdi-trash-can"></i></button>
                                                            </td>
                                                        </tr>
                                                    <?php endforeach; ?>
                                                <?php else: ?>
                                                    <tr>
                                                        <td class="text-center row-number">1</td>
                                                        <td><textarea class="form-control" name="sasaran_rincian[]" rows="2" placeholder="Tuliskan sasaran bulan ini..." required></textarea></td>
                                                        <td><textarea class="form-control" name="rincian_tindakan_eval[]" rows="2" placeholder="Tuliskan rincian tindakan..." required></textarea></td>
                                                        <td class="text-center">
                                                            <button type="button" class="btn btn-danger btn-sm btn-remove" disabled><i class="mdi mdi-trash-can"></i></button>
                                                        </td>
                                                    </tr>
                                                <?php endif; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="text-right">
                                        <button type="button" class="btn btn-info btn-sm" id="btn-add-rincian">
                                            <i class="mdi mdi-plus"></i> Tambah Baris
                                        </button>
                                    </div>
                                </div>

                                <!-- 5. Kesimpulan Hasil PPK -->
                                <div class="form-group mb-4">
                                    <h5 class="text-primary font-weight-bold mb-3"><i class="mdi mdi-file-check-outline mr-1"></i>
                                        </i>Kesimpulan Hasil PPK</h5>
                                    <div class="pl-3">
                                        <div class="custom-control custom-radio mb-2">
                                            <input type="radio" id="kesimpulan1" name="kesimpulan" class="custom-control-input" value="Berhasil" <?= (isset($evaluasi->kesimpulan) && $evaluasi->kesimpulan == 'Berhasil') ? 'checked' : '' ?>>
                                            <label class="custom-control-label font-weight-bold text-success" for="kesimpulan1">Berhasil</label>
                                        </div>
                                        <div class="custom-control custom-radio">
                                            <input type="radio" id="kesimpulan2" name="kesimpulan" class="custom-control-input" value="Belum Berhasil" <?= (isset($evaluasi->kesimpulan) && $evaluasi->kesimpulan == 'Belum Berhasil') ? 'checked' : '' ?>>
                                            <label class="custom-control-label font-weight-bold text-danger" for="kesimpulan2">Belum Berhasil, dilanjutkan dengan PPK Tahap selanjutnya</label>
                                        </div>
                                    </div>
                                </div>

                                <!-- 6. Persetujuan -->
                                <div class="form-group mb-4">
                                    <h5 class="text-success font-weight-bold mb-3"><i class="mdi mdi-check-decagram mr-1"></i>Persetujuan</h5>
                                    <div class="row text-center">
                                        <!-- Pegawai -->
                                        <div class="col-md-3 mb-3">
                                            <div class="card border-secondary border h-100">
                                                <div class="card-body p-3 d-flex flex-column">
                                                    <h6 class="card-title font-weight-bold mb-3">Disetujui bersama oleh<br><small>Pegawai</small></h6>
                                                    <div class="flex-grow-1 d-flex justify-content-center align-items-center">
                                                        <?php if (!$is_penilai): // Jika User adalah Pegawai 
                                                        ?>
                                                            <?php
                                                            $is_signed = (isset($evaluasi->status_pegawai) && $evaluasi->status_pegawai == 'Disetujui');
                                                            $sig_style = $is_signed ? 'border-color: #28a745; background-color: #f0fff4;' : 'border: 2px dashed #ccc;';
                                                            ?>
                                                            <div>
                                                                <input type="checkbox" id="status_pegawai_eval" name="status_pegawai_eval" value="Disetujui" style="display:none;" <?= $is_signed ? 'checked' : '' ?>>
                                                                <div id="sig-pegawai" class="signature-box d-flex flex-column align-items-center justify-content-center p-2" style="<?= $sig_style ?> border-radius: 8px; cursor: pointer; min-height: 80px; transition: all 0.3s;">
                                                                    <div class="unsigned-content <?= $is_signed ? 'd-none' : '' ?>">
                                                                        <i class="mdi mdi-draw text-primary" style="font-size: 2rem;"></i>
                                                                        <div class="small text-muted mt-1">Klik untuk Tanda Tangan</div>
                                                                    </div>
                                                                    <div class="signed-content <?= $is_signed ? '' : 'd-none' ?>">
                                                                        <div class="text-success signer-name" style="font-family: 'Brush Script MT', cursive; font-size: 1.4rem; line-height: 1.2;">
                                                                            <?= isset($current_user->nama) ? htmlspecialchars($current_user->nama) : '(Nama Pegawai)' ?>
                                                                        </div>
                                                                        <div class="small text-muted mt-1" style="font-size: 0.65rem;">Digitally Signed</div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        <?php else: // Jika User adalah Penilai (View Only untuk kolom Pegawai) 
                                                        ?>
                                                            <?php if (isset($evaluasi->status_pegawai) && $evaluasi->status_pegawai == 'Disetujui'): ?>
                                                                <span class="badge badge-success p-2" style="font-size: 14px;">Disetujui</span>
                                                            <?php else: ?>
                                                                <span class="badge badge-secondary p-2" style="font-size: 14px;">Belum Disetujui</span>
                                                            <?php endif; ?>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- Atasan Pegawai -->
                                        <div class="col-md-3 mb-3">
                                            <div class="card border-secondary border h-100">
                                                <div class="card-body p-3 d-flex flex-column">
                                                    <h6 class="card-title font-weight-bold mb-3">Disetujui bersama oleh<br><small>Atasan Pegawai</small></h6>
                                                    <div class="flex-grow-1 d-flex justify-content-center align-items-center">
                                                        <?php if ($is_penilai): // Jika User adalah Penilai 
                                                        ?>
                                                            <?php
                                                            $is_signed_penilai = (isset($evaluasi->status_penilai1) && $evaluasi->status_penilai1 == 'Disetujui');
                                                            $sig_style_penilai = $is_signed_penilai ? 'border-color: #28a745; background-color: #f0fff4;' : 'border: 2px dashed #ccc;';
                                                            ?>
                                                            <div>
                                                                <input type="checkbox" id="status_penilai1_eval" name="status_penilai1_eval" value="Disetujui" style="display:none;" <?= $is_signed_penilai ? 'checked' : '' ?>>
                                                                <div id="sig-penilai" class="signature-box d-flex flex-column align-items-center justify-content-center p-2" style="<?= $sig_style_penilai ?> border-radius: 8px; cursor: pointer; min-height: 80px; transition: all 0.3s;">
                                                                    <div class="unsigned-content <?= $is_signed_penilai ? 'd-none' : '' ?>">
                                                                        <i class="mdi mdi-draw text-primary" style="font-size: 2rem;"></i>
                                                                        <div class="small text-muted mt-1">Klik untuk Tanda Tangan</div>
                                                                    </div>
                                                                    <div class="signed-content <?= $is_signed_penilai ? '' : 'd-none' ?>">
                                                                        <div class="text-success signer-name" style="font-family: 'Brush Script MT', cursive; font-size: 1.4rem; line-height: 1.2;">
                                                                            <?= isset($current_user->nama) ? htmlspecialchars($current_user->nama) : '(Nama Penilai)' ?>
                                                                        </div>
                                                                        <div class="small text-muted mt-1" style="font-size: 0.65rem;">Digitally Signed</div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        <?php else: // Jika User adalah Pegawai (View Only untuk kolom Penilai) 
                                                        ?>
                                                            <?php if (isset($evaluasi->status_penilai1) && $evaluasi->status_penilai1 == 'Disetujui'): ?>
                                                                <span class="badge badge-success p-2" style="font-size: 14px;">Disetujui</span>
                                                            <?php else: ?>
                                                                <span class="badge badge-secondary p-2" style="font-size: 14px;">Belum Disetujui</span>
                                                            <?php endif; ?>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- Divisi SDI -->
                                        <div class="col-md-3 mb-3">
                                            <div class="card border-secondary border h-100">
                                                <div class="card-body p-3 d-flex flex-column">
                                                    <h6 class="card-title font-weight-bold mb-3">Diverifikasi oleh<br><small>Divisi SDI</small></h6>
                                                    <div class="flex-grow-1 d-flex justify-content-center align-items-center">
                                                        <?php if (isset($evaluasi->status_msdi) && $evaluasi->status_msdi == 'Disetujui'): ?>
                                                            <span class="badge badge-success p-2" style="font-size: 14px;">Diverifikasi</span>
                                                        <?php else: ?>
                                                            <span class="badge badge-secondary p-2" style="font-size: 14px;">Belum Diverifikasi</span>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- Pimpinan Unit Kerja -->
                                        <div class="col-md-3 mb-3">
                                            <div class="card border-secondary border h-100">
                                                <div class="card-body p-3 d-flex flex-column">
                                                    <h6 class="card-title font-weight-bold mb-3">Diketahui oleh<br><small>Pimpinan Unit Kerja</small></h6>
                                                    <div class="flex-grow-1 d-flex justify-content-center align-items-center">
                                                        <?php if (isset($evaluasi->status_pimpinanunit) && $evaluasi->status_pimpinanunit == 'Disetujui'): ?>
                                                            <span class="badge badge-success p-2" style="font-size: 14px;">Diketahui</span>
                                                        <?php else: ?>
                                                            <span class="badge badge-secondary p-2" style="font-size: 14px;">Belum Diketahui</span>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Tombol Aksi -->
                                <div class="row mt-4">
                                    <div class="col-12 text-right">
                                        <a href="<?= base_url('pegawai/ppk_penilai') ?>" class="btn btn-secondary mr-2"><i class="mdi mdi-arrow-left"></i> Kembali</a>
                                        <button type="submit" class="btn btn-primary"><i class="mdi mdi-content-save"></i> Simpan Evaluasi</button>
                                    </div>
                                </div>

                            </form>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {

        // --- Fungsi Generic untuk Tabel Dinamis ---
        function setupDynamicTable(addButtonId, tableBodyId, rowTemplate) {
            const addButton = document.getElementById(addButtonId);
            const tableBody = document.getElementById(tableBodyId);

            if (!addButton || !tableBody) return;

            // Fungsi Tambah Baris
            addButton.addEventListener('click', function() {
                const rowCount = tableBody.rows.length + 1;
                const newRowHtml = rowTemplate.replace(/\${rowCount}/g, rowCount);
                tableBody.insertAdjacentHTML('beforeend', newRowHtml);
                updateRowNumbers(tableBodyId);
            });

            // Fungsi Hapus Baris (Delegated Event)
            tableBody.addEventListener('click', function(e) {
                if (e.target.closest('.btn-remove')) {
                    const row = e.target.closest('tr');
                    if (tableBody.querySelectorAll('tr').length > 1) {
                        row.remove();
                        updateRowNumbers(tableBodyId);
                    }
                }
            });
        }

        // Fungsi Update Nomor Urut
        function updateRowNumbers(tableBodyId) {
            const tableBody = document.getElementById(tableBodyId);
            if (!tableBody) return;

            const rows = tableBody.querySelectorAll('tr');
            rows.forEach(function(row, index) {
                const rowNumberCell = row.querySelector('.row-number');
                if (rowNumberCell) {
                    rowNumberCell.textContent = index + 1;
                }

                const btnRemove = row.querySelector('.btn-remove');
                if (btnRemove) {
                    btnRemove.disabled = (rows.length === 1);
                }
            });
        }

        // --- Inisialisasi untuk Tabel 2: Hasil Pencapaian ---
        const hasilRowTemplate = `<tr><td class="text-center row-number">\${rowCount}</td><td><textarea class="form-control" name="sasaran_hasil[]" rows="2" placeholder="Tuliskan sasaran bulan ini..." required></textarea></td><td><textarea class="form-control" name="hasil_pencapaian[]" rows="2" placeholder="Tuliskan hasil pencapaian..." required></textarea></td><td class="text-center"><button type="button" class="btn btn-danger btn-sm btn-remove"><i class="mdi mdi-trash-can"></i></button></td></tr>`;
        setupDynamicTable('btn-add-hasil', 'hasil-body', hasilRowTemplate);

        // --- Inisialisasi untuk Tabel 4: Rincian Tindakan ---
        const rincianRowTemplate = `<tr><td class="text-center row-number">\${rowCount}</td><td><textarea class="form-control" name="sasaran_rincian[]" rows="2" placeholder="Tuliskan sasaran bulan ini..." required></textarea></td><td><textarea class="form-control" name="rincian_tindakan_eval[]" rows="2" placeholder="Tuliskan rincian tindakan..." required></textarea></td><td class="text-center"><button type="button" class="btn btn-danger btn-sm btn-remove"><i class="mdi mdi-trash-can"></i></button></td></tr>`;
        setupDynamicTable('btn-add-rincian', 'rincian-body', rincianRowTemplate);

        // --- Logic Tanda Tangan Digital ---
        function setupSignature(buttonId, checkboxId, signerName) {
            const btnSig = document.getElementById(buttonId);
            const chkSig = document.getElementById(checkboxId);

            if (!btnSig || !chkSig) return;

            const nameHolder = btnSig.querySelector('.signer-name');
            if (nameHolder && signerName) {
                nameHolder.textContent = signerName;
            }

            btnSig.addEventListener('click', function() {
                chkSig.checked = !chkSig.checked;
                updateSignatureUI();
            });

            function updateSignatureUI() {
                const unsigned = btnSig.querySelector('.unsigned-content');
                const signed = btnSig.querySelector('.signed-content');

                if (chkSig.checked) {
                    unsigned.classList.add('d-none');
                    signed.classList.remove('d-none');
                    btnSig.style.borderColor = '#28a745';
                    btnSig.style.backgroundColor = '#f0fff4';
                } else {
                    signed.classList.add('d-none');
                    unsigned.classList.remove('d-none');
                    btnSig.style.borderColor = '#ccc';
                    btnSig.style.backgroundColor = '#fff';
                }
            }
            updateSignatureUI(); // Initial state
        }

        // Inisialisasi tanda tangan untuk Pegawai
        <?php if (!$is_penilai): ?>
            const employeeName = "<?= isset($current_user->nama) ? htmlspecialchars($current_user->nama, ENT_QUOTES, 'UTF-8') : '(Nama Pegawai)' ?>";
            setupSignature('sig-pegawai', 'status_pegawai_eval', employeeName);
        <?php else: ?>
            const penilaiName = "<?= isset($current_user->nama) ? htmlspecialchars($current_user->nama, ENT_QUOTES, 'UTF-8') : '(Nama Penilai)' ?>";
            setupSignature('sig-penilai', 'status_penilai1_eval', penilaiName);
        <?php endif; ?>
    });
</script>