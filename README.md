# Onleaked — Cybersecurity Intelligence Platform

> Privacy-first cybersecurity SaaS built with Laravel 13, PostgreSQL, Alpine.js and Tailwind CSS.

---

## Overview

**Onleaked** is a cybersecurity intelligence platform offering three core services:

| Service | Description | Auth required |
|---|---|---|
| **Leak Check & Digital Footprint** | Check if an email appears in known data breaches and discover accounts across 120+ platforms via Holehe | No |
| **Domain Analysis** | DNS records, SPF/DMARC audit, subdomain enumeration, reputation check | No |
| **Vulnerability Alerts** | Subscribe to technologies and receive AI-powered CVE email alerts | Yes |

---

## Tech Stack

| Layer | Technology |
|---|---|
| Framework | Laravel 13.8 (PHP 8.3) |
| Database | PostgreSQL 15 |
| Frontend | Blade + Alpine.js + Tailwind CSS v4 |
| Auth | Laravel Breeze (session-based, email verification) |
| Queue | Redis |
| Email | SMTP (Mailable + Queue) |
| Python service | Holehe (digital footprint via subprocess) |

---

## Architecture

```
onleaked/
├── app/
│   ├── Console/Commands/
│   │   └── SendVulnerabilityAlerts.php   # Artisan command: send CVE alert emails
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── LeakCheckController.php       # POST /check-email — breach + footprint
│   │   │   ├── DomainAnalysisController.php  # POST /analyze-domain — DNS + email config
│   │   │   ├── UserTechnologyController.php  # POST /technologies — subscription sync
│   │   │   ├── ProfileController.php         # GET|PATCH|DELETE /profile
│   │   │   └── Auth/                         # Breeze auth controllers (9 files)
│   │   └── Middleware/
│   │       ├── SecurityHeaders.php       # X-Frame-Options, CSP, HSTS, Referrer-Policy
│   │       └── AdminMiddleware.php       # Role-based admin gate
│   ├── Mail/
│   │   └── VulnerabilityAlertMail.php
│   └── Models/
│       ├── User.php            # UUID PK, role field, belongsToMany(Technology)
│       ├── Technology.php      # UUID PK, unique name, hasMany(Vulnerability)
│       └── Vulnerability.php   # UUID PK, CVE data + AI recommendation
├── database/migrations/
│   ├── create_users_table.php
│   ├── create_technologies_table.php
│   ├── create_technology_user_table.php  # pivot
│   └── create_vulnerabilities_table.php
├── python_service/
│   ├── footprint.py      # Calls holehe CLI, parses output, returns JSON
│   ├── venv/             # Python virtual environment
│   └── requirements.txt
├── resources/views/
│   ├── layouts/
│   │   ├── public.blade.php    # Public layout (nav + footer)
│   │   └── app.blade.php       # Authenticated layout
│   ├── welcome.blade.php           # Landing page (hero + service cards)
│   ├── leak-check.blade.php        # Leak Check & Footprint tool
│   ├── domain-analysis.blade.php   # Domain Analysis tool
│   ├── dashboard.blade.php         # Vulnerability alerts dashboard
│   └── admin/dashboard.blade.php   # Admin-only panel
└── routes/
    ├── web.php     # All application routes
    └── auth.php    # Breeze auth routes
```

---

## Database Schema

```
users
├── id               UUID, PK
├── name, email      unique
├── role             string  — 'user' | 'admin'  (default: 'user')
├── password
└── email_verified_at, remember_token, timestamps

technologies
├── id               UUID, PK
├── name             unique
└── timestamps

technology_user  [pivot]
├── technology_id    FK → technologies (cascade delete)
└── user_id          FK → users (cascade delete)

vulnerabilities
├── id               UUID, PK
├── technology_id    FK → technologies (cascade delete)
├── cve_id           unique
├── title, description, severity
├── ai_recommendation
└── published_at, timestamps
```

---

## Routes

