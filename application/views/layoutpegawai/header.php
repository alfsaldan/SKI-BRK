<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <title>SKI-BRKS</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="A fully featured admin theme which can be used to build CRM, CMS, etc." name="description" />
    <meta content="Coderthemes" name="author" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <!-- App favicon -->
    <link rel="shortcut icon" href="<?= base_url('assets/images/LogoKapalBRK.png') ?>">

    <!-- Bootstrap select pluings -->
    <link href="<?= base_url('assets/libs/bootstrap-select/bootstrap-select.min.css') ?>" rel="stylesheet"
        type="text/css" />
    <!-- Table datatable css -->
    <link href="<?= base_url(' assets/libs/datatables/dataTables.bootstrap4.min.css') ?>" rel="stylesheet"
        type="text/css" />
    <link href="<?= base_url('assets/libs/datatables/responsive.bootstrap4.min.css') ?>" rel="stylesheet"
        type="text/css" />
    <link href="<?= base_url('assets/libs/datatables/buttons.bootstrap4.min.css') ?>" rel="stylesheet"
        type="text/css" />
    <link href="<?= base_url('assets/libs/datatables/fixedHeader.bootstrap4.min.css') ?>" rel="stylesheet"
        type="text/css" />
    <link href="<?= base_url('assets/libs/datatables/scroller.bootstrap4.min.css') ?>" rel="stylesheet"
        type="text/css" />
    <link href="<?= base_url('assets/libs/datatables/dataTables.colVis.css') ?>" rel="stylesheet" type="text/css" />
    <link href="<?= base_url('assets/libs/datatables/fixedColumns.bootstrap4.min.css') ?>" rel="stylesheet"
        type="text/css" />

    <!-- c3 plugin css -->
    <link rel="stylesheet" type="text/css" href="<?= base_url('assets/libs/c3/c3.min.css') ?>">

    <!-- App css -->
    <link href="<?= base_url('assets/css/bootstrap.min.css') ?>" rel="stylesheet" type="text/css" />
    <link href="<?= base_url('assets/css/icons.min.css') ?>" rel="stylesheet" type="text/css" />
    <link href="<?= base_url('assets/css/app.min.css') ?>" rel="stylesheet" type="text/css" />

    <script src="<?= base_url('assets/js/jquery.min.js') ?>"></script>
    <script src="<?= base_url('assets/libs/jquery-steps/jquery.steps.min.js') ?>"></script>
    <link href="<?= base_url('assets/libs/jquery-steps/jquery.steps.css') ?>" rel="stylesheet">


    <!-- Custom CSS Navbar & Sidebar -->
    <style>
        /* Sidebar container */
        #sidebar-menu {
            background: #ffffff;
            color: #94a3b8;
        }

        #sidebar-menu ul li a {
            display: flex;
            align-items: center;
            padding: 8px 14px;
            border-radius: 10px;
            color: #6b7280;
            font-weight: 500;
            font-size: 13px;
            transition: all 0.3s ease;
            margin: 4px 8px;
        }

        #sidebar-menu ul li a i {
            margin-right: 10px;
            font-size: 18px;
            transition: transform 0.3s ease, color 0.3s ease;
        }

        /* Hover efek — gradasi hijau lembut */
        #sidebar-menu ul li a:hover {
            background: linear-gradient(90deg, #16a34a, #22c55e);
            color: #ffffff !important;
            box-shadow: 0 0 8px rgba(34, 197, 94, 0.5);
            transform: translateX(3px);
        }

        #sidebar-menu ul li a:hover i {
            color: #fff;
            transform: scale(1.05);
        }

        /* Aktif — hijau gradasi tetap nyala */
        #sidebar-menu ul li.active>a {
            background: linear-gradient(90deg, #15803d, #22c55e);
            color: #fff !important;
            font-weight: 600;
            box-shadow: 0 0 10px rgba(34, 197, 94, 0.6);
        }

        #sidebar-menu ul li.active>a i {
            color: #fff !important;
            transform: scale(1.05);
        }

        /* Mengurangi indentasi dropdown agar sejajar dengan menu utama */
        #sidebar-menu ul ul {
            padding-left: 0 !important;
        }

        /* Section title */
        .menu-title {
            padding: 10px 16px;
            text-transform: uppercase;
            font-size: 11px;
            letter-spacing: 1px;
            color: #9ca3af;
            font-weight: 700;
            margin: 15px 0 5px 0;
        }

        /* Navbar putih dengan shadow ringan */
        .navbar-custom {
            background-color: #ffffff !important;
            border-bottom: 1px solid #e5e5e5;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.08);
        }

        .topnav-menu>li>a {
            color: #333333;
            font-weight: 500;
        }

        .topnav-menu>li>a:hover {
            color: #27AE60;
        }

        .noti-icon {
            color: #333333;
        }

        .noti-icon-badge {
            background-color: #E74C3C;
            color: #ffffff;
            font-size: 0.65rem;
            top: 5px;
            right: 5px;
        }

        .logo-box {
            background-color: transparent;
            text-align: center;
        }

        .logo-box img {
            max-height: 32px;
        }

        .left-side-menu {
            background-color: #ffffff;
            border-right: 1px solid #e5e5e5;
        }

        .metismenu a {
            color: #495057;
            font-weight: 500;
        }

        .metismenu a:hover,
        .metismenu li.active>a {
            color: #277caeff;
            background-color: rgba(39, 174, 96, 0.05);
            border-radius: 0.25rem;
        }

        .button-menu-mobile {
            background-color: transparent;
            border: none;
            padding: 6px 10px;
        }

        .button-menu-mobile i.fe-menu {
            color: #333333;
            font-size: 18px;
        }

        .button-menu-mobile:hover i.fe-menu {
            color: #277caeff;
        }

        /* Custom style for dropdown headers to resemble menu-title */
        #sidebar-menu ul li a.special-header-link {
            text-transform: uppercase;
            font-size: 11px;
            letter-spacing: 1px;
            color: #9ca3af;
            font-weight: 700;
            background: transparent;
            box-shadow: none;
            margin: 15px 0 5px 0;
            padding: 10px 16px;
            border-radius: 0;
        }

        #sidebar-menu ul li a.special-header-link:hover,
        #sidebar-menu ul li.active>a.special-header-link {
            background: transparent !important;
            color: #6b7280 !important;
            box-shadow: none !important;
            transform: none !important;
        }

        #sidebar-menu ul li a.special-header-link i {
            font-size: 14px;
            color: #9ca3af;
            margin-right: 8px;
        }

        #sidebar-menu ul li a.special-header-link:hover i,
        #sidebar-menu ul li.active>a.special-header-link i {
            color: #6b7280 !important;
            transform: none !important;
        }

        /* Pastikan dropdown tetap terbuka ketika parent aktif */
        body:not(.enlarged) .nav-first-level.in,
        body:not(.enlarged) .nav-second-level.in {
            display: block !important;
            opacity: 1 !important;
            visibility: visible !important;
            height: auto !important;
            overflow: visible !important;
        }

        /* Force dropdown container tetap visible */
        body:not(.enlarged) #sidebar-menu li.active > ul {
            display: block !important;
        }

        /* Fix: Saat sidebar collapsed, sembunyikan dropdown aktif dan text */
        body.enlarged #sidebar-menu li.active > ul {
            display: none !important;
        }
        body.enlarged #sidebar-menu li.active:hover > ul {
            display: block !important;
        }
        body.enlarged #sidebar-menu > ul > li > a span {
            display: none !important;
        }
    </style>

