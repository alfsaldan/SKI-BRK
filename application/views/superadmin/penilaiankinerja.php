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

            <?php if (isset($pegawai_detail) && $pegawai_detail) { ?>

                <!-- Detail Pegawai -->
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <h5>Detail Pegawai</h5>
                                <p><b>NIK:</b> <?= $pegawai_detail->nik; ?></p>
                                <p><b>Nama:</b> <?= $pegawai_detail->nama; ?></p>
                                <p><b>Jabatan:</b> <?= $pegawai_detail->jabatan; ?></p>
                                <p><b>Unit Kantor:</b> </p>
                                <input type="hidden" id="nik" value="<?= $pegawai_detail->nik ?>">
                                <h5>Penilai I</h5>
                                <p><b>NIK:</b></p>
                                <p><b>Nama:</b></p>
                                <p><b>Jabatan:</b></p>
                                <h5>Penilai II</h5>
                                <p><b>NIK:</b></p>
                                <p><b>Nama:</b></p>
                                <p><b>Jabatan:</b></p>
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
                        <div class="card">
                            <div class="card-body">
                                <h5>Form Penilaian</h5>
                                <div class="table-responsive">
                                    <table class="table table-bordered" id="tabel-penilaian">
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
                                                <th>Status</th>
                                                <th>Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $printed_any = false;
                                            foreach ($order as $persp) {
                                                if (empty($grouped[$persp])) continue;
                                                $printed_any = true;
                                                $persp_rows = count_rows($grouped[$persp]);
                                                $first_persp_cell = true;
                                                $subtotal_bobot_perspektif = 0;

                                                foreach ($grouped[$persp] as $sasaran => $items) {
                                                    $sasaran_rows = count($items);
                                                    $first_sas_cell = true;

                                                    foreach ($items as $i) {
                                                        $id = $i->id;
                                                        $bobot = $i->bobot ?? 0;
                                                        $indik = $i->indikator ?? '';
                                                        $subtotal_bobot_perspektif += $bobot;

                                                        $statusClass = 'text-secondary';
                                                        $statusText = 'Belum Dinilai';
                                            ?>
                                                        <tr data-id="<?= $id; ?>" data-bobot="<?= $bobot; ?>" data-perspektif="<?= $persp; ?>">
                                                            <?php if ($first_persp_cell) { ?>
                                                                <td rowspan="<?= $persp_rows; ?>" style="vertical-align:middle;font-weight:600;background:#C8E6C9;"><?= $persp; ?></td>
                                                            <?php $first_persp_cell = false;
                                                            } ?>

                                                            <?php if ($first_sas_cell) { ?>
                                                                <td rowspan="<?= $sasaran_rows; ?>" style="vertical-align:middle;background:#E3F2FD;"><?= $sasaran; ?></td>
                                                            <?php $first_sas_cell = false;
                                                            } ?>

                                                            <td class="text-center"><?= $bobot; ?>
                                                                <input type="hidden" class="bobot" value="<?= $bobot ?>">
                                                            </td>
                                                            <td><?= $indik; ?></td>

                                                            <td><input type="text" class="form-control target-input" value="<?= $i->target; ?>"></td>
                                                            <td><input type="date" class="form-control" value="<?= $i->batas_waktu; ?>"></td>
                                                            <td><input type="text" class="form-control realisasi-input" value="<?= $i->realisasi; ?>"></td>

                                                            <td class="text-center"><input type="text" class="form-control form-control-sm pencapaian-output" readonly></td>
                                                            <td class="text-center"><input type="text" class="form-control form-control-sm nilai-output" readonly></td>
                                                            <td class="text-center"><input type="text" class="form-control form-control-sm nilai-bobot-output" readonly></td>

                                                            <td class="text-center <?= $statusClass; ?>"><?= $statusText; ?></td>
                                                            <td class="text-center">
                                                                <button type="button" class="btn btn-sm btn-primary simpan-penilaian">Simpan</button>
                                                            </td>
                                                        </tr>
                                                <?php
                                                    } // endforeach items
                                                } // endforeach grouped
                                                ?>
                                                <tr class="subtotal-row" style="font-weight:bold;background:#F1F8E9;">
                                                    <td colspan="2">Sub Total Bobot <?= $persp; ?></td>
                                                    <td class="text-center"><span class="subtotal-bobot"><?= $subtotal_bobot_perspektif; ?></span></td>
                                                    <td colspan="6" class="text-center">Sub Total Nilai <?= $persp; ?> Dibobot</td>
                                                    <td class="text-center"><span class="subtotal-nilai-bobot">0.00</span></td>
                                                    <td colspan="2"></td>
                                                </tr>
                                            <?php
                                            } // endforeach order
                                            if (!$printed_any) { ?>
                                                <tr>
                                                    <td colspan="12" class="text-center">Tidak ada indikator untuk jabatan ini</td>
                                                </tr>
                                            <?php } ?>
                                        </tbody>
                                        <tfoot style="background-color:#2E7D32;color:#fff;font-weight:bold;text-align:center;">
                                            <tr>
                                                <td colspan="2">Total</td>
                                                <td><span id="total-bobot">0</span></td>
                                                <td colspan="6" class="text-center">Total Nilai Kinerja</td>
                                                <td><span id="total-nilai-bobot">0.00</span></td>
                                                <td colspan="2"></td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            <?php } // endif 
            ?>

        </div>
    </div>
