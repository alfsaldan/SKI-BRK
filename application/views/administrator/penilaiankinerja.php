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
                                <li class="breadcrumb-item active">Penilaian Kinerja</li>
                            </ol>
                        </div>
                        <h4 class="page-title"><i class="mdi mdi-account-edit mr-2 text-primary"></i> Penilaian Kinerja Pegawai</h4>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12 col-md-6">
                    <div class="card shadow-sm mb-3">
                        <div class="card-body">
                            <h5 class="text-primary font-weight-bold mb-3">
                                <i class="mdi mdi-calendar-range mr-2"></i> Pilih Periode Penilaian
                            </h5>

                            <!-- Dropdown Pilihan Periode -->
                            <select id="periode_select" class="form-control">
                                <option value="">-- Pilih Periode --</option>
                                <?php foreach ($periode_list as $p): ?>
                                    <option value="<?= $p->periode_awal . '|' . $p->periode_akhir ?>"
                                        <?= ($p->periode_awal == $periode_awal && $p->periode_akhir == $periode_akhir) ? 'selected' : '' ?>>
                                        <?= $p->periode_awal ?> s/d <?= $p->periode_akhir ?>
                                    </option>
                                <?php endforeach; ?>
                                <option value="baru">+ Tambah Periode Baru</option>
                            </select>

                            <!-- Input Manual Periode Baru (disembunyikan dulu) -->
                            <div id="periode_manual" style="display:none;">
                                <input type="date" id="periode_awal" class="form-control" name="periode_awal">
                                <input type="date" id="periode_akhir" class="form-control" name="periode_akhir">
                            </div>

                            <!-- Hidden untuk dikirim ke server -->
                            <input type="hidden" id="hidden_periode_awal" value="<?= $periode_awal ?>">
                            <input type="hidden" id="hidden_periode_akhir" value="<?= $periode_akhir ?>">

                            <!-- Checkbox Lock -->
                            <div class="form-check mt-3">
                                <input class="form-check-input" type="checkbox" id="lock_input_checkbox">
                                <label class="form-check-label" for="lock_input_checkbox">
                                    🔐 Kunci Periode Ini
                                </label>
                            </div>

                        </div>
                    </div>
                </div>
            </div>

            <!-- Card Input NIK -->
            <div class="row">
                <div class="col-12 col-md-6">
                    <div class="card shadow-sm">
                        <div class="card-body">
                            <h5>Masukkan NIK Pegawai</h5>
                            <form action="<?= base_url('Administrator/cariPenilaian'); ?>" method="post">
                                <input type="hidden" name="periode_awal" id="hidden_periode_awal" value="<?= $periode_awal ?>">
                                <input type="hidden" name="periode_akhir" id="hidden_periode_akhir" value="<?= $periode_akhir ?>">
                                <input type="text" name="nik" class="form-control" placeholder="Masukkan NIK Pegawai" required>
                                <button type="submit" class="btn btn-success mt-2">Nilai</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <?php if (isset($pegawai_detail) && $pegawai_detail) { ?>
                <div class="row">
                    <div class="col-12">
                        <div class="card shadow-sm border-0 mt-3">
                            <div class="card-body">

                                <!-- Detail Pegawai & Informasi Penilaian -->
                                <div class="row mb-3">
                                    <!-- Detail Pegawai -->
                                    <div class="col-md-6 mb-3">
                                        <h5 class="text-primary font-weight-bold mb-3">
                                            <i class="mdi mdi-account-circle-outline mr-2"></i>Detail Pegawai
                                        </h5>
                                        <ul class="list-group list-group-flush">
                                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                                <span class="text-dark font-weight-medium">NIK</span>
                                                <span class="badge badge-primary badge-pill"><?= $pegawai_detail->nik; ?></span>
                                            </li>
                                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                                <span class="text-dark font-weight-medium">Nama</span>
                                                <span class="text-dark"><?= $pegawai_detail->nama; ?></span>
                                            </li>
                                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                                <span class="text-dark font-weight-medium">Jabatan</span>
                                                <span class="text-dark"><?= $pegawai_detail->jabatan; ?></span>
                                            </li>
                                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                                <span class="text-dark font-weight-medium">Unit Kantor</span>
                                                <span class="text-dark"><?= $pegawai_detail->unit_kerja; ?> <?= $pegawai_detail->unit_kantor ?? '-'; ?></span>
                                            </li>
                                        </ul>
                                        <input type="hidden" id="nik" value="<?= $pegawai_detail->nik ?>">
                                    </div>

                                    <!-- Informasi Penilaian -->
                                    <div class="col-md-6 mb-3">
                                        <h5 class="text-success font-weight-bold mb-3">
                                            <i class="mdi mdi-file-document-outline mr-2"></i>Informasi Penilaian
                                        </h5>

                                        <div class="form-group">
                                            <label class="text-dark font-weight-medium">Periode Penilaian:</label>
                                            <input type="text" id="periode_text" class="form-control" readonly>
                                        </div>

                                        <div id="periode_manual" style="display: none;">
                                            <div class="form-inline mb-2">
                                                <label class="mr-2 text-dark font-weight-medium">Periode Penilaian Baru:</label>
                                                <input type="date" id="periode_awal" class="form-control mr-2"
                                                    value="<?= $periode_awal ?? date('Y-01-01'); ?>">
                                                <span class="mr-2">s/d</span>
                                                <input type="date" id="periode_akhir" class="form-control mr-2"
                                                    value="<?= $periode_akhir ?? date('Y-12-31'); ?>">
                                                <button type="button" id="btn-sesuaikan-periode" class="btn btn-primary ml-2">
                                                    Terapkan
                                                </button>
                                            </div>
                                        </div>
                                        <p class="text-dark font-weight-medium"><b>Unit Kantor Penilai:</b> <span class="text-dark"><?= $pegawai_detail->unit_kerja; ?> <?= $pegawai_detail->unit_kantor ?? '-'; ?></span></p>
                                    </div>
                                </div>

                                <hr>

                                <!-- Penilai I & Penilai II -->
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <h5 class="text-info font-weight-bold mb-3">
                                            <i class="mdi mdi-account-check-outline mr-2"></i>Penilai I
                                        </h5>
                                        <ul class="list-group list-group-flush">
                                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                                <span class="text-dark font-weight-medium">NIK</span>
                                                <span class="badge badge-info badge-pill"><?= $pegawai_detail->penilai1_nik ?? '-'; ?></span>
                                            </li>
                                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                                <span class="text-dark font-weight-medium">Nama</span>
                                                <span class="text-dark"><?= $pegawai_detail->penilai1_nama ?? '-'; ?></span>
                                            </li>
                                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                                <span class="text-dark font-weight-medium">Jabatan</span>
                                                <span class="text-dark"><?= $pegawai_detail->penilai1_jabatan ?? '-'; ?></span>
                                            </li>
                                        </ul>
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <h5 class="text-warning font-weight-bold mb-3">
                                            <i class="mdi mdi-account-check-outline mr-2"></i>Penilai II
                                        </h5>
                                        <ul class="list-group list-group-flush">
                                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                                <span class="text-dark font-weight-medium">NIK</span>
                                                <span class="badge badge-warning badge-pill"><?= $pegawai_detail->penilai2_nik ?? '-'; ?></span>
                                            </li>
                                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                                <span class="text-dark font-weight-medium">Nama</span>
                                                <span class="text-dark"><?= $pegawai_detail->penilai2_nama ?? '-'; ?></span>
                                            </li>
                                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                                <span class="text-dark font-weight-medium">Jabatan</span>
                                                <span class="text-dark"><?= $pegawai_detail->penilai2_jabatan ?? '-'; ?></span>
                                            </li>
                                        </ul>
                                    </div>
                                </div>

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
                    foreach ($arr as $items)
                        $sum += count($items);
                    return $sum;
                }
                ?>

                <!-- Tabel Penilaian -->
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <h5>Form Penilaian Sasaran Kerja</h5>
                                <div class="table-responsive">
                                    <table class="table table-bordered" id="tabel-penilaian">
                                        <thead style="background-color:#2E7D32;color:#fff;text-align:center;">
                                            <tr>
                                                <th>Perspektif</th>
                                                <th>Sasaran Kerja</th>
                                                <th class="text-center" style="width: 80px;">Bobot (%)</th>
                                                <th>Indikator</th>
                                                <th class="text-center" style="width: 120px;">Target</th>
                                                <th class="text-center" style="width: 80px;">Batas Waktu</th>
                                                <th class="text-center" style="width: 120px;">Realisasi</th>
                                                <th class="text-center" style="width: 120px;">Pencapaian (%)</th>
                                                <th class="text-center" style="width: 120px;">Nilai</th>
                                                <th class="text-center" style="width: 120px;">Nilai Dibobot</th>
                                                <th class="text-center" style="width: 100px;">Status</th>
                                                <th class="text-center" style="width: 100px;">Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $printed_any = false;
                                            foreach ($order as $persp) {
                                                if (empty($grouped[$persp]))
                                                    continue;
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

                                                        $status = strtolower(trim($i->status ?? ''));

                                                        $statusClass = 'badge badge-danger';
                                                        $statusText  = 'Belum Dinilai';

                                                        switch ($status) {
                                                            case 'ada catatan':
                                                                $statusClass = 'badge badge-warning';
                                                                $statusText  = 'Ada Catatan';
                                                                break;
                                                            case 'disetujui':
                                                                $statusClass = 'badge badge-success';
                                                                $statusText  = 'Disetujui';
                                                                break;
                                                        }
                                            ?>
                                                        <tr data-id="<?= $id; ?>" data-bobot="<?= $bobot; ?>"
                                                            data-perspektif="<?= $persp; ?>" data-indikator="<?= htmlspecialchars($indik, ENT_QUOTES, 'UTF-8'); ?>">
                                                            <?php if ($first_persp_cell) { ?>
                                                                <td rowspan="<?= $persp_rows; ?>"
                                                                    style="vertical-align:middle;font-weight:600;background:#C8E6C9;">
                                                                    <?= $persp; ?></td>
                                                            <?php $first_persp_cell = false;
                                                            } ?>

                                                            <?php if ($first_sas_cell) { ?>
                                                                <td rowspan="<?= $sasaran_rows; ?>"
                                                                    style="vertical-align:middle;background:#E3F2FD;"><?= $sasaran; ?></td>
                                                            <?php $first_sas_cell = false;
                                                            } ?>

                                                            <td class="text-center align-middle"><?= $bobot; ?>
                                                                <input type="hidden" class="bobot" value="<?= $bobot ?>">
                                                            </td>
                                                            <td><?= $indik; ?></td>

                                                            <!-- Target -->
                                                            <td class="text-center align-middle">
                                                                <input type="text"
                                                                    class="form-control target-input text-center"
                                                                    style="min-width:120px;"
                                                                    value="<?= $i->target ?? ''; ?>">
                                                            </td>

                                                            <td class="text-center align-middle"><input type="date" class="form-control" style="min-width:120px;"
                                                                    value="<?= $i->batas_waktu ?? ''; ?>"></td>

                                                            <td class="text-center align-middle">
                                                                <input type="text"
                                                                    class="form-control realisasi-input text-center"
                                                                    style="min-width:120px;"
                                                                    value="<?= $i->realisasi ?? ''; ?>">
                                                            </td>

                                                            <td class="text-center align-middle"><input type="text" style="min-width:60px;"
                                                                    class="form-control form-control-sm text-center pencapaian-output" readonly>
                                                            </td>
                                                            <td class="text-center align-middle"><input type="text" style="min-width:50px;"
                                                                    class="form-control form-control-sm text-center nilai-output" readonly></td>
                                                            <td class="text-center align-middle"><input type="text"
                                                                    class="form-control form-control-sm text-center nilai-bobot-output" readonly>
                                                            </td>

                                                            <td class="text-center align-middle">
                                                                <span class="<?= $statusClass; ?>"><?= $statusText; ?></span>
                                                            </td>
                                                            <td class="text-center align-middle">
                                                                <button type="button"
                                                                    class="btn btn-sm btn-primary simpan-penilaian">Simpan</button>
                                                            </td>
                                                        </tr>
                                                <?php
                                                    }
                                                }
                                                ?>
                                                <tr class="subtotal-row" data-perspektif="<?= $persp; ?>"
                                                    style="font-weight:bold;background:#F1F8E9;">
                                                    <td colspan="2">Sub Total Bobot <?= $persp; ?></td>
                                                    <td class="text-center"><span
                                                            class="subtotal-bobot"><?= $subtotal_bobot_perspektif; ?></span>
                                                    </td>
                                                    <td colspan="6" class="text-center">Sub Total Nilai <?= $persp; ?> Dibobot
                                                    </td>
                                                    <td class="text-center"><span class="subtotal-nilai-bobot">0.00</span></td>
                                                    <td colspan="2"></td>
                                                </tr>
                                            <?php
                                            }
                                            if (!$printed_any) { ?>
                                                <tr>
                                                    <td colspan="12" class="text-center">Tidak ada indikator untuk jabatan ini
                                                    </td>
                                                </tr>
                                            <?php } ?>
                                        </tbody>
                                        <tfoot
                                            style="background-color:#2E7D32;color:#fff;font-weight:bold;text-align:center;">
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
                <!-- Form Penilaian Budaya -->
                <div class="row mt-4">
                    <div class="col-12">
                        <div class="card shadow-sm border-0">
                            <div class="card-body">
                                <h5 class="text-success font-weight-bold mb-3">
                                    <i class="mdi mdi-account-star-outline mr-2"></i> Form Penilaian Budaya
                                </h5>

                                <div class="table-responsive">
                                    <table class="table table-bordered" id="tabel-penilaian-budaya">
                                        <thead style="background-color:#2E7D32;color:#fff;font-weight:bold;text-align:center;">
                                            <tr>
                                                <th style="width:50px;">No</th>
                                                <th>Budaya Kerja</th>
                                                <th style="width:120px;" class="text-center">Nilai (0-5)</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $budaya_list = [
                                                "Jujur dan berkomitmen",
                                                "Bekerja sama, saling menghargai dan mendukung",
                                                "Berpikir positif",
                                                "Peduli, proaktif dan cepat tanggap",
                                                "Tulus ikhlas",
                                                "Berorientasi pada solusi terbaik",
                                                "Kompeten dan bertanggung jawab",
                                                "Bekerja cerdas, efektif dan efisien",
                                                "Kreatif, inovatif dan bernilai tambah",
                                                "Memberikan hasil terbaik"
                                            ];
                                            $no = 1;
                                            foreach ($budaya_list as $b): ?>
                                                <tr>
                                                    <td class="text-center"><?= $no++; ?></td>
                                                    <td><?= $b; ?></td>
                                                    <td class="text-center">
                                                        <input type="number" step="0.1" min="0" max="5"
                                                            class="form-control form-control-sm text-center budaya"
                                                            name="budaya[]" value="">
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                        <tfoot style="background-color:#2E7D32;color:#fff;font-weight:bold;text-align:center;">
                                            <tr>
                                                <td colspan="2" class="text-right align-middle">Rata-Rata Nilai Internalisasi Budaya</td>
                                                <td class="text-center">
                                                    <input type="text" id="rata-rata-budaya"
                                                        class="form-control form-control-sm text-center" readonly>
                                                </td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <script>
                    // hitung otomatis rata-rata budaya
                    document.querySelectorAll('.budaya').forEach(input => {
                        input.addEventListener('input', function() {
                            let total = 0,
                                count = 0;
                            document.querySelectorAll('.budaya').forEach(i => {
                                let val = parseFloat(i.value);
                                if (!isNaN(val)) {
                                    total += val;
                                    count++;
                                }
                            });
                            let rata = count > 0 ? (total / count).toFixed(2) : 0;
                            document.getElementById('rata-rata-budaya').value = rata;
                            // update ke bagian Nilai Akhir juga kalau perlu
                            document.getElementById('rata-budaya').innerText = rata;

                            hitungNilaiAkhir();
                        });
                    });
                </script>

                <!-- Nilai Akhir & Catatan -->
                <div class="card mt-4">
                    <div class="card-body">
                        <h5 class="text-success font-weight-bold mb-3">
                            <i class="mdi mdi-star-circle mr-2"></i> Nilai Akhir (q)
                        </h5>

                        <!-- Bagian Atas: Perhitungan -->
                        <table class="table table-bordered mb-4">
                            <tr>
                                <th>Total Nilai Sasaran Kerja</th>
                                <td class="text-center" id="total-sasaran">
                                    <?= $nilai_akhir['total_sasaran'] ?? 0 ?>
                                </td>
                                <td>x Bobot % Sasaran Kerja</td>
                                <td>
                                    <input type="text" id="bobot-sasaran"
                                        class="form-control form-control-sm text-center"
                                        value="95%" readonly>
                                </td>
                                <td class="text-center" id="nilai-sasaran">
                                    <?= $nilai_akhir['nilai_sasaran'] ?? 0 ?>
                                </td>
                            </tr>
                            <tr>
                                <th>Rata-rata Nilai Internalisasi Budaya</th>
                                <td class="text-center" id="rata-budaya">
                                    <?= $nilai_akhir['rata_budaya'] ?? '-' ?>
                                </td>
                                <td>x Bobot % Budaya Perusahaan</td>
                                <td>
                                    <input type="text" id="bobot-budaya"
                                        class="form-control form-control-sm text-center"
                                        value="5%" readonly>
                                </td>
                                <td class="text-center" id="nilai-budaya">
                                    <?= $nilai_akhir['nilai_budaya'] ?? '-' ?>
                                </td>
                            </tr>
                            <tr>
                                <th colspan="4" class="text-right">Total Nilai</th>
                                <td class="text-center" id="total-nilai">
                                    <?= $nilai_akhir['total_nilai'] ?? 0 ?>
                                </td>
                            </tr>
                            <tr>
                                <th colspan="4" class="text-right">
                                    Fraud<br>
                                    <small>(diisi 1 jika melakukan fraud, 0 jika tidak)</small>
                                </th>
                                <td>
                                    <input type="number" min="0" max="1"
                                        class="form-control form-control-sm text-center"
                                        id="fraud-input"
                                        value="<?= $nilai_akhir['fraud'] ?? 0 ?>">
                                </td>
                            </tr>
                        </table>

                        <!-- Bagian Bawah: Kiri-Kanan -->
                        <div class="row">
                            <div class="col-md-6">
                                <!-- Tabel Predikat -->
                                <table class="table table-bordered text-center">
                                    <thead class="bg-success text-white">
                                        <tr>
                                            <th>Nilai Akhir</th>
                                            <th>Yudisium / Predikat</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>&ge; 4.50 - 5</td>
                                            <td><b>Excellent (E)</b></td>
                                        </tr>
                                        <tr>
                                            <td>3.50 - &lt; 4.50</td>
                                            <td><b>Very Good (VG)</b></td>
                                        </tr>
                                        <tr>
                                            <td>3.00 - &lt; 3.50</td>
                                            <td><b>Good (G)</b></td>
                                        </tr>
                                        <tr>
                                            <td>2.00 - &lt; 3.00</td>
                                            <td><b>Fair (F)</b></td>
                                        </tr>
                                        <tr>
                                            <td>&lt; 2.00</td>
                                            <td><b>Minus (M)</b></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                            <div class="col-md-6">
                                <!-- Nilai Akhir & Pencapaian -->
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="card text-center mb-3">
                                            <div class="card-header bg-success text-white">Nilai Akhir</div>
                                            <div class="card-body">
                                                <h3 id="nilai-akhir">
                                                    <?= $nilai_akhir['nilai_akhir'] ?? 0 ?>
                                                </h3>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="card text-center">
                                            <div class="card-header bg-success text-white">Pencapaian Akhir</div>
                                            <div class="card-body">
                                                <h3 id="pencapaian-akhir">
                                                    <?= $nilai_akhir['pencapaian'] ?? '-' ?>
                                                </h3>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Predikat -->
                                <div class="card text-center mb-3">
                                    <div class="card-header bg-success text-white">Yudisium / Predikat</div>
                                    <div class="card-body">
                                        <h3 id="predikat">
                                            <?= $nilai_akhir['predikat'] ?? '-' ?>
                                        </h3>
                                    </div>
                                </div>

                                <div class="text-right mt-3">
                                    <button id="btn-simpan-nilai-akhir" class="btn btn-primary">
                                        <i class="mdi mdi-content-save"></i> Simpan Nilai Akhir
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row mt-3">
                    <!-- Catatan Penilai -->
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-body">
                                <h5>Catatan Penilai</h5>
                                <div class="table-responsive">
                                    <table id="tabel-catatan" class="table table-bordered table-striped">
                                        <thead>
                                            <tr>
                                                <th>No</th>
                                                <th>Penilai</th>
                                                <th>Catatan</th>
                                                <th>Tanggal</th>
                                            </tr>
                                        </thead>
                                        <tbody></tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Catatan Pegawai -->
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-body">
                                <h5>Catatan Pegawai</h5>
                                <div class="table-responsive">
                                    <table id="tabel-catatan-pegawai" class="table table-bordered table-striped">
                                        <thead>
                                            <tr>
                                                <th>No</th>
                                                <th>Catatan</th>
                                                <th>Tanggal</th>
                                            </tr>
                                        </thead>
                                        <tbody></tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php } ?>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<?php if (isset($message) && !empty($message)): ?>
    <script>
        Swal.fire({
            icon: '<?= $message['type']; ?>',
            title: 'Informasi',
            text: '<?= $message['text']; ?>',
            confirmButtonColor: '#2E7D32'
        });
    </script>
