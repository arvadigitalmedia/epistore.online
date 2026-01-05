# Dokumentasi Lokalisasi EPI-OSS

## Ikhtisar
EPI-OSS menggunakan fitur lokalisasi bawaan Laravel untuk mendukung Multi-bahasa. Saat ini, fokus utama adalah Bahasa Indonesia (`id`) dengan fallback ke Bahasa Inggris (`en`).

## Konfigurasi
- **Locale Default**: `id` (Diatur di `.env` `APP_LOCALE` dan `config/app.php`)
- **Locale Fallback**: `en`
- **File Bahasa**:
  - `lang/id.json`: Terjemahan string JSON (Key = Bahasa Inggris/Identifier, Value = Bahasa Indonesia)
  - `lang/en.json`: (Opsional) Jika diperlukan override bahasa Inggris.

## Cara Menggunakan
Gunakan helper `__('Key')` di file Blade atau PHP.

### Contoh di Blade:
```blade
<!-- Sebelum -->
<span>Dashboard</span>

<!-- Sesudah -->
<span>{{ __('Dashboard') }}</span>
```

### Contoh di PHP:
```php
// Sebelum
return redirect()->back()->with('success', 'Product added!');

// Sesudah
return redirect()->back()->with('success', __('Product added!'));
```

## Menambahkan Terjemahan Baru
1. Tambahkan key baru di file Blade/PHP menggunakan `__('Key Baru')`.
2. Buka file `lang/id.json`.
3. Tambahkan entri baru:
   ```json
   "Key Baru": "Terjemahan Bahasa Indonesia"
   ```

## Daftar File yang Diperbarui (Januari 2026)
Berikut adalah file-file utama yang telah disesuaikan untuk lokalisasi:
- `config/app.php` (Set locale)
- `lang/id.json` (File master terjemahan)
- `resources/views/layouts/sidebar.blade.php` (Menu Admin/Distributor)
- `resources/views/components/cart-sidebar.blade.php` (Sidebar Keranjang)
- `resources/views/shop/index.blade.php` (Halaman Toko)
- `resources/views/shop/show.blade.php` (Halaman Detail Produk)
- `resources/views/welcome.blade.php` (Halaman Depan)
- `resources/views/auth/*.blade.php` (Halaman Login/Register)
- `resources/views/layouts/guest.blade.php` (Layout Guest)

## Mekanisme Fallback
Jika terjemahan tidak ditemukan di `lang/id.json`, sistem akan otomatis menampilkan key aslinya (biasanya Bahasa Inggris). Ini memastikan UI tidak rusak meskipun terjemahan belum lengkap.
