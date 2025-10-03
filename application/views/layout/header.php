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

        /* Logo */
        .logo-box {
            background-color: transparent;
            text-align: center;
        }

        .logo-box img {
            max-height: 32px;
        }

        /* Sidebar putih minimalis */
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

        /* Tombol hamburger mobile */
        .button-menu-mobile {
            background-color: transparent;
            /* biar tidak mengganggu tampilan navbar */
            border: none;
            padding: 6px 10px;
        }

        /* Warna ikon hamburger selalu terlihat */
        .button-menu-mobile i.fe-menu {
            color: #333333;
            /* warna gelap agar terlihat di navbar putih */
            font-size: 18px;
        }

        /* Hover efek (opsional) */
        .button-menu-mobile:hover i.fe-menu {
            color: #277caeff;
            /* hijau saat hover */
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
                        <a href="<?= base_url('administrator/datadiri'); ?>" class="dropdown-item notify-item">
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
                <a href="<?= base_url('Administrator'); ?>" class="logo text-center">
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
                            <!-- MENU ADMIN -->
                            <li class="menu-title">Halaman Utama</li>

                            <li class="<?= ($activeController == 'administrator' && $activeMethod == '') ? 'active' : '' ?>">
                                <a href="<?= base_url('administrator') ?>">
                                    <i class="fe-airplay"></i>
                                    <span> Dashboard </span>
                                </a>
                            </li>

                            <li class="menu-title mt-2">Fitur Utama</li>

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

                            <li class="<?= ($activeController == 'administrator_renstra' && in_array($activeMethod, ['kpi_indikatorkinerja'])) ? 'active' : '' ?>">
                                <a href="<?= base_url('administrator/kpi_indikatorkinerja') ?>">
                                    <i class="mdi mdi-key-variant"></i>
                                    <span> Kelola KPI  </span>
                                </a>
                            </li>

                            <li class="<?= ($activeController == 'administrator' && in_array($activeMethod, ['penilaiankinerja', 'caripenilaian'])) ? 'active' : '' ?>">
                                <a href="<?= base_url('administrator/penilaiankinerja') ?>">
                                    <i class="mdi mdi-account-edit"></i>
                                    <span> Penilaian Kinerja </span>
                                </a>
                            </li>

                            <li class="menu-title mt-2">Lainnya</li>

                            <li class="<?= ($activeController == 'administrator' && in_array($activeMethod, ['datapegawai', 'caridatapegawai'])) ? 'active' : '' ?>">
                                <a href="<?= base_url('administrator/datapegawai') ?>">
                                    <i class="mdi mdi-account-card-details"></i>
                                    <span> Cek Kinerja Pegawai </span>
                                </a>
                            </li>

                            <li class="<?= ($activeController == 'administrator' && $activeMethod == 'kelolatingkatanjabatan') ? 'active' : '' ?>">
                                <a href="<?= base_url('administrator/kelolatingkatanjabatan') ?>">
                                    <i class="mdi mdi-briefcase"></i>
                                    <span>Kelola Jabatan </span>
                                </a>
                            </li>
                        <?php endif; ?>

                        <!-- MENU PEGAWAI (muncul untuk semua role) -->
                        <li class="menu-title">Halaman Pegawai</li>
                        <li class="<?= ($activeController == 'pegawai' && $activeMethod == '') ? 'active' : '' ?>">
                            <a href="<?= base_url('pegawai') ?>">
                                <i class="mdi mdi-account"></i>
                                <span> Kinerja Individu </span>
                            </a>
                        </li>

                        <li class="menu-title mt-2">Halaman Penilai</li>
                        <li class="<?= ($activeController == 'pegawai' && in_array($activeMethod, ['nilaipegawai', 'nilaipegawaidetail'])) ? 'active' : '' ?>">
                            <a href="<?= base_url('pegawai/nilaipegawai') ?>">
                                <i class="mdi mdi-account-edit"></i>
                                <span> Nilai Pegawai </span>
                            </a>
                        </li>

                    </ul>
                </div>
            </div>
        </div>

        <!-- End Sidebar -->

        <div class="clearfix"></div>

    </div>
    <!-- Sidebar -left -->

    </div>
    <!-- Left Sidebar End -->