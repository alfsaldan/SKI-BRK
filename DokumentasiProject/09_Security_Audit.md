# 09 — Security Audit

## Executive Summary

> [!CAUTION]
> Terdapat **beberapa kerentanan keamanan kritis** yang HARUS diperbaiki sebelum deployment ke production.

## 1. Authentication & Password Security

### ✅ Password Hashing
- Menggunakan `password_hash()` dengan `PASSWORD_DEFAULT` (bcrypt) — **AMAN**
- Verifikasi dengan `password_verify()` — **AMAN**

### ⚠️ Default Password = NIK
- Jika password kosong saat import Excel, default = NIK pegawai
- Terdapat mekanisme `must_change_password` yang memaksa perubahan
- **Risiko**: Jika mekanisme bypass, akun bisa diakses siapapun yang tahu NIK

### 🔴 create_superadmin() Endpoint Terbuka
```php
// Auth.php line 94-119
public function create_superadmin() {
    // Membuat superadmin dengan NIK/password hardcoded
    // TIDAK ADA AUTH CHECK!
}
```
**Risiko**: CRITICAL — Siapapun bisa membuat superadmin via `/auth/create_superadmin`
**Rekomendasi**: Hapus method ini atau tambahkan guard

## 2. Authorization

### ✅ Role-based Access Control
Setiap controller memeriksa role di constructor:
```php
// Administrator.php
if ($this->session->userdata('role') !== 'administrator') {
    redirect('auth');
}
```

### ⚠️ No Middleware Pattern
- Auth check dilakukan per-controller, bukan via middleware/hook
- Risiko lupa menambahkan check pada method baru

### 🔴 ExcelImport Controller — No Auth Guard
```php
// ExcelImport.php - TIDAK ADA auth check di constructor
class ExcelImport extends CI_Controller {
    // Semua method bisa diakses tanpa login!
}
```
**Risiko**: HIGH — Upload dan parsing file tanpa autentikasi
**Rekomendasi**: Tambahkan session check di constructor

## 3. CSRF Protection

### 🔴 CSRF Disabled
```php
// config.php
$config['csrf_protection'] = FALSE;
```
**Risiko**: HIGH — Semua form POST rentan CSRF attack
**Rekomendasi**: Enable CSRF dan tambahkan token di semua form

## 4. SQL Injection

### ✅ Query Builder (Mayoritas Aman)
Sebagian besar query menggunakan CI Query Builder yang otomatis escape:
```php
$this->db->get_where('users', ['nik' => $nik]); // AMAN
```

### ⚠️ Raw Query dengan Parameter Binding
```php
// PenilaiMapping_model.php - Menggunakan parameterized query
$this->db->query($sql, [$kode_cabang, $mapping->key, $target->unit_kerja]);
```
**Status**: AMAN — menggunakan parameter binding

### ⚠️ String Interpolation dalam JOIN
```php
// Pegawai.php line 539-562
$in_list = "'" . implode("','", $special_keys) . "'";
$sql = "... WHERE pm.`key` IN ($in_list) ...";
```
**Risiko**: LOW — `$special_keys` adalah hardcoded array, bukan user input

## 5. XSS (Cross-Site Scripting)

### ⚠️ Mixed XSS Protection
- Beberapa output menggunakan `htmlspecialchars()`:
  ```php
  htmlspecialchars($nik) // Di error messages
  ```
- Namun kebanyakan output view TIDAK di-escape:
  ```php
  <?= $pegawai->nama ?> // TIDAK di-escape
  ```
**Risiko**: MEDIUM — Jika data mengandung HTML/JS, bisa dieksekusi
**Rekomendasi**: Gunakan `htmlspecialchars()` atau `html_escape()` di semua output view

### ⚠️ Global XSS Filtering Disabled
```php
$config['global_xss_filtering'] = FALSE; // config.php
```

## 6. File Upload Security

### ✅ Extension Validation
```php
if (!in_array($ext, ['xls', 'xlsx'])) { // Validasi ekstensi
```

### ⚠️ No MIME Type Validation
- Hanya cek ekstensi file, bukan MIME type
- **Risiko**: File berbahaya bisa di-rename ke .xlsx

### ⚠️ Direct File Access
- File upload disimpan di `uploads/` yang bisa diakses langsung via URL
- **Rekomendasi**: Pindahkan ke luar web root atau tambahkan `.htaccess` deny

## 7. Session Security

### ⚠️ Session Configuration
```php
$config['sess_driver'] = 'files';
$config['sess_cookie_name'] = 'ci_session';
$config['sess_expiration'] = 7200; // 2 jam
$config['sess_match_ip'] = FALSE; // Tidak match IP
$config['sess_time_to_update'] = 300;
```
**Risiko**: Session hijacking jika cookie dicuri (tidak ada IP matching)
**Rekomendasi**: 
- Enable `sess_match_ip` di production
- Set `cookie_secure = TRUE` (HTTPS)
- Set `cookie_httponly = TRUE` (sudah TRUE)

## 8. Database Security

### 🔴 Root Password Kosong
```php
$db['default']['username'] = 'root';
$db['default']['password'] = '';
```
**Risiko**: CRITICAL — DB root tanpa password
**Rekomendasi**: Buat user DB terpisah dengan privilege minimum

## 9. Error Handling

### ⚠️ Development Mode Active
```php
// index.php
define('ENVIRONMENT', isset($_SERVER['CI_ENV']) ? ... : 'development');
```
- Error messages ditampilkan ke user di mode development
- **Rekomendasi**: Set ke `production` di server live

## Security Score Summary

| Area | Score | Status |
|------|-------|--------|
| Password Hashing | 9/10 | ✅ Baik |
| Role Authorization | 7/10 | ⚠️ Perlu middleware |
| CSRF Protection | 1/10 | 🔴 Disabled |
| SQL Injection | 8/10 | ✅ Mostly safe |
| XSS Protection | 4/10 | ⚠️ Inconsistent |
| File Upload | 5/10 | ⚠️ Partial |
| Session Security | 6/10 | ⚠️ Needs hardening |
| Database Config | 2/10 | 🔴 Root no password |
| Error Handling | 3/10 | ⚠️ Dev mode |

## Priority Fixes

1. **CRITICAL**: Enable CSRF protection
2. **CRITICAL**: Remove/protect `create_superadmin()` endpoint
3. **CRITICAL**: Add auth guard to ExcelImport controller
4. **CRITICAL**: Change DB credentials for production
5. **HIGH**: Set environment to `production` on live server
6. **HIGH**: Add XSS escaping to all view outputs
7. **MEDIUM**: Add MIME type validation for file uploads
8. **MEDIUM**: Enable session IP matching
