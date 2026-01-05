# Panduan Deployment EPI-OSS (cPanel Shared Hosting)

Dokumen ini berisi panduan lengkap untuk melakukan deployment sistem **EPI-OSS** ke lingkungan **cPanel Shared Hosting** (Linux/CloudLinux).

---

## 1. Persiapan Awal

### A. Strategi Branching (Git)
Tetap gunakan strategi dua branch utama di GitHub:
1.  **`main`**: Untuk **Production**.
2.  **`develop`**: Untuk **Development/Staging**.

### B. Persiapan cPanel (Server Environment)

Anda tidak perlu menginstal software via terminal (`apt install`) karena sudah disediakan oleh hosting. Lakukan konfigurasi berikut melalui dashboard cPanel:

1.  **Set Versi PHP**:
    *   Cari menu **MultiPHP Manager** atau **Select PHP Version**.
    *   Pilih domain/subdomain yang akan digunakan.
    *   Set versi PHP ke **PHP 8.3**.
    *   Pastikan ekstensi berikut dicentang/aktif: `mbstring`, `xml`, `bcmath`, `mysql/mysqli`, `pdo`, `pdo_mysql`, `intl`, `gd`, `zip`, `fileinfo`.

2.  **Database MySQL**:
    *   Masuk menu **MySQL® Database Wizard**.
    *   Buat Database baru (misal: `username_epi_oss`).
    *   Buat User Database baru dan password kuat.
    *   Berikan hak akses **ALL PRIVILEGES** user ke database tersebut.
    *   Catat nama database, user, dan password untuk file `.env`.

3.  **Akses SSH (Terminal)**:
    *   Masuk menu **SSH Access** -> **Manage SSH Keys**.
    *   Generate New Key (atau Import Public Key dari PC Anda).
    *   Setelah generate, klik **Manage** pada key tersebut dan pilih **Authorize**.
    *   Catat username cPanel dan IP Address server.

---

## 2. Konfigurasi GitHub

### A. Repository Secrets
Masuk ke repository GitHub -> **Settings** -> **Secrets and variables** -> **Actions**.
Update secrets sesuai credential cPanel:

| Secret Name | Deskripsi |
| :--- | :--- |
| `SSH_HOST` | IP Address Shared Hosting (`160.187.143.242`) |
| `SSH_USER` | Username cPanel Anda |
| `SSH_KEY` | Private Key SSH (bisa generate di cPanel lalu download private key-nya, atau pakai key local PC yang public key-nya sudah diupload ke cPanel) |
| `SSH_PORT` | Port SSH (biasanya `21098` atau `22`, cek info hosting) |

### B. GitHub Actions (CI/CD)
Workflow akan melakukan remote command via SSH user cPanel untuk melakukan `git pull`.

---

## 3. Setup Aplikasi di cPanel

### A. Struktur Folder yang Disarankan
Demi keamanan, **jangan** taruh source code project langsung di dalam `public_html`.
Struktur yang disarankan:

```text
/home/username/
├── repositories/          <-- Folder baru untuk menyimpan repo git
│   ├── epi-oss-dev/       <-- Clone branch develop
│   └── epi-oss-prod/      <-- Clone branch main
├── public_html/           <-- Folder akses web Production
├── dev.domain.com/        <-- Folder akses web Development (Subdomain)
```

### B. Clone Repository Pertama Kali
Buka **Terminal** di cPanel (atau SSH dari lokal):

```bash
# 1. Buat folder repositories
mkdir -p ~/repositories
cd ~/repositories

# 2. Setup Git (jika belum)
git config --global user.email "email@anda.com"
git config --global user.name "Nama Anda"

# 3. Clone Repository (Gunakan token HTTPS atau SSH Key GitHub)
# Clone untuk Dev
git clone -b develop https://github.com/username/epi-oss.git epi-oss-dev

# Clone untuk Prod
git clone -b main https://github.com/username/epi-oss.git epi-oss-prod
```

### C. Instalasi Dependensi & Setup Awal
Lakukan langkah ini untuk folder `epi-oss-dev` dan `epi-oss-prod`.

