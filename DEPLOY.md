# Tutorial Deploy Veyra Educate ke Production

Host target: **PHP.ID** (CloudLinux + cPanel)
Domain: `https://veyracreative.site`

---

## Tahap 0 — Persiapan di Komputer Lokal

### 0.1 Cek Struktur Folder

Pastikan struktur folder di lokal seperti ini:

```
veyraeducate/
├── app/
├── bootstrap/
├── config/
├── database/
├── public/          ← JANGAN upload folder ini ke root hosting
├── resources/
├── routes/
├── storage/
├── vendor/
├── .env.production
├── .htaccess
├── index.php
└── composer.json
```

Catatan penting: **folder `public/` TIDAK DIUPLOAD**. Yang diupload adalah isi dalam `public/` langsung ke `public_html/`.

---

## Tahap 1 — Persiapan di cPanel PHP.ID

### 1.1 Buat Database

1. Login cPanel → **MySQL Databases**
2. Buat database baru, catat namanya (contoh: `u77oazzk_webveyra`)
3. Buat user database baru, catat username & password
4. Assign user ke database dengan semua privilege

### 1.2 Setting PHP Version

1. cPanel → **Select PHP Version** atau **MultiPHP Manager**
2. Pilih PHP **8.3** atau lebih tinggi
3. Aktifkan ekstensi berikut jika belum:
   - `pdo_mysql`
   - `mbstring`
   - `openssl`
   - `curl`
   - `tokenizer`
   - `json`
   - `xml`
   - `ctype`
   - `fileinfo`

### 1.3 Point Domain ke Folder yang Benar

1. cPanel → **Domains** → kelola domain `veyracreative.site`
2. Root document harus指向 ke `public_html/` (bukan subfolder)

---

## Tahap 2 — Upload File

### 2.1 Cara Upload (Pilih Salah Satu)

**Opsi A: Via Git (paling direkomendasikan)**
```bash
# Di server SSH
cd /home/u77oazzk/public_html
git clone https://github.com/username/veyraeducate.git .
```

**Opsi B: Via File Manager cPanel**
1. Buka **File Manager** → masuk ke `public_html/`
2. Upload file-fichier berikut (exclude folder `vendor/` dan `.git/`):
   - `.env.production`
   - `.htaccess`
   - `index.php`
   - `artisan`
   - Semua folder: `app/`, `bootstrap/`, `config/`, `database/`, `public/`, `resources/`, `routes/`, `storage/`
3. Upload file dari `public/` lokal ke `public_html/`:
   - Copy isi `public/` (termasuk `.htaccess` dari public) ke `public_html/`

**Opsi C: Via FTP**
1. Gunakan FileZilla / FTP client
2. Connect ke server PHP.ID
3. Upload seluruh file kecuali `vendor/` dan `node_modules/`

### 2.2 Setelah Upload Selesai

Masuk ke terminal/server via SSH atau File Manager cPanel, lalu jalankan:

```bash
cd /home/u77oazzk/public_html

# Install composer dependencies (tanpa dev packages)
composer install --no-dev --optimize-autoloader

# Buat file .env dari template production
cp .env.production .env

# Edit .env, ganti nilai yang masih placeholder
nano .env
```

Hal yang WAJIB diubah di `.env`:

```ini
# Ganti dengan password database kamu
DB_PASSWORD=PASSWORD_YG_SUDAH_DIBUAT_DI_Cpanel

# Ganti dengan key Midtrans (sandbox atau production)
MIDTRANS_SERVER_KEY=YOUR_SERVER_KEY
MIDTRANS_CLIENT_KEY=YOUR_CLIENT_KEY
```

### 2.3 Generate Application Key

```bash
php artisan key:generate --force
```

Ini akan membuat `APP_KEY` baru di `.env`. Jika sudah ada key yang valid dari lokal, gunakan key yang sama agar session tidak invalid.

---

## Tahap 3 — Migrasi Database

```bash
# Jalankan semua migrasi
php artisan migrate --force
```

Pastikan output semua migration `[Ran]`. Jika ada error, baca pesan error dan perbaiki.

Tabel yang dibuat otomatis:
- `users`, `sessions` (untuk auth)
- `cache` (untuk cache)
- `jobs`, `failed_jobs` (untuk queue)
- Semua tabel aplikasi

---

## Tahap 4 — Buat Storage Symlink

