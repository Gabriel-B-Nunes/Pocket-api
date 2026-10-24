# Project Brief

## Project Name
Pocket API (`Pocket-api`)

## Summary
`Pocket-api` is the backend REST API for the **Pocket** application. It provides authenticated CRUD endpoints for users, groups, items, and movement items (stock/inventory movements), with input validation/sanitization, JWT-based authentication, localized responses, and MySQL + Redis backing services.

## Core Requirements
- Register and authenticate users (`/api/v1/users`, `/api/v1/users/login`, `/api/v1/users/refresh`).
- Manage **groups** (collections owned by a user).
- Manage **items** (products/materials belonging to a user and group).
- Manage **movement items** (quantity/value movements tied to a user and item, with unit, quantity, unit value, and currency).
- Enforce authentication on every route except user registration/login/refresh via JWT bearer tokens.
- Validate and sanitize all inbound DTO payloads using reflection-based attribute rules.
- Return localized error/response messages based on `Accept-Language` (English/Portuguese).
- Persist data in MySQL (`pocket` database) and store JWT sessions in Redis.

## Goals
- Provide a self-contained, framework-free PHP 8.5 REST API.
- Keep a consistent controller/DAO/DTO/model/service layering.
- Support Docker-based local development (Apache + PHP 8.5 + MySQL + Redis).

## In Scope
- REST endpoints under `/api/v1/*`.
- JWT access (1h) and refresh (7d) tokens with Redis session storage.
- CRUD for groups, items, and movement items.
- User creation and login/refresh.

## Out of Scope (current)
- Frontend/client application.
- Automated test suite (none present).
- User profile update/delete/read endpoints (not implemented).
- Role/permission model beyond per-user ownership via UUID.

## Source of Truth
- Repository: https://github.com/Gabriel-B-Nunes/Pocket-api.git
- Default branch for development: `develop`.
