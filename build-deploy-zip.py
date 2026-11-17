#!/usr/bin/env python3
"""Сборка deploy-архива с vendor/ внутри (~120 MB)."""
import os
import shutil
import zipfile
from pathlib import Path

ROOT = Path(__file__).resolve().parent
ZIP  = ROOT / "veyra-educate-deploy.zip"
TMP  = ROOT / "_veyra_build_staging"


def run(cmd):
    print(f"  $ {cmd}")
    rc = os.system(cmd)
    if rc != 0:
        raise SystemExit(f"command failed with code {rc}: {cmd}")


print("[1/5] Очистка staging...")
if TMP.exists():
    shutil.rmtree(TMP)
TMP.mkdir()

print("[2/5] Fresh install vendor (без dev-pacakges)...")
vendor_dir = ROOT / "vendor"
if vendor_dir.exists():
    shutil.rmtree(vendor_dir)
run("php composer install --no-dev --optimize-autoloader --no-scripts")
assert (ROOT / "vendor" / "autoload.php").exists(), "vendor/autoload.php не создан!"

print("[3/5] Копирование в staging...")
SKIP_DIRS   = {"storage", "tests", ".git", "__pycache__"}
SKIP_FILES  = {".env", ".env.local", ".env.production", "*.zip", "*.log"}


def _should_skip(name, is_dir):
    if is_dir and name in SKIP_DIRS:
        return True
    if any(p.startswith(".") for p in name.split(os.sep)):
        return True
    return False


def copy_tree(src, dst):
    for child in src.iterdir():
        if _should_skip(child.name, child.is_dir()):
            continue
        dest = dst / child.name
        if child.is_dir():
            dest.mkdir(exist_ok=True)
            copy_tree(child, dest)
        else:
            shutil.copy2(child, dest)


copy_tree(ROOT, TMP)
# явно копируем vendor (вдруг пропустили выше)
shutil.copytree(ROOT / "vendor", TMP / "vendor",
                dirs_exist_ok=True, symlinks=True)

with open(TMP / ".env.example", "w") as f:
    f.write("# скопируйте в .env и заполните DB_PASSWORD, MIDTRANS keys\n")

count = sum(1 for _ in TMP.rglob("*"))
print(f"      Строк в staging: {count}")

print("[4/5] Упаковка ZIP (deflate)...")
if ZIP.exists():
    ZIP.unlink()
with zipfile.ZipFile(ZIP, "w", compression=zipfile.ZIP_DEFLATED, compresslevel=9) as zf:
    for p in sorted(TMP.rglob("*")):
        arcname = str(p.relative_to(TMP))
        zf.write(p, arcname)
sz_mb = ZIP.stat().st_size / 1e6
print(f"      Размер ZIP: {sz_mb:.1f} MB")

print("[5/5] Очистка staging...")
shutil.rmtree(TMP)

# sanity check
with zipfile.ZipFile(ZIP, "r") as zf:
    names = zf.namelist()
    vend  = [n for n in names if n.startswith("vendor/")]
    auth  = [n for n in names if "autoload" in n.lower()]
print(f"\n✅ DEPLOY ZIP готов!")
print(f"   Путь  : {ZIP}")
print(f"   Размер: {sz_mb:.1f} MB  (vendor включён)")
print(f"   Записей: {len(names)}, vendor: {len(vend)}")
print(f"\n   Шаги после загрузки на сервер:")
print(f"   1. Extract в /home/u77oazzk/public_html/VEYRA/")
print(f"   2. Редактировать .env → DB_PASSWORD + MIDTRANS keys")
print(f"   3. php artisan migrate --force")
print(f"   4. ln -sf storage/app/public public/storage")
print(f"   5. chmod -R 775 storage bootstrap/cache")
