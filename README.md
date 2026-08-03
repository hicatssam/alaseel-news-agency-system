# Al-Aseel News Platform

A full-featured Arabic digital news CMS built with **Laravel 12** and **PHP 8.2**.

---

## Requirements

| Tool | Version |
|------|---------|
| PHP | 8.2+ |
| Composer | 2.x |
| MySQL | 8.0+ |
| Node.js (optional) | 18+ |

---

## Local Setup

### 1 — Clone and install dependencies

```bash
git clone <repo-url>
cd alaseel-laravel

composer install
```

### 2 — Environment file

```bash
cp .env.example .env
php artisan key:generate
```

Open `.env` and set your MySQL credentials:

```env
DB_DATABASE=alaseel
DB_USERNAME=root
DB_PASSWORD=your_password
```

### 3 — Create the MySQL database

```sql
CREATE DATABASE alaseel CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

### 4 — Run migrations and seed

```bash
php artisan migrate --seed
```

This creates all tables and seeds:
- 1 Admin user: `admin@alaseel.news` / `password`
- 1 Editor user: `editor@alaseel.news` / `password`
- 10 categories, 10 articles, 3 videos, sample settings

### 5 — Storage link

```bash
php artisan storage:link
```

### 6 — Start the server

```bash
php artisan serve
```

Open [http://localhost:8000](http://localhost:8000)  
Admin panel: [http://localhost:8000/admin](http://localhost:8000/admin)

---

## Folder Structure

```
app/
  Http/Controllers/
    Admin/          ← Admin panel controllers
    ArticleController.php
    CategoryController.php
    VideoController.php
  Models/           ← Eloquent models
  Providers/        ← ViewComposer for nav categories

resources/views/
  layouts/
    app.blade.php   ← Public layout (dark theme)
    admin.blade.php ← Admin layout
  auth/
    login.blade.php ← Premium black-and-gold login page
  home.blade.php    ← Homepage
  admin/            ← All admin panel views

database/
  migrations/       ← All table migrations
  seeders/          ← DatabaseSeeder.php

routes/
  web.php           ← All routes
```

---

## Admin Panel

Navigate to `/admin` to access the control panel.

| Section | URL |
|---------|-----|
| Dashboard | `/admin` |
| Articles | `/admin/articles` |
| Categories | `/admin/categories` |
| Videos | `/admin/videos` |
| Users | `/admin/users` |
| Settings | `/admin/settings` |

---

## Key Features

- RTL Arabic UI with Cairo / Inter fonts
- Dark premium black-and-gold design
- Dynamic navigation pulled from database (no hardcoded menus)
- Breaking news ticker
- Featured hero section
- Trending / Most Read sidebar
- Editor's Picks section
- Video section
- Role-based access (Admin / Editor)
- Newsletter subscription
- SEO-friendly slugs
- Soft deletes on users

---

## Production Deployment

Before going live, update `.env`:

```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-domain.com

SESSION_SECURE_COOKIE=true
SESSION_SAME_SITE=lax
```

Then run:

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```
