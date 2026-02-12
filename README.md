# Arrivo Savings API (Laravel 12)

Production-grade backend API for a fintech savings MVP. Implements JWT authentication, role-based access control, personal savings and group savings modules, invitations, friends, and privileged admin/super-admin operations.

## Project Overview

This project provides a clean, secure, and testable REST API with:

- **JWT Auth** (`register`, `login`, `refresh`, `logout`, `me`)
- **RBAC** with a single `users.role` column (`user`, `admin`, `super_admin`)
- **Friends** requests + accept/remove/list
- **Personal Savings** CRUD (owner-only modifications via policies)
- **Group Savings** CRUD + invite flow + members list (creator-only management)
- **Invitations** accept/reject + expiry handling
- **Admin**: list users, suspend user, list all savings
- **Super Admin**: promote admin, view system stats

## Tech Stack

- **Laravel**: 12.x
- **PHP**: ^8.3 (project requirement)
- **Auth**: `tymon/jwt-auth` (JWT Guard)
- **Docs**: `darkaonline/l5-swagger` (Swagger UI)
- **Database**: PostgreSQL (recommended), SQLite for tests
- **Testing**: PHPUnit feature + unit tests

## Setup Instructions

### 1) Install dependencies

```bash
composer install
```

### 2) Configure environment

```bash
copy .env.example .env
php artisan key:generate
```

### 3) Database setup

This repo supports PostgreSQL (recommended for production) and SQLite.

#### PostgreSQL (recommended)

Update `.env`:

```env
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=arrivo_savings
DB_USERNAME=postgres
DB_PASSWORD=your_password
```

Run migrations:

```bash
php artisan migrate
```

#### SQLite (quick local)

Update `.env`:

```env
DB_CONNECTION=sqlite
DB_DATABASE=database/database.sqlite
```

Create the SQLite file if missing:

```bash
type nul > database/database.sqlite
php artisan migrate
```

### 4) Seed (idempotent)

This project’s `DatabaseSeeder` is designed to be **idempotent**.

```bash
php artisan db:seed
```

## Environment Variables

Key variables (see `.env.example` for the full list):

- **App**
  - `APP_ENV`, `APP_DEBUG`, `APP_URL`, `APP_KEY`
- **Database**
  - `DB_CONNECTION`, `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`
- **JWT**
  - `JWT_SECRET`
  - `JWT_TTL` (default 15 minutes)
  - `JWT_REFRESH_TTL` (default 20160 minutes)
  - Optional: `JWT_PUBLIC_KEY`, `JWT_PRIVATE_KEY`, `JWT_PASSPHRASE`
- **Swagger**
  - `L5_SWAGGER_GENERATE_ALWAYS` (dev only)
  - `L5_SWAGGER_USE_ABSOLUTE_PATH`

## JWT Setup

### Generate JWT secret

If you don’t have `JWT_SECRET` set:

```bash
php artisan jwt:secret
```

Notes:

- Auth guard `api` is configured to use the **JWT driver** (`config/auth.php`).
- Access tokens are returned from `POST /api/v1/auth/login` and `POST /api/v1/auth/register`.

## Running the API

```bash
php artisan serve
```

Default base URL:

- `http://127.0.0.1:8000/api/v1`

## Running Tests

```bash
php artisan test
```

Tests use SQLite in-memory (`DB_CONNECTION=sqlite`, `DB_DATABASE=:memory:`) via `phpunit.xml`.

### Coverage (80% target)

Coverage requires **Xdebug** or **PCOV** enabled in your CLI PHP.

```bash
php artisan test --coverage --min=80
```

If you see `Code coverage driver not available`, install/enable Xdebug/PCOV for the CLI PHP binary.

## Swagger / OpenAPI Documentation

Swagger UI route (from `config/l5-swagger.php`):

- `GET /api/documentation`

Generate docs (if required by your environment):

```bash
php artisan l5-swagger:generate
```

Recommended:

