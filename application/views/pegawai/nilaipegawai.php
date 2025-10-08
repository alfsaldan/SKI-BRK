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
                            <i class="mdi mdi-account-edit mr-2 text-primary"></i> Nilai Pegawai
                        </h3>
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="#">KPI Online-BRKS</a></li>
                            <li class="breadcrumb-item active">Nilai Pegawai</li>
                        </ol>
                    </div>
                </div>
            </div>

            <?php if (empty($pegawai_dinilai)) { ?>
                <div class="alert alert-info">
                    Anda tidak memiliki daftar pegawai untuk dinilai.
                </div>
            <?php } else { ?>
                <div class="row mb-3">
                    <div class="col-md-4">
                        <input type="text" id="filter-nik" class="form-control" placeholder="Filter berdasarkan NIK...">
                    </div>
                </div>

                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-bordered" id="tabel-pegawai-dinilai">
                                        <thead style="background-color:#2E7D32;color:#fff;text-align:center;">
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
                                            foreach ($pegawai_dinilai as $p) { ?>
                                                <tr>
                                                    <td class="text-center"><?= $no++; ?></td>
                                                    <td><?= $p->nik; ?></td>
                                                    <td><?= $p->nama; ?></td>
                                                    <td><?= $p->jabatan; ?></td>
                                                    <td><?= $p->unit_kerja; ?></td>
                                                    <td class="text-center">
                                                        <a href="<?= base_url('Pegawai/nilaiPegawaiDetail/' . $p->nik) ?>" class="btn btn-sm btn-primary">Nilai</a>
                                                    </td>
                                                </tr>
                                            <?php } ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php } ?>

        </div>
    </div>
</div>
<!-- ============================================================== -->
<!-- End Page Content here -->
<!-- ============================================================== -->

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const filterInput = document.getElementById('filter-nik');
        filterInput.addEventListener('input', function() {
            const filter = this.value.toLowerCase();
            document.querySelectorAll('#tabel-pegawai-dinilai tbody tr').forEach(row => {
                const nik = row.cells[1].innerText.toLowerCase();
                row.style.display = nik.includes(filter) ? '' : 'none';
            });
        });
    });
</script>