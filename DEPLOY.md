# Tutorial Deploy Veyra Educate ke Production

Host target: **PHP.ID** (CloudLinux + cPanel)
Domain: `https://veyracreative.site`
Folder di server: `/public_html/VEYRA/`

---

## Struktur Aplikasi

Aplikasi ini menggunakan **struktur non-standar Laravel**: `index.php` berada di **root folder**, bukan di folder `public/`. Semua file diupload langsung ke `public_html/VEYRA/`.

```
public_html/VEYRA/
├── app/              ← kode sumber utama
├── bootstrap/        ← autoloader & bootstrap
├── config/           ← konfigurasi Laravel
├── database/         ← migrations & seeders
├── public/           ← aset statis (build output, css, js, images)
│   └── storage/      ← symlink ke storage/app/public
├── resources/        ← views, lang, raw assets
├── routes/           ← routing aplikasi
├── storage/          ← logs, cache, uploaded files
├── vendor/           ← composer packages (sudah termasuk di ZIP deploy)
├── .env.production   ← template environment
├── .htaccess         ← URL rewriting & security
├── index.php         ← front controller utama
├── artisan           ← command line Laravel
└── composer.json
```

---

## Tahap 1 — Persiapan di cPanel PHP.ID

### 1.1 Buat Database

1. Login cPanel → **MySQL Databases**
2. Klik **Create Database**, beri nama (contoh: `u77oazzk_webveyra`)
3. Klik **Create User**, isi username & password, simpan
4. Tambahkan user ke database → centang **ALL PRIVILEGES** → **Make Changes**

### 1.2 Setting PHP Version

1. cPanel → **Select PHP Version** atau **MultiPHP Manager**
2. Pilih PHP **8.3** atau lebih tinggi
3. Pastikan ekstensi berikut aktif:
   - `pdo_mysql`
   - `mbstring`
   - `openssl`
   - `curl`
   - `tokenizer`
   - `json`
   - `xml`
   - `fileinfo`

### 1.3 Point Domain ke Folder yang Benar

1. cPanel → **Domains** → kelola domain `veyracreative.site`
2. Root document harus mengarah ke `public_html/VEYRA/` (bukan subfolder)

---

## Tahap 2 — Upload File

### 2.1 Upload ZIP ke cPanel

1. Buka **File Manager** di cPanel → masuk ke `public_html/VEYRA/`
2. Upload file `veyra-educate-deploy.zip` (~29 MB)
3. Klik **Extract** pada file ZIP tersebut
4. Hapus file `.zip` setelah selesai diekstrak

> **Catatan:** Archief `veyra-educate-deploy.zip` sudah include semua file `vendor/`. Tidak perlu composer install.

---

## Tahap 3 — Konfigurasi Awal di Server

Setelah extract, jalankan perintah berikut via SSH atau terminal cPanel:

```bash
cd /home/u77oazzk/public_html/VEYRA

# 1. Buat folder storage required
mkdir -p storage/app/public
mkdir -p storage/framework/cache/data
mkdir -p storage/framework/views
mkdir -p storage/framework/sessions
mkdir -p storage/logs

# 2. Buat symlink storage
ln -sf storage/app/public public/storage

# 3. Set permission
chmod -R 775 storage
chmod -R 775 bootstrap/cache

# 4. Copy .env dari template production
cp .env.production .env

# 5. Regenerate autoload (safety step)
php composer dump-autoload --optimize
```

---

## Tahap 4 — Edit Konfigurasi (.env)

Edit file `.env`:

```bash
nano .env
```

**Nilai yang WAJIB diisi:**

```ini
# Password database dari Tahap 1.1
DB_PASSWORD=MASUKKAN_PASSWORD_DATABASE_KAMU

# Kunci Midtrans (sandbox untuk testing, production untuk live)
MIDTRANS_SERVER_KEY=YOUR_SERVER_KEY
MIDTRANS_CLIENT_KEY=YOUR_CLIENT_KEY
```