</head>

<body>

    <!-- Begin page -->
    <div id="wrapper">
        <!-- Topbar Start -->
        <div class="navbar-custom">
            <ul class="list-unstyled topnav-menu float-right mb-0">

                <!-- <li class="d-none d-sm-block">
                    <form class="app-search">
                        <div class="app-search-box">
                            <div class="input-group">
                                <input type="text" class="form-control" placeholder="Search...">
                                <div class="input-group-append">
                                    <button class="btn" type="submit">
                                        <i class="fe-search"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </li> -->

                <li class="dropdown notification-list">
                    <a class="nav-link dropdown-toggle  waves-effect waves-light" data-toggle="dropdown" href="#"
                        role="button" aria-haspopup="false" aria-expanded="false">
                        <i class="fe-bell noti-icon"></i>
                        <span class="badge badge-danger rounded-circle noti-icon-badge" id="chat-unread-count">0</span>
                    </a>
                    <div class="dropdown-menu dropdown-menu-right dropdown-lg">
                        <div class="dropdown-item noti-title">
                            <h5 class="m-0">
                                <span class="float-right">
                                    <a href="javascript:void(0);" class="text-dark" id="clear-chat-notif">
                                        <small>Clear All</small>
                                    </a>
                                </span>Notification
                            </h5>
                        </div>
                        <div class="slimscroll noti-scroll" id="chat-unread-list">
                            <!-- Pesan baru room chat akan di-load via AJAX -->
                        </div>
                        <a href="javascript:void(0);"
                            class="dropdown-item text-center text-primary notify-item notify-all">
                            View all
                            <i class="fi-arrow-right"></i>
                        </a>
                    </div>
                </li>

                <li class="dropdown notification-list">
                    <a class="nav-link dropdown-toggle nav-user mr-0 waves-effect waves-light" data-toggle="dropdown"
                        href="#" role="button" aria-haspopup="false" aria-expanded="false">
                        <img src="<?= base_url('assets/images/users/avatar-1.png') ?>" alt="user-image"
                            class="rounded-circle">
                    </a>
                    <div class="dropdown-menu dropdown-menu-right profile-dropdown ">
                        <!-- item-->
                        <div class="dropdown-header noti-title">
                            <h6 class="text-overflow m-0">Selamat Datang</h6>
                        </div>

                        <!-- item-->
                        <a href="<?= base_url('pegawai/datadiriPegawai'); ?>" class="dropdown-item notify-item">
                            <i class="fe-user"></i>
                            <span>Data Diri</span>
                        </a>


                        <!-- item-->
                        <!-- <a href="javascript:void(0);" class="dropdown-item notify-item">
                            <i class="fe-settings"></i>
                            <span>Pengaturan</span>
                        </a> -->

                        <div class="dropdown-divider"></div>

                        <!-- item-->
                        <a href="<?= base_url('auth/logout'); ?>" class="dropdown-item notify-item">
                            <i class="fe-log-out"></i>
                            <span>Logout</span>
                        </a>


                    </div>
                </li>

            </ul>

            <!-- LOGO -->
            <div class="logo-box">
                <a href="<?= base_url('pegawai'); ?>" class="logo text-center">
                    <span class="logo-lg">
                        <img src="<?= base_url('assets/images/Logo_BRK_Syariah.png') ?>" alt="Logo BRK Syariah"
                            height="20">
                    </span>
                    <span class="logo-sm">
                        <img src="<?= base_url('assets/images/LogoKapalBRK.png') ?>" alt="Logo Kapal BRK" height="24">
                    </span>
                </a>
            </div>


            <ul class="list-unstyled topnav-menu topnav-menu-left m-0">
                <li>
                    <button class="button-menu-mobile waves-effect waves-light">
                        <i class="fe-menu"></i>
                    </button>
                </li>

            </ul>
        </div>
        <!-- end Topbar -->


        <!-- ========== Left Sidebar Start ========== -->
        <?php
        $activeController = strtolower($this->uri->segment(1, ''));
        $activeMethod     = strtolower($this->uri->segment(2, ''));
        $role             = $this->session->userdata('role'); // administrator / pegawai
        ?>
        <div class="left-side-menu">
            <div class="slimscroll-menu">
                <div id="sidebar-menu">
                    <ul class="metismenu" id="side-menu">

                        <?php if ($role == 'administrator'): ?>
                            <li class="menu-title">Halaman Utama</li>

                            <li class="<?= ($activeController == 'administrator' && $activeMethod == '') ? 'active' : '' ?>">
                                <a href="<?= base_url('administrator') ?>">
                                    <i class="fe-airplay"></i>
                                    <span> Dashboard </span>
                                </a>
                            </li>

                            <?php
                            $fiturUtamaActive = ($activeController == 'administrator' && in_array($activeMethod, [
                                'keloladatapegawai',
                                'detailpegawai',
                                'indikatorkinerja',
                                'penilaiankinerja',
                                'caripenilaian',
                                'verifikasi_penilaian',
                                'detailverifikasi',
                                'datapegawai',
                                'caridatapegawai',
                                'monitoringkinerja',
                                'caripenilaianbulanan',
                            ]));
                            ?>
                            <li class="<?= $fiturUtamaActive ? 'active' : '' ?>">
                                <a href="javascript: void(0);" class="special-header-link">
                                    <span> Fitur Utama </span>
                                    <span class="menu-arrow"></span>
                                </a>
                                <ul class="nav-first-level <?= $fiturUtamaActive ? 'in' : '' ?>" aria-expanded="<?= $fiturUtamaActive ? 'true' : 'false' ?>">
                                    <li class="<?= ($activeController == 'administrator' && in_array($activeMethod, ['keloladatapegawai', 'detailpegawai'])) ? 'active' : '' ?>">
                                        <a href="<?= base_url('administrator/keloladatapegawai') ?>">
                                            <i class="mdi mdi-account-card-details"></i>
                                            <span>Kelola Data Pegawai</span>
                                        </a>
                                    </li>

                                    <li class="<?= ($activeController == 'administrator' && in_array($activeMethod, ['indikatorkinerja'])) ? 'active' : '' ?>">
                                        <a href="<?= base_url('administrator/indikatorkinerja') ?>">
                                            <i class="mdi mdi-target-account"></i>
                                            <span> Indikator Kinerja </span>
                                        </a>
                                    </li>

                                    <li class="<?= ($activeController == 'administrator' && in_array($activeMethod, ['penilaiankinerja', 'caripenilaian'])) ? 'active' : '' ?>">
                                        <a href="<?= base_url('administrator/penilaiankinerja') ?>">
                                            <i class="mdi mdi-account-edit"></i>
                                            <span> Penilaian Kinerja </span>
                                        </a>
                                    </li>

                                    <li class="<?= ($activeController == 'administrator' && in_array($activeMethod, ['verifikasi_penilaian', 'detailverifikasi'])) ? 'active' : '' ?>">
                                        <a href="<?= base_url('administrator/verifikasi_penilaian') ?>">
                                            <i class="mdi mdi-clipboard-check-outline"></i>
                                            <span> Verifikasi Penilaian </span>
                                        </a>
                                    </li>

                                    <li class="<?= ($activeController == 'administrator' && in_array($activeMethod, ['datapegawai', 'caridatapegawai'])) ? 'active' : '' ?>">
                                        <a href="<?= base_url('administrator/datapegawai') ?>">
                                            <i class="mdi mdi-account-card-details"></i>
                                            <span> Cek Kinerja Pegawai </span>
                                        </a>
                                    </li>

                                    <li class="<?= ($activeController == 'administrator' && in_array($activeMethod, ['monitoringkinerja', 'caripenilaianbulanan'])) ? 'active' : '' ?>">
                                        <a href="<?= base_url('administrator/monitoringkinerja') ?>">
                                            <i class="mdi mdi-clipboard-pulse"></i>
                                            <span> Monitoring Kinerja </span>
                                        </a>
                                    </li>
                                </ul>
                            </li>
                            <?php
                            $peningkatanKinerjaActive = ($activeController == 'administrator' && in_array($activeMethod, [
                                'verifikasi_ppk',
                                'monitoring_ppk'
                            ]));
                            ?>
                            <li class="<?= $peningkatanKinerjaActive ? 'active' : '' ?>">
                                <a href="javascript: void(0);" class="special-header-link">
                                    <span> Peningkatan Kinerja </span>
                                    <span class="menu-arrow"></span>
                                </a>
                                <ul class="nav-first-level <?= $peningkatanKinerjaActive ? 'in' : '' ?>" aria-expanded="<?= $peningkatanKinerjaActive ? 'true' : 'false' ?>">
                                    <li class="<?= ($activeController == 'administrator' && in_array($activeMethod, ['verifikasi_ppk'])) ? 'active' : '' ?>">
                                        <a href="<?= base_url('administrator/verifikasi_ppk') ?>">
                                            <i class="mdi mdi-account-group"></i>
                                            <span> Verifikasi PPK </span>
                                        </a>
                                    </li>

                                    <li class="<?= ($activeController == 'administrator' && in_array($activeMethod, ['monitoring_ppk'])) ? 'active' : '' ?>">
                                        <a href="<?= base_url('administrator/monitoring_ppk') ?>">
                                            <i class="mdi mdi-account-badge-alert-outline"></i>
                                            <span> Monitoring PPK </span>
                                        </a>
                                    </li>
                                </ul>
                            </li>

                            <?php
                            $lainnyaActive = ($activeController == 'administrator' && in_array($activeMethod, [
                                'kelolatingkatanjabatan',
                                'kelolabudaya'
                            ]));
                            ?>
                            <li class="<?= $lainnyaActive ? 'active' : '' ?>">
                                <a href="javascript: void(0);" class="special-header-link">
                                    <span> Lainnya </span>
                                    <span class="menu-arrow"></span>
                                </a>
                                <ul class="nav-second-level <?= $lainnyaActive ? 'in' : '' ?>" aria-expanded="<?= $lainnyaActive ? 'true' : 'false' ?>">
                                    <li class="<?= ($activeController == 'administrator' && $activeMethod == 'kelolatingkatanjabatan') ? 'active' : '' ?>">
                                        <a href="<?= base_url('administrator/kelolatingkatanjabatan') ?>">
                                            <i class="mdi mdi-briefcase"></i>
                                            <span>Kelola Jabatan </span>
                                        </a>
                                    </li>

                                    <li class="<?= ($activeController == 'administrator' && $activeMethod == 'kelolabudaya') ? 'active' : '' ?>">
                                        <a href="<?= base_url('administrator/kelolabudaya') ?>">
                                            <i class="mdi mdi-white-balance-sunny"></i>
                                            <span>Kelola Budaya</span>
                                        </a>
                                    </li>
                                </ul>
                            </li>
                        <?php endif; ?>

                        <?php
                        $halamanPegawaiActive = ($activeController == 'pegawai' && in_array($activeMethod, [
                            '',
                            'index',
                            'rekapnilaipegawai',
                            'monitoringindividu',
                            'caripenilaianbulanan',
                            'ppk_pegawai'
                        ]));
                        ?>
                        <li class="<?= $halamanPegawaiActive ? 'active' : '' ?>">
                            <a href="javascript: void(0);" class="special-header-link">
                                <span> Halaman Pegawai </span>
                                <span class="menu-arrow"></span>
                            </a>
                            <ul class="nav-second-level <?= $halamanPegawaiActive ? 'in' : '' ?>" aria-expanded="<?= $halamanPegawaiActive ? 'true' : 'false' ?>">
                                <li class="<?= ($activeController == 'pegawai' && in_array($activeMethod, ['', 'index'])) ? 'active' : '' ?>">
                                    <a href="<?= base_url('pegawai') ?>">
                                        <i class="mdi mdi-account"></i>
                                        <span> Kinerja Individu </span>
                                    </a>
                                </li>
                                <li class="<?= ($activeController == 'pegawai' && $activeMethod == 'rekapnilaipegawai') ? 'active' : '' ?>">
                                    <a href="<?= base_url('pegawai/rekapnilaipegawai') ?>">
                                        <i class="mdi mdi-file-chart"></i>
                                        <span> Rekap KPI Individu </span>
                                    </a>
                                </li>
                                <li class="<?= ($activeController == 'pegawai' && in_array($activeMethod, ['monitoringindividu', 'caripenilaianbulanan'])) ? 'active' : '' ?>">
                                    <a href="<?= base_url('pegawai/monitoringindividu') ?>">
                                        <i class="mdi mdi-clipboard-pulse-outline"></i>
                                        <span> Monitoring Individu </span>
                                    </a>
                                </li>
                                <li class="<?= ($activeController == 'pegawai' && in_array($activeMethod, ['ppk_pegawai'])) ? 'active' : '' ?>">
                                    <a href="<?= base_url('pegawai/ppk_pegawai') ?>">
                                        <i class="mdi mdi-account-badge-alert mr-1"></i>
                                        <span> Program PPK </span>
                                    </a>
                                </li>
                            </ul>
                        </li>

                        <?php
                        $halamanPenilaiActive = ($activeController == 'pegawai' && in_array($activeMethod, [
                            'nilaipegawai',
                            'nilaipegawaidetail',
                            'nilaipegawaidetail2'
                        ]));
                        ?>
                        <li class="<?= $halamanPenilaiActive ? 'active' : '' ?>">
                            <a href="javascript: void(0);" class="special-header-link">
                                <span> Halaman Penilai </span>
                                <span class="menu-arrow"></span>
                            </a>
                            <ul class="nav-second-level <?= $halamanPenilaiActive ? 'in' : '' ?>" aria-expanded="<?= $halamanPenilaiActive ? 'true' : 'false' ?>">
                                <li class="<?= ($activeController == 'pegawai' && in_array($activeMethod, ['nilaipegawai', 'nilaipegawaidetail', 'nilaipegawaidetail2'])) ? 'active' : '' ?>">
                                    <a href="<?= base_url('pegawai/nilaipegawai') ?>">
                                        <i class="mdi mdi-account-edit"></i>
                                        <span> Nilai Pegawai </span>
                                    </a>
                                </li>
                            </ul>
                        </li>
                    </ul>
                </div> <!-- end sidebar-menu -->
            </div> <!-- end slimscroll-menu -->
        </div> <!-- end left-side-menu -->

        <!-- End Sidebar -->

        <div class="clearfix"></div>

    </div>
    <!-- Sidebar -left -->

    </div>
    <!-- Left Sidebar End -->
    <script>
        // Pastikan dropdown tetap terbuka ketika parent menu aktif
        document.addEventListener('DOMContentLoaded', function() {
            // Force buka semua dropdown yang memiliki item child aktif
            const activeParents = document.querySelectorAll('#sidebar-menu li.active > ul');
            activeParents.forEach(function(dropdown) {
                dropdown.classList.add('in');
                dropdown.style.display = 'block';
                dropdown.setAttribute('aria-expanded', 'true');
            });
        });
    </script>
</body>

</html>