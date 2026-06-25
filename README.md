# Orders 2026

Laravel application with Filament admin panel, Livewire, and PostgreSQL.

## Stack

- PHP 8.4, Laravel 12
- PostgreSQL 16
- Redis
- Nginx
- Livewire 3
- Filament 3
- nwidart/laravel-modules
- Pest

## Requirements

- [Docker Desktop](https://www.docker.com/products/docker-desktop/) (Mac, Windows, Linux)
- Git

---

## Setup

### Mac

```bash
# 1. Clone the repository
git clone <repo-url> orders-2026
cd orders-2026

# 2. Copy environment file
cp .env.example .env

# 3. Build and start containers
docker compose up -d --build

# 4. Generate application key
docker compose exec app php artisan key:generate

# 5. Run migrations
docker compose exec app php artisan migrate
```

---

### Windows

> Run commands in **PowerShell** or **Git Bash**. Make sure Docker Desktop is running.

```powershell
# 1. Clone the repository
git clone <repo-url> orders-2026
cd orders-2026

# 2. Copy environment file
copy .env.example .env

# 3. Build and start containers
docker compose up -d --build

# 4. Generate application key
docker compose exec app php artisan key:generate

# 5. Run migrations
docker compose exec app php artisan migrate
```

> **Note for Windows:** if you face line-ending issues, run:
> ```bash
> git config --global core.autocrlf false
> ```
> before cloning.

---

### Linux

```bash
# 1. Clone the repository
git clone <repo-url> orders-2026
cd orders-2026

# 2. Copy environment file
cp .env.example .env

# 3. Build and start containers
docker compose up -d --build

# 4. Generate application key
docker compose exec app php artisan key:generate

# 5. Run migrations
docker compose exec app php artisan migrate
```

---

## Access

| Service   | URL / Host          |
|-----------|---------------------|
| App       | http://localhost:8000 |
| PostgreSQL | localhost:5432      |
| Redis     | localhost:6379      |

## Useful commands

```bash
# View running containers
docker compose ps

# View logs
docker compose logs -f

# Run artisan commands
docker compose exec app php artisan <command>

# Run Pest tests
docker compose exec app ./vendor/bin/pest

# Stop containers
docker compose down

# Stop and remove volumes (resets database)
docker compose down -v
```
