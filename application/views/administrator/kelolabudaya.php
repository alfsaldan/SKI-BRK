<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div class="content-page">
    <div class="content">
        <div class="container-fluid">
            <h4 class="page-title mb-3">Kelola Budaya</h4>

            <button class="btn btn-success mb-3" id="btnTambah">
                <i class="mdi mdi-plus"></i> Tambah Budaya
            </button>

            <div class="table-responsive">
                <table class="table table-bordered table-striped" id="tabelBudaya">
                    <thead class="text-center">
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

            <!-- Modal -->
            <div class="modal fade" id="modalBudaya" tabindex="-1" role="dialog" aria-labelledby="modalBudayaLabel" aria-hidden="true">
                <div class="modal-dialog modal-lg" role="document">
                    <div class="modal-content">
                        <form id="formBudaya">
                            <div class="modal-header bg-success text-white">
                                <h5 class="modal-title" id="modalBudayaLabel">Tambah Budaya</h5>
                                <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
                            </div>
                            <div class="modal-body">
                                <input type="hidden" name="id_budaya" id="id_budaya">
                                <div class="form-group">
                                    <label>Perilaku Utama</label>
                                    <input type="text" class="form-control" name="perilaku_utama" id="perilaku_utama" required>
                                </div>

                                <div class="form-group">
                                    <label>Panduan Perilaku</label>
                                    <div id="panduanContainer">
                                        <div class="input-group mb-2 panduan-item">
                                            <input type="text" name="panduan_perilaku[]" class="form-control" placeholder="Isi panduan perilaku..." required>
                                            <div class="input-group-append">
                                                <button type="button" class="btn btn-danger removePanduan">&times;</button>
                                            </div>
                                        </div>
                                    </div>
                                    <button type="button" id="addPanduan" class="btn btn-info btn-sm">
                                        <i class="mdi mdi-plus"></i> Tambah Panduan
                                    </button>
                                </div>
                            </div>
                            <div class="modal-footer">
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
    $(document).on('click', '#addPanduan', function(){
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
    $(document).on('click', '.removePanduan', function(){
        $(this).closest('.panduan-item').remove();
    });

    // Tampilkan modal tambah
    $('#btnTambah').on('click', function(){
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

    // Simpan data (Tambah/Edit)
    $('#formBudaya').submit(function(e){
        e.preventDefault();
        $.ajax({
            url: '<?= base_url("Administrator/simpanBudayaAjax") ?>',
            type: 'POST',
            data: $(this).serialize(),
            dataType: 'json',
            success: function(res){
                if(res.status === 'success'){
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil',
                        text: res.message,
                        timer: 1500,
                        showConfirmButton: false
                    });
                    $('#modalBudaya').modal('hide');
                    loadBudaya();
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal',
                        text: res.message
                    });
                }
            },
            error: function(){
                Swal.fire('Error', 'Terjadi kesalahan saat menyimpan data', 'error');
            }
        });
    });

    // Edit data
    $(document).on('click', '.editBudaya', function(){
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
    $(document).on('click', '.hapusBudaya', function(){
        const id = $(this).data('id');
        Swal.fire({
            title: 'Hapus Data?',
            text: 'Data budaya akan dihapus permanen!',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '<?= base_url("Administrator/hapusBudayaAjax/") ?>' + id,
                    type: 'GET',
                    dataType: 'json',
                    success: function(res){
                        Swal.fire({
                            icon: res.status === 'success' ? 'success' : 'error',
                            title: res.title,
                            text: res.message,
                            timer: 1500,
                            showConfirmButton: false
                        });
                        loadBudaya();
                    }
                });
            }
        });
    });

    // Load data budaya
    function loadBudaya(){
        $.ajax({
            url: '<?= base_url("Administrator/getBudaya") ?>',
            type: 'GET',
            dataType: 'json',
            success: function(data){
                let html = '';
                let no = 1;
                data.forEach(b => {
                    const panduan = JSON.parse(b.panduan_perilaku);
                    let list = '<ul class="mb-0">';
                    panduan.forEach(p => list += `<li>${p}</li>`);
                    list += '</ul>';
                    html += `
                        <tr>
                            <td class="text-center">${no++}</td>
                            <td>${b.perilaku_utama}</td>
                            <td>${list}</td>
                            <td class="text-center">
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
            }
        });
    }
});
</script>
