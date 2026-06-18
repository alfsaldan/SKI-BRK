<div id="wrapper">
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
                                <li class="breadcrumb-item"><a href="#">SKI Online-BRKS</a></li>
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
                                    <button class="btn btn-primary btn-sm" data-toggle="modal"
                                        data-target="#tambahUserModal">
                                        <i class="fas fa-plus"></i> Tambah User
                                    </button>
                                </div>

                                <!-- Tabel Role User -->
                                <div class="table-responsive">
                                    <table id="datatable-users"
                                        class="table table-hover table-striped table-bordered nowrap w-100">
                                        <thead class="thead-light">
                                            <tr>
                                                <th>No</th>
                                                <th>NIP</th>
                                                <th>Nama</th>
                                                <th>Role</th>
                                                <th>Status</th>
                                                <th>Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php $no = 1;
                                            foreach ($users as $u): ?>
                                                <tr>
                                                    <td><?= $no++ ?></td>
                                                    <td><?= htmlspecialchars($u->nik) ?></td>
                                                    <td><?= htmlspecialchars($u->nama ?? '-') ?></td>
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
                                                            data-nama="<?= $u->nama ?>"
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
        <div class="modal-dialog modal-lg">
            <form action="<?= base_url('superadmin/tambahRoleUser') ?>" method="post" class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title"><i class="fas fa-user-plus"></i> Tambah User</h5>
                    <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label>NIP</label>
                            <input type="text" name="nik" class="form-control" required maxlength="6" oninput="this.value = this.value.replace(/[^0-9]/g, '');">
                        </div>
                        <div class="form-group col-md-6">
                            <label>Nama</label>
                            <input type="text" name="nama" class="form-control" required>
                        </div>
                        <div class="form-group col-md-6">
                            <label>Jenis Unit</label>
                            <select name="unit_kerja" id="sa_unitKerja" class="form-control select2" required>
                                <option value="">Pilih Jenis Unit</option>
                                <?php if(isset($unitkerja_list)): ?>
                                <?php foreach ($unitkerja_list as $u): ?>
                                    <option value="<?= $u->unit_kerja ?>"><?= $u->unit_kerja ?></option>
                                <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </div>
                        <div class="form-group col-md-6">
                            <label>Unit Kantor</label>
                            <select name="unit_kantor" id="sa_unitKantor" class="form-control select2" required disabled>
                                <option value="">Pilih Jenis Unit terlebih dahulu</option>
                            </select>
                        </div>
                        <div class="form-group col-md-6">
                            <label>Jabatan</label>
                            <select name="jabatan" id="sa_jabatan" class="form-control select2" required disabled>
                                <option value="">Pilih Unit Kantor terlebih dahulu</option>
                            </select>
                        </div>
                        <div class="form-group col-md-6">
                            <label>Role</label>
                            <select name="role" class="form-control" required>
                                <option value="pegawai">pegawai</option>
                                <option value="administrator">administrator</option>
                                <option value="administrator_renstra">administrator_renstra</option>
                                <option value="superadmin">superadmin</option>
                            </select>
                        </div>
                        <div class="form-group col-md-6">
                            <label>Password</label>
                            <div class="input-group">
                                <input type="password" name="password" id="add_password" class="form-control" required>
                                <div class="input-group-append">
                                    <span class="input-group-text" style="cursor: pointer;" onclick="togglePassword('add_password', this)">
                                        <i class="fas fa-eye"></i>
                                    </span>
                                </div>
                            </div>
                            <small class="form-text text-muted">Password minimal 8 karakter, mengandung huruf besar, kecil, angka, dan simbol.</small>
                        </div>
                        <div class="form-group col-md-6">
                            <label>Status</label>
                            <select name="is_active" class="form-control" required>
                                <option value="1">Aktif</option>
                                <option value="0">Nonaktif</option>
                            </select>
                        </div>
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
                        <label>NIP</label>
                        <input type="text" id="edit_nik" class="form-control" readonly>
                    </div>
                    <div class="form-group">
                        <label>Nama</label>
                        <input type="text" id="edit_nama" class="form-control" readonly>
                    </div>
                    <div class="form-group">
                        <label>Password <small>(Kosongkan jika tidak diubah)</small></label>
                        <div class="input-group">
                            <input type="password" name="password" id="edit_password" class="form-control">
                            <div class="input-group-append">
                                <span class="input-group-text" style="cursor: pointer;" onclick="togglePassword('edit_password', this)">
                                    <i class="fas fa-eye"></i>
                                </span>
                            </div>
                        </div>
                        <small class="form-text text-muted">Jika diisi, wajib minimal 8 karakter, huruf besar, kecil, angka, dan simbol.</small>
                    </div>
                    <div class="form-group">
                        <label>Role</label>
                        <select name="role" id="edit_role" class="form-control" required>
                            <option value="pegawai">pegawai</option>
                            <option value="administrator">administrator</option>
                            <option value="administrator_renstra">administrator_renstra</option>
                            <option value="superadmin">superadmin</option>
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

