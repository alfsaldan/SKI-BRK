<div class="content-page">
    <div class="content">
        <div class="container-fluid">
            <h4 class="page-title">Penilaian Pegawai: <?= $pegawai_detail->nama ?></h4>

            <div class="card">
                <div class="card-body">
                    <p><b>NIK:</b> <?= $pegawai_detail->nik ?></p>
                    <p><b>Nama:</b> <?= $pegawai_detail->nama ?></p>
                    <p><b>Jabatan:</b> <?= $pegawai_detail->jabatan ?></p>
                    <p><b>Unit Kerja:</b> <?= $pegawai_detail->unit_kerja ?></p>

                    <hr>

                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Perspektif</th>
                                <th>Sasaran Kerja</th>
                                <th>Indikator</th>
                                <th>Target</th>
                                <th>Realisasi</th>
                                <th>Pencapaian</th>
                                <th>Nilai</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($indikator_by_jabatan as $i): ?>
                                <tr>
                                    <td><?= $i->perspektif ?></td>
                                    <td><?= $i->sasaran_kerja ?></td>
                                    <td><?= $i->indikator ?></td>
                                    <td><input type="text" class="form-control" value="<?= $i->target ?>" readonly></td>
                                    <td><input type="text" class="form-control" value="<?= $i->realisasi ?>" readonly></td>
                                    <td><input type="text" class="form-control" value="<?= $i->pencapaian ?>" readonly></td>
                                    <td><input type="text" class="form-control" value="<?= $i->nilai ?>" readonly></td>
                                    <td>
                                        <select class="form-control status-select" data-id="<?= $i->id ?>">
                                            <option value="Belum Dinilai" <?= $i->status == 'Belum Dinilai' ? 'selected' : '' ?>>Belum Dinilai</option>
                                            <option value="Dinilai" <?= $i->status == 'Dinilai' ? 'selected' : '' ?>>Dinilai</option>
                                            <option value="Revisi" <?= $i->status == 'Revisi' ? 'selected' : '' ?>>Revisi</option>
                                        </select>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>

                    <button id="btn-simpan-status" class="btn btn-primary mt-2">Simpan Status</button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.getElementById('btn-simpan-status').addEventListener('click', function() {
        const updates = [];
        document.querySelectorAll('.status-select').forEach(sel => {
            updates.push({
                id: sel.dataset.id,
                status: sel.value
            });
        });

        fetch('<?= base_url("Pegawai/simpanStatusPenilaian") ?>', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(updates)
        }).then(res => res.json()).then(res => {
            if (res.status === 'success') {
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil',
                    text: 'Status berhasil disimpan',
                    timer: 2000,
                    showConfirmButton: false
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal',
                    text: 'Terjadi kesalahan'
                });
            }
        });
    });
</script>