<?php endif; ?>


<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const nik = document.getElementById('nik')?.value;
        const periodeAwal = document.getElementById('periode_awal');
        const periodeAkhir = document.getElementById('periode_akhir');

        // 🔹 Set default value (jaga-jaga kalau value di HTML kosong)
        if (!periodeAwal.value) periodeAwal.value = "2025-01-01";
        if (!periodeAkhir.value) periodeAkhir.value = "2025-12-31";

        // 🔹 Validasi supaya periode akhir tidak lebih kecil dari awal
        periodeAwal.addEventListener('change', function() {
            if (periodeAkhir.value < this.value) periodeAkhir.value = this.value;
        });
        periodeAkhir.addEventListener('change', function() {
            if (this.value < periodeAwal.value) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Periode salah',
                    text: 'Tanggal akhir tidak boleh lebih kecil dari tanggal awal',
                    confirmButtonColor: '#d33'
                });
                this.value = periodeAwal.value;
            }
        });


        // 🔹 format angka
        function formatAngka(nilai) {
            let num = parseFloat(nilai);
            if (isNaN(num)) return '';
            return Number.isInteger(num) ? num.toString() : num.toFixed(2);
        }

        function hitungPencapaianOtomatis(target, realisasi, indikatorText = "") {
            let pencapaian = 0;

            // Normalisasi teks
            indikatorText = indikatorText.toLowerCase();

            // 🔹 Daftar keyword
            const keywords = {
                rumus1: ["biaya", "beban", "efisiensi", "npf pembiayaan", "npf nominal"], // indikator biaya / beban
                rumus3: ["outstanding", "pertumbuhan"] // indikator outstanding / pertumbuhan
            };

            // Fungsi cek keyword pakai regex \b...\b
            const containsKeyword = (list, text) => {
                return list.some(k => new RegExp(`\\b${k}\\b`, "i").test(text));
            };

            if (target <= 999) {
                // 🔹 Rumus 2 (default untuk target ≤ 3 digit)
                pencapaian = (realisasi / target) * 100;
            } else {
                // 🔹 Target > 3 digit → pilih rumus 1 atau 3 berdasarkan kata kunci indikator
                if (containsKeyword(keywords.rumus1, indikatorText)) {
                    // Rumus 1 → biasanya indikator biaya/beban
                    pencapaian = ((target + (target - realisasi)) / target) * 100;
                } else if (containsKeyword(keywords.rumus3, indikatorText)) {
                    // Rumus 3 → biasanya indikator outstanding/pertumbuhan
                    pencapaian = ((realisasi - target) / Math.abs(target) + 1) * 100;
                } else {
                    // fallback default (anggap rumus 2)
                    pencapaian = (realisasi / target) * 100;
                }
            }
            // 🔹 Batas maksimal 130%
            return Math.min(pencapaian, 130);
        }


        function hitungNilai(pencapaian) {
            let nilai = 0;

            if (pencapaian < 0) {
                nilai = 0;
            } else if (pencapaian < 80) {
                nilai = (pencapaian / 80) * 2;
            } else if (pencapaian < 90) {
                nilai = 2 + ((pencapaian - 80) / 10);
            } else if (pencapaian < 110) {
                nilai = 3 + ((pencapaian - 90) / 20 * 0.5);
            } else if (pencapaian < 120) {
                nilai = 3.5 + ((pencapaian - 110) / 10 * 1);
            } else if (pencapaian < 130) {
                nilai = 4.5 + ((pencapaian - 120) / 10 * 0.5);
            } else {
                nilai = 5;
            }

            return nilai;
        }


        function hitungRow(row, totalBobot) {
            const targetVal = row.querySelector('.target-input').value;
            const realisasiVal = row.querySelector('.realisasi-input').value;
            const bobot = parseFloat(row.querySelector('.bobot').value) || 0;

            // 🔹 Ambil teks indikator dari atribut data-indikator
            const indikatorText = row.dataset.indikator || "";

            let pencapaian = "";
            let nilai = "";
            let nilaiBobot = "";

            if (targetVal !== "" && realisasiVal !== "") {
                const target = parseFloat(targetVal) || 0;
                const realisasi = parseFloat(realisasiVal) || 0;

                pencapaian = hitungPencapaianOtomatis(target, realisasi, indikatorText);
                nilai = hitungNilai(pencapaian);

                if (totalBobot > 0) {
                    nilaiBobot = (nilai * bobot) / totalBobot;
                }
            }

            row.querySelector('.pencapaian-output').value = pencapaian === "" ? "" : formatAngka(pencapaian);
            row.querySelector('.nilai-output').value = nilai === "" ? "" : formatAngka(nilai);
            row.querySelector('.nilai-bobot-output').value = nilaiBobot === "" ? "" : formatAngka(nilaiBobot);

            return {
                bobot,
                nilaiBobot: nilaiBobot === "" ? 0 : parseFloat(formatAngka(nilaiBobot)), // ← bulatkan per baris
                perspektif: row.dataset.perspektif
            };
        }

        function hitungTotal() {
            let totalBobot = 0,
                totalNilai = 0;
            const subtotalMap = {};

            // 🔹 hitung total bobot dulu
            document.querySelectorAll('#tabel-penilaian tbody tr[data-id]').forEach(row => {
                totalBobot += parseFloat(row.querySelector('.bobot').value) || 0;
            });

            // 🔹 lalu panggil hitungRow dengan totalBobot
            document.querySelectorAll('#tabel-penilaian tbody tr[data-id]').forEach(row => {
                const {
                    bobot,
                    nilaiBobot,
                    perspektif
                } = hitungRow(row, totalBobot);
                totalNilai += nilaiBobot;

                if (!subtotalMap[perspektif]) subtotalMap[perspektif] = 0;
                subtotalMap[perspektif] += nilaiBobot;
            });

            document.getElementById('total-bobot').innerText = formatAngka(totalBobot);
            document.getElementById('total-nilai-bobot').innerText = formatAngka(totalNilai);

            document.querySelectorAll('.subtotal-row').forEach(row => {
                const perspektif = row.dataset.perspektif;
                row.querySelector('.subtotal-nilai-bobot').innerText = formatAngka(subtotalMap[perspektif] || 0);
            });

            // Tambahkan baris ini agar total-sasaran sama dengan total-nilai-bobot
            document.getElementById('total-sasaran').textContent = formatAngka(totalNilai);

            hitungNilaiAkhir();
        }

        function hitungNilaiAkhir() {
            const bobotSasaran = 0.95;
            const bobotBudaya = 0.05;

            const fraud = parseFloat(document.getElementById("fraud-input").value) || 0;

            // Ambil nilai sasaran dari total-nilai-bobot
            const totalSasaran = parseFloat(document.getElementById("total-nilai-bobot").textContent) || 0;
            const rataBudaya = parseFloat(document.getElementById("rata-rata-budaya").textContent) || 0;

            // Total nilai sasaran kerja 
            const nilaiSasaran = totalSasaran * bobotSasaran;

            // Nilai budaya
            const nilaiBudaya = rataBudaya * bobotBudaya;

            // Total nilai
            const totalNilai = nilaiSasaran + nilaiBudaya;

            // Nilai akhir sesuai rumus Excel
            let nilaiAkhir;
            if (fraud === 1) {
                nilaiAkhir = totalNilai - fraud;
            } else {
                nilaiAkhir = totalNilai;
            }

            // Predikat
            let predikat;
            let predikatClass = "";

            if (nilaiAkhir === "Tidak ada nilai") {
                predikat = "Tidak ada yudisium/predikat";
                predikatClass = "text-dark";
            } else if (nilaiAkhir === 0) {
                predikat = "Belum Ada Nilai";
                predikatClass = "text-dark";
            } else if (nilaiAkhir < 2) {
                predikat = "Minus";
                predikatClass = "text-danger"; // merah
            } else if (nilaiAkhir < 3) {
                predikat = "Fair";
                predikatClass = "text-warning"; // jingga
            } else if (nilaiAkhir < 3.5) {
                predikat = "Good";
                predikatClass = "text-primary"; // biru
            } else if (nilaiAkhir < 4.5) {
                predikat = "Very Good";
                predikatClass = "text-success"; // hijau muda
            } else {
                predikat = "Excellent";
                predikatClass = "text-success font-weight-bold"; // hijau tua (lebih tebal)
            }

            // Pencapaian Akhir
            let pencapaian = "";
            if (nilaiAkhir !== "Tidak ada nilai") {
                const v = parseFloat(nilaiAkhir) || 0;
                if (v < 0) pencapaian = 0;
                else if (v < 2) pencapaian = (v / 2) * 0.8 * 100;
                else if (v < 3) pencapaian = 80 + ((v - 2) / 1) * 10;
                else if (v < 3.5) pencapaian = 90 + ((v - 3) / 0.5) * 20;
                else if (v < 4.5) pencapaian = 110 + ((v - 3.5) / 1) * 10;
                else if (v < 5) pencapaian = 120 + ((v - 4.5) / 0.5) * 10;
                else pencapaian = 130;
            } else {
                pencapaian = 0;
            }

            // Update ke tampilan
            document.getElementById("nilai-sasaran").textContent = nilaiSasaran.toFixed(2);
            document.getElementById("nilai-budaya").textContent = nilaiBudaya.toFixed(2);
            document.getElementById("total-nilai").textContent = totalNilai.toFixed(2);
            document.getElementById("nilai-akhir").textContent =
                nilaiAkhir === "Tidak ada nilai" ? nilaiAkhir : nilaiAkhir.toFixed(2);
            document.getElementById("predikat").textContent = predikat;
            document.getElementById("predikat").className = predikatClass;
            document.getElementById("pencapaian-akhir").textContent =
                pencapaian === "" ? "" : pencapaian.toFixed(2) + "%";
        }

        document.getElementById('fraud-input').addEventListener('input', hitungNilaiAkhir);

        // 🔹 trigger perhitungan saat input diubah
        document.querySelectorAll('.target-input, .realisasi-input').forEach(input => {
            input.addEventListener('input', hitungTotal);
        });
        hitungTotal();

        // 🔹 Simpan penilaian
        document.querySelectorAll('.simpan-penilaian').forEach(btn => {
            btn.addEventListener('click', function() {
                const row = this.closest('tr');
                const indikator_id = row.dataset.id;
                const target = row.querySelector('.target-input').value;
                const batas_waktu = row.querySelector('input[type="date"]').value;
                const realisasi = row.querySelector('.realisasi-input').value;
                const pencapaian = row.querySelector('.pencapaian-output').value;
                const nilai = row.querySelector('.nilai-output').value;
                const nilai_dibobot = row.querySelector('.nilai-bobot-output').value;

                const periode_awal = periodeAwal.value;
                const periode_akhir = periodeAkhir.value;

                // <-- taruh console.log di sini
                console.log("DEBUG: nik=", nik, "indikator_id=", indikator_id, "periode_awal=", periode_awal, "periode_akhir=", periode_akhir);

                fetch('<?= base_url("Administrator/simpanPenilaianBaris") ?>', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded'
                        },
                        body: `nik=${nik}&indikator_id=${indikator_id}&target=${encodeURIComponent(target)}&batas_waktu=${encodeURIComponent(batas_waktu)}&realisasi=${encodeURIComponent(realisasi)}&pencapaian=${encodeURIComponent(pencapaian)}&nilai=${encodeURIComponent(nilai)}&nilai_dibobot=${encodeURIComponent(nilai_dibobot)}&periode_awal=${encodeURIComponent(periode_awal)}&periode_akhir=${encodeURIComponent(periode_akhir)}`
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
                            hitungTotal();
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
        document.getElementById('btn-sesuaikan-periode').addEventListener('click', function() {
            const nik = document.getElementById('nik').value;
            const awal = periodeAwal.value;
            const akhir = periodeAkhir.value;

            if (!nik) {
                Swal.fire({
                    icon: 'warning',
                    title: 'NIK kosong',
                    text: 'Masukkan NIK terlebih dahulu',
                    confirmButtonColor: '#d33'
                });
                return;
            }

            window.location.href = `<?= base_url("Administrator/cariPenilaian") ?>?nik=${nik}&awal=${awal}&akhir=${akhir}`;
        });
        $(document).ready(function() {
            const nikPegawai = $('#nik').val(); // NIK pegawai saat ini

            var tableCatatan = $('#tabel-catatan').DataTable({
                processing: true,
                serverSide: true,
                responsive: true,
                ajax: {
                    url: '<?= base_url("Administrator/getCatatanPenilai") ?>',
                    type: 'POST',
                    data: {
                        nik_pegawai: nikPegawai
                    }
                },
                columns: [{
                        data: 'no',
                        orderable: false
                    }, // Nomor urut
                    {
                        data: 'nama_penilai'
                    }, // Nama penilai
                    {
                        data: 'catatan',
                        orderable: false
                    }, // Catatan
                    {
                        data: 'tanggal',
                        render: function(data, type, row) {
                            if (!data) return '';
                            const date = new Date(data + ' UTC'); // pastikan server kirim UTC
                            return date.toLocaleString('id-ID', {
                                day: '2-digit',
                                month: '2-digit',
                                year: 'numeric',
                                hour: '2-digit',
                                minute: '2-digit',
                                hour12: false,
                                timeZone: 'Asia/Jakarta'
                            });
                        }
                    }
                ],
                order: [
                    [3, 'desc']
                ], // urut terbaru di atas
                paging: true,
                searching: true,
                info: true,
                language: {
                    search: "Cari:",
                    lengthMenu: "Tampilkan _MENU_ baris",
                    info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ catatan",
                    infoEmpty: "Menampilkan 0 sampai 0 dari 0 catatan",
                    zeroRecords: "Tidak ada catatan yang ditemukan",
                    paginate: {
                        first: "Pertama",
                        last: "Terakhir",
                        next: "Berikut",
                        previous: "Sebelumnya"
                    }
                },
                dom: '<"row mb-2"<"col-md-6"l><"col-md-6 text-right"f>>rt<"row mt-2"<"col-md-6"i><"col-md-6 d-flex justify-content-end"p>>',
                drawCallback: function(settings) {
                    // nomor urut otomatis 1 -> n
                    var api = this.api();
                    api.column(0, {
                        order: 'applied'
                    }).nodes().each(function(cell, i) {
                        cell.innerHTML = i + 1;
                    });
                }
            });
        });

        $(document).ready(function() {
            const nikPegawai = $('#nik').val(); // NIK pegawai saat ini

            var tableCatatanPegawai = $('#tabel-catatan-pegawai').DataTable({
                processing: true,
                serverSide: true,
                responsive: false,
                ajax: {
                    url: '<?= base_url("Administrator/getCatatanPegawai") ?>',
                    type: 'POST',
                    data: {
                        nik_pegawai: nikPegawai
                    }
                },
                columns: [{
                        data: 'no',
                        orderable: false
                    }, // Nama penilai
                    {
                        data: 'catatan',
                        orderable: false
                    }, // Catatan
                    {
                        data: 'tanggal',
                        render: function(data, type, row) {
                            if (!data) return '';
                            const date = new Date(data + ' UTC'); // pastikan server kirim UTC
                            return date.toLocaleString('id-ID', {
                                day: '2-digit',
                                month: '2-digit',
                                year: 'numeric',
                                hour: '2-digit',
                                minute: '2-digit',
                                hour12: false,
                                timeZone: 'Asia/Jakarta'
                            });
                        }
                    }
                ],
                order: [
                    [2, 'desc']
                ], // urut terbaru di atas
                paging: true,
                searching: true,
                info: true,
                language: {
                    search: "Cari:",
                    lengthMenu: "Tampilkan _MENU_ baris",
                    info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ catatan",
                    infoEmpty: "Menampilkan 0 sampai 0 dari 0 catatan",
                    zeroRecords: "Tidak ada catatan yang ditemukan",
                    paginate: {
                        first: "Pertama",
                        last: "Terakhir",
                        next: "Berikut",
                        previous: "Sebelumnya"
                    }
                },
                dom: '<"row mb-2"<"col-md-6"l><"col-md-6 text-right"f>>rt<"row mt-2"<"col-md-6"i><"col-md-6 d-flex justify-content-end"p>>',
                drawCallback: function(settings) {
                    // nomor urut otomatis 1 -> n
                    var api = this.api();
                    api.column(0, {
                        order: 'applied'
                    }).nodes().each(function(cell, i) {
                        cell.innerHTML = i + 1;
                    });
                }
            });
        });


    });

    document.addEventListener('DOMContentLoaded', function() {
        const periodeSelect = document.getElementById('periode_select');
        const periodeManual = document.getElementById('periode_manual');
        const periodeAwal = document.getElementById('periode_awal');
        const periodeAkhir = document.getElementById('periode_akhir');
        const nik = document.getElementById('nik')?.value;

        // toggle form manual + auto refresh jika pilih periode lama
        periodeSelect.addEventListener('change', function() {
            if (this.value === "baru") {
                // tampilkan input manual
                periodeManual.style.display = "block";
            } else {
                periodeManual.style.display = "none";
                if (this.value && nik) {
                    const [awal, akhir] = this.value.split('|');
                    window.location.href = `<?= base_url("Administrator/cariPenilaian") ?>?nik=${nik}&awal=${awal}&akhir=${akhir}`;
                }
            }
        });

        // tombol manual tetap untuk periode baru
        document.getElementById('btn-sesuaikan-periode').addEventListener('click', function() {
            if (!nik) {
                Swal.fire({
                    icon: 'warning',
                    title: 'NIK kosong',
                    text: 'Masukkan NIK terlebih dahulu',
                    confirmButtonColor: '#d33'
                });
                return;
            }
            const awal = periodeAwal.value;
            const akhir = periodeAkhir.value;
            window.location.href = `<?= base_url("Administrator/cariPenilaian") ?>?nik=${nik}&awal=${awal}&akhir=${akhir}`;
        });

        document.getElementById('btn-simpan-nilai-akhir').addEventListener('click', function() {
            const nik = document.getElementById('nik').value;
            const periode_awal = document.getElementById('periode_awal').value;
            const periode_akhir = document.getElementById('periode_akhir').value;

            // 1️⃣ Simpan semua baris dulu
            const rows = document.querySelectorAll('#tabel-penilaian tbody tr[data-id]');
            let promises = [];

            rows.forEach(row => {
                const indikator_id = row.dataset.id;
                const target = row.querySelector('.target-input').value;
                const batas_waktu = row.querySelector('input[type="date"]').value;
                const realisasi = row.querySelector('.realisasi-input').value;
                const pencapaian = row.querySelector('.pencapaian-output').value;
                const nilai = row.querySelector('.nilai-output').value;
                const nilai_dibobot = row.querySelector('.nilai-bobot-output').value;

                let formData = `nik=${nik}&indikator_id=${indikator_id}&target=${encodeURIComponent(target)}&batas_waktu=${encodeURIComponent(batas_waktu)}&realisasi=${encodeURIComponent(realisasi)}&pencapaian=${encodeURIComponent(pencapaian)}&nilai=${encodeURIComponent(nilai)}&nilai_dibobot=${encodeURIComponent(nilai_dibobot)}&periode_awal=${encodeURIComponent(periode_awal)}&periode_akhir=${encodeURIComponent(periode_akhir)}`;

                promises.push(
                    fetch('<?= base_url("Administrator/simpanPenilaianBaris") ?>', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded'
                        },
                        body: formData
                    }).then(res => res.json())
                );
            });

            // 2️⃣ Setelah semua baris berhasil disimpan, simpan nilai akhir
            Promise.all(promises).then(results => {
                // kalau ada yang gagal, stop
                if (results.some(r => r.status !== 'success')) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal',
                        text: 'Ada baris yang gagal disimpan. Cek kembali inputnya.',
                        confirmButtonColor: '#d33'
                    });
                    return;
                }

                // ambil nilai akhir
                const nilai_sasaran = document.getElementById('total-sasaran').textContent;
                const nilai_budaya = document.getElementById('nilai-budaya').textContent;
                const total_nilai = document.getElementById('total-nilai').textContent;
                const fraud = document.getElementById('fraud-input').value;
                const nilai_akhir = document.getElementById('nilai-akhir').textContent;
                const predikat = document.getElementById('predikat').textContent;
                const pencapaian = document.getElementById('pencapaian-akhir').textContent;

                fetch('<?= base_url("Administrator/simpanNilaiAkhir") ?>', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded'
                        },
                        body: `nik=${encodeURIComponent(nik)}&periode_awal=${encodeURIComponent(periode_awal)}&periode_akhir=${encodeURIComponent(periode_akhir)}&nilai_sasaran=${encodeURIComponent(nilai_sasaran)}&nilai_budaya=${encodeURIComponent(nilai_budaya)}&total_nilai=${encodeURIComponent(total_nilai)}&fraud=${encodeURIComponent(fraud)}&nilai_akhir=${encodeURIComponent(nilai_akhir)}&pencapaian=${encodeURIComponent(pencapaian)}&predikat=${encodeURIComponent(predikat)}`
                    })
                    .then(res => res.json())
                    .then(res => {
                        if (res.status === 'success') {
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil',
                                text: 'Semua nilai berhasil disimpan',
                                timer: 2000,
                                showConfirmButton: false
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal',
                                text: res.message || 'Gagal menyimpan nilai akhir',
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

<script>
    // ===============================
    // 🔐 LOCK INPUT GLOBAL
    // ===============================
    const lockCheckbox = document.getElementById('lock_input_checkbox');
    const periodeAwal = document.getElementById('hidden_periode_awal');
    const periodeAkhir = document.getElementById('hidden_periode_akhir');

    // ⏳ Saat halaman pertama kali dibuka, cek status lock dari server
    document.addEventListener('DOMContentLoaded', function() {
        fetch(`<?= base_url("Administrator/getLockStatus") ?>?awal=${periodeAwal.value}&akhir=${periodeAkhir.value}`)
            .then(res => res.json())
            .then(data => {
                if (data.locked) {
                    lockCheckbox.checked = true;
                    toggleInputLock(true);
                } else {
                    lockCheckbox.checked = false;
                    toggleInputLock(false);
                }
            });
    });

    // 🎯 Saat checkbox diubah (Lock / Unlock)
    lockCheckbox.addEventListener('change', function() {
        const isLocked = this.checked ? 1 : 0;

        fetch('<?= base_url("Administrator/setLockStatus") ?>', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: `periode_awal=${periodeAwal.value}&periode_akhir=${periodeAkhir.value}&lock_input=${isLocked}`
            })
            .then(res => res.json())
            .then(data => {
                if (data.status === 'success') {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil',
                        text: isLocked ? 'Periode berhasil dikunci.' : 'Kunci periode telah dibuka.',
                        timer: 2000,
                        showConfirmButton: false
                    });
                    toggleInputLock(isLocked);
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal',
                        text: 'Tidak dapat mengubah status kunci.',
                    });
                    // Kembalikan checkbox ke posisi sebelumnya
                    lockCheckbox.checked = !this.checked;
                }
            })
            .catch(() => {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Terjadi kesalahan koneksi ke server.'
                });
                lockCheckbox.checked = !this.checked;
            });
    });

    // 🔒 Fungsi untuk mengunci / membuka semua input
    function toggleInputLock(lock) {
        document.querySelectorAll('.target-input, input[type="date"], .realisasi-input, .simpan-penilaian, #btn-simpan-nilai-akhir')
            .forEach(el => {
                el.disabled = lock;
            });
    }


    // ===============================
    // 🔄 GANTI PERIODE DENGAN ALERT
    // ===============================
    document.getElementById('periode_select').addEventListener('change', function() {
        const val = this.value;
        const manualDiv = document.getElementById('periode_manual');

        if (val === 'baru') {
            // Jika user pilih input manual
            manualDiv.style.display = 'block';
        } else if (val) {
            // Jika user pilih periode dari dropdown
            manualDiv.style.display = 'none';
            const [awal, akhir] = val.split('|');

            Swal.fire({
                title: 'Ganti Periode?',
                text: `Kamu akan beralih ke periode ${awal} s/d ${akhir}.`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Ya, ganti!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Refresh halaman dengan query periode agar status lock ikut update
                    window.location.href = `<?= base_url('Administrator/penilaiankinerja') ?>?awal=${awal}&akhir=${akhir}`;
                } else {
                    // Jika dibatalkan, kembalikan dropdown ke nilai sebelumnya
                    this.value = "<?= $periode_awal . '|' . $periode_akhir ?>";
                }
            });
        }
    });

    document.addEventListener('DOMContentLoaded', function() {
        // Ambil periode dari query string atau value hidden PHP
        const urlParams = new URLSearchParams(window.location.search);
        let awal = urlParams.get('awal') || '<?= $periode_awal ?>';
        let akhir = urlParams.get('akhir') || '<?= $periode_akhir ?>';

        // Format tanggal menjadi d M Y
        function formatTanggal(tgl) {
            const date = new Date(tgl);
            const options = {
                day: '2-digit',
                month: 'short',
                year: 'numeric'
            };
            return date.toLocaleDateString('id-ID', options);
        }

        document.getElementById('periode_text').value = formatTanggal(awal) + ' s/d ' + formatTanggal(akhir);
    });
</script>