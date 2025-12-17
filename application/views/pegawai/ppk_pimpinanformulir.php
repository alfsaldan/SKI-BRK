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

                                <form action="<?= base_url('pegawai/simpan_ppk_pimpinan') ?>" method="post" id="form-ppk">
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
                                                    <p class="form-control-plaintext font-weight-bold mb-0"><?= isset($pegawai->nama) ? $pegawai->nama : '-' ?></p>
                                                </div>
                                            </div>
                                            <div class="form-group row mb-2">
                                                <label class="col-sm-4 col-form-label font-weight-medium">NIK</label>
                                                <div class="col-sm-8">
                                                    <p class="form-control-plaintext mb-0"><?= isset($pegawai->nik) ? $pegawai->nik : '-' ?></p>
                                                    <input type="hidden" name="nik" value="<?= isset($pegawai->nik) ? $pegawai->nik : '-' ?>">
                                                </div>
                                            </div>
                                            <div class="form-group row mb-2">
                                                <label class="col-sm-4 col-form-label font-weight-medium">Jabatan</label>
                                                <div class="col-sm-8">
                                                    <p class="form-control-plaintext mb-0"><?= isset($pegawai->jabatan) ? $pegawai->jabatan : '-' ?></p>
                                                </div>
                                            </div>
                                            <div class="form-group row mb-2">
                                                <label class="col-sm-4 col-form-label font-weight-medium">Unit Kantor</label>
                                                <div class="col-sm-8">
                                                    <p class="form-control-plaintext mb-0"><?= isset($pegawai->unit_kantor) ? $pegawai->unit_kantor : '-' ?></p>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-6 border-left">
                                            <h5 class="text-warning mb-3"><i class="mdi mdi-calendar-clock mr-1"></i> Periode Program</h5>
                                            <div class="form-group row mb-2">
                                                <label class="col-sm-4 col-form-label">PPK Tahap ke</label>
                                                <div class="col-sm-8">
                                                    <p class="form-control-plaintext mb-0"><?= $ppk->tahap ?? '' ?></p>
                                                </div>
                                            </div>
                                            <div class="form-group row mb-2">
                                                <label class="col-sm-4 col-form-label">Periode PPK</label>
                                                <div class="col-sm-8">
                                                    <p class="form-control-plaintext mb-0"><?= $ppk->periode_ppk ?? $periode_ppk_string ?></p>
                                                    <input type="hidden" name="periode_ppk" value="<?= $ppk->periode_ppk ?? $periode_ppk_string ?>">
                                                </div>
                                            </div>
                                            <div class="form-group row mb-2">
                                                <label class="col-sm-4 col-form-label">Periode Coaching</label>
                                                <div class="col-sm-8">
                                                    <p class="form-control-plaintext mb-0"><?= $ppk->periode_coaching ?? '' ?></p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <hr class="my-0">

                                    <!-- Bagian 2: Review Kinerja Sebelumnya -->
                                    <h5 class="text-primary mb-3"><i class="mdi mdi-history mr-1"></i> Review Kinerja Sebelumnya</h5>
                                    <div class="form-group">
                                        <label class="font-weight-bold">Hasil Evaluasi Kinerja Sebelumnya</label>
                                        <p class="form-control-plaintext border rounded p-2 bg-light mb-0"><?= nl2br(htmlspecialchars($ppk->review_sebelum ?? '')) ?></p>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label class="font-weight-bold">Target</label>
                                                <p class="form-control-plaintext border rounded p-2 bg-light mb-0"><?= htmlspecialchars($ppk->target ?? '') ?></p>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label class="font-weight-bold">Pencapaian</label>
                                                <p class="form-control-plaintext border rounded p-2 bg-light mb-0"><?= htmlspecialchars($ppk->pencapaian ?? '') ?></p>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label class="font-weight-bold">Aktivitas</label>
                                                <p class="form-control-plaintext border rounded p-2 bg-light mb-0"><?= htmlspecialchars($ppk->aktivitas ?? '') ?></p>
                                            </div>
                                        </div>
                                    </div>

                                    <hr class="my-0">

                                    <!-- Bagian 3: Rencana Perbaikan -->
                                    <h5 class="text-primary mb-3"><i class="mdi mdi-bullseye-arrow mr-1"></i> Rencana dan Aktivitas Perbaikan</h5>
                                    <div class="form-group">
                                        <label class="font-weight-bold">Rencana Tindakan untuk Mencapai Sasaran</label>
                                        <p class="form-control-plaintext border rounded p-2 bg-light mb-0"><?= nl2br(htmlspecialchars($ppk->rencana ?? '')) ?></p>
                                    </div>

                                    <!-- Bagian 4: Sasaran & Tindakan (Dynamic Table) -->
                                    <div class="table-responsive mt-3">
                                        <table class="table table-bordered table-striped" id="table-sasaran">
                                            <thead class="thead-light text-center">
                                                <tr>
                                                    <th style="width: 50px;">No</th>
                                                    <th>Sasaran Bulan Ini</th>
                                                    <th>Rincian Tindakan</th>
                                                </tr>
                                            </thead>
                                            <tbody id="sasaran-body">
                                                <?php if (!empty($sasaran)): ?>
                                                    <?php foreach ($sasaran as $index => $s): ?>
                                                        <tr>
                                                            <td class="text-center row-number"><?= $index + 1 ?></td>
                                                         <td class="align-middle"><?= htmlspecialchars($s['sasaran_bulan'] ?? '') ?></td>
                                                         <td class="align-middle"><?= nl2br(htmlspecialchars($s['rincian_tindakan'] ?? '')) ?></td>
                                                        </tr>
                                                    <?php endforeach; ?>
                                                <?php else: ?>
                                                    <tr>
                                                        <td colspan="3" class="text-center">Tidak ada data sasaran.</td>
                                                    </tr>
                                                <?php endif; ?>
                                            </tbody>
                                        </table>
                                    </div>

                                    <hr class="my-0">

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
                                                        <?php
                                                        $st_penilai1 = isset($ppk->status_penilai1) ? $ppk->status_penilai1 : 'Belum Disetujui';
                                                        $cls_penilai1 = ($st_penilai1 == 'Disetujui') ? 'badge-success' : (($st_penilai1 == 'Ditolak') ? 'badge-danger' : 'badge-secondary');
                                                        ?>
                                                        <span class="badge <?= $cls_penilai1 ?> p-2" style="font-size: 14px;"><?= $st_penilai1 ?></span>
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
                                                        <div>
                                                            <!-- Hidden Checkbox (untuk form submit) -->
                                                            <input type="checkbox" id="status_pimpinanunit" name="status_pimpinanunit" value="Disetujui" style="display:none;" <?= (isset($ppk->status_pimpinanunit) && $ppk->status_pimpinanunit == 'Disetujui') ? 'checked' : '' ?>>

                                                            <!-- Area Tanda Tangan Interaktif -->
                                                            <div id="btn-signature" class="d-flex flex-column align-items-center justify-content-center p-2" style="border: 2px dashed #ccc; border-radius: 8px; cursor: pointer; min-height: 80px; transition: all 0.3s;">
                                                                <div class="unsigned-content <?= (isset($ppk->status_pimpinanunit) && $ppk->status_pimpinanunit == 'Disetujui') ? 'd-none' : '' ?>">
                                                                    <i class="mdi mdi-draw text-primary" style="font-size: 2rem;"></i>
                                                                    <div class="small text-muted mt-1">Klik untuk Tanda Tangan</div>
                                                                </div>
                                                                <div class="signed-content <?= (isset($ppk->status_pimpinanunit) && $ppk->status_pimpinanunit == 'Disetujui') ? '' : 'd-none' ?>">
                                                                    <div class="text-success" style="font-family: 'Brush Script MT', cursive; font-size: 1.4rem; line-height: 1.2;">
                                                                        <?= isset($pimpinan->nama) ? $pimpinan->nama : 'Pimpinan' ?>
                                                                    </div>
                                                                    <div class="small text-muted mt-1" style="font-size: 0.65rem;">Digitally Signed</div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Tombol Simpan -->
                                    <div class="row mt-4">
                                        <div class="col-12 text-right">
                                        <a href="<?= base_url('pegawai/ppk_penilai#pimpinan') ?>" class="btn btn-secondary px-4"><i class="mdi mdi-arrow-left mr-1"></i> Kembali</a>
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
            // Logic Tanda Tangan Digital
            const btnSig = document.getElementById('btn-signature');
            const chkSig = document.getElementById('status_pimpinanunit');

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