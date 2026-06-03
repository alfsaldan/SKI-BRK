# 06 — Controller Documentation

## Controller Overview

| # | Controller | File | Size | Methods | Purpose |
|---|-----------|------|------|---------|---------|
| 1 | Auth | [Auth.php](file:///d:/Laragon/laragon/www/SKI-BRK/application/controllers/Auth.php) | 3.4KB | 5 | Authentication |
| 2 | Administrator | [Administrator.php](file:///d:/Laragon/laragon/www/SKI-BRK/application/controllers/Administrator.php) | 160KB | 50+ | Admin SKI management |
| 3 | Pegawai | [Pegawai.php](file:///d:/Laragon/laragon/www/SKI-BRK/application/controllers/Pegawai.php) | 156KB | 40+ | Employee dashboard |
| 4 | SuperAdmin | [SuperAdmin.php](file:///d:/Laragon/laragon/www/SKI-BRK/application/controllers/SuperAdmin.php) | 10KB | 12 | System admin |
| 5 | Administrator_Renstra | [Administrator_Renstra.php](file:///d:/Laragon/laragon/www/SKI-BRK/application/controllers/Administrator_Renstra.php) | 15KB | 14 | KPI management |
| 6 | ExcelImport | [ExcelImport.php](file:///d:/Laragon/laragon/www/SKI-BRK/application/controllers/ExcelImport.php) | 22KB | 8 | Excel import |
| 7 | Welcome | [Welcome.php](file:///d:/Laragon/laragon/www/SKI-BRK/application/controllers/Welcome.php) | 679B | 1 | Default CI welcome |

---

## 1. Auth Controller

**Tujuan**: Menangani autentikasi user (login, logout, cek role)

**Auth Guard**: Tidak ada (halaman publik)

| Method | Fungsi | Model | View |
|--------|--------|-------|------|
| `index()` | Tampilkan form login | - | `login` |
| `login()` | Proses login via NIK+password | Auth_model | redirect |
| `logout()` | Hapus session & redirect | - | redirect to `auth` |
| `check_role()` | AJAX: cek role by NIK | Auth_model | JSON |
| `create_superadmin()` | Buat superadmin default | - (direct DB) | echo text |

**Call Flow**:
```
User → POST /auth/login → Auth_model.get_user(nik)
  → password_verify → set session → redirect by role
```

---

## 2. Administrator Controller

**Tujuan**: Pengelolaan SKI oleh administrator (indikator, penilaian, data pegawai, monitoring, verifikasi, PPK)

**Auth Guard**: `role === 'administrator'`

**Models Loaded**: Indikator_model, Penilaian_model, RiwayatJabatan_model, DataDiri_model, DataPegawai_model, PenilaiMapping_model, Administrator_model, Budaya_model, Monitoring_model, Syarat_ppk_model, Ppk_responses_model

**Functional Groups**:

### Dashboard
| Method | Fungsi | View |
|--------|--------|------|
| `index()` | Dashboard dengan statistik & grafik | `administrator/index` |
| `get_grafik_all()` | AJAX: data grafik seluruh pegawai | JSON |
| `get_grafik_cabang($kode)` | AJAX: grafik per cabang | JSON |
| `get_grafik_unit($kode)` | AJAX: grafik per unit | JSON |
| `get_unit_kantor($kode)` | AJAX: dropdown unit per cabang | JSON |

### Indikator Kinerja
| Method | Fungsi | View |
|--------|--------|------|
| `indikatorKinerja()` | Kelola sasaran & indikator kinerja | `administrator/indikatorkinerja` |
| `getJabatanByUnit()` | AJAX: dropdown jabatan by unit | JSON |
| `addSasaranKerja()` | Tambah sasaran kerja | redirect |
| `addIndikator()` | Tambah indikator | redirect |
| `deleteIndikator($id)` | Hapus indikator | redirect |
| `updateIndikator()` | AJAX: update indikator | JSON |
| `updateSasaran()` | AJAX: update sasaran | JSON |
| `saveSasaranAjax()` | AJAX: simpan sasaran baru | JSON |
| `saveIndikatorAjax()` | AJAX: simpan indikator baru | JSON |
| `deleteIndikatorAjax()` | AJAX: hapus indikator | JSON |

### Penilaian Kinerja
| Method | Fungsi | View |
|--------|--------|------|
| `penilaiankinerja()` | Halaman utama penilaian | `administrator/penilaiankinerja` |
| `cariPenilaian()` | Cari & tampilkan penilaian pegawai | `administrator/penilaiankinerja` |
| `simpanPenilaian()` | POST: simpan semua penilaian | redirect |
| `simpanPenilaianBaris()` | AJAX: simpan penilaian per baris | JSON |
| `simpanNilaiAkhir()` | AJAX: simpan nilai akhir | JSON |
| `tambahPeriodePenilaian()` | AJAX: tambah periode baru | JSON |
| `getLockStatus()` / `setLockStatus()` | AJAX: lock/unlock input | JSON |
| `getLockStatus2()` / `setLockStatus2()` | AJAX: lock/unlock input 2 | JSON |

### Kelola Data Pegawai
| Method | Fungsi | View |
|--------|--------|------|
| `kelolaDataPegawai()` | Daftar semua pegawai | `administrator/keloladatapegawai` |
| `tambahPegawai()` | Tambah pegawai manual | redirect |
| `importPegawai()` | Import pegawai dari Excel | redirect |
| `importMutasiPegawai()` | Import mutasi jabatan Excel | redirect |
| `downloadTemplatePegawai()` | Download template Excel pegawai | file download |
| `downloadTemplateMutasiPegawai()` | Download template mutasi | file download |
| `deletePegawai($nik)` | Hapus pegawai | redirect |
| `detailPegawai($nik)` | Detail pegawai + riwayat | `administrator/detailpegawai` |
| `updateJabatan()` | Mutasi jabatan baru | redirect |
| `nonaktifPegawai($nik)` | Nonaktifkan pegawai | redirect |
| `aktifkanPegawai($nik)` | Aktifkan pegawai | redirect |
| `dataPegawai()` | Halaman cek data pegawai | `administrator/datapegawai` |
| `cariDataPegawai()` | Cari & tampilkan data pegawai | `administrator/datapegawai` |
| `downloadDataPegawai()` | Export data penilaian ke Excel | file download |

---

## 3. Pegawai Controller

**Tujuan**: Dashboard & fitur untuk pegawai (input target/realisasi, coaching, PPK, ubah penilai)

**Auth Guard**: `role IN ('pegawai', 'administrator', 'administrator_renstra')`

**Special**: Force change default password (NIK=password) sebelum akses fitur lain

**Models Loaded**: Pegawai_model (subfolder), Nilai_model, Coaching_model, MonitoringPegawai_model, Penilaian_model, Indikator_model, DataDiri_model, Administrator_model, Ppk_model

### Functional Groups:

| Group | Methods | View |
|-------|---------|------|
| Dashboard | `index()` | `pegawai/index` |
| Penilaian Diri | `simpanPenilaianBaris()`, `simpanNilaiAkhir()`, `simpan_sasaran_baru()`, `simpan_indikator_baru()` | AJAX JSON |
| Nilai Pegawai (Penilai) | `nilaiPegawai()`, `nilaiPegawaiDetail()`, `nilaiPegawaiDetail2()` | `pegawai/nilaipegawai*` |
| Penilai Mapping | `getPenilaiCandidates()`, `updatePenilai()` | AJAX / redirect |
| Data Diri | `datadiriPegawai()` | `pegawai/datadiripegawai` |
| Monitoring | `monitoringIndividu()` | `pegawai/monitoringindividu` |
| PPK | `ppk_pegawai()`, `ppk_penilai()`, `ppk_pimpinan()` | `pegawai/ppk_*` |
| Coaching | Chat system via AJAX | `pegawai/index` (embedded) |

---

## 4. SuperAdmin Controller

**Tujuan**: Manajemen sistem: user management, mapping penilai, tingkatan jabatan

**Auth Guard**: `role === 'superadmin'`

**Models Loaded**: SuperAdmin_model, PenilaiMapping_model

| Method | Fungsi | View |
|--------|--------|------|
| `index()` | Dashboard statistik user | `superadmin/index` |
| `kelolaRoleUser()` | CRUD users | `superadmin/kelolaroleuser` |
| `tambahRoleUser()` | Tambah user + validasi | redirect |
| `editRoleUser()` | Edit user | redirect |
| `hapusRoleUser($id)` | Hapus user | redirect |
| `kelolatingkatanjabatan_kpi()` | Mapping cabang-unit-jabatan | `superadmin/kelolatingkatanjabatan_kpi` |
| `tambahPenilaiMapping()` | Tambah mapping | redirect |
| `editPenilaiMapping($id)` | Edit mapping | redirect |
| `hapusPenilaiMapping($id)` | Hapus mapping | redirect |
| `getKodeUnit($kode_cabang)` | AJAX: unit per cabang | JSON |
| `getMappingJabatan($kode_unit)` | AJAX: mapping per unit | JSON |
| `kelolarumus()` | Kelola rumus (placeholder) | `superadmin/kelolarumus` |

---

## 5. Administrator_Renstra Controller

**Tujuan**: Pengelolaan KPI (Key Performance Indicator) berbasis Rencana Strategis

**Auth Guard**: `role === 'administrator_renstra'`

**Models**: KPI_Indikator_model, KPI_Penilaian_model, DataPegawai_model, PenilaiMapping_model

| Method | Fungsi | View |
|--------|--------|------|
| `index()` | Dashboard Renstra | `administrator_renstra/index` |
| `kpi_indikatorKinerja()` | Kelola indikator KPI | `administrator_renstra/kpi_indikatorkinerja` |
| `kpi_penilaianKinerja()` | Halaman penilaian KPI | `administrator_renstra/kpi_penilaiankinerja` |
| `cariPenilaian()` | Cari pegawai KPI | `administrator_renstra/kpi_penilaiankinerja` |
| `lihatPenilaianRenstra()` | Detail penilaian KPI per pegawai | `administrator_renstra/kpi_penilaiankinerja` |

---

## 6. ExcelImport Controller

**Tujuan**: Import indikator kinerja dari file Excel

**Auth Guard**: Tidak ada (⚠️ Security Issue)

**Models**: Indikator_model

| Method | Fungsi |
|--------|--------|
| `uploadIndikatorKinerja()` | Parse Excel → preview JSON (admin) |
| `uploadIndikatorKinerjaPegawai()` | Parse Excel → preview JSON (pegawai, dengan NIK) |
| `saveParsedData()` | Simpan data parsed ke DB (admin) |
| `saveParsedDataPegawai()` | Simpan data parsed ke DB (pegawai, owner_nik) |

**Core Parser**: `parseIndikatorSheet()` — Membaca format template Excel dengan kolom Perspektif (A), Sasaran (C/D), Bobot (G), Indikator (I/H).
