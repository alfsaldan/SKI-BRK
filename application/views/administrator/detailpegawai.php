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
                    <form method="post" action="<?= base_url('Administrator/updateJabatan') ?>">
                        <input type="hidden" name="nik" value="<?= $pegawai->nik ?>">
                        <div class="form-row">
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
                                <select name="unit_kantor" id="unitKantorSelect" class="form-control select2" required>
                                    <option value="">Pilih atau ketik Unit Kantor</option>
                                    <?php foreach ($unitkantor_list as $uk): ?>
                                        <option value="<?= $uk->unit_kantor ?>"><?= $uk->unit_kantor ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="form-group col-md-4">
                                <label>Jabatan Baru</label>
                                <select name="jabatan" id="jabatanSelect" class="form-control select2" required>
                                    <option value="">Pilih atau ketik Jabatan</option>
                                    <?php foreach ($jabatan_list as $j): ?>
                                        <option value="<?= $j->jabatan ?>"><?= $j->jabatan ?></option>
                                    <?php endforeach; ?>
                                </select>
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

            <div class="card-body text-right">
                <a href="<?= base_url('Administrator/toggleStatusPegawai/' . $pegawai->nik . '/nonaktif') ?>"
                    class="btn btn-danger btn-toggle-status"
                    data-action="nonaktif"
                    data-nik="<?= $pegawai->nik ?>">
                    <i class="fas fa-user-slash"></i> Nonaktifkan
                </a>

                <a href="<?= base_url('Administrator/toggleStatusPegawai/' . $pegawai->nik . '/aktif') ?>"
                    class="btn btn-success btn-toggle-status"
                    data-action="aktif"
                    data-nik="<?= $pegawai->nik ?>">
                    <i class="fas fa-user-check"></i> Aktifkan
                </a>
            </div>



            <!-- Judul Halaman -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <a href="<?= base_url('Administrator/kelolaDataPegawai') ?>" class="btn btn-outline-secondary btn-sm">
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

<script>
    $(document).ready(function() {
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

        $('#unitKantorSelect').select2({
            placeholder: "Pilih atau ketik Unit Kantor",
            tags: true,
            allowClear: true,
            width: '100%'
        });
    });
</script>


<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    $(document).ready(function() {
        $('.btn-toggle-status').click(function(e) {
            e.preventDefault();

            let nik = $(this).data('nik'); // ✅ sekarang ada data-nik
            let action = $(this).data('action');
            let url = "<?= base_url('Administrator/toggleStatusPegawai') ?>/" + nik + "/" + action;

            let title = (action === 'nonaktif') ? 'Nonaktifkan Pegawai?' : 'Aktifkan Pegawai Kembali?';
            let text = (action === 'nonaktif') ?
                'Pegawai ini tidak akan bisa login lagi ke sistem!' :
                'Pegawai ini akan bisa login kembali ke sistem!';
            let icon = (action === 'nonaktif') ? 'warning' : 'success';
            let confirmText = (action === 'nonaktif') ? 'Ya, Nonaktifkan!' : 'Ya, Aktifkan!';

            Swal.fire({
                title: title,
                text: text,
                icon: icon,
                showCancelButton: true,
                confirmButtonColor: (action === 'nonaktif') ? '#d33' : '#28a745',
                cancelButtonColor: '#6c757d',
                confirmButtonText: confirmText,
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    // AJAX POST ke controller
                    $.ajax({
                        url: url,
                        type: 'POST',
                        dataType: 'json',
                        success: function(res) {
                            if (res.status === 'success') {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Berhasil!',
                                    text: res.message,
                                    timer: 1500,
                                    showConfirmButton: false
                                }).then(() => {
                                    location.reload(); // reload halaman untuk update status terbaru
                                });
                            } else {
                                Swal.fire('Error', res.message, 'error');
                            }
                        },
                        error: function() {
                            Swal.fire('Error', 'Terjadi kesalahan server', 'error');
                        }
                    });
                }
            });
        });
    });
</script>