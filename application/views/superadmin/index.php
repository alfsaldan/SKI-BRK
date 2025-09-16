<!-- ============================================================== -->
<!-- Start Page Content here -->
<!-- ============================================================== -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/flot/0.8.3/jquery.flot.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/flot/0.8.3/jquery.flot.categories.min.js"></script>

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
                        <h5 class="page-title">Selamat Datang, <b>SuperAdmin</b>!</h5>
                        <p class="text-muted">
                        <h5>Sistem Penilaian Kinerja Insani PT Bank Riau Kepri Syariah</h5>
                        </p>
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
                                <!-- <p class="text-white m-0"> - </p> -->
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
                                <h2 class="text-white"><span data-plugin="counterup">184</span></h2>
                                <!-- <p class="text-white m-0"> - </p> -->
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
                                <h2 class="text-white"><span data-plugin="counterup">142</span></h2>
                                <!-- <p class="text-white m-0"> - </p> -->
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
                                <!-- <p class="text-white m-0"> - </p> -->
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
                                <div id="donut-charts"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <h4 class="header-title mb-3">Grafik Nilai Pegawai</h4>
                        <div id="grafik-nilai-pegawai" style="height: 320px;"></div>
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

<!-- Tambahkan di bawah sebelum </body>, setelah jquery dan flot js -->
<script>
    $(document).ready(function() {
        // ticks / label kategori
        var ticks = [
            [0, "Minus"],
            [1, "Fair"],
            [2, "Good"],
            [3, "Very Good"],
            [4, "Excellent"]
        ];

        // nilai contoh (ganti dengan data dari DB kalau perlu)
        var values = [5, 12, 25, 30, 18];

        // warna masing-masing kategori
        var colors = ["#ff4d4d", "#ff9900", "#1e90ff", "#32cd32", "#186c18ff"];

        // buat series bar per kategori (satu titik per series) -> tiap series punya warna sendiri
        var barSeries = values.map(function(v, i) {
            return {
                // label opsional, kalau mau legend per bar gunakan label; kalau tidak ingin legend, bisa kosong
                label: ticks[i][1],
                data: [
                    [i, v]
                ],
                bars: {
                    show: true,
                    barWidth: 0.5,
                    align: "center"
                },
                color: colors[i]
            };
        });

        // series line yang menghubungkan semua titik
        var lineSeries = {
            label: "Trend",
            data: values.map(function(v, i) {
                return [i, v];
            }),
            lines: {
                show: true,
                fill: false
            },
            points: {
                show: true,
                radius: 3
            },
            color: "#a3a3a3ff"
        };

        // plot (barSeries + line)
        $.plot("#grafik-nilai-pegawai", barSeries.concat([lineSeries]), {
            xaxis: {
                ticks: ticks
            },
            grid: {
                hoverable: true,
                clickable: true,
                borderWidth: 1,
                borderColor: "#f0f0f0"
            },
            // kalau mau legend (akan menampilkan satu entry per series), ubah show:true
            legend: {
                show: false
            }
        });

        // tooltip
        $("<div id='tooltip-nilai'></div>").css({
            position: "absolute",
            display: "none",
            border: "1px solid #ccc",
            padding: "6px",
            "background-color": "#fff",
            opacity: 0.95,
            "z-index": 10000
        }).appendTo("body");

        $("#grafik-nilai-pegawai").bind("plothover", function(event, pos, item) {
            if (item) {
                var x = Number(item.datapoint[0]);
                var y = item.datapoint[1];
                var label = (ticks.find(function(t) {
                    return Number(t[0]) === x;
                }) || [null, item.series.label])[1];
                $("#tooltip-nilai").html(label + " : " + y)
                    .css({
                        top: item.pageY + 8,
                        left: item.pageX + 8
                    })
                    .fadeIn(100);
            } else {
                $("#tooltip-nilai").hide();
            }
        });
    });
</script>