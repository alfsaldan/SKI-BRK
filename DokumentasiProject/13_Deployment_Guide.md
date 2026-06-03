# 13 — Deployment & Setup Guide

## Local Development Setup

### Prerequisites
- **Laragon** (atau XAMPP/WAMP) dengan:
  - PHP ≥ 7.4
  - MySQL/MariaDB 5.7+
  - Apache 2.x dengan mod_rewrite
- **Composer** (untuk PHP dependencies)
- **Git** (opsional)

### Step-by-Step Setup

```
1. Clone/copy project ke web root:
   D:\Laragon\laragon\www\SKI-BRK\

2. Install PHP dependencies:
   cd D:\Laragon\laragon\www\SKI-BRK
   composer install

3. Buat database:
   CREATE DATABASE `ski-brk` CHARACTER SET utf8 COLLATE utf8_general_ci;

4. Import SQL (jika ada dump):
   mysql -u root ski-brk < database_dump.sql

5. Jalankan SQL migrasi (jika tabel PPK belum ada):
   mysql -u root ski-brk < sql/create_syarat_ppk.sql
   mysql -u root ski-brk < sql/create_ppk_responses.sql
   mysql -u root ski-brk < sql/alter_pegawai_add_ppk_eligible.sql

6. Konfigurasi database:
   Edit: application/config/database.php
   - hostname: localhost
   - username: root (atau user custom)
   - password: (sesuaikan)
   - database: ski-brk

7. Konfigurasi base URL:
   Edit: application/config/config.php
   $config['base_url'] = 'http://localhost/SKI-BRK/';

8. Pastikan mod_rewrite aktif (Laragon default aktif)

9. Buat superadmin pertama:
   Akses: http://localhost/SKI-BRK/auth/create_superadmin
   (Hanya untuk setup awal, HAPUS method setelah selesai!)

10. Login:
    URL: http://localhost/SKI-BRK/
    NIK: (sesuai superadmin yang dibuat)
    Password: (sesuai)
```

## Production Deployment

### Server Requirements
- VPS/Shared Hosting dengan:
  - PHP 7.4+ (recommended 8.0+)
  - MySQL 5.7+ / MariaDB 10.x
  - Apache 2.x + mod_rewrite
  - SSL Certificate (HTTPS)

### Deployment Checklist

```
1. Upload files ke server (exclude: .git, node_modules, src/)

2. Buat database production + user khusus:
   CREATE USER 'ski_user'@'localhost' IDENTIFIED BY 'StrongP@ss!';
   GRANT SELECT,INSERT,UPDATE,DELETE ON `ski-brk`.* TO 'ski_user'@'localhost';

3. Update konfigurasi:
   - database.php: username, password, hostname
   - config.php: base_url, csrf_protection=TRUE
   - index.php: ENVIRONMENT='production'

4. Set file permissions:
   - application/ → 755
   - uploads/ → 755
   - application/logs/ → 755
   - system/ → 755

5. Hapus endpoint sensitif:
   - Remove create_superadmin() dari Auth.php

6. Pastikan .htaccess aktif

7. Test semua fitur utama
```

## Troubleshooting

| Problem | Cause | Solution |
|---------|-------|----------|
| Halaman blank / 500 Error | PHP error, mod_rewrite off | Cek `application/logs/`, enable mod_rewrite |
| "Unable to load database" | Config DB salah | Cek `database.php` credentials |
| URL routing error | .htaccess tidak aktif | Enable mod_rewrite, cek AllowOverride |
| Excel import gagal | PhpSpreadsheet belum install | Jalankan `composer install` |
| Session expired cepat | `sess_expiration` terlalu kecil | Naikkan nilai di config.php |
| CSRF token mismatch | CSRF enabled tapi form belum ada token | Tambahkan `<?= form_open() ?>` |
| Data pegawai duplikat | Import Excel tanpa cek NIK | Cek logika deduplikasi di controller |