</div>

<!-- SweetAlert -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<?php if ($this->session->flashdata('message')): ?>
<script>
    Swal.fire({
        icon: '<?= $this->session->flashdata('message')['type']; ?>',
        title: 'Informasi',
        text: '<?= $this->session->flashdata('message')['text']; ?>',
        confirmButtonColor: '#2E7D32'
    });
</script>
<?php endif; ?>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const nik = document.getElementById('nik')?.value;

        function hitungRow(row) {
            const target = parseFloat(row.querySelector('.target-input').value) || 0;
            const realisasi = parseFloat(row.querySelector('.realisasi-input').value) || 0;
            const bobot = parseFloat(row.querySelector('.bobot').value) || 0;

            let pencapaian = 0,
                nilai = 0,
                nilaiBobot = 0;
            if (target > 0) {
                pencapaian = (realisasi / target) * 100;
                nilai = Math.min(pencapaian, 100);
                nilaiBobot = (nilai * bobot) / 100;
            }

            row.querySelector('.pencapaian-output').value = pencapaian.toFixed(2);
            row.querySelector('.nilai-output').value = nilai.toFixed(2);
            row.querySelector('.nilai-bobot-output').value = nilaiBobot.toFixed(2);
            return {
                bobot,
                nilaiBobot,
                perspektif: row.dataset.perspektif
            };
        }

        function hitungTotal() {
            let totalBobot = 0,
                totalNilai = 0;
            const subtotalMap = {};

            document.querySelectorAll('#tabel-penilaian tbody tr[data-id]').forEach(row => {
                const { bobot, nilaiBobot, perspektif } = hitungRow(row);
                totalBobot += bobot;
                totalNilai += nilaiBobot;

                if (!subtotalMap[perspektif]) {
                    subtotalMap[perspektif] = 0;
                }
                subtotalMap[perspektif] += nilaiBobot;
            });

            document.getElementById('total-bobot').innerText = totalBobot.toFixed(2);
            document.getElementById('total-nilai-bobot').innerText = totalNilai.toFixed(2);

            document.querySelectorAll('.subtotal-row').forEach(row => {
                const perspektif = row.querySelector('td[colspan="2"]').innerText.replace('Sub Total ', '');
                const nilaiSub = subtotalMap[perspektif] || 0;
                row.querySelector('.subtotal-nilai-bobot').innerText = nilaiSub.toFixed(2);
            });
        }

        document.querySelectorAll('.target-input, .realisasi-input').forEach(input => {
            input.addEventListener('input', hitungTotal);
        });

        hitungTotal();

        document.querySelectorAll('.simpan-penilaian').forEach(btn => {
            btn.addEventListener('click', function() {
                const row = this.closest('tr');
                const indikator_id = row.dataset.id;
                const target = row.querySelector('.target-input').value;
                const batas_waktu = row.querySelector('input[type="date"]').value;
                const realisasi = row.querySelector('.realisasi-input').value;

                fetch('<?= base_url("SuperAdmin/simpanPenilaianBaris") ?>', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                        body: `nik=${nik}&indikator_id=${indikator_id}&target=${encodeURIComponent(target)}&batas_waktu=${encodeURIComponent(batas_waktu)}&realisasi=${encodeURIComponent(realisasi)}`
                    })
                    .then(res => res.json())
                    .then(res => {
                        if (res.status === 'success') {
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil',
                                text: res.message,
                                timer: 2000,
                                showConfirmButton: false
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal',
                                text: res.message || 'Gagal menyimpan',
                                confirmButtonColor: '#d33'
                            });
                        }
                    })
                    .catch(err => {
                        console.error(err);
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Terjadi kesalahan server',
                            confirmButtonColor: '#d33'
                        });
                    });
            });
        });
    });
</script>
