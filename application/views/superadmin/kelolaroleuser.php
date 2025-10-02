<!-- ============================================================== -->
<!-- Start Page Content -->
<!-- ============================================================== -->
<div class="content-page">
    <div class="content">
        <div class="container-fluid">

            <!-- Judul Halaman -->
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-flex justify-content-between align-items-center">
                        <h3 class="page-title">
                            <i class="fas fa-users mr-2 text-primary"></i> Kelola Role User
                        </h3>
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="#">KPI Online-BRKS</a></li>
                            <li class="breadcrumb-item active">Kelola Role User</li>
                        </ol>
                    </div>
                </div>
            </div>

            <!-- Card Utama -->
            <div class="row">
                <div class="col-12">
                    <div class="card shadow-sm border-0">
                        <div class="card-body">

                            <!-- Header Aksi -->
                            <div class="mb-3 d-flex justify-content-between align-items-center">
                                <h5 class="card-title mb-0">📋 Daftar Role User</h5>
                                <button class="btn btn-primary btn-sm" data-toggle="modal" data-target="#tambahUserModal">
                                    <i class="fas fa-plus"></i> Tambah User
                                </button>
                            </div>

                            <!-- Tabel Role User -->
                            <div class="table-responsive">
                                <table id="datatable-users" class="table table-hover table-striped table-bordered nowrap w-100">
                                    <thead class="thead-light">
                                        <tr>
                                            <th>No</th>
                                            <th>NIK</th>
                                            <th>Role</th>
                                            <th>Status</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $no = 1; foreach ($users as $u): ?>
                                            <tr>
                                                <td><?= $no++ ?></td>
                                                <td><?= htmlspecialchars($u->nik) ?></td>
                                                <td><?= ucfirst(htmlspecialchars($u->role)) ?></td>
                                                <td>
                                                    <?php if ($u->is_active == 1): ?>
                                                        <span class="badge badge-success">Aktif</span>
                                                    <?php else: ?>
                                                        <span class="badge badge-danger">Nonaktif</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <button type="button" class="btn btn-warning btn-sm btn-edit"
                                                            data-id="<?= $u->id ?>"
                                                            data-nik="<?= $u->nik ?>"
                                                            data-role="<?= $u->role ?>"
                                                            data-status="<?= $u->is_active ?>">
                                                        <i class="fas fa-edit"></i> Edit
                                                    </button>
                                                    <button type="button" class="btn btn-danger btn-sm btn-delete"
                                                            data-id="<?= $u->id ?>">
                                                        <i class="fas fa-trash"></i> Hapus
                                                    </button>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>

                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<!-- Modal Tambah User -->
<div class="modal fade" id="tambahUserModal" tabindex="-1">
    <div class="modal-dialog">
        <form action="<?= base_url('superadmin/tambahRoleUser') ?>" method="post" class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title"><i class="fas fa-user-plus"></i> Tambah User</h5>
                <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label>NIK</label>
                    <input type="text" name="nik" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>Password</label>
                    <input type="password" name="password" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>Role</label>
                    <select name="role" class="form-control" required>
                        <option value="pegawai">Pegawai</option>
                        <option value="penilai">Penilai</option>
                        <option value="admin">Admin</option>
                        <option value="superadmin">Superadmin</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Status</label>
                    <select name="is_active" class="form-control" required>
                        <option value="1">Aktif</option>
                        <option value="0">Nonaktif</option>
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-success"><i class="fas fa-save"></i> Simpan</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Edit User -->
<div class="modal fade" id="editUserModal" tabindex="-1">
    <div class="modal-dialog">
        <form action="<?= base_url('superadmin/editRoleUser') ?>" method="post" class="modal-content">
            <div class="modal-header bg-warning text-white">
                <h5 class="modal-title"><i class="fas fa-user-edit"></i> Edit User</h5>
                <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <input type="hidden" name="id" id="edit_id">
                <div class="form-group">
                    <label>NIK</label>
                    <input type="text" id="edit_nik" class="form-control" readonly>
                </div>
                <div class="form-group">
                    <label>Password (kosongkan jika tidak diganti)</label>
                    <input type="password" name="password" class="form-control">
                </div>
                <div class="form-group">
                    <label>Role</label>
                    <select name="role" id="edit_role" class="form-control" required>
                        <option value="pegawai">Pegawai</option>
                        <option value="penilai">Penilai</option>
                        <option value="admin">Admin</option>
                        <option value="superadmin">Superadmin</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Status</label>
                    <select name="is_active" id="edit_status" class="form-control" required>
                        <option value="1">Aktif</option>
                        <option value="0">Nonaktif</option>
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-warning"><i class="fas fa-save"></i> Update</button>
            </div>
        </form>
    </div>
</div>

<!-- Styling kecil DataTable -->
<style>
    .dataTables_length select {
        width: 70px !important;
        display: inline-block;
        margin: 0 5px;
        padding: 2px 5px;
        font-size: 0.875rem;
    }
</style>

<!-- JS -->
<script>
$(document).ready(function () {

    // ✅ Inisialisasi DataTables lengkap
    $('#datatable-users').DataTable({
        responsive: true,
        pageLength: 5,
        lengthMenu: [
            [5, 10, 25, 50, -1],
            [5, 10, 25, 50, "Semua"]
        ],
        order: [],
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

    // ✅ SweetAlert flashdata
    <?php if ($this->session->flashdata('success')): ?>
        Swal.fire("Berhasil", "<?= $this->session->flashdata('success'); ?>", "success");
    <?php elseif ($this->session->flashdata('error')): ?>
        Swal.fire("Gagal", "<?= $this->session->flashdata('error'); ?>", "error");
    <?php endif; ?>

    // ✅ Modal Edit (pakai event delegation)
    $(document).on('click', '.btn-edit', function () {
        const id = $(this).data('id');
        const nik = $(this).data('nik');
        const role = $(this).data('role');
        const status = $(this).data('status');

        $('#edit_id').val(id);
        $('#edit_nik').val(nik);
        $('#edit_role').val(role);
        $('#edit_status').val(status);

        $('#editUserModal').modal('show');
    });

    // ✅ Tombol hapus dengan SweetAlert
    $(document).on('click', '.btn-delete', function () {
        const id = $(this).data('id');
        if (!id) {
            Swal.fire('Error', 'ID user tidak ditemukan!', 'error');
            return;
        }
        Swal.fire({
            title: 'Apakah anda yakin?',
            text: "Data user ini akan dihapus permanen!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = "<?= base_url('superadmin/hapusRoleUser/') ?>" + id;
            }
        });
    });
});
</script>
