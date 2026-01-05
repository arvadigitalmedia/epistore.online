# EPI Order & Sales System (EPI-OSS) – CONTEXT

## 1. Business Overview

EPI-OSS adalah aplikasi web internal yang dipakai PT Emas Perak Indonesia (EPI) untuk mengelola:

- Jaringan **Distributor** pemasar brand emas-perak EPI.
- Daftar produk emas-perak dengan harga terpusat yang dikontrol Super Admin.
- Mini “store” untuk masing-masing Distributor agar bisa menerima dan mengelola order hingga informasi pengiriman.

Konteks bisnis:

- Super Admin (EPI) memiliki kontrol penuh:
  - Manajemen user & distributor.
  - Manajemen brand & produk.
  - Manajemen harga terpusat (price list).
- Distributor berperan sebagai:
  - Penerima order (dari agen, reseller, atau end-customer sesuai model bisnis).
  - Pengelola status order, pembayaran (tracking), dan pengiriman.

Sistem harus fleksibel dan mudah dikembangkan, mirip platform:

- Seller Center marketplace,
- B2B order management,
- Mini-ERP untuk logam mulia.

---

## 2. User Types & Roles

### 2.1 Super Admin (EPI Head Office)

- Mengelola semua user (HO & distributor).
- Mengelola brand & produk.
- Menetapkan harga terpusat (price list).
- Menetapkan aturan diskon global (jika ada).
- Melihat semua order dan laporan lintas distributor.
- Mengelola konfigurasi sistem (logo, warna, parameter pajak, dsb.).

### 2.2 EPI Staff (Ops / Sales Admin)

- Membantu Super Admin mengelola data master (product, price list, distributor).
- Memantau dan mengaudit order distributor.
- Menjalankan tugas administratif: export data, verifikasi hal tertentu jika dibutuhkan.

### 2.3 Distributor Owner / Admin Distributor

- Mengelola **store** miliknya:
  - Profil store (nama toko, logo, alamat, kontak).
  - Pengaturan alamat pengiriman utama.
- Mengelola user internal distributor (staff).
- Mengakses daftar produk yang disediakan Super Admin (harga terpusat).
- Mengatur visibilitas produk di store (aktif/non-aktif, highlight, urutan).
- Menerima order, mengubah status order, memasukkan informasi pengiriman.

### 2.4 Distributor Staff (Sales / CS)

- Mengelola order harian:
  - Membuat order manual (jika perlu).
  - Meng-update status order.
  - Menginput nomor resi & kurir.
- Melihat riwayat order dan pelanggan (jika modul pelanggan diaktifkan).

### 2.5 Customer / End User (Opsional)

- Jika di masa depan store ingin semi-publik:
  - Browsing katalog produk distributor tertentu.
  - Membuat order online (checkout sederhana).
  - Melihat status order.

Awal implementasi bisa berbasis internal saja (tanpa customer login), namun struktur dibuat siap jika modul customer diaktifkan.

---

## 3. Core Features & Modules

### 3.1 Authentication & User Management

- Login / logout / reset password.
- Role & permission:
  - `super_admin`, `epi_staff`, `distributor_owner`, `distributor_staff`, `customer` (opsional).
- Super Admin:
  - Tambah / edit / non-aktifkan user.
  - Assign user ke distributor tertentu (untuk role distributor).

### 3.2 Brand & Product Management (Super Admin)

- Brand:
  - Contoh: Silvergram, Goldgram, dsb.
  - Kolom: `id`, `code`, `name`, `description`, `active`.
- Product:
  - Terpusat di level EPI, bukan di distributor.
  - Kolom contoh:
    - `id`, `sku`, `name`, `brand_id`
    - `metal_type` (gold, silver, dll.)
    - `purity` (misal 999.9)
    - `weight_gram`
    - `unit` (gram)
    - `description`
    - `is_active`
- Opsional:
  - Tag / kategori produk (misal: reguler, limited edition, collectible).

### 3.3 Central Price List (Harga Terpusat)

- Super Admin menentukan harga resmi:
  - Tabel `price_lists`:
    - `id`, `name`, `description`, `valid_from`, `valid_to`, `is_active`.
  - Tabel `price_list_items`:
    - `id`, `price_list_id`, `product_id`, `base_price`, `currency` (IDR).
