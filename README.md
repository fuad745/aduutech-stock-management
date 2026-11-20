# Aduu Tech Inventory Management System

A modern inventory management system built with Laravel and Filament PHP.

## Server Requirements

- PHP 8.2 or higher
- Composer 2.0 or higher
- Node.js 18+ & NPM 9+
- MySQL 8.0+ / PostgreSQL / SQLite
- Web server (Nginx/Apache)
- Redis (recommended for production)

## Deployment Instructions

### 1. Clone the Repository

```bash
git clone [your-repository-url] atsm
cd atsm
```

### 2. Install Dependencies

```bash
composer install --optimize-autoloader --no-dev
npm ci
```

### 3. Environment Setup

```bash
cp .env.example .env
php artisan key:generate
```

Edit `.env` with your production settings:

```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-domain.com

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=your_database
DB_USERNAME=your_username
DB_PASSWORD=your_secure_password

CACHE_DRIVER=redis
SESSION_DRIVER=redis
QUEUE_CONNECTION=redis

# For file uploads
FILESYSTEM_DISK=public
```

### 4. Database Setup

```bash
php artisan migrate --force
# Optional: Run seeders if needed
# php artisan db:seed --force
```

### 5. Storage and Cache

```bash
# Create storage symlink for uploaded files
php artisan storage:link

# Cache configuration
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache
```

### 6. Build Frontend Assets

```bash
npm run build
```

### 7. Set Filesystem Permissions

```bash
chmod -R 755 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
```

### 8. Queue Worker (for async jobs)

Set up a process manager like Supervisor to keep the queue worker running:

```ini
[program:atsm-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /path/to/atsm/artisan queue:work --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www-data
numprocs=2
redirect_stderr=true
stdout_logfile=/path/to/atsm/storage/logs/worker.log
stopwaitsecs=3600
```

### 9. Web Server Configuration (Nginx Example)

```nginx
server {
    listen 80;
    server_name your-domain.com;
    root /path/to/atsm/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-XSS-Protection "1; mode=block";
    add_header X-Content-Type-Options "nosniff";

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

### 10. SSL Certificate (Let's Encrypt)

```bash
sudo certbot --nginx -d your-domain.com
```

## Scheduled Tasks

Add this to your server's crontab:

```bash
* * * * * cd /path/to/atsm && php artisan schedule:run >> /dev/null 2>&1
```

## Development

For local development:

```bash
# Install dev dependencies
composer install
npm install

# Start development servers
php artisan serve
npm run dev
```

## Environment Variables

| Key | Description | Example |
|-----|-------------|---------|
| `APP_ENV` | Application environment | `production` |
| `APP_DEBUG` | Debug mode | `false` |
| `APP_URL` | Application URL | `https://example.com` |
| `DB_*` | Database configuration | - |
| `CACHE_DRIVER` | Cache driver | `redis` |
| `SESSION_DRIVER` | Session driver | `redis` |
| `QUEUE_CONNECTION` | Queue driver | `redis` |
| `MAIL_*` | Email configuration | - |
| `FILESYSTEM_DISK` | Default filesystem disk | `public` |

## Troubleshooting

- **File Uploads**: Ensure `storage` and `bootstrap/cache` directories are writable
- **Queue Workers**: Check logs at `storage/logs/worker.log`
- **Application Logs**: Check `storage/logs/laravel.log`

## Roles & Production Setup

### Role-Based Access Control

Install the required packages:

```bash
composer require spatie/laravel-permission
composer require filament/shield
php artisan vendor:publish --provider="Spatie\Permission\PermissionServiceProvider"
php artisan migrate
php artisan shield:install --fresh
php artisan shield:generate --all
```

Then seed the roles:

```bash
php artisan db:seed --class=RoleSeeder
```

Three roles are defined:

| Role | Permissions |
|------|-------------|
| **Admin** | Full access — create, edit, delete products & batches |
| **Storekeeper** | Create and edit products & batches, adjust stock. Cannot delete. |
| **Viewer** | Read-only access to products and batches |

The `admin@aduutech.com` user is automatically assigned the Admin role by the seeder.

### Backups

Install `spatie/laravel-backup` for automated off-site backups:

```bash
composer require spatie/laravel-backup
php artisan vendor:publish --provider="Spatie\LaravelBackup\BackupServiceProvider"
```

Configure `AWS_*` and `BACKUP_DISK` in `.env`. Add to crontab:

```bash
* * * * * cd /path/to/app && php artisan schedule:run >> /dev/null 2>&1
```

**Always test a restore before going live.**

## License

This project is proprietary software.
