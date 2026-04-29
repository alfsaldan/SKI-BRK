<!-- Modal: Upload Indikator Excel -->
<div class="modal fade" id="modalUploadExcel" role="dialog" aria-labelledby="modalUploadExcelLabel">
  <div class="modal-dialog modal-xl" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modalUploadExcelLabel">Upload Indikator dari Excel</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form id="formUploadExcel" enctype="multipart/form-data">
          <input type="hidden" name="unit_kerja">
          <input type="hidden" name="jabatan">

          <!-- Step 1: Upload -->
          <div id="stepUpload">
            <div class="form-group">
              <label>File Excel (.xls / .xlsx)</label>
              <input type="file" name="excel_file" class="form-control" accept=".xls,.xlsx" required>
            </div>
            <div class="form-group">
              <a href="<?= base_url('uploads/Template_UploadDataIndikator_Perunit_jabatan.xlsx') ?>"
                class="btn btn-link" download>
                <i class="fas fa-download"></i> Download Template
              </a>
            </div>
            <div class="form-group">
              <button type="submit" class="btn btn-primary" id="btnUploadFile">
                <i class="fas fa-search"></i> Parse &amp; Preview
              </button>
              <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
            </div>
          </div>

          <!-- Step 2: Preview -->
          <div id="stepPreview" style="display:none;">
            <h6>Preview Data yang Akan Disimpan</h6>
            <div id="previewSummary" class="mb-2"></div>
            <div style="max-height:400px;overflow:auto;">
              <table class="table table-sm table-bordered" id="previewTable">
                <thead class="thead-dark">
                  <tr>
                    <th>Perspektif</th>
                    <th>Sasaran Kerja</th>
                    <th>Indikator</th>
                    <th>Bobot (%)</th>
                  </tr>
                </thead>
                <tbody></tbody>
              </table>
            </div>
            <div id="previewErrors" class="mt-2 text-danger small"></div>
            <div class="mt-3">
              <button type="button" class="btn btn-success" id="btnSaveData">
                <i class="fas fa-save"></i> Simpan Data
              </button>
              <button type="button" class="btn btn-secondary" id="btnBackToUpload">
                <i class="fas fa-arrow-left"></i> Kembali
              </button>
            </div>
          </div>

          <!-- Step 3: Success -->
          <div id="stepSuccess" style="display:none;">
            <div class="alert alert-success text-center">
              <i class="fas fa-check-circle fa-2x mb-2"></i><br>
              Data berhasil disimpan. Halaman akan diperbarui...
            </div>
          </div>

        </form>
      </div>
    </div>
  </div>
</div>