- Mendukung:
  - Beberapa price list (misal tipe distributor berbeda).
  - Price list yang di-assign ke distributor tertentu (opsional).
- Distributor tidak boleh mengubah base price, namun:
  - Bisa mengatur markup tambahan per distributor (opsional, misal `extra_fee`).

### 3.4 Distributor & Store Settings

- Distributor:
  - `id`, `code`, `name`, `legal_name`
  - `type` (epi_store, epi_channel, silver_channel, dsb.)
  - `email`, `phone`, `address`, `city`, `province`, `postal_code`, `country`
  - `status` (active, suspended)
- Store Settings (per distributor):
  - Nama tampilan store.
  - Logo store.
  - Alamat pengiriman default.
  - Template catatan di halaman checkout / invoice.
  - Pengaturan kurir/pengiriman yang digunakan (manual / list kurir).

### 3.5 Product Catalog per Distributor

- Distributor melihat daftar produk global:
  - Bisa mengaktifkan atau menonaktifkan produk tertentu di store-nya.
  - Bisa mengatur label (mis. “produk unggulan”).
- Harga yang tampil:
  - Menggunakan price list terpusat + aturan markup distributor (jika diaktifkan).
- Untuk user distributor:
  - View katalog internal (untuk input order/manual).
- Untuk customer (opsional):
  - View katalog publik distributor tertentu.

### 3.6 Order Management

**Order dibuat di level Distributor:**

- Header:
  - `id`, `order_number`
  - `distributor_id`
  - `customer_id` (opsional, jika modul customer diaktifkan) atau data customer inline (nama, telp, alamat).
  - `status` (lihat lifecycle di bawah)
  - `order_date`
  - `grand_total`, `subtotal`, `discount_total`, `shipping_cost`, `tax_total` (opsional)
  - `payment_status` (unpaid, partially_paid, paid)
  - `shipping_status` (not_shipped, shipped, delivered, returned)
  - `notes`, `internal_notes`
- Item:
  - `id`, `order_id`, `product_id`
  - `quantity`
  - `unit_price`
  - `discount_amount` / `discount_percent` (opsional)
  - `line_total`

**Status lifecycle (contoh baseline):**

- `draft` → `pending_confirmation` → `confirmed` → `processing` → `shipped` → `completed`
- `cancelled`:
  - Dapat terjadi dari `draft` atau `pending_confirmation` dengan alasan terisi.

Setiap perubahan status harus:

- Dicatat di log status:
  - `from_status`, `to_status`, `changed_by`, `changed_at`, `reason`.

### 3.7 Shipping & Delivery Information

- Pengiriman dicatat pada tabel `shipments`:
  - `id`, `order_id`, `courier_name`, `tracking_number`
  - `ship_date`
  - `delivery_estimate` (opsional)
  - `delivery_date` (saat diterima)
  - `status` (prepared, shipped, delivered, returned)
  - `shipping_cost`
- Informasi pengiriman tampil jelas pada:
  - Detail order di panel distributor.
  - (Opsional) halaman tracking order untuk customer.

### 3.8 Payment Tracking (Level Distributor)

- Minimal versi awal:
  - Distributor mencatat metode pembayaran & status secara manual:
    - `payment_method` (transfer, cash, VA, dll.)
    - `payment_date`
    - `amount_paid`
    - `payment_status` (unpaid, partially_paid, paid)
- Bisa ditingkatkan:
  - Multi-payment record per order (partial payment).
  - Upload bukti pembayaran (bukti transfer).

### 3.9 Reporting & Dashboard

**Dashboard Super Admin:**

- Ringkasan:
  - Total distributor aktif.
  - Total orders hari ini / bulan ini.
  - Nilai transaksi per periode.
  - Top distributor by sales.
  - Top produk by sales.

**Dashboard Distributor:**

- Ringkasan:
  - Jumlah order hari ini / bulan ini.
  - Total omzet periode berjalan.
  - Order per status (draft, pending, processing, shipped, completed).
  - Produk terlaris.

**Laporan:**

- Orders:
  - Filter: tanggal, distributor, status, brand, produk.
