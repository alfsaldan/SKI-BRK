# 05 — Routing Documentation

## Routing System
CodeIgniter 3 menggunakan **segment-based routing**. URL diproses oleh `index.php` yang hidden via `.htaccess` rewrite.

**Pattern**: `http://localhost/SKI-BRK/{controller}/{method}/{params}`

## Explicit Routes
**Source**: [routes.php](file:///d:/Laragon/laragon/www/SKI-BRK/application/config/routes.php)

| URL Pattern | Controller | Method | Fungsi |
|-------------|------------|--------|--------|
| `/` (default) | Administrator | index | Dashboard Administrator |
| `/dashboard` | Administrator | index | Dashboard Administrator |
| `/login` | Auth | index | Halaman login |
| `/logout` | Auth | logout | Logout user |
| `/pegawai` | Pegawai | index | Dashboard Pegawai |
| `/administrator_renstra/kpi_indikatorkinerja` | Administrator_Renstra | kpi_indikatorKinerja | Kelola Indikator KPI |
| `/administrator_renstra/kpi_penilaiankinerja` | Administrator_Renstra | kpi_penilaianKinerja | Penilaian KPI |
| `/superadmin/getKodeUnit/{code}` | SuperAdmin | getKodeUnit | AJAX: Unit per cabang |
| `/superadmin/getMappingJabatan/{code}` | SuperAdmin | getMappingJabatan | AJAX: Mapping per unit |
| `/ExcelImport/uploadIndikatorKinerja` | ExcelImport | uploadIndikatorKinerja | Upload Excel indikator |
| `/ExcelImport/saveParsedData` | ExcelImport | saveParsedData | Simpan data parsed |
| `/ExcelImport/downloadTemplate` | ExcelImport | downloadTemplate | Download template |

## Implicit Routes (CI Convention)
Seluruh method `public` di controller otomatis bisa diakses via URL:

### Auth Controller
| URL | Method | HTTP | Fungsi |
|-----|--------|------|--------|
| `/auth` | index() | GET | Form login |
| `/auth/login` | login() | POST | Proses login |
| `/auth/logout` | logout() | GET | Logout |
| `/auth/check_role` | check_role() | POST/AJAX | Cek role by NIK |
| `/auth/create_superadmin` | create_superadmin() | GET | Buat superadmin default ⚠️ |

### Administrator Controller
| URL | Method | HTTP | Fungsi |
|-----|--------|------|--------|
| `/administrator` | index() | GET | Dashboard |
| `/administrator/indikatorKinerja` | indikatorKinerja() | GET | Kelola indikator |
| `/administrator/getJabatanByUnit` | getJabatanByUnit() | GET/AJAX | Dropdown jabatan |
| `/administrator/addSasaranKerja` | addSasaranKerja() | POST | Tambah sasaran |
| `/administrator/addIndikator` | addIndikator() | POST | Tambah indikator |
| `/administrator/deleteIndikator/{id}` | deleteIndikator() | GET | Hapus indikator |
| `/administrator/editIndikator/{id}` | editIndikator() | POST | Edit indikator |
| `/administrator/updateIndikator` | updateIndikator() | POST/AJAX | Update indikator |
| `/administrator/updateSasaran` | updateSasaran() | POST/AJAX | Update sasaran |
| `/administrator/saveSasaranAjax` | saveSasaranAjax() | POST/AJAX | Simpan sasaran |
| `/administrator/saveIndikatorAjax` | saveIndikatorAjax() | POST/AJAX | Simpan indikator |
| `/administrator/deleteIndikatorAjax` | deleteIndikatorAjax() | POST/AJAX | Hapus indikator |
| `/administrator/penilaiankinerja` | penilaiankinerja() | GET | Halaman penilaian |
| `/administrator/cariPenilaian` | cariPenilaian() | POST/GET | Cari pegawai untuk dinilai |
| `/administrator/simpanPenilaian` | simpanPenilaian() | POST | Simpan semua penilaian |
| `/administrator/simpanPenilaianBaris` | simpanPenilaianBaris() | POST/AJAX | Simpan per baris |
| `/administrator/simpanNilaiAkhir` | simpanNilaiAkhir() | POST/AJAX | Simpan nilai akhir |
| `/administrator/tambahPeriodePenilaian` | tambahPeriodePenilaian() | POST/AJAX | Tambah periode |
| `/administrator/getLockStatus` | getLockStatus() | GET/AJAX | Status lock input |
| `/administrator/setLockStatus` | setLockStatus() | POST/AJAX | Toggle lock |
| `/administrator/kelolaDataPegawai` | kelolaDataPegawai() | GET | Kelola data pegawai |
| `/administrator/tambahPegawai` | tambahPegawai() | POST | Tambah pegawai manual |
| `/administrator/importPegawai` | importPegawai() | POST | Import Excel pegawai |
| `/administrator/importMutasiPegawai` | importMutasiPegawai() | POST | Import mutasi |
| `/administrator/deletePegawai/{nik}` | deletePegawai() | GET | Hapus pegawai |
| `/administrator/detailPegawai/{nik}` | detailPegawai() | GET | Detail & riwayat |
| `/administrator/updateJabatan` | updateJabatan() | POST | Mutasi jabatan |
| `/administrator/dataPegawai` | dataPegawai() | GET | Cek data pegawai |
| `/administrator/cariDataPegawai` | cariDataPegawai() | GET/POST | Cari data pegawai |
| `/administrator/downloadDataPegawai` | downloadDataPegawai() | GET | Export Excel |
| `/administrator/get_grafik_all` | get_grafik_all() | GET/AJAX | Grafik semua |
| `/administrator/get_grafik_cabang/{kode}` | get_grafik_cabang() | GET/AJAX | Grafik per cabang |
| `/administrator/get_unit_kantor/{kode}` | get_unit_kantor() | GET/AJAX | Unit per cabang |
| `/administrator/get_grafik_unit/{kode}` | get_grafik_unit() | GET/AJAX | Grafik per unit |

### Pegawai Controller
| URL | Method | HTTP | Fungsi |
|-----|--------|------|--------|
| `/pegawai` | index() | GET | Dashboard pegawai |
| `/pegawai/simpanPenilaianBaris` | simpanPenilaianBaris() | POST/AJAX | Simpan penilaian |
| `/pegawai/simpanNilaiAkhir` | simpanNilaiAkhir() | POST/AJAX | Simpan nilai akhir |
| `/pegawai/nilaiPegawai` | nilaiPegawai() | GET | Daftar pegawai dinilai |
| `/pegawai/nilaiPegawaiDetail/{nik}` | nilaiPegawaiDetail() | GET | Detail penilaian (Penilai 1) |
| `/pegawai/nilaiPegawaiDetail2/{nik}` | nilaiPegawaiDetail2() | GET | Detail penilaian (Penilai 2) |
| `/pegawai/getPenilaiCandidates/{nik}` | getPenilaiCandidates() | GET/AJAX | Kandidat penilai |
| `/pegawai/updatePenilai` | updatePenilai() | POST | Update mapping penilai |
| `/pegawai/datadiriPegawai` | datadiriPegawai() | GET | Data diri pegawai |
| `/pegawai/simpan_sasaran_baru` | simpan_sasaran_baru() | POST/AJAX | Tambah sasaran |
| `/pegawai/simpan_indikator_baru` | simpan_indikator_baru() | POST/AJAX | Tambah indikator |
| `/pegawai/monitoringIndividu` | monitoringIndividu() | GET | Monitoring coaching |

### SuperAdmin Controller
| URL | Method | HTTP | Fungsi |
|-----|--------|------|--------|
| `/superadmin` | index() | GET | Dashboard SuperAdmin |
| `/superadmin/kelolaRoleUser` | kelolaRoleUser() | GET | Kelola role user |
| `/superadmin/tambahRoleUser` | tambahRoleUser() | POST | Tambah user |
| `/superadmin/editRoleUser` | editRoleUser() | POST | Edit user |
| `/superadmin/hapusRoleUser/{id}` | hapusRoleUser() | GET | Hapus user |
| `/superadmin/kelolatingkatanjabatan_kpi` | kelolatingkatanjabatan_kpi() | GET | Kelola mapping jabatan |
| `/superadmin/tambahPenilaiMapping` | tambahPenilaiMapping() | POST | Tambah mapping |
| `/superadmin/editPenilaiMapping/{id}` | editPenilaiMapping() | POST | Edit mapping |
| `/superadmin/hapusPenilaiMapping/{id}` | hapusPenilaiMapping() | GET | Hapus mapping |
| `/superadmin/kelolarumus` | kelolarumus() | GET | Kelola rumus |

### ExcelImport Controller
| URL | Method | HTTP | Fungsi |
|-----|--------|------|--------|
| `/ExcelImport/uploadIndikatorKinerja` | uploadIndikatorKinerja() | POST/AJAX | Upload Excel indikator (admin) |
| `/ExcelImport/uploadIndikatorKinerjaPegawai` | uploadIndikatorKinerjaPegawai() | POST/AJAX | Upload Excel indikator (pegawai) |
| `/ExcelImport/saveParsedData` | saveParsedData() | POST/AJAX | Simpan data parsed (admin) |
| `/ExcelImport/saveParsedDataPegawai` | saveParsedDataPegawai() | POST/AJAX | Simpan data parsed (pegawai) |
