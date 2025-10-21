<!-- Footer Start -->
<footer class="footer">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-6">
                <span id="footerYear"></span> &copy; KPI Online-BRKS by
                <a href="https://www.brksyariah.co.id/brkweb_syariah/" target="_blank">Bank Riau Kepri Syariah</a>
            </div>
            <div class="col-md-6">
                <div class="text-md-right footer-links d-none d-sm-block">
                    <a href="#">About Us</a>
                    <a href="#">Help</a>
                    <a href="#">Contact Us</a>
                </div>
            </div>
        </div>
    </div>
</footer>
<!-- end Footer -->

</div>

<!-- ============================================================== -->
<!-- End Page content -->
<!-- ============================================================== -->

</div>
<!-- END wrapper -->

<!-- Right Sidebar -->
<div class="right-bar">

</div>
<!-- /Right-bar -->

<!-- Right bar overlay-->
<div class="rightbar-overlay"></div>

<!-- Vendor js -->
<script src="<?= base_url('assets/js/vendor.min.js') ?>"></script>

<!-- Bootstrap select plugin -->
<script src="<?= base_url('assets/libs/bootstrap-select/bootstrap-select.min.js') ?>"></script>

<!-- App js -->
<script src="<?= base_url('assets/js/app.min.js') ?>"></script>

<!--Form Wizard-->
<script src="<?= base_url('assets/libs/jquery-steps/jquery.steps.min.js') ?>"></script>

<script src="<?= base_url('assets/libs/jquery-validation/jquery.validate.min.js') ?>"></script>

<!-- Init js-->
<script src="<?= base_url('assets/js/pages/form-wizard.init.js') ?>"></script>

<!-- Datatable plugin js -->
<script src="<?= base_url('assets/libs/datatables/jquery.dataTables.min.js') ?>"></script>
<script src="<?= base_url('assets/libs/datatables/dataTables.bootstrap4.min.js') ?>"></script>

<script src="<?= base_url('assets/libs/datatables/dataTables.responsive.min.js') ?>"></script>
<script src="<?= base_url('assets/libs/datatables/responsive.bootstrap4.min.js') ?>"></script>

<script src="<?= base_url('assets/libs/datatables/dataTables.buttons.min.js') ?>"></script>
<script src="<?= base_url('assets/libs/datatables/buttons.bootstrap4.min.js') ?>"></script>

<script src="<?= base_url('assets/libs/datatables/buttons.html5.min.js') ?>"></script>
<script src="<?= base_url('assets/libs/datatables/buttons.print.min.js') ?>"></script>

<script src="<?= base_url('assets/libs/datatables/dataTables.keyTable.min.js') ?>"></script>
<script src="<?= base_url('assets/libs/datatables/dataTables.fixedHeader.min.js') ?>"></script>
<script src="<?= base_url('assets/libs/datatables/dataTables.scroller.min.js') ?>"></script>
<script src="<?= base_url('assets/libs/datatables/dataTables.colVis.js') ?>"></script>
<script src="<?= base_url('assets/libs/datatables/dataTables.fixedColumns.min.js') ?>"></script>

<script src="<?= base_url('assets/libs/jszip/jszip.min.js') ?>"></script>
<script src="<?= base_url('assets/libs/pdfmake/pdfmake.min.js') ?>"></script>
<script src="<?= base_url('assets/libs/pdfmake/vfs_fonts.js') ?>"></script>

<script src="<?= base_url('assets/js/pages/datatables.init.js') ?>"></script>