- Penjualan:
  - Ringkasan omzet per hari/bulan.
- Pelanggan (jika modul customer diaktifkan):
  - Total pelanggan, frekuensi order.
- Export:
  - CSV/Excel untuk analisis lanjutan.

### 3.10 Notifications & Communication (Recommended Feature)

- Notifikasi internal:
  - Saat ada order baru.
  - Saat status order berubah.
- Channel:

  - Email notifikasi (basic).
  - Web notification (toast / badge).
  - Hook untuk integrasi WA di masa depan (webhook / API call).

---

## 4. Optional / Future Features (Recommended)

Beberapa fitur yang direkomendasikan untuk skala berikutnya (mirip model bisnis platform order management modern):

1. **Promo & Voucher**
   - Voucher diskon per distributor.
   - Diskon by produk atau by basket (misal min. nilai transaksi).

2. **Customer Module**
   - Customer registration & login (untuk store).
   - Riwayat order dan status.

3. **Multi-warehouse (per Distributor)**
   - Jika satu distributor punya lebih dari satu lokasi stok.

4. **Inventory Management (Basic)**
   - Track stok per produk per distributor.
   - Penyesuaian stok manual.

5. **API Integration**
   - API untuk sinkronisasi order dengan sistem lain (misal akuntansi atau marketplace eksternal).

6. **Sales Channel Expansion**
   - Support order yang datang dari kanal lain (offline, marketplace) tapi dicatat di EPI-OSS sebagai “single source of truth”.

---

## 5. Navigation / Sitemap (High-level)

### 5.1 Super Admin / HO Panel

- **Dashboard**
- **Users & Roles**
  - Users
  - Roles & Permissions
- **Brands & Products**
  - Brands
  - Products
- **Pricing**
  - Price Lists
  - Price List Items
- **Distributors**
  - Distributor List
  - Store Settings (per distributor)
- **Global Orders View**
  - All Orders (filter by distributor)
- **Reports**
  - Sales Summary
  - Orders Summary
- **System Settings**
  - General (logo, nama sistem)
  - Tax & Currency

### 5.2 Distributor Panel

- **Dashboard**
- **Catalog**
  - Product List (view dari EPI + setting visibilitas)
- **Orders**
  - Orders List
  - Order Detail
  - Create Order (manual)
- **Shipments**
  - Shipment List
- **Customers** (opsional)
  - Customer List
- **Reports**
  - Sales (distributor only)
- **Store Settings**
  - Store Profile
  - Payment Instructions (text bebas)
  - Shipping Options

---

## 6. Data Model Summary (High Level)

Tabel utama (nama bisa dimodifikasi saat implementasi, tapi makna harus sama):

- `users`
- `roles`, `permissions`, `model_has_roles`, `model_has_permissions`, `role_has_permissions`
- `distributors`
- `store_settings` (per distributor)
- `brands`
- `products`
- `price_lists`
- `price_list_items`
- `orders`
- `order_items`
- `shipments`
- `customers` (opsional)
- `payments` (opsional terpisah)
- `audit_logs`

Relasi penting:

- `users` → `distributors` (nullable; user HO tidak punya distributor_id).
- `orders.distributor_id` → `distributors.id`.
- `orders.customer_id` → `customers.id` (opsional).
- `order_items.product_id` → `products.id`.
- `products.brand_id` → `brands.id`.
- `price_list_items.product_id` → `products.id`.
- `price_list_items.price_list_id` → `price_lists.id`.

---

## 7. UX Guidelines (Khusus Tampilan Futuristik)

- Warna:
  - Dominan biru cerah (primary).
  - Aksen emas / kuning untuk highlight dan tombol utama.
- Style visual:
  - Clean dan modern, card-based.
  - Icon minimalis (misalnya dari heroicons atau lucide).
- Alur:
  - Super Admin:
    - “Login → Dashboard HO → Manage Distributor → Manage Product & Price → Lihat Order Consolidated.”
  - Distributor:
    - “Login → Dashboard → Cek order baru → Update status & pengiriman.”
- Copywriting UI:
  - Bahasa Indonesia yang jelas, tegas, dan tidak terlalu teknis.
  - Status dan pesan error/sukses harus eksplisit dan mudah dipahami.