- In development: set `L5_SWAGGER_GENERATE_ALWAYS=true`
- In production: keep it `false` and generate docs in CI/CD.

## API Endpoint List

Base prefix: `/api/v1`

### Auth

- `POST /auth/register` (throttled)
- `POST /auth/login` (throttled)
- `POST /auth/refresh` (auth + throttled)
- `POST /auth/logout` (auth)
- `GET /auth/me` (auth)

### Friends (auth)

- `GET /friends`
- `POST /friends/requests` (throttled)
- `POST /friends/requests/{friendRequest}/accept` (throttled)
- `DELETE /friends/{friend}` (throttled)

### Personal Savings (auth)

- `GET /personal-savings`
- `POST /personal-savings`
- `GET /personal-savings/{personalSaving}`
- `PUT /personal-savings/{personalSaving}` (**owner-only**)
- `DELETE /personal-savings/{personalSaving}` (**owner-only**)

### Group Savings (auth)

- `GET /group-savings`
- `POST /group-savings`
- `GET /group-savings/{groupSaving}`
- `PUT /group-savings/{groupSaving}` (**creator-only**)
- `DELETE /group-savings/{groupSaving}` (**creator-only**)
- `POST /group-savings/{groupSaving}/invite` (**creator-only**, throttled)
- `GET /group-savings/{groupSaving}/members`

### Invitations (auth)

- `GET /invitations` (throttled)
- `POST /invitations/{invitation}/accept` (throttled)
- `POST /invitations/{invitation}/reject` (throttled)

### Admin (auth + `admin` middleware)

- `GET /admin/users`
- `PATCH /admin/users/{user}/suspend`
- `GET /admin/savings`

### Super Admin (auth + `super_admin` middleware)

- `PATCH /super-admin/users/{user}/promote-admin`
- `GET /super-admin/stats`

## Security Decisions (What and Why)

- **JWT + Stateless API**
  - Auth is via `Authorization: Bearer <token>`.
  - Stateful session cookies are rejected at the API boundary.
  - Rationale: reduces CSRF exposure and simplifies horizontal scaling.

- **Rate limiting**
  - Login and invitation endpoints are throttled.
  - Rationale: mitigates brute-force and abuse.

- **Centralized JSON exception handling**
  - Consistent error shape and safe messages in production.
  - Rationale: prevents information leakage and improves client reliability.

- **Request correlation (`X-Request-Id`) + structured logging**
  - Every request/response includes `X-Request-Id`.
  - Rationale: production debugging and incident triage.

- **Security headers**
  - CSP, `X-Frame-Options`, `nosniff`, etc.
  - Rationale: reduces browser-context attack surface and clickjacking.

- **Password hashing**
  - Uses Laravel `hashed` cast + `config/hashing.php` (Argon2id default).
  - Rationale: modern memory-hard hashing for credential safety.

- **Mass assignment protection**
  - Explicit `$fillable` in models.
  - Rationale: prevents privilege escalation or unintended writes.

## Performance Optimizations

### PostgreSQL / Schema

The schema uses:

- **Targeted indexes** for common access patterns:
  - `friend_requests (recipient_id, status)`, `(sender_id, status)`
  - `personal_savings (user_id, status)`
  - `group_savings (creator_id, status)`
  - `invitations (invitee_id, status)`, `(group_savings_id, status)`, `(expires_at)`

- **Uniqueness constraints** to prevent duplicates and keep lookups fast:
  - `friend_requests (sender_id, recipient_id)`
  - `group_savings_members (group_savings_id, user_id)`
  - `invitations.token`

### API behavior

- Pagination is used where listing is expected (`paginate()` in controllers).
- Rate limiting reduces abusive load.

## Notes for Reviewers (Interview Context)

- Service layer is used for business logic + transactions on critical flows (invites, accept flows, suspension).
- Policies are used for ownership/management checks (personal savings owner, group creator manage).
- Tests cover the critical security flows (auth, RBAC, ownership enforcement, invitation lifecycle).