<!-- flot chart -->
<script src="<?= base_url('assets/libs/flot-charts/jquery.flot.js') ?>"></script>
<script src="<?= base_url('assets/libs/flot-charts/jquery.flot.time.js') ?>"></script>
<script src="<?= base_url('assets/libs/flot-charts/jquery.flot.tooltip.min.js') ?>"></script>
<script src="<?= base_url('assets/libs/flot-charts/jquery.flot.resize.js') ?>"></script>
<script src="<?= base_url('assets/libs/flot-charts/jquery.flot.pie.js') ?>"></script>
<script src="<?= base_url('assets/libs/flot-charts/jquery.flot.selection.js') ?>"></script>
<script src="<?= base_url('assets/libs/flot-charts/jquery.flot.stack.js') ?>"></script>
<script src="<?= base_url('assets/libs/flot-charts/jquery.flot.orderBars.js') ?>"></script>
<script src="<?= base_url('assets/libs/flot-charts/jquery.flot.crosshair.js') ?>"></script>



<!-- plugins -->
<script src="<?= base_url('assets/libs/c3/c3.min.js') ?>"></script>
<script src="<?= base_url('assets/libs/d3/d3.min.js') ?>"></script>

<!-- dashboard init -->
<script src="<?= base_url('assets/js/pages/dashboard.init.js') ?>"></script>


<script>
    document.getElementById('footerYear').innerText = new Date().getFullYear();
</script>

<script>
    const showBtn = document.getElementById('showSasaranBtn');
    const sasaranWrapper = document.getElementById('sasaranWrapper');
    const submitBtn = document.getElementById('submitSasaranBtn');

    showBtn.addEventListener('click', () => {
        sasaranWrapper.style.display = 'block'; // tampilkan input
        submitBtn.style.display = 'inline-block'; // tampilkan tombol submit
        showBtn.style.display = 'none'; // sembunyikan tombol tambah
    });
</script>
<!-- Script JS -->
<script>
    $(document).ready(function() {

        // Load Sasaran Kerja sesuai Perspektif
        $(" #perspektif").change(function() {
            let perspektif = $(this).val();
            if (perspektif != "") {
                $.ajax({
                    url: "<?= base_url('Administrator/get_sasaran_by_perspektif') ?>",
                    type: "POST",
                    data: {
                        perspektif: perspektif
                    },
                    dataType: "json",
                    success: function(res) {
                        $("#sasaran").empty().append('<option value="">--Pilih Sasaran Kerja--</option > ');
                        $.each(res, function(i, item) {
                            $("#sasaran").append('<option value="' + item.sasaran + '">' + item.sasaran + '</option>');
                        });
                    }
                });
            }
        });

        // Tambah Sasaran Baru
        $("#add-sasaran").click(function() {
            $("#new-sasaran-wrapper").toggle();
        });

        // Tambah Indikator Row
        $("#add-row").click(function() {
            let row = `<div class="form-row indikator-row mt-2">
            <div class="col-md-6">
                <input type="text" name="indikator[]" class="form-control" placeholder="Indikator" required>
            </div>
            <div class="col-md-3">
                <input type="number" name="bobot[]" class="form-control" placeholder="Bobot (%)" required>
            </div>
            <div class="col-md-2">
                <button type="button" class="btn btn-danger remove-row">Hapus</button>
            </div>
        </div>`;
            $("#indikator-wrapper").append(row);
        });

        // Hapus Indikator Row
        $(document).on("click", ".remove-row", function() {
            $(this).closest(".indikator-row").remove();
        });

    });
</script>

<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
    var optionsTargetRealisasi = {
        chart: {
            type: 'line',
            height: 350
        },
        series: [{
            name: 'Target',
            data: [80, 90, 70, 85, 95, 100, 110, 120, 125, 130, 140, 150] // Target per bulan
        }, {
            name: 'Realisasi',
            data: [75, 85, 65, 82, 90, 95, 105, 115, 120, 125, 135, 145] // Realisasi per bulan
        }],
        xaxis: {
            categories: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des']
        },
        colors: ['#039046', '#e63946'], // hijau BRKS untuk Target, merah untuk Realisasi
        stroke: {
            curve: 'smooth',
            width: 3
        },
        markers: {
            size: 5
        },
        dataLabels: {
            enabled: true
        },
        yaxis: {
            title: {
                text: 'Jumlah Kinerja'
            }
        }
    };

    var chartTargetRealisasi = new ApexCharts(document.querySelector("#chart-target-vs-realisasi"), optionsTargetRealisasi);
    chartTargetRealisasi.render();
