# VPS Setup Guide — Tabungan Masa Depan (BizShare)

**Target:** Ubuntu 24.04 LTS  
**Stack:** PHP 8.4 · Laravel 13 · SQLite · Nginx · Supervisor

---

## Table of Contents

1. [Initial Server Setup](#1-initial-server-setup)
2. [Install PHP 8.4](#2-install-php-84)
3. [Install Nginx](#3-install-nginx)
4. [Install Composer](#4-install-composer)
5. [Install Node.js (for asset build)](#5-install-nodejs)
6. [Deploy the Application](#6-deploy-the-application)
7. [Configure Environment](#7-configure-environment)
8. [Run Migrations](#8-run-migrations)
9. [Configure Nginx Virtual Host](#9-configure-nginx-virtual-host)
10. [SSL with Let's Encrypt](#10-ssl-with-lets-encrypt)
11. [Queue Worker with Supervisor](#11-queue-worker-with-supervisor)
12. [Scheduled Commands (Cron)](#12-scheduled-commands-cron)
13. [Storage & Permissions](#13-storage--permissions)
14. [Mail Configuration](#14-mail-configuration)
15. [Midtrans Webhook](#15-midtrans-webhook)
16. [Post-Deploy Checklist](#16-post-deploy-checklist)
17. [Updating the App](#17-updating-the-app)

---

## 1. Initial Server Setup

Log in as root and create a deploy user:

```bash
# Update system
apt update && apt upgrade -y

# Create deploy user
adduser deployer
usermod -aG sudo deployer

# Copy SSH key to new user (run from your local machine)
ssh-copy-id deployer@YOUR_SERVER_IP
```

Log out and log back in as `deployer` for all remaining steps.

---

## 2. Install PHP 8.4

```bash
sudo apt install -y software-properties-common
sudo add-apt-repository ppa:ondrej/php -y
sudo apt update

sudo apt install -y \
  php8.4 \
  php8.4-fpm \
  php8.4-cli \
  php8.4-sqlite3 \
  php8.4-mbstring \
  php8.4-xml \
  php8.4-curl \
  php8.4-zip \
  php8.4-bcmath \
  php8.4-intl \
  php8.4-gd \
  php8.4-fileinfo \
  php8.4-tokenizer \
  php8.4-pdo
```

Verify:

```bash
php -v
# PHP 8.4.x ...
```

### Tune PHP-FPM

```bash
sudo nano /etc/php/8.4/fpm/pool.d/www.conf
```

Change the user/group to your deploy user:

```ini
user = deployer
group = deployer
listen.owner = deployer
listen.group = deployer
```

Restart:

```bash
sudo systemctl restart php8.4-fpm
sudo systemctl enable php8.4-fpm
```

---

## 3. Install Nginx

```bash
sudo apt install -y nginx
sudo systemctl enable nginx
sudo systemctl start nginx
```

---

## 4. Install Composer

```bash
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer
composer --version
```

---

## 5. Install Node.js

Only needed once to build front-end assets (Vite/Filament):

```bash
curl -fsSL https://deb.nodesource.com/setup_20.x | sudo -E bash -
sudo apt install -y nodejs
node -v   # v20.x
npm -v
```

---

## 6. Deploy the Application

### 6a. Clone the repository

```bash
sudo mkdir -p /var/www
sudo chown deployer:deployer /var/www

cd /var/www
git clone https://github.com/YOUR_ORG/bizhare-app.git bizhare
cd bizhare
```

### 6b. Install PHP dependencies

```bash
composer install --no-dev --optimize-autoloader
```

### 6c. Build front-end assets

```bash
npm ci
npm run build
```

You can remove Node.js after the build if you want to keep the server lean — all that matters is the compiled `public/build/` output.

---

## 7. Configure Environment

```bash
cp .env.example .env
nano .env
```

Set every value below:

```dotenv
APP_NAME="Tabungan Masa Depan"
APP_ENV=production
APP_KEY=                        # will be generated next
APP_DEBUG=false
APP_URL=https://yourdomain.com

APP_LOCALE=id
APP_FALLBACK_LOCALE=en

LOG_CHANNEL=stack
LOG_LEVEL=warning

# ─── Database ──────────────────────────────────────────────────────────────
DB_CONNECTION=sqlite
# DB_DATABASE is auto-resolved to database/database.sqlite

# ─── Cache / Session / Queue ───────────────────────────────────────────────
CACHE_STORE=database
SESSION_DRIVER=database
QUEUE_CONNECTION=database          # uses jobs table; switch to redis if needed

# ─── Storage ───────────────────────────────────────────────────────────────
FILESYSTEM_DISK=public

# ─── Mail ──────────────────────────────────────────────────────────────────
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io          # replace with your SMTP provider
MAIL_PORT=587
MAIL_USERNAME=your_smtp_user
MAIL_PASSWORD=your_smtp_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="noreply@yourdomain.com"
MAIL_FROM_NAME="Tabungan Masa Depan"

# ─── Midtrans ──────────────────────────────────────────────────────────────
MIDTRANS_SERVER_KEY=Mid-server-xxxxxxxxxxxx
MIDTRANS_CLIENT_KEY=Mid-client-xxxxxxxxxxxx
MIDTRANS_IS_PRODUCTION=true

# ─── Admin Seed ────────────────────────────────────────────────────────────
ADMIN_NAME="Super Admin"
ADMIN_EMAIL="admin@yourdomain.com"
ADMIN_PASSWORD="ganti_dengan_password_kuat"
```

Generate the app key:

```bash
php artisan key:generate
```

---

## 8. Run Migrations

### 8a. Create SQLite database file

```bash
touch database/database.sqlite
```

### 8b. Run all migrations (creates tables + seeds admin user + system settings)

```bash
php artisan migrate --force
```

> The admin account and all default system settings are created automatically by the migration.

### 8c. Create storage symlink

```bash
php artisan storage:link
```

---

## 9. Configure Nginx Virtual Host

```bash
sudo nano /etc/nginx/sites-available/bizhare
```

Paste the following (replace `yourdomain.com`):

```nginx
server {
    listen 80;
    server_name yourdomain.com www.yourdomain.com;
    return 301 https://$host$request_uri;
}

server {
    listen 443 ssl http2;
    server_name yourdomain.com www.yourdomain.com;

    root /var/www/bizhare/public;
    index index.php;

    # SSL — will be filled by Certbot (step 10)
    ssl_certificate     /etc/letsencrypt/live/yourdomain.com/fullchain.pem;
    ssl_certificate_key /etc/letsencrypt/live/yourdomain.com/privkey.pem;
    include             /etc/letsencrypt/options-ssl-nginx.conf;
    ssl_dhparam         /etc/letsencrypt/ssl-dhparams.pem;

    # Security headers
    add_header X-Frame-Options           "SAMEORIGIN"   always;
    add_header X-Content-Type-Options    "nosniff"      always;
    add_header Referrer-Policy           "strict-origin-when-cross-origin" always;
    add_header X-XSS-Protection          "1; mode=block" always;

    # Gzip
    gzip on;
    gzip_types text/plain text/css application/json application/javascript text/xml application/xml;

    # Larger upload for ID photos (5 MB + overhead)
    client_max_body_size 12M;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    # Cache static assets
    location ~* \.(css|js|jpg|jpeg|png|gif|ico|svg|woff2?)$ {
        expires 1y;
        add_header Cache-Control "public, immutable";
        try_files $uri =404;
    }

    location ~ \.php$ {
        include        fastcgi_params;
        fastcgi_pass   unix:/var/run/php/php8.4-fpm.sock;
        fastcgi_index  index.php;
        fastcgi_param  SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        fastcgi_read_timeout 300;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }

    # Block direct access to sensitive directories
    location ~ ^/(\.env|storage|database|bootstrap/cache) {
        deny all;
    }

    error_log  /var/log/nginx/bizhare_error.log;
    access_log /var/log/nginx/bizhare_access.log;
}
```

Enable it:

```bash
sudo ln -s /etc/nginx/sites-available/bizhare /etc/nginx/sites-enabled/
sudo nginx -t          # must say "syntax is ok"
sudo systemctl reload nginx
```

---

## 10. SSL with Let's Encrypt

```bash
sudo apt install -y certbot python3-certbot-nginx

# Issue certificate (point DNS to this server first)
sudo certbot --nginx -d yourdomain.com -d www.yourdomain.com

# Verify auto-renewal
sudo certbot renew --dry-run
```

---

## 11. Queue Worker with Supervisor

Notifications (email + database) are queued. A Supervisor worker must be running to process them.

```bash
sudo apt install -y supervisor
```

Create the worker config:

```bash
sudo nano /etc/supervisor/conf.d/bizhare-worker.conf
```

```ini
[program:bizhare-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/bizhare/artisan queue:work database --sleep=3 --tries=3 --timeout=90 --max-time=3600
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=deployer
numprocs=2
redirect_stderr=true
stdout_logfile=/var/www/bizhare/storage/logs/worker.log
stopwaitsecs=3600
```

Apply:

```bash
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start bizhare-worker:*
sudo supervisorctl status
```

---

## 12. Scheduled Commands (Cron)

The app includes an installment reminder that runs daily via `php artisan schedule:run`.

```bash
crontab -e
```

Add this line:

```cron
* * * * * cd /var/www/bizhare && php artisan schedule:run >> /dev/null 2>&1
```

---

## 13. Storage & Permissions

```bash
cd /var/www/bizhare

# Directories writable by PHP-FPM
sudo chown -R deployer:www-data storage bootstrap/cache
sudo chmod -R 775 storage bootstrap/cache

# SQLite database must be writable
sudo chown deployer:www-data database/database.sqlite
sudo chmod 664 database/database.sqlite
sudo chown deployer:www-data database/
sudo chmod 775 database/
```

---

## 14. Mail Configuration

All notifications (top-up, payment, withdrawal, referral, profit) are sent via email. The recommended setup for production:

**Option A — Mailgun (recommended)**

```dotenv
MAIL_MAILER=mailgun
MAILGUN_DOMAIN=mg.yourdomain.com
MAILGUN_SECRET=key-xxxxxxxxxxxx
MAIL_FROM_ADDRESS="noreply@yourdomain.com"
```

Install the Mailgun driver:

```bash
composer require symfony/mailgun-mailer symfony/http-client
```

**Option B — SMTP (e.g. Google Workspace / Brevo / SendGrid)**

```dotenv
MAIL_MAILER=smtp
MAIL_HOST=smtp.brevo.com
MAIL_PORT=587
MAIL_USERNAME=your@email.com
MAIL_PASSWORD=smtp_password
MAIL_ENCRYPTION=tls
```

Test mail:

```bash
php artisan tinker
Mail::raw('Test email', fn($m) => $m->to('you@example.com')->subject('Test'));
```

---

## 15. Midtrans Webhook

Midtrans calls `POST /api/payments/midtrans/callback` to confirm QRIS payments.

1. Log in to [Midtrans Dashboard](https://dashboard.midtrans.com)
2. Go to **Settings → Configuration**
3. Set **Payment Notification URL** to: `https://yourdomain.com/api/payments/midtrans/callback`
4. Set **Finish / Unfinish / Error Redirect URL** to your app URL

> Make sure `APP_URL` in `.env` matches the domain registered in Midtrans, otherwise signature verification will fail.

Switch to production keys when ready:

```dotenv
MIDTRANS_SERVER_KEY=Mid-server-xxxxxxxxxxxx   # production key (no SB- prefix)
MIDTRANS_CLIENT_KEY=Mid-client-xxxxxxxxxxxx
MIDTRANS_IS_PRODUCTION=true
```

---

## 16. Post-Deploy Checklist

Run these after every fresh deployment:

```bash
cd /var/www/bizhare

# Optimise for production
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache
php artisan icons:cache       # Filament icon cache
php artisan filament:cache-components

# Confirm queue workers are running
sudo supervisorctl status bizhare-worker:*

# Confirm scheduler is registered
php artisan schedule:list
```

Verify the site is up:

```bash
curl -I https://yourdomain.com        # should return 200
curl -I https://yourdomain.com/admin  # should redirect to login
```

---

## 17. Updating the App

Use this script every time you push new code:

```bash
cd /var/www/bizhare

# 1. Pull latest code
git pull origin main

# 2. Install/update dependencies
composer install --no-dev --optimize-autoloader

# 3. Build assets (skip if no JS/CSS changes)
npm ci && npm run build

# 4. Run new migrations
php artisan migrate --force

# 5. Clear and re-cache everything
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan icons:cache

# 6. Restart queue workers to pick up new code
sudo supervisorctl restart bizhare-worker:*
```

> **Tip:** Save the above block as `/var/www/bizhare/deploy.sh`, run `chmod +x deploy.sh`, then deploy with `./deploy.sh`.

---

## Quick Reference

| What | Command |
|---|---|
| View application logs | `tail -f storage/logs/laravel.log` |
| View queue worker logs | `tail -f storage/logs/worker.log` |
| View Nginx error logs | `sudo tail -f /var/log/nginx/bizhare_error.log` |
| Restart PHP-FPM | `sudo systemctl restart php8.4-fpm` |
| Restart Nginx | `sudo systemctl reload nginx` |
| Restart queue workers | `sudo supervisorctl restart bizhare-worker:*` |
| Open Tinker REPL | `php artisan tinker` |
| Check failed jobs | `php artisan queue:failed` |
| Retry failed jobs | `php artisan queue:retry all` |
| Flush failed jobs | `php artisan queue:flush` |
| Admin panel URL | `https://yourdomain.com/admin` |

---

## Default Admin Credentials

Set in `.env` before running migrations:

| Field | Default (change immediately) |
|---|---|
| Email | `admin@yourdomain.com` |
| Password | value of `ADMIN_PASSWORD` in `.env` |

> Change the admin password immediately after first login via **Admin Panel → Profile**.
