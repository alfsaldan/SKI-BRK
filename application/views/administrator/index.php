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
                                <h2 class="text-white"><span data-plugin="counterup">42</span></h2>
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
                                    <select id="filter-unit" class="form-control mb-2">
                                        <option value="cabang">Cabang</option>
                                        <option value="cabang_utama">Cabang Utama</option>
                                        <option value="cabang_pembantu">Cabang Pembantu</option>
                                        <option value="kedai">Kedai</option>
                                    </select>
                                    <select id="filter-unitkantor" class="form-control">
                                        <!-- otomatis terisi -->
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
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/flot/0.8.3/jquery.flot.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/flot/0.8.3/jquery.flot.categories.min.js"></script>

<!-- Script Grafik -->
<script>
    $(document).ready(function() {
        var ticks = [
            [0, "Minus"],
            [1, "Fair"],
            [2, "Good"],
            [3, "Very Good"],
            [4, "Excellent"]
        ];

        // dummy data per unit + kantor
        var dataUnit = {
            "cabang": {
                "Pekanbaru Sudirman": [1, 12, 28, 22, 10],
                "Pekanbaru Tangkerang": [0, 10, 25, 20, 12]
            },
            "cabang_utama": {
                "Pekanbaru": [2, 5, 20, 15, 10]
            },
            "cabang_pembantu": {
                "Pekanbaru Harapan Raya": [3, 8, 22, 18, 14]
            },
            "kedai": {
                "Ramayana": [1, 7, 18, 12, 9]
            }
        };

        var colors = ["#ff4d4d", "#ff9900", "#1e90ff", "#32cd32", "#186c18"];

        function populateKantor(unit) {
            var $kantor = $("#filter-unitkantor");
            $kantor.empty();
            $.each(dataUnit[unit], function(namaKantor, nilai) {
                $kantor.append(`<option value="${namaKantor}">${namaKantor}</option>`);
            });
        }

        function renderChart(unit, kantor) {
            var values = dataUnit[unit][kantor];

            var barSeries = values.map(function(v, i) {
                return {
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
                color: "#a3a3a3"
            };

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
                legend: {
                    show: false
                }
            });
        }

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
                var label = (ticks.find(t => Number(t[0]) === x) || [null, item.series.label])[1];
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

        // render pertama kali
        var defaultUnit = "cabang";
        populateKantor(defaultUnit);
        var defaultKantor = $("#filter-unitkantor").val();
        renderChart(defaultUnit, defaultKantor);

        // ketika ganti unit
        $("#filter-unit").on("change", function() {
            var unit = $(this).val();
            populateKantor(unit);
            var kantor = $("#filter-unitkantor").val();
            renderChart(unit, kantor);
        });

        // ketika ganti kantor
        $("#filter-unitkantor").on("change", function() {
            var unit = $("#filter-unit").val();
            var kantor = $(this).val();
            renderChart(unit, kantor);
        });
    });
</script>