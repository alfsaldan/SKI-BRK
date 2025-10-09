<!-- ============================================================== -->
<!-- START: DETAIL VERIFIKASI PENILAIAN -->
<!-- ============================================================== -->
<div class="content-page">
    <div class="content">
        <div class="container-fluid">

            <!-- Judul Halaman -->
            <div class="row mb-3">
                <div class="col-12 d-flex justify-content-between align-items-center">
                    <h3 class="page-title">
                        <i class="mdi mdi-clipboard-check-outline"></i>
                        Detail Verifikasi Penilaian Pegawai
                    </h3>

                    <?php if ($status_penilaian == 'disetujui'): ?>
                        <span class="badge bg-success px-3 py-2 shadow-sm fs-6">
                            ✅ Sudah Diverifikasi
                        </span>
                    <?php elseif ($status_penilaian == 'ditolak'): ?>
                        <span class="badge bg-danger px-3 py-2 shadow-sm fs-6">
                            ❌ Ditolak
                        </span>
                    <?php else: ?>
                        <span class="badge bg-warning text-dark px-3 py-2 shadow-sm fs-6">
                            ⏳ Belum Diverifikasi
                        </span>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Pilih Periode (untuk menyesuaikan periode yang dilihat) -->
            <div class="row mb-3">
                <div class="col-12">
                    <form id="form-periode" method="get" action="<?= base_url('Administrator/detailVerifikasi/' . ($pegawai_detail->nik ?? '')) ?>">
                        <div class="d-flex gap-2 align-items-center">
                            <label class="mb-0">Pilih Periode:</label>
                            <select id="select_periode" class="form-control" style="max-width:350px;">
                                <?php if (!empty($periode_list)): ?>
                                    <?php foreach ($periode_list as $p):
                                        $val = $p->periode_awal . '|' . $p->periode_akhir;
                                        $label = date('d M Y', strtotime($p->periode_awal)) . ' - ' . date('d M Y', strtotime($p->periode_akhir));
                                        $selected = ($selected_awal == $p->periode_awal && $selected_akhir == $p->periode_akhir) ? 'selected' : '';
                                    ?>
                                        <option value="<?= $val ?>" <?= $selected ?>><?= $label ?></option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                            <input type="hidden" name="awal" id="form_awal" value="<?= htmlspecialchars($selected_awal ?? '', ENT_QUOTES) ?>">
                            <input type="hidden" name="akhir" id="form_akhir" value="<?= htmlspecialchars($selected_akhir ?? '', ENT_QUOTES) ?>">
                            <button type="submit" class="btn btn-outline-primary">Terapkan</button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Data Pegawai -->
            <div class="card shadow-lg rounded-4 mb-4">
                <div class="card-header bg-primary text-white rounded-top-4">
                    <h5 class="mb-0">
                        <i class="mdi mdi-account-circle-outline"></i> Data Pegawai
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row gy-3">
                        <div class="col-md-4">
                            <label class="fw-bold text-secondary">Nama Pegawai</label>
                            <input type="text" class="form-control" readonly
                                value="<?= htmlspecialchars($pegawai_detail->nama_pegawai ?? '-', ENT_QUOTES, 'UTF-8') ?>">
                        </div>
                        <div class="col-md-4">
                            <label class="fw-bold text-secondary">NIK</label>
                            <input type="text" class="form-control" readonly
                                value="<?= htmlspecialchars($pegawai_detail->nik ?? '-', ENT_QUOTES, 'UTF-8') ?>">
                        </div>
                        <div class="col-md-4">
                            <label class="fw-bold text-secondary">Jabatan</label>
                            <input type="text" class="form-control" readonly
                                value="<?= htmlspecialchars($pegawai_detail->jabatan ?? '-', ENT_QUOTES, 'UTF-8') ?>">
                        </div>
                        <div class="col-md-4">
                            <label class="fw-bold text-secondary">Unit Kerja</label>
                            <input type="text" class="form-control" readonly
                                value="<?= htmlspecialchars($pegawai_detail->unit_kerja ?? '-', ENT_QUOTES, 'UTF-8') ?>">
                        </div>
                        <div class="col-md-4">
                            <label class="fw-bold text-secondary">Atasan Langsung</label>
                            <input type="text" class="form-control" readonly
                                value="<?= htmlspecialchars($pegawai_detail->penilai1_nama ?? '-', ENT_QUOTES, 'UTF-8') ?>">
                        </div>
                        <div class="col-md-4">
                            <label class="fw-bold text-secondary">Penilai II</label>
                            <input type="text" class="form-control" readonly
                                value="<?= htmlspecialchars($pegawai_detail->penilai2_nama ?? '-', ENT_QUOTES, 'UTF-8') ?>">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Daftar Penilaian -->
            <div class="card shadow-lg rounded-4 mb-4">
                <div class="card-header bg-success text-white rounded-top-4">
                    <h5 class="mb-0">
                        <i class="mdi mdi-format-list-bulleted"></i>
                        Daftar Indikator Penilaian
                    </h5>
                </div>
                <div class="card-body table-responsive">
                    <?php
                    // helper functions from JS: hitungPencapaianOtomatis & hitungNilai
                    function hitungPencapaianOtomatisPHP($target, $realisasi, $indikatorText = '') {
                        $indikatorText = strtolower($indikatorText ?? '');
                        $keywords_rumus1 = ['biaya','beban','efisiensi','npf pembiayaan','npf nominal'];
                        $keywords_rumus3 = ['outstanding','pertumbuhan'];
                        $contains = function($list, $text) {
                            foreach ($list as $k) {
                                if (preg_match('/\b' . preg_quote($k,'/') . '\b/i', $text)) return true;
                            }
                            return false;
                        };
                        $pencapaian = 0;
                        if ($target <= 999) {
                            if ($target == 0) $pencapaian = 0; else $pencapaian = ($realisasi / $target) * 100;
                        } else {
                            if ($contains($keywords_rumus1, $indikatorText)) {
                                $pencapaian = (($target + ($target - $realisasi)) / $target) * 100;
                            } else if ($contains($keywords_rumus3, $indikatorText)) {
                                if ($target == 0) $pencapaian = 0; else $pencapaian = (($realisasi - $target) / abs($target) + 1) * 100;
                            } else {
                                if ($target == 0) $pencapaian = 0; else $pencapaian = ($realisasi / $target) * 100;
                            }
                        }
                        return min($pencapaian, 130);
                    }

                    function hitungNilaiPHP($pencapaian) {
                        $nilai = 0;
                        if ($pencapaian < 0) $nilai = 0;
                        else if ($pencapaian < 80) $nilai = ($pencapaian / 80) * 2;
                        else if ($pencapaian < 90) $nilai = 2 + (($pencapaian - 80) / 10);
                        else if ($pencapaian < 110) $nilai = 3 + (($pencapaian - 90) / 20 * 0.5);
                        else if ($pencapaian < 120) $nilai = 3.5 + (($pencapaian - 110) / 10 * 1);
                        else if ($pencapaian < 130) $nilai = 4.5 + (($pencapaian - 120) / 10 * 0.5);
                        else $nilai = 5;
                        return $nilai;
                    }

                    // Group penilaian by perspektif -> sasaran_kerja for clean rowspan merging
                    $grouped = [];
                    if (!empty($penilaian)) {
                        foreach ($penilaian as $row) {
                            $pers = $row->perspektif ?? 'Lainnya';
                            $sas = $row->sasaran_kerja ?? '-';
                            if (!isset($grouped[$pers])) $grouped[$pers] = [];
                            if (!isset($grouped[$pers][$sas])) $grouped[$pers][$sas] = [];
                            $grouped[$pers][$sas][] = $row;
                        }
                    }

                    // compute totals per perspektif and global using computed values
                    $pers_totals = [];
                    $global_bobot_sum = 0;
                    $global_nilai_dibobot = 0;
                    // first, compute total bobot
                    $totalBobot = 0;
                    foreach ($penilaian as $r) $totalBobot += floatval($r->bobot ?? 0);

                    foreach ($grouped as $pers => $sasList) {
                        $p_bobot = 0;
                        $p_nilai_dibobot = 0;
                        foreach ($sasList as $sas => $items) {
                            foreach ($items as $it) {
                                $target = floatval($it->target ?? 0);
                                $realisasi = floatval($it->realisasi ?? 0);
                                $indikatorText = $it->indikator ?? '';
                                $penc = hitungPencapaianOtomatisPHP($target, $realisasi, $indikatorText);
                                $n = hitungNilaiPHP($penc);
                                $n_bobot = ($totalBobot > 0) ? ($n * floatval($it->bobot ?? 0) / $totalBobot) : 0;
                                $p_bobot += floatval($it->bobot ?? 0);
                                $p_nilai_dibobot += $n_bobot;
                            }
                        }
                        $pers_totals[$pers] = ['bobot' => $p_bobot, 'nilai_dibobot' => $p_nilai_dibobot];
                        $global_bobot_sum += $p_bobot;
                        $global_nilai_dibobot += $p_nilai_dibobot;
                    }
                    ?>

                    <table class="table table-bordered table-hover align-middle text-center">
                        <thead class="bg-light">
                            <tr>
                                <th width="5%">No</th>
                                <th>Perspektif</th>
                                <th>Sasaran Kerja</th>
                                <th>Indikator</th>
                                <th>Bobot (%)</th>
                                <th>Target</th>
                                <th>Batas Waktu</th>
                                <th>Realisasi</th>
                                <th>Pencapaian (%)</th>
                                <th>Nilai</th>
                                <th>Nilai Dibobot</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($grouped)): ?>
                                <?php $no = 1; foreach ($grouped as $pers => $sasList):
                                    $pers_rowspan = 0;
                                    foreach ($sasList as $sas => $items) $pers_rowspan += count($items);
                                ?>
                                    <?php $firstPers = true; ?>
                                    <?php foreach ($sasList as $sas => $items): ?>
                                        <?php $sas_rowspan = count($items); $firstSas = true; ?>
                                        <?php foreach ($items as $it): ?>
                                            <tr>
                                                <td><?= $no++ ?></td>
                                                <?php if ($firstPers): ?>
                                                    <td class="text-start align-middle" rowspan="<?= $pers_rowspan ?>" style="background-color:#eaf6ea; color:#0a6b2b; font-weight:600; vertical-align:middle;"><?= htmlspecialchars($pers, ENT_QUOTES, 'UTF-8') ?></td>
                                                    <?php $firstPers = false; ?>
                                                <?php endif; ?>

                                                <?php if ($firstSas): ?>
                                                    <td class="text-start align-middle" rowspan="<?= $sas_rowspan ?>" style="background-color:#eef8ff; color:#0a4a6b;"><?= htmlspecialchars($sas, ENT_QUOTES, 'UTF-8') ?></td>
                                                    <?php $firstSas = false; ?>
                                                <?php endif; ?>

                                                <?php
                                                    $target = floatval($it->target ?? 0);
                                                    $realisasi = floatval($it->realisasi ?? 0);
                                                    $indikatorText = $it->indikator ?? '';
                                                    $penc = hitungPencapaianOtomatisPHP($target, $realisasi, $indikatorText);
                                                    $n = hitungNilaiPHP($penc);
                                                    $n_bobot = ($totalBobot > 0) ? ($n * floatval($it->bobot ?? 0) / $totalBobot) : 0;
                                                ?>
                                                <td class="text-start"><?= htmlspecialchars($it->indikator ?? '-', ENT_QUOTES, 'UTF-8') ?></td>
                                                <td><?= number_format($it->bobot ?? 0, 2) ?></td>
                                                <td><?= $target !== 0 ? htmlspecialchars($it->target, ENT_QUOTES, 'UTF-8') : '-' ?></td>
                                                <td><?= $realisasi !== 0 ? htmlspecialchars($it->realisasi, ENT_QUOTES, 'UTF-8') : '-' ?></td>
                                                <td><?= $it->batas_waktu ? htmlspecialchars($it->batas_waktu, ENT_QUOTES, 'UTF-8') : '-' ?></td>
                                                <td><?= $penc !== '' ? number_format($penc, 2) : '-' ?></td>
                                                <td><?= number_format($n, 2) ?></td>
                                                <td><?= number_format($n_bobot, 2) ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endforeach; ?>
                                    <!-- Subtotal for perspektif -->
                                    <tr class="fw-bold subtotal-row" data-perspektif="<?= htmlspecialchars($pers, ENT_QUOTES, 'UTF-8') ?>" style="background-color:#f3f8f3;">
                                        <td colspan="4" class="text-end">Sub Total <?= htmlspecialchars($pers) ?></td>
                                        <td><?= number_format($pers_totals[$pers]['bobot'] ?? 0, 2) ?></td>
                                        <td colspan="5" class="text-end">Sub Total Nilai Dibobot</td>
                                        <td class="subtotal-nilai-bobot"><?= number_format($pers_totals[$pers]['nilai_dibobot'] ?? 0, 2) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                                <!-- Grand total -->
                                <tr class="fw-bold" style="background-color:#1b722a; color:#fff;">
                                    <td colspan="4" class="text-center">Total</td>
                                    <td><?= number_format($global_bobot_sum, 2) ?></td>
                                    <td colspan="5" class="text-end">Total Nilai Dibobot</td>
                                    <td id="total-nilai-bobot"><?= number_format($global_nilai_dibobot, 2) ?></td>
                                </tr>
                            <?php else: ?>
                                <tr>
                                    <td colspan="11" class="text-muted">Belum ada data penilaian.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Ringkasan Nilai Akhir -->
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-body">
                    <h4 class="card-title mb-3">
                        <i class="mdi mdi-chart-line text-success"></i>
                        Nilai Akhir (q)
                    </h4>

                    <?php
                    // global_nilai_dibobot computed earlier, fallback if not set
                    $total_skor = $global_nilai_dibobot ?? 0;
                    // weights (as in screenshot): 95% untuk sasaran kerja, 5% untuk budaya
                    $w_sasaran = 0.95;
                    $w_budaya = 0.05;
                    // budaya average unknown in this view -> assume 0
                    $avg_budaya = 0.00;
                    $kontrib_sasaran = $total_skor * $w_sasaran;
                    $kontrib_budaya = $avg_budaya * $w_budaya;
                    $total_nilai = $kontrib_sasaran + $kontrib_budaya;

                    // Pencapaian (%) relative to max score (assume max 2.5 for scaling as used elsewhere)
                    $max_scale = 2.5;
                    $pencapaian_pct = $max_scale > 0 ? ($total_nilai / $max_scale) * 100 : 0;

                    // predikat mapping
                    $predikat = 'Minus';
                    if ($total_nilai >= 4.5) $predikat = 'Excellent (E)';
                    elseif ($total_nilai >= 3.5) $predikat = 'Very Good (VG)';
                    elseif ($total_nilai >= 3.0) $predikat = 'Good (G)';
                    elseif ($total_nilai >= 2.0) $predikat = 'Fair (F)';
                    else $predikat = 'Minus (M)';
                    ?>

                    <div class="row">
                        <div class="col-lg-8">
                            <table class="table table-borderless">
                                <tr>
                                    <td>Total Nilai Sasaran Kerja</td>
                                    <td style="width:140px; text-align:right;"><?= number_format($total_skor, 2) ?></td>
                                    <td style="width:160px; text-align:center;">x Bobot % Sasaran Kerja</td>
                                    <td style="width:100px; text-align:right;"><?= ($w_sasaran*100) ?>%</td>
                                    <td style="width:140px; text-align:right;"><?= number_format($kontrib_sasaran, 2) ?></td>
                                </tr>

                                <tr>
                                    <td>Rata-rata Nilai Internalisasi Budaya</td>
                                    <td style="text-align:right;"><?= number_format($avg_budaya, 2) ?></td>
                                    <td style="text-align:center;">x Bobot % Budaya Perusahaan</td>
                                    <td style="text-align:right;"><?= ($w_budaya*100) ?>%</td>
                                    <td style="text-align:right;"><?= number_format($kontrib_budaya, 2) ?></td>
                                </tr>

                                <tr>
                                    <td colspan="4" class="text-end fw-bold">Total Nilai</td>
                                    <td class="fw-bold" style="text-align:right;"><?= number_format($total_nilai, 2) ?></td>
                                </tr>

                                <tr>
                                    <td colspan="5" class="text-muted small">Fraud<br><span class="text-muted small">(1 jika melakukan fraud, 0 jika tidak melakukan fraud)</span></td>
                                </tr>
                            </table>
                        </div>

                        <div class="col-lg-4">
                            <div class="mb-3">
                                <div class="border rounded p-3 bg-light text-center">
                                    <h6 class="fw-bold">Nilai Akhir</h6>
                                    <div class="display-6 text-success fw-bolder"><?= number_format($total_nilai, 2) ?></div>
                                </div>
                            </div>
                            <div class="mb-3">
                                <div class="border rounded p-3 bg-light text-center">
                                    <h6 class="fw-bold">Pencapaian Akhir</h6>
                                    <div class="display-6 text-success fw-bolder"><?= number_format($pencapaian_pct, 2) ?>%</div>
                                </div>
                            </div>
                            <div class="bg-success text-white rounded p-3 text-center">
                                <h5>Yudisium / Predikat</h5>
                                <div class="mt-3 fw-bolder text-white" style="font-size:1.4rem;"><?= $predikat ?></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tombol Verifikasi -->
            <div class="text-end mt-4">
                <?php
                $verifLabel = ($status_penilaian === 'disetujui') ? 'Ubah Verifikasi' : 'Verifikasi Penilaian';
                ?>
                <button id="btn-verifikasi" class="btn btn-lg <?= ($status_penilaian === 'disetujui') ? 'btn-warning' : 'btn-success' ?> shadow px-4 py-2 rounded-pill">
                    <i class="mdi mdi-check-circle-outline"></i> <?= $verifLabel ?>
                </button>
                <a href="<?= base_url('Administrator/verifikasi_penilaian') ?>"
                    class="btn btn-lg btn-secondary shadow px-4 py-2 rounded-pill ms-2">
                    <i class="mdi mdi-arrow-left"></i> Kembali
                </a>
            </div>

        </div>
    </div>
