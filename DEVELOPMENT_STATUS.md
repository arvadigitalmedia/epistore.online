# EPI-OSS Development Status Report
**Tanggal Laporan:** 03 Januari 2026
**Versi Dokumen:** 1.0.0

Dokumen ini merangkum status pengembangan terkini, fitur yang telah diimplementasikan, aspek teknis, dan rencana pengembangan selanjutnya untuk **EPI Order & Sales System (EPI-OSS)**.

---

## 1. Fitur dan Modul Terimplementasi

Berikut adalah komponen fungsional yang telah tersedia di sistem (Stable/Beta):

### A. Core System & Authentication
- **Multi-Guard Auth:** Login/Register berbasis Laravel Breeze.
- **Role-Based Access Control (RBAC):** Menggunakan `spatie/laravel-permission` dengan role utama: `super_admin`, `distributor`, `member` (customer).
- **Multi-Tenant Structure:** Database schema mendukung pemisahan data antar distributor (`distributor_id`).

### B. Modul Super Admin
- **User Management:** CRUD User dengan assign role.
- **Distributor Management:** Kelola data distributor, logo, dan level.
- **Master Data Produk & Brand:**
  - CRUD Brand & Product.
  - **Import/Export Excel:** Fitur bulk upload untuk Brand dan Product (via `maatwebsite/excel`).
- **Central Price Management:**
  - Manajemen harga terpusat.
  - Riwayat perubahan harga (`PriceHistory`).
  - Approval flow untuk perubahan harga (Pending/Approve/Reject).

### C. Modul Distributor
- **Dashboard Distributor:** Ringkasan performa toko.
- **Order Management:** Melihat daftar pesanan masuk, detail order, dan update status pengiriman.
- **Shipping Settings:** Konfigurasi asal pengiriman (Origin City) terintegrasi RajaOngkir.
- **Shipping Calculator:** Preview ongkos kirim real-time.

### D. Modul E-Commerce (Customer/Storefront)
- **Katalog Produk (Shop):** Listing produk dengan filter harga member.
- **Shopping Cart:** Tambah/hapus item, update quantity.
- **Checkout System:**
  - Alur checkout lengkap (Address -> Shipping -> Summary).
  - Integrasi API RajaOngkir untuk pemilihan Kota/Kecamatan.
  - Layout responsif 60:40 (Cart Items vs Shipping Form).
- **Order History:** Pelanggan dapat melihat riwayat pesanan mereka.
- **Member Upgrade:** Fitur pengajuan upgrade status member.

---

## 2. Tech Stack & Environment

### Backend
- **Framework:** Laravel 11.x (compatible with 12.0 dev dependencies)
- **Language:** PHP 8.3+
- **Database:** MySQL 8.0 (InnoDB, utf8mb4)
- **Dependencies Utama:**
  - `spatie/laravel-permission`: Manajemen Hak Akses
  - `maatwebsite/excel`: Import/Export Data
  - `guzzlehttp/guzzle`: HTTP Client (RajaOngkir)

### Frontend
- **Stack:** Blade Templates + Tailwind CSS v3.x
- **Interactivity:** Alpine.js v3.x
- **Build Tool:** Vite + PostCSS

### Services
- **Shipping API:** RajaOngkir (Pro/Starter) integration via `RajaOngkirService`.
- **Cache/Queue:** Redis (configured via `.env`).

---

## 3. Catatan Perubahan (Changelog) & Updates

### Versi 0.5.0 - Current State (Jan 2026)
**New Features:**
- Implementasi layout Checkout 60:40 yang responsif.
- Penambahan kolom alamat (`address`, `province_id`, `city_id`, `district_id`) di tabel Users.
- Integrasi dinamis dropdown wilayah (Provinsi -> Kota -> Kecamatan) di halaman Checkout.

