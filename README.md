# Orders 2026

![CI](https://github.com/<your-username>/orders-2026/actions/workflows/ci.yml/badge.svg)

Modular e-commerce order management system built with Laravel 12, Filament, Livewire, and PostgreSQL.

## Stack

| Layer | Technology |
|-------|-----------|
| Language | PHP 8.4 |
| Framework | Laravel 12 |
| Database | PostgreSQL 16 |
| Cache / Queue | Redis |
| Web server | Nginx |
| Frontend | Livewire 3 |
| Admin panel | Filament 3 |
| Queue dashboard | Laravel Horizon |
| Modules | nwidart/laravel-modules 13 |
| Testing | Pest 3 |
| Code quality | Laravel Duster (Pint + PHP CS Fixer + TLint + PHPCS) + Larastan level 5 |

## Requirements

- [Docker Desktop](https://www.docker.com/products/docker-desktop/)
- Git
- Node.js 20+ (for frontend build, runs on host)

---

## Setup

### 1. Clone

**Mac / Linux**
```bash
git clone <repo-url> orders-2026
cd orders-2026
```

**Windows** — disable line-ending conversion before cloning:
```bash
git config --global core.autocrlf false
git clone <repo-url> orders-2026
cd orders-2026
```

### 2. Environment

**Mac / Linux**
```bash
cp .env.example .env
```

**Windows**
```powershell
copy .env.example .env
```

`.env.example` is pre-configured for Docker. Key variables:

| Variable | Value | Notes |
|----------|-------|-------|
| `DB_HOST` | `postgres` | Docker service name |
| `REDIS_HOST` | `redis` | Docker service name |
| `QUEUE_CONNECTION` | `redis` | Required for Horizon |

### 3. Build and start containers

```bash
docker compose up -d --build
```

Starts: `app` (PHP-FPM 8.4), `nginx`, `postgres`, `redis`, `horizon`.

### 4. Database

```bash
docker compose exec app php artisan key:generate
docker compose exec app php artisan migrate
docker compose exec app php artisan db:seed
```

The seeder is idempotent — re-running it skips already-applied versions. See [Seed versioning](#seed-versioning).

### 5. Assets

Frontend CSS/JS is built with Vite on the **host machine** (not inside the container):

```bash
npm install
npm run build
```

For development with hot-reload:
```bash
npm run dev
```

### 6. Filament admin assets

Filament publishes its CSS and JS to `public/` separately from Vite:

```bash
docker compose exec app php artisan filament:assets
```

Re-run this after every `composer update` that upgrades Filament.

---

## Running the application

```bash
docker compose up -d
```

| URL | Description |
|-----|-------------|
| http://localhost:8000 | Landing page |
| http://localhost:8000/products | Product catalog |
| http://localhost:8000/orders/create | Place an order |
| http://localhost:8000/admin | Filament admin panel |
| http://localhost:8000/horizon | Horizon queue dashboard |

### Pages

**`/products` — Product catalog**
- Cards with In stock / Out of stock badge and remaining quantity
- Price per product
- "Order" button only on in-stock items

**`/orders/create` — Order form (Livewire)**
- Left column: product list with `+` / `−` quantity controls per row
- Right column: cart with product names, per-line subtotals, and running total
- Loading state on the "Place Order" button during submission

**`/orders/{id}` — Order confirmation**
- Items table with prices and quantities
- Order status badge
- Customer details

### Create an admin user

```bash
docker compose exec app php artisan make:filament-user
```

> Run this directly — do **not** enter the container shell first (`bash`), otherwise the host PHP will be used instead of the container's.

Then log in at http://localhost:8000/admin.

---

## Seed versioning

Seeds are versioned to support incremental data updates on staging and production without re-seeding from scratch.

### How it works

- The `seed_versions` table records every applied version.
- `php artisan db:seed` checks each version before running it — already-applied versions are skipped.
- On a fresh database all versions apply; on an existing database only new ones run.

```
php artisan db:seed

  Skipping catalog_v1 if already applied
  Skipping order_v1 if already applied
```

### Adding new seed data

Create a new seeder class, then register it in `database/seeders/DatabaseSeeder.php`:

```php
public function run(): void
{
    $this->applyVersioned('catalog_v1', CatalogDatabaseSeeder::class);
    $this->applyVersioned('catalog_v2', CatalogV2Seeder::class); // new
    $this->applyVersioned('order_v1', OrderDatabaseSeeder::class);
}
```

Versions are applied in declaration order. Existing versions are never re-run.

---

## Module structure

```
Modules/
├── Catalog/          # Products and categories
│   ├── app/
│   │   ├── Filament/Resources/   # CategoryResource, ProductResource
│   │   ├── Models/               # Category, Product (SoftDeletes)
│   │   ├── Observers/            # ProductObserver, CategoryObserver
│   │   ├── Providers/            # CatalogServiceProvider (binds repository, registers observers)
│   │   └── Repositories/        # EloquentProductRepository
│   └── database/
│       ├── factories/
│       ├── migrations/
│       └── seeders/
│
└── Order/            # Order lifecycle
    ├── app/
    │   ├── Actions/              # PlaceOrderAction
    │   ├── Builders/             # OrderItemsBuilder
    │   ├── DataTransferObjects/  # OrderPayload
    │   ├── Events/               # OrderPlaced
    │   ├── Filament/Resources/   # OrderResource
    │   ├── Livewire/             # PlaceOrderForm
    │   ├── Models/               # Order, OrderItem
    │   ├── Observers/            # OrderObserver
    │   ├── Providers/            # OrderServiceProvider (registers observers)
    │   └── Validators/           # CartValidator
    └── database/
        ├── factories/
        ├── migrations/
        └── seeders/

app/
├── Contracts/Catalog/            # ProductRepositoryInterface
├── DataTransferObjects/Catalog/  # ProductData (DTO)
├── Enums/                        # OrderStatus, AuditAction
└── Models/                       # AuditLog
```

**Cross-module rule:** modules communicate only via contracts (`app/Contracts/`) and events. Direct model imports between modules are forbidden.

**Order status workflow:**
```
pending → confirmed → shipped → delivered
```

---

## Audit Trail

All entity changes are recorded in the `audit_logs` table via Eloquent Observers.

### Tracked entities

| Entity | Created | Updated | Deleted | Source |
|--------|---------|---------|---------|--------|
| Product | ✓ | ✓ | ✓ (soft) | Observer |
| Category | ✓ | ✓ | ✓ (soft) | Observer |
| Order | ✓ | ✓ | — | `created` → `PlaceOrderAction`, `updated` → Observer |

### Table structure

| Column | Type | Description |
|--------|------|-------------|
| `entity` | string | Model name: `product`, `category`, `order` |
| `entity_id` | bigint | ID of the affected record |
| `action` | string | `created`, `updated`, `deleted` |
| `changes` | json | Changed attributes (`null` for `deleted`) |
| `user_id` | bigint nullable | Admin who made the change (null for system/queue) |

- **`created`** — stores initial attributes (excludes `id`, timestamps). For orders: includes full items list + customer info, recorded explicitly in `PlaceOrderAction` after the transaction commits
- **`updated`** — stores only changed fields (excludes `updated_at`)
- **`deleted`** — records the deletion; `changes` is `null`

### Adding audit trail to a new module

1. Create an observer in `Modules/{Name}/app/Observers/{Model}Observer.php`
2. Register it in the module's `ServiceProvider::boot()`:

```php
Product::observe(ProductObserver::class);
```

### Domain-specific error logs

Each module writes errors to its own daily log channel:

| Channel | Path |
|---------|------|
| `products` | `storage/logs/products/products-YYYY-MM-DD.log` |
| `orders` | `storage/logs/orders/orders-YYYY-MM-DD.log` |

Usage: `Log::channel('products')->error('...', [...])`

---

## Tests

Tests use an in-memory SQLite database (configured in `phpunit.xml`) — no extra setup required.

```bash
# Run all tests
docker compose exec app ./vendor/bin/pest

# By suite
docker compose exec app ./vendor/bin/pest --testsuite=Feature
docker compose exec app ./vendor/bin/pest --testsuite=Modules

# By module
docker compose exec app ./vendor/bin/pest Modules/Catalog
docker compose exec app ./vendor/bin/pest Modules/Order

# Show pending (todo) tests
docker compose exec app ./vendor/bin/pest --todo

# Via composer
docker compose exec app composer test
```

---

## Code quality

```bash
# Check style (Duster)
docker compose exec app composer lint

# Auto-fix style
docker compose exec app composer fix

# Static analysis (Larastan level 5)
docker compose exec app composer analyse

# Run everything: lint + analyse + test
docker compose exec app composer check
```

### CI/CD

GitHub Actions runs on every push and pull request to `main`:

1. Migrate database
2. Apply versioned seeds
3. Code style — Duster
4. Static analysis — Larastan level 5
5. Tests — Pest

See `.github/workflows/ci.yml`.

---

## Useful commands

```bash
# Container status
docker compose ps

# Stream all logs
docker compose logs -f

# Stream Horizon logs only
docker compose logs -f horizon

# Artisan
docker compose exec app php artisan <command>

# Shell inside container
docker compose exec app bash

# Stop
docker compose down

# Stop and wipe volumes (database reset)
docker compose down -v
```
