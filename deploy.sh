#!/bin/bash
# ============================================
# Veyra Educate - Deploy Helper Script
# Jalankan setelah upload & ekstrak file ke server
# ============================================

APP_PATH="/home/u77oazzk/public_html/VEYRA"

echo "=== Veyra Educate Deploy Script ==="
echo ""

# 1. Buat folder required
echo "[1/7] Memastikan folder storage tersedia..."
mkdir -p "$APP_PATH/bootstrap/cache"
mkdir -p "$APP_PATH/storage/app/public"
mkdir -p "$APP_PATH/storage/framework/cache/data"
mkdir -p "$APP_PATH/storage/framework/views"
mkdir -p "$APP_PATH/storage/framework/sessions"
mkdir -p "$APP_PATH/storage/logs"
echo "      Done."

# 2. Setup symlink storage
echo "[2/7] Membuat symlink storage..."
if [ -L "$APP_PATH/public/storage" ]; then
    rm "$APP_PATH/public/storage"
fi
ln -sf "$APP_PATH/storage/app/public" "$APP_PATH/public/storage"
echo "      Done."

# 3. Set permission
echo "[3/7] Mengatur permission..."
chmod -R 775 "$APP_PATH/storage"
chmod -R 775 "$APP_PATH/bootstrap/cache"
echo "      Done."

# 4. Copy .env dari production template
echo "[4/7] Setup .env..."
if [ ! -f "$APP_PATH/.env" ]; then
    cp "$APP_PATH/.env.production" "$APP_PATH/.env"
    echo "      .env dibuat dari .env.production"
else
    echo "      .env sudah ada, dilewati."
fi

# 5. Regenerate autoload (penting jika ada masalah Class not found)
echo "[5/7] Regenerate autoload..."
cd "$APP_PATH"
php composer dump-autoload --optimize 2>/dev/null
echo "      Done."

# 5. Check symlink
echo "[5/7] Verifikasi symlink..."
ls -la "$APP_PATH/public/storage" | grep -q "storage/app/public" && echo "      OK - symlink valid" || echo "      WARNING - symlink mungkin belum benar"

# 6. Optimasi cache
echo "[6/7] Optimasi cache..."
cd "$APP_PATH"
php artisan config:clear 2>/dev/null
php artisan cache:clear 2>/dev/null
php artisan route:clear 2>/dev/null
php artisan view:clear 2>/dev/null
php artisan config:cache 2>/dev/null
php artisan route:cache 2>/dev/null
php artisan view:cache 2>/dev/null
echo "      Done."

# 7. Selesai
echo "[7/7] Selesai!"
echo ""
echo "LANGKAH SELANJUTNYA:"
echo "  1. Edit .env — isi DB_PASSWORD dan MIDTRANS keys"
echo "  2. php artisan migrate --force"
echo "  3. php artisan key:generate --force  (hanya jika APP_KEY kosong)"
echo "  4. Buka https://veyracreative.site/admin untuk verifikasi"
