# 10 — Business Flow Documentation

## 1. Alur Utama: Siklus Penilaian Kinerja (SKI)

```mermaid
flowchart TD
    A[Admin buat Periode Penilaian] --> B[Admin upload/buat Indikator Kinerja]
    B --> C[Pegawai input Target & Realisasi]
    C --> D[Penilai I review & setujui/catatan]
    D --> E{Status Penilai I?}
    E -->|Ada Catatan| C
    E -->|Disetujui| F[Penilai II review & setujui/catatan]
    F --> G{Status Penilai II?}
    G -->|Ada Catatan| C
    G -->|Disetujui| H[Admin verifikasi akhir]
    H --> I{Status Verifikasi?}
    I -->|Ditolak| C
    I -->|Disetujui| J[Nilai Akhir Tersimpan]
    J --> K[Dashboard & Laporan]
```

## 2. Detail Alur per Fase

### Fase 1: Setup Periode & Indikator (Administrator)
1. Admin login → Dashboard
2. Admin menambah **Periode Penilaian** baru (periode_awal, periode_akhir)
3. Admin mengelola **Sasaran Kerja** per jabatan & unit kerja
4. Admin menambah **Indikator Kinerja** untuk setiap sasaran
5. Alternatif: Admin **upload Excel** template indikator (via ExcelImport)

### Fase 2: Input Penilaian (Pegawai)
1. Pegawai login → Dashboard
2. Sistem menampilkan indikator sesuai jabatan & unit kerja pegawai
3. Pegawai mengisi: **Target**, **Batas Waktu**, **Realisasi**
4. Sistem menghitung otomatis: Pencapaian, Nilai, Nilai Dibobot
5. Pegawai mengisi **Nilai Budaya** (skala 1-5 per aspek)
6. Pegawai melihat **Nilai Akhir** (readonly, auto-calculate)
7. Pegawai bisa menambah **Sasaran Baru** / **Indikator Baru** (owner_nik = self)

### Fase 3: Penilaian Penilai I
1. Penilai I login → Menu "Nilai Pegawai"
2. Sistem menampilkan daftar pegawai yang dia nilai (berdasarkan `penilai_mapping`)
3. Penilai I klik pegawai → Review penilaian
4. Penilai I bisa: **Setujui** / **Ada Catatan** per indikator (`status`)
5. Jika semua disetujui → Lanjut ke Penilai II

### Fase 4: Penilaian Penilai II
1. Penilai II login → Menu "Nilai Pegawai"
2. Review yang sama seperti Penilai I
3. Penilai II bisa: **Setujui** / **Ada Catatan** (`status2`)
4. Jika semua disetujui → Siap verifikasi admin

### Fase 5: Verifikasi Administrator
1. Admin → Menu "Verifikasi Penilaian"
2. Admin melihat daftar pegawai per periode
3. Admin memeriksa kelengkapan penilaian
4. Admin bisa: **Setujui** / **Tolak** (`status_penilaian`)
5. Status "disetujui" → Penilaian final dan ter-lock

## 3. Alur Lock/Unlock Input

```mermaid
sequenceDiagram
    participant A as Admin
    participant S as System
    participant P as Pegawai

    A->>S: setLockStatus(periode, true)
    S-->>S: UPDATE penilaian SET lock_input=1
    P->>S: Buka dashboard
    S-->>P: Form disabled (readonly)
    Note right of P: Tidak bisa edit target/realisasi
    A->>S: setLockStatus(periode, false)
    S-->>S: UPDATE penilaian SET lock_input=0
    P->>S: Buka dashboard
    S-->>P: Form aktif (editable)
```

## 4. Alur Import Pegawai (Excel)

```mermaid
flowchart TD
    A[Admin pilih file .xlsx] --> B[Validasi ekstensi]
    B --> C[Parse via PhpSpreadsheet]
    C --> D[Validasi header: NIK,Nama,Jabatan,Unit Kerja,Unit Kantor,Password]
    D --> E{Header valid?}
    E -->|Tidak| F[Error: Header tidak sesuai]
    E -->|Ya| G[Loop setiap baris]
    G --> H{NIK valid & unik?}
    H -->|Tidak| I[Catat error]
    H -->|Ya| J[Siapkan data]
    J --> K[Password default=NIK jika kosong]
    K --> L[Insert ke tabel pegawai]
    L --> M[Insert ke tabel users role=pegawai]
    M --> N[Insert riwayat_jabatan awal]
    I --> O{Ada baris lain?}
    N --> O
    O -->|Ya| G
    O -->|Tidak| P[Tampilkan summary sukses/error]
```

## 5. Alur Mutasi Jabatan

```mermaid
flowchart TD
    A[Admin input mutasi: NIK,jabatan baru,unit,tgl_mulai] --> B{Pegawai punya penilaian?}
    B -->|Ya| C{Semua status_penilaian = disetujui?}
    C -->|Tidak| D[Error: masih ada penilaian belum selesai]
    C -->|Ya| E[Mark semua penilaian 'selesai']
    B -->|Tidak| E2[Skip check penilaian]
    E --> F[Update periode_akhir penilaian lama]
    E2 --> F
    F --> G[Tutup riwayat_jabatan lama: tgl_selesai + nonaktif]
    G --> H[Insert riwayat_jabatan baru: tgl_mulai + aktif]
    H --> I[Update tabel pegawai: jabatan,unit_kerja,unit_kantor]
    I --> J[Redirect ke detail pegawai]
```

## 6. Alur Coaching

```mermaid
sequenceDiagram
    participant PG as Pegawai
    participant S as System
    participant PN as Penilai

    PG->>S: Kirim pesan (aktivitas_coaching)
    S-->>S: INSERT aktivitas_coaching
    PN->>S: Buka monitoring
    S-->>PN: Tampilkan chat history
    PN->>S: Kirim balasan
    S-->>S: INSERT aktivitas_coaching
    PG->>S: Refresh dashboard
    S-->>PG: Pesan baru muncul
```

## 7. Alur PPK (Penilaian Perilaku Kerja)

```mermaid
flowchart TD
    A[Admin kelola Syarat PPK master] --> B[Pegawai isi formulir PPK]
    B --> C[Jawaban disimpan: ppk_responses.answers JSON]
    C --> D[Hitung eligibility: semua 'ya' = eligible]
    D --> E[Update pegawai.ppk_eligible]
    E --> F[Penilai review & evaluasi PPK]
    F --> G[Admin monitoring PPK]
```

## 8. Predikat Penilaian

| Range Nilai | Predikat |
|-------------|----------|
| < 60 | Minus |
| 60 - 70 | Fair |
| 70 - 80 | Good |
| 80 - 90 | Very Good |
| ≥ 90 | Excellent |
