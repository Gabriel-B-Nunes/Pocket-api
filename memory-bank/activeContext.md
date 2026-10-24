# Active Context

## Current Work Focus
- Memory bank initialization for the repository.
- Movement item (movementItem) CRUD feature just completed on `develop`:
  - DTOs: create/update/delete/read/uuid.
  - DAO methods: create, readByUuid, readByQueryString, update, delete.
  - Controllers: create, read (list), read by UUID, update, delete.
  - Routes registered in `Dispatcher` for all movement item endpoints.

## Recent Changes
- Added `DeleteMovementItemController` and wired it into `Dispatcher` routes (latest commits).
- Completed update/read flows for movement items prior to delete.
- Existing complete CRUD for groups and items; users support create/login/refresh only.

## Next Steps
- Manually exercise all movement item endpoints end-to-end against a running stack.
- Address known code smells: empty `SessionManager`, controller filename typos.
- Consider adding read/update/delete capabilities for users if product requires profile management.
- Introduce an automated test strategy (none exists today).

## Active Decisions
- Keep framework-free PHP with custom autoloader and handler chain.
- Use Redis for JWT session storage and token invalidation on re-login.
- Use DTO attribute-based validation rather than a validation library.

## Important Patterns / Preferences
- Controllers receive DAO and InputHandler via constructor with default instances (testability-friendly).
- DAO methods accept DTOs or models, not raw arrays.
- Controllers inject authenticated `userUuid` via `Request::setUserUuid()`.

## Learnings / Insights
- Route regex for list endpoints must tolerate query strings; movement item list route does.
- UUIDs require binary conversion at the DAO boundary.
- `Accept-Language` defaults to English; only PT_BR has a translation file.
