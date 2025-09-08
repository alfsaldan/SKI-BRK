<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <title>Login - Sistem SKI BRKS</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="Login Sistem SKI BRKS" name="description" />
    <meta content="Coderthemes" name="author" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />

    <!-- App favicon -->
    <link rel="shortcut icon" href="<?= base_url('assets/images/LogoKapalBRK.png'); ?>">

    <!-- Bootstrap select plugins -->
    <link href="<?= base_url('assets/libs/bootstrap-select/bootstrap-select.min.css'); ?>" rel="stylesheet"
        type="text/css" />

    <!-- App css -->
    <link href="<?= base_url('assets/css/bootstrap.min.css'); ?>" rel="stylesheet" type="text/css" />
    <link href="<?= base_url('assets/css/icons.min.css'); ?>" rel="stylesheet" type="text/css" />
    <link href="<?= base_url('assets/css/app.min.css'); ?>" rel="stylesheet" type="text/css" />

</head>

<body class="bg-white">
    <div class="account-pages my-5 pt-5">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-8 col-lg-6 col-xl-5">
                    <div class="card shadow">

                        <div class="card-body p-4">

                            <div class="text-center mb-4">
                                <a href="<?= base_url(); ?>">
                                    <span><img src="<?= base_url('assets/images/Logo_BRK_Syariah.png'); ?>" alt=""
                                            height="40"></span>
                                </a>
                                <h4 class="mt-3">Login Sistem</h4>
                            </div>

                            <!-- Pesan error -->
                            <?php if ($this->session->flashdata('error')): ?>
                                <div class="alert alert-danger">
                                    <?= $this->session->flashdata('error'); ?>
                                </div>
                            <?php endif; ?>

                            <form action="<?= site_url('auth/login'); ?>" method="post">

                                <div class="form-group mb-3">
                                    <label for="nik">NIK</label>
                                    <input class="form-control" type="text" id="nik" name="nik" required
                                        placeholder="Masukkan NIK">
                                </div>

                                <div class="form-group mb-3 position-relative">
                                    <label for="password">Password</label>
                                    <input class="form-control" type="password" id="password" name="password" required
                                        placeholder="Masukkan Password">
                                    <!-- Tombol eye -->
                                    <span toggle="#password" class="mdi mdi-eye-outline field-icon toggle-password"
                                        style="position:absolute; top:38px; right:15px; cursor:pointer;"></span>
                                </div>

                                <div class="form-group text-center mb-3">
                                    <button class="btn btn-success btn-lg width-lg btn-rounded"
                                        type="submit">Login</button>
                                </div>

                            </form>

                        </div> <!-- end card-body -->
                    </div>
                    <!-- end card -->

                    <div class="row mt-3">
                        <div class="col-sm-12 text-center">
                            <p class="text-muted mb-0">Sistem Kinerja Insani - BRKS</p>
                        </div>
                    </div>

                </div> <!-- end col -->
            </div>
        </div>
    </div>


    <!-- Vendor js -->
    <script src="<?= base_url('assets/js/vendor.min.js'); ?>"></script>

    <!-- Bootstrap select plugin -->
    <script src="<?= base_url('assets/libs/bootstrap-select/bootstrap-select.min.js'); ?>"></script>

    <!-- App js -->
    <script src="<?= base_url('assets/js/app.min.js'); ?>"></script>

    <script>
        document.querySelector('.toggle-password').addEventListener('click', function () {
            const passwordInput = document.querySelector('#password');
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);
            // Toggle icon antara eye dan eye-off
            this.classList.toggle('mdi-eye-outline');
            this.classList.toggle('mdi-eye-off-outline');
        });
    </script>
    

</body>

</html>