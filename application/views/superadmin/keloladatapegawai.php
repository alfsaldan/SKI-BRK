<!-- ============================================================== -->
<!-- Start Page Content here Tess-->
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
                                <li class="breadcrumb-item"><a href="javascript:void(0);">SKI-BRKS</a></li>
                                <li class="breadcrumb-item active">Kelola Data Pegawai</li>
                            </ol>
                        </div>
                        <h4 class="page-title">Kelola Data Pegawai</h4>
                    </div>
                </div>
            </div>
            <!-- end page title -->

            <!-- Card Utama -->
            <div class="row">
                <div class="col-12">
                    <div class="card shadow-sm">
                        <div class="card-body">
                            <!-- Header Aksi -->
                            <div class="mb-3">
                                <h5 class="card-title mb-2">Daftar Pegawai</h5>
                                <div class="d-flex flex-wrap align-items-center action-buttons">

                                    <!-- Button Template -->
                                    <a href="<?= base_url('SuperAdmin/downloadTemplatePegawai') ?>"
                                        class="btn btn-secondary btn-sm mr-2 mb-2">
                                        <i class="fas fa-file-download"></i> Template Excel
                                    </a>

                                    <!-- Import Excel dengan Input Group -->
                                    <form action="<?= base_url('SuperAdmin/importPegawai') ?>" method="post"
                                        enctype="multipart/form-data" class="mr-2 mb-2">
                                        <div class="input-group input-group-sm">
                                            <div class="custom-file">
                                                <input type="file" name="file_excel" class="custom-file-input"
                                                    id="fileExcel" required>
                                                <label class="custom-file-label" for="fileExcel">Pilih File</label>
                                            </div>
                                            <div class="input-group-append">
                                                <button type="submit" class="btn btn-warning">
                                                    <i class="fas fa-file-upload"></i> Import
                                                </button>
                                            </div>
                                        </div>
                                    </form>

                                    <!-- Button Tambah Pegawai -->
                                    <button class="btn btn-primary btn-sm mr-2 mb-2" data-toggle="modal"
                                        data-target="#tambahPegawaiModal">
                                        <i class="fas fa-plus"></i> Tambah Pegawai
                                    </button>

                                </div>
                            </div>


                            <!-- Tabel Pegawai -->
                            <div class="table-responsive">
                                <table id="datatable-pegawai" class="table table-striped table-bordered nowrap w-100">
                                    <thead class="thead-light">
                                        <tr>
                                            <th>No</th>
                                            <th>NIK</th>
                                            <th>Nama</th>
                                            <th>Jabatan</th>
                                            <th>Unit Kerja</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $no = 1;
                                        foreach ($pegawai as $p): ?>
                                            <tr>
                                                <td><?= $no++ ?></td>
                                                <td><?= $p->nik ?></td>
                                                <td><?= $p->nama ?></td>
                                                <td><?= $p->jabatan ?></td>
                                                <td><?= $p->unit_kerja ?></td>
                                                <td>
                                                    <div class="dropdown">
                                                        <a href="#" class="dropdown-toggle text-secondary"
                                                            data-toggle="dropdown">
                                                            <i class="fas fa-ellipsis-v"></i>
                                                        </a>
                                                        <div class="dropdown-menu dropdown-menu-right">
                                                            <a class="dropdown-item"
                                                                href="<?= base_url('SuperAdmin/editPegawai/' . $p->nik) ?>">
                                                                Edit
                                                            </a>
                                                            <a class="dropdown-item text-danger"
                                                                href="<?= base_url('SuperAdmin/deletePegawai/' . $p->nik) ?>">
                                                                Hapus
                                                            </a>
                                                        </div>
                                                    </div>
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

<!-- Modal Tambah Pegawai -->
<div class="modal fade" id="tambahPegawaiModal" tabindex="-1">
    <div class="modal-dialog">
        <form action="<?= base_url('SuperAdmin/tambahPegawai') ?>" method="post" class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tambah Pegawai</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label>NIK</label>
                    <input type="text" name="nik" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>Nama</label>
                    <input type="text" name="nama" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>Jabatan</label>
                    <input type="text" name="jabatan" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>Unit Kerja</label>
                    <input type="text" name="unit_kerja" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>Password</label>
                    <input type="password" name="password" class="form-control" required>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-success">Simpan</button>
            </div>
        </form>
    </div>
</div>