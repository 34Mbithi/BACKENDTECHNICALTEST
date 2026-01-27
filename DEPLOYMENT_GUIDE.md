# Deployment Guide

This guide covers deployment options for your Laravel API on various hosting platforms.

## Pre-Deployment Checklist

Before deploying, ensure:

- ✅ All tests pass: `php artisan test`
- ✅ `.env` is configured for production
- ✅ `APP_KEY` is set (run `php artisan key:generate`)
- ✅ Database is migrated
- ✅ Storage is linked for public files
- ✅ Composer dependencies are installed (`composer install --no-dev`)
- ✅ Git is initialized and committed

## Deployment Options

---

## Option 1: Shared Hosting (cPanel / Plesk)

### Requirements
- PHP 8.1+
- MySQL/PostgreSQL
- SSH access
- Composer support

### Steps

1. **Upload files via FTP/SFTP**
   ```bash
   # Exclude these directories:
   # - node_modules/
   # - .git/
   # - vendor/ (will reinstall)
   # - storage/logs/ (will recreate)
   ```

2. **SSH into server**
   ```bash
   ssh user@yourdomain.com
   cd public_html/your-api
   ```

3. **Install dependencies**
   ```bash
   composer install --no-dev --optimize-autoloader
   ```

4. **Configure environment**
   ```bash
   cp .env.example .env
   php artisan key:generate
   # Edit .env with production database credentials
   nano .env
   ```

5. **Set permissions**
   ```bash
   chmod -R 755 storage bootstrap/cache
   chmod -R 644 storage bootstrap/cache/*
   chown -R www-data:www-data /path/to/app
   ```

6. **Run migrations**
   ```bash
   php artisan migrate --force
   php artisan db:seed --force
   ```

7. **Link storage (for thumbnails)**
   ```bash
   php artisan storage:link
   ```

8. **Configure web server**
   
   **Apache** — Create `.htaccess` in `/public`:
   ```apache
   <IfModule mod_rewrite.c>
       <IfModule mod_negotiation.c>
           Options -MultiViews -Indexes
       </IfModule>

       RewriteEngine On

       # Handle Authorization Header
       RewriteCond %{HTTP:Authorization} .
       RewriteRule .* - [E=HTTP_AUTHORIZATION:%{HTTP:Authorization}]

       # Redirect Trailing Slashes If Not A Folder...
       RewriteCond %{REQUEST_FILENAME} !-d
       RewriteCond %{REQUEST_URI} (.+)/$
       RewriteRule ^ %1 [L,R=301]

       # Handle Front Controller...
       RewriteCond %{REQUEST_FILENAME} !-d
       RewriteCond %{REQUEST_FILENAME} !-f
       RewriteRule ^ index.php [L]
   </IfModule>
   ```

   **Nginx** — Configure in server block:
   ```nginx
   server {
       listen 80;
       server_name yourdomain.com;
       root /var/www/your-api/public;

       index index.php index.html;

       location / {
           try_files $uri $uri/ /index.php?$query_string;
       }

       location ~ \.php$ {
           fastcgi_pass unix:/var/run/php/php8.1-fpm.sock;
           fastcgi_index index.php;
           fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
           include fastcgi_params;
       }

       location ~ /\.(?!well-known).* {
           deny all;
       }
   }
   ```

9. **Optimize for production**
   ```bash
   php artisan config:cache
   php artisan route:cache
   php artisan view:cache
   ```

---

## Option 2: Cloud Platforms

### Heroku

**Requirements:** Git + Heroku CLI

**Steps:**

1. **Create Procfile**
   ```
   web: vendor/bin/heroku-php-apache2 public/
   ```

2. **Push to Heroku**
   ```bash
   heroku create your-api-name
   heroku config:set APP_KEY=$(php artisan key:generate --show)
   git push heroku main
   ```

3. **Run migrations**
   ```bash
   heroku run php artisan migrate --seed
   heroku run php artisan storage:link
   ```

---

### AWS (EC2 + RDS)

**Requirements:** AWS account, EC2 instance (t2.micro eligible), RDS database

**Steps:**

1. **Launch EC2 instance** (Ubuntu 20.04+)
2. **Install dependencies**
   ```bash
   sudo apt update
   sudo apt install php8.1 php8.1-cli php8.1-curl php8.1-mysql php8.1-xml composer nginx -y
   ```