</script>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    <?php if ($this->session->flashdata('message')):
        $msg = $this->session->flashdata('message'); ?>
        Swal.fire({
            icon: '<?= $msg['type']; ?>', // success / error
            title: '<?= $msg['type'] === "success" ? "Berhasil" : "Gagal"; ?>',
            text: '<?= $msg['text']; ?>',
            timer: 2000,
            showConfirmButton: false
        });
    <?php endif; ?>
</script>

<script>
    document.addEventListener('click', function(e) {
        if (e.target.closest('.deleteIndikatorBtn')) {
            const btn = e.target.closest('.deleteIndikatorBtn');
            const id = btn.dataset.id;

            Swal.fire({
                title: 'Yakin hapus?',
                text: "Data indikator akan dihapus!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Redirect ke delete URL
                    window.location.href = "<?= base_url('Administrator/deleteIndikator/'); ?>" + id;
                }
            });
        }
    });
</script>
<style>
    /* Atur jarak antar tombol di header aksi */
    .action-buttons>* {
        margin: 3px;
        /* jarak antar item */
    }

    /* Perkecil ukuran input file biar sejajar rapi */
    .action-buttons input[type="file"] {
        font-size: 0.85rem;
        padding: 3px;
    }
</style>


<style>
    .dataTables_length select {
        width: 70px !important;
        /* kecilkan ukuran dropdown */
        display: inline-block;
        margin: 0 5px;
        padding: 2px 5px;
        font-size: 0.875rem;
        /* font lebih kecil */
    }
</style>
<script>
    $(document).ready(function() {
        $('#datatable-pegawai').DataTable({
            responsive: true,
            pageLength: 5,
            lengthMenu: [
                [5, 10, 25, 50, -1],
                [5, 10, 25, 50, "Semua"]
            ],
            order: [], // biar gak auto urut NIK
            dom: '<"row mb-1"<"col-md-6 d-flex align-items-center"l><"col-md-6 text-right"f>>' +
                'rt' +
                '<"row mt-3"<"col-md-6"i><"col-md-6 d-flex justify-content-end"p>>',
            language: {
                search: "Pencarian:",
                searchPlaceholder: "Masukan keyword",
                lengthMenu: "Tampilkan _MENU_ data",
                zeroRecords: "Tidak ditemukan data",
                info: "Menampilkan _START_ - _END_ dari _TOTAL_ data",
                infoEmpty: "Tidak ada data tersedia",
                infoFiltered: "(difilter dari _MAX_ total data)",
                paginate: {
                    previous: "Sebelumnya",
                    next: "Berikutnya"
                }
            }
        });
    });
</script>


<script>
    // Update label saat file dipilih
    $(document).on('change', '.custom-file-input', function(event) {
        let fileName = event.target.files[0].name;
        $(this).next('.custom-file-label').html(fileName);
    });
</script>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    <?php if ($this->session->flashdata('success')): ?>
        Swal.fire({
            icon: 'success',
            title: 'Berhasil',
            html: '<?= $this->session->flashdata('success'); ?>',
        });
    <?php endif; ?>

    <?php if ($this->session->flashdata('warning')): ?>
        Swal.fire({
            icon: 'warning',
            title: 'Perhatian',
            html: '<?= $this->session->flashdata('warning'); ?>',
        });
    <?php endif; ?>

    <?php if ($this->session->flashdata('error')): ?>
        Swal.fire({
            icon: 'error',
            title: 'Gagal',
            html: '<?= $this->session->flashdata('error'); ?>',
        });
    <?php endif; ?>
