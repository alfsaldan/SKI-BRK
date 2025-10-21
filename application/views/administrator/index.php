<!-- ============================================================== -->
<!-- Start Page Content here -->
<!-- ============================================================== -->

<div class="content-page">
    <div class="content">

        <!-- Start Content-->
        <div class="container-fluid">

            <!-- start page title -->
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box">
                        <div class="page-title-right">
                            <ol class="breadcrumb m-0">
                                <li class="breadcrumb-item"><a href="javascript:void(0);">KPI Online-BRKS</a></li>
                                <li class="breadcrumb-item active">Dashboard</li>
                            </ol>
                        </div>
                        <h5 class="page-title">Selamat Datang, <b>Administrator</b>!</h5>
                        <p class="text-muted">
                        <h5>Sistem Penilaian Kinerja Insani PT Bank Riau Kepri Syariah</h5>
                        </p>
                    </div>
                </div>
            </div>
            <!-- end page title -->

            <!-- Filter Periode (Popup Floating) -->
            <div class="d-flex justify-content-end mb-3">
                <button id="btn_show_filter" class="btn btn-outline-secondary btn-sm">
                    <i class="mdi mdi-tune"></i> Filter Periode
                </button>
            </div>

            <!-- Popup Filter -->
            <div id="filter_popup" class="card shadow p-3"
                style="position:absolute; top:120px; right:40px; width:380px; display:none; z-index:999;">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h6 class="mb-0"><i class="mdi mdi-calendar-range"></i> Pilih Periode</h6>
                    <button id="btn_close_filter" class="btn btn-sm btn-light">
                        <i class="mdi mdi-close"></i>
                    </button>
                </div>
                <select id="filter_periode" class="form-control mb-3">
                    <?php if (!empty($periode_list)): ?>
                        <option value="">Keseluruhan</option>
                        <?php foreach ($periode_list as $p):
                            $label = date('d M Y', strtotime($p->periode_awal)) . ' - ' . date('d M Y', strtotime($p->periode_akhir));
                            $val = $p->periode_awal . '|' . $p->periode_akhir;
                            $sel = ((isset($selected_awal) && isset($selected_akhir)) && $selected_awal == $p->periode_awal && $selected_akhir == $p->periode_akhir) ? 'selected' : '';
                        ?>
                            <option value="<?= $val ?>" <?= $sel ?>><?= $label ?></option>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <?php $def_awal = $selected_awal ?? date('Y-01-01');
                        $def_akhir = $selected_akhir ?? date('Y-12-31'); ?>
                        <option value="<?= $def_awal . '|' . $def_akhir ?>">Default (<?= $def_awal ?> - <?= $def_akhir ?>)</option>
                    <?php endif; ?>
                </select>
                <button id="btn_refresh" class="btn btn-primary w-100">
                    <i class="mdi mdi-refresh"></i> Terapkan
                </button>
            </div>

            <!-- Statistik ringkas -->
            <div class="row">

                <div class="col-xl-3 col-md-6">
                    <div class="card widget-box-two bg-success">
                        <div class="card-body">
                            <div class="float-right avatar-lg rounded-circle bg-soft-light mt-2">
                                <i class="mdi mdi-account-group font-22 avatar-title text-white"></i>
                            </div>
                            <div class="wigdet-two-content">
                                <p class="m-0 text-uppercase text-white">Total Pegawai</p>
                                <h2 class="text-white"><span data-plugin="counterup"><?= $total_pegawai ?? 0 ?></span></h2>
                            </div>
                        </div>
                    </div>
                </div><!-- end col -->

                <div class="col-xl-3 col-md-6">
                    <div class="card widget-box-two bg-info">
                        <div class="card-body">
                            <div class="float-right avatar-lg rounded-circle bg-soft-light mt-2">
                                <i class="mdi mdi-clipboard-check font-22 avatar-title text-white"></i>
                            </div>
                            <div class="wigdet-two-content">
                                <p class="m-0 text-white text-uppercase">Selesai Dinilai</p>
                                <h2 class="text-white"><span data-plugin="counterup"><?= $selesai ?? 0 ?></span></h2>
                            </div>
                        </div>
                    </div>
                </div><!-- end col -->

                <div class="col-xl-3 col-md-6">
                    <div class="card widget-box-two bg-warning">
                        <div class="card-body">
                            <div class="float-right avatar-lg rounded-circle bg-soft-light mt-2">
                                <i class="mdi mdi-progress-clock font-22 avatar-title text-white"></i>
                            </div>
                            <div class="wigdet-two-content">
                                <p class="m-0 text-uppercase text-white">Masih Proses</p>
                                <h2 class="text-white"><span data-plugin="counterup"><?= $proses ?? 0 ?></span></h2>
                            </div>
                        </div>
                    </div>
                </div><!-- end col -->

                <div class="col-xl-3 col-md-6">
                    <div class="card widget-box-two bg-danger">
                        <div class="card-body">
                            <div class="float-right avatar-lg rounded-circle bg-soft-light mt-2">
                                <i class="mdi mdi-alert-circle font-22 avatar-title text-white"></i>
                            </div>
                            <div class="wigdet-two-content">
                                <p class="m-0 text-uppercase text-white">Belum Dinilai</p>
                                <h2 class="text-white"><span data-plugin="counterup"><?= $belum ?? 0 ?></span></h2>
                            </div>
                        </div>
                    </div>
                </div><!-- end col -->

            </div>
            <!-- end row -->

            <!-- Grafik -->
            <div class="row">
                <div class="col-xl-6">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="header-title mb-3">Distribusi Penilaian Pegawai</h4>
                            <div dir="ltr">
                                <div id="donut-charts"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-6">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between mb-3">
                                <h4 class="header-title">Grafik Nilai Pegawai</h4>
                                <div class="d-flex w-65">
                                    <!-- Dropdown Cabang -->
                                    <select id="filter-unit" class="form-control mb-2 me-2">
                                        <option value="">Pilih Cabang</option>
                                        <?php foreach ($this->Administrator_model->getCabangList() as $cb) : ?>
                                            <option value="<?= $cb->kode_cabang ?>">
                                                <?= $cb->unit_kantor ?> (<?= $cb->kode_cabang ?>)
                                            </option>
                                        <?php endforeach; ?>
                                    </select>

                                    <!-- Dropdown Unit Kantor -->
                                    <select id="filter-unitkantor" class="form-control">
                                        <option value="">Pilih Unit Kantor</option>
                                    </select>
                                </div>
                            </div>

                            <div id="grafik-nilai-pegawai" style="height: 332px;"></div>
                        </div>
                    </div>
                </div>
            </div>

        </div> <!-- end container-fluid -->

    </div> <!-- end content -->
</div>
<!-- ============================================================== -->
<!-- End Page Content here -->
<!-- ============================================================== -->
