#!/usr/bin/env bash
# ============================================================
#  install.sh — Intègre les fichiers Onleaked dans Laravel 13
#  À exécuter UNE FOIS après "composer create-project laravel/laravel ."
#  dans le dossier /var/www/Projets/nealix/onleaked/
# ============================================================
set -euo pipefail

GREEN='\033[0;32m'; YELLOW='\033[1;33m'; RED='\033[0;31m'; NC='\033[0m'
log()  { echo -e "${GREEN}[✓] $*${NC}"; }
warn() { echo -e "${YELLOW}[!] $*${NC}"; }
err()  { echo -e "${RED}[✗] $*${NC}"; exit 1; }

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
PATCH_DIR="${SCRIPT_DIR}/patch"
TARGET="${1:-$(pwd)}"

echo ""
echo "════════════════════════════════════════"
echo "  🔍 Onleaked — Installation des fichiers"
echo "════════════════════════════════════════"
echo "  Source : ${PATCH_DIR}"
echo "  Cible  : ${TARGET}"
echo ""

[[ -d "${PATCH_DIR}" ]] || err "Dossier patch/ introuvable dans ${SCRIPT_DIR}"
[[ -f "${TARGET}/artisan" ]] || {
    warn "Pas de projet Laravel détecté dans ${TARGET}"
    warn "Création d'un nouveau projet Laravel 13..."
    cd "$(dirname ${TARGET})"
    composer create-project laravel/laravel "$(basename ${TARGET})" --no-interaction -q
    cd "${TARGET}"
}

# ── Copie récursive du patch ──
log "Copie des fichiers Onleaked..."
cp -rf "${PATCH_DIR}/." "${TARGET}/"

# ── Installation dépendances Composer ──
log "Installation des dépendances PHP..."
cd "${TARGET}"
composer require \
    io-developer/php-whois:^4.0 \
    predis/predis:^2.3 \
    --no-interaction -q 2>&1 || warn "Vérifiez les dépendances manuellement"

# ── Création des dossiers storage ──
log "Création des dossiers de stockage..."
mkdir -p storage/app/osint_tmp
mkdir -p storage/app/public
mkdir -p storage/framework/{cache/data,sessions,views,testing}
mkdir -p storage/logs
chmod -R 755 storage bootstrap/cache
chmod 700 storage/app/osint_tmp

# ── Placeholder .gitkeep ──
touch storage/app/.gitkeep
touch storage/app/public/.gitkeep
touch storage/framework/cache/.gitkeep
touch storage/framework/sessions/.gitkeep
touch storage/framework/views/.gitkeep
touch storage/logs/.gitkeep

# ── APP_KEY si .env existe ──
if [[ -f ".env" ]] && ! grep -q "APP_KEY=base64:" .env; then
    php artisan key:generate
fi

log "Installation terminée !"
echo ""
echo "  Prochaines étapes :"
echo "  1. Configurez .env (DB, MAIL, API keys)"
echo "  2. php artisan migrate"
echo "  3. Lancez deploy.sh pour la config VPS complète"
echo ""
