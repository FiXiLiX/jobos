# Laravel + MariaDB Docker Compose Template

This project provides a Docker Compose setup for running a Laravel application with a MariaDB database.

## Usage

1. Copy `.env.example` to `.env` in the `laravel` directory and set `APP_KEY` (after container is running, run `php artisan key:generate`).
2. Build and start the containers:
   ```bash
   docker compose up --build
   ```
3. Access the app at [http://localhost:8000](http://localhost:8000)

## Services
- **app**: PHP 8.2 FPM running Laravel
- **db**: MariaDB 11.3

## Volumes
- `dbdata`: MariaDB persistent data

## Notes
- The `laravel` directory is where your Laravel app code lives.
- Composer is installed in the PHP container.
- Database credentials are set in `.env` and `docker-compose.yml`.

---

Replace this README with your project details.
