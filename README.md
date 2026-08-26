# Dr. Abdallah Usman Gadon Kaya — Official Website

A Laravel 12, Blade and Bootstrap 5 website and content-management system designed for shared cPanel hosting. Laravel 11 was replaced during runtime verification because Composer blocks every permitted Laravel 11 release for current security advisories. It includes bilingual English/Hausa navigation, articles, YouTube and TikTok video experiences, audio, events, gallery albums, books, contact messages, editable page copy and branding, social links, an audit log, and role-protected administration.

## Local setup

Requirements: PHP 8.2+, Composer 2, MySQL 8, and the PHP extensions Laravel requires.

1. Run `composer install`.
2. Copy `.env.example` to `.env` and enter your MySQL and mail details.
3. Run `php artisan key:generate`.
4. Run `php artisan migrate --seed`.
5. Run `php artisan storage:link`.
6. Run `php artisan serve`, then open the displayed address.

The seeded administrator is `admin@example.com`. Its password is taken from `ADMIN_PASSWORD` in `.env`, or defaults to `ChangeMeNow!`; change it immediately in any real installation.

The official `YOUTUBE_CHANNEL_ID` is included in `.env.example`. Run `php artisan youtube:sync` to import the latest public uploads through YouTube's official RSS feed. Adding a `YOUTUBE_API_KEY` enables the fuller Data API sync, including a larger archive and durations; the site automatically falls back to RSS if the key is absent or the API is temporarily unavailable.

## Content architecture

Static interface text uses Laravel files in `lang/en` and `lang/ha`. Long-form multilingual content uses a JSON `translations` field so more languages can be added without altering the schema. Categories carry a `type` field, allowing one category table to serve articles and future content types without collisions.

## Production notes

- Public registration is intentionally disabled.
- Admin login and contact submission are rate-limited.
- Uploaded files are MIME and size validated; executable types are not accepted.
- Rich HTML is stripped of active script/embed elements before storage.
- Set `APP_ENV=production`, `APP_DEBUG=false`, `APP_FORCE_HTTPS=true`, secure mail credentials, and a unique `APP_KEY`.
- Complete shared-hosting instructions are in `deploy/README-CPANEL.md`.

## Third-party software

- Laravel Framework — MIT
- Laravel Breeze (development/auth scaffolding dependency) — MIT
- Bootstrap 5 — MIT
- TinyMCE Community via CDN — GPLv2-or-later; replace with CKEditor if your distribution policy requires a different license
- Google Fonts (DM Sans, Libre Baskerville) — SIL Open Font License

All are commercially usable. Production requires PHP/Composer only; it does not require Redis, Supervisor, Node.js, or an npm build.
