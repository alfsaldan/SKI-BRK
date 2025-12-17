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
                                <li class="breadcrumb-item active">Program PPK</li>
                            </ol>
                        </div>
                        <h4 class="page-title text-primary"><i class="mdi mdi-account-badge-alert-outline mr-1"></i> Program Peningkatan Kinerja (PPK)</h4>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12">
                    <div class="card shadow-sm border-0">
                        <div class="card-body">
                            <h4 class="header-title text-center mb-0 text-uppercase text-primary font-weight-bold">Formulir Program Peningkatan Kinerja</h4>
                            <hr>

                            <form action="<?= base_url('pegawai/simpan_ppk_penilai') ?>" method="post" id="form-ppk">
                                <input type="hidden" name="id_nilai_akhir" value="<?= $nilai_akhir->id ?? '' ?>">
                                <?php
                                    $periode_ppk_string = '';
                                    if (!empty($periode_ppk_response)) {
                                        $periode_ppk_string = $periode_ppk_response;
                                    } elseif (isset($nilai_akhir->periode_awal) && isset($nilai_akhir->periode_akhir)) {
                                        // Format "dd Month YYYY - dd Month YYYY"
                                        $start_date = date('Y-m-d', strtotime($nilai_akhir->periode_akhir . ' +1 day'));
                                        $end_date = date('Y-m-d', strtotime($start_date . ' +6 months -1 day'));
                                        $periode_ppk_string = date('d F Y', strtotime($start_date)) . ' - ' . date('d F Y', strtotime($end_date));
                                    }
                                ?>
                                <!-- Bagian 1: Data Pegawai & Periode -->
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <h5 class="text-info mb-3"><i class="mdi mdi-account-details mr-1"></i> Data Pegawai</h5>
                                        <div class="form-group row mb-2">
                                            <label class="col-sm-4 col-form-label font-weight-medium">Nama</label>
                                            <div class="col-sm-8">
                                                <input type="text" class="form-control-plaintext font-weight-bold" value="<?= isset($pegawai->nama) ? $pegawai->nama : '-' ?>" readonly>
                                            </div>
                                        </div>
                                        <div class="form-group row mb-2">
                                            <label class="col-sm-4 col-form-label font-weight-medium">NIK</label>
                                            <div class="col-sm-8">
                                                <input type="text" class="form-control-plaintext" name="nik" value="<?= isset($pegawai->nik) ? $pegawai->nik : '-' ?>" readonly>
                                            </div>
                                        </div>
                                        <div class="form-group row mb-2">
                                            <label class="col-sm-4 col-form-label font-weight-medium">Jabatan</label>
                                            <div class="col-sm-8">
                                                <input type="text" class="form-control-plaintext" value="<?= isset($pegawai->jabatan) ? $pegawai->jabatan : '-' ?>" readonly>
                                            </div>
                                        </div>
                                        <div class="form-group row mb-2">
                                            <label class="col-sm-4 col-form-label font-weight-medium">Unit Kantor</label>
                                            <div class="col-sm-8">
                                                <input type="text" class="form-control-plaintext" value="<?= isset($pegawai->unit_kantor) ? $pegawai->unit_kantor : '-' ?>" readonly>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-6 border-left">
                                        <h5 class="text-warning mb-3"><i class="mdi mdi-calendar-clock mr-1"></i> Periode Program</h5>
                                        <div class="form-group row mb-2">
                                            <label class="col-sm-4 col-form-label">PPK Tahap ke</label>
                                            <div class="col-sm-8">
                                                <input type="number" class="form-control" name="tahap" placeholder="Masukkan tahap (misal: 1)" value="<?= $ppk->tahap ?? '' ?>" required>
                                            </div>
                                        </div>
                                        <div class="form-group row mb-2">
                                            <label class="col-sm-4 col-form-label">Periode PPK</label>
                                            <div class="col-sm-8">
                                                <input type="text" class="form-control" name="periode_ppk" value="<?= $ppk->periode_ppk ?? $periode_ppk_string ?>" readonly required>
                                            </div>
                                        </div>
                                        <div class="form-group row mb-2">
                                            <label class="col-sm-4 col-form-label">Periode Coaching</label>
                                            <div class="col-sm-8">
                                                <input type="text" class="form-control" name="periode_coaching" placeholder="Contoh: ke-4" value="<?= $ppk->periode_coaching ?? '' ?>" required>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <hr class="my-0">

                                <!-- Bagian 2: Review Kinerja Sebelumnya -->
                                <h5 class="text-primary mb-3"><i class="mdi mdi-history mr-1"></i> Review Kinerja Sebelumnya</h5>
                                <div class="form-group">
                                    <label class="font-weight-bold">Hasil Evaluasi Kinerja Sebelumnya</label>
                                    <textarea class="form-control" name="review_sebelum" rows="3" placeholder="Deskripsikan hasil evaluasi kinerja sebelumnya..."><?= $ppk->review_sebelum ?? '' ?></textarea>
                                </div>
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="font-weight-bold">Target</label>
                                            <input type="text" class="form-control" name="target" placeholder="Target yang ditetapkan..." value="<?= $ppk->target ?? '' ?>">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="font-weight-bold">Pencapaian</label>
                                            <input type="text" class="form-control" name="pencapaian" placeholder="Pencapaian yang diraih..." value="<?= $ppk->pencapaian ?? '' ?>">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="font-weight-bold">Aktivitas</label>
                                            <input type="text" class="form-control" name="aktivitas" placeholder="Aktivitas yang dilakukan..." value="<?= $ppk->aktivitas ?? '' ?>">
                                        </div>
                                    </div>
                                </div>

                                <hr class="my-0">

                                <!-- Bagian 3: Rencana Perbaikan -->
                                <h5 class="text-primary mb-3"><i class="mdi mdi-bullseye-arrow mr-1"></i> Rencana dan Aktivitas Perbaikan</h5>
                                <div class="form-group">
                                    <label class="font-weight-bold">Rencana Tindakan untuk Mencapai Sasaran</label>
                                    <textarea class="form-control" name="rencana" rows="3" placeholder="Jelaskan rencana tindakan perbaikan..."><?= $ppk->rencana ?? '' ?></textarea>
                                </div>

                                <!-- Bagian 4: Sasaran & Tindakan (Dynamic Table) -->
                                <div class="table-responsive mt-3">
                                    <table class="table table-bordered table-striped" id="table-sasaran">
                                        <thead class="thead-light text-center">
                                            <tr>
                                                <th style="width: 50px;">No</th>
                                                <th>Sasaran Bulan Ini</th>
                                                <th>Rincian Tindakan</th>
                                                <th style="width: 100px;">Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody id="sasaran-body">
                                            <?php if (!empty($sasaran)): ?>
                                                <?php foreach ($sasaran as $index => $s): ?>
                                                    <tr>
                                                        <td class="text-center row-number"><?= $index + 1 ?></td>
                                                        <td><input type="text" class="form-control" name="sasaran_bulan[]" placeholder="Masukkan sasaran..." value="<?= htmlspecialchars($s['sasaran_bulan'] ?? '') ?>" required></td>
                                                        <td><textarea class="form-control" name="rincian_tindakan[]" placeholder="Masukkan rincian tindakan..." rows="2" required><?= htmlspecialchars($s['rincian_tindakan'] ?? '') ?></textarea></td>
                                                        <td class="text-center">
                                                            <button type="button" class="btn btn-danger btn-sm btn-remove" <?= $index == 0 ? 'disabled' : '' ?>><i class="mdi mdi-trash-can"></i></button>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            <?php else: ?>
                                                <tr>
                                                    <td class="text-center row-number">1</td>
                                                    <td><input type="text" class="form-control" name="sasaran_bulan[]" placeholder="Masukkan sasaran..." required></td>
                                                    <td><textarea class="form-control" name="rincian_tindakan[]" placeholder="Masukkan rincian tindakan..." rows="2" required></textarea></td>
                                                    <td class="text-center">
                                                        <button type="button" class="btn btn-danger btn-sm btn-remove" disabled><i class="mdi mdi-trash-can"></i></button>
                                                    </td>
                                                </tr>
                                            <?php endif; ?>
                                        </tbody>
                                    </table>
                                    <div class="text-right">
                                        <button type="button" class="btn btn-info btn-sm" id="btn-add-row">
                                            <i class="mdi mdi-plus"></i> Tambah Sasaran
                                        </button>
                                    </div>
                                </div>

                                <hr class="my-0">

                                <!-- Bagian Tambahan: Catatan Divisi MSDI -->
                                <?php if (!empty($ppk) && !empty($ppk->catatan_msdi)): ?>
                                    <h5 class="text-primary mb-3 mt-3"><i class="mdi mdi-comment-text-outline mr-1"></i> Catatan Divisi MSDI</h5>
                                    <div class="alert alert-info" role="alert">
                                        <?= nl2br(htmlspecialchars($ppk->catatan_msdi)) ?>
                                    </div>
                                    <hr class="my-0">
                                <?php endif; ?>

                                <!-- Bagian 5: Status Verifikasi -->
                                <h5 class="text-success mb-3"><i class="mdi mdi-check-decagram mr-1"></i> Status Verifikasi</h5>
                                <div class="row text-center">
                                    <div class="col-md-3 mb-3">
                                        <div class="card border-secondary border h-100">
                                            <div class="card-body p-3 text-center d-flex flex-column">
                                                <h6 class="card-title font-weight-bold mb-3">Pegawai</h6>
                                                <div class="flex-grow-1 d-flex justify-content-center align-items-center">
                                                    <?php
                                                    $st_pegawai = isset($ppk->status_pegawai) ? $ppk->status_pegawai : 'Belum Disetujui';
                                                    $cls_pegawai = ($st_pegawai == 'Disetujui') ? 'badge-success' : (($st_pegawai == 'Ditolak') ? 'badge-danger' : 'badge-secondary');
                                                    ?>
                                                    <span class="badge <?= $cls_pegawai ?> p-2" style="font-size: 14px;"><?= $st_pegawai ?></span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <div class="card border-secondary border h-100">
                                            <div class="card-body p-3 text-center d-flex flex-column">
                                                <h6 class="card-title font-weight-bold mb-3">Penilai I</h6>
                                                <div class="flex-grow-1 d-flex justify-content-center align-items-center">
                                                    <div>
                                                        <!-- Hidden Checkbox (untuk form submit) -->
                                                        <input type="checkbox" id="status_penilai1" name="status_penilai1" value="Disetujui" style="display:none;" <?= (isset($ppk->status_penilai1) && $ppk->status_penilai1 == 'Disetujui') ? 'checked' : '' ?>>

                                                        <!-- Area Tanda Tangan Interaktif -->
                                                        <div id="btn-signature" class="d-flex flex-column align-items-center justify-content-center p-2" style="border: 2px dashed #ccc; border-radius: 8px; cursor: pointer; min-height: 80px; transition: all 0.3s;">
                                                            <div class="unsigned-content <?= (isset($ppk->status_penilai1) && $ppk->status_penilai1 == 'Disetujui') ? 'd-none' : '' ?>">
                                                                <i class="mdi mdi-draw text-primary" style="font-size: 2rem;"></i>
                                                                <div class="small text-muted mt-1">Klik untuk Tanda Tangan</div>
                                                            </div>
                                                            <div class="signed-content <?= (isset($ppk->status_penilai1) && $ppk->status_penilai1 == 'Disetujui') ? '' : 'd-none' ?>">
                                                                <div class="text-success" style="font-family: 'Brush Script MT', cursive; font-size: 1.4rem; line-height: 1.2;">
                                                                    <?= isset($penilai->nama) ? $penilai->nama : 'Penilai I' ?>
                                                                </div>
                                                                <div class="small text-muted mt-1" style="font-size: 0.65rem;">Digitally Signed</div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <div class="card border-secondary border h-100">
                                            <div class="card-body p-3 text-center d-flex flex-column">
                                                <h6 class="card-title font-weight-bold mb-3">Divisi MSDI</h6>
                                                <div class="flex-grow-1 d-flex justify-content-center align-items-center">
                                                    <?php
                                                    $st_msdi = isset($ppk->status_msdi) ? $ppk->status_msdi : 'Belum Disetujui';
                                                    $cls_msdi = ($st_msdi == 'Disetujui') ? 'badge-success' : (($st_msdi == 'Ditolak') ? 'badge-danger' : 'badge-secondary');
                                                    ?>
                                                    <span class="badge <?= $cls_msdi ?> p-2" style="font-size: 14px;"><?= $st_msdi ?></span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <div class="card border-secondary border h-100">
                                            <div class="card-body p-3 text-center d-flex flex-column">
                                                <h6 class="card-title font-weight-bold mb-3">Pimpinan Unit Kerja</h6>
                                                <div class="flex-grow-1 d-flex justify-content-center align-items-center">
                                                    <?php
                                                    $st_pimpinan = isset($ppk->status_pimpinanunit) ? $ppk->status_pimpinanunit : 'Belum Disetujui';
                                                    $cls_pimpinan = ($st_pimpinan == 'Disetujui') ? 'badge-success' : (($st_pimpinan == 'Ditolak') ? 'badge-danger' : 'badge-secondary');
                                                    ?>
                                                    <span class="badge <?= $cls_pimpinan ?> p-2" style="font-size: 14px;"><?= $st_pimpinan ?></span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Tombol Simpan -->
                                <div class="row mt-4">
                                    <div class="col-12 text-right">
                                        <a href="<?= base_url('pegawai/ppk_penilai') ?>" class="btn btn-secondary px-4"><i class="mdi mdi-arrow-left mr-1"></i> Kembali</a>
                                        <button type="submit" class="btn btn-primary px-4 ml-1"><i class="mdi mdi-content-save mr-1"></i> Simpan Formulir</button>
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

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        <?php if ($this->session->flashdata('success')): ?>
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: '<?= $this->session->flashdata('success'); ?>',
                timer: 2500,
                showConfirmButton: false
            });
        <?php elseif ($this->session->flashdata('error')): ?>
            Swal.fire({
                icon: 'error',
                title: 'Gagal!',
                text: '<?= $this->session->flashdata('error'); ?>',
            });
        <?php endif; ?>
    });
