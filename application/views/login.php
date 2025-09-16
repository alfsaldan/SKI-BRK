<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <title>Login - Sistem SKI BRKS</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="Login Sistem SKI BRKS" name="description" />
    <meta content="Coderthemes" name="author" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />

    <link rel="shortcut icon" href="<?= base_url('assets/images/LogoKapalBRK.png'); ?>">
    <link href="<?= base_url('assets/css/bootstrap.min.css'); ?>" rel="stylesheet" />
    <link href="<?= base_url('assets/css/icons.min.css'); ?>" rel="stylesheet" />
    <link href="<?= base_url('assets/css/app.min.css'); ?>" rel="stylesheet" />
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
                                    <img src="<?= base_url('assets/images/Logo_BRK_Syariah.png'); ?>" alt=""
                                        height="40">
                                </a>
                                <h4 class="mt-3">Login Sistem SKI-BRKS</h4>
                            </div>

                            <?php if ($this->session->flashdata('error')): ?>
                                <div class="alert alert-danger" id="nikError">
                                    <?= $this->session->flashdata('error'); ?>
                                </div>
                                <script>
                                    setTimeout(() => {
                                        const alertDiv = document.getElementById('nikError');
                                        if (alertDiv) alertDiv.style.display = 'none';
                                    }, 2000); // hilang setelah 2 detik
                                </script>
                            <?php endif; ?>


                            <form id="loginForm" action="<?= site_url('auth/login'); ?>" method="post">
                                <!-- Input NIK -->
                                <div class="form-group mb-3" id="nikDiv">
                                    <label for="nik"><b>Masukkan NIK</b></label>
                                    <div class="input-group">
                                        <input type="text" class="form-control" id="nik" name="nik" required
                                            placeholder="Masukkan NIK" value="<?= isset($nik) ? $nik : ''; ?>">
                                        <div class="input-group-append">
                                            <button class="btn btn-success" type="button" id="checkNikBtn">Next</button>
                                        </div>
                                    </div>
                                </div>

                                <!-- Role (hanya muncul jika superadmin) -->
                                <div class="form-group mb-3" id="roleDiv" style="display:none;">
                                    <label><b>Login Sebagai:</b></label><br>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="role" id="role_superadmin"
                                            value="superadmin" required checked>
                                        <label class="form-check-label" for="role_superadmin">Superadmin</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="role" id="role_pegawai"
                                            value="pegawai" required>
                                        <label class="form-check-label" for="role_pegawai">Pegawai</label>
                                    </div>
                                </div>

                                <!-- Password -->
                                <div class="form-group mb-3 position-relative" id="passwordDiv" style="display:none;">
                                    <label for="password">Password</label>
                                    <input class="form-control" type="password" id="password" name="password" required
                                        placeholder="Masukkan Password">
                                    <span toggle="#password" class="mdi mdi-eye-outline field-icon toggle-password"
                                        style="position:absolute; top:38px; right:15px; cursor:pointer;"></span>
                                </div>

                                <div class="form-group text-center mb-3" id="loginBtnDiv" style="display:none;">
                                    <button class="btn btn-success btn-lg width-lg btn-rounded"
                                        type="submit">Login</button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-sm-12 text-center">
                            <p class="text-muted mb-0">Sistem Kinerja Insani - BRKS</p>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <script src="<?= base_url('assets/js/vendor.min.js'); ?>"></script>
    <script src="<?= base_url('assets/js/app.min.js'); ?>"></script>

    <script>
        const checkBtn = document.getElementById('checkNikBtn');
        const nikInput = document.getElementById('nik');
        const roleDiv = document.getElementById('roleDiv');
        const passwordDiv = document.getElementById('passwordDiv');
        const loginBtnDiv = document.getElementById('loginBtnDiv');

        checkBtn.addEventListener('click', function () {
            const nik = nikInput.value.trim();
            if (!nik) return alert("Masukkan NIK");

            // AJAX cek apakah NIK superadmin
            fetch("<?= site_url('auth/check_role'); ?>", {
                method: "POST",
                headers: {
                    "Content-Type": "application/x-www-form-urlencoded"
                },
                body: "nik=" + nik
            })
                .then(res => res.json())
                .then(data => {
                    nikInput.readOnly = true;
                    checkBtn.style.display = 'none';
                    passwordDiv.style.display = 'block';
                    loginBtnDiv.style.display = 'block';

                    if (data.is_superadmin) {
                        roleDiv.style.display = 'block';
                    } else {
                        // langsung set hidden role=pegawai
                        const hiddenRole = document.createElement('input');
                        hiddenRole.type = 'hidden';
                        hiddenRole.name = 'role';
                        hiddenRole.value = 'pegawai';
                        document.getElementById('loginForm').appendChild(hiddenRole);
                    }
                });
        });

        // Toggle password
        const toggle = document.querySelector('.toggle-password');
        if (toggle) {
            toggle.addEventListener('click', function () {
                const passwordInput = document.querySelector('#password');
                const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                passwordInput.setAttribute('type', type);
                this.classList.toggle('mdi-eye-outline');
                this.classList.toggle('mdi-eye-off-outline');
            });
        }
    </script>

</body>

</html>