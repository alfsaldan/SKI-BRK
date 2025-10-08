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


<!-- plugins -->
<script src="<?= base_url('assets/libs/c3/c3.min.js') ?>"></script>
<script src="<?= base_url('assets/libs/d3/d3.min.js') ?>"></script>

<!-- dashboard init -->
<script src="<?= base_url('assets/js/pages/dashboard.init.js') ?>"></script>



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
        console.log("✅ DataTables Init Started...");

        // pastikan tabel ditemukan
        if ($('#datatable-users').length) {
            $('#datatable-users').DataTable({
                responsive: true,
                pageLength: 5,
                lengthMenu: [
                    [5, 10, 25, 50, -1],
                    [5, 10, 25, 50, "Semua"]
                ],
                order: [],
                dom: '<"row mb-2"<"col-md-6 d-flex align-items-center"l><"col-md-6 text-right"f>>' +
                    'rt' +
                    '<"row mt-2"<"col-md-6"i><"col-md-6 d-flex justify-content-end"p>>',
                language: {
                    search: "🔍 Pencarian:",
                    searchPlaceholder: "Masukkan kata kunci...",
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
            console.log("✅ DataTables berhasil diinisialisasi");
        } else {
            console.error("❌ Tabel dengan ID #datatable-users tidak ditemukan!");
        }
    });
</script>

<style>
    .dataTables_length select {
        width: 70px !important;
        display: inline-block;
        margin: 0 5px;
        padding: 2px 5px;
        font-size: 0.875rem;
    }
</style>


<script>
    $(document).on('click', '.btn-edit', function() {
        const id = $(this).data('id');
        const nik = $(this).data('nik');
        const nama = $(this).data('nama');
        const role = $(this).data('role');
        const status = $(this).data('status');

        // Isi data ke modal edit
        $('#edit_id').val(id);
        $('#edit_nik').val(nik);
        $('#edit_nama').val(nama);
        $('#edit_role').val(role);
        $('#edit_status').val(status);

        // Tampilkan modal
        $('#editUserModal').modal('show');
    });

    $(document).on('click', '.btn-delete', function() {
        const id = $(this).data('id');
        Swal.fire({
            title: 'Apakah Anda yakin?',
            text: "Data user ini akan dihapus permanen!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = "<?= base_url('superadmin/hapusRoleUser/') ?>" + id;
            }
        });
    });
</script>


<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    // Tampilkan SweetAlert kalau ada flashdata dari server
    <?php if ($this->session->flashdata('success')): ?>
        Swal.fire({
            icon: 'success',
            title: 'Berhasil!',
            text: "<?= $this->session->flashdata('success'); ?>",
            showConfirmButton: false,
            timer: 2000
        });
    <?php endif; ?>

    <?php if ($this->session->flashdata('error')): ?>
        Swal.fire({
            icon: 'error',
            title: 'Gagal!',
            text: "<?= $this->session->flashdata('error'); ?>",
            showConfirmButton: true
        });
    <?php endif; ?>
</script>


</body>

</html>