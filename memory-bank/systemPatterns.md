# System Patterns

## Architecture Style
Framework-free, layered PHP application using the **Front Controller** pattern with a **Chain of Responsibility** request pipeline, **DAO** data access, **DTO** input models with attribute-based validation, and **singleton** services for PDO/Redis.

## Request Flow
1. `index.php` → `FrontController::handle()`.
2. `RequestRouter::resolveFromGlobals()` builds a `Request` from superglobals.
3. Handler chain:
   - `TranslationHandler`: resolves translator from `Accept-Language`, stores in `Container`.
   - `AuthenticationHandler`: allows `/api/v1/users*` auth routes; otherwise validates JWT.
   - `ResponseHandler`: invokes next handler, sets headers, echoes JSON.
   - `DispatchingHandler`: calls `Dispatcher::dispatch()`.
4. `Dispatcher` matches `method` + URL regex → controller → `handleRequest()`.
5. Controller → `InputHandler` + DTO → model factory → DAO → `Response`.

## Key Components
- `var/www/html/service/controller/FrontController.php`
- `var/www/html/service/router/RequestRouter.php`
- `var/www/html/service/Dispatcher.php`
- `var/www/html/service/request/handler/*`
- `var/www/html/dao/*DAO.php`
- `var/www/html/dto/*/*DTO.php`
- `var/www/html/model/*`
- `var/www/html/service/data/InputHandler.php` + sanitizers/validators

## Design Patterns In Use
- **Front Controller**: single entry point `index.php`.
- **Chain of Responsibility**: handler pipeline.
- **DAO**: one DAO per entity (`UserDAO`, `GroupDAO`, `ItemDAO`, `MovementItemDAO`).
- **DTO + Attributes**: readonly constructor-promoted properties with `#[Sanitizer]`/`#[Validator]` attributes.
- **Factory Methods**: model `*Constructor(DTO)` static methods.
- **Singleton**: `PDOService`, `RedisService`.
- **Service Locator**: `Container` for translator (and cache).
- **Strategy-ish enums**: `Status`, `Unity`, `Currency`, `Cache`, `LangEnum`.

## Naming Conventions
- Namespaces: `App\...` mapped by autoloader to `var/www/html/...` (strips `App\` prefix).
- Controllers: `*Controller.php` (some typos: `Cotroller`, `UpdateGroupCotroller`, `ReadMovementItemCotroller`).
- DTOs grouped by entity under `dto/<entity>/`.
- DAOs under `dao/`.
- Routes use regex with UUID v4-like pattern `[\\d\\w]{8}-...`.

## Data Flow & Ownership
- All domain entities carry a `userUuid` (binary UUID in DB, string in PHP).
- Controllers call `Request::setUserUuid()` to inject authenticated user UUID into body where needed.
- DAOs convert UUIDs with `UUID_TO_BIN`/`BIN_TO_UUID` for MySQL binary(16) columns.

## Authentication Pattern
- `JWTService::createToken()` issues access + refresh tokens and stores them in Redis under `token:accessToken:*`, `token:refreshToken:*`, `user:session:*`.
- `JWTService::validate()` checks HMAC signature and Redis session.
- `AuthenticationHandler` exempts `/api/v1/users`, `/api/v1/users/login`, `/api/v1/users/refresh`.

## Validation Pattern
- `InputHandler::handle(dtoClass, rawData)` reflects constructor params, applies sanitizers then validators, collects errors, returns DTO or `false`.
- Validators: `StringValidator`, `IntegerValidator`, `ArrayValidator`, `EnumValidator`.
- Sanitizers: `StringSanitizer`, `IntegerSanitizer`, `ArraySanitizer`.

## Error Handling
- Domain exceptions: `ValidationException`, `NotFoundException`, `UnauthorizedException`, `BadRequestException`, `MethodNotAllowedException`, `UniqueConstraintViolationException`, `ForeignKeyConstraintViolationException`, `InternalServerErrorException`.
- `FrontController` catches and renders translated JSON with HTTP status.

## Database Schema (high level)
- `user(id, uuid, name, email, cellphoneNumber, password, status, createdAt, updatedAt)`
- `group(id, uuid, userUuid, name, status, ...)` unique `(name, userUuid)`
- `item(id, uuid, userUuid, groupUuid, name, status, ...)` unique `(name, userUuid)`
- `movementItem(id, uuid, userUuid, itemUuid, unitValue, quantity, unity, currency, ...)`
- Foreign keys with `ON DELETE RESTRICT`, `ON UPDATE CASCADE`.

## Known Code Smells / Inconsistencies
- `SessionManager.php` is a placeholder (empty implementation).
- Several controller/DAO filenames contain typos (`Cotroller`).
- `JWTService::getSubject()` is the low-level JWT helper; user identity is resolved once in `AuthenticationHandler` and carried by `Request`.
- No automated tests.
