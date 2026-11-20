# Aduu Tech Inventory Management System

Medical-grade inventory management for Aduu Tech — lot/batch tracking, FEFO picking, recall traceability, expiry alerts, and role-based access. Built with Laravel 12 + Filament 4.

---

## Table of Contents

1. [Server Requirements](#server-requirements)
2. [Production Deployment](#production-deployment)
3. [First Login & Role Setup](#first-login--role-setup)
4. [Nginx Configuration](#nginx-configuration)
5. [Queue Worker (Supervisor)](#queue-worker-supervisor)
6. [Scheduled Tasks](#scheduled-tasks)
7. [Backups](#backups)
8. [Local Development](#local-development)
9. [Environment Variables](#environment-variables)
10. [Roles & Permissions](#roles--permissions)
11. [Troubleshooting](#troubleshooting)

---

## Server Requirements

| Requirement | Minimum |
|-------------|---------|
| PHP | 8.2+ |
| Composer | 2.0+ |
| Node.js / NPM | 18+ / 9+ |
| MySQL | 8.0+ (SQLite for local dev only) |
| Web server | Nginx or Apache |
| Redis | Recommended for production |

---

## Production Deployment

### 1. Clone the repository

```bash
git clone https://github.com/your-org/aduutech-stock-management.git /var/www/atsm
cd /var/www/atsm
```

### 2. Install dependencies

```bash
composer install --optimize-autoloader --no-dev
npm ci
npm run build
```

### 3. Configure environment

```bash
cp .env.example .env
php artisan key:generate
```

Open `.env` and set at minimum:

```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-domain.com

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=aduutech_stock
DB_USERNAME=your_db_user
DB_PASSWORD=your_db_password

CACHE_STORE=redis
SESSION_DRIVER=redis
QUEUE_CONNECTION=redis

FILESYSTEM_DISK=public

REDIS_HOST=127.0.0.1
REDIS_PORT=6379
```

### 4. Run migrations and seed

```bash
# Run all migrations (creates products, batches, customers, stock movements, stock count tables)
php artisan migrate --force

# Seed the admin account and roles
php artisan db:seed --force
```

> This creates the default admin user and the Admin / Storekeeper / Viewer roles.
> See [First Login & Role Setup](#first-login--role-setup) for credentials.

### 5. Storage and caching

```bash
# Create the storage symlink for uploaded product images
php artisan storage:link

# Cache all config, routes, views, and events for production speed
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache
```

### 6. Set filesystem permissions

```bash
chmod -R 755 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
```

### 7. Install role-based access control (RBAC)

```bash
composer require spatie/laravel-permission
php artisan vendor:publish --provider="Spatie\Permission\PermissionServiceProvider"
php artisan migrate --force

# Optional: install Filament Shield for fine-grained resource permissions
composer require filament/shield
php artisan shield:install --fresh
php artisan shield:generate --all
```

Then re-run the seeder to assign roles:

```bash
php artisan db:seed --class=RoleSeeder
```

---

## First Login & Role Setup

After seeding, log in at `https://your-domain.com/admin` with:

| Field | Value |
|-------|-------|
| Email | `admin@aduutech.com` |
| Password | `Admin@2025!` |

**Change the password immediately after first login.**

The admin account is automatically assigned the **Admin** role by the seeder. Create additional users from the admin panel and assign them Storekeeper or Viewer roles as needed.

---

## Nginx Configuration

```nginx
server {
    listen 80;
    server_name your-domain.com;
    return 301 https://$host$request_uri;
}

server {
    listen 443 ssl;
    server_name your-domain.com;
    root /var/www/atsm/public;

    ssl_certificate     /etc/letsencrypt/live/your-domain.com/fullchain.pem;
    ssl_certificate_key /etc/letsencrypt/live/your-domain.com/privkey.pem;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";
    add_header X-XSS-Protection "1; mode=block";

    index index.php;
    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

**Get a free SSL certificate:**

```bash
sudo certbot --nginx -d your-domain.com
```

---

## Queue Worker (Supervisor)

The CSV batch importer runs as a queued job. Install Supervisor to keep the worker alive:

```bash
sudo apt install supervisor
```

Create `/etc/supervisor/conf.d/atsm-worker.conf`:

```ini
[program:atsm-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/atsm/artisan queue:work redis --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www-data
numprocs=2
redirect_stderr=true
stdout_logfile=/var/www/atsm/storage/logs/worker.log
stopwaitsecs=3600
```

```bash
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start atsm-worker:*
```

---

## Scheduled Tasks

Add to the `www-data` crontab (`sudo crontab -u www-data -e`):

```bash
* * * * * cd /var/www/atsm && php artisan schedule:run >> /dev/null 2>&1
```

This drives expiry alert notifications and backup scheduling.

---

## Backups

Install `spatie/laravel-backup` for automated off-site backups:

```bash
composer require spatie/laravel-backup
php artisan vendor:publish --provider="Spatie\LaravelBackup\BackupServiceProvider"
```

Add to `.env`:

```env
BACKUP_DISK=s3
AWS_ACCESS_KEY_ID=your_key
AWS_SECRET_ACCESS_KEY=your_secret
AWS_DEFAULT_REGION=us-east-1
AWS_BUCKET=your-backup-bucket
```

The scheduler (via crontab above) will trigger nightly backups automatically.

> **Always test a restore before going live.**

---

## Local Development

```bash
git clone https://github.com/your-org/aduutech-stock-management.git
cd aduutech-stock-management

composer install
npm install

cp .env.example .env
# Set DB_CONNECTION=sqlite in .env for zero-config local dev

php artisan key:generate
php artisan migrate --force
php artisan db:seed --force
php artisan storage:link

# Run both servers in separate terminals
php artisan serve
npm run dev
```

Access the admin panel at `http://localhost:8000/admin`.

---

## Environment Variables

| Variable | Description | Production value |
|----------|-------------|-----------------|
| `APP_ENV` | Environment | `production` |
| `APP_DEBUG` | Debug mode | `false` |
| `APP_URL` | Full URL with https | `https://your-domain.com` |
| `DB_CONNECTION` | Database driver | `mysql` |
| `DB_DATABASE` | Database name | `aduutech_stock` |
| `CACHE_STORE` | Cache backend | `redis` |
| `SESSION_DRIVER` | Session backend | `redis` |
| `QUEUE_CONNECTION` | Queue backend | `redis` |
| `FILESYSTEM_DISK` | File storage | `public` |
| `BACKUP_DISK` | Backup destination | `s3` |

---

## Roles & Permissions

| Role | Products | Batches | Customers | Stock Adjust | Delete |
|------|----------|---------|-----------|--------------|--------|
| **Admin** | ✅ Full | ✅ Full | ✅ Full | ✅ | ✅ |
| **Storekeeper** | ✅ Create/Edit | ✅ Create/Edit | ✅ Create/Edit | ✅ | ❌ |
| **Viewer** | 👁 Read only | 👁 Read only | 👁 Read only | ❌ | ❌ |

Roles require `spatie/laravel-permission`. Without it, the panel defaults to **deny all** (fail-closed).

### CSV Batch Import

A sample import template is at `public/templates/batch-import-template.csv`. Download it from the Batches list page → Import Batches → Download Template. Required columns: `product_name`, `lot_number`, `expiry_date` (YYYY-MM-DD), `quantity`. Optional: `supplier`, `received_date`, `landed_cost`, `storage_condition`.

---

## Troubleshooting

| Problem | Fix |
|---------|-----|
| Blank page after deploy | Run `php artisan config:cache && php artisan view:cache` |
| Product images not showing | Run `php artisan storage:link` |
| CSV import stuck | Check queue worker is running: `supervisorctl status atsm-worker:*` |
| Login loop | Check `SESSION_DRIVER` and Redis connectivity |
| Migrations fail | Ensure DB user has `CREATE TABLE` privilege |
| Queue jobs failing | Check `storage/logs/worker.log` |
| Application errors | Check `storage/logs/laravel.log` |

---

## License

Proprietary — Aduu Tech. All rights reserved.
