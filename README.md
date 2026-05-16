# 🔍 Onleaked

**Outil gratuit de sensibilisation à l'empreinte numérique email** — like HaveIBeenPwned, made in France.

[![Laravel](https://img.shields.io/badge/Laravel-13-red.svg)](https://laravel.com)
[![PHP](https://img.shields.io/badge/PHP-8.3-blue.svg)](https://php.net)
[![PostgreSQL](https://img.shields.io/badge/PostgreSQL-16-blue.svg)](https://postgresql.org)

---

## ✨ Fonctionnalités

- **Aucune inscription** — entrez votre email, recevez le rapport instantanément
- **10+ sources OSINT** : Holehe, EmailRep.io, BreachDirectory, Gravatar, DNS/WHOIS
- **Score de risque** de A (sûr) à F (très exposé)
- **Cache intelligent** — résultats conservés 6h pour accélérer les analyses répétées
- **Interface animée** — terminal live pendant l'analyse
- **100% gratuit, sans pub, sans tracking**

## 🏗 Stack technique

```
Ubuntu 22.04 LTS
├── Nginx (reverse proxy + SSL Let's Encrypt)
├── PHP 8.3-FPM
├── Laravel 13 (framework principal)
├── PostgreSQL 16 (base de données)
├── Redis 7 (queue driver + cache)
├── Supervisor (queue workers)
└── Python 3 + Holehe (détection comptes)
```

## 🚀 Déploiement rapide

```bash
# Cloner le projet
git clone https://github.com/nealix/onleaked.git /var/www/Projets/nealix/onleaked

# Lancer le script de déploiement (root)
chmod +x deploy.sh && sudo ./deploy.sh
```

Le script installe et configure automatiquement tout l'environnement.

## ⚙️ Configuration

Après déploiement, éditez `/var/www/Projets/nealix/onleaked/.env` :

```env
# SMTP (Brevo gratuit : 300 mails/jour)
MAIL_HOST=smtp-relay.brevo.com
MAIL_USERNAME=your@email.com
MAIL_PASSWORD=your_brevo_key

# APIs OSINT (optionnelles mais recommandées)
EMAILREP_API_KEY=           # emailrep.io — gratuit
BREACHDIRECTORY_API_KEY=    # RapidAPI free tier
```

Puis : `php artisan config:cache`

## 📦 Sources OSINT intégrées

| Source | Type | Coût |
|--------|------|------|
| Holehe | Détection comptes 250+ sites | Gratuit |
| EmailRep.io | Réputation, activité suspecte | Gratuit (limité) |
| BreachDirectory | Fuites mots de passe | RapidAPI free tier |
| Gravatar | Profil public | Gratuit |
| DNS/WHOIS | Infrastructure domaine | Gratuit (natif PHP) |

## 🔒 Vie privée & Sécurité

- Emails jamais stockés en clair → hash SHA-256 uniquement
- Rate limiting : 5 analyses/heure/IP
- Résultats accessibles uniquement via UUID unique
- HTTPS enforced, headers de sécurité complets

## 🩺 Diagnostics

```bash
# État des workers
supervisorctl status

# Logs application
tail -f storage/logs/laravel.log

# Logs workers queue
tail -f storage/logs/worker.log

# Monitor queue
php artisan queue:monitor redis:osint
```

## 📄 Licence

MIT — Développé par [Nealix](https://nealix.org)
