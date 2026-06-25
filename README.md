# Orders 2026

![CI](https://github.com/<your-username>/orders-2026/actions/workflows/ci.yml/badge.svg)

Modular e-commerce order management system built with Laravel 12, Filament, Livewire, and PostgreSQL.

## Stack

- PHP 8.4, Laravel 12
- PostgreSQL 16
- Redis
- Nginx
- Livewire 3
- Filament 3 (admin panel)
- Laravel Horizon (queue dashboard)
- nwidart/laravel-modules 13
- Pest (testing)
- Laravel Duster + Larastan (code quality)

## Requirements

- [Docker Desktop](https://www.docker.com/products/docker-desktop/)
- Git

---

## Setup Instructions

### 1. Installation

**Mac / Linux**
```bash
git clone <repo-url> orders-2026
cd orders-2026
```

**Windows** — run in PowerShell or Git Bash. Before cloning, disable line-ending conversion:
```bash
git config --global core.autocrlf false
git clone <repo-url> orders-2026
cd orders-2026
```

### 2. Environment configuration

**Mac / Linux**
```bash
cp .env.example .env
```

**Windows**
```powershell
copy .env.example .env
```

The `.env.example` is pre-configured for Docker. Key settings:

| Variable | Value | Description |
|----------|-------|-------------|
| `DB_CONNECTION` | `pgsql` | PostgreSQL driver |
| `DB_HOST` | `postgres` | Docker service name |
| `DB_DATABASE` | `orders` | Database name |
| `REDIS_HOST` | `redis` | Docker service name |
| `QUEUE_CONNECTION` | `redis` | Horizon uses Redis |

### 3. Build and start containers

```bash
docker compose up -d --build
```

This starts: `app` (PHP-FPM), `nginx`, `postgres`, `redis`, `horizon`.

### 4. Database setup

```bash
# Generate application key
docker compose exec app php artisan key:generate

# Run migrations
docker compose exec app php artisan migrate
```

### 5. Asset compilation

```bash
# Install Node dependencies
docker compose exec app npm install

# Build assets (one-time)
docker compose exec app npm run build

# Or watch for changes during development
docker compose exec app npm run dev
```

---

## Running the Application

### Start

```bash
docker compose up -d
```

### Access

| Service | URL |
|---------|-----|
| Application | http://localhost:8000 |
| Filament Admin | http://localhost:8000/admin |
| Horizon Dashboard | http://localhost:8000/horizon |

### Admin interface

Create an admin user:
```bash
docker compose exec app php artisan make:filament-user
```

Then log in at http://localhost:8000/admin.

### Testing main functionality

1. Open http://localhost:8000 — product catalog
2. Add products to cart and place an order
3. Log into admin at http://localhost:8000/admin to manage orders
4. Track queue jobs at http://localhost:8000/horizon

---

## Running Tests

### Test-specific setup

Tests use an in-memory SQLite database — no extra configuration needed.
The test environment is defined in `phpunit.xml`.

### Run all tests

```bash
docker compose exec app ./vendor/bin/pest
```

### Run by suite

```bash
# Core application tests only
docker compose exec app ./vendor/bin/pest --testsuite=Feature

# All module tests
docker compose exec app ./vendor/bin/pest --testsuite=Modules

# Catalog module only
docker compose exec app ./vendor/bin/pest Modules/Catalog

# Order module only
docker compose exec app ./vendor/bin/pest Modules/Order
```

### Show pending (todo) tests

```bash
docker compose exec app ./vendor/bin/pest --todo
```

### Run via composer

```bash
docker compose exec app composer test
```

---

## Code Quality

### Check & fix

```bash
# Check code style (Duster: Pint + PHP CS Fixer + TLint + PHPCS)
docker compose exec app composer lint

# Auto-fix style issues
docker compose exec app composer fix

# Static analysis (Larastan level 5)
docker compose exec app composer analyse

# Run all checks at once (lint + analyse + test)
docker compose exec app composer check
```

### CI/CD

GitHub Actions runs the full pipeline on every push and pull request to `main`:
- Code style validation (Duster)
- Static analysis (Larastan level 5)
- Tests (Pest)

See `.github/workflows/ci.yml`.

---

## Useful commands

```bash
# View running containers
docker compose ps

# View all logs
docker compose logs -f

# View Horizon logs
docker compose logs -f horizon

# Run any artisan command
docker compose exec app php artisan <command>

# Open a shell inside the container
docker compose exec app bash

# Stop containers
docker compose down

# Stop and wipe the database
docker compose down -v
```
