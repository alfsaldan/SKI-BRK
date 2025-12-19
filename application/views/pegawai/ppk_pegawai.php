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
                                <li class="breadcrumb-item active"> PPK</li>
                            </ol>
                        </div>
                        <h4 class="page-title text-primary"><i class="mdi mdi-account-badge-alert mr-1"></i> Program Peningkatan Kinerja (PPK)</h4>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12">
                    <div class="card shadow-sm border-0">
                        <div class="card-body">
                            <h4 class="header-title mb-4 text-primary text-center font-weight-bold"> Riwayat Program Peningkatan Kinerja (PPK)
                            </h4>
                            <div class="table-responsive">
                                <?php
                                // Filter data: hanya tampilkan jika periode_ppk ada di tabel ppk_responses untuk user ini
                                $nik_session = $this->session->userdata('nik');
                                $valid_periods = [];
                                $q_resp = $this->db->select('periode_ppk')->where('nik', $nik_session)->get('ppk_responses');
                                foreach ($q_resp->result() as $resp) {
                                    $valid_periods[] = $resp->periode_ppk;
                                }

                                $filtered_ppk = [];
                                if (isset($list_ppk) && !empty($list_ppk)) {
                                    foreach ($list_ppk as $item) {
                                        if (in_array($item->periode_ppk, $valid_periods)) {
                                            $filtered_ppk[] = $item;
                                        }
                                    }
                                }
                                ?>
                                <?php if (!empty($filtered_ppk)): ?>
                                <table class="table table-bordered table-hover dt-responsive nowrap" id="tabel-ppk">
                                    <thead class="thead-light text-center">
                                        <tr>
                                            <th>No</th>
                                            <th>Tahap PPK</th>
                                            <th>Predikat</th>
                                            <th>Periode</th>
                                            <th>Predikat Periodik</th>
                                            <th>Status</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $no=1; foreach($filtered_ppk as $row): ?>
                                            <tr>
                                                <td class="text-center"><?= $no++ ?></td>
                                                <td class="text-center">Tahap <?= $row->tahap ?? '-' ?></td>
                                                <td class="text-center"><span class="badge badge-danger"><?= $row->predikat ?></span></td>
                                                <td class="text-center"><?= $row->periode_ppk ?></td>
                                                <td class="text-center">
                                                    <?php if (!empty($row->predikat_list)): ?>
                                                        <?php foreach ($row->predikat_list as $p): ?>
                                                            <?php $badge_cls = (strtolower($p) == 'minus') ? 'badge-danger' : 'badge-info'; ?>
                                                            <span class="badge <?= $badge_cls ?> mr-1"><?= $p ?></span>
                                                        <?php endforeach; ?>
                                                    <?php else: ?>
                                                        <span class="text-muted small">Belum ada predikat terbaru</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td class="text-center">
                                                    <?php
                                                        // Status Formulir
                                                        $s_pegawai = $row->status_pegawai ?? null;
                                                        $s_penilai1 = $row->status_penilai1 ?? null;
                                                        $s_msdi = $row->status_msdi ?? null;
                                                        $s_pimpinan = $row->status_pimpinanunit ?? null;

                                                        $status = 'Belum Dinilai';
                                                        $badge_class = 'secondary';

                                                        if ($s_pegawai === 'Ditolak' || $s_penilai1 === 'Ditolak' || $s_msdi === 'Ditolak' || $s_pimpinan === 'Ditolak') {
                                                            $status = 'Ditolak';
                                                            $badge_class = 'danger';
                                                        } elseif ($s_pegawai === 'Disetujui' && $s_penilai1 === 'Disetujui' && $s_msdi === 'Disetujui' && $s_pimpinan === 'Disetujui') {
                                                            $status = 'Disetujui';
                                                            $badge_class = 'success';
                                                        } elseif ($s_pegawai || $s_penilai1 || $s_msdi || $s_pimpinan) {
                                                            $status = 'Menunggu Persetujuan';
                                                            $badge_class = 'warning';
                                                        }
                                                        $formulirBadge = '<span class="badge badge-'.$badge_class.'">'.$status.'</span>';

                                                        // Status Kesimpulan
                                                        $kesimpulanBadge = '';
                                                        if (!empty($row->kesimpulan)) {
                                                            if ($row->kesimpulan == 'Berhasil') {
                                                                $kesimpulanBadge = '<span class="badge badge-success mt-1">Berhasil</span>';
                                                            } else {
                                                                $kesimpulanBadge = '<span class="badge badge-secondary mt-1">Belum<br>Berhasil</span>';
                                                            }
                                                        }
                                                    ?>
                                                    <div class="d-flex flex-column">
                                                        <?= $formulirBadge ?>
                                                        <?= $kesimpulanBadge ?>
                                                    </div>
                                                </td>
                                                <td class="text-center">
                                                    <a href="<?= base_url('pegawai/ppk_pegawaiformulir/'.$row->id) ?>" class="btn btn-sm btn-primary">
                                                        <i class="mdi mdi-file-document-edit-outline mr-1"></i> Formulir
                                                    </a>
                                                    <a href="<?= base_url('pegawai/ppk_pegawaievaluasi/'.$row->id) ?>" class="btn btn-sm btn-info">
                                                        <i class="mdi mdi-clipboard-check-outline mr-1"></i> Evaluasi
                                                    </a>
                                                </td>
                                            </tr>
                                            <?php endforeach; ?>
                                    </tbody>
                                </table>
                                <?php else: ?>
                                    <div class="text-center p-4"><h4 class="text-success font-weight-bold">Tidak ada riwayat PPK, terus berikan Kinerja terbaik.</h4></div>
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
    #tabel-ppk td {
        vertical-align: middle;
    }
</style>