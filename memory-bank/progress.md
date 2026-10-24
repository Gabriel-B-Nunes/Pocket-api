# Progress

## What Works
- Project scaffolding: Apache rewrite, autoloader, front controller, handler chain, dispatcher.
- User registration (`POST /api/v1/users`).
- User login (`POST /api/v1/users/login`) issuing JWT access/refresh tokens.
- Token refresh (`POST /api/v1/users/refresh`).
- Group CRUD: create, read (list/by UUID), update, delete.
- Item CRUD: create, read (list/by UUID), update, delete.
- Movement item CRUD: create, read (list/by UUID), update, delete.
- Input validation/sanitization pipeline via DTO attributes.
- Localized error responses (EN_US default, PT_BR).
- MySQL persistence with UUID binary conversion and constraint handling.
- Redis-backed JWT sessions.

## What's Left To Build
- Automated tests (unit/integration) — none currently.
- User profile read/update/delete endpoints (if required).
- Complete/robust `SessionManager` implementation (currently placeholder).
- Potential fixes: controller/DAO filename typos.
- Docker Compose orchestration files (only Dockerfile + configs present).
- API documentation (README is empty).

## Current Status
- Development on `develop` branch; latest commit merges movement item delete feature.
- Core feature set for groups/items/movement items is functionally complete.
- No CI/CD or test automation.

## Known Issues
- `SessionManager.php` is empty/placeholder.
- `JWTService::getUserUuid()` removed; identity now resolved via `AuthenticationHandler` + `Request::getUserUuid()`.
- Typos in filenames (`Cotroller`) and class names (`UpdateGroupCotroller`, `ReadMovementItemCotroller`).
- `PDOService` host is hardcoded to `database`; environment-specific.
- Redis connection is not guarded against connection failures.
- No request logging/observability.

## Evolution of Project Decisions
- Moved from a broader "pocket-web" project to a standalone Pocket API.
- Adopted handler chain + dispatcher instead of a routing library.
- Adopted DTO attribute-based validation instead of manual per-controller validation.
- Added Redis for JWT session storage to support token invalidation on re-login.
