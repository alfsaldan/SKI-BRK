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
            <h3 class="page-title mb-0 text-primary font-weight-bold">
              <i class="mdi mdi-chart-bar mr-2"></i> Rekap Nilai Akhir Pegawai
            </h3>
            <ol class="breadcrumb m-0">
              <li class="breadcrumb-item"><a href="#">KPI Online-BRKS</a></li>
              <li class="breadcrumb-item active">Rekap Nilai</li>
            </ol>
          </div>
        </div>
      </div>

      <?php if (!empty($rekap)) { ?>
        <div class="accordion" id="rekapAccordion">
          <?php $i = 1; foreach ($rekap as $r): ?>
            <div class="card shadow-sm mb-3 border-0 rounded-lg overflow-hidden">

              <!-- HEADER CARD -->
              <div class="card-header accordion-header d-flex justify-content-between align-items-center"
                id="heading<?= $i ?>" style="cursor:pointer;"
                data-toggle="collapse" data-target="#collapse<?= $i ?>"
                aria-expanded="false" aria-controls="collapse<?= $i ?>">

                <div class="d-flex align-items-center">
                  <i class="mdi mdi-calendar-range mr-2 mdi-24px"></i>
                  <h5 class="mb-0 font-weight-bold text-white">Tahun <?= $r->tahun ?></h5>
                </div>
                <div class="d-flex align-items-center">
                  <span class="badge badge-light text-dark font-weight-semibold mr-3 px-3 py-2">
                    🏅 Predikat Tahunan: <strong><?= strtoupper($r->predikat_tahunan) ?></strong>
                  </span>
                  <i class="mdi mdi-chevron-down text-white mdi-24px toggle-icon"></i>
                </div>
              </div>

              <!-- ISI CARD (COLLAPSIBLE) -->
              <div id="collapse<?= $i ?>" class="collapse" aria-labelledby="heading<?= $i ?>" data-parent="#rekapAccordion">
                <div class="card-body bg-white fade-in">
                  <div class="row">
                    <?php foreach ($r->periode as $p): ?>
                      <?php
                        $color = 'secondary';
                        if ($p->predikat == 'Very Good') $color = 'success';
                        elseif ($p->predikat == 'Good') $color = 'info';
                        elseif ($p->predikat == 'Fair') $color = 'warning';
                        elseif ($p->predikat == 'Poor') $color = 'danger';
                      ?>
                      <div class="col-md-6 col-lg-4 mb-3">
                        <div class="card border-left-<?= $color ?> shadow-sm hover-card h-100">
                          <div class="card-body">
                            <h6 class="text-primary font-weight-bold mb-2">
                              <i class="mdi mdi-calendar mr-1"></i> <?= $p->periode ?>
                            </h6>
                            <div class="small text-muted mb-3">
                              <div><strong>🎯 Nilai Sasaran:</strong> <?= $p->nilai_sasaran ?></div>
                              <div><strong>🌱 Nilai Budaya:</strong> <?= $p->nilai_budaya ?></div>
                              <div><strong>📊 Total Nilai:</strong> <?= $p->total_nilai ?></div>
                              <div><strong>🏁 Nilai Akhir:</strong> <?= $p->nilai_akhir ?></div>
                              <div><strong>🚀 Pencapaian:</strong> <?= $p->pencapaian ?></div>
                            </div>
                            <span class="badge badge-<?= $color ?> px-3 py-2 font-weight-bold shadow-sm">
                              <?= strtoupper($p->predikat) ?>
                            </span>
                          </div>
                        </div>
                      </div>
                    <?php endforeach; ?>
                  </div>

                  <div class="alert alert-info border-0 mt-3 mb-0 shadow-sm">
                    <strong>📅 Rekapitulasi Tahun <?= $r->tahun ?>:</strong><br>
                    🎯 <b>Nilai Sasaran:</b> <?= $r->rata_nilai_sasaran ?> |
                    🌱 <b>Nilai Budaya:</b> <?= $r->rata_nilai_budaya ?> |
                    📊 <b>Total Nilai:</b> <?= $r->rata_total_nilai ?> |
                    🏁 <b>Nilai Akhir:</b> <?= $r->rata_nilai_akhir ?> |
                    🚀 <b>Pencapaian:</b> <?= $r->rata_pencapaian ?>
                  </div>
                </div>
              </div>

            </div>
          <?php $i++; endforeach; ?>
        </div>
      <?php } else { ?>
        <div class="alert alert-info mt-4 shadow-sm">
          <i class="mdi mdi-information mr-1"></i> Tidak ada data rekap nilai yang tersedia.
        </div>
      <?php } ?>

    </div>
  </div>
</div>
<!-- ============================================================== -->
<!-- End Page Content -->
<!-- ============================================================== -->

<!-- Bootstrap & jQuery -->

<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>

<script>
$(document).ready(function () {
  // semua collapse tertutup awalnya
  $('.collapse').removeClass('show');

  // ubah icon saat show dan hide
  $('#rekapAccordion .collapse').on('show.bs.collapse', function () {
    const icon = $(this).prev('.card-header').find('.toggle-icon');
    icon.removeClass('mdi-chevron-down').addClass('mdi-chevron-up');
  });

  $('#rekapAccordion .collapse').on('hide.bs.collapse', function () {
    const icon = $(this).prev('.card-header').find('.toggle-icon');
    icon.removeClass('mdi-chevron-up').addClass('mdi-chevron-down');
  });
});
</script>

<style>
  .accordion-header {
    background: linear-gradient(135deg, #1976d2, #00bcd4);
    color: #fff;
    padding: 16px 20px;
    font-size: 1rem;
    border: none;
    transition: all 0.3s ease-in-out;
  }

  .accordion-header:hover {
    background: linear-gradient(135deg, #1565c0, #26c6da);
    transform: scale(1.02);
  }

  .hover-card {
    transition: all 0.25s ease;
  }

  .hover-card:hover {
    transform: translateY(-6px);
  }

  .fade-in {
    animation: fadeIn 0.3s ease-in-out;
  }

  @keyframes fadeIn {
    from {opacity: 0; transform: translateY(-5px);}
    to {opacity: 1; transform: translateY(0);}
  }

  .border-left-success { border-left: 4px solid #28a745 !important; }
  .border-left-info { border-left: 4px solid #17a2b8 !important; }
  .border-left-warning { border-left: 4px solid #ffc107 !important; }
  .border-left-danger { border-left: 4px solid #dc3545 !important; }
  .border-left-secondary { border-left: 4px solid #6c757d !important; }

  .badge { font-size: 0.85rem; }
  .small { font-size: 0.9rem; }
</style>