<script>
    // Fungsi toggle lihat/sembunyi password
    function togglePassword(fieldId, el) {
        const input = document.getElementById(fieldId);
        const icon = el.querySelector("i");
        if (input.type === "password") {
            input.type = "text";
            icon.classList.remove("fa-eye");
            icon.classList.add("fa-eye-slash");
        } else {
            input.type = "password";
            icon.classList.remove("fa-eye-slash");
            icon.classList.add("fa-eye");
        }
    }

    // Validasi Strong Password saat form disubmit
    $(document).ready(function() {
        function validateStrongPassword(password) {
            const regex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}$/;
            return regex.test(password);
        }

        $('#tambahUserModal form, #editUserModal form').on('submit', function(e) {
            const formId = $(this).closest('.modal').attr('id');
            const passInput = formId === 'tambahUserModal' ? $('#add_password').val() : $('#edit_password').val();
            
            // Jika edit dan password kosong, biarkan lolos karena berarti tidak diubah
            if (formId === 'editUserModal' && passInput === '') return;

            if (!validateStrongPassword(passInput)) {
                e.preventDefault();
                Swal.fire({
                    icon: 'warning',
                    title: 'Password Lemah!',
                    text: 'Password wajib minimal 8 karakter, serta mengandung huruf besar, huruf kecil, angka, dan karakter spesial/simbol.'
                });
            }
        });
    });

    // JS untuk dropdown dinamis (Unit Kerja -> Unit Kantor -> Jabatan)
    $(document).ready(function () {
        $('#sa_unitKerja').change(function () {
            const unitKerja = $(this).val();
            $('#sa_unitKantor').prop('disabled', true).html('<option value="">Loading...</option>');
            $('#sa_jabatan').prop('disabled', true).html('<option value="">Pilih Unit Kantor terlebih dahulu</option>');

            if (unitKerja) {
                $.get('<?= base_url("Superadmin/getUnitKantorByUnitKerjatambah") ?>', {
                    unit_kerja: unitKerja
                }, function (data) {
                    let options = '<option value="">Pilih Unit Kantor</option>';
                    if (typeof data === 'string') {
                        try { data = JSON.parse(data); } catch(e) { data = []; }
                    }
                    data.forEach(function (item) {
                        options += `<option value="${item.unit_kantor}">${item.unit_kantor}</option>`;
                    });
                    $('#sa_unitKantor').html(options).prop('disabled', false);
                });
            } else {
                $('#sa_unitKantor').html('<option value="">Pilih Jenis Unit terlebih dahulu</option>').prop('disabled', true);
            }
        });

        $('#sa_unitKantor').change(function () {
            const unitKantor = $(this).val();
            $('#sa_jabatan').prop('disabled', true).html('<option value="">Loading...</option>');

            if (unitKantor) {
                $.get('<?= base_url("Superadmin/getJabatanByUnitKantortambah") ?>', {
                    unit_kantor: unitKantor
                }, function (data) {
                    let options = '<option value="">Pilih Jabatan</option>';
                    if (typeof data === 'string') {
                        try { data = JSON.parse(data); } catch(e) { data = []; }
                    }
                    data.forEach(function (item) {
                        options += `<option value="${item.jabatan}">${item.jabatan}</option>`;
                    });
                    $('#sa_jabatan').html(options).prop('disabled', false);
                });
            } else {
                $('#sa_jabatan').html('<option value="">Pilih Unit Kantor terlebih dahulu</option>').prop('disabled', true);
            }
        });
    });
</script>

    