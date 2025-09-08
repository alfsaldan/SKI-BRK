<!-- Footer Start -->
<footer class="footer">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-6">
                2016 - 2019 &copy; Codefox theme by <a href="">Coderthemes</a>
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

<!-- Script JS -->
<script>
    $(document).ready(function () {

        // Load Sasaran Kerja sesuai Perspektif
        $("#perspektif").change(function () {
            let perspektif = $(this).val();
            if (perspektif != "") {
                $.ajax({
                    url: "<?= base_url('SuperAdmin/get_sasaran_by_perspektif') ?>",
                    type: "POST",
                    data: { perspektif: perspektif },
                    dataType: "json",
                    success: function (res) {
                        $("#sasaran").empty().append('<option value="">-- Pilih Sasaran Kerja --</option>');
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

</body>

</html>