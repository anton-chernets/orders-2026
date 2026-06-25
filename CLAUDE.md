# CLAUDE.md — Project Instructions

## Stack

- PHP 8.4, Laravel 12
- PostgreSQL 16 (container: `orders-postgres`, host: `postgres`)
- Redis (container: `orders-redis`, host: `redis`)
- Nginx (port 8000)
- Laravel Horizon (queue worker, separate container)
- Livewire 3, Filament 3, nwidart/laravel-modules 13
- Pest for testing

## Running commands

Always run artisan, composer, pest, duster, and phpstan inside the container:

```bash
docker compose exec app php artisan <command>
docker compose exec app composer <command>
docker compose exec app ./vendor/bin/pest
docker compose exec app ./vendor/bin/duster lint
docker compose exec app ./vendor/bin/phpstan analyse --memory-limit=512M
```

## Code quality — required before every commit

```bash
docker compose exec app composer lint      # Duster style check
docker compose exec app composer fix       # Duster auto-fix
docker compose exec app composer analyse   # Larastan level 5
docker compose exec app composer test      # Pest
docker compose exec app composer check     # all of the above
```

## Code style

- PSR-12 enforced via Laravel Duster (Pint + PHP CS Fixer + TLint + PHPCS)
- Config: `pint.json` (preset: psr12)
- Do not add doc blocks to migration `up()` / `down()` methods — TLint will fail
- Order class elements: static methods before instance methods

## Static analysis

- Larastan level 5, zero errors
- Config: `phpstan.neon`
- Always pass `--memory-limit=512M` (default container limit is 128M)

## Modular architecture

The project uses `nwidart/laravel-modules`. Modules live in `Modules/`.

### Planned modules

| Module    | Responsibility                              |
|-----------|---------------------------------------------|
| Catalog   | Products and categories                     |
| Customers | Customer profiles                           |
| Orders    | Order lifecycle, OrderStatus workflow       |
| Inventory | Stock tracking                              |
| Payments  | Payment processing                          |

### Module boundaries — STRICT rules

- **Modules MUST NOT import models from other modules directly.**
- Cross-module communication happens only via:
  1. **Contracts** (interfaces) defined in `app/Contracts/` — the consuming module depends on the interface, not the implementation
  2. **Laravel Events** — a module fires an event, other modules listen via their `EventServiceProvider`
- Each module registers its own contract bindings in its `ServiceProvider`
- DTOs (readonly classes) may be shared across modules as they carry no behaviour

### Correct cross-module example

```php
// app/Contracts/Catalog/ProductRepositoryInterface.php
interface ProductRepositoryInterface {
    public function findById(int $id): ProductData; // DTO, not Model
}

// Modules/Orders — depends on contract, not on Catalog model
public function __construct(
    private readonly ProductRepositoryInterface $products
) {}
```

### Wrong — never do this

```php
// Inside Modules/Orders — direct model import from another module
use Modules\Catalog\Models\Product; // FORBIDDEN
```

### OrderStatus workflow

```
pending → confirmed → shipped → delivered
```

Defined in `app/Enums/OrderStatus.php`. Use `canTransitionTo()` before every status change.

### Inter-module events

Fire events from the source module, listen in the target module:

```php
// Modules/Orders fires:
event(new OrderPlaced($orderId, $items));

// Modules/Inventory listens in its EventServiceProvider:
OrderPlaced::class => [DecrementStock::class]
```

## AI Tool Usage Guidelines

- **Verify current patterns**: Laravel APIs change between versions — always validate suggestions against Laravel 12 documentation
- **Adapt suggestions**: modify generated code to fit the project architecture, do not blindly copy
- **Quality over speed**: prefer clean, maintainable solutions
- **Understand the code**: do not submit code you cannot explain
- **Pay attention to versions**: check package versions for compatibility (PHP 8.4, Laravel 12)
- **Test setup instructions**: verify that setup steps work on a clean environment
- **Module boundaries**: never suggest direct cross-module model imports — always use contracts or events
