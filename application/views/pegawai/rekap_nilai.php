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
            <h3 class="page-title mb-0 text-success font-weight-bold">
              <i class="mdi mdi-chart-bar mr-2"></i> Rekap Nilai Akhir Pegawai
            </h3>
            <ol class="breadcrumb m-0">
              <li class="breadcrumb-item"><a href="#">KPI Online-BRKS</a></li>
              <li class="breadcrumb-item active">Rekap Nilai</li>
            </ol>
          </div>
        </div>
      </div>

      <style>
        .info-button {
          position: absolute;
          top: 10px;
          right: 10px;
          width: 32px;
          height: 32px;
          background-color: #86d4e1;
          /* Biru muda */
          color: #fff;
          border-radius: 50%;
          display: flex;
          align-items: center;
          justify-content: center;
          font-size: 18px;
          text-decoration: none;
          z-index: 10;
          transition: all 0.2s ease-in-out;
        }

        .info-button:hover {
          background-color: #17a2b8;
          /* Warna info */
          transform: scale(1.1);
        }
      </style>

      <?php
      // Helper function untuk render card
      if (!function_exists('render_rekap_card')) {
          function render_rekap_card($p, $column_class, $is_arsip = false)
          {
            $predikat_class = 'secondary';
            $icon = 'mdi-help-circle-outline';
            if (str_contains(strtolower($p->predikat), 'excellent')) {
              $predikat_class = 'excellent';
              $icon = 'mdi-rocket-launch';
            } elseif (str_contains(strtolower($p->predikat), 'very good')) {
              $predikat_class = 'very-good';
              $icon = 'mdi-trending-up';
            } elseif (str_contains(strtolower($p->predikat), 'good')) {
              $predikat_class = 'good';
              $icon = 'mdi-thumb-up-outline';
            } elseif (str_contains(strtolower($p->predikat), 'fair')) {
              $predikat_class = 'fair';
              $icon = 'mdi-alert-outline';
            } elseif (str_contains(strtolower($p->predikat), 'minus')) {
              $predikat_class = 'minus';
              $icon = 'mdi-trending-down';
            }
        ?>
            <div class="<?= $column_class ?> mb-4">
              <div class="card card-rekap bg-white shadow-sm hover-card h-100">
                <div class="card-body position-relative">
                  <!-- Tombol Info Detail -->
                  <?php
                  // Tentukan URL berdasarkan status arsip
                  $url = $is_arsip
                    ? base_url('Pegawai/arsipDetail/' . $p->periode_awal . '/' . $p->periode_akhir)
                    : base_url('Pegawai/index?awal=' . $p->periode_awal . '&akhir=' . $p->periode_akhir);
                  ?>
                  <a href="<?= $url ?>" class="info-button" data-toggle="tooltip" data-placement="top" title="Lihat Detail Penilaian">
                    <i class="mdi mdi-information-outline"></i>
                  </a>

                  <i class="mdi <?= $icon ?> card-icon-bg"></i>
                  <h6 class="text-predikat-<?= $predikat_class ?> font-weight-bold mb-2 text-center">
                    <i class="mdi mdi-calendar-check-outline mr-1"></i>
                    <?= $p->periode ?>
                  </h6>
                  <ul class="list-unstyled small text-muted mb-3">
                    <li class="d-flex justify-content-between">
                      <span><strong>🎯 Nilai Sasaran:</strong></span>
                      <span class="font-weight-bold text-dark"><?= $p->nilai_sasaran ?></span>
                    </li>
                    <li class="d-flex justify-content-between">
                      <span><strong>🌱 Nilai Budaya:</strong></span>
                      <span class="font-weight-bold text-dark"><?= $p->nilai_budaya ?></span>
                    </li>
                    <li class="d-flex justify-content-between">
                      <span><strong>🔀 Share KPI</strong></span>
                      <span class="font-weight-bold text-dark"><?= $p->share_kpi_value ?></span>
                    </li>
                    <li class="d-flex justify-content-between">
                      <span><strong>📊 Total Nilai:</strong></span>
                      <span class="font-weight-bold text-dark"><?= $p->total_nilai ?></span>
                    </li>
                    <li class="d-flex justify-content-between">
                      <span><strong>🚫 Fraud</strong></span>
                      <span class="font-weight-bold <?= $p->fraud == 1 ? 'text-danger' : 'text-dark' ?>"><?= $p->fraud ?></span>
                    </li>
                    <li class="d-flex justify-content-between">
                      <span><strong>🏁 Nilai Akhir:</strong></span>
                      <span class="font-weight-bold text-dark"><?= $p->nilai_akhir ?></span>
                    </li>
                    <li class="d-flex justify-content-between">
                      <span><strong>🏅 Pencapaian:</strong></span>
                      <span class="font-weight-bold text-dark"><?= $p->pencapaian ?></span>
                    </li>
                    <!-- <li class="d-flex justify-content-between">
                                <span><strong>🚨 Fraud:</strong></span>
                                <span class="font-weight-bold text-dark"><?= $p->fraud ?? '0' ?></span>
                              </li> -->

                    <!-- <?php if (isset($p->fraud) && $p->fraud == 1) : ?>
                      <li class="d-flex justify-content-between">
                        <span class="font-weight-bold text-danger mt-2"><i class="mdi mdi-alert-octagon-outline mr-1"></i>FRAUD</span>
                      </li>
                    <?php endif; ?> -->
                  </ul>
                  <span class="badge badge-predikat-<?= $predikat_class ?> px-3 py-2 font-weight-bold shadow-sm">
                    <?= strtoupper($p->predikat) ?>
                  </span>
                </div>
              </div>
            </div>
        <?php }
      } // End of helper function
      ?>

      <?php if (!empty($rekap)) { ?>
        <div class="accordion" id="rekapAccordion">
          <?php $i = 1;
          foreach ($rekap as $r): ?>
            <div class="card shadow-sm mb-3 border-0 rounded-lg overflow-hidden">

              <!-- HEADER CARD -->
              <div class="card-header accordion-header d-flex justify-content-between align-items-center"
                id="heading<?= $i ?>" style="cursor:pointer;"
                data-toggle="collapse" data-target="#collapse<?= $i ?>"
                aria-expanded="false" aria-controls="collapse<?= $i ?>">

                <h5 class="mb-0 font-weight-bold text-white d-flex align-items-center"><i class="mdi mdi-calendar-range mr-2 mdi-24px"></i> Tahun <?= $r->tahun ?></h5>
                <div class="d-flex align-items-center">
                  <?php
                  $predikat_tahunan_class = 'secondary';
                  if (str_contains(strtolower($r->predikat_tahunan), 'excellent')) {
                    $predikat_tahunan_class = 'excellent';
                  } elseif (str_contains(strtolower($r->predikat_tahunan), 'very good')) {
                    $predikat_tahunan_class = 'very-good';
                  } elseif (str_contains(strtolower($r->predikat_tahunan), 'good')) {
                    $predikat_tahunan_class = 'good';
                  } elseif (str_contains(strtolower($r->predikat_tahunan), 'fair')) {
                    $predikat_tahunan_class = 'fair';
                  } elseif (str_contains(strtolower($r->predikat_tahunan), 'minus')) {
                    $predikat_tahunan_class = 'minus';
                  }
                  ?>
                  <span class="text-predikat-<?= $predikat_tahunan_class ?> font-weight-bold mr-3" style="background-color: rgba(255,255,255,0.9); border-radius: 20px; padding: 6px 12px;">
                    🏅 Predikat Tahunan: <strong><?= strtoupper($r->predikat_tahunan) ?></strong>
                  </span>
                  <i class="mdi mdi-chevron-down text-white mdi-24px toggle-icon"></i>
                </div>
              </div>

              <!-- ISI CARD (COLLAPSIBLE) -->
              <div id="collapse<?= $i ?>" class="collapse" aria-labelledby="heading<?= $i ?>" data-parent="#rekapAccordion">
                <div class="card-body bg-white fade-in">
                  <div class="row justify-content-center">
                    <?php
                    // Tentukan class kolom berdasarkan jumlah periode aktif
                    $periode_count = count($r->periode_aktif);
                    $column_class = ($periode_count == 4) ? 'col-lg-3 col-md-6' : 'col-md-6 col-lg-4';
                    ?>

                    <!-- Bagian Penilaian Selesai (Arsip) -->
                    <?php if (!empty($r->periode_selesai)): ?>
                      <div class="row justify-content-center w-100">
                        <div class="col-12 mb-3">
                          <h5 class="text-muted font-weight-bold"><i class="mdi mdi-file-chart"></i> Arsip Penilaian Selesai</h5>
                        </div>

                        <?php
                        $last_jabatan_selesai = null;
                        $periode_selesai_tersisa = $r->periode_selesai;
                        if (isset($riwayat_jabatan) && !empty($riwayat_jabatan)) {
                          $total_riwayat = count($riwayat_jabatan);
                          foreach ($riwayat_jabatan as $index => $jabatan) {
                        ?>
                            <div class="col-12 mb-4">
                              <div class="row">
                                <!-- Info Jabatan -->
                                <div class="col-md-4 mb-3">
                                  <div class="alert alert-light border-primary shadow-sm h-100 d-flex align-items-center py-2 px-3">
                                    <i class="mdi mdi-briefcase-outline mdi-24px text-primary mr-3"></i>
                                    <div>
                                      <small class="d-block text-muted">Jabatan Sebelumnya</small>
                                      <strong class="d-block"><?= htmlspecialchars($jabatan->jabatan) ?></strong>
                                    </div>
                                  </div>
                                </div>
                                <!-- Info Unit Kantor -->
                                <div class="col-md-4 mb-3">
                                  <div class="alert alert-light border-success shadow-sm h-100 d-flex align-items-center py-2 px-3">
                                    <i class="mdi mdi-domain mdi-24px text-success mr-3"></i>
                                    <div>
                                      <small class="d-block text-muted">Unit Kantor Sebelumnya</small>
                                      <strong class="d-block"><?= htmlspecialchars($jabatan->unit_kantor) ?></strong>
                                    </div>
                                  </div>
                                </div>
                                <div class="col-md-4 mb-3">
                                  <div class="alert alert-light border-danger shadow-sm h-100 d-flex align-items-center py-2 px-3">
                                    <i class="mdi mdi-calendar-check-outline mdi-24px text-danger mr-3"></i>
                                    <div>
                                      <small class="d-block text-muted">Tanggal Selesai</small>
                                      <strong class="d-block"><?= date('d M Y', strtotime($jabatan->tgl_selesai)) ?></strong>
                                    </div>
                                  </div>
                                </div>
                              </div>
                            </div>
                            <?php
                            $periode_jabatan_ini = [];
                            $tgl_selesai_jabatan_ini = strtotime($jabatan->tgl_selesai);

                            foreach ($periode_selesai_tersisa as $key => $p) {
                              $tgl_akhir_periode = strtotime($p->periode_akhir);
                              // Tampilkan periode jika tanggal akhirnya <= tgl selesai jabatan ini
                              // DAN (ini jabatan pertama ATAU tgl akhirnya > tgl selesai jabatan sebelumnya)
                              if ($tgl_akhir_periode <= $tgl_selesai_jabatan_ini && ($last_jabatan_selesai === null || $tgl_akhir_periode > $last_jabatan_selesai)) {
                                render_rekap_card($p, $column_class, true); // Tandai sebagai arsip
                                unset($periode_selesai_tersisa[$key]); // Hapus periode yang sudah ditampilkan
                              }
                            }
                            $last_jabatan_selesai = $tgl_selesai_jabatan_ini;

                            // Tambahkan pemisah jika ini bukan riwayat jabatan terakhir
                            if ($index < $total_riwayat - 1) {
                            ?>
                              <div class="col-12 my-3">
                                <hr style="border-top: 2px dashed #ccc;">
                              </div>
                        <?php }
                          } // end foreach riwayat_jabatan
                        } // end if isset riwayat_jabatan
                        ?>
                      </div>
                      <div class="w-100"></div> <!-- Force new line -->
                      <?php if (!empty($r->periode_aktif)): ?>
                        <div class="col-12 my-3">
                          <hr style="border-top: 2px dashed #ccc;">
                        </div>
                      <?php endif; ?>
                    <?php endif; ?>

                    <!-- Bagian Penilaian Aktif -->
                    <?php if (!empty($r->periode_aktif)): ?>
                      <div class="col-12 mb-3">
                        <h5 class="text-success font-weight-bold"><i class="mdi mdi-run-fast"></i> Penilaian Aktif</h5>
                      </div>
                      <!-- Menampilkan Jabatan Sekarang -->
                      <?php if (isset($jabatan_sekarang) && $jabatan_sekarang): ?>
                        <div class="col-12 mb-3">
                          <div class="row">
                            <!-- Info Jabatan -->
                            <div class="col-md-6 mb-2">
                              <div class="alert alert-light border-primary shadow-sm h-100 d-flex align-items-center py-2 px-3">
                                <i class="mdi mdi-briefcase-outline mdi-24px text-primary mr-3"></i>
                                <div>
                                  <small class="d-block text-muted">Jabatan</small>
                                  <strong class="d-block"><?= htmlspecialchars($jabatan_sekarang->jabatan) ?></strong>
                                </div>
                              </div>
                            </div>
                            <!-- Info Unit Kantor -->
                            <div class="col-md-6 mb-2">
                              <div class="alert alert-light border-success shadow-sm h-100 d-flex align-items-center py-2 px-3">
                                <i class="mdi mdi-domain mdi-24px text-success mr-3"></i>
                                <div>
                                  <small class="d-block text-muted">Unit Kantor</small>
                                  <strong class="d-block"><?= htmlspecialchars($jabatan_sekarang->unit_kantor) ?></strong>
                                </div>
                              </div>
                            </div>
                          </div>
                        </div>
                      <?php endif; ?>
                      <div class="row justify-content-center w-100">
                        <?php foreach ($r->periode_aktif as $p): ?>
                          <?php render_rekap_card($p, $column_class); ?>
                        <?php endforeach; ?>
                      </div>
                    <?php endif; ?>
                  </div>

                  <div class="col-12 mb-3">
                    <h5 class="text-success font-weight-bold">
                      <i class="mdi mdi-medal"></i>Yudisium Akhir</h5>
                  </div>
                  <div class="card mt-0 shadow-sm">
                    <div class="card-body">
                      <div class="row text-center">
                        <div class="col">
                          <div class="rekap-item p-2 rounded">
                            <h5 class="mb-0 font-weight-bold text-predikat-<?= $predikat_tahunan_class ?>"><?= $r->rata_nilai_sasaran ?></h5>
                            <small class="text-muted">🎯 Nilai Sasaran</small>
                          </div>
                        </div>
                        <div class="col">
                          <div class="rekap-item p-2 rounded">
                            <h5 class="mb-0 font-weight-bold text-predikat-<?= $predikat_tahunan_class ?>"><?= $r->rata_nilai_budaya ?></h5>
                            <small class="text-muted">🌱 Nilai Budaya</small>
                          </div>
                        </div>
                        <div class="col">
                          <div class="rekap-item p-2 rounded">
                            <h5 class="mb-0 font-weight-bold text-predikat-<?= $predikat_tahunan_class ?>"><?= $r->rata_total_nilai ?></h5>
                            <small class="text-muted">📊 Total Nilai</small>
                          </div>
                        </div>
                        <div class="col">
                          <div class="rekap-item p-2 rounded">
                            <h5 class="mb-0 font-weight-bold text-predikat-<?= $predikat_tahunan_class ?>"><?= $r->rata_nilai_akhir ?></h5>
                            <small class="text-muted">🏁 Nilai Akhir</small>
                          </div>
                        </div>
                        <div class="col">
                          <div class="rekap-item p-2 rounded">
                            <h5 class="mb-0 font-weight-bold text-predikat-<?= $predikat_tahunan_class ?>"><?= $r->rata_pencapaian ?></h5>
                            <small class="text-muted">🏅 Pencapaian</small>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

            </div>
          <?php $i++;
          endforeach; ?>
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
  $(document).ready(function() {
    // semua collapse tertutup awalnya
    $('.collapse').removeClass('show');

    // ubah icon saat show dan hide
    $('#rekapAccordion .collapse').on('show.bs.collapse', function() {
      const icon = $(this).prev('.card-header').find('.toggle-icon');
      icon.removeClass('mdi-chevron-down').addClass('mdi-chevron-up');
    });

    $('#rekapAccordion .collapse').on('hide.bs.collapse', function() {
      const icon = $(this).prev('.card-header').find('.toggle-icon');
      icon.removeClass('mdi-chevron-up').addClass('mdi-chevron-down');
    });
  });
</script>

<style>
  .card-rekap {
    border-radius: 12px;
    border: none;
    overflow: hidden;
  }

  .card-icon-bg {
    position: absolute;
    right: 10px;
    bottom: 10px;
    font-size: 90px;
    color: rgba(0, 0, 0, 0.05);
    transform: rotate(-15deg);
    transition: transform 0.3s ease;
  }

  .card-rekap:hover .card-icon-bg {
    transform: rotate(0deg) scale(1.1);
  }

  .rekap-item {
    background-color: #ffffff;
    border: 1px solid #dee2e6;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
    transition: all 0.2s ease-in-out;
  }

  .rekap-item:hover {
    transform: translateY(-3px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
  }

  /* Warna Teks Predikat */
  .text-predikat-minus {
    color: #dc3545 !important;
  }

  .text-predikat-fair {
    color: #856404 !important;
  }

  .text-predikat-good {
    color: #17a2b8 !important;
  }

  .text-predikat-very-good {
    color: #28a745 !important;
  }

  .text-predikat-excellent {
    color: #198754 !important;
  }

  .text-predikat-secondary {
    color: #383d41 !important;
  }

  /* Warna Badge Predikat */
  .badge-predikat-minus {
    background-color: #f8d7da;
    color: #dc3545;
  }

  .badge-predikat-fair {
    background-color: #fff3cd;
    color: #856404;
  }

  .badge-predikat-good {
    background-color: #d1ecf1;
    color: #17a2b8;
  }

  .badge-predikat-very-good {
    background-color: #d4edda;
    color: #28a745;
  }

  .badge-predikat-excellent {
    background-color: #155724;
    color: #fff;
  }

  .badge-predikat-secondary {
    background-color: #e2e3e5;
    color: #383d41;
  }


  .accordion-header {
    background: linear-gradient(90deg, #16a34a, #22c55e);
    color: #fff;
    padding: 16px 20px;
    font-size: 1rem;
    border: none;
    transition: all 0.3s ease-in-out;
  }

  .accordion-header:hover {
    background: linear-gradient(90deg, #16a34a, #22c55e);
    transform: scale(1.02);
  }

  .hover-card {
    transition: all 0.25s ease;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1) !important;
  }

  .hover-card:hover {
    transform: translateY(-6px);
  }

  .fade-in {
    animation: fadeIn 0.3s ease-in-out;
  }

  @keyframes fadeIn {
    from {
      opacity: 0;
      transform: translateY(-5px);
    }

    to {
      opacity: 1;
      transform: translateY(0);
    }
  }

  .border-left-success {
    border-left: 4px solid #28a745 !important;
  }

  .border-left-info {
    border-left: 4px solid #17a2b8 !important;
  }

  .border-left-warning {
    border-left: 4px solid #ffc107 !important;
  }

  .border-left-danger {
    border-left: 4px solid #dc3545 !important;
  }

  .border-left-secondary {
    border-left: 4px solid #6c757d !important;
  }

  .badge {
    font-size: 0.85rem;
  }

  .small {
    font-size: 0.9rem;
  }
</style>