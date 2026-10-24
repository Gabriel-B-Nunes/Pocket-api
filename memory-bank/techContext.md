# Tech Context

## Runtime
- **PHP 8.5** (Apache module) — see `php/dockerfile` (`FROM php:8.5-apache`).
- **Apache** with `mod_rewrite` enabled; `.htaccess` rewrites `^api/(.*)$` to `index.php`.

## Data Stores
- **MySQL** (`pocket` database), host `database`, user `pocket`, charset `utf8mb4` (see `my.cnf` and `PDOService`).
- **Redis**, host `redis`, port `6379` (see `RedisService`).

## PHP Extensions / Dependencies
- `redis` (PECL)
- `xdebug` (PECL)
- `intl`
- `mysqli`
- `pdo_mysql`
- No Composer; autoloading via `var/www/html/autoload.php` (PSR-4-like, `App\` → `var/www/html/`).

## Environment Variables
- `MYSQL_PASSWORD` — MySQL password for `pocket` user.
- `JWT_SECRET` — HMAC secret for JWT signing/validation.
- `HASH_PEPPER` — pepper appended to passwords before hashing.

## Development Setup
- Docker image built from `php/dockerfile`; PHP config from `php/php.ini`.
- MySQL config from `my.cnf`.
- DB schema created from `init-scripts/*.sql` in order:
  1. `0user.sql`
  2. `1group.sql`
  3. `2item.sql`
  4. `3movementItem.sql`
- Document root: `var/www/html`.
- Entry point: `var/www/html/index.php`.

## Tooling
- Git (branches: `master`, `develop`; active development on `develop`).
- No test framework configured.
- No package manager (Composer) in use.

## Constraints
- Autoloader depends on `$_SERVER["DOCUMENT_ROOT"]`, so requests must go through Apache (not CLI directly) unless `DOCUMENT_ROOT` is set.
- All source lives under `var/www/html`; namespaces strip the `App\` prefix.
- UUIDs stored as `BINARY(16)` in MySQL; convert with `UUID_TO_BIN`/`BIN_TO_UUID`.
- Responses are always JSON; cache headers derived from `Cache` enum.

## Supported Languages
- `EN_US` (default, identity translation).
- `PT_BR` (loaded from `service/translation/lang/PT_BR.php`).
