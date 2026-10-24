# Product Context

## Why This Project Exists
`Pocket-api` is the server-side component of the Pocket ecosystem. It exposes the data and business operations needed by the Pocket client to track users, organizational groups, catalog items, and stock/inventory movements (entries with unit value, quantity, unity, and currency).

## Problems It Solves
- Centralizes persistence and business rules for Pocket data instead of embedding them in a client.
- Secures access to per-user data using JWT authentication and Redis-backed sessions.
- Normalizes and validates incoming payloads before they reach the database (DTO + sanitizers/validators).
- Provides consistent, localized error responses for clients.
- Maps database constraint violations (unique/foreign keys) to meaningful HTTP errors.

## How It Should Work
1. Client sends an HTTP request to `/api/v1/...`.
2. Apache rewrites the request to `index.php`.
3. `FrontController` builds a `Request`, runs the handler chain (Translation → Authentication → Response → Dispatching).
4. `Dispatcher` matches method + URL regex to a controller class.
5. Controller validates input via `InputHandler` + DTO, calls DAO, and returns a `Response`.
6. `ResponseHandler` serializes JSON with appropriate status code and cache headers.

## User Experience Goals
- Predictable REST JSON responses with `timestamp`, localized `title`, and `status`.
- Clear validation errors keyed by field name.
- Localized messages in English (default) and Brazilian Portuguese via `Accept-Language`.
- Secure-by-default: all non-auth routes require a valid bearer token.

## Key Domain Concepts
- **User**: account with name, email, cellphone, hashed password, status.
- **Group**: named collection owned by a user (unique name per user).
- **Item**: named product/material owned by a user and linked to a group (unique name per user).
- **MovementItem**: a movement record (unit value, quantity, unity, currency) owned by a user and linked to an item.