Symlink wajib ada agar file upload bisa diakses via web.

**Via SSH:**
```bash
cd /home/u77oazzk/public_html
ln -sf /home/u77oazzk/public_html/storage/app/public /home/u77oazzk/public_html/public/storage
```

**Via File Manager cPanel:**
1. Buka `public_html/`
2. Klik kanan `storage` → **Symbolic Link**
3. Target: `/home/u77oazzk/public_html/storage/app/public`
4. Nama link: `storage`

Verifikasi:
```bash
ls -la /home/u77oazzk/public_html/public/storage
# Harus muncul: storage -> /home/u77oazzk/.../storage/app/public
```

Test akses: buka URL `https://veyracreative.site/storage/app/public/test.txt` (buat file test dulu).

---

## Tahap 5 — Optimasi Produksi

```bash
# Clear semua cache
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

# Cache konfigurasi & route untuk performa
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Set permission folder
chmod -R 775 /home/u77oazzk/public_html/storage
chmod -R 775 /home/u77oazzk/public_html/bootstrap/cache
```

---

## Tahap 6 — Queue Worker (Opsional tapi Disarankan)

Karena `QUEUE_CONNECTION=database`, perlu worker yang berjalan terus.

**Via Cron Job (cPanel):**
1. cPanel → **Cron Jobs**
2. Tambah cron job setiap menit:
```
* * * * * cd /home/u77oazzk/public_html && php artisan queue:work --stop-when-empty
```

Atau gunakan supervisor (jika ada akses SSH penuh):
```bash
# Instal supervisor
sudo apt install supervisor

# Buat config /etc/supervisor/conf.d/veyra-queue.conf
[program:veyra-queue]
command=php /home/u77oazzk/public_html/artisan queue:work database --sleep=3 --tries=3 --max-time=3600
directory=/home/u77oazzk/public_html
autostart=true
autorestart=true
stderr_logfile=/var/log/veyra-queue.err.log
stdout_logfile=/var/log/veyra-queue.out.log
```

---

## Tahap 7 — SSL & HTTPS

1. cPanel → **SSL/TLS** → **AutoSSL**
2. Pilih domain `veyracreative.site`
3. Klik **Run Now**
4. Tunggu beberapa menit hingga SSL terbit

Setelah SSL aktif, pastikan `.env` sudah benar:
```ini
APP_URL=https://veyracreative.site
```

---

## Tahap 8 — Verifikasi Akhir

Jalankan semua command ini satu per satu:

```bash
# Cek status migration
php artisan migrate:status

# Cek health endpoint
curl https://veyracreative.site/health

# Test login admin (via browser)
# Buka https://veyracreative.site/admin/login
```

---

## Troubleshooting Umum

| Masalah | Solusi |
|---------|--------|
| `Class 'App\Models\FailedJob' not found` | Run `php artisan optimize:clear` setelah file model dibuat |
| White screen / 500 error | Cek `storage/logs/laravel.log`, aktifkan `APP_DEBUG=true` sementara |
| Session tidak tersimpan | Pastikan tabel `sessions` sudah dibuat (`migrate --force`) |
| File upload 404 | Periksa symlink `public/storage` |
| Queue tidak jalan | Pastikan cron job / supervisor aktif |
| Cache lama masih terbaca | Jalankan `php artisan config:clear && php artisan cache:clear` |
| APP_KEY mismatch setelah deploy ulang | Gunakan key yang sama dari `.env` sebelumnya |
| DB connection refused | Cek username/password di `.env`, pastikan user DB punya akses dari `127.0.0.1` |

---

## Checklist Cepat (Print-friendly)

```
[ ] 1. Database created di cPanel
[ ] 2. PHP version = 8.3+
[ ] 3. File uploaded (tanpa vendor/)
[ ] 4. composer install --no-dev --optimize-autoloader
[ ] 5. .env copied & edited (DB_PASSWORD, MIDTRANS keys)
[ ] 6. php artisan key:generate --force
[ ] 7. php artisan migrate --force
[ ] 8. Storage symlink dibuat
[ ] 9. chmod 775 storage & bootstrap/cache
[ ] 10. php artisan config:cache && route:cache && view:cache
[ ] 11. AutoSSL dijalankan
[ ] 12. Cron job queue worker dipasang
[ ] 13. Verifikasi https://veyracreative.site/health
```