### Public
| Method | URI | Handler | Notes |
|---|---|---|---|
| GET | `/` | `welcome` view | Landing page |
| GET | `/leak-check` | `leak-check` view | Leak Check & Footprint tool |
| POST | `/check-email` | `LeakCheckController@check` | Rate limited 5/min/IP |
| GET | `/domain` | `DomainAnalysisController@show` | Domain Analysis tool |
| POST | `/analyze-domain` | `DomainAnalysisController@analyze` | Rate limited 5/min/IP |
| GET | `/services` | `pages.services` view | Services overview |
| GET | `/contact` | `pages.contact` view | Contact page |

### Authenticated (`auth` + `verified`)
| Method | URI | Handler |
|---|---|---|
| GET | `/dashboard` | Technology subscription management |
| POST | `/technologies` | `UserTechnologyController@update` |
| GET/PATCH/DELETE | `/profile` | `ProfileController` |

### Admin (`auth` + `verified` + `admin`)
| Method | URI | Handler |
|---|---|---|
| GET | `/admin` | `admin.dashboard` view |

---

## External APIs & Services

| Service | Usage | Cache |
|---|---|---|
| [XposedOrNot](https://xposedornot.com/api_doc) | Email breach lookup | Breach list cached 24h |
| [crt.sh](https://crt.sh) | Subdomain enumeration (Certificate Transparency) | None |
| Holehe (Python subprocess) | Digital footprint across 120+ platforms | None |

---

## Local Development Setup

### Prerequisites
- PHP 8.3+ with extensions: `pgsql`, `pdo_pgsql`, `redis`, `curl`
- Composer 2
- Node.js 20+ / npm
- PostgreSQL 15
- Redis
- Python 3.10+

### 1. Install dependencies
```bash
composer install
npm install
```

### 2. Environment
```bash
cp .env.example .env
php artisan key:generate
```

Minimal `.env` for local dev:
```env
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8000

DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_DATABASE=onleaked_bd
DB_USERNAME=postgres
DB_PASSWORD=your_password

SESSION_DRIVER=file
QUEUE_CONNECTION=redis
REDIS_HOST=127.0.0.1
```

### 3. Database & seed
```bash
php artisan migrate
php artisan db:seed --class=TechnologySeeder
```

### 4. Python service
```bash
cd python_service
python3 -m venv venv
source venv/bin/activate
pip install holehe
```

### 5. Run development servers
```bash
# Terminal 1 — Laravel
php artisan serve

# Terminal 2 — Vite (hot reload)
npm run dev

# Terminal 3 — Queue worker (email alerts)
php artisan queue:work
```

---

## Production Deployment

### Environment hardening (`.env`)
```env
APP_ENV=production
APP_DEBUG=false

SESSION_DRIVER=file
SESSION_ENCRYPT=true
SESSION_SECURE_COOKIE=true
SESSION_SAME_SITE=strict
SESSION_HTTP_ONLY=true
```

### Asset build & cache
```bash
npm run build
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### Queue — Supervisor example
```ini
[program:onleaked-worker]
command=php /var/www/onleaked/artisan queue:work redis --sleep=3 --tries=3
autostart=true
autorestart=true
user=www-data
```

### Scheduled commands — crontab
```cron
* * * * * cd /var/www/onleaked && php artisan schedule:run >> /dev/null 2>&1
```

This triggers `onleaked:send-alerts` daily to notify subscribed users of new CVEs.

---

## Security Features

| Feature | Implementation |
|---|---|
| CSRF protection | X-CSRF-TOKEN header on all AJAX POST requests |
| Rate limiting | 5 requests/min/IP on public POST endpoints |
| Privacy-first | Email never stored, logged, or cached |
| Security headers | `SecurityHeaders` middleware (X-Frame-Options, CSP, X-Content-Type-Options, Referrer-Policy) |
| Access control | `AdminMiddleware` for role-based admin gate |
| Session hardening | Encrypted sessions, HttpOnly + SameSite cookies |
| Input sanitization | Whitelist validation + regex filtering on all public inputs |
| Safe subprocess | `Symfony\Component\Process\Process` instead of `shell_exec` |

---

## Artisan Commands

```bash
# Send CVE alert emails to subscribed users
php artisan onleaked:send-alerts

# Utilities
php artisan migrate:fresh --seed   # Reset DB with seed data
php artisan queue:work             # Process queued jobs
php artisan tinker                 # Interactive REPL
```

---

## License

Proprietary — © 2025 Nealix. All rights reserved.
