# 01 — Project Overview

## Nama Project
**SKI-BRK** — Sistem Sasaran Kinerja Individu (SKI) Bank Riau Kepri Syariah

## Deskripsi Singkat
Aplikasi web untuk mengelola **penilaian kinerja individu pegawai** (SKI) dan **Key Performance Indicator** (KPI) pada PT Bank Riau Kepri Syariah. Sistem ini memungkinkan pengelolaan indikator kinerja, penilaian oleh atasan (Penilai I & II), verifikasi, coaching, serta pelaporan hasil kinerja pegawai per periode.

## Tujuan Sistem
1. Mendigitalkan proses penilaian kinerja pegawai bank yang sebelumnya manual
2. Menyediakan hierarki penilaian (Penilai I → Penilai II → Verifikasi)
3. Menyediakan dashboard monitoring kinerja untuk administrator
4. Mendukung proses PPK (Penilaian Perilaku Kerja) berbasis formulir
5. Menyediakan modul KPI untuk penilaian berbasis Rencana Strategis (Renstra)
6. Menyediakan export laporan ke Excel (.xlsx)

## Domain Bisnis
- Perbankan Syariah (BRK Syariah)
- Human Resource Management (HRM)
- Performance Management System (PMS)

## User Roles

| Role | Deskripsi | Akses Utama |
|------|-----------|-------------|
| `superadmin` | Administrator tertinggi | Kelola users, role, mapping penilai, tingkatan jabatan |
| `administrator` | Admin penilai SKI | Dashboard, indikator, penilaian, kelola pegawai, verifikasi, monitoring, PPK, export |
| `administrator_renstra` | Admin penilai KPI | Indikator KPI, penilaian KPI |
| `pegawai` | Karyawan bank | Dashboard individu, input target/realisasi, coaching, ubah penilai, PPK |

## Base URL
```
http://localhost/SKI-BRK/
```

## Repository Structure (Root)
```
SKI-BRK/
├── application/          # CodeIgniter Application (MVC)
│   ├── config/           # Konfigurasi CI
│   ├── controllers/      # 7 Controllers
│   ├── core/             # Core override (empty)
│   ├── helpers/          # Custom helpers (empty)
│   ├── hooks/            # Hooks (empty)
│   ├── libraries/        # Custom libraries (Excel.php)
│   ├── models/           # 15 Models + 5 sub-models
│   └── views/            # Views (10 directories, 40+ files)
├── assets/               # CSS, JS, Images, Fonts, Plugins
├── dist/                 # Template theme (Codefox Admin)
├── sql/                  # SQL Migration scripts (4 files)
├── src/                  # SCSS source files
├── third_party_js/       # QRCode.js, xlsx-js
├── uploads/              # Excel templates & temp files
├── vendor/               # Composer packages (PhpSpreadsheet)
├── system/               # CodeIgniter core framework
├── composer.json         # PHP dependencies
├── package.json          # NPM dependencies (Codefox theme)
├── index.php             # Front controller
├── .htaccess             # URL rewriting
└── gulpfile.js           # Asset build pipeline
```

## Statistik Kode

| Komponen | Jumlah | Total Size |
|----------|--------|------------|
| Controllers | 7 files | ~367 KB |
| Models | 20 files | ~148 KB |
| Views | 40+ files | ~1.8 MB |
| Config | 15 files | ~56 KB |
| SQL Migrations | 4 files | ~4.6 KB |
| Libraries | 1 file | 523 B |

## Tanggal Audit
**3 Juni 2026**
