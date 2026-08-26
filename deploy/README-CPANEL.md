# Deploying on cPanel

This guide assumes a typical Apache cPanel account with PHP 8.2 or newer and MySQL.

## 1. Prepare the files

Preferred: build a release locally with `composer install --no-dev --optimize-autoloader`, then upload the entire project (including `vendor`) as a ZIP and extract it outside `public_html`, for example `/home/account/gadon-kaya`.

If cPanel Terminal and Composer are available, upload without `vendor`, open Terminal, change to the project folder, and run `composer install --no-dev --optimize-autoloader`.

Never upload a local `.env` containing secrets.

## 2. Point the domain to Laravel

In cPanel Domains, set the domain document root to `/home/account/gadon-kaya/public`. This is the secure and strongly preferred layout.

If the host will not change the document root, copy only the contents of the project's `public` directory to `public_html`. Edit `public_html/index.php` so its two paths point to the real project, for example:

```php
require __DIR__.'/../gadon-kaya/vendor/autoload.php';
$app = require_once __DIR__.'/../gadon-kaya/bootstrap/app.php';
```

Keep `app`, `vendor`, `.env`, and storage outside `public_html`. Do not expose the project root through a blanket redirect.

## 3. Create MySQL and `.env`

In MySQL Databases create a database and user, assign the user **All Privileges**, and remember cPanel prefixes both names. Copy `.env.example` to `.env` and set:

```text
APP_ENV=production
APP_DEBUG=false
APP_URL=https://example.com
APP_FORCE_HTTPS=true
DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=account_gadonkaya
DB_USERNAME=account_editor
DB_PASSWORD=your-strong-password
QUEUE_CONNECTION=database
CACHE_STORE=file
FILESYSTEM_DISK=public
SESSION_SECURE_COOKIE=true
```

Set mail values using the mailbox details shown under cPanel Email Accounts → Connect Devices. Generate `APP_KEY` locally with `php artisan key:generate --show`, then paste it into `.env`.

## 4. Database and caches

With Terminal, from the project directory run:

```text
php artisan migrate --force
php artisan db:seed --force
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

Without Terminal, run migrations and seeding on a matching local MySQL database, export it in phpMyAdmin, then import it into cPanel phpMyAdmin. Do not expose a public “run migration” web script.

## 5. Public uploads

With Terminal, run `php artisan storage:link`.

Without Terminal, use cPanel File Manager's link feature if available and create `public/storage` pointing to `/home/account/gadon-kaya/storage/app/public`. If symlinks are prohibited, ask hosting support to create it; this is safer than exposing arbitrary storage through a download route.

## 6. Permissions

Directories normally use `755` and files `644`. Ensure the cPanel account/PHP process can write to `storage` and `bootstrap/cache`; use `775` only if the host's group model requires it. Never use `777`.

## 7. Scheduler and queue

In cPanel Cron Jobs add this every minute (replace paths and select the correct PHP binary shown by your host):

```text
* * * * * /usr/local/bin/php /home/account/gadon-kaya/artisan schedule:run >> /dev/null 2>&1
```

The scheduler syncs YouTube every six hours and drains the database queue in short, shared-hosting-safe runs. It does not require Redis or Supervisor.

## 8. YouTube and final checks

Create a restricted YouTube Data API v3 key in Google Cloud, then enter the key and exact channel ID in Admin → Site Settings. Use **Sync YouTube now** once and confirm videos appear. Restrict the key to the YouTube Data API and the server IP where possible.

Finally verify HTTPS, admin login, contact email delivery, an image upload, `/sitemap.xml`, and the scheduled task log. Delete any uploaded ZIP archives and change the seeded administrator password immediately.
