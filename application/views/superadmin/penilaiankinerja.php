<div class="content-page">
    <div class="content">
        <div class="container-fluid">

            <!-- Judul -->
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box">
                        <h4 class="page-title">Penilaian Kinerja Pegawai</h4>
                    </div>
                </div>
            </div>

            <!-- Form cari NIK -->
            <div class="row">
                <div class="col-6">
                    <div class="card">
                        <div class="card-body">
                            <h5>Masukkan NIK Pegawai</h5>
                            <form action="<?= base_url('SuperAdmin/cariPenilaian'); ?>" method="post">
                                <input type="text" name="nik" class="form-control" placeholder="Masukkan NIK Pegawai" required>
                                <button type="submit" class="btn btn-success mt-2">Nilai</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <?php if (isset($pegawai_detail) && $pegawai_detail): ?>
                <!-- Detail Pegawai -->
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <h5>Detail Pegawai</h5>
                                <p><b>NIK:</b> <?= $pegawai_detail->nik; ?></p>
                                <p><b>Nama:</b> <?= $pegawai_detail->nama; ?></p>
                                <p><b>Jabatan:</b> <?= $pegawai_detail->jabatan; ?></p>
                            </div>
                        </div>
                    </div>
                </div>

                <?php
                $order = ['Keuangan (F)', 'Pelanggan (C)', 'Proses Internal (IP)', 'Pembelajaran & Pertumbuhan (LG)'];
                $grouped = [];
                if (!empty($indikator_by_jabatan)) {
                    foreach ($indikator_by_jabatan as $row) {
                        $p = $row->perspektif ?? '';
                        $s = $row->sasaran_kerja ?? '';
                        $grouped[$p][$s][] = $row;
                    }
                }
                function count_rows($arr)
                {
                    $sum = 0;
                    foreach ($arr as $items) $sum += count($items);
                    return $sum;
                }
                ?>

                <!-- Form Penilaian -->
                <div class="row">
                    <div class="col-12">
                        <form action="<?= base_url('SuperAdmin/simpanPenilaian'); ?>" method="post">
                            <input type="hidden" name="nik" value="<?= $pegawai_detail->nik; ?>">
                            <div class="card">
                                <div class="card-body">
                                    <h5>Form Penilaian</h5>
                                    <div class="table-responsive">
                                        <table class="table table-bordered">
                                            <thead style="background-color:#2E7D32;color:#fff;text-align:center;">
                                                <tr>
                                                    <th>Perspektif</th>
                                                    <th>Sasaran Kerja</th>
                                                    <th>Bobot (%)</th>
                                                    <th>Indikator</th>
                                                    <th>Target</th>
                                                    <th>Batas Waktu</th>
                                                    <th>Realisasi</th>
                                                    <th>Pencapaian (%)</th>
                                                    <th>Nilai</th>
                                                    <th>Nilai Dibobot</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                $printed_any = false;
                                                foreach ($order as $persp):
                                                    if (empty($grouped[$persp])) continue;
                                                    $printed_any = true;

                                                    $persp_rows = count_rows($grouped[$persp]);
                                                    $first_persp_cell = true;
                                                    $subtotal_bobot_perspektif = 0;

                                                    foreach ($grouped[$persp] as $sasaran => $items):
                                                        $sasaran_rows = count($items);
                                                        $first_sas_cell = true;

                                                        foreach ($items as $i):
                                                            $id = $i->id;
                                                            $bobot = $i->bobot ?? 0;
                                                            $indik = $i->indikator ?? '';
                                                            $subtotal_bobot_perspektif += $bobot;
                                                ?>
                                                            <tr data-id="<?= $id; ?>" data-bobot="<?= $bobot; ?>" data-perspektif="<?= $persp; ?>">
                                                                <?php if ($first_persp_cell): ?>
                                                                    <td rowspan="<?= $persp_rows; ?>" style="vertical-align:middle;font-weight:600;background:#C8E6C9;"><?= $persp; ?></td>
                                                                <?php $first_persp_cell = false;
                                                                endif; ?>
                                                                <?php if ($first_sas_cell): ?>
                                                                    <td rowspan="<?= $sasaran_rows; ?>" style="vertical-align:middle;background:#E3F2FD;"><?= $sasaran; ?></td>
                                                                <?php $first_sas_cell = false;
                                                                endif; ?>
                                                                <td style="text-align:center;"><?= $bobot; ?></td>
                                                                <td><?= $indik; ?></td>
                                                                <td><input type="text" name="target[<?= $id; ?>]" class="form-control target-input"></td>
                                                                <td><input type="date" name="batas_waktu[<?= $id; ?>]" class="form-control"></td>
                                                                <td><input type="text" name="realisasi[<?= $id; ?>]" class="form-control realisasi-input"></td>
                                                                <td class="text-center"><input type="text" class="form-control form-control-sm pencapaian-output" readonly></td>
                                                                <td class="text-center"><input type="text" class="form-control form-control-sm nilai-output" readonly></td>
                                                                <td class="text-center"><input type="text" class="form-control form-control-sm nilai-bobot-output" readonly></td>
                                                            </tr>
                                                    <?php
                                                        endforeach;
                                                    endforeach;
                                                    ?>
                                                    <!-- Baris subtotal perspektif -->
                                                    <tr class="subtotal-row" style="font-weight:bold;background:#F1F8E9;">
                                                        <td colspan="2">Sub Total <?= $persp; ?></td> <!-- Perspektif + Sasaran -->
                                                        <td class="text-center"><span class="subtotal-bobot"><?= $subtotal_bobot_perspektif; ?></span></td> <!-- Bobot subtotal -->
                                                        <td colspan="6" class="text-center">Sub Total Nilai <?= $persp; ?> Dibobot</td> <!-- Merge Target → Nilai Dibobot, hanya teks -->
                                                        <td class="text-center"><span class="subtotal-nilai-bobot">0.00</span></td> <!-- Angka subtotal pindah ke kolom terakhir -->
                                                    </tr>

                                                <?php endforeach;
                                                if (!$printed_any): ?>
                                                    <tr>
                                                        <td colspan="10" class="text-center">Tidak ada indikator untuk jabatan ini</td>
                                                    </tr>
                                                <?php endif; ?>
                                            </tbody>
                                            <tfoot style="background-color:#2E7D32;color:#fff;font-weight:bold;text-align:center;">
                                                <tr>
                                                    <td colspan="2">Total</td>
                                                    <td><span id="total-bobot">0</span></td>
                                                    <td colspan="6" class="text-center">Total Nilai Dibobot</td>
                                                    <td><span id="total-nilai-bobot">0.00</span></td>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>

                                    <button type="submit" class="btn btn-primary">Simpan Penilaian</button>
                                    <p class="text-muted mt-2 mb-0" style="font-size:12px;">
                                        *Kolom Pencapaian, Nilai, dan Nilai Dibobot sementara hanya tampilan. Perhitungan final bisa diaktifkan saat penyimpanan.
                                    </p>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

            <?php endif; ?>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', updateAllCalculations);
    document.addEventListener('input', function(e) {
        if (!e.target.classList.contains('target-input') && !e.target.classList.contains('realisasi-input')) return;
        updateAllCalculations();
    });

    function updateAllCalculations() {
        const parseNum = v => {
            if (!v) return NaN;
            v = (v + '').replace(/[^0-9.,-]/g, '').replace(/\./g, '').replace(',', '.');
            return parseFloat(v);
        };
        let totalNilaiBobot = 0,
            totalBobot = 0,
            perspektifSubtotals = {};

        document.querySelectorAll('tbody tr[data-perspektif]').forEach(row => {
            const target = parseNum(row.querySelector('.target-input').value),
                realisasi = parseNum(row.querySelector('.realisasi-input').value),
                bobot = parseFloat(row.dataset.bobot || '0'),
                perspektif = row.dataset.perspektif,
                pencapaianEl = row.querySelector('.pencapaian-output'),
                nilaiEl = row.querySelector('.nilai-output'),
                nilaiBobotEl = row.querySelector('.nilai-bobot-output');
            let nilaiBobot = 0;
            if (!isNaN(target) && target !== 0 && !isNaN(realisasi)) {
                let pc = (realisasi / target) * 100;
                if (pc < 0) pc = 0;
                pencapaianEl.value = pc.toFixed(2);
                let nilai = Math.min(pc, 100);
                nilaiEl.value = nilai.toFixed(2);
                nilaiBobot = nilai * (bobot / 100);
                nilaiBobotEl.value = nilaiBobot.toFixed(2);
            } else {
                pencapaianEl.value = '';
                nilaiEl.value = '';
                nilaiBobotEl.value = '';
            }
            if (!isNaN(bobot)) totalBobot += bobot;
            totalNilaiBobot += nilaiBobot;
            if (!perspektifSubtotals[perspektif]) perspektifSubtotals[perspektif] = 0;
            perspektifSubtotals[perspektif] += nilaiBobot;
        });

        // Update subtotal
        document.querySelectorAll('.subtotal-row').forEach(row => {
            const perspektifName = row.querySelector('td').innerText.replace('Sub Total ', '').trim();
            if (perspektifSubtotals[perspektifName] !== undefined) {
                row.querySelector('.subtotal-nilai-bobot').innerText = perspektifSubtotals[perspektifName].toFixed(2);
            }
        });

        document.getElementById('total-bobot').innerText = totalBobot.toFixed(0);
        document.getElementById('total-nilai-bobot').innerText = totalNilaiBobot.toFixed(2);
    }
</script>