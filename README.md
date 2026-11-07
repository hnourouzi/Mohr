# مُهر (Mohr)

سرویس اعتبارسنجی و moderation محتوا با Laravel 13 و PHP 8.3.

## معماری

1. **لایه ۱ — لیست سیاه محلی:** کلمات `block` (رد کامل) و `flag` (نیاز به بررسی انسانی، بدون AI).
2. **لایه ۲ — AI (OpenRouter):** طبقه‌بندی JSON و تعیین `approved` / `rejected`.
3. **کیف پول:** قبل از پردازش، هزینهٔ ثابت هر درخواست کسر می‌شود؛ در خطای سیستمی AI، **حداکثر یک** بازگشت وجه (refund) برای هر محتوا.
4. **صف:** `moderation` (لایه ۱) و `moderation-ai` (لایه ۲)، با `after_commit` روی اتصال database.

## پیش‌نیازها

- PHP ^8.3
- Composer
- MySQL (یا SQLite برای تست)
- Node.js (برای Vite در صورت نیاز به build فرانت)

## نصب

```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
npm install && npm run build
```

برای دادهٔ نمونه (اختیاری):

```bash
php artisan db:seed
```

## پیکربندی OpenRouter

در `.env`:

- `OPENROUTER_API_KEY`
- `OPENROUTER_MODEL` (پیش‌فرض: `openrouter/free`)
- `MODERATION_COST_PER_REQUEST` (تومان، عدد صحیح)

تنظیمات متمرکز در `config/moderation.php`.

## صف و worker

```bash
php artisan queue:work --queue=moderation,moderation-ai --tries=3
```

اتصال پیش‌فرض: `database` (`QUEUE_CONNECTION`). برای production، worker را با supervisor/systemd نگه دارید و `queue:failed` را مانیتور کنید.

## کیف پول

- در **پنل وب** کاربران خودشان نمی‌توانند کیف پول را شارژ کنند (تا اتصال درگاه پرداخت).
- در **API** شارژ شبیه‌سازی‌شده با `POST /api/v1/wallet/top-up` وجود دارد (فقط برای توسعه/تست).
- مدیر از پنل ادمین یا `POST /api/v1/admin/users/{user}/top-up` می‌تواند شارژ دستی انجام دهد.

## مدیر سیستم

کاربر با نقش `admin` در دیتابیس (یا seeder) به مسیر `/admin` دسترسی دارد. ثبت‌نام همیشه نقش `user` می‌سازد؛ فیلد `role` mass-assignable نیست.

## API

پیشوند: `/api/v1` — احراز هویت با **Laravel Sanctum** (توکن Bearer).

### حساب‌های seed

پس از `php artisan db:seed`:

| ایمیل | رمز | نقش | موجودی تقریبی |
|--------|-----|------|----------------|
| `admin@example.com` | `password` | admin | متغیر |
| `user@example.com` | `password` | user | ۵۰٬۰۰۰ تومان |

### جریان احراز هویت

1. `POST /api/v1/auth/login` با `email` و `password` (اختیاری: `device_name`).
2. مقدار `token` را در هدر `Authorization: Bearer {token}` بفرستید.
3. `POST /api/v1/auth/logout` فقط توکن جاری را باطل می‌کند.

انقضای توکن: متغیر env `SANCTUM_TOKEN_EXPIRATION` (دقیقه، پیش‌فرض ۷ روز = `10080`).

### جدول endpointها

| متد | مسیر | توضیح |
|-----|------|--------|
| POST | `/auth/register` | ثبت‌نام |
| POST | `/auth/login` | ورود (throttle: ۵/دقیقه به ازای ایمیل+IP) |
| POST | `/auth/logout` | خروج |
| GET | `/auth/me` | کاربر + موجودی |
| GET | `/wallet` | موجودی + هزینه هر درخواست |
| GET | `/wallet/transactions` | تراکنش‌ها (صفحه‌بندی) |
| POST | `/wallet/top-up` | شارژ شبیه‌سازی‌شده |
| POST | `/contents` | ارسال محتوا (throttle: ۳۰/دقیقه، async) |
| GET | `/contents` | فیلتر: `status`, `channel`, `per_page`≤۵۰ |
| GET | `/contents/{id}` | جزئیات + لاگ‌ها |
| GET | `/contents/{id}/logs` | لاگ moderation |
| POST | `/contents/{id}/retry` | تلاش مجدد (۲۰۲ یا ۴۰۹) |
| GET | `/admin/contents` | همه محتواها (admin) |
| GET | `/admin/users` | کاربران |
| POST | `/admin/users/{user}/top-up` | شارژ کاربر |
| GET/POST/DELETE | `/admin/blacklist` | لیست سیاه |

خطاهای API: `{"message","code","errors"}` — مثلاً `402` + `insufficient_balance`، `403` + `forbidden`.

### نمونه cURL

```bash
TOKEN=$(curl -s -X POST http://localhost:8000/api/v1/auth/login \
  -H "Accept: application/json" \
  -H "Content-Type: application/json" \
  -d '{"email":"user@example.com","password":"password"}' | jq -r .token)

curl -s http://localhost:8000/api/v1/contents \
  -H "Accept: application/json" \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -d '{"body":"متن نمونه برای تست API","channel":"web"}'
```

### Postman

1. Import: `docs/postman/Mohr.postman_collection.json`
2. Environment: `docs/postman/Mohr.local.postman_environment.json`
3. ابتدا **Login (user)** و **Login (admin)** را اجرا کنید؛ سپس بقیه درخواست‌ها.

## تست

```bash
php artisan test
```

تست‌ها از `Http::fake()` برای OpenRouter استفاده می‌کنند و درخواست واقعی به provider نمی‌زنند.

## استقرار

- `php artisan migrate --force`
- `php artisan config:cache` / `route:cache` در production
- راه‌اندازی queue worker
- `APP_DEBUG=false` و کلیدهای واقعی در `.env` (هرگز commit نشوند)
