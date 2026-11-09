# Tugas Mandiri: Admin Dashboard & Role-Based Access Control (RBAC)

Aplikasi Web Portal Akademik & Panel Backoffice Administrator berbasis **Laravel 13**, **AdminLTE v4 Layouting**, **Laravel Breeze Authentication**, dan **Spatie Laravel Permission (RBAC)** yang dibangun untuk memenuhi kriteria penilaian **Tugas Mandiri Pertemuan 10 (CPMK-1: 25%)** mata kuliah Pemrograman Berbasis Kerangka Kerja (PBKK), Departemen Teknik Informatika, Fakultas Teknologi Elektro dan Informatika Cerdas (FTEIC), Institut Teknologi Sepuluh Nopember (ITS).

---

## 👤 Identitas Mahasiswa

- **Nama:** Abdurrahman Arrafi Ravsan Zarnadi
- **NRP:** 5025241241
- **Email:** 5025241241@student.its.ac.id / 5025241241@alumni.its.ac.id
- **Departemen:** Teknik Informatika — FTEIC ITS
- **Mata Kuliah:** Pemrograman Berbasis Kerangka Kerja (PBKK) — Kelas A/B

---

## 🎯 Ringkasan Implementasi Tugas

Aplikasi ini merupakan integrasi menyeluruh antara portal akademik publik (Minggu 4) dengan panel backoffice admin profesional yang dilengkapi sistem otorisasi dinamis berbasis hak akses (RBAC).

```
               [ Halaman Login Breeze ]
                          │
         ┌────────────────┴────────────────┐
         ▼                                 ▼
   Peran: Admin / Staf             Peran: Mahasiswa (Publik)
         │                                 │
         ▼                                 ▼
[ Panel Admin Backoffice ]       [ Portal Akademik Publik ]
(/admin/dashboard)               (/beranda)
- Widget Statistik Agregat       - Profil & Mata Kuliah
- CRUD Mahasiswa, Dosen, Tugas   - Ide Riset Agentic AI
- Filter when() & Paginasi       - Toggle Mode Gelap
- Hak Hapus Eksklusif (Admin)
```

---

## 🔐 Akun Uji Coba & Demo Cepat (Auto-Login Credentials)

Tersedia **fitur tantangan 1-Klik Auto-Login (Challenge A+)** langsung pada halaman login (`/login`) atau melalui URL rute instan untuk mempermudah sesi evaluasi demo dosen/asdos:

