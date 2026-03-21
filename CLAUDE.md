# CLAUDE.md - Lily

## Project Overview

Lily is a medication history tracking system built with Laravel 12 and PHP 8.4. It provides:
- **Admin web interface** (Laravel AdminLTE) for managing drugs, medication histories, and admin users
- **REST API** for client applications (drugs and medication histories CRUD)
- **Discord bot** integration for messaging

Package name: `new_world_org/medication_history`

## Architecture

The project follows **Domain-Driven Design (DDD)** with three layers:

```
app/        → Application layer (Controllers, API Actions, Services, Providers, Middleware)
domain/     → Domain layer (Entities, Value Objects, Repository interfaces)
infra/      → Infrastructure layer (Eloquent repositories, Eloquent models, Discord client)
```

### Domain Modules
`AdminUser`, `Drug`, `MedicationHistory`, `Channel`, `Message`, `DiscordBot`, `Base`, `Common`, `Exception`

### Key Patterns
- **Repository pattern**: Interfaces in `domain/`, Eloquent implementations in `infra/EloquentRepository/`
- **Action/Responder**: API endpoints are invokable action classes in `app/Http/Api/`
- **Value Objects**: Strongly typed wrappers in domain (e.g., `DrugId`, `Amount`, `MedicationDate`)
- **Readonly classes** in domain entities
- `declare(strict_types=1)` in domain layer

### Namespaces (PSR-4)
- `App\` → `app/`
- `Domain\` → `domain/`
- `Infra\` → `infra/`
- `Database\Factories\` → `database/factories/`
- `Database\Seeders\` → `database/seeders/`
- `Tests\` → `tests/`

## Tech Stack

- **PHP 8.4** / **Laravel 12**
- **MySQL 9.1** (utf8mb4)
- **Vite 6** / **jQuery 3.7** (frontend)
- **Docker** (PHP Unit server, MySQL, Node 22)
- **Traefik** reverse proxy (external `docker_default` network)
- **Composer** (PHP deps) / **Yarn** (JS deps)
- **Task** (task runner, see `Taskfile.yaml`)

## Common Commands

All commands use [Task](https://taskfile.dev/) and run inside Docker containers:

| Command | Description |
|---------|-------------|
| `task up` | Start Docker containers |
| `task down` | Stop containers |
| `task bash` | Shell into app container |
| `task setup` | Full setup (composer install, key gen, migrate, seed) |
| `task docker_build` | Build Docker images (no cache) |
| `task composer_install` | Install PHP dependencies |
| `task migrate` | Run database migrations |
| `task migrate_seed` | Fresh migration with seeding |
| `task test_unit` | Run unit tests |
| `task test_feature` | Run feature tests |
| `task test_db_integration` | Run DB integration tests |
| `task test_seed` | Seed testing database |
| `task swagger` | Generate Swagger/OpenAPI docs |
| `task start_discord_bot` | Run Discord bot |
| `task ide_helper` | Generate IDE helper files |
| `task cache_clear` | Clear all Laravel caches |

### Running tests directly (inside container)
```bash
vendor/bin/phpunit --testsuite Unit --testdox --colors=always
vendor/bin/phpunit --testsuite Feature --testdox --colors=always
vendor/bin/phpunit --testsuite DbIntegration --testdox --colors=always
```

## Code Style & Quality

- **PSR-12** coding standard (via `php-cs-fixer`)
- **PHPStan level max** (static analysis via `larastan`) — paths: `domain/`, `infra/`, `app/`
- **StyleCI** with Laravel preset (`.styleci.yml`)
- **PHPUnit 11** for testing (3 test suites: Unit, Feature, DbIntegration)
- Test DB uses `mysql` connection with database name `test`

## CI/CD (GitHub Actions)

- `unit-test.yml` — Unit tests on push/PR
- `feature-test.yml` — Feature tests on push/PR
- `db-integration-test.yml` — DB integration tests on push/PR
- `swagger.yml` — Generate Swagger docs

## Routes

- **Web**: `/admin/*` — Admin panel (auth required), root redirects to login
- **API**: `/api/drugs/*`, `/api/medication_histories/*`, `/api/csrf_token`

## Git

- Main branch: `master`
