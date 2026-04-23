<div class="content-page">
    <div class="content">
        <div class="container-fluid mt-4">

            <!-- Alert pesan sukses/gagal -->
            <?php if ($this->session->flashdata('success')): ?>
                <div class="alert alert-success" id="flashMessage">
                    <?= $this->session->flashdata('success'); ?>
                </div>
            <?php elseif ($this->session->flashdata('error')): ?>
                <div class="alert alert-danger" id="flashMessage">
                    <?= $this->session->flashdata('error'); ?>
                </div>
            <?php endif; ?>

            <div class="row">
                <div class="col-lg-8 offset-lg-2">
                    <div class="card shadow-lg rounded-lg">
                        <div class="card-body">
                            <h3 class="card-title text-center mb-4">Profil Data Diri</h3>
                            <div class="text-center mb-4">
                                <img src="<?= base_url('assets/images/users/avatar-1.png') ?>"
                                    class="rounded-circle avatar-xl" alt="user-image">
                            </div>
                            <table class="table table-borderless">
                                <tr>
                                    <th>NIK</th>
                                    <td><?= isset($pegawai['nik']) ? $pegawai['nik'] : '-' ?></td>
                                </tr>
                                <tr>
                                    <th>Nama</th>
                                    <td><?= isset($pegawai['nama']) ? $pegawai['nama'] : '-' ?></td>
                                </tr>
                                <tr>
                                    <th>Jabatan</th>
                                    <td><?= isset($pegawai['jabatan']) ? $pegawai['jabatan'] : '-' ?></td>
                                </tr>
                                <tr>
                                    <th>Kantor Unit</th>
                                    <td><?= isset($pegawai['unit_kerja']) ? $pegawai['unit_kerja'] : '-' ?> <?= isset($pegawai['unit_kantor']) ? $pegawai['unit_kantor'] : '-' ?></td>
                                </tr>
                            </table>
                            <hr>
                            <h5 class="mb-3">Perbarui Password</h5>
                            
                            <div class="alert alert-info">
                                <strong>Syarat Password Kuat:</strong><br>
                                - Minimal 8 karakter<br>
                                - Mengandung huruf besar (A-Z)<br>
                                - Mengandung huruf kecil (a-z)<br>
                                - Mengandung angka (0-9)<br>
                                - Mengandung karakter spesial (contoh: @, #, $, dll)
                            </div>

                            <form id="formPassword" method="post">
                                <div class="form-group">
                                    <label>Password Baru</label>
                                    <div class="input-group">
                                        <input type="password" name="password" id="password" class="form-control"
                                            required>
                                        <div class="input-group-append">
                                            <span class="input-group-text" onclick="togglePassword('password', this)" style="cursor: pointer;">
                                                <i class="fe-eye"></i>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label>Konfirmasi Password</label>
                                    <div class="input-group">
                                        <input type="password" name="konfirmasi_password" id="konfirmasi_password"
                                            class="form-control" required>
                                        <div class="input-group-append">
                                            <span class="input-group-text"
                                                onclick="togglePassword('konfirmasi_password', this)" style="cursor: pointer;">
                                                <i class="fe-eye"></i>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <button type="button" class="btn btn-success btn-block" onclick="konfirmasiUpdate()">
                                    Simpan Perubahan
                                </button>
                                <input type="hidden" name="update_password" value="1">
                            </form>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<?php if ($this->session->userdata('must_change_password')): ?>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        Swal.fire({
            icon: 'warning',
            title: 'Wajib Ubah Password!',
            text: 'Demi keamanan, silakan perbarui password Anda yang saat ini masih menggunakan NIK sebelum mengakses halaman lain.',
            confirmButtonText: 'Mengerti',
            allowOutsideClick: false,
            allowEscapeKey: false
        });
    });
</script>
<?php elseif ($this->session->flashdata('warning_password')): ?>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        Swal.fire({
            icon: 'warning',
            title: 'Akses Ditolak',
            text: '<?= $this->session->flashdata('warning_password'); ?>',
            confirmButtonText: 'OK'
        });
    });
</script>
<?php endif; ?>

<script>
    // 👁 Toggle lihat/sembunyi password
    function togglePassword(fieldId, el) {
        const input = document.getElementById(fieldId);
        const icon = el.querySelector("i");
        if (input.type === "password") {
            input.type = "text";
            icon.classList.remove("fe-eye");
            icon.classList.add("fe-eye-off");
        } else {
            input.type = "password";
            icon.classList.remove("fe-eye-off");
            icon.classList.add("fe-eye");
        }
    }

    // 🔐 Konfirmasi sebelum submit form
    function konfirmasiUpdate() {
        const password = document.getElementById('password').value;
        const konfirmasi = document.getElementById('konfirmasi_password').value;

        if (password.trim() === '') {
            Swal.fire({
                icon: 'warning',
                title: 'Peringatan',
                text: 'Password tidak boleh kosong!'
            });
            return;
        }
        
        // Validasi Strong Password (Client Side)
        const uppercase = /[A-Z]/.test(password);
        const lowercase = /[a-z]/.test(password);
        const number = /[0-9]/.test(password);
        const specialChars = /[^\w]/.test(password);

        if (password.length < 8 || !uppercase || !lowercase || !number || !specialChars) {
            Swal.fire({
                icon: 'error',
                title: 'Password Lemah',
                text: 'Password harus minimal 8 karakter, mengandung huruf besar, huruf kecil, angka, dan karakter spesial!'
            });
            return;
        }

        if (password !== konfirmasi) {
            Swal.fire({
                icon: 'error',
                title: 'Gagal',
                text: 'Password dan Konfirmasi Password tidak cocok!'
            });
            return;
        }

        Swal.fire({
            title: 'Yakin perbarui password?',
            text: "Pastikan kamu mengingat password baru ini!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, perbarui!'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('formPassword').submit();
            }
        });
    }

    // ⏳ Auto hide alert setelah 2 detik
    setTimeout(() => {
        const alertDiv = document.getElementById('flashMessage');
        if (alertDiv) {
            alertDiv.style.transition = "opacity 0.5s ease";
            alertDiv.style.opacity = "0";
            setTimeout(() => alertDiv.remove(), 500);
        }
    }, 2000);
</script>