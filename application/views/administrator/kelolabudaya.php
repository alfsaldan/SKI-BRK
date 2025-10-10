<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<div class="content-page">
    <div class="content">
        <div class="container-fluid">

            <!-- Judul Halaman -->
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-flex justify-content-between align-items-center">
                        <h3 class="page-title">
                             <i class="mdi mdi-white-balance-sunny mr-2 text-primary"></i> Kelola Budaya
                        </h3>
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="#">KPI Online-BRKS</a></li>
                            <li class="breadcrumb-item active">Kelola Budaya</li>
                        </ol>
                    </div>
                </div>
            </div>
            
            <!-- Tombol Tambah -->
            <button class="btn btn-success mb-3 shadow-sm" id="btnTambah">
                <i class="mdi mdi-plus"></i> Tambah Budaya
            </button>

            <!-- Card Tabel -->
            <div class="card shadow-lg border-0">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="mdi mdi-format-list-bulleted"></i> Data Budaya</h5>
                </div>
                <div class="card-body bg-light">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover table-striped" id="tabelBudaya">
                            <thead class="text-center bg-success text-white">
                                <tr>
                                    <th width="5%">No</th>
                                    <th>Perilaku Utama</th>
                                    <th>Panduan Perilaku</th>
                                    <th width="15%">Aksi</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Modal Tambah/Edit -->
            <div class="modal fade" id="modalBudaya" tabindex="-1" role="dialog" aria-labelledby="modalBudayaLabel" aria-hidden="true">
                <div class="modal-dialog modal-lg" role="document">
                    <div class="modal-content shadow">
                        <form id="formBudaya">
                            <div class="modal-header bg-success text-white">
                                <h5 class="modal-title" id="modalBudayaLabel">Tambah Budaya</h5>
                                <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
                            </div>
                            <div class="modal-body bg-light">
                                <input type="hidden" name="id_budaya" id="id_budaya">

                                <div class="form-group">
                                    <label><strong>Perilaku Utama</strong></label>
                                    <input type="text" class="form-control" name="perilaku_utama" id="perilaku_utama" placeholder="Masukkan perilaku utama..." required>
                                </div>

                                <div class="form-group">
                                    <label><strong>Panduan Perilaku</strong></label>
                                    <div id="panduanContainer">
                                        <div class="input-group mb-2 panduan-item">
                                            <input type="text" name="panduan_perilaku[]" class="form-control" placeholder="Isi panduan perilaku..." required>
                                            <div class="input-group-append">
                                                <button type="button" class="btn btn-danger removePanduan">&times;</button>
                                            </div>
                                        </div>
                                    </div>
                                    <button type="button" id="addPanduan" class="btn btn-info btn-sm mt-1">
                                        <i class="mdi mdi-plus"></i> Tambah Panduan
                                    </button>
                                </div>
                            </div>
                            <div class="modal-footer bg-white">
                                <button type="submit" class="btn btn-success">Simpan</button>
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<!-- JS -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    $(document).ready(function() {
        loadBudaya();

        // Tambah panduan baru
        $(document).on('click', '#addPanduan', function() {
            $('#panduanContainer').append(`
                <div class="input-group mb-2 panduan-item">
                    <input type="text" name="panduan_perilaku[]" class="form-control" placeholder="Isi panduan perilaku..." required>
                    <div class="input-group-append">
                        <button type="button" class="btn btn-danger removePanduan">&times;</button>
                    </div>
                </div>
            `);
        });

        // Hapus panduan input
        $(document).on('click', '.removePanduan', function() {
            $(this).closest('.panduan-item').remove();
        });

        // Modal tambah
        $('#btnTambah').on('click', function() {
            $('#modalBudayaLabel').text('Tambah Budaya');
            $('#formBudaya')[0].reset();
            $('#id_budaya').val('');
            $('#panduanContainer').html(`
                <div class="input-group mb-2 panduan-item">
                    <input type="text" name="panduan_perilaku[]" class="form-control" placeholder="Isi panduan perilaku..." required>
                    <div class="input-group-append">
                        <button type="button" class="btn btn-danger removePanduan">&times;</button>
                    </div>
                </div>
            `);
            $('#modalBudaya').modal('show');
        });

        // Simpan (Tambah/Edit)
        $('#formBudaya').submit(function(e) {
            e.preventDefault();
            $.ajax({
                url: '<?= base_url("Administrator/simpanBudayaAjax") ?>',
                type: 'POST',
                data: $(this).serialize(),
                dataType: 'json',
                success: function(res) {
                    if (res.status === 'success') {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil',
                            text: res.message,
                            timer: 1500,
                            showConfirmButton: false
                        });
                        $('#modalBudaya').modal('hide');
                        setTimeout(() => loadBudaya(), 800);
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal',
                            text: res.message
                        });
                    }
                },
                error: function() {
                    Swal.fire('Error', 'Terjadi kesalahan saat menyimpan data', 'error');
                }
            });
        });

        // Edit data
        $(document).on('click', '.editBudaya', function() {
            const id = $(this).data('id');
            const nama = $(this).data('nama');
            const panduan = JSON.parse($(this).attr('data-panduan'));
            $('#modalBudayaLabel').text('Edit Budaya');
            $('#id_budaya').val(id);
            $('#perilaku_utama').val(nama);
            $('#panduanContainer').html('');
            panduan.forEach(p => {
                $('#panduanContainer').append(`
                    <div class="input-group mb-2 panduan-item">
                        <input type="text" name="panduan_perilaku[]" class="form-control" value="${p}" required>
                        <div class="input-group-append">
                            <button type="button" class="btn btn-danger removePanduan">&times;</button>
                        </div>
                    </div>
                `);
            });
            $('#modalBudaya').modal('show');
        });

        // Hapus data
        $(document).on('click', '.hapusBudaya', function() {
            const id = $(this).data('id');
            Swal.fire({
                title: 'Yakin hapus data ini?',
                text: 'Data budaya akan dihapus permanen!',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '<?= base_url("Administrator/hapusBudayaAjax/") ?>' + id,
                        type: 'GET',
                        dataType: 'json',
                        success: function(res) {
                            Swal.fire({
                                icon: res.status === 'success' ? 'success' : 'error',
                                title: res.title,
                                text: res.message,
                                timer: 1500,
                                showConfirmButton: false
                            });
                            setTimeout(() => loadBudaya(), 800);
                        },
                        error: function() {
                            Swal.fire('Error', 'Tidak dapat menghapus data', 'error');
                        }
                    });
                }
            });
        });

        // Load data budaya
        function loadBudaya() {
            $.ajax({
                url: '<?= base_url("Administrator/getBudaya") ?>',
                type: 'GET',
                dataType: 'json',
                success: function(data) {
                    let html = '';
                    let no = 1;
                    data.forEach(b => {
                        const panduan = JSON.parse(b.panduan_perilaku);
                        let list = '<ul class="mb-0">';
                        panduan.forEach(p => list += `<li>${p}</li>`);
                        list += '</ul>';
                        html += `
                            <tr>
                                <td class="text-center align-middle">${no++}</td>
                                <td class="align-middle">${b.perilaku_utama}</td>
                                <td>${list}</td>
                                <td class="text-center align-middle">
                                    <button class="btn btn-warning btn-sm editBudaya"
                                        data-id="${b.id_budaya}"
                                        data-nama="${b.perilaku_utama}"
                                        data-panduan='${b.panduan_perilaku}'>
                                        <i class="mdi mdi-pencil"></i>
                                    </button>
                                    <button class="btn btn-danger btn-sm hapusBudaya" data-id="${b.id_budaya}">
                                        <i class="mdi mdi-delete"></i>
                                    </button>
                                </td>
                            </tr>
                        `;
                    });
                    $('#tabelBudaya tbody').html(html);
                },
                error: function() {
                    $('#tabelBudaya tbody').html('<tr><td colspan="4" class="text-center text-danger">Gagal memuat data</td></tr>');
                }
            });
        }
    });
</script>