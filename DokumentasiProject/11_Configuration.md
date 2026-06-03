# 11 — Configuration Documentation

## Config Files Overview

| File | Fungsi | Critical |
|------|--------|----------|
| [config.php](file:///d:/Laragon/laragon/www/SKI-BRK/application/config/config.php) | Konfigurasi utama CI | ✅ |
| [database.php](file:///d:/Laragon/laragon/www/SKI-BRK/application/config/database.php) | Koneksi database | ✅ |
| [routes.php](file:///d:/Laragon/laragon/www/SKI-BRK/application/config/routes.php) | URL routing | ✅ |
| [autoload.php](file:///d:/Laragon/laragon/www/SKI-BRK/application/config/autoload.php) | Auto-loaded components | ✅ |
| [constants.php](file:///d:/Laragon/laragon/www/SKI-BRK/application/config/constants.php) | Konstanta global | - |

## Key Configuration Settings

### config.php
```
base_url         = 'http://localhost/SKI-BRK/'
index_page       = ''                    (clean URL)
uri_protocol     = 'REQUEST_URI'
url_suffix       = ''
charset          = 'UTF-8'
csrf_protection  = FALSE                 ⚠️ HARUS TRUE di production
log_threshold    = 1                     (error logging only)
```

### Session
```
sess_driver          = 'files'
sess_cookie_name     = 'ci_session'
sess_expiration      = 7200              (2 jam)
sess_save_path       = NULL              (PHP default)
sess_match_ip        = FALSE             ⚠️ Enable di production
sess_time_to_update  = 300               (regenerate setiap 5 menit)
cookie_prefix        = ''
cookie_domain        = ''
cookie_path          = '/'
cookie_secure        = FALSE             ⚠️ TRUE jika HTTPS
cookie_httponly       = FALSE            ⚠️ Sebaiknya TRUE
```

### database.php
```
hostname = 'localhost'
username = 'root'                        ⚠️ Ubah di production
password = ''                            ⚠️ Ubah di production
database = 'ski-brk'
dbdriver = 'mysqli'
char_set = 'utf8'
dbcollat = 'utf8_general_ci'
```

### autoload.php
```php
$autoload['libraries'] = array('database', 'session', 'email');
$autoload['helper']    = array('url');
```

## .htaccess Configuration
```apache
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteRule ^(.*)$ index.php?/$1 [L]
</IfModule>
```

## Environment Variable
```php
// index.php
define('ENVIRONMENT', isset($_SERVER['CI_ENV']) ? $_SERVER['CI_ENV'] : 'development');
```
**⚠️ Set `CI_ENV=production` di server live**

## Production Checklist

| Setting | Development | Production |
|---------|-------------|------------|
| ENVIRONMENT | development | **production** |
| csrf_protection | FALSE | **TRUE** |
| sess_match_ip | FALSE | **TRUE** |
| cookie_secure | FALSE | **TRUE** (HTTPS) |
| cookie_httponly | FALSE | **TRUE** |
| DB username | root | **dedicated user** |
| DB password | (empty) | **strong password** |
| log_threshold | 1 | 1 or 2 |
| base_url | localhost | **domain.com** |