```bash
cd ~/repositories/epi-oss-dev

# Copy .env
cp .env.example .env

# Edit .env sesuai database cPanel
nano .env
# (Isi DB_DATABASE, DB_USERNAME, DB_PASSWORD, APP_URL, dll)

# Install Dependencies
# Gunakan path penuh php jika perlu, misal /opt/cpanel/ea-php83/root/usr/bin/php
/usr/bin/php83 /usr/local/bin/composer install --no-dev --optimize-autoloader

# Generate Key
/usr/bin/php83 artisan key:generate

# Migrate Database
/usr/bin/php83 artisan migrate --force

# Link Storage
/usr/bin/php83 artisan storage:link
```

*Catatan: Sesuaikan `/usr/bin/php83` dengan binary PHP 8.3 di server Anda. Bisa dicek dengan perintah `which php` atau tanya support hosting path lengkapnya.*

### D. Menghubungkan ke Public Access (Symlink)
Agar website bisa diakses, kita harus menghubungkan folder `public` Laravel ke folder domain cPanel.

**Untuk Production (Main Domain `public_html`):**
```bash
# Backup folder public_html lama jika ada
mv ~/public_html ~/public_html_backup

# Buat symlink dari folder public project ke public_html
ln -s ~/repositories/epi-oss-prod/public ~/public_html
```

**Untuk Development (Subdomain `dev.domain.com`):**
Pastikan subdomain sudah dibuat di cPanel dengan Document Root misal `/home/username/dev.domain.com`.
```bash
# Hapus folder document root default subdomain (hati-hati isinya kosongkan dulu)
rm -rf ~/dev.domain.com

# Buat symlink
ln -s ~/repositories/epi-oss-dev/public ~/dev.domain.com
```

### E. Konfigurasi .htaccess (PENTING)
Pastikan file `.htaccess` di dalam folder `public` project Laravel sudah bawaan Laravel. Apache cPanel akan otomatis membacanya.

---

## 4. Automation (Cron Job)

Di shared hosting, kita tidak bisa menggunakan PM2/Supervisor secara persisten (daemon). Solusinya adalah menggunakan **Cron Job** cPanel.

Masuk menu **Cron Jobs** di cPanel.

**1. Laravel Scheduler (Wajib)**
Jalankan setiap menit (`* * * * *`).
```bash
cd /home/username/repositories/epi-oss-prod && /usr/bin/php83 artisan schedule:run >> /dev/null 2>&1
```

**2. Queue Worker (Workaround Shared Hosting)**
Karena `queue:work` adalah long-running process yang bisa dibunuh oleh CloudLinux, gunakan flag `--stop-when-empty` dan jalankan tiap menit via Cron.
```bash
cd /home/username/repositories/epi-oss-prod && /usr/bin/php83 artisan queue:work --stop-when-empty --tries=3 >> /dev/null 2>&1
```
*(Ulangi setup cron yang sama untuk folder `epi-oss-dev` jika development butuh queue).*

---

## 5. Alur Deployment Otomatis (GitHub Actions)

Workflow GitHub Actions akan melakukan remote SSH ke cPanel untuk menjalankan perintah update.

**Alur Development:**
1.  Push ke branch `develop`.
2.  GitHub Actions SSH ke server.
3.  Masuk folder `~/repositories/epi-oss-dev`.
4.  `git pull`.
5.  `composer install`, `migrate`, `optimize`.

**Alur Production:**
1.  Merge PR ke `main`.
2.  GitHub Actions SSH ke server.
3.  Masuk folder `~/repositories/epi-oss-prod`.
4.  Update kode dan optimasi.

---

## 6. Verifikasi & Troubleshooting

**Masalah: 500 Internal Server Error**
- Cek log di folder `storage/logs/laravel.log`.
- Pastikan permission folder `storage` dan `bootstrap/cache` adalah `775` (atau `755` tergantung hosting).
  ```bash
  chmod -R 775 storage bootstrap/cache
  ```

**Masalah: 403 Forbidden**
- Pastikan Symlink benar mengarah ke folder `public`.
- Pastikan tidak ada file `.htaccess` di root `public_html` yang memblokir akses.

**Masalah: "Composer detected issues in your platform"**
- Tambahkan flag `--ignore-platform-reqs` jika versi PHP CLI default server berbeda dengan PHP web. Atau gunakan full path `/usr/bin/php83`.
