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
                                <li class="breadcrumb-item active">Penilaian KPI</li>
                            </ol>
                        </div>
                        <h4 class="page-title"><i class="mdi mdi-account-edit mr-2 text-primary"></i> Penilaian Key Performance Indicator (KPI)</h4>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12 col-md-6">
                    <div class="card">
                        <div class="card-body">
                            <h5>Pilih Unit Kerja & Pegawai</h5>
                            <form action="<?= base_url('administrator_renstra/lihatPenilaianRenstra'); ?>" method="post">
                                <!-- Dropdown Unit Kerja -->
                                <select name="unit_kerja" id="unit_kerja" class="form-control mb-2" required>
                                    <option value="">-- Pilih Unit Kerja --</option>
                                    <?php foreach ($unit_kerja as $u): ?>
                                        <option value="<?= $u->unit_kerja ?>"><?= $u->unit_kerja ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <!-- Dropdown Pegawai -->
                                <select name="nik" id="pegawai" class="form-control mb-2" required>
                                    <option value="">-- Pilih Pegawai --</option>
                                </select>
                                <button type="submit" class="btn btn-success mt-2 w-100">Lihat Penilaian</button>
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
                                            <label class="text-dark font-weight-medium">Pilih Periode Penilaian:</label>
                                            <select id="periode_select" class="form-control mb-2">
                                                <option value="">-- Pilih Periode --</option>
                                                <?php if (!empty($periode_list)): ?>
                                                    <?php foreach ($periode_list as $p):
                                                        $val = $p->periode_awal . "|" . $p->periode_akhir;
                                                        $text = date('d M Y', strtotime($p->periode_awal)) . " s/d " . date('d M Y', strtotime($p->periode_akhir));
                                                        $selected = ($periode_awal == $p->periode_awal && $periode_akhir == $p->periode_akhir) ? 'selected' : '';
                                                    ?>
                                                        <option value="<?= $val ?>" <?= $selected ?>><?= $text ?></option>
                                                    <?php endforeach; ?>
                                                <?php endif; ?>
                                                <option value="baru">+ Tambah Periode Baru</option>
                                            </select>
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
                        $s = $row->sasaran_kpi ?? '';
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
                                        <thead style="background-color:#B71C1C;color:#fff;text-align:center;">
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
                                                        $id = $i->id_indikator;
                                                        $bobot = $i->bobot ?? 0;
                                                        $indik = $i->nama_indikator ?? '';
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
                                            style="background-color:#B71C1C;color:#fff;font-weight:bold;text-align:center;">
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
                                        value="100%" readonly>
                                </td>
                                <td class="text-center" id="nilai-sasaran">
                                    <?= $nilai_akhir['nilai_sasaran'] ?? 0 ?>
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
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>


<script>
    $(document).ready(function() {
        // Load pegawai sesuai unit kerja
        $('#unit_kerja').change(function() {
            var unit = $(this).val();
            if (unit) {
                $.get("<?= base_url('Administrator_Renstra/getPegawaiByUnit'); ?>", {
                    unit_kerja: unit
                }, function(data) {
                    $('#pegawai').empty().append('<option value="">-- Pilih Pegawai --</option>');
                    $.each(data, function(i, p) {
                        $('#pegawai').append('<option value="' + p.nik + '">' + p.nama + ' - ' + p.jabatan + ' - ' + p.unit_kantor + '</option>');
                    });
                }, 'json');
            } else {
                $('#pegawai').html('<option value="">-- Pilih Pegawai --</option>');
            }
            // reset detail pegawai lama
            $('#detail-pegawai').html('');
        });

        // Load detail pegawai
        $('#btn-lihat-penilaian').click(function(e) {
            e.preventDefault();
            var nik = $('#pegawai').val();
            if (!nik) return alert('Pilih pegawai dulu');
            $.post("<?= base_url('Administrator_Renstra/lihatPenilaianRenstra'); ?>", {
                    nik: nik,
                    unit_kerja: $('#unit_kerja').val()
                },
                function(html) {
                    $('#detail-pegawai').html(html);
                });
        });
    });
</script>