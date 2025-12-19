<div class="content-page">
    <div class="content">
        <div class="container-fluid">

            <!-- Judul Halaman -->
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box">
                        <div class="page-title-right">
                            <ol class="breadcrumb m-0">
                                <li class="breadcrumb-item"><a href="javascript:void(0);">KPI Online-BRKS</a></li>
                                <li class="breadcrumb-item active">Penilaian PPK</li>
                            </ol>
                        </div>
                        <h4 class="page-title text-primary"><i class="mdi mdi-account-check-outline mr-1"></i> Penilaian Program Peningkatan Kinerja (PPK)</h4>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12">
                    <div class="card shadow-sm border-0">
                        <div class="card-body">
                            
                            <!-- Nav Tabs -->
                            <ul class="nav nav-tabs nav-bordered mb-0">
                                <li class="nav-item">
                                    <a href="#penilai1" data-toggle="tab" aria-expanded="true" class="nav-link active text-info">
                                        <i class="mdi mdi-account-tie mr-1"></i> Sebagai Penilai I
                                    </a>
                                </li>
                                <?php if($is_pimpinan): ?>
                                <li class="nav-item">
                                    <a href="#pimpinan" data-toggle="tab" aria-expanded="false" class="nav-link text-warning">
                                        <i class="mdi mdi-domain mr-1"></i> Sebagai Pimpinan Unit
                                    </a>
                                </li>
                                <?php endif; ?>
                            </ul>

                            <div class="tab-content pt-2">
                                <!-- TAB PENILAI 1 -->
                                <div class="tab-pane show active" id="penilai1">
                                    <h5 class="text-info mb-3">Daftar Pegawai yang Dinilai (Penilai I)</h5>
                                    <div class="table-responsive">
                                        <?php
                                        // Filter: hanya tampilkan jika pegawai ybs sudah mengisi respon (ada di ppk_responses)
                                        $filtered_penilai1 = [];
                                        if (!empty($list_ppk_penilai1)) {
                                            $niks = [];
                                            foreach($list_ppk_penilai1 as $r) $niks[] = $r->nik;
                                            $niks = array_unique($niks);
                                            
                                            $map_responses = [];
                                            if(!empty($niks)){
                                                $this->db->select('nik, periode_ppk');
                                                $this->db->where_in('nik', $niks);
                                                $q = $this->db->get('ppk_responses');
                                                foreach($q->result() as $resp){
                                                    $map_responses[$resp->nik][] = $resp->periode_ppk;
                                                }
                                            }

                                            foreach ($list_ppk_penilai1 as $row) {
                                                if (isset($map_responses[$row->nik]) && in_array($row->periode_ppk, $map_responses[$row->nik])) {
                                                    $filtered_penilai1[] = $row;
                                                }
                                            }
                                        }
                                        ?>
                                        <table class="table table-bordered table-hover display responsive nowrap" style="width:100%" id="tabel-ppk-penilai1">
                                            <thead class="thead-light text-center">
                                                <tr>
                                                    <th class="text-center">No</th>
                                                    <th class="text-center">NIK</th>
                                                    <th class="text-center">Nama Pegawai</th>
                                                    <th class="text-center">Jabatan</th>
                                                    <th class="text-center">Tahap PPK</th>
                                                    <th class="text-center">Periode</th>
                                                    <th class="text-center">Status</th>
                                                    <th class="text-center">Aksi</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php if(!empty($filtered_penilai1)): ?>
                                                    <?php $no=1; foreach($filtered_penilai1 as $row): ?>
                                                    <tr>
                                                        <td class="text-center"><?= $no++ ?></td>
                                                        <td><?= $row->nik ?></td>
                                                        <td><?= $row->nama ?></td>
                                                        <td><?= $row->jabatan ?></td>
                                                        <td class="text-center">Tahap <?= $row->tahap ?? '-' ?></td>
                                                        <td class="text-center"><?= $row->periode_ppk ?></td>
                                                        <td class="text-center">
                                                            <div class="d-flex flex-column">
                                                                <?php
                                                                    $st = $row->status_penilai1 ?? 'Belum Disetujui';
                                                                    $cls = ($st == 'Disetujui') ? 'badge-success' : (($st == 'Ditolak') ? 'badge-danger' : 'badge-secondary');
                                                                    echo '<span class="badge '.$cls.'">'.$st.'</span>';

                                                                    if (!empty($row->kesimpulan)) {
                                                                        if ($row->kesimpulan == 'Berhasil') {
                                                                            echo '<span class="badge badge-success mt-1">Berhasil</span>';
                                                                        } else {
                                                                            echo '<span class="badge badge-secondary mt-1">Belum<br>Berhasil</span>';
                                                                        }
                                                                    }
                                                                ?>
                                                            </div>
                                                        </td>
                                                        <td class="text-center">
                                                            <a href="<?= base_url('pegawai/ppk_penilaiformulir/'.$row->id_nilai_akhir) ?>" class="btn btn-sm btn-primary">
                                                                <i class="mdi mdi-file-document-edit-outline mr-1"></i> Formulir
                                                            </a>
                                                            <a href="<?= base_url('pegawai/ppk_penilaievaluasi/'.$row->id_nilai_akhir) ?>" class="btn btn-sm btn-info">
                                                                <i class="mdi mdi-clipboard-check-outline mr-1"></i> Evaluasi
                                                            </a>
                                                        </td>
                                                    </tr>
                                                    <?php endforeach; ?>
                                                <?php else: ?>
                                                    <tr><td colspan="8" class="text-center">Tidak ada data PPK untuk dinilai.</td></tr>
                                                <?php endif; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                                <!-- TAB PIMPINAN UNIT -->
                                <?php if($is_pimpinan): ?>
                                <div class="tab-pane" id="pimpinan">
                                    <h5 class="text-warning mb-3">Daftar Pegawai Unit Kerja (Pimpinan Unit)</h5>
                                    <div class="table-responsive">
                                        <?php
                                        // Filter: hanya tampilkan jika pegawai ybs sudah mengisi respon
                                        $filtered_pimpinan = [];
                                        if (!empty($list_ppk_pimpinan)) {
                                            $niks = [];
                                            foreach($list_ppk_pimpinan as $r) $niks[] = $r->nik;
                                            $niks = array_unique($niks);
                                            
                                            $map_responses = [];
                                            if(!empty($niks)){
                                                $this->db->select('nik, periode_ppk');
                                                $this->db->where_in('nik', $niks);
                                                $q = $this->db->get('ppk_responses');
                                                foreach($q->result() as $resp){
                                                    $map_responses[$resp->nik][] = $resp->periode_ppk;
                                                }
                                            }

                                            foreach ($list_ppk_pimpinan as $row) {
                                                if (isset($map_responses[$row->nik]) && in_array($row->periode_ppk, $map_responses[$row->nik])) {
                                                    $filtered_pimpinan[] = $row;
                                                }
                                            }
                                        }
                                        ?>
                                        <table class="table table-bordered table-hover display responsive nowrap" style="width:100%" id="tabel-ppk-pimpinan">
                                            <thead class="thead-light text-center">
                                                <tr>
                                                    <th class="text-center">No</th>
                                                    <th class="text-center">NIK</th>
                                                    <th class="text-center">Nama Pegawai</th>
                                                    <th class="text-center">Jabatan</th>
                                                    <th class="text-center">Tahap PPK</th>
                                                    <th class="text-center">Periode</th>
                                                    <th class="text-center">Status</th>
                                                    <th class="text-center">Aksi</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php if(!empty($filtered_pimpinan)): ?>
                                                    <?php $no=1; foreach($filtered_pimpinan as $row): ?>
                                                    <tr>
                                                        <td class="text-center"><?= $no++ ?></td>
                                                        <td><?= $row->nik ?></td>
                                                        <td><?= $row->nama ?></td>
                                                        <td><?= $row->jabatan ?></td>
                                                        <td class="text-center">Tahap <?= $row->tahap ?? '-' ?></td>
                                                        <td class="text-center"><?= $row->periode_ppk ?></td>
                                                        <td class="text-center">
                                                            <div class="d-flex flex-column">
                                                                <?php
                                                                    $st = $row->status_pimpinanunit ?? 'Belum Disetujui';
                                                                    $cls = ($st == 'Disetujui') ? 'badge-success' : (($st == 'Ditolak') ? 'badge-danger' : 'badge-secondary');
                                                                    echo '<span class="badge '.$cls.'">'.$st.'</span>';

                                                                    if (!empty($row->kesimpulan)) {
                                                                        if ($row->kesimpulan == 'Berhasil') {
                                                                            echo '<span class="badge badge-success mt-1">Berhasil</span>';
                                                                        } else {
                                                                            echo '<span class="badge badge-secondary mt-1">Belum<br>Berhasil</span>';
                                                                        }
                                                                    }
                                                                ?>
                                                            </div>
                                                        </td>
                                                        <td class="text-center">
                                                            <a href="<?= base_url('pegawai/ppk_pimpinanformulir/'.$row->id_nilai_akhir) ?>" class="btn btn-sm btn-primary">
                                                                <i class="mdi mdi-file-document-edit-outline mr-1"></i> Formulir
                                                            </a>
                                                            <a href="<?= base_url('pegawai/ppk_pimpinanevaluasi/'.$row->id_nilai_akhir) ?>" class="btn btn-sm btn-info">
                                                                <i class="mdi mdi-clipboard-check-outline mr-1"></i> Evaluasi
                                                            </a>
                                                        </td>
                                                    </tr>
                                                    <?php endforeach; ?>
                                                <?php else: ?>
                                                    <tr><td colspan="8" class="text-center">Tidak ada data PPK di unit ini.</td></tr>
                                                <?php endif; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <?php endif; ?>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    #tabel-ppk-penilai1 td,
    #tabel-ppk-pimpinan td {
        vertical-align: middle;
    }
</style>

<!-- DataTables JS & CSS (CDN) -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.dataTables.min.css">
<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>

<script>
    $(document).ready(function() {
        var dataTableOptions = {
            responsive: true,
            pageLength: 10,
            lengthMenu: [
                [10, 25, 50, 100, -1],
                [10, 25, 50, 100, "Semua"]
            ],
            language: {
                search: "Cari:",
                lengthMenu: "Tampilkan _MENU_ data per halaman",
                info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                infoEmpty: "Tidak ada data tersedia",
                zeroRecords: "Tidak ditemukan data yang sesuai",
                paginate: {
                    first: "Awal",
                    last: "Akhir",
                    next: "Selanjutnya",
                    previous: "Sebelumnya"
                }
            }
        };

        $('#tabel-ppk-penilai1').DataTable(dataTableOptions);
        
        <?php if($is_pimpinan): ?>
        $('#tabel-ppk-pimpinan').DataTable(dataTableOptions);
        <?php endif; ?>
    });
</script>