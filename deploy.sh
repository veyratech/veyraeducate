#!/bin/bash
# ============================================
# Veyra Educate - Deploy Helper Script
# Jalankan setelah upload file ke server
# ============================================

APP_PATH="/home/u77oazzk/public_html/VEYRA"

echo "=== Veyra Educate Deploy Script ==="
echo ""

# 1. Setup symlink storage
echo "[1/6] Membuat symlink storage..."
if [ -L "$APP_PATH/public/storage" ]; then
    rm "$APP_PATH/public/storage"
fi
ln -sf "$APP_PATH/storage/app/public" "$APP_PATH/public/storage"
echo "      Done."

# 2. Buat folder required
echo "[2/6] Memastikan folder storage tersedia..."
mkdir -p "$APP_PATH/storage/app/public"
mkdir -p "$APP_PATH/storage/framework/cache/data"
mkdir -p "$APP_PATH/storage/framework/views"
mkdir -p "$APP_PATH/storage/framework/sessions"
mkdir -p "$APP_PATH/storage/logs"
echo "      Done."

# 3. Set permission
echo "[3/6] Mengatur permission..."
chmod -R 775 "$APP_PATH/storage"
chmod -R 775 "$APP_PATH/bootstrap/cache"
echo "      Done."

# 4. Copy .env dari production template
echo "[4/6] Setup .env..."
if [ ! -f "$APP_PATH/.env" ]; then
    cp "$APP_PATH/.env.production" "$APP_PATH/.env"
    echo "      .env dibuat dari .env.production"
else
    echo "      .env sudah ada, dilewati."
fi

# 5. Check symlink
echo "[5/6] Verifikasi symlink..."
ls -la "$APP_PATH/public/storage" | grep -q "storage/app/public" && echo "      OK - symlink valid" || echo "      WARNING - symlink mungkin belum benar"

# 6. Selesai
echo "[6/6] Selesai!"
echo ""
echo "LANGKAH SELANJUTNYA:"
echo "  - Edit .env, isi DB_PASSWORD dan MIDTRANS keys"
echo "  - Jalankan: php artisan migrate --force"
echo "  - Jalankan: php artisan key:generate --force"
echo "  - Jalankan: php artisan config:cache && php artisan route:cache"
