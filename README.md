# FastFood Invoice System

<p align="center">
  <img src="public/images/logo.png" alt="FastFood Invoice System Logo" width="180">
</p>

<p align="center">
  <img src="https://img.shields.io/badge/Laravel-12.x-FF2D20?style=flat-square&logo=laravel&logoColor=white" alt="Laravel 12">
  <img src="https://img.shields.io/badge/PHP-8.2+-777BB4?style=flat-square&logo=php&logoColor=white" alt="PHP 8.2+">
  <img src="https://img.shields.io/badge/License-MIT-green?style=flat-square" alt="MIT License">
</p>

FastFood Invoice System is a Laravel-based POS and restaurant operations platform for fast food businesses. It supports role-based access for cashiers, managers, and owners, with a custom OTP login flow and dashboards for daily sales, order tracking, menu management, staff management, and reporting.

## Visual Overview

<p align="center">
  <img src="public/images/empty-states/dashboard-icon.png" alt="Dashboard Preview" width="180">
  <img src="public/images/empty-states/empty-cart.png" alt="Empty State Preview" width="180">
</p>

## Key Features

- Custom OTP-based login with role routing for `cashier`, `manager`, and `owner`.
- Manager dashboard with today’s revenue, order activity, staff count, top items, and recent orders.
- Cashier POS flow for creating and tracking orders.
- Menu management with availability toggles and performance-aware indexing.
- Staff management for manager and owner roles.
- Daily reports and PDF export for owner workflows.
- Dark mode support via the shared layout.
- Local static assets and offline-friendly UI delivery through `public/assets` and `public/images`.

## Role Flow

```mermaid
flowchart TD
    A[Login Form] --> B[Validate Credentials]
    B --> C[Send OTP]
    C --> D[Verify OTP]
    D --> E[Auth::login(user)]
    E --> F{User Role}
    F -->|cashier| G[Cashier Dashboard / POS]
    F -->|manager| H[Manager Dashboard / Menu / Staff]
    F -->|owner| I[Owner Dashboard / Reports / Staff]
```

## Tech Stack

- Laravel 12
- PHP 8.2+
- Laravel UI
- Blade templates
- MySQL or SQLite
- Vite for frontend asset bundling
- DomPDF for report exports

## Project Structure

- `app/Http/Controllers/Auth/AuthController.php` handles custom login, OTP verification, and role redirects.
- `app/Http/Middleware/RoleMiddleware.php` protects routes by user role.
- `routes/web.php` defines the protected cashier, manager, and owner areas.
- `resources/views/` contains the dashboards, POS pages, history screens, auth screens, and reports.
- `database/migrations/` contains menu, orders, order items, reports, stock, and user auth schema changes.

## Screens and Modules

- Login and OTP verification
- Cashier dashboard, POS, invoice, and order history
- Manager dashboard and menu management
- Owner dashboard, staff management, reports, and PDF export

## Installation

### Requirements

- PHP 8.2 or newer
- Composer
- Node.js and npm
- MySQL, MariaDB, or SQLite

### Quick Start

#### Windows

```powershell
copy .env.example .env
composer install
npm install
php artisan key:generate
php artisan migrate
php artisan serve
```

#### macOS / Linux

```bash
cp .env.example .env
composer install
npm install
php artisan key:generate
php artisan migrate
php artisan serve
```

Then open the app at `http://127.0.0.1:8000`.

## Development Scripts

The repository includes helpful Composer scripts:

- `composer setup` installs dependencies, creates `.env`, generates the app key, runs migrations, and builds frontend assets.
- `composer dev` runs the Laravel server, queue listener, log tailing, and Vite in parallel.
- `composer test` clears config cache and runs the test suite.

## Environment Notes

- Sessions use the database driver by default.
- Authentication is custom and role-aware, not the stock Breeze scaffold.
- Dark theme styling is handled in the shared layout and may need page-specific overrides for white surfaces.
- Keep assets local where possible to preserve offline and intranet usability.

## Useful URLs

- `/login`
- `/otp-verify`
- `/cashier/dashboard`
- `/manager/dashboard`
- `/owner/dashboard`
- `/orders/history`

## Contributing

If you extend the system, keep role checks in middleware, avoid querying models directly inside Blade when aggregate data can be prepared in controllers, and prefer local assets over remote CDN dependencies.

## License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
