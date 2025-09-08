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
                                <li class="breadcrumb-item"><a href="javascript:void(0);">SKI-BRKS</a></li>
                                <li class="breadcrumb-item active">Dashboard</li>
                            </ol>
                        </div>
                        <h4 class="page-title">Selamat Datang, <b>SuperAdmin</b>!</h4>
                        <p class="text-muted">Sistem Penilaian Kinerja Insani Bank BRK Syariah</p>
                    </div>
                </div>
            </div>
            <!-- end page title -->

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
                                <h2 class="text-white"><span data-plugin="counterup">256</span></h2>
                                <p class="text-white m-0">Semua unit kerja</p>
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
                                <p class="m-0 text-white text-uppercase">Target Kinerja</p>
                                <h2 class="text-white"><span data-plugin="counterup">184</span></h2>
                                <p class="text-white m-0">Tahun berjalan</p>
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
                                <p class="m-0 text-uppercase text-white">Realisasi</p>
                                <h2 class="text-white"><span data-plugin="counterup">142</span></h2>
                                <p class="text-white m-0">Sedang diproses</p>
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
                                <p class="m-0 text-white text-uppercase">Belum Dinilai</p>
                                <h2 class="text-white"><span data-plugin="counterup">42</span></h2>
                                <p class="text-white m-0">Menunggu verifikasi</p>
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
                            <h4 class="header-title mb-3">Perbandingan Target vs Realisasi</h4>
                            <div id="chart-target-vs-realisasi"></div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-6">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="header-title mb-3">Distribusi Penilaian Pegawai</h4>
                            <div dir="ltr">
                                <div id="donut-chart"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- end row -->

            <!-- Daftar pegawai terakhir -->
            <div class="row">
                <div class="col-xl-12">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="header-title mb-3">Pegawai Terbaru</h4>

                            <div class="table-responsive">
                                <table class="table table-striped mb-0">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>NIK</th>
                                            <th>Nama</th>
                                            <th>Unit</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>1</td>
                                            <td>123456789</td>
                                            <td>Ahmad Fauzi</td>
                                            <td>Kredit Mikro</td>
                                            <td><span class="badge badge-success">Aktif</span></td>
                                        </tr>
                                        <tr>
                                            <td>2</td>
                                            <td>987654321</td>
                                            <td>Siti Aminah</td>
                                            <td>Operasional</td>
                                            <td><span class="badge badge-warning">Cuti</span></td>
                                        </tr>
                                        <tr>
                                            <td>3</td>
                                            <td>567891234</td>
                                            <td>Rizky Firmansyah</td>
                                            <td>Pembiayaan</td>
                                            <td><span class="badge badge-success">Aktif</span></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
            <!-- end row -->

        </div> <!-- end container-fluid -->

    </div> <!-- end content -->
</div>
<!-- ============================================================== -->
<!-- End Page Content here -->
<!-- ============================================================== -->