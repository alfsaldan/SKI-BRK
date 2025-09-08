<!-- Footer Start -->
<footer class="footer">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-6">
                <span id="footerYear"></span> &copy; SKI-BRKS by
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
    $(document).ready(function () {

        // Load Sasaran Kerja sesuai Perspektif
        $(" #perspektif").change(function () {
            let perspektif = $(this).val(); if (perspektif != "") {
                $.ajax({
                    url: "<?= base_url('SuperAdmin/get_sasaran_by_perspektif') ?>", type: "POST", data: { perspektif: perspektif },
                    dataType: "json", success: function (res) {
                        $("#sasaran").empty().append('<option value="">--Pilih Sasaran Kerja--</option > ');
                        $.each(res, function (i, item) {
                            $("#sasaran").append('<option value="' + item.sasaran + '">' + item.sasaran + '</option>');
                        });
                    }
                });
            }
        });

        // Tambah Sasaran Baru
        $("#add-sasaran").click(function () {
            $("#new-sasaran-wrapper").toggle();
        });

        // Tambah Indikator Row
        $("#add-row").click(function () {
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
        $(document).on("click", ".remove-row", function () {
            $(this).closest(".indikator-row").remove();
        });

    });
</script>

<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
    var options = {
        chart: {
            type: 'bar',
            height: 350
        },
        series: [{
            name: 'Target',
            data: [80, 90, 70, 85, 95]
        }, {
            name: 'Realisasi',
            data: [75, 88, 60, 80, 90]
        }],
        xaxis: {
            categories: ['Pegawai A', 'Pegawai B', 'Pegawai C', 'Pegawai D', 'Pegawai E']
        },
        colors: ['#039046', '#e63946'], // hijau BRKS & merah realisasi
        dataLabels: {
            enabled: true
        }
    };

    var chart = new ApexCharts(document.querySelector("#chart-target-vs-realisasi"), options);
    chart.render();
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
    document.addEventListener('click', function (e) {
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
                    window.location.href = "<?= base_url('SuperAdmin/deleteIndikator/'); ?>" + id;
                }
            });
        }
    });
</script>



</body>

</html>