</script>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        <?php if ($this->session->flashdata('success')): ?>
            Swal.fire({
                icon: 'success',
                title: 'Berhasil',
                html: '<?= $this->session->flashdata('success'); ?>',
                confirmButtonColor: '#3085d6'
            });
        <?php endif; ?>

        <?php if ($this->session->flashdata('warning')): ?>
            Swal.fire({
                icon: 'warning',
                title: 'Peringatan',
                html: '<?= $this->session->flashdata('warning'); ?>',
                confirmButtonColor: '#f0ad4e'
            });
        <?php endif; ?>

        <?php if ($this->session->flashdata('error')): ?>
            Swal.fire({
                icon: 'error',
                title: 'Error',
                html: '<?= $this->session->flashdata('error'); ?>',
                confirmButtonColor: '#d33'
            });
        <?php endif; ?>
    });
</script>


<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    $(document).on('click', '.btn-delete', function(e) {
        e.preventDefault();
        let url = $(this).data('url');

        Swal.fire({
            title: 'Yakin hapus?',
            text: "Data pegawai akan dihapus permanen!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = url;
            }
        });
    });
</script>

<script>
    $(document).ready(function() {
        const nikPegawai = $('#nik').val(); // Ambil NIK pegawai dari input hidden

        $('#tabel-catatan').DataTable({
            processing: true,
            serverSide: true,
            responsive: true,
            pageLength: 10,
            lengthMenu: [
                [10, 25, 50, 100, -1],
                [10, 25, 50, 100, "Semua"]
            ],
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
                },
                {
                    data: 'nama_penilai'
                },
                {
                    data: 'catatan',
                    orderable: false
                },
                {
                    data: 'tanggal',
                    render: function(data) {
                        if (!data) return '';
                        const date = new Date(data + ' UTC');
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
            ],
            dom: '<"row mb-1"<"col-md-6 d-flex align-items-center"l><"col-md-6 text-right"f>>' +
                'rt' +
                '<"row mt-3"<"col-md-6"i><"col-md-6 d-flex justify-content-end"p>>',
            language: {
                search: "Pencarian:",
                searchPlaceholder: "Masukan keyword",
                lengthMenu: "Tampilkan _MENU_ data",
                zeroRecords: "Tidak ditemukan data",
                info: "Menampilkan _START_ - _END_ dari _TOTAL_ catatan",
                infoEmpty: "Tidak ada data tersedia",
                infoFiltered: "(difilter dari _MAX_ total catatan)",
                paginate: {
                    previous: "Sebelumnya",
                    next: "Berikutnya"
                }
            },
            drawCallback: function(settings) {
                var api = this.api();
                api.column(0, {
                    order: 'applied'
                }).nodes().each(function(cell, i) {
                    cell.innerHTML = i + 1;
                });
            }
        });
    });
</script>

<script>
    $(document).ready(function() {
        const nikPegawai = $('#nik').val();

        $('#tabel-catatan-pegawai').DataTable({
            processing: true,
            serverSide: true,
            responsive: false,
            pageLength: 10,
            lengthMenu: [
                [10, 25, 50, 100, -1],
                [10, 25, 50, 100, "Semua"]
            ],
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
                },
                {
                    data: 'catatan',
                    orderable: false
                },
                {
                    data: 'tanggal',
                    render: function(data) {
                        if (!data) return '';
                        const date = new Date(data + ' UTC');
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
            ],
            dom: '<"row mb-2"<"col-md-6"l><"col-md-6 text-right"f>>rt<"row mt-2"<"col-md-6"i><"col-md-6 d-flex justify-content-end"p>>',
            language: {
                search: "Pencarian:",
                searchPlaceholder: "Masukan keyword",
                lengthMenu: "Tampilkan _MENU_ catatan",
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
            drawCallback: function(settings) {
                var api = this.api();
                api.column(0, {
                    order: 'applied'
                }).nodes().each(function(cell, i) {
                    cell.innerHTML = i + 1;
                });
            }
        });
    });
</script>


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
        $.getJSON("<?= base_url('administrator/get_grafik_all') ?>", function(data) {
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
        $("#filter-unit").on("change", function() {
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


</body>

</html>