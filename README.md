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

- کاربران **نمی‌توانند** خودشان کیف پول را شارژ کنند (تا اتصال درگاه پرداخت).
- مدیر از پنل ادمین (`admin/users/{user}`) می‌تواند شارژ دستی انجام دهد.

## مدیر سیستم

کاربر با نقش `admin` در دیتابیس (یا seeder) به مسیر `/admin` دسترسی دارد. ثبت‌نام همیشه نقش `user` می‌سازد؛ فیلد `role` mass-assignable نیست.

## API

مسیرهای HTTP API در حال حاضر **فعال نیستند** (`routes/api.php` ثبت نشده). فقط پنل وب session-based استفاده می‌شود.

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
