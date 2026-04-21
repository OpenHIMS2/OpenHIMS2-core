# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project

OpenHIMS2 — Health Information Management System. Laravel 10.10, MySQL (`phims` DB), XAMPP.
Admin login: `admin@phims.lk` / `password`

## Setup Commands

```bash
# 1. Install dependencies (run once)
npm install bootstrap bootstrap-icons

# 2. Copy Bootstrap to public/vendor/ (run once, or after npm install)
php artisan app:publish-assets

# 3. Migrate and seed (admin user + static templates)
php artisan migrate:fresh --seed

# 4. Serve (or use XAMPP at http://localhost/PHIMS/public)
php artisan serve
```

## Common Development Commands

```bash
php artisan migrate                    # Run pending migrations
php artisan migrate:fresh --seed       # Full reset + re-seed
php artisan make:controller Admin/FooController
php artisan make:model Foo -m
php artisan route:list --path=admin    # Inspect admin routes
php artisan optimize:clear             # Clear all caches
php artisan test                       # Run test suite
php artisan test tests/Feature/FooTest.php
```

## Architecture

### Two Access Modes

| Mode | URL Prefix | Middleware | Who |
|------|-----------|-----------|-----|
| Admin | `/admin` | `admin` | `role = admin` |
| Clinical | `/clinical` | `clinical` | `role = user` |

Both middleware aliases are in `app/Http/Kernel.php`. The `admin` middleware (`app/Http/Middleware/AdminMiddleware.php`) checks `$user->isAdmin()`. The `clinical` middleware redirects admins to their dashboard.

### Database Schema

```
institutions   (id, name, parent_id self-ref)
unit_templates (id, name, code)               ← seeded, static
view_templates (id, name, code, blade_path, unit_template_id) ← seeded, static
units          (id, name, institution_id, unit_template_id)
unit_views     (id, name, unit_id, view_template_id)
user_units     (user_id, unit_id)             ← pivot
user_views     (user_id, unit_view_id)        ← pivot
users          (standard + role, institution_id)
```

### Key Models

- `Institution` — recursive `allChildren()` relationship for tree rendering
- `UnitView` — the bridge between a physical unit (e.g., "GMC Akurana") and a view template (e.g., "GMC Doctor View")
- `User::views()` → `belongsToMany(UnitView, 'user_views')` — a user's assigned clinical views

### Login Flow

`LoginController` inspects `$user->views` after auth:
- Admin → `/admin/dashboard`
- 1 view → `/clinical/{unitViewId}` directly
- Multiple views → `/clinical` (selection page)
- 0 views → error

### Clinical Pages

`ClinicalDashboardController::show(UnitView $unitView)` calls `view($viewTemplate->blade_path, [...])` where `blade_path` is a string like `clinical.gmc.doctor`. All 12 clinical blade files live under `resources/views/clinical/`.

### Frontend

All CSS/JS served from **local files** in `public/vendor/bootstrap/` and `public/vendor/bootstrap-icons/`. No Vite build. Re-run `php artisan app:publish-assets` to update after `npm install`.
.

### Static Seed Data

**Unit Templates:** GMC, DC, GI, GP, OFFICE
**View Templates (12):** GMC (doctor/clerk/nurse), DC (doctor/clerk/nurse), GI (doctor/clerk/nurse), GP (pharmacist/clerk), Office (staff)

Admin creates *instances* of these: institutions → units → unit_views → assign to users.
