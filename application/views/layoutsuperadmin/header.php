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
        /* === Sidebar Styling === */
        #sidebar-menu {
            background: #ffffff;
            color: #64748b;
        }

        #sidebar-menu ul li {
            list-style: none;
        }

        #sidebar-menu ul li a {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 8px 14px;
            margin: 4px 10px;
            border-radius: 10px;
            color: #475569 !important;
            font-weight: 500;
            transition: all 0.3s ease;
            background: transparent !important;
        }

        #sidebar-menu ul li a i {
            font-size: 18px;
            transition: transform 0.3s ease, color 0.3s ease;
            color: #475569 !important;
        }

        /* === Hover efek — gradasi hijau lembut === */
        #sidebar-menu ul li a:hover {
            background: linear-gradient(90deg, #16a34a, #22c55e) !important;
            color: #ffffff !important;
            box-shadow: 0 0 6px rgba(34, 197, 94, 0.4);
            transform: translateX(3px);
        }

        #sidebar-menu ul li a:hover i {
            color: #ffffff !important;
            transform: scale(1.05);
        }

        /* === Aktif — tetap nyala hijau di halaman aktif === */
        #sidebar-menu ul li.active>a,
        #sidebar-menu ul li.mm-active>a,
        #sidebar-menu ul li.active>a:focus,
        #sidebar-menu ul li.active>a:active {
            background: linear-gradient(90deg, #15803d, #22c55e) !important;
            color: #ffffff !important;
            font-weight: 600;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(34, 197, 94, 0.5);
        }

        #sidebar-menu ul li.active>a i,
        #sidebar-menu ul li.mm-active>a i {
            color: #ffffff !important;
            transform: scale(1.05);
        }

        /* === Hilangkan gaya bawaan metismenu / app.min.css yang menimpa === */
        .metismenu a:hover,
        .metismenu li.active>a,
        .metismenu li.mm-active>a {
            background: unset !important;
            color: unset !important;
        }

        /* === Section title === */
        .menu-title {
            padding: 10px 16px 4px;
            text-transform: uppercase;
            font-size: 12px;
            letter-spacing: 1px;
            color: #9ca3af;
            margin-top: 10px;
        }

        /* === Sidebar container === */
        .left-side-menu {
            background-color: #ffffff;
            border-right: 1px solid #e5e5e5;
        }

        /* === Navbar putih bersih === */
        .navbar-custom {
            background-color: #ffffff !important;
            border-bottom: 1px solid #e5e5e5;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
        }

        .topnav-menu>li>a {
            color: #334155;
            font-weight: 500;
        }

        .topnav-menu>li>a:hover {
            color: #22c55e;
        }

        .button-menu-mobile {
            background: transparent;
            border: none;
            padding: 6px 10px;
        }

        .button-menu-mobile i.fe-menu {
            color: #334155;
            font-size: 18px;
        }

        .button-menu-mobile:hover i.fe-menu {
            color: #22c55e;
        }

        .logo-box {
            background-color: transparent;
            text-align: center;
        }

        .logo-box img {
            max-height: 32px;
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
                        <!-- <a href="<?= base_url('pegawai/datadiriPegawai'); ?>" class="dropdown-item notify-item">
                            <i class="fe-user"></i>
                            <span>Data Diri</span>
                        </a> -->


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
                <a href="<?= base_url('superadmin'); ?>" class="logo text-center">
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
        <div class="left-side-menu">

            <div class="slimscroll-menu">

                <!--- Sidemenu -->
                <div id="sidebar-menu">

                    <ul class="metismenu" id="side-menu">

                        <li class="menu-title">Halaman Utama</li>

                        <li class="<?= $this->uri->segment(2) == '' ? 'active' : '' ?>">
                            <a href="<?= base_url('superadmin/index') ?>">
                                <i class="fe-airplay"></i>
                                <span> Dashboard </span>
                            </a>
                        </li>

                        <li class="menu-title mt-2">Fitur Utama</li>

                        <?php
                        $activeMenu = strtolower($this->uri->segment(2, ''));
                        ?>

                        <li
                            class="<?= in_array($activeMenu, ['tambahroleuser', 'editroleuser']) ? 'active' : '' ?>">
                            <a href="<?= base_url('superadmin/kelolaroleuser') ?>">
                                <i class="mdi mdi-account-edit"></i>
                                <span> Kelola Role User </span>
                            </a>
                        </li>


                        <?php
                        $activeMenu = strtolower($this->uri->segment(2, ''));
                        ?>

                        <li
                            class="<?= in_array($activeMenu, ['kelolatingkatanjabatan', 'kelolatingkatanjabatan_kpi']) ? 'active' : '' ?>">
                            <a href="<?= base_url('superadmin/kelolatingkatanjabatan_kpi') ?>">
                                <i class="mdi mdi-briefcase"></i>
                                <span> Kelola Jabatan </span>
                            </a>
                        </li>

                        <?php
                        $activeMenu = strtolower($this->uri->segment(2, ''));
                        ?>

                        <li
                            class="<?= in_array($activeMenu, ['tambahroleuser', 'editroleuser']) ? 'active' : '' ?>">
                            <a href="<?= base_url('superadmin/kelolarumus') ?>">
                                <i class="mdi mdi-square-root"></i>
                                <span> Kelola Rumus </span>
                            </a>
                        </li>

                    </ul>

                </div>
                <!-- End Sidebar -->

                <div class="clearfix"></div>

            </div>
            <!-- Sidebar -left -->

        </div>
        <!-- Left Sidebar End -->