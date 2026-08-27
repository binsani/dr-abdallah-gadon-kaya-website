# Dr. Abdallah Usman Gadon Kaya — Official Website

The official bilingual website and content-management system for Dr. Abdallah Usman Gadon Kaya. Built with Laravel 12, Blade, MySQL, and Bootstrap 5, the application is designed to run on conventional shared cPanel hosting without Redis or a permanently running queue worker.

## Features

### Public website

- English and Hausa navigation with a session-based language switcher
- Homepage featuring selected videos, recent articles, and upcoming events
- Searchable and filterable YouTube video library
- Articles, audio lectures, events, gallery albums, books, and biography timeline
- Dedicated TikTok and Facebook pages linked to the configured official accounts
- Site-wide search across articles, videos, and audio
- Contact form and dynamically generated XML sitemap
- Editable identity, homepage copy, biography, contact details, social links, branding images, and analytics ID

### Administration

- Role-protected dashboard for `super_admin` and `editor` accounts
- Content management for videos, articles, audio, events, gallery albums, books, and timeline entries
- Gallery image management and an official-channel gallery importer
- YouTube synchronization from the Data API or the public RSS fallback
- Contact-message inbox, shared media library, and activity log
- Super-admin-only user management
- Draft, published, and scheduled article states
- Featured and hidden controls for videos
- MIME- and size-validated uploads for images, MP3 audio, and PDF books
- Sanitization of active script and embedded HTML before rich text is stored

## Technology

- PHP 8.2 or newer
- Laravel 12 and Blade
- MySQL 8 for production
- Bootstrap 5.3 loaded from jsDelivr for the public and admin interfaces
- TinyMCE 7 loaded from its CDN for rich-text editing
- Tailwind CSS 3 and Vite 6 development tooling
- PHPUnit 11, with GitHub Actions testing PHP 8.2, 8.3, and 8.4

## Local installation

Install PHP 8.2+, Composer 2, MySQL 8, Node.js/npm, and the PHP extensions required by Laravel.

```bash
composer install
npm install
```

Copy `.env.example` to `.env`, then configure the application URL, MySQL connection, mail transport, and any YouTube credentials.

```bash
php artisan key:generate
php artisan migrate --seed
php artisan storage:link
```

Start the application with either:

```bash
composer run dev
```

or, when frontend asset rebuilding is not needed:

```bash
php artisan serve
```

Open the address printed by Laravel. The administration login is available at `/admin/login`.

## Seeded administrator

The database seeder creates the following super administrator:

- Email: `admin@example.com`
- Password: the value of `ADMIN_PASSWORD` in `.env`, or `ChangeMeNow!` when that variable is absent

Set a strong `ADMIN_PASSWORD` before seeding a real installation and change the password immediately after the first login.

## YouTube and gallery synchronization

The official channel ID is included in `.env.example`. Run:

```bash
php artisan youtube:sync
```

When `YOUTUBE_API_KEY` is configured, the command imports up to 200 recent uploads with durations and view counts through YouTube Data API v3. Without a key—or when the API request fails—it falls back to the channel's public RSS feed.

The command also accepts downloaded source data for offline imports:

```bash
php artisan youtube:sync --feed-file=/path/to/feed.xml
php artisan youtube:sync --flat-file=/path/to/videos.tsv
```

After videos have been imported, populate the official gallery from their thumbnails with:

```bash
php artisan gallery:sync-official
```

Laravel's scheduler runs the YouTube sync every six hours, refreshes the official gallery daily, and drains the database queue every minute in short, shared-hosting-friendly runs.

## Testing

```bash
php artisan test
```

The feature suite uses SQLite in memory and covers public pages, authentication and role protection, content management, uploads, contact messages, localization, YouTube imports, and security-sensitive flows.

## Content and localization

Interface translations live in `lang/en` and `lang/ha`. Article records also include a JSON `translations` field so additional long-form translations can be stored without changing the database schema. Categories include a content `type` to prevent collisions as the CMS grows.

## Production deployment

For production, configure at least:

```dotenv
APP_ENV=production
APP_DEBUG=false
APP_URL=https://example.com
APP_FORCE_HTTPS=true
SESSION_SECURE_COOKIE=true
QUEUE_CONNECTION=database
FILESYSTEM_DISK=public
```

Use a unique `APP_KEY`, strong database credentials, a real mail transport, and a restricted YouTube API key when API synchronization is enabled. Point the domain document root to Laravel's `public` directory, create the storage link, cache the application configuration, and run `php artisan schedule:run` once per minute from cron.

The complete shared-hosting procedure is in [`deploy/README-CPANEL.md`](deploy/README-CPANEL.md). Day-to-day publishing instructions are in [`docs/ADMIN-GUIDE.md`](docs/ADMIN-GUIDE.md).

## Security notes

- Public registration is disabled.
- Admin login and contact submissions are rate-limited.
- Admin routes enforce authenticated role checks.
- The YouTube API key is encrypted when saved through Site Settings.
- Executable uploads are not accepted.
- Existing content remains available when an external synchronization fails.

Never commit `.env`, credentials, database exports, or private uploaded files.

## Third-party software

- Laravel Framework — MIT
- Laravel Breeze — MIT
- Bootstrap — MIT
- Tailwind CSS — MIT
- TinyMCE Community — GPLv2-or-later
- Google Fonts (DM Sans and Libre Baskerville) — SIL Open Font License

Production requests are served by PHP and Laravel. Node.js is used for local frontend development and asset builds, but Redis, Supervisor, and a continuously running queue worker are not required for the documented cPanel deployment.
