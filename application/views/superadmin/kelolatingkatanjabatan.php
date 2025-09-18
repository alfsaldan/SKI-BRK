<div class="content-page">
    <div class="content">
        <div class="container-fluid">

            <!-- Page Title -->
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box">
                        <h4 class="page-title"><?= $judul ?></h4>
                    </div>
                </div>
            </div>

            <!-- Card -->
            <div class="card">
                <div class="card-body">
                    <button class="btn btn-success mb-3" data-toggle="modal" data-target="#modalTambah">
                        <i class="fa fa-plus"></i> Tambah Mapping
                    </button>

                    <div class="table-responsive">
                        <table id="datatable-mapping" class="table table-bordered table-striped">
                            <thead class="bg-secondary text-white text-center">
                                <tr>
                                    <th width="5%">No</th>
                                    <th>Jabatan</th>
                                    <th>Jenis Unit</th>
                                    <th>Penilai I</th>
                                    <th>Penilai II</th>
                                    <th width="15%">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($list)):
                                    $no = 1;
                                    foreach ($list as $row): ?>
                                        <tr>
                                            <td><?= $no++; ?></td>
                                            <td><?= $row->jabatan; ?></td>
                                            <td><?= $row->unit_kerja; ?></td>
                                            <td><?= $row->penilai1_jabatan; ?></td>
                                            <td><?= $row->penilai2_jabatan; ?></td>
                                            <td class="text-center">
                                                <button class="btn btn-sm btn-warning btn-edit"
                                                    data-id="<?= $row->id ?>"
                                                    data-jabatan="<?= $row->jabatan ?>"
                                                    data-unit="<?= $row->unit_kerja ?>"
                                                    data-penilai1="<?= $row->penilai1_jabatan ?>"
                                                    data-penilai2="<?= $row->penilai2_jabatan ?>">
                                                    Edit
                                                </button>
                                                <a href="<?= base_url('superadmin/hapusPenilaiMapping/' . $row->id) ?>"
                                                    class="btn btn-sm btn-danger btn-delete">
                                                    Hapus
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach;
                                else: ?>
                                    <tr>
                                        <td colspan="6" class="text-center">Belum ada data mapping</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<!-- Modal Tambah -->
<div class="modal fade" id="modalTambah" tabindex="-1">
    <div class="modal-dialog">
        <form method="post" action="<?= base_url('superadmin/tambahPenilaiMapping') ?>" class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title">Tambah Mapping Jabatan</h5>
                <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label>Jabatan</label>
                    <input type="text" name="jabatan" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>Jenis Unit</label>
                    <input type="text" name="unit_kerja" class="form-control">
                </div>
                <div class="form-group">
                    <label>Penilai I (Jabatan)</label>
                    <input type="text" name="penilai1_jabatan" class="form-control">
                </div>
                <div class="form-group">
                    <label>Penilai II (Jabatan)</label>
                    <input type="text" name="penilai2_jabatan" class="form-control">
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" data-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-success">Simpan</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Edit -->
<div class="modal fade" id="modalEdit" tabindex="-1">
    <div class="modal-dialog">
        <form method="post" id="formEdit" class="modal-content">
            <div class="modal-header bg-warning">
                <h5 class="modal-title">Edit Mapping Jabatan</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <input type="hidden" name="id" id="edit_id">
                <div class="form-group">
                    <label>Jabatan</label>
                    <input type="text" name="jabatan" id="edit_jabatan" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>Unit Kerja</label>
                    <input type="text" name="unit_kerja" id="edit_unit" class="form-control">
                </div>
                <div class="form-group">
                    <label>Penilai I (Jabatan)</label>
                    <input type="text" name="penilai1_jabatan" id="edit_penilai1" class="form-control">
                </div>
                <div class="form-group">
                    <label>Penilai II (Jabatan)</label>
                    <input type="text" name="penilai2_jabatan" id="edit_penilai2" class="form-control">
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" data-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-warning">Update</button>
            </div>
        </form>
    </div>
</div>

<!-- JS Dependencies -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<!-- Datatables JS -->
<script src="<?= base_url('assets/libs/datatables/jquery.dataTables.min.js') ?>"></script>
<script src="<?= base_url('assets/libs/datatables/dataTables.bootstrap4.min.js') ?>"></script>
<script src="<?= base_url('assets/libs/datatables/dataTables.responsive.min.js') ?>"></script>
<script src="<?= base_url('assets/libs/datatables/responsive.bootstrap4.min.js') ?>"></script>

<script>
    $(document).ready(function() {

        // DataTables init
        $('#datatable-mapping').DataTable({
            responsive: true,
            pageLength: 5,
            lengthMenu: [
                [5, 10, 25, 50, -1],
                [5, 10, 25, 50, "Semua"]
            ],
            order: [],
            dom: '<"row mb-1"<"col-md-6 d-flex align-items-center"l><"col-md-6 text-right"f>>rt<"row mt-3"<"col-md-6"i><"col-md-6 d-flex justify-content-end"p>>',
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

        // Edit modal
        $(document).on('click', '.btn-edit', function() {
            $('#edit_id').val($(this).data('id'));
            $('#edit_jabatan').val($(this).data('jabatan'));
            $('#edit_unit').val($(this).data('unit'));
            $('#edit_penilai1').val($(this).data('penilai1'));
            $('#edit_penilai2').val($(this).data('penilai2'));
            $('#formEdit').attr('action', "<?= base_url('superadmin/editPenilaiMapping/') ?>" + $(this).data('id'));
            $('#modalEdit').modal('show');
        });

        // Hapus dengan SweetAlert
        $(document).on('click', '.btn-delete', function(e) {
            e.preventDefault();
            var url = $(this).attr('href');
            Swal.fire({
                title: 'Yakin hapus data ini?',
                text: "Data yang sudah dihapus tidak bisa dikembalikan!",
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

    });
</script>