3. **Clone repository**
   ```bash
   git clone https://github.com/yourusername/your-repo.git
   cd your-repo
   composer install --no-dev --optimize-autoloader
   ```

4. **Configure .env** with RDS credentials
5. **Run migrations and cache**
   ```bash
   php artisan migrate --force
   php artisan config:cache
   php artisan route:cache
   ```

6. **Configure Nginx** (use config from Option 1)

7. **Setup SSL** (with Certbot)
   ```bash
   sudo apt install certbot python3-certbot-nginx
   sudo certbot --nginx -d yourdomain.com
   ```

---

### DigitalOcean / Linode / Vultr

Similar to AWS:

1. Create Droplet (Ubuntu 20.04+, $6/month)
2. SSH and follow AWS steps above
3. Use SSL certificate (Let's Encrypt via Certbot)
4. Use DigitalOcean Managed Databases for scalability

---

### Google Cloud Platform (App Engine / Cloud Run)

**Steps for Cloud Run:**

1. **Create app.yaml**
   ```yaml
   runtime: php81
   env: flex
   
   env_variables:
     APP_LOG: stackdriver
   ```

2. **Deploy**
   ```bash
   gcloud app deploy
   ```

---

## Option 3: Platform-as-a-Service (PaaS)

### Laravel Forge

1. Create server on Forge
2. Connect GitHub repository
3. Forge auto-deploys on git push
4. Handles SSL, monitoring, backups

### Vapor (AWS Lambda)

1. Install Vapor: `composer require laravel/vapor`
2. Configure `vapor.yml`
3. Deploy: `vapor deploy production`

---

## Post-Deployment Checklist

After deployment:

- ✅ Test login: `POST /api/v1/auth/login`
- ✅ Test products: `GET /api/v1/products`
- ✅ Verify file uploads work
- ✅ Check logs: `tail -f storage/logs/laravel.log`
- ✅ Monitor with uptime checker
- ✅ Set up error tracking (Sentry recommended)
- ✅ Configure backups (automated)
- ✅ Set `APP_DEBUG=false` in production
- ✅ Enable HTTPS only

---

## Production Environment Variables

Update `.env` for production:

```dotenv
APP_ENV=production
APP_DEBUG=false
APP_URL=https://yourdomain.com

DB_CONNECTION=mysql
DB_HOST=your-db-host
DB_PORT=3306
DB_DATABASE=your_db
DB_USERNAME=your_user
DB_PASSWORD=your_secure_password

FILESYSTEM_DISK=public

LOG_CHANNEL=stack
LOG_LEVEL=warning

CACHE_DRIVER=redis
QUEUE_CONNECTION=database

MAIL_FROM_ADDRESS=noreply@yourdomain.com
```

---

## Database Migration & Seeding

```bash
# Migrate only (no seed)
php artisan migrate --force

# Migrate + seed (populate with test data)
php artisan migrate --seed --force

# Rollback last batch
php artisan migrate:rollback --force
```

---

## Monitoring & Maintenance

### Enable Laravel Telescope (optional, dev only)
```bash
php artisan telescope:install
php artisan migrate
```

### Monitor logs
```bash
tail -f storage/logs/laravel.log
```

### Setup cron for queue jobs
```bash
* * * * * cd /path/to/app && php artisan schedule:run >> /dev/null 2>&1
```

---

## Recommended Hosting

| Platform | Cost | Difficulty | Scalability |
|----------|------|------------|-------------|
| Shared Hosting | $5-15/mo | Easy | Low |
| DigitalOcean | $6-24/mo | Medium | High |
| AWS | Pay-as-you-go | Hard | Very High |
| Heroku | $7-50/mo | Easy | Medium |
| Laravel Forge | $12.99/mo | Easy | High |
| Vapor | Pay-as-you-go | Hard | Very High |

---

## Quick Deploy Checklist

For **immediate deployment**, try **DigitalOcean** or **Heroku**:

### Heroku (fastest)
```bash
heroku create
heroku config:set APP_KEY=$(php artisan key:generate --show)
git push heroku main
heroku run php artisan migrate --seed
```

### DigitalOcean (most control)
```bash
# Create $6/mo droplet
# SSH in and run AWS steps above
```

---

## Questions?

If you want step-by-step help with a specific platform, let me know and I'll guide you through deployment!
