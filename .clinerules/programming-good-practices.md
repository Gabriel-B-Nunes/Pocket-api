# Programming Best Practices — Pocket-api

Best practices that MUST be followed across the codebase (PHP 8.5, PSR-4 `App\` / `Tests\`).

## SOLID
- S — Single Responsibility: a class has one reason to change.
- O — Open/Closed: open for extension, closed for modification.
- L — Liskov Substitution: subtypes must be substitutable without breaking behavior.
- I — Interface Segregation: prefer small, specific interfaces over fat ones.
- D — Dependency Inversion: depend on abstractions, not concrete implementations.

## Clean Code
- Meaningful Names: clear, intent-revealing names for variables, functions, and classes.
- Small Functions: short, single-purpose, telling a cohesive story.
- DRY: avoid duplication; extract and reuse.
- Boy Scout Rule: leave the code cleaner than you found it.
- Comments: code should be self-explanatory; comment only complex business rules.
- Error Handling: handle exceptions explicitly; avoid returning `null`.
- Clean Tests: follow FIRST (Fast, Independent, Repeatable, Self-validating, Timely).

## Code Conventions
- Declare `strict_types=1` in every PHP file.
- Follow PSR-12 formatting and the project's PSR-4 autoload (`App\` → `var/www/html/`).
- Classes in `PascalCase`, methods/variables in `camelCase`.
- Namespaces must match the directory structure.

## Error Handling
- Use the project's custom exceptions (`NotFoundException`, `ValidationException`, `UnauthorizedException`).
- Never swallow exceptions; log or rethrow with context.

## Automated Tests (PHPUnit 12)
- Every new class in `var/www/html/` MUST have a matching `*Test.php` in `tests/`.
- Tests run via `composer test` (`tests/phpunit.xml.dist`).
- Keep tests fast, isolated, and deterministic.

## Design Patterns in Use
- DTOs for data transfer (`dto/`).
- DAOs for data access (`dao/`).
- FrontController + Dispatcher for routing (`service/controller/`, `service/Dispatcher.php`).
- Service layer for business logic (`service/`).
- Apply creational/structural/behavioral patterns only when they reduce complexity.

## Security
- Validate all input; use prepared statements (PDO) for DB access.
- Never concatenate user input into SQL.
