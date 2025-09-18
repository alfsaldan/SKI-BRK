<div class="content-page">
    <div class="content">
        <div class="container-fluid">

            <!-- Judul Halaman -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h3 class="page-title">
                    <i class="fas fa-id-card mr-2 text-primary"></i> Detail Pegawai
                </h3>
            </div>

            <!-- Profil Pegawai -->
            <div class="card shadow-sm mb-4 border-0">
                <div class="card-body d-flex align-items-center">
                    <div class="avatar bg-primary text-white rounded-circle d-flex align-items-center justify-content-center mr-3"
                        style="width:70px; height:70px; font-size:24px;">
                        <i class="fas fa-user"></i>
                    </div>
                    <div>
                        <h4 class="mb-1"><?= $pegawai->nama ?></h4>
                        <p class="mb-0 text-muted">
                            <strong>NIK:</strong> <?= $pegawai->nik ?>
                        </p>
                    </div>
                </div>
            </div>

            <!-- Riwayat Jabatan -->
            <div class="card shadow-sm mb-4 border-0">
                <div class="card-header bg-light">
                    <h5 class="mb-0"><i class="fas fa-briefcase mr-2 text-primary"></i> Riwayat Jabatan</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover table-bordered">
                            <thead class="thead-light">
                                <tr>
                                    <th>Jabatan</th>
                                    <th>Jenis Unit</th>
                                    <th>Unit Kantor</th>
                                    <th>Tanggal Mulai</th>
                                    <th>Tanggal Selesai</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($riwayat)): ?>
                                    <?php foreach ($riwayat as $r): ?>
                                        <tr>
                                            <td><?= $r->jabatan ?></td>
                                            <td><?= $r->unit_kerja ?></td>
                                            <td><?= $r->unit_kantor ?></td>
                                            <td><?= date('d M Y', strtotime($r->tgl_mulai)) ?></td>
                                            <td><?= $r->tgl_selesai ? date('d M Y', strtotime($r->tgl_selesai)) : '-' ?></td>
                                            <td>
                                                <?php if ($r->status == 'aktif'): ?>
                                                    <span class="badge badge-success">Aktif</span>
                                                <?php else: ?>
                                                    <span class="badge badge-secondary">Nonaktif</span>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="5" class="text-center text-muted">Belum ada riwayat jabatan</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Tambah Jabatan Baru -->
            <div class="card shadow-sm mb-4 border-0">
                <div class="card-header bg-light">
                    <h5 class="mb-0"><i class="fas fa-plus-circle mr-2 text-primary"></i> Tambah Riwayat Jabatan</h5>
                </div>
                <div class="card-body">
                    <form method="post" action="<?= base_url('SuperAdmin/updateJabatan') ?>">
                        <input type="hidden" name="nik" value="<?= $pegawai->nik ?>">
                        <div class="form-row">
                            <div class="form-group col-md-4">
                                <label>Jabatan Baru</label>
                                <select name="jabatan" id="jabatanSelect" class="form-control select2" required>
                                    <option value="">Pilih atau ketik Jabatan</option>
                                    <?php foreach ($jabatan_list as $j): ?>
                                        <option value="<?= $j->jabatan ?>"><?= $j->jabatan ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="form-group col-md-4">
                                <label>Jenis Unit</label>
                                <select name="unit_kerja" id="unitKerjaSelect" class="form-control select2" required>
                                    <option value="">Pilih atau ketik Jenis Unit</option>
                                    <?php foreach ($unitkerja_list as $u): ?>
                                        <option value="<?= $u->unit_kerja ?>"><?= $u->unit_kerja ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="form-group col-md-4">
                                <label>Unit Kantor</label>
                                <input type="text" name="unit_kantor" class="form-control" placeholder="Isi Unit Kantor"
                                    required>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Tanggal Mulai</label>
                            <input type="date" name="tgl_mulai" class="form-control" required>
                        </div>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Simpan Jabatan
                        </button>
                    </form>
                </div>
            </div>

            <!-- Aktifkan / Nonaktifkan Pegawai -->
            <div class="card shadow-sm border-0">
                <div class="card-body text-right">
                    <!-- Aktifkan / Nonaktifkan Pegawai -->
                    <a href="javascript:void(0);" class="btn btn-danger btn-toggle-status"
                        data-url="<?= base_url('SuperAdmin/nonaktifPegawai/' . $pegawai->nik) ?>"
                        data-action="nonaktif">
                        <i class="fas fa-user-slash"></i> Nonaktifkan
                    </a>

                    <a href="javascript:void(0);" class="btn btn-success btn-toggle-status"
                        data-url="<?= base_url('SuperAdmin/aktifkanPegawai/' . $pegawai->nik) ?>" data-action="aktif">
                        <i class="fas fa-user-check"></i> Aktifkan
                    </a>
                </div>
            </div>


            <!-- Judul Halaman -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <a href="<?= base_url('SuperAdmin/kelolaDataPegawai') ?>" class="btn btn-outline-secondary btn-sm">
                    <i class="fas fa-arrow-left"></i> Kembali
                </a>
            </div>

        </div>
    </div>
