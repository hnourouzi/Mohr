# Mohr

Content validation and moderation service built with Laravel 13 and PHP 8.3.

## Architecture

1. **Layer 1 — Local blacklist:** `block` words (full rejection) and `flag` words (human review, no AI).
2. **Layer 2 — AI (OpenRouter):** JSON classification and `approved` / `rejected` decisions.
3. **Wallet:** A fixed per-request fee is deducted before processing; on AI system errors, **at most one** refund per content item.
4. **Queues:** `moderation` (layer 1) and `moderation-ai` (layer 2), with `after_commit` on the database connection.

## Prerequisites

- PHP ^8.3
- Composer
- MySQL (or SQLite for testing)
- Node.js (for Vite if you need to build the frontend)

## Installation

```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
npm install && npm run build
```

Optional sample data:

```bash
php artisan db:seed
```

## OpenRouter configuration

In `.env`:

- `OPENROUTER_API_KEY`
- `OPENROUTER_MODEL` (default: `openrouter/free`)
- `MODERATION_COST_PER_REQUEST` (tomans, integer)

Central settings live in `config/moderation.php`.

## Queue and worker

```bash
php artisan queue:work --queue=moderation,moderation-ai --tries=3
```

Default connection: `database` (`QUEUE_CONNECTION`). In production, keep the worker running via supervisor/systemd and monitor `queue:failed`.

## Wallet

- In the **web panel**, users cannot top up their wallet yet (pending payment gateway integration).
- The **API** exposes a simulated top-up via `POST /api/v1/wallet/top-up` (development/testing only).
- Admins can manually credit users from the admin panel or `POST /api/v1/admin/users/{user}/top-up`.

## System administrator

Users with the `admin` role in the database (or via seeder) can access `/admin`. Registration always creates a `user` role; `role` is not mass-assignable.

## API

Prefix: `/api/v1` — authentication via **Laravel Sanctum** (Bearer token).

### Seeded accounts

After `php artisan db:seed`:

| Email | Password | Role | Approx. balance |
|-------|----------|------|-----------------|
| `admin@example.com` | `password` | admin | varies |
| `user@example.com` | `password` | user | 50,000 tomans |

### Authentication flow

1. `POST /api/v1/auth/login` with `email` and `password` (optional: `device_name`).
2. Send the returned `token` in the `Authorization: Bearer {token}` header.
3. `POST /api/v1/auth/logout` revokes only the current token.

Token expiry: env `SANCTUM_TOKEN_EXPIRATION` (minutes, default 7 days = `10080`).

### Endpoints

| Method | Path | Description |
|--------|------|-------------|
| POST | `/auth/register` | Register |
| POST | `/auth/login` | Login (throttle: 5/min per email+IP) |
| POST | `/auth/logout` | Logout |
| GET | `/auth/me` | Current user + balance |
| GET | `/wallet` | Balance + cost per request |
| GET | `/wallet/transactions` | Transactions (paginated) |
| POST | `/wallet/top-up` | Simulated top-up |
| POST | `/contents` | Submit content (throttle: 30/min, async) |
| GET | `/contents` | Filters: `status`, `channel`, `per_page` ≤ 50 |
| GET | `/contents/{id}` | Details + logs |
| GET | `/contents/{id}/logs` | Moderation logs |
| POST | `/contents/{id}/retry` | Retry (202 or 409) |
| GET | `/admin/contents` | All content (admin) |
| GET | `/admin/users` | Users |
| POST | `/admin/users/{user}/top-up` | Credit user |
| GET/POST/DELETE | `/admin/blacklist` | Blacklist |

API errors: `{"message","code","errors"}` — e.g. `402` + `insufficient_balance`, `403` + `forbidden`.

### Example cURL

```bash
TOKEN=$(curl -s -X POST http://localhost:8000/api/v1/auth/login \
  -H "Accept: application/json" \
  -H "Content-Type: application/json" \
  -d '{"email":"user@example.com","password":"password"}' | jq -r .token)

curl -s http://localhost:8000/api/v1/contents \
  -H "Accept: application/json" \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -d '{"body":"Sample text for API testing","channel":"web"}'
```

### Postman

1. Import: `docs/postman/Mohr.postman_collection.json`
2. Environment: `docs/postman/Mohr.local.postman_environment.json`
3. Run **Login (user)** and **Login (admin)** first; then the remaining requests.

## Testing

```bash
php artisan test
```

Tests use `Http::fake()` for OpenRouter and do not call the live provider.

## Deployment

- `php artisan migrate --force`
- `php artisan config:cache` / `route:cache` in production
- Run the queue worker
- Set `APP_DEBUG=false` and real keys in `.env` (never commit them)
