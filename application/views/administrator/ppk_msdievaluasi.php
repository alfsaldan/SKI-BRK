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
                                <li class="breadcrumb-item active">Evaluasi PPK (Pimpinan)</li>
                            </ol>
                        </div>
                        <h4 class="page-title text-primary"><i class="mdi mdi-clipboard-check-outline mr-1"></i> Evaluasi Program Peningkatan Kinerja (PPK)</h4>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12">
                    <div class="card shadow-sm border-0">
                        <div class="card-body">
                            <h4 class="header-title mb-4 text-primary text-center font-weight-bold">Formulir Evaluasi PPK (Divisi MSDI)</h4>

                            <form action="<?= base_url('Administrator/simpan_ppk_msdievaluasi') ?>" method="post">
                                <input type="hidden" name="id_ppk" value="<?= $ppk->id ?? '' ?>">
                                <input type="hidden" name="nik" value="<?= $pegawai->nik ?? '' ?>">
                                <input type="hidden" name="periode_awal_kembali" value="<?= $periode_awal_kembali ?? '' ?>">
                                <input type="hidden" name="periode_akhir_kembali" value="<?= $periode_akhir_kembali ?? '' ?>">
                                <input type="hidden" name="id_nilai_akhir" value="<?= $nilai_akhir->id ?? '' ?>">

                                <!-- 1. Evaluasi Pelaksanaan PPK -->
                                <div class="form-group mb-4">
                                    <h5 class="text-primary font-weight-bold mb-3">
                                        <i class="mdi mdi-clipboard-check-outline mr-1"></i>
                                        Evaluasi Pelaksanaan PPK
                                    </h5>
                                    <div class="p-3 bg-light border rounded">
                                        <?= !empty($evaluasi->evaluasi_pelaksanaan) ? nl2br(htmlspecialchars($evaluasi->evaluasi_pelaksanaan)) : '<span class="text-muted">Belum ada evaluasi.</span>' ?>
                                    </div>
                                </div>

                                <!-- 2. Hasil Pencapaian Sasaran -->
                                <div class="form-group mb-4">
                                    <h5 class="text-primary font-weight-bold mb-3"><i class="mdi mdi-chart-line mr-1"></i>Hasil Pencapaian Sasaran</h5>
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-hover">
                                            <thead class="thead-light text-center">
                                                <tr>
                                                    <th style="width: 50px;">No</th>
                                                    <th>Sasaran Bulan ini</th>
                                                    <th>Hasil</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php if (!empty($detail_evaluasi)): ?>
                                                    <?php foreach ($detail_evaluasi as $i => $row): ?>
                                                        <tr>
                                                            <td class="text-center"><?= $i + 1 ?></td>
                                                            <td><?= nl2br(htmlspecialchars($row['sasaran'] ?? '')) ?></td>
                                                            <td><?= nl2br(htmlspecialchars($row['response'] ?? $row['hasil'] ?? '')) ?></td>
                                                        </tr>
                                                    <?php endforeach; ?>
                                                <?php else: ?>
                                                    <tr><td colspan="3" class="text-center">Tidak ada data.</td></tr>
                                                <?php endif; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                                <!-- 3. Komitmen Lanjutan -->
                                <div class="form-group mb-4">
                                    <h5 class="text-primary font-weight-bold mb-3"><i class="mdi mdi-account-check-outline mr-1"></i>Komitmen Lanjutan</h5>
                                    <div class="p-3 bg-light border rounded">
                                        <?= !empty($evaluasi->komitmen_lanjutan) ? nl2br(htmlspecialchars($evaluasi->komitmen_lanjutan)) : '<span class="text-muted">Belum ada komitmen lanjutan.</span>' ?>
                                    </div>
                                </div>

                                <!-- 4. Rincian Tindakan -->
                                <div class="form-group mb-4">
                                    <h5 class="text-primary font-weight-bold mb-3"><i class="mdi mdi-format-list-checks mr-1"></i>Rincian Tindakan</h5>
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-hover">
                                            <thead class="thead-light text-center">
                                                <tr>
                                                    <th style="width: 50px;">No</th>
                                                    <th>Sasaran Bulan ini</th>
                                                    <th>Rincian Tindakan</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php if (!empty($detail_tindakan)): ?>
                                                    <?php foreach ($detail_tindakan as $i => $row): ?>
                                                        <tr>
                                                            <td class="text-center"><?= $i + 1 ?></td>
                                                            <td><?= nl2br(htmlspecialchars($row['sasaran'] ?? '')) ?></td>
                                                            <td><?= nl2br(htmlspecialchars($row['response'] ?? $row['rincian'] ?? '')) ?></td>
                                                        </tr>
                                                    <?php endforeach; ?>
                                                <?php else: ?>
                                                    <tr><td colspan="3" class="text-center">Tidak ada data.</td></tr>
                                                <?php endif; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                                <!-- 5. Kesimpulan -->
                                <div class="form-group mb-4">
                                    <h5 class="text-primary font-weight-bold mb-3"><i class="mdi mdi-file-check-outline mr-1"></i>Kesimpulan Hasil PPK</h5>
                                    <div class="pl-3">
                                        <?php 
                                            $kesimpulan = $evaluasi->kesimpulan ?? '';
                                            if ($kesimpulan == 'Berhasil') {
                                                echo '<span class="badge badge-success p-2" style="font-size: 1rem;">Berhasil</span>';
                                            } elseif ($kesimpulan == 'Belum Berhasil') {
                                                echo '<span class="badge badge-danger p-2" style="font-size: 1rem;">Belum Berhasil</span>';
                                            } else {
                                                echo '<span class="text-muted">Belum ada kesimpulan.</span>';
                                            }
                                        ?>
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
                                                        <?php if (isset($evaluasi->status_pegawai) && $evaluasi->status_pegawai == 'Disetujui'): ?>
                                                            <span class="badge badge-success p-2" style="font-size: 14px;">Disetujui</span>
                                                        <?php else: ?>
                                                            <span class="badge badge-secondary p-2" style="font-size: 14px;">Belum Disetujui</span>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- Penilai -->
                                        <div class="col-md-3 mb-3">
                                            <div class="card border-secondary border h-100">
                                                <div class="card-body p-3 d-flex flex-column">
                                                    <h6 class="card-title font-weight-bold mb-3">Disetujui bersama oleh<br><small>Atasan Pegawai</small></h6>
                                                    <div class="flex-grow-1 d-flex justify-content-center align-items-center">
                                                        <?php if (isset($evaluasi->status_penilai1) && $evaluasi->status_penilai1 == 'Disetujui'): ?>
                                                            <span class="badge badge-success p-2" style="font-size: 14px;">Disetujui</span>
                                                        <?php else: ?>
                                                            <span class="badge badge-secondary p-2" style="font-size: 14px;">Belum Disetujui</span>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- MSDI -->
                                        <div class="col-md-3 mb-3">
                                            <div class="card border-secondary border h-100">
                                                <div class="card-body p-3 d-flex flex-column">
                                                    <h6 class="card-title font-weight-bold mb-3">Diverifikasi oleh<br><small>Divisi SDI</small></h6>
                                                    <div class="flex-grow-1 d-flex justify-content-center align-items-center">
                                                        <?php
                                                        $is_signed = (isset($evaluasi->status_msdi) && $evaluasi->status_msdi == 'Disetujui');
                                                        $sig_style = $is_signed ? 'border-color: #28a745; background-color: #f0fff4;' : 'border: 2px dashed #ccc;';
                                                        ?>
                                                        <div>
                                                            <input type="checkbox" id="status_msdi" name="status_msdi" value="Disetujui" style="display:none;" <?= $is_signed ? 'checked' : '' ?>>
                                                            <div id="sig-msdi" class="signature-box d-flex flex-column align-items-center justify-content-center p-2" style="<?= $sig_style ?> border-radius: 8px; cursor: pointer; min-height: 80px; transition: all 0.3s;">
                                                                <div class="unsigned-content <?= $is_signed ? 'd-none' : '' ?>">
                                                                    <i class="mdi mdi-draw text-primary" style="font-size: 2rem;"></i>
                                                                    <div class="small text-muted mt-1">Klik untuk Tanda Tangan</div>
                                                                </div>
                                                                <div class="signed-content <?= $is_signed ? '' : 'd-none' ?>">
                                                                    <div class="text-success signer-name" style="font-family: 'Brush Script MT', cursive; font-size: 1.4rem; line-height: 1.2;">
                                                                        <?= isset($current_user->nama) ? htmlspecialchars($current_user->nama) : 'MSDI' ?>
                                                                    </div>
                                                                    <div class="small text-muted mt-1" style="font-size: 0.65rem;">Digitally Signed</div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- Pimpinan Unit (Interactive) -->
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
                                        <?php
                                        $kembali_url = base_url('Administrator/monitoring_ppk');
                                        if (!empty($periode_awal_kembali) && !empty($periode_akhir_kembali)) {
                                            $kembali_url .= '?awal=' . urlencode($periode_awal_kembali) . '&akhir=' . urlencode($periode_akhir_kembali);
                                        }
                                        ?>
                                        <a href="<?= $kembali_url ?>" class="btn btn-secondary mr-2"><i class="mdi mdi-arrow-left"></i> Kembali</a>
                                        <button type="submit" class="btn btn-primary"><i class="mdi mdi-content-save"></i> Simpan Persetujuan</button>
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
        // Logic Tanda Tangan Digital
        const btnSig = document.getElementById('sig-msdi');
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
                    btnSig.style.borderColor = '#28a745';
                    btnSig.style.backgroundColor = '#f0fff4';
                } else {
                    signed.classList.add('d-none');
                    unsigned.classList.remove('d-none');
                    btnSig.style.borderColor = '#ccc';
                    btnSig.style.backgroundColor = '#fff';
                }
            }
        }
    });
</script>