</div>

<!-- ============================== -->
<!-- 💬 JAVASCRIPT VERIFIKASI PENILAIAN -->
<!-- ============================== -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const btnVerifikasi = document.getElementById("btn-verifikasi");
        if (!btnVerifikasi) return;

        const pegawaiNama = "<?= addslashes($pegawai_detail->nama_pegawai ?? '-') ?>";
        const pegawaiNik = "<?= addslashes($pegawai_detail->nik ?? '-') ?>";
        const statusBadge = document.querySelector(".badge");

        // Sync periode select into hidden awal/akhir inputs so the form submits separate params
        const selectPeriode = document.getElementById('select_periode');
        const inputAwal = document.getElementById('form_awal');
        const inputAkhir = document.getElementById('form_akhir');
        const formPeriode = document.getElementById('form-periode');

        function syncPeriodeInputs() {
            if (!selectPeriode || !inputAwal || !inputAkhir) return;
            const parts = (selectPeriode.value || '').split('|');
            if (parts.length === 2) {
                inputAwal.value = parts[0];
                inputAkhir.value = parts[1];
            } else {
                inputAwal.value = '';
                inputAkhir.value = '';
            }
        }

        // initialize and bind
        syncPeriodeInputs();
        if (selectPeriode) selectPeriode.addEventListener('change', syncPeriodeInputs);
        if (formPeriode) formPeriode.addEventListener('submit', syncPeriodeInputs);

        btnVerifikasi.addEventListener("click", function() {
            Swal.fire({
                title: 'Setujui Penilaian Ini?',
                html: `
                <div class="text-start">
                    <p class="mb-1"><strong>Nama:</strong> ${pegawaiNama}</p>
                    <p><strong>NIK:</strong> ${pegawaiNik}</p>
                    <p class="text-muted small mb-0">
                        Apakah Anda yakin ingin menyetujui hasil penilaian ini?
                    </p>
                </div>
            `,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#198754',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, Setujui',
                cancelButtonText: 'Tidak',
                reverseButtons: true,
            }).then((result) => {
                if (result.isConfirmed) {
                    kirimVerifikasi('disetujui');
                } else if (result.dismiss === Swal.DismissReason.cancel) {
                    Swal.fire({
                        title: 'Tolak Penilaian?',
                        text: 'Apakah Anda ingin menandai penilaian ini sebagai ditolak?',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'Ya, Tolak',
                        cancelButtonText: 'Batal',
                        confirmButtonColor: '#dc3545',
                    }).then((tolak) => {
                        if (tolak.isConfirmed) {
                            kirimVerifikasi('ditolak');
                        }
                    });
                }
            });
        });

        function kirimVerifikasi(status) {
            Swal.fire({
                title: 'Memproses...',
                text: 'Mohon tunggu, sedang memperbarui status penilaian.',
                allowOutsideClick: false,
                didOpen: () => Swal.showLoading()
            });

            // sertakan periode yang sedang dipilih (jika ada)
            const sel = document.getElementById('select_periode');
            let awal = '<?= $selected_awal ?? date('Y-01-01') ?>';
            let akhir = '<?= $selected_akhir ?? date('Y-12-31') ?>';
            if (sel) {
                const parts = (sel.value || '').split('|');
                if (parts.length === 2) {
                    awal = parts[0];
                    akhir = parts[1];
                }
            }

            fetch("<?= base_url('administrator/verifikasiPenilaian') ?>", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/x-www-form-urlencoded"
                    },
                    body: `nik=${pegawaiNik}&status=${status}&awal=${encodeURIComponent(awal)}&akhir=${encodeURIComponent(akhir)}`
                })
                .then(res => res.json())
                .then(data => {
                    if (data.status === 'success') {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: data.message,
                            timer: 2000,
                            showConfirmButton: false
                        });
                        setTimeout(() => location.reload(), 2000);
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal',
                            text: data.message
                        });
                    }
                })
                .catch(() => {
                    Swal.fire({
                        icon: 'error',
                        title: 'Kesalahan',
                        text: 'Tidak dapat terhubung ke server.'
                    });
                });
        }
    });
</script>

<style>
    .card {
        animation: fadeIn 0.4s ease-in-out;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(8px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .table th {
        vertical-align: middle !important;
    }

    .badge {
        font-size: 0.95rem;
        transition: all 0.3s ease;
    }

    #btn-verifikasi:hover {
        transform: scale(1.05);
        transition: 0.2s;
    }
</style>