**Bug Fixes:**
- [Fixed] Warna tombol "PLACE ORDER" yang sebelumnya putih (invisible) menjadi Indigo-600.
- [Fixed] Error `Undefined method` pada `CheckoutController` dan `RajaOngkirService` (Type hinting added).
- [Fixed] Isu "Site can't be reached" pada environment local development.
- [Fixed] `DistributorDeletionTest` error properti `$admin` yang tidak terdefinisi.

**Improvements:**
- Validasi ketat pada Import Excel (Brands/Products).
- Optimasi query N+1 pada listing produk dan order.

---

## 4. Daftar Pekerjaan (WIP & Gaps)

### Work In Progress (WIP)
- **Status:** *Payment Gateway Integration* (Belum ada controller pembayaran real, saat ini manual/COD flow).
- **Status:** *Inventory Management* (Schema dasar produk ada, tapi tabel stok per distributor/warehouse belum diimplementasi penuh).

### Known Issues / Bugs
- **Environment Test:** Menjalankan PHPUnit (`php artisan test`) membutuhkan konfigurasi driver database testing (SQLite/MySQL) yang konsisten di `.env.testing`.

### Gaps (Kebutuhan Bisnis vs Implementasi)
1.  **Inventory Tracking:** Belum ada sistem potong stok otomatis saat order terjadi.
2.  **Payment Gateway:** Belum terintegrasi dengan Midtrans/Xendit.
3.  **API Mobile:** File `routes/api.php` belum tersedia/dikonfigurasi untuk aplikasi mobile.
4.  **Reporting:** Laporan penjualan detail (PDF/Excel) belum tersedia, hanya dashboard view.

---

## 5. Dokumentasi Teknis Ringkas

### Arsitektur Sistem
Menggunakan pola **MVC (Model-View-Controller)** standar Laravel dengan tambahan layer:
- **Services:** (`App\Services`) Untuk logika bisnis eksternal (mis: `RajaOngkirService`).
- **Imports:** (`App\Imports`) Untuk logika manipulasi file Excel.
- **View Components:** Blade components (`x-input`, `x-primary-button`) untuk UI konsisten.

### Alur Data Checkout
1.  User mengisi Cart (`carts`, `cart_items`).
2.  User masuk Checkout -> Load `RajaOngkirService` untuk data wilayah.
3.  User Submit -> `CheckoutController@store`:
    - Validasi Stock (To-do).
    - Buat `Order` & `OrderItem`.
    - Kurangi Stock (To-do).
    - Hapus Cart.
    - Redirect ke Payment/Success.

### Petunjuk Setup Development
1.  **Clone Repository.**
2.  **Install Dependencies:**
    ```bash
    composer install
    npm install
    ```
3.  **Environment Setup:**
    - Copy `.env.example` ke `.env`.
    - Set DB Credentials & `RAJAONGKIR_API_KEY`.
    ```bash
    php artisan key:generate
    php artisan migrate --seed
    ```
4.  **Run Application:**
    ```bash
    npm run dev
    php artisan serve
    ```

---

## 6. Rekomendasi & Roadmap

### Jangka Pendek (Next Sprint)
1.  **Inventory System:** Buat tabel `product_stocks` (distributor_id, product_id, quantity) dan logika pengurangan stok.
2.  **Payment Gateway:** Integrasi Midtrans Snap untuk pembayaran otomatis.
3.  **Order Notifications:** Email/WhatsApp notifikasi ke Distributor saat ada order baru.

### Jangka Panjang
1.  **API Development:** Membangun REST API (`routes/api.php`) untuk mendukung Mobile Apps (Flutter/React Native).
2.  **Advanced Reporting:** Modul laporan keuangan dan performa sales distributor yang bisa diexport.
3.  **Promo & Voucher:** Sistem diskon kode voucher.

---

**Kontak Tim Pengembang:**
- **Lead Architect:** Arva (AI Assistant)
- **Backend/Frontend:** Tim Developer EPI

*Dokumen ini diperbarui terakhir pada 03 Januari 2026.*
