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
                            <i class="fas fa-users mr-2 text-primary"></i> Kelola Data Pegawai
                        </h3>
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="#">KPI Online-BRKS</a></li>
                            <li class="breadcrumb-item active">Kelola Data Pegawai</li>
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
                            <!-- Header Aksi -->
                            <div class="mb-3">
                                <!-- Judul di atas -->
                                <h5 class="card-title mb-2">📋 Daftar Pegawai</h5>

                                <!-- Tombol aksi di bawah -->
                                <div class="d-flex flex-wrap align-items-center action-buttons">
                                    <!-- Button Template -->
                                    <a href="<?= base_url('Administrator/downloadTemplatePegawai') ?>"
                                        class="btn btn-secondary btn-sm mr-2 mb-2">
                                        <i class="fas fa-file-download"></i> Template Excel
                                    </a>

                                    <!-- Import Excel -->
                                    <form action="<?= base_url('Administrator/importPegawai') ?>" method="post"
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

                                    <!-- Button Tambah -->
                                    <button class="btn btn-primary btn-sm mr-2 mb-2" data-toggle="modal"
                                        data-target="#tambahPegawaiModal">
                                        <i class="fas fa-plus"></i> Tambah Pegawai
                                    </button>
                                </div>
                            </div>

                            <!-- Tabel Pegawai -->
                            <div class="table-responsive">
                                <table id="datatable-pegawai"
                                    class="table table-hover table-striped table-bordered nowrap w-100">
                                    <thead class="thead-light">
                                        <tr>
                                            <th>No</th>
                                            <th>NIK</th>
                                            <th>Nama</th>
                                            <th>Jabatan</th>
                                            <th>Jenis Unit</th>
                                            <th>Unit Kantor</th>
                                            <th>Status</th>
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
                                                <td><?= $p->unit_kantor ?></td>
                                                <td>
                                                    <?php if (isset($p->status) && $p->status === 'aktif'): ?>
                                                        <span class="badge badge-success">Aktif</span>
                                                    <?php else: ?>
                                                        <span class="badge badge-danger">Nonaktif</span>
                                                    <?php endif; ?>
                                                </td>

                                                <td>
                                                    <div class="dropdown">
                                                        <a href="#" class="dropdown-toggle text-secondary"
                                                            data-toggle="dropdown">
                                                            <i class="fas fa-ellipsis-v"></i>
                                                        </a>
                                                        <div class="dropdown-menu dropdown-menu-right">
                                                            <a class="dropdown-item"
                                                                href="<?= base_url('Administrator/detailPegawai/' . $p->nik) ?>">
                                                                <i class="fas fa-eye text-info"></i> Detail
                                                            </a>
                                                            <a href="javascript:void(0);"
                                                                class="dropdown-item text-danger btn-delete"
                                                                data-url="<?= base_url('Administrator/deletePegawai/' . $p->nik) ?>">
                                                                <i class="fas fa-trash"></i> Hapus
                                                            </a>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>

                                </table>
                            </div>

                            <!-- Catatan Import -->
                            <div class="alert alert-info mt-3">
                                <strong>ℹ️ Catatan Import:</strong>
                                <ul class="mb-0">
                                    <li>Gunakan template Excel resmi untuk format sesuai.</li>
                                    <li>Kolom wajib: <code>NIK, Nama, Jabatan, Jenis Unit,Unit Kantor, Password</code>.</li>
                                    <li><code>NIK</code> harus unik (tidak boleh duplikat).</li>
                                    <li>Password minimal 6 karakter.</li>
                                    <li>File hanya mendukung <code>.xls</code> atau <code>.xlsx</code>.</li>
                                </ul>
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
    <div class="modal-dialog modal-lg">
        <form action="<?= base_url('Administrator/tambahPegawai') ?>" method="post" class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title text-white"><i class="fas fa-user-plus"></i> Tambah Pegawai</h5>
                <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label>NIK</label>
                        <input type="text" name="nik" class="form-control" required>
                    </div>
                    <div class="form-group col-md-6">
                        <label>Nama</label>
                        <input type="text" name="nama" class="form-control" required>
                    </div>

                    <!-- Perubahan: Jenis Unit, Unit Kantor, Jabatan menjadi Select -->
                    <div class="form-group col-md-6">
                        <label>Jenis Unit</label>
                        <select name="unit_kerja" id="add_unitKerja" class="form-control select2" required>
                            <option value="">Pilih Jenis Unit</option>
                            <?php foreach ($unitkerja_list as $u): ?>
                                <option value="<?= $u->unit_kerja ?>"><?= $u->unit_kerja ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group col-md-6">
                        <label>Unit Kantor</label>
                        <select name="unit_kantor" id="add_unitKantor" class="form-control select2" required disabled>
                            <option value="">Pilih Jenis Unit terlebih dahulu</option>
                        </select>
                    </div>
                    <div class="form-group col-md-6">
                        <label>Jabatan</label>
                        <select name="jabatan" id="add_jabatan" class="form-control select2" required disabled>
                            <option value="">Pilih Unit Kantor terlebih dahulu</option>
                        </select>
                    </div>
                    <!-- Akhir Perubahan -->

                    <div class="form-group col-md-6">
                        <label>Password</label>
                        <input type="password" name="password" class="form-control" required>
                    </div>

                </div>
                <!-- Debug panel (temporary) -->
                <div id="modalDebugPanel" style="display:none; margin-top:10px;">
                    <label>Debug response (temporary):</label>
                    <pre id="modalDebug" style="background:#f8f9fa; border:1px solid #ddd; padding:8px; max-height:200px; overflow:auto;"></pre>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-success"><i class="fas fa-save"></i> Simpan</button>
            </div>
        </form>
    </div>
</div>