</script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Fungsi Tambah Baris
        document.getElementById('btn-add-row').addEventListener('click', function() {
            var tbody = document.getElementById('sasaran-body');
            var rowCount = tbody.rows.length + 1;
            var newRow = `
                <tr>
                    <td class="text-center row-number">${rowCount}</td>
                    <td><input type="text" class="form-control" name="sasaran_bulan[]" placeholder="Masukkan sasaran..." required></td>
                    <td><textarea class="form-control" name="rincian_tindakan[]" placeholder="Masukkan rincian tindakan..." rows="2" required></textarea></td>
                    <td class="text-center">
                        <button type="button" class="btn btn-danger btn-sm btn-remove"><i class="mdi mdi-trash-can"></i></button>
                    </td>
                </tr>
            `;
            tbody.insertAdjacentHTML('beforeend', newRow);
        });

        // Fungsi Hapus Baris (Delegated Event)
        document.getElementById('sasaran-body').addEventListener('click', function(e) {
            if (e.target.closest('.btn-remove')) {
                var row = e.target.closest('tr');
                // Jangan hapus jika hanya tersisa 1 baris
                if (document.querySelectorAll('#sasaran-body tr').length > 1) {
                    row.remove();
                    updateRowNumbers();
                }
            }
        });

        // Update Nomor Urut
        function updateRowNumbers() {
            var rows = document.querySelectorAll('#sasaran-body tr');
            rows.forEach(function(row, index) {
                row.querySelector('.row-number').textContent = index + 1;
                // Disable tombol hapus jika hanya 1 baris tersisa
                var btnRemove = row.querySelector('.btn-remove');
                if (rows.length === 1) {
                    btnRemove.disabled = true;
                } else {
                    btnRemove.disabled = false;
                }
            });
        }

        // Logic Tanda Tangan Digital
        const btnSig = document.getElementById('btn-signature');
        const chkSig = document.getElementById('status_penilai1');

        if (btnSig && chkSig) {
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
                    btnSig.style.borderColor = '#28a745'; // Hijau saat ditandatangani
                    btnSig.style.backgroundColor = '#f0fff4';
                } else {
                    signed.classList.add('d-none');
                    unsigned.classList.remove('d-none');
                    btnSig.style.borderColor = '#ccc'; // Abu-abu saat belum
                    btnSig.style.backgroundColor = '#fff';
                }
            }

            // Init state style saat load
            updateSignatureUI();
        }
    });
</script>