# EPI Order & Sales System (EPI-OSS) – PROJECT RULES

## Purpose

- Aturan ini menjadi panduan teknis untuk seluruh pengembangan EPI Order & Sales System (EPI-OSS) untuk PT Emas Perak Indonesia (EPI) dan para distributor.
- TRAE (atau dev lain) **wajib** membaca dan memahami `CONTEXT.md` sebelum menulis atau mengubah kode.
- Tujuan: membangun sistem order management multi-distributor yang:
  - Aman (high security),
  - Stabil & scalable,
  - Ramah pengguna untuk tim EPI dan distributor,
  - Mudah diextend fitur-fiturnya.

---

## Tech Stack

- **Backend**: PHP 8.3+, Laravel 11.x
- **Database**: MySQL 8+ (InnoDB, utf8mb4_unicode_ci)
- **Cache & Queue**: Redis
- **Frontend**: Laravel Blade, HTML5, CSS3
  - Tailwind CSS (utility-first)
  - Alpine.js (interaktivitas ringan)
- **Auth**: Laravel Breeze atau Jetstream (stack Blade)
- **API & Token**: Laravel Sanctum
- **RBAC (Role-Based Access Control)**: spatie/laravel-permission
- **Logging & Monitoring**: Laravel default logging (`stack`), siap integrasi ke tool eksternal bila dibutuhkan.

---

## General Engineering Principles

1. **Readable first**  
   - Kode harus mudah dibaca dan dipahami oleh dev lain, bukan hanya “jalan”.
2. **Convention over configuration**  
   - Ikuti konvensi Laravel dan PSR-12 sebisa mungkin.
3. **Separation of concerns**  
   - Controller tipis, business logic di Service/Action class, akses data di Model/Repository.
4. **Single source of truth**  
   - Untuk aturan bisnis penting (misal perhitungan harga, status order), centralize di satu tempat (Service / domain class).
5. **Small steps, safe changes**  
   - Setiap perubahan besar harus dipecah menjadi langkah-langkah kecil dengan scope jelas.

---

## Security Guidelines

- **Credential & Secret**
  - Jangan pernah hard-code password, API key, dsb.
  - Gunakan `.env` untuk semua konfigurasi sensitif.
  - Jangan commit `.env` ke git.

- **Authentication & Session**
  - Gunakan guard default Laravel untuk web (`web` + CSRF).
  - Gunakan Sanctum untuk API/internal token bila dibutuhkan.

- **Authorization**
  - Semua route di belakang login harus memakai middleware `auth`.
  - Role & permission diatur via spatie/laravel-permission.
  - Policy Laravel untuk proteksi resource tertentu (mis. Distributor hanya mengakses data miliknya).

- **Input Validation**
  - Semua input dari form atau API wajib divalidasi dengan Form Request.
  - Jangan pernah percaya data dari client.

- **SQL & Data Access**
  - Gunakan Eloquent / Query Builder.
  - Raw SQL hanya bila benar-benar perlu, dengan binding parameter yang aman.
  - Tambahkan index pada kolom yang sering dipakai filter atau join.

- **CSRF & XSS**
  - Pastikan CSRF protection aktif di semua form.
  - Escape output di Blade secara default (`{{ }}`), hanya gunakan `{!! !!}` bila yakin aman.

- **Audit & Logging**
  - Log event penting: login gagal, perubahan role, perubahan status order, update harga, dll.
  - Siapkan tabel `audit_logs` untuk mencatat aktivitas bisnis sensitif.

---

## Architecture & Structure

- Struktur folder mengikuti standar Laravel:
  - `app/Models` – Model Eloquent.
  - `app/Http/Controllers` – Controller HTTP.
  - `app/Http/Requests` – Form Request untuk validasi.
  - `app/Services` – Business logic / service layer.
  - `app/Actions` – Action kecil reusable (opsional).
  - `database/migrations` – Migration database.
  - `resources/views` – Blade view.

- Organisasi Controller per domain (namespace):
  - `App\Http\Controllers\Admin` (Super Admin / HO)
  - `App\Http\Controllers\Distributor` (panel distributor)
  - `App\Http\Controllers\Auth`
  - `App\Http\Controllers\Api` (kalau ada API)