</div>

<!-- CSS Select2 -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<!-- jQuery & JS Select2 -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
    $(document).ready(function () {
        $('#jabatanSelect').select2({
            placeholder: "Pilih atau ketik Jabatan",
            tags: true,
            allowClear: true,
            width: '100%'
        });

        $('#unitKerjaSelect').select2({
            placeholder: "Pilih atau ketik Jenis Unit",
            tags: true,
            allowClear: true,
            width: '100%'
        });
    });
</script>


<!-- Tambahkan SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    $(document).ready(function () {
        $('.btn-nonaktif').on('click', function (e) {
            e.preventDefault();
            let url = $(this).data('url');

            Swal.fire({
                title: 'Nonaktifkan Pegawai?',
                text: "Pegawai ini tidak akan bisa login lagi ke sistem!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, Nonaktifkan!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = url;
                }
            });
        });
    });
</script>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    $(document).ready(function () {
        $('.btn-toggle-status').on('click', function (e) {
            e.preventDefault();
            let url = $(this).data('url');
            let action = $(this).data('action');

            let title = action === 'nonaktif' ? 'Nonaktifkan Pegawai?' : 'Aktifkan Pegawai Kembali?';
            let text = action === 'nonaktif'
                ? 'Pegawai ini tidak akan bisa login lagi ke sistem!'
                : 'Pegawai ini akan bisa login kembali ke sistem!';
            let icon = action === 'nonaktif' ? 'warning' : 'info';
            let confirmText = action === 'nonaktif' ? 'Ya, Nonaktifkan!' : 'Ya, Aktifkan!';

            Swal.fire({
                title: title,
                text: text,
                icon: icon,
                showCancelButton: true,
                confirmButtonColor: action === 'nonaktif' ? '#d33' : '#28a745',
                cancelButtonColor: '#6c757d',
                confirmButtonText: confirmText,
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = url;
                }
            });
        });
    });

    $('.btn-toggle-status').on('click', function (e) {
        e.preventDefault();
        let url = $(this).data('url');
        let action = $(this).data('action');
        let currentStatus = "<?= $pegawai->status ?>"; // ambil dari PHP

        // Kalau status sudah sama, kasih alert info
        if ((action === 'aktif' && currentStatus === 'aktif') ||
            (action === 'nonaktif' && currentStatus === 'nonaktif')) {
            Swal.fire({
                title: 'Info',
                text: 'Pegawai ini statusnya sudah ' + currentStatus + '!',
                icon: 'info',
                confirmButtonColor: '#3085d6',
                confirmButtonText: 'OK'
            });
            return;
        }

        // Kalau beda status → konfirmasi update
        let title = action === 'nonaktif' ? 'Nonaktifkan Pegawai?' : 'Aktifkan Pegawai Kembali?';
        let text = action === 'nonaktif'
            ? 'Pegawai ini tidak akan bisa login lagi ke sistem!'
            : 'Pegawai ini akan bisa login kembali ke sistem!';
        let icon = action === 'nonaktif' ? 'warning' : 'success';
        let confirmText = action === 'nonaktif' ? 'Ya, Nonaktifkan!' : 'Ya, Aktifkan!';

        Swal.fire({
            title: title,
            text: text,
            icon: icon,
            showCancelButton: true,
            confirmButtonColor: action === 'nonaktif' ? '#d33' : '#28a745',
            cancelButtonColor: '#6c757d',
            confirmButtonText: confirmText,
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = url;
            }
        });
    });

</script>