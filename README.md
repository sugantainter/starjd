# StarJD

Laravel + Vue 3 + Vite + Tailwind CSS 4 — Influencer marketing landing page (Connect. Create. Collaborate.)

## Project structure (Laravel)

- **Backend:** Laravel 12 (PHP 8.2+)
- **Frontend:** Vue 3 SPA in `resources/js/App.vue`, built with Vite
- **Styles:** Tailwind CSS 4 in `resources/css/app.css`
- **Views:** Blade layout `resources/views/layouts/app.blade.php`, entry `resources/views/welcome.blade.php`
- **Routes:** `routes/web.php` — home route uses `HomeController@index` (named route: `home`)

## Requirements

- PHP 8.2+
- Composer
- Node.js 18+ and npm

## Setup

```bash
# Install PHP dependencies
composer install

# Copy environment and generate key
cp .env.example .env
php artisan key:generate

# Install frontend dependencies and build
npm install
npm run build
```

## Run the Laravel project

**Development (Laravel server + Vite dev):**

```bash
composer dev
```

This runs `php artisan serve`, queue, pail, and `npm run dev` together. Open [http://localhost:8000](http://localhost:8000).

**Or run separately:**

```bash
# Terminal 1: Laravel
php artisan serve

# Terminal 2: Vite (for hot reload)
npm run dev
```

**Production:**

```bash
npm run build
php artisan serve
# Or point your web server (Apache/Nginx) to the `public` directory.
```

## Laravel project layout

| Path | Purpose |
|------|---------|
| `app/Http/Controllers/HomeController.php` | Serves the main page (Vue SPA) |
| `routes/web.php` | Web routes; `GET /` → `home` |
| `resources/views/layouts/app.blade.php` | Main HTML layout (Vite, meta, CSRF) |
| `resources/views/welcome.blade.php` | SPA entry; mounts Vue to `#app` |
| `resources/js/App.vue` | Vue 3 landing page |
| `resources/js/app.js` | Vue app bootstrap |
| `resources/css/app.css` | Global styles and Tailwind |
| `public/` | Web root; `php artisan serve` serves from here |

---

<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## About Laravel

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

- [Simple, fast routing engine](https://laravel.com/docs/routing).
- [Powerful dependency injection container](https://laravel.com/docs/container).
- Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
- Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
- Database agnostic [schema migrations](https://laravel.com/docs/migrations).
- [Robust background job processing](https://laravel.com/docs/queues).
- [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework. You can also check out [Laravel Learn](https://laravel.com/learn), where you will be guided through building a modern Laravel application.

If you don't feel like reading, [Laracasts](https://laracasts.com) can help. Laracasts contains thousands of video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

## Laravel Sponsors

We would like to extend our thanks to the following sponsors for funding Laravel development. If you are interested in becoming a sponsor, please visit the [Laravel Partners program](https://partners.laravel.com).

### Premium Partners

- **[Vehikl](https://vehikl.com)**
- **[Tighten Co.](https://tighten.co)**
- **[Kirschbaum Development Group](https://kirschbaumdevelopment.com)**
- **[64 Robots](https://64robots.com)**
- **[Curotec](https://www.curotec.com/services/technologies/laravel)**
- **[DevSquad](https://devsquad.com/hire-laravel-developers)**
- **[Redberry](https://redberry.international/laravel-development)**
- **[Active Logic](https://activelogic.com)**

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
