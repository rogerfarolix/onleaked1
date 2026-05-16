#!/usr/bin/env bash
# ============================================================
#  deploy.sh — Onleaked VPS Deployment Script
#  Ubuntu 22.04+ · PHP 8.3 · PostgreSQL 16 · Redis · Nginx
#  Cible : /var/www/Projets/nealix/onleaked/
# ============================================================
set -euo pipefail

# ── Couleurs ──
RED='\033[0;31m'; GREEN='\033[0;32m'; YELLOW='\033[1;33m'; NC='\033[0m'
log()  { echo -e "${GREEN}[✓] $*${NC}"; }
warn() { echo -e "${YELLOW}[!] $*${NC}"; }
err()  { echo -e "${RED}[✗] $*${NC}"; exit 1; }

PROJECT_DIR="/var/www/Projets/nealix/onleaked"
REPO_URL="https://github.com/nealix/onleaked.git"   # ← changer si besoin
DB_NAME="onleaked_db"
DB_USER="onleaked_user"
DB_PASS="$(openssl rand -base64 24)"
PHP_VER="8.3"
DOMAIN="onleaked.nealix.org"

echo ""
echo "═══════════════════════════════════════"
echo "   🔍 Onleaked — Déploiement VPS"
echo "═══════════════════════════════════════"
echo ""

# ── 0. Root check ──
[[ $EUID -ne 0 ]] && err "Ce script doit être exécuté en root (sudo)"

# ── 1. Dépendances système ──
log "Mise à jour des paquets..."
apt-get update -qq

log "Installation PHP ${PHP_VER} + extensions..."
add-apt-repository ppa:ondrej/php -y -q 2>/dev/null || true
apt-get update -qq
apt-get install -y -q \
    php${PHP_VER}-fpm \
    php${PHP_VER}-cli \
    php${PHP_VER}-pgsql \
    php${PHP_VER}-redis \
    php${PHP_VER}-mbstring \
    php${PHP_VER}-xml \
    php${PHP_VER}-curl \
    php${PHP_VER}-zip \
    php${PHP_VER}-intl \
    php${PHP_VER}-bcmath \
    php${PHP_VER}-gd \
    nginx \
    postgresql-16 \
    redis-server \
    supervisor \
    python3 \
    python3-pip \
    python3-venv \
    git \
    curl \
    unzip \
    certbot \
    python3-certbot-nginx

# ── 2. Composer ──
if ! command -v composer &>/dev/null; then
    log "Installation de Composer..."
    curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
fi

# ── 3. Holehe via pip venv ──
log "Installation de Holehe..."
python3 -m venv /opt/holehe-venv
/opt/holehe-venv/bin/pip install --upgrade pip -q
/opt/holehe-venv/bin/pip install holehe -q
# Créer wrapper pour que 'python3 -m holehe' fonctionne via PATH
cat > /usr/local/bin/holehe-run <<'EOF'
#!/bin/bash
/opt/holehe-venv/bin/python3 -m holehe "$@"
EOF
chmod +x /usr/local/bin/holehe-run
log "Holehe installé dans /opt/holehe-venv"

# ── 4. PostgreSQL ──
log "Configuration PostgreSQL..."
systemctl enable postgresql --now

# Créer DB + user
sudo -u postgres psql -c "SELECT 1 FROM pg_database WHERE datname='${DB_NAME}'" | grep -q 1 || \
    sudo -u postgres psql -c "CREATE DATABASE ${DB_NAME} ENCODING 'UTF8';"

sudo -u postgres psql -c "SELECT 1 FROM pg_roles WHERE rolname='${DB_USER}'" | grep -q 1 || \
    sudo -u postgres psql -c "CREATE USER ${DB_USER} WITH PASSWORD '${DB_PASS}';"

sudo -u postgres psql -c "GRANT ALL PRIVILEGES ON DATABASE ${DB_NAME} TO ${DB_USER};"
sudo -u postgres psql -d ${DB_NAME} -c "GRANT ALL ON SCHEMA public TO ${DB_USER};"

log "PostgreSQL : base '${DB_NAME}' prête"

# ── 5. Redis ──
log "Configuration Redis..."
systemctl enable redis-server --now

# ── 6. Dossier projet ──
log "Création du dossier projet..."
mkdir -p "${PROJECT_DIR}"
mkdir -p /var/www/Projets/nealix

# Cloner ou copier (si le repo est déjà local)
if [[ -d "${PROJECT_DIR}/.git" ]]; then
    warn "Projet déjà existant — pull..."
    cd "${PROJECT_DIR}" && git pull
elif [[ -n "${REPO_URL}" ]] && [[ "${REPO_URL}" != *"nealix/onleaked.git"* ]]; then
    git clone "${REPO_URL}" "${PROJECT_DIR}"
else
    warn "Aucun repo Git configuré — copier vos fichiers dans ${PROJECT_DIR} manuellement"
    warn "Puis relancer ce script avec --skip-clone"
fi

cd "${PROJECT_DIR}"

# ── 7. .env ──
log "Configuration .env..."
if [[ ! -f ".env" ]]; then
    cp .env.example .env
    # Remplacer les valeurs
    sed -i "s|DB_DATABASE=.*|DB_DATABASE=${DB_NAME}|" .env
    sed -i "s|DB_USERNAME=.*|DB_USERNAME=${DB_USER}|" .env
    sed -i "s|DB_PASSWORD=.*|DB_PASSWORD=${DB_PASS}|" .env
    sed -i "s|APP_URL=.*|APP_URL=https://${DOMAIN}|" .env
    warn "Éditez .env pour configurer MAIL_* et les clés API OSINT"
