# Laravel Backend — Smart Agri-Advisory Platform

Source code for the Laravel 11 web application, rebuilt against the
**26-table normalized database schema** (see `docs/Database_Schema.md`
if you added it, or your original schema PDF). `vendor/` and
`composer.lock` are **not** included since this sandbox has no
Packagist access — run `composer install` once locally.

## Setup

```bash
cd laravel-backend
composer install
cp .env.example .env
php artisan key:generate

# Create a MySQL database named agri_advisory (or edit .env), then:
php artisan migrate --seed

# Point this app at your running Flask ML service (see ../ml-service)
# ML_SERVICE_URL is already set in .env.example

php artisan serve   # -> http://localhost:8000
```

## Demo accounts (from the seeder)

| Role               | Email                        | Password    |
|--------------------|-------------------------------|-------------|
| Admin              | admin@agriadvisory.test       | password123 |
| Farmer             | farmer@agriadvisory.test      | password123 |
| Extension Officer  | officer@agriadvisory.test     | password123 |
| Supplier           | supplier@agriadvisory.test    | password123 (pre-verified) |

The seeder also creates the 6 `climate_zones` and 15 `crops` reference
rows — the crop names match `ml-service/generate_data.py` exactly, so
`Crop::findByNameOrCreate()` in `app/Models/Crop.php` can resolve every
crop name the ML service returns straight to a `crops.id`.

## Schema → code map (26 tables)

| Group | Tables | Status in this codebase |
|---|---|---|
| Core/Auth | `users` | Fully wired (RBAC, `EnsureRole` middleware) |
| Reference | `climate_zones`, `crops` | Fully wired + seeded; `crop_calendar` migration included, no UI yet (optional) |
| Farmer | `farm_profiles` (soil pH/N/P/K live here), `weather_logs` | Fully wired; `weather_logs` used opportunistically for crop recommendation, no OpenWeather cron yet |
| ML Output | `recommendations`, `fertilizer_recommendations`, `price_forecasts`, `disease_detections` | Fully wired — every ML call from `FarmerController` persists here |
| Feedback | `recommendation_feedback` | Fully wired |
| Extension Officer | `officer_verifications`, `officer_zone_assignments`, `officer_overrides`, `advisory_messages`, `alerts`, `training_sessions`, `training_attendees` | Verification + overrides + advisory + alerts + training all wired; `training_attendees` migration only (no registration UI yet) |
| Supplier | `suppliers`, `products`, `orders`, `order_items`, `inquiries`, `demand_forecasts` | Fully wired |
| Admin | `admin_logs`, `model_retraining_jobs`, `system_backups`, `analytics_snapshots` | Fully wired — every admin action writes an `admin_logs` row |

## RBAC

`users.role` is `farmer | extension_officer | supplier | admin`, enforced by
`app/Http/Middleware/EnsureRole.php` (aliased `role` in `bootstrap/app.php`).
New `extension_officer`/`supplier` signups start `status = pending`
(a `suppliers` row is also created, `verified = false`) and need Admin
approval — `POST /admin/users/{user}/approve`.

## How this talks to the ML service

`app/Services/MlService.php` calls the Flask endpoints in
`ml-service/app.py`. `FarmerController` calls it, resolves the returned
crop-name string to a `crops.id` via `Crop::findByNameOrCreate()`, and
writes the result into `recommendations` / `fertilizer_recommendations`
/ `price_forecasts` / `disease_detections` for history and for Extension
Officers to review.

## Folder map

```
app/Models/             28 Eloquent models — one per migration
app/Http/Controllers/    Auth, Farmer, ExtensionOfficer, Supplier, Admin
app/Http/Middleware/     EnsureRole.php (RBAC)
app/Services/MlService.php   HTTP client for the Flask ML microservice
database/migrations/     28 migration files covering all 26 tables, in FK-safe order
database/seeders/        DatabaseSeeder.php — zones, crops, demo users
routes/web.php            All routes, grouped by role
resources/views/          Blade templates (Bengali UI), per role
public/css/app.css        Styling
public/js/app.js          Axios helper functions
```
