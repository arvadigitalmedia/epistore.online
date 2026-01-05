# Dokumentasi Sistem Pengiriman (Shipping System)

## Ikhtisar

Sistem pengiriman EPI-OSS dirancang untuk fleksibilitas tinggi, mendukung multi-distributor dengan lokasi asal yang berbeda-beda. Sistem ini menggunakan arsitektur service-based untuk memastikan integrasi yang mudah dengan modul checkout dan API eksternal (RajaOngkir).

## Arsitektur

### Komponen Utama

1.  **DistributorShippingService** (`App\Services\DistributorShippingService`)
    *   **Peran**: Service utama yang merangkum logika bisnis pengiriman.
    *   **Fungsi**:
        *   `getSettings(Distributor $distributor)`: Mengambil konfigurasi gabungan (DB + JSON).
        *   `updateSettings(Distributor $distributor, array $data)`: Menyimpan pengaturan lokasi dan preferensi.
        *   `calculateShipping(...)`: Menghitung ongkir + margin keuntungan distributor.

2.  **RajaOngkirService** (`App\Services\RajaOngkirService`)
    *   **Peran**: Wrapper untuk API pihak ketiga.
    *   **Fungsi**: Caching data wilayah (Provinsi, Kota, Kecamatan) dan request biaya ke API.

3.  **ShippingController** (`App\Http\Controllers\Distributor\ShippingController`)
    *   **Peran**: Menangani request HTTP dari UI Distributor.
    *   **Fungsi**: Validasi input form dan memanggil `DistributorShippingService`.

### Struktur Data

*   **Tabel `distributors`**: Menyimpan lokasi utama (`province_id`, `city_id`, `district_id`) untuk query cepat dan relasi.
*   **Kolom `config` (JSON)**: Menyimpan preferensi tambahan seperti `margin` biaya, `couriers` yang aktif, dan `default_weight`.
*   **Tabel `shipping_calculation_logs`**: Mencatat setiap kalkulasi ongkir untuk audit dan analisis margin.

---

## Panduan Integrasi (Checkout)

Untuk menggunakan sistem pengiriman pada halaman Checkout pelanggan, gunakan `DistributorShippingService`.

### Contoh Penggunaan di Controller Checkout

```php
use App\Services\DistributorShippingService;

class CheckoutController extends Controller 
{
    protected $shippingService;

    public function __construct(DistributorShippingService $shippingService)
    {
        $this->shippingService = $shippingService;
    }

    public function checkOngkir(Request $request)
    {
        $cart = $request->user()->cart; // Asumsi ada cart
        $distributor = $cart->distributor; // Ambil distributor pemilik produk
        
        try {
            $costs = $this->shippingService->calculateShipping(
                $distributor,
                $request->destination_city_id,
                $request->destination_district_id, // Opsional
                $cart->total_weight,
                $request->courier // 'jne', 'pos', dll
            );
            
            return response()->json($costs);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 422);
        }
    }
}
```

## Fitur Utama

1.  **Multi-Origin**: Setiap distributor memiliki titik asal pengiriman sendiri.
2.  **Margin Otomatis**: Distributor dapat mengatur margin (mark-up) biaya kirim untuk keuntungan tambahan atau biaya packing.
3.  **Persistensi Data**: Pengaturan lokasi disimpan di kolom database terindeks untuk performa, sementara preferensi dinamis di JSON.
4.  **Logging**: Semua cek ongkir tercatat, memungkinkan Super Admin memantau aktivitas pengiriman.

## Maintenance & Reset

Jika diperlukan reset total konfigurasi (misal: migrasi besar atau error data masif), gunakan command:

```bash
php artisan epi:reset-shipping --force
```

**PERINGATAN**: Command ini akan menghapus semua konfigurasi lokasi dan log pengiriman distributor. Backup otomatis akan dibuat di `storage/app/backups/`.