**Simpan dan keluar** (`Ctrl+X`, `Y`, `Enter`).

---

## Tahap 5 — Generate Application Key

```bash
php artisan key:generate --force
```

> Jika `APP_KEY` di `.env.production` sudah valid, bisa dilewati. Hanya generate ulang jika ingin key baru.

---

## Tahap 6 — Migrasi Database

```bash
php artisan migrate --force
```

Pastikan output semua migration menunjukkan `[Ran]`. Tabel yang dibuat:

- `users` — data pengguna & admin
- `schools` — data sekolah
- `plans`, `subscriptions`, `payment_orders` — sistem pembayaran
- `web_apps`, `school_web_apps` — aplikasi sekolah
- `homepage_sections` — halaman beranda dinamis
- `marketing_pages` — halaman marketing (brosur/proposal/demo)
- `sessions`, `cache`, `jobs`, `notifications`, `audit_logs` — sistem internal

---

## Tahap 7 — Optimasi Produksi

```bash
# Clear semua cache
php artisan optimize:clear

# Cache konfigurasi & route untuk performa
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

---

## Tahap 8 — SSL & HTTPS

1. cPanel → **SSL/TLS** → **AutoSSL**
2. Pilih domain `veyracreative.site`
3. Klik **Run Now**
4. Tunggu beberapa menit hingga SSL terbit

Setelah SSL aktif, pastikan `.env` sudah benar:
```ini
APP_URL=https://veyracreative.site
```

---

## Tahap 9 — Queue Worker (Opsional)

Karena `QUEUE_CONNECTION=database`, queue perlu worker yang berjalan terus.

**Via Cron Job (cPanel):**
1. cPanel → **Cron Jobs**
2. Tambah cron job setiap menit:
```
* * * * * cd /home/u77oazzk/public_html/VEYRA && php artisan queue:work --stop-when-empty
```

---

## Tahap 10 — Verifikasi Akhir

Buka browser dan akses:
- Beranda: **https://veyracreative.site**
- Admin panel: **https://veyracreative.site/admin/login**

---

## Checklist Cepat

```
[ ] 1. Database created di cPanel (catat nama DB, user, password)
[ ] 2. PHP version = 8.3+ dengan ekstensi lengkap
[ ] 3. Domain pointed ke public_html/VEYRA/
[ ] 4. Extract veyra-educate-deploy.zip di public_html/VEYRA/
[ ] 5. .env dibuat dari .env.production & diedit (DB_PASSWORD, MIDTRANS)
[ ] 6. php composer dump-autoload --optimize
[ ] 7. php artisan key:generate --force
[ ] 8. php artisan migrate --force
[ ] 9. Storage symlink dibuat (ln -sf storage/app/public public/storage)
[ ] 10. chmod 775 storage & bootstrap/cache
[ ] 11. php artisan optimize:clear
[ ] 12. AutoSSL dijalankan
[ ] 13. Cron job queue worker (opsional)
[ ] 14. Verifikasi https://veyracreative.site/admin/login
```

---

## Troubleshooting

| Masalah | Solusi |
|---------|--------|
| `Class not found` | Jalankan `php composer dump-autoload --optimize` lalu `php artisan optimize:clear` |
| White screen / 500 error | Aktifkan `APP_DEBUG=true` sementara, cek `storage/logs/laravel.log` |
| Session tidak tersimpan | Pastikan tabel `sessions` sudah ada (`migrate --force`) |
| File upload 404 | Periksa symlink: `ls -la public/storage` harus menunjuk ke `storage/app/public` |
| Cache lama masih terbaca | Jalankan `php artisan config:clear && php artisan cache:clear` |
| APP_KEY mismatch | Gunakan key yang sama dari `.env` sebelumnya, atau regenerate dan login ulang |
| DB connection refused | Cek username/password di `.env`, pastikan user DB punya akses dari `127.0.0.1` |
