# 08 — View Documentation

## Layout System

| Layout | Header | Footer | Role |
|--------|--------|--------|------|
| Admin | `layout/header.php` | `layout/footer.php` | administrator |
| Pegawai | `layoutpegawai/header.php` | `layoutpegawai/footer.php` | pegawai |
| SuperAdmin | `layoutsuperadmin/header.php` | `layoutsuperadmin/footer.php` | superadmin |
| Renstra | `layoutrenstra/header.php` | `layoutrenstra/footer.php` | administrator_renstra |

## Administrator Views

| View | Controller | Fungsi |
|------|-----------|--------|
| `administrator/index.php` | `index()` | Dashboard: statistik + grafik |
| `administrator/indikatorkinerja.php` | `indikatorKinerja()` | CRUD sasaran & indikator |
| `administrator/penilaiankinerja.php` | `penilaiankinerja()` | Form penilaian lengkap |
| `administrator/keloladatapegawai.php` | `kelolaDataPegawai()` | DataTable pegawai + import |
| `administrator/detailpegawai.php` | `detailPegawai()` | Detail + riwayat jabatan |
| `administrator/datapegawai.php` | `dataPegawai()` | Cek data pegawai |
| `administrator/monitoringkinerja.php` | `monitoringKinerja()` | Monitoring + coaching |
| `administrator/verifikasi_penilaian.php` | `verifikasiPenilaian()` | Verifikasi penilaian |
| `administrator/detailverifikasi.php` | `detailVerifikasi()` | Detail verifikasi |
| `administrator/kelolatingkatanjabatan.php` | `kelolaTingkatanJabatan()` | Mapping jabatan |
| `administrator/kelolabudaya.php` | `kelolaBudaya()` | CRUD budaya kerja |
| `administrator/monitoring_ppk.php` | `monitoringPpk()` | Monitoring PPK |
| `administrator/verifikasi_ppk.php` | `verifikasiPpk()` | Verifikasi PPK |
| `administrator/ppk_msdiformulir.php` | - | Formulir PPK MSD |
| `administrator/ppk_msdievaluasi.php` | - | Evaluasi PPK MSD |

## Pegawai Views

| View | Controller | Fungsi |
|------|-----------|--------|
| `pegawai/index.php` (182KB) | `index()` | Dashboard lengkap |
| `pegawai/nilaipegawai.php` | `nilaiPegawai()` | Daftar pegawai dinilai |
| `pegawai/nilaipegawai_detail.php` | `nilaiPegawaiDetail()` | Penilaian Penilai 1 |
| `pegawai/nilaipegawai_detail2.php` | `nilaiPegawaiDetail2()` | Penilaian Penilai 2 |
| `pegawai/datadiripegawai.php` | `datadiriPegawai()` | Profile + ubah password |
| `pegawai/monitoringindividu.php` | `monitoringIndividu()` | Monitoring + catatan |
| `pegawai/ppk_pegawai.php` | - | PPK pegawai |
| `pegawai/ppk_penilai.php` | - | PPK penilai |
| `pegawai/rekap_nilai.php` | - | Rekap nilai |
| `pegawai/arsip_detail.php` | - | Arsip penilaian |

## SuperAdmin Views

| View | Controller | Fungsi |
|------|-----------|--------|
| `superadmin/index.php` | `index()` | Dashboard |
| `superadmin/kelolaroleuser.php` | `kelolaRoleUser()` | CRUD users |
| `superadmin/kelolatingkatanjabatan_kpi.php` | `kelolatingkatanjabatan_kpi()` | Mapping jabatan |
| `superadmin/kelolarumus.php` | `kelolarumus()` | Placeholder |

## Renstra Views

| View | Controller | Fungsi |
|------|-----------|--------|
| `administrator_renstra/index.php` | `index()` | Dashboard Renstra |
| `administrator_renstra/kpi_indikatorkinerja.php` | `kpi_indikatorKinerja()` | Indikator KPI |
| `administrator_renstra/kpi_penilaiankinerja.php` | `kpi_penilaianKinerja()` | Penilaian KPI |

## JS Libraries Used in Views
- **jQuery AJAX** — Form submissions
- **DataTables** — Table rendering
- **Chart.js** — Dashboard charts
- **SweetAlert2** — Confirmations
- **Select2** — Enhanced dropdowns
