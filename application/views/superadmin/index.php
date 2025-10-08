<?php
// Pastikan timezone diset ke Indonesia
date_default_timezone_set('Asia/Jakarta');
?>
<div class="content-page">
    <div class="content">
        <div class="container-fluid">

            <!-- Judul Dashboard -->
            <div class="page-title-box d-flex justify-content-between align-items-center mb-1">
                <h3 class="page-title">
                    <i class="mdi mdi-view-dashboard mr-2 text-primary"></i> Selamat Datang di Dashboard SuperAdmin
                </h3>
            </div>

            <!-- Statistik Utama -->
            <div class="row">

                <!-- Total Pengguna -->
                <div class="col-xl-4 col-md-6">
                    <div class="card widget-box-two bg-success shadow-sm">
                        <div class="card-body">
                            <div class="float-right avatar-lg rounded-circle bg-soft-light mt-2">
                                <i class="mdi mdi-account-group font-22 avatar-title text-white"></i>
                            </div>
                            <div class="wigdet-two-content">
                                <p class="m-0 text-uppercase text-white">Total Pengguna</p>
                                <h2 class="text-white">
                                    <span data-plugin="counterup"><?= $total_users ?></span>
                                </h2>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- User Aktif -->
                <div class="col-xl-4 col-md-6">
                    <div class="card widget-box-two bg-info shadow-sm">
                        <div class="card-body">
                            <div class="float-right avatar-lg rounded-circle bg-soft-light mt-2">
                                <i class="mdi mdi-account-check font-22 avatar-title text-white"></i>
                            </div>
                            <div class="wigdet-two-content">
                                <p class="m-0 text-uppercase text-white">User Aktif</p>
                                <h2 class="text-white">
                                    <span data-plugin="counterup"><?= $statusCount['aktif'] ?></span>
                                </h2>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- User Nonaktif -->
                <div class="col-xl-4 col-md-6">
                    <div class="card widget-box-two bg-danger shadow-sm">
                        <div class="card-body">
                            <div class="float-right avatar-lg rounded-circle bg-soft-light mt-2">
                                <i class="mdi mdi-account-off font-22 avatar-title text-white"></i>
                            </div>
                            <div class="wigdet-two-content">
                                <p class="m-0 text-uppercase text-white">User Nonaktif</p>
                                <h2 class="text-white">
                                    <span data-plugin="counterup"><?= $statusCount['nonaktif'] ?></span>
                                </h2>
                            </div>
                        </div>
                    </div>
                </div>

            </div> <!-- end row -->

            <!-- Grafik Distribusi -->
            <div class="row mt-2">
                <!-- Distribusi Role -->
                <div class="col-lg-6">
                    <div class="card shadow-sm mb-4">
                        <div class="card-header bg-primary text-white">
                            <h6 class="mb-0">
                                <i class="mdi mdi-chart-pie mr-2"></i> Distribusi Role User
                            </h6>
                        </div>
                        <div class="card-body" style="height: 320px;">
                            <canvas id="chartRole"></canvas>
                        </div>
                    </div>
                </div>

                <!-- Status Keaktifan -->
                <div class="col-lg-6">
                    <div class="card shadow-sm mb-4">
                        <div class="card-header bg-success text-white">
                            <h6 class="mb-0">
                                <i class="mdi mdi-chart-donut mr-2"></i> Status Keaktifan User
                            </h6>
                        </div>
                        <div class="card-body" style="height: 320px;">
                            <canvas id="chartStatus"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Peringatan Pintar -->
            <?php if ($statusCount['nonaktif'] > $statusCount['aktif'] / 2): ?>
                <div class="alert alert-danger mt-3 shadow-sm">
                    <i class="mdi mdi-alert-circle mr-2"></i>
                    Banyak pengguna nonaktif! Pertimbangkan untuk mengevaluasi aktivitas pengguna.
                </div>
            <?php endif; ?>

        </div> <!-- container-fluid -->

        <!-- Insight Utama -->
        <div class="row mt-2 mb-3">
            <div class="col-md-4">
                <div class="alert alert-primary shadow-sm">
                    <i class="mdi mdi-trending-up mr-2"></i>
                    <strong><?= round(($statusCount['aktif'] / max($total_users, 1)) * 100) ?>%</strong> pengguna aktif dari total pengguna.
                </div>
            </div>
            <div class="col-md-4">
                <div class="alert alert-info shadow-sm">
                    <i class="mdi mdi-account-star mr-2"></i>
                    Role terbanyak:
                    <strong>
                        <?php
                        $maxRole = array_search(max($roleCount), $roleCount);
                        echo ucfirst($maxRole);
                        ?>
                    </strong>
                </div>
            </div>
            <div class="col-md-4">
                <div class="alert alert-warning shadow-sm">
                    <i class="mdi mdi-clock-outline mr-2"></i>
                    Terakhir diperbarui: <strong id="waktu-update"></strong>
                </div>
            </div>
        </div>
    </div> <!-- content -->
</div>

<!-- Script Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // 🔹 Format waktu update
    const now = new Date();
    const options = {
        day: '2-digit',
        month: 'short',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
        hour12: false,
        timeZone: 'Asia/Jakarta'
    };
    document.getElementById('waktu-update').textContent = now.toLocaleString('id-ID', options);

    // 🔹 Data dari PHP
    const roleLabels = <?= json_encode(array_keys($roleCount)) ?>;
    const roleValues = <?= json_encode(array_values($roleCount)) ?>;

    const statusLabels = ['Aktif', 'Nonaktif'];
    const statusValues = [<?= $statusCount['aktif'] ?>, <?= $statusCount['nonaktif'] ?>];

    // 🔹 Variabel chart agar tidak ganda
    let chartRoleInstance = null;
    let chartStatusInstance = null;

    // 🔹 Render Chart Role
    function renderChartRole() {
        const ctx = document.getElementById('chartRole').getContext('2d');
        if (chartRoleInstance) chartRoleInstance.destroy();

        chartRoleInstance = new Chart(ctx, {
            type: 'pie',
            data: {
                labels: roleLabels,
                datasets: [{
                    data: roleValues,
                    backgroundColor: ['#007bff', '#ffc107', '#28a745', '#dc3545', '#6f42c1', '#17a2b8']
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom'
                    },
                    title: {
                        display: true,
                        text: 'Distribusi Role Pengguna',
                        font: {
                            size: 16
                        }
                    }
                }
            }
        });
    }

    // 🔹 Render Chart Status
    function renderChartStatus() {
        const ctx = document.getElementById('chartStatus').getContext('2d');
        if (chartStatusInstance) chartStatusInstance.destroy();

        chartStatusInstance = new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: statusLabels,
                datasets: [{
                    data: statusValues,
                    backgroundColor: ['#28a745', '#dc3545']
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom'
                    },
                    title: {
                        display: true,
                        text: 'Status Keaktifan Pengguna',
                        font: {
                            size: 16
                        }
                    }
                }
            }
        });
    }

    // 🔹 Jalankan setelah halaman siap
    document.addEventListener("DOMContentLoaded", () => {
        renderChartRole();
        renderChartStatus();
    });
</script>