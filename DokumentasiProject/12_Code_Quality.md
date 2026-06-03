# 12 — Code Quality & Refactoring Analysis

## Code Smells Identified

### 🔴 God Controller Anti-Pattern
- **Administrator.php**: 3,879 lines / 160KB — Terlalu besar
- **Pegawai.php**: 3,603 lines / 156KB — Terlalu besar
- **Rekomendasi**: Split ke sub-controllers atau services:
  - `AdministratorDashboardController`
  - `AdministratorPenilaianController`
  - `AdministratorPegawaiController`
  - `AdministratorMonitoringController`
  - `AdministratorVerifikasiController`
  - `AdministratorPpkController`

### 🔴 Duplicated Code

1. **getPegawaiWithPenilai()** — Duplikat di 3 model:
   - `Penilaian_model.php` (line 202-230)
   - `DataPegawai_model.php` (line 181-209)
   - `pegawai/Nilai_model.php`
   - **Rekomendasi**: Pindahkan ke satu model shared (misal `Pegawai_base_model`)

2. **getClosestPeriode() logic** — Duplikat di Pegawai.php dan Administrator.php
   - Pegawai controller memiliki inline period resolution (line 82-108)
   - Administrator menggunakan `getClosestPeriode()` helper method
   - **Rekomendasi**: Extract ke shared helper

3. **Lock status methods** — Duplikat antara `Penilaian_model` dan `pegawai/Pegawai_model`
   - `getLockStatus()` / `setLockStatus()` — ada di kedua model
   - **Rekomendasi**: Centralize ke satu model

4. **getAllBudaya()** — Duplikat di 3 model:
   - `Penilaian_model`
   - `DataPegawai_model`
   - `pegawai/Nilai_model`

### ⚠️ Inconsistent Response Format
- Beberapa AJAX endpoint mengembalikan:
  ```php
  ['status' => 'success', 'message' => '...']  // Format 1
  ['success' => true, 'message' => '...']       // Format 2
  ```
- **Rekomendasi**: Standarisasi format response

### ⚠️ Direct DB Queries in Controllers
- Controller seharusnya tidak langsung akses `$this->db`:
  ```php
  // Pegawai.php line 133-139
  $status2_data = $this->db->select('indikator_id, status2')
      ->from('penilaian')...
  ```
- **Rekomendasi**: Pindahkan ke model

### ⚠️ Hardcoded Default Periode
```php
// Penilaian_model.php
if (!$periode_awal) $periode_awal = '2025-01-01';  // Hardcoded year!
if (!$periode_akhir) $periode_akhir = '2025-12-31';
```
- **Rekomendasi**: Gunakan `date('Y-01-01')` dinamis

### ⚠️ Duplicate Validation in updateJabatan()
```php
// Administrator.php line 1249-1260 — Validasi yang sama diulang 2x
if (empty($nik) || empty($jabatan) || ...) { // Line 1249
    ...
}
if (empty($nik) || empty($jabatan) || ...) { // Line 1256 — DUPLIKAT!
    ...
}
```

## Dead Code / Unused Code

| File | Location | Description |
|------|----------|-------------|
| Administrator_model.php | Line 281-315 | Commented-out dummy grafik methods |
| Welcome.php | Entire file | Default CI controller, not used |
| welcome_message.php | Entire file | Default CI view, not used |
| libraries/Excel.php | Entire file | Empty/minimal library |

## Naming Inconsistencies

| Issue | Example | Standard |
|-------|---------|----------|
| Method naming | `kelolaDataPegawai()` vs `dataPegawai()` | camelCase consistent |
| Method naming | `tambahPeriodePenilaian()` vs `addSasaranKerja()` | Bahasa campuran |
| View naming | `keloladatapegawai.php` vs `datapegawai.php` | lowercase concat |
| AJAX endpoint | GET vs POST inconsistent | Standardize HTTP methods |

## Complexity Metrics

| Controller | Lines | Public Methods | Complexity |
|-----------|-------|----------------|------------|
| Administrator | 3,879 | 50+ | 🔴 Very High |
| Pegawai | 3,603 | 40+ | 🔴 Very High |
| ExcelImport | 530 | 8 | ⚠️ High (parsing) |
| Administrator_Renstra | 395 | 14 | ✅ Moderate |
| SuperAdmin | 272 | 12 | ✅ Good |
| Auth | 120 | 5 | ✅ Good |

## Refactoring Recommendations (Priority)

1. **Split God Controllers** — Break Administrator.php and Pegawai.php into feature-based controllers
2. **Extract Shared Models** — Create base model for duplicated methods (getPegawaiWithPenilai, getAllBudaya, etc.)
3. **Create Helper Classes** — Period resolution, lock status management
4. **Standardize AJAX Response** — Create a response trait/helper
5. **Move DB queries out of controllers** — All `$this->db` calls should be in models
6. **Create CI Hooks for Auth** — Replace per-controller auth checks with pre_controller hook
7. **Add Form Validation Library** — Use CI form_validation instead of manual checks
8. **Remove Dead Code** — Delete commented-out code and unused files
