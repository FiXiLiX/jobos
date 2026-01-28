# Quick Start Guide for JobOS

## One-Time Setup

### 1. Ensure .env file exists
The application will automatically use `.env.example` as a template. You can customize settings if needed:
```bash
cp laravel/.env.example laravel/.env
```

### 2. Start the application
Simply run:
```bash
docker compose up -d
```

That's it! The Docker entrypoint script will automatically:
- ✅ Copy `.env` file (if missing)
- ✅ Generate `APP_KEY` (if not set)
- ✅ Wait for database to be ready
- ✅ Run database migrations
- ✅ Initialize S3 bucket
- ✅ Ensure budgets exist for the current year
- ✅ Seed exchange rates (if none exist)
- ✅ Fetch latest exchange rates
- ✅ Start the Laravel dev server

## What Gets Automated

| Task | Command | Runs | Notes |
|------|---------|------|-------|
| ENV setup | Copy `.env.example` → `.env` | Once | Only if `.env` doesn't exist |
| App Key | `artisan key:generate` | Once | Only if `APP_KEY` not set |
| Database | `artisan migrate --force` | Every start | Idempotent, safe to run multiple times |
| S3 Bucket | `artisan s3:init-bucket` | Every start | Creates bucket if missing |
| Budgets | `artisan budgets:ensure` | Every start | Creates missing year budgets |
| Exchange Rates | `artisan db:seed --class=CurrencyExchangeSeeder` | Once | Only if no rates in DB |
| Latest Rates | `artisan exchange-rates:fetch` | Every start | Non-blocking, graceful failure |

## Useful Commands

### View application logs
```bash
docker compose logs app -f
```

### Run a Laravel command manually
```bash
docker compose exec app php artisan <command>
```

### Rebuild the Docker image
```bash
docker compose build --no-cache
```

### Stop and remove all containers
```bash
docker compose down
```

### Reset everything (dangerous!)
```bash
docker compose down -v
# Delete database: docker volume rm template_dbdata
# Then: docker compose up -d
```

## Services Running

- **Laravel App**: http://localhost:8000
- **Filament Admin**: http://localhost:8000/admin
- **MariaDB**: localhost:3306
- **MinIO (S3)**: http://localhost:9001 (admin/admin_secret)

## Troubleshooting

### Application won't start
Check logs: `docker compose logs app`

### Database migration failed
Wait a few seconds for MariaDB to be ready, then manually run:
```bash
docker compose exec app php artisan migrate --force
```

### S3 bucket not created
Manually initialize:
```bash
docker compose exec app php artisan s3:init-bucket
```

### Exchange rates not fetching
The app falls back to seeded rates. Fetch manually:
```bash
docker compose exec app php artisan exchange-rates:fetch
```
