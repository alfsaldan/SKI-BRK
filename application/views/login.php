<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <title>Login - Sistem SKI BRKS</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta content="Login Sistem SKI BRKS" name="description" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <link rel="shortcut icon" href="<?= base_url('assets/images/LogoKapalBRK.png'); ?>">
    <link href="<?= base_url('assets/css/bootstrap.min.css'); ?>" rel="stylesheet" />
    <link href="<?= base_url('assets/css/icons.min.css'); ?>" rel="stylesheet" />
    <link href="<?= base_url('assets/css/app.min.css'); ?>" rel="stylesheet" />
    <link href="<?= base_url('assets/css/css2.css'); ?>" rel="stylesheet" />
    <style>
        /* 🌿 Background */
        body {
            background: radial-gradient(circle at top right, #17562aff 30%, #a8270bff 100%);
            min-height: 100vh;
            font-family: 'Poppins', sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            position: relative;
            margin: 0;
            padding: 20px;
        }

        /* ✨ Floating Particles */
        .circle {
            position: absolute;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.2);
            animation: float 8s ease-in-out infinite;
            z-index: 0;
        }

        .circle:nth-child(1) {
            width: 120px;
            height: 120px;
            top: 10%;
            left: 15%;
            animation-delay: 0s;
        }

        .circle:nth-child(2) {
            width: 80px;
            height: 80px;
            bottom: 15%;
            right: 20%;
            animation-delay: 2s;
        }

        .circle:nth-child(3) {
            width: 100px;
            height: 100px;
            bottom: 25%;
            left: 10%;
            animation-delay: 4s;
        }

        @keyframes float {

            0%,
            100% {
                transform: translateY(0px) rotate(0deg);
            }

            50% {
                transform: translateY(-30px) rotate(45deg);
            }
        }

        /* 🧊 Card Style (Glassmorphism) */
        .card {
            background: rgba(255, 255, 255, 0.2);
            border-radius: 20px;
            backdrop-filter: blur(15px);
            border: 1px solid rgba(255, 255, 255, 0.3);
            padding: 40px 30px;
            width: 100%;
            max-width: 420px;
            text-align: center;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
            animation: fadeInUp 1s ease forwards;
            position: relative;
            z-index: 1;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* ✍️ Form Input */
        .form-control {
            border-radius: 12px;
            border: 1px solid rgba(255, 255, 255, 0.5);
            background-color: rgba(255, 255, 255, 0.25);
            color: #fff;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            box-shadow: 0 0 12px rgba(233, 196, 106, 0.7);
            border-color: #E9C46A;
        }

        .form-control::placeholder {
            color: rgba(255, 255, 255, 0.85);
        }

        label {
            color: #fff;
            font-weight: 500;
            text-align: left;
            width: 100%;
        }

        /* 🌟 Button */
        .btn-success {
            background: linear-gradient(45deg, #1d6b35ff, #c1330cff);
            border: none;
            border-radius: 12px;
            font-weight: 600;
            box-shadow: 0 4px 15px rgba(233, 196, 106, 0.4);
            transition: all 0.3s ease-in-out;
        }

        .btn-success:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(233, 196, 106, 0.7);
        }

        /* 👁️ Toggle Password */
        .toggle-password {
            color: #fff;
            opacity: 0.8;
            transition: 0.3s;
        }

        .toggle-password:hover {
            opacity: 1;
        }

        /* ✨ Title */
        h4 {
            color: #fff;
            font-weight: 700;
            letter-spacing: 1px;
        }

        p.text-muted {
            color: rgba(255, 255, 255, 0.9) !important;
        }

        /* 🌈 Logo */
        .logo img {
            filter: drop-shadow(0 0 10px rgba(255, 255, 255, 0.8));
            transition: transform 0.4s ease;
        }

        .logo img:hover {
            transform: scale(1.08);
        }

        /* 🔔 Alert Fade */
        #nikError {
            animation: fadeOut 0.5s ease-in-out 3s forwards;
        }

        @keyframes fadeOut {
            to {
                opacity: 0;
                transform: scale(0.9);
            }
        }

        /* 📱 Responsif */
        @media (max-width: 480px) {
            body {
                padding: 10px;
                align-items: flex-start;
            }

            .card {
                margin-top: 40px;
                padding: 25px 20px;
            }

            .logo img {
                height: 55px;
            }

            h4 {
                font-size: 18px;
            }

            .btn-success {
                font-size: 14px;
                padding: 10px;
            }

            label {
                font-size: 14px;
            }

            .form-control {
                font-size: 14px;
                padding: 10px;
            }
        }
    </style>
</head>

<body>
    <!-- ✨ Floating Particles -->
    <div class="circle"></div>
    <div class="circle"></div>
    <div class="circle"></div>

    <div class="card">
        <div class="text-center mb-4 logo">
            <a href="<?= base_url(); ?>">
                <img src="<?= base_url('assets/images/Logo_BRK_Syariah.png'); ?>" alt="Logo" height="70">
            </a>
            <h4 class="mt-3">Login Sistem SKI Online-BRKS</h4>
        </div>

        <?php if ($this->session->flashdata('error')): ?>
            <div class="alert alert-danger text-center" id="nikError">
                <?= $this->session->flashdata('error'); ?>
            </div>
        <?php endif; ?>

        <form id="loginForm" action="<?= site_url('auth/login'); ?>" method="post">
            <div class="form-group mb-3 text-left">
                <label for="nik"><b>NIP</b></label>
                <input type="text" class="form-control" id="nik" name="nik" required maxlength="6"
                    placeholder="Masukkan NIP Pegawai" value="<?= isset($nik) ? $nik : ''; ?>"
                    oninput="this.value = this.value.replace(/[^0-9]/g, '');">
            </div>

            <div class="form-group mb-4 position-relative">
                <label for="password">Password</label>
                <input class="form-control" type="password" id="password" name="password" required
                    placeholder="Masukkan Password">
                <span toggle="#password" class="mdi mdi-eye-outline field-icon toggle-password"
                    style="color: rgba(9, 121, 11, 0.9); position:absolute; top:40px; right:15px; cursor:pointer;"></span>
            </div>

            <button class="btn btn-success btn-lg w-100" type="submit">
                <i class="mdi mdi-login"></i> Login
            </button>
        </form>

        <p class="text-center text-muted mt-4 mb-0">SKI Online - BRKS</p>
    </div>

    <script src="<?= base_url('assets/js/vendor.min.js'); ?>"></script>
    <script src="<?= base_url('assets/js/app.min.js'); ?>"></script>

    <script>
        // 👁️ Toggle Password Visibility
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