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

## Architecture guidelines

- Use `App\Enums` for enums (e.g. `OrderStatus`)
- Module-based structure via `nwidart/laravel-modules`
- Validate patterns against current Laravel 12 docs — AI training data may contain outdated patterns
- Prefer clean, readable code over clever abstractions
- No unnecessary comments — code should be self-explanatory

## AI Tool Usage Guidelines

- **Verify current patterns**: Laravel APIs change between versions — always validate suggestions against Laravel 12 documentation
- **Adapt suggestions**: modify generated code to fit the project architecture, do not blindly copy
- **Quality over speed**: prefer clean, maintainable solutions
- **Understand the code**: do not submit code you cannot explain
- **Pay attention to versions**: check package versions for compatibility (PHP 8.4, Laravel 12)
- **Test setup instructions**: verify that setup steps work on a clean environment
