#!/bin/bash
# ============================================================
# deploy.sh — Script Deploy Otomatis
# Aplikasi Pesantren Nurul Furqon
# ============================================================
# Penggunaan:
#   chmod +x deploy.sh
#   ./deploy.sh          → Deploy pertama kali (fresh install)
#   ./deploy.sh update   → Update setelah git pull
# ============================================================

set -e

# Warna output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

echo -e "${BLUE}============================================================${NC}"
echo -e "${BLUE}  🕌  Deploy Aplikasi Pesantren Nurul Furqon${NC}"
echo -e "${BLUE}============================================================${NC}"
echo ""

MODE=${1:-"fresh"}

# ============================================================
# 1. Cek keberadaan .env
# ============================================================
if [ ! -f ".env" ]; then
    echo -e "${YELLOW}⚠️  File .env tidak ditemukan!${NC}"
    if [ -f ".env.production" ]; then
        echo -e "${YELLOW}   Meng-copy .env.production → .env${NC}"
        cp .env.production .env
        echo -e "${RED}   ❗ PENTING: Edit .env dan isi semua value <GANTI_INI>${NC}"
        echo -e "${RED}   Jalankan ulang script ini setelah mengisi .env${NC}"
        exit 1
    else
        echo -e "${RED}   ❌ File .env.production juga tidak ada. Buat .env terlebih dahulu.${NC}"
        exit 1
    fi
fi

echo -e "${GREEN}✅ File .env ditemukan${NC}"

# ============================================================
# 2. Install PHP dependencies
# ============================================================
echo ""
echo -e "${BLUE}📦 Installing PHP dependencies...${NC}"
composer install --optimize-autoloader --no-dev --no-interaction
echo -e "${GREEN}✅ PHP dependencies installed${NC}"

# ============================================================
# 3. Generate APP_KEY jika kosong
# ============================================================
if grep -q "APP_KEY=$" .env || grep -q "APP_KEY=\"\"" .env; then
    echo ""
    echo -e "${BLUE}🔑 Generating application key...${NC}"
    php artisan key:generate --force
    echo -e "${GREEN}✅ Application key generated${NC}"
fi

# ============================================================
# 4. Install & build frontend assets
# ============================================================
echo ""
echo -e "${BLUE}🎨 Installing & building frontend assets...${NC}"
npm install
npm run build
echo -e "${GREEN}✅ Frontend assets built${NC}"

# ============================================================
# 5. Run migrations
# ============================================================
echo ""
echo -e "${BLUE}🗄️  Running database migrations...${NC}"
php artisan migrate --force
echo -e "${GREEN}✅ Migrations completed${NC}"

# ============================================================
# 6. Storage symlink
# ============================================================
echo ""
echo -e "${BLUE}🔗 Creating storage symlink...${NC}"
php artisan storage:link 2>/dev/null || echo -e "${YELLOW}   ℹ️  Storage link sudah ada${NC}"
echo -e "${GREEN}✅ Storage link ready${NC}"

# ============================================================
# 7. Cache & Optimize
# ============================================================
echo ""
echo -e "${BLUE}⚡ Optimizing for production...${NC}"
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache
echo -e "${GREEN}✅ Application optimized${NC}"

# ============================================================
# 8. Set permissions (Linux only)
# ============================================================
echo ""
echo -e "${BLUE}🔒 Setting file permissions...${NC}"
if [ "$(uname)" = "Linux" ] || [ "$(uname)" = "Darwin" ]; then
    chmod -R 775 storage bootstrap/cache
    if id "www-data" &>/dev/null; then
        chown -R www-data:www-data storage bootstrap/cache
    fi
    echo -e "${GREEN}✅ Permissions set${NC}"
else
    echo -e "${YELLOW}   ℹ️  Bukan Linux, skip permission setting${NC}"
fi

# ============================================================
# Done!
# ============================================================
echo ""
echo -e "${BLUE}============================================================${NC}"
echo -e "${GREEN}  🎉  DEPLOY SELESAI!${NC}"
echo -e "${BLUE}============================================================${NC}"
echo ""
echo -e "${YELLOW}  📋 Checklist pasca-deploy:${NC}"
echo -e "     1. Pastikan APP_URL di .env sudah benar"
echo -e "     2. Pastikan HTTPS sudah aktif (SSL certificate)"
echo -e "     3. Ganti password akun default (admin@pesantren.id)"
echo -e "     4. Isi FONNTE_TOKEN jika butuh notifikasi WhatsApp"
echo -e "     5. Setup cron job untuk Laravel scheduler:"
echo -e "        ${BLUE}* * * * * cd /path-to-app && php artisan schedule:run >> /dev/null 2>&1${NC}"
echo -e "     6. Setup queue worker (supervisor/systemd):"
echo -e "        ${BLUE}php artisan queue:work --sleep=3 --tries=3 --max-time=3600${NC}"
echo ""
