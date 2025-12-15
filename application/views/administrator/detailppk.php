<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<div class="content-page">
    <div class="content">
        <div class="container-fluid">

            <!-- Judul Halaman -->
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box">
                        <div class="page-title-right">
                            <ol class="breadcrumb m-0">
                                <li class="breadcrumb-item"><a href="<?= base_url('administrator/monitoring_ppk') ?>">Monitoring PPK</a></li>
                                <li class="breadcrumb-item active">Detail PPK</li>
                            </ol>
                        </div>
                        <h4 class="page-title text-primary"><i class="mdi mdi-account-badge-alert-outline mr-1"></i> Detail Program Peningkatan Kinerja (PPK)</h4>
                    </div>
                </div>
            </div>

            <?php if (isset($pegawai) && isset($ppk)): ?>
                <div class="row">
                    <div class="col-12">
                        <div class="card shadow-sm border-0">
                            <div class="card-body">
                                <h4 class="header-title text-center mb-0 text-uppercase text-primary font-weight-bold">Formulir Program Peningkatan Kinerja</h4>
                                <hr>

                                <form action="<?= base_url('administrator/simpan_verifikasi_ppk') ?>" method="post" id="form-ppk">
                                    <input type="hidden" name="ppk_id" value="<?= $ppk->id ?? '' ?>">

                                    <!-- Bagian 1: Data Pegawai & Periode -->
                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <h5 class="text-info mb-3"><i class="mdi mdi-account-details mr-1"></i> Data Pegawai</h5>
                                            <div class="form-group row mb-2">
                                                <label class="col-sm-4 col-form-label font-weight-medium">Nama</label>
                                                <div class="col-sm-8">
                                                    <input type="text" class="form-control-plaintext font-weight-bold" value="<?= $pegawai->nama ?? '-' ?>" readonly>
                                                </div>
                                            </div>
                                            <div class="form-group row mb-2">
                                                <label class="col-sm-4 col-form-label font-weight-medium">NIK</label>
                                                <div class="col-sm-8">
                                                    <input type="text" class="form-control-plaintext" name="nik" value="<?= $pegawai->nik ?? '-' ?>" readonly>
                                                </div>
                                            </div>
                                            <div class="form-group row mb-2">
                                                <label class="col-sm-4 col-form-label font-weight-medium">Jabatan</label>
                                                <div class="col-sm-8">
                                                    <input type="text" class="form-control-plaintext" value="<?= $pegawai->jabatan ?? '-' ?>" readonly>
                                                </div>
                                            </div>
                                            <div class="form-group row mb-2">
                                                <label class="col-sm-4 col-form-label font-weight-medium">Unit Kerja</label>
                                                <div class="col-sm-8">
                                                    <input type="text" class="form-control-plaintext" value="<?= $pegawai->unit_kerja ?? '-' ?>" readonly>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-6 border-left">
                                            <h5 class="text-warning mb-3"><i class="mdi mdi-calendar-clock mr-1"></i> Periode Program</h5>
                                            <div class="form-group row mb-2">
                                                <label class="col-sm-4 col-form-label">PPK Tahap ke</label>
                                                <div class="col-sm-8">
                                                    <input type="text" class="form-control-plaintext" value="<?= $ppk->tahap ?? '-' ?>" readonly>
                                                </div>
                                            </div>
                                            <div class="form-group row mb-2">
                                                <label class="col-sm-4 col-form-label">Periode PPK</label>
                                                <div class="col-sm-8">
                                                    <input type="text" class="form-control-plaintext" value="<?= $ppk->periode_ppk ?? '-' ?>" readonly>
                                                </div>
                                            </div>
                                            <div class="form-group row mb-2">
                                                <label class="col-sm-4 col-form-label">Periode Coaching</label>
                                                <div class="col-sm-8">
                                                    <input type="text" class="form-control-plaintext" value="<?= $ppk->periode_coaching ?? '-' ?>" readonly>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <hr class="my-0">

                                    <!-- Bagian 2: Review Kinerja Sebelumnya -->
                                    <h5 class="text-primary mb-3"><i class="mdi mdi-history mr-1"></i> Review Kinerja Sebelumnya</h5>
                                    <div class="form-group">
                                        <label class="font-weight-bold">Hasil Evaluasi Kinerja Sebelumnya</label>
                                        <textarea class="form-control" rows="3" readonly><?= $ppk->review_sebelum ?? '' ?></textarea>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label class="font-weight-bold">Target</label>
                                                <input type="text" class="form-control" value="<?= $ppk->target ?? '' ?>" readonly>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label class="font-weight-bold">Pencapaian</label>
                                                <input type="text" class="form-control" value="<?= $ppk->pencapaian ?? '' ?>" readonly>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label class="font-weight-bold">Aktivitas</label>
                                                <input type="text" class="form-control" value="<?= $ppk->aktivitas ?? '' ?>" readonly>
                                            </div>
                                        </div>
                                    </div>

                                    <hr class="my-0">

                                    <!-- Bagian 3: Rencana Perbaikan -->
                                    <h5 class="text-primary mb-3"><i class="mdi mdi-bullseye-arrow mr-1"></i> Rencana dan Aktivitas Perbaikan</h5>
                                    <div class="form-group">
                                        <label class="font-weight-bold">Rencana Tindakan untuk Mencapai Sasaran</label>
                                        <textarea class="form-control" rows="3" readonly><?= $ppk->rencana ?? '' ?></textarea>
                                    </div>

                                    <!-- Bagian 4: Sasaran & Tindakan (Static Table) -->
                                    <div class="table-responsive mt-3">
                                        <table class="table table-bordered table-striped" id="table-sasaran">
                                            <thead class="thead-light text-center">
                                                <tr>
                                                    <th style="width: 50px;">No</th>
                                                    <th>Sasaran Bulan Ini</th>
                                                    <th>Rincian Tindakan</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php if (!empty($sasaran)): ?>
                                                    <?php $no = 1; foreach ($sasaran as $s): ?>
                                                        <tr>
                                                            <td class="text-center"><?= $no++ ?></td>
                                                            <td><input type="text" class="form-control" value="<?= htmlspecialchars($s->sasaran_bulan ?? '') ?>" readonly></td>
                                                            <td><input type="text" class="form-control" value="<?= htmlspecialchars($s->rincian_tindakan ?? '') ?>" readonly></td>
                                                        </tr>
                                                    <?php endforeach; ?>
                                                <?php else: ?>
                                                    <tr>
                                                        <td colspan="3" class="text-center text-muted">Tidak ada data sasaran.</td>
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
                                                        $cls_pegawai = ($st_pegawai == 'Disetujui') ? 'badge-success' : 'badge-secondary';
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
                                                        <div>
                                                            <input type="checkbox" id="status_msdi" name="status_msdi" value="Disetujui" style="display:none;" <?= (isset($ppk->status_msdi) && $ppk->status_msdi == 'Disetujui') ? 'checked' : '' ?>>
                                                            <div id="btn-signature-msdi" class="d-flex flex-column align-items-center justify-content-center p-2" style="border: 2px dashed #ccc; border-radius: 8px; cursor: pointer; min-height: 80px; transition: all 0.3s;">
                                                                <div class="unsigned-content <?= (isset($ppk->status_msdi) && $ppk->status_msdi == 'Disetujui') ? 'd-none' : '' ?>">
                                                                    <i class="mdi mdi-draw text-primary" style="font-size: 2rem;"></i>
                                                                    <div class="small text-muted mt-1">Klik untuk Tanda Tangan</div>
                                                                </div>
                                                                <div class="signed-content <?= (isset($ppk->status_msdi) && $ppk->status_msdi == 'Disetujui') ? '' : 'd-none' ?>">
                                                                    <div class="text-success" style="font-family: 'Brush Script MT', cursive; font-size: 1.4rem; line-height: 1.2;">
                                                                        <?= $msdi_nama ?? 'Divisi MSDI' ?>
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
                                            <button type="submit" class="btn btn-primary px-4"><i class="mdi mdi-content-save mr-1"></i> Simpan Verifikasi</button>
                                        </div>
                                    </div>

                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            <?php else: ?>
                <div class="alert alert-warning text-center">
                    <i class="mdi mdi-alert-outline mr-2"></i> Data PPK untuk pegawai ini tidak ditemukan.
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Logic Tanda Tangan Digital untuk MSDI
        const btnSig = document.getElementById('btn-signature-msdi');
        const chkSig = document.getElementById('status_msdi');

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