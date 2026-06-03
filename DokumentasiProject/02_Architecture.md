# 02 — Architecture

## Framework
**CodeIgniter 3.x** (latest 3.1.x branch) — MVC Architecture

## Architecture Pattern
Classic **Model–View–Controller (MVC)** with the following layers:

```mermaid
graph TD
    A[Browser/Client] -->|HTTP Request| B[.htaccess / index.php]
    B --> C[CI Router]
    C --> D[Controller]
    D --> E[Model]
    E --> F[(MySQL Database)]
    D --> G[View]
    G --> H[Layout Header + Content + Footer]
    D --> I[Library / Helper]
    I --> E
```

## Request Lifecycle

```
1. Client sends HTTP Request
2. .htaccess rewrites URL → index.php
3. index.php bootstraps CodeIgniter
4. CI_Router parses URI → maps to Controller/Method
5. Controller constructor:
   - Loads required Models
   - Checks session authentication & role authorization
   - Redirects to `auth` if unauthorized
6. Controller method:
   - Reads input (GET/POST)
   - Calls Model methods for data operations
   - Prepares $data array
   - Loads Views (header → content → footer)
7. View renders HTML/JSON response
8. CI_Output sends response to client
```

## Authentication Flow

```mermaid
sequenceDiagram
    participant U as User
    participant V as Login View
    participant A as Auth Controller
    participant M as Auth_model
    participant DB as Database
    participant S as Session

    U->>V: Enter NIK + Password
    V->>A: POST /auth/login
    A->>M: get_user(nik)
    M->>DB: SELECT FROM users WHERE nik=? AND is_active=1
    DB-->>M: User record
    M-->>A: User object
    A->>A: password_verify(input, hash)
    A->>A: Check default password (NIK == password)
    A->>S: Set session (nik, role, logged_in, must_change_password)
    A-->>U: Redirect based on role
```

## Layered Architecture

| Layer | Folder | Responsibility |
|-------|--------|----------------|
| **Routing** | `config/routes.php` | URL → Controller mapping |
| **Controller** | `controllers/` | Request handling, auth guard, business orchestration |
| **Model** | `models/` | Database queries, CRUD, business logic |
| **View** | `views/` | HTML rendering, JS-based AJAX interaction |
| **Library** | `libraries/` | Reusable utilities (Excel) |
| **Config** | `config/` | System & app configuration |
| **Assets** | `assets/` | CSS, JS, images, third-party plugins |

## Layout System
Each role uses a separate layout wrapper:

| Role | Header | Footer | Content Folder |
|------|--------|--------|----------------|
| Administrator | `layout/header.php` | `layout/footer.php` | `administrator/` |
| Pegawai | `layoutpegawai/header.php` | `layoutpegawai/footer.php` | `pegawai/` |
| SuperAdmin | `layoutsuperadmin/header.php` | `layoutsuperadmin/footer.php` | `superadmin/` |
| Admin Renstra | `layoutrenstra/header.php` | `layoutrenstra/footer.php` | `administrator_renstra/` |

## Component Dependency Map

```mermaid
graph LR
    subgraph Controllers
        AUTH[Auth]
        ADM[Administrator]
        PEG[Pegawai]
        SA[SuperAdmin]
        AR[Administrator_Renstra]
        EI[ExcelImport]
    end

    subgraph Models
        AM[Auth_model]
        ADM_M[Administrator_model]
        PM[Penilaian_model]
        IM[Indikator_model]
        DPM[DataPegawai_model]
        PMM[PenilaiMapping_model]
        SAM[SuperAdmin_model]
        KIM[KPI_Indikator_model]
        KPM[KPI_Penilaian_model]
        PGM[Pegawai_model]
        NM[Nilai_model]
        CM[Coaching_model]
        MPM[MonitoringPegawai_model]
        PPK[Ppk_model]
    end

    AUTH --> AM
    ADM --> PM & IM & DPM & PMM & ADM_M
    PEG --> PGM & NM & CM & MPM & PM & IM & PPK
    SA --> SAM & PMM
    AR --> KIM & KPM & DPM & PMM
    EI --> IM
```

## Key Design Decisions
1. **No ORM** — Uses CodeIgniter Query Builder for all database operations
2. **Session-based Auth** — File-based sessions (not DB), 2-hour expiry
3. **Role-based Access** — Checked in constructor of each controller
4. **AJAX-heavy UI** — Most data operations use AJAX (jQuery $.ajax / fetch)
5. **Excel Integration** — PHPSpreadsheet for import/export of data
6. **Multi-tier Assessment** — Penilai I, Penilai II, Administrator verification workflow