| Peran (Role) | Email | Password | Hak Akses Utama | URL Login Instan |
| :--- | :--- | :--- | :--- | :--- |
| 👑 **Admin** | `admin@akademik.its.ac.id` | `password` | **Akses Penuh:** Melihat dashboard agregat, create, update, dan **Hak Eksklusif Hapus Data** | [`/quick-login/admin`](http://127.0.0.1:8000/quick-login/admin) |
| 🛡️ **Staf** | `staf@akademik.its.ac.id` | `password` | **Akses Terbatas:** Melihat dashboard agregat, create, update. **Tombol & Endpoint Hapus Data Dikunci (403)** | [`/quick-login/staf`](http://127.0.0.1:8000/quick-login/staf) |
| 🎓 **Mahasiswa** | `mahasiswa@student.its.ac.id` | `password` | **Akses Publik:** Dibatasi dari panel admin backoffice, diarahkan ke portal akademik publik | [`/quick-login/mahasiswa`](http://127.0.0.1:8000/quick-login/mahasiswa) |
| 👨‍🏫 **Dosen** | `dosen@akademik.its.ac.id` | `password` | **Akses Dosen:** Dashboard, kelola tugas, dan kelola nilai | [`/quick-login/dosen`](http://127.0.0.1:8000/quick-login/dosen) |

---

## 📋 Pemenuhan Spesifikasi Teknis Tugas (Aturan Wajib)

### 1. Integrasi UI Backoffice AdminLTE Layouting
- Memanfaatkan paket resmi `jeroennoten/laravel-adminlte` v4.
- Setiap tampilan view backoffice (`resources/views/admin/*`) mewarisi master layout `@extends('adminlte::page')` dengan `@section('content_header')` dan `@section('content')`.
- Konfigurasi terpusat di `config/adminlte.php`:
  - Pengaturan menu navigasi, sidebar dinamis, breadcrumbs, logo FTEIC ITS, user dropdown profile badge, dan dark mode switcher.

### 2. Sistem Autentikasi Terverifikasi via Laravel Breeze
- Seluruh rute backoffice (`/admin/*`) berada di bawah proteksi middleware otentikasi `auth`.
- Pengalihan cerdas pasca-login pada `AuthenticatedSessionController`:
  - Pengguna berhak akses internal (Admin / Staf) otomatis dialihkan ke `/admin/dashboard`.
  - Pengguna publik / non-admin otomatis dialihkan ke beranda publik (`/` atau `/beranda`).
- Pengguna yang mencoba membypass URL `/admin/dashboard` tanpa login akan dicegat dan diarahkan ke `/login`.

### 3. Otorisasi Dinamis Spatie Laravel Permission
- Hak akses user disimpan dinamis ke database fisik melalui 5 tabel relasional Spatie (`roles`, `permissions`, `model_has_roles`, `model_has_permissions`, `role_has_permissions`).
- Trait `HasRoles` terpasang pada model `App\Models\User`.
- Inisialisasi peran `'admin'` dan `'staf'` dilakukan via `RoleAndPermissionSeeder`.

### 4. Widget Dashboard Agregat Statistik Dinamis
- Menampilkan minimal 3 widget box statistik dinamis di beranda admin dari hasil eksekusi kueri agregasi model `count()`:
  1. **Total Mahasiswa:** `Mahasiswa::count()` (dilengkapi rincian Mahasiswa Aktif vs Cuti Akademik).
  2. **Total Staf Dosen:** `Dosen::count()` (kuantitas dosen wali FTEIC ITS).
  3. **Total Tugas Aktif:** `Tugas::where('status', 'Aktif')->count()` (tugas kuliah yang belum deadline).
  4. **Widget Ekstra:** Rata-rata IPK Mahasiswa FTEIC (`Mahasiswa::avg('ipk')`) dan Total Departemen (`Departemen::count()`).
- Dilengkapi tabel pemetaan distribusi mahasiswa/dosen per jurusan serta feed 5 penugasan kuliah terbaru.

### 5. Pengaman Controller & Hak Eksklusif Hapus Data
- **Pengamanan Visual (Blade Directive):** Tombol aksi 'Hapus Data' pada tabel Mahasiswa, Dosen, dan Tugas dibungkus dengan direktif Blade `@can('hapus-mahasiswa')`, `@can('hapus-dosen')`, dan `@can('hapus-tugas')`. Pada layar pengguna Staf, tombol hapus sama sekali tidak dirender di DOM.
- **Pengamanan Server-Side (Controller & Middleware):**
  - Menerapkan pengaman controller via interface `HasMiddleware` dengan `new Middleware('permission:hapus-...', only: ['destroy'])`.
  - Dilengkapi otorisasi berlapis `abort_unless(auth()->user()->can('hapus-...'), 403)` pada method `destroy()`.
  - Jika pengguna Staf mencoba memanipulasi request endpoint `DELETE` via Postman atau browser inspect, server langsung menolak dengan status HTTP **403 Forbidden**.

---

## 🌟 Tantangan Tambahan / Challenge (Nilai Plus A+)

1. **Filter Pencarian dan Kueri `when()`:**
   - Pencarian data pada controller (`MahasiswaController`, `DosenController`, `TugasController`) disusun bersih menggunakan klausa `when()` tanpa blok `if-else` bertingkat yang rawan spaghetti code.
   - Mendukung multi-kolom search (nama ATAU NRP/NIP) serta multi-variabel filter (dropdown departemen dan status aktif/cuti).
2. **Pengunci Link Pagination `withQueryString()`:**
   - Mempertahankan seluruh parameter filter pencarian saat pengguna berpindah halaman pagination tabel data menggunakan method chaining `->paginate(...)->withQueryString()`.
3. **Auto-Seeder Faker Realistis:**
   - Disediakan seeder otomatis `AcademicDataSeeder` yang langsung menyiram puluhan data dummy realistis: 5 departemen FTEIC ITS, 20 dosen pengajar dengan NIP & keahlian, 60+ mahasiswa dengan variasi IPK & angkatan 2021-2024, serta 25 tugas kuliah aktif.
4. **Auto-Login Test Accounts (1-Klik):**
   - Halaman login dilengkapi kartu panel pintas yang menyediakan tombol login otomatis untuk Admin, Staf, dan Mahasiswa.

---

## ⚙️ Panduan Setup & Instalasi Proyek

Pastikan sistem telah terpasang **PHP >= 8.3**, **Composer**, dan **Node.js (NPM)**.

### 1. Kloning Repositori
```bash
git clone <url-repositori-github>
cd "PBKK Week 4 Tugas"
```

### 2. Pasang Dependensi Backend (Composer)
```bash
composer install
```

### 3. Konfigurasi Environment & Application Key
```bash
cp .env.example .env
php artisan key:generate
```

### 4. Eksekusi Migrasi Database & Auto-Seeder
Perintah berikut akan membuat seluruh skema database (termasuk tabel Spatie Permission) dan mengisi data dummy realistis via Faker serta akun demo:
```bash
php artisan migrate:fresh --seed
```

### 5. Kompilasi Aset Frontend (Vite)
```bash
npm install
npm run build
```

### 6. Jalankan Web Server Lokal
```bash
php artisan serve
```
Buka browser pada alamat: **http://127.0.0.1:8000**
- Akses Portal Publik: `http://127.0.0.1:8000/`
- Akses Halaman Login: `http://127.0.0.1:8000/login`
- Akses Dashboard Admin: `http://127.0.0.1:8000/admin/dashboard`

---

## 🧪 Pengujian Otomatis (PHPUnit & Feature Tests)

Aplikasi dilengkapi suite pengujian otomatis komprehensif yang menguji otentikasi Breeze, proteksi middleware Spatie, kueri `when()`, penguncian paginasi, hak eksklusif hapus data, dan portal akademik:

```bash
# Menjalankan pengujian fitur Dashboard & RBAC
php artisan test --filter=AdminDashboardAndRBACTest

# Menjalankan seluruh test suite aplikasi
php artisan test
```

### Hasil Pengujian:
```text
   PASS  Tests\Feature\AdminDashboardAndRBACTest
  ✓ guest is redirected to login when accessing dashboard
  ✓ regular user without backoffice role is forbidden
  ✓ admin and staf can access admin dashboard
  ✓ spatie roles and permissions are properly configured
  ✓ dashboard renders aggregate count widgets
  ✓ admin can see delete button in blade and delete mahasiswa
  ✓ staf cannot see delete button in blade
  ✓ staf cannot delete mahasiswa via server request
  ✓ staf cannot delete dosen or tugas
  ✓ mahasiswa search filter with conditional when query
  ✓ pagination preserves query string with withquerystring
  ✓ quick login routes work for demo evaluation
  ✓ academic data seeder populates realistic faker data

  Tests:    45 passed (163 assertions)
  Duration: 2.66s
```
**Status: 100% PASS (Semua 45 pengujian berhasil lolos tanpa kegagalan).**

---

## 📁 Struktur Berkas Utama

```text
├── app/
│   ├── Http/Controllers/
│   │   ├── Admin/
│   │   │   ├── DashboardController.php   # Controller dashboard agregat statistik
│   │   │   ├── MahasiswaController.php   # CRUD Mahasiswa + when() + Spatie RBAC
│   │   │   ├── DosenController.php       # CRUD Dosen + when() + Spatie RBAC
│   │   │   └── TugasController.php       # CRUD Tugas + when() + constructor middleware
│   │   ├── Auth/
│   │   │   └── AuthenticatedSessionController.php # Login Breeze + pengalihan peran + Auto-Login
│   │   └── PageController.php            # Controller portal akademik publik
│   └── Models/
│       ├── Departemen.php                # Model Jurusan FTEIC ITS
│       ├── Dosen.php                     # Model Dosen Wali & Pengampu
│       ├── Mahasiswa.php                 # Model Mahasiswa FTEIC
│       ├── Tugas.php                     # Model Penugasan Kuliah
│       └── User.php                      # Model User dengan Trait HasRoles
├── config/
│   ├── adminlte.php                      # Konfigurasi master AdminLTE v4 (Menu, Sidebar, Logo)
│   └── permission.php                    # Konfigurasi Spatie Laravel Permission
├── database/
│   ├── migrations/                       # Skema tabel users, permissions, departemen, dosen, mhs, tugas
│   └── seeders/
│       ├── DatabaseSeeder.php            # Seeder utama (Akun Uji Coba)
│       ├── RoleAndPermissionSeeder.php   # Registrasi Peran Admin & Staf Spatie
│       └── AcademicDataSeeder.php        # Auto-Seeder Faker realistis (60+ mahasiswa, 20 dosen, 25 tugas)
├── resources/views/
│   ├── admin/
│   │   ├── dashboard.blade.php           # View Dashboard (@extends('adminlte::page'))
│   │   ├── mahasiswa/                    # Index (Filter when()), Create, Edit Mahasiswa
│   │   ├── dosen/                        # Index, Create, Edit Dosen
│   │   └── tugas/                        # Index, Create, Edit Tugas
│   ├── auth/
│   │   └── login.blade.php               # Halaman Login Breeze + Kartu Demo Auto-Login
│   └── layouts/ & partials/              # Layout Blade Portal Akademik Publik
├── routes/
│   ├── web.php                           # Definisi rute publik, auto-login, dan admin backoffice
│   └── auth.php                          # Rute autentikasi Laravel Breeze
└── tests/Feature/
    ├── AdminDashboardAndRBACTest.php     # Pengujian komprehensif RBAC, Dashboard & Filter
    ├── MultiViewAcademicTest.php         # Pengujian portal akademik publik
    └── Auth/                             # Pengujian autentikasi Breeze
```

---

&copy; 2026 Abdurrahman Arrafi Ravsan Zarnadi — Departemen Teknik Informatika, FTEIC, Institut Teknologi Sepuluh Nopember (ITS).