else
    warn ".env déjà présent — non modifié"
fi

# ── 8. Composer install ──
log "Installation des dépendances PHP..."
composer install --optimize-autoloader --no-dev --no-interaction -q

# ── 9. Laravel setup ──
log "Génération clé APP_KEY..."
php artisan key:generate --force

log "Migrations PostgreSQL..."
php artisan migrate --force

log "Optimisation Laravel..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# ── 10. Permissions ──
log "Permissions www-data..."
chown -R www-data:www-data "${PROJECT_DIR}"
chmod -R 755 "${PROJECT_DIR}/storage"
chmod -R 755 "${PROJECT_DIR}/bootstrap/cache"
mkdir -p "${PROJECT_DIR}/storage/app/osint_tmp"
chmod 700 "${PROJECT_DIR}/storage/app/osint_tmp"

# ── 11. PHP-FPM tuning ──
log "Configuration PHP-FPM..."
cat > /etc/php/${PHP_VER}/fpm/pool.d/onleaked.conf <<EOF
[onleaked]
user = www-data
group = www-data
listen = /var/run/php/php${PHP_VER}-fpm-onleaked.sock
listen.owner = www-data
listen.group = www-data
pm = dynamic
pm.max_children = 20
pm.start_servers = 4
pm.min_spare_servers = 2
pm.max_spare_servers = 8
pm.max_requests = 500
php_admin_value[disable_functions] = passthru,system,popen,proc_open
php_admin_value[memory_limit] = 256M
php_admin_value[max_execution_time] = 60
EOF

# Mettre à jour socket dans Nginx si besoin
systemctl restart php${PHP_VER}-fpm

# ── 12. Nginx ──
log "Configuration Nginx..."
cp "${PROJECT_DIR}/infra/nginx-onleaked.conf" "/etc/nginx/sites-available/onleaked"
# Update socket path in nginx config
sed -i "s|php8.3-fpm.sock|php${PHP_VER}-fpm-onleaked.sock|g" /etc/nginx/sites-available/onleaked
ln -sf /etc/nginx/sites-available/onleaked /etc/nginx/sites-enabled/
rm -f /etc/nginx/sites-enabled/default
nginx -t && systemctl reload nginx

# ── 13. SSL Let's Encrypt ──
if ! [[ -f "/etc/letsencrypt/live/${DOMAIN}/fullchain.pem" ]]; then
    log "Génération certificat SSL Let's Encrypt..."
    certbot --nginx -d "${DOMAIN}" --non-interactive --agree-tos -m admin@nealix.org || \
        warn "Certbot échoué — configurez manuellement le SSL"
else
    log "Certificat SSL déjà présent"
fi

# ── 14. Supervisor ──
log "Configuration Supervisor..."
cp "${PROJECT_DIR}/infra/onleaked-worker.conf" /etc/supervisor/conf.d/
# Update path in supervisor config
sed -i "s|PHP_VER|${PHP_VER}|g" /etc/supervisor/conf.d/onleaked-worker.conf
supervisorctl reread
supervisorctl update
supervisorctl start onleaked-worker:* || supervisorctl restart onleaked-worker:*

# ── 15. Laravel Scheduler (cron) ──
log "Configuration cron scheduler..."
(crontab -l 2>/dev/null | grep -v "onleaked"; \
 echo "* * * * * www-data php ${PROJECT_DIR}/artisan schedule:run >> /dev/null 2>&1") | \
 crontab -

# ── 16. Logrotate ──
cat > /etc/logrotate.d/onleaked <<EOF
${PROJECT_DIR}/storage/logs/*.log {
    daily
    missingok
    rotate 14
    compress
    delaycompress
    notifempty
    create 0644 www-data www-data
    sharedscripts
    postrotate
        supervisorctl restart onleaked-worker:* > /dev/null 2>&1 || true
    endscript
}
EOF

# ── Résumé ──
echo ""
echo "═══════════════════════════════════════════════════════"
echo -e "${GREEN}  ✅ Déploiement Onleaked terminé !${NC}"
echo "═══════════════════════════════════════════════════════"
echo ""
echo "  📁 Projet     : ${PROJECT_DIR}"
echo "  🌐 URL        : https://${DOMAIN}"
echo "  🗄️  Base       : ${DB_NAME} (user: ${DB_USER})"
echo -e "  🔑 DB Pass    : ${YELLOW}${DB_PASS}${NC}  ← SAUVEGARDEZ-LE"
echo ""
echo "  📋 Prochaines étapes :"
echo "  1. Éditez ${PROJECT_DIR}/.env → MAIL_*, EMAILREP_API_KEY, BREACHDIRECTORY_API_KEY"
echo "  2. Relancez : php artisan config:cache"
echo "  3. Vérifiez : supervisorctl status"
echo "  4. Testez   : https://${DOMAIN}"
echo ""
echo "  🩺 Diagnostics :"
echo "  - Logs app   : tail -f ${PROJECT_DIR}/storage/logs/laravel.log"
echo "  - Logs worker: tail -f ${PROJECT_DIR}/storage/logs/worker.log"
echo "  - Queue      : php artisan queue:monitor redis:osint"
echo "═══════════════════════════════════════════════════════"
