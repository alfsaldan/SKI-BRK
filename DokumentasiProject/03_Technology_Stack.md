# 03 — Technology Stack

## Technology Matrix

| Teknologi | Versi | Fungsi |
|-----------|-------|--------|
| **PHP** | ≥5.3.7 (recommended 7.4+) | Backend language |
| **CodeIgniter** | 3.x (3.1.13) | MVC Framework |
| **MySQL/MariaDB** | 5.7+ | Database (mysqli driver) |
| **Apache** | 2.x (via Laragon) | Web server |
| **Bootstrap** | 4.3.1 | CSS Framework / responsive layout |
| **jQuery** | 3.4.1 | DOM manipulation, AJAX |
| **DataTables** | 1.10.19 | Interactive data tables |
| **Chart.js** | 2.7.2 | Dashboard charts |
| **SweetAlert2** | 8.13.4 | Modal dialogs & confirmations |
| **Select2** | 4.0.6-rc.1 | Enhanced dropdowns |
| **D3.js** | 5.7.0 | Organizational chart visualization |
| **Moment.js** | 2.24.0 | Date manipulation |
| **MetisMenu** | 3.0.4 | Sidebar navigation |
| **PhpSpreadsheet** | ^5.1 | Excel import/export |
| **Toastr** | 2.1.4 | Toast notifications |
| **Dropzone** | 5.5.1 | Drag & drop file upload |
| **Fullcalendar** | 3.9.0 | Calendar component |
| **Morris.js** | 0.5.0 | Chart alternative |
| **QRCode.js** | - | QR Code generation |
| **SheetJS (xlsx)** | - | Client-side Excel processing |

## Backend Details

### Framework: CodeIgniter 3
- **Source**: [application/config/config.php](file:///d:/Laragon/laragon/www/SKI-BRK/application/config/config.php)
- **Base URL**: `http://localhost/SKI-BRK/`
- **Index Page**: removed (mod_rewrite)
- **URI Protocol**: REQUEST_URI
- **Composer Autoload**: Loaded via `index.php` (line 281–283)

### PHP Dependencies (composer.json)
| Package | Version | Purpose |
|---------|---------|---------|
| `phpoffice/phpspreadsheet` | ^5.1 | Read/write Excel files (import pegawai, indikator, export penilaian) |
| `mikey179/vfsstream` | 1.6.* | Dev: virtual filesystem for testing |
| `phpunit/phpunit` | 4/5/9.* | Dev: unit testing |

**Path**: [composer.json](file:///d:/Laragon/laragon/www/SKI-BRK/composer.json)

### NPM Dependencies (package.json)
Theme: **Codefox 3.0.0** by Coderthemes

Key frontend packages:
| Package | Version | Purpose |
|---------|---------|---------|
| `bootstrap` | ^4.3.1 | UI Framework |
| `jquery` | ^3.4.1 | DOM & AJAX |
| `datatables.net-bs4` | ^1.10.19 | Data tables with Bootstrap 4 |
| `chart.js` | ^2.7.2 | Dashboard charts |
| `sweetalert2` | ^8.13.4 | Alert dialogs |
| `select2` | ^4.0.6-rc.1 | Enhanced select |
| `d3` | ^5.7.0 | D3 charts/orgchart |
| `toastr` | ^2.1.4 | Notifications |
| `moment` | ^2.24.0 | Date formatting |
| `pdfmake` | ^0.1.53 | PDF generation |
| `jszip` | ^3.1.3 | ZIP for DataTables export |
| `dropzone` | ^5.5.1 | File upload |
| `summernote` | ^0.8.10 | Rich text editor |
| `quill` | ^1.3.6 | Rich text editor |

**Path**: [package.json](file:///d:/Laragon/laragon/www/SKI-BRK/package.json)

## Server Requirements
- **Web Server**: Apache 2.x with `mod_rewrite` enabled
- **PHP**: ≥ 7.4 recommended (minimum 5.3.7 per composer.json)
- **PHP Extensions**: `mysqli`, `mbstring`, `zip`, `gd`, `xml`, `openssl`
- **Database**: MySQL 5.7+ / MariaDB 10.x
- **Composer**: For PHP dependency management
- **Node.js/NPM**: For theme asset compilation (optional, using Gulp)

## Asset Build Pipeline
- **Gulp 3.9.1**: Task runner for SCSS compilation, CSS minification, JS bundling
- **Source SCSS**: `src/scss/`
- **Compiled Assets**: `dist/assets/` and `assets/`
- **Config**: [gulpfile.js](file:///d:/Laragon/laragon/www/SKI-BRK/gulpfile.js)