<script>
  $(function () {

    var parsedPayload = null;

    /* ────────────────────────────────────────────────────
       FIX aria-hidden: blur fokus sebelum Bootstrap
       menutup modal dan meng-set aria-hidden="true"
    ──────────────────────────────────────────────────── */
    $('#modalUploadExcel').on('hide.bs.modal', function () {
      var modal = this;
      if (document.activeElement && (document.activeElement === modal || $.contains(modal, document.activeElement))) {
        document.activeElement.blur();
      }
      // Kembalikan fokus ke tombol pemicu agar screen reader tidak bingung
      setTimeout(function () { $('#btnUploadExcel').trigger('focus'); }, 10);
    });

    /* ── Reset saat modal dibuka ── */
    $('#modalUploadExcel').on('show.bs.modal', function () {
      var unit = $('#unit_kerja_filter').val();
      var jab = $('#jabatan_filter').val();
      $(this).find('[name="unit_kerja"]').val(unit);
      $(this).find('[name="jabatan"]').val(jab);

      parsedPayload = null;
      $('#formUploadExcel')[0].reset();
      $('#stepUpload').show();
      $('#stepPreview, #stepSuccess').hide();
      $('#previewTable tbody').empty();
      $('#previewSummary, #previewErrors').empty();
      $('#btnUploadFile').prop('disabled', false).html('<i class="fas fa-search"></i> Parse &amp; Preview');
      $('#btnSaveData').prop('disabled', false).html('<i class="fas fa-save"></i> Simpan Data');
    });

    /* ── Step 1: Upload & parse ── */
    $('#formUploadExcel').on('submit', function (e) {
      e.preventDefault();

      var fd = new FormData(this);
      $('#btnUploadFile').prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Parsing...');

      $.ajax({
        url: '<?= base_url('ExcelImport/uploadIndikatorKinerja') ?>',
        method: 'POST',
        data: fd,
        processData: false,
        contentType: false,
        dataType: 'json',
        success: function (res) {
          $('#btnUploadFile').prop('disabled', false).html('<i class="fas fa-search"></i> Parse &amp; Preview');

          if (!res || res.success === false) {
            Swal.fire('Error', (res && res.message) || 'Terjadi kesalahan saat memproses file.', 'error');
            return;
          }
          if (!res.data || Object.keys(res.data).length === 0) {
            Swal.fire('Perhatian', 'Tidak ada data indikator yang berhasil dibaca. Pastikan format file sesuai template.', 'warning');
            return;
          }

          parsedPayload = res;
          renderPreview(res);
        },
        error: function (xhr) {
          $('#btnUploadFile').prop('disabled', false).html('<i class="fas fa-search"></i> Parse &amp; Preview');
          var msg = 'Gagal memproses file Excel.';
          try { var j = JSON.parse(xhr.responseText); if (j && j.message) msg = j.message; } catch (e) { }
          Swal.fire('Error', msg, 'error');
        }
      });
    });

    /* ── Render preview tabel ── */
    function renderPreview(res) {
      $('#stepUpload').hide();
      $('#stepPreview').show();

      var tbody = $('#previewTable tbody').empty();
      var data = res.data || {};
      var summary = res.summary || {};
      var errors = res.errors || [];

      var perspektifOrder = [
        'Keuangan (F)',
        'Pelanggan (C)',
        'Proses Internal (IP)',
        'Pembelajaran & Pertumbuhan (LG)'
      ];

      var keys = Object.keys(data).sort(function (a, b) {
        var ia = perspektifOrder.indexOf(a); if (ia < 0) ia = 99;
        var ib = perspektifOrder.indexOf(b); if (ib < 0) ib = 99;
        return ia - ib;
      });

      keys.forEach(function (p) {
        var sasList = data[p];
        var sasaranKeys = Object.keys(sasList);
        var totalRows = sasaranKeys.reduce(function (n, k) { return n + sasList[k].length; }, 0);
        var perspDone = false;

        sasaranKeys.forEach(function (sas) {
          var inds = sasList[sas];
          var sasDone = false;

          inds.forEach(function (it, ii) {
            var tr = $('<tr/>');

            if (!perspDone) {
              tr.append($('<td/>').attr('rowspan', totalRows)
                .css({ 'vertical-align': 'middle', 'font-weight': 'bold', 'background-color': '#C8E6C9' })
                .text(p));
              perspDone = true;
            }
            if (!sasDone) {
              tr.append($('<td/>').attr('rowspan', inds.length)
                .css({ 'vertical-align': 'middle', 'background-color': '#BBDEFB' })
                .text(sas));
              sasDone = true;
            }

            tr.append($('<td/>').text((ii + 1) + '. ' + it.indikator));
            tr.append($('<td/>').css('text-align', 'center').text(it.bobot));
            tbody.append(tr);
          });
        });
      });

      var sumHtml = '<strong>Total Bobot per Perspektif:</strong><ul class="mb-0">';
      keys.forEach(function (k) {
        sumHtml += '<li>' + k + ': <strong>' + (summary[k] || 0) + '%</strong></li>';
      });
      sumHtml += '</ul>';
      $('#previewSummary').html(sumHtml);

      if (errors.length) {
        $('#previewErrors').html('<strong>Peringatan:</strong><br>' + errors.join('<br>'));
      } else {
        $('#previewErrors').empty();
      }
    }

    /* ── Kembali ke step 1 ── */
    $('#btnBackToUpload').on('click', function () {
      $('#stepPreview').hide();
      $('#stepUpload').show();
    });

    /* ── Step 3: Simpan
       PERBAIKAN UTAMA: kirim sebagai form POST biasa (field 'parsed' berisi JSON string)
       bukan raw JSON body — supaya CI3 bisa baca via $this->input->post('parsed')
       dan tidak 500 error karena php://input kosong di beberapa konfigurasi server
    ── */
    $('#btnSaveData').on('click', function () {
      if (!parsedPayload) {
        Swal.fire('Info', 'Tidak ada data untuk disimpan.', 'info');
        return;
      }

      var $btn = $(this).prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Menyimpan...');

      $.ajax({
        url: '<?= base_url('ExcelImport/saveParsedData') ?>',
        method: 'POST',
        // Kirim sebagai form field biasa, bukan raw JSON body
        data: { parsed: JSON.stringify(parsedPayload) },
        dataType: 'json',
        success: function (resp) {
          $btn.prop('disabled', false).html('<i class="fas fa-save"></i> Simpan Data');

          if (!resp || resp.success === false) {
            Swal.fire('Error', (resp && resp.message) || 'Gagal menyimpan data.', 'error');
            return;
          }

          // Tampilkan pesan sukses
          $('#stepPreview').hide();
          $('#stepSuccess').show();

          // Reload SETELAH modal benar-benar tertutup (event hidden.bs.modal)
          $('#modalUploadExcel').one('hidden.bs.modal', function () {
            location.reload();
          });

          // Blur fokus dulu, baru tutup modal
          setTimeout(function () {
            if (document.activeElement && (document.activeElement === $('#modalUploadExcel')[0] || $.contains($('#modalUploadExcel')[0], document.activeElement))) {
              document.activeElement.blur();
            }
            $('#modalUploadExcel').modal('hide');
          }, 900);
        },
        error: function (xhr) {
          $btn.prop('disabled', false).html('<i class="fas fa-save"></i> Simpan Data');
          var msg = 'Gagal menyimpan data ke server.';
          try { var j = JSON.parse(xhr.responseText); if (j && j.message) msg = j.message; } catch (e) { }
          Swal.fire('Error', msg + ' (HTTP ' + xhr.status + ')', 'error');
        }
      });
    });

  });
</script>