# De Groene Weide

A camping reservation management system built for the fictional campsite *De Groene Weide* (The Green Meadow). This project was developed as a school assignment for the **Hogeschool Utrecht (HU)**.

The application consists of a public-facing booking site for customers and a full admin panel for staff and administrators.

---

## Features

### Customer-facing

- Browse and filter available campsites
- Make reservations with optional extras and discount coupons
- Passwordless authentication via magic links
- Pay online via Stripe or on arrival
- Cancel reservations via the self-service portal
- Confirmation and cancellation emails

### Admin panel

- Dashboard with live stats: revenue, today's arrivals, occupancy, and low-stock alerts
- Full CRUD for campsites, seasons, pricing, extras, coupons, reservations, customers, and employees
- Create reservations on behalf of customers (phone, walk-in, email bookings)
- Role-based access control (Admin / Employee)
- GDPR-compliant customer data purging commands

---

## Tech Stack

| Layer | Technology |
|---|---|
| Language | PHP 8.4 |
| Framework | Laravel 13 |
| Admin panel | FilamentPHP 5 |
| Frontend | Vite 8 + Tailwind CSS 4 |
| Payments | Stripe via Laravel Cashier 16 |
| Database | MySQL (SQLite for tests) |
| Testing | Pest 4 |

---

## Getting Started

### Requirements

- PHP >= 8.4
- Composer
- Node.js >= 20
- MySQL

### Installation

```bash
git clone <repository-url>
cd de-groene-weide
composer run setup
```

`composer run setup` installs dependencies, generates an app key, runs migrations with seeders, and builds frontend assets.

### Environment Variables

Copy `.env.example` to `.env` and fill in the required values:

```env
APP_URL=http://localhost

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=de_groene_weide
DB_USERNAME=root
DB_PASSWORD=

# Stripe (required for online payments)
STRIPE_KEY=
STRIPE_SECRET=
STRIPE_WEBHOOK_SECRET=

# Credentials for the seeded admin account
ADMIN_EMAIL="bertina@degroeneweide.nl"
ADMIN_PASSWORD=
```

---

## Development

```bash
composer run dev    # starts server, queue worker, log monitor, and Vite concurrently
composer run test   # run the test suite
```

The admin panel is available at `/admin`. Log in with the credentials set via `ADMIN_EMAIL` and `ADMIN_PASSWORD` in `.env`.

---

## Testing

Tests are written with [Pest](https://pestphp.com/) and run against an SQLite in-memory database. Coverage includes the booking flow, pricing logic, extras availability, signed URL generation, Filament admin resources, and GDPR data purging.

```bash
composer run test
```

---

## CI/CD

| Workflow | Trigger | Purpose |
|---|---|---|
| `run-tests.yml` | Pull request | Run the full test suite |
| `composer-audit.yml` | PR to `production`/`staging` | Dependency security audit |
| `check-branch-name.yml` | PR opened | Enforce branch naming conventions |
| `check-source-branch.yml` | PR opened | Validate source branch rules |
| `trigger-deploy.yml` | Push to `production`/`staging` | Trigger deployment in the infra repo |
