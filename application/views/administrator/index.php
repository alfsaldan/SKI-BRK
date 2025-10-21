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

<!-- DataTables JS/CSS harus sudah tersedia di template -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Restore periode dari URL
        (function restoreSelectedPeriodFromUrl() {
            const params = new URLSearchParams(window.location.search);
            const awal = params.get('awal');
            const akhir = params.get('akhir');
            if (awal && akhir) {
                const sel = document.getElementById('filter_periode');
                const optionVal = awal + '|' + akhir;
                for (let i = 0; i < sel.options.length; i++) {
                    if (sel.options[i].value === optionVal) {
                        sel.selectedIndex = i;
                        break;
                    }
                }
            }
        })();

        // Tombol Refresh untuk reload dengan parameter awal|akhir
        document.getElementById('btn_refresh').addEventListener('click', function() {
            const val = document.getElementById('filter_periode').value;
            if (val) {
                const parts = val.split('|');
                window.location.href = `?awal=${parts[0]}&akhir=${parts[1]}`;
            } else {
                window.location.href = `?`;
            }
        });

        // Donut Chart
        var optionsDonut = {
            chart: {
                type: 'donut',
                height: 350
            },
            series: [<?= $selesai ?? 0 ?>, <?= $proses ?? 0 ?>, <?= $belum ?? 0 ?>],
            labels: ['Selesai', 'Proses', 'Belum'],
            colors: ['#039be5', '#f9a825', '#d32f2f'],
            legend: {
                position: 'bottom'
            },
            dataLabels: {
                enabled: true,
                formatter: function(val) {
                    return val.toFixed(1) + "%";
                }
            }
        };
        var chartDonut = new ApexCharts(document.querySelector("#donut-charts"), optionsDonut);
        chartDonut.render();
    });
</script>


<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    var jq36 = jQuery.noConflict(true);
</script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/flot/0.8.3/jquery.flot.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/flot/0.8.3/jquery.flot.categories.min.js"></script>

<!-- Script Grafik -->
<script>
    jq36(document).ready(function() {
        var ticks = [
            [0, "Minus"],
            [1, "Fair"],
            [2, "Good"],
            [3, "Very Good"],
            [4, "Excellent"]
        ];

        var colors = ["#ff4d4d", "#ff9900", "#1e90ff", "#32cd32", "#186c18"];

        // 🟢 Fungsi render chart reusable
        function renderChart(data) {
            var barSeries = data.map(function(item, i) {
                return {
                    label: item[0],
                    data: [
                        [i, item[1]]
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
                data: data.map(function(item, i) {
                    return [i, item[1]];
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

        // 🟢 1️⃣ Tampilkan grafik awal (semua data)
        jq36.getJSON("<?= base_url('administrator/get_grafik_all') ?>", function(data) {
            renderChart(data);
        });

        // 🟢 2️⃣ Load daftar cabang
        $.getJSON("<?= base_url('administrator/get_unit_kantor_list') ?>", function(data) {
            var $cabang = $("#filter-unit");
            $.each(data, function(i, item) {
                $cabang.append(`<option value="${item.kode_cabang}">${item.unit_kantor}</option>`);
            });
        });

        // 🟢 3️⃣ Saat cabang dipilih
        jq36("#filter-unit").on("change", function() {
            var kode_cabang = $(this).val();
            var $unit = $("#filter-unitkantor");
            $unit.empty().append('<option value="">Pilih Unit Kantor</option>');
            $("#grafik-nilai-pegawai").empty();

            if (!kode_cabang) {
                // Jika kosong, tampilkan semua data lagi
                $.getJSON("<?= base_url('administrator/get_grafik_all') ?>", renderChart);
                return;
            }

            // Ambil daftar unit kantor berdasarkan cabang
            $.getJSON("<?= base_url('administrator/get_unit_kantor/') ?>" + kode_cabang, function(data) {
                $.each(data, function(i, item) {
                    $unit.append(`<option value="${item.kode_unit}">${item.unit_kantor}</option>`);
                });
            });

            // 🔹 Langsung tampilkan grafik total cabang yang dipilih
            $.getJSON("<?= base_url('administrator/get_grafik_cabang/') ?>" + kode_cabang, renderChart);
        });

        // 🟢 4️⃣ Saat unit kantor dipilih → tampil grafik spesifik unit
        $("#filter-unitkantor").on("change", function() {
            var kode_unit = $(this).val();
            var kode_cabang = $("#filter-unit").val();

            if (!kode_unit && kode_cabang) {
                // Jika unit dikosongkan tapi cabang masih ada
                $.getJSON("<?= base_url('administrator/get_grafik_cabang/') ?>" + kode_cabang, renderChart);
                return;
            } else if (!kode_unit) {
                // Jika dua-duanya kosong → tampil semua
                $.getJSON("<?= base_url('administrator/get_grafik_all') ?>", renderChart);
                return;
            }

            // Ambil grafik khusus unit
            $.getJSON("<?= base_url('administrator/get_grafik_unit/') ?>" + kode_unit, renderChart);
        });

        // 🟢 5️⃣ Tooltip
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
    });
</script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const btnShow = document.getElementById('btn_show_filter');
        const btnClose = document.getElementById('btn_close_filter');
        const popup = document.getElementById('filter_popup');

        // tampil/sembunyikan popup
        btnShow.addEventListener('click', () => {
            popup.style.display = (popup.style.display === 'none' ? 'block' : 'none');
        });
        btnClose.addEventListener('click', () => {
            popup.style.display = 'none';
        });

        // klik di luar popup = tutup
        document.addEventListener('click', function(e) {
            if (!popup.contains(e.target) && !btnShow.contains(e.target)) {
                popup.style.display = 'none';
            }
        });

        // Tombol Refresh
        document.getElementById('btn_refresh').addEventListener('click', function() {
            const val = document.getElementById('filter_periode').value;
            if (val) {
                const parts = val.split('|');
                window.location.href = `?awal=${parts[0]}&akhir=${parts[1]}`;
            } else {
                window.location.href = `?`;
            }
        });
    });
</script>