- **Multi-tenant secara logis**
  - Semua data yang spesifik distributor harus memiliki `distributor_id` (misalnya orders, store settings).
  - Super Admin bisa mengakses semua data.
  - User distributor hanya mengakses record dengan `distributor_id` miliknya.

---

## Frontend, UI & UX Rules

- **Layout dasar**
  - 1 layout utama `resources/views/layouts/app.blade.php`:
    - Sidebar navigasi kiri.
    - Topbar (profil, notifikasi singkat).
    - Area konten (card, tabel, form).
- **Tema warna**
  - Primary: biru cerah (untuk tombol utama, link penting, highlight).
  - Secondary: emas / kuning (badge status, angka KPI, accent).
  - Background: putih / abu muda yang kontras dengan teks hitam/abu gelap.

- **Style komponen**
  - Card dengan rounded corners dan shadow lembut.
  - Spacing konsisten (`p-4`, `p-6`).
  - Tabel dengan header tebal, zebra rows opsional, pagination jelas.
  - Status chip (badge) untuk status order/pembayaran/pengiriman dengan warna konsisten.

- **Responsiveness**
  - Desain mobile-first.
  - Sidebar bisa collapsible di layar kecil.
  - Tabel lebar harus mendukung horizontal scroll di mobile.

- **UX prinsip**
  - Alur kerja distributor:
    - “Masuk → lihat dashboard singkat → buat order → cek status order/pengiriman” harus terasa simpel.
  - Minimize klik berulang.
  - Notifikasi feedback jelas setiap action (toast / alert).

---

## Database & Migrations

- Gunakan `bigIncrements`/`unsignedBigInteger` untuk primary & foreign key.
- Semua relasi penting:
  - `distributor_id` di tabel yang perlu.
  - Foreign key dengan `on delete` yang logis (umumnya `restrict` atau `cascade`).
- Gunakan soft delete (`SoftDeletes`) untuk data penting:
  - Users, Distributors, Products, Price Lists, Orders.
- Kolom standar:
  - `created_at`, `updated_at` di semua tabel.
  - `created_by`, `updated_by` di tabel bisnis (bila relevan).
- Status:
  - Simpan status sebagai string/enum dengan konstanta di Model (mis. `Order::STATUS_DRAFT`, dll).

---

## Testing & Quality

- Gunakan PHPUnit dan test bawaan Laravel.
- Minimal test untuk:
  - Auth (login, proteksi route).
  - Pembuatan order oleh user distributor.
  - Pergantian status order + efek (misal ke stock / log).
- Buat Factory untuk model utama (User, Distributor, Product, Order, dsb.).
- Hindari men-silence exception. Lebih baik biarkan error jelas saat dev.

---

## TRAE Workflow

Sebelum TRAE membuat perubahan modul besar:

1. Baca ulang `PROJECT_RULES.md` & `CONTEXT.md`.
2. Rangkum task yang diminta user dalam beberapa bullet.
3. Sebutkan file apa saja yang akan diubah/dibuat:
   - Migration
   - Model
   - Controller
   - View
   - Route
   - Test
4. Kerjakan perubahan bertahap, commit-friendly.

Saat membuat **modul baru**, TRAE harus:

1. Mendefinisikan schema di migration.
2. Membuat Model + relasi antar Model.
3. Membuat Form Request untuk validasi input.
4. Membuat Controller + route (web/api).
5. Membuat Blade view (index, create/edit, show bila perlu).
6. Menambahkan minimal 1–2 unit/feature test.

---

## Non-Functional Requirements

- **Timezone**: default `Asia/Jakarta`.
- **Currency**: default `IDR` (Rupiah).
- **Performance**
  - Gunakan pagination di semua listing.
  - Hindari N+1 query (pakai `with()` untuk eager loading).
  - Index di kolom filter utama (status, tanggal, distributor_id, product_id).
- **Scalability**
  - Struktur data dan modul harus memudahkan penambahan fitur baru (promo, API ke marketplace, dsb.)
- **Auditability**
  - Aktivitas penting tercatat di `audit_logs`.
  - Perubahan konfigurasi harga terpusat harus mudah ditelusuri.